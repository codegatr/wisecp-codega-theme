<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega Müşteri Paneli Footer (clientArea_type=1)
 *
 * Panel UX'ine uygun KOMPAKT footer.
 * Ana sayfa footer'ı (main-footer.php) panel için fazla büyük (4 sütun + 200px).
 * Burada sadece copyright + version + AKSOY GROUP rozeti gösterilir.
 */

// Theme version bilgisini oku
$cdg_theme_version = '';
$cdg_theme_date = '';
$cdg_theme_config_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'theme-config.php';
if(file_exists($cdg_theme_config_file)) {
    $cdg_theme_config = @include $cdg_theme_config_file;
    if(is_array($cdg_theme_config)) {
        $cdg_theme_version = $cdg_theme_config['version'] ?? '';
    }
}
$cdg_version_json = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'version.json';
if(file_exists($cdg_version_json)) {
    $vj = @json_decode(file_get_contents($cdg_version_json), true);
    if(is_array($vj)) {
        $cdg_theme_date = $vj['release_date'] ?? '';
    }
}
$cdg_year = date('Y');
?>
<style>
.cdg-ac-footer {
    flex-shrink: 0;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: #64748b;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
.cdg-ac-footer a { color: #475569; text-decoration: none; transition: color 0.15s ease; }
.cdg-ac-footer a:hover { color: #2E3B4E; }

.cdg-ac-footer-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cdg-ac-footer-left strong {
    color: #2E3B4E;
    font-weight: 700;
}
.cdg-ac-footer-version {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.3px;
}
.cdg-ac-footer-meta {
    color: #94a3b8;
    font-size: 11px;
}

.cdg-ac-footer-center {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}
.cdg-ac-footer-center a {
    font-size: 12px;
    font-weight: 600;
}

.cdg-ac-footer-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cdg-ac-footer-aksoy {
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.15s ease;
    text-decoration: none;
}
.cdg-ac-footer-aksoy:hover {
    background: #e0e7ff;
    border-color: #c7d2fe;
    transform: translateY(-1px);
}
.cdg-ac-footer-aksoy strong {
    font-size: 11px;
    font-weight: 800;
    color: #2E3B4E;
    letter-spacing: 0.4px;
}
.cdg-ac-footer-aksoy span {
    font-size: 10px;
    color: #94a3b8;
    font-weight: 500;
}

@media (max-width: 768px) {
    .cdg-ac-footer {
        flex-direction: column;
        text-align: center;
        padding: 12px 16px;
    }
    .cdg-ac-footer-left,
    .cdg-ac-footer-center,
    .cdg-ac-footer-right {
        justify-content: center;
        width: 100%;
    }
}
</style>

<footer class="cdg-ac-footer">
    <div class="cdg-ac-footer-left">
        <span>&copy; <?php echo $cdg_year; ?> <strong>CODEGA</strong></span>
        <?php if($cdg_theme_version): ?>
        <span class="cdg-ac-footer-version"><i class="bi bi-tag-fill" style="font-size:9px;"></i> v<?php echo htmlspecialchars($cdg_theme_version, ENT_QUOTES); ?></span>
        <?php endif; ?>
        <?php if($cdg_theme_date): ?>
        <span class="cdg-ac-footer-meta">&middot; <?php echo htmlspecialchars(date('d.m.Y', strtotime($cdg_theme_date)), ENT_QUOTES); ?></span>
        <?php endif; ?>
    </div>

    <div class="cdg-ac-footer-center">
        <a href="/kvkk-aydinlatma-metni.html" target="_blank">KVKK</a>
        <a href="/gizlilik-politikasi.html" target="_blank">Gizlilik</a>
        <a href="/cerez-politikasi.html" target="_blank">Çerez</a>
        <a href="/hizmet-sozlesmesi.html" target="_blank">Hizmet Sözleşmesi</a>
        <a href="/sistem-durumu.html" target="_blank"><i class="bi bi-activity"></i> Sistem Durumu</a>
    </div>

    <div class="cdg-ac-footer-right">
        <a href="https://aksoy.web.tr" target="_blank" rel="noopener" class="cdg-ac-footer-aksoy">
            <strong>AKSOY GROUP</strong><span>iştirakidir</span>
        </a>
        <?php if(class_exists('View') && method_exists('View', 'show_brand')) View::show_brand(); ?>
    </div>
</footer>
