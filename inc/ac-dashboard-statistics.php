<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Dashboard İstatistik Kartları
 * 4 kart: Hizmetlerim, Domainlerim, Bekleyen Faturalar, Destek Talepleri
 * WiseCP runtime: $statistic1-4, $acsidebar_links, $pg_activation, $visibility_invoice,
 *                 $visibility_ticket, $udata, $links
 */

$acsidebar_links = isset($acsidebar_links) && is_array($acsidebar_links) ? $acsidebar_links : [];
$pg_activation = isset($pg_activation) && is_array($pg_activation) ? $pg_activation : [];
$udata = isset($udata) && is_array($udata) ? $udata : [];
$links = isset($links) && is_array($links) ? $links : [];

$visibility_invoice = !empty($visibility_invoice);
$visibility_ticket = !empty($visibility_ticket);
$show_domain = !empty($pg_activation['domain']);
$can_create_ticket = !(isset($udata['block_create_ticket']) && $udata['block_create_ticket']);

$st1 = (int)($statistic1 ?? 0);  // Hizmetler
$st2 = (int)($statistic2 ?? 0);  // Domainler
$st3 = (int)($statistic3 ?? 0);  // Bekleyen faturalar
$st4 = (int)($statistic4 ?? 0);  // Destek talepleri

$url_services = $acsidebar_links['menu-2'] ?? '#';
$url_domains  = $acsidebar_links['menu-3'] ?? '#';
$url_invoices = $acsidebar_links['menu-4'] ?? '#';
$url_tickets  = $acsidebar_links['menu-1'] ?? '#';
$url_create_ticket = $links['create-ticket-request'] ?? '#';
?>

<style>
.cdg-ds {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 22px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-ds *, .cdg-ds *::before, .cdg-ds *::after { box-sizing: border-box; }
.cdg-ds-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    transition: all 0.22s;
    text-decoration: none;
    color: #0f172a;
    display: block;
    position: relative;
    overflow: hidden;
}
.cdg-ds-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(15,23,42,0.10);
    color: #0f172a;
    text-decoration: none;
}
.cdg-ds-card::before {
    content: '';
    position: absolute;
    top: -30%; right: -20%;
    width: 200px; height: 200px;
    border-radius: 50%;
    opacity: 0.06;
    pointer-events: none;
}
.cdg-ds-card-services::before { background: #10b981; }
.cdg-ds-card-domains::before  { background: #1e40af; }
.cdg-ds-card-invoices::before { background: #ef4444; }
.cdg-ds-card-tickets::before  { background: #64748b; }

.cdg-ds-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: grid; place-items: center;
    color: #fff;
    font-size: 22px;
    margin-bottom: 14px;
    position: relative;
    z-index: 1;
}
.cdg-ds-card-services .cdg-ds-icon { background: linear-gradient(135deg, #10b981, #34d399); }
.cdg-ds-card-domains .cdg-ds-icon  { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.cdg-ds-card-invoices .cdg-ds-icon { background: linear-gradient(135deg, #ef4444, #f87171); }
.cdg-ds-card-tickets .cdg-ds-icon  { background: linear-gradient(135deg, #64748b, #94a3b8); }

.cdg-ds-num {
    font-size: 36px;
    font-weight: 900;
    line-height: 1;
    color: #0f172a;
    margin-bottom: 4px;
    letter-spacing: -1px;
    position: relative; z-index: 1;
}
.cdg-ds-label {
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative; z-index: 1;
}
.cdg-ds-action {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    transition: all 0.18s;
    text-decoration: none;
    position: relative; z-index: 1;
}
.cdg-ds-card-services .cdg-ds-action { background: linear-gradient(135deg, #10b981, #34d399); }
.cdg-ds-card-domains .cdg-ds-action  { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.cdg-ds-card-invoices .cdg-ds-action { background: linear-gradient(135deg, #ef4444, #f87171); }
.cdg-ds-card-tickets .cdg-ds-action  { background: linear-gradient(135deg, #64748b, #94a3b8); }
.cdg-ds-card:hover .cdg-ds-action { transform: translateX(2px); }

.cdg-ds-secondary {
    margin-left: 8px;
    color: #1e40af;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    position: relative; z-index: 1;
}
.cdg-ds-secondary:hover { text-decoration: underline; }
</style>

<div class="cdg-ds">

    <a href="<?php echo htmlspecialchars($url_services); ?>" class="cdg-ds-card cdg-ds-card-services">
        <div class="cdg-ds-icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
        <div class="cdg-ds-num"><?php echo $st1; ?></div>
        <div class="cdg-ds-label">Aktif Hizmetlerim</div>
        <span class="cdg-ds-action"><i class="bi bi-arrow-right"></i> Hizmetlere Git</span>
    </a>

    <?php if($show_domain): ?>
    <a href="<?php echo htmlspecialchars($url_domains); ?>" class="cdg-ds-card cdg-ds-card-domains">
        <div class="cdg-ds-icon"><i class="bi bi-globe2"></i></div>
        <div class="cdg-ds-num"><?php echo $st2; ?></div>
        <div class="cdg-ds-label">Domainlerim</div>
        <span class="cdg-ds-action"><i class="bi bi-arrow-right"></i> Domainlere Git</span>
    </a>
    <?php endif; ?>

    <?php if($visibility_invoice): ?>
    <a href="<?php echo htmlspecialchars($url_invoices); ?>" class="cdg-ds-card cdg-ds-card-invoices">
        <div class="cdg-ds-icon"><i class="bi bi-receipt"></i></div>
        <div class="cdg-ds-num"><?php echo $st3; ?></div>
        <div class="cdg-ds-label">Bekleyen Faturalar</div>
        <span class="cdg-ds-action"><i class="bi bi-arrow-right"></i> Faturaları Görüntüle</span>
    </a>
    <?php endif; ?>

    <?php if($visibility_ticket): ?>
    <div class="cdg-ds-card cdg-ds-card-tickets" style="cursor:default;">
        <div class="cdg-ds-icon"><i class="bi bi-headset"></i></div>
        <a href="<?php echo htmlspecialchars($url_tickets); ?>" style="text-decoration:none;color:inherit;display:block;">
            <div class="cdg-ds-num"><?php echo $st4; ?></div>
            <div class="cdg-ds-label">Destek Taleplerim</div>
        </a>
        <?php if($can_create_ticket): ?>
        <a href="<?php echo htmlspecialchars($url_create_ticket); ?>" class="cdg-ds-action">
            <i class="bi bi-plus-circle"></i> Yeni Talep Aç
        </a>
        <?php else: ?>
        <a href="<?php echo htmlspecialchars($url_tickets); ?>" class="cdg-ds-action">
            <i class="bi bi-arrow-right"></i> Taleplerimi Gör
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
