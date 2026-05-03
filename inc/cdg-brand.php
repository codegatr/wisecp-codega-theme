<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA Brand Identity - Merkezi Kurumsal Kimlik
 *
 * Logo (SVG inline) + Renk Paleti + Tipografi tek noktada.
 * Tüm sayfalardan include edilir, CSS değişkenleri herkesçe kullanılabilir.
 *
 * Renk Paleti (logodan):
 *   #00E5FF  Cyan Bright (glow)
 *   #00D3E5  Cyan Mid (accent)
 *   #2E3B4E  Lacivert/Kurumsal (primary)
 *   #B0B3B6  Gümüş Gri (silver)
 */

if(!function_exists('cdg_logo_svg')) {
    /**
     * Codega Logo - Inline SVG (mesh/network architecture)
     *
     * @param string $variant 'full' | 'icon' | 'mark' | 'mono' | 'white'
     * @param int    $size    Yükseklik (px)
     * @param string $extra   Ekstra CSS class
     */
    function cdg_logo_svg($variant = 'full', $size = 40, $extra = '') {
        $variant = in_array($variant, ['full','icon','mark','mono','white']) ? $variant : 'full';

        // ICON SVG (kürelelik network mesh)
        $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="cdg-brand-icon-svg" aria-hidden="true">
            <defs>
                <radialGradient id="cdgIconGlow' . uniqid() . '" cx="50%" cy="50%" r="60%">
                    <stop offset="0%" stop-color="#00E5FF" stop-opacity="0.85"/>
                    <stop offset="55%" stop-color="#00D3E5" stop-opacity="0.50"/>
                    <stop offset="100%" stop-color="#2E3B4E" stop-opacity="0.95"/>
                </radialGradient>
                <linearGradient id="cdgIconStroke' . uniqid() . '" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#00E5FF"/>
                    <stop offset="100%" stop-color="#00D3E5"/>
                </linearGradient>
                <filter id="cdgIconBlur' . uniqid() . '" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="0.8"/>
                </filter>
            </defs>
            <!-- Hexagonal core -->
            <polygon points="32,6 54,18 54,46 32,58 10,46 10,18" fill="url(#cdgIconGlow' . uniqid() . ')" opacity="0.95"/>
            <!-- Inner mesh lines -->
            <g stroke="url(#cdgIconStroke' . uniqid() . ')" stroke-width="0.8" fill="none" opacity="0.85">
                <line x1="32" y1="6"  x2="32" y2="32"/>
                <line x1="32" y1="32" x2="32" y2="58"/>
                <line x1="10" y1="18" x2="32" y2="32"/>
                <line x1="54" y1="18" x2="32" y2="32"/>
                <line x1="10" y1="46" x2="32" y2="32"/>
                <line x1="54" y1="46" x2="32" y2="32"/>
                <line x1="10" y1="18" x2="54" y2="18"/>
                <line x1="10" y1="46" x2="54" y2="46"/>
                <line x1="22" y1="22" x2="42" y2="22"/>
                <line x1="22" y1="42" x2="42" y2="42"/>
                <line x1="22" y1="22" x2="22" y2="42"/>
                <line x1="42" y1="22" x2="42" y2="42"/>
            </g>
            <!-- Vertex nodes -->
            <g fill="#00E5FF">
                <circle cx="32" cy="6"  r="2.2"/>
                <circle cx="54" cy="18" r="2.2"/>
                <circle cx="54" cy="46" r="2.2"/>
                <circle cx="32" cy="58" r="2.2"/>
                <circle cx="10" cy="46" r="2.2"/>
                <circle cx="10" cy="18" r="2.2"/>
                <circle cx="22" cy="22" r="1.6"/>
                <circle cx="42" cy="22" r="1.6"/>
                <circle cx="22" cy="42" r="1.6"/>
                <circle cx="42" cy="42" r="1.6"/>
                <circle cx="32" cy="32" r="2.4" fill="#fff"/>
            </g>
            <!-- Outer glow nodes -->
            <g fill="#00E5FF" opacity="0.65">
                <circle cx="32" cy="2"  r="1.4"/>
                <circle cx="58" cy="14" r="1.4"/>
                <circle cx="58" cy="50" r="1.4"/>
                <circle cx="32" cy="62" r="1.4"/>
                <circle cx="6"  cy="50" r="1.4"/>
                <circle cx="6"  cy="14" r="1.4"/>
            </g>
        </svg>';

        if($variant === 'icon' || $variant === 'mark') {
            $h = (int)$size;
            return '<span class="cdg-brand-mark ' . htmlspecialchars($extra, ENT_QUOTES) . '" style="display:inline-flex;align-items:center;justify-content:center;width:' . $h . 'px;height:' . $h . 'px;flex-shrink:0;">' . $iconSvg . '</span>';
        }

        // FULL (icon + wordmark)
        $color_word = $variant === 'white' ? '#FFFFFF' : '#2E3B4E';
        $color_sub  = $variant === 'white' ? 'rgba(255,255,255,0.70)' : '#64748B';
        $sub = $variant === 'mono' ? '' : '<span class="cdg-brand-sub" style="display:block;font-size:' . ($size * 0.22) . 'px;font-weight:500;color:' . $color_sub . ';letter-spacing:0.5px;line-height:1;margin-top:2px;">Software &amp; Hosting</span>';

        $h = (int)$size;
        return '<span class="cdg-brand-logo ' . htmlspecialchars($extra, ENT_QUOTES) . '" style="display:inline-flex;align-items:center;gap:' . ($size * 0.22) . 'px;height:' . $h . 'px;flex-shrink:0;line-height:1;">'
            . '<span class="cdg-brand-mark" style="display:inline-flex;align-items:center;justify-content:center;width:' . $h . 'px;height:' . $h . 'px;flex-shrink:0;">' . $iconSvg . '</span>'
            . '<span class="cdg-brand-text" style="display:inline-flex;flex-direction:column;justify-content:center;line-height:1;">'
            . '<span class="cdg-brand-name" style="display:block;font-family:\'Plus Jakarta Sans\',sans-serif;font-size:' . ($size * 0.55) . 'px;font-weight:800;color:' . $color_word . ';letter-spacing:-0.01em;line-height:1;">CODEGA</span>'
            . $sub
            . '</span>'
            . '</span>';
    }
}
?>

<style>
/* === CODEGA KURUMSAL KİMLİK - CSS DEĞİŞKENLERİ === */
:root {
    /* Brand Colors (logodan) */
    --cdg-brand-primary:       #2E3B4E;       /* Lacivert kurumsal */
    --cdg-brand-primary-deep:  #1A2332;       /* Daha koyu */
    --cdg-brand-primary-light: #485A75;       /* Daha açık */
    --cdg-brand-accent:        #00D3E5;       /* Cyan mid (vurgu) */
    --cdg-brand-accent-bright: #00E5FF;       /* Cyan parlak (glow) */
    --cdg-brand-silver:        #B0B3B6;       /* Gümüş gri */

    /* === MAIN SYSTEM OVERRIDE - eski wisecp.php #2E3B4E tonlarını yeni paletle değiştir === */
    --cdg-primary:             #2E3B4E !important;
    --cdg-primary-rgb:         46, 59, 78 !important;
    --cdg-secondary:           #00D3E5 !important;
    --cdg-secondary-rgb:       0, 211, 229 !important;
    --cdg-accent:              #00D3E5 !important;
    --cdg-info:                #00D3E5 !important;
    --cdg-gradient:            linear-gradient(135deg, #2E3B4E 0%, #00D3E5 100%) !important;
    --cdg-shadow-primary:      0 8px 24px rgba(0, 211, 229, 0.18) !important;

    --cdg-primary-deep:        #1A2332;
    --cdg-primary-light:       #485A75;
    --cdg-accent-light:        #00E5FF;

    /* Surface */
    --cdg-bg:                  #FFFFFF;
    --cdg-bg-alt:              #F8FAFC;
    --cdg-card:                #FFFFFF;
    --cdg-border:              #E2E8F0;
    --cdg-border-strong:       #CBD5E1;

    /* Text */
    --cdg-text:                #0F172A;
    --cdg-text-soft:           #334155;
    --cdg-muted:               #64748B;
    --cdg-muted-light:         #94A3B8;

    /* Status */
    --cdg-success:             #10B981;
    --cdg-success-soft:        #DCFCE7;
    --cdg-warning:             #F59E0B;
    --cdg-warning-soft:        #FEF3C7;
    --cdg-danger:              #EF4444;
    --cdg-danger-soft:         #FEE2E2;
    --cdg-info-soft:           #CFFAFE;

    /* Gradients */
    --cdg-gradient-primary:    linear-gradient(135deg, #2E3B4E 0%, #1A2332 100%);
    --cdg-gradient-accent:     linear-gradient(135deg, #00E5FF 0%, #00D3E5 100%);
    --cdg-gradient-hero:       linear-gradient(135deg, #1A2332 0%, #2E3B4E 35%, #1A4F5C 75%, #00D3E5 100%);
    --cdg-gradient-mesh:       radial-gradient(circle at 20% 20%, rgba(0,229,255,0.15) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(0,211,229,0.12) 0%, transparent 50%);

    /* Shadows */
    --cdg-shadow-xs:           0 1px 2px rgba(15,23,42,0.05);
    --cdg-shadow-sm:           0 2px 6px rgba(15,23,42,0.06);
    --cdg-shadow:              0 4px 14px rgba(15,23,42,0.08);
    --cdg-shadow-lg:           0 12px 32px rgba(15,23,42,0.12);
    --cdg-shadow-xl:           0 20px 48px rgba(15,23,42,0.16);
    --cdg-shadow-glow:         0 8px 32px rgba(0,211,229,0.25);

    /* Radius */
    --cdg-radius-sm:           8px;
    --cdg-radius:              12px;
    --cdg-radius-lg:           16px;
    --cdg-radius-xl:           24px;

    /* Typography */
    --cdg-font:                'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --cdg-font-mono:           'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
}

/* === LOGO COMPONENT === */
.cdg-brand-logo,
.cdg-brand-mark {
    user-select: none;
    transition: transform 0.2s ease;
}
.cdg-brand-logo:hover,
.cdg-brand-mark:hover {
    transform: scale(1.02);
}
.cdg-brand-icon-svg {
    width: 100%; height: 100%;
    filter: drop-shadow(0 4px 12px rgba(0,211,229,0.30));
    transition: filter 0.3s ease;
}
.cdg-brand-mark:hover .cdg-brand-icon-svg,
.cdg-brand-logo:hover .cdg-brand-icon-svg {
    filter: drop-shadow(0 6px 18px rgba(0,229,255,0.50));
}
.cdg-brand-name {
    background: linear-gradient(135deg, #2E3B4E 0%, #1A2332 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.cdg-brand-logo[data-theme="white"] .cdg-brand-name,
.cdg-brand-logo.cdg-brand-white .cdg-brand-name {
    background: linear-gradient(135deg, #FFFFFF 0%, #E0F7FA 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* === KURUMSAL TYPE === */
body { font-family: var(--cdg-font); }

/* === KURUMSAL UTILITY === */
.cdg-text-brand { color: var(--cdg-brand-primary) !important; }
.cdg-text-accent { color: var(--cdg-brand-accent) !important; }
.cdg-bg-brand { background: var(--cdg-brand-primary) !important; }
.cdg-bg-accent { background: var(--cdg-brand-accent) !important; }
.cdg-gradient-brand-text {
    background: var(--cdg-gradient-accent);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* === LEGACY MAVİ → YENİ KURUMSAL OVERRIDE ===
   Eski sayfalardaki sabit mavi (#2E3B4E, #485A75) tonları otomatik dönüşür */
:where([style*="#2E3B4E"], [style*="#2E3B4E"]) { /* override sadece pure-blue için */ }
</style>
