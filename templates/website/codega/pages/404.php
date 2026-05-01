<?php
/**
 * CODEGA Theme - 404 Page
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

if (!headers_sent()) http_response_code(404);

$title = "Sayfa Bulunamadı — CODEGA";
$description = "Aradığınız sayfa bulunamadı.";

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';
?>
<body>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'header.php'; ?>

<section style="min-height:60vh; display:flex; align-items:center; justify-content:center; padding:80px 24px; background:var(--cg-bg-soft);">
    <div style="text-align:center; max-width:520px;">
        <div style="font-family:var(--cg-font-display); font-size:9rem; font-weight:500; color:var(--cg-gold); line-height:1; letter-spacing:-0.05em;">
            404
        </div>
        <h1 style="margin-top:8px;">Bulamadık.</h1>
        <p class="cg-text-muted cg-mt-2 cg-mb-4">
            Aradığınız sayfa kaldırılmış, taşınmış veya hiç var olmamış olabilir.
            Anasayfaya dönüp tekrar deneyebilirsiniz.
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="/" class="cg-btn cg-btn-primary">Anasayfaya Dön</a>
            <a href="/contact" class="cg-btn cg-btn-ghost">Destek İletişimi</a>
        </div>
    </div>
</section>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
