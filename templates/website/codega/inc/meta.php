<?php
/**
 * CODEGA Theme - HTML head meta tags
 * Included from every page
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$_lang   = $this->language ?? [];
$_config = $this->config ?? [];
$_settings = $_config['settings'] ?? [];

// Defensive WiseCP API access
$cg_get = function ($key, $default = '') {
    if (class_exists('Config') && method_exists('Config', 'get')) {
        $val = Config::get($key);
        if ($val) return $val;
    }
    return $default;
};

$cg_clang = 'tr';
if (class_exists('Bootstrap') && isset(Bootstrap::$lang) && !empty(Bootstrap::$lang->clang)) {
    $cg_clang = Bootstrap::$lang->clang;
}

$cg_title    = $title       ?? $cg_get("settings/title", "CODEGA");
$cg_desc     = $description ?? $cg_get("settings/description", "CODEGA — Konya merkezli web çözümleri, kurumsal hosting ve özel yazılım hizmetleri.");
$cg_keywords = $keywords    ?? $cg_get("settings/keywords", "hosting, codega, web yazılım, kurumsal hosting, konya");

$cg_meta_color = $_settings['meta-color'] ?? '#0a1628';
$cg_url        = rtrim($cg_get("settings/site-url", "https://ca.codega.com.tr"), "/");
$cg_canonical  = $cg_url . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($cg_clang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="<?= htmlspecialchars($cg_meta_color) ?>">

    <title><?= htmlspecialchars($cg_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($cg_desc) ?>">
    <meta name="keywords"    content="<?= htmlspecialchars($cg_keywords) ?>">
    <meta name="author"      content="CODEGA">

    <link rel="canonical" href="<?= htmlspecialchars($cg_canonical) ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= htmlspecialchars($cg_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($cg_desc) ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($cg_canonical) ?>">
    <meta property="og:site_name"   content="CODEGA">
    <meta property="og:image"       content="<?= $cg_url ?>/templates/website/codega/img/og-cover.png">
    <meta property="og:locale"      content="tr_TR">

    <!-- Twitter -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($cg_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($cg_desc) ?>">
    <meta name="twitter:image"       content="<?= $cg_url ?>/templates/website/codega/img/og-cover.png">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= $cg_url ?>/templates/website/codega/img/favicon.svg">
    <link rel="apple-touch-icon" href="<?= $cg_url ?>/templates/website/codega/img/apple-touch-icon.png">

    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Theme stylesheet (dynamic) -->
    <link rel="stylesheet" href="<?= $cg_url ?>/templates/website/codega/css/wisecp.css">

    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
