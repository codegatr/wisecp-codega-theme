<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA - Hosting Ürün Detay (v3.5.83+)
 *
 * - JavaScript YOK (CSS-only tab sistemi)
 * - WiseCP panel API entegre ($buttons → DirectAdmin/cPanel auto-login)
 * - $panel_name, $panel_logo, $supported runtime variable'larını kullanır
 *
 * WiseCP Runtime Variables:
 *   $proanse  - hizmet detayı (id, name, status, duedate, options, vs)
 *   $options  - hosting opsiyonları (domain, username, ip, panel_url, ns1-4, vs)
 *   $buttons  - panel auto-login butonları: ['cpanel' => ['url'=>..., 'name'=>...]]
 *   $panel_name - 'DirectAdmin', 'cPanel', 'Plesk'
 *   $panel_logo - panel logo URL'i
 *   $supported - ['change-password', 'manage-email-account', ...]
 *   $bills    - bu hizmete ait faturalar
 */

// === WiseCP variables (defansif) ===
$product   = isset($product) && is_array($product) ? $product : [];
$proanse   = isset($proanse) && is_array($proanse) ? $proanse : $product;
$options   = isset($options) && is_array($options) ? $options : (isset($proanse['options']) && is_array($proanse['options']) ? $proanse['options'] : []);
$bills     = isset($bills) && is_array($bills) ? $bills : [];
$buttons   = isset($buttons) && is_array($buttons) ? $buttons : [];
$supported = isset($supported) && is_array($supported) ? $supported : [];
$links     = isset($links) && is_array($links) ? $links : [];

// WiseCP native AJAX endpoint - tum hosting operasyonlari buraya POST atilir
$controller_url = $links['controller'] ?? '';

$d_id        = (int)($proanse['id'] ?? 0);
$d_name      = $proanse['name'] ?? 'Hosting';
$d_status    = strtolower($proanse['status'] ?? 'unknown');
$d_duedate   = $proanse['duedate'] ?? '';
$d_cdate     = $proanse['cdate'] ?? '';
$d_period    = $proanse['period'] ?? '';
$d_ptime     = $proanse['period_time'] ?? '';
$d_amount    = $proanse['amount'] ?? 0;

$d_domain    = $options['domain'] ?? '';
$d_username  = $options['username'] ?? '';
$d_ip        = $options['ip'] ?? '';

// FTP
$d_ftp_host  = $options['ftp_host'] ?? ($d_domain ? 'ftp.' . $d_domain : '');
$d_ftp_user  = $options['ftp_user'] ?? $d_username;
$d_ftp_port  = $options['ftp_port'] ?? '21';

// DNS
$d_dns = [];
for($i=1; $i<=4; $i++) {
    if(!empty($options['ns'.$i])) $d_dns[] = $options['ns'.$i];
}

// Panel adı/logosu/manuel URL
$panel_name  = $proanse['panel_name'] ?? ($panel_name ?? 'Kontrol Paneli');
$panel_logo  = $panel_logo ?? '';
$d_panel_url = $options['panel_url'] ?? ($options['cp_url'] ?? ($options['login_url'] ?? ''));

// Hosting limit bilgileri
$d_quota     = $options['disk'] ?? ($options['disk_space'] ?? '');
$d_bandwidth = $options['bandwidth'] ?? ($options['traffic'] ?? '');
$d_emails    = $options['emails'] ?? '';
$d_databases = $options['databases'] ?? '';

// Aktif/destek flagleri
$is_active = ($d_status === 'active');
$has_buttons = ($is_active && !empty($buttons));
$can_change_pw = ($is_active && in_array('change-password', $supported));
$can_manage_email = ($is_active && in_array('manage-email-account', $supported));

// $buttons'tan webmail varsa direkt URL al
$webmail_url = '';
foreach($buttons as $b_type => $b_value) {
    if(stripos((string)$b_type, 'webmail') !== false || stripos((string)($b_value['name'] ?? ''), 'webmail') !== false) {
        $webmail_url = $b_value['url'] ?? '';
        break;
    }
}

// Status meta
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

// Panel butonu için ikon eşleştir
$cdg_panel_icon = function($type, $name) {
    $key = strtolower($type . ' ' . $name);
    if(strpos($key, 'webmail') !== false || strpos($key, 'mail') !== false) return 'bi-envelope-fill';
    if(strpos($key, 'cpanel') !== false) return 'bi-server';
    if(strpos($key, 'directadmin') !== false || strpos($key, 'direct') !== false) return 'bi-shield-lock-fill';
    if(strpos($key, 'plesk') !== false) return 'bi-grid-3x3-gap-fill';
    if(strpos($key, 'phpmyadmin') !== false || strpos($key, 'mysql') !== false) return 'bi-database-fill';
    if(strpos($key, 'file') !== false || strpos($key, 'dosya') !== false) return 'bi-folder-fill';
    return 'bi-box-arrow-up-right';
};
?>

<style>
/* ===== CODEGA HOSTING DETAY - CSS-ONLY TAB ===== */
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
    --c-card: #ffffff;
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
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
}

.cdg-h-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--c-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--c-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-h-back:hover { border-color: var(--c-primary); color: var(--c-primary); }

.cdg-h-shell {
    background: var(--c-card);
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
    color: #fff; font-size: 24px;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(16,185,129,0.20);
}
.cdg-h-title { margin: 0 0 3px; font-size: 18px; font-weight: 800; line-height: 1.2; }
.cdg-h-sub { font-size: 13px; color: var(--c-muted); font-weight: 500; }
.cdg-h-status {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 11px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Tab nav */
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
    color: var(--c-muted);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    white-space: nowrap;
    user-select: none;
    transition: color 0.15s, background 0.15s, border-color 0.15s;
}
.cdg-h-tab:hover { color: var(--c-text); background: rgba(46,59,78,0.04); }

#cdg-h-r-summary:checked  ~ .cdg-h-tabs label[for="cdg-h-r-summary"],
#cdg-h-r-emails:checked   ~ .cdg-h-tabs label[for="cdg-h-r-emails"],
#cdg-h-r-password:checked ~ .cdg-h-tabs label[for="cdg-h-r-password"],
#cdg-h-r-renewal:checked  ~ .cdg-h-tabs label[for="cdg-h-r-renewal"],
#cdg-h-r-bills:checked    ~ .cdg-h-tabs label[for="cdg-h-r-bills"],
#cdg-h-r-transfer:checked ~ .cdg-h-tabs label[for="cdg-h-r-transfer"],
#cdg-h-r-cancel:checked   ~ .cdg-h-tabs label[for="cdg-h-r-cancel"] {
    color: var(--c-primary);
    border-bottom-color: var(--c-primary);
    font-weight: 700;
}

.cdg-h-pane { display: none; padding: 24px; }

#cdg-h-r-summary:checked  ~ .cdg-h-body .cdg-h-pane[data-pane="summary"]  { display: block; }
#cdg-h-r-emails:checked   ~ .cdg-h-body .cdg-h-pane[data-pane="emails"]   { display: block; }
#cdg-h-r-password:checked ~ .cdg-h-body .cdg-h-pane[data-pane="password"] { display: block; }
#cdg-h-r-renewal:checked  ~ .cdg-h-body .cdg-h-pane[data-pane="renewal"]  { display: block; }
#cdg-h-r-bills:checked    ~ .cdg-h-body .cdg-h-pane[data-pane="bills"]    { display: block; }
#cdg-h-r-transfer:checked ~ .cdg-h-body .cdg-h-pane[data-pane="transfer"] { display: block; }
#cdg-h-r-cancel:checked   ~ .cdg-h-body .cdg-h-pane[data-pane="cancel"]   { display: block; }

/* Card */
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
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
    color: var(--c-text);
}
.cdg-h-card-head h3 i { color: var(--c-primary); font-size: 15px; }
.cdg-h-card-body { padding: 18px; }

/* Info list */
.cdg-h-info { list-style: none; padding: 0; margin: 0; }
.cdg-h-info li {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 9px 0;
    border-bottom: 1px dashed var(--c-border);
    font-size: 13px;
    gap: 12px;
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

/* Button */
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
    color: #fff;
    box-shadow: 0 6px 18px rgba(46,59,78,0.20);
}
.cdg-h-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-h-btn-success {
    background: linear-gradient(135deg, var(--c-success), #059669);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.20);
}
.cdg-h-btn-success:hover { transform: translateY(-1px); color: #fff; }
.cdg-h-btn-danger {
    background: linear-gradient(135deg, var(--c-danger), #dc2626);
    color: #fff;
}
.cdg-h-btn-danger:hover { transform: translateY(-1px); color: #fff; }

/* Alert */
.cdg-h-alert {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.55;
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

/* Form */
.cdg-h-field { margin-bottom: 14px; }
.cdg-h-label {
    display: block; font-size: 12px; font-weight: 700;
    color: var(--c-text); text-transform: uppercase;
    letter-spacing: 0.5px; margin-bottom: 7px;
}
.cdg-h-input, .cdg-h-select, .cdg-h-textarea {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--c-border);
    border-radius: 10px;
    font-size: 14px; font-family: inherit;
    background: #fff; color: var(--c-text);
    outline: none;
}
.cdg-h-input:focus, .cdg-h-select:focus, .cdg-h-textarea:focus {
    border-color: var(--c-primary);
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}

/* === PANEL ERIŞIM KARTI === */
.cdg-h-panel-hero {
    background: linear-gradient(135deg, var(--c-primary-deep) 0%, var(--c-primary) 100%);
    border-radius: 16px;
    padding: 24px;
    color: #fff;
    margin: 0 0 14px;
    position: relative;
    overflow: hidden;
}
.cdg-h-panel-hero::before {
    content: '';
    position: absolute;
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
    flex-shrink: 0;
    overflow: hidden;
}
.cdg-h-panel-hero-icon img {
    max-width: 36px;
    max-height: 36px;
    object-fit: contain;
}
.cdg-h-panel-hero-icon i {
    color: var(--c-info);
    font-size: 28px;
}
.cdg-h-panel-hero-title h4 {
    margin: 0 0 3px;
    font-size: 18px;
    font-weight: 800;
    color: #fff;
}
.cdg-h-panel-hero-title p {
    margin: 0;
    font-size: 13px;
    color: rgba(255,255,255,0.75);
}

/* Panel buton listesi */
.cdg-h-panel-btns {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
    position: relative;
    z-index: 1;
}
.cdg-h-panel-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.20);
    border-radius: 12px;
    color: #fff !important;
    font-weight: 700;
    font-size: 13px;
    transition: all 0.2s;
}
.cdg-h-panel-btn:hover {
    background: var(--c-info);
    border-color: var(--c-info);
    color: #0f172a !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0,229,255,0.30);
}
.cdg-h-panel-btn i {
    font-size: 18px;
    flex-shrink: 0;
}
.cdg-h-panel-btn .cdg-h-panel-btn-text {
    display: flex; flex-direction: column;
    gap: 2px;
    line-height: 1.2;
}
.cdg-h-panel-btn .cdg-h-panel-btn-text strong {
    font-size: 13px;
    font-weight: 700;
}
.cdg-h-panel-btn .cdg-h-panel-btn-text small {
    font-size: 10px;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Boş durumda manuel link */
.cdg-h-panel-manual {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    background: var(--c-info);
    color: #0f172a !important;
    border-radius: 10px;
    font-weight: 700; font-size: 13px;
    transition: transform 0.15s;
}
.cdg-h-panel-manual:hover { transform: translateY(-1px); }

/* Şifre input grup (eye + magic butonlu) */
.cdg-h-pw-row {
    display: flex; gap: 6px;
    background: #fff;
    border: 1.5px solid var(--c-border);
    border-radius: 10px;
    padding: 4px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cdg-h-pw-row:focus-within {
    border-color: var(--c-primary);
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}
.cdg-h-pw-input {
    flex: 1;
    border: 0; outline: none;
    padding: 9px 12px;
    font-size: 14px;
    background: transparent;
    color: var(--c-text);
    font-family: "JetBrains Mono", Consolas, monospace;
}
.cdg-h-pw-side {
    width: 40px;
    background: #f1f5f9;
    border: 0;
    border-radius: 8px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--c-muted);
    transition: background 0.15s, color 0.15s;
}
.cdg-h-pw-side:hover {
    background: var(--c-primary);
    color: #fff;
}
.cdg-h-pw-side i { font-size: 16px; }
</style>

<div class="cdg-h">

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES); ?>" class="cdg-h-back">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <div class="cdg-h-shell">

        <!-- Radio button'lar - tab seçimi için -->
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-summary" class="cdg-h-tab-radio" checked>
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-emails" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-password" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-renewal" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-bills" class="cdg-h-tab-radio">
        <input type="radio" name="cdg-h-tab" id="cdg-h-r-transfer" class="cdg-h-tab-radio">
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
        <div class="cdg-h-tabs">
            <label class="cdg-h-tab" for="cdg-h-r-summary"><i class="bi bi-info-circle"></i> Özet</label>
            <label class="cdg-h-tab" for="cdg-h-r-emails"><i class="bi bi-envelope"></i> E-posta</label>
            <label class="cdg-h-tab" for="cdg-h-r-password"><i class="bi bi-key"></i> Şifre</label>
            <label class="cdg-h-tab" for="cdg-h-r-renewal"><i class="bi bi-arrow-clockwise"></i> Yenileme</label>
            <?php if(!empty($bills)): ?>
            <label class="cdg-h-tab" for="cdg-h-r-bills"><i class="bi bi-receipt"></i> Faturalar (<?php echo count($bills); ?>)</label>
            <?php endif; ?>
            <label class="cdg-h-tab" for="cdg-h-r-transfer"><i class="bi bi-arrow-left-right"></i> Transfer</label>
            <label class="cdg-h-tab" for="cdg-h-r-cancel"><i class="bi bi-ban"></i> İptal</label>
        </div>

        <!-- Pane'ler -->
        <div class="cdg-h-body">

            <!-- ÖZET -->
            <div class="cdg-h-pane" data-pane="summary">

                <!-- ============================================
                     PANEL ERIŞIM HERO KARTI - WiseCP $buttons API
                     ============================================ -->
                <?php if($has_buttons): ?>
                <div class="cdg-h-panel-hero">
                    <div class="cdg-h-panel-hero-head">
                        <div class="cdg-h-panel-hero-icon">
                            <?php if($panel_logo): ?>
                                <img src="<?php echo htmlspecialchars($panel_logo, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?>">
                            <?php else: ?>
                                <i class="bi bi-key-fill"></i>
                            <?php endif; ?>
                        </div>
                        <div class="cdg-h-panel-hero-title">
                            <h4><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?> Erişim</h4>
                            <p>Şifre girmeden tek tıkla panellere giriş yapın</p>
                        </div>
                    </div>
                    <div class="cdg-h-panel-btns">
                        <?php foreach($buttons as $b_type => $b_value):
                            $url = $b_value['url'] ?? '';
                            $name = $b_value['name'] ?? ucfirst((string)$b_type);
                            if(!$url) continue;
                            $icon = $cdg_panel_icon($b_type, $name);
                        ?>
                        <a href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-panel-btn">
                            <i class="bi <?php echo $icon; ?>"></i>
                            <span class="cdg-h-panel-btn-text">
                                <strong><?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
                                <small>Tek tıkla giriş</small>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php elseif($d_panel_url): ?>
                <!-- Auto-login yoksa manuel URL -->
                <div class="cdg-h-panel-hero">
                    <div class="cdg-h-panel-hero-head">
                        <div class="cdg-h-panel-hero-icon"><i class="bi bi-key-fill"></i></div>
                        <div class="cdg-h-panel-hero-title">
                            <h4><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?></h4>
                            <p>Hosting yönetimi için panele giriş yapın</p>
                        </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($d_panel_url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-panel-manual">
                        <i class="bi bi-box-arrow-up-right"></i> <?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?>'e Git
                    </a>
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
                            <h3><i class="bi bi-gear"></i> Erişim Bilgileri</h3>
                        </div>
                        <div class="cdg-h-card-body">
                            <ul class="cdg-h-info">
                                <?php if($d_domain): ?>
                                <li><span class="cdg-h-info-l">Domain</span><span class="cdg-h-info-v"><?php echo htmlspecialchars($d_domain, ENT_QUOTES); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_username): ?>
                                <li><span class="cdg-h-info-l">Kullanıcı Adı</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($d_username, ENT_QUOTES); ?></code></span></li>
                                <?php endif; ?>
                                <?php if($d_ip): ?>
                                <li><span class="cdg-h-info-l">IP</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($d_ip, ENT_QUOTES); ?></code></span></li>
                                <?php endif; ?>
                                <?php if($d_quota): ?>
                                <li><span class="cdg-h-info-l">Disk Alanı</span><span class="cdg-h-info-v"><?php echo htmlspecialchars((string)$d_quota, ENT_QUOTES); ?> MB</span></li>
                                <?php endif; ?>
                                <?php if($d_bandwidth): ?>
                                <li><span class="cdg-h-info-l">Aylık Trafik</span><span class="cdg-h-info-v"><?php echo htmlspecialchars((string)$d_bandwidth, ENT_QUOTES); ?> MB</span></li>
                                <?php endif; ?>
                                <?php if($d_emails): ?>
                                <li><span class="cdg-h-info-l">E-posta Hesabı</span><span class="cdg-h-info-v"><?php echo htmlspecialchars((string)$d_emails, ENT_QUOTES); ?></span></li>
                                <?php endif; ?>
                                <?php if($d_databases): ?>
                                <li><span class="cdg-h-info-l">Veritabanı</span><span class="cdg-h-info-v"><?php echo htmlspecialchars((string)$d_databases, ENT_QUOTES); ?></span></li>
                                <?php endif; ?>
                                <li><span class="cdg-h-info-l">Kontrol Paneli</span><span class="cdg-h-info-v"><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?></span></li>
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
                                <?php if($d_ftp_host): ?><li><span class="cdg-h-info-l">Sunucu</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($d_ftp_host, ENT_QUOTES); ?></code></span></li><?php endif; ?>
                                <?php if($d_ftp_user): ?><li><span class="cdg-h-info-l">Kullanıcı Adı</span><span class="cdg-h-info-v"><code><?php echo htmlspecialchars($d_ftp_user, ENT_QUOTES); ?></code></span></li><?php endif; ?>
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

            <!-- E-POSTA -->
            <div class="cdg-h-pane" data-pane="emails">
                <div class="cdg-h-alert cdg-h-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>E-posta Hesabı Yönetimi</strong><br>
                        E-posta hesaplarınızı oluşturmak ve yönetmek için <strong><?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?></strong> paneline giriş yapın.
                        <?php if($d_emails): ?>Paketinizde <strong><?php echo (int)$d_emails; ?> e-posta hesabı</strong> hakkı vardır.<?php endif; ?>
                    </div>
                </div>

                <div class="cdg-h-grid-2">
                    <?php if($webmail_url): ?>
                    <a href="<?php echo htmlspecialchars($webmail_url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-success">
                        <i class="bi bi-envelope-fill"></i> Webmail'e Tek Tıkla Gir
                    </a>
                    <?php endif; ?>

                    <?php
                    // İlk panel butonu (cPanel/DirectAdmin) - email'e değil
                    $primary_panel_btn = null;
                    foreach($buttons as $b_type => $b_value) {
                        if(stripos((string)$b_type, 'webmail') === false && stripos((string)($b_value['name'] ?? ''), 'webmail') === false) {
                            $primary_panel_btn = $b_value;
                            break;
                        }
                    }
                    ?>
                    <?php if($primary_panel_btn && !empty($primary_panel_btn['url'])): ?>
                    <a href="<?php echo htmlspecialchars($primary_panel_btn['url'], ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-primary">
                        <i class="bi bi-box-arrow-up-right"></i> <?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?>'de E-posta Yönet
                    </a>
                    <?php elseif($d_panel_url): ?>
                    <a href="<?php echo htmlspecialchars($d_panel_url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-primary">
                        <i class="bi bi-box-arrow-up-right"></i> <?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?>'e Git
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ŞİFRE - WiseCP native şifre değiştirme -->
            <div class="cdg-h-pane" data-pane="password">
                <?php if($can_change_pw): ?>
                <div class="cdg-h-card">
                    <div class="cdg-h-card-head">
                        <h3><i class="bi bi-key-fill"></i> <?php echo htmlspecialchars($panel_name, ENT_QUOTES); ?> Şifre Değiştir</h3>
                    </div>
                    <div class="cdg-h-card-body">
                        <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
                            Yeni hosting şifrenizi belirleyin. Güçlü şifre en az 8 karakter, harf, sayı ve özel karakter içermelidir.
                        </p>

                        <form id="cdgPwForm" data-id="<?php echo $d_id; ?>" autocomplete="off">
                            <div class="cdg-h-pw-row">
                                <input type="password" name="password" id="cdgPwInput"
                                    class="cdg-h-pw-input" placeholder="Yeni şifre"
                                    autocomplete="new-password" minlength="6" required>
                                <button type="button" id="cdgPwToggle" class="cdg-h-pw-side" title="Göster/Gizle">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" id="cdgPwGen" class="cdg-h-pw-side" title="Otomatik Oluştur">
                                    <i class="bi bi-magic"></i>
                                </button>
                            </div>

                            <div class="cdg-h-alert cdg-h-alert-warn" style="margin-top:14px;">
                                <i class="bi bi-exclamation-triangle"></i>
                                <div style="font-size:12px;">
                                    <strong>Önemli:</strong> Şifre değiştikten sonra FTP, e-posta yapılandırmalarınızı yeni şifre ile güncellemeyi unutmayın.
                                </div>
                            </div>

                            <button type="submit" class="cdg-h-btn cdg-h-btn-primary" style="width:100%;margin-top:14px;">
                                <i class="bi bi-check2-circle"></i> Şifreyi Değiştir
                            </button>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="cdg-h-alert cdg-h-alert-warn">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Şifre Değişikliği</strong><br>
                        Bu hizmet için panel üzerinden şifre değişikliği desteklenmiyor.
                        <?php if($has_buttons || $d_panel_url): ?>
                        Lütfen kontrol paneline giriş yapın.
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- YENİLEME -->
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
                        <form id="cdgRenForm" data-id="<?php echo $d_id; ?>" style="margin-top:14px;text-align:center;">
                            <button type="submit" class="cdg-h-btn cdg-h-btn-success">
                                <i class="bi bi-arrow-clockwise"></i> Yenileme Faturası Oluştur
                            </button>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="cdg-h-alert cdg-h-alert-warn">
                    <i class="bi bi-exclamation-circle"></i>
                    <div>Bu hizmet için yenileme yapılamaz. Durum: <strong><?php echo $smeta[0]; ?></strong></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- FATURALAR -->
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
                                <span class="cdg-h-info-l">
                                    #<?php echo (int)$b_id; ?> · <?php echo $cdg_date_fmt($b_date); ?>
                                </span>
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

            <!-- TRANSFER -->
            <div class="cdg-h-pane" data-pane="transfer">
                <div class="cdg-h-alert cdg-h-alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>Hosting Transferi</strong><br>
                        Sitenizi başka bir hosting hesabından bizim sunucumuza taşımak için lütfen destek talebi açın. Teknik ekibimiz transfer işlemini sizin için ücretsiz gerçekleştirir.
                    </div>
                </div>
                <div style="text-align:center;padding:20px;">
                    <a href="/hesabim/destek-talebi-olustur?subject=Hosting+Transfer+%23<?php echo $d_id; ?>" class="cdg-h-btn cdg-h-btn-primary">
                        <i class="bi bi-headset"></i> Transfer İçin Destek Aç
                    </a>
                </div>
            </div>

            <!-- İPTAL -->
            <div class="cdg-h-pane" data-pane="cancel">
                <?php if($d_status === 'active'): ?>
                <div class="cdg-h-alert cdg-h-alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Hizmet İptal İşlemi</strong><br>
                        Bu hizmeti iptal etmek istediğinizden emin misiniz? İptal sonrası tüm verileriniz silinecektir.
                    </div>
                </div>
                <form id="cdgCancelForm" data-id="<?php echo $d_id; ?>" style="margin-top:14px;">

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
                        <button type="submit" class="cdg-h-btn cdg-h-btn-danger">
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

<?php if($controller_url && ($can_change_pw || $is_active)): ?>
<script>
/**
 * CODEGA Hosting Detay - Minimal Form Submit Handler
 * - Tab sistemi CSS-only (HİÇ JS yok)
 * - Sadece form submit'leri WiseCP native API'ye gönderilir
 * - Inline onclick KULLANILMAZ - addEventListener pattern
 * - WiseCP MioAjax tercih edilir, fetch fallback
 */
(function(){
    'use strict';

    var CONTROLLER_URL = <?php echo json_encode($controller_url); ?>;
    var PRODUCT_ID = <?php echo (int)$d_id; ?>;

    // WiseCP API çağrısı (MioAjax > fetch fallback)
    function api(operation, data, callback) {
        var payload = { operation: operation };
        for(var k in data) payload[k] = data[k];

        // 1) WiseCP'in native MioAjax fonksiyonu varsa kullan
        if(typeof MioAjax === 'function') {
            try {
                MioAjax({
                    url: CONTROLLER_URL,
                    type: 'post',
                    data: payload,
                    result: function(r){ callback(r); }
                });
                return;
            } catch(e) { /* fall through to fetch */ }
        }

        // 2) Fallback: native fetch
        var fd = new FormData();
        for(var k2 in payload) fd.append(k2, payload[k2]);
        fetch(CONTROLLER_URL, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r){ return r.text(); })
        .then(function(t){
            var j = null;
            try { j = JSON.parse(t); } catch(e) { j = { status: 'error', message: 'Yanıt çözümlenemedi' }; }
            callback(j);
        })
        .catch(function(){ callback({ status: 'error', message: 'Bağlantı hatası' }); });
    }

    // Bildirim gösterimi (WiseCP alert_success/alert_error tercih)
    function notify(type, msg) {
        if(type === 'success' && typeof alert_success === 'function') {
            alert_success(msg, { timer: 3000 });
        } else if((type === 'error' || type === 'danger') && typeof alert_error === 'function') {
            alert_error(msg, { timer: 4000 });
        } else if(type === 'success' && typeof toastr !== 'undefined') {
            toastr.success(msg);
        } else if((type === 'error' || type === 'danger') && typeof toastr !== 'undefined') {
            toastr.error(msg);
        } else {
            alert(msg);
        }
    }

    // Buton loading state helper
    function btnLoading(btn, text) {
        if(!btn) return null;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> ' + (text || 'İşleniyor...');
        return function(){ btn.disabled = false; btn.innerHTML = orig; };
    }

    // === ŞİFRE FORM ===
    var pwForm = document.getElementById('cdgPwForm');
    if(pwForm) {
        // Toggle göz ikonu
        var togBtn = document.getElementById('cdgPwToggle');
        if(togBtn) {
            togBtn.addEventListener('click', function(){
                var inp = document.getElementById('cdgPwInput');
                if(!inp) return;
                inp.type = (inp.type === 'password') ? 'text' : 'password';
                var icon = togBtn.querySelector('i');
                if(icon) icon.className = (inp.type === 'password') ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        }

        // Şifre üreteci
        var genBtn = document.getElementById('cdgPwGen');
        if(genBtn) {
            genBtn.addEventListener('click', function(){
                var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
                var pw = '';
                for(var i=0; i<14; i++) {
                    pw += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                var inp = document.getElementById('cdgPwInput');
                if(inp) {
                    inp.value = pw;
                    inp.type = 'text';
                    var icon = togBtn ? togBtn.querySelector('i') : null;
                    if(icon) icon.className = 'bi bi-eye-slash';
                }
            });
        }

        // Form submit -> WiseCP change_hosting_password
        pwForm.addEventListener('submit', function(e){
            e.preventDefault();
            var inp = document.getElementById('cdgPwInput');
            if(!inp || !inp.value) {
                notify('error', 'Lütfen yeni şifre girin');
                return;
            }
            if(inp.value.length < 6) {
                notify('error', 'Şifre en az 6 karakter olmalı');
                return;
            }
            if(!confirm('Hosting şifrenizi değiştirmek istediğinize emin misiniz?')) return;

            var btn = pwForm.querySelector('button[type=submit]');
            var restore = btnLoading(btn, 'İşleniyor...');

            api('change_hosting_password', { id: PRODUCT_ID, password: inp.value }, function(r){
                restore && restore();
                if(r && (r.status === 'successful' || r.status === 'success')) {
                    notify('success', r.message || 'Hosting şifreniz başarıyla değiştirildi');
                    inp.value = '';
                    inp.type = 'password';
                    var icon = togBtn ? togBtn.querySelector('i') : null;
                    if(icon) icon.className = 'bi bi-eye';
                } else {
                    notify('error', (r && r.message) || 'Şifre değiştirilemedi');
                }
            });
        });
    }

    // === YENİLEME FORM ===
    var renForm = document.getElementById('cdgRenForm');
    if(renForm) {
        renForm.addEventListener('submit', function(e){
            e.preventDefault();
            if(!confirm('Yenileme faturası oluşturulacak. Onaylıyor musunuz?')) return;

            var btn = renForm.querySelector('button[type=submit]');
            var restore = btnLoading(btn, 'Fatura oluşturuluyor...');

            api('order_renewal', { id: PRODUCT_ID }, function(r){
                restore && restore();
                if(r && (r.status === 'successful' || r.status === 'success')) {
                    notify('success', r.message || 'Yenileme faturası oluşturuldu');
                    if(r.url || r.redirect) {
                        setTimeout(function(){ window.location.href = (r.url || r.redirect); }, 1500);
                    } else {
                        setTimeout(function(){ window.location.reload(); }, 1500);
                    }
                } else {
                    notify('error', (r && r.message) || 'Yenileme faturası oluşturulamadı');
                }
            });
        });
    }

    // === İPTAL FORM ===
    var cancelForm = document.getElementById('cdgCancelForm');
    if(cancelForm) {
        cancelForm.addEventListener('submit', function(e){
            e.preventDefault();
            if(!confirm('İptal işlemini onaylıyor musunuz? Bu işlem geri alınamaz.')) return;

            var typeSel = cancelForm.querySelector('select[name="cancel_type"]');
            var reasonTa = cancelForm.querySelector('textarea[name="cancel_reason"]');
            var data = {
                id: PRODUCT_ID,
                type: typeSel ? typeSel.value : 'end_of_period',
                reason: reasonTa ? reasonTa.value : ''
            };

            var btn = cancelForm.querySelector('button[type=submit]');
            var restore = btnLoading(btn, 'Talep gönderiliyor...');

            api('canceled_product', data, function(r){
                restore && restore();
                if(r && (r.status === 'successful' || r.status === 'success')) {
                    notify('success', r.message || 'İptal talebiniz alındı');
                    setTimeout(function(){ window.location.reload(); }, 2000);
                } else {
                    notify('error', (r && r.message) || 'İptal talebi gönderilemedi');
                }
            });
        });
    }
})();
</script>
<?php endif; ?>
