<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Diagnostic: ?_diag=1 ile TLD durumunu kontrol
if(isset($_GET['_diag']) && $_GET['_diag'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== INDEX.PHP TLD DIAGNOSTIC ===\n\n";
    echo "GLOBALS['tldList']: " . (isset($GLOBALS['tldList']) && is_array($GLOBALS['tldList']) ? 'SET (' . count($GLOBALS['tldList']) . ' adet)' : 'NOT SET') . "\n";
    echo "Tld class exists: " . (class_exists('Tld') ? 'YES' : 'NO') . "\n";
    if(class_exists('Tld')) {
        echo "Tld methods:\n";
        foreach(['getActives','getList','getAll','getall','lister'] as $m) {
            echo "  - $m: " . (method_exists('Tld', $m) ? 'YES' : 'NO') . "\n";
        }
    }
    echo "Domains class exists: " . (class_exists('Domains') ? 'YES' : 'NO') . "\n";
    if(class_exists('Domains')) {
        $methods = get_class_methods('Domains');
        echo "Domains methods: " . implode(', ', array_slice($methods ?? [], 0, 15)) . "\n";
    }
    echo "DB class exists: " . (class_exists('DB') ? 'YES' : 'NO') . "\n";
    echo "\n=== INSTALLED CORE CLASSES (WiseCP related) ===\n";
    foreach(get_declared_classes() as $cls) {
        if(stripos($cls, 'Tld') !== false || stripos($cls, 'Domain') !== false) {
            echo "  - $cls\n";
        }
    }
    exit;
}

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        global $links;
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
            'tickets'                 => 'ac-ps-tickets',
            'my-tickets'              => 'ac-ps-tickets',
            'messages'                => 'ac-ps-messages',
            'detail-message'          => 'ac-ps-detail-message',
            'invoices'                => 'ac-ps-invoices',
            'detail-invoice'          => 'ac-ps-detail-invoice',
            'detail-invoice-pdf'      => 'ac-ps-detail-invoice',
            'balance'                 => 'ac-ps-balance',
            'balance-page'            => 'ac-ps-balance',
            'info'                    => 'ac-ps-info',
            'ac-info'                 => 'ac-ps-info',
            'products'                => 'ac-ps-products',
            'all-orders'              => 'ac-ps-products',
            'products-t'              => 'ac-ps-products-t',
            'product'                 => 'ac-ps-product',
            'sms'                     => 'ac-ps-sms',
            'affiliate'               => 'ac-affiliate',
            'ac-affiliate'            => 'ac-affiliate',
            'reseller'                => 'ac-reseller',
            'domains'                 => 'ac-products-domain',
            'products-domain'         => 'ac-products-domain',
            'whois-profiles'          => 'ac-products-domain-whois-profiles',
            'products-domain-whois-profiles' => 'ac-products-domain-whois-profiles',
            'create-whois-profile'    => 'ac-products-domain-create-whois-profile',
            'products-domain-create-whois-profile' => 'ac-products-domain-create-whois-profile',
            'login'                   => 'sign-in',
            'register'                => 'sign-up',
            'logout'                  => 'sign-out',
            'account'                 => 'my-account',
            'homepage'                => '',
            'home'                    => '',
        ];
        $real_slug = isset($aliases[$slug]) ? $aliases[$slug] : $slug;
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) {}
        }
        // Son care: $links bakilirsa kullan
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$domain_url   = cdg_link('domain');
$hosting_url  = cdg_link('products', ['hosting']);
$contact_url  = cdg_link('contact');
$register_url = cdg_link('register');
$login_url    = cdg_link('login');

$mod_hosting = isset($pg_activation['hosting']) ? !empty($pg_activation['hosting']) : true;
$mod_domain  = isset($pg_activation['domain'])  ? !empty($pg_activation['domain'])  : true;

// === GERÇEK PAKETLER ===
$pricing_categories = [
    [
        'id' => 'ekonomik', 'name' => 'Ekonomik SSD Hosting', 'icon' => 'bi-rocket-takeoff', 'color' => '#10b981',
        'desc' => 'Bireysel ve küçük projeler için uygun fiyatlı paketler',
        'packages' => [
            ['name' => 'Linux Hosting 1', 'subtitle' => 'Bireysel siteler', 'price' => '150', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => false, 'features' => ['1 Web Sitesi', '5 GB NVMe SSD', '50 GB Trafik', '5 E-posta', 'Ücretsiz SSL', 'Günlük Yedekleme']],
            ['name' => 'Linux Hosting 2', 'subtitle' => 'Hobi siteleri', 'price' => '289', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => true, 'features' => ['3 Web Sitesi', '20 GB NVMe SSD', 'Sınırsız Trafik', '20 E-posta', 'Ücretsiz SSL', 'LiteSpeed', 'Günlük Yedekleme']],
            ['name' => 'Linux Hosting 3', 'subtitle' => 'Küçük işletme', 'price' => '389', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => false, 'features' => ['5 Web Sitesi', '50 GB NVMe SSD', 'Sınırsız Trafik', 'Sınırsız E-posta', 'Ücretsiz SSL', 'LiteSpeed', 'Saatlik Yedek']],
            ['name' => 'Linux Hosting 4', 'subtitle' => 'Geniş projeler', 'price' => '450', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => false, 'features' => ['10 Web Sitesi', '100 GB NVMe SSD', 'Sınırsız Trafik', 'Sınırsız E-posta', 'Ücretsiz SSL', 'LiteSpeed Enterprise', 'Saatlik Yedek']],
        ],
    ],
    [
        'id' => 'profesyonel', 'name' => 'Profesyonel SSD Hosting', 'icon' => 'bi-stars', 'color' => '#1e40af',
        'desc' => 'Yüksek trafikli siteler ve kurumsal çözümler için',
        'packages' => [
            ['name' => 'Profesyonel 1', 'subtitle' => 'Kurumsal başlangıç', 'price' => '450', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => false, 'features' => ['10 Web Sitesi', '100 GB NVMe SSD', 'Sınırsız Trafik', '2 Core CPU', '2 GB RAM', 'Ücretsiz SSL', 'LiteSpeed Enterprise']],
            ['name' => 'Profesyonel 2', 'subtitle' => 'Büyük kurumsal', 'price' => '750', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => true, 'features' => ['25 Web Sitesi', '250 GB NVMe SSD', 'Sınırsız Trafik', '4 Core CPU', '4 GB RAM', 'Ücretsiz SSL', 'LiteSpeed Enterprise', 'Öncelikli Destek']],
            ['name' => 'Profesyonel 3', 'subtitle' => 'Yüksek trafik', 'price' => '1.200', 'currency' => '₺', 'period' => 'yıllık', 'highlight' => false, 'features' => ['Sınırsız Site', '500 GB NVMe SSD', 'Sınırsız Trafik', '8 Core CPU', '8 GB RAM', 'Ücretsiz SSL', 'LiteSpeed Enterprise', 'Dedicated IP']],
        ],
    ],
    [
        'id' => 'bayi', 'name' => 'Bayi (Reseller) Hosting', 'icon' => 'bi-people-fill', 'color' => '#8b5cf6',
        'desc' => 'Web tasarımcıları ve ajanslar için bayilik çözümleri',
        'packages' => [
            ['name' => 'S BAYİ', 'subtitle' => 'Küçük bayilik', 'price' => '14', 'currency' => '$', 'period' => 'aylık', 'highlight' => false, 'features' => ['10 cPanel Hesabı', '20 GB NVMe SSD', '200 GB Trafik', 'WHM Yönetim', 'Ücretsiz SSL', 'White Label']],
            ['name' => 'M BAYİ', 'subtitle' => 'Orta bayilik', 'price' => '24', 'currency' => '$', 'period' => 'aylık', 'highlight' => true, 'features' => ['25 cPanel Hesabı', '50 GB NVMe SSD', 'Sınırsız Trafik', 'WHM Yönetim', 'Ücretsiz SSL', 'White Label', 'cPanel Lisansı']],
            ['name' => 'L BAYİ', 'subtitle' => 'Büyük bayilik', 'price' => '39', 'currency' => '$', 'period' => 'aylık', 'highlight' => false, 'features' => ['50 cPanel Hesabı', '100 GB NVMe SSD', 'Sınırsız Trafik', 'WHM Yönetim', 'Ücretsiz SSL', 'White Label', 'Marka Çözümleri']],
        ],
    ],
];

// === DOMAIN FİYATLARI - WiseCP'den gerçek çekim ===
$popular_tlds = [];
$tld_loaded_from_api = false;
$override_usrcurrency = isset($override_usrcurrency) ? $override_usrcurrency : false;

if($mod_domain) {
    $wanted_exts = ['com.tr', 'com', 'net', 'org', 'tr', 'xyz'];

    // Yardımcı: TLD'yi popular_tlds yapısına dönüştür
    $build_tld = function($name, $reg_amount, $reg_cid) use ($override_usrcurrency) {
        $price_str = '-';
        $symbol = '₺';
        if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $reg_amount !== null) {
            try {
                $formatted = Money::formatter_symbol($reg_amount, $reg_cid, !$override_usrcurrency);
                // Sembolü ayır
                $parts = explode(' ', trim($formatted));
                if(count($parts) >= 2) {
                    if(!preg_match('/\d/', $parts[0])) {
                        $symbol = $parts[0];
                        $price_str = implode(' ', array_slice($parts, 1));
                    } elseif(!preg_match('/\d/', end($parts))) {
                        $symbol = end($parts);
                        array_pop($parts);
                        $price_str = implode(' ', $parts);
                    } else {
                        $price_str = $formatted;
                    }
                } else {
                    $price_str = $formatted;
                }
            } catch(Exception $e) {
                $price_str = (string)$reg_amount;
            }
        } elseif($reg_amount !== null) {
            $price_str = (string)$reg_amount;
        }
        return [
            'ext'      => '.' . ltrim($name, '.'),
            'price'    => $price_str,
            'currency' => $symbol,
            'old'      => '',
            'badge'    => '',
        ];
    };

    // Yöntem 1: Global $tldList (WiseCP domain sayfasından gelir, anasayfada yok)
    if(isset($GLOBALS['tldList']) && is_array($GLOBALS['tldList']) && count($GLOBALS['tldList']) > 0) {
        $by_ext = [];
        foreach($GLOBALS['tldList'] as $t) {
            $by_ext[strtolower(ltrim($t['name'] ?? '', '.'))] = $t;
        }
        foreach($wanted_exts as $we) {
            if(isset($by_ext[$we])) {
                $t = $by_ext[$we];
                $popular_tlds[] = $build_tld(
                    $t['name'],
                    $t['reg_price']['amount'] ?? null,
                    $t['reg_price']['cid'] ?? 1
                );
            }
        }
        if(!empty($popular_tlds)) $tld_loaded_from_api = true;
    }

    // Yöntem 2: Tld::getActives veya Tld::getList (WiseCP core class)
    if(!$tld_loaded_from_api && class_exists('Tld')) {
        $methods = ['getActives', 'getList', 'getAll', 'getall', 'lister'];
        foreach($methods as $m) {
            if(method_exists('Tld', $m)) {
                try {
                    $list = call_user_func(['Tld', $m]);
                    if(is_array($list) && count($list) > 0) {
                        $by_ext = [];
                        foreach($list as $t) {
                            $tname = strtolower(ltrim($t['name'] ?? '', '.'));
                            if($tname) $by_ext[$tname] = $t;
                        }
                        foreach($wanted_exts as $we) {
                            if(isset($by_ext[$we])) {
                                $t = $by_ext[$we];
                                $reg_amount = $t['reg_price']['amount'] ?? $t['register_price'] ?? $t['price'] ?? null;
                                $reg_cid = $t['reg_price']['cid'] ?? $t['cid'] ?? 1;
                                $popular_tlds[] = $build_tld($t['name'], $reg_amount, $reg_cid);
                            }
                        }
                        if(!empty($popular_tlds)) {
                            $tld_loaded_from_api = true;
                            break;
                        }
                    }
                } catch(Exception $e) { continue; }
            }
        }
    }

    // Yöntem 3: Veritabanından doğrudan (DB::table varsa)
    if(!$tld_loaded_from_api && class_exists('DB')) {
        try {
            // Yaygın WiseCP tablo adları: tlds, domain_tlds, dl_tlds
            $candidates = ['tlds', 'domain_tlds'];
            foreach($candidates as $tbl) {
                if(method_exists('DB', 'table')) {
                    $rows = @DB::table($tbl)->where('status', 1)->get();
                    if($rows && is_array($rows)) {
                        $by_ext = [];
                        foreach($rows as $r) {
                            $rn = strtolower(ltrim($r['name'] ?? $r['tld'] ?? '', '.'));
                            if($rn) $by_ext[$rn] = $r;
                        }
                        foreach($wanted_exts as $we) {
                            if(isset($by_ext[$we])) {
                                $r = $by_ext[$we];
                                $reg = $r['register'] ?? $r['register_price'] ?? $r['price'] ?? null;
                                $cid = $r['cid'] ?? $r['currency_id'] ?? 1;
                                $popular_tlds[] = $build_tld($r['name'] ?? $r['tld'], $reg, $cid);
                            }
                        }
                        if(!empty($popular_tlds)) {
                            $tld_loaded_from_api = true;
                            break;
                        }
                    }
                }
            }
        } catch(Exception $e) { /* fallback */ }
    }

    // Yöntem 4: theme-config.php'den (admin tanımlamışsa)
    if(!$tld_loaded_from_api) {
        $config = include __DIR__ . DS . 'theme-config.php';
        $ts_settings = isset($config['settings']) ? $config['settings'] : [];
        if(!empty($ts_settings['featured_tlds']) && is_array($ts_settings['featured_tlds'])) {
            $popular_tlds = $ts_settings['featured_tlds'];
            $tld_loaded_from_api = true;
        }
    }

    // Hiçbir yöntem başarısız olursa: BOŞ bırak (sahte veri yok)
    // Yunus admin panelinde TLD tanımladığında otomatik gelecek
}

// Tech stack
$tech_stack = [
    ['name' => 'NVMe SSD',     'icon' => 'bi-hdd-fill',         'color' => '#10b981'],
    ['name' => 'LiteSpeed',    'icon' => 'bi-lightning-fill',   'color' => '#f59e0b'],
    ['name' => 'Cloudflare',   'icon' => 'bi-cloud-fill',       'color' => '#06b6d4'],
    ['name' => 'cPanel',       'icon' => 'bi-grid-1x2-fill',    'color' => '#1e40af'],
    ['name' => 'PHP 8.3',      'icon' => 'bi-filetype-php',     'color' => '#8b5cf6'],
    ['name' => 'MariaDB',      'icon' => 'bi-database-fill',    'color' => '#ec4899'],
    ['name' => 'Redis',        'icon' => 'bi-memory',           'color' => '#ef4444'],
    ['name' => 'Imunify360',   'icon' => 'bi-shield-fill',      'color' => '#34d399'],
];

$compare_features = [
    ['feature' => 'NVMe SSD Disk',         'starter' => '5 GB',     'pro' => '50 GB',     'business' => '250 GB',   'enterprise' => 'Sınırsız'],
    ['feature' => 'Web Sitesi',            'starter' => '1',        'pro' => '5',         'business' => '25',       'enterprise' => 'Sınırsız'],
    ['feature' => 'CPU Core',              'starter' => '1',        'pro' => '2',         'business' => '4',        'enterprise' => '8'],
    ['feature' => 'RAM',                   'starter' => '1 GB',     'pro' => '2 GB',      'business' => '4 GB',     'enterprise' => '8 GB'],
    ['feature' => 'Aylık Trafik',          'starter' => '50 GB',    'pro' => 'Sınırsız',  'business' => 'Sınırsız', 'enterprise' => 'Sınırsız'],
    ['feature' => 'E-posta',               'starter' => '5',        'pro' => 'Sınırsız',  'business' => 'Sınırsız', 'enterprise' => 'Sınırsız'],
    ['feature' => 'Ücretsiz SSL',          'starter' => 'check',    'pro' => 'check',     'business' => 'check',    'enterprise' => 'check'],
    ['feature' => 'LiteSpeed Enterprise',  'starter' => 'cross',    'pro' => 'check',     'business' => 'check',    'enterprise' => 'check'],
    ['feature' => 'Saatlik Yedekleme',     'starter' => 'cross',    'pro' => 'cross',     'business' => 'check',    'enterprise' => 'check'],
    ['feature' => 'Dedicated IP',          'starter' => 'cross',    'pro' => 'cross',     'business' => 'cross',    'enterprise' => 'check'],
    ['feature' => 'Öncelikli Destek',      'starter' => 'cross',    'pro' => 'cross',     'business' => 'check',    'enterprise' => 'check'],
];

$advantages = [
    ['icon' => 'bi-lightning-charge-fill', 'title' => '%100 NVMe SSD',         'desc' => 'NVMe disklerle 10x daha hızlı I/O performansı.'],
    ['icon' => 'bi-speedometer2',          'title' => 'LiteSpeed Enterprise',  'desc' => 'Apache\'den 9 kat hızlı, kaynak verimli.'],
    ['icon' => 'bi-shield-fill-check',     'title' => 'Ücretsiz SSL',          'desc' => 'Tüm paketlerde Let\'s Encrypt SSL otomatik.'],
    ['icon' => 'bi-arrow-clockwise',       'title' => 'Saatlik Yedekleme',     'desc' => 'Verileriniz saat başı yedeklenir.'],
    ['icon' => 'bi-cloud-arrow-up-fill',   'title' => 'Ücretsiz Taşıma',       'desc' => 'Mevcut sitenizi ücretsiz taşıyalım.'],
    ['icon' => 'bi-fingerprint',           'title' => 'DDoS Koruması',         'desc' => 'Gelişmiş firewall + 24/7 izleme.'],
    ['icon' => 'bi-graph-up-arrow',        'title' => '%99.99 Uptime',         'desc' => 'SLA garantili kesintisiz hizmet.'],
    ['icon' => 'bi-headset',               'title' => '7/24 Destek',           'desc' => 'WhatsApp, telefon, panel üzerinden.'],
];

$solutions = [
    ['icon' => 'bi-cart3',           'title' => 'E-Ticaret',     'desc' => 'WooCommerce, OpenCart, PrestaShop optimize',     'color' => '#10b981'],
    ['icon' => 'bi-wordpress',       'title' => 'WordPress',     'desc' => 'Tek tıkla kurulum + WP-CLI + auto update',       'color' => '#21759b'],
    ['icon' => 'bi-buildings',       'title' => 'Kurumsal',      'desc' => 'Yüksek trafik + dedicated kaynak + SLA',         'color' => '#8b5cf6'],
    ['icon' => 'bi-mortarboard-fill','title' => 'Eğitim',        'desc' => 'Moodle, Open edX, BBB optimize',                 'color' => '#f59e0b'],
    ['icon' => 'bi-newspaper',       'title' => 'Haber/Blog',    'desc' => 'Yüksek trafik + CDN + cache layer',              'color' => '#ec4899'],
    ['icon' => 'bi-stack-overflow',  'title' => 'Yazılımcı',     'desc' => 'Git, Composer, Node, SSH erişimi',               'color' => '#06b6d4'],
];

$faqs = [
    ['q' => 'Hosting hizmetinde performans nasıl sağlanır?', 'a' => 'Tüm sunucularımızda NVMe SSD diskler, LiteSpeed Enterprise web server, son nesil Intel/AMD işlemciler ve ECC RAM kullanılıyor. Bu altyapı ile siteleriniz Apache\'ye göre 9 kat daha hızlı yüklenir.'],
    ['q' => 'Mevcut sitemi CODEGA\'ya nasıl taşırım?', 'a' => 'Hosting paketinizi aldıktan sonra panel üzerinden taşıma talebi oluşturabilirsiniz. Uzman ekibimiz cPanel veya hosting backup\'ınızı alır, veri kaybı olmadan taşır. 5 adete kadar ÜCRETSİZ taşırız.'],
    ['q' => 'Ücretsiz SSL sertifikası nasıl aktif olur?', 'a' => 'Tüm hosting paketlerinde Let\'s Encrypt SSL otomatik kurulur. Domain\'inizi ekledikten sonra cPanel\'de SSL sekmesinden tek tıkla aktif edebilirsiniz. SSL 90 günde bir otomatik yenilenir.'],
    ['q' => '7/24 destek hangi kanallardan sağlanıyor?', 'a' => 'WhatsApp, telefon, e-posta ve panel üzerinden destek talebi açabilirsiniz. Ortalama yanıt süresi 5 dakikanın altındadır.'],
    ['q' => 'İade garantisi nasıl işliyor?', 'a' => 'Hosting hizmetinden memnun kalmamanız halinde 30 gün içerisinde koşulsuz iade talebinde bulunabilirsiniz.'],
    ['q' => 'Domain transferi ücretsiz mi?', 'a' => 'Evet, .com .net .org gibi gTLD\'ler için transfer işlemi ÜCRETSİZdir, ayrıca 1 yıl süresine ekleme yapılır.'],
];
?>

<!-- 1. ÜST DUYURU -->
<div class="cdg-top-banner">
    <div class="cdg-container">
        <i class="bi bi-megaphone-fill"></i>
        <span><strong>YENİ:</strong> Tüm hosting paketlerinde %30 indirim! Kampanya için son 7 gün.</span>
        <a href="<?php echo $hosting_url; ?>" class="cdg-top-banner-cta">Hemen Al <i class="bi bi-arrow-right"></i></a>
    </div>
</div>

<!-- 2. HERO PRO MAX -->
<section class="cdg-hero cdg-hero-future">
    <div class="cdg-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-glow cdg-hero-glow-1"></div>
        <div class="cdg-hero-glow cdg-hero-glow-2"></div>
        <div class="cdg-hero-glow cdg-hero-glow-3"></div>
        <div class="cdg-hero-grid-pattern"></div>
        <div class="cdg-hero-particles">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
    <div class="cdg-container">
        <div class="cdg-hero-grid">
            <div class="cdg-hero-content">
                <div class="cdg-hero-eyebrow cdg-hero-eyebrow-glow">
                    <span class="cdg-hero-pulse"></span>
                    <span>Yeni Nesil Hosting Sağlayıcısı</span>
                </div>
                <h1>Geleceğin <span class="cdg-text-gradient">hosting</span><br>deneyimi <span class="cdg-text-gradient-cyan">bugün</span></h1>
                <p class="cdg-hero-lead">CODEGA ile siteleriniz <strong>9 kat daha hızlı</strong>, <strong>%99.99 uptime</strong>. AI tabanlı optimizasyon, NVMe SSD, LiteSpeed Enterprise ve 7/24 uzman desteği.</p>

                <?php if($mod_domain): ?>
                <form action="<?php echo $domain_url; ?>" method="get" class="cdg-hero-domain cdg-hero-domain-glow<?php echo (isset($captcha) && $captcha) ? ' cdg-hero-domain-with-captcha' : ''; ?>">
                    <div class="cdg-hero-domain-input">
                        <i class="bi bi-search"></i>
                        <input type="text" name="domain" placeholder="alanadi.com" required autocomplete="off">
                    </div>

                    <?php if(isset($captcha) && $captcha): ?>
                    <div class="cdg-hero-domain-captcha">
                        <div class="cdg-hero-captcha-img-inline"><?php echo $captcha->getOutput(); ?></div>
                        <?php if($captcha->input): ?>
                        <input type="text" name="<?php echo $captcha->input_name; ?>" placeholder="Güvenlik Kodu" required autocomplete="off" maxlength="10">
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="cdg-btn cdg-btn-primary cdg-btn-glow">
                        <i class="bi bi-globe2"></i> <span>Domain Sorgula</span>
                    </button>
                </form>
                <?php if(!empty($popular_tlds)): ?>
                <div class="cdg-hero-popular-tlds">
                    <span class="muted">Popüler:</span>
                    <?php foreach(array_slice($popular_tlds, 0, 3) as $t): ?>
                    <a href="<?php echo $domain_url; ?>"><?php echo $t['ext']; ?> <strong><?php echo $t['price']; ?> <?php echo $t['currency'] ?? '₺'; ?></strong></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <div class="cdg-hero-trust">
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>%99.99 Uptime</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>30 Gün İade</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>Ücretsiz SSL</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-check-circle-fill"></i><span>Ücretsiz Taşıma</span></div>
                </div>
            </div>

            <div class="cdg-hero-visual">
                <div class="cdg-hero-orb-wrap">
                    <div class="cdg-hero-orb cdg-hero-orb-future"></div>
                    <div class="cdg-hero-ring cdg-hero-ring-1"></div>
                    <div class="cdg-hero-ring cdg-hero-ring-2"></div>
                    <div class="cdg-hero-ring cdg-hero-ring-3"></div>
                </div>
                <div class="cdg-float-card cdg-float-card-1 cdg-glass"><div class="icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-hdd-network"></i></div><div class="body"><div class="title">Hosting</div><div class="meta">NVMe SSD</div></div></div>
                <div class="cdg-float-card cdg-float-card-2 cdg-glass"><div class="icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-globe2"></i></div><div class="body"><div class="title">Domain</div><div class="meta">500+ uzantı</div></div></div>
                <div class="cdg-float-card cdg-float-card-3 cdg-glass"><div class="icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-shield-fill-check"></i></div><div class="body"><div class="title">SSL</div><div class="meta">Ücretsiz</div></div></div>
                <div class="cdg-float-card cdg-float-card-4 cdg-glass"><div class="icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="bi bi-cpu-fill"></i></div><div class="body"><div class="title">AI</div><div class="meta">Otomatik tune</div></div></div>
                <div class="cdg-float-stat cdg-glass"><div class="num">%99.99</div><div class="lbl">Uptime</div></div>
            </div>
        </div>
    </div>
</section>

<!-- 3. POPÜLER TLD'ler -->
<?php if($mod_domain && !empty($popular_tlds)): ?>
<section class="cdg-tld-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Domain Sorgulama</div>
            <h2>Hayalinizdeki <span class="cdg-text-gradient">alan adını</span> kaydedin</h2>
            <p>500+ uzantı desteği ile her ihtiyaça uygun domain. Fiyatlar panelden anlık güncellenir.</p>
        </div>
        <div class="cdg-tld-grid">
            <?php foreach($popular_tlds as $tld): ?>
            <a href="<?php echo $domain_url; ?>" class="cdg-tld-card">
                <?php if(!empty($tld['badge'])): ?><span class="cdg-tld-badge"><?php echo htmlspecialchars($tld['badge'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><?php endif; ?>
                <div class="cdg-tld-ext"><?php echo htmlspecialchars($tld['ext'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <?php if(!empty($tld['old'])): ?><div class="cdg-tld-old"><?php echo htmlspecialchars($tld['old'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo $tld['currency'] ?? '₺'; ?></div><?php endif; ?>
                <div class="cdg-tld-price"><span class="cdg-tld-amt"><?php echo htmlspecialchars($tld['price'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><span class="cdg-tld-curr"><?php echo $tld['currency'] ?? '₺'; ?></span></div>
                <div class="cdg-tld-period">/yıl</div>
                <div class="cdg-tld-cta">Sorgula <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 4. TECH STACK -->
<section class="cdg-tech-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Teknoloji Yığını</div>
            <h2>Sektörün <span class="cdg-text-gradient">en güçlü</span> teknolojileri</h2>
            <p>NVMe SSD, LiteSpeed Enterprise, Cloudflare ve daha fazlası — geleceğin altyapısı bugün.</p>
        </div>
        <div class="cdg-tech-grid">
            <?php foreach($tech_stack as $t): ?>
            <div class="cdg-tech-card">
                <div class="cdg-tech-icon" style="background:linear-gradient(135deg,<?php echo $t['color']; ?>,<?php echo $t['color']; ?>cc);">
                    <i class="bi <?php echo $t['icon']; ?>"></i>
                </div>
                <span><?php echo $t['name']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. HOSTING PRICING -->
<?php if($mod_hosting): ?>
<section class="cdg-pricing-tabbed cdg-section" id="paketler">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Paketleri</div>
            <h2>Her ihtiyaça uygun <span class="cdg-text-gradient">hosting paketleri</span></h2>
            <p>Bireysel sitelerden bayilik çözümlerine, NVMe SSD + LiteSpeed altyapısı ile.</p>
        </div>

        <div class="cdg-pricing-tabs" role="tablist">
            <?php foreach($pricing_categories as $i => $cat): ?>
            <button type="button" class="cdg-pricing-tab<?php echo $i === 0 ? ' active' : ''; ?>" data-tab="<?php echo $cat['id']; ?>" role="tab">
                <i class="bi <?php echo $cat['icon']; ?>" style="color:<?php echo $cat['color']; ?>;"></i>
                <span><?php echo htmlspecialchars($cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <small><?php echo count($cat['packages']); ?> paket</small>
            </button>
            <?php endforeach; ?>
        </div>

        <?php foreach($pricing_categories as $i => $cat): ?>
        <div class="cdg-pricing-pane<?php echo $i === 0 ? ' active' : ''; ?>" data-pane="<?php echo $cat['id']; ?>" role="tabpanel">
            <div class="cdg-pricing-pane-desc"><?php echo htmlspecialchars($cat['desc'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            <div class="cdg-pricing-grid cdg-pricing-grid-<?php echo count($cat['packages']); ?>">
                <?php foreach($cat['packages'] as $pkg): ?>
                <div class="cdg-price-card<?php echo !empty($pkg['highlight']) ? ' cdg-price-card-highlight' : ''; ?>">
                    <?php if(!empty($pkg['highlight'])): ?><div class="cdg-price-ribbon">EN POPÜLER</div><?php endif; ?>
                    <div class="cdg-price-cat-tag" style="color:<?php echo $cat['color']; ?>;background:<?php echo $cat['color']; ?>15;">
                        <i class="bi <?php echo $cat['icon']; ?>"></i> <?php echo htmlspecialchars($cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                    <h3 class="cdg-price-name"><?php echo htmlspecialchars($pkg['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                    <p class="cdg-price-subtitle"><?php echo htmlspecialchars($pkg['subtitle'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                    <div class="cdg-price-amount">
                        <div class="cdg-price-current">
                            <span class="cdg-price-curr"><?php echo $pkg['currency']; ?></span>
                            <span class="cdg-price-num"><?php echo $pkg['price']; ?></span>
                        </div>
                        <span class="cdg-price-period">/<?php echo $pkg['period']; ?></span>
                    </div>
                    <ul class="cdg-price-features">
                        <?php foreach($pkg['features'] as $feat): ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($feat, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo $hosting_url; ?>" class="cdg-btn <?php echo !empty($pkg['highlight']) ? 'cdg-btn-primary cdg-btn-glow' : 'cdg-btn-outline'; ?> cdg-btn-block">
                        <i class="bi bi-cart-plus"></i> Hemen Satın Al
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<script>
(function(){
    var tabs = document.querySelectorAll('.cdg-pricing-tab');
    var panes = document.querySelectorAll('.cdg-pricing-pane');
    tabs.forEach(function(tab){
        tab.addEventListener('click', function(){
            var target = tab.getAttribute('data-tab');
            tabs.forEach(function(t){ t.classList.remove('active'); });
            panes.forEach(function(p){ p.classList.remove('active'); });
            tab.classList.add('active');
            var pane = document.querySelector('.cdg-pricing-pane[data-pane="'+target+'"]');
            if(pane) pane.classList.add('active');
        });
    });
})();
</script>
<?php endif; ?>

<!-- 6. COMPARE TABLE -->
<section class="cdg-compare-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Paket Karşılaştırma</div>
            <h2>Detaylı <span class="cdg-text-gradient">paket özellikleri</span></h2>
            <p>Hangi paket size uygun? Tüm özellikleri karşılaştırın.</p>
        </div>
        <div class="cdg-compare-wrap">
            <table class="cdg-compare-table">
                <thead>
                    <tr>
                        <th class="cdg-compare-feature-col">Özellik</th>
                        <th class="cdg-compare-plan"><div class="plan-name">Başlangıç</div><div class="plan-price">150 ₺<small>/yıl</small></div></th>
                        <th class="cdg-compare-plan"><div class="plan-name">Profesyonel</div><div class="plan-price">389 ₺<small>/yıl</small></div></th>
                        <th class="cdg-compare-plan cdg-compare-plan-popular"><div class="plan-badge">EN POPÜLER</div><div class="plan-name">Business</div><div class="plan-price">750 ₺<small>/yıl</small></div></th>
                        <th class="cdg-compare-plan"><div class="plan-name">Enterprise</div><div class="plan-price">1.200 ₺<small>/yıl</small></div></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($compare_features as $row): ?>
                    <tr>
                        <td class="cdg-compare-feature"><?php echo htmlspecialchars($row['feature'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <?php foreach(['starter', 'pro', 'business', 'enterprise'] as $col): ?>
                        <td<?php if($col === 'business') echo ' class="cdg-compare-popular-cell"'; ?>>
                            <?php
                            if($row[$col] === 'check') echo '<i class="bi bi-check-circle-fill" style="color:#10b981;font-size:18px;"></i>';
                            elseif($row[$col] === 'cross') echo '<i class="bi bi-x-circle" style="color:#cbd5e1;font-size:18px;"></i>';
                            else echo '<span>' . htmlspecialchars($row[$col], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>';
                            ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="cdg-compare-cta-row">
                        <td></td>
                        <td><a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">Seç</a></td>
                        <td><a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">Seç</a></td>
                        <td><a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">Seç</a></td>
                        <td><a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">Seç</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 7. AI HOSTING -->
<section class="cdg-ai-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-ai-grid">
            <div class="cdg-ai-content">
                <div class="cdg-eyebrow cdg-eyebrow-glow">
                    <i class="bi bi-cpu-fill"></i> AI-Powered Hosting
                </div>
                <h2>Yapay zeka ile <span class="cdg-text-gradient-cyan">otomatik optimizasyon</span></h2>
                <p>Sitenizin trafik örüntüsünü öğrenen AI sistemi, kaynaklarınızı gerçek zamanlı olarak optimize eder. Daha hızlı, daha verimli, daha akıllı hosting.</p>
                <div class="cdg-ai-features">
                    <div class="cdg-ai-feat">
                        <div class="cdg-ai-feat-icon" style="background:linear-gradient(135deg,#06b6d4,#0891b2);"><i class="bi bi-graph-up-arrow"></i></div>
                        <div><strong>Akıllı Cache Yönetimi</strong><span>AI, hangi sayfaların cache\'de kalacağını öğrenir.</span></div>
                    </div>
                    <div class="cdg-ai-feat">
                        <div class="cdg-ai-feat-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);"><i class="bi bi-shield-shaded"></i></div>
                        <div><strong>Tehdit Tespiti</strong><span>Anormal davranışları tespit eden makine öğrenmesi.</span></div>
                    </div>
                    <div class="cdg-ai-feat">
                        <div class="cdg-ai-feat-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="bi bi-arrow-repeat"></i></div>
                        <div><strong>Otomatik Ölçekleme</strong><span>Trafik artışında kaynak otomatik artar.</span></div>
                    </div>
                    <div class="cdg-ai-feat">
                        <div class="cdg-ai-feat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div><strong>Predictive Loading</strong><span>Kullanıcının bir sonraki isteğini öngör.</span></div>
                    </div>
                </div>
            </div>
            <div class="cdg-ai-visual">
                <div class="cdg-ai-radar">
                    <div class="cdg-ai-radar-grid"></div>
                    <div class="cdg-ai-radar-sweep"></div>
                    <div class="cdg-ai-radar-dot cdg-ai-radar-dot-1"></div>
                    <div class="cdg-ai-radar-dot cdg-ai-radar-dot-2"></div>
                    <div class="cdg-ai-radar-dot cdg-ai-radar-dot-3"></div>
                    <div class="cdg-ai-radar-dot cdg-ai-radar-dot-4"></div>
                    <div class="cdg-ai-radar-center"><i class="bi bi-cpu-fill"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 8. WORDPRESS -->
<section class="cdg-wp-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-wp-grid">
            <div class="cdg-wp-content">
                <div class="cdg-wp-logo">
                    <i class="bi bi-wordpress"></i>
                    <span>WordPress Hosting</span>
                </div>
                <h2>WordPress için <span class="cdg-text-gradient">optimize edilmiş</span> hosting</h2>
                <p class="cdg-wp-lead">CODEGA WordPress hosting paketleri ile siteniz <strong>3-5 kat daha hızlı</strong> çalışır.</p>
                <div class="cdg-wp-features">
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>Tek Tıkla Kurulum</strong><span>Softaculous ile dakikalar içinde aktif.</span></div></div>
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>Auto Update</strong><span>Core, theme ve eklenti otomatik güncelleme.</span></div></div>
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>WP-CLI Erişim</strong><span>SSH/Terminal üzerinden komut satırı.</span></div></div>
                    <div class="cdg-wp-feat"><i class="bi bi-check-circle-fill"></i><div><strong>Staging Site</strong><span>Test ortamı + 1 tık production'a aktarma.</span></div></div>
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

<!-- 9. SECURITY SHIELD -->
<section class="cdg-security-section">
    <div class="cdg-container">
        <div class="cdg-security-grid">
            <div class="cdg-security-content">
                <div class="cdg-eyebrow" style="background:rgba(16,185,129,0.15);color:#10b981;border-color:rgba(16,185,129,0.30);">
                    <i class="bi bi-shield-fill-check"></i> Güvenlik Birinci
                </div>
                <h2>7/24 izlenen <span class="cdg-text-gradient-green">güvenli altyapı</span></h2>
                <p>Imunify360 firewall, gerçek zamanlı tehdit izleme, otomatik malware temizleme ve DDoS koruma — sitenizin güvenliği bizim önceliğimizdir.</p>
                <div class="cdg-security-features">
                    <div class="cdg-sec-feat"><i class="bi bi-shield-fill-check"></i> WAF (Web Application Firewall)</div>
                    <div class="cdg-sec-feat"><i class="bi bi-bug-fill"></i> Otomatik Malware Tarama</div>
                    <div class="cdg-sec-feat"><i class="bi bi-cloud-fill"></i> DDoS Koruma (Cloudflare)</div>
                    <div class="cdg-sec-feat"><i class="bi bi-lock-fill"></i> SSL/TLS 1.3</div>
                    <div class="cdg-sec-feat"><i class="bi bi-eye-fill"></i> 24/7 Tehdit İzleme</div>
                    <div class="cdg-sec-feat"><i class="bi bi-arrow-clockwise"></i> Otomatik Yedekleme</div>
                </div>
            </div>
            <div class="cdg-security-visual">
                <div class="cdg-shield-wrap">
                    <div class="cdg-shield-pulse"></div>
                    <div class="cdg-shield-pulse cdg-shield-pulse-2"></div>
                    <div class="cdg-shield-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <div class="cdg-shield-orbits">
                        <div class="cdg-shield-orbit cdg-shield-orbit-1"><span></span></div>
                        <div class="cdg-shield-orbit cdg-shield-orbit-2"><span></span></div>
                        <div class="cdg-shield-orbit cdg-shield-orbit-3"><span></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 10. SEKTÖREL -->
<section class="cdg-solutions-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sektörel Çözümler</div>
            <h2>İhtiyacınıza özel <span class="cdg-text-gradient">hosting çözümleri</span></h2>
            <p>E-ticaretten eğitim platformlarına, her sektör için optimize edilmiş altyapı.</p>
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

<!-- 11. AVANTAJLAR -->
<section class="cdg-advantages-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Neden CODEGA?</div>
            <h2>Hızlı, güvenli, <span class="cdg-text-gradient">profesyonel</span> hosting</h2>
            <p>Sektörde lider altyapı ve teknolojilerle hizmet veriyoruz.</p>
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

<!-- 12. MIGRATION WIZARD -->
<section class="cdg-migration-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Ücretsiz Taşıma</div>
            <h2>Sitenizi <span class="cdg-text-gradient">3 adımda</span> taşıyalım</h2>
            <p>Mevcut hosting sağlayıcınızdan CODEGA'ya geçmek artık çok kolay.</p>
        </div>
        <div class="cdg-migration-steps">
            <div class="cdg-mig-step">
                <div class="cdg-mig-step-num">01</div>
                <div class="cdg-mig-step-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-cart-check"></i></div>
                <h3>Paket Seçin</h3>
                <p>İhtiyacınıza uygun hosting paketini seçin ve sipariş verin. Hesabınız dakikalar içinde aktif olur.</p>
            </div>
            <div class="cdg-mig-arrow"><i class="bi bi-arrow-right"></i></div>
            <div class="cdg-mig-step">
                <div class="cdg-mig-step-num">02</div>
                <div class="cdg-mig-step-icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                <h3>Taşıma Talebi</h3>
                <p>Panel üzerinden taşıma talebi oluşturun. Mevcut hosting bilgilerinizi paylaşın, ekibimiz devralsın.</p>
            </div>
            <div class="cdg-mig-arrow"><i class="bi bi-arrow-right"></i></div>
            <div class="cdg-mig-step">
                <div class="cdg-mig-step-num">03</div>
                <div class="cdg-mig-step-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-check-circle-fill"></i></div>
                <h3>Hazır!</h3>
                <p>Uzman ekibimiz sitenizi kesintisiz taşır. DNS yönlendirmesi yapın, hazırsınız!</p>
            </div>
        </div>
        <div style="text-align:center;margin-top:36px;">
            <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow">
                <i class="bi bi-rocket-takeoff-fill"></i> Hemen Taşıyalım
            </a>
        </div>
    </div>
</section>

<!-- 13. VERİ MERKEZLERİ -->
<section class="cdg-dc-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Veri Merkezleri</div>
            <h2>Tier-3 sertifikalı <span class="cdg-text-gradient">veri merkezleri</span></h2>
            <p>Türkiye ve Avrupa'da yedekli altyapı ile %99.99 uptime garantisi.</p>
        </div>
        <div class="cdg-dc-grid">
            <div class="cdg-dc-card cdg-dc-card-pulse"><div class="cdg-dc-flag">🇹🇷</div><h3>İstanbul</h3><p>Ana veri merkezi · Tier-3 · 24/7 fiziksel güvenlik</p><div class="cdg-dc-specs"><span><i class="bi bi-fire"></i> 7ms ping</span><span><i class="bi bi-shield-fill-check"></i> ISO 27001</span></div></div>
            <div class="cdg-dc-card cdg-dc-card-pulse"><div class="cdg-dc-flag">🇩🇪</div><h3>Frankfurt</h3><p>Avrupa hub · Tier-3 · 100 Gbps backbone</p><div class="cdg-dc-specs"><span><i class="bi bi-fire"></i> 35ms ping</span><span><i class="bi bi-shield-fill-check"></i> ISO 27001</span></div></div>
            <div class="cdg-dc-card cdg-dc-card-pulse"><div class="cdg-dc-flag">🇳🇱</div><h3>Amsterdam</h3><p>AMS-IX hub · Tier-3 · DDoS korumalı</p><div class="cdg-dc-specs"><span><i class="bi bi-fire"></i> 42ms ping</span><span><i class="bi bi-shield-fill-check"></i> ISO 27001</span></div></div>
        </div>
    </div>
</section>

<!-- 14. FAQ -->
<section class="cdg-faq-section cdg-section">
    <div class="cdg-container">
        <div class="cdg-faq-grid">
            <div class="cdg-faq-intro">
                <div class="cdg-eyebrow">Sık Sorulan Sorular</div>
                <h2>Aklınızdaki <span class="cdg-text-gradient">tüm sorulara cevap</span></h2>
                <p>En çok sorulan soruları sizin için derledik.</p>
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

<!-- 15. SERTİFİKALAR -->
<section class="cdg-certs-section">
    <div class="cdg-container">
        <div class="cdg-certs-row">
            <div class="cdg-cert-item"><i class="bi bi-shield-fill-check"></i><div><strong>ISO 27001</strong><span>Bilgi Güvenliği</span></div></div>
            <div class="cdg-cert-item"><i class="bi bi-award-fill"></i><div><strong>ETBİS Kayıtlı</strong><span>E-Ticaret Sistemi</span></div></div>
            <div class="cdg-cert-item"><i class="bi bi-file-earmark-lock-fill"></i><div><strong>KVKK Uyumlu</strong><span>Veri Koruma</span></div></div>
            <div class="cdg-cert-item"><i class="bi bi-patch-check-fill"></i><div><strong>SSL Korumalı</strong><span>256-bit AES</span></div></div>
            <div class="cdg-cert-item"><i class="bi bi-trophy-fill"></i><div><strong>Yerli Yazılım</strong><span>%100 Türk yapımı</span></div></div>
        </div>
    </div>
</section>

<!-- DESTEK + SON CTA -->
<section class="cdg-support-cta cdg-section">
    <div class="cdg-container">
        <div class="cdg-support-card">
            <div class="cdg-support-illu">
                <div class="cdg-support-orb"></div>
                <i class="bi bi-headset"></i>
            </div>
            <div class="cdg-support-content">
                <div class="cdg-eyebrow" style="color:rgba(255,255,255,0.85);">7/24 Destek</div>
                <h2>Yardım mı gerekiyor? <span class="cdg-text-gradient-light">Buradayız!</span></h2>
                <p>CODEGA destek ekibi sorularınıza haftanın 7 günü, günün 24 saati cevap vermek için hazır.</p>
                <div class="cdg-support-features">
                    <div class="cdg-support-feature"><i class="bi bi-telephone-fill"></i><div><div class="title">Telefon</div><div class="meta">0 510 220 42 06</div></div></div>
                    <div class="cdg-support-feature"><i class="bi bi-whatsapp"></i><div><div class="title">WhatsApp</div><div class="meta">Anlık destek</div></div></div>
                    <div class="cdg-support-feature"><i class="bi bi-chat-dots-fill"></i><div><div class="title">Canlı Destek</div><div class="meta">7/24</div></div></div>
                </div>
                <div class="cdg-support-actions">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-white"><i class="bi bi-envelope-fill"></i> İletişim</a>
                    <a href="<?php echo $register_url; ?>" class="cdg-btn cdg-btn-ghost"><i class="bi bi-person-plus-fill"></i> Kayıt Ol</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Hemen Başlayın</div>
            <h2>Profesyonel hosting deneyimine<br><span class="cdg-text-gradient">CODEGA</span> ile başlayın</h2>
            <p>Bugün kayıt olun, %30 indirim + ücretsiz domain + 30 gün para iade garantisi.</p>
            <div class="cdg-final-cta-actions">
                <?php if($mod_hosting): ?><a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-rocket-takeoff-fill"></i> Hosting Al</a><?php endif; ?>
                <?php if($mod_domain): ?><a href="<?php echo $domain_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-globe2"></i> Domain Sorgula</a><?php endif; ?>
            </div>
            <div class="cdg-final-trust">
                <span><i class="bi bi-shield-check"></i> %99.99 Uptime</span>
                <span><i class="bi bi-arrow-counterclockwise"></i> 30 Gün İade</span>
                <span><i class="bi bi-headset"></i> 7/24 Destek</span>
                <span><i class="bi bi-cpu-fill"></i> AI Optimizasyon</span>
            </div>
        </div>
    </div>
</section>

<!-- WhatsApp -->
<a href="https://wa.me/905102204206" class="cdg-floating-wa" target="_blank" rel="noopener" title="WhatsApp Destek">
    <i class="bi bi-whatsapp"></i>
    <span class="cdg-floating-pulse"></span>
</a>
