<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Giris Yap</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Giris</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:480px;">
        <div class="cdg-card" style="padding:36px;">
            <h2 style="margin-bottom:8px;">Tekrar hos geldiniz</h2>
            <p class="text-muted" style="margin-bottom:24px;">Hesabiniza giris yaparak hizmetlerinizi yonetin.</p>

            <?php if(isset($error) && $error): ?>
                <div class="cdg-alert cdg-alert-error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if(isset($success) && $success): ?>
                <div class="cdg-alert cdg-alert-success"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <form action="<?php echo isset($login_link) ? $login_link : ''; ?>" method="post" id="Signin_Form" autocomplete="off">
                <?php echo Validation::get_csrf_token('sign'); ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">E-posta</label>
                    <input type="text" name="email" class="cdg-form-control" required autofocus>
                </div>

                <div class="cdg-form-group">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;">
                        <label class="cdg-form-label">Sifre</label>
                        <a href="javascript:void(0);" onclick="if(typeof forget_password==='function'){forget_password();return false;}" style="font-size:12px;">Sifremi unuttum</a>
                    </div>
                    <input type="password" name="password" class="cdg-form-control" required>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-checkbox">
                        <input type="checkbox" name="remember" value="1">
                        <span>Beni hatirla</span>
                    </label>
                </div>

                <button type="submit" class="cdg-btn cdg-btn-primary mio-ajax-submit" style="width:100%;padding:13px;">
                    <i class="bi bi-box-arrow-in-right"></i> Giris Yap
                </button>
            </form>

            <div class="text-center mt-3" style="font-size:14px;color:var(--cdg-muted);">
                Henuz uye degil misiniz?
                <a href="<?php echo isset($register_link) ? $register_link : (class_exists('Controllers') ? Controllers::$init->CRLink('sign-up') : '/sign-up'); ?>" style="font-weight:600;">Hemen kayit olun</a>
            </div>
        </div>
    </div>
</section>
