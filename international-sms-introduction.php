<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-globe-europe-africa"></i> Uluslararasi SMS Hizmeti</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Uluslararasi SMS</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-bottom:32px;">
            <div class="cdg-card" style="padding:24px;text-align:center;">
                <div style="width:54px;height:54px;border-radius:12px;background:linear-gradient(135deg,#3b82f6,#60a5fa);color:#fff;display:inline-grid;place-items:center;font-size:24px;margin-bottom:12px;">
                    <i class="bi bi-globe2"></i>
                </div>
                <h3 style="font-size:16px;font-weight:800;margin:0 0 6px;">200+ Ulke</h3>
                <p style="font-size:13px;color:#64748b;margin:0;">Dunya genelinde 200'den fazla ulkeye SMS gonderin.</p>
            </div>
            <div class="cdg-card" style="padding:24px;text-align:center;">
                <div style="width:54px;height:54px;border-radius:12px;background:linear-gradient(135deg,#10b981,#34d399);color:#fff;display:inline-grid;place-items:center;font-size:24px;margin-bottom:12px;">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <h3 style="font-size:16px;font-weight:800;margin:0 0 6px;">Aninda Teslimat</h3>
                <p style="font-size:13px;color:#64748b;margin:0;">Yuksek hizla SMS gonderim altyapisi.</p>
            </div>
            <div class="cdg-card" style="padding:24px;text-align:center;">
                <div style="width:54px;height:54px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#a78bfa);color:#fff;display:inline-grid;place-items:center;font-size:24px;margin-bottom:12px;">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h3 style="font-size:16px;font-weight:800;margin:0 0 6px;">Detayli Raporlar</h3>
                <p style="font-size:13px;color:#64748b;margin:0;">Teslimat raporlari, basari oranlari, kara liste yonetimi.</p>
            </div>
        </div>

        <div class="cdg-card" style="padding:36px;text-align:center;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;">
            <i class="bi bi-rocket-takeoff" style="font-size:48px;margin-bottom:16px;"></i>
            <h2 style="font-size:24px;font-weight:800;margin:0 0 10px;">Hemen Baslamak Icin</h2>
            <p style="font-size:14px;opacity:0.9;margin:0 0 20px;">SMS hizmetlerimizi inceleyip size uygun paketi secin</p>
            <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:12px 24px;background:#fff;color:#1e40af;text-decoration:none;border-radius:8px;font-size:14px;font-weight:700;">
                <i class="bi bi-chat-dots"></i> Iletisime Gec
            </a>
        </div>
    </div>
</section>
