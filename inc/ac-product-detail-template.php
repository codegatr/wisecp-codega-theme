<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Ortak Ürün Yönetim Template
 * Hosting / Server / SMS / Software / Special için kullanılır
 *
 * Caller dosyalar şu variable'ları set eder:
 *   $cdg_pd_kind     : 'hosting'|'server'|'sms'|'software'|'special'
 *   $cdg_pd_title    : 'Hosting Yönetimi'
 *   $cdg_pd_icon     : 'hdd-network-fill'
 *   $cdg_pd_color    : '#10b981'
 *   $cdg_pd_back_slug: 'products-hosting'
 *
 * WiseCP runtime: $product, $proanse, $options, $module_con, $invoice, $links
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$wide_content = true;
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
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
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$product   = isset($product) && is_array($product) ? $product : [];
$proanse   = isset($proanse) && is_array($proanse) ? $proanse : $product;
$options   = isset($options) && is_array($options) ? $options : (isset($proanse['options']) && is_array($proanse['options']) ? $proanse['options'] : []);
$invoice   = isset($invoice) && is_array($invoice) ? $invoice : null;
$links     = isset($links) && is_array($links) ? $links : [];
$addons    = isset($addons) && is_array($addons) ? $addons : [];
$upgrades  = isset($upgrades) && is_array($upgrades) ? $upgrades : [];

$cdg_pd_kind      = $cdg_pd_kind ?? 'hosting';
$cdg_pd_title     = $cdg_pd_title ?? 'Hizmet Yönetimi';
$cdg_pd_icon      = $cdg_pd_icon ?? 'hdd-network-fill';
$cdg_pd_color     = $cdg_pd_color ?? '#10b981';
$cdg_pd_back_slug = $cdg_pd_back_slug ?? 'products-hosting';

$d_id      = $proanse['id'] ?? 0;
$d_name    = $proanse['name'] ?? 'Hizmet';
$d_status  = strtolower($proanse['status'] ?? 'unknown');
$d_duedate = $proanse['duedate'] ?? '';
$d_cdate   = $proanse['cdate'] ?? '';
$d_period  = $proanse['period'] ?? '';
$d_ptime   = $proanse['period_time'] ?? '';
$d_amount  = $proanse['amount'] ?? 0;
$d_amount_cid = $proanse['amount_cid'] ?? 0;
$d_autopay = !empty($proanse['auto_pay']);

// Kind-specific extra
$d_domain  = $options['domain'] ?? '';
$d_hostname = $options['hostname'] ?? '';
$d_ip      = $options['ip'] ?? '';
$d_username = $options['username'] ?? '';

$controller_url = $links['controller'] ?? '';
$back_url = cdg_link($cdg_pd_back_slug);

function cdg_pd_status_meta($status) {
    $map = [
        'active'    => ['cls' => 'cdg-pd2-badge-success', 'lbl' => 'Aktif',         'icon' => 'check-circle-fill'],
        'inprocess' => ['cls' => 'cdg-pd2-badge-warning', 'lbl' => 'İşlemde',       'icon' => 'gear-fill'],
        'waiting'   => ['cls' => 'cdg-pd2-badge-info',    'lbl' => 'Onay Bekliyor', 'icon' => 'hourglass-split'],
        'suspended' => ['cls' => 'cdg-pd2-badge-warning', 'lbl' => 'Askıda',        'icon' => 'pause-circle-fill'],
        'cancelled' => ['cls' => 'cdg-pd2-badge-danger',  'lbl' => 'İptal',         'icon' => 'x-circle-fill'],
        'expired'   => ['cls' => 'cdg-pd2-badge-danger',  'lbl' => 'Süresi Doldu',  'icon' => 'calendar-x-fill'],
    ];
    return $map[$status] ?? ['cls' => 'cdg-pd2-badge-info', 'lbl' => ucfirst($status), 'icon' => 'question-circle'];
}
$st_meta = cdg_pd_status_meta($d_status);

function cdg_pd_date($d) {
    if(!$d) return '-';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        return DateManager::format(Config::get("options/date-format") ?: 'd.m.Y', $d);
    }
    if(strpos((string)$d, '0000') === 0) return '-';
    return date('d.m.Y', strtotime((string)$d));
}

function cdg_pd_money($amount, $cid = 0) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol') && $cid) {
        return Money::formatter_symbol($amount, $cid);
    }
    return number_format((float)$amount, 2, ',', '.');
}

function cdg_pd_csrf($action) {
    if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) {
        return Validation::get_csrf_token($action);
    }
    return '';
}

// Kind-specific extra fields - shows on summary card
$extra_options = [];
foreach($options as $opt_k => $opt_v) {
    if(in_array($opt_k, ['domain','hostname','ip','username','password','autopay','dns_manage','transferlock','whois_privacy','nameservers','ns','block_access'])) continue;
    if(is_array($opt_v) || is_object($opt_v)) continue;
    if(is_string($opt_v) && strlen($opt_v) > 0 && strlen($opt_v) < 200) {
        $extra_options[$opt_k] = $opt_v;
    }
}
?>

<style>
.cdg-pd2 {
    --p-primary: #1e40af;
    --p-success: #10b981;
    --p-warning: #f59e0b;
    --p-danger: #ef4444;
    --p-info: #06b6d4;
    --p-bg: #f8fafc;
    --p-card: #fff;
    --p-text: #0f172a;
    --p-muted: #64748b;
    --p-border: #e2e8f0;
    --p-radius: 14px;
    --p-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --p-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--p-text);
    background: var(--p-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-pd2 *, .cdg-pd2 *::before, .cdg-pd2 *::after { box-sizing: border-box; }
.cdg-pd2 a { text-decoration: none; color: inherit; }
.cdg-pd2-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-pd2-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--p-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--p-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-pd2-back:hover { border-color: var(--p-primary); color: var(--p-primary); }

.cdg-pd2-hero {
    background: linear-gradient(135deg, <?php echo $cdg_pd_color; ?> 0%, #3b82f6 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
}
.cdg-pd2-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-pd2-hero-row {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 20px;
    align-items: center;
    position: relative; z-index: 1;
}
.cdg-pd2-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
}
.cdg-pd2-hero-text h1 {
    font-size: 28px; font-weight: 800;
    margin: 0 0 6px;
    letter-spacing: -0.5px;
    word-break: break-word;
}
.cdg-pd2-hero-eyebrow {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 4px;
}
.cdg-pd2-hero-meta {
    display: flex; gap: 14px; flex-wrap: wrap;
    font-size: 13px;
    opacity: 0.92;
}
.cdg-pd2-hero-meta span { display: inline-flex; align-items: center; gap: 6px; }
.cdg-pd2-hero-status {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    background: rgba(255,255,255,0.22);
    backdrop-filter: blur(10px);
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.cdg-pd2-tabs {
    background: #fff;
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    padding: 8px;
    box-shadow: var(--p-shadow);
    margin-bottom: 18px;
    display: flex;
    gap: 4px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.cdg-pd2-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--p-muted);
    cursor: pointer;
    background: transparent;
    border: 0;
    font-family: inherit;
    white-space: nowrap;
    transition: all 0.18s;
}
.cdg-pd2-tab:hover { background: var(--p-bg); color: var(--p-text); }
.cdg-pd2-tab.active {
    background: var(--p-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(30,64,175,0.22);
}

.cdg-pd2-pane { display: none; }
.cdg-pd2-pane.active { display: block; animation: cdg-pd2-fade 0.25s ease; }
@keyframes cdg-pd2-fade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

.cdg-pd2-card {
    background: #fff;
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    box-shadow: var(--p-shadow);
    margin-bottom: 18px;
    overflow: hidden;
}
.cdg-pd2-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--p-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-pd2-card-head h3 {
    font-size: 14px; font-weight: 800; margin: 0;
    color: var(--p-text);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-pd2-card-head h3 i { color: var(--p-primary); font-size: 16px; }
.cdg-pd2-card-body { padding: 22px; }

.cdg-pd2-info { list-style: none; padding: 0; margin: 0; }
.cdg-pd2-info li {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px dashed var(--p-border);
    font-size: 13px;
    gap: 12px;
}
.cdg-pd2-info li:last-child { border-bottom: 0; padding-bottom: 0; }
.cdg-pd2-info li:first-child { padding-top: 0; }
.cdg-pd2-info-label { color: var(--p-muted); font-weight: 600; flex-shrink: 0; }
.cdg-pd2-info-value { color: var(--p-text); font-weight: 700; text-align: right; word-break: break-word; }

.cdg-pd2-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-pd2-badge-success { background: #d1fae5; color: #065f46; }
.cdg-pd2-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-pd2-badge-danger  { background: #fee2e2; color: #991b1b; }
.cdg-pd2-badge-info    { background: #dbeafe; color: #1e40af; }

.cdg-pd2-alert {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 13px;
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 14px;
    line-height: 1.5;
}
.cdg-pd2-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.cdg-pd2-alert-info { background: #dbeafe; color: #1e3a8a; border: 1px solid #93c5fd; }
.cdg-pd2-alert-info i { color: #1e40af; }
.cdg-pd2-alert-warning { background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }
.cdg-pd2-alert-warning i { color: #f59e0b; }

.cdg-pd2-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.cdg-pd2-btn-primary {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    box-shadow: 0 6px 18px rgba(30,64,175,0.22);
}
.cdg-pd2-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-pd2-btn-success {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.22);
}
.cdg-pd2-btn-success:hover { transform: translateY(-1px); color: #fff; }
.cdg-pd2-btn-danger {
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: #fff;
    box-shadow: 0 6px 18px rgba(239,68,68,0.22);
}
.cdg-pd2-btn-danger:hover { transform: translateY(-1px); color: #fff; }
.cdg-pd2-btn-outline {
    background: #fff;
    color: var(--p-text);
    border: 1px solid var(--p-border);
}
.cdg-pd2-btn-outline:hover { border-color: var(--p-primary); color: var(--p-primary); }

.cdg-pd2-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

.cdg-pd2-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--p-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--p-text);
    background: #fff;
    outline: none;
    transition: all 0.18s;
    font-family: inherit;
}
.cdg-pd2-input:focus {
    border-color: var(--p-primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,0.10);
}
.cdg-pd2-field { margin-bottom: 14px; }
.cdg-pd2-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--p-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 7px;
}

.cdg-pd2-addon-card {
    background: #f8fafc;
    border: 1px solid var(--p-border);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 8px;
    display: flex; justify-content: space-between; align-items: center;
    gap: 12px;
}
.cdg-pd2-addon-name { font-weight: 700; font-size: 13px; }
.cdg-pd2-addon-price { color: var(--p-primary); font-weight: 800; font-size: 14px; }

@media (max-width: 768px) {
    .cdg-pd2-hero-row { grid-template-columns: 1fr; text-align: center; }
    .cdg-pd2-hero-status { justify-self: center; }
    .cdg-pd2-grid-2 { grid-template-columns: 1fr; }
    .cdg-pd2-hero-text h1 { font-size: 22px; }
}
</style>

<div class="cdg-pd2">
<div class="cdg-pd2-wrap">

    <a href="<?php echo htmlspecialchars($back_url); ?>" class="cdg-pd2-back">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <section class="cdg-pd2-hero">
        <div class="cdg-pd2-hero-row">
            <div class="cdg-pd2-hero-icon"><i class="bi bi-<?php echo htmlspecialchars($cdg_pd_icon); ?>"></i></div>
            <div class="cdg-pd2-hero-text">
                <div class="cdg-pd2-hero-eyebrow"><?php echo htmlspecialchars(strtoupper($cdg_pd_title)); ?></div>
                <h1><?php echo htmlspecialchars($d_name); ?></h1>
                <div class="cdg-pd2-hero-meta">
                    <?php if($d_domain): ?>
                    <span><i class="bi bi-globe"></i> <?php echo htmlspecialchars($d_domain); ?></span>
                    <?php endif; ?>
                    <?php if($d_hostname && $d_hostname !== $d_domain): ?>
                    <span><i class="bi bi-server"></i> <?php echo htmlspecialchars($d_hostname); ?></span>
                    <?php endif; ?>
                    <?php if($d_ip): ?>
                    <span><i class="bi bi-hdd-network"></i> <?php echo htmlspecialchars($d_ip); ?></span>
                    <?php endif; ?>
                    <?php if($d_duedate): ?>
                    <span><i class="bi bi-calendar-check"></i> Bitiş: <?php echo htmlspecialchars(cdg_pd_date($d_duedate)); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cdg-pd2-hero-status">
                <i class="bi bi-<?php echo htmlspecialchars($st_meta['icon']); ?>"></i>
                <?php echo htmlspecialchars($st_meta['lbl']); ?>
            </div>
        </div>
    </section>

    <!-- TAB NAV -->
    <div class="cdg-pd2-tabs">
        <button class="cdg-pd2-tab active" data-pane="summary"><i class="bi bi-info-circle"></i> Özet</button>
        <?php if(!empty($addons)): ?>
        <button class="cdg-pd2-tab" data-pane="addons"><i class="bi bi-rocket-takeoff"></i> Ek Hizmetler</button>
        <?php endif; ?>
        <?php if(!empty($upgrades)): ?>
        <button class="cdg-pd2-tab" data-pane="upgrade"><i class="bi bi-arrow-up-circle"></i> Yükselt</button>
        <?php endif; ?>
        <?php if($cdg_pd_kind === 'hosting'): ?>
        <button class="cdg-pd2-tab" data-pane="password"><i class="bi bi-key"></i> Şifre</button>
        <?php endif; ?>
        <button class="cdg-pd2-tab" data-pane="renewal"><i class="bi bi-arrow-clockwise"></i> Yenileme</button>
        <button class="cdg-pd2-tab" data-pane="cancel"><i class="bi bi-ban"></i> İptal</button>
    </div>

    <!-- TAB: SUMMARY -->
    <div class="cdg-pd2-pane active" id="cdg-pd2-pane-summary">
        <div class="cdg-pd2-grid-2">
            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-info-circle"></i> Hizmet Bilgileri</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <li><span class="cdg-pd2-info-label">Paket</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_name); ?></span></li>
                        <li><span class="cdg-pd2-info-label">Durum</span><span class="cdg-pd2-info-value"><span class="cdg-pd2-badge <?php echo $st_meta['cls']; ?>"><?php echo htmlspecialchars($st_meta['lbl']); ?></span></span></li>
                        <?php if($d_cdate): ?>
                        <li><span class="cdg-pd2-info-label">Sipariş Tarihi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_date($d_cdate)); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_duedate): ?>
                        <li><span class="cdg-pd2-info-label">Bitiş Tarihi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_date($d_duedate)); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_period && $d_ptime): ?>
                        <li><span class="cdg-pd2-info-label">Periyot</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_period . ' ' . $d_ptime); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_amount): ?>
                        <li><span class="cdg-pd2-info-label">Ücret</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_money($d_amount, $d_amount_cid)); ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-gear"></i> Erişim Bilgileri</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <?php if($d_domain): ?>
                        <li><span class="cdg-pd2-info-label">Domain</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_domain); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_hostname && $d_hostname !== $d_domain): ?>
                        <li><span class="cdg-pd2-info-label">Hostname</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_hostname); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_ip): ?>
                        <li><span class="cdg-pd2-info-label">IP Adresi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_ip); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_username): ?>
                        <li><span class="cdg-pd2-info-label">Kullanıcı Adı</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_username); ?></span></li>
                        <?php endif; ?>
                        <?php foreach($extra_options as $k => $v): ?>
                        <li><span class="cdg-pd2-info-label"><?php echo htmlspecialchars(ucfirst(str_replace('_',' ',$k))); ?></span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($v); ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if(!empty($options['cp_url']) || !empty($options['panel_url'])):
                        $cp_url = $options['cp_url'] ?? $options['panel_url']; ?>
                    <div style="margin-top:14px;">
                        <a href="<?php echo htmlspecialchars($cp_url); ?>" target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-primary" style="width:100%;justify-content:center;">
                            <i class="bi bi-box-arrow-up-right"></i> Kontrol Paneline Git
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: ADDONS -->
    <?php if(!empty($addons)): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-addons">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-rocket-takeoff"></i> Ek Hizmetler</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div class="cdg-pd2-alert cdg-pd2-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Hizmetinize ek özellikler ekleyebilir, mevcut paketinizi genişletebilirsiniz.</div>
                </div>

                <?php foreach($addons as $addon):
                    if(!is_array($addon)) continue;
                    $a_name = $addon['name'] ?? 'Eklenti';
                    $a_amount = $addon['amount'] ?? 0;
                    $a_cid = $addon['amount_cid'] ?? 0;
                    $a_id = $addon['id'] ?? 0;
                ?>
                <div class="cdg-pd2-addon-card">
                    <div>
                        <div class="cdg-pd2-addon-name"><?php echo htmlspecialchars($a_name); ?></div>
                        <?php if(!empty($addon['description'])): ?>
                        <div style="font-size:12px;color:var(--p-muted);margin-top:2px;"><?php echo htmlspecialchars($addon['description']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <span class="cdg-pd2-addon-price"><?php echo htmlspecialchars(cdg_pd_money($a_amount, $a_cid)); ?></span>
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success cdg-pd2-btn-sm" onclick="cdgPd2.addAddon(<?php echo (int)$a_id; ?>)">
                            <i class="bi bi-cart-plus"></i> Ekle
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB: UPGRADE -->
    <?php if(!empty($upgrades)): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-upgrade">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-arrow-up-circle"></i> Paket Yükseltme</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div class="cdg-pd2-alert cdg-pd2-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Mevcut paketinizden daha üst bir pakete yükselterek daha fazla kaynak ve özellikten yararlanabilirsiniz.</div>
                </div>

                <?php foreach($upgrades as $upgrade):
                    if(!is_array($upgrade)) continue;
                    $u_name = $upgrade['name'] ?? 'Paket';
                    $u_amount = $upgrade['amount'] ?? 0;
                    $u_cid = $upgrade['amount_cid'] ?? 0;
                    $u_id = $upgrade['id'] ?? 0;
                ?>
                <div class="cdg-pd2-addon-card">
                    <div>
                        <div class="cdg-pd2-addon-name"><?php echo htmlspecialchars($u_name); ?></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <span class="cdg-pd2-addon-price"><?php echo htmlspecialchars(cdg_pd_money($u_amount, $u_cid)); ?></span>
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary cdg-pd2-btn-sm" onclick="cdgPd2.upgrade(<?php echo (int)$u_id; ?>)">
                            <i class="bi bi-arrow-up"></i> Yükselt
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB: PASSWORD (sadece hosting) -->
    <?php if($cdg_pd_kind === 'hosting'): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-password">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-key"></i> Şifre Değiştir</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div class="cdg-pd2-alert cdg-pd2-alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>Hosting kontrol paneli (cPanel/DirectAdmin) şifrenizi buradan değiştirebilirsiniz.</div>
                </div>

                <form id="cdg-pd2-pwd-form" onsubmit="return false;">
                    <div class="cdg-pd2-field">
                        <label class="cdg-pd2-label">Yeni Şifre</label>
                        <input type="password" id="cdg-pd2-newpass" class="cdg-pd2-input" minlength="8" autocomplete="new-password">
                    </div>
                    <div class="cdg-pd2-field">
                        <label class="cdg-pd2-label">Yeni Şifre (Tekrar)</label>
                        <input type="password" id="cdg-pd2-newpass2" class="cdg-pd2-input" minlength="8" autocomplete="new-password">
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary" onclick="cdgPd2.changePassword()">
                            <i class="bi bi-key"></i> Şifreyi Değiştir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB: RENEWAL -->
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-renewal">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-arrow-clockwise"></i> Yenileme</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <?php if($invoice && !empty($invoice['id'])): ?>
                <div class="cdg-pd2-alert cdg-pd2-alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Bekleyen yenileme faturanız var.</strong><br>
                        Fatura no: #<?php echo htmlspecialchars($invoice['number'] ?? $invoice['id']); ?>
                    </div>
                </div>
                <?php if(!empty($invoice['detail_link'])): ?>
                <a href="<?php echo htmlspecialchars($invoice['detail_link']); ?>" class="cdg-pd2-btn cdg-pd2-btn-success" style="width:100%;justify-content:center;">
                    <i class="bi bi-credit-card"></i> Faturayı Görüntüle / Öde
                </a>
                <?php endif; ?>
                <?php else: ?>
                <div class="cdg-pd2-alert cdg-pd2-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Hizmetinizin bitiş tarihi yaklaştığında otomatik fatura oluşturulur. İsterseniz şimdi de yenileyebilirsiniz.</div>
                </div>
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success" style="width:100%;justify-content:center;" onclick="cdgPd2.renew()">
                    <i class="bi bi-arrow-clockwise"></i> Şimdi Yenile
                </button>
                <?php endif; ?>

                <div style="margin-top:16px;">
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="width:100%;justify-content:center;" onclick="cdgPd2.toggleAutoPay()">
                        <i class="bi bi-credit-card-2-back"></i>
                        Otomatik Ödeme: <strong><?php echo $d_autopay ? 'Aktif' : 'Pasif'; ?></strong>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: CANCEL -->
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-cancel">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-ban"></i> İptal Talebi</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div class="cdg-pd2-alert cdg-pd2-alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Hizmet iptali geri alınamaz.</strong><br>
                        İptal talebiniz onaylandıktan sonra hizmetiniz sonlandırılır ve tüm verileriniz silinir. Lütfen iptalden önce verilerinizi yedekleyin.
                    </div>
                </div>

                <div class="cdg-pd2-field">
                    <label class="cdg-pd2-label">İptal Türü</label>
                    <select id="cdg-pd2-cancel-type" class="cdg-pd2-input">
                        <option value="immediately">Hemen İptal Et</option>
                        <option value="end-of-period">Periyot Sonunda İptal Et</option>
                    </select>
                </div>
                <div class="cdg-pd2-field">
                    <label class="cdg-pd2-label">İptal Sebebi (opsiyonel)</label>
                    <textarea id="cdg-pd2-cancel-reason" class="cdg-pd2-input" style="min-height:80px;" placeholder="İptal sebebinizi belirtin..."></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-danger" onclick="cdgPd2.cancel()">
                        <i class="bi bi-x-circle"></i> İptal Talebi Gönder
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
(function(){
    document.querySelectorAll('.cdg-pd2-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            var pane = this.getAttribute('data-pane');
            document.querySelectorAll('.cdg-pd2-tab').forEach(function(t){ t.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.cdg-pd2-pane').forEach(function(p){ p.classList.remove('active'); });
            var target = document.getElementById('cdg-pd2-pane-' + pane);
            if(target) target.classList.add('active');
            try { history.replaceState(null, '', '#' + pane); } catch(e) {}
        });
    });
    if(location.hash) {
        var hash = location.hash.substring(1);
        var tab = document.querySelector('.cdg-pd2-tab[data-pane="' + hash + '"]');
        if(tab) tab.click();
    }
})();

window.cdgPd2 = {
    productId: <?php echo (int)$d_id; ?>,
    controllerUrl: '<?php echo htmlspecialchars($controller_url); ?>',

    renew: function(){
        if(!confirm('Yenileme faturası oluşturulacak. Devam edilsin mi?')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'product_renewal', id: this.productId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yenileme talebi oluşturuldu', {timer: 2000});
                    setTimeout(function(){ if(r.redirect) location.href = r.redirect; else location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    toggleAutoPay: function(){
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'set_auto_pay_status', id: this.productId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Güncellendi', {timer: 1500});
                    setTimeout(function(){ location.reload(); }, 1200);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    changePassword: function(){
        var p1 = document.getElementById('cdg-pd2-newpass').value;
        var p2 = document.getElementById('cdg-pd2-newpass2').value;
        if(!p1 || p1.length < 8) {
            if(typeof alert_error === 'function') alert_error('Şifre en az 8 karakter olmalı', {timer: 3000});
            return;
        }
        if(p1 !== p2) {
            if(typeof alert_error === 'function') alert_error('Şifreler eşleşmiyor', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'change_hosting_password', id: this.productId, password: p1, password2: p2 },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Şifre değiştirildi', {timer: 2000});
                    document.getElementById('cdg-pd2-newpass').value = '';
                    document.getElementById('cdg-pd2-newpass2').value = '';
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    },

    cancel: function(){
        if(!confirm('Hizmet iptal talebinizi göndermek istediğinize emin misiniz? Bu işlem geri alınamaz.')) return;
        var type = document.getElementById('cdg-pd2-cancel-type').value;
        var reason = document.getElementById('cdg-pd2-cancel-reason').value;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'canceled_product', id: this.productId, type: type, reason: reason },
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

    addAddon: function(addonId){
        if(!confirm('Eklenti satın almak için fatura oluşturulacak. Devam edilsin mi?')) return;
        // operation: addon_buy / product_addon
        if(typeof alert_info === 'function') alert_info('İşleniyor...', {timer: 2000});
    },

    upgrade: function(upgradeId){
        if(!confirm('Paket yükseltme için fatura oluşturulacak. Devam edilsin mi?')) return;
        if(typeof alert_info === 'function') alert_info('İşleniyor...', {timer: 2000});
    }
};
</script>

<?php
// === Paket Yükseltme (hosting/server/software/special için ortak) ===
if(in_array($cdg_pd_kind ?? '', ['hosting','server','software','special'])) {
    $upgrade_inc = __DIR__ . DS . 'ac-product-upgrade.php';
    if(file_exists($upgrade_inc)) include $upgrade_inc;
}
