<?php
/**
 * CODEGA Theme - Homepage (index.php)
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

// === DEBUG MODE (v1.0.1) ===
// Tema aktif edilemiyor / boş sayfa hatası ayıklaması için geçici.
// Sorun çözülünce bu blok kaldırılacak (v1.0.2'de).
@error_reporting(E_ALL);
@ini_set('display_errors', '1');
@ini_set('log_errors', '1');
$cg_debug_marker = "<!-- CODEGA THEME v1.0.1 — pages/index.php LOADED " . date('Y-m-d H:i:s') . " — \$params=" . json_encode($params ?? []) . " -->\n";
echo $cg_debug_marker;
// === END DEBUG ===

$title = "CODEGA — Premium Hosting ve Web Çözümleri";
$description = "Konya merkezli profesyonel web çözümleri. Kurumsal hosting, domain tescili ve özel yazılım hizmetleri. %99.9 uptime garantisi, 7/24 destek.";

try {
    include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';
} catch (\Throwable $e) {
    echo '<div style="background:#fee;border:2px solid #c00;padding:16px;margin:16px;font-family:monospace;">META ERROR: ' . htmlspecialchars($e->getMessage()) . ' @ ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</div>';
}
?>
<body>

<?php
try {
    include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'header.php';
} catch (\Throwable $e) {
    echo '<div style="background:#fee;border:2px solid #c00;padding:16px;margin:16px;font-family:monospace;">HEADER ERROR: ' . htmlspecialchars($e->getMessage()) . ' @ ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</div>';
}
?>

<!-- ===== HERO ===== -->
<section class="cg-hero">
    <div class="cg-container">
        <div class="cg-hero-inner cg-fade-up">
            <span class="cg-eyebrow" style="color: var(--cg-gold);">Konya · CODEGA · 2026</span>
            <h1>İşinizin omurgasını kuran <em>premium altyapı</em>.</h1>
            <p class="cg-hero-lead">
                Kurumsal projeleriniz için saniyeler içinde devreye giren hosting, alan adı ve
                özel yazılım hizmetleri. SSD depolama, LiteSpeed sunucular ve özel destek ekibi.
            </p>
            <div class="cg-hero-cta">
                <a href="/store/hosting" class="cg-btn cg-btn-primary cg-btn-lg">
                    Hosting Paketleri
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="/store/domain" class="cg-btn cg-btn-secondary cg-btn-lg">Domain Sorgula</a>
            </div>
        </div>

        <div class="cg-hero-stats">
            <div>
                <div class="cg-hero-stat-num">99.9%</div>
                <div class="cg-hero-stat-label">Uptime Garantisi</div>
            </div>
            <div>
                <div class="cg-hero-stat-num">7/24</div>
                <div class="cg-hero-stat-label">Türkçe Destek</div>
            </div>
            <div>
                <div class="cg-hero-stat-num">&lt;200ms</div>
                <div class="cg-hero-stat-label">Sunucu Tepki Süresi</div>
            </div>
            <div>
                <div class="cg-hero-stat-num">10+</div>
                <div class="cg-hero-stat-label">Yıl Tecrübe</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURES ===== -->
<section class="cg-section" style="background: var(--cg-bg-soft);">
    <div class="cg-container">
        <div style="text-align:center; max-width:680px; margin: 0 auto 56px;">
            <span class="cg-eyebrow">Neden CODEGA?</span>
            <h2 style="margin-top: 14px;">Sadece <em class="cg-display">"hosting"</em> satmıyoruz.</h2>
            <p class="cg-text-muted cg-mt-2">
                Her müşterimize — kuruluş aşamasından operasyon dönemine kadar — kendi geliştirici
                ekibimizin desteğiyle hizmet veriyoruz.
            </p>
        </div>

        <div class="cg-grid cg-grid-3">
            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <h3>NVMe SSD + LiteSpeed</h3>
                <p class="cg-text-muted cg-mt-2">
                    Yüksek hızlı NVMe disklerde barınan siteleriniz, LiteSpeed Web Server ve LSCache
                    ile WordPress'te 5x daha hızlı yüklenir.
                </p>
            </div>

            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Ücretsiz SSL & Yedekleme</h3>
                <p class="cg-text-muted cg-mt-2">
                    Tüm paketlerde Let's Encrypt SSL otomatik kurulu. Günlük otomatik yedek + 7 gün
                    geri yükleme penceresi standart.
                </p>
            </div>

            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <h3>Geliştirici Desteği</h3>
                <p class="cg-text-muted cg-mt-2">
                    Sıradan "yardım masası" değil. Sorunlarınıza PHP, MySQL ve sunucu seviyesinde
                    hakim, sertifikalı CODEGA mühendisleri yanıt verir.
                </p>
            </div>

            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>1 Saat İçinde Aktivasyon</h3>
                <p class="cg-text-muted cg-mt-2">
                    Ödeme onayından sonra hesabınız 1 saat içinde aktif edilir. Mesai dışı sipariş
                    veriler için bile ortalama açılış süresi 90 dakika.
                </p>
            </div>

            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <h3>Gerçek Zamanlı İzleme</h3>
                <p class="cg-text-muted cg-mt-2">
                    Tüm sunucularımız 60 saniyelik aralıklarla izlenir. Olası kesintilerde size haber
                    vermeden önce ekibimiz müdahale eder.
                </p>
            </div>

            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                </div>
                <h3>Türkçe + 7/24 Destek</h3>
                <p class="cg-text-muted cg-mt-2">
                    Telefon, e-posta, canlı destek ve panel tickets. Pazar günü dahil 7/24, her zaman
                    Türkçe ve insan tarafından yanıtlanır.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ===== HOSTING PRICING ===== -->
<section class="cg-section">
    <div class="cg-container">
        <div style="text-align:center; max-width:680px; margin: 0 auto 56px;">
            <span class="cg-eyebrow">Hosting Paketleri</span>
            <h2 style="margin-top: 14px;">Boyutuna göre <em class="cg-display">tam oturan</em> bir paket.</h2>
            <p class="cg-text-muted cg-mt-2">
                Aylık veya yıllık ödeme. İstediğiniz zaman yükseltin, KDV dahil net fiyatlar.
            </p>
        </div>

        <div class="cg-grid cg-grid-3">

            <div class="cg-pricing-card">
                <div class="cg-pricing-name">Başlangıç</div>
                <div class="cg-pricing-price">
                    <span class="currency">₺</span>49<span style="font-size:1.25rem;color:var(--cg-text-muted);font-family:var(--cg-font-body);">/ay</span>
                </div>
                <div class="cg-pricing-period">Yıllık ödendiğinde ₺39/ay</div>
                <ul class="cg-pricing-features">
                    <li>5 GB NVMe SSD Alan</li>
                    <li>Sınırsız Aylık Trafik</li>
                    <li>3 Adet E-posta Hesabı</li>
                    <li>Ücretsiz SSL</li>
                    <li>Günlük Yedekleme</li>
                </ul>
                <a href="/cart/?a=add&pid=1" class="cg-btn cg-btn-ghost cg-btn-block">Satın Al</a>
            </div>

            <div class="cg-pricing-card featured">
                <div class="cg-badge">En Popüler</div>
                <div class="cg-pricing-name">Profesyonel</div>
                <div class="cg-pricing-price">
                    <span class="currency">₺</span>99<span style="font-size:1.25rem;color:var(--cg-text-muted);font-family:var(--cg-font-body);">/ay</span>
                </div>
                <div class="cg-pricing-period">Yıllık ödendiğinde ₺79/ay</div>
                <ul class="cg-pricing-features">
                    <li>25 GB NVMe SSD Alan</li>
                    <li>Sınırsız Aylık Trafik</li>
                    <li>Sınırsız E-posta Hesabı</li>
                    <li>Ücretsiz SSL + Wildcard</li>
                    <li>2x Günlük Yedekleme</li>
                    <li>LiteSpeed + LSCache</li>
                    <li>Öncelikli Destek</li>
                </ul>
                <a href="/cart/?a=add&pid=2" class="cg-btn cg-btn-primary cg-btn-block">Satın Al</a>
            </div>

            <div class="cg-pricing-card">
                <div class="cg-pricing-name">Kurumsal</div>
                <div class="cg-pricing-price">
                    <span class="currency">₺</span>199<span style="font-size:1.25rem;color:var(--cg-text-muted);font-family:var(--cg-font-body);">/ay</span>
                </div>
                <div class="cg-pricing-period">Yıllık ödendiğinde ₺159/ay</div>
                <ul class="cg-pricing-features">
                    <li>100 GB NVMe SSD Alan</li>
                    <li>Sınırsız Aylık Trafik</li>
                    <li>Sınırsız E-posta + Office365 Bağlama</li>
                    <li>Ücretsiz SSL + Wildcard</li>
                    <li>Saatlik Yedekleme</li>
                    <li>LiteSpeed Enterprise</li>
                    <li>Adanmış Destek Yöneticisi</li>
                </ul>
                <a href="/cart/?a=add&pid=3" class="cg-btn cg-btn-ghost cg-btn-block">Satın Al</a>
            </div>

        </div>
    </div>
</section>

<!-- ===== CTA STRIP ===== -->
<section style="background: var(--cg-navy); color: white; padding: 64px 0; position:relative; overflow:hidden;">
    <div class="cg-container" style="position:relative; z-index:1;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px;">
            <div style="max-width:560px;">
                <h2 style="color:white; font-weight:400; margin-bottom:8px;">Hazırsanız <em class="cg-display" style="color:var(--cg-gold);">başlayalım</em>.</h2>
                <p style="color:rgba(255,255,255,0.7); font-size:1rem;">
                    Mevcut hosting'inizden taşınma süreci ücretsiz. Bir saat içinde sitelerinize tekrar erişin.
                </p>
            </div>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="/contact" class="cg-btn cg-btn-primary cg-btn-lg">Ücretsiz Taşıma Başlat</a>
                <a href="/store/hosting" class="cg-btn cg-btn-secondary cg-btn-lg">Paketleri İncele</a>
            </div>
        </div>
    </div>
    <div style="position:absolute; right:-100px; top:-100px; width:400px; height:400px; background: radial-gradient(circle, rgba(212,165,116,0.15), transparent 60%); pointer-events:none;"></div>
</section>

<?php
try {
    include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php';
} catch (\Throwable $e) {
    echo '<div style="background:#fee;border:2px solid #c00;padding:16px;margin:16px;font-family:monospace;">FOOTER ERROR: ' . htmlspecialchars($e->getMessage()) . ' @ ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</div>';
}
echo "\n<!-- CODEGA THEME — render complete -->\n";
?>
