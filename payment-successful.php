<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$invoice_data = isset($invoice) && is_array($invoice) ? $invoice : null;
$amount_str = '';
if($invoice_data && isset($invoice_data['amount']) && isset($invoice_data['cid']) && class_exists('Money') && method_exists('Money','formatter_symbol')) {
    try { $amount_str = Money::formatter_symbol($invoice_data['amount'], $invoice_data['cid']); } catch(\Throwable $e) {}
}

$products_link = isset($links['products']) ? $links['products']
    : (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('products') : '/products');
$invoices_link = isset($links['invoices']) ? $links['invoices']
    : (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('invoices') : '/invoices');
?>

<section class="cdg-page-head" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7) !important;border-bottom-color:#86efac !important;">
    <div class="cdg-container">
        <h1 style="color:#15803d;"><i class="bi bi-check2-circle"></i> Odeme Basarili</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Odeme Basarili</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:560px;">
        <div class="cdg-card" style="padding:40px 32px;text-align:center;">

            <div class="cdg-pay-success-icon" style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#10b981,#34d399);display:inline-grid;place-items:center;margin-bottom:24px;animation:cdgPaySuccess 0.6s ease;box-shadow:0 12px 32px rgba(16,185,129,0.35);">
                <i class="bi bi-check2" style="font-size:64px;color:#fff;font-weight:bold;"></i>
            </div>

            <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:0 0 10px;">Tesekkurler!</h2>
            <p style="font-size:15px;color:#64748b;margin:0 0 24px;line-height:1.5;">
                Odemeniz basariyla alindi. Hesabinizla ilgili guncel detaylara musteri panelinden ulasabilirsiniz.
            </p>

            <?php if($invoice_data): ?>
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:16px 20px;margin-bottom:20px;text-align:left;">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#15803d;margin-bottom:6px;">
                    <span>Fatura No:</span>
                    <strong>#<?php echo htmlspecialchars($invoice_data['number'] ?? $invoice_data['id'] ?? ''); ?></strong>
                </div>
                <?php if($amount_str): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#15803d;">
                    <span>Tutar:</span>
                    <strong style="font-size:16px;"><?php echo htmlspecialchars($amount_str); ?></strong>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <a href="<?php echo htmlspecialchars($products_link); ?>" class="cdg-btn cdg-btn-success">
                    <i class="bi bi-box-seam"></i> Hizmetlerime Git
                </a>
                <a href="<?php echo htmlspecialchars($invoices_link); ?>" class="cdg-btn cdg-btn-outline">
                    <i class="bi bi-receipt"></i> Faturalarim
                </a>
            </div>

            <div style="margin-top:24px;padding-top:18px;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;">
                <i class="bi bi-envelope"></i> Odeme onayi e-posta adresinize gonderildi.
            </div>
        </div>

        <div style="text-align:center;margin-top:14px;">
            <a href="<?php echo APP_URI; ?>/" style="font-size:13px;color:#64748b;text-decoration:none;">
                <i class="bi bi-arrow-left"></i> Anasayfaya don
            </a>
        </div>
    </div>
</section>

<style>
@keyframes cdgPaySuccess {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
