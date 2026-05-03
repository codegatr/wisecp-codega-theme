<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Debug panel artik panel footer'da - tum hesabim sayfalarinda calisir
?>
<?php /**
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
$hoptions = ["datatables"];

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

// === EK BİLGİLER ===
// Kontrol paneli giriş linki (cPanel/Plesk/DirectAdmin)
$d_panel_url   = $options['panel_url'] ?? ($options['cp_url'] ?? ($options['login_url'] ?? ''));
$d_panel_type  = $options['panel_type'] ?? '';
$d_panel_link  = $options['panel_link'] ?? '';

// FTP bilgileri
$d_ftp_info = isset($options['ftp_info']) && is_array($options['ftp_info']) ? $options['ftp_info'] : [];
$d_ftp_raw  = $options['ftp_raw'] ?? '';
$d_ftp_host = $d_ftp_info['host'] ?? ($options['ftp_host'] ?? $d_ip);
$d_ftp_user = $d_ftp_info['username'] ?? ($options['ftp_user'] ?? $d_username);
$d_ftp_port = $d_ftp_info['port'] ?? ($options['ftp_port'] ?? '21');

// DNS sunucuları
$d_dns = isset($options['dns']) && is_array($options['dns']) ? $options['dns'] : [];
if(empty($d_dns)) {
    $tmp_dns = [];
    for($i=1;$i<=4;$i++) {
        if(!empty($options['ns'.$i])) $tmp_dns[] = $options['ns'.$i];
    }
    $d_dns = $tmp_dns;
}

// Server-spesifik (RDP, SSH)
$d_root_pass = $options['root_password'] ?? ($options['password'] ?? '');
$d_ssh_port  = $options['ssh_port'] ?? '22';
$d_rdp_port  = $options['rdp_port'] ?? '3389';
$d_os        = $options['os'] ?? ($options['operating_system'] ?? '');
$d_cpu       = $options['cpu'] ?? ($options['vcpu'] ?? '');
$d_ram       = $options['ram'] ?? ($options['memory'] ?? '');
$d_disk      = $options['disk'] ?? ($options['storage'] ?? '');

// Yenileme tarihi (eğer farklı geliyorsa)
$d_renewal_date = $proanse['renewal_date'] ?? ($proanse['next_due_date'] ?? '');

// Notlar (admin notes - genelde gizli, ama public notes varsa)
$d_notes = $proanse['public_notes'] ?? ($options['notes'] ?? '');

// Custom fields (admin tanımlı özel alanlar)
$d_custom_fields = isset($proanse['custom_fields']) && is_array($proanse['custom_fields']) ? $proanse['custom_fields'] : [];

// Bu hizmete ait faturalar
$d_bills = [];
if(isset($bills) && is_array($bills)) {
    $d_bills = $bills;
} elseif(isset($invoices) && is_array($invoices)) {
    $d_bills = array_filter($invoices, function($inv) use ($d_id) {
        return isset($inv['oid']) && $inv['oid'] == $d_id;
    });
}

// === HOSTING KULLANIM (disk + bandwidth + email + database + ftp + addon kullanım) ===
$d_usage = [];
if(isset($options['usage']) && is_array($options['usage'])) {
    $d_usage = $options['usage'];
} elseif(isset($usage) && is_array($usage)) {
    $d_usage = $usage;
}

// === MODÜL-SPESİFİK BİLGİ BLOKLARI (cPanel/Plesk/DA notları) ===
$d_blocks = [];
if(isset($options['blocks']) && is_array($options['blocks'])) {
    $d_blocks = $options['blocks'];
}

// === MODÜL ÖZELLİKLERİ - hangi action'ları destekliyor ===
$d_module_supports = [
    'changePassword'   => false,
    'addEmail'         => false,
    'deleteEmail'      => false,
    'addForward'       => false,
    'deleteForward'    => false,
    'reboot'           => false,
    'reinstall'        => false,
    'shutdown'         => false,
    'powerOn'          => false,
    'console'          => false,
    'changeRootPass'   => false,
    'getStatus'        => false,
    'getUsage'         => false,
];
if(isset($module_con) && is_object($module_con)) {
    foreach($d_module_supports as $action => $_) {
        $methods = [$action, strtolower($action), 'set' . ucfirst(str_replace('_', '', $action))];
        foreach($methods as $m) {
            if(method_exists($module_con, $m)) { $d_module_supports[$action] = true; break; }
        }
    }
}

// === SUBSCRIPTION (Stripe/PayPal otomatik yenileme aboneliği) ===
$d_subscription = null;
if(isset($subscription) && is_array($subscription) && !empty($subscription)) {
    $d_subscription = $subscription;
}

// === ÜRÜNE ÖZEL BUTONLAR ($buttons array) ===
$d_extra_buttons = [];
if(isset($buttons) && is_array($buttons)) {
    $d_extra_buttons = $buttons;
}

// === MODÜL DİNAMİK SAYFA (m_page - cPanel/Plesk modülünden gelen panel) ===
$d_m_page = $m_page ?? '';
$d_module_panel = $module_panel ?? '';

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
    --p-primary: #2E3B4E;
    --p-success: #10b981;
    --p-warning: #f59e0b;
    --p-danger: #ef4444;
    --p-info: #00D3E5;
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
    padding: 8px 0 28px;
    box-sizing: border-box;
}
.cdg-pd2 *, .cdg-pd2 *::before, .cdg-pd2 *::after { box-sizing: border-box; }
.cdg-pd2 a { text-decoration: none; color: inherit; }
.cdg-pd2-wrap { max-width: 100%; margin: 0; padding: 0; }

/* === PANEL SHELL - Kurumsal kart konteyner === */
.cdg-pd2-shell {
    background: #ffffff;
    border: 1px solid var(--p-border);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.04);
    overflow: hidden;
    margin-bottom: 20px;
}
.cdg-pd2-shell-head {
    padding: 20px 24px;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid var(--p-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.cdg-pd2-shell-head-left {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}
.cdg-pd2-shell-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--p-primary), #485A75);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 24px;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(46,59,78,0.20);
}
.cdg-pd2-shell-title {
    margin: 0 0 3px;
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.cdg-pd2-shell-sub {
    font-size: 13px;
    color: var(--p-muted);
    font-weight: 500;
}
.cdg-pd2-shell-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.cdg-pd2-shell-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #fff;
    border: 1px solid var(--p-border);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--p-text);
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none;
    font-family: inherit;
}
.cdg-pd2-shell-btn:hover {
    border-color: var(--p-primary);
    color: var(--p-primary);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15,23,42,0.06);
}
.cdg-pd2-shell-btn-primary {
    background: linear-gradient(135deg, #1A2332, #485A75);
    border-color: #1A2332;
    color: #fff;
}
.cdg-pd2-shell-btn-primary:hover {
    color: #fff;
    border-color: #1A2332;
    box-shadow: 0 6px 16px rgba(46,59,78,0.30);
}
.cdg-pd2-shell-btn-primary i { color: #fde047; }
.cdg-pd2-shell-body { padding: 24px; }

/* Hizmet durum chip - kurumsal görünüm */
.cdg-pd2-status-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 11px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: 6px;
}
.cdg-pd2-status-chip.active { background: #dcfce7; color: #15803d; }
.cdg-pd2-status-chip.pending { background: #fef3c7; color: #92400e; }
.cdg-pd2-status-chip.suspended { background: #fed7aa; color: #9a3412; }
.cdg-pd2-status-chip.cancelled, .cdg-pd2-status-chip.expired, .cdg-pd2-status-chip.terminated { background: #fee2e2; color: #991b1b; }

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
    background: linear-gradient(135deg, <?php echo $cdg_pd_color; ?> 0%, #00D3E5 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(46,59,78,0.20);
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
    border: 0;
    border-bottom: 1px solid var(--p-border);
    border-radius: 0;
    padding: 0 24px;
    box-shadow: none;
    margin: 0;
    display: flex;
    gap: 2px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}
.cdg-pd2-tabs::-webkit-scrollbar { height: 2px; }
.cdg-pd2-tabs::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
.cdg-pd2-tab {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 13px 18px;
    border-radius: 0;
    font-size: 13px; font-weight: 600;
    color: var(--p-muted);
    cursor: pointer;
    background: transparent;
    border: 0;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    font-family: inherit;
    white-space: nowrap;
    transition: all 0.15s;
}
.cdg-pd2-tab:hover { color: var(--p-text); background: rgba(46,59,78,0.04); }
.cdg-pd2-tab.active {
    color: var(--p-primary);
    border-bottom-color: var(--p-primary);
    background: transparent;
    box-shadow: none;
    font-weight: 700;
}
.cdg-pd2-tab.active i { color: var(--p-primary); }

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

/* === DİREKADMIN ERİŞİM BİLGİLERİ === */
.cdg-pd2-cred {
    display: inline-block;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 6px;
    font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
    font-size: 12.5px;
    font-weight: 600;
    color: #0f172a;
    margin-right: 4px;
}
.cdg-pd2-cred-masked {
    letter-spacing: 2px;
    color: #475569;
}
.cdg-pd2-copy {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    border-radius: 6px;
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s;
    margin-left: 2px;
    padding: 0;
}
.cdg-pd2-copy:hover {
    background: #00D3E5;
    border-color: #00D3E5;
    color: #fff;
}
.cdg-pd2-copy.copied {
    background: #10b981;
    border-color: #10b981;
    color: #fff;
}

/* === PANEL GİRİŞİ KARTI === */
.cdg-pd2-panel-login {
    margin-top: 18px;
    padding: 18px;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
.cdg-pd2-panel-login-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.cdg-pd2-panel-login-head i {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #2E3B4E, #1A2332);
    border-radius: 10px;
    color: #00E5FF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.cdg-pd2-panel-login-head strong {
    display: block;
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.cdg-pd2-panel-login-head span {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-top: 2px;
}
.cdg-pd2-panel-login-url {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 12px;
    font-size: 12.5px;
}
.cdg-pd2-panel-login-url > i {
    color: #94a3b8;
    flex-shrink: 0;
}
.cdg-pd2-panel-login-url code {
    flex: 1;
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
    color: #2E3B4E;
    font-family: inherit;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.cdg-pd2-panel-login-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 13px 20px;
    background: linear-gradient(135deg, #2E3B4E, #1A2332);
    color: #fff !important;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.18s;
    box-shadow: 0 6px 16px rgba(46,59,78,0.25);
}
.cdg-pd2-panel-login-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(0,211,229,0.30);
    background: linear-gradient(135deg, #1A2332, #2E3B4E);
}
.cdg-pd2-panel-login-btn i { color: #00E5FF; font-size: 16px; }
.cdg-pd2-panel-login-note {
    margin: 12px 0 0;
    padding: 10px 12px;
    background: rgba(0,211,229,0.06);
    border-left: 3px solid #00D3E5;
    border-radius: 6px;
    font-size: 12px;
    color: #475569;
    line-height: 1.5;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.cdg-pd2-panel-login-note i { color: #00D3E5; flex-shrink: 0; margin-top: 2px; }

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
.cdg-pd2-badge-info    { background: #CFFAFE; color: #2E3B4E; }

.cdg-pd2-alert {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 13px;
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 14px;
    line-height: 1.5;
}
.cdg-pd2-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.cdg-pd2-alert-info { background: #CFFAFE; color: #1A2332; border: 1px solid #67E8F9; }
.cdg-pd2-alert-info i { color: #2E3B4E; }
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
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    box-shadow: 0 6px 18px rgba(46,59,78,0.22);
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
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
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
/* === HIZLI EYLEMLER BARI === */
.cdg-pd2-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
}
.cdg-pd2-action {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px;
    background: var(--p-card);
    border: 1px solid var(--p-border);
    border-radius: 12px;
    color: var(--p-text);
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
}
.cdg-pd2-action:hover {
    border-color: var(--p-primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(15,23,42,0.08);
}
.cdg-pd2-action i {
    font-size: 22px;
    color: var(--p-primary);
    flex-shrink: 0;
}
.cdg-pd2-action span {
    display: flex; flex-direction: column;
    line-height: 1.2;
    min-width: 0;
}
.cdg-pd2-action strong {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--p-text);
}
.cdg-pd2-action small {
    font-size: 11px;
    color: var(--p-muted);
    margin-top: 2px;
}
.cdg-pd2-action-primary {
    background: linear-gradient(135deg, #1A2332, #485A75);
    border-color: #1A2332;
    color: #fff;
}
.cdg-pd2-action-primary i { color: #fde047; }
.cdg-pd2-action-primary strong { color: #fff; }
.cdg-pd2-action-primary small { color: rgba(255,255,255,0.75); }
.cdg-pd2-action-primary:hover { color: #fff; box-shadow: 0 10px 24px rgba(46,59,78,0.30); }

/* === TABLE === */
.cdg-pd2-table th { font-weight: 700; }
.cdg-pd2-table tr:hover { background: #fafbfd !important; }

/* === EMPTY STATE === */
.cdg-pd2-empty {
    text-align: center;
    padding: 36px 18px;
}
.cdg-pd2-empty i {
    font-size: 42px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 10px;
}
.cdg-pd2-empty h4 {
    margin: 0 0 6px;
    font-size: 15px;
    font-weight: 700;
    color: var(--p-text);
}
.cdg-pd2-empty p {
    margin: 0 0 14px;
    font-size: 13px;
    color: var(--p-muted);
    line-height: 1.5;
}

/* === NOTE BOX === */
.cdg-pd2-note {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12.5px;
    color: var(--p-muted);
    line-height: 1.5;
}
.cdg-pd2-note i {
    color: var(--p-primary);
    margin-right: 4px;
}

/* === BTN-SM, BTN-GHOST === */
.cdg-pd2-btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}
.cdg-pd2-btn-ghost {
    background: transparent;
    border: 1px solid transparent;
    color: var(--p-muted);
}
.cdg-pd2-btn-ghost:hover {
    background: var(--p-bg);
    color: var(--p-primary);
}

/* === ALT-TAB (Transfer pane içinde) === */
.cdg-pd2-subtab {
    background: transparent;
    border: 0;
    border-bottom: 2px solid transparent;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--p-muted);
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.cdg-pd2-subtab:hover { color: var(--p-text); }
.cdg-pd2-subtab.active {
    color: var(--p-primary);
    border-bottom-color: var(--p-primary);
}
.cdg-pd2-subpane { animation: cdgFadeIn 0.2s ease; }
@keyframes cdgFadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

/* === WARNING BUTTON === */
.cdg-pd2-btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    border: 0;
}
.cdg-pd2-btn-warning:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(245,158,11,0.30);
}

/* === ALERT DANGER === */
.cdg-pd2-alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-color: #fca5a5;
    color: #991b1b;
}
.cdg-pd2-alert-danger i { color: #dc2626; }

</style>

<div class="cdg-pd2">
<div class="cdg-pd2-wrap">

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd2-back" data-cdg-action="history-back">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <!-- KURUMSAL PANEL SHELL -->
    <div class="cdg-pd2-shell">
        <div class="cdg-pd2-shell-head">
            <div class="cdg-pd2-shell-head-left">
                <div class="cdg-pd2-shell-icon" style="background:linear-gradient(135deg, <?php echo htmlspecialchars($cdg_pd_color, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>, <?php echo htmlspecialchars($cdg_pd_color, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>cc);">
                    <i class="bi bi-<?php echo htmlspecialchars($cdg_pd_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                </div>
                <div style="min-width:0;">
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;">
                        <h1 class="cdg-pd2-shell-title">
                            <?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </h1>
                        <span class="cdg-pd2-status-chip <?php echo htmlspecialchars($d_status, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            <i class="bi bi-<?php echo htmlspecialchars($st_meta['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                            <?php echo htmlspecialchars($st_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </div>
                    <div class="cdg-pd2-shell-sub">
                        <?php if($d_domain): ?>
                        <i class="bi bi-globe"></i> <strong><?php echo htmlspecialchars($d_domain, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
                        <?php endif; ?>
                        <?php if($d_ip): ?>
                        <span style="margin:0 6px;color:#cbd5e1;">·</span>
                        <i class="bi bi-hdd-network"></i> <?php echo htmlspecialchars($d_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        <?php endif; ?>
                        <?php if($d_duedate): ?>
                        <span style="margin:0 6px;color:#cbd5e1;">·</span>
                        <i class="bi bi-calendar-check"></i> Bitiş: <?php echo htmlspecialchars(cdg_pd_date($d_duedate), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="cdg-pd2-shell-actions">
                <?php
                // DirectAdmin / Plesk / cPanel - kontrol paneli URL'si
                $cp_url_hero = $options['cp_url'] ?? ($options['panel_url'] ?? $d_panel_link);
                // Panel tipi - DirectAdmin önceliği
                $panel_label = 'Kontrol Paneli';
                $panel_brand = '';
                if($d_panel_type) {
                    $pt = strtolower($d_panel_type);
                    if(strpos($pt, 'directadmin') !== false || strpos($pt, 'da') !== false) {
                        $panel_label = 'DirectAdmin';
                        $panel_brand = 'DirectAdmin';
                    } elseif(strpos($pt, 'plesk') !== false) {
                        $panel_label = 'Plesk';
                        $panel_brand = 'Plesk';
                    } elseif(strpos($pt, 'cpanel') !== false) {
                        $panel_label = 'Kontrol Paneli';
                        $panel_brand = 'Kontrol Paneli';
                    } else {
                        $panel_brand = $d_panel_type;
                    }
                } elseif($cdg_pd_kind === 'hosting') {
                    // Default Codega: DirectAdmin
                    $panel_label = 'DirectAdmin';
                    $panel_brand = 'DirectAdmin';
                }
                ?>
                <?php if($cp_url_hero): ?>
                <a href="<?php echo htmlspecialchars($cp_url_hero, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-pd2-shell-btn cdg-pd2-shell-btn-primary">
                    <i class="bi bi-box-arrow-up-right"></i> <?php echo htmlspecialchars($panel_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </a>
                <?php endif; ?>
                <?php if($d_status === 'active' && $d_period && $d_period !== 'none'): ?>
                <button type="button" class="cdg-pd2-shell-btn" data-cdg-action="goto-tab" data-cdg-target="renewal">
                    <i class="bi bi-arrow-clockwise"></i> Yenile
                </button>
                <?php endif; ?>
                <?php if(!empty($upgrades)): ?>
                <button type="button" class="cdg-pd2-shell-btn" data-cdg-action="goto-tab" data-cdg-target="upgrade">
                    <i class="bi bi-arrow-up-circle"></i> Yükselt
                </button>
                <?php endif; ?>
                <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>?subject=%23<?php echo (int)$d_id; ?>+<?php echo urlencode($d_name); ?>" class="cdg-pd2-shell-btn">
                    <i class="bi bi-headset"></i> Destek
                </a>
            </div>
        </div>

    <!-- TAB NAV - shell-head'in hemen altında (yapışık) -->
    <div class="cdg-pd2-tabs">
        <button type="button" class="cdg-pd2-tab active" data-pane="summary"><i class="bi bi-info-circle"></i> Özet</button>
        <?php if($cdg_pd_kind === 'hosting'): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="emails"><i class="bi bi-envelope"></i> E-posta</button>
        <?php endif; ?>
        <?php if(!empty($requirements) && is_array($requirements)): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="requirements"><i class="bi bi-check2-square"></i> Bilgi Formları</button>
        <?php endif; ?>
        <?php if(!empty($addons)): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="addons"><i class="bi bi-rocket-takeoff"></i> Ek Hizmetler</button>
        <?php endif; ?>
        <?php if(!empty($upgrades)): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="upgrade"><i class="bi bi-arrow-up-circle"></i> Yükselt</button>
        <?php endif; ?>
        <?php if($cdg_pd_kind === 'hosting'): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="password"><i class="bi bi-key"></i> Şifre</button>
        <?php endif; ?>
        <button type="button" class="cdg-pd2-tab" data-pane="renewal"><i class="bi bi-arrow-clockwise"></i> Yenileme</button>
        <?php if(!empty($d_bills)): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="bills"><i class="bi bi-receipt"></i> Faturalar (<?php echo count($d_bills); ?>)</button>
        <?php endif; ?>
        <?php if(in_array($cdg_pd_kind, ['hosting','server'])): ?>
        <button type="button" class="cdg-pd2-tab" data-pane="transfer"><i class="bi bi-arrow-left-right"></i> Transfer</button>
        <?php endif; ?>
        <button type="button" class="cdg-pd2-tab" data-pane="cancel"><i class="bi bi-ban"></i> İptal</button>
    </div>

        <div class="cdg-pd2-shell-body">

    <?php if($cdg_pd_kind === 'server' && ($d_module_supports['reboot'] || $d_module_supports['reinstall'] || $d_module_supports['shutdown'] || $d_module_supports['powerOn'] || $d_module_supports['console'])): ?>
    <!-- SERVER AKSİYON PANELİ -->
    <div class="cdg-pd2-card" style="margin-bottom:14px;background:linear-gradient(135deg,#0f172a,#1e293b);border-color:#1e293b;">
        <div class="cdg-pd2-card-head" style="border-bottom:1px solid rgba(255,255,255,0.10);">
            <h3 style="color:#fff;"><i class="bi bi-power" style="color:#fde047;"></i> Sunucu Aksiyonları</h3>
            <span style="font-size:11px;color:rgba(255,255,255,0.60);">Bu işlemler sunucunuzu doğrudan etkiler</span>
        </div>
        <div class="cdg-pd2-card-body" style="padding:16px;">
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <?php if($d_module_supports['reboot']): ?>
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-warning" data-cdg-action="server-action" data-cdg-server-cmd="reboot">
                    <i class="bi bi-arrow-clockwise"></i> Yeniden Başlat
                </button>
                <?php endif; ?>
                <?php if($d_module_supports['shutdown']): ?>
                <button type="button" class="cdg-pd2-btn" style="background:#dc2626;color:#fff;border:0;" data-cdg-action="server-action" data-cdg-server-cmd="shutdown">
                    <i class="bi bi-power"></i> Kapat
                </button>
                <?php endif; ?>
                <?php if($d_module_supports['powerOn']): ?>
                <button type="button" class="cdg-pd2-btn" style="background:#16a34a;color:#fff;border:0;" data-cdg-action="server-action" data-cdg-server-cmd="powerOn">
                    <i class="bi bi-play-circle"></i> Aç
                </button>
                <?php endif; ?>
                <?php if($d_module_supports['reinstall']): ?>
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="background:rgba(255,255,255,0.10);color:#fff;border-color:rgba(255,255,255,0.30);" data-cdg-action="server-action" data-cdg-server-cmd="reinstall">
                    <i class="bi bi-cpu"></i> İşletim Sistemini Yeniden Kur
                </button>
                <?php endif; ?>
                <?php if($d_module_supports['console']): ?>
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="background:rgba(255,255,255,0.10);color:#fde047;border-color:#fde047;" data-cdg-action="pd2-serverConsole" data-cdg-args=''>
                    <i class="bi bi-terminal"></i> Web Konsol (VNC)
                </button>
                <?php endif; ?>
            </div>
            <p style="margin:12px 0 0;font-size:12px;color:rgba(255,255,255,0.60);">
                <i class="bi bi-info-circle"></i> İşlemler doğrudan sunucu sağlayıcısına gönderilir, etkili olması 30 saniye sürebilir.
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB: SUMMARY -->
    <div class="cdg-pd2-pane active" id="cdg-pd2-pane-summary">
        <div class="cdg-pd2-grid-2">
            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-info-circle"></i> Hizmet Bilgileri</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <?php if($d_id): ?>
                        <li><span class="cdg-pd2-info-label">Sipariş No</span><span class="cdg-pd2-info-value">#<?php echo (int)$d_id; ?></span></li>
                        <?php endif; ?>
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
                        <?php
                        // Subscription detail (Classic uyumlu)
                        // - $subscription set + status != cancelled: Aktif subscription, AJAX subscription_detail ile bilgi yuklenir
                        // - $subscription cancelled VEYA YOK + card-storage-module: auto_pay checkbox + stored_cards kontrolu
                        $cdg_subscription = isset($subscription) && is_array($subscription) ? $subscription : null;
                        $cdg_sub_active = ($cdg_subscription && ($cdg_subscription['status'] ?? '') !== 'cancelled');
                        $cdg_stored_cards = isset($stored_cards) && $stored_cards;
                        ?>
                        <?php if($cdg_sub_active): ?>
                        <!-- Aktif subscription: backend'ten yuklenecek -->
                        <li>
                            <span class="cdg-pd2-info-label">Otomatik Ödeme (Subscription)</span>
                            <span class="cdg-pd2-info-value">
                                <span id="subscription_status">
                                    <span style="color:#3b82f6;font-size:12px;"><i class="bi bi-arrow-clockwise" style="animation:spin 1s linear infinite;"></i> Yükleniyor...</span>
                                </span>
                            </span>
                        </li>
                        <script>
                        (function(){
                            var ctrlUrl = '<?php echo htmlspecialchars($links['controller'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
                            if(window.jQuery && ctrlUrl) {
                                jQuery.get(ctrlUrl + '?operation=subscription_detail', function(data){
                                    if(data) jQuery('#subscription_status').html(data);
                                }).fail(function(){
                                    jQuery('#subscription_status').html('<span style="color:#94a3b8;font-size:12px;">Subscription bilgisi alinamadi</span>');
                                });
                            }
                        })();
                        </script>
                        <?php else: ?>
                        <!-- Subscription yok / iptal edilmis - Auto-pay checkbox -->
                        <li>
                            <span class="cdg-pd2-info-label">Otomatik Ödeme</span>
                            <span class="cdg-pd2-info-value">
                                <button type="button" data-cdg-action="pd2-toggleAutoPay" data-cdg-args='<?php echo $cdg_stored_cards ? 'true' : 'false'; ?>' style="background:transparent;border:0;padding:0;cursor:pointer;" title="<?php echo $d_autopay ? 'Otomatik ödemeyi kapat' : 'Otomatik ödemeyi aç'; ?>">
                                <?php if($d_autopay): ?>
                                    <span class="cdg-pd2-badge cdg-pd2-badge-success"><i class="bi bi-check-circle-fill"></i> Açık</span>
                                <?php else: ?>
                                    <span class="cdg-pd2-badge cdg-pd2-badge-info"><i class="bi bi-x-circle"></i> Kapalı</span>
                                <?php endif; ?>
                                </button>
                                <?php if(!$cdg_stored_cards): ?>
                                <small style="display:block;color:#94a3b8;font-size:10px;margin-top:4px;font-weight:600;">
                                    <i class="bi bi-info-circle"></i> Önce bir kart kayıt etmelisiniz
                                </small>
                                <?php endif; ?>
                            </span>
                        </li>
                        <?php endif; ?>
                        <?php if($d_renewal_date && $d_renewal_date !== $d_duedate): ?>
                        <li><span class="cdg-pd2-info-label">Yenileme Tarihi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars(cdg_pd_date($d_renewal_date), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
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
                        <li>
                            <span class="cdg-pd2-info-label">Kullanıcı Adı</span>
                            <span class="cdg-pd2-info-value">
                                <code class="cdg-pd2-cred"><?php echo htmlspecialchars($d_username, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                                <button type="button" class="cdg-pd2-copy" data-cdg-action="copy-cred" data-cdg-text="<?php echo htmlspecialchars($d_username, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" title="Kopyala">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </span>
                        </li>
                        <?php endif; ?>
                        <?php if(!empty($d_root_pass)): ?>
                        <li>
                            <span class="cdg-pd2-info-label">Şifre</span>
                            <span class="cdg-pd2-info-value">
                                <code class="cdg-pd2-cred cdg-pd2-cred-masked" data-pw="<?php echo htmlspecialchars($d_root_pass, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">••••••••</code>
                                <button type="button" class="cdg-pd2-copy cdg-pd2-toggle-pw" data-cdg-action="toggle-pw" title="Göster/Gizle">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="cdg-pd2-copy" data-cdg-action="copy-cred" data-cdg-text="<?php echo htmlspecialchars($d_root_pass, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" title="Kopyala">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </span>
                        </li>
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
                        $cp_url = $options['cp_url'] ?? $options['panel_url'];
                        $panel_lbl = $panel_brand ?? ($d_panel_type ?? 'DirectAdmin');
                        ?>
                    <div class="cdg-pd2-panel-login">
                        <div class="cdg-pd2-panel-login-head">
                            <i class="bi bi-shield-lock-fill"></i>
                            <div>
                                <strong>Panel Girişi</strong>
                                <span><?php echo htmlspecialchars($panel_lbl, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> kontrol paneline güvenli giriş</span>
                            </div>
                        </div>
                        <div class="cdg-pd2-panel-login-url">
                            <i class="bi bi-link-45deg"></i>
                            <code><?php echo htmlspecialchars($cp_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                            <button type="button" class="cdg-pd2-copy" data-cdg-action="copy-cred" data-cdg-text="<?php echo htmlspecialchars($cp_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" title="Kopyala">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <a href="<?php echo htmlspecialchars($cp_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-pd2-panel-login-btn">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span><?php echo htmlspecialchars($panel_lbl, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> Paneline Giriş Yap</span>
                        </a>
                        <p class="cdg-pd2-panel-login-note">
                            <i class="bi bi-info-circle"></i>
                            Yukarıdaki kullanıcı adı ve şifre ile giriş yapabilirsiniz. Güvenliğiniz için panel girişinden sonra şifrenizi değiştirmenizi öneririz.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if(!empty($d_ftp_info) || $d_ftp_raw || (!empty($d_dns) && count($d_dns) > 0)): ?>
        <!-- FTP & DNS BILGILERI -->
        <div class="cdg-pd2-grid-2" style="margin-top:14px;">
            <?php if(!empty($d_ftp_info) || $d_ftp_raw || $d_ftp_host): ?>
            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-folder-symlink"></i> FTP Bilgileri</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <?php if($d_ftp_host): ?>
                        <li><span class="cdg-pd2-info-label">Sunucu</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_ftp_host, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php endif; ?>
                        <?php if($d_ftp_user): ?>
                        <li><span class="cdg-pd2-info-label">Kullanıcı Adı</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_ftp_user, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php endif; ?>
                        <li><span class="cdg-pd2-info-label">Port</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_ftp_port, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php if(!empty($d_ftp_info['protocol'])): ?>
                        <li><span class="cdg-pd2-info-label">Protokol</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_ftp_info['protocol'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php endif; ?>
                    </ul>
                    <?php if($d_ftp_raw): ?>
                    <div class="cdg-pd2-note" style="margin-top:10px;">
                        <i class="bi bi-info-circle"></i> <?php echo nl2br(htmlspecialchars($d_ftp_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($d_dns) && count($d_dns) > 0): ?>
            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-globe"></i> DNS Sunucuları</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <?php foreach($d_dns as $i => $ns): ?>
                        <li>
                            <span class="cdg-pd2-info-label">NS<?php echo ($i+1); ?></span>
                            <span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars(is_array($ns) ? implode(' ', $ns) : (string)$ns, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="cdg-pd2-note" style="margin-top:10px;">
                        <i class="bi bi-info-circle"></i> Domaininizi bu sunuculara yönlendirin (alan adı kayıt firmanızdan).
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if($cdg_pd_kind === 'server'): ?>
        <!-- SERVER ÖZEL BİLGİLER -->
        <div class="cdg-pd2-grid-2" style="margin-top:14px;">
            <?php if($d_os || $d_cpu || $d_ram || $d_disk): ?>
            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-cpu"></i> Sunucu Donanımı</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <?php if($d_os): ?>
                        <li><span class="cdg-pd2-info-label">İşletim Sistemi</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_os, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_cpu): ?>
                        <li><span class="cdg-pd2-info-label">CPU / vCPU</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_cpu, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_ram): ?>
                        <li><span class="cdg-pd2-info-label">RAM</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_ram, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                        <?php if($d_disk): ?>
                        <li><span class="cdg-pd2-info-label">Disk</span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($d_disk, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="cdg-pd2-card">
                <div class="cdg-pd2-card-head">
                    <h3><i class="bi bi-shield-lock"></i> SSH / RDP Erişim</h3>
                </div>
                <div class="cdg-pd2-card-body">
                    <ul class="cdg-pd2-info">
                        <?php if($d_ip): ?>
                        <li><span class="cdg-pd2-info-label">IP / Host</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php endif; ?>
                        <li><span class="cdg-pd2-info-label">SSH Port (Linux)</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_ssh_port, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <li><span class="cdg-pd2-info-label">RDP Port (Windows)</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_rdp_port, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php if($d_username): ?>
                        <li><span class="cdg-pd2-info-label">Kullanıcı</span><span class="cdg-pd2-info-value"><code><?php echo htmlspecialchars($d_username, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></span></li>
                        <?php endif; ?>
                    </ul>
                    <div class="cdg-pd2-note" style="margin-top:10px;">
                        <i class="bi bi-shield-exclamation"></i> Güvenlik nedeniyle root şifresi burada gösterilmez. <strong>"Şifre"</strong> sekmesinden değiştirebilirsiniz.
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($d_custom_fields)): ?>
        <!-- CUSTOM FIELDS -->
        <div class="cdg-pd2-card" style="margin-top:14px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-card-list"></i> Ek Bilgiler</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <ul class="cdg-pd2-info">
                    <?php foreach($d_custom_fields as $cf):
                        $cf_name = is_array($cf) ? ($cf['name'] ?? '') : '';
                        $cf_value = is_array($cf) ? ($cf['value'] ?? '') : (string)$cf;
                        if(!$cf_name || !$cf_value) continue;
                    ?>
                    <li><span class="cdg-pd2-info-label"><?php echo htmlspecialchars($cf_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><span class="cdg-pd2-info-value"><?php echo htmlspecialchars($cf_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if($d_notes): ?>
        <!-- PUBLIC NOTES -->
        <div class="cdg-pd2-card" style="margin-top:14px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-sticky"></i> Notlar</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div class="cdg-pd2-note" style="background:#fef3c7;border-color:#fcd34d;">
                    <?php echo nl2br(htmlspecialchars($d_notes, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:700;color:#2E3B4E;">
                            <?php echo htmlspecialchars($server_hostname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <button type="button" data-cdg-action="copy-text" data-cdg-text="<?php echo htmlspecialchars(addslashes($server_hostname), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="margin-left:6px;background:#f1f5f9;border:0;padding:2px 6px;border-radius:4px;cursor:pointer;font-size:11px;color:#64748b;" title="Kopyala">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </span>
                    </li>
                    <?php endif; ?>
                    <?php if($server_ip): ?>
                    <li>
                        <span class="cdg-pd2-info-label">Sunucu IP</span>
                        <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:700;color:#2E3B4E;">
                            <?php echo htmlspecialchars($server_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <button type="button" data-cdg-action="copy-text" data-cdg-text="<?php echo htmlspecialchars(addslashes($server_ip), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="margin-left:6px;background:#f1f5f9;border:0;padding:2px 6px;border-radius:4px;cursor:pointer;font-size:11px;color:#64748b;" title="Kopyala">
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
                        'disk_limit'      => ['label' => 'Disk Alanı', 'icon' => 'hdd-fill', 'unit' => 'MB', 'color' => '#00D3E5'],
                        'bandwidth_limit' => ['label' => 'Aylık Trafik', 'icon' => 'arrow-down-up', 'unit' => 'MB', 'color' => '#7c3aed'],
                        'email_limit'     => ['label' => 'E-posta Hesabı', 'icon' => 'envelope-fill', 'unit' => 'adet', 'color' => '#f59e0b'],
                        'database_limit'  => ['label' => 'Veritabanı', 'icon' => 'database-fill', 'unit' => 'adet', 'color' => '#10b981'],
                        'addons_limit'    => ['label' => 'Eklenti Domain', 'icon' => 'plus-square-fill', 'unit' => 'adet', 'color' => '#00D3E5'],
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

        <?php
        // === HOSTING KULLANIM BARLARI (disk + bandwidth + email + database + ftp) ===
        if(!empty($d_usage)):
            $usage_items = [];
            // disk
            if(isset($d_usage['disk_used_percent']) || isset($d_usage['disk_used'])) {
                $usage_items[] = [
                    'label' => 'Disk Kullanımı',
                    'icon'  => 'hdd-fill',
                    'color' => '#00D3E5',
                    'percent' => (int)($d_usage['disk_used_percent'] ?? 0),
                    'used'  => $d_usage['disk_used_format'] ?? ($d_usage['disk_used'] ?? '0'),
                    'limit' => $d_usage['disk_limit_format'] ?? ($d_usage['disk_limit'] ?? '0'),
                ];
            }
            // bandwidth
            if(isset($d_usage['bandwidth_used_percent']) || isset($d_usage['bandwidth_used'])) {
                $usage_items[] = [
                    'label' => 'Aylık Trafik',
                    'icon'  => 'arrow-down-up',
                    'color' => '#7c3aed',
                    'percent' => (int)($d_usage['bandwidth_used_percent'] ?? 0),
                    'used'  => $d_usage['bandwidth_used_format'] ?? ($d_usage['bandwidth_used'] ?? '0'),
                    'limit' => $d_usage['bandwidth_limit_format'] ?? ($d_usage['bandwidth_limit'] ?? '0'),
                ];
            }
            // email
            if(isset($d_usage['email_used'])) {
                $email_pct = !empty($d_usage['email_limit']) ? (int)(($d_usage['email_used'] / max(1,$d_usage['email_limit'])) * 100) : 0;
                $usage_items[] = [
                    'label' => 'E-posta Hesapları',
                    'icon'  => 'envelope-fill',
                    'color' => '#f59e0b',
                    'percent' => $email_pct,
                    'used'  => $d_usage['email_used'],
                    'limit' => $d_usage['email_limit'] ?: '∞',
                ];
            }
            // database
            if(isset($d_usage['database_used'])) {
                $db_pct = !empty($d_usage['database_limit']) ? (int)(($d_usage['database_used'] / max(1,$d_usage['database_limit'])) * 100) : 0;
                $usage_items[] = [
                    'label' => 'Veritabanları',
                    'icon'  => 'database-fill',
                    'color' => '#10b981',
                    'percent' => $db_pct,
                    'used'  => $d_usage['database_used'],
                    'limit' => $d_usage['database_limit'] ?: '∞',
                ];
            }
            // ftp
            if(isset($d_usage['ftp_used'])) {
                $ftp_pct = !empty($d_usage['ftp_limit']) ? (int)(($d_usage['ftp_used'] / max(1,$d_usage['ftp_limit'])) * 100) : 0;
                $usage_items[] = [
                    'label' => 'FTP Hesapları',
                    'icon'  => 'cloud-arrow-up-fill',
                    'color' => '#64748b',
                    'percent' => $ftp_pct,
                    'used'  => $d_usage['ftp_used'],
                    'limit' => $d_usage['ftp_limit'] ?: '∞',
                ];
            }
            if(!empty($usage_items)):
        ?>
        <div class="cdg-pd2-card" style="margin-top:14px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-graph-up"></i> Kullanım Durumu</h3>
                <?php if($d_module_supports['getUsage']): ?>
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-ghost cdg-pd2-btn-sm" data-cdg-action="pd2-refreshUsage" data-cdg-args='' title="Yenile">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <?php endif; ?>
            </div>
            <div class="cdg-pd2-card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;">
                    <?php foreach($usage_items as $u):
                        $pct = max(0, min(100, $u['percent']));
                        $bar_color = $pct >= 90 ? '#ef4444' : ($pct >= 75 ? '#f59e0b' : $u['color']);
                    ?>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-<?php echo $u['icon']; ?>" style="color:<?php echo $u['color']; ?>;font-size:16px;"></i>
                                <span style="font-size:13px;font-weight:700;color:#0f172a;"><?php echo htmlspecialchars($u['label'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            </div>
                            <span style="font-size:13px;font-weight:800;color:<?php echo $bar_color; ?>;">%<?php echo $pct; ?></span>
                        </div>
                        <div style="height:8px;background:#e2e8f0;border-radius:100px;overflow:hidden;margin-bottom:6px;">
                            <div style="height:100%;width:<?php echo $pct; ?>%;background:linear-gradient(90deg, <?php echo $bar_color; ?>, <?php echo $bar_color; ?>cc);border-radius:100px;transition:width 0.4s ease;"></div>
                        </div>
                        <div style="font-size:11px;color:#64748b;text-align:right;">
                            <?php echo htmlspecialchars((string)$u['used'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> / <?php echo htmlspecialchars((string)$u['limit'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; endif; ?>

        <?php if(!empty($d_blocks)): ?>
        <!-- MODÜL-SPESİFİK BİLGİ BLOKLARI (cPanel/Plesk/DA notları) -->
        <?php foreach($d_blocks as $block):
            $b_title = $block['title'] ?? '';
            $b_desc  = $block['description'] ?? '';
            $b_icon  = $block['icon'] ?? 'info-square';
            $b_color = $block['color'] ?? '#2E3B4E';
            if(!$b_title && !$b_desc) continue;
        ?>
        <div class="cdg-pd2-card" style="margin-top:14px;border-left:4px solid <?php echo $b_color; ?>;">
            <?php if($b_title): ?>
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-<?php echo $b_icon; ?>" style="color:<?php echo $b_color; ?>;"></i> <?php echo htmlspecialchars($b_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
            </div>
            <?php endif; ?>
            <?php if($b_desc): ?>
            <div class="cdg-pd2-card-body">
                <div style="line-height:1.7;color:#475569;font-size:14px;"><?php echo nl2br($b_desc); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($hosting_dns)): ?>
        <div class="cdg-pd2-card" style="margin-top:18px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-server"></i> Sunucu DNS Bilgileri</h3>
                <span style="font-size:11px;color:#64748b;">Domaininizi bu sunucuya yonlendirmek için bu nameserver'lari kullanin</span>
            </div>
            <div class="cdg-pd2-card-body">
                <ul class="cdg-pd2-info">
                    <?php foreach($hosting_dns as $idx => $ns):
                        if(!$ns) continue;
                    ?>
                    <li style="font-family:'Courier New',monospace;">
                        <span class="cdg-pd2-info-label">NS<?php echo ($idx + 1); ?></span>
                        <span class="cdg-pd2-info-value" style="font-weight:700;color:#2E3B4E;letter-spacing:0.3px;"><?php echo htmlspecialchars($ns, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
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
                            <span style="display:inline-block;padding:3px 10px;background:#eff6ff;color:#2E3B4E;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;">
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
                        <span class="cdg-pd2-info-label">Oluşturulma</span>
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
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success cdg-pd2-btn-sm" data-cdg-action="add-addon" data-cdg-id="<?php echo (int)$a_id; ?>">
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
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary cdg-pd2-btn-sm" data-cdg-action="upgrade" data-cdg-id="<?php echo (int)$u_id; ?>">
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
                    <div>Hosting kontrol paneli (DirectAdmin) şifrenizi buradan değiştirebilirsiniz.</div>
                </div>

                <form id="cdg-pd2-pwd-form" onsubmit="return false;">
                    <div class="cdg-pd2-field">
                        <label class="cdg-pd2-label">Yeni Şifre</label>
                        <div style="display:flex;gap:8px;">
                            <input type="password" id="cdg-pd2-newpass" class="cdg-pd2-input" minlength="8" autocomplete="new-password" style="flex:1;">
                            <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" data-cdg-action="generate-password" title="Güçlü şifre oluştur">
                                <i class="bi bi-arrow-repeat"></i> Otomatik Oluştur
                            </button>
                        </div>
                        <small style="display:block;margin-top:4px;color:#64748b;font-size:11px;">
                            <i class="bi bi-info-circle"></i> En az 8 karakter, harf+rakam+sembol önerilir
                        </small>
                    </div>
                    <div class="cdg-pd2-field">
                        <label class="cdg-pd2-label">Yeni Şifre (Tekrar)</label>
                        <input type="password" id="cdg-pd2-newpass2" class="cdg-pd2-input" minlength="8" autocomplete="new-password">
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;">
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary" data-cdg-action="pd2-changePassword" data-cdg-args=''>
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
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary" data-cdg-action="order-renewal">
                            <i class="bi bi-cart-plus"></i> Sepete Ekle
                        </button>
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-top:6px;">
                        <i class="bi bi-info-circle"></i> Mevcut süre yerine yeni bir dönem seçerek devam edebilirsiniz. Seçim sonrası ödeme sayfasına yönlendirileceksiniz.
                    </div>
                </div>
                <?php endif; ?>

                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success" style="width:100%;justify-content:center;" data-cdg-action="pd2-renew" data-cdg-args=''>
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
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="width:100%;justify-content:center;" data-cdg-action="pd2-cancelSubscription" data-cdg-args=''>
                        <i class="bi bi-x-circle"></i> Aboneliği İptal Et
                    </button>
                    <?php else: ?>
                    <!-- ABONELIK YOK - manuel auto_pay checkbox (stored_cards gerekir) -->
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" style="width:100%;justify-content:center;" data-cdg-action="pd2-toggleAutoPay" data-cdg-args=''>
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
    <!-- E-POSTA YÖNETİMİ -->
    <?php if($cdg_pd_kind === 'hosting'): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-emails">
        <?php
        // Hosting modülünün e-posta yönetimini destekleyip desteklemediği
        $supports_email_create = false;
        $supports_email_delete = false;
        if(isset($module_con) && is_object($module_con)) {
            if(method_exists($module_con, 'createEmail') || method_exists($module_con, 'create_email')) $supports_email_create = true;
            if(method_exists($module_con, 'deleteEmail') || method_exists($module_con, 'delete_email')) $supports_email_delete = true;
        }
        // Domains for email creation (hosting domain + addon domains)
        $email_domains = [];
        if($d_domain) $email_domains[] = $d_domain;
        if(!empty($options['addon_domains']) && is_array($options['addon_domains'])) {
            foreach($options['addon_domains'] as $ad) $email_domains[] = $ad;
        }

        $cur_email_count = $options['used_email_count'] ?? null;
        $email_lim_val = $email_limit ?: ($options['email_limit'] ?? 0);
        ?>

        <!-- E-POSTA KOTASI -->
        <?php if($email_lim_val !== null): ?>
        <div class="cdg-pd2-card" style="margin-bottom:14px;">
            <div class="cdg-pd2-card-body" style="padding:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#CFFAFE,#A5F3FC);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#2E3B4E;flex-shrink:0;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">E-posta Kotanız</div>
                            <div style="font-size:18px;font-weight:800;color:#0f172a;">
                                <?php if($cur_email_count !== null): ?>
                                    <?php echo (int)$cur_email_count; ?> / <?php echo $email_lim_val == 0 ? '∞' : (int)$email_lim_val; ?>
                                <?php else: ?>
                                    <?php echo $email_lim_val == 0 ? 'Sınırsız' : (int)$email_lim_val . ' adet'; ?>
                                <?php endif; ?>
                                <span style="font-size:13px;font-weight:600;color:#64748b;"> hesap</span>
                            </div>
                        </div>
                    </div>
                    <?php if($cp_url_hero): ?>
                    <a href="<?php echo htmlspecialchars($cp_url_hero, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-outline cdg-pd2-btn-sm">
                        <i class="bi bi-box-arrow-up-right"></i> DirectAdmin'de Detaylı Yönet
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- YENİ E-POSTA EKLE -->
        <?php if($supports_email_create && !empty($email_domains)): ?>
        <div class="cdg-pd2-card" style="margin-bottom:14px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-envelope-plus"></i> Yeni E-posta Ekle</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <form id="cdg-pd2-add-email-form" onsubmit="return false;">
                    <div class="cdg-pd2-grid-2" style="gap:12px;">
                        <div class="cdg-pd2-field">
                            <label class="cdg-pd2-label">E-posta Adresi</label>
                            <div style="display:flex;align-items:stretch;gap:0;">
                                <input type="text" id="cdg-pd2-email-prefix" class="cdg-pd2-input" placeholder="ornek" style="border-radius:10px 0 0 10px;border-right:0;">
                                <span style="display:flex;align-items:center;padding:0 10px;background:#f8fafc;border:1px solid var(--p-border);font-size:14px;font-weight:600;color:#64748b;">@</span>
                                <select id="cdg-pd2-email-domain" class="cdg-pd2-input" style="border-radius:0 10px 10px 0;border-left:0;flex:1;">
                                    <?php foreach($email_domains as $ed): ?>
                                    <option value="<?php echo htmlspecialchars($ed, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($ed, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="cdg-pd2-field">
                            <label class="cdg-pd2-label">Şifre</label>
                            <div style="display:flex;gap:6px;">
                                <input type="text" id="cdg-pd2-email-pass" class="cdg-pd2-input" minlength="8" placeholder="En az 8 karakter" style="flex:1;">
                                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline cdg-pd2-btn-sm" data-cdg-action="pd2-generatePassword" data-cdg-args=''cdg-pd2-email-pass'' title="Otomatik">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="cdg-pd2-grid-2" style="gap:12px;align-items:flex-end;">
                        <div class="cdg-pd2-field">
                            <label class="cdg-pd2-label">Kota (MB)</label>
                            <input type="number" id="cdg-pd2-email-quota" class="cdg-pd2-input" placeholder="250" min="0">
                        </div>
                        <div class="cdg-pd2-field">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#475569;">
                                <input type="checkbox" id="cdg-pd2-email-unlimited" style="width:18px;height:18px;">
                                <span><i class="bi bi-infinity"></i> Sınırsız kota</span>
                            </label>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:8px;">
                        <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary" data-cdg-action="pd2-addEmail" data-cdg-args=''>
                            <i class="bi bi-plus-circle"></i> E-posta Hesabı Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- MEVCUT E-POSTA HESAPLARI -->
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-list-ul"></i> Mevcut E-posta Hesapları</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <?php if(isset($mailaccs) && is_array($mailaccs) && !empty($mailaccs)): ?>
                <table class="cdg-pd2-table" style="width:100%;border-collapse:separate;border-spacing:0 4px;">
                    <thead>
                        <tr style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">
                            <th style="text-align:left;padding:8px 12px;">E-posta Adresi</th>
                            <th style="text-align:right;padding:8px 12px;">Kota</th>
                            <th style="width:100px;text-align:center;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mailaccs as $em):
                            $em_addr = is_array($em) ? ($em['email'] ?? ($em['address'] ?? '-')) : $em;
                            $em_quota = is_array($em) ? ($em['quota'] ?? 0) : 0;
                            $em_used  = is_array($em) ? ($em['used'] ?? 0) : 0;
                        ?>
                        <tr style="background:#fff;">
                            <td style="padding:10px 12px;border-radius:8px 0 0 8px;border:1px solid #e2e8f0;border-right:0;">
                                <i class="bi bi-envelope" style="color:#2E3B4E;margin-right:6px;"></i>
                                <code style="font-size:13px;"><?php echo htmlspecialchars($em_addr, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                            </td>
                            <td style="padding:10px 12px;text-align:right;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">
                                <?php if($em_used && $em_quota): ?>
                                    <?php echo (int)$em_used; ?> / <?php echo (int)$em_quota; ?> MB
                                <?php elseif($em_quota): ?>
                                    <?php echo (int)$em_quota; ?> MB
                                <?php else: ?>
                                    <i class="bi bi-infinity"></i> Sınırsız
                                <?php endif; ?>
                            </td>
                            <td style="padding:10px 12px;border-radius:0 8px 8px 0;border:1px solid #e2e8f0;border-left:0;text-align:right;">
                                <?php if($cp_url_hero): ?>
                                <a href="<?php echo htmlspecialchars($cp_url_hero, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-ghost cdg-pd2-btn-sm" title="DirectAdmin'de yönet">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                <?php if($supports_email_delete): ?>
                                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-ghost cdg-pd2-btn-sm" data-cdg-action="delete-email" data-cdg-email="<?php echo htmlspecialchars($em_addr, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" title="Sil" style="color:#dc2626;">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="cdg-pd2-empty">
                    <i class="bi bi-envelope-paper"></i>
                    <h4>Henüz E-posta Hesabınız Yok</h4>
                    <?php if($supports_email_create): ?>
                    <p>Yukarıdaki formu kullanarak yeni e-posta hesabı oluşturabilirsiniz.</p>
                    <?php else: ?>
                    <p>E-posta hesaplarınızı görüntülemek ve yönetmek için <strong>Kontrol Paneli</strong> üzerinden işlem yapın.</p>
                    <?php if($cp_url_hero): ?>
                    <a href="<?php echo htmlspecialchars($cp_url_hero, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-primary">
                        <i class="bi bi-box-arrow-up-right"></i> DirectAdmin Paneline Git
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- E-POSTA YÖNLENDİRMELERİ -->
        <?php if(isset($forwards) && is_array($forwards) && !empty($forwards)): ?>
        <div class="cdg-pd2-card" style="margin-top:14px;">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-arrow-right-circle"></i> E-posta Yönlendirmeleri (<?php echo count($forwards); ?>)</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <table class="cdg-pd2-table" style="width:100%;border-collapse:separate;border-spacing:0 4px;">
                    <thead>
                        <tr style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">
                            <th style="text-align:left;padding:8px 12px;">Kaynak</th>
                            <th style="padding:8px 12px;"></th>
                            <th style="text-align:left;padding:8px 12px;">Hedef</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($forwards as $fw):
                            $fw_src = is_array($fw) ? ($fw['dest'] ?? '') : '';
                            $fw_dst = is_array($fw) ? ($fw['forward'] ?? '') : '';
                        ?>
                        <tr style="background:#fff;">
                            <td style="padding:10px 12px;border-radius:8px 0 0 8px;border:1px solid #e2e8f0;border-right:0;">
                                <code style="font-size:12px;"><?php echo htmlspecialchars($fw_src, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                            </td>
                            <td style="padding:10px 8px;text-align:center;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;color:#94a3b8;">
                                <i class="bi bi-arrow-right"></i>
                            </td>
                            <td style="padding:10px 12px;border-radius:0 8px 8px 0;border:1px solid #e2e8f0;border-left:0;">
                                <code style="font-size:12px;"><?php echo htmlspecialchars($fw_dst, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- GEREKSINIMLER (REQUIREMENTS) -->
    <?php if(!empty($requirements) && is_array($requirements)): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-requirements">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-check2-square"></i> Bilgi Formları</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <div class="cdg-pd2-alert cdg-pd2-alert-info" style="margin-bottom:14px;">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>Aşağıdaki bilgileri sipariş esnasında siz tarafınızdan girilmiş veya bizim tarafımızdan size gönderilmiştir.</div>
                </div>
                <ul class="cdg-pd2-info">
                    <?php foreach($requirements as $req):
                        $req_name = $req['requirement_name'] ?? ($req['name'] ?? '');
                        $req_response = $req['response'] ?? '';
                        $req_type = $req['response_type'] ?? 'text';
                        $req_id = $req['id'] ?? 0;

                        // Decode JSON for select/radio/checkbox/file
                        if(in_array($req_type, ['select', 'radio', 'checkbox', 'file']) && is_string($req_response)) {
                            if(class_exists('Utility') && method_exists('Utility','jdecode')) {
                                try { $req_response_decoded = Utility::jdecode($req_response, true); } catch(\Throwable $e) { $req_response_decoded = json_decode($req_response, true); }
                            } else {
                                $req_response_decoded = json_decode($req_response, true);
                            }
                            if(!is_array($req_response_decoded)) $req_response_decoded = [];
                        }
                    ?>
                    <li>
                        <span class="cdg-pd2-info-label"><?php echo htmlspecialchars($req_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                        <span class="cdg-pd2-info-value">
                            <?php if($req_type === 'file' && !empty($req_response_decoded)): ?>
                                <?php foreach($req_response_decoded as $k => $f):
                                    $file_name = is_array($f) ? ($f['file_name'] ?? 'dosya') : $f;
                                    $dl_link = $controller_url . '?operation=requirement-file-download&rid=' . (int)$req_id . '&key=' . htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                ?>
                                <a href="<?php echo $dl_link; ?>" target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-outline cdg-pd2-btn-sm" style="margin-right:6px;">
                                    <i class="bi bi-file-earmark-arrow-down"></i> <?php echo htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                </a>
                                <?php endforeach; ?>
                            <?php elseif(in_array($req_type, ['select', 'radio']) && !empty($req_response_decoded)): ?>
                                <?php echo htmlspecialchars(is_array($req_response_decoded) ? ($req_response_decoded[0] ?? '') : $req_response_decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <?php elseif($req_type === 'checkbox' && !empty($req_response_decoded)): ?>
                                <?php echo htmlspecialchars(implode(', ', (array)$req_response_decoded), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <?php else: ?>
                                <?php echo nl2br(htmlspecialchars((string)$req_response, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- FATURALAR -->
    <?php if(!empty($d_bills)): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-bills">
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-receipt"></i> Bu Hizmete Ait Faturalar</h3>
                <a href="<?php echo cdg_link('invoices'); ?>" class="cdg-pd2-btn cdg-pd2-btn-ghost cdg-pd2-btn-sm">
                    Tüm Faturalar <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="cdg-pd2-card-body">
                <table class="cdg-pd2-table" style="width:100%;border-collapse:separate;border-spacing:0 4px;">
                    <thead>
                        <tr style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">
                            <th style="text-align:left;padding:8px 12px;">Fatura No</th>
                            <th style="text-align:left;padding:8px 12px;">Tarih</th>
                            <th style="text-align:right;padding:8px 12px;">Tutar</th>
                            <th style="text-align:center;padding:8px 12px;">Durum</th>
                            <th style="width:120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bill_status_meta = [
                            'paid'      => ['cls' => 'success', 'lbl' => 'Ödendi',     'icon' => 'check-circle-fill'],
                            'unpaid'    => ['cls' => 'warning', 'lbl' => 'Bekliyor',   'icon' => 'hourglass-split'],
                            'overdue'   => ['cls' => 'danger',  'lbl' => 'Gecikmiş',   'icon' => 'exclamation-circle-fill'],
                            'cancelled' => ['cls' => 'info',    'lbl' => 'İptal',      'icon' => 'x-circle'],
                        ];
                        foreach(array_slice($d_bills, 0, 10) as $bill):
                            $b_id = $bill['id'] ?? '';
                            $b_status = strtolower($bill['status'] ?? 'unpaid');
                            $b_meta = $bill_status_meta[$b_status] ?? ['cls' => 'info', 'lbl' => ucfirst($b_status), 'icon' => 'circle'];
                            $b_date = $bill['cdate'] ?? ($bill['date'] ?? '');
                            $b_amount = $bill['amount'] ?? 0;
                            $b_amount_cid = $bill['amount_cid'] ?? $d_amount_cid;
                            $b_link = $bill['detail_link'] ?? '#';
                            $b_pay_link = $bill['pay_link'] ?? '#';
                        ?>
                        <tr style="background:#fff;">
                            <td style="padding:10px 12px;border-radius:8px 0 0 8px;border:1px solid #e2e8f0;border-right:0;">
                                <code style="color:#2E3B4E;font-weight:700;">#<?php echo htmlspecialchars((string)$b_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
                            </td>
                            <td style="padding:10px 12px;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">
                                <?php echo htmlspecialchars(cdg_pd_date($b_date), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </td>
                            <td style="padding:10px 12px;text-align:right;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;font-weight:700;color:#0f172a;">
                                <?php echo htmlspecialchars(cdg_pd_money($b_amount, $b_amount_cid), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </td>
                            <td style="padding:10px 12px;text-align:center;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
                                <span class="cdg-pd2-badge cdg-pd2-badge-<?php echo $b_meta['cls']; ?>">
                                    <i class="bi bi-<?php echo $b_meta['icon']; ?>"></i> <?php echo $b_meta['lbl']; ?>
                                </span>
                            </td>
                            <td style="padding:10px 12px;border-radius:0 8px 8px 0;border:1px solid #e2e8f0;border-left:0;text-align:right;">
                                <?php if($b_status === 'unpaid' || $b_status === 'overdue'): ?>
                                <a href="<?php echo htmlspecialchars($b_pay_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd2-btn cdg-pd2-btn-primary cdg-pd2-btn-sm">
                                    <i class="bi bi-credit-card"></i> Öde
                                </a>
                                <?php else: ?>
                                <a href="<?php echo htmlspecialchars($b_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pd2-btn cdg-pd2-btn-ghost cdg-pd2-btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if(count($d_bills) > 10): ?>
                <div style="text-align:center;margin-top:14px;">
                    <a href="<?php echo cdg_link('invoices'); ?>" class="cdg-pd2-btn cdg-pd2-btn-outline cdg-pd2-btn-sm">
                        <?php echo (count($d_bills) - 10); ?> fatura daha → Tümünü Gör
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TRANSFER -->
    <?php if(in_array($cdg_pd_kind, ['hosting','server'])): ?>
    <div class="cdg-pd2-pane" id="cdg-pd2-pane-transfer">
        <?php
        $ctoc_lim_active = isset($ctoc_limit) && strlen((string)$ctoc_limit) > 0;
        $ctoc_used_val = $ctoc_used ?? 0;
        $ctoc_expired = $ctoc_has_expired ?? false;
        ?>
        <div class="cdg-pd2-card">
            <div class="cdg-pd2-card-head">
                <h3><i class="bi bi-arrow-left-right"></i> Hizmet Taşıma & Devir</h3>
            </div>
            <div class="cdg-pd2-card-body">
                <!-- ALT-TAB NAV -->
                <div style="display:flex;gap:6px;border-bottom:1px solid #e2e8f0;margin-bottom:16px;flex-wrap:wrap;">
                    <button type="button" class="cdg-pd2-subtab active" data-subtab="server-migration" data-cdg-action="sub-tab" data-cdg-target="server-migration">
                        <i class="bi bi-cloud-arrow-up"></i> Sunucu Taşıma (Eski → Codega)
                    </button>
                    <button type="button" class="cdg-pd2-subtab" data-subtab="account-transfer" data-cdg-action="sub-tab" data-cdg-target="account-transfer">
                        <i class="bi bi-person-check"></i> Hesap Devri (Müşteriye)
                    </button>
                </div>

                <!-- ALT-TAB 1: Sunucu Taşıma -->
                <div id="cdg-pd2-subtab-server-migration" class="cdg-pd2-subpane">
                    <div class="cdg-pd2-alert cdg-pd2-alert-info" style="margin-bottom:14px;">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            <strong>Ücretsiz Taşıma Hizmeti</strong><br>
                            Mevcut hosting/sunucu sağlayıcınızdan <strong>5 web sitesine kadar</strong> ücretsiz taşıma yapıyoruz. Standart taşıma süresi <strong>1-24 saat</strong> arasındadır.
                        </div>
                    </div>
                    <h4 style="margin:16px 0 10px;font-size:13px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">Bizden istediğiniz şeyler:</h4>
                    <ul style="margin:0 0 16px;padding-left:22px;color:#475569;line-height:1.8;font-size:13.5px;">
                        <li>Eski hosting/sunucu erişim bilgileri (DirectAdmin/Plesk URL, kullanıcı adı, şifre)</li>
                        <li>Veya FTP + veritabanı (MySQL) erişim bilgileri</li>
                        <li>Domain'in NS sunucularını <strong><?php echo !empty($d_dns) ? htmlspecialchars(is_array($d_dns[0]) ? implode(' ', $d_dns[0]) : $d_dns[0], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'CODEGA NS'; ?></strong> olarak güncelleme erişiminiz</li>
                    </ul>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>?subject=Sunucu+Tasima+Talebi+%23<?php echo (int)$d_id; ?>" class="cdg-pd2-btn cdg-pd2-btn-primary">
                            <i class="bi bi-send"></i> Taşıma Talebi Oluştur
                        </a>
                        <a href="https://wa.me/908508850707?text=Merhaba%2C+%23<?php echo (int)$d_id; ?>+numarali+hizmetimi+tasitmak+istiyorum." target="_blank" rel="noopener" class="cdg-pd2-btn cdg-pd2-btn-outline">
                            <i class="bi bi-whatsapp"></i> WhatsApp ile İletişim
                        </a>
                    </div>
                </div>

                <!-- ALT-TAB 2: Hesap Devri -->
                <div id="cdg-pd2-subtab-account-transfer" class="cdg-pd2-subpane" style="display:none;">
                    <div class="cdg-pd2-alert cdg-pd2-alert-warning" style="margin-bottom:14px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Bu hizmeti başka bir CODEGA müşterisine devretmek üzeresiniz.</strong><br>
                            Devir tamamlandığında bu hizmet artık sizin hesabınızda görünmeyecek. Bu işlem geri alınamaz.
                        </div>
                    </div>

                    <?php if($ctoc_lim_active): ?>
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:14px;display:inline-flex;align-items:center;gap:10px;">
                        <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>
                        <div style="font-size:13px;color:#15803d;">
                            <strong>Devir Hakkı:</strong> <?php echo (int)($ctoc_limit - $ctoc_used_val); ?> / <?php echo (int)$ctoc_limit; ?> kalan
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!$ctoc_expired): ?>
                    <form id="cdg-pd2-transfer-form" onsubmit="return false;">
                        <div class="cdg-pd2-grid-2" style="gap:12px;">
                            <div class="cdg-pd2-field">
                                <label class="cdg-pd2-label">Devredilecek Kullanıcının E-postası</label>
                                <input type="email" id="cdg-pd2-transfer-email" class="cdg-pd2-input" placeholder="alici@example.com" required>
                            </div>
                            <div class="cdg-pd2-field">
                                <label class="cdg-pd2-label">Hesap Şifreniz</label>
                                <input type="password" id="cdg-pd2-transfer-pass" class="cdg-pd2-input" placeholder="Doğrulama için şifrenizi girin" required>
                            </div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:8px;">
                            <button type="button" class="cdg-pd2-btn cdg-pd2-btn-warning" data-cdg-action="pd2-transferService" data-cdg-args=''>
                                <i class="bi bi-send-arrow-up"></i> Devir Talebi Gönder
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="cdg-pd2-alert cdg-pd2-alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        <div>Devir hakkınız bulunmuyor. Detay için destek ekibimizle iletişime geçin.</div>
                    </div>
                    <?php endif; ?>

                    <?php if(isset($ctoc_s_t_list) && is_array($ctoc_s_t_list) && !empty($ctoc_s_t_list)): ?>
                    <h4 style="margin:24px 0 10px;font-size:13px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">
                        <i class="bi bi-hourglass-split"></i> Bekleyen Devir Talepleri
                    </h4>
                    <table class="cdg-pd2-table" style="width:100%;border-collapse:separate;border-spacing:0 4px;">
                        <thead>
                            <tr style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">
                                <th style="text-align:left;padding:8px 12px;">Alıcı</th>
                                <th style="text-align:left;padding:8px 12px;">E-posta</th>
                                <th style="text-align:left;padding:8px 12px;">Tarih</th>
                                <th style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ctoc_s_t_list as $tr):
                                $tr_id = $tr['id'] ?? 0;
                                $tr_data = $tr['data'] ?? [];
                                if(is_string($tr_data)) {
                                    if(class_exists('Utility')) { try { $tr_data = Utility::jdecode($tr_data, true); } catch(\Throwable $e) { $tr_data = json_decode($tr_data, true); } }
                                    else $tr_data = json_decode($tr_data, true);
                                }
                                $tr_name = $tr_data['to_full_name'] ?? '-';
                                $tr_email = $tr_data['to_email'] ?? '-';
                                $tr_date = $tr['cdate'] ?? '';
                                if(mb_strlen($tr_name) > 2) $tr_name = mb_substr($tr_name, 0, 2) . str_repeat('*', max(2, mb_strlen($tr_name) - 2));
                            ?>
                            <tr id="cdg-pd2-tr-<?php echo (int)$tr_id; ?>" style="background:#fff;">
                                <td style="padding:10px 12px;border-radius:8px 0 0 8px;border:1px solid #e2e8f0;border-right:0;font-size:13px;"><?php echo htmlspecialchars($tr_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                                <td style="padding:10px 12px;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;font-size:13px;"><code><?php echo htmlspecialchars($tr_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code></td>
                                <td style="padding:10px 12px;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;font-size:12px;color:#64748b;"><?php echo cdg_pd_date($tr_date); ?></td>
                                <td style="padding:10px 12px;border-radius:0 8px 8px 0;border:1px solid #e2e8f0;border-left:0;text-align:center;">
                                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-ghost cdg-pd2-btn-sm" data-cdg-action="remove-transfer" data-cdg-id="<?php echo (int)$tr_id; ?>" title="Talebi iptal et" style="color:#dc2626;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-success" data-cdg-action="pd2-removeCancellation" data-cdg-args=''>
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
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-danger" data-cdg-action="pd2-cancel" data-cdg-args=''>
                        <i class="bi bi-x-circle"></i> İptal Talebi Gönder
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

        </div><!-- /cdg-pd2-shell-body -->
    </div><!-- /cdg-pd2-shell -->

</div>
</div>

<script>
// === CDGPD2 - TAB SWITCH (inline onclick için) ===
window.cdgPd2Switch = function(btn, pane){
    if(!pane) return;
    try {
        // Tüm tab'lardan active'i kaldır
        document.querySelectorAll('.cdg-pd2-tab').forEach(function(t){ t.classList.remove('active'); });
        // Tıklanan tab'ı aktif yap
        if(btn) btn.classList.add('active');
        // Tüm pane'lerden active'i kaldır
        document.querySelectorAll('.cdg-pd2-pane').forEach(function(p){ p.classList.remove('active'); });
        // Hedef pane'i aktif yap
        var target = document.getElementById('cdg-pd2-pane-' + pane);
        if(target) target.classList.add('active');
        // URL hash güncelle
        try { history.replaceState(null, '', '#' + pane); } catch(e) {}
        // Custom event dispatch (eklenti kodu varsa dinlesin)
        try { document.dispatchEvent(new CustomEvent('cdgPd2TabChanged', { detail: { pane: pane } })); } catch(e) {}
    } catch(e) {
        console.error('[cdgPd2] Tab switch error:', e);
    }
};

// === DİREKADMIN ERİŞİM BİLGİLERİ - JS Helpers ===
window.cdgCopyCred = function(btn, text){
    if(!text) return;
    var done = function(){
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i>';
        btn.classList.add('copied');
        setTimeout(function(){
            btn.innerHTML = orig;
            btn.classList.remove('copied');
        }, 1400);
    };
    if(navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(function(){
            // Fallback
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); } catch(e) {}
            document.body.removeChild(ta);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); } catch(e) {}
        document.body.removeChild(ta);
    }
};
window.cdgTogglePw = function(btn){
    var code = btn.previousElementSibling;
    if(!code || !code.dataset.pw) return;
    var icon = btn.querySelector('i');
    if(code.classList.contains('cdg-pd2-cred-masked')) {
        code.textContent = code.dataset.pw;
        code.classList.remove('cdg-pd2-cred-masked');
        if(icon) { icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
    } else {
        code.textContent = '••••••••';
        code.classList.add('cdg-pd2-cred-masked');
        if(icon) { icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
    }
};
</script>
<script>
(function(){
    // === TAB TIKLAMASI - Global event delegation (DOM ready bekleme yok) ===
    document.addEventListener('click', function(ev){
        var tab = ev.target.closest('.cdg-pd2-tab');
        if(!tab) return;
        ev.preventDefault();
        var pane = tab.getAttribute('data-pane');
        if(!pane) return;
        try {
            document.querySelectorAll('.cdg-pd2-tab').forEach(function(t){ t.classList.remove('active'); });
            tab.classList.add('active');
            document.querySelectorAll('.cdg-pd2-pane').forEach(function(p){ p.classList.remove('active'); });
            var target = document.getElementById('cdg-pd2-pane-' + pane);
            if(target) target.classList.add('active');
            try { history.replaceState(null, '', '#' + pane); } catch(e) {}
        } catch(e) {
            console.error('[cdgPd2] tab click error:', e);
        }
    });

    // Hash'le geldiyse o tab'ı aktifleştir
    function activateHashTab() {
        if(!location.hash) return;
        var hash = location.hash.substring(1);
        var tab = document.querySelector('.cdg-pd2-tab[data-pane="' + hash + '"]');
        if(tab) tab.click();
    }
    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', activateHashTab);
    } else {
        activateHashTab();
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
    _ready: true,
    _version: '3.5.40',

    // === Toast Notification (kendi fallback'imiz) ===
    _toast: function(msg, type){
        // WiseCP varsa kullan
        if(type === 'success' && typeof alert_success === 'function') return alert_success(msg, {timer: 2500});
        if(type === 'error' && typeof alert_error === 'function')   return alert_error(msg, {timer: 4000});
        if(type === 'info' && typeof alert_info === 'function')     return alert_info(msg, {timer: 2500});

        // Fallback: kendi toast'umuz
        var t = document.createElement('div');
        var clr = type === 'error' ? '#dc2626' : (type === 'success' ? '#10b981' : '#2E3B4E');
        t.style.cssText = 'position:fixed;top:20px;right:20px;background:'+clr+';color:#fff;padding:14px 20px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.20);z-index:99999;font-size:14px;font-weight:600;max-width:380px;animation:cdgToastIn 0.3s ease;';
        t.innerHTML = '<i class="bi bi-'+(type==='error'?'exclamation-circle':(type==='success'?'check-circle':'info-circle'))+'"></i> '+msg;
        document.body.appendChild(t);
        setTimeout(function(){ t.style.opacity = '0'; t.style.transform = 'translateX(20px)'; t.style.transition = 'all 0.3s'; }, 3000);
        setTimeout(function(){ t.remove(); }, 3400);
    },

    // === HTTP POST (MioAjax varsa onu, yoksa fetch) ===
    _post: function(data, onSuccess, onError){
        var self = this;
        // MioAjax varsa kullan (Classic uyumlu)
        if(typeof MioAjax === 'function') {
            MioAjax({
                url: this.controllerUrl,
                type: 'post',
                data: data,
                result: function(r){
                    if(typeof r === 'string') {
                        try { r = JSON.parse(r); } catch(e) {}
                    }
                    if(r && r.status === 'successful') {
                        if(onSuccess) onSuccess(r);
                        else { self._toast(r.message || 'İşlem başarılı', 'success'); setTimeout(function(){ location.reload(); }, 1500); }
                    } else {
                        if(onError) onError(r);
                        else self._toast((r && r.message) ? r.message : 'Bir hata oluştu', 'error');
                    }
                }
            });
            return;
        }
        // Fallback: fetch API
        var fd = new FormData();
        Object.keys(data).forEach(function(k){ fd.append(k, data[k]); });
        fetch(this.controllerUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(txt){
                var r = null;
                try { r = JSON.parse(txt); } catch(e) {}
                if(r && r.status === 'successful') {
                    if(onSuccess) onSuccess(r);
                    else { self._toast(r.message || 'İşlem başarılı', 'success'); setTimeout(function(){ location.reload(); }, 1500); }
                } else {
                    if(onError) onError(r || {message: 'Sunucu yanıtı işlenemedi'});
                    else self._toast((r && r.message) ? r.message : 'Bir hata oluştu', 'error');
                }
            })
            .catch(function(err){
                if(onError) onError({message: 'Bağlantı hatası: ' + err.message});
                else self._toast('Bağlantı hatası', 'error');
            });
    },

    renew: function(){
        if(!confirm('Yenileme faturası oluşturulacak. Devam edilsin mi?')) return;
        var self = this;
        this._post({ operation: 'product_renewal', id: this.productId }, function(r){
            self._toast(r.message || 'Yenileme talebi oluşturuldu', 'success');
            setTimeout(function(){ if(r.redirect) location.href = r.redirect; else location.reload(); }, 1500);
        });
    },

    // Classic format: operation, status (NO 'id'!)
    // hasStoredCards: kart kayitli mi - frontend'te kontrol edilmeli
    toggleAutoPay: function(hasStoredCards){
        var self = this;
        // Yeni status'u tespit et (mevcut: badge "Açık" mi/Kapalı mi)
        var btn = event && event.currentTarget ? event.currentTarget : null;
        var currentlyOn = btn ? btn.querySelector('.cdg-pd2-badge-success') !== null : false;
        var newStatus = currentlyOn ? 0 : 1;

        // Aktif etme isteniyor ve kart yok ise uyari ver
        if(newStatus === 1 && hasStoredCards === false) {
            self._toast('Otomatik ödeme için önce bir kart kayıt etmelisiniz. Hesap > Kayıtlı Kartlar bölümünü kontrol edin.', 'error');
            return;
        }

        this._post({ operation: 'set_auto_pay_status', status: newStatus }, function(r){
            self._toast(r.message || 'Otomatik ödeme güncellendi', 'success');
            setTimeout(function(){ location.reload(); }, 1200);
        });
    },

    cancelSubscription: function(){
        if(!confirm('Otomatik yenilemeyi iptal etmek istediğinize emin misiniz? İptalden sonra ürününüz manuel olarak yenilenebilir.')) return;
        var self = this;
        // Classic format: sadece operation (NO 'id'!)
        this._post({ operation: 'cancel_subscription' }, function(r){
            self._toast(r.message || 'Subscription iptal edildi', 'success');
            setTimeout(function(){ window.location.href = self.controllerUrl; }, 1200);
        });
    },

    // Aktif abonelik detayları (Classic'in subscription_detail operation)
    loadSubscriptionDetail: function(){
        var statusBox = document.getElementById('cdg-pd2-subscription-status');
        if(!statusBox) return;
        fetch(this.controllerUrl + '?operation=subscription_detail', { credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(html){ if(html && html.trim()) statusBox.innerHTML = html; })
            .catch(function(){ /* sessizce yut */ });
    },

    // Hosting kontrol paneli şifresi değiştir
    changePassword: function(){
        var p1 = document.getElementById('cdg-pd2-newpass').value;
        var p2 = document.getElementById('cdg-pd2-newpass2').value;
        if(!p1 || p1.length < 8) return this._toast('Şifre en az 8 karakter olmalı', 'error');
        if(p1 !== p2) return this._toast('Şifreler eşleşmiyor', 'error');
        var self = this;
        this._post({ operation: 'change_hosting_password', id: this.productId, password: p1, password2: p2 }, function(r){
            self._toast(r.message || 'Şifre değiştirildi', 'success');
            document.getElementById('cdg-pd2-newpass').value = '';
            document.getElementById('cdg-pd2-newpass2').value = '';
        });
    },

    // Random şifre oluştur
    generatePassword: function(targetId){
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#@!*';
        var pwd = '';
        for(var i = 0; i < 14; i++) pwd += chars[Math.floor(Math.random() * chars.length)];
        var el = document.getElementById(targetId);
        if(el) {
            el.type = 'text';
            el.value = pwd;
            this._toast('Yeni şifre oluşturuldu — kopyalamayı unutmayın!', 'info');
        }
    },

    // Hizmet iptal talebi
    cancel: function(){
        var urgency = document.getElementById('cdg-pd2-cancel-type').value;
        var reason = document.getElementById('cdg-pd2-cancel-reason').value;
        if(!reason || reason.trim().length < 10) return this._toast('İptal sebebini en az 10 karakter olarak yazın', 'error');
        if(!confirm('Hizmet iptal talebinizi göndermek istediğinize emin misiniz?')) return;
        this._post({ operation: 'canceled_product', id: this.productId, urgency: urgency, reason: reason });
    },

    // Mevcut iptal talebini geri çek
    removeCancellation: function(){
        if(!confirm('İptal talebinizi geri çekmek istediğinize emin misiniz? Hizmetiniz aktif kalacaktır.')) return;
        this._post({ operation: 'remove_cancelled_product', id: this.productId });
    },

    // Eklenti satın al (addon)
    addAddon: function(addonId){
        if(!confirm('Bu eklenti için fatura oluşturulup ödeme sayfasına yönlendirileceksiniz. Devam edilsin mi?')) return;
        var self = this;
        this._post({ operation: 'product_addon', id: this.productId, addon_id: addonId }, function(r){
            self._toast(r.message || 'Eklenti faturası oluşturuldu', 'success');
            setTimeout(function(){ if(r.redirect) location.href = r.redirect; else location.reload(); }, 1500);
        });
    },

    // Paket yükselt
    upgrade: function(upgradeId){
        if(!confirm('Paket yükseltme için fatura oluşturulup ödeme sayfasına yönlendirileceksiniz. Devam edilsin mi?')) return;
        var self = this;
        this._post({ operation: 'product_upgrade', id: this.productId, upgrade_id: upgradeId }, function(r){
            self._toast(r.message || 'Yükseltme faturası oluşturuldu', 'success');
            setTimeout(function(){ if(r.redirect) location.href = r.redirect; else location.reload(); }, 1500);
        });
    },

    // E-posta hesabı ekle (hosting modülü destekliyorsa)
    addEmail: function(){
        var prefix = document.getElementById('cdg-pd2-email-prefix').value.trim();
        var domain = document.getElementById('cdg-pd2-email-domain').value;
        var pass = document.getElementById('cdg-pd2-email-pass').value;
        var quota = document.getElementById('cdg-pd2-email-quota').value;
        var unlimited = document.getElementById('cdg-pd2-email-unlimited').checked ? 1 : 0;
        if(!prefix) return this._toast('E-posta kullanıcı adı boş olamaz', 'error');
        if(!domain) return this._toast('Domain seçin', 'error');
        if(!pass || pass.length < 8) return this._toast('Şifre en az 8 karakter olmalı', 'error');
        var self = this;
        this._post({
            operation: 'hosting_add_new_email',
            id: this.productId,
            username: prefix,
            domain: domain,
            password: pass,
            quota: unlimited ? 0 : quota,
            unlimited: unlimited
        }, function(r){
            self._toast(r.message || 'E-posta hesabı oluşturuldu', 'success');
            setTimeout(function(){ location.reload(); }, 1500);
        });
    },

    // E-posta hesabı sil
    deleteEmail: function(email){
        if(!confirm('"' + email + '" adresini silmek istediğinize emin misiniz? Bu işlem geri alınamaz.')) return;
        this._post({ operation: 'hosting_delete_email', id: this.productId, email: email });
    },

    // Hizmet transferi (müşteriden müşteriye)
    transferService: function(){
        var email = document.getElementById('cdg-pd2-transfer-email').value;
        var pass = document.getElementById('cdg-pd2-transfer-pass').value;
        if(!email || !email.includes('@')) return this._toast('Geçerli bir e-posta girin', 'error');
        if(!pass) return this._toast('Hesap şifrenizi girin', 'error');
        if(!confirm('Hizmeti "' + email + '" adresindeki kullanıcıya devretmek istediğinize emin misiniz?')) return;
        var self = this;
        this._post({ operation: 'transfer_service', id: this.productId, email: email, password: pass }, function(r){
            self._toast(r.message || 'Transfer talebi oluşturuldu — alıcı onayını bekleyin', 'success');
            setTimeout(function(){ location.reload(); }, 2000);
        });
    },

    // Bekleyen transfer talebini iptal et
    removeTransfer: function(transferId){
        if(!confirm('Bu transfer talebini iptal etmek istediğinize emin misiniz?')) return;
        this._post({ operation: 'remove_ctoc_s_t', id: transferId });
    },

    // E-posta yönlendirme sil
    deleteForward: function(dest, forward){
        if(!confirm('"' + dest + ' → ' + forward + '" yönlendirmesini silmek istediğinize emin misiniz?')) return;
        this._post({ operation: 'hosting_delete_email_forward', id: this.productId, dest: dest, forward: forward });
    },

    // E-posta yönlendirme ekle
    addForward: function(){
        var dest = document.getElementById('cdg-pd2-forward-from').value.trim();
        var forward = document.getElementById('cdg-pd2-forward-to').value.trim();
        if(!dest || !forward) return this._toast('Kaynak ve hedef adres zorunlu', 'error');
        if(!forward.includes('@')) return this._toast('Geçerli bir hedef e-posta girin', 'error');
        var self = this;
        this._post({ operation: 'hosting_add_email_forward', id: this.productId, dest: dest, forward: forward }, function(r){
            self._toast(r.message || 'Yönlendirme oluşturuldu', 'success');
            setTimeout(function(){ location.reload(); }, 1500);
        });
    },

    // Server aksiyonları (reboot/shutdown/powerOn/reinstall)
    serverAction: function(action){
        var labels = {
            reboot: 'yeniden başlatmak',
            shutdown: 'kapatmak',
            powerOn: 'açmak',
            reinstall: 'işletim sistemini yeniden kurmak'
        };
        var lbl = labels[action] || action;
        var warning = action === 'reinstall' ? '\n\n⚠️ DİKKAT: TÜM VERİLERİNİZ SİLİNECEK! Devam etmeden önce yedek aldığınızdan emin olun.' : '';
        if(!confirm('Sunucuyu ' + lbl + ' istediğinize emin misiniz?' + warning)) return;
        // Reinstall için ek doğrulama
        if(action === 'reinstall') {
            var confirmText = prompt('Onaylamak için "REINSTALL" yazın:');
            if(confirmText !== 'REINSTALL') return this._toast('Onay metni hatalı, işlem iptal edildi', 'error');
        }
        var self = this;
        this._post({ operation: 'server_' + action, id: this.productId }, function(r){
            self._toast(r.message || 'Komut sunucuya gönderildi', 'success');
        });
    },

    // Web Konsol (VNC/noVNC) - yeni sekmede
    serverConsole: function(){
        var self = this;
        // Önce konsol URL'sini al
        this._post({ operation: 'server_console', id: this.productId }, function(r){
            if(r.url) {
                window.open(r.url, '_blank', 'width=1024,height=720,resizable=yes');
            } else {
                self._toast(r.message || 'Konsol URL alınamadı', 'error');
            }
        });
    },

    // Kullanım istatistiklerini yenile
    refreshUsage: function(){
        var self = this;
        this._toast('Kullanım istatistikleri güncelleniyor...', 'info');
        fetch(this.controllerUrl + '?inc=get_hosting_informations&m_page=', { credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(txt){
                self._toast('Kullanım yenilendi', 'success');
                setTimeout(function(){ location.reload(); }, 800);
            })
            .catch(function(){
                self._toast('Yenileme başarısız', 'error');
            });
    },

    // Eklenti kaldır (mevcut bir addon'u sil)
    removeAddon: function(addonId){
        if(!confirm('Bu eklentiyi kaldırmak istediğinize emin misiniz?')) return;
        this._post({ operation: 'product_addon_remove', id: this.productId, addon_id: addonId });
    }
};

// CDGPD2 HAZIR - debug için console'a yaz
console.log('%c[CODEGA] cdgPd2 v' + cdgPd2._version + ' yüklendi', 'background:#2E3B4E;color:#00E5FF;padding:4px 8px;border-radius:4px;font-weight:bold;', { productId: cdgPd2.productId, controller: cdgPd2.controllerUrl ? 'OK' : 'EKSİK' });

// Global hata yakalama (sayfada JS hatasi olursa toast goster)
window.addEventListener('error', function(e){
    if(e && e.message && e.message.indexOf('cdgPd2') !== -1) {
        console.error('[CODEGA] cdgPd2 hatası:', e.message, e.filename, e.lineno);
    }
});

// Toast animation keyframes (style tag dışında)
if(!document.getElementById('cdg-pd2-toast-style')) {
    var s = document.createElement('style'); s.id = 'cdg-pd2-toast-style';
    s.textContent = '@keyframes cdgToastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }';
    document.head.appendChild(s);
}

// Alt-tab toggle (Transfer pane'de Sunucu Taşıma vs Hesap Devri)
window.cdgPd2SubTab = function(evt, name){
    document.querySelectorAll('.cdg-pd2-subtab').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.cdg-pd2-subpane').forEach(function(p){ p.style.display = 'none'; });
    if(evt && evt.target) evt.target.closest('.cdg-pd2-subtab').classList.add('active');
    var pane = document.getElementById('cdg-pd2-subtab-' + name);
    if(pane) pane.style.display = 'block';
};

// Yenileme dönemi seçimi (order_renewal) - global function
window.cdgPd2OrderRenewal = function(btn) {
    var sel = document.getElementById('cdg-pd2-renewal-period');
    if(!sel || !sel.value) return cdgPd2._toast('Lütfen yenileme dönemi seçin', 'error');

    var label = sel.options[sel.selectedIndex].text;
    if(!confirm('"' + label + '" yenileme talebi sepete eklenecek. Devam edilsin mi?')) return;

    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

    cdgPd2._post({ operation: 'order_renewal', id: cdgPd2.productId, period: sel.value }, function(r){
        btn.disabled = false; btn.innerHTML = orig;
        if(r.redirect) {
            cdgPd2._toast('Ödeme sayfasına yönlendiriliyorsunuz...', 'success');
            setTimeout(function(){ window.location.href = r.redirect; }, 1200);
        } else {
            cdgPd2._toast(r.message || 'Yenileme talebi oluşturuldu', 'success');
            setTimeout(function(){ window.location.reload(); }, 1500);
        }
    }, function(r){
        btn.disabled = false; btn.innerHTML = orig;
        cdgPd2._toast((r && r.message) ? r.message : 'Bir hata oluştu', 'error');
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
