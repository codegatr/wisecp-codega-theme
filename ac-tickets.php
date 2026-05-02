<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$items = isset($list) ? $list : (isset($tickets) ? $tickets : []);

$create_link = '';
if(isset($links['create-request']) && $links['create-request']) $create_link = $links['create-request'];
elseif(isset($links['create-ticket-request'])) $create_link = $links['create-ticket-request'];
else $create_link = cdg_link('ac-ps-create-ticket-request');

// Custom statüleri yükle
$custom_statuses = [];
if(class_exists('Helper') && method_exists('Helper', 'Load')) {
    @Helper::Load("Tickets");
    if(class_exists('Tickets') && method_exists('Tickets', 'custom_statuses')) {
        $custom_statuses = Tickets::custom_statuses();
    }
}

$ui_lang_local = isset($ui_lang) ? $ui_lang : 'tr';
?>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-headset"></i> Destek Talepleri</h3>
        <a href="<?php echo $create_link; ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">
            <i class="bi bi-plus-lg"></i> Yeni Talep
        </a>
    </div>

    <?php if(!empty($items)): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Konu</th>
                        <th style="text-align:center;">Durum</th>
                        <th style="text-align:right;">Islem</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($items as $row):
                    $tid    = isset($row['id']) ? $row['id'] : '-';
                    $title  = isset($row['title']) ? $row['title'] : '-';
                    $bold   = !isset($row['userunread']) || !$row['userunread'];

                    $situations = isset($situations) ? $situations : (isset($ticket_situations) ? $ticket_situations : []);
                    $status_str = isset($row['status']) && isset($situations[$row['status']]) ? $situations[$row['status']] : '';

                    if(isset($row['cstatus']) && $row['cstatus'] > 0 && isset($custom_statuses[$row['cstatus']])) {
                        $custom = $custom_statuses[$row['cstatus']];
                        if(isset($situations['custom'])) {
                            $status_str = str_replace(
                                ['{color}','{name}'],
                                [$custom['color'] ?? '#94a3b8', $custom['languages'][$ui_lang_local]['name'] ?? ($custom['name'] ?? '-')],
                                $situations['custom']
                            );
                        }
                    }
                ?>
                    <tr>
                        <td><span style="font-family:monospace;color:var(--cdg-muted);font-size:12px;">#<?php echo $tid; ?></span></td>
                        <td>
                            <?php if(isset($row['detail_link'])): ?>
                                <a href="<?php echo $row['detail_link']; ?>" style="<?php echo $bold ? 'font-weight:600;' : ''; ?>color:var(--cdg-text);"><?php echo htmlspecialchars($title); ?></a>
                            <?php else: ?>
                                <span style="<?php echo $bold ? 'font-weight:600;' : ''; ?>"><?php echo htmlspecialchars($title); ?></span>
                            <?php endif; ?>
                            <?php if(!empty($row['service'])): ?>
                                <div style="font-size:12px;color:var(--cdg-muted);"><i class="bi bi-link-45deg"></i> <?php echo htmlspecialchars($row['service']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;font-size:12px;"><?php echo $status_str; ?></td>
                        <td style="text-align:right;">
                            <?php if(isset($row['detail_link'])): ?>
                                <a href="<?php echo $row['detail_link']; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
                                    <i class="bi bi-eye"></i> Goruntule
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-emoji-smile"></i></div>
            <h3>Acik destek talebiniz yok</h3>
            <p>Bir sorununuz veya soru oldugunda destek ekibimize ulasabilirsiniz.</p>
            <a href="<?php echo $create_link; ?>" class="cdg-btn cdg-btn-primary mt-3">
                <i class="bi bi-plus-lg"></i> Yeni Talep Olustur
            </a>
        </div>
    <?php endif; ?>
</div>
