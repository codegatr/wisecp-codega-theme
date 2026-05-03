<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domainlerim Sayfası
 * WiseCP runtime: $products, $filter_counts, $situations, $links, $default_nameserver
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$hoptions = ["datatables"];

// === Yardımcı: link ===
if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        global $links;

        // CDG_LINK_HARDCODED - Yunus'un sitesinde KESIN dogru URL'ler (CRLink bypass)
        static $hardcoded = [
            'ac-ps-create-ticket-request' => '/hesabim/destek-talebi-olustur',
            'create-ticket-request'       => '/hesabim/destek-talebi-olustur',
            'create-ticket'               => '/hesabim/destek-talebi-olustur',
        ];
        if(isset($hardcoded[$slug])) {
            $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
            return $base . $hardcoded[$slug];
        }

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

// === Defansif defaults ===
// WiseCP runtime: $list, $orders, $products, $domain_orders gibi farkli isimler kullanabilir
$products = [];
$cdg_debug_source = 'NONE';

// Once dogrudan listeleri dene
if(isset($list) && is_array($list) && !empty($list)) {
    $products = $list;
    $cdg_debug_source = '$list';
} elseif(isset($orders) && is_array($orders) && !empty($orders)) {
    $products = $orders;
    $cdg_debug_source = '$orders';
} elseif(isset($GLOBALS['products']) && is_array($GLOBALS['products']) && !empty($GLOBALS['products'])) {
    $products = $GLOBALS['products'];
    $cdg_debug_source = '$GLOBALS[products]';
}

// Domain kategorize edilmis array yapisi: $domain_orders["all"], ["active"] vs.
if(empty($products) && isset($domain_orders) && is_array($domain_orders)) {
    if(isset($domain_orders['all']) && is_array($domain_orders['all'])) {
        $products = $domain_orders['all'];
        $cdg_debug_source = '$domain_orders[all]';
    } else {
        // $domain_orders zaten flat array olabilir
        $products = $domain_orders;
        $cdg_debug_source = '$domain_orders';
    }
}

// Diagnostic mode: ?_codega_diag=1 ile sayfanin uzerinde teknik bilgi goster
$cdg_show_diag = isset($_GET['_codega_diag']) && $_GET['_codega_diag'] === '1';
if($cdg_show_diag) {
    $cdg_diag_info = [
        '$list_isset'         => isset($list) ? 'YES (' . (is_array($list) ? count($list) . ' items' : gettype($list)) . ')' : 'NO',
        '$orders_isset'       => isset($orders) ? 'YES (' . (is_array($orders) ? count($orders) . ' items' : gettype($orders)) . ')' : 'NO',
        '$products_isset'     => isset($GLOBALS['products']) ? 'YES (' . (is_array($GLOBALS['products']) ? count($GLOBALS['products']) . ' items' : gettype($GLOBALS['products'])) . ')' : 'NO',
        '$domain_orders'      => isset($domain_orders) ? 'YES (' . (is_array($domain_orders) ? count($domain_orders) . ' keys: ' . implode(',', array_keys($domain_orders)) : gettype($domain_orders)) . ')' : 'NO',
        '$filter_counts'      => isset($filter_counts) ? json_encode($filter_counts) : 'NO',
        '$filter_status'      => isset($filter_status) ? $filter_status : 'NO',
        'final_$products'     => count($products) . ' items',
        'data_source'         => $cdg_debug_source,
    ];
    if(!empty($products)) {
        $cdg_diag_info['first_item_keys'] = implode(',', array_keys((array)$products[array_key_first($products)] ?? []));
    }
}

$filter_counts  = isset($filter_counts) && is_array($filter_counts) ? $filter_counts : [];
$situations     = isset($situations) && is_array($situations) ? $situations : [];
$links          = isset($links) && is_array($links) ? $links : [];
$default_nameserver = isset($default_nameserver) ? $default_nameserver : [];

// Filter sayaçları
$cnt_all        = (int)($filter_counts['all'] ?? 0);
$cnt_active     = (int)($filter_counts['active'] ?? 0);
$cnt_inprocess  = (int)($filter_counts['inprocess'] ?? 0);
$cnt_waiting    = (int)($filter_counts['waiting'] ?? 0);
$cnt_suspended  = (int)($filter_counts['suspended'] ?? 0);
$cnt_cancelled  = (int)($filter_counts['cancelled'] ?? 0);

$whois_url = $links['whois-profiles'] ?? cdg_link('ac-products-domain-whois-profiles');
$controller_url = $links['controller'] ?? '';
$shop_url = cdg_link('domain');

// Status -> rozet rengi
function cdg_domain_status_class($status) {
    $map = [
        'active'    => 'cdg-pd-badge-success',
        'inprocess' => 'cdg-pd-badge-warning',
        'waiting'   => 'cdg-pd-badge-info',
        'suspended' => 'cdg-pd-badge-warning',
        'cancelled' => 'cdg-pd-badge-danger',
        'expired'   => 'cdg-pd-badge-danger',
    ];
    return $map[$status] ?? 'cdg-pd-badge-info';
}
function cdg_domain_status_label($status) {
    $map = [
        'active'    => 'Aktif',
        'inprocess' => 'İşlemde',
        'waiting'   => 'Onay Bekliyor',
        'suspended' => 'Askıda',
        'cancelled' => 'İptal',
        'expired'   => 'Süresi Doldu',
    ];
    return $map[$status] ?? ucfirst($status);
}
?>

<style>
.cdg-pd {
    --pd-primary: #2E3B4E;
    --pd-success: #10b981;
    --pd-warning: #f59e0b;
    --pd-danger: #ef4444;
    --pd-info: #00D3E5;
    --pd-bg: #f8fafc;
    --pd-card: #fff;
    --pd-text: #0f172a;
    --pd-muted: #64748b;
    --pd-border: #e2e8f0;
    --pd-radius: 14px;
    --pd-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --pd-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--pd-text);
    background: var(--pd-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-pd *, .cdg-pd *::before, .cdg-pd *::after { box-sizing: border-box; }
.cdg-pd a { text-decoration: none; color: inherit; }

.cdg-pd-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

/* HERO */
.cdg-pd-hero {
    background: linear-gradient(135deg, #2E3B4E 0%, #00D3E5 50%, #00D3E5 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(46,59,78,0.20);
}
.cdg-pd-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(252,211,77,0.20), transparent 70%);
    pointer-events: none;
}
.cdg-pd-hero-row {
    display: flex; align-items: center; gap: 20px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-pd-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
}
.cdg-pd-hero-text { flex: 1; min-width: 240px; }
.cdg-pd-hero-text h1 {
    font-size: 26px; font-weight: 800; margin: 0 0 4px;
    letter-spacing: -0.4px;
}
.cdg-pd-hero-text p {
    font-size: 14px; opacity: 0.85; margin: 0;
}
.cdg-pd-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.cdg-pd-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
    font-family: inherit;
}
.cdg-pd-btn-gold {
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1A2332;
    box-shadow: 0 6px 18px rgba(252,211,77,0.30);
}
.cdg-pd-btn-gold:hover { transform: translateY(-1px); color: #1A2332; }
.cdg-pd-btn-ghost {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.30);
    backdrop-filter: blur(10px);
}
.cdg-pd-btn-ghost:hover { background: rgba(255,255,255,0.25); color: #fff; }
.cdg-pd-btn-primary {
    background: var(--pd-primary);
    color: #fff;
}
.cdg-pd-btn-primary:hover { background: #1A2332; color: #fff; }
.cdg-pd-btn-outline {
    background: #fff;
    color: var(--pd-text);
    border: 1px solid var(--pd-border);
}
.cdg-pd-btn-outline:hover { border-color: var(--pd-primary); color: var(--pd-primary); }
.cdg-pd-btn-sm { padding: 7px 14px; font-size: 12px; }

/* TAB FİLTRELER */
.cdg-pd-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
    flex-wrap: wrap;
    background: #fff;
    border-radius: var(--pd-radius);
    padding: 8px;
    box-shadow: var(--pd-shadow);
    border: 1px solid var(--pd-border);
}
.cdg-pd-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--pd-muted);
    cursor: pointer;
    transition: all 0.18s;
    background: transparent;
    border: 0;
    font-family: inherit;
}
.cdg-pd-tab:hover { background: var(--pd-bg); color: var(--pd-text); }
.cdg-pd-tab.active {
    background: var(--pd-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(46,59,78,0.22);
}
.cdg-pd-tab .count {
    background: rgba(0,0,0,0.10);
    padding: 2px 8px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
}
.cdg-pd-tab.active .count { background: rgba(255,255,255,0.22); }

/* NS KARTI */
.cdg-pd-ns-card {
    background: linear-gradient(135deg, #eff6ff, #fff);
    border: 1px solid #A5F3FC;
    border-radius: var(--pd-radius);
    padding: 18px 22px;
    margin-bottom: 18px;
}
.cdg-pd-ns-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 14px;
}
.cdg-pd-ns-head h3 {
    font-size: 14px; font-weight: 800;
    margin: 0; color: #1A2332;
    display: flex; align-items: center; gap: 8px;
}
.cdg-pd-ns-head h3 i { color: var(--pd-primary); }
.cdg-pd-ns-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 10px;
    margin-bottom: 12px;
}
.cdg-pd-ns-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--pd-border);
    border-radius: 8px;
    font-size: 13px;
    font-family: 'Courier New', monospace;
    color: var(--pd-text);
    background: #fff;
    outline: none;
    transition: all 0.2s;
}
.cdg-pd-ns-input:focus { border-color: var(--pd-primary); box-shadow: 0 0 0 3px rgba(46,59,78,0.10); }
.cdg-pd-ns-info {
    font-size: 12px;
    color: var(--pd-muted);
    line-height: 1.6;
    background: #f8fafc;
    padding: 10px 12px;
    border-radius: 8px;
    margin-top: 10px;
}
.cdg-pd-ns-info i { color: var(--pd-primary); }

/* DOMAIN CARD GRID */
.cdg-pd-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
.cdg-pd-card {
    background: var(--pd-card);
    border: 1px solid var(--pd-border);
    border-radius: var(--pd-radius);
    padding: 18px 22px;
    box-shadow: var(--pd-shadow);
    transition: all 0.18s;
    display: grid;
    grid-template-columns: auto 1fr auto auto;
    gap: 18px;
    align-items: center;
}
.cdg-pd-card:hover { box-shadow: var(--pd-shadow-lg); transform: translateY(-1px); }
.cdg-pd-card-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
.cdg-pd-card-body { min-width: 0; }
.cdg-pd-card-name {
    font-size: 16px; font-weight: 800;
    color: var(--pd-text);
    margin-bottom: 4px;
    word-break: break-all;
}
.cdg-pd-card-meta {
    font-size: 12px;
    color: var(--pd-muted);
    display: flex; gap: 14px; flex-wrap: wrap;
}
.cdg-pd-card-meta span {
    display: inline-flex; align-items: center; gap: 5px;
}
.cdg-pd-card-meta i { font-size: 13px; color: #94a3b8; }

/* BADGE */
.cdg-pd-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}
.cdg-pd-badge-success { background: #d1fae5; color: #065f46; }
.cdg-pd-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-pd-badge-danger  { background: #fee2e2; color: #991b1b; }
.cdg-pd-badge-info    { background: #CFFAFE; color: #2E3B4E; }

/* EMPTY */
.cdg-pd-empty {
    text-align: center;
    padding: 60px 20px;
    background: var(--pd-card);
    border: 2px dashed var(--pd-border);
    border-radius: var(--pd-radius);
}
.cdg-pd-empty-icon {
    font-size: 56px;
    color: #cbd5e1;
    margin-bottom: 12px;
}
.cdg-pd-empty h3 {
    font-size: 18px; font-weight: 800;
    color: var(--pd-text);
    margin: 0 0 6px;
}
.cdg-pd-empty p {
    font-size: 14px;
    color: var(--pd-muted);
    margin: 0 0 18px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .cdg-pd-card {
        grid-template-columns: auto 1fr;
        gap: 14px;
    }
    .cdg-pd-card-status,
    .cdg-pd-card-action {
        grid-column: 1 / -1;
    }
    .cdg-pd-card-action { display: flex; gap: 8px; }
    .cdg-pd-hero { padding: 22px 20px; }
    .cdg-pd-hero-text h1 { font-size: 22px; }
}
</style>

<div class="cdg-pd">
<div class="cdg-pd-wrap">

    <!-- HERO -->
    <section class="cdg-pd-hero">
        <div class="cdg-pd-hero-row">
            <div class="cdg-pd-hero-icon"><i class="bi bi-globe2"></i></div>
            <div class="cdg-pd-hero-text">
                <h1>Domainlerim</h1>
                <p>Kayıtlı tüm alan adlarınızı yönetin, NS kayıtlarını güncelleyin, transfer ve yenileme işlemleri yapın.</p>
            </div>
            <div class="cdg-pd-hero-actions">
                <a href="<?php echo htmlspecialchars($shop_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd-btn cdg-pd-btn-gold">
                    <i class="bi bi-plus-circle"></i> Yeni Domain Al
                </a>
                <a href="<?php echo htmlspecialchars($whois_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd-btn cdg-pd-btn-ghost">
                    <i class="bi bi-person-vcard"></i> Whois Profilleri
                </a>
            </div>
        </div>
    </section>

    <!-- VARSAYILAN NS KARTI -->
    <?php if(!empty($default_nameserver) && is_array($default_nameserver) && $controller_url): ?>
    <div class="cdg-pd-ns-card" id="default-nameserver">
        <div class="cdg-pd-ns-head">
            <h3><i class="bi bi-hdd-network-fill"></i> Varsayılan İsim Sunucuları (NS Kayıtları)</h3>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" id="ns-form" onsubmit="return false;">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('modify-default-nameserver'); ?>
            <input type="hidden" name="operation" value="modify_default_nameserver">

            <div class="cdg-pd-ns-grid">
                <?php for($i = 1; $i <= 4; $i++):
                    $ns_key = 'ns' . $i;
                    $ns_val = isset($default_nameserver[$ns_key]) ? $default_nameserver[$ns_key] : (isset($default_nameserver[$i-1]) ? $default_nameserver[$i-1] : '');
                ?>
                <input type="text"
                       name="values[]"
                       value="<?php echo htmlspecialchars($ns_val, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                       placeholder="ns<?php echo $i; ?>.codega.com.tr"
                       class="cdg-pd-ns-input"
                       autocomplete="off">
                <?php endfor; ?>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <div class="cdg-pd-ns-info">
                    <i class="bi bi-info-circle"></i>
                    Varsayılan NS kayıtlarını burada belirleyebilirsiniz. Yeni alacağınız domainlerde otomatik olarak bu sunucular tanımlanır.
                </div>
                <button type="submit" id="ModifyDefaultNameserver_submit" class="cdg-pd-btn cdg-pd-btn-primary">
                    <i class="bi bi-save2"></i> Güncelle
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- TAB FİLTRELER -->
    <div class="cdg-pd-tabs">
        <button class="cdg-pd-tab active" data-filter="all">
            <i class="bi bi-grid-3x3-gap"></i> Tümü <span class="count"><?php echo $cnt_all; ?></span>
        </button>
        <?php if($cnt_active > 0): ?>
        <button class="cdg-pd-tab" data-filter="active">
            <i class="bi bi-check-circle"></i> Aktif <span class="count"><?php echo $cnt_active; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_inprocess > 0): ?>
        <button class="cdg-pd-tab" data-filter="inprocess">
            <i class="bi bi-clock-history"></i> İşlemde <span class="count"><?php echo $cnt_inprocess; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_waiting > 0): ?>
        <button class="cdg-pd-tab" data-filter="waiting">
            <i class="bi bi-hourglass-split"></i> Onay Bekliyor <span class="count"><?php echo $cnt_waiting; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_suspended > 0): ?>
        <button class="cdg-pd-tab" data-filter="suspended">
            <i class="bi bi-pause-circle"></i> Askıda <span class="count"><?php echo $cnt_suspended; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_cancelled > 0): ?>
        <button class="cdg-pd-tab" data-filter="cancelled">
            <i class="bi bi-x-circle"></i> İptal <span class="count"><?php echo $cnt_cancelled; ?></span>
        </button>
        <?php endif; ?>
    </div>

    <!-- DOMAIN LİSTESİ -->

    <?php if($cdg_show_diag): ?>
    <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:10px;padding:16px;margin:16px 0;font-family:monospace;font-size:13px;">
        <h4 style="margin:0 0 10px;color:#92400e;">🔍 Codega Tani (Diagnostic) - Domain Liste</h4>
        <p style="margin:0 0 10px;color:#78350f;font-size:12px;">Bu bilgileri Yunus'a iletiniz, sorunu cozmemize yardimci olur. URL'den ?_codega_diag=1 kaldirarak normal goruntuye donebilirsiniz.</p>
        <table style="width:100%;border-collapse:collapse;">
            <?php foreach($cdg_diag_info as $k => $v): ?>
            <tr style="border-bottom:1px solid #fbbf24;">
                <td style="padding:6px;font-weight:700;color:#78350f;width:200px;"><?php echo htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                <td style="padding:6px;color:#451a03;word-break:break-all;"><?php echo htmlspecialchars(is_array($v) ? json_encode($v) : (string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <?php if(empty($products)): ?>
    <div class="cdg-pd-empty">
        <div class="cdg-pd-empty-icon"><i class="bi bi-globe"></i></div>
        <h3>Henüz domaininiz yok</h3>
        <p>Hayalinizdeki alan adını kaydederek dijital varlığınızı oluşturun.</p>
        <a href="<?php echo htmlspecialchars($shop_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd-btn cdg-pd-btn-gold">
            <i class="bi bi-plus-circle"></i> İlk Domaininizi Alın
        </a>
    </div>
    <?php else: ?>
    <div class="cdg-pd-grid" id="cdg-domain-list">
        <?php foreach($products as $row):
            $row_id     = $row['id'] ?? 0;
            $row_name   = $row['name'] ?? 'Domain';
            $row_status = $row['status'] ?? 'unknown';
            $row_due    = $row['duedate'] ?? '';
            $row_period = $row['period'] ?? '';
            $row_ptime  = $row['period_time'] ?? '';
            $row_amt    = $row['amount'] ?? 0;
            $row_cid    = $row['amount_cid'] ?? 0;
            $row_link   = $row['detail_link'] ?? '#';

            // Fiyat formatla
            $price_str = '';
            if($row_amt && $row_cid && class_exists('Money') && method_exists('Money','formatter_symbol')) {
                $price_str = Money::formatter_symbol($row_amt, $row_cid);
            }

            // Periyod metni
            $period_text = '';
            if($row_period && $row_ptime) {
                $period_text = $row_period . ' ' . $row_ptime;
            }
        ?>
        <div class="cdg-pd-card" data-status="<?php echo htmlspecialchars(strtolower($row_status), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <div class="cdg-pd-card-icon">
                <i class="bi bi-globe2"></i>
            </div>
            <div class="cdg-pd-card-body">
                <div class="cdg-pd-card-name"><?php echo htmlspecialchars($row_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <div class="cdg-pd-card-meta">
                    <?php if($row_due): ?>
                    <span><i class="bi bi-calendar-check"></i> Bitiş: <?php echo htmlspecialchars($row_due, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($period_text): ?>
                    <span><i class="bi bi-clock"></i> <?php echo htmlspecialchars($period_text, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($price_str): ?>
                    <span><i class="bi bi-tag"></i> <?php echo htmlspecialchars($price_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cdg-pd-card-status">
                <span class="cdg-pd-badge <?php echo cdg_domain_status_class($row_status); ?>">
                    <?php echo htmlspecialchars(cdg_domain_status_label($row_status), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </span>
            </div>
            <div class="cdg-pd-card-action">
                <a href="<?php echo htmlspecialchars($row_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd-btn cdg-pd-btn-outline cdg-pd-btn-sm">
                    <i class="bi bi-gear"></i> Yönet
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
(function(){
    // Tab filtre - case-insensitive ve display:grid restore
    document.querySelectorAll('.cdg-pd-tab').forEach(function(tab){
        tab.addEventListener('click', function(e){
            e.preventDefault();
            var filter = (this.getAttribute('data-filter') || '').toLowerCase();
            document.querySelectorAll('.cdg-pd-tab').forEach(function(t){ t.classList.remove('active'); });
            this.classList.add('active');

            var visible = 0;
            document.querySelectorAll('.cdg-pd-card').forEach(function(card){
                var s = (card.getAttribute('data-status') || '').toLowerCase();
                if(filter === 'all' || s === filter) {
                    card.style.display = 'grid';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Boş sonuç mesajı
            var grid = document.getElementById('cdg-domain-list');
            var emptyMsg = document.getElementById('cdg-no-result');
            if(visible === 0 && grid) {
                if(!emptyMsg) {
                    emptyMsg = document.createElement('div');
                    emptyMsg.id = 'cdg-no-result';
                    emptyMsg.style.cssText = 'text-align:center;padding:32px;color:#64748b;font-size:14px;background:#fff;border:2px dashed #e2e8f0;border-radius:14px;margin-top:12px;';
                    emptyMsg.innerHTML = '<i class="bi bi-funnel" style="font-size:32px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>Bu filtreye uygun domain bulunamadi.';
                    grid.parentNode.insertBefore(emptyMsg, grid.nextSibling);
                }
                emptyMsg.style.display = '';
            } else if(emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        });
    });

    // NS form AJAX submit
    var nsBtn = document.getElementById('ModifyDefaultNameserver_submit');
    if(nsBtn) {
        nsBtn.addEventListener('click', function(e){
            e.preventDefault();
            if(typeof MioAjaxElement === 'function') {
                MioAjaxElement(window.jQuery ? jQuery(this) : this, {
                    waiting_text: 'Kaydediliyor...',
                    result: 'ModifyDefaultNameserver_handler'
                });
            } else {
                // Fallback - normal form submit
                document.getElementById('ns-form').submit();
            }
        });
    }
})();

function ModifyDefaultNameserver_handler(result) {
    if(typeof getJson === 'function' && result) {
        var solve = getJson(result);
        if(solve !== false) {
            if(solve.status === 'error' && typeof alert_error === 'function') {
                alert_error(solve.message, {timer: 4000});
            } else if(solve.status === 'successful' && typeof alert_success === 'function') {
                alert_success(solve.message, {timer: 2000});
                setTimeout(function(){ location.reload(); }, 1500);
            }
        }
    }
}
</script>
