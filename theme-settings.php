<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$settings = isset($theme["config"]["settings"]) ? $theme["config"]["settings"] : [];
$banner   = $settings['banner'] ?? [];

// Header tipleri
$header_types = [
    1 => ['name' => 'Standart Menü',   'preview' => 'images/header-1.png'],
    2 => ['name' => 'Geniş / Kurumsal', 'preview' => 'images/header-2.png'],
];

// Müşteri panel tipleri
$client_area_types = [
    1 => ['name' => 'Sidebar Layout', 'preview' => 'images/sidebar-1.png'],
    2 => ['name' => 'Top Nav Layout', 'preview' => 'images/sidebar-2.png'],
];
?>

<div class="theme-settings-wrap">

    <h3 style="margin:0 0 18px;font-size:18px;font-weight:600;">Codega Tema Ayarları</h3>

    <!-- Renkler -->
    <div class="form-group">
        <label class="form-label"><strong>Birincil Renk (Primary)</strong></label>
        <input type="color" name="color1" value="#<?php echo $settings['color1'] ?? '1e40af'; ?>" class="form-control" style="height:42px;width:140px;">
        <small class="form-help">Ana vurgu rengi. Butonlar, başlıklar, linkler.</small>
    </div>

    <div class="form-group">
        <label class="form-label"><strong>İkincil Renk (Secondary)</strong></label>
        <input type="color" name="color2" value="#<?php echo $settings['color2'] ?? '3b82f6'; ?>" class="form-control" style="height:42px;width:140px;">
        <small class="form-help">Hover, gradyan ve aksanlar.</small>
    </div>

    <div class="form-group">
        <label class="form-label"><strong>Metin Rengi</strong></label>
        <input type="color" name="text_color" value="#<?php echo $settings['text_color'] ?? '1e293b'; ?>" class="form-control" style="height:42px;width:140px;">
        <small class="form-help">Genel metin rengi.</small>
    </div>

    <hr style="margin:28px 0;">

    <!-- Header tipi -->
    <div class="form-group">
        <label class="form-label"><strong>Header Stili</strong></label>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <?php foreach($header_types as $k => $v):
                $active = (int)($settings["header_type"] ?? 1) === $k;
            ?>
                <label style="cursor:pointer;border:2px solid <?php echo $active ? '#2E3B4E' : '#e2e8f0'; ?>;border-radius:8px;padding:12px;display:flex;align-items:center;gap:8px;">
                    <input type="radio" name="header_type" value="<?php echo $k; ?>"<?php echo $active ? ' checked' : ''; ?>>
                    <span><?php echo $v['name']; ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Müşteri Panel tipi -->
    <div class="form-group">
        <label class="form-label"><strong>Müşteri Paneli Stili</strong></label>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <?php foreach($client_area_types as $k => $v):
                $active = (int)($settings["clientArea_type"] ?? 1) === $k;
            ?>
                <label style="cursor:pointer;border:2px solid <?php echo $active ? '#2E3B4E' : '#e2e8f0'; ?>;border-radius:8px;padding:12px;display:flex;align-items:center;gap:8px;">
                    <input type="radio" name="clientArea_type" value="<?php echo $k; ?>"<?php echo $active ? ' checked' : ''; ?>>
                    <span><?php echo $v['name']; ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <hr style="margin:28px 0;">

    <!-- Banner / Hero -->
    <h4 style="margin:0 0 14px;font-size:16px;">Anasayfa Banner</h4>

    <div class="form-group">
        <label class="form-label"><strong>Başlık</strong></label>
        <input type="text" name="banner_heading" value="<?php echo htmlspecialchars($banner['heading'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="form-control" placeholder="Modern Yazılımla İşinizi Büyütün">
        <small class="form-help">HTML kullanılabilir. Satır atlama için &lt;br&gt; kullanın.</small>
    </div>

    <div class="form-group">
        <label class="form-label"><strong>Açıklama</strong></label>
        <textarea name="banner_content" class="form-control" rows="3"><?php echo htmlspecialchars($banner['content'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></textarea>
    </div>

    <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Birinci Buton Yazısı</label>
            <input type="text" name="banner_button_text1" value="<?php echo htmlspecialchars($banner['button_text1'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="form-control">
        </div>
        <div>
            <label class="form-label">Birinci Buton Linki</label>
            <input type="text" name="banner_button_link1" value="<?php echo htmlspecialchars($banner['button_link1'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="form-control">
        </div>
        <div>
            <label class="form-label">İkinci Buton Yazısı</label>
            <input type="text" name="banner_button_text2" value="<?php echo htmlspecialchars($banner['button_text2'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="form-control">
        </div>
        <div>
            <label class="form-label">İkinci Buton Linki</label>
            <input type="text" name="banner_button_link2" value="<?php echo htmlspecialchars($banner['button_link2'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="form-control">
        </div>
    </div>

    <hr style="margin:28px 0;">

    <!-- Bölüm açma/kapama -->
    <h4 style="margin:0 0 14px;font-size:16px;">Anasayfa Bölümleri</h4>

    <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="show_services" value="1"<?php echo ($settings['show_services'] ?? 1) ? ' checked' : ''; ?>>
            <span>Hizmet Kartları (Web Yazılım, Hosting, Domain, Sunucu, SMS, Özel Yazılım)</span>
        </label>
    </div>
    <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="show_features" value="1"<?php echo ($settings['show_features'] ?? 1) ? ' checked' : ''; ?>>
            <span>Avantajlar (Neden CODEGA?)</span>
        </label>
    </div>
    <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="show_pricing" value="1"<?php echo ($settings['show_pricing'] ?? 1) ? ' checked' : ''; ?>>
            <span>Hosting Paketleri Önizleme</span>
        </label>
    </div>
    <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="show_references" value="1"<?php echo ($settings['show_references'] ?? 1) ? ' checked' : ''; ?>>
            <span>Referans Logoları</span>
        </label>
    </div>
    <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="show_cta" value="1"<?php echo ($settings['show_cta'] ?? 1) ? ' checked' : ''; ?>>
            <span>Alt Çağrı Bandı (CTA)</span>
        </label>
    </div>

</div>

<style>
.theme-settings-wrap .form-group { margin-bottom: 18px; }
.theme-settings-wrap .form-label { display:block; margin-bottom:6px; font-weight:500; color:#1e293b; }
.theme-settings-wrap .form-control { width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:14px; }
.theme-settings-wrap .form-help { display:block; margin-top:4px; font-size:12px; color:#64748b; }
.theme-settings-wrap hr { border:none; border-top:1px solid #e2e8f0; }
</style>
