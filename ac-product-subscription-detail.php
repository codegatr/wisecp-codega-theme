<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Otomatik Ödeme / Abonelik Detayı
 * Bu sayfa genelde modal/inline content olarak gösterilir
 * WiseCP runtime: $subscription (module, ...)
 */

$subscription = isset($subscription) && is_array($subscription) ? $subscription : [];
$module_name = $subscription['module'] ?? 'Modül';
?>

<style>
.cdg-sub-detail {
    padding: 20px 24px;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1px solid #6ee7b7;
    border-radius: 12px;
    color: #065f46;
    margin: 12px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.cdg-sub-detail-row {
    display: flex; align-items: center; gap: 14px;
    flex-wrap: wrap;
}
.cdg-sub-detail-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: #10b981;
    color: #fff;
    display: grid; place-items: center;
    font-size: 20px;
    flex-shrink: 0;
}
.cdg-sub-detail-text { flex: 1; min-width: 200px; }
.cdg-sub-detail-text strong { color: #065f46; }
.cdg-sub-detail-text small {
    display: block;
    font-size: 12px;
    color: #047857;
    opacity: 0.85;
    margin-top: 2px;
}
.cdg-sub-detail-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.18s;
    font-family: inherit;
}
.cdg-sub-detail-btn:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
}
</style>

<div class="cdg-sub-detail">
    <div class="cdg-sub-detail-row">
        <div class="cdg-sub-detail-icon">
            <i class="bi bi-arrow-repeat"></i>
        </div>
        <div class="cdg-sub-detail-text">
            <strong>Otomatik Ödeme Aktif</strong>
            <small>Bu hizmetiniz <strong><?php echo htmlspecialchars($module_name); ?></strong> ile yenileme tarihinde otomatik tahsil edilir.</small>
        </div>
        <a href="javascript:void(0);" class="cdg-sub-detail-btn" id="cancel_subscription_btn" onclick="cancel_subscription(this);">
            <i class="bi bi-x-circle"></i> Aboneliği İptal Et
        </a>
    </div>
</div>

<script>
function cancel_subscription(el) {
    if(!confirm('Otomatik ödeme aboneliğinizi iptal etmek istediğinize emin misiniz?')) return;
    if(typeof MioAjaxElement === 'function' && window.jQuery) {
        MioAjaxElement(jQuery(el), {
            waiting_text: 'İşleniyor...',
            data: { operation: 'cancel_subscription' }
        });
    }
}
</script>
