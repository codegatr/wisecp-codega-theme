<?php
/**
 * CODEGA Theme - Client Services
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = "Hizmetlerim — CODEGA";
$description = "Aktif hizmetlerinizi görüntüleyin ve yönetin";

$services = $vars['services'] ?? ($services ?? []);

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
                <h1 class="cg-mt-1">Hizmetlerim</h1>
                <p>Aktif hosting, sunucu ve diğer hizmetlerinizin listesi.</p>
            </div>
            <a href="/store/hosting" class="cg-btn cg-btn-primary cg-btn-sm">+ Yeni Hizmet</a>
        </div>

        <div class="cg-table-wrap">
            <?php if (empty($services)): ?>
                <div style="padding:64px 32px; text-align:center;">
                    <div style="width:64px; height:64px; background:var(--cg-cream); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:18px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--cg-gold-deep)" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/></svg>
                    </div>
                    <h3 class="cg-mb-2">Henüz aktif hizmetiniz yok</h3>
                    <p class="cg-text-muted cg-mb-3">İlk hosting paketinizi seçin ve dakikalar içinde sitenizi yayına alın.</p>
                    <a href="/store/hosting" class="cg-btn cg-btn-primary">Paketleri İncele</a>
                </div>
            <?php else: ?>
                <table class="cg-table">
                    <thead>
                        <tr>
                            <th>Ürün / Plan</th>
                            <th>Domain</th>
                            <th>Durum</th>
                            <th>Yenileme</th>
                            <th>Ücret</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($services as $svc): ?>
                        <?php
                        $name     = $svc['product_name'] ?? ($svc['name'] ?? 'Hizmet');
                        $domain   = $svc['domain']       ?? '-';
                        $status   = strtolower($svc['status'] ?? 'unknown');
                        $next_due = $svc['next_due_date'] ?? ($svc['due_date'] ?? '-');
                        $price    = $svc['recurring_amount'] ?? ($svc['total'] ?? 0);
                        $currency = $svc['currency'] ?? 'TRY';
                        $sid      = $svc['id']    ?? 0;

                        $status_map = [
                            'active'    => ['Aktif',     'cg-pill-active'],
                            'pending'   => ['Beklemede', 'cg-pill-pending'],
                            'cancelled' => ['İptal',     'cg-pill-cancelled'],
                            'suspended' => ['Askıya alındı', 'cg-pill-pending'],
                            'unpaid'    => ['Ödenmemiş', 'cg-pill-unpaid'],
                        ];
                        [$status_label, $status_class] = $status_map[$status] ?? [ucfirst($status), 'cg-pill-cancelled'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($name) ?></strong></td>
                            <td><span class="cg-mono"><?= htmlspecialchars($domain) ?></span></td>
                            <td><span class="cg-pill <?= $status_class ?>"><?= htmlspecialchars($status_label) ?></span></td>
                            <td><?= htmlspecialchars($next_due) ?></td>
                            <td><strong><?= htmlspecialchars(number_format((float)$price, 2)) ?></strong> <?= htmlspecialchars($currency) ?></td>
                            <td style="text-align:right;">
                                <a href="/clientarea/services/<?= (int)$sid ?>" class="cg-btn cg-btn-ghost cg-btn-sm">Yönet</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
