<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-server"></i> Server Hizmetleri</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Server Hizmetleri</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:600px;">
        <div class="cdg-card" style="padding:40px 32px;text-align:center;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#7c3aed99);color:#fff;display:inline-grid;place-items:center;font-size:36px;margin-bottom:18px;">
                <i class="bi bi-server"></i>
            </div>
            <h2 style="font-size:22px;font-weight:800;margin:0 0 10px;">Server Hizmetlerimizi Inceleyin</h2>
            <p style="font-size:14px;color:#64748b;margin:0 0 22px;line-height:1.5;">
                Profesyonel Server cozumlerimiz hakkinda detayli bilgi almak icin bizimle iletisime gecin
                veya tum hizmetlerimizi inceleyin.
            </p>
            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-chat-dots"></i> Iletisim
                </a>
                <a href="<?php echo APP_URI; ?>/" class="cdg-btn cdg-btn-outline">
                    <i class="bi bi-house"></i> Anasayfa
                </a>
            </div>
        </div>
    </div>
</section>
