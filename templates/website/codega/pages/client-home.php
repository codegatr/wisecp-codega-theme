<?php
/**
 * CODEGA Theme - Client Area Home (Dashboard)
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = "Müşteri Paneli — CODEGA";
$description = "CODEGA müşteri paneli";

$user = $_SESSION['user'] ?? [];
$user_name = trim(($user['name'] ?? '') . ' ' . ($user['surname'] ?? ''));
if (!$user_name) $user_name = 'Müşteri';

// Counts (best effort - WiseCP variables may differ)
$active_services = $vars['active_services_count'] ?? ($vars['services_active'] ?? 0);
$unpaid_invoices = $vars['unpaid_invoices_count'] ?? ($vars['invoices_unpaid'] ?? 0);
$open_tickets    = $vars['open_tickets_count']    ?? ($vars['tickets_open'] ?? 0);
$domains_count   = $vars['domains_count']         ?? ($vars['domains_total'] ?? 0);

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'meta.php';
?>
<body>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'header.php'; ?>

<div class="cg-client">

    <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'client-sidebar.php'; ?>

    <main class="cg-content">

        <div class="cg-page-header">
            <div>
                <span class="cg-eyebrow">Müşteri Paneli</span>
                <h1 class="cg-mt-1"><?= htmlspecialchars($user_name) ?>, <em class="cg-display">hoş geldiniz</em>.</h1>
                <p>Hesabınızla ilgili özet bilgiler aşağıdadır.</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="/store/hosting" class="cg-btn cg-btn-ghost cg-btn-sm">+ Hizmet Ekle</a>
                <a href="/clientarea/tickets/new" class="cg-btn cg-btn-primary cg-btn-sm">Destek Talebi</a>
            </div>
        </div>

        <?php if ($unpaid_invoices > 0): ?>
        <div class="cg-alert cg-alert-warning cg-mb-3">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div>
                <strong><?= $unpaid_invoices ?> ödenmemiş faturanız var.</strong>
                Hizmetlerinizin kesintisiz devam etmesi için lütfen <a href="/clientarea/invoices" style="font-weight:600;">faturalarınızı kontrol edin</a>.
            </div>
        </div>
        <?php endif; ?>

        <!-- Stat cards -->
        <div class="cg-grid cg-grid-4 cg-mb-4">

            <div class="cg-stat-card">
                <div class="cg-stat-label">Aktif Hizmetler</div>
                <div class="cg-stat-value"><?= (int)$active_services ?></div>
                <div class="cg-stat-meta">
                    <a href="/clientarea/services" style="color:var(--cg-gold-deep); font-weight:600;">Tümünü gör →</a>
                </div>
            </div>

            <div class="cg-stat-card">
                <div class="cg-stat-label">Ödenmemiş Faturalar</div>
                <div class="cg-stat-value" style="<?= $unpaid_invoices > 0 ? 'color:var(--cg-danger);' : '' ?>"><?= (int)$unpaid_invoices ?></div>
                <div class="cg-stat-meta">
                    <a href="/clientarea/invoices" style="color:var(--cg-gold-deep); font-weight:600;">Faturalar →</a>
                </div>
            </div>

            <div class="cg-stat-card">
                <div class="cg-stat-label">Açık Destek Talepleri</div>
                <div class="cg-stat-value"><?= (int)$open_tickets ?></div>
                <div class="cg-stat-meta">
                    <a href="/clientarea/tickets" style="color:var(--cg-gold-deep); font-weight:600;">Talepler →</a>
                </div>
            </div>

            <div class="cg-stat-card">
                <div class="cg-stat-label">Alan Adlarım</div>
                <div class="cg-stat-value"><?= (int)$domains_count ?></div>
                <div class="cg-stat-meta">
                    <a href="/clientarea/domains" style="color:var(--cg-gold-deep); font-weight:600;">Domainler →</a>
                </div>
            </div>

        </div>

        <!-- Quick actions -->
        <div class="cg-grid cg-grid-2">

            <div class="cg-card" style="background:linear-gradient(135deg, var(--cg-cream) 0%, white 100%); border-color:var(--cg-gold-soft);">
                <div class="cg-card-icon" style="background:white;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </div>
                <h3>Yeni alan adı tescili</h3>
                <p class="cg-text-muted cg-mt-2 cg-mb-3">.com, .com.tr, .net, .org ve 30+ uzantı için anında sorgulayın ve tescil edin.</p>
                <a href="/store/domain" class="cg-btn cg-btn-dark cg-btn-sm">Domain Sorgula</a>
            </div>

            <div class="cg-card">
                <div class="cg-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <h3>Hızlı destek</h3>
                <p class="cg-text-muted cg-mt-2 cg-mb-3">Teknik destek ekibimiz 7/24 ortalama 15 dakika içinde size yanıt veriyor.</p>
                <a href="/clientarea/tickets/new" class="cg-btn cg-btn-ghost cg-btn-sm">Yeni Talep Aç</a>
            </div>

        </div>

    </main>
</div>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
