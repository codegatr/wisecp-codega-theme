<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$error_msg = isset($error) ? $error : '';
$invoice_data = isset($invoice) && is_array($invoice) ? $invoice : null;
$invoice_link = $invoice_data && isset($invoice_data['detail_link']) ? $invoice_data['detail_link'] : '';
$products_link = isset($links['products']) ? $links['products']
    : (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('products') : '/products');
$tickets_link = isset($links['tickets']) ? $links['tickets']
    : (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('tickets') : '/tickets');
?>

<section class="cdg-page-head" style="background:linear-gradient(135deg,#fef2f2,#fee2e2) !important;border-bottom-color:#fca5a5 !important;">
    <div class="cdg-container">
        <h1 style="color:#b91c1c;"><i class="bi bi-x-circle"></i> Odeme Basarisiz</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Odeme Basarisiz</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:560px;">
        <div class="cdg-card" style="padding:40px 32px;text-align:center;">

            <div class="cdg-pay-fail-icon" style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#f87171);display:inline-grid;place-items:center;margin-bottom:24px;animation:cdgPayFail 0.5s ease;box-shadow:0 12px 32px rgba(239,68,68,0.30);">
                <i class="bi bi-x-lg" style="font-size:56px;color:#fff;font-weight:bold;"></i>
            </div>

            <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:0 0 10px;">Odeme Tamamlanamadi</h2>
            <p style="font-size:15px;color:#64748b;margin:0 0 20px;line-height:1.5;">
                Odeme isleminizde bir sorun olustu. Kart bilgilerinizi kontrol edip tekrar deneyebilirsiniz.
            </p>

            <?php if($error_msg): ?>
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;text-align:left;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:18px;flex-shrink:0;margin-top:1px;"></i>
                    <div style="font-size:13px;color:#7f1d1d;line-height:1.5;">
                        <strong>Hata Detayi:</strong><br>
                        <?php echo htmlspecialchars($error_msg, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:14px 18px;margin-bottom:24px;text-align:left;">
                <h4 style="font-size:13px;font-weight:800;color:#92400e;margin:0 0 8px;">
                    <i class="bi bi-info-circle"></i> Olasi Nedenler
                </h4>
                <ul style="font-size:12px;color:#78350f;margin:0;padding-left:18px;line-height:1.6;">
                    <li>Kart bilgilerinde hata olabilir (kart no, son kullanma tarihi, CVV)</li>
                    <li>Kart bakiyeniz veya limitiniz yetersiz olabilir</li>
                    <li>Bankanız işlemi reddetmiş olabilir (bankanızla iletişime geçin)</li>
                    <li>Kart 3D Secure dogrulamasi tamamlanmadi</li>
                </ul>
            </div>

            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <?php if($invoice_link): ?>
                <a href="<?php echo htmlspecialchars($invoice_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-arrow-clockwise"></i> Tekrar Dene
                </a>
                <?php else: ?>
                <a href="<?php echo htmlspecialchars($products_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-box-seam"></i> Hizmetlerime Git
                </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($tickets_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn cdg-btn-outline">
                    <i class="bi bi-life-preserver"></i> Destek Talep Et
                </a>
            </div>

            <div style="margin-top:24px;padding-top:18px;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;">
                <i class="bi bi-shield-check"></i> Hesabinizdan herhangi bir tutar tahsil edilmedi.
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
@keyframes cdgPayFail {
    0% { transform: scale(0) rotate(-180deg); opacity: 0; }
    100% { transform: scale(1) rotate(0); opacity: 1; }
}
</style>
