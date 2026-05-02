<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

// URL helper'ları
$domain_check_url = cdg_link('domain');
$hosting_url      = cdg_link('products', ['hosting']);
$server_url       = cdg_link('products', ['server']);
$sms_url          = cdg_link('products', ['sms']);
$contact_url      = cdg_link('contact');
$register_url     = cdg_link('register');
$login_url        = cdg_link('login');

// Domain ilk fiyatı
$domain_first_price = '';
if(isset($first_tld_price) && is_array($first_tld_price)) {
    $amt = isset($first_tld_price['register']['amount']) ? $first_tld_price['register']['amount'] : 0;
    $cid = isset($first_tld_price['register']['cid']) ? $first_tld_price['register']['cid'] : 0;
    if($amt && class_exists('Money') && method_exists('Money', 'formatter_symbol')) {
        $domain_first_price = Money::formatter_symbol($amt, $cid);
    }
}

// Modüller aktif mi
$mod_hosting = isset($pg_activation['hosting']) ? !empty($pg_activation['hosting']) : true;
$mod_server  = isset($pg_activation['server'])  ? !empty($pg_activation['server'])  : true;
$mod_domain  = isset($pg_activation['domain'])  ? !empty($pg_activation['domain'])  : true;
$mod_sms     = isset($pg_activation['sms'])     ? !empty($pg_activation['sms'])     : true;

// Popüler TLD'ler
$popular_tlds = [
    ['ext' => '.com.tr',  'price' => '199', 'badge' => 'POPULER'],
    ['ext' => '.com',     'price' => '299', 'badge' => '%10'],
    ['ext' => '.net',     'price' => '349', 'badge' => '%10'],
    ['ext' => '.org',     'price' => '329', 'badge' => '%10'],
    ['ext' => '.tr',      'price' => '149', 'badge' => 'YENI'],
    ['ext' => '.xyz',     'price' => '99',  'badge' => 'UCUZ'],
];

// Hosting paketleri
$hosting_packages = [
    [
        'name' => 'Baslangic',
        'subtitle' => 'Yeni baslayanlar icin',
        'icon' => 'bi-rocket-takeoff',
        'old_price' => '99', 'price' => '49,99', 'period' => 'aylik',
        'features' => ['1 Adet Web Sitesi','Sinirsiz NVMe SSD Disk','1 Core Islemci','1 GB RAM','Ucretsiz SSL','Gunluk Yedekleme'],
        'highlight' => false,
    ],
    [
        'name' => 'cPanel Hosting',
        'subtitle' => 'Kisisel siteler icin',
        'icon' => 'bi-hdd-rack',
        'old_price' => '149', 'price' => '79,99', 'period' => 'aylik',
        'features' => ['3 Adet Web Sitesi','Sinirsiz NVMe SSD Disk','2 Core Islemci','2 GB RAM','Ucretsiz SSL','Gunluk Yedekleme','LiteSpeed'],
        'highlight' => true,
    ],
    [
        'name' => 'Kurumsal',
        'subtitle' => 'Sirketler icin',
        'icon' => 'bi-building-fill-gear',
        'old_price' => '299', 'price' => '189,99', 'period' => 'aylik',
        'features' => ['10 Adet Web Sitesi','Sinirsiz NVMe SSD Disk','4 Core Islemci','4 GB RAM','Ucretsiz SSL','Saatlik Yedekleme','Oncelikli Destek'],
        'highlight' => false,
    ],
    [
        'name' => 'Reseller',
        'subtitle' => 'Bayilik icin',
        'icon' => 'bi-people-fill',
        'old_price' => '599', 'price' => '349,99', 'period' => 'aylik',
        'features' => ['25 cPanel Hesabi','100 GB NVMe Disk','WHM Yonetim','2 GB RAM (site basi)','Ucretsiz SSL','7/24 Destek','Marka Cozumleri'],
        'highlight' => false,
    ],
];

// Avantajlar
$advantages = [
    ['icon' => 'bi-lightning-charge-fill',  'title' => '%100 NVMe SSD',          'desc' => 'NVMe turu SSD diskler ile sektorun en hizli hosting deneyimi.'],
    ['icon' => 'bi-speedometer2',           'title' => 'LiteSpeed Web Server',   'desc' => 'Apache\'den 9 kat daha hizli web sunucu teknolojisi.'],
    ['icon' => 'bi-shield-fill-check',      'title' => 'Ucretsiz SSL',           'desc' => 'Tum hosting paketlerinde Let\'s Encrypt ile ucretsiz SSL.'],
    ['icon' => 'bi-arrow-repeat',           'title' => 'Tek Tikla Kurulum',      'desc' => '300+ uygulama tek tikla cPanel uzerinden kurulur.'],
    ['icon' => 'bi-fingerprint',            'title' => 'DDoS Korumasi',          'desc' => 'Gelismis firewall ve DDoS koruma altyapisi.'],
    ['icon' => 'bi-headset',                'title' => '7/24 Destek',            'desc' => 'Whatsapp, telefon ve panel uzerinden 7/24 teknik destek.'],
];

// Stats
$stats = [
    ['num' => '15.000+', 'label' => 'Web Sitesi'],
    ['num' => '7.500+',  'label' => 'Alan Adi'],
    ['num' => '12.000+', 'label' => 'Mutlu Musteri'],
    ['num' => '250+',    'label' => 'Sunucu'],
];

// FAQ
$faqs = [
    ['q' => '%100 Musteri Memnuniyeti',     'a' => 'Musterilerimize satis oncesi ve sonrasi 7/24 destek sunmaktayiz. Whatsapp, telefon, panel uzerinden ulasabilirsiniz.'],
    ['q' => 'Kesintisiz 7/24 Destek',       'a' => 'Alaninda uzman ekibimiz haftanin 7 gunu, gunun 24 saati aktif olarak teknik destek vermektedir.'],
    ['q' => 'Yuksek Performansli Altyapi',  'a' => 'Tum sunucularimizda NVMe SSD diskler, LiteSpeed web server ve son nesil islemciler kullanilmaktadir.'],
    ['q' => 'Ucretsiz Tasima Destegi',      'a' => 'Hosting hizmeti almaniz durumunda 5 adet sitenizi veri kaybi olmadan uzman ekibimizle ucretsiz tasiriz.'],
    ['q' => 'Codega Kalitesi',              'a' => 'CODEGA kalitesi ile birlikte ihtiyaciniz olan performansli hosting ve sunucu hizmetine sahip olacaksiniz.'],
    ['q' => '30 Gun Iade Garantisi',        'a' => 'Hizmetimizden memnun kalmamaniz durumunda 30 gun icerisinde kosulsuz iade garantisi sunmaktayiz.'],
];
?>

<!-- 1. ÜST DUYURU BANNERİ -->
<div class="cdg-top-banner">
    <div class="cdg-container">
        <i class="bi bi-megaphone-fill"></i>
        <span>Yeni musterilere ozel %30 indirim! Ilk yil hosting + ucretsiz domain kampanyasi devam ediyor.</span>
        <a href="<?php echo $hosting_url; ?>" class="cdg-top-banner-cta">Goz At <i class="bi bi-arrow-right"></i></a>
    </div>
</div>

<!-- 2. HERO SLİDER -->
<section class="cdg-hero">
    <div class="cdg-hero-bg">
        <div class="cdg-hero-glow cdg-hero-glow-1"></div>
        <div class="cdg-hero-glow cdg-hero-glow-2"></div>
        <div class="cdg-hero-grid-pattern"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-hero-grid">
            <div class="cdg-hero-content">
                <div class="cdg-hero-eyebrow">
                    <i class="bi bi-stars"></i>
                    <span>Turkiye'nin Gozde Servis Saglayicisi</span>
                </div>
                <h1>Sektorun <span class="cdg-text-gradient">en hizli</span><br>Hosting saglayicisi</h1>
                <p class="cdg-hero-lead">CODEGA ile siteleriniz cok daha hizli! NVMe SSD, LiteSpeed ve ucretsiz SSL ile kurumsal kalitede hosting deneyimi.</p>

                <!-- Domain arama formu -->
                <?php if($mod_domain): ?>
                <form action="<?php echo $domain_check_url; ?>" method="get" class="cdg-hero-domain">
                    <div class="cdg-hero-domain-input">
                        <i class="bi bi-search"></i>
                        <input type="text" name="domain" placeholder="Hayalinizdeki alan adini sorgulayin..." required>
                    </div>
                    <button type="submit" class="cdg-btn cdg-btn-primary">
                        <i class="bi bi-globe2"></i> <span>Sorgula</span>
                    </button>
                </form>
                <?php endif; ?>

                <div class="cdg-hero-trust">
                    <div class="cdg-trust-item">
                        <i class="bi bi-shield-check"></i>
                        <span>%100 SSD</span>
                    </div>
                    <div class="cdg-trust-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>LiteSpeed</span>
                    </div>
                    <div class="cdg-trust-item">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>30 Gun Iade</span>
                    </div>
                    <div class="cdg-trust-item">
                        <i class="bi bi-headset"></i>
                        <span>7/24 Destek</span>
                    </div>
                </div>
            </div>

            <!-- Hero illüstrasyon (floating cards) -->
            <div class="cdg-hero-visual">
                <div class="cdg-hero-orb"></div>
                <div class="cdg-float-card cdg-float-card-1">
                    <div class="icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-hdd-network"></i></div>
                    <div class="body">
                        <div class="title">Hosting</div>
                        <div class="meta">12.000+ aktif</div>
                    </div>
                </div>
                <div class="cdg-float-card cdg-float-card-2">
                    <div class="icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-globe2"></i></div>
                    <div class="body">
                        <div class="title">Domain</div>
                        <div class="meta">7.500+ kayit</div>
                    </div>
                </div>
                <div class="cdg-float-card cdg-float-card-3">
                    <div class="icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-server"></i></div>
                    <div class="body">
                        <div class="title">Sunucu</div>
                        <div class="meta">250+ aktif</div>
                    </div>
                </div>
                <div class="cdg-float-card cdg-float-card-4">
                    <div class="icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="bi bi-shield-lock"></i></div>
                    <div class="body">
                        <div class="title">SSL</div>
                        <div class="meta">Ucretsiz</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. POPÜLER TLD'LER -->
<?php if($mod_domain): ?>
<section class="cdg-tld-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Alan Adi Sorgulama</div>
            <h2>Internet dunyasina ilk adimi <span class="cdg-text-gradient">bir alan adi</span> ile atin</h2>
            <p>Kampanyali fiyatlar ve yuzlerce secenek arasindan size uygun olan domain uzantisini secin.</p>
        </div>
        <div class="cdg-tld-grid">
            <?php foreach($popular_tlds as $tld): ?>
            <a href="<?php echo $domain_check_url; ?>" class="cdg-tld-card">
                <?php if($tld['badge']): ?><span class="cdg-tld-badge"><?php echo $tld['badge']; ?></span><?php endif; ?>
                <div class="cdg-tld-ext"><?php echo $tld['ext']; ?></div>
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

<!-- 4. HOSTING PAKETLERİ -->
<?php if($mod_hosting): ?>
<section class="cdg-pricing-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Paketleri</div>
            <h2>Hemen bir <span class="cdg-text-gradient">paket secimi</span> yapin</h2>
            <p>Kucuk islerden kurumsal projelere kadar her ihtiyaca uygun hosting paketleri.</p>
        </div>
        <div class="cdg-pricing-grid">
            <?php foreach($hosting_packages as $pkg): ?>
            <div class="cdg-price-card<?php echo $pkg['highlight'] ? ' cdg-price-card-highlight' : ''; ?>">
                <?php if($pkg['highlight']): ?><div class="cdg-price-ribbon">EN POPULER</div><?php endif; ?>
                <div class="cdg-price-icon"><i class="bi <?php echo $pkg['icon']; ?>"></i></div>
                <h3 class="cdg-price-name"><?php echo $pkg['name']; ?></h3>
                <p class="cdg-price-subtitle"><?php echo $pkg['subtitle']; ?></p>
                <div class="cdg-price-amount">
                    <span class="cdg-price-old"><?php echo $pkg['old_price']; ?> TL</span>
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
            </div>
            <?php endforeach; ?>
        </div>
        <div class="cdg-pricing-footer">
            <a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-outline">
                Tum Hosting Paketleri <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. AVANTAJLAR -->
<section class="cdg-advantages-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Avantajlari</div>
            <h2>Hizli, guvenli ve <span class="cdg-text-gradient">profesyonel</span> hosting</h2>
            <p>Hosting paketlerini maksimum performansta calistirmak icin en iyi optimizasyon ve bilesenleri derledik.</p>
        </div>
        <div class="cdg-adv-grid">
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

<!-- 6. SUNUCU SEÇENEKLERİ -->
<?php if($mod_server): ?>
<section class="cdg-server-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sanal & Fiziksel Sunucular</div>
            <h2>Yuksek performansli <span class="cdg-text-gradient">sunucu cozumleri</span></h2>
            <p>VPS, VDS ve Dedicated sunucular - her ihtiyaca uygun yapilandirma.</p>
        </div>
        <div class="cdg-server-grid">
            <a href="<?php echo $server_url; ?>" class="cdg-server-card">
                <div class="cdg-server-card-icon"><i class="bi bi-cloud-fill"></i></div>
                <h3>VPS Sunucu</h3>
                <p>KVM tabanli sanal sunucular. Tam yetki, root erisimi.</p>
                <div class="cdg-server-card-spec">2 vCPU · 4 GB RAM · 60 GB NVMe</div>
                <div class="cdg-server-card-price">79 TL <span>/ay'dan</span></div>
            </a>
            <a href="<?php echo $server_url; ?>" class="cdg-server-card cdg-server-card-featured">
                <span class="cdg-server-card-badge">EN POPULER</span>
                <div class="cdg-server-card-icon"><i class="bi bi-server"></i></div>
                <h3>VDS Sunucu</h3>
                <p>Yuksek performansli VDS sunucular. Kaynaklar size ozel.</p>
                <div class="cdg-server-card-spec">4 vCPU · 8 GB RAM · 120 GB NVMe</div>
                <div class="cdg-server-card-price">199 TL <span>/ay'dan</span></div>
            </a>
            <a href="<?php echo $server_url; ?>" class="cdg-server-card">
                <div class="cdg-server-card-icon"><i class="bi bi-hdd-stack-fill"></i></div>
                <h3>Dedicated</h3>
                <p>Komple fiziksel sunucu. Maksimum performans ve guvenlik.</p>
                <div class="cdg-server-card-spec">Intel Xeon · 64 GB RAM · 2 TB NVMe</div>
                <div class="cdg-server-card-price">2.499 TL <span>/ay'dan</span></div>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. İSTATİSTİKLER BANNERİ -->
<section class="cdg-stats-section">
    <div class="cdg-container">
        <div class="cdg-stats-head">
            <h2>Herkes icin <span class="cdg-text-gradient-light">hizli ve profesyonel</span> cozumler</h2>
            <p>Binlerce musteri CODEGA'yi tercih ediyor.</p>
        </div>
        <div class="cdg-stats-grid">
            <?php foreach($stats as $stat): ?>
            <div class="cdg-stat-banner">
                <div class="cdg-stat-banner-num"><?php echo $stat['num']; ?></div>
                <div class="cdg-stat-banner-label"><?php echo $stat['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. SSS / NEDEN CODEGA -->
<section class="cdg-faq-section">
    <div class="cdg-container">
        <div class="cdg-faq-grid">
            <div class="cdg-faq-intro">
                <div class="cdg-eyebrow">Neden CODEGA?</div>
                <h2>Sorulariniza <span class="cdg-text-gradient">hizli cevaplar</span></h2>
                <p>CODEGA olarak sizlere kaliteli hizmet sunmak icin caliyoruz. En sik sorulan sorulari sizin icin derledik.</p>
                <div class="cdg-faq-cta">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary">
                        <i class="bi bi-chat-dots-fill"></i> Bizimle Iletisime Gec
                    </a>
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

<!-- 9. DESTEK CTA BANNERİ -->
<section class="cdg-support-cta">
    <div class="cdg-container">
        <div class="cdg-support-card">
            <div class="cdg-support-illu">
                <div class="cdg-support-orb"></div>
                <i class="bi bi-headset"></i>
            </div>
            <div class="cdg-support-content">
                <div class="cdg-eyebrow" style="color:rgba(255,255,255,0.85);">7/24 Destek</div>
                <h2>Aklinizda takilan bir <span class="cdg-text-gradient-light">soru</span> mu var?</h2>
                <p>CODEGA olarak sizlerin ihtiyac duydugu tum destegi sunmak icin caliyoruz.</p>
                <div class="cdg-support-features">
                    <div class="cdg-support-feature">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <div class="title">Telefon Destek</div>
                            <div class="meta">0 332 909 9656</div>
                        </div>
                    </div>
                    <div class="cdg-support-feature">
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <div class="title">7/24 Teknik Destek</div>
                            <div class="meta">Whatsapp & E-posta</div>
                        </div>
                    </div>
                    <div class="cdg-support-feature">
                        <i class="bi bi-chat-dots-fill"></i>
                        <div>
                            <div class="title">Canli Destek</div>
                            <div class="meta">Satis oncesi/sonrasi</div>
                        </div>
                    </div>
                </div>
                <div class="cdg-support-actions">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-white">
                        <i class="bi bi-envelope-fill"></i> Iletisim Formu
                    </a>
                    <a href="<?php echo $register_url; ?>" class="cdg-btn cdg-btn-ghost">
                        <i class="bi bi-person-plus-fill"></i> Hemen Kayit Ol
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 10. SON CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <h2>Internet dunyasina <span class="cdg-text-gradient">CODEGA</span> ile baslayin</h2>
            <p>Bugun kayit olun, %30 indirimden faydalanin. 30 gun para iade garantisi.</p>
            <div class="cdg-final-cta-actions">
                <?php if($mod_hosting): ?>
                <a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg">
                    <i class="bi bi-rocket-takeoff-fill"></i> Hosting Al
                </a>
                <?php endif; ?>
                <?php if($mod_domain): ?>
                <a href="<?php echo $domain_check_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg">
                    <i class="bi bi-globe2"></i> Domain Sorgula
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
