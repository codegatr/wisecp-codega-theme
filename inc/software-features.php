<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Yazılım Özellikleri (3 adım akış)
 * Yazılım/lisans satış sayfasının üst kısmında "Seç → Lisansla → Kullan" gibi süreç gösterir
 * WiseCP runtime: locale stringleri (website/softwares/feature-{1,2,3}-{icon,text})
 */

// Locale fallback
function cdg_sf_text($key, $default = '') {
    if(function_exists('__')) {
        $val = __($key);
        if($val && $val !== $key) return $val;
    }
    return $default;
}

// İkonları Bootstrap Icons'a normalize et (WiseCP'de fa-* gelirse fallback yap)
function cdg_sf_normalize_icon($icon, $fallback) {
    if(!$icon) return $fallback;
    // Eğer fa- ile başlıyorsa Bootstrap Icons karşılığını göster
    if(strpos($icon, 'fa-') === 0 || strpos($icon, 'fa ') !== false) {
        return $fallback;
    }
    return $icon;
}

$features = [
    [
        'icon' => cdg_sf_normalize_icon(cdg_sf_text("website/softwares/feature-1-icon"), 'bi bi-cart-check'),
        'text' => cdg_sf_text("website/softwares/feature-1-text", 'Lisansı Seçin'),
    ],
    [
        'icon' => cdg_sf_normalize_icon(cdg_sf_text("website/softwares/feature-2-icon"), 'bi bi-credit-card-2-back'),
        'text' => cdg_sf_text("website/softwares/feature-2-text", 'Güvenli Ödeme'),
    ],
    [
        'icon' => cdg_sf_normalize_icon(cdg_sf_text("website/softwares/feature-3-icon"), 'bi bi-rocket-takeoff'),
        'text' => cdg_sf_text("website/softwares/feature-3-text", 'Anında Aktivasyon'),
    ],
];
?>

<div class="cdg-sf" data-aos="fade-up">
    <?php foreach($features as $i => $f): ?>
    <div class="cdg-sf-step">
        <div class="cdg-sf-num"><?php echo $i + 1; ?></div>
        <div class="cdg-sf-icon">
            <i class="<?php echo htmlspecialchars($f['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
        </div>
        <h4 class="cdg-sf-title"><?php echo htmlspecialchars($f['text'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h4>
    </div>
    <?php if($i < count($features) - 1): ?>
    <div class="cdg-sf-arrow" aria-hidden="true">
        <i class="bi bi-chevron-right"></i>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <div class="cdg-sf-check" aria-hidden="true">
        <i class="bi bi-check-circle-fill"></i>
    </div>
</div>

<style>
.cdg-sf {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 28px 22px;
    background: linear-gradient(135deg, #eff6ff 0%, #CFFAFE 50%, #fef3c7 100%);
    border: 1px solid #A5F3FC;
    border-radius: 18px;
    margin: 22px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-sf *, .cdg-sf *::before, .cdg-sf *::after { box-sizing: border-box; }

.cdg-sf-step {
    display: flex; flex-direction: column; align-items: center;
    gap: 8px;
    text-align: center;
    position: relative;
    padding: 14px 18px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.06);
    min-width: 140px;
    transition: transform 0.22s, box-shadow 0.22s;
}
.cdg-sf-step:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(46,59,78,0.14);
}

.cdg-sf-num {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 26px; height: 26px;
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1A2332;
    border-radius: 50%;
    display: grid; place-items: center;
    font-size: 12px;
    font-weight: 900;
    box-shadow: 0 2px 8px rgba(252,211,77,0.40);
}

.cdg-sf-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    display: grid; place-items: center;
    font-size: 24px;
    box-shadow: 0 6px 14px rgba(46,59,78,0.25);
}

.cdg-sf-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
    max-width: 140px;
}

.cdg-sf-arrow {
    color: #94a3b8;
    font-size: 22px;
    display: flex; align-items: center;
}

.cdg-sf-check {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
    box-shadow: 0 6px 14px rgba(16,185,129,0.30);
    margin-left: 8px;
}

@media (max-width: 720px) {
    .cdg-sf { flex-direction: column; gap: 14px; }
    .cdg-sf-step { width: 100%; max-width: 320px; }
    .cdg-sf-arrow { transform: rotate(90deg); }
}
</style>
