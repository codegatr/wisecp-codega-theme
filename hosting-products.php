<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';

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

// === WiseCP Runtime'dan paketleri al (Classic temasi gibi) ===
// Runtime variable'lar (WiseCP setler): $showCategory, $category, $category2,
// $get_list, $get_categories, $category_route, $category_tab_route, $columns

// Paket render helper - WiseCP $product structure'ini standart formata cevirir
$cdg_format_package = function($product) use (&$columns) {
    $popular = !empty($product['options']['popular']);

    $price_text = '';
    $period_text = '';
    $amount_symbol = '';
    $amount_value = '';

    $prices = $product['prices'] ?? [];
    if(isset($prices[0]) && is_array($prices[0])) {
        $amount = $prices[0]['amount'] ?? 0;
        $cid    = $prices[0]['cid'] ?? 0;
        $override = !empty($product['override_usrcurrency']);

        if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $cid) {
            try {
                $price_text = Money::formatter_symbol($amount, $cid, !$override);
            } catch(\Throwable $e) {
                $price_text = number_format((float)$amount, 2, ',', '.');
            }
        } else {
            $price_text = number_format((float)$amount, 2, ',', '.');
        }

        // Currency symbol ayikla
        global $currency_symbols;
        if(is_array($currency_symbols ?? null) && $price_text) {
            $parts = explode(' ', $price_text);
            if(count($parts) >= 2) {
                if(in_array(reset($parts), $currency_symbols)) {
                    $amount_symbol = array_shift($parts);
                    $amount_value = implode(' ', $parts);
                } elseif(in_array(end($parts), $currency_symbols)) {
                    $amount_symbol = array_pop($parts);
                    $amount_value = implode(' ', $parts);
                } else {
                    $amount_value = $price_text;
                }
            } else {
                $amount_value = $price_text;
            }
        } else {
            $amount_value = $price_text;
        }

        // Periyot
        if(class_exists('View') && method_exists('View', 'period')) {
            try {
                $period_text = View::period($prices[0]['time'] ?? 1, $prices[0]['period'] ?? 'year');
            } catch(\Throwable $e) {
                $period_text = ($prices[0]['time'] ?? 1) . ' ' . ($prices[0]['period'] ?? 'year');
            }
        }
    }

    // Features parse
    $features = [];
    $raw_features = $product['features'] ?? '';
    $json_features = null;
    if(class_exists('Utility') && method_exists('Utility', 'jdecode')) {
        try { $json_features = Utility::jdecode($raw_features, true); } catch(\Throwable $e) {}
    }
    if(!$json_features && is_string($raw_features) && trim($raw_features)) {
        $decoded = json_decode($raw_features, true);
        if(is_array($decoded)) $json_features = $decoded;
    }

    if(is_array($json_features) && isset($columns) && is_array($columns)) {
        foreach($columns as $col) {
            $col_id = $col['id'] ?? null;
            $col_name = $col['name'] ?? '';
            if($col_id !== null && isset($json_features[$col_id])) {
                $val = $json_features[$col_id];
                if($val !== null && $val !== '') {
                    $features[] = $col_name . ': ' . $val;
                }
            }
        }
    } elseif(is_array($json_features)) {
        foreach($json_features as $k => $v) {
            if($v !== null && $v !== '') {
                $features[] = (is_string($k) ? $k . ': ' : '') . (is_array($v) ? json_encode($v) : (string)$v);
            }
        }
    } elseif(is_string($raw_features) && trim($raw_features)) {
        $lines = preg_split('/\r\n|\r|\n/', $raw_features);
        foreach($lines as $line) {
            $line = trim($line);
            if($line !== '') $features[] = $line;
        }
    }

    return [
        'name'          => $product['title'] ?? ($product['name'] ?? 'Paket'),
        'subtitle'      => $product['sub_title'] ?? ($product['description'] ?? ''),
        'amount_value'  => $amount_value,
        'amount_symbol' => $amount_symbol,
        'amount_pos'    => $amount_symbol ? 'left' : '',
        'period'        => $period_text ?: 'aylık',
        'features'      => $features,
        'highlight'     => $popular,
        'buy_link'      => $product['buy_link'] ?? '',
        'buy_label'     => $product['optionsl']['buy_button_name'] ?? 'Sepete Ekle',
    ];
};

// === Kategorileri al ===
$pricing_categories = [];
$wisecp_used = false;

$color_cycle = [
    ['icon' => 'bi-rocket-takeoff', 'color' => '#10b981'],
    ['icon' => 'bi-stars',          'color' => '#2E3B4E'],
    ['icon' => 'bi-people-fill',    'color' => '#8b5cf6'],
    ['icon' => 'bi-trophy-fill',    'color' => '#f59e0b'],
    ['icon' => 'bi-shield-fill-check','color' => '#00D3E5'],
];

// Yontem 1: $get_categories + $get_list
if(isset($get_categories) && is_callable($get_categories) && isset($category) && is_array($category)) {
    try {
        $cats = call_user_func($get_categories, $category['id'] ?? 0, $category['kind'] ?? 'hosting');
        if($cats && is_array($cats) && count($cats) > 0) {
            $i = 0;
            foreach($cats as $cat) {
                if(!is_array($cat)) continue;
                $cat_id = $cat['id'] ?? 0;
                $cat_kind = $cat['kind'] ?? 'hosting';

                $list = [];
                if(isset($get_list) && is_callable($get_list)) {
                    try { $list = call_user_func($get_list, $cat_id, $cat_kind); } catch(\Throwable $e) { $list = []; }
                }

                if(!$list || !is_array($list) || count($list) === 0) continue;

                $packages = [];
                foreach($list as $product) {
                    if(is_array($product)) $packages[] = $cdg_format_package($product);
                }

                if(count($packages) === 0) continue;

                $style = $color_cycle[$i % count($color_cycle)];
                $pricing_categories[] = [
                    'id'       => 'cat_' . $cat_id,
                    'name'     => $cat['title'] ?? ($cat['name'] ?? 'Kategori'),
                    'desc'     => $cat['sub_title'] ?? ($cat['description'] ?? ''),
                    'icon'     => $style['icon'],
                    'color'    => $style['color'],
                    'packages' => $packages,
                ];
                $i++;
            }
            if(count($pricing_categories) > 0) $wisecp_used = true;
        }
    } catch(\Throwable $e) {}
}

// Yontem 2: $showCategory + $get_list (tek kategori)
if(!$wisecp_used && isset($showCategory) && is_array($showCategory) && isset($get_list) && is_callable($get_list)) {
    try {
        $list = call_user_func($get_list, $showCategory['id'] ?? 0, $showCategory['kind'] ?? 'hosting');
        if($list && is_array($list) && count($list) > 0) {
            $packages = [];
            foreach($list as $product) {
                if(is_array($product)) $packages[] = $cdg_format_package($product);
            }
            if(count($packages) > 0) {
                $pricing_categories[] = [
                    'id'       => 'main',
                    'name'     => $showCategory['title'] ?? 'Hosting Paketleri',
                    'desc'     => $showCategory['sub_title'] ?? '',
                    'icon'     => 'bi-rocket-takeoff',
                    'color'    => '#2E3B4E',
                    'packages' => $packages,
                ];
                $wisecp_used = true;
            }
        }
    } catch(\Throwable $e) {}
}

// Yontem 3: Products::getList API
if(!$wisecp_used && class_exists('Products') && method_exists('Products', 'getList')) {
    try {
        $hosting_products = @Products::getList(['type' => 'hosting', 'status' => 'active']);
        if($hosting_products && is_array($hosting_products) && count($hosting_products) > 0) {
            $by_category = [];
            foreach($hosting_products as $p) {
                if(!is_array($p)) continue;
                $cat_id = $p['category_id'] ?? ($p['cid'] ?? 0);
                $cat_name = $p['category_name'] ?? 'Hosting Paketleri';
                if(!isset($by_category[$cat_id])) {
                    $by_category[$cat_id] = ['name' => $cat_name, 'list' => []];
                }
                $by_category[$cat_id]['list'][] = $p;
            }
            $i = 0;
            foreach($by_category as $cat_id => $bucket) {
                $packages = [];
                foreach($bucket['list'] as $product) {
                    $packages[] = $cdg_format_package($product);
                }
                if(count($packages) === 0) continue;
                $style = $color_cycle[$i % count($color_cycle)];
                $pricing_categories[] = [
                    'id'       => 'cat_' . $cat_id,
                    'name'     => $bucket['name'],
                    'desc'     => '',
                    'icon'     => $style['icon'],
                    'color'    => $style['color'],
                    'packages' => $packages,
                ];
                $i++;
            }
            if(count($pricing_categories) > 0) $wisecp_used = true;
        }
    } catch(\Throwable $e) {}
}

// Yontem 4: Hard-coded fallback (admin'e mesaj icin)
if(empty($pricing_categories)) {
    $pricing_categories = [
        [
            'id' => 'demo', 'name' => 'Hosting Paketleri', 'icon' => 'bi-rocket-takeoff', 'color' => '#2E3B4E',
            'desc' => 'WiseCP\'de henüz hosting paketi tanımlanmamış. Admin panelinden ekleyebilirsiniz.',
            'packages' => [],
        ],
    ];
}

// Hero baslik
$hero_title = 'Hosting Paketleri';
$hero_subtitle = 'NVMe SSD, LiteSpeed Enterprise, %99.99 uptime, 7/24 destek.';
if(isset($showCategory) && is_array($showCategory)) {
    if(!empty($showCategory['title'])) $hero_title = $showCategory['title'];
    if(!empty($showCategory['sub_title'])) $hero_subtitle = $showCategory['sub_title'];
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
            <h1><?php echo htmlspecialchars($hero_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
            <p><?php echo htmlspecialchars($hero_subtitle, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <strong>Tüm paketlerde ücretsiz SSL.</strong></p>
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
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#00D3E5;"><i class="bi bi-graph-up-arrow"></i></div><div class="cdg-perf-num">%99.99<span></span></div><div class="cdg-perf-lbl">Uptime SLA</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#f59e0b;"><i class="bi bi-lightning-charge-fill"></i></div><div class="cdg-perf-num">9<span>x</span></div><div class="cdg-perf-lbl">Hızlı LiteSpeed</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#8b5cf6;"><i class="bi bi-shield-fill-check"></i></div><div class="cdg-perf-num">SSL<span></span></div><div class="cdg-perf-lbl">Ücretsiz</div></div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="cdg-pricing-tabbed cdg-section" id="packages">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Paketleri</div>
            <h2>Her ihtiyaca uygun <span class="cdg-text-gradient">hosting paketleri</span></h2>
            <p>Bireysel sitelerden bayilik çözümlerine, NVMe SSD + LiteSpeed altyapısı ile.</p>
        </div>

        <?php if(count($pricing_categories) > 1): ?>
        <div class="cdg-pricing-tabs" role="tablist">
            <?php foreach($pricing_categories as $i => $cat): ?>
            <button type="button" class="cdg-pricing-tab<?php echo $i === 0 ? ' active' : ''; ?>" data-tab="<?php echo htmlspecialchars($cat['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" role="tab">
                <i class="bi <?php echo htmlspecialchars($cat['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="color:<?php echo htmlspecialchars($cat['color'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>;"></i>
                <span><?php echo htmlspecialchars($cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <small><?php echo count($cat['packages']); ?> paket</small>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php foreach($pricing_categories as $i => $cat):
            $pkg_count = count($cat['packages']);
            $grid_class = 'cdg-pricing-grid-' . min(4, max(1, $pkg_count ?: 1));
        ?>
        <div class="cdg-pricing-pane<?php echo $i === 0 ? ' active' : ''; ?>" data-pane="<?php echo htmlspecialchars($cat['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" role="tabpanel">
            <?php if(!empty($cat['desc'])): ?>
            <div class="cdg-pricing-pane-desc"><?php echo htmlspecialchars($cat['desc'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if($pkg_count === 0): ?>
            <div style="text-align:center;padding:60px 20px;color:#64748b;">
                <i class="bi bi-info-circle" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:16px;"></i>
                <p style="font-size:15px;margin:0;">Bu kategoride henüz paket bulunmuyor.</p>
            </div>
            <?php else: ?>

            <div class="cdg-pricing-grid <?php echo $grid_class; ?>">
                <?php foreach($cat['packages'] as $pkg): ?>
                <div class="cdg-price-card<?php echo !empty($pkg['highlight']) ? ' cdg-price-card-highlight' : ''; ?>">
                    <?php if(!empty($pkg['highlight'])): ?>
                    <div class="cdg-price-ribbon">EN POPÜLER</div>
                    <?php endif; ?>
                    <div class="cdg-price-cat-tag" style="color:<?php echo htmlspecialchars($cat['color'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>;background:<?php echo htmlspecialchars($cat['color'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>15;">
                        <i class="bi <?php echo htmlspecialchars($cat['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i> <?php echo htmlspecialchars($cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                    <h3 class="cdg-price-name"><?php echo htmlspecialchars($pkg['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                    <?php if(!empty($pkg['subtitle'])): ?>
                    <p class="cdg-price-subtitle"><?php echo htmlspecialchars($pkg['subtitle'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <div class="cdg-price-amount">
                        <div class="cdg-price-current">
                            <?php if($pkg['amount_pos'] === 'left' && !empty($pkg['amount_symbol'])): ?>
                            <span class="cdg-price-curr"><?php echo htmlspecialchars($pkg['amount_symbol'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <span class="cdg-price-num"><?php echo htmlspecialchars($pkg['amount_value'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <?php if($pkg['amount_pos'] === 'right' && !empty($pkg['amount_symbol'])): ?>
                            <span class="cdg-price-curr"><?php echo htmlspecialchars($pkg['amount_symbol'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($pkg['period'])): ?>
                        <span class="cdg-price-period">/<?php echo htmlspecialchars($pkg['period'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($pkg['features']) && is_array($pkg['features'])): ?>
                    <ul class="cdg-price-features">
                        <?php foreach($pkg['features'] as $feat): ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars((string)$feat, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <?php
                        $btn_link = !empty($pkg['buy_link']) && $pkg['buy_link'] !== '#' ? $pkg['buy_link'] : $basket_url;
                        $btn_class = !empty($pkg['highlight']) ? 'cdg-btn-primary cdg-btn-glow' : 'cdg-btn-outline';
                    ?>
                    <a href="<?php echo htmlspecialchars($btn_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn <?php echo $btn_class; ?> cdg-btn-block">
                        <i class="bi bi-cart-plus"></i> <?php echo htmlspecialchars($pkg['buy_label'] ?? 'Sepete Ekle', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
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
                <div class="cdg-faq-answer">İhtiyacınıza göre yukarıdaki paket karşılaştırmasını inceleyin. Bireysel kullanıcılar için ekonomik paketler, kurumsal projeler için profesyonel paketler önerilir. Karar veremiyorsanız bize danışın!</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Mevcut sitemi taşımanız ücretli mi?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Hayır, <strong>5 sitee kadar tamamen ücretsiz</strong> taşıyoruz. cPanel backup, FTP+veritabanı veya manuel — her yöntem destekleniyor. Tipik taşıma süresi 1-3 saat.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>İade politikanız nasıl işliyor?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer"><strong>30 gün koşulsuz iade garantisi.</strong> Beğenmezseniz panelden talep oluşturun, 1 iş günü içinde tam iade. Domain ücreti hariç.</div>
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
            <div class="cdg-final-cta-bg">
                <div class="cdg-final-cta-blob cdg-final-cta-blob-1"></div>
                <div class="cdg-final-cta-blob cdg-final-cta-blob-2"></div>
                <div class="cdg-final-cta-grid"></div>
            </div>
            <div class="cdg-final-cta-inner">
                <div class="cdg-eyebrow cdg-eyebrow-light">Hemen Başlayın</div>
                <h2 class="cdg-final-cta-title">Profesyonel <span>hosting deneyimine</span> hoş geldiniz</h2>
                <p class="cdg-final-cta-lead">Bugün kayıt olun, ücretsiz SSL + ücretsiz taşıma + 30 gün para iade garantisi.</p>
                <div class="cdg-final-cta-actions">
                    <a href="#packages" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-rocket-takeoff-fill"></i> Paketleri İncele</a>
                    <a href="<?php echo $domain_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-globe2"></i> Domain Sorgula</a>
                </div>
            </div>
        </div>
    </div>
</section>
