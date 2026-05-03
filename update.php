<?php
/**
 * Codega Theme - Otomatik Güncelleme Sistemi
 *
 * GitHub Releases üzerinden tek tıkla güncelleme yapar.
 *
 * Kullanım:
 *   /templates/website/Codega/update.php
 *   (Sadece admin panelinden veya direct URL ile erişim)
 *
 * Kontroller:
 *   - PHP session: WiseCP admin oturumu olmalı
 *   - Yoksa: ?key=XXXX ile manuel erişim (theme-config.php'den okunan secret)
 */

// === YETKİLENDİRME ===
// 1) Çok katmanlı WiseCP admin oturum tespiti
// 2) Secret key fallback (theme-config.php'den okunan + ilk çalıştırmada üretilen)
// 3) Erişim yoksa: Self-service URL göster (kullanıcıya direkt link ver)

if(session_status() == PHP_SESSION_NONE) @session_start();

$is_admin = false;

// Session içinde herhangi bir admin/wisecp anahtarı?
$admin_keys = ['admin','admin_id','wisecp_admin','wisecp_admin_id','admin_data','admin_user','wcp_admin','adminpanel'];
foreach($admin_keys as $ak) {
    if(!empty($_SESSION[$ak])) { $is_admin = true; break; }
}
// Session anahtarlarında "admin" geçen + boş olmayan
if(!$is_admin) {
    foreach($_SESSION as $k => $v) {
        if(stripos($k,'admin') !== false && !empty($v)) { $is_admin = true; break; }
    }
}
// WiseCP CORE Admin class
if(!$is_admin && class_exists('Admin', false)) {
    if(method_exists('Admin','logged_in') && @Admin::logged_in()) $is_admin = true;
    elseif(isset(\Admin::$init) && @\Admin::$init->logged_in()) $is_admin = true;
}

// Theme config'ten secret oku/üret
$config = @include __DIR__ . '/theme-config.php';
$secret = isset($config['settings']['update_secret']) ? $config['settings']['update_secret'] : '';
if(!$secret) {
    $secret = bin2hex(random_bytes(16));
    $cfg_content = @file_get_contents(__DIR__ . '/theme-config.php');
    if($cfg_content && strpos($cfg_content, 'update_secret') === false) {
        $cfg_content = str_replace(
            "'show_cta'      => 1,",
            "'show_cta'      => 1,\n        'update_secret' => '" . $secret . "',",
            $cfg_content
        );
        @file_put_contents(__DIR__ . '/theme-config.php', $cfg_content);
    }
}

// URL'deki key parametresi
$key_param = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
if(!$is_admin && $secret && hash_equals($secret, $key_param)) $is_admin = true;

// Erişim yoksa SELF-SERVICE URL göster
if(!$is_admin) {
    $self_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://')
              . $_SERVER['HTTP_HOST']
              . strtok($_SERVER['REQUEST_URI'], '?')
              . '?key=' . urlencode($secret);

    http_response_code(200); // 403 değil, normal sayfa - kullanıcı dostu
    ?><!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"><title>Codega - Güncelleme Erişimi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Plus Jakarta Sans',sans-serif}
    body{background:linear-gradient(135deg,#f8fafc,#e0e7ff);min-height:100vh;display:grid;place-items:center;padding:20px;color:#1e293b}
    .card{max-width:640px;background:#fff;border-radius:20px;padding:48px;box-shadow:0 20px 60px rgba(30,64,175,0.10)}
    .icon{width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;display:grid;place-items:center;font-size:36px;margin-bottom:24px;box-shadow:0 8px 24px rgba(30,64,175,0.30)}
    h1{font-size:30px;font-weight:800;margin-bottom:10px;color:#0f172a}
    .lead{color:#64748b;margin-bottom:24px;line-height:1.6;font-size:15px}
    .url-box{background:#f8fafc;padding:18px;border-radius:12px;font-family:'Menlo','Monaco',monospace;font-size:13px;word-break:break-all;border:2px dashed #c7d2fe;margin-bottom:20px;color:#2E3B4E;line-height:1.5}
    .btn{display:inline-flex;align-items:center;gap:10px;padding:16px 32px;background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;text-decoration:none;border-radius:12px;font-weight:700;font-size:15px;transition:all 0.2s;box-shadow:0 4px 16px rgba(30,64,175,0.20)}
    .btn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(30,64,175,0.40)}
    .info{margin-top:28px;padding:16px;background:#fffbeb;border-radius:10px;font-size:13px;color:#92400e;border-left:4px solid #f59e0b;line-height:1.6}
    .info strong{display:block;margin-bottom:4px;color:#78350f}
    .small{font-size:12px;color:#94a3b8;margin-top:24px;padding-top:20px;border-top:1px solid #e2e8f0;line-height:1.6}
    code{background:#f1f5f9;padding:2px 8px;border-radius:6px;font-family:'Menlo','Monaco',monospace;font-size:12px;color:#2E3B4E}
    </style></head><body>
    <div class="card">
        <div class="icon">🔐</div>
        <h1>Güncelleme Merkezi</h1>
        <p class="lead">Admin oturumunuz otomatik tespit edilemedi. Aşağıdaki güvenli URL'i kullanarak güncelleme sayfasına erişebilirsiniz:</p>
        <div class="url-box"><?php echo htmlspecialchars($self_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
        <a href="<?php echo htmlspecialchars($self_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="btn">
            <span>Güncelleme Merkezine Git</span>
            <span>&rarr;</span>
        </a>
        <div class="info">
            <strong>💡 Bu URL'i kaydedin (yer imine ekleyin)</strong>
            Secret key her tema kurulumunda otomatik ve benzersiz uretilir. Sadece bu sunucuya FTP/dosya erisimi olan kisi gorebilir.
        </div>
        <div class="small">
            Secret key'i değiştirmek için <code>theme-config.php</code> dosyasında <code>update_secret</code> değerini güncelleyin.
        </div>
    </div>
    </body></html><?php
    exit;
}

// İşlem yönlendirici
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Manifest oku
$manifest = @json_decode(@file_get_contents(__DIR__ . '/manifest.json'), true) ?: [];
$current_version = isset($manifest['version']) ? $manifest['version'] : '0.0.0';
$repo            = isset($manifest['repo']) ? $manifest['repo'] : 'codegatr/wisecp-codega-theme';
$check_url       = isset($manifest['checking_version_url']) ? $manifest['checking_version_url'] : "https://raw.githubusercontent.com/{$repo}/master/version.json";

// AJAX handlerlar
if($action === 'check') {
    header('Content-Type: application/json; charset=utf-8');
    $ctx = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: Codega-Theme-Updater\r\n"]]);
    $remote = @file_get_contents($check_url, false, $ctx);
    if(!$remote) {
        echo json_encode(['ok' => false, 'error' => 'GitHub baglantisi kurulamadi: ' . $check_url]);
        exit;
    }
    $remote_data = json_decode($remote, true);
    if(!$remote_data || !isset($remote_data['version'])) {
        echo json_encode(['ok' => false, 'error' => 'version.json okunamadi']);
        exit;
    }
    $is_newer = version_compare($remote_data['version'], $current_version, '>');
    echo json_encode([
        'ok'              => true,
        'current_version' => $current_version,
        'latest_version'  => $remote_data['version'],
        'has_update'      => $is_newer,
        'release_date'    => $remote_data['release_date'] ?? '',
        'changelog'       => $remote_data['changelog'] ?? [],
        'download_url'    => $remote_data['file_url'] ?? '',
        'details_url'     => $remote_data['details_url'] ?? '',
    ]);
    exit;
}

if($action === 'apply') {
    header('Content-Type: application/json; charset=utf-8');

    // Yine kontrol et + indirme yap
    $ctx = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: Codega-Theme-Updater\r\n"]]);
    $remote_data = @json_decode(@file_get_contents($check_url, false, $ctx), true);
    if(!$remote_data || empty($remote_data['file_url'])) {
        echo json_encode(['ok' => false, 'error' => 'version.json veya file_url okunamadi']);
        exit;
    }

    $download_url = $remote_data['file_url'];
    $new_version  = $remote_data['version'];

    // Önce yedek al
    $backup_dir = __DIR__ . '/.backups';
    if(!is_dir($backup_dir)) @mkdir($backup_dir, 0755, true);
    $backup_file = $backup_dir . '/backup-' . $current_version . '-' . date('Ymd-His') . '.zip';

    // ZIP'i indir
    $tmp_zip = sys_get_temp_dir() . '/codega-update-' . uniqid() . '.zip';

    $ch = curl_init($download_url);
    $fp = fopen($tmp_zip, 'wb');
    curl_setopt_array($ch, [
        CURLOPT_FILE           => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_USERAGENT      => 'Codega-Theme-Updater',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $ok = curl_exec($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if(!$ok || $http_code !== 200 || !file_exists($tmp_zip) || filesize($tmp_zip) < 1000) {
        @unlink($tmp_zip);
        echo json_encode(['ok' => false, 'error' => "ZIP indirilemedi (HTTP {$http_code}): {$err}", 'url' => $download_url]);
        exit;
    }

    // Mevcut dosyaları yedekle
    $zip_backup = new ZipArchive();
    if($zip_backup->open($backup_file, ZipArchive::CREATE) === true) {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
        foreach($rii as $file) {
            if($file->isDir()) continue;
            $rel = substr($file->getPathname(), strlen(__DIR__) + 1);
            // Yedek klasörünü ve cache'i atla
            if(strpos($rel, '.backups/') === 0) continue;
            if(strpos($rel, '.git/') === 0) continue;
            $zip_backup->addFile($file->getPathname(), $rel);
        }
        $zip_backup->close();
    }

    // Yeni ZIP'i çıkar
    $zip = new ZipArchive();
    if($zip->open($tmp_zip) !== true) {
        @unlink($tmp_zip);
        echo json_encode(['ok' => false, 'error' => 'ZIP acilamadi (corrupt)']);
        exit;
    }

    $extract_to = sys_get_temp_dir() . '/codega-extract-' . uniqid();
    @mkdir($extract_to);
    $zip->extractTo($extract_to);
    $zip->close();
    @unlink($tmp_zip);

    // Çıkartılan dizin: $extract_to/Codega/...
    $source_dir = $extract_to . '/Codega';
    if(!is_dir($source_dir)) {
        // Belki direkt çıktı, alt klasör yok
        $source_dir = $extract_to;
    }

    // Recursive copy (üzerine yaz)
    $copyRecursive = function($src, $dst) use (&$copyRecursive) {
        $dir = opendir($src);
        if(!is_dir($dst)) @mkdir($dst, 0755, true);
        while(($file = readdir($dir)) !== false) {
            if($file == '.' || $file == '..') continue;
            $src_path = $src . '/' . $file;
            $dst_path = $dst . '/' . $file;
            if(is_dir($src_path)) {
                $copyRecursive($src_path, $dst_path);
            } else {
                @copy($src_path, $dst_path);
            }
        }
        closedir($dir);
    };

    try {
        $copyRecursive($source_dir, __DIR__);
    } catch(Exception $e) {
        echo json_encode(['ok' => false, 'error' => 'Kopyalama hatasi: ' . $e->getMessage()]);
        exit;
    }

    // Cleanup
    $rmdir_recursive = function($d) use (&$rmdir_recursive) {
        if(!is_dir($d)) return;
        $items = scandir($d);
        foreach($items as $i) {
            if($i == '.' || $i == '..') continue;
            $p = $d . '/' . $i;
            if(is_dir($p)) $rmdir_recursive($p);
            else @unlink($p);
        }
        @rmdir($d);
    };
    $rmdir_recursive($extract_to);

    echo json_encode([
        'ok'              => true,
        'old_version'     => $current_version,
        'new_version'     => $new_version,
        'backup_file'     => basename($backup_file),
        'message'         => "v{$current_version} -> v{$new_version} başarıyla güncellendi",
    ]);
    exit;
}

// HTML görünüm
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codega Tema - Güncelleme Merkezi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap-icons.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh;padding:40px 20px;}
        .container{max-width:900px;margin:0 auto}
        .card{background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,0.05);margin-bottom:20px}
        .hero{background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;border-radius:16px;padding:40px;margin-bottom:24px}
        .hero .eyebrow{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:99px;background:rgba(255,255,255,0.15);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;backdrop-filter:blur(10px);margin-bottom:12px}
        .hero h1{font-size:36px;font-weight:700;margin-bottom:8px}
        .hero p{opacity:0.9;font-size:16px}
        .version-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(255,255,255,0.20);border-radius:99px;font-size:13px;font-weight:600;margin-top:12px}
        h2{font-size:22px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px}
        h2 i{color:#2E3B4E}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border:none;border-radius:10px;font-family:inherit;font-weight:600;font-size:14px;cursor:pointer;transition:all 0.2s;text-decoration:none}
        .btn-primary{background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(30,64,175,0.30)}
        .btn-outline{background:#fff;border:1px solid #e2e8f0;color:#1e293b}
        .btn-outline:hover{border-color:#2E3B4E;color:#2E3B4E}
        .btn:disabled{opacity:0.5;cursor:not-allowed;transform:none!important}
        .alert{padding:14px 18px;border-radius:10px;margin:16px 0;display:flex;align-items:flex-start;gap:10px;font-size:14px}
        .alert i{font-size:20px;flex-shrink:0;margin-top:1px}
        .alert-info{background:#eff6ff;color:#2E3B4E}
        .alert-success{background:#dcfce7;color:#166534}
        .alert-warning{background:#fef3c7;color:#92400e}
        .alert-error{background:#fef2f2;color:#991b1b}
        .change-list{list-style:none;padding:0}
        .change-list li{padding:10px 14px;background:#f8fafc;border-left:3px solid #2E3B4E;border-radius:6px;margin-bottom:8px;font-size:14px;display:flex;gap:8px}
        .change-list li::before{content:"✓";color:#2E3B4E;font-weight:700}
        .stat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:20px 0}
        .stat{padding:16px;background:#f8fafc;border-radius:10px}
        .stat .lbl{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.3px;margin-bottom:4px}
        .stat .val{font-size:18px;font-weight:700;color:#2E3B4E}
        .progress{height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-top:12px}
        .progress span{display:block;height:100%;background:linear-gradient(135deg,#2E3B4E,#00D3E5);transition:width 0.4s}
        code{background:#f1f5f9;padding:2px 8px;border-radius:6px;font-family:'Menlo','Monaco',monospace;font-size:13px;color:#2E3B4E}
        .spinner{width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;display:inline-block}
        @keyframes spin{to{transform:rotate(360deg)}}
        .text-muted{color:#64748b;font-size:14px}
    </style>
</head>
<body>
<div class="container">

    <div class="hero">
        <div class="eyebrow"><i class="bi bi-arrow-clockwise"></i> Güncelleme Merkezi</div>
        <h1>Codega Tema</h1>
        <p>WiseCP için geliştirilen özel tema</p>
        <span class="version-badge">
            <i class="bi bi-tag"></i> Mevcut Surum: v<?php echo htmlspecialchars($current_version, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
        </span>
    </div>

    <div class="card">
        <h2><i class="bi bi-cloud-download"></i> Otomatik Güncelleme</h2>
        <p class="text-muted">Tek tıkla GitHub üzerinden son sürüme güncelleyin.</p>

        <div class="stat-grid">
            <div class="stat">
                <div class="lbl">Mevcut Surum</div>
                <div class="val">v<?php echo htmlspecialchars($current_version, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            </div>
            <div class="stat">
                <div class="lbl">Son Surum</div>
                <div class="val" id="latest-version">Kontrol ediliyor...</div>
            </div>
            <div class="stat">
                <div class="lbl">Durum</div>
                <div class="val" id="status">-</div>
            </div>
        </div>

        <div id="message-area"></div>
        <div id="changelog-area"></div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:24px;">
            <button id="check-btn" class="btn btn-outline" onclick="checkUpdate()">
                <i class="bi bi-search"></i> Guncellemeleri Kontrol Et
            </button>
            <button id="apply-btn" class="btn btn-primary" onclick="applyUpdate()" disabled>
                <i class="bi bi-cloud-arrow-down"></i> Guncellemeyi Uygula
            </button>
            <a href="https://github.com/<?php echo htmlspecialchars($repo, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>/releases" target="_blank" class="btn btn-outline">
                <i class="bi bi-github"></i> GitHub Releases
            </a>
        </div>
    </div>

    <div class="card">
        <h2><i class="bi bi-info-circle"></i> Hakkında</h2>
        <p class="text-muted" style="margin-bottom:12px;">
            Codega tema, tek tikla GitHub Releases uzerinden otomatik guncellenir.
            Guncellemeden once mevcut dosyalar <code>.backups/</code> klasorune yedeklenir.
        </p>
        <ul class="change-list">
            <li>GitHub Repo: <code><?php echo htmlspecialchars($repo, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></li>
            <li>Update URL: <code><?php echo htmlspecialchars($check_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></li>
            <li>Yedekleme: Her güncellemeden önce otomatik</li>
        </ul>
    </div>

</div>

<script>
async function checkUpdate() {
    const btn = document.getElementById('check-btn');
    const status = document.getElementById('status');
    const latest = document.getElementById('latest-version');
    const msg = document.getElementById('message-area');
    const cl = document.getElementById('changelog-area');
    const apply = document.getElementById('apply-btn');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Kontrol ediliyor...';
    status.textContent = 'Kontrol...';

    try {
        const r = await fetch('?action=check&key=<?php echo urlencode($secret); ?>');
        const d = await r.json();

        if(!d.ok) {
            msg.innerHTML = '<div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> <div><strong>Hata:</strong> ' + d.error + '</div></div>';
            status.textContent = 'Hata';
            return;
        }

        latest.textContent = 'v' + d.latest_version;

        if(d.has_update) {
            status.innerHTML = '<span style="color:#f59e0b;">Güncelleme Var</span>';
            msg.innerHTML = '<div class="alert alert-warning"><i class="bi bi-cloud-arrow-down"></i><div><strong>Yeni surum mevcut!</strong> v' + d.current_version + ' &rarr; v' + d.latest_version + ' (' + (d.release_date || '') + ')</div></div>';
            apply.disabled = false;
            apply.dataset.version = d.latest_version;

            if(d.changelog && d.changelog.length) {
                let html = '<h3 style="margin:20px 0 10px;font-size:16px;font-weight:600;">Yenilikler</h3><ul class="change-list">';
                d.changelog.forEach(c => html += '<li>' + escapeHtml(c) + '</li>');
                html += '</ul>';
                cl.innerHTML = html;
            }
        } else {
            status.innerHTML = '<span style="color:#10b981;">Guncel</span>';
            msg.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle"></i><div>Tema en son surum kullaniliyor (v' + d.current_version + ').</div></div>';
            cl.innerHTML = '';
        }
    } catch(e) {
        msg.innerHTML = '<div class="alert alert-error"><i class="bi bi-exclamation-circle"></i><div>Baglanti hatasi: ' + e.message + '</div></div>';
        status.textContent = 'Hata';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i> Tekrar Kontrol Et';
    }
}

async function applyUpdate() {
    const btn = document.getElementById('apply-btn');
    const msg = document.getElementById('message-area');

    if(!confirm('Tema su andan itibaren guncellenecek. Devam edilsin mi?')) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Guncelleniyor (bu birkac saniye surebilir)...';
    msg.innerHTML = '<div class="alert alert-info"><i class="bi bi-cloud-arrow-down"></i><div>GitHub üzerinden indiriliyor ve dosyalar güncelleniyorlleniyor...</div></div>';

    try {
        const r = await fetch('?action=apply&key=<?php echo urlencode($secret); ?>');
        const d = await r.json();

        if(!d.ok) {
            msg.innerHTML = '<div class="alert alert-error"><i class="bi bi-exclamation-circle"></i><div><strong>Hata:</strong> ' + d.error + '</div></div>';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-arrow-down"></i> Tekrar Dene';
            return;
        }

        msg.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><div><strong>Basarili!</strong> ' + d.message + '. Yedek: <code>' + d.backup_file + '</code></div></div>';
        btn.innerHTML = '<i class="bi bi-check2"></i> Guncellendi';
        setTimeout(() => location.reload(), 2500);
    } catch(e) {
        msg.innerHTML = '<div class="alert alert-error"><i class="bi bi-exclamation-circle"></i><div>Beklenmeyen hata: ' + e.message + '</div></div>';
        btn.disabled = false;
    }
}

function escapeHtml(s) { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

// Sayfa açılınca otomatik kontrol
window.addEventListener('DOMContentLoaded', () => setTimeout(checkUpdate, 300));
</script>

</body>
</html>
