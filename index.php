<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$domain_url   = cdg_link('domain');
$hosting_url  = cdg_link('products', ['hosting']);
$server_url   = cdg_link('products', ['server']);
$ssl_url      = cdg_link('ssl');
$contact_url  = cdg_link('contact');
$register_url = cdg_link('register');
$login_url    = cdg_link('login');

$mod_hosting = isset($pg_activation['hosting']) ? !empty($pg_activation['hosting']) : true;
$mod_server  = isset($pg_activation['server'])  ? !empty($pg_activation['server'])  : true;
$mod_domain  = isset($pg_activation['domain'])  ? !empty($pg_activation['domain'])  : true;

// Popüler TLD'ler
$popular_tlds = [
    ['ext' => '.com.tr',  'price' => '199', 'old' => '249', 'badge' => 'POPULER',  'badge_color' => 'gold'],
    ['ext' => '.com',     'price' => '299', 'old' => '349', 'badge' => '%15 INDIRIM', 'badge_color' => 'blue'],
    ['ext' => '.net',     'price' => '349', 'old' => '399', 'badge' => '',         'badge_color' => ''],
    ['ext' => '.org',     'price' => '329', 'old' => '379', 'badge' => '',         'badge_color' => ''],
    ['ext' => '.tr',      'price' => '149', 'old' => '199', 'badge' => 'YENI',     'badge_color' => 'green'],
    ['ext' => '.xyz',     'price' => '99',  'old' => '149', 'badge' => 'UCUZ',     'badge_color' => 'purple'],
];

// 4 Hosting paketi
$hosting_packages = [
    [
        'name' => 'Baslangic', 'subtitle' => 'Bireysel siteler icin',
        'icon' => 'bi-rocket-takeoff', 'old' => '99', 'price' => '49,99', 'period' => 'aylik',
        'features' => ['1 Web Sitesi', '10 GB NVMe SSD', '1 Core Islemci', '1 GB RAM', 'Ucretsiz SSL', 'Gunluk Yedekleme', '7/24 Destek'],
        'highlight' => false,
    ],
    [
        'name' => 'cPanel', 'subtitle' => 'Kisisel + kucuk isletme',
        'icon' => 'bi-hdd-rack', 'old' => '149', 'price' => '79,99', 'period' => 'aylik',
        'features' => ['3 Web Sitesi', 'Sinirsiz NVMe SSD', '2 Core Islemci', '2 GB RAM', 'LiteSpeed', 'Gunluk Yedekleme', 'cPanel Lisansi', 'Ucretsiz Tasima'],
        'highlight' => true,
    ],
    [
        'name' => 'Kurumsal', 'subtitle' => 'Sirket ve e-ticaret',
        'icon' => 'bi-building-fill-gear', 'old' => '299', 'price' => '189,99', 'period' => 'aylik',
        'features' => ['10 Web Sitesi', 'Sinirsiz NVMe SSD', '4 Core Islemci', '4 GB RAM', 'LiteSpeed Enterprise', 'Saatlik Yedekleme', 'Oncelikli Destek', 'Dedicated IP'],
        'highlight' => false,
    ],
    [
        'name' => 'Reseller', 'subtitle' => 'Bayilik ve ajans',
        'icon' => 'bi-people-fill', 'old' => '599', 'price' => '349,99', 'period' => 'aylik',
        'features' => ['25 cPanel Hesabi', '100 GB NVMe SSD', 'WHM Yonetim', '2 GB RAM (site basi)', 'Ucretsiz SSL (her hesap)', '7/24 Destek', 'White Label'],
        'highlight' => false,
    ],
];

// Sektörel çözümler
$solutions = [
    ['icon' => 'bi-cart3',           'title' => 'E-Ticaret',     'desc' => 'WooCommerce, OpenCart, PrestaShop optimize',     'color' => '#10b981'],
    ['icon' => 'bi-wordpress',       'title' => 'WordPress',     'desc' => 'Tek tikla kurulum + WP-CLI + auto update',       'color' => '#3b82f6'],
    ['icon' => 'bi-buildings',       'title' => 'Kurumsal',      'desc' => 'Yuksek trafik + dedicated kaynak + SLA',         'color' => '#8b5cf6'],
    ['icon' => 'bi-mortarboard-fill','title' => 'Egitim',        'desc' => 'Moodle, Open edX, BBB optimize edilmis',         'color' => '#f59e0b'],
    ['icon' => 'bi-newspaper',       'title' => 'Haber/Blog',    'desc' => 'Yuksek trafik + CDN + cache layer',              'color' => '#ec4899'],
    ['icon' => 'bi-stack-overflow',  'title' => 'Yazilimci',     'desc' => 'Git, Composer, Node, SSH erisimi',               'color' => '#06b6d4'],
];

// Avantajlar (8 özellik)
$advantages = [
    ['icon' => 'bi-lightning-charge-fill',  'title' => '%100 NVMe SSD',         'desc' => 'NVMe disklerle 10x daha hizli I/O performansi.'],
    ['icon' => 'bi-speedometer2',           'title' => 'LiteSpeed Enterprise',  'desc' => 'Apache\'den 9 kat hizli, kaynak verimli web server.'],
    ['icon' => 'bi-shield-fill-check',      'title' => 'Ucretsiz SSL',          'desc' => 'Tum paketlerde Let\'s Encrypt SSL otomatik kurulur.'],
    ['icon' => 'bi-arrow-clockwise',        'title' => 'Saatlik Yedekleme',     'desc' => 'Verileriniz saat basi yedeklenir, anlik geri yukleme.'],
    ['icon' => 'bi-cloud-arrow-up-fill',    'title' => 'Ucretsiz Tasima',       'desc' => 'Mevcut sitenizi ucretsiz tasiyalim, kesintisiz.'],
    ['icon' => 'bi-fingerprint',            'title' => 'DDoS Korumasi',         'desc' => 'Gelismis firewall + 24/7 saldiri izleme.'],
    ['icon' => 'bi-graph-up-arrow',         'title' => '%99.99 Uptime',         'desc' => 'Yedekli altyapi + N+1 redundancy + monitoring.'],
    ['icon' => 'bi-headset',                'title' => '7/24 Destek',           'desc' => 'WhatsApp, telefon ve panel uzerinden 7/24.'],
];

// Müşteri yorumları
$testimonials = [
    ['name' => 'Ahmet Y.', 'company' => 'Akinsoft Bayisi',         'avatar' => 'A', 'rating' => 5, 'text' => 'CODEGA ile hosting deneyimimiz mukemmel. Sitelerimiz 3 kat hizlandi, destek ekibi her zaman ulasilabilir.'],
    ['name' => 'Mehmet K.', 'company' => 'E-ticaret Magazasi',     'avatar' => 'M', 'rating' => 5, 'text' => 'Onceki sirketten gelirken kaygiliyduk ama gecis ucretsiz ve kesintisizdi. Performans %200 arttı.'],
    ['name' => 'Ayse D.',  'company' => 'Egitim Platformu',        'avatar' => 'A', 'rating' => 5, 'text' => 'Moodle platformumuzu kurumsal pakete tasidik. 5000 kullaniciyi sorunsuz karsiliyoruz, cok memnunuz.'],
];

// Stats
$stats = [
    ['num' => '15.000+', 'label' => 'Aktif Web Sitesi',  'icon' => 'bi-globe2'],
    ['num' => '7.500+',  'label' => 'Kayitli Domain',    'icon' => 'bi-tag-fill'],
    ['num' => '12.000+', 'label' => 'Mutlu Musteri',     'icon' => 'bi-people-fill'],
    ['num' => '%99.99',  'label' => 'Uptime Garantisi',  'icon' => 'bi-shield-check'],
];

// FAQ
$faqs = [
    ['q' => 'Hosting hizmetinde performans nasil saglanir?', 'a' => 'Tum sunucularimizda NVMe SSD diskler, LiteSpeed Enterprise web server, son nesil Intel/AMD islemciler ve ECC RAM kullaniliyor. Bu altyapi ile siteleriniz Apache\'ye gore 9 kat daha hizli yuklenir.'],
    ['q' => 'Mevcut sitemi CODEGA\'ya nasil tasirim?',         'a' => 'Hosting paketinizi aldiktan sonra panel uzerinden tasima talebi olusturabilirsiniz. Uzman ekibimiz cPanel, Plesk veya direct hosting backup\'inizi alir, veri kaybi olmadan tasir. 5 adete kadar sitenizi UCRETSIZ tasiriz.'],
    ['q' => 'Ucretsiz SSL sertifikasi nasil aktif olur?',       'a' => 'Tum hosting paketlerinde Let\'s Encrypt SSL otomatik kurulur. Domain\'inizi ekledikten sonra cPanel\'de SSL sekmesinden tek tikla aktif edebilirsiniz. SSL 90 gunde bir otomatik yenilenir.'],
    ['q' => '7/24 destek hangi kanallardan saglaniyor?',         'a' => 'Whatsapp, telefon, e-posta ve panel uzerinden destek talebi acabilirsiniz. Ortalama yanit suresi 5 dakikanin altindadir. Acil durumlarda WhatsApp/telefon ile dakikalar icinde cozum.'],
    ['q' => 'Iade garantisi nasil isliyor?',                   'a' => 'Hosting hizmetinden memnun kalmamaniz halinde 30 gun icerisinde kosulsuz iade talebinde bulunabilirsiniz. Talebiniz 24 saat icinde sonuclandirilir, ucret eksiksiz iade edilir.'],
    ['q' => 'Domain transferi ucretsiz mi?',                   'a' => 'Evet, .com .net .org gibi gTLD\'ler icin transfer islemi UCRETSIZdir, ayrica 1 yil suresine ekleme yapilir. .tr ve .com.tr transferleri Trabis koordineli yurutulur.'],
];
?>

<!-- 1. ÜST DUYURU BANNER -->
<div class="cdg-top-banner">
    <div class="cdg-container">
        <i class="bi bi-megaphone-fill"></i>
        <span><strong>YENI:</strong> Tum hosting paketlerinde %30 indirim! Ilk yil icin gecerli, 30 Kasim'a kadar.</span>
        <a href="<?php echo $hosting_url; ?>" class="cdg-top-banner-cta">Hemen Al <i class="bi bi-arrow-right"></i></a>
    </div>
</div>

<!-- 2. HERO -->
<section class="cdg-hero cdg-hero-pro">
    <div class="cdg-hero-bg">
        <div class="cdg-hero-glow cdg-hero-glow-1"></div>
        <div class="cdg-hero-glow cdg-hero-glow-2"></div>
        <div class="cdg-hero-glow cdg-hero-glow-3"></div>
        <div class="cdg-hero-grid-pattern"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-hero-grid">
            <div class="cdg-hero-content">
                <div class="cdg-hero-eyebrow">
                    <span class="cdg-hero-pulse"></span>
                    <span>Turkiye'nin Premium Hosting Saglayicisi</span>
                </div>
                <h1>Profesyonel hosting<br>icin <span class="cdg-text-gradient">akilli secim</span></h1>
                <p class="cdg-hero-lead">CODEGA ile siteleriniz <strong>9 kat daha hizli</strong>, <strong>%99.99 uptime</strong> garantili. NVMe SSD, LiteSpeed Enterprise ve 7/24 uzman destegi ile premium hosting deneyimi.</p>

                <?php if($mod_domain): ?>
                <form action="<?php echo $domain_url; ?>" method="get" class="cdg-hero-domain">
                    <div class="cdg-hero-domain-input">
                        <i class="bi bi-search"></i>
                        <input type="text" name="domain" placeholder="alanadi.com" required>
                    </div>
                    <button type="submit" class="cdg-btn cdg-btn-primary">
                        <i class="bi bi-globe2"></i> <span>Domain Sorgula</span>
                    </button>
                </form>
                <div class="cdg-hero-popular-tlds">
                    <span class="muted">Populer:</span>
                    <a href="<?php echo $domain_url; ?>">.com.tr <strong>199 TL</strong></a>
                    <a href="<?php echo $domain_url; ?>">.com <strong>299 TL</strong></a>
                    <a href="<?php echo $domain_url; ?>">.net <strong>349 TL</strong></a>
                </div>
                <?php endif; ?>

                <div class="cdg-hero-trust">
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>%99.99 Uptime</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>30 Gun Iade</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>Ucretsiz SSL</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>Ucretsiz Tasima</span></div>
                </div>
            </div>

            <!-- 3D Hero illustrasyon -->
            <div class="cdg-hero-visual">
                <div class="cdg-hero-orb-wrap">
                    <div class="cdg-hero-orb"></div>
                    <div class="cdg-hero-ring cdg-hero-ring-1"></div>
                    <div class="cdg-hero-ring cdg-hero-ring-2"></div>
                </div>
                <div class="cdg-float-card cdg-float-card-1">
                    <div class="icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-hdd-network"></i></div>
                    <div class="body"><div class="title">Hosting</div><div class="meta">15.000+ aktif</div></div>
                </div>
                <div class="cdg-float-card cdg-float-card-2">
                    <div class="icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-globe2"></i></div>
                    <div class="body"><div class="title">Domain</div><div class="meta">7.500+ kayit</div></div>
                </div>
                <div class="cdg-float-card cdg-float-card-3">
                    <div class="icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="bi bi-server"></i></div>
                    <div class="body"><div class="title">Sunucu</div><div class="meta">250+ aktif</div></div>
                </div>
                <div class="cdg-float-card cdg-float-card-4">
                    <div class="icon" style="background:linear-gradient(135deg,#ec4899,#f472b6);"><i class="bi bi-shield-lock-fill"></i></div>
                    <div class="body"><div class="title">SSL</div><div class="meta">Ucretsiz</div></div>
                </div>
                <div class="cdg-float-stat">
                    <div class="num">%99.99</div>
                    <div class="lbl">Uptime</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. PERFORMANS GÖSTERGELERİ -->
<section class="cdg-perf-section">
    <div class="cdg-container">
        <div class="cdg-perf-grid">
            <div class="cdg-perf-card">
                <div class="cdg-perf-icon" style="color:#10b981;"><i class="bi bi-speedometer2"></i></div>
                <div class="cdg-perf-num">28<span>ms</span></div>
                <div class="cdg-perf-lbl">Ortalama Yanit</div>
                <div class="cdg-perf-desc">Sunucularimizdan ilk paket 28ms'de doner.</div>
            </div>
            <div class="cdg-perf-card">
                <div class="cdg-perf-icon" style="color:#3b82f6;"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="cdg-perf-num">%99.99<span></span></div>
                <div class="cdg-perf-lbl">Uptime Garantisi</div>
                <div class="cdg-perf-desc">SLA ile garantili kesintisiz hizmet.</div>
            </div>
            <div class="cdg-perf-card">
                <div class="cdg-perf-icon" style="color:#f59e0b;"><i class="bi bi-lightning-charge-fill"></i></div>
                <div class="cdg-perf-num">9<span>x</span></div>
                <div class="cdg-perf-lbl">Daha Hizli</div>
                <div class="cdg-perf-desc">LiteSpeed ile Apache'ye gore.</div>
            </div>
            <div class="cdg-perf-card">
                <div class="cdg-perf-icon" style="color:#8b5cf6;"><i class="bi bi-clock-history"></i></div>
                <div class="cdg-perf-num">5<span>dk</span></div>
                <div class="cdg-perf-lbl">Destek Yaniti</div>
                <div class="cdg-perf-desc">Ortalama destek talebi yanit suresi.</div>
            </div>
        </div>
    </div>
</section>

<!-- 4. POPÜLER TLD'LER -->
<?php if($mod_domain): ?>
<section class="cdg-tld-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Domain Sorgulama</div>
            <h2>Hayalinizdeki <span class="cdg-text-gradient">alan adini</span> kaydedin</h2>
            <p>500+ uzanti destegi ile her ihtiyaca uygun domain. %30'a varan ilk yil indirimleri.</p>
        </div>
        <div class="cdg-tld-grid">
            <?php foreach($popular_tlds as $tld): ?>
            <a href="<?php echo $domain_url; ?>" class="cdg-tld-card">
                <?php if($tld['badge']): ?><span class="cdg-tld-badge cdg-badge-<?php echo $tld['badge_color']; ?>"><?php echo $tld['badge']; ?></span><?php endif; ?>
                <div class="cdg-tld-ext"><?php echo $tld['ext']; ?></div>
                <div class="cdg-tld-old"><?php echo $tld['old']; ?> TL</div>
                <div class="cdg-tld-price">
                    <span class="cdg-tld-amt"><?php echo $tld['price']; ?></span>
                    <span class="cdg-tld-curr">TL</span>
                </div>
                <div class="cdg-tld-period">/yil</div>
                <div class="cdg-tld-cta">Sorgula <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. HOSTING PAKETLERİ -->
<?php if($mod_hosting): ?>
<section class="cdg-pricing-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Paketleri</div>
            <h2>Her butceye uygun <span class="cdg-text-gradient">premium hosting</span></h2>
            <p>NVMe SSD + LiteSpeed Enterprise altyapisi ile sektorun en hizli hosting paketleri.</p>
        </div>
        <div class="cdg-pricing-grid">
            <?php foreach($hosting_packages as $pkg): ?>
            <div class="cdg-price-card<?php echo $pkg['highlight'] ? ' cdg-price-card-highlight' : ''; ?>">
                <?php if($pkg['highlight']): ?><div class="cdg-price-ribbon">EN POPULER</div><?php endif; ?>
                <div class="cdg-price-icon"><i class="bi <?php echo $pkg['icon']; ?>"></i></div>
                <h3 class="cdg-price-name"><?php echo $pkg['name']; ?></h3>
                <p class="cdg-price-subtitle"><?php echo $pkg['subtitle']; ?></p>
                <div class="cdg-price-amount">
                    <span class="cdg-price-old"><?php echo $pkg['old']; ?> TL</span>
                    <div class="cdg-price-current">
                        <span class="cdg-price-curr">TL</span>
                        <span class="cdg-price-num"><?php echo $pkg['price']; ?></span>
                    </div>
                    <span class="cdg-price-period">/<?php echo $pkg['period']; ?></span>
                </div>
                <ul class="cdg-price-features">
                    <?php foreach($pkg['features'] as $feat): ?>
                    <li><i class="bi bi-check-circle-fill"></i> <?php echo $feat; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo $hosting_url; ?>" class="cdg-btn <?php echo $pkg['highlight'] ? 'cdg-btn-primary' : 'cdg-btn-outline'; ?> cdg-btn-block">
                    <i class="bi bi-cart-plus"></i> Hemen Satin Al
                </a>
                <?php if($pkg['highlight']): ?>
                <div class="cdg-price-guarantee"><i class="bi bi-shield-check"></i> 30 gun para iade garantisi</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. WORDPRESS HOSTING ÖZEL BÖLÜMÜ -->
<section class="cdg-wp-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-wp-grid">
            <div class="cdg-wp-content">
                <div class="cdg-wp-logo">
                    <i class="bi bi-wordpress"></i>
                    <span>WordPress Hosting</span>
                </div>
                <h2>WordPress icin <span class="cdg-text-gradient">optimize edilmis</span> hosting</h2>
                <p class="cdg-wp-lead">CODEGA WordPress hosting paketleri ile siteniz <strong>3-5 kat daha hizli</strong> calisir. Tek tikla kurulum, otomatik guncelleme ve uzman desteği.</p>
                <div class="cdg-wp-features">
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>Tek Tikla Kurulum</strong><span>Softaculous ile dakikalar icinde aktif.</span></div></div>
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>Auto Update</strong><span>Core, theme ve eklenti otomatik guncelleme.</span></div></div>
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>WP-CLI Erisim</strong><span>SSH/Terminal uzerinden komut satiri.</span></div></div>
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>Staging Site</strong><span>Test ortami + 1 tik production'a aktarma.</span></div></div>
                </div>
                <a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-primary"><i class="bi bi-arrow-right-circle"></i> WordPress Paketleri</a>
            </div>
            <div class="cdg-wp-visual">
                <div class="cdg-wp-mockup">
                    <div class="cdg-wp-mockup-bar">
                        <span></span><span></span><span></span>
                        <div class="cdg-wp-mockup-url">siteniz.com</div>
                    </div>
                    <div class="cdg-wp-mockup-body">
                        <div class="cdg-wp-perf">
                            <div class="num">A+</div>
                            <div class="lbl">PageSpeed Score</div>
                        </div>
                        <div class="cdg-wp-bars">
                            <div class="cdg-wp-bar"><div class="lbl">FCP</div><div class="val">0.8s</div><div class="track"><div class="fill" style="width:90%;"></div></div></div>
                            <div class="cdg-wp-bar"><div class="lbl">LCP</div><div class="val">1.2s</div><div class="track"><div class="fill" style="width:85%;"></div></div></div>
                            <div class="cdg-wp-bar"><div class="lbl">TTI</div><div class="val">1.5s</div><div class="track"><div class="fill" style="width:80%;"></div></div></div>
                            <div class="cdg-wp-bar"><div class="lbl">CLS</div><div class="val">0.01</div><div class="track"><div class="fill" style="width:95%;"></div></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. SEKTÖREL ÇÖZÜMLER -->
<section class="cdg-solutions-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sektorel Cozumler</div>
            <h2>Ihtiyaciniza ozel <span class="cdg-text-gradient">hosting cozumleri</span></h2>
            <p>E-ticaretten egitim platformlarina, her sektor icin optimize edilmis altyapi ve destek.</p>
        </div>
        <div class="cdg-solutions-grid">
            <?php foreach($solutions as $sol): ?>
            <a href="<?php echo $hosting_url; ?>" class="cdg-solution-card">
                <div class="cdg-solution-icon" style="background:linear-gradient(135deg,<?php echo $sol['color']; ?>,<?php echo $sol['color']; ?>cc);"><i class="bi <?php echo $sol['icon']; ?>"></i></div>
                <h3><?php echo $sol['title']; ?></h3>
                <p><?php echo $sol['desc']; ?></p>
                <div class="cdg-solution-cta">Detaylar <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. SUNUCU PAKETLERİ -->
<?php if($mod_server): ?>
<section class="cdg-server-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sanal & Fiziksel Sunucular</div>
            <h2>Yuksek performansli <span class="cdg-text-gradient">sunucu cozumleri</span></h2>
            <p>VPS, VDS ve Dedicated Server seceneklerimiz - Intel Xeon ve AMD EPYC islemciler ile.</p>
        </div>
        <div class="cdg-server-grid">
            <a href="<?php echo $server_url; ?>" class="cdg-server-card">
                <div class="cdg-server-card-icon"><i class="bi bi-cloud-fill"></i></div>
                <h3>VPS Sunucu</h3>
                <p>KVM tabanli sanal sunucu, root erisimi, snapshot.</p>
                <ul class="cdg-server-spec-list">
                    <li><i class="bi bi-cpu"></i> 2 vCPU (Intel Xeon)</li>
                    <li><i class="bi bi-memory"></i> 4 GB DDR4 RAM</li>
                    <li><i class="bi bi-hdd"></i> 60 GB NVMe SSD</li>
                    <li><i class="bi bi-globe-asia-australia"></i> 1 Gbps + 5 TB</li>
                </ul>
                <div class="cdg-server-card-price">79 TL <span>/ay'dan</span></div>
                <div class="cdg-server-card-cta">Detaylar <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="<?php echo $server_url; ?>" class="cdg-server-card cdg-server-card-featured">
                <span class="cdg-server-card-badge">EN POPULER</span>
                <div class="cdg-server-card-icon"><i class="bi bi-server"></i></div>
                <h3>VDS Sunucu</h3>
                <p>Dedicated kaynak, AMD EPYC + NVMe storage.</p>
                <ul class="cdg-server-spec-list">
                    <li><i class="bi bi-cpu"></i> 4 vCPU (AMD EPYC)</li>
                    <li><i class="bi bi-memory"></i> 8 GB DDR4 ECC</li>
                    <li><i class="bi bi-hdd"></i> 120 GB NVMe SSD</li>
                    <li><i class="bi bi-globe-asia-australia"></i> 1 Gbps + 10 TB</li>
                </ul>
                <div class="cdg-server-card-price">199 TL <span>/ay'dan</span></div>
                <div class="cdg-server-card-cta">Detaylar <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="<?php echo $server_url; ?>" class="cdg-server-card">
                <div class="cdg-server-card-icon"><i class="bi bi-hdd-stack-fill"></i></div>
                <h3>Dedicated</h3>
                <p>Komple fiziksel sunucu, full erisim, RAID 1.</p>
                <ul class="cdg-server-spec-list">
                    <li><i class="bi bi-cpu"></i> Intel Xeon 8C/16T</li>
                    <li><i class="bi bi-memory"></i> 64 GB DDR4 ECC</li>
                    <li><i class="bi bi-hdd"></i> 2x 1 TB NVMe RAID 1</li>
                    <li><i class="bi bi-globe-asia-australia"></i> 1 Gbps unmetered</li>
                </ul>
                <div class="cdg-server-card-price">2.499 TL <span>/ay'dan</span></div>
                <div class="cdg-server-card-cta">Detaylar <i class="bi bi-arrow-right"></i></div>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 9. AVANTAJLAR (8 özellik) -->
<section class="cdg-advantages-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Neden CODEGA?</div>
            <h2>Hizli, guvenli, <span class="cdg-text-gradient">profesyonel</span> hosting</h2>
            <p>Sektorde lider altyapi ve teknolojilerle hizmet veriyoruz.</p>
        </div>
        <div class="cdg-adv-grid cdg-adv-grid-4">
            <?php foreach($advantages as $adv): ?>
            <div class="cdg-adv-card">
                <div class="cdg-adv-icon"><i class="bi <?php echo $adv['icon']; ?>"></i></div>
                <h3><?php echo $adv['title']; ?></h3>
                <p><?php echo $adv['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 10. VERİ MERKEZLERİ -->
<section class="cdg-dc-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Veri Merkezleri</div>
            <h2>Tier-3 sertifikali <span class="cdg-text-gradient">veri merkezleri</span></h2>
            <p>Turkiye ve Avrupa'da yedekli altyapi ile %99.99 uptime garantisi.</p>
        </div>
        <div class="cdg-dc-grid">
            <div class="cdg-dc-card">
                <div class="cdg-dc-flag">🇹🇷</div>
                <h3>Istanbul, Turkiye</h3>
                <p>Ana veri merkezi · Tier-3 · 24/7 fiziksel guvenlik</p>
                <div class="cdg-dc-specs"><span><i class="bi bi-fire"></i> 7 dakika test ping</span><span><i class="bi bi-shield-fill-check"></i> ISO 27001</span></div>
            </div>
            <div class="cdg-dc-card">
                <div class="cdg-dc-flag">🇩🇪</div>
                <h3>Frankfurt, Almanya</h3>
                <p>Avrupa hub · Tier-3 · 100 Gbps backbone</p>
                <div class="cdg-dc-specs"><span><i class="bi bi-fire"></i> 35 dakika test ping</span><span><i class="bi bi-shield-fill-check"></i> ISO 27001</span></div>
            </div>
            <div class="cdg-dc-card">
                <div class="cdg-dc-flag">🇳🇱</div>
                <h3>Amsterdam, Hollanda</h3>
                <p>AMS-IX hub · Tier-3 · DDoS korumali</p>
                <div class="cdg-dc-specs"><span><i class="bi bi-fire"></i> 42 dakika test ping</span><span><i class="bi bi-shield-fill-check"></i> ISO 27001</span></div>
            </div>
        </div>
    </div>
</section>

<!-- 11. İSTATİSTİKLER (gradient banner) -->
<section class="cdg-stats-section">
    <div class="cdg-container">
        <div class="cdg-stats-head">
            <h2>Binlerce isletmenin <span class="cdg-text-gradient-light">guvendigi</span> hosting</h2>
            <p>2017'den beri sektorun en hizli ve guvenilir saglayicisi olarak hizmet veriyoruz.</p>
        </div>
        <div class="cdg-stats-grid">
            <?php foreach($stats as $stat): ?>
            <div class="cdg-stat-banner">
                <div class="cdg-stat-banner-icon"><i class="bi <?php echo $stat['icon']; ?>"></i></div>
                <div class="cdg-stat-banner-num"><?php echo $stat['num']; ?></div>
                <div class="cdg-stat-banner-label"><?php echo $stat['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 12. MÜŞTERİ YORUMLARI -->
<section class="cdg-testimonial-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Musteri Yorumlari</div>
            <h2>Kullanicilarimiz <span class="cdg-text-gradient">ne diyor?</span></h2>
            <p>12.000+ memnun musterimiz CODEGA'yi tercih ediyor.</p>
        </div>
        <div class="cdg-testimonial-grid">
            <?php foreach($testimonials as $t): ?>
            <div class="cdg-testimonial-card">
                <div class="cdg-test-rating">
                    <?php for($i=0;$i<$t['rating'];$i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                </div>
                <p class="cdg-test-text">"<?php echo $t['text']; ?>"</p>
                <div class="cdg-test-author">
                    <div class="cdg-test-avatar"><?php echo $t['avatar']; ?></div>
                    <div>
                        <div class="cdg-test-name"><?php echo $t['name']; ?></div>
                        <div class="cdg-test-company"><?php echo $t['company']; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 13. SSS -->
<section class="cdg-faq-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-faq-grid">
            <div class="cdg-faq-intro">
                <div class="cdg-eyebrow">Sik Sorulan Sorular</div>
                <h2>Aklinizdaki <span class="cdg-text-gradient">tum sorulara cevap</span></h2>
                <p>En cok sorulan sorulari sizin icin derledik. Daha fazlasi icin destek ekibimize ulasin.</p>
                <div class="cdg-faq-cta">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary"><i class="bi bi-chat-dots-fill"></i> Bize Sorun</a>
                </div>
            </div>
            <div class="cdg-faq-list">
                <?php foreach($faqs as $i => $faq): ?>
                <details class="cdg-faq-item"<?php if($i === 0) echo ' open'; ?>>
                    <summary>
                        <span><?php echo $faq['q']; ?></span>
                        <i class="bi bi-plus-lg"></i>
                    </summary>
                    <div class="cdg-faq-answer"><?php echo $faq['a']; ?></div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 14. SERTİFİKALAR BANDI -->
<section class="cdg-certs-section">
    <div class="cdg-container">
        <div class="cdg-certs-row">
            <div class="cdg-cert-item">
                <i class="bi bi-shield-fill-check"></i>
                <div><strong>ISO 27001</strong><span>Bilgi Guvenligi Yonetim Sistemi</span></div>
            </div>
            <div class="cdg-cert-item">
                <i class="bi bi-award-fill"></i>
                <div><strong>ETBIS Kayitli</strong><span>Elektronik Ticaret Bilgi Sistemi</span></div>
            </div>
            <div class="cdg-cert-item">
                <i class="bi bi-file-earmark-lock-fill"></i>
                <div><strong>KVKK Uyumlu</strong><span>Kisisel Verileri Koruma Kanunu</span></div>
            </div>
            <div class="cdg-cert-item">
                <i class="bi bi-patch-check-fill"></i>
                <div><strong>SSL Korumali</strong><span>256-bit AES sifreleme</span></div>
            </div>
            <div class="cdg-cert-item">
                <i class="bi bi-trophy-fill"></i>
                <div><strong>2017'den Beri</strong><span>Sektorde 9 yillik tecrube</span></div>
            </div>
        </div>
    </div>
</section>

<!-- 15. DESTEK CTA -->
<section class="cdg-support-cta cdg-section">
    <div class="cdg-container">
        <div class="cdg-support-card">
            <div class="cdg-support-illu">
                <div class="cdg-support-orb"></div>
                <i class="bi bi-headset"></i>
            </div>
            <div class="cdg-support-content">
                <div class="cdg-eyebrow" style="color:rgba(255,255,255,0.85);">7/24 Destek</div>
                <h2>Yardim mi gerekiyor? <span class="cdg-text-gradient-light">Buradayiz!</span></h2>
                <p>CODEGA destek ekibi sorulariniza haftanin 7 gunu, gunun 24 saati cevap vermek icin hazir.</p>
                <div class="cdg-support-features">
                    <div class="cdg-support-feature">
                        <i class="bi bi-telephone-fill"></i>
                        <div><div class="title">Telefon Destek</div><div class="meta">0 332 909 9656</div></div>
                    </div>
                    <div class="cdg-support-feature">
                        <i class="bi bi-whatsapp"></i>
                        <div><div class="title">WhatsApp</div><div class="meta">Anlik destek hatti</div></div>
                    </div>
                    <div class="cdg-support-feature">
                        <i class="bi bi-chat-dots-fill"></i>
                        <div><div class="title">Canli Destek</div><div class="meta">Web sitesi uzerinden</div></div>
                    </div>
                </div>
                <div class="cdg-support-actions">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-white"><i class="bi bi-envelope-fill"></i> Iletisim Formu</a>
                    <a href="<?php echo $register_url; ?>" class="cdg-btn cdg-btn-ghost"><i class="bi bi-person-plus-fill"></i> Hemen Kayit Ol</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 16. SON CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Hemen Baslayin</div>
            <h2>Profesyonel hosting deneyimine<br><span class="cdg-text-gradient">CODEGA</span> ile baslayin</h2>
            <p>Bugun kayit olun, %30 indirim + ucretsiz domain + 30 gun para iade garantisi.</p>
            <div class="cdg-final-cta-actions">
                <?php if($mod_hosting): ?>
                <a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg"><i class="bi bi-rocket-takeoff-fill"></i> Hosting Al</a>
                <?php endif; ?>
                <?php if($mod_domain): ?>
                <a href="<?php echo $domain_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-globe2"></i> Domain Sorgula</a>
                <?php endif; ?>
            </div>
            <div class="cdg-final-trust">
                <span><i class="bi bi-shield-check"></i> %99.99 Uptime</span>
                <span><i class="bi bi-arrow-counterclockwise"></i> 30 Gun Iade</span>
                <span><i class="bi bi-headset"></i> 7/24 Destek</span>
                <span><i class="bi bi-gift-fill"></i> Ucretsiz Tasima</span>
            </div>
        </div>
    </div>
</section>

<!-- FLOATING WHATSAPP BUTON -->
<a href="https://wa.me/903329099656" class="cdg-floating-wa" target="_blank" rel="noopener" title="WhatsApp Destek">
    <i class="bi bi-whatsapp"></i>
    <span class="cdg-floating-pulse"></span>
</a>
