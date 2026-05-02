<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = [
    'page' => 'hosting-products',
    'dataTables',
];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$contact_url = cdg_link('contact');
$domain_url  = cdg_link('domain');

// Currency symbol cache
$currency_symbols = [];
if(class_exists('Money') && method_exists('Money', 'getCurrencies')) {
    foreach(Money::getCurrencies() AS $currency){
        $symbol = !empty($currency["prefix"]) ? trim($currency["prefix"]) : trim($currency["suffix"] ?? '');
        if(!$symbol) $symbol = $currency["code"] ?? '₺';
        $currency_symbols[] = $symbol;
    }
}
$GLOBALS["currency_symbols"] = $currency_symbols;

// Bir paket kartını render eden fonksiyon - WiseCP product yapısını kullanır
$render_product_card = function($product) use ($currency_symbols) {
    $prices = $product["prices"] ?? [];
    $product_amount = '';
    $product_period = '';
    $popular = !empty($product["options"]["popular"]);
    $amount_symbol = '';

    if(isset($prices[0]) && !empty($prices[0])) {
        $amount = $prices[0]["amount"];
        $cid = $prices[0]["cid"];
        $override = $product["override_usrcurrency"] ?? false;
        $product_amount = Money::formatter_symbol($amount, $cid, !$override);

        $split_amount = explode(" ", $product_amount);
        if(in_array(current($split_amount), $currency_symbols)){
            $amount_symbol = current($split_amount);
            array_shift($split_amount);
            $product_amount = implode(" ", $split_amount);
        } elseif(in_array(end($split_amount), $currency_symbols)){
            $amount_symbol = end($split_amount);
            array_pop($split_amount);
            $product_amount = implode(" ", $split_amount);
        }

        if(class_exists('View') && method_exists('View', 'period')) {
            $product_period = View::period($prices[0]["time"] ?? 1, $prices[0]["period"] ?? 'm');
        } else {
            $period_map = ['m' => 'Aylık', 'q' => '3 Aylık', 's' => '6 Aylık', 'y' => 'Yıllık', 'b' => '2 Yıllık', 't' => '3 Yıllık'];
            $product_period = $period_map[$prices[0]["period"] ?? 'y'] ?? 'Yıllık';
        }
    }

    // Özellikleri çıkar
    $features_html = '';
    if(!empty($product["features"])) {
        $features = class_exists('Utility') && method_exists('Utility', 'jdecode') ? Utility::jdecode($product["features"], true) : null;
        if($features && is_array($features)) {
            foreach($features as $key => $value) {
                if(is_numeric($key)) {
                    $features_html .= '<li><i class="bi bi-check-circle-fill"></i> ' . htmlspecialchars($value) . '</li>';
                } else {
                    $features_html .= '<li><i class="bi bi-check-circle-fill"></i> <strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
                }
            }
        } else {
            // Plain text features
            $lines = preg_split('/\r\n|\r|\n/', strip_tags($product["features"]));
            foreach($lines as $line) {
                $line = trim($line);
                if($line) $features_html .= '<li><i class="bi bi-check-circle-fill"></i> ' . htmlspecialchars($line) . '</li>';
            }
        }
    }

    $buy_link = $product["buy_link"] ?? cdg_link('order-steps-hosting', [$product['id'] ?? '']);
    $product_title = $product["title"] ?? $product["name"] ?? 'Hosting Paketi';
    ?>
    <div class="cdg-price-card<?php echo $popular ? ' cdg-price-card-highlight' : ''; ?>">
        <?php if($popular): ?><div class="cdg-price-ribbon">EN POPÜLER</div><?php endif; ?>

        <h3 class="cdg-price-name"><?php echo htmlspecialchars($product_title); ?></h3>

        <div class="cdg-price-amount">
            <div class="cdg-price-current">
                <?php if($amount_symbol): ?><span class="cdg-price-curr"><?php echo $amount_symbol; ?></span><?php endif; ?>
                <span class="cdg-price-num"><?php echo $product_amount ?: '-'; ?></span>
            </div>
            <?php if($product_period): ?><span class="cdg-price-period">/<?php echo htmlspecialchars($product_period); ?></span><?php endif; ?>
        </div>

        <?php if(count($prices) > 1): ?>
        <select class="cdg-price-period-select" onchange="cdgUpdatePrice(this)">
            <?php foreach($prices as $price_option):
                $formatted_price = Money::formatter_symbol($price_option["amount"], $price_option["cid"], !($product["override_usrcurrency"] ?? false));
                $formatted_period = class_exists('View') ? View::period($price_option["time"], $price_option["period"]) : 'Yıllık';
                $option_link = $product["buy_link"] ?? '';
                if($option_link) {
                    $option_link .= (strpos($option_link, '?') !== false ? '&' : '?') . 'period=' . urlencode($price_option["id"] ?? '');
                }
            ?>
            <option value="<?php echo htmlspecialchars($formatted_price); ?>" data-url="<?php echo htmlspecialchars($option_link); ?>">
                <?php echo htmlspecialchars($formatted_period . ' / ' . $formatted_price); ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <?php if($features_html): ?>
        <ul class="cdg-price-features">
            <?php echo $features_html; ?>
        </ul>
        <?php endif; ?>

        <a href="<?php echo htmlspecialchars($buy_link); ?>" class="cdg-btn <?php echo $popular ? 'cdg-btn-primary cdg-btn-glow' : 'cdg-btn-outline'; ?> cdg-btn-block cdg-product-buy-btn">
            <i class="bi bi-cart-plus"></i> Hemen Sipariş Et
        </a>
    </div>
    <?php
};

// Bir kategorinin paketlerini render eden fonksiyon
$render_category_section = function($cat, $list) use ($render_product_card) {
    if(empty($list)) return;
    $count = count($list);
    $grid_class = 'cdg-pricing-grid-' . min($count, 4);
    ?>
    <div class="cdg-pricing-grid <?php echo $grid_class; ?>">
        <?php foreach($list as $product) $render_product_card($product); ?>
    </div>
    <?php
};
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
            <div class="cdg-eyebrow cdg-eyebrow-glow">
                <i class="bi bi-hdd-network-fill"></i>
                <?php echo isset($showCategory['title']) ? htmlspecialchars($showCategory['title']) : 'Hosting Paketleri'; ?>
            </div>
            <h1>
                <?php if(isset($showCategory['title'])): ?>
                    <?php echo htmlspecialchars($showCategory['title']); ?>
                <?php else: ?>
                    NVMe SSD <span class="cdg-text-gradient-light">hosting paketleri</span>
                <?php endif; ?>
            </h1>
            <p>
                <?php if(isset($showCategory['sub_title']) && $showCategory['sub_title']): ?>
                    <?php echo htmlspecialchars(strip_tags($showCategory['sub_title'])); ?>
                <?php else: ?>
                    LiteSpeed Enterprise, %99.99 uptime, 7/24 destek. <strong>Tüm paketlerde ücretsiz SSL.</strong>
                <?php endif; ?>
            </p>
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
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#3b82f6;"><i class="bi bi-graph-up-arrow"></i></div><div class="cdg-perf-num">%99.99</div><div class="cdg-perf-lbl">Uptime SLA</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#f59e0b;"><i class="bi bi-lightning-charge-fill"></i></div><div class="cdg-perf-num">9<span>x</span></div><div class="cdg-perf-lbl">LiteSpeed</div></div>
            <div class="cdg-perf-card"><div class="cdg-perf-icon" style="color:#8b5cf6;"><i class="bi bi-shield-fill-check"></i></div><div class="cdg-perf-num">SSL</div><div class="cdg-perf-lbl">Ücretsiz</div></div>
        </div>
    </div>
</section>

<!-- PAKETLER (WiseCP runtime $get_list, $get_categories) -->
<section class="cdg-section" id="packages">
    <div class="cdg-container">
        <?php
        // 1. Direkt paketler varsa göster (tek kategori)
        if(isset($showCategory) && isset($get_list) && is_callable($get_list)) {
            // Mevcut kategorinin paketleri
            $main_list = $get_list($showCategory["id"], $showCategory["kind"] ?? 'hosting');

            if($main_list) {
                ?>
                <div class="cdg-section-head">
                    <h2><?php echo htmlspecialchars($showCategory["title"]); ?></h2>
                    <?php if(!empty($showCategory["sub_title"])): ?>
                    <p><?php echo htmlspecialchars(strip_tags($showCategory["sub_title"])); ?></p>
                    <?php endif; ?>
                </div>
                <?php $render_category_section($showCategory, $main_list); ?>
                <?php
            }

            // Alt kategoriler
            if(isset($get_categories) && is_callable($get_categories) && isset($category)) {
                $sub_categories = $get_categories($category["id"], $category["kind"] ?? 'hosting');

                if($sub_categories && count($sub_categories) > 0) {
                    // Her alt kategori için paket çek
                    $cats_with_products = [];
                    foreach($sub_categories as $cat) {
                        $list = $get_list($cat["id"], $cat["kind"] ?? 'hosting');
                        if($list && count($list) > 0) {
                            $cats_with_products[] = ['cat' => $cat, 'products' => $list];
                        }
                    }

                    if(count($cats_with_products) > 0) {
                        ?>
                        <?php if(count($cats_with_products) > 1): ?>
                        <div class="cdg-pricing-tabs" role="tablist" style="margin-top:48px;">
                            <?php foreach($cats_with_products as $i => $cwp): ?>
                            <button type="button" class="cdg-pricing-tab<?php echo $i === 0 ? ' active' : ''; ?>" data-tab="cat-<?php echo $cwp['cat']['id']; ?>" role="tab">
                                <i class="bi bi-collection" style="color:#1e40af;"></i>
                                <span><?php echo htmlspecialchars($cwp['cat']['title']); ?></span>
                                <small><?php echo count($cwp['products']); ?> paket</small>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php foreach($cats_with_products as $i => $cwp): ?>
                        <div class="cdg-pricing-pane<?php echo $i === 0 ? ' active' : ''; ?>" data-pane="cat-<?php echo $cwp['cat']['id']; ?>" role="tabpanel">
                            <?php if(count($cats_with_products) > 1): ?>
                            <div class="cdg-section-head" style="margin-top:32px;">
                                <h2><?php echo htmlspecialchars($cwp['cat']['title']); ?></h2>
                                <?php if(!empty($cwp['cat']['sub_title'])): ?>
                                <p><?php echo htmlspecialchars(strip_tags($cwp['cat']['sub_title'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php $render_category_section($cwp['cat'], $cwp['products']); ?>
                        </div>
                        <?php endforeach; ?>

                        <?php if(count($cats_with_products) > 1): ?>
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
                        <?php
                    }
                }
            }

        // 2. Hiçbir runtime callback yoksa empty state
        } else {
        ?>
            <div class="cdg-dash-empty" style="background:#fff;border:1px solid #e2e8f0;">
                <div class="cdg-dash-empty-icon"><i class="bi bi-box-seam"></i></div>
                <h2>Henüz <span class="cdg-text-gradient">aktif paket</span> bulunmuyor</h2>
                <p>Hosting paketleri yakında bu sayfada listelenecek. Bilgi için bize ulaşın.</p>
                <div class="cdg-dash-empty-actions">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary"><i class="bi bi-chat-dots-fill"></i> İletişim</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

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

<script>
function cdgUpdatePrice(select) {
    var card = select.closest('.cdg-price-card');
    if(!card) return;
    var num = card.querySelector('.cdg-price-num');
    var btn = card.querySelector('.cdg-product-buy-btn');
    if(num) num.textContent = select.value;
    if(btn && select.options[select.selectedIndex].dataset.url) {
        btn.href = select.options[select.selectedIndex].dataset.url;
    }
}
</script>
