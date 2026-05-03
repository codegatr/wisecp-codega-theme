<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Fatura PDF (Classic-uyumlu, mPDF/Dompdf/Chromium hepsiyle uyumlu)
 * master_content_none ile bağımsız sayfa
 * WiseCP runtime: $invoice, $items, $logo
 */

$master_content_none = true;

// Logo
$logo = '';
if(class_exists('Utility') && method_exists('Utility','image_link_determiner') && class_exists('Config')) {
    $logo = Utility::image_link_determiner(Config::get("theme/invoice-detail-logo"));
    if(!$logo) $logo = Utility::image_link_determiner(Config::get("theme/header-logo"));
    if(!$logo) $logo = Utility::image_link_determiner(Config::get("theme/notifi-header-logo"));
}

// Defansif defaults
$invoice = isset($invoice) && is_array($invoice) ? $invoice : [];

// === ITEMS - 3 katmanlı fallback ===
// 1) Önce $invoice['items']
// 2) Sonra global $items runtime variable
// 3) Son çare: Invoices::item_listing($invoice['id'])
$inv_items = [];
if(isset($invoice['items']) && is_array($invoice['items']) && !empty($invoice['items'])) {
    $inv_items = $invoice['items'];
} elseif(isset($items) && is_array($items) && !empty($items)) {
    $inv_items = $items;
} elseif(isset($invoice['id']) && class_exists('Invoices') && method_exists('Invoices', 'item_listing')) {
    try {
        $tmp = Invoices::item_listing($invoice['id']);
        if(is_array($tmp)) $inv_items = $tmp;
    } catch(\Throwable $e) {}
}

$user_data = isset($invoice['user_data']) && is_array($invoice['user_data']) ? $invoice['user_data'] : [];

$inv_number   = $invoice['number'] ?? ($invoice['id'] ?? '?');
$inv_status   = strtolower($invoice['status'] ?? 'unpaid');
$inv_total    = $invoice['total'] ?? 0;
$inv_subtotal = $invoice['subtotal'] ?? 0;
$inv_tax      = $invoice['tax'] ?? 0;
$inv_taxrate  = $invoice['taxrate'] ?? 0;
$inv_currency = $invoice['currency'] ?? 'TRY';
$inv_cdate    = $invoice['cdate'] ?? '';
$inv_duedate  = $invoice['duedate'] ?? '';
$inv_datepaid = $invoice['datepaid'] ?? '';
$inv_pmethod  = $invoice['pmethod'] ?? '';
$inv_sendbta  = $invoice['sendbta'] ?? 0;
$inv_sendbta_amount = $invoice['sendbta_amount'] ?? 0;
$inv_pcommission = $invoice['pmethod_commission'] ?? 0;
$inv_pcommission_rate = $invoice['pmethod_commission_rate'] ?? 0;

// İndirimler
$discount_items = [];
$total_discount = 0;
if(!empty($invoice['discounts']) && is_array($invoice['discounts'])) {
    foreach($invoice['discounts'] as $disc) {
        if(isset($disc['items']) && is_array($disc['items'])) {
            // Classic format: discounts.items.coupon, discounts.items.promotions, discounts.items.dealership
            foreach(['coupon','promotions','dealership'] as $dtype) {
                if(isset($disc['items'][$dtype]) && is_array($disc['items'][$dtype])) {
                    foreach($disc['items'][$dtype] as $di) {
                        $discount_items[] = $di;
                        $total_discount += (float)($di['amountd'] ?? ($di['amount'] ?? 0));
                    }
                }
            }
            // Fallback: doğrudan items (eski format)
            if(empty($discount_items)) {
                foreach($disc['items'] as $di) {
                    if(is_array($di)) {
                        $discount_items[] = $di;
                        $total_discount += (float)($di['amountd'] ?? ($di['amount'] ?? 0));
                    }
                }
            }
        }
    }
}

function cdg_pdf_date($date) {
    if(!$date) return '';
    if(strpos((string)$date, '1881') === 0 || strpos((string)$date, '0000') === 0) return '';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        $fmt = Config::get("options/date-format") ?: 'd.m.Y';
        return DateManager::format($fmt, $date);
    }
    return date('d.m.Y', strtotime($date));
}
function cdg_pdf_money($amount, $cid = 0) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol') && $cid) {
        return Money::formatter_symbol($amount, $cid);
    }
    return number_format((float)$amount, 2, ',', '.');
}

// Status
$status_lbl_map = [
    'paid'    => 'ÖDENDİ',
    'unpaid'  => 'ÖDENMEMİŞ',
    'waiting' => 'ONAY BEKLİYOR',
    'refund'  => 'İADE EDİLDİ',
    'cancelled' => 'İPTAL EDİLDİ',
];
$status_lbl = $status_lbl_map[$inv_status] ?? strtoupper($inv_status);
$status_color = [
    'paid'    => '#10b981',
    'unpaid'  => '#f59e0b',
    'waiting' => '#00D3E5',
    'refund'  => '#ef4444',
    'cancelled' => '#94a3b8',
][$inv_status] ?? '#64748b';

// Kullanıcı
$u_kind     = $user_data['kind'] ?? 'individual';
$u_company  = $user_data['company_name'] ?? '';
$u_name     = trim(($user_data['name'] ?? '') . ' ' . ($user_data['surname'] ?? ''));
if(!$u_name) $u_name = $user_data['full_name'] ?? '';
$u_email    = $user_data['email'] ?? '';
$u_phone    = $user_data['phone'] ?? ($user_data['gsm'] ?? '');
$u_address  = $user_data['address'] ?? '';
$u_city     = $user_data['city'] ?? '';
$u_country  = $user_data['country'] ?? '';
$u_taxoffice= $user_data['company_tax_office'] ?? '';
$u_taxnumber= $user_data['company_tax_number'] ?? ($user_data['identity'] ?? '');

// Şirket bilgileri
$company_name = '';
$company_address = '';
$company_phone = '';
$company_email = '';
$company_taxoffice = '';
$company_taxnumber = '';
if(class_exists('Config')) {
    $company_name      = Config::get('options/company-name') ?: (Config::get('options/website-name') ?: 'CODEGA');
    $company_address   = Config::get('options/company-address') ?: '';
    $company_phone     = Config::get('options/company-phone') ?: '';
    $company_email     = Config::get('options/company-email') ?: '';
    $company_taxoffice = Config::get('options/company-tax-office') ?: '';
    $company_taxnumber = Config::get('options/company-tax-number') ?: '';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Fatura #<?php echo htmlspecialchars($inv_number, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></title>
<style>
* { box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
    color: #0f172a;
    background: #f1f5f9;
    line-height: 1.5;
    margin: 0;
    padding: 30px 20px;
    font-size: 13px;
}
.pdf-page {
    max-width: 850px;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 8px 32px rgba(15,23,42,0.10);
    padding: 40px 50px;
}

/* Üst aksiyon butonları (sadece ekranda) */
.pdf-actions {
    max-width: 850px;
    margin: 0 auto 16px;
    text-align: right;
}
.pdf-btn {
    display: inline-block;
    padding: 10px 18px;
    background: #2E3B4E;
    color: #fff;
    border: 0;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    margin-left: 8px;
    font-family: inherit;
}
.pdf-btn:hover { background: #1A2332; }
.pdf-btn-back { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; }

/* Header */
.pdf-header { width: 100%; margin-bottom: 30px; }
.pdf-header td { vertical-align: top; }
.pdf-logo img { max-height: 60px; max-width: 200px; }
.pdf-company-info {
    text-align: right;
    font-size: 11px;
    color: #475569;
    line-height: 1.7;
}
.pdf-company-info strong { color: #0f172a; font-size: 14px; }

/* Title row */
.pdf-title-row { width: 100%; margin: 30px 0 25px; padding-top: 20px; border-top: 2px solid #2E3B4E; }
.pdf-title-row td { vertical-align: middle; }
.pdf-title h1 {
    font-size: 38px;
    font-weight: 900;
    margin: 0;
    color: #0f172a;
    letter-spacing: 2px;
}
.pdf-title .subtitle {
    color: #64748b;
    font-size: 13px;
    margin-top: 4px;
}
.pdf-status-badge {
    display: inline-block;
    padding: 10px 20px;
    background: <?php echo $status_color; ?>;
    color: #fff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1px;
}

/* Meta */
.pdf-meta {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin: 20px 0;
}
.pdf-meta td {
    padding: 14px 18px;
    width: 33.33%;
    vertical-align: top;
}
.pdf-meta .lbl {
    font-size: 10px;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 4px;
}
.pdf-meta .val {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}

/* Düzenleyen / Alıcı */
.pdf-info { width: 100%; margin: 25px 0; }
.pdf-info td {
    width: 50%;
    padding: 0 10px;
    vertical-align: top;
}
.pdf-info td:first-child { padding-left: 0; }
.pdf-info td:last-child { padding-right: 0; }
.pdf-info-block {
    border-top: 2px solid #cbd5e1;
    padding-top: 15px;
}
.pdf-info-label {
    font-size: 11px;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}
.pdf-info-content {
    font-size: 13px;
    line-height: 1.7;
    color: #1e293b;
}
.pdf-info-content strong { color: #0f172a; }
.pdf-info-content .muted { color: #64748b; }

/* Items table */
.pdf-table {
    width: 100%;
    border-collapse: collapse;
    margin: 25px 0;
}
.pdf-table thead th {
    background: #2E3B4E;
    color: #fff;
    padding: 12px 15px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.pdf-table thead th:last-child { text-align: right; }
.pdf-table tbody td {
    padding: 14px 15px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
}
.pdf-table tbody td:last-child {
    text-align: right;
    font-weight: 700;
    white-space: nowrap;
}
.pdf-table tbody tr:nth-child(even) td { background: #f8fafc; }
.pdf-table tbody tr.discount-row td { color: #92400e; background: #fef9e7; }
.pdf-item-name { font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.pdf-item-desc { color: #64748b; font-size: 11px; line-height: 1.5; }

/* Totals (table-based, no flex) */
.pdf-totals {
    width: 100%;
    margin: 20px 0;
}
.pdf-totals-table {
    width: 350px;
    margin-left: auto;
    border-collapse: collapse;
}
.pdf-totals-table td {
    padding: 8px 0;
    font-size: 13px;
}
.pdf-totals-table td:first-child {
    color: #475569;
}
.pdf-totals-table td:last-child {
    text-align: right;
    font-weight: 700;
    color: #0f172a;
}
.pdf-totals-table tr.discount td { color: #92400e; }
.pdf-totals-table tr.final td {
    border-top: 2px solid #2E3B4E;
    padding-top: 12px;
    font-size: 16px;
}
.pdf-totals-table tr.final td:first-child { color: #0f172a; font-weight: 800; }
.pdf-totals-table tr.final td:last-child { color: #2E3B4E; }

/* Footer */
.pdf-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    font-size: 10px;
    color: #94a3b8;
    text-align: center;
    line-height: 1.6;
}
.pdf-footer .stamp {
    display: inline-block;
    margin-top: 14px;
    padding: 8px 16px;
    border: 2px solid <?php echo $status_color; ?>;
    color: <?php echo $status_color; ?>;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
}
.pdf-footer .website {
    margin-top: 6px;
    font-size: 11px;
    color: #2E3B4E;
    font-weight: 700;
}

@media print {
    body { background: #fff; padding: 0; margin: 0; }
    .pdf-actions { display: none !important; }
    .pdf-page {
        box-shadow: none;
        max-width: 100%;
        padding: 25px 30px;
    }
    @page { margin: 12mm; size: A4; }
}
</style>
</head>
<body>

<div class="pdf-actions">
    <a href="javascript:history.back()" class="pdf-btn pdf-btn-back">← Geri</a>
    <button class="pdf-btn" onclick="window.print();return false;">🖨 Yazdır / PDF</button>
</div>

<div class="pdf-page">

    <!-- HEADER -->
    <table class="pdf-header" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="pdf-logo">
                <?php if($logo): ?>
                <img src="<?php echo htmlspecialchars($logo, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="Logo">
                <?php else: ?>
                <strong style="font-size:22px;color:#2E3B4E;"><?php echo htmlspecialchars($company_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
                <?php endif; ?>
            </td>
            <td class="pdf-company-info">
                <strong><?php echo htmlspecialchars($company_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong><br>
                <?php if($company_address): ?><?php echo nl2br(htmlspecialchars($company_address, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?><br><?php endif; ?>
                <?php if($company_phone): ?>Tel: <?php echo htmlspecialchars($company_phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><br><?php endif; ?>
                <?php if($company_email): ?>E-posta: <?php echo htmlspecialchars($company_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><br><?php endif; ?>
                <?php if($company_taxoffice): ?>VD: <?php echo htmlspecialchars($company_taxoffice, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                <?php if($company_taxnumber): ?> · No: <?php echo htmlspecialchars($company_taxnumber, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- TITLE -->
    <table class="pdf-title-row" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="pdf-title">
                <h1>FATURA</h1>
                <div class="subtitle">Belge No: #<?php echo htmlspecialchars($inv_number, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            </td>
            <td style="text-align:right;">
                <div class="pdf-status-badge"><?php echo htmlspecialchars($status_lbl, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            </td>
        </tr>
    </table>

    <!-- META -->
    <table class="pdf-meta" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <div class="lbl">Düzenleme</div>
                <div class="val"><?php echo htmlspecialchars(cdg_pdf_date($inv_cdate) ?: '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            </td>
            <td>
                <div class="lbl">Son Ödeme</div>
                <div class="val"><?php echo htmlspecialchars(cdg_pdf_date($inv_duedate) ?: '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            </td>
            <td>
                <?php if($inv_status === 'paid' && $inv_datepaid): ?>
                <div class="lbl">Ödendi</div>
                <div class="val"><?php echo htmlspecialchars(cdg_pdf_date($inv_datepaid), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <?php elseif($inv_pmethod && $inv_pmethod !== 'none'): ?>
                <div class="lbl">Ödeme Yöntemi</div>
                <div class="val"><?php echo htmlspecialchars($inv_pmethod, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <?php else: ?>
                <div class="lbl">Para Birimi</div>
                <div class="val"><?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- DÜZENLEYEN / ALICI -->
    <table class="pdf-info" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <div class="pdf-info-block">
                    <div class="pdf-info-label">Düzenleyen</div>
                    <div class="pdf-info-content">
                        <strong><?php echo htmlspecialchars($company_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong><br>
                        <?php if($company_address): ?><span class="muted"><?php echo nl2br(htmlspecialchars($company_address, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></span><br><?php endif; ?>
                        <?php if($company_taxoffice): ?>VD: <?php echo htmlspecialchars($company_taxoffice, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                        <?php if($company_taxnumber): ?> · <?php echo htmlspecialchars($company_taxnumber, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                    </div>
                </div>
            </td>
            <td>
                <div class="pdf-info-block">
                    <div class="pdf-info-label">Alıcı</div>
                    <div class="pdf-info-content">
                        <?php if($u_kind === 'company' && $u_company): ?>
                            <strong><?php echo htmlspecialchars($u_company, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong><br>
                            <?php if($u_name): ?><span class="muted"><?php echo htmlspecialchars($u_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><br><?php endif; ?>
                        <?php else: ?>
                            <strong><?php echo htmlspecialchars($u_name ?: '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong><br>
                        <?php endif; ?>
                        <?php if($u_address): ?><span class="muted"><?php echo htmlspecialchars($u_address, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php if($u_city): ?>, <?php echo htmlspecialchars($u_city, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?></span><br><?php endif; ?>
                        <?php if($u_email): ?><?php echo htmlspecialchars($u_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><br><?php endif; ?>
                        <?php if($u_phone): ?>Tel: <?php echo htmlspecialchars($u_phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><br><?php endif; ?>
                        <?php if($u_kind === 'company' && $u_taxoffice): ?>VD: <?php echo htmlspecialchars($u_taxoffice, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                        <?php if($u_taxnumber): ?> · <?php echo htmlspecialchars($u_taxnumber, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="pdf-table" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
                <th>Açıklama</th>
                <th style="width:160px;">Tutar</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($inv_items)): ?>
            <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:30px;">Fatura öğesi bulunamadı.</td></tr>
            <?php else: ?>
                <?php foreach($inv_items as $item):
                    if(!is_array($item)) continue;
                    $iname = $item['name'] ?? ($item['title'] ?? '');
                    $idesc = $item['description'] ?? '';
                    if(!$iname && $idesc) {
                        // Sadece description varsa onu name olarak kullan, ilk satırı al
                        $idesc_lines = preg_split('/\r\n|\r|\n/', $idesc);
                        $iname = $idesc_lines[0] ?? 'Öğe';
                        $idesc = isset($idesc_lines[1]) ? implode("\n", array_slice($idesc_lines, 1)) : '';
                    }
                    if(!$iname) $iname = 'Hizmet';

                    $iamount = $item['amount'] ?? 0;
                    $icid = $item['currency'] ?? ($item['cid'] ?? 0);
                    $itotal = $item['total_amount'] ?? ($item['total'] ?? $iamount);
                ?>
                <tr>
                    <td>
                        <div class="pdf-item-name"><?php echo htmlspecialchars($iname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <?php if($idesc): ?>
                        <div class="pdf-item-desc"><?php echo nl2br(htmlspecialchars($idesc, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars(cdg_pdf_money($itotal, $icid), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if($inv_sendbta > 0): ?>
            <tr>
                <td><div class="pdf-item-name">Havale Komisyonu</div></td>
                <td><?php echo htmlspecialchars(cdg_pdf_money($inv_sendbta_amount, $inv_currency), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php endif; ?>

            <?php if($inv_pcommission > 0): ?>
            <tr>
                <td><div class="pdf-item-name">Ödeme Yöntemi Komisyonu (<?php echo (float)$inv_pcommission_rate; ?>%)</div></td>
                <td><?php echo htmlspecialchars(cdg_pdf_money($inv_pcommission, $inv_currency), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php endif; ?>

            <?php if(!empty($discount_items)): ?>
                <?php foreach($discount_items as $di):
                    if(!is_array($di)) continue;
                    $dname = $di['name'] ?? ($di['code'] ?? 'İndirim');
                    $damount = $di['amountd'] ?? ($di['amount'] ?? 0);
                ?>
                <tr class="discount-row">
                    <td>
                        <div class="pdf-item-name">↓ <?php echo htmlspecialchars($dname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <div class="pdf-item-desc">İndirim uygulandı</div>
                    </td>
                    <td>-<?php echo htmlspecialchars(cdg_pdf_money($damount), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="pdf-totals">
        <table class="pdf-totals-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>Ara Toplam</td>
                <td><?php echo htmlspecialchars(cdg_pdf_money($inv_subtotal), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php if($total_discount > 0): ?>
            <tr class="discount">
                <td>İndirim</td>
                <td>-<?php echo htmlspecialchars(cdg_pdf_money($total_discount), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($inv_tax > 0): ?>
            <tr>
                <td>KDV (<?php echo (float)$inv_taxrate; ?>%)</td>
                <td><?php echo htmlspecialchars(cdg_pdf_money($inv_tax), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="final">
                <td>GENEL TOPLAM</td>
                <td><?php echo htmlspecialchars(cdg_pdf_money($inv_total), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php echo htmlspecialchars($inv_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
        </table>
    </div>

    <?php
    // === AddQRCodetoInvoiceDetailinClientArea Hook (PDF için) ===
    // E-Fatura/e-Arşiv entegrasyonları PDF içinde de QR kod gösterebilsin
    if(class_exists('Hook') && !empty($invoice)) {
        try {
            $cdg_pdf_qr_codes = Hook::run("AddQRCodetoInvoiceDetailinClientArea", $invoice);
            if(is_array($cdg_pdf_qr_codes) && !empty($cdg_pdf_qr_codes)) {
                ?>
                <div style="margin:18px 0;text-align:center;page-break-inside:avoid;">
                    <?php foreach($cdg_pdf_qr_codes as $cdg_pdf_qr_block):
                        if(!is_array($cdg_pdf_qr_block)) continue;
                        foreach($cdg_pdf_qr_block as $cdg_pdf_qr):
                            if(!is_array($cdg_pdf_qr)) continue;
                            $cdg_pdf_qr_img = $cdg_pdf_qr['image'] ?? '';
                            $cdg_pdf_qr_ttl = $cdg_pdf_qr['title'] ?? '';
                            if(!$cdg_pdf_qr_img) continue;
                    ?>
                    <div style="display:inline-block;margin:0 12px;text-align:center;">
                        <img src="<?php echo htmlspecialchars($cdg_pdf_qr_img, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="width:90px;height:90px;">
                        <?php if($cdg_pdf_qr_ttl): ?>
                            <div style="margin-top:4px;font-size:9px;color:#555;"><?php echo htmlspecialchars($cdg_pdf_qr_ttl, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; endforeach; ?>
                </div>
                <?php
            }
        } catch(\Throwable $e) { /* sessiz geç */ }
    }
    ?>

    <!-- FOOTER -->
    <div class="pdf-footer">
        <span class="stamp"><?php echo htmlspecialchars($status_lbl, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        <br>
        Bu fatura elektronik ortamda düzenlenmiştir.<br>
        <?php echo htmlspecialchars($company_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> · Belge No: <?php echo htmlspecialchars($inv_number, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> · <?php echo date('d.m.Y H:i'); ?>
        <?php if(class_exists('Config')):
            $url = Config::get('options/website-url');
            if($url): ?>
            <div class="website"><?php echo htmlspecialchars(parse_url($url, PHP_URL_HOST) ?: $url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
        <?php endif; endif; ?>
    </div>

</div>

</body>
</html>
