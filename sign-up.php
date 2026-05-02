<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Uye Ol</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Uye Ol</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:560px;">
        <div class="cdg-card" style="padding:36px;">
            <h2 style="margin-bottom:8px;">Hesap olusturun</h2>
            <p class="text-muted" style="margin-bottom:24px;">CODEGA ailesine katilin, hizmetlerinize hemen baslayin.</p>

            <?php if(isset($error) && $error): ?>
                <div class="cdg-alert cdg-alert-error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if(isset($success) && $success): ?>
                <div class="cdg-alert cdg-alert-success"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <form action="<?php echo isset($register_link) ? $register_link : ''; ?>" method="post" id="Signup_Form" autocomplete="off">
                <?php echo Validation::get_csrf_token('sign'); ?>

                <div class="cdg-form-row">
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Ad</label>
                        <input type="text" name="name" class="cdg-form-control" required>
                    </div>
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Soyad</label>
                        <input type="text" name="surname" class="cdg-form-control" required>
                    </div>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">E-posta</label>
                    <input type="email" name="email" class="cdg-form-control" required>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Telefon</label>
                    <input type="tel" name="phone" class="cdg-form-control" placeholder="+90 5xx xxx xx xx">
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Sifre</label>
                    <input type="password" name="password" class="cdg-form-control" minlength="8" required>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-checkbox">
                        <input type="checkbox" name="agree" value="1" required>
                        <span>Kullanim sartlarini ve gizlilik politikasini kabul ediyorum.</span>
                    </label>
                </div>

                <button type="submit" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;">
                    <i class="bi bi-person-plus"></i> Hesap Olustur
                </button>
            </form>

            <div class="text-center mt-3" style="font-size:14px;color:var(--cdg-muted);">
                Zaten uye misiniz? <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('sign-in') : '/sign-in'); ?>" style="font-weight:600;">Giris yapin</a>
            </div>
        </div>
    </div>
</section>
