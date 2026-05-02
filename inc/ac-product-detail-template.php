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

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd2-back">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <section class="cdg-pd2-hero">
        <div class="cdg-pd2-hero-row">
            <div class="cdg-pd2-hero-icon"><i class="bi bi-<?php echo htmlspecialchars($cdg_pd_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i></div>
            <div class="cdg-pd2-hero-text">
                <div class="cdg-pd2-hero-eyebrow"><?php echo htmlspecialchars(mb_strtoupper($cdg_pd_title, 'UTF-8')); ?></div>
                <h1><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
                <div class="cdg-pd2-hero-meta">
                    <?php if($d_domain): ?>
                    <span><i class="bi bi-globe"></i> <?php echo htmlspecialchars($d_domain, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($d_hostname && $d_hostname !== $d_domain): ?>
                    <span><i class="bi bi-server"></i> <?php echo htmlspecialchars($d_hostname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($d_ip): ?>
                    <span><i class="bi bi-hdd-network"></i> <?php echo htmlspecialchars($d_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($d_duedate): ?>
                    <span><i class="bi bi-calendar-check"></i> Bitiş: <?php echo htmlspecialchars(cdg_pd_date($d_duedate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cdg-pd2-hero-status">
                <i class="bi bi-<?php echo htmlspecialchars($st_meta['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                <?php echo htmlspecialchars($st_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
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
                        <li><span class="cdg-pd2-info-label">Paket</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <li><span class="cdg-pd2-info-label">Durum</span><span class="cdg-pd2-info-value"><span class="cdg-pd2-badge <?php echo $st_meta['cls']; ?>"><?php echo htmlspecialchars($st_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></span></li>
                        <?php if($d_cdate): ?>
                        <li><span class="cdg-pd2-info-label">Sipariş Tarihi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_date($d_cdate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_duedate): ?>
                        <li><span class="cdg-pd2-info-label">Bitiş Tarihi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_date($d_duedate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_period && $d_ptime):
                            // year/month/day -> Yil/Ay/Gun
                            $period_unit_map = [
                                'year' => 'Yıl', 'years' => 'Yıl', 'y' => 'Yıl',
                                'month' => 'Ay', 'months' => 'Ay', 'm' => 'Ay',
                                'week' => 'Hafta', 'weeks' => 'Hafta', 'w' => 'Hafta',
                                'day' => 'Gün', 'days' => 'Gün', 'd' => 'Gün',
                                'hour' => 'Saat', 'hours' => 'Saat', 'h' => 'Saat',
                            ];
                            $unit_tr = $period_unit_map[strtolower((string)$d_ptime)] ?? $d_ptime;
                            $period_display = (int)$d_period . ' ' . $unit_tr;
                        ?>
                        <li><span class="cdg-pd2-info-label">Periyot</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($period_display, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_amount): ?>
                        <li><span class="cdg-pd2-info-label">Ücret</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_money($d_amount, $d_amount_cid), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
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
                        <li><span class="cdg-pd2-info-label">Domain</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_domain, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_hostname && $d_hostname !== $d_domain): ?>
                        <li><span class="cdg-pd2-info-label">Hostname</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_hostname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_ip): ?>
                        <li><span class="cdg-pd2-info-label">IP Adresi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_username): ?>
                        <li><span class="cdg-pd2-info-label">Kullanıcı Adı</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_username, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php
                        // Extra option key'leri Turkce label'a cevir
                        $option_label_map = [
                            'group_name'         => 'Hosting Grubu',
                            'local_group_name'   => 'Yerel Grup',
                            'category_name'      => 'Hosting Kategorisi',
                            'local_category_name'=> 'Yerel Kategori',
                            'panel_type'         => 'Kontrol Paneli',
                            'panel_url'          => 'Panel Adresi',
                            'cp_url'             => 'Panel Adresi',
                            'disk_limit'         => 'Disk Alanı (MB)',
                            'bandwidth_limit'    => 'Aylık Trafik (MB)',
                            'email_limit'        => 'E-posta Hesabı',
                            'database_limit'     => 'Veritabanı',
                            'ftp_limit'          => 'FTP Hesabı',
                            'park_limit'         => 'Park Domain',
                            'addons_limit'       => 'Eklenti Domain',
                            'subdomain_limit'    => 'Alt Alan Adı',
                            'username'           => 'Kullanıcı Adı',
                            'password'           => 'Şifre',
                            'hostname'           => 'Sunucu Adı',
                            'ip'                 => 'IP Adresi',
                            'domain'             => 'Alan Adı',
                            'ns1'                => 'Birincil Ad Sunucusu',
                            'ns2'                => 'İkincil Ad Sunucusu',
                            'os'                 => 'İşletim Sistemi',
                            'cpu'                => 'İşlemci',
                            'ram'                => 'Bellek (RAM)',
                            'storage'            => 'Disk',
                            'bandwidth'          => 'Bant Genişliği',
                            'location'           => 'Konum',
                            'datacenter'         => 'Veri Merkezi',
                        ];
                        foreach($extra_options as $k => $v):
                            $key_label = $option_label_map[$k] ?? ucfirst(str_replace('_', ' ', $k));
                            // 'unlimited' / 'sinirsiz' degerini Turkcele
                            $val_display = $v;
                            if(is_string($v) && in_array(strtolower(trim($v)), ['unlimited', 'sinirsiz'])) {
                                $val_display = 'Sınırsız';
                            }
                        ?>
                        <li><span class="cdg-pd2-info-label"><?php echo htmlspecialchars($key_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars((string)$val_display, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if(!empty($options['cp_url']) || !empty($options['panel_url'])):
                        $cp_url = $options['cp_url'] ?? $options['panel_url']; ?>
                    <div style="margin-top:14px;">
                        <a href="<?php echo htmlspecialchars($cp_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-primary" style="width:100%;justify-content:center;">
                            <i class="bi bi-box-arrow-up-right"></i> Kontrol Paneline Git
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
        // === HOSTING PAKET DETAYLARI (kota bilgileri) ===
        if(in_array($cdg_pd_kind, ['hosting', 'server'])):
            $disk_limit       = isset($options['disk_limit']) ? $options['disk_limit'] : null;
            $bandwidth_limit  = isset($options['bandwidth_limit']) ? $options['bandwidth_limit'] : null;
            $email_limit      = isset($options['email_limit']) ? $options['email_limit'] : null;
            $database_limit   = isset($options['database_limit']) ? $options['database_limit'] : null;
            $addons_limit     = isset($options['addons_limit']) ? $options['addons_limit'] : null;
            $subdomain_limit  = isset($options['subdomain_limit']) ? $options['subdomain_limit'] : null;
            $ftp_limit        = isset($options['ftp_limit']) ? $options['ftp_limit'] : null;
            $park_limit       = isset($options['park_limit']) ? $options['park_limit'] : null;
            $cpu_limit        = isset($options['cpu_limit']) ? $options['cpu_limit'] : null;
            $ram_limit        = isset($options['ram_limit']) ? $options['ram_limit'] : null;
            $max_email_per_hour = isset($options['max_email_per_hour']) ? $options['max_email_per_hour'] : null;
            $panel_type       = isset($options['panel_type']) ? $options['panel_type'] : null;
            $panel_link       = isset($options['panel_link']) ? $options['panel_link'] : null;
            $hosting_dns      = isset($options['dns']) && is_array($options['dns']) ? $options['dns'] : [];
            $ftp_info         = isset($options['ftp_info']) && is_array($options['ftp_info']) ? $options['ftp_info'] : [];
            $creation_info    = isset($options['creation_info']) && is_array($options['creation_info']) ? $options['creation_info'] : [];

            // WiseCP runtime: $server array (hostname, ip, status, vb.)
            $cdg_server_info = isset($server) && is_array($server) ? $server : [];
            $server_hostname = $cdg_server_info['hostname'] ?? ($options['server_hostname'] ?? '');
            $server_ip       = $cdg_server_info['ip'] ?? ($options['server_ip'] ?? '');
            $server_status   = $cdg_server_info['status'] ?? '';
            $server_panel_url = $cdg_server_info['panel_url'] ?? ($options['panel_url'] ?? $panel_link);

            $has_quota = $disk_limit !== null || $bandwidth_limit !== null || $email_limit !== null || $database_limit !== null;
            $has_panel = $panel_type || $panel_link || $server_panel_url;
            $has_server = !empty($server_hostname) || !empty($server_ip);
        ?>

        <?php if($has_server): ?>
        <!-- Sunucu Bilgi Karti ($server runtime variable) -->
        <div class="cdg-pd2-card" style="margin-top:18px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-server"></i> Sunucu Bilgileri</h3>
                <?php if($server_status === 'active'): ?>
                <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;">
                    <i class="bi bi-check-circle"></i> Aktif
                </span>
                <?php endif; ?>
            </div>
            <div class="cdg-pd2-card-body">
                <ul class="cdg-pd2-info">
                    <?php if($server_hostname): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Sunucu Hostname</span>
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:700;color:#1e40af;">
                            <?php echo htmlspecialchars($server_hostname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <button type="button" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars(addslashes($server_hostname), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>'); this.innerHTML='<i class=\'bi bi-check\'></i>';" style="margin-left:6px;background:#f1f5f9;border:0;padding:2px 6px;border-radius:4px;cursor:pointer;font-size:11px;color:#64748b;" title="Kopyala">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </span>
                    </li>
                    <?php endif; ?>
                    <?php if($server_ip): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Sunucu IP</span>
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:700;color:#1e40af;">
                            <?php echo htmlspecialchars($server_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <button type="button" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars(addslashes($server_ip), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>'); this.innerHTML='<i class=\'bi bi-check\'></i>';" style="margin-left:6px;background:#f1f5f9;border:0;padding:2px 6px;border-radius:4px;cursor:pointer;font-size:11px;color:#64748b;" title="Kopyala">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </span>
                    </li>
                    <?php endif; ?>
                    <?php if(!empty($hosting_dns)): ?>
                    <li>
                        <span class="cdg-pd2-info-label">DNS Sunucularimiz</span>
                        <span class="cdg-pd2-info-value">
                            <?php foreach($hosting_dns as $idx => $dns): ?>
                            <code style="display:block;background:#f1f5f9;padding:4px 8px;border-radius:4px;font-size:12px;margin:2px 0;"><?php echo htmlspecialchars($dns, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                            <?php endforeach; ?>
                        </span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if($has_quota): ?>
        <div class="cdg-pd2-card" style="margin-top:18px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-hdd-stack"></i> Paket Kotalari</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
                    <?php
                    $quotas = [
                        'disk_limit'      => ['label' => 'Disk Alanı', 'icon' => 'hdd-fill', 'unit' => 'MB', 'color' => '#3b82f6'],
                        'bandwidth_limit' => ['label' => 'Aylık Trafik', 'icon' => 'arrow-down-up', 'unit' => 'MB', 'color' => '#7c3aed'],
                        'email_limit'     => ['label' => 'E-posta Hesabı', 'icon' => 'envelope-fill', 'unit' => 'adet', 'color' => '#f59e0b'],
                        'database_limit'  => ['label' => 'Veritabanı', 'icon' => 'database-fill', 'unit' => 'adet', 'color' => '#10b981'],
                        'addons_limit'    => ['label' => 'Eklenti Domain', 'icon' => 'plus-square-fill', 'unit' => 'adet', 'color' => '#06b6d4'],
                        'subdomain_limit' => ['label' => 'Alt Alan Adı', 'icon' => 'diagram-3-fill', 'unit' => 'adet', 'color' => '#ec4899'],
                        'ftp_limit'       => ['label' => 'FTP Hesabı', 'icon' => 'cloud-arrow-up-fill', 'unit' => 'adet', 'color' => '#64748b'],
                        'park_limit'      => ['label' => 'Park Domain', 'icon' => 'p-square-fill', 'unit' => 'adet', 'color' => '#8b5cf6'],
                    ];
                    foreach($quotas as $key => $info):
                        $val = $$key;
                        if($val === null) continue;
                        $is_unlimited = ($val === 0 || $val === '0' || strtolower((string)$val) === 'unlimited' || strtolower((string)$val) === 'sinirsiz' || strtolower((string)$val) === 'sınırsız');
                        $display = $is_unlimited ? 'Sınırsız' : (is_numeric($val) ? number_format((int)$val, 0, ',', '.') : $val);
                    ?>
                    <div style="background:linear-gradient(135deg,<?php echo $info['color']; ?>15,<?php echo $info['color']; ?>05);border:1px solid <?php echo $info['color']; ?>30;border-radius:10px;padding:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;color:<?php echo $info['color']; ?>;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                            <i class="bi bi-<?php echo $info['icon']; ?>"></i>
                            <?php echo $info['label']; ?>
                        </div>
                        <div style="font-size:18px;font-weight:800;color:#0f172a;">
                            <?php echo htmlspecialchars($display, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <?php if(!$is_unlimited): ?>
                            <span style="font-size:11px;font-weight:600;color:#64748b;"><?php echo $info['unit']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if($cpu_limit): ?>
                    <div style="background:linear-gradient(135deg,#ef444415,#ef444405);border:1px solid #ef444430;border-radius:10px;padding:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;color:#ef4444;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                            <i class="bi bi-cpu-fill"></i> CPU
                        </div>
                        <div style="font-size:18px;font-weight:800;color:#0f172a;"><?php echo htmlspecialchars($cpu_limit, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if($ram_limit): ?>
                    <div style="background:linear-gradient(135deg,#8b5cf615,#8b5cf605);border:1px solid #8b5cf630;border-radius:10px;padding:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;color:#8b5cf6;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                            <i class="bi bi-memory"></i> RAM
                        </div>
                        <div style="font-size:18px;font-weight:800;color:#0f172a;"><?php echo htmlspecialchars($ram_limit, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if($max_email_per_hour): ?>
                    <div style="background:linear-gradient(135deg,#f59e0b15,#f59e0b05);border:1px solid #f59e0b30;border-radius:10px;padding:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;color:#f59e0b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                            <i class="bi bi-send-fill"></i> Saatlik E-posta
                        </div>
                        <div style="font-size:18px;font-weight:800;color:#0f172a;"><?php echo (int)$max_email_per_hour; ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($hosting_dns)): ?>
        <div class="cdg-pd2-card" style="margin-top:18px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-server"></i> Sunucu DNS Bilgileri</h3>
                <span style="font-size:11px;color:#64748b;">Domaininizi bu sunucuya yonlendirmek icin bu nameserver'lari kullanin</span>
            </div>
            <div class="cdg-pd2-card-body">
                <ul class="cdg-pd2-info">
                    <?php foreach($hosting_dns as $idx => $ns):
                        if(!$ns) continue;
                    ?>
                    <li style="font-family:'Courier New',monospace;">
                        <span class="cdg-pd2-info-label">NS<?php echo ($idx + 1); ?></span>
                        <span class="cdg-pd2-info-value" style="font-weight:700;color:#1e40af;letter-spacing:0.3px;"><?php echo htmlspecialchars($ns, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ftp_info) || $panel_type): ?>
        <div class="cdg-pd2-card" style="margin-top:18px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-info-square"></i> Teknik Bilgiler</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <ul class="cdg-pd2-info">
                    <?php if($panel_type): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Kontrol Paneli</span>
                        <span class="cdg-pd2-info-value">
                            <span style="display:inline-block;padding:3px 10px;background:#eff6ff;color:#1e40af;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;">
                                <?php echo htmlspecialchars($panel_type, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </span>
                        </span>
                    </li>
                    <?php endif; ?>
                    <?php if(!empty($ftp_info['host'])): ?>
                    <li>
                        <span class="cdg-pd2-info-label">FTP Sunucu</span>
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:600;"><?php echo htmlspecialchars($ftp_info['host'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if(!empty($ftp_info['port'])): ?>
                    <li>
                        <span class="cdg-pd2-info-label">FTP Port</span>
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:600;"><?php echo (int)$ftp_info['port']; ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if(!empty($ftp_info['username'])): ?>
                    <li>
                        <span class="cdg-pd2-info-label">FTP Kullanici</span>
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:600;"><?php echo htmlspecialchars($ftp_info['username'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if(!empty($creation_info['date'])): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Olusturulma</span>
                        <span class="cdg-pd2-info-value"><?php echo htmlspecialchars($creation_info['date'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; // hosting/server kotalari ?>
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
                        <div class="cdg-pd2-addon-name"><?php echo htmlspecialchars($a_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <?php if(!empty($addon['description'])): ?>
                        <div style="font-size:12px;color:var(--p-muted);margin-top:2px;"><?php echo htmlspecialchars($addon['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <span class="cdg-pd2-addon-price"><?php echo htmlspecialchars(cdg_pd_money($a_amount, $a_cid), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
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
                        <div class="cdg-pd2-addon-name"><?php echo htmlspecialchars($u_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <span class="cdg-pd2-addon-price"><?php echo htmlspecialchars(cdg_pd_money($u_amount, $u_cid), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
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
                        Fatura no: #<?php echo htmlspecialchars($invoice['number'] ?? $invoice['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                </div>
                <?php if(!empty($invoice['detail_link'])): ?>
                <a href="<?php echo htmlspecialchars($invoice['detail_link'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd2-btn cdg-pd2-btn-success" style="width:100%;justify-content:center;">
                    <i class="bi bi-credit-card"></i> Faturayı Görüntüle / Öde
                </a>
                <?php endif; ?>
                <?php else: ?>
                <div class="cdg-pd2-alert cdg-pd2-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>Hizmetinizin bitiş tarihi yaklaştığında otomatik fatura oluşturulur. İsterseniz şimdi de yenileyebilirsiniz.</div>
                </div>

                <?php
                // Yenileme dönemi seçici (order_renewal) - WiseCP runtime: $product['price'] array
                $renewal_prices = [];
                if(isset($product) && is_array($product) && isset($product['price']) && is_array($product['price'])) {
                    $renewal_prices = $product['price'];
                }
                if(!empty($renewal_prices)):
                ?>
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.4px;">
                        <i class="bi bi-calendar3"></i> Yenileme Dönemi Seç
                    </label>
                    <div style="display:grid;grid-template-columns:1fr auto;gap:8px;">
                        <select id="cdg-pd2-renewal-period" class="cdg-pdm-select" style="font-size:13px;">
                            <option value="">Yenileme dönemi seçin...</option>
                            <?php foreach($renewal_prices as $k => $v):
                                $r_time = $v['time'] ?? 1;
                                $r_period = $v['period'] ?? 'm';
                                $r_amount = $v['amount'] ?? 0;
                                $r_cid = $v['cid'] ?? 'TRY';
                                $period_label = '';
                                if(class_exists('View') && method_exists('View','period')) {
                                    try { $period_label = View::period($r_time, $r_period); } catch(\Throwable $e) {}
                                }
                                $amount_str = '';
                                if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
                                    try { $amount_str = Money::formatter_symbol($r_amount, $r_cid, true); } catch(\Throwable $e) { $amount_str = $r_amount . ' ' . $r_cid; }
                                } else {
                                    $amount_str = $r_amount . ' ' . $r_cid;
                                }
                                $is_current = false;
                                if(isset($proanse['period']) && isset($proanse['period_time'])) {
                                    if($proanse['period'] == $r_period && $proanse['period_time'] == $r_time) $is_current = true;
                                }
                            ?>
                            <option value="<?php echo htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($period_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> — <?php echo htmlspecialchars($amount_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php echo $is_current ? ' (mevcut)' : ''; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary" onclick="cdgPd2OrderRenewal(this)">
                            <i class="bi bi-cart-plus"></i> Sepete Ekle
                        </button>
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-top:6px;">
                        <i class="bi bi-info-circle"></i> Mevcut süre yerine yeni bir dönem seçerek devam edebilirsiniz. Seçim sonrası ödeme sayfasına yönlendirileceksiniz.
                    </div>
                </div>
                <?php endif; ?>

                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success" style="width:100%;justify-content:center;" onclick="cdgPd2.renew()">
                    <i class="bi bi-arrow-clockwise"></i> Mevcut Dönemle Yenile
                </button>
                <?php endif; ?>

                <div style="margin-top:16px;">
                    <?php
                    // Subscription detay kontrolu (Classic uyumlu)
                    // $subscription set ve status != cancelled ise: aktif abonelik var
                    // $subscription set degilse veya cancelled: auto_pay manuel checkbox
                    $cdg_subscription = isset($subscription) && is_array($subscription) ? $subscription : null;
                    $cdg_sub_status = $cdg_subscription['status'] ?? '';
                    $cdg_has_active_sub = $cdg_subscription && $cdg_sub_status !== 'cancelled';
                    $cdg_stored_cards = isset($stored_cards) && is_array($stored_cards) && !empty($stored_cards);
                    ?>

                    <?php if($cdg_has_active_sub): ?>
                    <!-- AKTIF ABONELIK - subscription_detail AJAX -->
                    <div style="padding:14px;background:#dcfce7;border-left:4px solid #10b981;border-radius:8px;margin-bottom:10px;">
                        <div style="font-weight:800;color:#166534;margin-bottom:6px;">
                            <i class="bi bi-arrow-repeat"></i> Otomatik Yenileme Aktif
                        </div>
                        <div id="cdg-pd2-subscription-status" style="font-size:13px;color:#15803d;">
                            <i class="bi bi-arrow-clockwise" style="animation:spin 1s linear infinite;"></i> Abonelik bilgileri yükleniyor...
                        </div>
                        <?php
                        $cdg_sub_amount = $cdg_subscription['amount'] ?? '';
                        $cdg_sub_next_date = $cdg_subscription['next_payment_date'] ?? ($cdg_subscription['next_date'] ?? '');
                        $cdg_sub_period = $cdg_subscription['period'] ?? '';
                        if($cdg_sub_amount || $cdg_sub_next_date):
                        ?>
                        <div style="margin-top:8px;font-size:12px;color:#166534;">
                            <?php if($cdg_sub_amount): ?>
                            <div><strong>Tutar:</strong> <?php echo htmlspecialchars($cdg_sub_amount, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <?php endif; ?>
                            <?php if($cdg_sub_next_date): ?>
                            <div><strong>Sonraki Ödeme:</strong> <?php echo htmlspecialchars($cdg_sub_next_date, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <?php endif; ?>
                            <?php if($cdg_sub_period): ?>
                            <div><strong>Periyot:</strong> <?php echo htmlspecialchars($cdg_sub_period, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="width:100%;justify-content:center;" onclick="cdgPd2.cancelSubscription()">
                        <i class="bi bi-x-circle"></i> Aboneliği İptal Et
                    </button>
                    <?php else: ?>
                    <!-- ABONELIK YOK - manuel auto_pay checkbox (stored_cards gerekir) -->
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="width:100%;justify-content:center;" onclick="cdgPd2.toggleAutoPay()">
                        <i class="bi bi-credit-card-2-back"></i>
                        Otomatik Ödeme: <strong><?php echo $d_autopay ? 'Aktif' : 'Pasif'; ?></strong>
                    </button>
                    <?php if(!$cdg_stored_cards && !$d_autopay): ?>
                    <div style="margin-top:8px;padding:8px 10px;background:#fef3c7;border-left:3px solid #f59e0b;border-radius:6px;font-size:12px;color:#92400e;">
                        <i class="bi bi-info-circle"></i> Otomatik ödeme aktif edilebilmesi için önce hesabınıza kart eklemeniz gerekir.
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: CANCEL -->
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-cancel">
        <?php
        // Mevcut iptal talebi var mı? WiseCP runtime: $p_cancellation
        $p_cancel = isset($p_cancellation) && $p_cancellation ? $p_cancellation : null;
        $p_cancel_data = [];
        if($p_cancel && isset($p_cancel['data'])) {
            if(is_string($p_cancel['data'])) {
                if(class_exists('Utility') && method_exists('Utility','jdecode')) {
                    try { $p_cancel_data = Utility::jdecode($p_cancel['data'], true); } catch(\Throwable $e) {}
                }
                if(empty($p_cancel_data)) {
                    $tmp = json_decode($p_cancel['data'], true);
                    if(is_array($tmp)) $p_cancel_data = $tmp;
                }
            } elseif(is_array($p_cancel['data'])) {
                $p_cancel_data = $p_cancel['data'];
            }
        }

        if($p_cancel):
            $cancel_status = $p_cancel['status'] ?? 'pending';
            $cancel_status_meta = [
                'pending'  => ['cls' => 'cdg-pd2-badge-warning', 'lbl' => 'Beklemede',  'icon' => 'hourglass-split', 'color' => '#f59e0b'],
                'approved' => ['cls' => 'cdg-pd2-badge-danger',  'lbl' => 'Onaylandi',  'icon' => 'check-circle-fill', 'color' => '#ef4444'],
                'rejected' => ['cls' => 'cdg-pd2-badge-success', 'lbl' => 'Reddedildi', 'icon' => 'x-circle-fill', 'color' => '#10b981'],
            ];
            $csm = $cancel_status_meta[$cancel_status] ?? $cancel_status_meta['pending'];
            $cancel_reason = $p_cancel_data['reason'] ?? '';
            $cancel_urgency = $p_cancel_data['urgency'] ?? 'now';
            $cancel_date = $p_cancel['cdate'] ?? '';
            $admin_note = $p_cancel['operator_note'] ?? '';
        ?>
        <!-- MEVCUT IPTAL TALEBI -->
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head" style="background:linear-gradient(135deg, <?php echo $csm['color']; ?>15, transparent);">
                <h3><i class="bi bi-<?php echo $csm['icon']; ?>" style="color:<?php echo $csm['color']; ?>;"></i> Iptal Talebi Mevcut</h3>
                <span class="cdg-pd2-badge <?php echo $csm['cls']; ?>"><?php echo htmlspecialchars($csm['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </div>
            <div class="cdg-pd2-card-body">
                <ul class="cdg-pd2-info">
                    <?php if($cancel_reason): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Iptal Sebebi</span>
                        <span class="cdg-pd2-info-value" style="font-style:italic;color:#475569;">"<?php echo htmlspecialchars($cancel_reason, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"</span>
                    </li>
                    <?php endif; ?>
                    <?php if($cancel_urgency): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Iptal Turu</span>
                        <span class="cdg-pd2-info-value">
                            <?php echo $cancel_urgency === 'now' ? 'Hemen Iptal' : 'Periyot Sonunda Iptal'; ?>
                        </span>
                    </li>
                    <?php endif; ?>
                    <?php if($cancel_date): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Talep Tarihi</span>
                        <span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_date($cancel_date), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    </li>
                    <?php endif; ?>
                </ul>

                <?php if($admin_note): ?>
                <div class="cdg-pd2-alert cdg-pd2-alert-info" style="margin-top:14px;">
                    <i class="bi bi-chat-square-text"></i>
                    <div>
                        <strong>Yonetici Notu:</strong><br>
                        <?php echo nl2br(htmlspecialchars($admin_note, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($cancel_status !== 'approved'): ?>
                <div style="margin-top:18px;display:flex;justify-content:center;">
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success" onclick="cdgPd2.removeCancellation()">
                        <i class="bi bi-arrow-counterclockwise"></i> Iptal Talebini Geri Cek
                    </button>
                </div>
                <?php else: ?>
                <div class="cdg-pd2-alert cdg-pd2-alert-danger" style="margin-top:14px;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Talep onaylandı.</strong> Bu hizmet kısa sürede sonlandırılacaktır.
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php else: ?>
        <!-- YENI IPTAL FORMU -->
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
                        <option value="now">Hemen İptal Et</option>
                        <option value="period-ending">Periyot Sonunda İptal Et</option>
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
        <?php endif; ?>
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
    // Subscription detayini yukle (varsa)
    if(document.getElementById('cdg-pd2-subscription-status')) {
        if(typeof cdgPd2 !== 'undefined' && cdgPd2.loadSubscriptionDetail) {
            cdgPd2.loadSubscriptionDetail();
        }
    }
})();

window.cdgPd2 = {
    productId: <?php echo (int)$d_id; ?>,
    controllerUrl: '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',

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

    // Aktif abonelik detaylarini AJAX ile cek (Classic'in subscription_detail operation)
    loadSubscriptionDetail: function(){
        var statusBox = document.getElementById('cdg-pd2-subscription-status');
        if(!statusBox || !window.jQuery) return;
        // Classic format: GET ?operation=subscription_detail
        jQuery.get(this.controllerUrl + '?operation=subscription_detail', function(html){
            if(html && html.trim()) statusBox.innerHTML = html;
        }).fail(function(){
            // Hata durumunda PHP-side'tan render edilmis bilgileri biraz
            statusBox.innerHTML = '<i class="bi bi-info-circle"></i> Otomatik yenileme bilgileri Classic detay endpoint\'inden alinamadi. PHP-side renderdaki bilgiler gecerlidir.';
        });
    },

    // Aboneligi iptal et
    cancelSubscription: function(){
        if(!confirm('Otomatik yenilemeyi iptal etmek istediğinize emin misiniz? İptalden sonra ürününüz manuel olarak yenilenebilir.')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'cancel_subscription', id: this.productId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Abonelik iptal edildi', {timer: 1500});
                    setTimeout(function(){ location.reload(); }, 1500);
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
        var urgency = document.getElementById('cdg-pd2-cancel-type').value;
        var reason = document.getElementById('cdg-pd2-cancel-reason').value;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'canceled_product', id: this.productId, urgency: urgency, reason: reason },
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

    // Mevcut iptal talebini geri cek
    removeCancellation: function(){
        if(!confirm('Iptal talebinizi geri cekmek istediginize emin misiniz? Hizmetiniz aktif kalacaktir.')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { operation: 'remove_cancelled_product', id: this.productId },
            result: function(r){
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Iptal talebi geri cekildi', {timer: 2500});
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

// Yenileme dönemi seçimi (order_renewal) - global function
window.cdgPd2OrderRenewal = function(btn) {
    var sel = document.getElementById('cdg-pd2-renewal-period');
    if(!sel || !sel.value) {
        if(typeof alert_error === 'function') alert_error('Lütfen yenileme dönemi seçin', {timer: 3000});
        return;
    }
    if(typeof MioAjax !== 'function') return;

    var label = sel.options[sel.selectedIndex].text;
    if(!confirm('"' + label + '" yenileme talebi sepete eklenecek. Devam edilsin mi?')) return;

    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

    MioAjax({
        url: cdgPd2.controllerUrl, type: 'post',
        data: { operation: 'order_renewal', id: cdgPd2.productId, period: sel.value },
        result: function(r) {
            btn.disabled = false; btn.innerHTML = orig;
            if(r && r.status === 'successful') {
                if(r.redirect) {
                    if(typeof alert_success === 'function') alert_success('Ödeme sayfasına yönlendiriliyorsunuz...', {timer: 1500});
                    setTimeout(function(){ window.location.href = r.redirect; }, 1200);
                } else {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yenileme talebi oluşturuldu', {timer: 2000});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                }
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
};
</script>

<?php
// === Paket Yükseltme (hosting/server/software/special için ortak) ===
if(in_array($cdg_pd_kind ?? '', ['hosting','server','software','special'])) {
    $upgrade_inc = __DIR__ . DS . 'ac-product-upgrade.php';
    if(file_exists($upgrade_inc)) include $upgrade_inc;
    $addon_inc = __DIR__ . DS . "ac-product-addons.php";
    if(file_exists($addon_inc)) include $addon_inc;
}
