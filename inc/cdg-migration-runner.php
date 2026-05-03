<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA Theme - Migration Runner
 *
 * Tema güncellemesi sonrası ilk frontend isteğinde OTOMATIK çalışır.
 * Idempotent: zaten uygulanmış migration'ları atlamak için flag dosyası tutar.
 * Hata olursa silent log, frontend isteğini bozmaz.
 *
 * Tracking: data/migration-applied.json
 * Migration source: tema kökünde migration.sql
 */

class CdgMigrationRunner {
    /** @var string Tema dizini */
    private static $themeDir;

    /** @var string Migration SQL dosyası */
    private static $sqlFile;

    /** @var string Applied migrations flag dosyası */
    private static $flagFile;

    /** @var string Log dosyası */
    private static $logFile;

    /** @var int Max execution time (saniye) - frontend'i bloklamamak için */
    private static $maxRuntime = 8;

    public static function init() {
        self::$themeDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
        self::$sqlFile  = self::$themeDir . DIRECTORY_SEPARATOR . 'migration.sql';
        self::$flagFile = self::$themeDir . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'migration-applied.json';
        self::$logFile  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codega-migration.log';
    }

    /**
     * Ana çağrı - tema header'ında veya boot'ta çağrılır.
     * Hızlı erken-çıkışlar ile frontend'i bloklamaz.
     */
    public static function autoRun() {
        try {
            self::init();

            // 1. Migration dosyası yoksa atla
            if (!file_exists(self::$sqlFile)) return;

            // 2. WiseCP DB hazır değilse atla (frontend erken yüklenebilir)
            if (!class_exists('DB') && !class_exists('Mysqli') && !function_exists('mysqli_connect')) return;

            // 3. Flag kontrolü - zaten uygulandı mı?
            $sqlHash = md5_file(self::$sqlFile);
            $applied = self::loadFlag();
            if (isset($applied['hash']) && $applied['hash'] === $sqlHash) {
                return; // Aynı SQL, zaten uygulanmış
            }

            // 4. SQL'i parse et ve uygula
            $stats = self::runSql();

            // 5. Flag'i güncelle
            self::saveFlag([
                'hash'         => $sqlHash,
                'applied_at'   => date('Y-m-d H:i:s'),
                'theme_version' => self::getThemeVersion(),
                'stats'        => $stats,
            ]);

            self::log('OK', "Migration applied: " . json_encode($stats));
        } catch (\Throwable $e) {
            self::log('ERR', $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            // Migration başarısız olsa bile frontend bozulmasın
        }
    }

    /**
     * Migration SQL'i parse et ve çalıştır.
     */
    private static function runSql() {
        $sql = file_get_contents(self::$sqlFile);
        if (!$sql) return ['error' => 'sql_empty'];

        // SQL'i statement'lara böl - basit ayırma (-- yorumları + ;)
        $statements = self::splitSql($sql);

        $stats = ['total' => count($statements), 'ok' => 0, 'skip' => 0, 'err' => 0];
        $startTime = microtime(true);

        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (!$stmt || strlen($stmt) < 10) continue;

            // Time limit
            if ((microtime(true) - $startTime) > self::$maxRuntime) {
                self::log('TIMEOUT', "Stopped at $stats[ok] of $stats[total] (>$maxRuntime s)");
                break;
            }

            try {
                $ok = self::execStatement($stmt);
                if ($ok === 'skipped') $stats['skip']++;
                elseif ($ok) $stats['ok']++;
                else $stats['err']++;
            } catch (\Throwable $e) {
                $stats['err']++;
                self::log('STMT_ERR', substr($stmt, 0, 80) . "... -> " . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Tek SQL statement çalıştır - WiseCP DB sınıfını dene, fallback PDO/mysqli.
     */
    private static function execStatement($stmt) {
        // 1) WiseCP DB sınıfı (preferred)
        if (class_exists('DB') && isset(DB::$db) && method_exists(DB::$db, 'pure')) {
            try {
                DB::$db->pure($stmt);
                return true;
            } catch (\Throwable $e) {
                // Bazı duplicate hataları (zaten var) sessizce skip
                if (stripos($e->getMessage(), 'Duplicate') !== false) return 'skipped';
                throw $e;
            }
        }

        // 2) MysqlConnection global (WiseCP eski)
        if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli'] instanceof \mysqli) {
            $r = @$GLOBALS['mysqli']->query($stmt);
            if ($r === false) {
                $err = $GLOBALS['mysqli']->error;
                if (stripos($err, 'Duplicate') !== false) return 'skipped';
                throw new \Exception($err);
            }
            return true;
        }

        // 3) Hiçbir DB yöntemi yoksa
        throw new \Exception('No DB driver available');
    }

    /**
     * SQL'i statement'lara böl. Basit parser - SET @var = LAST_INSERT_ID() içeren multi-statement
     * için tek seferde execute etmek gerek; bu yüzden semicolon-based split + manuel grup.
     */
    private static function splitSql($sql) {
        // Yorumları sil
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);
        // Boş satırları sil
        $sql = preg_replace('/^\s*\n/m', '', $sql);

        // SET @var ile başlayan grupları (INSERT + SET + INSERT + INSERT) tek statement olarak gruplayabilmek için,
        // basit ; ile bölmek yetersiz olur. Bu yüzden migration.sql'i SADECE BAĞIMSIZ statement'lar içersin.
        // İdempotent INSERT'leri tek tek bölüyoruz:
        $statements = [];
        $current = '';
        $lines = explode("\n", $sql);
        foreach ($lines as $line) {
            $current .= $line . "\n";
            if (preg_match('/;\s*$/', trim($line))) {
                $statements[] = $current;
                $current = '';
            }
        }
        if (trim($current)) $statements[] = $current;

        return $statements;
    }

    /**
     * Flag dosyasını oku.
     */
    private static function loadFlag() {
        if (!file_exists(self::$flagFile)) return [];
        $j = @file_get_contents(self::$flagFile);
        $d = @json_decode($j, true);
        return is_array($d) ? $d : [];
    }

    /**
     * Flag dosyasına yaz.
     */
    private static function saveFlag($data) {
        $dir = dirname(self::$flagFile);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        @file_put_contents(self::$flagFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Tema versiyonu (manifest.json).
     */
    private static function getThemeVersion() {
        $manifest = self::$themeDir . DIRECTORY_SEPARATOR . 'manifest.json';
        if (file_exists($manifest)) {
            $d = @json_decode(file_get_contents($manifest), true);
            return $d['version'] ?? '0.0.0';
        }
        return '0.0.0';
    }

    /**
     * Log yaz - sys_get_temp_dir altında, frontend'i etkilemez.
     */
    private static function log($level, $msg) {
        $line = '[' . date('Y-m-d H:i:s') . "] [$level] $msg\n";
        @file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Manuel çağrı - admin panelden tetiklenebilir
     * (URL üzerinden admin auth'lu çağrı: /codega-migration-run.php?secret=XXX)
     */
    public static function forceRun() {
        self::init();
        // Flag'i sil ki tekrar uygulansın
        if (file_exists(self::$flagFile)) @unlink(self::$flagFile);
        self::autoRun();
        return self::loadFlag();
    }
}

// Auto-call: WiseCP boot sonrası çağrılır
CdgMigrationRunner::autoRun();
