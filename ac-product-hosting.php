<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA - Hosting Ürün Detay (v3.5.82+) - SIFIRDAN YAZILDI
 *
 * - JavaScript YOK (CSS-only tab sistemi - radio button + sibling selector)
 * - SES, MetaMask, ad blocker hiçbir şey bozamaz
 * - WiseCP variable'lar defansif kontrolde
 * - Mutlak minimum kod
 */

// === WiseCP variables (defansif) ===
$product   = isset($product) && is_array($product) ? $product : [];
$proanse   = isset($proanse) && is_array($proanse) ? $proanse : $product;
$options   = isset($options) && is_array($options) ? $options : (isset($proanse['options']) && is_array($proanse['options']) ? $proanse['options'] : []);
$bills     = isset($bills) && is_array($bills) ? $bills : [];
$links     = isset($links) && is_array($links) ? $links : [];

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

// Panel girişi
$d_panel_url = $options['panel_url'] ?? ($options['cp_url'] ?? ($options['login_url'] ?? ''));

// Hosting limitler
$d_quota     = $options['disk'] ?? ($options['disk_space'] ?? '');
$d_bandwidth = $options['bandwidth'] ?? ($options['traffic'] ?? '');
$d_emails    = $options['emails'] ?? '';
$d_databases = $options['databases'] ?? '';

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

/* CSS-ONLY TAB: Radio button'lar gizli */
.cdg-h-tab-radio {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
}

/* Geri butonu */
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

/* Shell */
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

/* Tab nav (label'lar) */
.cdg-h-tabs {
    display: flex;
    gap: 2px;
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

/* AKTİF TAB STYLİNG - radio:checked + sibling label */
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

/* Pane'ler default gizli, checked olan radio kardeş seçici ile gösterilir */
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

/* Grid */
.cdg-h-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 768px) { .cdg-h-grid-2 { grid-template-columns: 1fr; } }

/* Button */
.cdg-h-btn {
    display: inline-flex; align-items: center; gap: 8px;
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

/* DirectAdmin panel kartı */
.cdg-h-panel-card {
    background: linear-gradient(135deg, var(--c-primary-deep), var(--c-primary));
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    margin: 14px 0;
}
.cdg-h-panel-card h4 { margin: 0 0 6px; font-size: 16px; font-weight: 800; color: #fff; }
.cdg-h-panel-card p { margin: 0 0 14px; opacity: 0.85; font-size: 13px; }
.cdg-h-panel-card a {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    background: var(--c-info); color: #0f172a !important;
    border-radius: 10px;
    font-weight: 700; font-size: 13px;
    transition: transform 0.15s;
}
.cdg-h-panel-card a:hover { transform: translateY(-1px); }
</style>

<div class="cdg-h">

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES); ?>" class="cdg-h-back">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <!-- ============================================
         CSS-ONLY TAB SİSTEMİ (HİÇ JS YOK)
         Radio inputlar shell'in DOĞRUDAN ÇOCUĞU
         Label[for]'lar bu input'lara bağlanır
         ============================================ -->
    <div class="cdg-h-shell">

        <!-- Radio button'lar (gizli) - tab seçimi için -->
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
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if($d_panel_url): ?>
                <div class="cdg-h-panel-card">
                    <h4><i class="bi bi-key-fill"></i> Kontrol Paneli</h4>
                    <p>Hosting yönetimi için DirectAdmin paneline giriş yapın</p>
                    <a href="<?php echo htmlspecialchars($d_panel_url, ENT_QUOTES); ?>" target="_blank" rel="noopener">
                        <i class="bi bi-box-arrow-up-right"></i> Panele Git
                    </a>
                </div>
                <?php endif; ?>

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
                        <strong>E-posta Yönetimi</strong><br>
                        E-posta hesaplarınızı oluşturmak ve yönetmek için DirectAdmin paneline giriş yapın.
                        <?php if($d_emails): ?>Paketinizde toplam <strong><?php echo (int)$d_emails; ?> e-posta hesabı</strong> hakkı vardır.<?php endif; ?>
                    </div>
                </div>
                <?php if($d_panel_url): ?>
                <div style="text-align:center;padding:20px;">
                    <a href="<?php echo htmlspecialchars($d_panel_url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-primary">
                        <i class="bi bi-envelope-plus"></i> E-posta Hesabı Oluştur (Panele Git)
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ŞİFRE -->
            <div class="cdg-h-pane" data-pane="password">
                <div class="cdg-h-alert cdg-h-alert-warn">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Şifre Değişikliği</strong><br>
                        Hosting şifrenizi değiştirmek için DirectAdmin paneline giriş yapın. Yeni şifreyi paneldeki "Hesap Yönetimi" bölümünden değiştirebilirsiniz.
                    </div>
                </div>
                <?php if($d_panel_url): ?>
                <div style="text-align:center;padding:20px;">
                    <a href="<?php echo htmlspecialchars($d_panel_url, ENT_QUOTES); ?>" target="_blank" rel="noopener" class="cdg-h-btn cdg-h-btn-primary">
                        <i class="bi bi-key"></i> Şifre Değiştir (Panele Git)
                    </a>
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
