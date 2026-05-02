<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$config    = include __DIR__ . DS . 'theme-config.php';
$ts        = isset($config['settings']) ? $config['settings'] : [];
$contact_i = isset($ts['contact']) ? $ts['contact'] : [];
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Iletisim</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Iletisim</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-grid cdg-grid-2" style="gap:48px;">

            <div>
                <span class="cdg-section-eyebrow">Bize ulasin</span>
                <h2 style="margin-top:8px;">Sorulariniz mi var?</h2>
                <p style="color:var(--cdg-muted);font-size:16px;margin-bottom:30px;">
                    Projeleriniz, hizmetlerimiz veya mevcut hesabinizla ilgili her konuda yanindayiz.
                </p>

                <div style="display:flex;flex-direction:column;gap:18px;">
                    <?php if(!empty($contact_i['phone'])): ?>
                    <div style="display:flex;gap:14px;">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--cdg-gradient-subtle);color:var(--cdg-primary);display:grid;place-items:center;flex-shrink:0;"><i class="bi bi-telephone" style="font-size:18px;"></i></div>
                        <div>
                            <div style="font-weight:600;font-size:14px;">Telefon</div>
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/','',$contact_i['phone']); ?>" style="font-size:14px;color:var(--cdg-muted);"><?php echo htmlspecialchars($contact_i['phone']); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($contact_i['email'])): ?>
                    <div style="display:flex;gap:14px;">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--cdg-gradient-subtle);color:var(--cdg-primary);display:grid;place-items:center;flex-shrink:0;"><i class="bi bi-envelope" style="font-size:18px;"></i></div>
                        <div>
                            <div style="font-weight:600;font-size:14px;">E-posta</div>
                            <a href="mailto:<?php echo $contact_i['email']; ?>" style="font-size:14px;color:var(--cdg-muted);"><?php echo htmlspecialchars($contact_i['email']); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($contact_i['address'])): ?>
                    <div style="display:flex;gap:14px;">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--cdg-gradient-subtle);color:var(--cdg-primary);display:grid;place-items:center;flex-shrink:0;"><i class="bi bi-geo-alt" style="font-size:18px;"></i></div>
                        <div>
                            <div style="font-weight:600;font-size:14px;">Adres</div>
                            <div style="font-size:14px;color:var(--cdg-muted);"><?php echo htmlspecialchars($contact_i['address']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="cdg-card" style="padding:32px;">
                    <h3 style="margin-bottom:20px;">Bize Yazin</h3>

                    <?php if(isset($error) && $error): ?>
                        <div class="cdg-alert cdg-alert-error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if(isset($success) && $success): ?>
                        <div class="cdg-alert cdg-alert-success"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
                    <?php endif; ?>

                    <form action="" method="post">
                <?php if(class_exists('Validation') && method_exists('Validation', 'get_csrf_token')) echo Validation::get_csrf_token('sign'); ?>
                        <div class="cdg-form-row">
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Ad Soyad</label>
                                <input type="text" name="name" class="cdg-form-control" required>
                            </div>
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">E-posta</label>
                                <input type="email" name="email" class="cdg-form-control" required>
                            </div>
                        </div>

                        <div class="cdg-form-group">
                            <label class="cdg-form-label">Konu</label>
                            <input type="text" name="subject" class="cdg-form-control" required>
                        </div>

                        <div class="cdg-form-group">
                            <label class="cdg-form-label">Mesaj</label>
                            <textarea name="message" class="cdg-form-control" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;">
                            <i class="bi bi-send"></i> Mesaji Gonder
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
