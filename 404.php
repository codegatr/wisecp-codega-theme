<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Sayfa Bulunamadı</h1>
        <div class="breadcrumb">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>">Anasayfa</a>
            <span class="sep">/</span>
            <span>404</span>
        </div>
    </div>
</section>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-compass"></i></div>
            <h3>Aradığınız sayfa bulunamadı</h3>
            <p>Sayfa kaldırılmış veya taşınmış olabilir.</p>
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>" class="cdg-btn cdg-btn-primary mt-3">
                <i class="bi bi-house"></i> Anasayfaya Dön
            </a>
        </div>
    </div>
</section>
