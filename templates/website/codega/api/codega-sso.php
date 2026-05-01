<?php
/**
 * CODEGA Theme - SSO Endpoint
 * 
 * URL: https://ca.codega.com.tr/codega-sso?email=...&ts=...&nonce=...&sig=...
 * 
 * Validates HMAC-signed token from codega.com.tr and logs the user in.
 * If user does not exist in WiseCP, optionally auto-creates the account.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'codega-bridge.php';

$_email = Filter::init('GET/email');
$_ts    = Filter::init('GET/ts',    'numbers');
$_nonce = Filter::init('GET/nonce');
$_sig   = Filter::init('GET/sig');

$result = Codega_Bridge::validateSso($_email, $_ts, $_nonce, $_sig);

if (!$result || empty($result['email'])) {
    // Invalid SSO — show generic error and redirect to login
    if (!headers_sent()) {
        header('HTTP/1.1 403 Forbidden');
    }
    echo '<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Geçersiz Oturum</title>';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<style>body{font-family:system-ui,-apple-system,sans-serif;background:#0a1628;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:24px;}';
    echo '.box{max-width:420px;text-align:center;}h1{font-size:1.75rem;margin:0 0 12px;color:#d4a574;}p{color:rgba(255,255,255,0.75);line-height:1.6;}a{color:#d4a574;text-decoration:none;border-bottom:1px solid rgba(212,165,116,0.4);}</style></head><body>';
    echo '<div class="box"><h1>Oturum Doğrulanamadı</h1>';
    echo '<p>codega.com.tr üzerinden gönderilen oturum bağlantısı geçersiz, süresi dolmuş veya zaten kullanılmış. Lütfen tekrar giriş yapın.</p>';
    echo '<p style="margin-top:24px;"><a href="/login">Giriş sayfasına dön</a></p>';
    echo '</div></body></html>';
    exit;
}

// Find or create WiseCP user with this email
$email = $result['email'];

try {
    $user = DB::row("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
} catch (Throwable $e) {
    // Fallback for differing DB API
    $user = null;
    if (class_exists('User') && method_exists('User', 'getUserByEmail')) {
        $user = User::getUserByEmail($email);
    }
}

if (!$user) {
    // Optionally auto-create — gated by setting; default redirect to register
    $settings = Config::get("theme/settings") ?: [];
    $auto_create = !empty($settings['sso_auto_create']);

    if ($auto_create && class_exists('User') && method_exists('User', 'create')) {
        // Minimal account creation — most installs will want manual provisioning
        $newUserId = User::create([
            'email'      => $email,
            'name'       => strstr($email, '@', true) ?: 'User',
            'surname'    => '-',
            'password'   => bin2hex(random_bytes(12)), // random; user must reset
            'status'     => 'Active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($newUserId) {
            $user = DB::row("SELECT * FROM users WHERE id = ? LIMIT 1", [$newUserId]);
        }
    } else {
        // Redirect to register with email pre-filled
        header('Location: /register?email=' . urlencode($email) . '&from=codega-sso');
        exit;
    }
}

// Log user in (WiseCP session)
if ($user) {
    $_SESSION['user'] = is_array($user) ? $user : (array)$user;
    if (class_exists('User') && method_exists('User', 'loginById') && !empty($user['id'])) {
        User::loginById($user['id']);
    }

    // Audit log entry (best effort)
    if (class_exists('Modules') && method_exists('Modules', 'log')) {
        Modules::log('codega-sso', 'Login via codega.com.tr SSO: ' . $email);
    }

    // Redirect target
    $redirect = Filter::init('GET/redirect') ?: '/clientarea';
    if (!preg_match('#^/[^/]#', $redirect)) $redirect = '/clientarea'; // only same-origin paths
    header('Location: ' . $redirect);
    exit;
}

header('Location: /login?error=sso_user_resolution_failed');
exit;
