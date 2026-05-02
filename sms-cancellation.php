<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$sc_action = isset($cancellation_action) ? $cancellation_action : '';
$sc_phone = isset($phone) ? $phone : '';
$sc_status = isset($status) ? $status : '';
$sc_message = isset($message) ? $message : '';
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-phone-vibrate"></i> SMS Aboneligi Iptal</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>SMS Iptal</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:520px;">
        <div class="cdg-card" style="padding:36px;">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;display:inline-grid;place-items:center;font-size:32px;margin-bottom:12px;">
                    <i class="bi bi-phone-vibrate"></i>
                </div>
                <h2 style="margin:0 0 8px;font-size:22px;font-weight:800;">Ticari SMS Aboneligini Iptal Et</h2>
                <p class="text-muted" style="font-size:14px;line-height:1.5;margin:0;">
                    IYS (Iletisim Yonetim Sistemi) kapsaminda ticari SMS aboneliginizi iptal edebilirsiniz.
                </p>
            </div>

            <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:14px 16px;margin-bottom:18px;font-size:12px;color:#78350f;line-height:1.5;">
                <i class="bi bi-info-circle"></i>
                Iptal isleminden sonra hizmetinizle ilgili acil bildirimler ve fatura uyarilari haric ticari icerikli SMS'ler gonderilmeyecektir.
            </div>

            <?php if($sc_status === 'successful'): ?>
            <div class="cdg-alert cdg-alert-success" style="margin-bottom:16px;">
                <i class="bi bi-check-circle"></i>
                <?php echo $sc_message ?: 'SMS aboneliginiz iptal edildi.'; ?>
            </div>
            <?php elseif($sc_status === 'error'): ?>
            <div class="cdg-alert cdg-alert-error" style="margin-bottom:16px;">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo $sc_message ?: 'Hata olustu, tekrar deneyin.'; ?>
            </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($sc_action); ?>" method="post">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('sms_cancellation'); ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Telefon Numarasi</label>
                    <input type="tel" name="phone" class="cdg-form-control" placeholder="05XX XXX XX XX" value="<?php echo htmlspecialchars($sc_phone); ?>" required>
                </div>

                <?php if(isset($captcha) && $captcha): ?>
                <div class="cdg-form-group">
                    <label class="cdg-form-label">Guvenlik Dogrulamasi</label>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;text-align:center;margin-bottom:8px;">
                        <?php echo $captcha->getOutput(); ?>
                    </div>
                    <?php if($captcha->input): ?>
                    <input type="text" name="<?php echo htmlspecialchars($captcha->input_name); ?>" class="cdg-form-control" placeholder="Resimdeki kodu girin" required>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <button type="submit" class="cdg-btn cdg-btn-danger" style="width:100%;padding:13px;">
                    <i class="bi bi-x-circle"></i> Aboneligi Iptal Et
                </button>
            </form>
        </div>
    </div>
</section>
