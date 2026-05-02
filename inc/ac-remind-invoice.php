<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Bekleyen Fatura Hatırlatması
 * Müşteri panelinde bekleyen ödenmemiş fatura varsa uyarı gösterir
 * WiseCP runtime: $visibility_invoice, $statistic3 (ödenmemiş fatura sayısı)
 */

if(isset($visibility_invoice) && $visibility_invoice && isset($statistic3) && $statistic3 > 0) {

    // Toplam tutarı bir kere hesapla, sonraki çağrılarda cache'le
    if(!isset($get_unpaid_invoice_total)) {
        if(class_exists('Helper') && method_exists('Helper','Load')) {
            Helper::Load(["Invoices", "Money"]);
        }
        if(class_exists('UserManager') && method_exists('UserManager','LoginData')) {
            $u_data = UserManager::LoginData("member");
            if(class_exists('Invoices') && method_exists('Invoices','get_total_unpaid_invoices_amount')) {
                $get_unpaid_invoice_total = Invoices::get_total_unpaid_invoices_amount($u_data["id"] ?? 0);
            }
        }
        if(!isset($get_unpaid_invoice_total)) $get_unpaid_invoice_total = 0;
    }

    $total_str = '';
    if(class_exists('Money') && method_exists('Money','formatter_symbol') && method_exists('Money','getUCID')) {
        $total_str = Money::formatter_symbol($get_unpaid_invoice_total, Money::getUCID());
    } else {
        $total_str = number_format((float)$get_unpaid_invoice_total, 2, ',', '.');
    }

    $bulk_url = '#';
    if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
        $bulk_url = Controllers::$init->CRLink("ac-ps-invoices-p", ["bulk-payment"]);
    }

    $count = (int)$statistic3;
    $btn_label = $count > 1 ? 'Faturaları Topluca Öde' : 'Faturayı Öde';
?>

<style>
.cdg-rmd {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1px solid #fcd34d;
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 18px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: 0 4px 12px rgba(245,158,11,0.15);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #78350f;
    flex-wrap: wrap;
    box-sizing: border-box;
}
.cdg-rmd *, .cdg-rmd *::before, .cdg-rmd *::after { box-sizing: border-box; }
.cdg-rmd-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    display: grid; place-items: center;
    font-size: 24px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(245,158,11,0.30);
}
.cdg-rmd-text { flex: 1; min-width: 220px; }
.cdg-rmd-title {
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 4px;
    color: #78350f;
}
.cdg-rmd-desc {
    font-size: 13px;
    color: #92400e;
    line-height: 1.5;
}
.cdg-rmd-desc strong {
    color: #78350f;
    font-weight: 800;
}
.cdg-rmd-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    border: 0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.18s;
    box-shadow: 0 6px 18px rgba(245,158,11,0.25);
    flex-shrink: 0;
    white-space: nowrap;
}
.cdg-rmd-btn:hover { transform: translateY(-1px); color: #fff; }
@media (max-width: 600px) {
    .cdg-rmd { flex-direction: column; text-align: center; align-items: center; }
    .cdg-rmd-btn { width: 100%; justify-content: center; }
}
</style>

<div class="cdg-rmd">
    <div class="cdg-rmd-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <div class="cdg-rmd-text">
        <div class="cdg-rmd-title">Bekleyen Faturanız Var</div>
        <div class="cdg-rmd-desc">
            <strong><?php echo $count; ?></strong> adet ödenmemiş faturanız bulunuyor.
            Toplam tutar: <strong><?php echo htmlspecialchars($total_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
        </div>
    </div>
    <a href="<?php echo htmlspecialchars($bulk_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-rmd-btn">
        <i class="bi bi-credit-card"></i> <?php echo htmlspecialchars($btn_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
    </a>
</div>
<?php
}
?>
