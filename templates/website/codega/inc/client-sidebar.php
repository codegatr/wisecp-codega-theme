<?php
/**
 * CODEGA Theme - Client Area Sidebar
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

if (class_exists('Filter') && method_exists('Filter', 'folder')) {
    $current = Filter::folder($params[1] ?? 'home');
} else {
    $current = preg_replace('/[^a-z0-9_-]/i', '', $params[1] ?? 'home');
}

$icon_home     = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
$icon_services = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>';
$icon_domains  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';
$icon_invoices = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>';
$icon_tickets  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
$icon_account  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>';
$icon_logout   = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>';

$items = [
    ['key' => 'home',     'href' => '/clientarea',          'label' => 'Panel',           'icon' => $icon_home],
    ['key' => 'services', 'href' => '/clientarea/services', 'label' => 'Hizmetlerim',     'icon' => $icon_services],
    ['key' => 'domains',  'href' => '/clientarea/domains',  'label' => 'Alan Adlarım',    'icon' => $icon_domains],
    ['key' => 'invoices', 'href' => '/clientarea/invoices', 'label' => 'Faturalarım',     'icon' => $icon_invoices],
    ['key' => 'tickets',  'href' => '/clientarea/tickets',  'label' => 'Destek',          'icon' => $icon_tickets],
    ['key' => 'account',  'href' => '/clientarea/account',  'label' => 'Hesap Ayarları',  'icon' => $icon_account],
];
?>
<aside class="cg-sidebar" id="cgSidebar">
    <div class="cg-sidebar-section">
        <div class="cg-sidebar-title">Müşteri Paneli</div>
        <?php foreach ($items as $item): ?>
            <a href="<?= htmlspecialchars($item['href']) ?>"
               class="cg-sidebar-link <?= $current === $item['key'] ? 'active' : '' ?>">
                <?= $item['icon'] ?>
                <?= htmlspecialchars($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="cg-sidebar-section" style="margin-top:auto; padding-top:24px; border-top:1px solid var(--cg-border-soft);">
        <a href="/logout" class="cg-sidebar-link" style="color:var(--cg-danger);">
            <?= $icon_logout ?>
            Güvenli Çıkış
        </a>
    </div>
</aside>
