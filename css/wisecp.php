<?php
/**
 * Codega Theme Dynamic CSS
 *
 * Bu dosya iki şekilde çağrılabilir:
 * 1. WiseCP üzerinden: theme.php router'ı tarafından (CORE_FOLDER tanımlı)
 * 2. Direkt URL: .htaccess ile wisecp.css->wisecp.php rewrite (standalone mod)
 *
 * Standalone modda theme-config.php'den varsayılan renkleri okur.
 */

// Standalone mod - WiseCP class'ları yok
$is_standalone = !defined('CORE_FOLDER');

if($is_standalone) {
    // Theme config'ten renkleri oku
    $theme_config_file = __DIR__ . '/../theme-config.php';
    $color1 = '1e40af';
    $color2 = '3b82f6';
    $tcolor = '1e293b';

    if(file_exists($theme_config_file)) {
        $theme_config = include $theme_config_file;
        if(isset($theme_config['settings'])) {
            $s = $theme_config['settings'];
            $color1 = ltrim($s['color1'] ?? $color1, '#');
            $color2 = ltrim($s['color2'] ?? $color2, '#');
            $tcolor = ltrim($s['text_color'] ?? $tcolor, '#');
        }
    }

    $color1 = '#' . $color1;
    $color2 = '#' . $color2;
    $tcolor = '#' . $tcolor;

    // CSS header
    header("Content-Type: text/css; charset=UTF-8");
    header('Cache-Control: public, max-age=3600');
} else {
    // WiseCP üzerinden çalışıyor
    $color1 = "#" . ltrim(Config::get("theme/color1"), "#");
    $color2 = "#" . ltrim(Config::get("theme/color2"), "#");
    $tcolor = "#" . ltrim(Config::get("theme/text-color"), "#");
}

// HEX → rgb dönüştürücü (rgba kullanımı için)
function cdg_hex2rgb($hex) {
    $hex = ltrim($hex, '#');
    if(strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    if(strlen($hex) !== 6) return '30, 64, 175';
    return hexdec(substr($hex,0,2)) . ', ' . hexdec(substr($hex,2,2)) . ', ' . hexdec(substr($hex,4,2));
}
$color1_rgb = cdg_hex2rgb($color1);
$color2_rgb = cdg_hex2rgb($color2);
?>
    --cdg-primary: <?php echo $color1; ?>;
    --cdg-primary-rgb: <?php echo $color1_rgb; ?>;
    --cdg-secondary: <?php echo $color2; ?>;
    --cdg-secondary-rgb: <?php echo $color2_rgb; ?>;
    --cdg-text: <?php echo $tcolor; ?>;
    --cdg-bg: #ffffff;
    --cdg-bg-alt: #f8fafc;
    --cdg-bg-dark: #0f172a;
    --cdg-bg-darker: #020617;
    --cdg-border: #e2e8f0;
    --cdg-border-dark: #cbd5e1;
    --cdg-muted: #64748b;
    --cdg-muted-light: #94a3b8;
    --cdg-success: #10b981;
    --cdg-warning: #f59e0b;
    --cdg-danger: #ef4444;
    --cdg-info: #00D3E5;
    --cdg-accent: #00D3E5;
    --cdg-radius: 10px;
    --cdg-radius-sm: 6px;
    --cdg-radius-lg: 16px;
    --cdg-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
    --cdg-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    --cdg-shadow-lg: 0 12px 32px rgba(15, 23, 42, 0.08);
    --cdg-shadow-primary: 0 8px 24px rgba(<?php echo $color1_rgb; ?>, 0.18);
    --cdg-gradient: linear-gradient(135deg, <?php echo $color1; ?> 0%, <?php echo $color2; ?> 100%);
    --cdg-gradient-subtle: linear-gradient(135deg, rgba(<?php echo $color1_rgb; ?>, 0.06) 0%, rgba(<?php echo $color2_rgb; ?>, 0.04) 100%);
    --cdg-font: "Plus Jakarta Sans", "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
}
