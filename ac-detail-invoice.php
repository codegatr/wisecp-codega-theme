<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Fatura Detayı Sayfası
 * WiseCP runtime: $invoice, $items (öğeler), $form_action, $methods (ödeme yöntemleri)
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}

// === Yardımcı: link ===
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

// === Defansif defaults ===
$invoice      = isset($invoice) && is_array($invoice) ? $invoice : [];
$items        = [];
if(isset($invoice['items']) && is_array($invoice['items'])) $items = $invoice['items'];
elseif(isset($items) && is_array($items)) { /* zaten set */ } else { $items = []; }

$form_action  = isset($form_action) ? $form_action : '';
$methods      = isset($methods) && is_array($methods) ? $methods : [];
$user_data    = isset($invoice['user_data']) && is_array($invoice['user_data']) ? $invoice['user_data'] : [];

$inv_number   = $invoice['number'] ?? ($invoice['id'] ?? '?');
$inv_status   = $invoice['status'] ?? 'unpaid';
$inv_total    = $invoice['total'] ?? 0;
$inv_subtotal = $invoice['subtotal'] ?? 0;
$inv_tax      = $invoice['tax'] ?? 0;
$inv_taxrate  = $invoice['taxrate'] ?? 0;
$inv_currency = $invoice['currency'] ?? 'TRY';
$inv_cdate    = $invoice['cdate'] ?? '';
$inv_duedate  = $invoice['duedate'] ?? '';
$inv_datepaid = $invoice['datepaid'] ?? '';
$inv_pmethod  = $invoice['pmethod'] ?? '';
$inv_legal    = $invoice['legal'] ?? '';
$inv_taxed_file = $invoice['taxed_file'] ?? '';

// Tarih formatla
function cdg_inv_date($date) {
    if(!$date) return '';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        $fmt = Config::get("options/date-format") ?: 'd.m.Y';
        return DateManager::format($fmt, $date);
    }
    if(strpos($date, '1881') === 0 || strpos($date, '0000') === 0) return '';
    return date('d.m.Y', strtotime($date));
}

// Fiyat formatla
function cdg_inv_money($amount, $cid = 0) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol') && $cid) {
        return Money::formatter_symbol($amount, $cid);
    }
    return number_format((float)$amount, 2, ',', '.');
}

// Status -> renk + etiket
function cdg_inv_status_meta($status) {
    $meta = [
        'paid'    => ['cls' => 'cdg-inv-badge-success', 'lbl' => 'Ödendi',          'icon' => 'check-circle-fill'],
        'unpaid'  => ['cls' => 'cdg-inv-badge-warning', 'lbl' => 'Ödenmemiş',       'icon' => 'exclamation-circle-fill'],
        'waiting' => ['cls' => 'cdg-inv-badge-info',    'lbl' => 'Onay Bekleniyor', 'icon' => 'hourglass-split'],
        'refund'  => ['cls' => 'cdg-inv-badge-danger',  'lbl' => 'İade Edildi',     'icon' => 'arrow-counterclockwise'],
    ];
    return $meta[$status] ?? ['cls' => 'cdg-inv-badge-info', 'lbl' => ucfirst($status), 'icon' => 'question-circle'];
}
$status_meta = cdg_inv_status_meta($inv_status);

// Kullanıcı bilgileri
$u_kind        = $user_data['kind'] ?? 'individual';
$u_company     = $user_data['company_name'] ?? '';
// WiseCP runtime: full_name primary, name+surname fallback
$u_name        = '';
if(!empty($user_data['full_name'])) {
    $u_name = trim($user_data['full_name']);
}
if(!$u_name) {
    $u_name = trim(($user_data['name'] ?? '') . ' ' . ($user_data['surname'] ?? ''));
}
$u_email       = $user_data['email'] ?? '';
$u_phone       = $user_data['phone'] ?? ($user_data['gsm'] ?? '');
$u_address     = $user_data['address'] ?? '';
$u_city        = $user_data['city'] ?? '';
$u_counti      = $user_data['counti'] ?? '';
$u_zipcode     = $user_data['zipcode'] ?? '';
$u_country     = $user_data['country'] ?? ($user_data['country_id'] ?? '');
$u_identity    = $user_data['identity'] ?? '';
$u_taxoffice   = $user_data['company_tax_office'] ?? '';
$u_taxnumber   = $user_data['company_tax_number'] ?? '';

// Komisyon ve refund (Classic uyumlu)
$inv_commission     = $invoice['pmethod_commission'] ?? 0;
$inv_commission_rate = $invoice['pmethod_commission_rate'] ?? 0;
$inv_refunddate     = $invoice['refunddate'] ?? '';
$inv_sendbta        = !empty($invoice['sendbta']);
$inv_sendbta_amount = $invoice['sendbta_amount'] ?? 0;

// PDF link
$pdf_link = cdg_link('detail-invoice-pdf', [(int)($invoice['id'] ?? 0)]);
$invoices_url = cdg_link('invoices');

// İndirimler
$discount_items = [];
$total_discount = 0;
if(!empty($invoice['discounts'])) {
    if(class_exists('Utility') && method_exists('Utility','jdecode')) {
        $disc = Utility::jdecode($invoice['discounts'], true);
        if(isset($disc['items']) && is_array($disc['items'])) {
            $discount_items = $disc['items'];
            foreach($discount_items as $di) {
                $total_discount += (float)($di['amountd'] ?? 0);
            }
        }
    }
}
$discounted_total = $inv_subtotal - $total_discount;
?>

<style>
.cdg-inv {
    --inv-primary: #1e40af;
    --inv-success: #10b981;
    --inv-warning: #f59e0b;
    --inv-danger: #ef4444;
    --inv-info: #06b6d4;
    --inv-bg: #f8fafc;
    --inv-card: #fff;
    --inv-text: #0f172a;
    --inv-muted: #64748b;
    --inv-border: #e2e8f0;
    --inv-radius: 14px;
    --inv-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --inv-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--inv-text);
    background: var(--inv-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-inv *, .cdg-inv *::before, .cdg-inv *::after { box-sizing: border-box; }
.cdg-inv a { text-decoration: none; color: inherit; }

.cdg-inv-wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

/* TOP BAR */
.cdg-inv-topbar {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap; gap: 12px;
}
.cdg-inv-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--inv-border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--inv-text);
    transition: all 0.18s;
}
.cdg-inv-back:hover {
    border-color: var(--inv-primary);
    color: var(--inv-primary);
}
.cdg-inv-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.cdg-inv-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
    font-family: inherit;
}
.cdg-inv-btn-pay {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.30);
}
.cdg-inv-btn-pay:hover { transform: translateY(-1px); color: #fff; }
.cdg-inv-btn-pdf {
    background: #fff;
    color: var(--inv-text);
    border: 1px solid var(--inv-border);
}
.cdg-inv-btn-pdf:hover { border-color: var(--inv-primary); color: var(--inv-primary); }
.cdg-inv-btn-primary {
    background: var(--inv-primary);
    color: #fff;
}
.cdg-inv-btn-primary:hover { background: #1e3a8a; color: #fff; }

/* HERO BAR */
.cdg-inv-hero {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 60%, #06b6d4 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: center;
}
.cdg-inv-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-inv-hero-num {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 4px;
}
.cdg-inv-hero h1 {
    font-size: 30px;
    font-weight: 800;
    margin: 0 0 10px;
    letter-spacing: -0.5px;
}
.cdg-inv-hero-meta {
    display: flex; gap: 16px; flex-wrap: wrap;
    font-size: 13px;
    opacity: 0.92;
}
.cdg-inv-hero-meta span {
    display: inline-flex; align-items: center; gap: 6px;
}
.cdg-inv-hero-amount {
    text-align: right;
    position: relative; z-index: 1;
}
.cdg-inv-hero-amount-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 4px;
}
.cdg-inv-hero-amount-value {
    font-size: 36px;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1;
}

/* STATUS BADGE */
.cdg-inv-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 12px;
}
.cdg-inv-badge-success { background: #d1fae5; color: #065f46; }
.cdg-inv-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-inv-badge-danger  { background: #fee2e2; color: #991b1b; }
.cdg-inv-badge-info    { background: #dbeafe; color: #1e40af; }

.cdg-inv-hero .cdg-inv-badge {
    background: rgba(255,255,255,0.18);
    color: #fff;
    backdrop-filter: blur(10px);
}

/* CARD */
.cdg-inv-card {
    background: var(--inv-card);
    border: 1px solid var(--inv-border);
    border-radius: var(--inv-radius);
    box-shadow: var(--inv-shadow);
    margin-bottom: 18px;
    overflow: hidden;
}
.cdg-inv-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--inv-border);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-inv-card-head h3 {
    font-size: 14px;
    font-weight: 800;
    margin: 0;
    color: var(--inv-text);
    display: inline-flex; align-items: center; gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-inv-card-head h3 i { color: var(--inv-primary); font-size: 16px; }
.cdg-inv-card-body { padding: 20px 22px; }

/* TWO COL */
.cdg-inv-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}

/* INFO LIST */
.cdg-inv-info { list-style: none; padding: 0; margin: 0; }
.cdg-inv-info li {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px dashed var(--inv-border);
    font-size: 13px;
    gap: 12px;
}
.cdg-inv-info li:last-child { border-bottom: 0; padding-bottom: 0; }
.cdg-inv-info li:first-child { padding-top: 0; }
.cdg-inv-info-label {
    color: var(--inv-muted);
    font-weight: 600;
    flex-shrink: 0;
}
.cdg-inv-info-value {
    color: var(--inv-text);
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

/* ITEMS TABLE */
.cdg-inv-items {
    width: 100%;
    border-collapse: collapse;
}
.cdg-inv-items thead th {
    background: var(--inv-bg);
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--inv-muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 1px solid var(--inv-border);
}
.cdg-inv-items thead th:last-child { text-align: right; }
.cdg-inv-items tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--inv-border);
    font-size: 13px;
    vertical-align: top;
}
.cdg-inv-items tbody td:last-child { text-align: right; font-weight: 700; }
.cdg-inv-items tbody tr:last-child td { border-bottom: 0; }
.cdg-inv-items tbody tr.cdg-inv-discount td {
    background: #fef9e7;
    color: #92400e;
}

.cdg-inv-item-name { font-weight: 700; color: var(--inv-text); margin-bottom: 4px; }
.cdg-inv-item-desc { font-size: 12px; color: var(--inv-muted); line-height: 1.5; }

/* TOTALS */
.cdg-inv-totals {
    background: linear-gradient(135deg, #f8fafc, #fff);
    border-top: 2px solid var(--inv-border);
    padding: 18px 22px;
}
.cdg-inv-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 6px 0;
    font-size: 13px;
    color: var(--inv-text);
}
.cdg-inv-total-row strong { font-weight: 700; }
.cdg-inv-total-final {
    border-top: 2px solid var(--inv-border);
    margin-top: 10px;
    padding-top: 12px;
    font-size: 18px;
    font-weight: 800;
    color: var(--inv-primary);
}

/* PAYMENT METHODS */
.cdg-inv-pay-methods {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}
.cdg-inv-pm {
    border: 2px solid var(--inv-border);
    border-radius: 10px;
    padding: 16px 14px;
    cursor: pointer;
    transition: all 0.18s;
    text-align: center;
    background: #fff;
}
.cdg-inv-pm:hover { border-color: var(--inv-primary); transform: translateY(-2px); }
.cdg-inv-pm.selected {
    border-color: var(--inv-primary);
    background: #eff6ff;
    box-shadow: 0 6px 18px rgba(30,64,175,0.18);
}
.cdg-inv-pm input[type="radio"] { display: none; }
.cdg-inv-pm i {
    font-size: 28px;
    color: var(--inv-primary);
    margin-bottom: 6px;
    display: block;
}
.cdg-inv-pm-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--inv-text);
}

/* PAID NOTE */
.cdg-inv-paid-note {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1px solid #6ee7b7;
    border-radius: var(--inv-radius);
    padding: 18px 22px;
    display: flex; align-items: center; gap: 14px;
    color: #065f46;
}
.cdg-inv-paid-note i { font-size: 28px; flex-shrink: 0; }
.cdg-inv-paid-note h4 { font-size: 14px; font-weight: 800; margin: 0 0 2px; }
.cdg-inv-paid-note p { font-size: 12px; margin: 0; opacity: 0.85; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .cdg-inv-hero {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 24px 20px;
    }
    .cdg-inv-hero-amount { text-align: center; }
    .cdg-inv-hero h1 { font-size: 24px; }
    .cdg-inv-hero-amount-value { font-size: 28px; }
    .cdg-inv-grid-2 { grid-template-columns: 1fr; }
    .cdg-inv-items thead { display: none; }
    .cdg-inv-items tbody td { display: block; padding: 6px 16px; border: 0; }
    .cdg-inv-items tbody tr { border-bottom: 1px solid var(--inv-border); padding: 10px 0; display: block; }
    .cdg-inv-items tbody td:last-child { text-align: left; }
}
</style>

<div class="cdg-inv">
<div class="cdg-inv-wrap">

    <!-- TOP BAR -->
    <div class="cdg-inv-topbar">
        <a href="<?php echo htmlspecialchars($invoices_url); ?>" class="cdg-inv-back">
            <i class="bi bi-arrow-left"></i> Faturalarıma Dön
        </a>
        <div class="cdg-inv-actions">
            <?php if($pdf_link): ?>
            <a href="<?php echo htmlspecialchars($pdf_link); ?>" target="_blank" rel="noopener" class="cdg-inv-btn cdg-inv-btn-pdf">
                <i class="bi bi-file-earmark-pdf"></i> PDF İndir
            </a>
            <?php endif; ?>
            <?php if(!empty($inv_taxed_file)): ?>
            <a href="<?php echo htmlspecialchars($inv_taxed_file); ?>" target="_blank" rel="noopener" class="cdg-inv-btn cdg-inv-btn-pdf">
                <i class="bi bi-receipt"></i> e-Fatura
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- HERO -->
    <section class="cdg-inv-hero">
        <div>
            <div class="cdg-inv-badge">
                <i class="bi bi-<?php echo htmlspecialchars($status_meta['icon']); ?>"></i>
                <?php echo htmlspecialchars($status_meta['lbl']); ?>
            </div>
            <div class="cdg-inv-hero-num">FATURA</div>
            <h1>#<?php echo htmlspecialchars($inv_number); ?></h1>
            <div class="cdg-inv-hero-meta">
                <?php if($inv_cdate): ?>
                <span><i class="bi bi-calendar-event"></i> Düzenleme: <?php echo htmlspecialchars(cdg_inv_date($inv_cdate)); ?></span>
                <?php endif; ?>
                <?php if($inv_duedate): ?>
                <span><i class="bi bi-calendar-check"></i> Son Ödeme: <?php echo htmlspecialchars(cdg_inv_date($inv_duedate)); ?></span>
                <?php endif; ?>
                <?php if($inv_status === 'paid' && $inv_datepaid): ?>
                <span><i class="bi bi-check2-circle"></i> Ödendi: <?php echo htmlspecialchars(cdg_inv_date($inv_datepaid)); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="cdg-inv-hero-amount">
            <div class="cdg-inv-hero-amount-label">Toplam</div>
            <div class="cdg-inv-hero-amount-value"><?php echo htmlspecialchars(cdg_inv_money($inv_total)); ?> <?php echo htmlspecialchars($inv_currency); ?></div>
        </div>
    </section>

    <!-- ÖDENDİ NOTU -->
    <?php if($inv_status === 'paid'): ?>
    <div class="cdg-inv-paid-note">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <h4>Bu fatura ödenmiştir</h4>
            <p>
                Ödeme tarihi: <?php echo htmlspecialchars(cdg_inv_date($inv_datepaid)); ?> · Yöntem: <?php echo htmlspecialchars($inv_pmethod ?: 'Bilinmiyor'); ?>
                <?php if($inv_commission > 0): ?>
                · Komisyon: <strong><?php echo htmlspecialchars(cdg_inv_money($inv_commission, $inv_currency)); ?></strong>
                <?php if($inv_commission_rate): ?>(<?php echo htmlspecialchars($inv_commission_rate); ?>%)<?php endif; ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php elseif($inv_status === 'refund' || $inv_refunddate): ?>
    <div class="cdg-inv-paid-note" style="background:linear-gradient(135deg,#fee2e2,#fecaca);border-color:#fca5a5;color:#991b1b;">
        <i class="bi bi-arrow-counterclockwise"></i>
        <div>
            <h4>Bu fatura iade edilmiştir</h4>
            <p>
                İade tarihi: <?php echo htmlspecialchars(cdg_inv_date($inv_refunddate)); ?>
                <?php if($inv_datepaid && substr($inv_datepaid, 0, 4) !== '1881'): ?>
                · Önceden ödendi: <?php echo htmlspecialchars(cdg_inv_date($inv_datepaid)); ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- İKİ SUTUNLU -->
    <div class="cdg-inv-grid-2">

        <!-- FATURA SAHİBİ -->
        <div class="cdg-inv-card">
            <div class="cdg-inv-card-head">
                <h3><i class="bi bi-person-vcard"></i> Fatura Sahibi</h3>
            </div>
            <div class="cdg-inv-card-body">
                <ul class="cdg-inv-info">
                    <?php if($u_kind === 'corporate' || $u_kind === 'company'): ?>
                        <?php if($u_company): ?>
                        <li><span class="cdg-inv-info-label">Firma</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_company); ?></span></li>
                        <?php endif; ?>
                        <?php if($u_taxoffice): ?>
                        <li><span class="cdg-inv-info-label">Vergi Dairesi</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_taxoffice); ?></span></li>
                        <?php endif; ?>
                        <?php if($u_taxnumber): ?>
                        <li><span class="cdg-inv-info-label">Vergi No</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_taxnumber); ?></span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($u_name): ?>
                    <li><span class="cdg-inv-info-label">Ad Soyad</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_name); ?></span></li>
                    <?php endif; ?>

                    <?php if(($u_kind === 'individual') && $u_identity): ?>
                    <li><span class="cdg-inv-info-label">TC Kimlik</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_identity); ?></span></li>
                    <?php endif; ?>

                    <?php if($u_email): ?>
                    <li><span class="cdg-inv-info-label">E-Posta</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_email); ?></span></li>
                    <?php endif; ?>
                    <?php if($u_phone): ?>
                    <li><span class="cdg-inv-info-label">Telefon</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($u_phone); ?></span></li>
                    <?php endif; ?>
                    <?php if($u_address):
                        $addr_parts = [$u_address];
                        if($u_counti) $addr_parts[] = $u_counti;
                        if($u_city) $addr_parts[] = $u_city;
                        if($u_zipcode) $addr_parts[] = $u_zipcode;
                        if($u_country) $addr_parts[] = $u_country;
                        $full_addr = implode(', ', array_filter($addr_parts));
                    ?>
                    <li><span class="cdg-inv-info-label">Adres</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($full_addr); ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- FATURA BİLGİLERİ -->
        <div class="cdg-inv-card">
            <div class="cdg-inv-card-head">
                <h3><i class="bi bi-file-earmark-text"></i> Fatura Bilgileri</h3>
            </div>
            <div class="cdg-inv-card-body">
                <ul class="cdg-inv-info">
                    <li><span class="cdg-inv-info-label">Fatura No</span><span class="cdg-inv-info-value">#<?php echo htmlspecialchars($inv_number); ?></span></li>
                    <li><span class="cdg-inv-info-label">Durum</span><span class="cdg-inv-info-value"><span class="cdg-inv-badge <?php echo $status_meta['cls']; ?>" style="margin:0;"><?php echo htmlspecialchars($status_meta['lbl']); ?></span></span></li>
                    <?php if($inv_cdate): ?>
                    <li><span class="cdg-inv-info-label">Düzenleme Tarihi</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars(cdg_inv_date($inv_cdate)); ?></span></li>
                    <?php endif; ?>
                    <?php if($inv_duedate): ?>
                    <li><span class="cdg-inv-info-label">Son Ödeme</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars(cdg_inv_date($inv_duedate)); ?></span></li>
                    <?php endif; ?>
                    <?php if($inv_pmethod): ?>
                    <li><span class="cdg-inv-info-label">Ödeme Yöntemi</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($inv_pmethod); ?></span></li>
                    <?php endif; ?>
                    <?php if($inv_legal): ?>
                    <li><span class="cdg-inv-info-label">e-Fatura No</span><span class="cdg-inv-info-value"><?php echo htmlspecialchars($inv_legal); ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>

    <!-- ÖĞELER TABLOSU -->
    <div class="cdg-inv-card">
        <div class="cdg-inv-card-head">
            <h3><i class="bi bi-list-check"></i> Fatura Öğeleri</h3>
        </div>

        <table class="cdg-inv-items">
            <thead>
                <tr>
                    <th>Açıklama</th>
                    <th style="text-align:right;">Tutar</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($items)): ?>
                <tr><td colspan="2" style="text-align:center;color:var(--inv-muted);padding:30px;">Fatura öğesi bulunamadı.</td></tr>
                <?php else: ?>
                    <?php foreach($items as $item):
                        $iname = $item['name'] ?? 'Öğe';
                        $idesc = $item['description'] ?? '';
                        $iamount = $item['amount'] ?? 0;
                        $icid = $item['cid'] ?? 0;
                        $itotal = $item['total_amount'] ?? $iamount;
                    ?>
                    <tr>
                        <td>
                            <div class="cdg-inv-item-name"><?php echo htmlspecialchars($iname); ?></div>
                            <?php if($idesc): ?>
                            <div class="cdg-inv-item-desc"><?php echo nl2br(htmlspecialchars($idesc)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars(cdg_inv_money($itotal, $icid)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- İndirimler -->
                <?php if(!empty($discount_items)): ?>
                    <?php foreach($discount_items as $di):
                        $dname = $di['name'] ?? 'İndirim';
                        $damount = $di['amountd'] ?? 0;
                    ?>
                    <tr class="cdg-inv-discount">
                        <td>
                            <div class="cdg-inv-item-name"><i class="bi bi-tag-fill"></i> <?php echo htmlspecialchars($dname); ?></div>
                            <div class="cdg-inv-item-desc">İndirim uygulandı</div>
                        </td>
                        <td>-<?php echo htmlspecialchars(cdg_inv_money($damount)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TOPLAMLAR -->
        <div class="cdg-inv-totals">
            <div class="cdg-inv-total-row">
                <span>Ara Toplam</span>
                <strong><?php echo htmlspecialchars(cdg_inv_money($inv_subtotal)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
            </div>
            <?php if($total_discount > 0): ?>
            <div class="cdg-inv-total-row" style="color:#92400e;">
                <span>İndirim</span>
                <strong>-<?php echo htmlspecialchars(cdg_inv_money($total_discount)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
            </div>
            <?php endif; ?>
            <?php if($inv_tax > 0): ?>
            <div class="cdg-inv-total-row">
                <span>KDV (<?php echo (int)$inv_taxrate; ?>%)</span>
                <strong><?php echo htmlspecialchars(cdg_inv_money($inv_tax)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
            </div>
            <?php endif; ?>
            <div class="cdg-inv-total-row cdg-inv-total-final">
                <span>GENEL TOPLAM</span>
                <strong><?php echo htmlspecialchars(cdg_inv_money($inv_total)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
            </div>
        </div>
    </div>

    <!-- ÖDEME -->
    <?php if($inv_status === 'unpaid' && $form_action): ?>
    <div class="cdg-inv-card">
        <div class="cdg-inv-card-head">
            <h3><i class="bi bi-credit-card-2-front"></i> Faturayı Öde</h3>
        </div>
        <div class="cdg-inv-card-body">

            <?php if(!empty($methods)): ?>
            <form method="post" action="<?php echo htmlspecialchars($form_action); ?>" id="cdg-pay-form">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('pay-invoice'); ?>

                <div class="cdg-inv-pay-methods">
                    <?php $first = true; foreach($methods as $m_id => $m_data):
                        $m_name = is_array($m_data) ? ($m_data['name'] ?? $m_id) : $m_data;
                        $m_icon = (is_array($m_data) && isset($m_data['icon'])) ? $m_data['icon'] : 'bank2';
                    ?>
                    <label class="cdg-inv-pm <?php echo $first ? 'selected' : ''; ?>">
                        <input type="radio" name="pmethod" value="<?php echo htmlspecialchars($m_id); ?>" <?php echo $first ? 'checked' : ''; ?>>
                        <i class="bi bi-<?php echo htmlspecialchars($m_icon); ?>"></i>
                        <div class="cdg-inv-pm-label"><?php echo htmlspecialchars($m_name); ?></div>
                    </label>
                    <?php $first = false; endforeach; ?>
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:20px;">
                    <button type="submit" class="cdg-inv-btn cdg-inv-btn-pay">
                        <i class="bi bi-shield-lock-fill"></i> Güvenli Öde · <?php echo htmlspecialchars(cdg_inv_money($inv_total)); ?> <?php echo htmlspecialchars($inv_currency); ?>
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div style="text-align:center;padding:30px;color:var(--inv-muted);">
                <i class="bi bi-info-circle" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                Ödeme yöntemi yapılandırılmamış. Lütfen yöneticiyle iletişime geçin.
            </div>
            <?php endif; ?>

        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
(function(){
    // Ödeme yöntemi seçimi
    document.querySelectorAll('.cdg-inv-pm').forEach(function(pm){
        pm.addEventListener('click', function(){
            document.querySelectorAll('.cdg-inv-pm').forEach(function(p){ p.classList.remove('selected'); });
            this.classList.add('selected');
            var radio = this.querySelector('input[type="radio"]');
            if(radio) radio.checked = true;
        });
    });
})();
</script>
