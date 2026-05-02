<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Affiliate / Tavsiye Programı
 * 4 tab: İşlemler, Çekimler, Tıklamalar, Banner'lar
 * WiseCP runtime: $aff, $links, $transactions, $withdrawals, $hits, $banners, $rates_conditions, $header_title
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$wide_content = true;
$hoptions = ["datatables", "iziModal", "select2"];

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
// WiseCP runtime - Classic standardi: $transaction_list, $withdrawals_list, $hits, $banners
$aff          = isset($aff) && is_array($aff) ? $aff : [];
$links        = isset($links) && is_array($links) ? $links : [];

// Classic primary, bizim eski isimler fallback
if(isset($transaction_list) && is_array($transaction_list)) {
    $transactions = $transaction_list;
} elseif(!isset($transactions) || !is_array($transactions)) {
    $transactions = [];
}
if(isset($withdrawals_list) && is_array($withdrawals_list)) {
    $withdrawals = $withdrawals_list;
} elseif(!isset($withdrawals) || !is_array($withdrawals)) {
    $withdrawals = [];
}
$hits         = isset($hits) && is_array($hits) ? $hits : [];
$banners      = isset($banners) && is_array($banners) ? $banners : [];
$rates_conditions = isset($rates_conditions) ? $rates_conditions : null;
$header_title = $header_title ?? 'Tavsiye Programı';

$aff_disabled      = !empty($aff['disabled']);
$aff_disabled_note = $aff['disabled_note'] ?? '';
$aff_currency      = $aff['currency'] ?? 'TRY';
$aff_code          = $aff['code'] ?? ($aff['ref_code'] ?? '');
$aff_rate          = $aff['rate'] ?? 0;
$aff_balance       = $aff['balance'] ?? 0;

$controller_url = $links['controller'] ?? '';
$tracking_url   = $links['tracking'] ?? '';
$withdrawal_url = $links['withdrawal'] ?? '';
$rates_cond_url = $links['rates-conditions'] ?? '';
$payment_info_url = $links['payment-information'] ?? '';

// Toplam çekilen
$total_withdrawn = 0;
foreach($withdrawals as $w) {
    if(is_array($w) && !empty($w['completed_time'])) {
        $total_withdrawn += (float)($w['amount'] ?? 0);
    }
}

// Toplam tıklama
$total_hits = 0;
foreach($hits as $h) {
    if(is_array($h)) $total_hits += (int)($h['hits'] ?? 0);
}

function cdg_aff_money($a) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
        return Money::formatter_symbol($a);
    }
    return number_format((float)$a, 2, ',', '.');
}
function cdg_aff_date($d) {
    if(!$d) return '-';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        return DateManager::format(Config::get("options/date-format") ?: 'd.m.Y', $d);
    }
    return date('d.m.Y', strtotime((string)$d));
}
?>

<style>
.cdg-aff {
    --aff-primary: #1e40af;
    --aff-success: #10b981;
    --aff-warning: #f59e0b;
    --aff-danger: #ef4444;
    --aff-bg: #f8fafc;
    --aff-card: #fff;
    --aff-text: #0f172a;
    --aff-muted: #64748b;
    --aff-border: #e2e8f0;
    --aff-radius: 14px;
    --aff-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--aff-text);
    background: var(--aff-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-aff *, .cdg-aff *::before, .cdg-aff *::after { box-sizing: border-box; }
.cdg-aff a { text-decoration: none; color: inherit; }
.cdg-aff-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-aff-hero {
    background: linear-gradient(135deg, #1e40af 0%, #06b6d4 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
}
.cdg-aff-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-aff-hero-row {
    display: flex; align-items: center; gap: 18px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-aff-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
}
.cdg-aff-hero-text { flex: 1; min-width: 200px; }
.cdg-aff-hero-text h1 { font-size: 26px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-aff-hero-text p { font-size: 13px; opacity: 0.88; margin: 0; }

.cdg-aff-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    text-decoration: none;
    font-family: inherit;
    white-space: nowrap;
}
.cdg-aff-btn-gold {
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1e3a8a;
    box-shadow: 0 6px 18px rgba(252,211,77,0.30);
}
.cdg-aff-btn-gold:hover { transform: translateY(-1px); color: #1e3a8a; }
.cdg-aff-btn-success {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.22);
}
.cdg-aff-btn-success:hover { transform: translateY(-1px); color: #fff; }
.cdg-aff-btn-outline {
    background: #fff;
    color: var(--aff-text);
    border: 1px solid var(--aff-border);
}
.cdg-aff-btn-outline:hover { border-color: var(--aff-primary); color: var(--aff-primary); }
.cdg-aff-btn-sm { padding: 7px 14px; font-size: 12px; }

.cdg-aff-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-bottom: 22px;
}
.cdg-aff-stat-card {
    background: var(--aff-card);
    border: 1px solid var(--aff-border);
    border-radius: var(--aff-radius);
    padding: 18px 22px;
    box-shadow: var(--aff-shadow);
    display: flex; align-items: center; gap: 14px;
}
.cdg-aff-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
.cdg-aff-stat-card-balance .cdg-aff-stat-icon { background: linear-gradient(135deg, #10b981, #34d399); }
.cdg-aff-stat-card-withdrawn .cdg-aff-stat-icon { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.cdg-aff-stat-card-rate .cdg-aff-stat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.cdg-aff-stat-card-clicks .cdg-aff-stat-icon { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.cdg-aff-stat-info { flex: 1; min-width: 0; }
.cdg-aff-stat-label {
    font-size: 11px;
    color: var(--aff-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 2px;
}
.cdg-aff-stat-value {
    font-size: 20px;
    font-weight: 900;
    color: var(--aff-text);
    line-height: 1.2;
}
.cdg-aff-stat-value small { font-size: 13px; font-weight: 600; }

.cdg-aff-tracking {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px dashed #fcd34d;
    border-radius: var(--aff-radius);
    padding: 18px 22px;
    margin-bottom: 22px;
    display: flex; align-items: center; gap: 14px;
    flex-wrap: wrap;
}
.cdg-aff-tracking-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: #f59e0b;
    color: #fff;
    display: grid; place-items: center;
    font-size: 19px;
    flex-shrink: 0;
}
.cdg-aff-tracking-info { flex: 1; min-width: 200px; }
.cdg-aff-tracking-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 700;
    color: #78350f;
}
.cdg-aff-tracking-url {
    font-size: 14px;
    font-weight: 700;
    color: #78350f;
    word-break: break-all;
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    margin-top: 3px;
}

.cdg-aff-tabs {
    background: #fff;
    border: 1px solid var(--aff-border);
    border-radius: var(--aff-radius);
    padding: 8px;
    box-shadow: var(--aff-shadow);
    margin-bottom: 18px;
    display: flex;
    gap: 4px;
    overflow-x: auto;
}
.cdg-aff-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--aff-muted);
    cursor: pointer;
    background: transparent;
    border: 0;
    font-family: inherit;
    white-space: nowrap;
    transition: all 0.18s;
}
.cdg-aff-tab:hover { background: var(--aff-bg); color: var(--aff-text); }
.cdg-aff-tab.active {
    background: var(--aff-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(30,64,175,0.22);
}

.cdg-aff-pane { display: none; }
.cdg-aff-pane.active { display: block; }

.cdg-aff-card {
    background: #fff;
    border: 1px solid var(--aff-border);
    border-radius: var(--aff-radius);
    box-shadow: var(--aff-shadow);
    overflow: hidden;
}
.cdg-aff-card-head {
    padding: 14px 20px;
    border-bottom: 1px solid var(--aff-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-aff-card-head h3 {
    font-size: 13px; font-weight: 800; margin: 0;
    text-transform: uppercase; letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-aff-card-head h3 i { color: var(--aff-primary); }

.cdg-aff-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.cdg-aff-table thead th {
    background: #f8fafc;
    color: var(--aff-muted);
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--aff-border);
}
.cdg-aff-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--aff-border);
    color: var(--aff-text);
}
.cdg-aff-table tbody tr:last-child td { border-bottom: 0; }
.cdg-aff-table tbody tr:hover td { background: #fafbfc; }

.cdg-aff-empty {
    text-align: center;
    padding: 50px 20px;
    color: var(--aff-muted);
}
.cdg-aff-empty i {
    font-size: 48px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 10px;
}

.cdg-aff-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-aff-badge-success { background: #d1fae5; color: #065f46; }
.cdg-aff-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-aff-badge-info    { background: #dbeafe; color: #1e40af; }
.cdg-aff-badge-danger  { background: #fee2e2; color: #991b1b; }

.cdg-aff-disabled {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 1px solid #fca5a5;
    border-radius: var(--aff-radius);
    padding: 18px 22px;
    color: #991b1b;
    text-align: center;
    margin-bottom: 22px;
}
.cdg-aff-disabled i { font-size: 32px; display: block; margin-bottom: 8px; }
.cdg-aff-disabled strong { display: block; margin-bottom: 4px; }

@media (max-width: 768px) {
    .cdg-aff-hero-row { flex-direction: column; text-align: center; }
    .cdg-aff-table { font-size: 12px; }
    .cdg-aff-table thead th, .cdg-aff-table tbody td { padding: 10px 12px; }
}
</style>

<div class="cdg-aff">
<div class="cdg-aff-wrap">

    <section class="cdg-aff-hero">
        <div class="cdg-aff-hero-row">
            <div class="cdg-aff-hero-icon"><i class="bi bi-people-fill"></i></div>
            <div class="cdg-aff-hero-text">
                <h1>Tavsiye Programı</h1>
                <p>Tanıttığınız her müşteri için komisyon kazanın. Tracking linkinizi paylaşın, kazançlarınızı çekin.</p>
            </div>
            <?php if(!$aff_disabled && $withdrawal_url): ?>
            <a href="<?php echo htmlspecialchars($withdrawal_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-aff-btn cdg-aff-btn-gold">
                <i class="bi bi-cash-stack"></i> Para Çek
            </a>
            <?php endif; ?>
            <?php if($rates_cond_url): ?>
            <a href="<?php echo htmlspecialchars($rates_cond_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-aff-btn cdg-aff-btn-outline">
                <i class="bi bi-info-circle"></i> Koşullar
            </a>
            <?php endif; ?>
        </div>
    </section>

    <?php if($aff_disabled): ?>
    <div class="cdg-aff-disabled">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Tavsiye Programınız Pasif Durumda</strong>
        <?php if($aff_disabled_note): ?>
        <span><?php echo htmlspecialchars($aff_disabled_note, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        <?php else: ?>
        <span>Programa katılmak için lütfen destek ekibimizle iletişime geçin.</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="cdg-aff-stats">
        <div class="cdg-aff-stat-card cdg-aff-stat-card-balance">
            <div class="cdg-aff-stat-icon"><i class="bi bi-wallet2"></i></div>
            <div class="cdg-aff-stat-info">
                <div class="cdg-aff-stat-label">Mevcut Bakiye</div>
                <div class="cdg-aff-stat-value"><?php echo htmlspecialchars(cdg_aff_money($aff_balance), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <small><?php echo htmlspecialchars($aff_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></small></div>
            </div>
        </div>
        <div class="cdg-aff-stat-card cdg-aff-stat-card-withdrawn">
            <div class="cdg-aff-stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="cdg-aff-stat-info">
                <div class="cdg-aff-stat-label">Toplam Çekilen</div>
                <div class="cdg-aff-stat-value"><?php echo htmlspecialchars(cdg_aff_money($total_withdrawn), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <small><?php echo htmlspecialchars($aff_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></small></div>
            </div>
        </div>
        <div class="cdg-aff-stat-card cdg-aff-stat-card-rate">
            <div class="cdg-aff-stat-icon"><i class="bi bi-percent"></i></div>
            <div class="cdg-aff-stat-info">
                <div class="cdg-aff-stat-label">Komisyon Oranı</div>
                <div class="cdg-aff-stat-value">%<?php echo (float)$aff_rate; ?></div>
            </div>
        </div>
        <div class="cdg-aff-stat-card cdg-aff-stat-card-clicks">
            <div class="cdg-aff-stat-icon"><i class="bi bi-cursor"></i></div>
            <div class="cdg-aff-stat-info">
                <div class="cdg-aff-stat-label">Toplam Tıklama</div>
                <div class="cdg-aff-stat-value"><?php echo (int)$total_hits; ?></div>
            </div>
        </div>

        <?php
        // Yonlendirme sayilari (WiseCP runtime: $references_today, $references_total)
        $ref_today = isset($references_today) ? (int)$references_today : 0;
        $ref_total = isset($references_total) ? (int)$references_total : 0;
        if($ref_today > 0 || $ref_total > 0):
        ?>
        <div class="cdg-aff-stat-card" style="border-left:4px solid #ef4444;">
            <div class="cdg-aff-stat-icon" style="background:linear-gradient(135deg,#ef4444,#f87171);"><i class="bi bi-people"></i></div>
            <div class="cdg-aff-stat-info">
                <div class="cdg-aff-stat-label">Yönlendirme</div>
                <div class="cdg-aff-stat-value"><?php echo $ref_today; ?></div>
                <div style="font-size:11px;color:#64748b;margin-top:4px;">Toplam: <strong><?php echo $ref_total; ?></strong></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($tracking_url): ?>
    <div class="cdg-aff-tracking">
        <div class="cdg-aff-tracking-icon"><i class="bi bi-link-45deg"></i></div>
        <div class="cdg-aff-tracking-info">
            <div class="cdg-aff-tracking-label">Tavsiye / Tracking Linkiniz</div>
            <div class="cdg-aff-tracking-url" id="cdg-tracking-url"><?php echo htmlspecialchars($tracking_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
        </div>
        <button type="button" class="cdg-aff-btn cdg-aff-btn-success" onclick="cdgAffCopyTracking()">
            <i class="bi bi-clipboard"></i> <span id="cdg-copy-text">Kopyala</span>
        </button>
    </div>
    <?php endif; ?>

    <div class="cdg-aff-tabs">
        <button class="cdg-aff-tab active" data-pane="transactions"><i class="bi bi-receipt"></i> İşlemler</button>
        <button class="cdg-aff-tab" data-pane="withdrawals"><i class="bi bi-cash-coin"></i> Para Çekimleri</button>
        <button class="cdg-aff-tab" data-pane="hits"><i class="bi bi-cursor"></i> Tıklamalar</button>
        <?php if(isset($referrer_list) && is_array($referrer_list) && !empty($referrer_list)): ?>
        <button class="cdg-aff-tab" data-pane="referrers"><i class="bi bi-people"></i> Yönlendirmeler</button>
        <?php endif; ?>
        <button class="cdg-aff-tab" data-pane="banners"><i class="bi bi-image"></i> Banner'lar</button>
    </div>

    <!-- TRANSACTIONS -->
    <div class="cdg-aff-pane active" id="cdg-aff-pane-transactions">
        <div class="cdg-aff-card">
            <div class="cdg-aff-card-head">
                <h3><i class="bi bi-receipt"></i> Komisyon İşlemleri</h3>
            </div>
            <?php if(empty($transactions)): ?>
            <div class="cdg-aff-empty">
                <i class="bi bi-inbox"></i>
                <p>Henüz komisyon işleminiz yok. Tracking linkinizi paylaşmaya başlayın!</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="cdg-aff-table">
                <thead>
                    <tr>
                        <th>Sipariş</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Oran</th>
                        <th>Komisyon</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $row):
                        if(!is_array($row)) continue;
                        $r_complete = !empty($row['completed_time']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_name'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(cdg_aff_money($row['amount'] ?? 0), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td>%<?php echo htmlspecialchars($row['rate'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><strong style="color:#10b981;"><?php echo htmlspecialchars(cdg_aff_money($row['commission'] ?? 0), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong></td>
                        <td><?php echo htmlspecialchars(cdg_aff_date($row['ctime'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td>
                            <?php if($r_complete): ?>
                            <span class="cdg-aff-badge cdg-aff-badge-success">Tamamlandı</span>
                            <?php else: ?>
                            <span class="cdg-aff-badge cdg-aff-badge-warning">Bekliyor</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- WITHDRAWALS -->
    <div class="cdg-aff-pane" id="cdg-aff-pane-withdrawals">
        <div class="cdg-aff-card">
            <div class="cdg-aff-card-head">
                <h3><i class="bi bi-cash-coin"></i> Para Çekim Geçmişi</h3>
                <?php if(!$aff_disabled && $withdrawal_url): ?>
                <a href="<?php echo htmlspecialchars($withdrawal_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-aff-btn cdg-aff-btn-success cdg-aff-btn-sm">
                    <i class="bi bi-plus"></i> Yeni Çekim
                </a>
                <?php endif; ?>
            </div>
            <?php if(empty($withdrawals)): ?>
            <div class="cdg-aff-empty">
                <i class="bi bi-cash"></i>
                <p>Henüz para çekimi yapmadınız.</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="cdg-aff-table">
                <thead>
                    <tr>
                        <th>Tutar</th>
                        <th>Yöntem</th>
                        <th>Hesap Bilgisi</th>
                        <th>Talep Tarihi</th>
                        <th>Sonuç Tarihi</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($withdrawals as $row):
                        if(!is_array($row)) continue;
                        $w_complete = !empty($row['completed_time']);
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars(cdg_aff_money($row['amount'] ?? 0), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['gateway'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td style="font-size:12px;color:var(--aff-muted);"><?php echo htmlspecialchars($row['gateway_info'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(cdg_aff_date($row['ctime'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><?php echo $w_complete ? htmlspecialchars(cdg_aff_date($row['completed_time']), ENT_QUOTES | ENT_HTML5, 'UTF-8') : '-'; ?></td>
                        <td>
                            <?php if($w_complete): ?>
                            <span class="cdg-aff-badge cdg-aff-badge-success">Ödendi</span>
                            <?php else: ?>
                            <span class="cdg-aff-badge cdg-aff-badge-warning">Bekliyor</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- HITS -->
    <div class="cdg-aff-pane" id="cdg-aff-pane-hits">
        <div class="cdg-aff-card">
            <div class="cdg-aff-card-head">
                <h3><i class="bi bi-cursor"></i> Tracking Linkinden Gelen Ziyaretçiler</h3>
            </div>
            <?php if(empty($hits)): ?>
            <div class="cdg-aff-empty">
                <i class="bi bi-cursor"></i>
                <p>Henüz tracking linkinizden ziyaretçi gelmedi. Linkinizi paylaşmaya başlayın!</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="cdg-aff-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Tıklama Sayısı</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($hits as $row):
                        if(!is_array($row)) continue;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars(cdg_aff_date($row['ctime'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><strong><?php echo (int)($row['hits'] ?? 0); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REFERRERS / YONLENDIRMELER -->
    <?php if(isset($referrer_list) && is_array($referrer_list) && !empty($referrer_list)): ?>
    <div class="cdg-aff-pane" id="cdg-aff-pane-referrers">
        <div class="cdg-aff-card">
            <div class="cdg-aff-card-head">
                <h3><i class="bi bi-people"></i> Yönlendirilen Kullanıcılar</h3>
                <span style="background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;">
                    Toplam: <?php echo count($referrer_list); ?>
                </span>
            </div>
            <div style="overflow-x:auto;">
            <table class="cdg-aff-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Yönlendiren URL</th>
                        <th style="width:120px;text-align:center;">Tıklama</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($referrer_list as $k => $row):
                        if(!is_array($row)) continue;
                        $r_url = $row['referrer'] ?? '';
                        $r_hits = $row['hits'] ?? 0;
                    ?>
                    <tr>
                        <td><?php echo (int)$k + 1; ?></td>
                        <td>
                            <?php if($r_url): ?>
                            <a href="<?php echo htmlspecialchars($r_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noreferrer noopener" style="color:#1e40af;text-decoration:none;word-break:break-all;">
                                <i class="bi bi-link-45deg"></i> <?php echo htmlspecialchars($r_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </a>
                            <?php else: ?>
                            <span style="color:#94a3b8;font-style:italic;">Doğrudan ziyaret</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;"><strong style="color:#1e40af;"><?php echo (int)$r_hits; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- BANNERS -->
    <div class="cdg-aff-pane" id="cdg-aff-pane-banners">
        <div class="cdg-aff-card">
            <div class="cdg-aff-card-head">
                <h3><i class="bi bi-image"></i> Hazır Banner ve Görseller</h3>
            </div>
            <div style="padding:22px;">
                <?php if(empty($banners)): ?>
                <div class="cdg-aff-empty" style="padding:30px 20px;">
                    <i class="bi bi-images"></i>
                    <p>Henüz hazır banner bulunmuyor. Yakında eklenecektir.</p>
                </div>
                <?php else: ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;">
                    <?php foreach($banners as $banner):
                        if(!is_array($banner)) continue;
                        $b_url  = $banner['url'] ?? $banner['link'] ?? '';
                        $b_size = $banner['size'] ?? '';
                        $b_html = $banner['html'] ?? '';
                    ?>
                    <div style="border:1px solid var(--aff-border);border-radius:10px;padding:14px;text-align:center;">
                        <?php if($b_url): ?>
                        <img src="<?php echo htmlspecialchars($b_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="banner" style="max-width:100%;height:auto;border-radius:6px;margin-bottom:8px;">
                        <?php endif; ?>
                        <div style="font-size:12px;color:var(--aff-muted);margin-bottom:8px;"><?php echo htmlspecialchars($b_size, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <?php if($b_html): ?>
                        <textarea readonly style="width:100%;font-family:monospace;font-size:11px;padding:6px;border:1px solid var(--aff-border);border-radius:6px;resize:vertical;min-height:60px;" onclick="this.select();"><?php echo htmlspecialchars($b_html, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></textarea>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
</div>

<script>
(function(){
    document.querySelectorAll('.cdg-aff-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            var pane = this.getAttribute('data-pane');
            document.querySelectorAll('.cdg-aff-tab').forEach(function(t){ t.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.cdg-aff-pane').forEach(function(p){ p.classList.remove('active'); });
            var target = document.getElementById('cdg-aff-pane-' + pane);
            if(target) target.classList.add('active');
            try { history.replaceState(null, '', '#' + pane); } catch(e) {}
        });
    });
    if(location.hash) {
        var hash = location.hash.substring(1);
        var t = document.querySelector('.cdg-aff-tab[data-pane="' + hash + '"]');
        if(t) t.click();
    }
})();

function cdgAffCopyTracking() {
    var url = document.getElementById('cdg-tracking-url').textContent;
    if(navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){
            var t = document.getElementById('cdg-copy-text');
            if(t) {
                var orig = t.textContent;
                t.textContent = 'Kopyalandı!';
                setTimeout(function(){ t.textContent = orig; }, 2000);
            }
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
    }
}
</script>
