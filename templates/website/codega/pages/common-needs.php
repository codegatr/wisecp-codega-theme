<?php
/**
 * CODEGA Theme - Common Needs
 * 
 * Generic status display template used for confirmations,
 * order completed, invoice paid, ticket created, etc.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = ($vars['title'] ?? '') ?: 'Durum — CODEGA';
$description = $vars['message'] ?? '';

$type     = $vars['type']     ?? 'info';   // success | error | info | warning
$heading  = $vars['heading']  ?? ($vars['title'] ?? 'Bilgi');
$message  = $vars['message']  ?? '';
$btn_text = $vars['btn_text'] ?? 'Devam Et';
$btn_url  = $vars['btn_url']  ?? '/';
$details  = $vars['details']  ?? [];

$icon_map = [
    'success' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    'error'   => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    'info'    => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    'warning' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
];
$color_map = [
    'success' => 'var(--cg-success)',
    'error'   => 'var(--cg-danger)',
    'info'    => 'var(--cg-info)',
    'warning' => 'var(--cg-warning)',
];
$icon  = $icon_map[$type]  ?? $icon_map['info'];
$color = $color_map[$type] ?? $color_map['info'];

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';
?>
<body>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'header.php'; ?>

<section style="min-height:60vh; display:flex; align-items:center; justify-content:center; padding:80px 24px;">
    <div style="text-align:center; max-width:520px;">

        <div style="width:88px; height:88px; background:rgba(212,165,116,0.1); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; color:<?= $color ?>; margin-bottom:24px;">
            <?= $icon ?>
        </div>

        <h1 style="margin-bottom:14px;"><?= htmlspecialchars($heading) ?></h1>

        <?php if ($message): ?>
            <p class="cg-text-muted cg-mb-4" style="font-size:1rem; line-height:1.7;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($details) && is_array($details)): ?>
            <div class="cg-card" style="text-align:left; background:var(--cg-bg-soft); margin-bottom:24px;">
                <?php foreach ($details as $key => $val): ?>
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--cg-border-soft); font-size:14px;">
                        <span class="cg-text-muted"><?= htmlspecialchars($key) ?></span>
                        <strong><?= htmlspecialchars((string)$val) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="<?= htmlspecialchars($btn_url) ?>" class="cg-btn cg-btn-primary cg-btn-lg">
            <?= htmlspecialchars($btn_text) ?>
        </a>
    </div>
</section>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
