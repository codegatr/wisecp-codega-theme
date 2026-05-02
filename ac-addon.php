<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Eklenti / Addon Detay
 * WiseCP runtime: $header_title, $module_content
 */
$hoptions = ["datatables", "iziModal"];

$header_title = $header_title ?? 'Eklenti';
$module_content = $module_content ?? '';
?>

<style>
.cdg-addon {
    --a-primary: #1e40af;
    --a-bg: #f8fafc;
    --a-card: #fff;
    --a-text: #0f172a;
    --a-muted: #64748b;
    --a-border: #e2e8f0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--a-text);
    background: var(--a-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-addon *, .cdg-addon *::before, .cdg-addon *::after { box-sizing: border-box; }
.cdg-addon-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-addon-hero {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    border-radius: 18px;
    padding: 26px 30px;
    color: #fff;
    margin-bottom: 22px;
    display: flex; align-items: center; gap: 18px;
    box-shadow: 0 16px 40px rgba(245,158,11,0.22);
}
.cdg-addon-hero-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 26px;
    flex-shrink: 0;
}
.cdg-addon-hero h1 { font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.4px; }

.cdg-addon-content {
    background: var(--a-card);
    border: 1px solid var(--a-border);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
}
.cdg-addon-content > div,
.cdg-addon-content > section { line-height: 1.6; }

@media (max-width: 600px) {
    .cdg-addon-hero { flex-direction: column; text-align: center; padding: 22px 20px; }
}
</style>

<div class="cdg-addon">
<div class="cdg-addon-wrap">

    <section class="cdg-addon-hero">
        <div class="cdg-addon-hero-icon"><i class="bi bi-star-fill"></i></div>
        <h1><?php echo htmlspecialchars($header_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
    </section>

    <div class="cdg-addon-content">
        <?php echo $module_content; /* Modül kendi içeriğini renderler */ ?>
    </div>

</div>
</div>
