<?php
/**
 * CODEGA Theme - Header (top navigation)
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$_settings  = $this->config['settings'] ?? [];
$show_main_nav = !empty($_settings['show_main_site_nav']);
$main_url   = rtrim($_settings['codega_main_url'] ?? 'https://codega.com.tr', '/');

$is_logged = !empty($_SESSION['user']);
if (!$is_logged && class_exists('User') && method_exists('User', 'login_status')) {
    try { $is_logged = (bool) User::login_status(); } catch (\Throwable $e) { $is_logged = false; }
}

$current_page = '';
if (class_exists('Filter') && method_exists('Filter', 'folder')) {
    $current_page = Filter::folder($params[0] ?? '');
} else {
    $current_page = preg_replace('/[^a-z0-9_-]/i', '', $params[0] ?? '');
}
?>
<header class="cg-header">
    <div class="cg-container">
        <div class="cg-header-inner">

            <a href="/" class="cg-logo" aria-label="CODEGA">
                <span class="cg-logo-mark">C</span>
                <span class="cg-logo-text">CO<span>DE</span>GA</span>
            </a>

            <nav class="cg-nav" aria-label="Ana menü">
                <?php if ($show_main_nav): ?>
                    <a href="<?= htmlspecialchars($main_url) ?>" class="cg-nav-item">Ana Sayfa</a>
                    <a href="<?= htmlspecialchars($main_url) ?>/hakkimizda" class="cg-nav-item">Hakkımızda</a>
                    <a href="<?= htmlspecialchars($main_url) ?>/hizmetler" class="cg-nav-item">Hizmetler</a>
                <?php endif; ?>
                <a href="/store/hosting" class="cg-nav-item <?= $current_page === 'store-hosting' ? 'active' : '' ?>">Hosting</a>
                <a href="/store/domain" class="cg-nav-item <?= $current_page === 'store-domain' ? 'active' : '' ?>">Domain</a>
                <a href="/contact" class="cg-nav-item">Destek</a>
            </nav>

            <div class="cg-header-actions">
                <?php if ($is_logged): ?>
                    <a href="/clientarea" class="cg-btn cg-btn-secondary cg-btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                        Panel
                    </a>
                    <a href="/logout" class="cg-btn cg-btn-primary cg-btn-sm">Çıkış</a>
                <?php else: ?>
                    <a href="/login"    class="cg-btn cg-btn-secondary cg-btn-sm">Giriş Yap</a>
                    <a href="/register" class="cg-btn cg-btn-primary cg-btn-sm">Kayıt Ol</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>
