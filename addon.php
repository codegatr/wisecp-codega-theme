<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Site Genel Eklenti / Modül Container
 * Site üzerinde modül sayfaları gösterilirken (account dışı) bu container kullanılır
 *
 * WiseCP runtime: $module_content, $header_title
 */

$hoptions = ['page' => 'addon'];

$header_title = $header_title ?? 'Sayfa';
$module_content = $module_content ?? '';
?>

<div class="cdg-addon-page">
    <div class="cdg-addon-page-wrap">
        <?php if($header_title): ?>
        <header class="cdg-addon-page-header">
            <h1><?php echo htmlspecialchars($header_title); ?></h1>
        </header>
        <?php endif; ?>

        <div class="cdg-addon-page-body">
            <?php echo $module_content; /* Modül kendi içeriğini renderler */ ?>
        </div>
    </div>
</div>

<style>
.cdg-addon-page {
    background: #f8fafc;
    min-height: 60vh;
    padding: 32px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-addon-page *, .cdg-addon-page *::before, .cdg-addon-page *::after { box-sizing: border-box; }
.cdg-addon-page-wrap {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
}
.cdg-addon-page-header {
    margin-bottom: 22px;
    padding-bottom: 18px;
    border-bottom: 2px solid #e2e8f0;
}
.cdg-addon-page-header h1 {
    font-size: 28px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.5px;
}
.cdg-addon-page-body {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    line-height: 1.6;
}
@media (max-width: 600px) {
    .cdg-addon-page-body { padding: 22px 18px; }
    .cdg-addon-page-header h1 { font-size: 22px; }
}
</style>
