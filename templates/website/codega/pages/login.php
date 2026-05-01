<?php
/**
 * CODEGA Theme - Login page
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = "Giriş Yap — CODEGA";
$description = "CODEGA müşteri panelinize giriş yapın. Hizmetlerinizi yönetin, faturalarınızı görüntüleyin.";

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'codega-bridge.php';

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';

$err   = Filter::init('GET/error');
$msg   = Filter::init('GET/msg');
$email = Filter::init('GET/email');

$action_url = '/login';
$csrf_field = '';
if (function_exists('CSRF') || class_exists('CSRF')) {
    if (method_exists('CSRF', 'token')) {
        $csrf_field = '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(CSRF::token()) . '">';
    }
}

$sso_enabled = Codega_Bridge::enabled();
?>
<body>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'header.php'; ?>

<main class="cg-auth">
    <!-- Branded side -->
    <aside class="cg-auth-side">
        <div>
            <a href="/" class="cg-logo">
                <span class="cg-logo-mark">C</span>
                <span class="cg-logo-text">CO<span>DE</span>GA</span>
            </a>
            <h2 class="cg-mt-4">İşinizi yöneten <em>tek panel</em>.</h2>
            <p>
                Hosting, alan adları, faturalar ve destek talepleriniz — hepsi tek bir yerden,
                hızlı ve güvenli erişimle.
            </p>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; padding-top:32px; border-top:1px solid rgba(212,165,116,0.18);">
            <div>
                <div style="font-family:var(--cg-font-display); font-size:2rem; font-weight:500; color:var(--cg-gold); line-height:1;">SSL</div>
                <div style="font-size:13px; color:rgba(255,255,255,0.55); margin-top:4px;">Şifreli Bağlantı</div>
            </div>
            <div>
                <div style="font-family:var(--cg-font-display); font-size:2rem; font-weight:500; color:var(--cg-gold); line-height:1;">2FA</div>
                <div style="font-size:13px; color:rgba(255,255,255,0.55); margin-top:4px;">İki Faktörlü Doğrulama</div>
            </div>
        </div>
    </aside>

    <!-- Form side -->
    <section class="cg-auth-form">
        <div class="cg-auth-form-inner">
            <h1>Tekrar hoş geldiniz</h1>
            <p class="lead">Müşteri panelinize giriş yapın.</p>

            <?php if ($err): ?>
                <div class="cg-alert cg-alert-danger">
                    <?php
                    $msgs = [
                        'invalid'                       => 'E-posta veya şifre hatalı.',
                        'locked'                        => 'Hesabınız geçici olarak kilitli. Lütfen şifre sıfırlama yapın.',
                        'sso_user_resolution_failed'    => 'codega.com.tr ile giriş başarısız. Lütfen e-posta/şifre ile giriş yapın.',
                    ];
                    echo htmlspecialchars($msgs[$err] ?? 'Bir hata oluştu, tekrar deneyin.');
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($msg === 'logged_out'): ?>
                <div class="cg-alert cg-alert-success">Başarıyla çıkış yaptınız.</div>
            <?php endif; ?>

            <?php if ($sso_enabled): ?>
                <a href="<?= htmlspecialchars(Codega_Bridge::loginRedirectUrl()) ?>"
                   class="cg-btn cg-btn-dark cg-btn-block"
                   style="background:#142042; border:1px solid rgba(212,165,116,0.3);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    codega.com.tr ile Giriş Yap
                </a>
                <div class="cg-auth-divider">veya e-posta ile</div>
            <?php endif; ?>

            <form method="POST" action="<?= htmlspecialchars($action_url) ?>" autocomplete="on">
                <?= $csrf_field ?>

                <div class="cg-form-group">
                    <label class="cg-label" for="cg-email">E-posta Adresi</label>
                    <input type="email" id="cg-email" name="email" required autofocus
                           value="<?= htmlspecialchars($email) ?>"
                           class="cg-input" placeholder="ornek@firma.com" autocomplete="email">
                </div>

                <div class="cg-form-group">
                    <div style="display:flex; justify-content:space-between; align-items:baseline;">
                        <label class="cg-label" for="cg-pass">Şifre</label>
                        <a href="/lostpassword" style="font-size:12px;">Şifremi unuttum</a>
                    </div>
                    <input type="password" id="cg-pass" name="password" required
                           class="cg-input" placeholder="••••••••" autocomplete="current-password">
                </div>

                <div class="cg-form-group" style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" id="cg-remember" name="remember" value="1">
                    <label for="cg-remember" style="font-size:13px; color:var(--cg-text-muted); cursor:pointer;">Beni hatırla</label>
                </div>

                <button type="submit" class="cg-btn cg-btn-primary cg-btn-block cg-btn-lg">Giriş Yap</button>
            </form>

            <div class="cg-text-center cg-mt-4" style="color:var(--cg-text-muted); font-size:14px;">
                Hesabınız yok mu? <a href="/register" style="font-weight:600;">Hemen kaydolun</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
