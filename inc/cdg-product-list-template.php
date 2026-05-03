<?php
/**
 * CDG Product List Template — Generic
 *
 * Bu template tum urun tiplerini (hosting, server, sms, special) ayni mantikla
 * render eder. WiseCP'den gercek paketleri ceker, her birinin buy_link runtime
 * field'ini kullanarak sepete dogrudan eklenmesini saglar.
 *
 * KULLANIM:
 *   Cagiran sayfa $cdg_pt config'ini set edip include eder:
 *
 *   $cdg_pt = [
 *       'type'       => 'server',                    // hosting | server | sms | special | software
 *       'page_title' => 'Sunucu Paketleri',
 *       'page_icon'  => 'bi-hdd-rack-fill',
 *       'hero_title' => 'Profesyonel Sunucu Cozumleri',
 *       'hero_desc'  => 'VPS ve dedicated sunucu paketlerimiz',
 *       'color'      => '#7c3aed',
 *   ];
 *   include __DIR__.'/inc/cdg-product-list-template.php';
 *
 * RUNTIME (WiseCP'den gelir):
 *   $showCategory, $category, $category2, $get_list, $get_categories,
 *   $columns, $list (kategori altindaki paketler)
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'cdg-public-styles.php';

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

// Default config (caller'in atadiklarina ek)
$cdg_pt = is_array($cdg_pt ?? null) ? $cdg_pt : [];
$cdg_pt = array_merge([
    'type'       => 'hosting',
    'page_title' => 'Paketlerimiz',
    'page_icon'  => 'bi-grid-fill',
    'hero_title' => 'Paketlerimizi Inceleyin',
    'hero_desc'  => 'Ihtiyaciniza uygun paketi secin, anlik kuruluma gecin.',
    'color'      => '#10b981',
    'singular'   => 'Paket',  // Tek paket icin kelime (Hosting, Sunucu, SMS vs.)
], $cdg_pt);

$contact_url = cdg_link('contact');
$basket_url  = cdg_link('basket');

// === Currency sembolleri (price formatter icin) ===
$cdg_currency_symbols = [];
if(class_exists('Money') && method_exists('Money', 'getCurrencies')) {
    foreach(Money::getCurrencies() AS $_cur) {
        $_sym = trim(($_cur['prefix'] ?? '') ?: ($_cur['suffix'] ?? ''));
        if(!$_sym) $_sym = $_cur['code'] ?? '';
        if($_sym) $cdg_currency_symbols[] = $_sym;
    }
}

// === Paket formatter (hosting-products.php'deki ile uyumlu) ===
$cdg_format_pkg = function($product) use (&$columns, $cdg_currency_symbols) {
    $popular = !empty($product['options']['popular']);

    $amount_value = '';
    $amount_symbol = '';
    $period_text = '';
    $prices = $product['prices'] ?? [];
    if(isset($prices[0]) && is_array($prices[0])) {
        $amount = $prices[0]['amount'] ?? 0;
        $cid    = $prices[0]['cid'] ?? 0;
        $override = !empty($product['override_usrcurrency']);
        $price_text = '';
        if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $cid) {
            try { $price_text = Money::formatter_symbol($amount, $cid, !$override); }
            catch(\Throwable $e) { $price_text = number_format((float)$amount, 2, ',', '.'); }
        } else {
            $price_text = number_format((float)$amount, 2, ',', '.');
        }

        if($cdg_currency_symbols && $price_text) {
            $parts = explode(' ', $price_text);
            if(count($parts) >= 2) {
                if(in_array(reset($parts), $cdg_currency_symbols)) {
                    $amount_symbol = array_shift($parts);
                    $amount_value = implode(' ', $parts);
                } elseif(in_array(end($parts), $cdg_currency_symbols)) {
                    $amount_symbol = array_pop($parts);
                    $amount_value = implode(' ', $parts);
                } else { $amount_value = $price_text; }
            } else { $amount_value = $price_text; }
        } else { $amount_value = $price_text; }

        if(class_exists('View') && method_exists('View', 'period')) {
            try { $period_text = View::period($prices[0]['time'] ?? 1, $prices[0]['period'] ?? 'year'); }
            catch(\Throwable $e) { $period_text = ($prices[0]['time'] ?? 1) . ' ' . ($prices[0]['period'] ?? ''); }
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
            if($v !== null && $v !== '' && !is_array($v)) {
                $features[] = (is_string($k) && !is_numeric($k) ? $k . ': ' : '') . (string)$v;
            } elseif(is_array($v) && isset($v['name'])) {
                $val = $v['value'] ?? '';
                $features[] = $v['name'] . ($val ? ': ' . $val : '');
            }
        }
    } elseif(is_string($raw_features) && trim($raw_features)) {
        foreach(preg_split('/\r\n|\r|\n/', $raw_features) as $line) {
            $line = trim($line);
            if($line) $features[] = $line;
        }
    }

    return [
        'name'          => $product['title'] ?? ($product['name'] ?? 'Paket'),
        'subtitle'      => $product['sub_title'] ?? '',
        'amount_value'  => $amount_value ?: '-',
        'amount_symbol' => $amount_symbol ?: '',
        'period'        => $period_text ?: '',
        'features'      => array_slice($features, 0, 8),
        'highlight'     => $popular,
        'buy_link'      => $product['buy_link'] ?? '',
        'buy_label'     => $product['optionsl']['buy_button_name'] ?? 'Hemen Satın Al',
    ];
};

// === Kategori bazinda paketleri grupla ===
$cdg_categories = [];   // [cat_id => ['name' => ..., 'packages' => []]]
$cdg_used_dynamic = false;

// Yontem 1: WiseCP runtime ($get_list closure'u)
if(isset($get_list) && is_callable($get_list) && isset($showCategory)) {
    try {
        $list = call_user_func($get_list, $showCategory['id'] ?? 0, $showCategory['kind'] ?? $cdg_pt['type']);
        if(is_array($list) && !empty($list)) {
            $cat_name = $showCategory['title'] ?? $cdg_pt['page_title'];
            $cat_id   = $showCategory['id'] ?? 'cat_default';
            $cdg_categories[$cat_id] = [
                'id'       => 'cat_' . $cat_id,
                'name'     => $cat_name,
                'packages' => [],
            ];
            foreach($list as $p) {
                if(!is_array($p)) continue;
                $f = $cdg_format_pkg($p);
                if($f['buy_link']) $cdg_categories[$cat_id]['packages'][] = $f;
            }
            $cdg_used_dynamic = true;
        }
    } catch(\Throwable $e) {}
}

// Yontem 2: get_products_with_category + manuel prices + buy_link (Products::getList YOK)
if(!$cdg_used_dynamic && class_exists('Products') && method_exists('Products', 'get_products_with_category') && method_exists('Products', 'get_select_categories')) {
    try {
        $cats = @Products::get_select_categories($cdg_pt['type'], 0);
        if(is_array($cats)) {
            foreach($cats as $hc) {
                $cat_id = $hc['id'] ?? 0;
                if(!$cat_id) continue;
                $pks = @Products::get_products_with_category($cdg_pt['type'], $cat_id);
                if(!is_array($pks) || empty($pks)) continue;

                $cdg_categories[$cat_id] = [
                    'id'       => 'cat_' . $cat_id,
                    'name'     => $hc['title'] ?? ('Kategori #' . $cat_id),
                    'packages' => [],
                ];

                foreach($pks as $p) {
                    if(!is_array($p)) continue;
                    $p['category'] = $cat_id;

                    // 1) Prices ayri sorgu
                    $p['prices'] = [];
                    if(method_exists('Products', 'get_prices')) {
                        try {
                            $pr = @Products::get_prices('periodicals', 'products', $p['id']);
                            if(is_array($pr)) $p['prices'] = $pr;
                        } catch(\Throwable $e) {}
                    }

                    // 2) buy_link: Once CRLink dene, basarisizsa direkt URL insa et
                    // (WiseCP gercek URL formati /order-steps/{type}/{id})
                    $p['buy_link'] = '';
                    $buy_try = '';
                    if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
                        try {
                            $buy_try = Controllers::$init->CRLink('order-steps-p', [$cdg_pt['type'], (int)$p['id'], 1]);
                            if(!$buy_try || strpos($buy_try, 'order-steps-p') !== false) $buy_try = '';
                        } catch(\Throwable $e) { $buy_try = ''; }
                    }
                    if(!$buy_try) {
                        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
                        $buy_try = $base . '/order-steps/' . $cdg_pt['type'] . '/' . (int)$p['id'];
                    }
                    $p['buy_link'] = $buy_try;

                    // 3) JSON alanlari decode et
                    if(isset($p['options']) && is_string($p['options'])) {
                        $dec = @json_decode($p['options'], true);
                        if(class_exists('Utility') && method_exists('Utility', 'jdecode')) {
                            try { $dec = Utility::jdecode($p['options'], true); } catch(\Throwable $e) {}
                        }
                        $p['options'] = is_array($dec) ? $dec : [];
                    }
                    if(isset($p['options_lang']) && is_string($p['options_lang'])) {
                        $dec = @json_decode($p['options_lang'], true);
                        if(class_exists('Utility') && method_exists('Utility', 'jdecode')) {
                            try { $dec = Utility::jdecode($p['options_lang'], true); } catch(\Throwable $e) {}
                        }
                        $p['optionsl'] = is_array($dec) ? $dec : [];
                    }

                    $f = $cdg_format_pkg($p);
                    if($f['buy_link']) $cdg_categories[$cat_id]['packages'][] = $f;
                }

                if(empty($cdg_categories[$cat_id]['packages'])) {
                    unset($cdg_categories[$cat_id]);
                }
            }
            if(!empty($cdg_categories)) $cdg_used_dynamic = true;
        }
    } catch(\Throwable $e) {}
}

// Bos kategorileri sil
foreach($cdg_categories as $k => $c) {
    if(empty($c['packages'])) unset($cdg_categories[$k]);
}

// === RENDER ===
?>

<section class="cdg-section" style="padding:48px 0 32px;background:linear-gradient(135deg,#1A2332 0%,<?php echo htmlspecialchars($cdg_pt['color']); ?> 100%);color:#fff;">
    <div class="cdg-container" style="text-align:center;">
        <div style="display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:14px;background:rgba(255,255,255,0.12);font-size:28px;margin-bottom:14px;">
            <i class="<?php echo htmlspecialchars($cdg_pt['page_icon']); ?>"></i>
        </div>
        <h1 style="font-size:32px;font-weight:800;margin:0 0 10px;"><?php echo htmlspecialchars($cdg_pt['hero_title']); ?></h1>
        <p style="font-size:15px;color:rgba(255,255,255,0.8);max-width:680px;margin:0 auto;line-height:1.7;"><?php echo htmlspecialchars($cdg_pt['hero_desc']); ?></p>
    </div>
</section>

<section class="cdg-section" style="padding:48px 0 64px;background:#f8fafc;">
    <div class="cdg-container">

        <?php if(empty($cdg_categories)): ?>
        <!-- Hicbir paket yoksa -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:48px 32px;text-align:center;max-width:600px;margin:0 auto;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,<?php echo htmlspecialchars($cdg_pt['color']); ?>,<?php echo htmlspecialchars($cdg_pt['color']); ?>99);color:#fff;display:inline-grid;place-items:center;font-size:36px;margin-bottom:18px;">
                <i class="<?php echo htmlspecialchars($cdg_pt['page_icon']); ?>"></i>
            </div>
            <h2 style="font-size:22px;font-weight:800;margin:0 0 10px;color:#0f172a;"><?php echo htmlspecialchars($cdg_pt['singular']); ?> Hizmetlerimiz</h2>
            <p style="font-size:14px;color:#64748b;margin:0 0 22px;line-height:1.7;">
                <?php echo htmlspecialchars($cdg_pt['singular']); ?> hizmetlerimiz hakkında detaylı bilgi almak ve size uygun paketi belirlemek için bizimle iletişime geçin. Talebinize özel teklif hazırlayalım.
            </p>
            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-chat-dots"></i> İletişime Geç
                </a>
                <a href="<?php echo cdg_link(''); ?>" class="cdg-btn cdg-btn-outline">
                    <i class="bi bi-house"></i> Anasayfa
                </a>
            </div>
        </div>

        <?php else: ?>

        <?php foreach($cdg_categories as $cat): ?>
        <div style="margin-bottom:48px;">
            <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin:0 0 6px;text-align:center;"><?php echo htmlspecialchars($cat['name']); ?></h2>
            <p style="font-size:13.5px;color:#64748b;text-align:center;margin:0 0 28px;"><?php echo count($cat['packages']); ?> paket mevcut</p>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;">
                <?php foreach($cat['packages'] as $pkg): ?>
                <div style="background:#fff;border:<?php echo $pkg['highlight'] ? '2px solid ' . htmlspecialchars($cdg_pt['color']) : '1px solid #e2e8f0'; ?>;border-radius:16px;padding:24px 22px;position:relative;<?php echo $pkg['highlight'] ? 'box-shadow:0 12px 32px rgba(0,0,0,0.08);' : ''; ?>">
                    <?php if($pkg['highlight']): ?>
                    <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:<?php echo htmlspecialchars($cdg_pt['color']); ?>;color:#fff;padding:4px 14px;border-radius:99px;font-size:11px;font-weight:800;letter-spacing:0.5px;">EN POPÜLER</div>
                    <?php endif; ?>

                    <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;"><?php echo htmlspecialchars($pkg['name']); ?></h3>
                    <?php if($pkg['subtitle']): ?>
                    <p style="font-size:12.5px;color:#64748b;margin:0 0 18px;"><?php echo htmlspecialchars($pkg['subtitle']); ?></p>
                    <?php else: ?>
                    <div style="height:18px;"></div>
                    <?php endif; ?>

                    <div style="margin-bottom:18px;display:flex;align-items:flex-end;gap:6px;">
                        <span style="font-size:14px;color:#64748b;font-weight:600;line-height:1;"><?php echo htmlspecialchars($pkg['amount_symbol']); ?></span>
                        <span style="font-size:36px;font-weight:800;color:#0f172a;line-height:1;"><?php echo htmlspecialchars($pkg['amount_value']); ?></span>
                        <?php if($pkg['period']): ?>
                        <span style="font-size:12px;color:#94a3b8;font-weight:600;line-height:1;padding-bottom:4px;">/ <?php echo htmlspecialchars($pkg['period']); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if($pkg['features']): ?>
                    <ul style="list-style:none;padding:0;margin:0 0 22px;display:grid;gap:8px;">
                        <?php foreach($pkg['features'] as $feat): ?>
                        <li style="font-size:13px;color:#475569;display:flex;gap:8px;align-items:flex-start;line-height:1.6;">
                            <i class="bi bi-check-circle-fill" style="color:<?php echo htmlspecialchars($cdg_pt['color']); ?>;flex-shrink:0;font-size:14px;margin-top:2px;"></i>
                            <span><?php echo htmlspecialchars($feat); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <a href="<?php echo htmlspecialchars($pkg['buy_link']); ?>" class="cdg-btn <?php echo $pkg['highlight'] ? 'cdg-btn-primary' : 'cdg-btn-outline'; ?> cdg-btn-block" style="<?php echo $pkg['highlight'] ? 'background:'.htmlspecialchars($cdg_pt['color']).';border-color:'.htmlspecialchars($cdg_pt['color']).';' : ''; ?>">
                        <i class="bi bi-cart-plus"></i> <?php echo htmlspecialchars($pkg['buy_label']); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Iletisim CTA — paketlere ek olarak -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:32px;text-align:center;margin-top:32px;">
            <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">Aradığınız paketi bulamadınız mı?</h3>
            <p style="font-size:13.5px;color:#64748b;margin:0 0 18px;line-height:1.7;">İhtiyacınıza özel paket hazırlayabiliriz. Bizimle iletişime geçin, size uygun çözümü birlikte planlayalım.</p>
            <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-outline">
                <i class="bi bi-envelope"></i> Özel Teklif İste
            </a>
        </div>

        <?php endif; ?>

    </div>
</section>

<?php
/* DEFANSIVE FALLBACK */
if(empty($_cdg_in_master_content) && !headers_sent()) {
    if(file_exists(__DIR__ . "/main-footer.php")) {
        include __DIR__ . "/main-footer.php";
    }
    if(class_exists("View") && method_exists("View", "footer_codes")) View::footer_codes();
}
?>
