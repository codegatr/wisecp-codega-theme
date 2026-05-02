<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$contact_url = cdg_link('contact');
$hosting_url = cdg_link('products', ['hosting']);

// CODEGA YAZILIM PORTFOYU
$products = [
    [
        'id' => 'codega-erp',
        'name' => 'CodeGa ERP',
        'tagline' => 'Kurumsal Kaynak Planlama Sistemi',
        'desc' => 'Sirketinizin tum operasyonlarini tek panelden yoneten bulut tabanli ERP cozumu. Muhasebe, stok, satis, satin alma, CRM, IK, uretim ve daha fazlasi.',
        'icon' => 'bi-building-fill',
        'color' => '#1e40af',
        'highlight' => true,
        'badge' => 'AMIRAL GEMISI',
        'features' => [
            '96+ entegre modul',
            'Cari Risk Analizi',
            'GPS-tabanli PDKS / Personel takip',
            'Sevkiyat planlama (harita)',
            'Cok kullaniciu, rol-yetki sistemi',
            'Bulut tabanli, mobil uyumlu',
        ],
        'use_cases' => 'Kucuk-orta isletmeler, bayilik agi olan markalar, uretim sirketleri',
    ],
    [
        'id' => 'xnews',
        'name' => 'XNEWS',
        'tagline' => 'PHP Haber Agregator Sistemi',
        'desc' => '90+ RSS kaynagindan otomatik haber toplayan, kategorize eden ve yayinlayan profesyonel haber portali yazilimi. SEO odakli, hizli, kolay yonetilebilir.',
        'icon' => 'bi-newspaper',
        'color' => '#ec4899',
        'badge' => 'AGREGATOR',
        'features' => [
            '90+ RSS kaynagi destegi',
            'Otomatik kategori atama',
            'Cron job ile periyodik cekme',
            'Diagnostik panel',
            'SEO friendly URL\'ler',
            'Yonetim paneli',
        ],
        'use_cases' => 'Haber siteleri, blog ag\'lari, sektorel duyuru platformlari',
    ],
    [
        'id' => 'malimusafir',
        'name' => 'MaliMusafir',
        'tagline' => 'e-Tebligat Otomasyonu',
        'desc' => 'Mali musavirler icin GIB e-Tebligat otomasyonu. Mukellefleriniz icin nightly tebligat sorgulamasi, e-posta bildirimleri, son tarih takibi.',
        'icon' => 'bi-shield-fill-exclamation',
        'color' => '#10b981',
        'features' => [
            'GIB e-Tebligat otomatik sorgu',
            'Nightly cron (01:00-07:00)',
            '15 gun oncesinde e-posta uyarisi',
            'Cok mukellef destegi',
            'Karanlik tema panel',
            'Audit log',
        ],
        'use_cases' => 'Mali musavirler, mali musavirlik burolari, danismanlik firmalari',
    ],
    [
        'id' => 'kamyongaraji',
        'name' => 'KamyonGaraji',
        'tagline' => 'B2B Lojistik Marketplace',
        'desc' => 'Kamyon ve agir vasita lojistigi icin B2B platformu. Yuk-arac eslestirme, GeoIP konum, telefon gizligi, dogrulanmis uye sistemi.',
        'icon' => 'bi-truck-front-fill',
        'color' => '#f59e0b',
        'features' => [
            'GeoIP tabanli konum',
            'Telefon gizliligi',
            'Bot tespiti',
            'E-posta dogrulama',
            'Dogrulanmis uye rozeti',
            'Yuk ilan sistemi',
        ],
        'use_cases' => 'Lojistik firmalari, nakliyeci, yuk sahipleri',
    ],
    [
        'id' => 'tekcanmetal',
        'name' => 'Tekcanmetal',
        'tagline' => 'Cok Dilli E-Ticaret + Bayi Portali',
        'desc' => '5 dil destekli (TR/EN/AR/RU/FR) e-ticaret + bayi yonetim sistemi. Dinamik urun, kategori, banka entegrasyonu, fiyatlandirma.',
        'icon' => 'bi-cart-fill',
        'color' => '#06b6d4',
        'features' => [
            '5 dil destegi (TR/EN/AR/RU/FR)',
            'Bayi yonetim paneli',
            'Banka tanimlamalari',
            'Migration sistemi',
            'Audit log',
            'Cok kullaniciu',
        ],
        'use_cases' => 'Uluslararasi B2B sirketleri, ihracat odakli markalar',
    ],
    [
        'id' => 'akilliticaret',
        'name' => 'AkilliTicaret.NET',
        'tagline' => 'Multi-Vendor Marketplace',
        'desc' => 'Cok satici destekli marketplace platformu. Saticilarin kendi panellerinden urun ekleyip yonettigi, admin denetimli e-ticaret cozumu.',
        'icon' => 'bi-shop',
        'color' => '#8b5cf6',
        'features' => [
            'Cok satici destegi',
            'Rol/yetki sistemi',
            'Satici import/export',
            'Footer menu yonetimi',
            'Modern admin paneli',
            'Inter font, dark sidebar',
        ],
        'use_cases' => 'Pazaryeri kurmak isteyenler, ag isletmecileri',
    ],
    [
        'id' => 'cminer',
        'name' => 'CMiner Exchange',
        'tagline' => 'Kripto Para Borsasi',
        'desc' => 'Tam ozellikli kripto para borsasi yazilimi. Spot trading, faucet, trading bot, 2FA, AES-256-GCM sifreleme, mobil uyumlu.',
        'icon' => 'bi-currency-bitcoin',
        'color' => '#fbbf24',
        'features' => [
            'Spot trading (Grid/DCA/MM)',
            '2FA zorunlu',
            'AES-256-GCM sifreleme',
            'Faucet sistemi',
            'Trading bot',
            'Audit log + Login bildirimi',
        ],
        'use_cases' => 'Kripto para borsa sahipleri, BabaCoin gibi tokenlar icin',
    ],
    [
        'id' => 'minya3d',
        'name' => 'Minya 3D',
        'tagline' => '3D Yazici E-Ticaret',
        'desc' => '3D yazici urunleri ve filament satisi yapan e-ticaret platformu. Gram tabanli fiyatlandirma, 81 il SEO, Schema.org markup.',
        'icon' => 'bi-printer-fill',
        'color' => '#ef4444',
        'features' => [
            '125 urunluk katalog',
            'Gram tabanli fiyatlandirma',
            '81 il SEO landing',
            'WhatsApp + e-posta bildirim',
            'Schema.org markup',
            'Admin paneli',
        ],
        'use_cases' => '3D baski hizmeti veren firmalar, urun saticilari',
    ],
    [
        'id' => 'kiratakip',
        'name' => 'KiraTakipSistemi',
        'tagline' => 'Kiralama Yonetim Sistemi',
        'desc' => 'Mulk sahipleri ve emlak yonetimi sirketleri icin kira takip ve odeme yonetim sistemi. Kiraci destek talepleri, otomatik bildirim.',
        'icon' => 'bi-house-door-fill',
        'color' => '#10b981',
        'features' => [
            'Kiraci destek talepleri',
            'Odeme bildirimleri',
            'HMAC tabanli stateless CSRF',
            'Bootstrap 5.3 modern UI',
            'Detayli raporlar',
            'Cok kiralayan destegi',
        ],
        'use_cases' => 'Mulk sahipleri, emlak yonetimi, apart yoneticileri',
    ],
];

$service_categories = [
    ['icon' => 'bi-code-slash',     'title' => 'Web Uygulamalari',      'desc' => 'PHP, MySQL, Laravel ile ozel web uygulamalari.'],
    ['icon' => 'bi-phone-fill',     'title' => 'Mobil Uygulamalar',     'desc' => 'iOS ve Android icin native + cross-platform.'],
    ['icon' => 'bi-cart-check',     'title' => 'E-Ticaret Sistemleri',  'desc' => 'Multi-vendor, B2B, B2C tum e-ticaret cozumleri.'],
    ['icon' => 'bi-bar-chart-fill', 'title' => 'ERP / CRM',             'desc' => 'Kurumsal kaynak planlama ve musteri iliskileri.'],
    ['icon' => 'bi-cpu-fill',       'title' => 'API Entegrasyonlari',   'desc' => 'GIB, banka, kargo, SMS API entegrasyonlari.'],
    ['icon' => 'bi-shield-shaded',  'title' => 'Guvenlik Cozumleri',    'desc' => 'AES-256-GCM, 2FA, audit log, KVKK uyumlu.'],
];
?>

<!-- 1. PAGE HERO -->
<section class="cdg-page-hero">
    <div class="cdg-page-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
        <div class="cdg-hero-particles">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span>
        </div>
    </div>
    <div class="cdg-container">
        <div class="cdg-page-hero-content">
            <div class="cdg-eyebrow cdg-eyebrow-glow"><i class="bi bi-code-square"></i> Yazilim Cozumlerimiz</div>
            <h1>Sektorlere ozel <span class="cdg-text-gradient">yazilim</span> ve <span class="cdg-text-gradient-cyan">ERP</span> cozumleri</h1>
            <p>CODEGA olarak gelistirmis oldugumuz <strong>9+ farkli sektor</strong> icin tasarlanmis modern, olceklenebilir yazilim urunleri. Her birini kullaniyoruz, her biri canli sistemlerde test edildi.</p>
            <div class="cdg-page-hero-cta">
                <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-rocket-takeoff-fill"></i> Teklif Al</a>
                <a href="#cdg-products" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-grid-fill"></i> Urunleri Gor</a>
            </div>
        </div>
    </div>
</section>

<!-- 2. STATS -->
<section class="cdg-section" style="padding-top:48px;">
    <div class="cdg-container">
        <div class="cdg-soft-stats">
            <div class="cdg-soft-stat"><div class="num"><span class="cdg-counter" data-target="9">9</span>+</div><div class="lbl">Hazir Yazilim Urunu</div></div>
            <div class="cdg-soft-stat"><div class="num"><span class="cdg-counter" data-target="50">50</span>+</div><div class="lbl">Tamamlanmis Proje</div></div>
            <div class="cdg-soft-stat"><div class="num"><span class="cdg-counter" data-target="9">9</span></div><div class="lbl">Yillik Tecrube</div></div>
            <div class="cdg-soft-stat"><div class="num"><span class="cdg-counter" data-target="100">%100</span></div><div class="lbl">Yerli Yazilim</div></div>
        </div>
    </div>
</section>

<!-- 3. SERVICE CATEGORIES -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hizmet Alanlarimiz</div>
            <h2>Her ihtiyaca yonelik <span class="cdg-text-gradient">yazilim cozumleri</span></h2>
            <p>Web, mobil, e-ticaret, ERP — projenizi sifirdan veya hazir altyapilarimiz uzerinden gelistiriyoruz.</p>
        </div>
        <div class="cdg-solutions-grid">
            <?php foreach($service_categories as $cat): ?>
            <div class="cdg-solution-card" style="cursor:default;">
                <div class="cdg-solution-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi <?php echo $cat['icon']; ?>"></i></div>
                <h3><?php echo $cat['title']; ?></h3>
                <p><?php echo $cat['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. PRODUCT SHOWCASE -->
<section class="cdg-section" id="cdg-products" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Urunlerimiz</div>
            <h2>Hazir <span class="cdg-text-gradient">yazilim urunlerimiz</span></h2>
            <p>Sektorel ihtiyaclar icin hazir olarak gelistirdigimiz, canli sistemlerde calisan profesyonel yazilim urunleri.</p>
        </div>

        <div class="cdg-soft-products">
            <?php foreach($products as $p): ?>
            <div class="cdg-soft-product<?php echo !empty($p['highlight']) ? ' highlight' : ''; ?>">
                <?php if(!empty($p['badge'])): ?>
                <div class="cdg-soft-badge" style="background:linear-gradient(135deg,<?php echo $p['color']; ?>,<?php echo $p['color']; ?>cc);"><?php echo $p['badge']; ?></div>
                <?php endif; ?>

                <div class="cdg-soft-product-head">
                    <div class="cdg-soft-product-icon" style="background:linear-gradient(135deg,<?php echo $p['color']; ?>,<?php echo $p['color']; ?>cc);">
                        <i class="bi <?php echo $p['icon']; ?>"></i>
                    </div>
                    <div>
                        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                        <div class="cdg-soft-tagline"><?php echo htmlspecialchars($p['tagline']); ?></div>
                    </div>
                </div>

                <p class="cdg-soft-desc"><?php echo htmlspecialchars($p['desc']); ?></p>

                <ul class="cdg-soft-features">
                    <?php foreach($p['features'] as $f): ?>
                    <li><i class="bi bi-check-circle-fill" style="color:<?php echo $p['color']; ?>;"></i> <?php echo htmlspecialchars($f); ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="cdg-soft-usecase">
                    <i class="bi bi-bullseye"></i>
                    <span><strong>Hedef:</strong> <?php echo htmlspecialchars($p['use_cases']); ?></span>
                </div>

                <div class="cdg-soft-actions">
                    <a href="<?php echo $contact_url; ?>?subject=<?php echo urlencode($p['name'] . ' Hakkinda'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-block">
                        <i class="bi bi-info-circle"></i> Detayli Bilgi Al
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. PROCESS -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Calisma Surecimiz</div>
            <h2>Projeniz <span class="cdg-text-gradient">5 adimda</span> hayata geciyor</h2>
        </div>
        <div class="cdg-soft-process">
            <div class="cdg-soft-step">
                <div class="cdg-soft-step-num">01</div>
                <h3>Ihtiyac Analizi</h3>
                <p>Projenizi anlamak icin detayli toplantilar, kullanici akislari ve gereksinim analizi.</p>
            </div>
            <div class="cdg-soft-step">
                <div class="cdg-soft-step-num">02</div>
                <h3>Tasarim & Prototype</h3>
                <p>UI/UX tasarimi, wireframe, etkileskili prototipler. Onay sonrasi gelistirmeye geciyoruz.</p>
            </div>
            <div class="cdg-soft-step">
                <div class="cdg-soft-step-num">03</div>
                <h3>Gelistirme</h3>
                <p>Modern teknolojilerle, agile metodoloji ile haftalik demo'lar. Surekli geri bildirim.</p>
            </div>
            <div class="cdg-soft-step">
                <div class="cdg-soft-step-num">04</div>
                <h3>Test & Yayin</h3>
                <p>Detayli test asamalari, beta yayinlama, performans optimizasyonu, canliya alma.</p>
            </div>
            <div class="cdg-soft-step">
                <div class="cdg-soft-step-num">05</div>
                <h3>Bakim & Destek</h3>
                <p>1 yil ucretsiz bakim, 7/24 teknik destek, periyodik guncellemeler.</p>
            </div>
        </div>
    </div>
</section>

<!-- 6. WHY US -->
<section class="cdg-section" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Neden CODEGA?</div>
            <h2>Yazilim icin <span class="cdg-text-gradient">dogru ortak</span></h2>
        </div>
        <div class="cdg-adv-grid cdg-adv-grid-4">
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-trophy-fill"></i></div><h3>9 Yillik Tecrube</h3><p>2017'den beri sektorde, 50+ proje teslim ettik.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-cloud-fill"></i></div><h3>Bulut Tabanli</h3><p>Tum sistemler scalable, mobil uyumlu, modern.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-shield-fill-check"></i></div><h3>KVKK Uyumlu</h3><p>Veri guvenligi, audit log, sifreleme standardi.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-headset"></i></div><h3>Canli Destek</h3><p>WhatsApp, telefon, panel uzerinden 7/24.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-arrow-repeat"></i></div><h3>Surekli Guncelleme</h3><p>Yeni ozellikler, performans iyilestirmeleri.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-flag-fill"></i></div><h3>%100 Yerli</h3><p>Turkce arayuz, Turkiye yasal mevzuatina uygun.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-gear-fill"></i></div><h3>Ozellestirilebilir</h3><p>Sirketinize ozel modul ve raporlar gelistiriyoruz.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-rocket-takeoff-fill"></i></div><h3>Hizli Teslim</h3><p>Standart projeler 2-4 hafta, ozel projeler 1-3 ay.</p></div>
        </div>
    </div>
</section>

<!-- 7. CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Hemen Baslayin</div>
            <h2>Yazilim projeniz icin <span class="cdg-text-gradient">ucretsiz teklif</span> alin</h2>
            <p>24 saat icinde detayli teklif gonderiyoruz. Ister hazir urunlerimizden, ister sifirdan ozel gelistirme.</p>
            <div class="cdg-final-cta-actions">
                <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-envelope-paper-fill"></i> Teklif Iste</a>
                <a href="https://wa.me/903329099656" target="_blank" rel="noopener" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-whatsapp"></i> WhatsApp</a>
            </div>
            <div class="cdg-final-trust">
                <span><i class="bi bi-shield-check"></i> KVKK Uyumlu</span>
                <span><i class="bi bi-arrow-counterclockwise"></i> 1 Yil Bakim</span>
                <span><i class="bi bi-headset"></i> 7/24 Destek</span>
                <span><i class="bi bi-flag-fill"></i> %100 Yerli</span>
            </div>
        </div>
    </div>
</section>
