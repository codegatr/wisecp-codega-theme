<?php
/**
 * CODEGA Theme - Integration Bridge
 * 
 * Handles SSO + API communication between ca.codega.com.tr (WiseCP)
 * and codega.com.tr (main site).
 * 
 * SSO Flow:
 *   1. User clicks "Müşteri Paneli" on codega.com.tr
 *   2. codega.com.tr generates HMAC-signed token: HMAC-SHA256(secret, "{email}|{ts}|{nonce}")
 *   3. Redirect to https://ca.codega.com.tr/codega-sso?email=...&ts=...&nonce=...&sig=...
 *   4. WiseCP validates HMAC + timestamp (max 60s skew) and creates session
 * 
 * API Flow:
 *   - codega.com.tr server makes HTTPS POST to ca.codega.com.tr/codega-api/{endpoint}
 *   - Headers: X-Codega-Timestamp, X-Codega-Nonce, X-Codega-Signature
 *   - Signature = HMAC-SHA256(secret, "{method}|{path}|{ts}|{nonce}|{body_hash}")
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

class Codega_Bridge {

    /** Maximum allowed timestamp skew in seconds (replay protection) */
    const MAX_SKEW = 60;

    /** Replay-cache file path */
    const NONCE_CACHE = 'codega_nonces.json';

    /**
     * Get the shared HMAC secret from theme settings
     */
    public static function secret()
    {
        $settings = Config::get("theme/settings") ?: [];
        return $settings['codega_shared_secret'] ?? '';
    }

    /**
     * Whether the SSO/API bridge is enabled in theme settings
     */
    public static function enabled()
    {
        $settings = Config::get("theme/settings") ?: [];
        return !empty($settings['sso_enabled']) && !empty($settings['codega_shared_secret']);
    }

    /**
     * Constant-time string comparison
     */
    public static function safeEquals($a, $b)
    {
        if (function_exists('hash_equals')) return hash_equals((string)$a, (string)$b);
        $a = (string)$a; $b = (string)$b;
        if (strlen($a) !== strlen($b)) return false;
        $r = 0;
        for ($i = 0, $n = strlen($a); $i < $n; $i++) $r |= ord($a[$i]) ^ ord($b[$i]);
        return $r === 0;
    }

    /**
     * Validate SSO token (used by api/codega-sso.php)
     * 
     * @return array|false  ['email' => ...] on success, false on failure
     */
    public static function validateSso($email, $ts, $nonce, $sig)
    {
        if (!self::enabled())   return false;
        if (!$email || !$ts || !$nonce || !$sig) return false;

        // Replay window
        if (abs(time() - (int)$ts) > self::MAX_SKEW) return false;

        // Nonce uniqueness
        if (self::nonceUsed($nonce)) return false;

        $expected = hash_hmac('sha256', $email . '|' . $ts . '|' . $nonce, self::secret());
        if (!self::safeEquals($expected, $sig)) return false;

        self::storeNonce($nonce, $ts);
        return ['email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false];
    }

    /**
     * Validate API request (used by api/codega-api.php)
     */
    public static function validateApi($method, $path, $body)
    {
        if (!self::enabled()) return false;

        $ts    = $_SERVER['HTTP_X_CODEGA_TIMESTAMP'] ?? '';
        $nonce = $_SERVER['HTTP_X_CODEGA_NONCE']     ?? '';
        $sig   = $_SERVER['HTTP_X_CODEGA_SIGNATURE'] ?? '';

        if (!$ts || !$nonce || !$sig) return false;
        if (abs(time() - (int)$ts) > self::MAX_SKEW) return false;
        if (self::nonceUsed($nonce)) return false;

        $body_hash = hash('sha256', (string)$body);
        $payload   = strtoupper($method) . '|' . $path . '|' . $ts . '|' . $nonce . '|' . $body_hash;
        $expected  = hash_hmac('sha256', $payload, self::secret());

        if (!self::safeEquals($expected, $sig)) return false;

        self::storeNonce($nonce, $ts);
        return true;
    }

    /**
     * Nonce replay protection — keeps last 1000 nonces in cache
     */
    protected static function nonceFile()
    {
        $dir = defined('CACHE_DIR') ? CACHE_DIR : sys_get_temp_dir() . '/';
        return rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . self::NONCE_CACHE;
    }

    protected static function loadNonces()
    {
        $f = self::nonceFile();
        if (!file_exists($f)) return [];
        $data = @file_get_contents($f);
        if (!$data) return [];
        $arr = @json_decode($data, true);
        return is_array($arr) ? $arr : [];
    }

    protected static function nonceUsed($nonce)
    {
        $nonces = self::loadNonces();
        return isset($nonces[$nonce]);
    }

    protected static function storeNonce($nonce, $ts)
    {
        $nonces = self::loadNonces();
        // Garbage collect entries older than 2x skew
        $cutoff = time() - (self::MAX_SKEW * 2);
        foreach ($nonces as $k => $t) {
            if ($t < $cutoff) unset($nonces[$k]);
        }
        $nonces[$nonce] = (int)$ts;

        // Cap at 1000 entries
        if (count($nonces) > 1000) {
            asort($nonces);
            $nonces = array_slice($nonces, -1000, null, true);
        }

        @file_put_contents(self::nonceFile(), json_encode($nonces), LOCK_EX);
    }

    /**
     * Generate the URL on codega.com.tr that should redirect users back here.
     * Used for "Login with codega.com.tr" links.
     */
    public static function loginRedirectUrl()
    {
        $settings = Config::get("theme/settings") ?: [];
        $main = rtrim($settings['codega_main_url'] ?? 'https://codega.com.tr', '/');
        $back = rtrim(Config::get("settings/site-url") ?: 'https://ca.codega.com.tr', '/') . '/codega-sso/return';
        return $main . '/sso/redirect?return=' . urlencode($back);
    }
}
