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

$items = isset($list) ? $list : (isset($invoices) ? $invoices : []);
?>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-receipt"></i> Faturalarim</h3>
        <?php if(isset($statistic3) && $statistic3 > 0): ?>
            <a href="<?php echo cdg_link('ac-ps-invoices-p', ['bulk-payment']); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">
                <i class="bi bi-credit-card"></i> Toplu Odeme (<?php echo (int)$statistic3; ?>)
            </a>
        <?php endif; ?>
    </div>

    <?php if(!empty($items)): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead>
                    <tr>
                        <th>Fatura No</th>
                        <th style="text-align:center;">Olusturma</th>
                        <th style="text-align:center;">Vade</th>
                        <th style="text-align:right;">Tutar</th>
                        <th style="text-align:center;">Durum</th>
                        <th style="text-align:right;">Islem</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($items as $row):
                    $iid = isset($row['id']) ? $row['id'] : (isset($row['num']) ? $row['num'] : '-');
                    $num = isset($row['num']) ? $row['num'] : $iid;

                    $amount = '';
                    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                        $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']);
                    }

                    $ctime = isset($row['ctime']) ? $row['ctime'] : (isset($row['creation_date']) ? $row['creation_date'] : '');
                    $duedate = isset($row['duedate']) ? $row['duedate'] : '';

                    $ctime_fmt = '';
                    $duedate_fmt = '';
                    if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
                        $df = Config::get("options/date-format");
                        if($ctime)   $ctime_fmt = DateManager::format($df, $ctime);
                        if($duedate && !in_array(substr($duedate,0,4), ['1881','1970'])) $duedate_fmt = DateManager::format($df, $duedate);
                    } else {
                        if($ctime)   $ctime_fmt = date('d.m.Y', strtotime($ctime));
                        if($duedate) $duedate_fmt = date('d.m.Y', strtotime($duedate));
                    }

                    $status_html = '';
                    if(isset($invoice_situations) && isset($row['status']) && isset($invoice_situations[$row['status']])) {
                        $status_html = $invoice_situations[$row['status']];
                    } elseif(isset($situations) && isset($row['status']) && isset($situations[$row['status']])) {
                        $status_html = $situations[$row['status']];
                    } else {
                        $status_html = '<span class="cdg-badge cdg-badge-' . (isset($row['status']) && $row['status']=='paid' ? 'success' : 'warning') . '">' . (isset($row['status']) ? $row['status'] : '-') . '</span>';
                    }
                ?>
                    <tr>
                        <td><span style="font-family:monospace;font-size:13px;">#<?php echo htmlspecialchars($num); ?></span></td>
                        <td style="text-align:center;font-size:13px;"><?php echo $ctime_fmt; ?></td>
                        <td style="text-align:center;font-size:13px;"><?php echo $duedate_fmt ?: '-'; ?></td>
                        <td style="text-align:right;font-weight:600;"><?php echo $amount; ?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
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
            <div class="icon"><i class="bi bi-receipt"></i></div>
            <h3>Fatura bulunmuyor</h3>
            <p>Henuz hicbir faturaniz olusturulmamis.</p>
        </div>
    <?php endif; ?>
</div>
