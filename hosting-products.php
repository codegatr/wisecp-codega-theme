<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';

// Diagnostic mode
if(isset($_GET['_diag']) && $_GET['_diag'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== CODEGA HOSTING-PRODUCTS DIAGNOSTIC ===\n\n";
    echo "showCategory: " . (isset($showCategory) ? 'YES' : 'NO') . "\n";
    if(isset($showCategory)) {
        echo "  id: " . ($showCategory['id'] ?? '?') . "\n";
        echo "  title: " . ($showCategory['title'] ?? '?') . "\n";
        echo "  kind: " . ($showCategory['kind'] ?? '?') . "\n";
    }
    echo "\ncategory: " . (isset($category) ? 'YES' : 'NO') . "\n";
    if(isset($category)) {
        echo "  id: " . ($category['id'] ?? '?') . "\n";
        echo "  title: " . ($category['title'] ?? '?') . "\n";
    }
    echo "\nget_list: " . (isset($get_list) && is_callable($get_list) ? 'YES' : 'NO') . "\n";
    echo "get_categories: " . (isset($get_categories) && is_callable($get_categories) ? 'YES' : 'NO') . "\n";

    if(isset($get_list) && is_callable($get_list) && isset($showCategory)) {
        $list = $get_list($showCategory['id'], $showCategory['kind'] ?? 'hosting');
        echo "\nget_list(" . $showCategory['id'] . "," . ($showCategory['kind'] ?? 'hosting') . ") returned:\n";
        echo "  count: " . (is_array($list) ? count($list) : 'NOT ARRAY') . "\n";
        if(is_array($list) && count($list) > 0) {
            echo "  First product:\n";
            $p = $list[0];
            echo "    title: " . ($p['title'] ?? '?') . "\n";
            echo "    buy_link: " . ($p['buy_link'] ?? '?') . "\n";
            echo "    prices count: " . (isset($p['prices']) ? count($p['prices']) : 0) . "\n";
        }
    }

    if(isset($get_categories) && is_callable($get_categories) && isset($category)) {
        $cats = $get_categories($category['id'], $category['kind'] ?? 'hosting');
        echo "\nget_categories(" . $category['id'] . "," . ($category['kind'] ?? 'hosting') . ") returned:\n";
        echo "  count: " . (is_array($cats) ? count($cats) : 'NOT ARRAY') . "\n";
        if(is_array($cats)) {
            foreach($cats as $c) {
                echo "  - id:" . ($c['id'] ?? '?') . " title:" . ($c['title'] ?? '?') . "\n";
            }
        }
    }
    exit;
}

$hoptions = [
    'page' => "hosting-products",
    'dataTables',
    'jquery-ui',
];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        // Bu yuzden once alias+CRLink, $links sadece bilinmeyen slug'lar icin son fallback
        global $links;

        // 2) Kısa-isim -> WiseCP gerçek route alias map
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
            'ac-ps-create-ticket-request' => 'ac-ps-create-ticket-request',
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

        // 3) CRLink dene (gerçek WiseCP routing)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                // Bozuk URL kontrolü (boş ID parametresi vb.)
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) { /* fallback'e düş */ }
        }

        // 4) Son çare: APP_URI base + slug
        // Son care: $links bakilirsa kullan (sadece bilinmeyen slug'lar icin)
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$contact_url = cdg_link('contact');
$domain_url  = cdg_link('domain');

$currency_symbols = [];
if(class_exists('Money') && method_exists('Money', 'getCurrencies')) {
    foreach(Money::getCurrencies() as $currency){
        $symbol = !empty($currency["prefix"]) ? trim($currency["prefix"]) : trim($currency["suffix"] ?? '');
        if(!$symbol) $symbol = $currency["code"] ?? '₺';
        $currency_symbols[] = $symbol;
    }
}
$GLOBALS["currency_symbols"] = $currency_symbols;

// === NETWISE'IN MANTIĞI BİRE BİR (test edilmiş, çalışan) ===
$render_products = function($cat = [], $list = []) use ($currency_symbols) {
    if(!$list || empty($list)) return;

    $product_count = count($list);
    $grid_class = 'cdg-pricing-grid-' . min($product_count, 4);
    ?>
    <div class="cdg-pricing-grid <?php echo $grid_class; ?>" style="margin-top:32px;">
        <?php foreach($list as $product):
            $prices = !isset($product["prices"]) ? [] : $product["prices"];
            $product_amount = '';
            $product_period = '';
            $popular = isset($product["options"]["popular"]) && $product["options"]["popular"];
            $amount_symbol = '';

            if(isset($prices[0]) && $prices[0] > 0) {
                $amount = $prices[0]["amount"];
                $cid = $prices[0]["cid"];
                $product_amount = Money::formatter_symbol($amount, $cid, !$product["override_usrcurrency"]);
                $split_amount = explode(" ", $product_amount);
                if(in_array(current($split_amount), $currency_symbols)) {
                    $amount_symbol = current($split_amount);
                    array_shift($split_amount);
                } elseif(in_array(end($split_amount), $currency_symbols)) {
                    $amount_symbol = end($split_amount);
                    array_pop($split_amount);
                }
                $product_amount = implode(" ", $split_amount);
            }
            if(isset($prices[0])) {
                $product_period = View::period($prices[0]["time"], $prices[0]["period"]);
            }
        ?>
        <div class="cdg-price-card<?php echo $popular ? ' cdg-price-card-highlight' : ''; ?>">
            <?php if($popular): ?><div class="cdg-price-ribbon">EN POPÜLER</div><?php endif; ?>

            <h3 class="cdg-price-name"><?php echo htmlspecialchars($product["title"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
            <p class="cdg-price-subtitle">Başlangıç fiyatı</p>

            <div class="cdg-price-amount">
                <div class="cdg-price-current">
                    <?php if($amount_symbol): ?><span class="cdg-price-curr"><?php echo $amount_symbol; ?></span><?php endif; ?>
                    <span class="cdg-price-num"><?php echo $product_amount; ?></span>
                </div>
                <span class="cdg-price-period">/<?php echo htmlspecialchars($product_period, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </div>

            <?php if(count($prices) > 1): ?>
            <select class="cdg-price-period-select" data-buy-link="<?php echo htmlspecialchars($product["buy_link"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" onchange="cdgUpdatePeriod(this)">
                <?php foreach($prices as $price_option):
                    $formatted_price = Money::formatter_symbol($price_option["amount"], $price_option["cid"], !$product["override_usrcurrency"]);
                    $formatted_period = View::period($price_option["time"], $price_option["period"]);
                ?>
                <option value="<?php echo htmlspecialchars($price_option["id"] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" data-price="<?php echo htmlspecialchars($formatted_price, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                    <?php echo $formatted_period . " — " . $formatted_price; ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>

            <?php
                $features = Utility::jdecode($product["features"], true);
                if($features && is_array($features)) {
                ?>
                <ul class="cdg-price-features">
                    <?php foreach($features as $feature => $value): ?>
                    <li><i class="bi bi-check-circle-fill"></i>
                        <?php
                        if(is_numeric($feature)) echo htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        else echo '<strong>' . htmlspecialchars($feature, ENT_QUOTES | ENT_HTML5, 'UTF-8') . ':</strong> ' . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php
                } elseif(!empty($product["features"])) {
                ?>
                <ul class="cdg-price-features">
                    <?php
                    $lines = preg_split('/\r\n|\r|\n/', strip_tags($product["features"]));
                    foreach($lines as $line) {
                        $line = trim($line);
                        if($line) echo '<li><i class="bi bi-check-circle-fill"></i> ' . htmlspecialchars($line, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</li>';
                    }
                    ?>
                </ul>
                <?php
                }
            ?>

            <a href="<?php echo htmlspecialchars($product["buy_link"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn <?php echo $popular ? 'cdg-btn-primary cdg-btn-glow' : 'cdg-btn-outline'; ?> cdg-btn-block cdg-product-buy-btn">
                <i class="bi bi-cart-plus"></i> Hemen Sipariş Et
            </a>
        </div>
        <?php endforeach; ?>
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
            <div class="cdg-eyebrow cdg-eyebrow-glow"><i class="bi bi-hdd-network-fill"></i> Hosting Paketleri</div>
            <h1>
                <?php if(isset($showCategory['title']) && $showCategory['title']): ?>
                    <?php echo htmlspecialchars($showCategory['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                <?php else: ?>
                    NVMe SSD <span class="cdg-text-gradient-light">hosting paketleri</span>
                <?php endif; ?>
            </h1>
            <p>
                <?php if(isset($showCategory['sub_title']) && $showCategory['sub_title']): ?>
                    <?php echo htmlspecialchars(strip_tags($showCategory['sub_title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
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

<!-- PAKETLER (Netwise mantığı bire bir) -->
<section class="cdg-section" id="packages">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <h2>
                <?php if(isset($showCategory['title']) && $showCategory['title']): ?>
                    <?php echo htmlspecialchars($showCategory['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                <?php else: ?>
                    Hosting Paketleri
                <?php endif; ?>
            </h2>
            <?php if(isset($showCategory['sub_title']) && $showCategory['sub_title']): ?>
            <p><?php echo htmlspecialchars(strip_tags($showCategory['sub_title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <?php
        // === Netwise mantığı bire bir ===

        // 1. Mevcut kategorinin direkt paketlerini göster
        if(!isset($category2) || !$category2) {
            if(isset($showCategory) && isset($get_list) && is_callable($get_list)) {
                $list = $get_list($showCategory["id"], $showCategory["kind"]);
                $render_products($showCategory, $list);
            }
        }

        // 2. Alt kategorileri çek
        if(isset($category) && isset($get_categories) && is_callable($get_categories)) {
            $categories = $get_categories($category["id"], $category["kind"]);

            if($categories && count($categories) > 0) {
                $catSize = sizeof($categories);
                $category_products = [];
                $categories_list = [];
                $selection = isset($category2) && $category2 ? $showCategory : false;

                // Her alt kategori için ürün ve alt kategori çek
                foreach($categories as $k => $cat) {
                    $list = $get_list($cat["id"], $cat["kind"]);
                    $subs = $get_categories($cat["id"], $cat["kind"]);
                    $category_products[$cat["id"]] = $list;
                    $categories_list[$cat["id"]] = $subs;

                    if(!$list && !$subs) continue;

                    if($category["id"]) {
                        if(!$selection && $k == 0) $selection = $cat;
                    } else {
                        if(isset($category2) && $category2 && $category2["id"] == $cat["id"]){
                            $selection = $cat;
                        } elseif(!$selection && $category_products[$cat["id"]] && $k == 0) {
                            $selection = $cat;
                        } elseif(!$selection && $k == 0) {
                            $category = $cat;
                            $selection = $cat;
                        }
                    }
                }

                // Tab navigation (alt kategoriler birden fazlaysa)
                if($catSize > 1) {
                    ?>
                    <div class="cdg-pricing-tabs" role="tablist" style="margin-top:32px;">
                        <?php foreach($categories as $k => $cat):
                            if(!$category_products[$cat["id"]] && !$categories_list[$cat["id"]]) continue;
                            $is_selected = $selection && $cat["id"] == $selection["id"];
                            $tab_route = '';
                            if(isset($category_tab_route) && is_callable($category_tab_route) && isset($cat["route"])) {
                                $tab_route = $category_products[$cat["id"]] ? $category_tab_route($cat["route"]) : $category_route($cat["route"]);
                            }
                        ?>
                        <a href="<?php echo htmlspecialchars($tab_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pricing-tab<?php echo $is_selected ? ' active' : ''; ?>">
                            <?php if(!empty($cat["options"]["icon"])): ?>
                                <i class="<?php echo htmlspecialchars($cat["options"]["icon"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                            <?php elseif(!empty($cat["icon"])): ?>
                                <i class="bi bi-collection" style="color:#1e40af;"></i>
                            <?php else: ?>
                                <i class="bi bi-folder" style="color:#1e40af;"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($cat["title"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <small><?php echo count($category_products[$cat["id"]] ?? []); ?> paket</small>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php
                }

                // Seçili kategorinin paketlerini ve alt kategorilerini göster
                if(isset($selection) && $selection) {
                    if(isset($category_products[$selection["id"]]) && $category_products[$selection["id"]]) {
                        $list = $category_products[$selection["id"]];
                        $render_products($selection, $list);

                        // Alt-alt kategoriler
                        if(isset($category["id"]) && $category["id"]) {
                            $sub_categories = isset($categories_list[$selection["id"]]) ? $categories_list[$selection["id"]] : false;
                            if($sub_categories) {
                                foreach($sub_categories as $sub_cat) {
                                    $sub_list = $get_list($sub_cat["id"], $sub_cat["kind"]);
                                    if($sub_list) {
                                        ?>
                                        <div class="cdg-section-head" style="margin-top:48px;">
                                            <h3 style="font-size:22px;font-weight:800;color:#0f172a;"><?php echo htmlspecialchars($sub_cat["title"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                                        </div>
                                        <?php
                                        $render_products($sub_cat, $sub_list);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Hiçbir global yoksa kullanıcıyı bilgilendir
        if(!isset($get_list) || !is_callable($get_list)):
        ?>
            <div class="cdg-dash-empty" style="background:#fff;border:1px solid #e2e8f0;margin-top:32px;">
                <div class="cdg-dash-empty-icon"><i class="bi bi-box-seam"></i></div>
                <h2>Paketler <span class="cdg-text-gradient">yükleniyor</span></h2>
                <p>Hosting paketleri admin panelinden yapılandırılmaktadır. Bilgi için bizimle iletişime geçin.</p>
                <div class="cdg-dash-empty-actions">
                    <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary"><i class="bi bi-chat-dots-fill"></i> İletişim</a>
                </div>
            </div>
        <?php endif; ?>
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
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-lightning-charge-fill"></i></div><h3>NVMe SSD</h3><p>10 kat daha hızlı I/O performansı.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-speedometer2"></i></div><h3>LiteSpeed</h3><p>Apache'den 9 kat hızlı.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-shield-fill-check"></i></div><h3>Ücretsiz SSL</h3><p>Let's Encrypt otomatik kurulum.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-arrow-clockwise"></i></div><h3>Yedekleme</h3><p>Saatlik veya günlük yedek.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div><h3>Ücretsiz Taşıma</h3><p>5 siteye kadar kesintisiz.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-fingerprint"></i></div><h3>Imunify360</h3><p>Malware ve WAF koruması.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-graph-up-arrow"></i></div><h3>%99.99 Uptime</h3><p>SLA garantili hizmet.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-headset"></i></div><h3>7/24 Destek</h3><p>WhatsApp, telefon, panel.</p></div>
        </div>
    </div>
</section>

<!-- DESTEK CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Yardıma mı ihtiyaçınız var?</div>
            <h2>Sizin için <span class="cdg-text-gradient">en uygun paketi</span> birlikte seçelim</h2>
            <p>Uzman ekibimiz size en uygun paketi önersin. Hemen WhatsApp veya telefonla ulaşın.</p>
            <div class="cdg-final-cta-actions">
                <a href="https://wa.me/905102204206" target="_blank" rel="noopener" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                <a href="tel:+905102204206" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-telephone-fill"></i> 0 510 220 42 06</a>
            </div>
        </div>
    </div>
</section>

<script>
function cdgUpdatePeriod(select) {
    var card = select.closest('.cdg-price-card');
    if(!card) return;
    var opt = select.options[select.selectedIndex];
    var price = opt.dataset.price || '';
    var num = card.querySelector('.cdg-price-num');
    var curr = card.querySelector('.cdg-price-curr');
    if(num && price) {
        // Para birimi sembolünü ayır
        var parts = price.split(' ');
        var hasSymbol = parts.length > 1;
        if(hasSymbol) {
            // İlk veya son parça sembol olabilir
            var first = parts[0];
            var last = parts[parts.length - 1];
            // Basit kontrol: rakam içermiyor → sembol
            if(!/\d/.test(first)) {
                if(curr) curr.textContent = first;
                num.textContent = parts.slice(1).join(' ');
            } else if(!/\d/.test(last)) {
                if(curr) curr.textContent = last;
                num.textContent = parts.slice(0, -1).join(' ');
            } else {
                num.textContent = price;
            }
        } else {
            num.textContent = price;
        }
    }
    // buy_link güncelle
    var btn = card.querySelector('.cdg-product-buy-btn');
    if(btn && select.dataset.buyLink) {
        var url = select.dataset.buyLink;
        var sep = url.indexOf('?') === -1 ? '?' : '&';
        btn.href = url + sep + 'period=' + encodeURIComponent(select.value);
    }
}
</script>
