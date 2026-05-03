<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain Yönetim Sayfası
 * Tab'lar: Özet, WHOIS, DNS, Yönlendirme, Doğrulama, Güvenlik, Transfer, İptal
 *
 * WiseCP runtime: $product, $proanse, $options, $module_con, $invoice, $links, $tld, $whois_information
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$hoptions = ["datatables", "jquery-ui"];

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
$product   = isset($product) && is_array($product) ? $product : [];
$proanse   = isset($proanse) && is_array($proanse) ? $proanse : $product;
$options   = isset($options) && is_array($options) ? $options : [];
$invoice   = isset($invoice) && is_array($invoice) ? $invoice : null;
$links     = isset($links) && is_array($links) ? $links : [];
$tld       = isset($tld) ? $tld : '';
$module_con = isset($module_con) ? $module_con : null;
$whois_profiles = isset($whois_profiles) && is_array($whois_profiles) ? $whois_profiles : [];
$require_verification = !empty($require_verification);

// Domain bilgileri
$d_id       = $proanse['id'] ?? 0;
$d_name     = $proanse['name'] ?? ($options['domain'] ?? 'domain.com');
$d_status   = strtolower($proanse['status'] ?? 'unknown');
$d_duedate  = $proanse['duedate'] ?? '';
$d_cdate    = $proanse['cdate'] ?? '';
$d_renewal  = $proanse['renewaldate'] ?? '';
$d_autopay  = !empty($proanse['auto_pay']);
$d_period   = $proanse['period'] ?? '';
$d_ptime    = $proanse['period_time'] ?? '';

$controller_url = $links['controller'] ?? '';
$ajax_url       = $links['ajax'] ?? '';
$products_url   = cdg_link('products-domain');

// Dokümantasyon yetkileri
$dns_manage           = !empty($product['dns_manage']) || !empty($options['dns_manage']);
$allow_dns_cns        = (is_object($module_con) && method_exists($module_con,'CNSList'));
$allow_dns_records    = (is_object($module_con) && method_exists($module_con,'getDnsRecords'));
$allow_dns_sec_records = (is_object($module_con) && method_exists($module_con,'getDnsSecRecords'));
$allow_forwarding_dmn = (is_object($module_con) && method_exists($module_con,'getForwardingDomain'));
$allow_forwarding_eml = (is_object($module_con) && method_exists($module_con,'getEmailForwards'));
$allow_documents      = (isset($info_docs) && is_array($info_docs) && !empty($info_docs));

// CNS list preload (Classic uyumlu - PHP-side)
$cdg_cns_list = [];
if($allow_dns_cns) {
    try {
        $tmp_cns = $module_con->CNSList($options);
        if(is_array($tmp_cns)) $cdg_cns_list = $tmp_cns;
    } catch(\Throwable $e) {
        // Module hatasi - sessiz gec
    }
}

// DNS Records preload
$cdg_dns_records = [];
if($allow_dns_records) {
    try {
        $tmp_dns = $module_con->getDnsRecords();
        if(is_array($tmp_dns)) $cdg_dns_records = $tmp_dns;
    } catch(\Throwable $e) {}
}

// DNSSEC Records preload
$cdg_dnssec_records = [];
if($allow_dns_sec_records) {
    try {
        $tmp_sec = $module_con->getDnsSecRecords();
        if(is_array($tmp_sec)) $cdg_dnssec_records = $tmp_sec;
    } catch(\Throwable $e) {}
}

// Email Forwards preload
$cdg_email_forwards = [];
if($allow_forwarding_eml) {
    try {
        $tmp_eml = $module_con->getEmailForwards();
        if(is_array($tmp_eml)) $cdg_email_forwards = $tmp_eml;
    } catch(\Throwable $e) {}
}

// Forwarding Domain preload
$cdg_forwarding_domain = null;
if($allow_forwarding_dmn) {
    try {
        $tmp_fwd = $module_con->getForwardingDomain();
        if(is_array($tmp_fwd)) $cdg_forwarding_domain = $tmp_fwd;
    } catch(\Throwable $e) {}
}

// Status meta
function cdg_pdom_status($status) {
    $map = [
        'active'    => ['cls' => 'cdg-pdm-badge-success', 'lbl' => 'Aktif',          'icon' => 'check-circle-fill', 'color' => '#10b981'],
        'inprocess' => ['cls' => 'cdg-pdm-badge-warning', 'lbl' => 'İşlemde',        'icon' => 'gear-fill',         'color' => '#f59e0b'],
        'waiting'   => ['cls' => 'cdg-pdm-badge-info',    'lbl' => 'Onay Bekliyor',  'icon' => 'hourglass-split',   'color' => '#00D3E5'],
        'suspended' => ['cls' => 'cdg-pdm-badge-warning', 'lbl' => 'Askıda',         'icon' => 'pause-circle-fill', 'color' => '#f59e0b'],
        'cancelled' => ['cls' => 'cdg-pdm-badge-danger',  'lbl' => 'İptal',          'icon' => 'x-circle-fill',     'color' => '#ef4444'],
        'expired'   => ['cls' => 'cdg-pdm-badge-danger',  'lbl' => 'Süresi Doldu',   'icon' => 'calendar-x-fill',   'color' => '#ef4444'],
    ];
    return $map[$status] ?? ['cls' => 'cdg-pdm-badge-info', 'lbl' => ucfirst($status), 'icon' => 'question-circle', 'color' => '#64748b'];
}
$st_meta = cdg_pdom_status($d_status);

function cdg_pdom_date($d) {
    if(!$d) return '-';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        return DateManager::format(Config::get("options/date-format") ?: 'd.m.Y', $d);
    }
    if(strpos((string)$d, '0000') === 0) return '-';
    return date('d.m.Y', strtotime((string)$d));
}

// CSRF token helper
function cdg_csrf($action) {
    if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) {
        return Validation::get_csrf_token($action);
    }
    return '';
}

// Mevcut NS kayıtları (WiseCP runtime: $options['ns1'], $options['ns2'], $options['ns3'], $options['ns4'])
$current_ns = [];
for($i = 1; $i <= 4; $i++) {
    $key = 'ns' . $i;
    if(isset($options[$key]) && $options[$key]) {
        $current_ns[$i-1] = $options[$key];
    } else {
        $current_ns[$i-1] = '';
    }
}
// Geri uyumluluk: bazı module'lar 'nameservers' veya 'ns' array'ı kullanır
if(empty(array_filter($current_ns))) {
    if(isset($options['nameservers']) && is_array($options['nameservers'])) {
        foreach(array_values($options['nameservers']) as $i => $ns) {
            if($i < 4) $current_ns[$i] = $ns;
        }
    } elseif(isset($options['ns']) && is_array($options['ns'])) {
        foreach(array_values($options['ns']) as $i => $ns) {
            if($i < 4) $current_ns[$i] = $ns;
        }
    }
}

// Whois privacy
// Whois privacy - WiseCP runtime: $wprivacy primary, $options['whois_privacy'] fallback
$whois_privacy_active = isset($wprivacy) ? !empty($wprivacy) : !empty($options['whois_privacy']);
$whois_privacy_price = $wprivacy_price ?? '';
$whois_privacy_endtime = $wprivacy_endtime ?? '';
// Transfer lock
$transfer_lock = !empty($options['transferlock']);
?>

<style>
.cdg-pdm {
    --pdm-primary: #2E3B4E;
    --pdm-success: #10b981;
    --pdm-warning: #f59e0b;
    --pdm-danger: #ef4444;
    --pdm-info: #00D3E5;
    --pdm-purple: #8b5cf6;
    --pdm-bg: #f8fafc;
    --pdm-card: #fff;
    --pdm-text: #0f172a;
    --pdm-muted: #64748b;
    --pdm-border: #e2e8f0;
    --pdm-radius: 14px;
    --pdm-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --pdm-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--pdm-text);
    background: var(--pdm-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-pdm *, .cdg-pdm *::before, .cdg-pdm *::after { box-sizing: border-box; }
.cdg-pdm a { text-decoration: none; color: inherit; }
.cdg-pdm-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-pdm-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--pdm-border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--pdm-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-pdm-back:hover { border-color: var(--pdm-primary); color: var(--pdm-primary); }

/* HERO PANEL */
.cdg-pdm-hero {
    background: linear-gradient(135deg, #2E3B4E 0%, #00D3E5 50%, #00D3E5 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(46,59,78,0.20);
}
.cdg-pdm-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-pdm-hero-row {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 20px;
    align-items: center;
    position: relative; z-index: 1;
}
.cdg-pdm-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
}
.cdg-pdm-hero-text { min-width: 0; }
.cdg-pdm-hero-eyebrow {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 4px;
}
.cdg-pdm-hero-text h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 6px;
    letter-spacing: -0.5px;
    word-break: break-all;
}
.cdg-pdm-hero-meta {
    display: flex; gap: 14px; flex-wrap: wrap;
    font-size: 13px;
    opacity: 0.92;
}
.cdg-pdm-hero-meta span { display: inline-flex; align-items: center; gap: 6px; }
.cdg-pdm-hero-status {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    background: rgba(255,255,255,0.22);
    backdrop-filter: blur(10px);
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    flex-shrink: 0;
}

/* QUICK ACTIONS */
.cdg-pdm-quick {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 10px;
    margin-bottom: 22px;
}
.cdg-pdm-quick-card {
    background: #fff;
    border: 1px solid var(--pdm-border);
    border-radius: var(--pdm-radius);
    padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
    box-shadow: var(--pdm-shadow);
    cursor: pointer;
    transition: all 0.18s;
}
.cdg-pdm-quick-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--pdm-shadow-lg);
    border-color: var(--pdm-primary);
}
.cdg-pdm-quick-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: grid; place-items: center;
    color: #fff;
    font-size: 16px;
    flex-shrink: 0;
}
.cdg-pdm-quick-card-renewal .cdg-pdm-quick-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.cdg-pdm-quick-card-autopay .cdg-pdm-quick-icon { background: linear-gradient(135deg, #10b981, #34d399); }
.cdg-pdm-quick-card-lock .cdg-pdm-quick-icon { background: linear-gradient(135deg, #2E3B4E, #00D3E5); }
.cdg-pdm-quick-card-privacy .cdg-pdm-quick-icon { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.cdg-pdm-quick-info { min-width: 0; flex: 1; }
.cdg-pdm-quick-info-label {
    font-size: 11px;
    color: var(--pdm-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.cdg-pdm-quick-info-value {
    font-size: 14px;
    font-weight: 700;
    color: var(--pdm-text);
}

/* TAB NAVIGATION */
.cdg-pdm-tabs {
    background: #fff;
    border: 1px solid var(--pdm-border);
    border-radius: var(--pdm-radius);
    padding: 8px;
    box-shadow: var(--pdm-shadow);
    margin-bottom: 18px;
    display: flex;
    gap: 4px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}
.cdg-pdm-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--pdm-muted);
    cursor: pointer;
    transition: all 0.18s;
    background: transparent;
    border: 0;
    font-family: inherit;
    white-space: nowrap;
}
.cdg-pdm-tab:hover { background: var(--pdm-bg); color: var(--pdm-text); }
.cdg-pdm-tab.active {
    background: var(--pdm-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(46,59,78,0.22);
}

/* TAB PANE */
.cdg-pdm-pane { display: none; }
.cdg-pdm-pane.active { display: block; animation: cdg-pdm-fade 0.25s ease; }
@keyframes cdg-pdm-fade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

/* CARD */
.cdg-pdm-card {
    background: #fff;
    border: 1px solid var(--pdm-border);
    border-radius: var(--pdm-radius);
    box-shadow: var(--pdm-shadow);
    margin-bottom: 18px;
    overflow: hidden;
}
.cdg-pdm-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--pdm-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-pdm-card-head h3 {
    font-size: 14px;
    font-weight: 800;
    margin: 0;
    color: var(--pdm-text);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-pdm-card-head h3 i { color: var(--pdm-primary); font-size: 16px; }
.cdg-pdm-card-body { padding: 22px; }

/* INFO LIST */
.cdg-pdm-info { list-style: none; padding: 0; margin: 0; }
.cdg-pdm-info li {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px dashed var(--pdm-border);
    font-size: 13px;
    gap: 12px;
}
.cdg-pdm-info li:last-child { border-bottom: 0; padding-bottom: 0; }
.cdg-pdm-info li:first-child { padding-top: 0; }
.cdg-pdm-info-label { color: var(--pdm-muted); font-weight: 600; flex-shrink: 0; }
.cdg-pdm-info-value { color: var(--pdm-text); font-weight: 700; text-align: right; word-break: break-word; }

/* FORM */
.cdg-pdm-field { margin-bottom: 14px; }
.cdg-pdm-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--pdm-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 7px;
}
.cdg-pdm-input,
.cdg-pdm-select {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--pdm-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--pdm-text);
    background: #fff;
    outline: none;
    transition: all 0.18s;
    font-family: inherit;
}
.cdg-pdm-input:focus,
.cdg-pdm-select:focus {
    border-color: var(--pdm-primary);
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}
.cdg-pdm-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* BUTTONS */
.cdg-pdm-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.cdg-pdm-btn-primary {
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    box-shadow: 0 6px 18px rgba(46,59,78,0.22);
}
.cdg-pdm-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-pdm-btn-success {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.22);
}
.cdg-pdm-btn-success:hover { transform: translateY(-1px); color: #fff; }
.cdg-pdm-btn-warning {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    box-shadow: 0 6px 18px rgba(245,158,11,0.22);
}
.cdg-pdm-btn-warning:hover { transform: translateY(-1px); color: #fff; }
.cdg-pdm-btn-danger {
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: #fff;
    box-shadow: 0 6px 18px rgba(239,68,68,0.22);
}
.cdg-pdm-btn-danger:hover { transform: translateY(-1px); color: #fff; }
.cdg-pdm-btn-outline {
    background: #fff;
    color: var(--pdm-text);
    border: 1px solid var(--pdm-border);
}
.cdg-pdm-btn-outline:hover { border-color: var(--pdm-primary); color: var(--pdm-primary); }

/* TOGGLE SWITCH */
.cdg-pdm-toggle {
    display: inline-flex; align-items: center; gap: 12px;
    cursor: pointer;
    user-select: none;
}
.cdg-pdm-toggle-switch {
    width: 44px;
    height: 24px;
    background: var(--pdm-border);
    border-radius: 99px;
    position: relative;
    transition: background 0.2s;
}
.cdg-pdm-toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.cdg-pdm-toggle.active .cdg-pdm-toggle-switch { background: var(--pdm-success); }
.cdg-pdm-toggle.active .cdg-pdm-toggle-switch::after { transform: translateX(20px); }
.cdg-pdm-toggle-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--pdm-text);
}

/* BADGE */
.cdg-pdm-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-pdm-badge-success { background: #d1fae5; color: #065f46; }
.cdg-pdm-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-pdm-badge-danger  { background: #fee2e2; color: #991b1b; }
.cdg-pdm-badge-info    { background: #CFFAFE; color: #2E3B4E; }

/* ALERT */
.cdg-pdm-alert {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 13px;
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 14px;
    line-height: 1.5;
}
.cdg-pdm-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.cdg-pdm-alert-info { background: #CFFAFE; color: #1A2332; border: 1px solid #67E8F9; }
.cdg-pdm-alert-info i { color: #2E3B4E; }
.cdg-pdm-alert-warning { background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }
.cdg-pdm-alert-warning i { color: #f59e0b; }
.cdg-pdm-alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.cdg-pdm-alert-success i { color: #10b981; }
.cdg-pdm-alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.cdg-pdm-alert-danger i { color: #ef4444; }

/* TWO COL */
.cdg-pdm-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .cdg-pdm-hero-row { grid-template-columns: 1fr; text-align: center; }
    .cdg-pdm-hero-status { justify-self: center; }
    .cdg-pdm-grid-2 { grid-template-columns: 1fr; }
    .cdg-pdm-row { grid-template-columns: 1fr; }
    .cdg-pdm-hero-text h1 { font-size: 22px; }
}
</style>

<div class="cdg-pdm">
<div class="cdg-pdm-wrap">

    <a href="<?php echo htmlspecialchars($products_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pdm-back">
        <i class="bi bi-arrow-left"></i> Domainlerime Dön
    </a>

    <!-- HERO -->
    <section class="cdg-pdm-hero">
        <div class="cdg-pdm-hero-row">
            <div class="cdg-pdm-hero-icon"><i class="bi bi-globe2"></i></div>
            <div class="cdg-pdm-hero-text">
                <div class="cdg-pdm-hero-eyebrow">DOMAIN YÖNETİMİ</div>
                <h1><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
                <div class="cdg-pdm-hero-meta">
                    <?php if($d_duedate): ?>
                    <span><i class="bi bi-calendar-check"></i> Bitiş: <?php echo htmlspecialchars(cdg_pdom_date($d_duedate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($d_period && $d_ptime): ?>
                    <span><i class="bi bi-clock-history"></i> <?php echo htmlspecialchars($d_period . ' ' . $d_ptime, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cdg-pdm-hero-status">
                <i class="bi bi-<?php echo htmlspecialchars($st_meta['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                <?php echo htmlspecialchars($st_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </div>
        </div>
    </section>

    <!-- QUICK ACTIONS / STATUS CARDS -->
    <div class="cdg-pdm-quick">
        <div class="cdg-pdm-quick-card cdg-pdm-quick-card-renewal" onclick="cdgDomain.renew()">
            <div class="cdg-pdm-quick-icon"><i class="bi bi-arrow-clockwise"></i></div>
            <div class="cdg-pdm-quick-info">
                <div class="cdg-pdm-quick-info-label">Yenileme</div>
                <div class="cdg-pdm-quick-info-value">Şimdi Yenile</div>
            </div>
        </div>

        <div class="cdg-pdm-quick-card cdg-pdm-quick-card-autopay" onclick="cdgDomain.toggleAutoPay(this)">
            <div class="cdg-pdm-quick-icon"><i class="bi bi-credit-card-2-back"></i></div>
            <div class="cdg-pdm-quick-info">
                <div class="cdg-pdm-quick-info-label">Otomatik Ödeme</div>
                <div class="cdg-pdm-quick-info-value" id="cdg-autopay-status">
                    <?php echo $d_autopay ? 'Aktif' : 'Pasif'; ?>
                </div>
            </div>
        </div>

        <div class="cdg-pdm-quick-card cdg-pdm-quick-card-lock">
            <div class="cdg-pdm-quick-icon"><i class="bi bi-shield-lock"></i></div>
            <div class="cdg-pdm-quick-info">
                <div class="cdg-pdm-quick-info-label">Transfer Kilidi</div>
                <div class="cdg-pdm-quick-info-value">
                    <?php echo $transfer_lock ? 'Kilitli' : 'Açık'; ?>
                </div>
            </div>
        </div>

        <div class="cdg-pdm-quick-card cdg-pdm-quick-card-privacy">
            <div class="cdg-pdm-quick-icon"><i class="bi bi-eye-slash"></i></div>
            <div class="cdg-pdm-quick-info">
                <div class="cdg-pdm-quick-info-label">WHOIS Gizliliği</div>
                <div class="cdg-pdm-quick-info-value">
                    <?php echo $whois_privacy_active ? 'Aktif' : 'Pasif'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION -->
    <div class="cdg-pdm-tabs">
        <button class="cdg-pdm-tab active" data-pane="summary">
            <i class="bi bi-info-circle"></i> Özet
        </button>
        <button class="cdg-pdm-tab" data-pane="dns">
            <i class="bi bi-server"></i> DNS / Nameserver
        </button>
        <button class="cdg-pdm-tab" data-pane="whois">
            <i class="bi bi-person-vcard"></i> WHOIS
        </button>
        <button class="cdg-pdm-tab" data-pane="forwarding">
            <i class="bi bi-arrow-right-circle"></i> Yönlendirme
        </button>
        <button class="cdg-pdm-tab" data-pane="security">
            <i class="bi bi-shield-check"></i> Güvenlik
        </button>
        <button class="cdg-pdm-tab" data-pane="transfer">
            <i class="bi bi-arrow-left-right"></i> Transfer
        </button>
    </div>

    <!-- TAB: ÖZET -->
    <div class="cdg-pdm-pane active" id="cdg-pdm-pane-summary">
        <div class="cdg-pdm-grid-2">
            <div class="cdg-pdm-card">
                <div class="cdg-pdm-card-head">
                    <h3><i class="bi bi-info-circle"></i> Hizmet Bilgileri</h3>
                </div>
                <div class="cdg-pdm-card-body">
                    <ul class="cdg-pdm-info">
                        <li><span class="cdg-pdm-info-label">Domain</span><span class="cdg-pdm-info-value"><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php if($tld): ?>
                        <li><span class="cdg-pdm-info-label">TLD</span><span class="cdg-pdm-info-value">.<?php echo htmlspecialchars(ltrim($tld, '.'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <li><span class="cdg-pdm-info-label">Durum</span><span class="cdg-pdm-info-value"><span class="cdg-pdm-badge <?php echo $st_meta['cls']; ?>"><?php echo htmlspecialchars($st_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></span></li>
                        <?php if($d_cdate): ?>
                        <li><span class="cdg-pdm-info-label">Kayıt Tarihi</span><span class="cdg-pdm-info-value"><?php echo htmlspecialchars(cdg_pdom_date($d_cdate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_duedate): ?>
                        <li><span class="cdg-pdm-info-label">Bitiş Tarihi</span><span class="cdg-pdm-info-value"><?php echo htmlspecialchars(cdg_pdom_date($d_duedate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_renewal): ?>
                        <li><span class="cdg-pdm-info-label">Yenileme Tarihi</span><span class="cdg-pdm-info-value"><?php echo htmlspecialchars(cdg_pdom_date($d_renewal), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_period && $d_ptime): ?>
                        <li><span class="cdg-pdm-info-label">Periyot</span><span class="cdg-pdm-info-value"><?php echo htmlspecialchars($d_period . ' ' . $d_ptime, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="cdg-pdm-card">
                <div class="cdg-pdm-card-head">
                    <h3><i class="bi bi-receipt"></i> Yenileme & Fatura</h3>
                </div>
                <div class="cdg-pdm-card-body">
                    <?php if($invoice && !empty($invoice['id'])): ?>
                    <div class="cdg-pdm-alert cdg-pdm-alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Bekleyen yenileme faturanız bulunuyor.</strong><br>
                            Fatura no: #<?php echo htmlspecialchars($invoice['number'] ?? $invoice['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </div>
                    </div>
                    <?php if(!empty($invoice['detail_link'])): ?>
                    <a href="<?php echo htmlspecialchars($invoice['detail_link'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pdm-btn cdg-pdm-btn-warning" style="width:100%;justify-content:center;margin-bottom:8px;">
                        <i class="bi bi-credit-card"></i> Faturayı Görüntüle / Öde
                    </a>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="cdg-pdm-alert cdg-pdm-alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>Yenileme zamanı geldiğinde otomatik fatura oluşturulur. İsterseniz şimdi de yenileyebilirsiniz.</div>
                    </div>

                    <?php
                    // WiseCP runtime: $renewal_list = [year => formatted_price] (yenileme donemleri)
                    $renewal_periods = isset($renewal_list) && is_array($renewal_list) ? $renewal_list : [];
                    ?>

                    <?php if(!empty($renewal_periods)): ?>
                    <!-- Yenileme Donemi Secici -->
                    <div class="cdg-pdm-field" style="margin-bottom:10px;">
                        <label class="cdg-pdm-label">Yenileme Donemi</label>
                        <select id="cdg-pdm-renewal-period" class="cdg-pdm-select">
                            <?php foreach($renewal_periods as $year => $price): ?>
                            <option value="<?php echo (int)$year; ?>"><?php echo (int)$year; ?> Yıl - <?php echo htmlspecialchars($price, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-success" style="width:100%;justify-content:center;margin-bottom:8px;" onclick="cdgDomain.renewWithPeriod()">
                        <i class="bi bi-arrow-clockwise"></i> Secilen Donemde Yenile
                    </button>
                    <?php else: ?>
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-success" style="width:100%;justify-content:center;margin-bottom:8px;" onclick="cdgDomain.renew()">
                        <i class="bi bi-arrow-clockwise"></i> Şimdi Yenile
                    </button>
                    <?php endif; ?>
                    <?php endif; // dış: $invoice && !empty($invoice['id']) ?>

                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-outline" style="width:100%;justify-content:center;" onclick="cdgDomain.toggleAutoPay(this)">
                        <i class="bi bi-credit-card-2-back"></i>
                        Otomatik Ödeme: <span id="cdg-autopay-status-2"><?php echo $d_autopay ? 'Aktif' : 'Pasif'; ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: DNS / NAMESERVER -->
    <div class="cdg-pdm-pane" id="cdg-pdm-pane-dns">
        <div class="cdg-pdm-card">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-server"></i> Nameserver Ayarları</h3>
                <span class="cdg-pdm-badge cdg-pdm-badge-info">Maks. 4 NS</span>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Nameserver değişiklikleri internette yayılması için 24 ila 48 saat sürebilir.</div>
                </div>

                <form method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" id="cdg-ns-form">
                    <?php echo cdg_csrf('domain_modify_dns'); ?>
                    <input type="hidden" name="operation" value="domain_modify_dns">
                    <input type="hidden" name="id" value="<?php echo (int)$d_id; ?>">

                    <?php for($i = 1; $i <= 4; $i++): $val = $current_ns[$i-1] ?? ''; ?>
                    <div class="cdg-pdm-field">
                        <label class="cdg-pdm-label">Nameserver <?php echo $i; ?> <?php if($i <= 2): ?><span style="color:#ef4444;">*</span><?php endif; ?></label>
                        <input type="text" name="dns[]" class="cdg-pdm-input" value="<?php echo htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="ns<?php echo $i; ?>.example.com">
                    </div>
                    <?php endfor; ?>

                    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;">
                        <button type="submit" class="cdg-pdm-btn cdg-pdm-btn-primary">
                            <i class="bi bi-save"></i> Nameserver'ları Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if($allow_dns_records || $allow_dns_cns): ?>
        <div class="cdg-pdm-card">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-list-task"></i> Gelişmiş DNS Yönetimi</h3>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-success">
                    <i class="bi bi-check-circle"></i>
                    <div>
                        Bu domain için gelişmiş DNS yönetimi mevcuttur.
                        <?php if($allow_dns_records): ?>A/AAAA/CNAME/MX/TXT/SRV kayıtları oluşturabilir,<?php endif; ?>
                        <?php if($allow_dns_cns): ?>özel nameserver (Child NS) tanımlayabilirsiniz.<?php endif; ?>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php if($allow_dns_records): ?>
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-outline" onclick="cdgDomain.openDnsRecords()">
                        <i class="bi bi-list-ul"></i> DNS Kayıtlarını Yönet
                    </button>
                    <?php endif; ?>
                    <?php if($allow_dns_cns): ?>
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-outline" onclick="cdgDomain.openCNS()">
                        <i class="bi bi-server"></i> Child Nameserver
                    </button>
                    <?php endif; ?>
                    <?php if($allow_dns_sec_records): ?>
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-outline" onclick="cdgDomain.openDnsSec()">
                        <i class="bi bi-shield-lock"></i> DNSSEC
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- TAB: WHOIS -->
    <div class="cdg-pdm-pane" id="cdg-pdm-pane-whois">
        <div class="cdg-pdm-card">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-person-vcard"></i> WHOIS İletişim Bilgileri</h3>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>WHOIS bilgileri domain kaydında bulunan iletişim bilgilerinizdir. Whois profili oluşturarak çoklu domain yönetimi kolaylaşır.</div>
                </div>

                <?php if(!empty($whois_profiles)): ?>
                <form method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                    <?php echo cdg_csrf('domain_modify_whois'); ?>
                    <input type="hidden" name="operation" value="domain_modify_whois">
                    <input type="hidden" name="id" value="<?php echo (int)$d_id; ?>">

                    <div class="cdg-pdm-field">
                        <label class="cdg-pdm-label">WHOIS Profili Seç</label>
                        <select name="profile_id" class="cdg-pdm-select">
                            <option value="">— Profil Seçin —</option>
                            <?php foreach($whois_profiles as $pf):
                                if(!is_array($pf)) continue;
                                $pf_id    = $pf['id'] ?? 0;
                                $pf_name  = $pf['name'] ?? 'Profil';
                                $pf_pname = $pf['person_name'] ?? '';
                                $pf_email = $pf['person_email'] ?? '';
                            ?>
                            <option value="<?php echo (int)$pf_id; ?>"><?php echo htmlspecialchars($pf_name . ($pf_pname ? ' (' . $pf_pname . ')' : '') . ($pf_email ? ' - ' . $pf_email : ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                        <a href="<?php echo htmlspecialchars(cdg_link('products-domain-whois-profiles'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pdm-btn cdg-pdm-btn-outline">
                            <i class="bi bi-people"></i> WHOIS Profillerimi Yönet
                        </a>
                        <button type="submit" class="cdg-pdm-btn cdg-pdm-btn-primary">
                            <i class="bi bi-save"></i> WHOIS'i Güncelle
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="cdg-pdm-alert cdg-pdm-alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        Henüz WHOIS profiliniz yok. Önce bir profil oluşturarak WHOIS bilgilerinizi yönetebilirsiniz.
                    </div>
                </div>
                <a href="<?php echo htmlspecialchars(cdg_link('products-domain-create-whois-profile'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pdm-btn cdg-pdm-btn-primary">
                    <i class="bi bi-plus-circle"></i> WHOIS Profili Oluştur
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // === MANUEL WHOIS FORM (Detayli Iletisim Bilgileri) ===
        // WiseCP runtime: $contact_types (registrant, admin, tech, billing), $whois (mevcut bilgiler)
        $cdg_contact_types = isset($contact_types) && is_array($contact_types) ? $contact_types : [
            'registrant' => 'Sahibi',
            'admin'      => 'Yonetici',
            'tech'       => 'Teknik',
            'billing'    => 'Faturalama',
        ];
        $cdg_whois_data = isset($whois) && is_array($whois) ? $whois : [];
        ?>

        <div class="cdg-pdm-card" style="margin-top:18px;">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-pencil-square"></i> Detaylı WHOIS Düzenleme</h3>
                <span style="font-size:11px;color:#94a3b8;">Tüm kontak tipleri için manuel düzenleme</span>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>WHOIS bilgilerinizi profil seçmeden de manuel olarak düzenleyebilirsiniz. Her kontak tipi için ayrı bilgi girebilirsinizsiniz.</div>
                </div>

                <form method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" id="cdg-pdm-whois-form">
                    <?php echo cdg_csrf('domain_modify_whois'); ?>
                    <input type="hidden" name="operation" value="domain_modify_whois">
                    <input type="hidden" name="id" value="<?php echo (int)$d_id; ?>">

                    <!-- Contact Type Tabs -->
                    <div style="display:flex;gap:4px;background:#f8fafc;padding:5px;border-radius:8px;margin-bottom:14px;flex-wrap:wrap;">
                        <?php $first_ct = true; foreach($cdg_contact_types as $ct_key => $ct_label): ?>
                        <button type="button" class="cdg-pdm-ct-tab <?php echo $first_ct ? 'active' : ''; ?>" data-ct="<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" onclick="cdgPdmCtTab(this, '<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>')">
                            <?php
                            $ct_icon = ['registrant' => 'person-fill', 'admin' => 'person-gear', 'tech' => 'tools', 'billing' => 'receipt'][$ct_key] ?? 'person';
                            ?>
                            <i class="bi bi-<?php echo $ct_icon; ?>"></i> <?php echo htmlspecialchars($ct_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </button>
                        <?php $first_ct = false; endforeach; ?>
                    </div>

                    <!-- Contact Type Panes -->
                    <?php $first_ct = true; foreach($cdg_contact_types as $ct_key => $ct_label):
                        $cw = $cdg_whois_data[$ct_key] ?? [];
                    ?>
                    <div class="cdg-pdm-ct-pane" id="cdg-pdm-ct-pane-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:<?php echo $first_ct ? 'block' : 'none'; ?>;">

                        <!-- Profil Sec (varsa) -->
                        <?php if(!empty($whois_profiles)): ?>
                        <div class="cdg-pdm-field">
                            <label class="cdg-pdm-label">Hızlı Profil Secimi</label>
                            <select name="profile_id[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>]" class="cdg-pdm-select" data-ct="<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" onchange="cdgPdmWhoisFillFromProfile(this, '<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>')">
                                <option value="0">— Manuel doldurun veya profil secin —</option>
                                <?php foreach($whois_profiles as $pf):
                                    if(!is_array($pf)) continue;
                                    $pf_id = $pf['id'] ?? 0;
                                    $pf_name = $pf['name'] ?? 'Profil';
                                    $pf_info = $pf['information'] ?? '';
                                    $pf_selected = (isset($cw['profile_id']) && $cw['profile_id'] == $pf_id) ? 'selected' : '';
                                ?>
                                <option value="<?php echo (int)$pf_id; ?>" data-information='<?php echo htmlspecialchars($pf_info, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>' <?php echo $pf_selected; ?>>
                                    <?php echo htmlspecialchars($pf_name . (!empty($pf['person_name']) ? ' (' . $pf['person_name'] . ')' : ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="cdg-pdm-form-row" style="grid-template-columns:1fr 1fr;">
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Tam Ad <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][Name]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-Name" value="<?php echo htmlspecialchars($cw['Name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Ad Soyad">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Şirket Adı</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][Company]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-Company" value="<?php echo htmlspecialchars($cw['Company'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Opsiyonel">
                            </div>
                        </div>

                        <div class="cdg-pdm-field">
                            <label class="cdg-pdm-label">E-posta <span style="color:#ef4444;">*</span></label>
                            <input type="email" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][EMail]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-EMail" value="<?php echo htmlspecialchars($cw['EMail'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="ornek@example.com">
                        </div>

                        <div class="cdg-pdm-form-row" style="grid-template-columns:120px 1fr 120px 1fr;">
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Tel Kod</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][PhoneCountryCode]" class="cdg-pdm-input" value="<?php echo htmlspecialchars($cw['PhoneCountryCode'] ?? '+90', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="+90">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Telefon <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][Phone]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-Phone" value="<?php echo htmlspecialchars($cw['Phone'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="555 123 4567">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Faks Kod</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][FaxCountryCode]" class="cdg-pdm-input" value="<?php echo htmlspecialchars($cw['FaxCountryCode'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Faks</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][Fax]" class="cdg-pdm-input" value="<?php echo htmlspecialchars($cw['Fax'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            </div>
                        </div>

                        <div class="cdg-pdm-field">
                            <label class="cdg-pdm-label">Adres <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][Address]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-Address" value="<?php echo htmlspecialchars($cw['AddressLine1'] ?? ($cw['Address'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Sokak, mahalle, no">
                        </div>

                        <div class="cdg-pdm-form-row" style="grid-template-columns:1fr 1fr 1fr 120px;">
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Şehir</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][City]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-City" value="<?php echo htmlspecialchars($cw['City'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Bolge</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][State]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-State" value="<?php echo htmlspecialchars($cw['State'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Ulke (Kod)</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][Country]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-Country" value="<?php echo htmlspecialchars($cw['Country'] ?? 'TR', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" maxlength="3" placeholder="TR">
                            </div>
                            <div class="cdg-pdm-field">
                                <label class="cdg-pdm-label">Posta Kodu</label>
                                <input type="text" name="info[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>][ZipCode]" class="cdg-pdm-input cdg-pdm-whois-<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>-ZipCode" value="<?php echo htmlspecialchars($cw['ZipCode'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            </div>
                        </div>

                        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#475569;cursor:pointer;margin-top:8px;">
                            <input type="checkbox" name="apply_to_all[<?php echo htmlspecialchars($ct_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>]" value="1">
                            <i class="bi bi-arrows-collapse"></i> Bu bilgileri diger tum kontak tiplerine de uygula
                        </label>
                    </div>
                    <?php $first_ct = false; endforeach; ?>

                    <div style="margin-top:20px;display:flex;justify-content:flex-end;">
                        <button type="submit" class="cdg-pdm-btn cdg-pdm-btn-primary">
                            <i class="bi bi-save"></i> Manuel WHOIS'i Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB: YÖNLENDIRME -->
    <div class="cdg-pdm-pane" id="cdg-pdm-pane-forwarding">
        <?php if($allow_forwarding_dmn): ?>
        <?php
        // PHP-side preloaded forwarding domain
        $fwd_active = isset($cdg_forwarding_domain) && is_array($cdg_forwarding_domain) && !empty($cdg_forwarding_domain['status']);
        $fwd_protocol = $cdg_forwarding_domain['protocol'] ?? 'http';
        $fwd_target_domain = $cdg_forwarding_domain['domain'] ?? '';
        $fwd_method = $cdg_forwarding_domain['method'] ?? '301';
        ?>
        <div class="cdg-pdm-card">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-arrow-right-circle"></i> Domain Yönlendirme</h3>
                <?php if($fwd_active): ?>
                <span class="cdg-pdm-badge cdg-pdm-badge-success"><i class="bi bi-check-circle"></i> Aktif</span>
                <?php endif; ?>
            </div>
            <div class="cdg-pdm-card-body">
                <?php if($fwd_active): ?>
                <div class="cdg-pdm-alert cdg-pdm-alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>
                        <strong>Yönlendirme aktif:</strong>
                        <code style="background:rgba(16,185,129,0.10);padding:2px 6px;border-radius:4px;font-family:monospace;">
                            <?php echo htmlspecialchars($fwd_protocol . '://' . $fwd_target_domain, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </code>
                        (<?php echo (int)$fwd_method; ?> <?php echo $fwd_method == '301' ? 'Kalıcı' : 'Geçici'; ?>)
                    </div>
                </div>
                <?php else: ?>
                <div class="cdg-pdm-alert cdg-pdm-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Bu domain ziyaretçilerini başka bir adrese (URL) yönlendirebilirsiniz.</div>
                </div>
                <?php endif; ?>

                <form id="cdg-fwd-form">
                    <div class="cdg-pdm-field">
                        <label class="cdg-pdm-label">Hedef URL</label>
                        <div style="display:grid;grid-template-columns:120px 1fr;gap:8px;">
                            <select id="forward_protocol" class="cdg-pdm-select" <?php echo $fwd_active ? 'disabled' : ''; ?>>
                                <option value="http"<?php echo $fwd_protocol === 'http' ? ' selected' : ''; ?>>http://</option>
                                <option value="https"<?php echo $fwd_protocol === 'https' ? ' selected' : ''; ?>>https://</option>
                            </select>
                            <input type="text" id="forward_domain" class="cdg-pdm-input" placeholder="hedef-site.com" value="<?php echo htmlspecialchars($fwd_target_domain, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $fwd_active ? 'disabled' : ''; ?>>
                        </div>
                    </div>
                    <div class="cdg-pdm-field">
                        <label class="cdg-pdm-label">Yönlendirme Yöntemi</label>
                        <div style="display:flex;gap:14px;flex-wrap:wrap;">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="radio" name="method" value="301"<?php echo $fwd_method == '301' ? ' checked' : ''; ?> style="accent-color:var(--pdm-primary);" <?php echo $fwd_active ? 'disabled' : ''; ?>>
                                <span>301 (Kalıcı)</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="radio" name="method" value="302"<?php echo $fwd_method == '302' ? ' checked' : ''; ?> style="accent-color:var(--pdm-primary);" <?php echo $fwd_active ? 'disabled' : ''; ?>>
                                <span>302 (Geçici)</span>
                            </label>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <?php if($fwd_active): ?>
                        <button type="button" class="cdg-pdm-btn cdg-pdm-btn-danger" onclick="cdgDomain.cancelForward()">
                            <i class="bi bi-x-circle"></i> Yönlendirmeyi İptal Et
                        </button>
                        <?php else: ?>
                        <button type="button" class="cdg-pdm-btn cdg-pdm-btn-primary" onclick="cdgDomain.setForward()">
                            <i class="bi bi-save"></i> Yönlendirme Ayarla
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="cdg-pdm-alert cdg-pdm-alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <div>Bu domain için yönlendirme özelliği mevcut değil. Domain modülünüz bu işlemi desteklemiyor olabilir.</div>
        </div>
        <?php endif; ?>

        <?php if($allow_forwarding_eml): ?>
        <!-- E-Posta Yönlendirme Kartı -->
        <div class="cdg-pdm-card" style="margin-top:18px;">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-envelope-arrow-up"></i> E-Posta Yönlendirme</h3>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Domain'inize gelen e-postaları (örn. <strong>info@<?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>) başka bir adrese yönlendirebilirsiniz.</div>
                </div>
                <div style="text-align:center;padding:8px 0;">
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-primary" onclick="cdgDomain.openEmailForwards()">
                        <i class="bi bi-list-task"></i> E-Posta Yönlendirmelerini Yönet
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- TAB: GUVENLIK -->
    <div class="cdg-pdm-pane" id="cdg-pdm-pane-security">
        <div class="cdg-pdm-grid-2">
            <div class="cdg-pdm-card">
                <div class="cdg-pdm-card-head">
                    <h3><i class="bi bi-shield-lock"></i> Transfer Kilidi</h3>
                </div>
                <div class="cdg-pdm-card-body">
                    <div class="cdg-pdm-alert cdg-pdm-alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>Transfer kilidi açıkken domaininiz başka bir kayıt firmasına izinsiz transfer edilemez.</div>
                    </div>
                    <div style="text-align:center;padding:14px;">
                        <div class="cdg-pdm-toggle <?php echo $transfer_lock ? 'active' : ''; ?>" id="cdg-transferlock-toggle" onclick="cdgDomain.toggleTransferLock(this)">
                            <div class="cdg-pdm-toggle-switch"></div>
                            <div class="cdg-pdm-toggle-label">Transfer Kilidi: <strong><?php echo $transfer_lock ? 'Açık' : 'Kapalı'; ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cdg-pdm-card">
                <div class="cdg-pdm-card-head">
                    <h3><i class="bi bi-eye-slash"></i> WHOIS Gizliliği</h3>
                    <?php if($whois_privacy_active): ?>
                    <span class="cdg-pdm-badge cdg-pdm-badge-success"><i class="bi bi-check-circle"></i> Aktif</span>
                    <?php endif; ?>
                </div>
                <div class="cdg-pdm-card-body">
                    <div class="cdg-pdm-alert cdg-pdm-alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>WHOIS gizliliği aktifken kişisel bilgileriniz kamuya açık WHOIS sorgularında gösterilmez.</div>
                    </div>

                    <?php if($whois_privacy_price): ?>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;text-align:center;">
                            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600;">Yıllık Ücret</div>
                            <div style="font-size:16px;font-weight:800;color:#2E3B4E;margin-top:4px;"><?php echo htmlspecialchars($whois_privacy_price, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        </div>
                        <?php if($whois_privacy_endtime): ?>
                        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px;text-align:center;">
                            <div style="font-size:11px;color:#15803d;text-transform:uppercase;font-weight:600;">Bitiş Tarihi</div>
                            <div style="font-size:14px;font-weight:700;color:#15803d;margin-top:4px;"><?php echo htmlspecialchars($whois_privacy_endtime, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div style="text-align:center;padding:14px;">
                        <div class="cdg-pdm-toggle <?php echo $whois_privacy_active ? 'active' : ''; ?>" id="cdg-whoispriv-toggle" onclick="cdgDomain.toggleWhoisPrivacy(this)">
                            <div class="cdg-pdm-toggle-switch"></div>
                            <div class="cdg-pdm-toggle-label">WHOIS Gizliliği: <strong><?php echo $whois_privacy_active ? 'Aktif' : 'Pasif'; ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($allow_documents): ?>
        <!-- Belge Yönetimi Kartı (sadece ülke kodlu domainler için) -->
        <div class="cdg-pdm-card" style="margin-top:18px;">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-file-earmark-text"></i> Belge Yönetimi</h3>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>Bu domain için kayıt firması belge istiyor. Domain aktifleşmeden önce gerekli belgeleri yüklemeniz gerekir.</div>
                </div>
                <div style="text-align:center;padding:8px 0;">
                    <button type="button" class="cdg-pdm-btn cdg-pdm-btn-primary" onclick="cdgDomain.openDocuments()">
                        <i class="bi bi-cloud-upload"></i> Belgeleri Yönet
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- TAB: TRANSFER -->
    <div class="cdg-pdm-pane" id="cdg-pdm-pane-transfer">
        <div class="cdg-pdm-card">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-arrow-left-right"></i> Domain Transfer Hizmeti</h3>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>Gelen transfer:</strong> Domaininizi başka bir kayıt firmasından bize getirmek için bu sayfayı kullanın.<br>
                        <strong>Giden transfer:</strong> Domaininizi başka bir firmaya götürmek için EPP / Auth Code'a ihtiyacınız vardır.
                    </div>
                </div>

                <div class="cdg-pdm-grid-2">
                    <div>
                        <h4 style="font-size:14px;font-weight:800;margin:0 0 10px;">EPP / Auth Code Talep Et</h4>
                        <p style="font-size:13px;color:var(--pdm-muted);margin:0 0 14px;">Domaininizi başka bir firmaya transfer etmek için gerekli olan kodu alın.</p>
                        <button type="button" class="cdg-pdm-btn cdg-pdm-btn-outline" onclick="cdgDomain.requestEpp()">
                            <i class="bi bi-key"></i> EPP Kodu Al
                        </button>
                    </div>
                    <div>
                        <h4 style="font-size:14px;font-weight:800;margin:0 0 10px;">İptal Talebi</h4>
                        <p style="font-size:13px;color:var(--pdm-muted);margin:0 0 14px;">Domaininizi sonlandırmak isterseniz iptal talebi oluşturabilirsiniz.</p>
                        <?php if($d_status === 'cancelled' || $d_status === 'canceled'): ?>
                        <button type="button" class="cdg-pdm-btn cdg-pdm-btn-success" onclick="cdgDomain.removeCancelled()">
                            <i class="bi bi-arrow-counterclockwise"></i> İptal Talebini Geri Al
                        </button>
                        <?php else: ?>
                        <button type="button" class="cdg-pdm-btn cdg-pdm-btn-danger" onclick="cdgDomain.cancelDomain()">
                            <i class="bi bi-x-circle"></i> İptal Talebi
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Müşteri-Müşteri Domain Transferi -->
        <div class="cdg-pdm-card" style="margin-top:18px;">
            <div class="cdg-pdm-card-head">
                <h3><i class="bi bi-people"></i> Domain Sahipliğini Başka Müşteriye Transfer</h3>
            </div>
            <div class="cdg-pdm-card-body">
                <div class="cdg-pdm-alert cdg-pdm-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Domaininizi platformumuzdaki başka bir müşteriye transfer edebilirsiniz. Hedef kullanıcının e-posta adresi ile talep oluşturun. Onaylandıktan sonra domain sahipliği değişir.</div>
                </div>

                <div class="cdg-pdm-grid-2" style="gap:18px;">
                    <div>
                        <h4 style="font-size:13px;font-weight:800;margin:0 0 10px;text-transform:uppercase;color:#475569;letter-spacing:0.5px;">
                            <i class="bi bi-send"></i> Yeni Transfer Talebi
                        </h4>
                        <div class="cdg-pdm-field">
                            <label class="cdg-pdm-label">Hedef Müşteri E-Posta</label>
                            <input type="email" id="cdg-tsv-email" class="cdg-pdm-input" placeholder="hedef@example.com">
                        </div>
                        <div class="cdg-pdm-field">
                            <label class="cdg-pdm-label">Şifreniz</label>
                            <input type="password" id="cdg-tsv-password" class="cdg-pdm-input" placeholder="Hesap şifreniz">
                        </div>
                        <button type="button" class="cdg-pdm-btn cdg-pdm-btn-primary" onclick="cdgDomain.transferServiceCreate()">
                            <i class="bi bi-arrow-right-circle"></i> Transfer Talebi Oluştur
                        </button>
                    </div>
                    <div>
                        <h4 style="font-size:13px;font-weight:800;margin:0 0 10px;text-transform:uppercase;color:#475569;letter-spacing:0.5px;">
                            <i class="bi bi-list-check"></i> Bekleyen Transferler
                        </h4>
                        <?php if(isset($ctoc_s_t_list) && is_array($ctoc_s_t_list) && !empty($ctoc_s_t_list)): ?>
                        <div class="cdg-dm-table-wrap" style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                            <table class="cdg-dm-table" style="font-size:12px;">
                                <thead>
                                    <tr><th>E-Posta</th><th style="width:90px;">Tarih</th><th style="width:50px;"></th></tr>
                                </thead>
                                <tbody>
                                <?php foreach($ctoc_s_t_list as $tsv):
                                    $evt = (class_exists('Utility') && method_exists('Utility','jdecode'))
                                        ? Utility::jdecode($tsv['data'] ?? '', true)
                                        : (is_string($tsv['data'] ?? '') ? json_decode($tsv['data'], true) : ($tsv['data'] ?? []));
                                    $to_email = $evt['to_email'] ?? '';
                                    $cdate = $tsv['cdate'] ?? '';
                                    $cdate_fmt = $cdate;
                                    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
                                        try { $cdate_fmt = DateManager::format(Config::get("options/date-format") . " H:i", $cdate); } catch(\Throwable $e) {}
                                    }
                                ?>
                                <tr id="cdg-tsv-row-<?php echo (int)($tsv['id'] ?? 0); ?>">
                                    <td><?php echo htmlspecialchars($to_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                                    <td style="font-size:11px;color:#64748b;"><?php echo htmlspecialchars($cdate_fmt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                                    <td>
                                        <button type="button" class="cdg-dm-row-btn cdg-dm-row-btn-danger" onclick="cdgDomain.transferServiceCancel(<?php echo (int)($tsv['id'] ?? 0); ?>)" title="Talebi iptal et">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div style="text-align:center;padding:20px;color:#94a3b8;border:1px dashed #e2e8f0;border-radius:10px;">
                            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:6px;"></i>
                            <span style="font-size:12px;">Bekleyen transfer talebi yok</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
// Tab switching
(function(){
    document.querySelectorAll('.cdg-pdm-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            var pane = this.getAttribute('data-pane');
            document.querySelectorAll('.cdg-pdm-tab').forEach(function(t){ t.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.cdg-pdm-pane').forEach(function(p){ p.classList.remove('active'); });
            var target = document.getElementById('cdg-pdm-pane-' + pane);
            if(target) target.classList.add('active');
            // Hash güncelle
            try { history.replaceState(null, '', '#' + pane); } catch(e) {}
        });
    });

    // İlk yüklemede hash kontrol
    if(location.hash) {
        var hash = location.hash.substring(1);
        var tab = document.querySelector('.cdg-pdm-tab[data-pane="' + hash + '"]');
        if(tab) tab.click();
    }
})();

// Domain operasyonları
window.cdgDomain = {
    domainId: <?php echo (int)$d_id; ?>,
    controllerUrl: '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',
    csrfRenewal: '<?php echo htmlspecialchars(cdg_csrf('domain_renewal'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',

    renew: function(){
        if(!confirm('Domaininizi yenilemek için fatura oluşturulacak. Devam edilsin mi?')) return;
        if(typeof MioAjax === 'function') {
            MioAjax({
                url: this.controllerUrl,
                type: 'post',
                data: { operation: 'domain_renewal', id: this.domainId },
                result: function(r){
                    if(r && r.status === 'successful') {
                        if(typeof alert_success === 'function') alert_success(r.message || 'Yenileme talebi oluşturuldu', {timer: 2000});
                        setTimeout(function(){ if(r.redirect) location.href = r.redirect; else location.reload(); }, 1500);
                    } else if(r && r.message) {
                        if(typeof alert_error === 'function') alert_error(r.message, {timer: 3000});
                    }
                }
            });
        } else {
            alert('AJAX motoru yüklenemedi - sayfayı yenileyin');
        }
    },

    renewWithPeriod: function(){
        var sel = document.getElementById('cdg-pdm-renewal-period');
        var period = sel ? sel.value : '';
        if(!period) { alert('Lutfen yenileme donemi secin'); return; }
        if(!confirm(period + ' yıl yenileme için fatura oluşturulacak. Devam edilsin mi?')) return;
        if(typeof MioAjax !== 'function') { alert('AJAX motoru yüklenemedi'); return; }
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'domain_renewal', id: this.domainId, period: period },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yenileme faturası oluşturuldu', {timer: 2000});
                    setTimeout(function(){ if(r.redirect) location.href = r.redirect; else location.reload(); }, 1500);
                } else if(r && r.message) {
                    if(typeof alert_error === 'function') alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    toggleAutoPay: function(el){
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'set_auto_pay_status', id: this.domainId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Güncellendi', {timer: 1500});
                    setTimeout(function(){ location.reload(); }, 1200);
                } else if(r && r.message) {
                    if(typeof alert_error === 'function') alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    toggleTransferLock: function(el){
        // Transfer kilidi domain modülüne özgü bir özelliktir; WiseCP core'da generic operation yoktur.
        // Mevcut durumu gösterir, değişiklik için kullanıcıyı destek talebine yönlendirir.
        if(!confirm('Transfer kilidi durumunu değiştirmek için destek ekibimize bilet açacağız. Devam edilsin mi?')) return;
        var ticketsUrl = '<?php echo htmlspecialchars(cdg_link("create-ticket-request"), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
        var domainName = '<?php echo htmlspecialchars($d_name, ENT_QUOTES); ?>';
        var subjectParam = encodeURIComponent('Transfer kilidi değişiklik talebi: ' + domainName);
        if(ticketsUrl && ticketsUrl !== '#') {
            location.href = ticketsUrl + (ticketsUrl.indexOf('?') >= 0 ? '&' : '?') + 'subject=' + subjectParam;
        } else if(typeof alert_info === 'function') {
            alert_info('Lütfen destek ekibimizle iletişime geçin.', {timer: 3000});
        }
    },

    toggleWhoisPrivacy: function(el){
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'domain_whois_privacy', id: this.domainId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Güncellendi', {timer: 1500});
                    setTimeout(function(){ location.reload(); }, 1200);
                } else if(r && r.message) {
                    if(typeof alert_error === 'function') alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    setForward: function(){
        var protocol = document.getElementById('forward_protocol').value;
        var dom = document.getElementById('forward_domain').value;
        if(!dom) {
            if(typeof alert_error === 'function') alert_error('Hedef adres boş olamaz', {timer: 3000});
            return;
        }
        var method = document.querySelector('input[name=method]:checked');
        method = method ? method.value : '301';

        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            // Classic format: protocol, domain, method (NO 'id'!)
            data: {
                operation: 'set_forward_domain',
                protocol: protocol,
                domain: dom,
                method: method
            },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme aktif', {timer: 1500});
                    setTimeout(function(){ location.reload(); }, 1200);
                } else if(r && r.message) {
                    if(typeof alert_error === 'function') alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    cancelDomain: function(){
        if(!confirm('Domaininizin iptal talebini oluşturmak istediğinize emin misiniz? Bu işlem geri alınamaz.')) return;
        if(typeof MioAjax !== 'function') {
            if(typeof alert_error === 'function') alert_error('AJAX motoru yüklenemedi', {timer: 3000});
            return;
        }
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'canceled_product', id: this.domainId, type: 'end-of-period' },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'İptal talebiniz alındı', {timer: 2500});
                    setTimeout(function(){ location.reload(); }, 2000);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    requestEpp: function(){
        // EPP/Auth code WiseCP core'da generic bir operation değil — modüle özgü
        // En iyi yaklaşım: Destek talebi oluşturmaya yönlendir
        if(!confirm('EPP / Auth Code talebi için destek ekibimize bir bilet açacağız. Devam edilsin mi?')) return;
        var ticketsUrl = '<?php echo htmlspecialchars(cdg_link("create-ticket-request"), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
        var domainName = '<?php echo htmlspecialchars($d_name, ENT_QUOTES); ?>';
        var subjectParam = encodeURIComponent('EPP / Auth Code talebi: ' + domainName);
        // Ticket sayfasına yönlendir, başlığı önceden doldur
        if(ticketsUrl && ticketsUrl !== '#') {
            location.href = ticketsUrl + (ticketsUrl.indexOf('?') >= 0 ? '&' : '?') + 'subject=' + subjectParam;
        } else {
            alert('Lütfen destek ekibimizle iletişime geçin.');
        }
    },

    // === MODAL AÇMA / KAPAMA ===
    openModal: function(id) {
        var m = document.getElementById(id);
        if(!m) return;
        m.classList.add('cdg-dm-open');
        document.body.style.overflow = 'hidden';
    },
    closeModal: function(id) {
        var m = document.getElementById(id);
        if(!m) return;
        m.classList.remove('cdg-dm-open');
        document.body.style.overflow = '';
    },

    // === DNS RECORDS ===
    openDnsRecords: function(){
        this.openModal('cdg-dns-records-modal');
        // PHP-side preload kullaniliyor - reload gereksiz
    },
    dnsRecordsReload: function(){
        // Sayfa reload (DNS records PHP-side rendered)
        setTimeout(function(){ window.location.reload(); }, 1500);
    },
    dnsRecordAdd: function(){
        var type = document.getElementById('DnsRecord_type').value;
        var name = document.getElementById('DnsRecord_name').value.trim();
        var value = document.getElementById('DnsRecord_value').value.trim();
        var ttl = document.getElementById('DnsRecord_ttl').value;
        var priorityEl = document.getElementById('DnsRecord_priority');
        var priority = (type === 'MX' && priorityEl) ? priorityEl.value : '';

        if(!name || !value) {
            if(typeof alert_error === 'function') alert_error('Ad ve değer alanları zorunludur', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;

        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            // Classic format: type, name, value, ttl, priority (NO 'id'!)
            data: { operation: 'add_dns_record', type: type, name: name, value: value, ttl: ttl, priority: priority },
            result: function(r){
                if(r && r.status === 'successful') {
                    document.getElementById('DnsRecord_name').value = '';
                    document.getElementById('DnsRecord_value').value = '';
                    if(typeof alert_success === 'function') alert_success(r.message || 'Kayıt eklendi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    dnsRecordDelete: function(k, identity, type, name) {
        if(!confirm('Bu DNS kaydını silmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function') return;
        var row = document.getElementById('DnsRecord_' + k);
        var value = '';
        if(row) {
            var valCell = row.querySelector('td:nth-child(3)');
            if(valCell) value = valCell.textContent.trim();
        }
        // Classic format: type, name, value, identity (NO 'id', NO 'k')
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'delete_dns_record', type: type, name: name, value: value, identity: identity },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Kayıt silindi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    // Inline edit: form alanlarini mevcut kayit ile doldur, kullanici update'e bassin
    dnsRecordEdit: function(k, data) {
        if(!data) return;
        // Mevcut kayit bilgilerini Add formuna doldur
        var typeSel = document.getElementById('DnsRecord_type');
        var nameInp = document.getElementById('DnsRecord_name');
        var valInp = document.getElementById('DnsRecord_value');
        var ttlSel = document.getElementById('DnsRecord_ttl');
        if(typeSel && data.type) typeSel.value = data.type;
        if(nameInp && data.name) nameInp.value = data.name;
        if(valInp && data.value) valInp.value = data.value;
        if(ttlSel && data.ttl) ttlSel.value = data.ttl;

        // Editing mode - identity'yi sakla
        window.cdgDomainEditingDnsIdentity = data.identity || '';
        window.cdgDomainEditingDnsKey = k;

        // Add butonunu Update'e cevir
        var addBtn = document.querySelector('button[onclick="cdgDomain.dnsRecordAdd()"]');
        if(addBtn) {
            addBtn.innerHTML = '<i class="bi bi-check-lg"></i> Güncelle';
            addBtn.setAttribute('onclick', 'cdgDomain.dnsRecordUpdate()');
            addBtn.style.background = '#00D3E5';
        }

        // Add form'a scroll
        if(addBtn) addBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Bilgi mesaji
        if(typeof alert_info === 'function') alert_info('Kayıt düzenleme moduna girildi. Form alanlarını güncelleyip Güncelle butonuna basın.', {timer: 3000});
    },
    // Update kaydi gonder
    dnsRecordUpdate: function() {
        var type = document.getElementById('DnsRecord_type').value;
        var name = document.getElementById('DnsRecord_name').value.trim();
        var value = document.getElementById('DnsRecord_value').value.trim();
        var ttl = document.getElementById('DnsRecord_ttl').value;
        var priorityEl = document.getElementById('DnsRecord_priority');
        var priority = (type === 'MX' && priorityEl) ? priorityEl.value : '';
        var identity = window.cdgDomainEditingDnsIdentity || '';

        if(!name || !value || !identity) {
            if(typeof alert_error === 'function') alert_error('Form bilgileri eksik veya kayit ID bulunamadi', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;
        // Classic format: type, name, value, identity, ttl, priority (NO 'id'!)
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'update_dns_record', type: type, name: name, value: value, identity: identity, ttl: ttl, priority: priority },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Kayıt güncellendi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    dnsRecordCancelEdit: function(k){
        // Edit modunu iptal et
        window.cdgDomainEditingDnsIdentity = '';
        window.cdgDomainEditingDnsKey = '';
        // Form alanlarini temizle
        var nameInp = document.getElementById('DnsRecord_name');
        var valInp = document.getElementById('DnsRecord_value');
        if(nameInp) nameInp.value = '';
        if(valInp) valInp.value = '';
        // Buton'u eski haline getir
        var addBtn = document.querySelector('button[onclick="cdgDomain.dnsRecordUpdate()"]');
        if(addBtn) {
            addBtn.innerHTML = '<i class="bi bi-plus-lg"></i> Ekle';
            addBtn.setAttribute('onclick', 'cdgDomain.dnsRecordAdd()');
            addBtn.style.background = '';
        }
    },

    // === CNS (Child Nameserver) ===
    openCNS: function(){
        this.openModal('cdg-cns-modal');
    },
    // CNS list reload artik gerekli degil - PHP-side preload kullanildi
    // Add/Modify/Delete sonrasi sayfa reload ediliyor
    cnsReload: function(){
        // Sayfa reload (CNS PHP-side rendered)
        setTimeout(function(){ window.location.reload(); }, 1500);
    },
    cnsAdd: function(){
        var ns = document.getElementById('CNS_ns').value.trim();
        var ip = document.getElementById('CNS_ip').value.trim();
        if(!ns || !ip) {
            if(typeof alert_error === 'function') alert_error('Hostname ve IP alanları zorunludur', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'domain_add_cns', id: this.domainId, ns: ns, ip: ip },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'CNS eklendi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === DNSSEC ===
    openDnsSec: function(){
        this.openModal('cdg-dnssec-modal');
        // PHP-side preload kullaniliyor
    },
    dnssecReload: function(){
        // Sayfa reload (DNSSEC PHP-side rendered)
        setTimeout(function(){ window.location.reload(); }, 1500);
    },
    dnssecAdd: function(){
        var digest = document.getElementById('DnsSecRecord_digest').value.trim();
        var keyTag = document.getElementById('DnsSecRecord_key_tag').value.trim();
        var digestType = document.getElementById('DnsSecRecord_digest_type').value;
        var algorithm = document.getElementById('DnsSecRecord_algorithm').value;

        if(!digest || !keyTag || !digestType || !algorithm) {
            if(typeof alert_error === 'function') alert_error('Tüm alanlar zorunludur', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl, type: 'post',
            // Classic format: digest, key_tag, digest_type, algorithm (NO 'id'!)
            data: {
                operation: 'add_dns_sec_record',
                digest: digest, key_tag: keyTag,
                digest_type: digestType, algorithm: algorithm
            },
            result: function(r){
                if(r && r.status === 'successful') {
                    document.getElementById('DnsSecRecord_digest').value = '';
                    document.getElementById('DnsSecRecord_key_tag').value = '';
                    document.getElementById('DnsSecRecord_digest_type').value = '';
                    document.getElementById('DnsSecRecord_algorithm').value = '';
                    if(typeof alert_success === 'function') alert_success(r.message || 'DNSSEC eklendi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === EMAIL FORWARDS ===
    openEmailForwards: function(){
        this.openModal('cdg-email-forwards-modal');
        // PHP-side preload kullaniliyor
    },
    emailForwardsReload: function(){
        // Sayfa reload (Email Forwards PHP-side rendered)
        setTimeout(function(){ window.location.reload(); }, 1500);
    },
    emailForwardAdd: function(){
        var prefix = document.getElementById('EmailForward_prefix').value.trim();
        var target = document.getElementById('EmailForward_target').value.trim();

        if(!prefix || !target) {
            if(typeof alert_error === 'function') alert_error('Prefix ve hedef e-posta zorunludur', {timer: 3000});
            return;
        }
        // Basit email validasyonu
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(target)) {
            if(typeof alert_error === 'function') alert_error('Geçerli bir e-posta adresi girin', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;

        MioAjax({
            url: this.controllerUrl, type: 'post',
            // Classic format: prefix, target (NO 'id'!)
            data: { operation: 'add_email_forward', prefix: prefix, target: target },
            result: function(r){
                if(r && r.status === 'successful') {
                    document.getElementById('EmailForward_prefix').value = '';
                    document.getElementById('EmailForward_target').value = '';
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme eklendi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    emailForwardEdit: function(k){
        var row = document.getElementById('EmailForward_' + k);
        if(!row) return;
        var editWrap = row.querySelector('.edit-wrap');
        var showWrap = row.querySelector('.show-wrap');
        var editContent = row.querySelector('.edit-content');
        var noEditContent = row.querySelector('.no-edit-content');
        var input = editWrap ? editWrap.querySelector('input') : null;
        var currentTarget = row.querySelector('input[name="target"]');
        if(input && currentTarget) input.value = currentTarget.value;
        if(editWrap) editWrap.style.display = 'block';
        if(showWrap) showWrap.style.display = 'none';
        if(editContent) editContent.style.display = 'inline-flex';
        if(noEditContent) noEditContent.style.display = 'none';
    },
    emailForwardCancelEdit: function(k){
        var row = document.getElementById('EmailForward_' + k);
        if(!row) return;
        var editWrap = row.querySelector('.edit-wrap');
        var showWrap = row.querySelector('.show-wrap');
        var editContent = row.querySelector('.edit-content');
        var noEditContent = row.querySelector('.no-edit-content');
        if(editWrap) editWrap.style.display = 'none';
        if(showWrap) showWrap.style.display = 'block';
        if(editContent) editContent.style.display = 'none';
        if(noEditContent) noEditContent.style.display = 'inline-flex';
    },
    emailForwardSave: function(k){
        var row = document.getElementById('EmailForward_' + k);
        if(!row || typeof MioAjax !== 'function') return;
        var prefix = row.querySelector('input[name="prefix"]').value;
        var oldTarget = row.querySelector('input[name="target"]').value;
        var identity = (row.querySelector('input[name="identity"]') || {}).value || '';
        var newTarget = (row.querySelector('.edit-wrap input') || {}).value;
        if(!newTarget) {
            if(typeof alert_error === 'function') alert_error('Hedef e-posta boş olamaz', {timer: 3000});
            return;
        }
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newTarget)) {
            if(typeof alert_error === 'function') alert_error('Geçerli bir e-posta adresi girin', {timer: 3000});
            return;
        }
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: {
                operation: 'update_email_forward', id: this.domainId,
                identity: identity, prefix: prefix, target: oldTarget, target_new: newTarget
            },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme güncellendi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    // Classic format: identity, prefix, target (NO 'id'!)
    // Yeni imza: emailForwardDelete(identity)
    emailForwardDelete: function(identity){
        if(!confirm('Bu yönlendirmeyi silmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function' || !identity) {
            if(typeof alert_error === 'function') alert_error('Yonlendirme kimliği bulunamadı', {timer: 3000});
            return;
        }
        var row = document.getElementById('EmailForward_' + identity);
        var data = { operation: 'delete_email_forward', identity: identity };
        if(row) {
            // data attribute'ten prefix ve target oku
            var prefix = row.getAttribute('data-prefix') || '';
            var target = row.getAttribute('data-target') || '';
            if(prefix) data.prefix = prefix;
            if(target) data.target = target;
        }
        MioAjax({
            url: this.controllerUrl, type: 'post', data: data,
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme silindi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === DOMAIN DOCUMENTS ===
    openDocuments: function(){ this.openModal('cdg-documents-modal'); },
    docTypeChange: function(sel){
        var opt = sel.options[sel.selectedIndex];
        var docId = sel.value;
        var type = opt ? opt.getAttribute('data-type') : '';

        // Hepsini gizle
        document.querySelectorAll('.cdg-doc-input').forEach(function(el){
            el.style.display = 'none';
        });
        // Select'leri disable et (form'a gönderilmesin)
        document.querySelectorAll('.cdg-doc-select-wrap select').forEach(function(s){
            s.disabled = true;
        });

        if(docId === '0' || !type) return;

        if(type === 'text') {
            var t = document.getElementById('cdg-doc-text');
            if(t) t.style.display = 'block';
        } else if(type === 'file') {
            var f = document.getElementById('cdg-doc-file');
            if(f) f.style.display = 'block';
        } else if(type === 'select') {
            var sw = document.getElementById('cdg-doc-select-' + docId);
            if(sw) {
                sw.style.display = 'block';
                var ss = sw.querySelector('select');
                if(ss) ss.disabled = false;
            }
        }
    },
    docAdd: function(el){
        var form = document.getElementById('addDomainDoc');
        if(!form || typeof MioAjaxElement !== 'function') {
            // jQuery fallback
            if(window.jQuery && form) {
                jQuery(form).ajaxSubmit({
                    success: function(result){
                        var solve;
                        try { solve = (typeof result === 'string') ? JSON.parse(result) : result; } catch(e) { solve = null; }
                        if(solve && solve.status === 'successful') {
                            if(typeof alert_success === 'function') alert_success(solve.message || 'Belge eklendi', {timer: 2000});
                            setTimeout(function(){ window.location.reload(); }, 1500);
                        } else if(solve && solve.message && typeof alert_error === 'function') {
                            alert_error(solve.message, {timer: 3000});
                        }
                    }
                });
            }
            return;
        }
        // WiseCP MioAjaxElement file upload destekli
        MioAjaxElement(el, {
            result: 'cdgDocAddHandle',
            waiting_text: 'Yükleniyor...',
            progress_text: 'Yükleniyor...'
        });
    },
    docSend: function(el){
        if(!confirm('Tüm belgelerinizi kayıt firmasına göndermek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'sent_domain_doc', id: this.domainId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Belgeler gönderildi', {timer: 2500});
                    setTimeout(function(){ window.location.reload(); }, 2000);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === TRANSFER SERVICE (Müşteri-Müşteri Domain Transferi) ===
    transferServiceCreate: function(){
        var emailEl = document.getElementById('cdg-tsv-email');
        var pwEl = document.getElementById('cdg-tsv-password');
        if(!emailEl || !pwEl) return;
        var email = emailEl.value.trim();
        var pw = pwEl.value;

        if(!email || !pw) {
            if(typeof alert_error === 'function') alert_error('E-posta ve şifre zorunludur', {timer: 3000});
            return;
        }
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            if(typeof alert_error === 'function') alert_error('Geçerli bir e-posta girin', {timer: 3000});
            return;
        }
        if(!confirm('Domain sahipliğini ' + email + ' adresine transfer etmek istediğinize emin misiniz? Onaylanırsa domain üzerindeki tüm haklarınız kaybolur.')) return;
        if(typeof MioAjax !== 'function') return;

        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'transfer_service', id: this.domainId, email: email, password: pw },
            result: function(r){
                if(r && r.status === 'successful') {
                    pwEl.value = '';
                    if(typeof alert_success === 'function') alert_success(r.message || 'Transfer talebi oluşturuldu', {timer: 2500});
                    setTimeout(function(){ window.location.reload(); }, 2000);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    transferServiceCancel: function(tsvId){
        if(!confirm('Bu transfer talebini iptal etmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'remove_transfer_service', id: this.domainId, tsv_id: tsvId },
            result: function(r){
                if(r && r.status === 'successful') {
                    var row = document.getElementById('cdg-tsv-row-' + tsvId);
                    if(row) row.remove();
                    if(typeof alert_success === 'function') alert_success(r.message || 'Transfer talebi iptal edildi', {timer: 1500});
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === DNSSEC Delete ===
    // Classic format: identity, digest, key_tag, digest_type, algorithm (NO 'id'!)
    // Yeni imza: dnssecDelete(identity) - PHP-side render edilen tablodan cagrilir
    dnssecDelete: function(identity){
        if(!confirm('Bu DNSSEC kaydını silmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function' || !identity) {
            if(typeof alert_error === 'function') alert_error('DNSSEC kaydı kimliği bulunamadı', {timer: 3000});
            return;
        }
        // Identity yeterli olmasi gereken minimum bilgi - WiseCP backend digerlerini kendi tablosundan cekebilir
        // Eger backend digger field'lari da istiyorsa, PHP-side render'da onlari da data attribute olarak ekleyebilirim
        var row = document.getElementById('DnsSecRecord_' + identity);
        var data = { operation: 'delete_dns_sec_record', identity: identity };
        if(row) {
            // Eger row icinde data attribute varsa, onlari da gonder
            ['digest', 'key_tag', 'digest_type', 'algorithm'].forEach(function(f) {
                var v = row.getAttribute('data-' + f.replace('_', '-'));
                if(v) data[f] = v;
            });
        }
        MioAjax({
            url: this.controllerUrl, type: 'post', data: data,
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'DNSSEC silindi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === CNS Modify / Delete ===
    // ÖNEMLI: Classic'te field 'id' = CNS_ID (NOT domain_id)
    // Bizim kodda da Classic'le uyumlu hale getirildi
    cnsModify: function(cnsId){
        var nsInput = document.getElementById('cns-ns-' + cnsId);
        var ipInput = document.getElementById('cns-ip-' + cnsId);
        if(!nsInput || !ipInput) {
            if(typeof alert_error === 'function') alert_error('CNS satırı bulunamadı', {timer: 2500});
            return;
        }
        var newNs = nsInput.value.trim();
        var newIp = ipInput.value.trim();
        if(!newNs || !newIp) {
            if(typeof alert_error === 'function') alert_error('Hostname ve IP alanları zorunludur', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;
        // Classic format: id=CNS_ID, ns, ip (NO domain_id, NO cns_id)
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'domain_modify_cns', id: cnsId, ns: newNs, ip: newIp },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'CNS güncellendi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },
    cnsDelete: function(cnsId){
        if(!confirm('Bu Child Nameserver kaydını silmek istediğinize emin misiniz? Bu islem geri alinamaz.')) return;
        if(typeof MioAjax !== 'function') return;
        // Classic format: id=CNS_ID (NO domain_id, NO cns_id)
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'domain_delete_cns', id: cnsId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'CNS silindi - sayfa yenileniyor', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === Domain Forward Cancel ===
    cancelForward: function(){
        if(!confirm('Domain yönlendirmesini iptal etmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl, type: 'post',
            // Classic format: sadece operation (NO 'id'!)
            data: { operation: 'cancel_forward_domain' },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme iptal edildi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1200);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    // === Cancel Undo (iptal talebini geri al) ===
    removeCancelled: function(){
        if(!confirm('İptal talebini geri almak istediğinize emin misiniz? Domain yenilemeye devam eder.')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl, type: 'post',
            data: { operation: 'remove_cancelled_product', id: this.domainId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'İptal talebi geri alındı', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    }
};

// Global doc add handler (MioAjaxElement callback için)
function cdgDocAddHandle(result) {
    if(!result) return;
    var solve;
    try { solve = (typeof result === 'string') ? JSON.parse(result) : result; } catch(e) { solve = null; }
    if(solve && solve.status === 'successful') {
        if(typeof alert_success === 'function') alert_success(solve.message || 'Belge eklendi', {timer: 2000});
        setTimeout(function(){ window.location.reload(); }, 1500);
    } else if(solve && solve.message && typeof alert_error === 'function') {
        alert_error(solve.message, {timer: 3000});
    }
}

// === Modal: ESC ile kapatma + Outside click ===
(function(){
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') {
            ['cdg-dns-records-modal','cdg-cns-modal','cdg-dnssec-modal','cdg-email-forwards-modal','cdg-documents-modal'].forEach(function(id){
                var m = document.getElementById(id);
                if(m && m.classList.contains('cdg-dm-open')) cdgDomain.closeModal(id);
            });
        }
    });
    document.querySelectorAll('.cdg-dm-overlay').forEach(function(ov){
        ov.addEventListener('click', function(e){
            if(e.target === this) cdgDomain.closeModal(this.id);
        });
    });

    // DNS Record type değişimine göre placeholder + priority göster/gizle
    var typeSel = document.getElementById('DnsRecord_type');
    if(typeSel) {
        typeSel.addEventListener('change', function(){
            var t = this.value;
            var pri = document.getElementById('DnsRecord_priority_wrap');
            var val = document.getElementById('DnsRecord_value');
            if(pri) pri.style.display = (t === 'MX') ? '' : 'none';
            if(val) {
                if(t === 'A') val.placeholder = 'IPv4 adresi (ör: 192.168.1.1)';
                else if(t === 'AAAA') val.placeholder = 'IPv6 adresi';
                else if(t === 'CNAME' || t === 'MX' || t === 'NS') val.placeholder = 'Hedef hostname';
                else if(t === 'TXT') val.placeholder = 'Metin değeri (SPF, DKIM vb.)';
                else if(t === 'SRV') val.placeholder = 'Hedef sunucu';
                else val.placeholder = '';
            }
        });
    }
})();
// === WHOIS Contact Type Tab Switching ===
window.cdgPdmCtTab = function(btn, ctKey) {
    document.querySelectorAll('.cdg-pdm-ct-tab').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.cdg-pdm-ct-pane').forEach(function(p){ p.style.display = 'none'; });
    btn.classList.add('active');
    var pane = document.getElementById('cdg-pdm-ct-pane-' + ctKey);
    if(pane) pane.style.display = 'block';
};

// === WHOIS Profile -> Form Fill ===
window.cdgPdmWhoisFillFromProfile = function(sel, ctKey) {
    var opt = sel.options[sel.selectedIndex];
    if(!opt || sel.value === '0') return;
    var info = opt.getAttribute('data-information');
    if(!info) return;
    try {
        var data = JSON.parse(info);
        var fields = ['Name', 'Company', 'EMail', 'Phone', 'PhoneCountryCode', 'Address', 'AddressLine1', 'City', 'State', 'Country', 'ZipCode'];
        fields.forEach(function(f){
            var el = document.querySelector('.cdg-pdm-whois-' + ctKey + '-' + f);
            if(el && data[f] !== undefined) el.value = data[f];
        });
    } catch(e) { console.error('Profil parse hatasi:', e); }
};

</script>

<style>
.cdg-pdm-ct-tab {
    flex: 1;
    min-width: 110px;
    padding: 9px 14px;
    background: transparent;
    border: 0;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.cdg-pdm-ct-tab:hover { background: #fff; color: #0f172a; }
.cdg-pdm-ct-tab.active { background: #fff; color: #2E3B4E; box-shadow: 0 1px 3px rgba(15,23,42,0.06); }
</style>

<?php
// === Domain Modalları (DNS Records / CNS / DNSSEC / Email Forwards / Documents) ===
$cdg_domain_modals_loaded = ['css' => false];
// === Domain Modallari CSS (her zaman yuklenir, tum modullerin ozel ihtiyaci) ===
include __DIR__ . DS . 'inc' . DS . 'ac-domain-modals-css.php';

if($allow_dns_records) include __DIR__ . DS . 'inc' . DS . 'ac-domain-dns-records.php';
if($allow_dns_cns)     include __DIR__ . DS . 'inc' . DS . 'ac-domain-cns.php';
if($allow_dns_sec_records) include __DIR__ . DS . 'inc' . DS . 'ac-domain-dnssec.php';
if($allow_forwarding_eml) include __DIR__ . DS . 'inc' . DS . 'ac-domain-email-forwards.php';
if($allow_documents) include __DIR__ . DS . 'inc' . DS . 'ac-domain-documents.php';
?>
