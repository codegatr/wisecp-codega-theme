<?php
/**
 * CODEGA Theme - Diagnostic page
 * 
 * URL: /diagnostic
 * 
 * Reports WiseCP environment status. Useful when the theme
 * isn't rendering properly. Should be accessible without auth
 * since it's used to debug the rendering itself.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

header('Content-Type: text/html; charset=UTF-8');

$checks = [
    'PHP version'             => PHP_VERSION,
    'CORE_FOLDER defined'     => defined('CORE_FOLDER') ? '✅ ' . CORE_FOLDER : '❌ NOT DEFINED',
    'DS defined'              => defined('DS') ? '✅ ' . DS : '❌ NOT DEFINED',
    'CONFIG_DIR defined'      => defined('CONFIG_DIR') ? '✅ ' . CONFIG_DIR : '❌ NOT DEFINED',
    'CACHE_DIR defined'       => defined('CACHE_DIR') ? '✅ ' . CACHE_DIR : '❌ NOT DEFINED',
    'class Bootstrap'         => class_exists('Bootstrap') ? '✅ exists' : '❌ missing',
    'class Config'            => class_exists('Config')    ? '✅ exists' : '❌ missing',
    'class Filter'            => class_exists('Filter')    ? '✅ exists' : '❌ missing',
    'class View'              => class_exists('View')      ? '✅ exists' : '❌ missing',
    'class User'              => class_exists('User')      ? '✅ exists' : '❌ missing',
    'class DB'                => class_exists('DB')        ? '✅ exists' : '❌ missing',
    'theme name'              => $this->name ?? 'unknown',
    'theme config loaded'     => !empty($this->config) ? '✅ loaded (' . count($this->config) . ' keys)' : '❌ empty',
    'theme version'           => $this->config['version'] ?? 'unknown',
    'language loaded'         => !empty($this->language) ? '✅ ' . count($this->language) . ' strings' : '❌ empty',
    'pages dir readable'      => is_readable(__DIR__) ? '✅ ' . __DIR__ : '❌',
    'index.php exists'        => file_exists(__DIR__ . '/index.php') ? '✅' : '❌',
    'css/wisecp.php exists'   => file_exists(dirname(__DIR__) . '/css/wisecp.php') ? '✅' : '❌',
    'inc/header.php exists'   => file_exists(dirname(__DIR__) . '/inc/header.php') ? '✅' : '❌',
    'codega-bridge loaded'    => class_exists('Codega_Bridge') ? '✅' : '⚠ not auto-loaded',
    '$_SESSION available'     => session_status() === PHP_SESSION_ACTIVE ? '✅ active' : '❌ not started',
    '$params (router input)'  => isset($params) ? json_encode($params) : 'not passed',
];

// Cache dir writable check
$cache_check = '?';
if (defined('CACHE_DIR') && is_dir(CACHE_DIR)) {
    $cache_check = is_writable(CACHE_DIR) ? '✅ writable' : '❌ not writable';
}
$checks['CACHE_DIR writable'] = $cache_check;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>CODEGA Theme — Diagnostic</title>
<style>
body { font-family: 'JetBrains Mono', Consolas, monospace; background: #0a1628; color: #fff; padding: 32px; line-height: 1.6; }
h1 { color: #d4a574; font-family: 'Cormorant Garamond', Georgia, serif; font-weight: 500; font-size: 2rem; }
h1 em { font-style: italic; }
table { border-collapse: collapse; width: 100%; max-width: 880px; margin-top: 24px; background: #142042; border-radius: 8px; overflow: hidden; }
th { background: #1a2851; color: #d4a574; text-align: left; padding: 12px 16px; font-weight: 600; }
td { padding: 10px 16px; border-top: 1px solid rgba(212,165,116,0.1); font-size: 13px; }
td:first-child { color: rgba(255,255,255,0.7); width: 240px; }
td:last-child { color: #fff; word-break: break-all; }
.note { color: rgba(255,255,255,0.6); margin-top: 24px; font-size: 13px; max-width: 880px; line-height: 1.7; }
.note a { color: #d4a574; }
</style>
</head>
<body>
<h1>CODEGA Theme — <em>Diagnostic</em></h1>

<table>
<thead><tr><th>Check</th><th>Status</th></tr></thead>
<tbody>
<?php foreach ($checks as $k => $v): ?>
<tr><td><?= htmlspecialchars($k) ?></td><td><?= htmlspecialchars(is_string($v) ? $v : json_encode($v)) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>

<p class="note">
Bu sayfa tema yüklendi mi diye doğrular. Tüm satırlar yeşil ✅ olduğunda anasayfa render olmalı.<br>
Sorun varsa ekran görüntüsü alıp paylaşabilirsin — log klasörünü de kontrol et: <code>logs/error.log</code><br>
<a href="/">← Anasayfaya dön</a>
</p>
</body>
</html>
