<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Hizmet Tanitim Sayfasi Sablonu
 *
 * Caller variables:
 *  - $svc_title         : Sayfa basligi
 *  - $svc_subtitle      : Alt baslik / aciklama
 *  - $svc_icon          : Bootstrap ikon (header)
 *  - $svc_gradient      : Hero gradient (CSS)
 *  - $svc_color         : Tema rengi (vurgu)
 *  - $svc_breadcrumb    : Breadcrumb etiketi
 *  - $svc_features      : Array of ['icon' => 'bi-name', 'title' => 'X', 'desc' => 'Y']
 *  - $svc_description   : Uzun aciklama metni (HTML destekli)
 *  - $svc_cta_text      : CTA buton metni (default: 'Bilgi Al')
 *  - $svc_cta_link      : CTA link (default: contact)
 *  - $svc_extra_html    : Opsiyonel ek HTML
 */

$svc_title       = $svc_title ?? 'Hizmet';
$svc_subtitle    = $svc_subtitle ?? '';
$svc_icon        = $svc_icon ?? 'box-seam';
$svc_gradient    = $svc_gradient ?? 'linear-gradient(135deg,#2E3B4E,#00D3E5)';
$svc_color       = $svc_color ?? '#2E3B4E';
$svc_breadcrumb  = $svc_breadcrumb ?? $svc_title;
$svc_features    = $svc_features ?? [];
$svc_description = $svc_description ?? '';
$svc_cta_text    = $svc_cta_text ?? 'Bilgi Al';
$svc_cta_link    = $svc_cta_link ?? (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact');
$svc_extra_html  = $svc_extra_html ?? '';
?>

<section class="cdg-page-head" style="background: <?php echo $svc_gradient; ?> !important; color: #fff; border-bottom: 0;">
    <div class="cdg-container">
        <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
            <div style="width:64px;height:64px;border-radius:14px;background:rgba(255,255,255,0.20);backdrop-filter:blur(10px);display:grid;place-items:center;font-size:28px;color:#fff;flex-shrink:0;">
                <i class="bi bi-<?php echo htmlspecialchars($svc_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
            </div>
            <div>
                <h1 style="color:#fff;margin:0 0 4px;"><?php echo htmlspecialchars($svc_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
                <?php if($svc_subtitle): ?>
                <p style="color:rgba(255,255,255,0.92);margin:0;font-size:14px;">
                    <?php echo htmlspecialchars($svc_subtitle, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="breadcrumb" style="color:rgba(255,255,255,0.85);margin-top:14px;">
            <a href="<?php echo APP_URI; ?>/" style="color:rgba(255,255,255,0.85);">Anasayfa</a>
            <span class="sep" style="color:rgba(255,255,255,0.5);">/</span>
            <span><?php echo htmlspecialchars($svc_breadcrumb, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">

        <?php if(!empty($svc_features)): ?>
        <div class="cdg-svc-features" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-bottom:32px;">
            <?php foreach($svc_features as $f):
                $f_icon = $f['icon'] ?? 'check-circle';
                $f_title = $f['title'] ?? '';
                $f_desc = $f['desc'] ?? '';
            ?>
            <div class="cdg-card" style="padding:24px;text-align:center;transition:transform 0.2s,box-shadow 0.2s;">
                <div style="width:54px;height:54px;border-radius:12px;background:<?php echo htmlspecialchars($svc_gradient, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>;color:#fff;display:inline-grid;place-items:center;font-size:22px;margin-bottom:14px;box-shadow:0 6px 16px rgba(15,23,42,0.10);">
                    <i class="bi bi-<?php echo htmlspecialchars($f_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                </div>
                <h3 style="font-size:15px;font-weight:800;margin:0 0 6px;color:#0f172a;"><?php echo htmlspecialchars($f_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;"><?php echo htmlspecialchars($f_desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if($svc_description): ?>
        <div class="cdg-card" style="padding:32px;margin-bottom:24px;">
            <div style="font-size:15px;line-height:1.7;color:#334155;font-family:'Plus Jakarta Sans',sans-serif;">
                <?php echo $svc_description; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($svc_extra_html) echo $svc_extra_html; ?>

        <!-- CTA -->
        <div class="cdg-card" style="padding:36px;text-align:center;background:<?php echo htmlspecialchars($svc_gradient, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>;color:#fff;">
            <i class="bi bi-rocket-takeoff" style="font-size:42px;margin-bottom:12px;display:block;opacity:0.9;"></i>
            <h2 style="font-size:22px;font-weight:800;margin:0 0 8px;color:#fff;">Hemen Baslayin</h2>
            <p style="font-size:14px;opacity:0.92;margin:0 0 18px;">Detaylı bilgi ve fiyat teklifi için bizimle iletişime geçin.</p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a href="<?php echo htmlspecialchars($svc_cta_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:12px 24px;background:#fff;color:<?php echo htmlspecialchars($svc_color, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>;text-decoration:none;border-radius:8px;font-size:14px;font-weight:700;transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <i class="bi bi-chat-dots"></i> <?php echo htmlspecialchars($svc_cta_text, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </a>
                <a href="<?php echo APP_URI; ?>/" style="display:inline-flex;align-items:center;gap:6px;padding:12px 24px;background:rgba(255,255,255,0.15);color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:700;border:1px solid rgba(255,255,255,0.25);">
                    <i class="bi bi-house"></i> Anasayfa
                </a>
            </div>
        </div>

    </div>
</section>

<style>
.cdg-svc-features .cdg-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(15,23,42,0.10); }
</style>
