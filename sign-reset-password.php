<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Sifre Sifirla</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Sifre Sifirla</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:480px;">
        <div class="cdg-card" style="padding:36px;">
            <h2 style="margin-bottom:8px;">Sifrenizi mi unuttunuz?</h2>
            <p class="text-muted" style="margin-bottom:24px;">E-posta adresinizi girin, size sifirlama baglantisi gonderelim.</p>

            <?php if(isset($error) && $error): ?>
                <div class="cdg-alert cdg-alert-error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if(isset($success) && $success): ?>
                <div class="cdg-alert cdg-alert-success"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <form action="<?php echo isset($forget_password_link) ? $forget_password_link : ''; ?>" method="post" id="ForgotPassword_Form">
                <?php echo Validation::get_csrf_token('sign'); ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">E-posta</label>
                    <input type="email" name="email" class="cdg-form-control" required autofocus>
                </div>

                <button type="submit" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;">
                    <i class="bi bi-envelope-arrow-up"></i> Sifirlama Baglantisi Gonder
                </button>
            </form>

            <div class="text-center mt-3" style="font-size:14px;color:var(--cdg-muted);">
                <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('sign-in') : '/sign-in'); ?>" style="font-weight:600;">Girise don</a>
            </div>
        </div>
    </div>
</section>
