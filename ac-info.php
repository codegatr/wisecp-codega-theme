<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$info = [];
if(class_exists('User') && isset(User::$init->info)) $info = User::$init->info;
elseif(isset($user_info)) $info = $user_info;
elseif(isset($info_data)) $info = $info_data;

$f = function($key, $default = '') use ($info) {
    return isset($info[$key]) ? $info[$key] : $default;
};
?>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-person"></i> Hesap Bilgilerim</h3>
    </div>

    <?php if(isset($error) && $error): ?>
        <div class="cdg-alert cdg-alert-error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    <?php if(isset($success) && $success): ?>
        <div class="cdg-alert cdg-alert-success"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="post" autocomplete="off">
        <?php if(class_exists('Validation') && method_exists('Validation', 'get_csrf_token')): ?>
            <input type="hidden" name="token" value="<?php echo Validation::get_csrf_token('account', false); ?>">
        <?php endif; ?>

        <h4 style="margin:20px 0 12px;color:var(--cdg-text);"><i class="bi bi-person-badge"></i> Kisisel Bilgiler</h4>
        <div class="cdg-form-row">
            <div class="cdg-form-group">
                <label class="cdg-form-label">Ad</label>
                <input type="text" name="name" class="cdg-form-control" value="<?php echo htmlspecialchars($f('name')); ?>">
            </div>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Soyad</label>
                <input type="text" name="surname" class="cdg-form-control" value="<?php echo htmlspecialchars($f('surname')); ?>">
            </div>
        </div>

        <div class="cdg-form-row">
            <div class="cdg-form-group">
                <label class="cdg-form-label">E-posta</label>
                <input type="email" name="email" class="cdg-form-control" value="<?php echo htmlspecialchars($f('email')); ?>">
            </div>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Telefon</label>
                <input type="tel" name="phone" class="cdg-form-control" value="<?php echo htmlspecialchars($f('phone')); ?>">
            </div>
        </div>

        <h4 style="margin:24px 0 12px;color:var(--cdg-text);"><i class="bi bi-geo-alt"></i> Adres Bilgileri</h4>
        <div class="cdg-form-group">
            <label class="cdg-form-label">Adres</label>
            <textarea name="address" class="cdg-form-control" rows="2"><?php echo htmlspecialchars($f('address')); ?></textarea>
        </div>

        <div class="cdg-form-row">
            <div class="cdg-form-group">
                <label class="cdg-form-label">Sehir</label>
                <input type="text" name="city" class="cdg-form-control" value="<?php echo htmlspecialchars($f('city')); ?>">
            </div>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Posta Kodu</label>
                <input type="text" name="zipcode" class="cdg-form-control" value="<?php echo htmlspecialchars($f('zipcode')); ?>">
            </div>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Ulke</label>
                <input type="text" name="country" class="cdg-form-control" value="<?php echo htmlspecialchars($f('country', 'Turkiye')); ?>">
            </div>
        </div>

        <h4 style="margin:24px 0 12px;color:var(--cdg-text);"><i class="bi bi-shield-lock"></i> Sifre Degisikligi</h4>
        <p style="font-size:13px;color:var(--cdg-muted);margin-bottom:12px;">Sifrenizi degistirmek istemiyorsaniz bu alanlari bos birakin.</p>
        <div class="cdg-form-row">
            <div class="cdg-form-group">
                <label class="cdg-form-label">Mevcut Sifre</label>
                <input type="password" name="current_password" class="cdg-form-control" autocomplete="current-password">
            </div>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Yeni Sifre</label>
                <input type="password" name="new_password" class="cdg-form-control" autocomplete="new-password">
            </div>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Yeni Sifre (Tekrar)</label>
                <input type="password" name="new_password_repeat" class="cdg-form-control" autocomplete="new-password">
            </div>
        </div>

        <div style="margin-top:24px;display:flex;justify-content:flex-end;">
            <button type="submit" name="action" value="update-info" class="cdg-btn cdg-btn-primary">
                <i class="bi bi-check2"></i> Degisiklikleri Kaydet
            </button>
        </div>
    </form>
</div>
