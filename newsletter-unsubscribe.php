<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';

$un_action = isset($unsubscribe_action) ? $unsubscribe_action : (isset($newsletter_unsubscribe_action) ? $newsletter_unsubscribe_action : '');
$un_email = isset($email) ? $email : '';
$un_status = isset($status) ? $status : '';
$un_message = isset($message) ? $message : '';
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-envelope-x"></i> Bulten Aboneligi Iptali</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Bulten Iptal</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:520px;">
        <div class="cdg-card" style="padding:36px;">
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#64748b,#94a3b8);color:#fff;display:inline-grid;place-items:center;font-size:32px;margin-bottom:12px;">
                    <i class="bi bi-envelope-slash"></i>
                </div>
                <h2 style="margin:0 0 8px;font-size:22px;font-weight:800;">Aboneligi Iptal Et</h2>
                <p class="text-muted" style="font-size:14px;line-height:1.5;margin:0;">
                    Bulten listemizden cikmak icin asagiya e-posta adresinizi girin.
                </p>
            </div>

            <?php if($un_status === 'successful'): ?>
            <div class="cdg-alert cdg-alert-success" style="margin-bottom:16px;">
                <i class="bi bi-check-circle"></i>
                <?php echo $un_message ?: 'Aboneliginiz basariyla iptal edildi.'; ?>
            </div>
            <?php elseif($un_status === 'error'): ?>
            <div class="cdg-alert cdg-alert-error" style="margin-bottom:16px;">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo $un_message ?: 'Bir hata olustu, lutfen tekrar deneyin.'; ?>
            </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($un_action, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" method="post">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('newsletter'); ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">E-posta Adresi</label>
                    <input type="email" name="email" class="cdg-form-control" placeholder="ornek@email.com" value="<?php echo htmlspecialchars($un_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" required>
                </div>

                <?php if(isset($captcha) && $captcha): ?>
                <div class="cdg-form-group">
                    <label class="cdg-form-label">Guvenlik Dogrulamasi</label>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;text-align:center;margin-bottom:8px;">
                        <?php echo $captcha->getOutput(); ?>
                    </div>
                    <?php if($captcha->input): ?>
                    <input type="text" name="<?php echo htmlspecialchars($captcha->input_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-form-control" placeholder="Resimdeki kodu girin" required>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <button type="submit" class="cdg-btn cdg-btn-danger" style="width:100%;padding:13px;margin-top:8px;">
                    <i class="bi bi-x-circle"></i> Aboneligimi Iptal Et
                </button>
            </form>

            <div style="margin-top:18px;padding-top:18px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#64748b;">
                Geri donmek istediginizde <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('newsletter') : '/newsletter'); ?>" style="color:#2E3B4E;font-weight:700;text-decoration:none;">tekrar abone olabilirsiniz</a>.
            </div>
        </div>
    </div>
</section>
