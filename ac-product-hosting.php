<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA - Hosting Ürün Detay (v3.5.87+) - YENİDEN YAZILDI
 *
 * Strateji:
 * - Tema config'inden hosting panel URL'leri okur (theme-config.php → settings.hosting_panels)
 * - WiseCP $buttons varsa onu kullanır, yoksa config'ten URL gösterir
 * - Doğru WiseCP field isimleri: disk_limit, bandwidth_limit, email_limit, database_limit, panel_link, panel_type
 * - JavaScript YOK (CSS-only tab sistemi)
 * - Debug YOK
 */

// === WiseCP runtime variables (defansif) ===
$product   = isset($product) && is_array($product) ? $product : [];
$proanse   = isset($proanse) && is_array($proanse) ? $proanse : $product;
$options   = isset($options) && is_array($options) ? $options : (isset($proanse['options']) && is_array($proanse['options']) ? $proanse['options'] : []);
$bills     = isset($bills) && is_array($bills) ? $bills : [];
$buttons   = isset($buttons) && is_array($buttons) ? $buttons : [];
$supported = isset($supported) && is_array($supported) ? $supported : [];

// Hizmet bilgileri
$d_id      = (int)($proanse['id'] ?? 0);
$d_name    = $proanse['name'] ?? 'Hosting';
$d_status  = strtolower($proanse['status'] ?? 'unknown');
$d_duedate = $proanse['duedate'] ?? '';
$d_cdate   = $proanse['cdate'] ?? '';
$d_period  = $proanse['period'] ?? '';
$d_ptime   = $proanse['period_time'] ?? '';
$d_amount  = $proanse['amount'] ?? 0;
$d_domain  = $options['domain'] ?? '';

// Hosting limit'leri (WiseCP doğru field isimleri)
$d_quota     = $options['disk_limit'] ?? '';
$d_bandwidth = $options['bandwidth_limit'] ?? '';
$d_emails    = $options['email_limit'] ?? '';
$d_databases = $options['database_limit'] ?? '';
$d_subdomains = $options['subdomain_limit'] ?? '';
$d_ftp_users = $options['ftp_limit'] ?? '';

// Panel tipi (DirectAdmin/cPanel/Plesk)
$d_panel_type = strtolower($options['panel_type'] ?? '');
$panel_type_map = [
    'directadmin' => 'DirectAdmin',
    'da'          => 'DirectAdmin',
    'cpanel'      => 'cPanel',
    'plesk'       => 'Plesk',
];
$panel_name = $panel_type_map[$d_panel_type] ?? ($proanse['panel_name'] ?? 'Kontrol Paneli');

// === HOSTING PANEL URL'leri (theme config'ten) ===
$cdg_panels = [];
if(class_exists('Config') && method_exists('Config', 'get')) {
    try {
        $tmp = Config::get('theme/hosting_panels');
        if(is_array($tmp)) $cdg_panels = $tmp;
    } catch(\Throwable $e) {}
}
// Fallback: theme-config.php'i direkt oku
if(empty($cdg_panels)) {
    $tc_file = __DIR__ . '/theme-config.php';
    if(file_exists($tc_file)) {
        $tc = @include $tc_file;
        if(is_array($tc) && isset($tc['settings']['hosting_panels'])) {
            $cdg_panels = $tc['settings']['hosting_panels'];
        }
    }
}

// Panel URL'ini belirle (öncelik sırası)
$panel_url_final = '';
$webmail_url_final = '';

// 1. WiseCP $buttons varsa kullan (auto-login token'lı — en iyi seçenek)
if(!empty($buttons)) {
    foreach($buttons as $b_type => $b_value) {
        $url = is_array($b_value) ? ($b_value['url'] ?? '') : (is_string($b_value) ? $b_value : '');
        $type_lower = strtolower((string)$b_type);
        if(stripos($type_lower, 'webmail') !== false) {
            $webmail_url_final = $url;
        } elseif(empty($panel_url_final)) {
            $panel_url_final = $url;
        }
    }
}

// 2. WiseCP runtime panel_link
if(empty($panel_url_final) && !empty($options['panel_link'])) {
    $panel_url_final = $options['panel_link'];
}

// 3. Servis runtime'dan server IP / hostname al → panel URL üret
//    (Müşteri domain bağlamamış olabilir, hostname yerine IP'ye yönlendiriyoruz)
$_cdg_server_ip = '';
$_cdg_server_host = '';
// $server runtime değişkeni (WiseCP hosting modülünden)
if(isset($server) && is_array($server)) {
    $_cdg_server_ip   = $server['ip']        ?? ($server['address']  ?? '');
    $_cdg_server_host = $server['hostname']  ?? ($server['name']     ?? '');
}
// $options içinde de olabilir
if(empty($_cdg_server_ip) && !empty($options['server_ip']))   $_cdg_server_ip = $options['server_ip'];
if(empty($_cdg_server_host) && !empty($options['hostname']))  $_cdg_server_host = $options['hostname'];

// IP varsa, panel tipine göre standart port ile URL üret
$_cdg_make_panel_url = function($host, $type) {
    if(!$host) return '';
    if($type === 'directadmin' || $type === 'da') return 'https://' . $host . ':2222';
    if($type === 'cpanel')                         return 'https://' . $host . ':2083';
    if($type === 'plesk')                          return 'https://' . $host . ':8443';
    return 'https://' . $host . ':2222'; // bilinmiyorsa DA varsayılan
};
$_cdg_make_webmail_url = function($host, $type) {
    if(!$host) return '';
    if($type === 'directadmin' || $type === 'da') return 'https://' . $host . ':2096';
    if($type === 'cpanel')                         return 'https://' . $host . '/webmail';
    if($type === 'plesk')                          return 'https://' . $host . '/webmail';
    return 'https://' . $host . ':2096';
};

if(empty($panel_url_final) && $_cdg_server_ip) {
    $panel_url_final = $_cdg_make_panel_url($_cdg_server_ip, $d_panel_type);
} elseif(empty($panel_url_final) && $_cdg_server_host) {
    $panel_url_final = $_cdg_make_panel_url($_cdg_server_host, $d_panel_type);
}

if(empty($webmail_url_final) && $_cdg_server_ip) {
    $webmail_url_final = $_cdg_make_webmail_url($_cdg_server_ip, $d_panel_type);
}

// 4. Tema config'ten panel tipine göre URL
if(empty($panel_url_final)) {
    if($d_panel_type === 'directadmin' || $d_panel_type === 'da') {
        $panel_url_final = $cdg_panels['directadmin_url'] ?? '';
    } elseif($d_panel_type === 'cpanel') {
        $panel_url_final = $cdg_panels['cpanel_url'] ?? '';
    } elseif($d_panel_type === 'plesk') {
        $panel_url_final = $cdg_panels['plesk_url'] ?? '';
    }
    // Hala boşsa default DirectAdmin URL
    if(empty($panel_url_final) && !empty($cdg_panels['directadmin_url'])) {
        $panel_url_final = $cdg_panels['directadmin_url'];
    }
}

// 5. Son çare — theme config default_server_ip
if(empty($panel_url_final) && !empty($cdg_panels['default_server_ip'])) {
    $panel_url_final = $_cdg_make_panel_url($cdg_panels['default_server_ip'], $d_panel_type);
}

// Webmail URL
if(empty($webmail_url_final) && !empty($cdg_panels['webmail_url'])) {
    $webmail_url_final = $cdg_panels['webmail_url'];
}
$phpmyadmin_url = $cdg_panels['phpmyadmin_url'] ?? '';
$show_panel_card = !empty($cdg_panels['show_panel_card']);

// === FTP bilgileri ===
$d_ftp_info = isset($options['ftp_info']) && is_array($options['ftp_info']) ? $options['ftp_info'] : [];
$d_ftp_host = $d_ftp_info['host'] ?? ($options['ftp_host'] ?? ($d_domain ? 'ftp.' . $d_domain : ''));
$d_ftp_user = $d_ftp_info['username'] ?? ($d_ftp_info['user'] ?? ($options['ftp_user'] ?? ''));
$d_ftp_port = $d_ftp_info['port'] ?? ($options['ftp_port'] ?? ($cdg_panels['ftp_port'] ?? '21'));

// === DNS sunucuları ===
$d_dns = [];
if(isset($options['dns'])) {
    if(is_array($options['dns'])) {
        $d_dns = array_values(array_filter($options['dns']));
    } elseif(is_string($options['dns'])) {
        $d_dns = array_values(array_filter(array_map('trim', explode(',', $options['dns']))));
    }
}
if(empty($d_dns)) {
    for($i=1; $i<=4; $i++) {
        if(!empty($options['ns'.$i])) $d_dns[] = $options['ns'.$i];
    }
}

// === Status meta ===
$status_meta = [
    'active'     => ['Aktif',       '#10b981', '#dcfce7'],
    'pending'    => ['Beklemede',   '#f59e0b', '#fef3c7'],
    'suspended'  => ['Askıda',      '#f97316', '#fed7aa'],
    'cancelled'  => ['İptal',       '#ef4444', '#fee2e2'],
    'expired'    => ['Süresi Doldu','#ef4444', '#fee2e2'],
    'terminated' => ['Sonlandı',    '#ef4444', '#fee2e2'],
];
$smeta = $status_meta[$d_status] ?? ['Bilinmiyor', '#64748b', '#f1f5f9'];

// Tarih formatı
$cdg_date_fmt = function($d) {
    if(!$d) return '-';
    $ts = is_numeric($d) ? (int)$d : strtotime($d);
    return $ts ? date('d/m/Y', $ts) : $d;
};

// Geri linki
$back_url = '/hesabim/hosting';
if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
    try {
        $tmp = Controllers::$init->CRLink('ac-ps-products', ['hosting']);
        if($tmp) $back_url = $tmp;
    } catch(\Throwable $e) {}
}
?>

<style>
.cdg-h {
    --c-primary: #2E3B4E;
    --c-primary-deep: #1A2332;
    --c-success: #10b981;
    --c-warning: #f59e0b;
    --c-danger: #ef4444;
    --c-info: #00D3E5;
    --c-text: #0f172a;
    --c-muted: #64748b;
    --c-border: #e2e8f0;
    font-family: 'Plus Jakarta Sans', -apple-system, "Segoe UI", sans-serif;
    color: var(--c-text);
    padding: 8px 0 28px;
    box-sizing: border-box;
}
.cdg-h *, .cdg-h *::before, .cdg-h *::after { box-sizing: border-box; }
.cdg-h a { text-decoration: none; color: inherit; }

.cdg-h-tab-radio {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
    width: 0 !important; height: 0 !important;
}

.cdg-h-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff; border: 1px solid var(--c-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--c-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-h-back:hover { border-color: var(--c-primary); color: var(--c-primary); }

.cdg-h-shell {
    background: #fff;
    border: 1px solid var(--c-border);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.04);
    overflow: hidden;
}
.cdg-h-head {
    padding: 20px 24px;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid var(--c-border);
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 16px;
}
.cdg-h-head-l { display: flex; align-items: center; gap: 16px; min-width: 0; }
.cdg-h-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--c-success), #059669);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 24px; flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(16,185,129,0.20);
}
.cdg-h-title { margin: 0 0 3px; font-size: 18px; font-weight: 800; }
.cdg-h-sub { font-size: 13px; color: var(--c-muted); font-weight: 500; }
.cdg-h-status {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 11px; border-radius: 100px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
}

.cdg-h-tabs {
    display: flex; gap: 2px;
    padding: 0 24px;
    border-bottom: 1px solid var(--c-border);
    background: #fff;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.cdg-h-tab {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 13px 18px;
    font-size: 13px; font-weight: 600;
    color: var(--c-muted); cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px; white-space: nowrap;
    user-select: none;
    transition: color 0.15s, background 0.15s, border-color 0.15s;
}
.cdg-h-tab:hover { color: var(--c-text); background: rgba(46,59,78,0.04); }

#cdg-h-r-summary:checked  ~ .cdg-h-tabs label[for="cdg-h-r-summary"],
#cdg-h-r-emails:checked   ~ .cdg-h-tabs label[for="cdg-h-r-emails"],
#cdg-h-r-renewal:checked  ~ .cdg-h-tabs label[for="cdg-h-r-renewal"],
#cdg-h-r-bills:checked    ~ .cdg-h-tabs label[for="cdg-h-r-bills"],
#cdg-h-r-password:checked ~ .cdg-h-tabs label[for="cdg-h-r-password"],
#cdg-h-r-cancel:checked   ~ .cdg-h-tabs label[for="cdg-h-r-cancel"] {
    color: var(--c-primary);
    border-bottom-color: var(--c-primary);
    font-weight: 700;
}

.cdg-h-pane { display: none; padding: 24px; }

#cdg-h-r-summary:checked  ~ .cdg-h-body .cdg-h-pane[data-pane="summary"]  { display: block; }
#cdg-h-r-emails:checked   ~ .cdg-h-body .cdg-h-pane[data-pane="emails"]   { display: block; }
#cdg-h-r-renewal:checked  ~ .cdg-h-body .cdg-h-pane[data-pane="renewal"]  { display: block; }
#cdg-h-r-bills:checked    ~ .cdg-h-body .cdg-h-pane[data-pane="bills"]    { display: block; }
#cdg-h-r-password:checked ~ .cdg-h-body .cdg-h-pane[data-pane="password"] { display: block; }
#cdg-h-r-cancel:checked   ~ .cdg-h-body .cdg-h-pane[data-pane="cancel"]   { display: block; }

.cdg-h-card {
    background: #fff;
    border: 1px solid var(--c-border);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 14px;
}
.cdg-h-card-head {
    padding: 14px 18px;
    border-bottom: 1px solid var(--c-border);
    background: linear-gradient(135deg, #fafbfd, #fff);
}
.cdg-h-card-head h3 {
    font-size: 13px; font-weight: 800; margin: 0;
    text-transform: uppercase; letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
    color: var(--c-text);
}
.cdg-h-card-head h3 i { color: var(--c-primary); font-size: 15px; }
.cdg-h-card-body { padding: 18px; }

.cdg-h-info { list-style: none; padding: 0; margin: 0; }
.cdg-h-info li {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 9px 0;
    border-bottom: 1px dashed var(--c-border);
    font-size: 13px; gap: 12px;
}
.cdg-h-info li:last-child { border-bottom: 0; padding-bottom: 0; }
.cdg-h-info li:first-child { padding-top: 0; }
.cdg-h-info-l { color: var(--c-muted); font-weight: 600; flex-shrink: 0; }
.cdg-h-info-v { color: var(--c-text); font-weight: 700; text-align: right; word-break: break-word; }
.cdg-h-info-v code {
    background: #f1f5f9; padding: 2px 8px; border-radius: 4px;
    font-family: "JetBrains Mono", Consolas, monospace;
    font-size: 12px; font-weight: 600;
}

.cdg-h-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 768px) { .cdg-h-grid-2 { grid-template-columns: 1fr; } }

.cdg-h-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    border: 0; cursor: pointer;
    text-decoration: none;
    transition: transform 0.18s, box-shadow 0.18s;
    font-family: inherit;
}
.cdg-h-btn-primary {
    background: linear-gradient(135deg, var(--c-primary-deep), #485A75);
    color: #fff; box-shadow: 0 6px 18px rgba(46,59,78,0.20);
}
.cdg-h-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-h-btn-success {
    background: linear-gradient(135deg, var(--c-success), #059669);
    color: #fff; box-shadow: 0 6px 18px rgba(16,185,129,0.20);
}
.cdg-h-btn-success:hover { transform: translateY(-1px); color: #fff; }
.cdg-h-btn-danger {
    background: linear-gradient(135deg, var(--c-danger), #dc2626);
    color: #fff;
}
.cdg-h-btn-danger:hover { transform: translateY(-1px); color: #fff; }

.cdg-h-alert {
    padding: 14px 18px; border-radius: 10px;
    font-size: 13px; line-height: 1.55;
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 14px;
}
.cdg-h-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.cdg-h-alert-info { background: #eff6ff; color: #1e3a8a; border: 1px solid #93c5fd; }
.cdg-h-alert-info i { color: #2563eb; }
.cdg-h-alert-warn { background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }
.cdg-h-alert-warn i { color: #d97706; }
.cdg-h-alert-danger { background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5; }
.cdg-h-alert-danger i { color: #dc2626; }

.cdg-h-field { margin-bottom: 14px; }
.cdg-h-label {
    display: block; font-size: 12px; font-weight: 700;
    color: var(--c-text); text-transform: uppercase;
    letter-spacing: 0.5px; margin-bottom: 7px;
}
.cdg-h-input, .cdg-h-select, .cdg-h-textarea {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--c-border); border-radius: 10px;
    font-size: 14px; font-family: inherit;
    background: #fff; color: var(--c-text); outline: none;
}
.cdg-h-input:focus, .cdg-h-select:focus, .cdg-h-textarea:focus {
    border-color: var(--c-primary);
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}

/* ===== PANEL ERIŞIM HERO KARTI ===== */
.cdg-h-panel-hero {
    background: linear-gradient(135deg, var(--c-primary-deep) 0%, var(--c-primary) 100%);
    border-radius: 16px;
    padding: 24px;
    color: #fff;
    margin: 0 0 14px;
    position: relative; overflow: hidden;
}
.cdg-h-panel-hero::before {
    content: ''; position: absolute;
    top: -50%; right: -10%;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(0,229,255,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-h-panel-hero-head {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
    position: relative; z-index: 1;
}
.cdg-h-panel-hero-icon {
    width: 56px; height: 56px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: var(--c-info); font-size: 28px;
    flex-shrink: 0;
}
.cdg-h-panel-hero h4 { margin: 0 0 3px; font-size: 18px; font-weight: 800; color: #fff; }
.cdg-h-panel-hero p { margin: 0; font-size: 13px; color: rgba(255,255,255,0.75); }

.cdg-h-panel-btns {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
    position: relative; z-index: 1;
}
.cdg-h-panel-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.20);
    border-radius: 12px;
    color: #fff !important;
    font-weight: 700; font-size: 13px;
    transition: all 0.2s;
}
.cdg-h-panel-btn:hover {
    background: var(--c-info);
    border-color: var(--c-info);
    color: #0f172a !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0,229,255,0.30);
}
.cdg-h-panel-btn i { font-size: 20px; flex-shrink: 0; }
.cdg-h-panel-btn-text { display: flex; flex-direction: column; gap: 2px; line-height: 1.2; }
.cdg-h-panel-btn-text strong { font-size: 13px; font-weight: 700; }
.cdg-h-panel-btn-text small { font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.3px; }
</style>

<div class="cdg-h">

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES); ?>" class="cdg-h-back">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <div class="cdg-h-shell">

        <!-- Radio button'lar - tab seçimi -->
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-summary" class="cdg-h-tab-radio" checked>
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-emails" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-renewal" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-bills" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-password" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-cancel" class="cdg-h-tab-radio">

        <!-- Header -->
        <div class="cdg-h-head">
            <div class="cdg-h-head-l">
                <div class="cdg-h-icon"><i class="bi bi-hdd-network-fill"></i></div>
                <div style="min-width:0;">
                    <h1 class="cdg-h-title"><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
                    <div class="cdg-h-sub">
                        <?php if($d_domain): ?>
                            <i class="bi bi-globe"></i> <strong><?php echo htmlspecialchars($d_domain, ENT_QUOTES); ?></strong>
                        <?php endif; ?>
                        <?php if($d_duedate): ?>
                            <span style="margin:0 6px;color:#cbd5e1;">·</span>
                            <i class="bi bi-calendar-check"></i> Bitiş: <?php echo $cdg_date_fmt($d_duedate); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div>
                <span class="cdg-h-status" style="background:<?php echo $smeta[2]; ?>;color:<?php echo $smeta[1]; ?>;">
                    <i class="bi bi-circle-fill" style="font-size:8px;"></i>
                    <?php echo $smeta[0]; ?>
                </span>
            </div>
        </div>

        <!-- Tab nav -->
        <?php
            // Şifre değiştirme: hosting aktif VE modül destekliyor
            $_cdg_can_change_pw = ($d_status === 'active') && (
                empty($supported) ||
                in_array('change-password', $supported, true) ||
                in_array('change_password', $supported, true)
            );
        ?>
        <div class="cdg-h-tabs">
            <label class="cdg-h-tab" for="cdg-h-r-summary"><i class="bi bi-info-circle"></i> Özet</label>
            <label class="cdg-h-tab" for="cdg-h-r-emails"><i class="bi bi-envelope"></i> E-posta</label>
            <label class="cdg-h-tab" for="cdg-h-r-renewal"><i class="bi bi-arrow-clockwise"></i> Yenileme</label>
            <?php if(!empty($bills)): ?>
            <label class="cdg-h-tab" for="cdg-h-r-bills"><i class="bi bi-receipt"></i> Faturalar (<?php echo count($bills); ?>)</label>
            <?php endif; ?>
            <?php if($_cdg_can_change_pw): ?>
            <label class="cdg-h-tab" for="cdg-h-r-password"><i class="bi bi-key"></i> Şifre Değiştir</label>
            <?php endif; ?>
            <label class="cdg-h-tab" for="cdg-h-r-cancel"><i class="bi bi-ban"></i> İptal</label>
        </div>

        <div class="cdg-h-body">

            <!-- ===== ÖZET ===== -->
            <div class="cdg-h-pane" data-pane="summary">

                <?php if($show_panel_card && ($panel_url_final || $webmail_url_final)): ?>
                <div class="cdg-h-panel-hero">
                    <div class="cdg-h-panel-hero-head">
                        <div class="cdg-h-panel-hero-icon">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div>
                            <h4><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?> Erişim</h4>
                            <p>Hosting yönetimi için kontrol panellerine giriş yapın</p>
                        </div>
                    </div>
                    <div class="cdg-h-panel-btns">
                        <?php if($panel_url_final): ?>
                        <a href="<?php echo htmlspecialchars($panel_url_final, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-panel-btn">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span class="cdg-h-panel-btn-text">
                                <strong><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?></strong>
                                <small>Hosting Yönetimi</small>
                            </span>
                        </a>
                        <?php endif; ?>

                        <?php if($webmail_url_final): ?>
                        <a href="<?php echo htmlspecialchars($webmail_url_final, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-panel-btn">
                            <i class="bi bi-envelope-fill"></i>
                            <span class="cdg-h-panel-btn-text">
                                <strong>Webmail</strong>
                                <small>E-posta Erişim</small>
                            </span>
                        </a>
                        <?php endif; ?>

                        <?php if($phpmyadmin_url): ?>
                        <a href="<?php echo htmlspecialchars($phpmyadmin_url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-panel-btn">
                            <i class="bi bi-database-fill"></i>
                            <span class="cdg-h-panel-btn-text">
                                <strong>phpMyAdmin</strong>
                                <small>Veritabanı</small>
                            </span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hizmet bilgileri grid -->
                <div class="cdg-h-grid-2">
                    <div class="cdg-h-card">
                        <div class="cdg-h-card-head">
                            <h3><i class="bi bi-info-circle"></i> Hizmet Bilgileri</h3>
                        </div>
                        <div class="cdg-h-card-body">
                            <ul class="cdg-h-info">
                                <li><span class="cdg-h-info-l">Sipariş No</span><span class="cdg-h-info-v">#<?php echo $d_id; ?></span></li>
                                <li><span class="cdg-h-info-l">Paket</span><span class="cdg-h-info-v"><?php echo htmlspecialchars($d_name, ENT_QUOTES); ?></span></li>
                                <li><span class="cdg-h-info-l">Durum</span><span class="cdg-h-info-v"><?php echo $smeta[0]; ?></span></li>
                                <li><span class="cdg-h-info-l">Kontrol Paneli</span><span class="cdg-h-info-v"><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?></span></li>
                                <?php if($d_cdate): ?>
                                <li><span class="cdg-h-info-l">Sipariş Tarihi</span><span class="cdg-h-info-v"><?php echo $cdg_date_fmt($d_cdate); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_duedate): ?>
                                <li><span class="cdg-h-info-l">Bitiş Tarihi</span><span class="cdg-h-info-v"><?php echo $cdg_date_fmt($d_duedate); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_amount): ?>
                                <li><span class="cdg-h-info-l">Ücret</span><span class="cdg-h-info-v"><?php echo (float)$d_amount; ?> ₺</span></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="cdg-h-card">
                        <div class="cdg-h-card-head">
                            <h3><i class="bi bi-bar-chart-fill"></i> Paket Limitleri</h3>
                        </div>
                        <div class="cdg-h-card-body">
                            <ul class="cdg-h-info">
                                <?php if($d_domain): ?>
                                <li><span class="cdg-h-info-l">Domain</span><span class="cdg-h-info-v"><?php echo htmlspecialchars($d_domain, ENT_QUOTES); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_quota !== '' && $d_quota != 0): ?>
                                <li><span class="cdg-h-info-l">Disk Alanı</span><span class="cdg-h-info-v"><?php echo (int)$d_quota >= 999999 ? 'Sınırsız' : ((int)$d_quota . ' MB'); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_bandwidth !== '' && $d_bandwidth != 0): ?>
                                <li><span class="cdg-h-info-l">Aylık Trafik</span><span class="cdg-h-info-v"><?php echo (int)$d_bandwidth >= 999999 ? 'Sınırsız' : ((int)$d_bandwidth . ' MB'); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_emails !== ''): ?>
                                <li><span class="cdg-h-info-l">E-posta Hesabı</span><span class="cdg-h-info-v"><?php echo (int)$d_emails >= 9999 || $d_emails == -1 ? 'Sınırsız' : (int)$d_emails; ?></span></li>
                                <?php endif; ?>
                                <?php if($d_databases !== ''): ?>
                                <li><span class="cdg-h-info-l">Veritabanı</span><span class="cdg-h-info-v"><?php echo (int)$d_databases >= 9999 || $d_databases == -1 ? 'Sınırsız' : (int)$d_databases; ?></span></li>
                                <?php endif; ?>
                                <?php if($d_subdomains !== ''): ?>
                                <li><span class="cdg-h-info-l">Alt Alan Adı</span><span class="cdg-h-info-v"><?php echo (int)$d_subdomains >= 9999 || $d_subdomains == -1 ? 'Sınırsız' : (int)$d_subdomains; ?></span></li>
                                <?php endif; ?>
                                <?php if($d_ftp_users !== ''): ?>
                                <li><span class="cdg-h-info-l">FTP Hesabı</span><span class="cdg-h-info-v"><?php echo (int)$d_ftp_users >= 9999 || $d_ftp_users == -1 ? 'Sınırsız' : (int)$d_ftp_users; ?></span></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FTP & DNS -->
                <div class="cdg-h-grid-2">
                    <?php if($d_ftp_host || $d_ftp_user): ?>
                    <div class="cdg-h-card">
                        <div class="cdg-h-card-head">
                            <h3><i class="bi bi-folder-symlink"></i> FTP Bilgileri</h3>
                        </div>
                        <div class="cdg-h-card-body">
                            <ul class="cdg-h-info">
                                <?php if($d_ftp_host): ?>
                                <li><span class="cdg-h-info-l">Sunucu</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($d_ftp_host, ENT_QUOTES); ?></code></span></li>
                                <?php endif; ?>
                                <?php if($d_ftp_user): ?>
                                <li><span class="cdg-h-info-l">Kullanıcı Adı</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($d_ftp_user, ENT_QUOTES); ?></code></span></li>
                                <?php endif; ?>
                                <li><span class="cdg-h-info-l">Port</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars((string)$d_ftp_port, ENT_QUOTES); ?></code></span></li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($d_dns)): ?>
                    <div class="cdg-h-card">
                        <div class="cdg-h-card-head">
                            <h3><i class="bi bi-globe"></i> DNS Sunucuları</h3>
                        </div>
                        <div class="cdg-h-card-body">
                            <ul class="cdg-h-info">
                                <?php foreach($d_dns as $i => $ns): ?>
                                <li><span class="cdg-h-info-l">NS<?php echo $i+1; ?></span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($ns, ENT_QUOTES); ?></code></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== E-POSTA ===== -->
            <div class="cdg-h-pane" data-pane="emails">
                <div class="cdg-h-alert cdg-h-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>E-posta Hesabı Yönetimi</strong><br>
                        E-posta hesaplarınızı oluşturmak ve yönetmek için <strong><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?></strong> paneline giriş yapın.
                        <?php if($d_emails): ?>Paketinizde <strong><?php echo (int)$d_emails >= 9999 ? 'sınırsız' : (int)$d_emails . ' adet'; ?> e-posta hesabı</strong> hakkı vardır.<?php endif; ?>
                    </div>
                </div>
                <div class="cdg-h-grid-2">
                    <?php if($webmail_url_final): ?>
                    <a href="<?php echo htmlspecialchars($webmail_url_final, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-success">
                        <i class="bi bi-envelope-fill"></i> Webmail'e Git
                    </a>
                    <?php endif; ?>
                    <?php if($panel_url_final): ?>
                    <a href="<?php echo htmlspecialchars($panel_url_final, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-primary">
                        <i class="bi bi-box-arrow-up-right"></i> <?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?> Paneli
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== YENİLEME ===== -->
            <div class="cdg-h-pane" data-pane="renewal">
                <?php if($d_status === 'active' || $d_status === 'expired'): ?>
                <div class="cdg-h-alert cdg-h-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>Hizmet Yenileme</strong><br>
                        Hizmetinizin bitiş tarihi: <strong><?php echo $cdg_date_fmt($d_duedate); ?></strong><br>
                        Ödeme ile hizmetinizi <?php echo (int)($d_period ?: 1); ?> <?php echo htmlspecialchars($d_ptime ?: 'Yıl', ENT_QUOTES); ?> daha uzatabilirsiniz.
                    </div>
                </div>
                <div class="cdg-h-card">
                    <div class="cdg-h-card-body">
                        <ul class="cdg-h-info">
                            <li><span class="cdg-h-info-l">Yenileme Ücreti</span><span class="cdg-h-info-v"><?php echo (float)$d_amount; ?> ₺</span></li>
                            <li><span class="cdg-h-info-l">Periyot</span><span class="cdg-h-info-v"><?php echo (int)$d_period; ?> <?php echo htmlspecialchars($d_ptime ?: 'Yıl', ENT_QUOTES); ?></span></li>
                        </ul>
                        <div style="margin-top:14px;text-align:center;">
                            <form method="post" action="" style="display:inline-block;">
                                <input type="hidden" name="renewal_id" value="<?php echo $d_id; ?>">
                                <button type="submit" name="action" value="renewal" class="cdg-h-btn cdg-h-btn-success">
                                    <i class="bi bi-arrow-clockwise"></i> Yenileme Faturası Oluştur
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="cdg-h-alert cdg-h-alert-warn">
                    <i class="bi bi-exclamation-circle"></i>
                    <div>Bu hizmet için yenileme yapılamaz. Durum: <strong><?php echo $smeta[0]; ?></strong></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ===== FATURALAR ===== -->
            <?php if(!empty($bills)): ?>
            <div class="cdg-h-pane" data-pane="bills">
                <div class="cdg-h-card">
                    <div class="cdg-h-card-head">
                        <h3><i class="bi bi-receipt"></i> Bu Hizmete Ait Faturalar (<?php echo count($bills); ?>)</h3>
                    </div>
                    <div class="cdg-h-card-body">
                        <ul class="cdg-h-info">
                            <?php foreach(array_slice($bills, 0, 20) as $b):
                                $b_id = $b['id'] ?? 0;
                                $b_date = $b['date'] ?? '';
                                $b_amount = $b['amount'] ?? 0;
                                $b_status = $b['status'] ?? '';
                            ?>
                            <li>
                                <span class="cdg-h-info-l">#<?php echo (int)$b_id; ?> · <?php echo $cdg_date_fmt($b_date); ?></span>
                                <span class="cdg-h-info-v">
                                    <?php echo (float)$b_amount; ?> ₺
                                    <span style="margin-left:8px;font-size:11px;color:<?php echo $b_status === 'paid' ? '#10b981' : '#f59e0b'; ?>;">
                                        <?php echo $b_status === 'paid' ? '✓ Ödendi' : 'Bekliyor'; ?>
                                    </span>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ===== ŞİFRE DEĞİŞTİR ===== -->
            <div class="cdg-h-pane" data-pane="password">
                <?php if($_cdg_can_change_pw): ?>
                <div class="cdg-h-card">
                    <div class="cdg-h-card-head">
                        <h3 style="margin:0;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-key" style="color:var(--c-primary);"></i> Hosting (DirectAdmin) Şifresini Değiştir
                        </h3>
                    </div>
                    <div style="padding:18px;">
                        <div class="cdg-h-alert cdg-h-alert-info" style="margin-bottom:14px;">
                            <i class="bi bi-info-circle"></i>
                            <div style="line-height:1.6;">
                                <strong>Yeni şifrenizi belirleyin.</strong><br>
                                Bu işlem hosting kontrol panelinizin (DirectAdmin) ana hesap şifresini değiştirir. FTP, e-posta ve veritabanı şifreleri ayrıca yönetilir. Yeni şifre derhal uygulanır &mdash; eski şifreyle artık giriş yapamayacaksınız.
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr;gap:12px;">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:var(--c-text);margin-bottom:6px;">
                                    Yeni Şifre <span style="color:var(--c-danger);">*</span>
                                </label>
                                <div style="display:flex;gap:8px;align-items:stretch;">
                                    <div style="flex:1;position:relative;">
                                        <input type="password" id="cdg-h-pw-new"
                                               style="width:100%;padding:10px 40px 10px 12px;border:1px solid var(--c-border);border-radius:8px;font-size:14px;font-family:'JetBrains Mono', monospace;letter-spacing:1px;"
                                               placeholder="••••••••••••••••"
                                               autocomplete="new-password"
                                               oninput="cdgHpwCheck();">
                                        <button type="button"
                                                onclick="cdgHpwToggleVisibility();"
                                                style="position:absolute;right:6px;top:50%;transform:translateY(-50%);background:transparent;border:0;color:var(--c-muted);cursor:pointer;padding:4px 8px;font-size:14px;"
                                                title="Şifreyi göster/gizle">
                                            <i class="bi bi-eye" id="cdg-h-pw-eye"></i>
                                        </button>
                                    </div>
                                    <button type="button" onclick="cdgHpwGenerate();"
                                            style="padding:10px 14px;background:#f1f5f9;border:1px solid var(--c-border);border-radius:8px;font-size:12.5px;font-weight:600;color:var(--c-text);cursor:pointer;white-space:nowrap;">
                                        <i class="bi bi-shuffle"></i> Rastgele Üret
                                    </button>
                                </div>
                                <!-- Güç göstergesi -->
                                <div style="margin-top:10px;">
                                    <div style="display:flex;gap:4px;height:6px;border-radius:3px;overflow:hidden;background:#f1f5f9;">
                                        <div id="cdg-h-pw-bar" style="height:100%;width:0;background:#cbd5e1;transition:width 0.25s, background 0.25s;"></div>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                                        <small id="cdg-h-pw-strength-text" style="font-size:11.5px;color:var(--c-muted);">Güç: bekleniyor</small>
                                        <small id="cdg-h-pw-len" style="font-size:11.5px;color:var(--c-muted);font-family:monospace;">0 karakter</small>
                                    </div>
                                </div>
                                <ul id="cdg-h-pw-rules" style="list-style:none;padding:0;margin:10px 0 0;font-size:11.5px;color:var(--c-muted);line-height:1.8;">
                                    <li id="cdg-h-pw-r-len"><i class="bi bi-circle"></i> En az 10 karakter</li>
                                    <li id="cdg-h-pw-r-up"><i class="bi bi-circle"></i> En az 1 büyük harf (A-Z)</li>
                                    <li id="cdg-h-pw-r-low"><i class="bi bi-circle"></i> En az 1 küçük harf (a-z)</li>
                                    <li id="cdg-h-pw-r-num"><i class="bi bi-circle"></i> En az 1 rakam (0-9)</li>
                                    <li id="cdg-h-pw-r-spc"><i class="bi bi-circle"></i> En az 1 özel karakter (#@!$%&*)</li>
                                </ul>
                            </div>

                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:var(--c-text);margin-bottom:6px;">
                                    Yeni Şifre (Tekrar) <span style="color:var(--c-danger);">*</span>
                                </label>
                                <input type="password" id="cdg-h-pw-confirm"
                                       style="width:100%;padding:10px 12px;border:1px solid var(--c-border);border-radius:8px;font-size:14px;font-family:'JetBrains Mono', monospace;letter-spacing:1px;"
                                       placeholder="••••••••••••••••"
                                       autocomplete="new-password"
                                       oninput="cdgHpwCheck();">
                                <small id="cdg-h-pw-match" style="display:block;font-size:11.5px;color:var(--c-muted);margin-top:6px;">&nbsp;</small>
                            </div>
                        </div>

                        <div class="cdg-h-alert cdg-h-alert-warning" style="margin-top:16px;font-size:12.5px;">
                            <i class="bi bi-exclamation-triangle"></i>
                            <div>
                                Şifre değişiklikten sonra <strong>FTP istemcileri ve e-posta uygulamalarınızda</strong> manuel olarak güncellenmelidir &mdash; aksi halde bağlanamazlar.
                            </div>
                        </div>

                        <div style="display:flex;gap:8px;margin-top:14px;align-items:center;">
                            <button type="button" id="cdg-h-pw-submit"
                                    onclick="cdgHpwSubmit();"
                                    disabled
                                    style="padding:10px 22px;background:var(--c-primary);color:#fff;border:0;border-radius:8px;font-size:13px;font-weight:700;cursor:not-allowed;opacity:0.5;">
                                <i class="bi bi-check-circle"></i> Şifreyi Değiştir
                            </button>
                            <button type="button" onclick="cdgHpwClear();"
                                    style="padding:10px 18px;background:#f1f5f9;border:1px solid var(--c-border);border-radius:8px;font-size:13px;font-weight:600;color:var(--c-text);cursor:pointer;">
                                <i class="bi bi-arrow-counterclockwise"></i> Temizle
                            </button>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="cdg-h-alert cdg-h-alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Şifre değişikliği şu anda mümkün değil.</strong><br>
                        <?php if($d_status !== 'active'): ?>
                            Hizmetiniz aktif olmadığı için şifre değiştirilemez. Hizmet durumu: <strong><?php echo htmlspecialchars($d_status, ENT_QUOTES); ?></strong>
                        <?php else: ?>
                            Hosting paneliniz bu özelliği desteklemiyor. Lütfen <a href="<?php echo cdg_link('create-ticket-request'); ?>" style="color:var(--c-primary);font-weight:700;">destek talebi</a> oluşturun.
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ===== İPTAL ===== -->
            <div class="cdg-h-pane" data-pane="cancel">
                <?php if($d_status === 'active'): ?>
                <div class="cdg-h-alert cdg-h-alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Hizmet İptal İşlemi</strong><br>
                        Bu hizmeti iptal etmek istediğinizden emin misiniz? İptal sonrası tüm verileriniz silinecektir.
                    </div>
                </div>
                <form method="post" action="" style="margin-top:14px;">
                    <input type="hidden" name="cancel_id" value="<?php echo $d_id; ?>">
                    <div class="cdg-h-field">
                        <label class="cdg-h-label">İptal Türü</label>
                        <select name="cancel_type" class="cdg-h-select">
                            <option value="end_of_period">Periyot Sonunda İptal Et (<?php echo $cdg_date_fmt($d_duedate); ?>)</option>
                            <option value="immediate">Hemen İptal Et</option>
                        </select>
                    </div>
                    <div class="cdg-h-field">
                        <label class="cdg-h-label">İptal Sebebi</label>
                        <textarea name="cancel_reason" class="cdg-h-textarea" rows="3" placeholder="İptal sebebinizi belirtir misiniz? (opsiyonel)"></textarea>
                    </div>
                    <div style="text-align:center;">
                        <button type="submit" name="action" value="cancel" class="cdg-h-btn cdg-h-btn-danger">
                            <i class="bi bi-ban"></i> İptal Talebi Gönder
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="cdg-h-alert cdg-h-alert-warn">
                    <i class="bi bi-info-circle"></i>
                    Bu hizmet zaten <strong><?php echo $smeta[0]; ?></strong> durumunda. İptal işlemi yapılamaz.
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php if($_cdg_can_change_pw): ?>
<script>
// === Hosting şifre değiştirme ===
(function(){
    var lastStrength = 0;

    window.cdgHpwGenerate = function() {
        // 16 karakter, A-Z+a-z+0-9+#@!$%&*?
        var charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ' + 'abcdefghjkmnpqrstuvwxyz' + '23456789' + '#@!$%&*?';
        var pw = '';
        var arr = new Uint32Array(16);
        if(window.crypto && window.crypto.getRandomValues) {
            window.crypto.getRandomValues(arr);
            for(var i=0; i<16; i++) pw += charset[arr[i] % charset.length];
        } else {
            for(var j=0; j<16; j++) pw += charset[Math.floor(Math.random() * charset.length)];
        }
        document.getElementById('cdg-h-pw-new').value = pw;
        document.getElementById('cdg-h-pw-confirm').value = pw;
        // Görünür hale getir ki kullanıcı kopyalayabilsin
        var pwEl = document.getElementById('cdg-h-pw-new');
        var cfEl = document.getElementById('cdg-h-pw-confirm');
        pwEl.type = 'text';
        cfEl.type = 'text';
        var eye = document.getElementById('cdg-h-pw-eye');
        if(eye) { eye.classList.remove('bi-eye'); eye.classList.add('bi-eye-slash'); }
        cdgHpwCheck();
    };

    window.cdgHpwToggleVisibility = function() {
        var pwEl = document.getElementById('cdg-h-pw-new');
        var cfEl = document.getElementById('cdg-h-pw-confirm');
        var eye  = document.getElementById('cdg-h-pw-eye');
        var show = pwEl.type === 'password';
        pwEl.type = show ? 'text' : 'password';
        cfEl.type = show ? 'text' : 'password';
        if(eye) {
            eye.classList.toggle('bi-eye', !show);
            eye.classList.toggle('bi-eye-slash', show);
        }
    };

    window.cdgHpwClear = function() {
        document.getElementById('cdg-h-pw-new').value = '';
        document.getElementById('cdg-h-pw-confirm').value = '';
        cdgHpwCheck();
    };

    window.cdgHpwCheck = function() {
        var pw = document.getElementById('cdg-h-pw-new').value;
        var cf = document.getElementById('cdg-h-pw-confirm').value;
        var len = pw.length;

        // Karakter sayısı
        document.getElementById('cdg-h-pw-len').textContent = len + ' karakter';

        // Kurallar
        var hasLen = len >= 10;
        var hasUp  = /[A-Z]/.test(pw);
        var hasLow = /[a-z]/.test(pw);
        var hasNum = /[0-9]/.test(pw);
        var hasSpc = /[^A-Za-z0-9]/.test(pw);

        function setRule(id, ok) {
            var el = document.getElementById(id);
            if(!el) return;
            var ic = el.querySelector('i');
            if(ok) {
                if(ic){ ic.classList.remove('bi-circle'); ic.classList.add('bi-check-circle-fill'); }
                el.style.color = '#10b981';
            } else {
                if(ic){ ic.classList.remove('bi-check-circle-fill'); ic.classList.add('bi-circle'); }
                el.style.color = '';
            }
        }
        setRule('cdg-h-pw-r-len', hasLen);
        setRule('cdg-h-pw-r-up',  hasUp);
        setRule('cdg-h-pw-r-low', hasLow);
        setRule('cdg-h-pw-r-num', hasNum);
        setRule('cdg-h-pw-r-spc', hasSpc);

        // Güç skoru (5 üzerinden)
        var score = (hasLen?1:0) + (hasUp?1:0) + (hasLow?1:0) + (hasNum?1:0) + (hasSpc?1:0);
        // Uzunluk bonusu
        if(len >= 14) score = Math.min(5, score + 1);

        var bar  = document.getElementById('cdg-h-pw-bar');
        var txt  = document.getElementById('cdg-h-pw-strength-text');
        var labels = ['Çok zayıf', 'Zayıf', 'Orta', 'İyi', 'Güçlü', 'Çok güçlü'];
        var colors = ['#ef4444', '#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#059669'];
        var idx = Math.min(score, 5);
        bar.style.width = (len === 0 ? 0 : (idx * 20)) + '%';
        bar.style.background = len === 0 ? '#cbd5e1' : colors[idx];
        txt.textContent = 'Güç: ' + (len === 0 ? 'bekleniyor' : labels[idx]);
        txt.style.color = len === 0 ? '' : colors[idx];
        lastStrength = score;

        // Eşleşme kontrolü
        var match = document.getElementById('cdg-h-pw-match');
        if(cf.length === 0) {
            match.textContent = '\u00A0';
            match.style.color = '';
        } else if(pw === cf) {
            match.innerHTML = '<i class="bi bi-check-circle-fill"></i> Şifreler eşleşiyor';
            match.style.color = '#10b981';
        } else {
            match.innerHTML = '<i class="bi bi-x-circle-fill"></i> Şifreler eşleşmiyor';
            match.style.color = '#ef4444';
        }

        // Submit butonu
        var canSubmit = hasLen && hasUp && hasLow && hasNum && pw === cf && cf.length > 0;
        var btn = document.getElementById('cdg-h-pw-submit');
        if(btn) {
            btn.disabled = !canSubmit;
            btn.style.cursor = canSubmit ? 'pointer' : 'not-allowed';
            btn.style.opacity = canSubmit ? '1' : '0.5';
        }
    };

    window.cdgHpwSubmit = function() {
        var pw = document.getElementById('cdg-h-pw-new').value;
        var cf = document.getElementById('cdg-h-pw-confirm').value;
        if(pw !== cf) {
            if(typeof alert_error === 'function') alert_error('Şifreler eşleşmiyor', {timer: 3000});
            return;
        }
        if(pw.length < 10) {
            if(typeof alert_error === 'function') alert_error('Şifre en az 10 karakter olmalı', {timer: 3000});
            return;
        }
        if(lastStrength < 4) {
            if(!confirm('Şifre gücü düşük. Yine de değiştirmek istiyor musunuz?')) return;
        }
        if(typeof MioAjax !== 'function') {
            alert('MioAjax yüklü değil — sayfa yenilemesi gerekiyor.');
            return;
        }
        var btn = document.getElementById('cdg-h-pw-submit');
        if(btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Değiştiriliyor...';
        }
        MioAjax({
            url: '<?php echo htmlspecialchars($links["controller"] ?? "", ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',
            type: 'post',
            data: {
                operation: 'hosting_change_password',
                id:       <?php echo (int)$d_id; ?>,
                password: pw
            },
            result: function(r) {
                if(r && (r.status === 'successful' || r.status === 'success')) {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Şifre değiştirildi', {timer: 3000});
                    cdgHpwClear();
                    if(btn) {
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> Değiştirildi';
                        setTimeout(function(){
                            btn.innerHTML = '<i class="bi bi-check-circle"></i> Şifreyi Değiştir';
                        }, 2500);
                    }
                } else {
                    if(typeof alert_error === 'function') alert_error((r && r.message) || 'Şifre değiştirilemedi', {timer: 3500});
                    if(btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> Şifreyi Değiştir';
                    }
                }
            }
        });
    };
})();
</script>
<?php endif; ?>
