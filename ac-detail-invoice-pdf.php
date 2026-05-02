<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Fatura PDF (Yazdırma görünümü)
 * master_content_none ile bağımsız sayfa - header/footer yok
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
$invoice   = isset($invoice) && is_array($invoice) ? $invoice : [];
$items     = [];
if(isset($invoice['items']) && is_array($invoice['items'])) $items = $invoice['items'];
elseif(isset($items) && is_array($items)) { /* zaten set */ } else { $items = []; }

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

function cdg_pdf_date($date) {
    if(!$date) return '';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        $fmt = Config::get("options/date-format") ?: 'd.m.Y';
        return DateManager::format($fmt, $date);
    }
    if(strpos((string)$date, '1881') === 0 || strpos((string)$date, '0000') === 0) return '';
    return date('d.m.Y', strtotime($date));
}
function cdg_pdf_money($amount, $cid = 0) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol') && $cid) {
        return Money::formatter_symbol($amount, $cid);
    }
    return number_format((float)$amount, 2, ',', '.');
}

// Status etiket
$status_lbl_map = [
    'paid' => 'ÖDENDİ', 'unpaid' => 'ÖDENMEMİŞ',
    'waiting' => 'ONAY BEKLİYOR', 'refund' => 'İADE EDİLDİ',
];
$status_lbl = $status_lbl_map[$inv_status] ?? strtoupper($inv_status);
$status_color = ['paid'=>'#10b981','unpaid'=>'#f59e0b','waiting'=>'#06b6d4','refund'=>'#ef4444'][$inv_status] ?? '#64748b';

// Kullanıcı
$u_kind     = $user_data['kind'] ?? 'individual';
$u_company  = $user_data['company_name'] ?? '';
$u_name     = trim(($user_data['name'] ?? '') . ' ' . ($user_data['surname'] ?? ''));
$u_email    = $user_data['email'] ?? '';
$u_phone    = $user_data['phone'] ?? '';
$u_address  = $user_data['address'] ?? '';
$u_city     = $user_data['city'] ?? '';
$u_country  = $user_data['country'] ?? '';
$u_taxoffice= $user_data['company_tax_office'] ?? '';
$u_taxnumber= $user_data['company_tax_number'] ?? ($user_data['identity'] ?? '');

// Şirket bilgileri (header üstü için)
$company_name = '';
$company_address = '';
$company_phone = '';
$company_email = '';
$company_taxoffice = '';
$company_taxnumber = '';
if(class_exists('Config')) {
    $company_name      = Config::get('theme/company-name') ?: 'CODEGA';
    $company_address   = Config::get('theme/company-address') ?: '';
    $company_phone     = Config::get('theme/company-phone') ?: '';
    $company_email     = Config::get('theme/company-email') ?: '';
    $company_taxoffice = Config::get('theme/company-tax-office') ?: '';
    $company_taxnumber = Config::get('theme/company-tax-number') ?: '';
}
if(!$company_name) $company_name = 'CODEGA';

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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Fatura #<?php echo htmlspecialchars($inv_number); ?> - <?php echo htmlspecialchars($company_name); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #0f172a;
            background: #f1f5f9;
            line-height: 1.5;
            padding: 30px 20px;
        }
        .pdf-page {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 8px 32px rgba(15,23,42,0.10);
            padding: 50px 55px;
            position: relative;
        }
        .pdf-watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 140px;
            font-weight: 900;
            opacity: 0.04;
            pointer-events: none;
            color: <?php echo $status_color; ?>;
            white-space: nowrap;
            letter-spacing: 8px;
        }
        .pdf-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex; gap: 10px;
            z-index: 100;
        }
        .pdf-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 18px;
            background: #1e40af;
            color: #fff;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(30,64,175,0.30);
            font-family: inherit;
        }
        .pdf-btn:hover { background: #1e3a8a; }
        .pdf-btn-back { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; }
        .pdf-btn-back:hover { border-color: #1e40af; color: #1e40af; }

        .pdf-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 3px double #e2e8f0;
            margin-bottom: 24px;
        }
        .pdf-logo img { max-height: 60px; max-width: 200px; }
        .pdf-logo-text {
            font-size: 28px;
            font-weight: 900;
            color: #1e40af;
            letter-spacing: -0.5px;
        }
        .pdf-company-info {
            text-align: right;
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
        .pdf-company-info strong { color: #0f172a; font-size: 13px; }

        .pdf-title-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 26px;
        }
        .pdf-title h1 {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .pdf-title .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        .pdf-status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff;
            background: <?php echo $status_color; ?>;
        }

        .pdf-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 26px;
        }
        .pdf-info-block { font-size: 12px; }
        .pdf-info-block .pdf-info-label {
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .pdf-info-block .pdf-info-content {
            line-height: 1.7;
            color: #0f172a;
        }
        .pdf-info-content strong { font-weight: 700; }
        .pdf-info-content .muted { color: #64748b; }

        .pdf-meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 26px;
        }
        .pdf-meta-item { font-size: 11px; }
        .pdf-meta-item .lbl {
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .pdf-meta-item .val { font-weight: 700; color: #0f172a; }

        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .pdf-table thead th {
            background: #1e40af;
            color: #fff;
            padding: 12px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pdf-table thead th:last-child { text-align: right; }
        .pdf-table tbody td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .pdf-table tbody td:last-child { text-align: right; font-weight: 700; }
        .pdf-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .pdf-table tbody tr.discount-row td { color: #92400e; background: #fef9e7; }
        .pdf-item-name { font-weight: 700; margin-bottom: 3px; }
        .pdf-item-desc { font-size: 11px; color: #64748b; line-height: 1.5; }

        .pdf-totals {
            margin-left: auto;
            width: 320px;
            font-size: 13px;
        }
        .pdf-totals-row {
            display: flex; justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .pdf-totals-row.discount { color: #92400e; }
        .pdf-totals-row.final {
            border: 0;
            border-top: 3px double #1e40af;
            margin-top: 6px;
            padding-top: 12px;
            font-size: 17px;
            font-weight: 900;
            color: #1e40af;
        }

        .pdf-footer {
            margin-top: 50px;
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
            transform: rotate(-2deg);
        }

        @media print {
            body { background: #fff; padding: 0; }
            .pdf-actions { display: none; }
            .pdf-page {
                box-shadow: none;
                max-width: 100%;
                padding: 25px 30px;
            }
            .pdf-watermark { display: block; }
            @page { margin: 12mm; size: A4; }
        }
        @media (max-width: 600px) {
            .pdf-page { padding: 28px 22px; }
            .pdf-header { flex-direction: column; gap: 14px; }
            .pdf-company-info { text-align: left; }
            .pdf-title-row { flex-direction: column; align-items: flex-start; gap: 10px; }
            .pdf-info-grid { grid-template-columns: 1fr; }
            .pdf-meta { grid-template-columns: 1fr 1fr; }
            .pdf-totals { width: 100%; }
        }
    </style>
</head>
<body>

<div class="pdf-actions">
    <a href="javascript:history.back()" class="pdf-btn pdf-btn-back">
        ← Geri
    </a>
    <button class="pdf-btn" onclick="window.print();return false;">
        🖨 Yazdır / PDF
    </button>
</div>

<div class="pdf-page">

    <div class="pdf-watermark"><?php echo htmlspecialchars($status_lbl); ?></div>

    <!-- HEADER -->
    <div class="pdf-header">
        <div class="pdf-logo">
            <?php if($logo): ?>
            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($company_name); ?>">
            <?php else: ?>
            <div class="pdf-logo-text"><?php echo htmlspecialchars($company_name); ?></div>
            <?php endif; ?>
        </div>
        <div class="pdf-company-info">
            <strong><?php echo htmlspecialchars($company_name); ?></strong><br>
            <?php if($company_address): ?><?php echo nl2br(htmlspecialchars($company_address)); ?><br><?php endif; ?>
            <?php if($company_phone): ?>Tel: <?php echo htmlspecialchars($company_phone); ?><br><?php endif; ?>
            <?php if($company_email): ?>E-posta: <?php echo htmlspecialchars($company_email); ?><br><?php endif; ?>
            <?php if($company_taxoffice): ?>VD: <?php echo htmlspecialchars($company_taxoffice); ?><?php endif; ?>
            <?php if($company_taxnumber): ?> · No: <?php echo htmlspecialchars($company_taxnumber); ?><?php endif; ?>
        </div>
    </div>

    <!-- TITLE -->
    <div class="pdf-title-row">
        <div class="pdf-title">
            <h1>Fatura</h1>
            <div class="subtitle">Belge No: #<?php echo htmlspecialchars($inv_number); ?></div>
        </div>
        <div class="pdf-status-badge"><?php echo htmlspecialchars($status_lbl); ?></div>
    </div>

    <!-- META BAR -->
    <div class="pdf-meta">
        <div class="pdf-meta-item">
            <div class="lbl">Düzenleme</div>
            <div class="val"><?php echo htmlspecialchars(cdg_pdf_date($inv_cdate) ?: '-'); ?></div>
        </div>
        <div class="pdf-meta-item">
            <div class="lbl">Son Ödeme</div>
            <div class="val"><?php echo htmlspecialchars(cdg_pdf_date($inv_duedate) ?: '-'); ?></div>
        </div>
        <?php if($inv_status === 'paid' && $inv_datepaid): ?>
        <div class="pdf-meta-item">
            <div class="lbl">Ödendi</div>
            <div class="val"><?php echo htmlspecialchars(cdg_pdf_date($inv_datepaid)); ?></div>
        </div>
        <?php endif; ?>
        <?php if($inv_pmethod): ?>
        <div class="pdf-meta-item">
            <div class="lbl">Ödeme Yöntemi</div>
            <div class="val"><?php echo htmlspecialchars($inv_pmethod); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- DÜZENLEYEN / ALICI -->
    <div class="pdf-info-grid">
        <div class="pdf-info-block">
            <div class="pdf-info-label">Düzenleyen</div>
            <div class="pdf-info-content">
                <strong><?php echo htmlspecialchars($company_name); ?></strong><br>
                <?php if($company_address): ?><span class="muted"><?php echo nl2br(htmlspecialchars($company_address)); ?></span><br><?php endif; ?>
                <?php if($company_taxoffice): ?>VD: <?php echo htmlspecialchars($company_taxoffice); ?><?php endif; ?>
                <?php if($company_taxnumber): ?> · <?php echo htmlspecialchars($company_taxnumber); ?><?php endif; ?>
            </div>
        </div>
        <div class="pdf-info-block">
            <div class="pdf-info-label">Alıcı</div>
            <div class="pdf-info-content">
                <?php if($u_kind === 'company' && $u_company): ?>
                    <strong><?php echo htmlspecialchars($u_company); ?></strong><br>
                    <?php if($u_name): ?><span class="muted"><?php echo htmlspecialchars($u_name); ?></span><br><?php endif; ?>
                <?php else: ?>
                    <strong><?php echo htmlspecialchars($u_name ?: '-'); ?></strong><br>
                <?php endif; ?>
                <?php if($u_address): ?><span class="muted"><?php echo htmlspecialchars($u_address); ?><?php if($u_city): ?>, <?php echo htmlspecialchars($u_city); ?><?php endif; ?></span><br><?php endif; ?>
                <?php if($u_email): ?><?php echo htmlspecialchars($u_email); ?><br><?php endif; ?>
                <?php if($u_phone): ?>Tel: <?php echo htmlspecialchars($u_phone); ?><br><?php endif; ?>
                <?php if($u_kind === 'company' && $u_taxoffice): ?>VD: <?php echo htmlspecialchars($u_taxoffice); ?><?php endif; ?>
                <?php if($u_taxnumber): ?> · <?php echo htmlspecialchars($u_taxnumber); ?><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ÖĞELER -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th>Açıklama</th>
                <th style="width:140px;text-align:right;">Tutar</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($items)): ?>
            <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:30px;">Fatura öğesi bulunamadı.</td></tr>
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
                        <div class="pdf-item-name"><?php echo htmlspecialchars($iname); ?></div>
                        <?php if($idesc): ?>
                        <div class="pdf-item-desc"><?php echo nl2br(htmlspecialchars($idesc)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars(cdg_pdf_money($itotal, $icid)); ?> <?php echo htmlspecialchars($inv_currency); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if(!empty($discount_items)): ?>
                <?php foreach($discount_items as $di):
                    $dname = $di['name'] ?? 'İndirim';
                    $damount = $di['amountd'] ?? 0;
                ?>
                <tr class="discount-row">
                    <td>
                        <div class="pdf-item-name">↓ <?php echo htmlspecialchars($dname); ?></div>
                        <div class="pdf-item-desc">İndirim uygulandı</div>
                    </td>
                    <td>-<?php echo htmlspecialchars(cdg_pdf_money($damount)); ?> <?php echo htmlspecialchars($inv_currency); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="pdf-totals">
        <div class="pdf-totals-row">
            <span>Ara Toplam</span>
            <strong><?php echo htmlspecialchars(cdg_pdf_money($inv_subtotal)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
        </div>
        <?php if($total_discount > 0): ?>
        <div class="pdf-totals-row discount">
            <span>İndirim</span>
            <strong>-<?php echo htmlspecialchars(cdg_pdf_money($total_discount)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
        </div>
        <?php endif; ?>
        <?php if($inv_tax > 0): ?>
        <div class="pdf-totals-row">
            <span>KDV (%<?php echo (int)$inv_taxrate; ?>)</span>
            <strong><?php echo htmlspecialchars(cdg_pdf_money($inv_tax)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
        </div>
        <?php endif; ?>
        <div class="pdf-totals-row final">
            <span>GENEL TOPLAM</span>
            <strong><?php echo htmlspecialchars(cdg_pdf_money($inv_total)); ?> <?php echo htmlspecialchars($inv_currency); ?></strong>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="pdf-footer">
        Bu fatura elektronik ortamda düzenlenmiştir.<br>
        <?php echo htmlspecialchars($company_name); ?> · Belge No: <?php echo htmlspecialchars($inv_number); ?> · <?php echo date('d.m.Y H:i'); ?>

        <?php if($inv_status === 'paid'): ?>
        <br><span class="stamp">✓ ÖDENMİŞTİR</span>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
