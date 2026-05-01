<?php
/**
 * CODEGA Theme - Client Invoices
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$title = "Faturalarım — CODEGA";
$description = "Faturalarınızı görüntüleyin ve ödeme yapın";

$invoices = $vars['invoices'] ?? ($invoices ?? []);

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
                <h1 class="cg-mt-1">Faturalarım</h1>
                <p>Tüm fatura geçmişiniz ve ödeme durumları.</p>
            </div>
        </div>

        <div class="cg-table-wrap">
            <?php if (empty($invoices)): ?>
                <div style="padding:64px 32px; text-align:center;">
                    <p class="cg-text-muted">Henüz fatura bulunmuyor.</p>
                </div>
            <?php else: ?>
                <table class="cg-table">
                    <thead>
                        <tr>
                            <th>Fatura No</th>
                            <th>Tarih</th>
                            <th>Vade</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($invoices as $inv): ?>
                        <?php
                        $iid    = $inv['id'] ?? 0;
                        $no     = $inv['invoice_no'] ?? ('#' . $iid);
                        $date   = $inv['created_at'] ?? '-';
                        $due    = $inv['due_date']   ?? '-';
                        $amount = $inv['total']      ?? 0;
                        $cur    = $inv['currency']   ?? 'TRY';
                        $status = strtolower($inv['status'] ?? 'unpaid');

                        $status_map = [
                            'paid'      => ['Ödendi',     'cg-pill-paid'],
                            'unpaid'    => ['Ödenmemiş',  'cg-pill-unpaid'],
                            'pending'   => ['Beklemede',  'cg-pill-pending'],
                            'cancelled' => ['İptal',      'cg-pill-cancelled'],
                            'refunded'  => ['İade',       'cg-pill-cancelled'],
                        ];
                        [$status_label, $status_class] = $status_map[$status] ?? [ucfirst($status), 'cg-pill-cancelled'];
                        ?>
                        <tr>
                            <td><strong class="cg-mono">#<?= htmlspecialchars((string)$iid) ?></strong></td>
                            <td><?= htmlspecialchars($date) ?></td>
                            <td><?= htmlspecialchars($due) ?></td>
                            <td><strong><?= htmlspecialchars(number_format((float)$amount, 2)) ?></strong> <?= htmlspecialchars($cur) ?></td>
                            <td><span class="cg-pill <?= $status_class ?>"><?= htmlspecialchars($status_label) ?></span></td>
                            <td style="text-align:right;">
                                <?php if ($status === 'unpaid'): ?>
                                    <a href="/clientarea/invoices/<?= (int)$iid ?>/pay" class="cg-btn cg-btn-primary cg-btn-sm">Öde</a>
                                <?php else: ?>
                                    <a href="/clientarea/invoices/<?= (int)$iid ?>" class="cg-btn cg-btn-ghost cg-btn-sm">Görüntüle</a>
                                <?php endif; ?>
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
