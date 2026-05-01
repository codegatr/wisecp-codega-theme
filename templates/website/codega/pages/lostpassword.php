<?php
/**
 * CODEGA Theme - Lost Password page
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = "Şifre Sıfırlama — CODEGA";
$description = "Şifrenizi unuttuysanız e-posta adresinizle yeniden belirleyebilirsiniz.";

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';

$err  = Filter::init('GET/error');
$sent = Filter::init('GET/sent');

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
            <h2 class="cg-mt-4">Şifrenizi <em>unutmuş olabilirsiniz</em>.</h2>
            <p>
                E-posta adresinizi girin, size sıfırlama bağlantısı gönderelim. Bağlantı 60 dakika
                geçerli olacak.
            </p>
        </div>
    </aside>

    <section class="cg-auth-form">
        <div class="cg-auth-form-inner">
            <h1>Şifre sıfırla</h1>
            <p class="lead">E-posta adresinize sıfırlama bağlantısı göndereceğiz.</p>

            <?php if ($sent): ?>
                <div class="cg-alert cg-alert-success">
                    <strong>Bağlantı gönderildi.</strong> Lütfen e-posta kutunuzu kontrol edin. Bağlantı 60 dakika geçerlidir.
                </div>
            <?php endif; ?>

            <?php if ($err): ?>
                <div class="cg-alert cg-alert-danger">
                    <?= htmlspecialchars($err === 'not_found' ? 'Bu e-posta ile kayıtlı hesap bulunamadı.' : 'Bir hata oluştu, tekrar deneyin.') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/lostpassword">
                <?= $csrf_field ?>

                <div class="cg-form-group">
                    <label class="cg-label" for="cg-email">E-posta Adresi</label>
                    <input type="email" id="cg-email" name="email" required autofocus
                           class="cg-input" placeholder="ornek@firma.com" autocomplete="email">
                </div>

                <button type="submit" class="cg-btn cg-btn-primary cg-btn-block cg-btn-lg">Sıfırlama Bağlantısı Gönder</button>
            </form>

            <div class="cg-text-center cg-mt-4" style="color:var(--cg-text-muted); font-size:14px;">
                Şifrenizi hatırladınız mı? <a href="/login" style="font-weight:600;">Giriş yapın</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
