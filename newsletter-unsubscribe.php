<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Bulten Iptal</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Bulten Iptal</span>
        </div>
    </div>
</section>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-envelope-x"></i></div>
            <h3>Bulten Iptal</h3>
            <p>Abonelik iptal edildi.</p>
            <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-primary mt-3">
                <i class="bi bi-chat-dots"></i> Bilgi Al
            </a>
        </div>
    </div>
</section>
