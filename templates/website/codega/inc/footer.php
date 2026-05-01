<?php
/**
 * CODEGA Theme - Footer
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$_settings = $this->config['settings'] ?? [];
$main_url  = rtrim($_settings['codega_main_url'] ?? 'https://codega.com.tr', '/');
$year      = date('Y');
$version   = $this->config['version'] ?? '1.0.0';

$cg_site_url = 'https://ca.codega.com.tr';
if (class_exists('Config') && method_exists('Config', 'get')) {
    $tmp = Config::get("settings/site-url");
    if ($tmp) $cg_site_url = rtrim($tmp, '/');
}
?>
<footer class="cg-footer">
    <div class="cg-container">

        <div class="cg-footer-grid">
            <div>
                <a href="/" class="cg-logo" style="margin-bottom:18px;">
                    <span class="cg-logo-mark">C</span>
                    <span class="cg-logo-text">CO<span>DE</span>GA</span>
                </a>
                <p style="color:rgba(255,255,255,0.65); font-size:14px; line-height:1.65; max-width:340px;">
                    Konya merkezli profesyonel web çözümleri, kurumsal hosting ve özel yazılım hizmetleri.
                    7/24 destek, %99.9 uptime garantisi ile yanınızdayız.
                </p>
            </div>

            <div>
                <h4>Hizmetler</h4>
                <a href="/store/hosting"      class="cg-footer-link">Web Hosting</a>
                <a href="/store/domain"       class="cg-footer-link">Domain Tescil</a>
                <a href="/store/sslcerts"     class="cg-footer-link">SSL Sertifika</a>
                <a href="/store/server"       class="cg-footer-link">Sunucu Çözümleri</a>
                <a href="<?= htmlspecialchars($main_url) ?>/yazilim" class="cg-footer-link">Özel Yazılım</a>
            </div>

            <div>
                <h4>Destek</h4>
                <a href="/clientarea"            class="cg-footer-link">Müşteri Paneli</a>
                <a href="/clientarea/tickets"    class="cg-footer-link">Destek Talepleri</a>
                <a href="/contact"               class="cg-footer-link">İletişim</a>
                <a href="/announcements"         class="cg-footer-link">Duyurular</a>
                <a href="/knowledgebase"         class="cg-footer-link">Bilgi Bankası</a>
            </div>

            <div>
                <h4>Kurumsal</h4>
                <a href="<?= htmlspecialchars($main_url) ?>/hakkimizda"  class="cg-footer-link">Hakkımızda</a>
                <a href="<?= htmlspecialchars($main_url) ?>/iletisim"    class="cg-footer-link">İletişim</a>
                <a href="/legal/terms"        class="cg-footer-link">Kullanım Şartları</a>
                <a href="/legal/privacy"      class="cg-footer-link">Gizlilik Politikası</a>
                <a href="/legal/sla"          class="cg-footer-link">SLA Sözleşmesi</a>
            </div>
        </div>

        <div class="cg-footer-bottom">
            <div>
                © <?= $year ?> <a href="<?= htmlspecialchars($main_url) ?>">CODEGA</a> · Tüm hakları saklıdır.
                <span style="margin-left:12px; opacity:0.5;">Tema v<?= htmlspecialchars($version) ?></span>
            </div>
            <div style="display:flex; gap:14px; align-items:center; opacity:0.6;">
                <span style="font-size:12px;">Konya, Türkiye 🇹🇷</span>
            </div>
        </div>

    </div>
</footer>

<script src="<?= htmlspecialchars($cg_site_url) ?>/templates/website/codega/js/codega.js" defer></script>
</body>
</html>
