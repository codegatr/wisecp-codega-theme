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
$basket_url  = cdg_link('basket');
$domain_url  = cdg_link('domain');

// === Gerçek paket çekimi (anasayfa ile aynı sistem) ===
$pricing_categories = [];
$wisecp_loaded = false;

if(class_exists('Products') && method_exists('Products', 'getList')) {
    try {
        $hosting_products = @Products::getList(['type' => 'hosting', 'status' => 'active']);
        if($hosting_products && is_array($hosting_products) && count($hosting_products) > 0) {
            $by_category = [];
            foreach($hosting_products as $p) {
                $cat_id = $p['category_id'] ?? $p['cid'] ?? 0;
                $cat_name = $p['category_name'] ?? 'Hosting Paketleri';
                if(!isset($by_category[$cat_id])) {
                    $by_category[$cat_id] = ['name' => $cat_name, 'packages' => []];
                }
                $by_category[$cat_id]['packages'][] = $p;
            }
            if(count($by_category) > 0) {
                $pricing_categories = array_values($by_category);
                $wisecp_loaded = true;
            }
        }
    } catch(Exception $e) { /* fallback */ }
}

if(!$wisecp_loaded) {
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
}
?>

<!-- HERO -->
<section class="cdg-page-hero">
    <div class="cdg-page-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
        <div class="cdg-auth-particles">
            <span></span><span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
    <div class="cdg-container">
        <div class="cdg-page-hero-content">
            <div class="cdg-eyebrow cdg-eyebrow-glow"><i class="bi bi-hdd-network-fill"></i> Hosting Paketleri</div>
            <h1>NVMe SSD <span class="cdg-text-gradient-light">hosting paketleri</span></h1>
            <p>LiteSpeed Enterprise, %99.99 uptime, 7/24 destek. <strong>Tüm paketlerde ücretsiz SSL ve domain seçeneği.</strong></p>
            <div class="cdg-page-hero-cta">
                <a href="#packages" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-arrow-down-circle-fill"></i> Paketleri Gör</a>
                <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-question-circle-fill"></i> Yardım Al</a>
            </div>
        </div>
    </div>
</section>

<!-- METRİKLER -->
<section class="cdg-section" style="padding-top:48px;">
    <div class="cdg-container">
        <div class="cdg-perf-grid">
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#10b981;"><i class="bi bi-speedometer2"></i></div><div class="cdg-perf-num">28<span>ms</span></div><div class="cdg-perf-lbl">Yanıt Süresi</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#3b82f6;"><i class="bi bi-graph-up-arrow"></i></div><div class="cdg-perf-num">%99.99<span></span></div><div class="cdg-perf-lbl">Uptime SLA</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#f59e0b;"><i class="bi bi-lightning-charge-fill"></i></div><div class="cdg-perf-num">9<span>x</span></div><div class="cdg-perf-lbl">Hızlı LiteSpeed</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#8b5cf6;"><i class="bi bi-shield-fill-check"></i></div><div class="cdg-perf-num">SSL<span></span></div><div class="cdg-perf-lbl">Ücretsiz</div></div>
        </div>
    </div>
</section>

<!-- PRICING (3 sekmeli) -->
<section class="cdg-pricing-tabbed cdg-section" id="packages">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Paketleri</div>
            <h2>Her ihtiyaca uygun <span class="cdg-text-gradient">hosting paketleri</span></h2>
            <p>Bireysel sitelerden bayilik çözümlerine, NVMe SSD + LiteSpeed altyapısı ile.</p>
        </div>

        <div class="cdg-pricing-tabs" role="tablist">
            <?php foreach($pricing_categories as $i => $cat): ?>
            <button type="button" class="cdg-pricing-tab<?php echo $i === 0 ? ' active' : ''; ?>" data-tab="<?php echo $cat['id']; ?>" role="tab">
                <i class="bi <?php echo $cat['icon']; ?>" style="color:<?php echo $cat['color']; ?>;"></i>
                <span><?php echo htmlspecialchars($cat['name']); ?></span>
                <small><?php echo count($cat['packages']); ?> paket</small>
            </button>
            <?php endforeach; ?>
        </div>

        <?php foreach($pricing_categories as $i => $cat): ?>
        <div class="cdg-pricing-pane<?php echo $i === 0 ? ' active' : ''; ?>" data-pane="<?php echo $cat['id']; ?>" role="tabpanel">
            <div class="cdg-pricing-pane-desc"><?php echo htmlspecialchars($cat['desc']); ?></div>
            <div class="cdg-pricing-grid cdg-pricing-grid-<?php echo count($cat['packages']); ?>">
                <?php foreach($cat['packages'] as $pkg): ?>
                <div class="cdg-price-card<?php echo !empty($pkg['highlight']) ? ' cdg-price-card-highlight' : ''; ?>">
                    <?php if(!empty($pkg['highlight'])): ?><div class="cdg-price-ribbon">EN POPÜLER</div><?php endif; ?>
                    <div class="cdg-price-cat-tag" style="color:<?php echo $cat['color']; ?>;background:<?php echo $cat['color']; ?>15;">
                        <i class="bi <?php echo $cat['icon']; ?>"></i> <?php echo htmlspecialchars($cat['name']); ?>
                    </div>
                    <h3 class="cdg-price-name"><?php echo htmlspecialchars($pkg['name']); ?></h3>
                    <p class="cdg-price-subtitle"><?php echo htmlspecialchars($pkg['subtitle']); ?></p>
                    <div class="cdg-price-amount">
                        <div class="cdg-price-current">
                            <span class="cdg-price-curr"><?php echo $pkg['currency']; ?></span>
                            <span class="cdg-price-num"><?php echo $pkg['price']; ?></span>
                        </div>
                        <span class="cdg-price-period">/<?php echo $pkg['period']; ?></span>
                    </div>
                    <ul class="cdg-price-features">
                        <?php foreach($pkg['features'] as $feat): ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($feat); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn <?php echo !empty($pkg['highlight']) ? 'cdg-btn-primary cdg-btn-glow' : 'cdg-btn-outline'; ?> cdg-btn-block">
                        <i class="bi bi-cart-plus"></i> Sepete Ekle
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

<!-- AVANTAJLAR -->
<section class="cdg-section" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Tüm Paketlerde</div>
            <h2>Standart olarak <span class="cdg-text-gradient">size sunduklarımız</span></h2>
        </div>
        <div class="cdg-adv-grid cdg-adv-grid-4">
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-lightning-charge-fill"></i></div><h3>NVMe SSD</h3><p>10x daha hızlı I/O performansı.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-speedometer2"></i></div><h3>LiteSpeed</h3><p>Apache'den 9 kat hızlı.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-shield-fill-check"></i></div><h3>Ücretsiz SSL</h3><p>Let's Encrypt otomatik.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-arrow-clockwise"></i></div><h3>Yedekleme</h3><p>Saatlik / günlük yedek.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div><h3>Ücretsiz Taşıma</h3><p>5 sitee kadar kesintisiz.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-fingerprint"></i></div><h3>Imunify360</h3><p>Malware + WAF koruma.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-graph-up-arrow"></i></div><h3>%99.99 Uptime</h3><p>SLA garantili hizmet.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-headset"></i></div><h3>7/24 Destek</h3><p>WhatsApp + telefon + panel.</p></div>
        </div>
    </div>
</section>

<!-- SSS -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sık Sorulan</div>
            <h2>Hosting hakkında <span class="cdg-text-gradient">bilmek istedikleriniz</span></h2>
        </div>
        <div class="cdg-faq-list" style="max-width:780px;margin:32px auto 0;">
            <details class="cdg-faq-item" open>
                <summary><span>Hangi paketi seçmeliyim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Tek site / kişisel: <strong>Linux Hosting 1 (150 ₺/yıl)</strong>. Hobi/blog: <strong>Linux Hosting 2 (289 ₺/yıl)</strong>. Kurumsal: <strong>Profesyonel 1 (450 ₺/yıl)</strong>. Yüksek trafik: <strong>Profesyonel 2 (750 ₺/yıl)</strong>. Karar veremiyorsanız bize danışın!</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Mevcut sitemi taşımanız ücretli mi?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Hayır, <strong>5 sitee kadar tamamen ücretsiz</strong> taşıyoruz. cPanel backup, FTP+veritabanı veya manuel — her yöntem destekleniyor. Tipik taşıma süresi 1-3 saat.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>İade politikanız nasıl işliyor?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer"><strong>30 gün koşulsuz iade garantisi.</strong> Beğenmezseniz panelden talep oluşturun, 1 iş günü içinde tam iade. Domain ücreti hariç (kayıt yapıldıysa iade edilemez).</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Paketimi sonradan yükseltebilir miyim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Evet! Panelden tek tıkla daha üst pakete geçebilirsiniz. Sadece <strong>fiyat farkını</strong> ödersiniz, veri kaybı yaşanmaz, sıfır kesinti.</div>
            </details>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Hemen Başlayın</div>
            <h2>Profesyonel <span class="cdg-text-gradient">hosting deneyimine</span> hoş geldiniz</h2>
            <p>Bugün kayıt olun, ücretsiz SSL + ücretsiz taşıma + 30 gün para iade garantisi.</p>
            <div class="cdg-final-cta-actions">
                <a href="#packages" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-rocket-takeoff-fill"></i> Paketleri İncele</a>
                <a href="<?php echo $domain_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-globe2"></i> Domain Sorgula</a>
            </div>
        </div>
    </div>
</section>
