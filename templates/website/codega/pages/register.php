<?php
/**
 * CODEGA Theme - Register page
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = "Kayıt Ol — CODEGA";
$description = "CODEGA müşteri hesabı oluşturun. Hosting, domain ve özel yazılım hizmetlerimize anında erişin.";

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';

$err   = Filter::init('GET/error');
$email = Filter::init('GET/email');
$from  = Filter::init('GET/from');

$csrf_field = '';
if (class_exists('CSRF') && method_exists('CSRF', 'token')) {
    $csrf_field = '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(CSRF::token()) . '">';
}
?>
<body>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'header.php'; ?>

<main class="cg-auth">
    <aside class="cg-auth-side">
        <div>
            <a href="/" class="cg-logo">
                <span class="cg-logo-mark">C</span>
                <span class="cg-logo-text">CO<span>DE</span>GA</span>
            </a>
            <h2 class="cg-mt-4">Hesabınızı oluşturun, <em>30 saniyede</em> hazır.</h2>
            <p>
                Kayıt olun, hosting paketinizi seçin, alan adınızı tescil ettirin. Tek bir hesapla
                tüm CODEGA hizmetlerini yönetin.
            </p>
        </div>

        <ul style="list-style:none; padding:0; margin-top:32px; display:flex; flex-direction:column; gap:14px;">
            <li style="display:flex; gap:12px; align-items:flex-start;">
                <span style="width:24px;height:24px;background:rgba(212,165,116,0.15);color:var(--cg-gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">✓</span>
                <span style="color:rgba(255,255,255,0.85); font-size:14px;">Anında aktivasyon, kredi kartı veya havale</span>
            </li>
            <li style="display:flex; gap:12px; align-items:flex-start;">
                <span style="width:24px;height:24px;background:rgba(212,165,116,0.15);color:var(--cg-gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">✓</span>
                <span style="color:rgba(255,255,255,0.85); font-size:14px;">İlk 30 gün içinde iade garantisi</span>
            </li>
            <li style="display:flex; gap:12px; align-items:flex-start;">
                <span style="width:24px;height:24px;background:rgba(212,165,116,0.15);color:var(--cg-gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">✓</span>
                <span style="color:rgba(255,255,255,0.85); font-size:14px;">Mevcut sitelerinizi ücretsiz taşıyalım</span>
            </li>
        </ul>
    </aside>

    <section class="cg-auth-form">
        <div class="cg-auth-form-inner">
            <h1>Hesap oluştur</h1>
            <p class="lead">Birkaç saniye içinde tamam.</p>

            <?php if ($from === 'codega-sso'): ?>
                <div class="cg-alert cg-alert-gold">
                    <strong>codega.com.tr hesabınızı bağlıyoruz.</strong> Lütfen bilgilerinizi tamamlayarak müşteri panelinizi açın.
                </div>
            <?php endif; ?>

            <?php if ($err): ?>
                <div class="cg-alert cg-alert-danger">
                    <?php
                    $msgs = [
                        'email_exists'   => 'Bu e-posta adresi zaten kayıtlı. Giriş yapmayı deneyin.',
                        'invalid_email'  => 'Geçerli bir e-posta adresi girin.',
                        'weak_password'  => 'Şifre en az 8 karakter olmalı.',
                        'password_mismatch' => 'Şifreler eşleşmiyor.',
                    ];
                    echo htmlspecialchars($msgs[$err] ?? 'Bir hata oluştu, tekrar deneyin.');
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" autocomplete="on">
                <?= $csrf_field ?>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div class="cg-form-group">
                        <label class="cg-label" for="cg-name">Ad</label>
                        <input type="text" id="cg-name" name="name" required class="cg-input" autocomplete="given-name">
                    </div>
                    <div class="cg-form-group">
                        <label class="cg-label" for="cg-surname">Soyad</label>
                        <input type="text" id="cg-surname" name="surname" required class="cg-input" autocomplete="family-name">
                    </div>
                </div>

                <div class="cg-form-group">
                    <label class="cg-label" for="cg-email">E-posta Adresi</label>
                    <input type="email" id="cg-email" name="email" required
                           value="<?= htmlspecialchars($email) ?>"
                           class="cg-input" placeholder="ornek@firma.com" autocomplete="email">
                </div>

                <div class="cg-form-group">
                    <label class="cg-label" for="cg-phone">Telefon</label>
                    <input type="tel" id="cg-phone" name="phone" required class="cg-input" placeholder="+90 5XX XXX XX XX" autocomplete="tel">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div class="cg-form-group">
                        <label class="cg-label" for="cg-pass">Şifre</label>
                        <input type="password" id="cg-pass" name="password" required minlength="8" class="cg-input" autocomplete="new-password">
                        <div class="cg-help">En az 8 karakter</div>
                    </div>
                    <div class="cg-form-group">
                        <label class="cg-label" for="cg-pass2">Şifre Tekrar</label>
                        <input type="password" id="cg-pass2" name="password_confirm" required minlength="8" class="cg-input" autocomplete="new-password">
                    </div>
                </div>

                <div class="cg-form-group" style="display:flex; align-items:flex-start; gap:10px;">
                    <input type="checkbox" id="cg-terms" name="terms" value="1" required style="margin-top:3px;">
                    <label for="cg-terms" style="font-size:13px; color:var(--cg-text-muted); cursor:pointer; line-height:1.5;">
                        <a href="/legal/terms" target="_blank">Kullanım Şartları</a> ve
                        <a href="/legal/privacy" target="_blank">Gizlilik Politikası</a>'nı okudum, kabul ediyorum.
                    </label>
                </div>

                <button type="submit" class="cg-btn cg-btn-primary cg-btn-block cg-btn-lg">Hesap Oluştur</button>
            </form>

            <div class="cg-text-center cg-mt-4" style="color:var(--cg-text-muted); font-size:14px;">
                Zaten hesabınız var mı? <a href="/login" style="font-weight:600;">Giriş yapın</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
