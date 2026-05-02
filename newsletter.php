<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$nl_action = isset($newsletter_action) ? $newsletter_action : '';
$nl_email = isset($email) ? $email : '';
$nl_status = isset($status) ? $status : '';
$nl_message = isset($message) ? $message : '';
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-envelope-paper-heart"></i> Bultenimize Abone Olun</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Bulten Aboneligi</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:520px;">
        <div class="cdg-card" style="padding:36px;">
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;display:inline-grid;place-items:center;font-size:32px;margin-bottom:12px;box-shadow:0 8px 24px rgba(30,64,175,0.20);">
                    <i class="bi bi-envelope-heart"></i>
                </div>
                <h2 style="margin:0 0 8px;font-size:22px;font-weight:800;">Yeniliklerden Haberdar Olun</h2>
                <p class="text-muted" style="font-size:14px;line-height:1.5;margin:0;">
                    E-posta listemize katilin, kampanyalar, yeni ozellikler ve teknik ipuclari hakkinda ilk siz bilgi sahibi olun.
                </p>
            </div>

            <?php if($nl_status === 'successful'): ?>
            <div class="cdg-alert cdg-alert-success" style="margin-bottom:16px;">
                <i class="bi bi-check-circle"></i>
                <?php echo $nl_message ?: 'Aboneliginiz basariyla olusturuldu! Tesekkurler.'; ?>
            </div>
            <?php elseif($nl_status === 'error'): ?>
            <div class="cdg-alert cdg-alert-error" style="margin-bottom:16px;">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo $nl_message ?: 'Bir hata olustu, lutfen tekrar deneyin.'; ?>
            </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($nl_action); ?>" method="post" id="newsletter_email">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('newsletter'); ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">E-posta Adresi</label>
                    <input type="email" name="email" class="cdg-form-control" placeholder="ornek@email.com" value="<?php echo htmlspecialchars($nl_email); ?>" required>
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

                <button type="submit" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;margin-top:8px;">
                    <i class="bi bi-bookmark-heart"></i> Abone Ol
                </button>

                <div style="margin-top:14px;text-align:center;font-size:11px;color:#94a3b8;">
                    <i class="bi bi-shield-check"></i> E-posta adresiniz sadece bulten gondermek icin kullanilir, ucuncu taraflarla paylasilmaz.
                </div>
            </form>

            <div style="margin-top:18px;padding-top:18px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#64748b;">
                Aboneliginizi iptal etmek icin <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('newsletter-unsubscribe') : '/newsletter-unsubscribe'); ?>" style="color:#1e40af;font-weight:700;text-decoration:none;">tiklayin</a>.
            </div>
        </div>
    </div>
</section>
