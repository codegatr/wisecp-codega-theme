<?php
/**
 * CODEGA WiseCP <- codega.com.tr SSO Bridge Endpoint
 *
 * Ana site (codega.com.tr) bu endpoint'e Codega\WiseCP\Client::ssoRedirectUrl()
 * ile imzalı GET isteği gönderir. Bu endpoint:
 *   1) HMAC-SHA256 imzayı doğrular
 *   2) ±60 saniye zaman penceresini kontrol eder (replay protection)
 *   3) nonce uniqueness kontrol eder
 *   4) Email'e göre WiseCP kullanıcısı bulur ve oturum açar
 *   5) return parametresine yönlendirir (varsayılan: my-account)
 *
 * URL: https://codega.com.tr/codega-sso.php?email=&ts=&nonce=&return=&sig=
 *
 * Güvenlik:
 *  - Sadece HTTPS kabul (Origin header opsiyonel kontrol)
 *  - Shared secret theme-config.php codega_integration.shared_secret
 *  - Nonce dosya-bazlı 5 dakika cache, bu süre içinde aynı nonce reddedilir
 *
 * @version 1.0.0
 */

defined('CORE_FOLDER') OR die('You can not get in here!');

// === 1) AYARLARI YÜKLE ===
$cdgIntegration = null;
$themeConfigPath = __DIR__ . DIRECTORY_SEPARATOR . 'theme-config.php';
if (file_exists($themeConfigPath)) {
    $themeConfig = require $themeConfigPath;
    $cdgIntegration = $themeConfig['settings']['codega_integration'] ?? null;
}

if (!$cdgIntegration || empty($cdgIntegration['enabled'])) {
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'error' => 'sso_disabled', 'message' => 'SSO bridge is disabled in theme settings']);
    exit;
}

$secret      = $cdgIntegration['shared_secret'] ?? '';
$timeWindow  = (int) ($cdgIntegration['time_window'] ?? 60);
$ssoDefault  = $cdgIntegration['sso_default'] ?? 'my-account';
$allowOrigin = $cdgIntegration['allowed_origin'] ?? '';
$logEnabled  = !empty($cdgIntegration['log_requests']);

if (strlen($secret) < 32 || strpos($secret, 'CHANGE_ME') !== false) {
    http_response_code(503);
    echo '<h1>SSO Misconfigured</h1><p>Shared secret is not set or too short. Please update theme-config.php codega_integration.shared_secret</p>';
    exit;
}

// === 2) PARAMETRELERİ AL ===
$email  = isset($_GET['email'])  ? trim($_GET['email'])  : '';
$ts     = isset($_GET['ts'])     ? (int) $_GET['ts']     : 0;
$nonce  = isset($_GET['nonce'])  ? trim($_GET['nonce'])  : '';
$return = isset($_GET['return']) ? trim($_GET['return']) : $ssoDefault;
$sig    = isset($_GET['sig'])    ? trim($_GET['sig'])    : '';

// Basic input sanitization
$email  = filter_var($email, FILTER_VALIDATE_EMAIL) ?: '';
$nonce  = preg_replace('/[^a-f0-9]/i', '', $nonce);
$return = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $return);
if (!$return) $return = $ssoDefault;

if (!$email || !$ts || !$nonce || !$sig) {
    cdg_sso_fail('missing_params', 'Required parameters missing');
}

// === 3) ZAMAN PENCERESİ KONTROLÜ ===
$now = time();
if (abs($now - $ts) > $timeWindow) {
    cdg_sso_fail('timestamp_out_of_range', "Timestamp is outside ±{$timeWindow}s window");
}

// === 4) NONCE UNIQUENESS KONTROLÜ ===
$nonceDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codega-bridge-nonces';
if (!is_dir($nonceDir)) @mkdir($nonceDir, 0700, true);
$nonceFile = $nonceDir . DIRECTORY_SEPARATOR . substr(md5($nonce), 0, 32);
// Eski nonce dosyalarını temizle (5dk üstü)
if (rand(1, 50) === 1) {
    foreach (glob($nonceDir . '/*') as $f) {
        if (filemtime($f) < ($now - 300)) @unlink($f);
    }
}
if (file_exists($nonceFile) && filemtime($nonceFile) > ($now - 300)) {
    cdg_sso_fail('replay', 'Nonce already used');
}
@touch($nonceFile);

// === 5) HMAC İMZA DOĞRULAMA ===
$expectedSig = cdg_sso_sign([
    'email'  => $email,
    'ts'     => $ts,
    'nonce'  => $nonce,
    'return' => $return,
], $secret);

if (!hash_equals($expectedSig, $sig)) {
    cdg_sso_fail('invalid_signature', 'HMAC signature mismatch');
}

// === 6) WISECP KULLANICI BULMA + OTURUM AÇMA ===
if (!class_exists('Users') && !class_exists('UserManager')) {
    cdg_sso_fail('wisecp_unavailable', 'WiseCP user system not loaded');
}

$user = null;
try {
    if (class_exists('Users') && method_exists('Users', 'getDataByEmail')) {
        $user = Users::getDataByEmail($email);
    } elseif (class_exists('UserManager') && method_exists('UserManager', 'getByEmail')) {
        $user = UserManager::getByEmail($email);
    } else {
        // DB'den manuel arama (fallback)
        if (class_exists('DB')) {
            $user = DB::$db->select('*')->from('users')->where('email', '=', $email)->execute();
            $user = is_array($user) && isset($user[0]) ? $user[0] : null;
        }
    }
} catch (\Throwable $e) {
    cdg_sso_log('user_lookup_error', $e->getMessage());
}

if (!is_array($user) || empty($user['id'])) {
    cdg_sso_fail('user_not_found', "Email '{$email}' not found in WiseCP", 404);
}

// === 7) OTURUM AÇMA ===
try {
    if (class_exists('UserManager') && method_exists('UserManager', 'login')) {
        UserManager::login($user['id']);
    } else {
        // Fallback: Session manuel ayarla
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user']      = $user;
        $_SESSION['logged_in'] = true;
    }
} catch (\Throwable $e) {
    cdg_sso_fail('login_error', 'Could not establish session: ' . $e->getMessage(), 500);
}

cdg_sso_log('success', "User {$email} (id={$user['id']}) logged in via SSO, redirect to {$return}");

// === 8) REDIRECT ===
$redirectUrl = '/' . ltrim($return, '/');
header("Location: {$redirectUrl}");
exit;


// ─────────────────────────────────────────────────────────
// HELPER FUNCTIONS
// ─────────────────────────────────────────────────────────

function cdg_sso_sign(array $params, $secret) {
    unset($params['sig']);
    ksort($params);
    $payload = '';
    foreach ($params as $k => $v) {
        $payload .= $k . '=' . $v . '&';
    }
    $payload = rtrim($payload, '&');
    return hash_hmac('sha256', $payload, $secret);
}

function cdg_sso_fail($code, $message, $httpCode = 403) {
    cdg_sso_log('fail:' . $code, $message);
    http_response_code($httpCode);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"><title>SSO Hatası</title>';
    echo '<style>body{font-family:-apple-system,sans-serif;max-width:480px;margin:80px auto;padding:30px;background:#f8fafc;color:#0f172a;}';
    echo '.card{background:#fff;border-radius:14px;padding:32px;box-shadow:0 8px 30px rgba(15,23,42,0.08);}';
    echo 'h1{color:#dc2626;font-size:22px;margin:0 0 12px;}p{color:#64748b;line-height:1.6;}code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:12px;}</style></head><body>';
    echo '<div class="card"><h1>🔒 SSO Bağlantı Hatası</h1>';
    echo '<p>Codega ana sitesinden gelen SSO isteği doğrulanamadı.</p>';
    echo '<p><strong>Sebep:</strong> ' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p>Lütfen <a href="/giris-yap">giriş sayfasından</a> tekrar deneyin veya destek ekibimizle iletişime geçin.</p>';
    echo '</div></body></html>';
    exit;
}

function cdg_sso_log($code, $message) {
    global $logEnabled;
    if (!$logEnabled) return;
    $logFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codega-bridge.log';
    $line = '[' . date('Y-m-d H:i:s') . '] [SSO:' . $code . '] ' . $message . "\n";
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}
