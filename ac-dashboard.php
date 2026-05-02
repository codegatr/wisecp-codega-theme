<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = ["datatables"];

// Sayım yardımcıları
$count_active_products = 0;
if(isset($orders) && is_array($orders)) $count_active_products += count($orders);
if(isset($domain_orders['active']) && is_array($domain_orders['active'])) $count_active_products += count($domain_orders['active']);

$count_open_tickets = 0;
if(isset($tickets) && is_array($tickets)) {
    foreach($tickets as $t) {
        if(isset($t['status']) && in_array($t['status'], ['open','answered','customer-reply','waiting'])) $count_open_tickets++;
    }
}

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

// Bakiye - WiseCP User::balance() veya benzeri
$user_balance_str = '0,00';
if(class_exists('User') && method_exists('User', 'logged_in') && User::logged_in() && isset(User::$init)) {
    if(isset(User::$init->info['balance'])) {
        $balance = User::$init->info['balance'];
        $bal_cid = isset(User::$init->info['balance_cid']) ? User::$init->info['balance_cid'] : 0;
        if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $bal_cid) {
            $user_balance_str = Money::formatter_symbol($balance, $bal_cid);
        } else {
            $user_balance_str = number_format((float)$balance, 2, ',', '.');
        }
    }
}
?>

<!-- Stat kartları -->
<div class="cdg-stat-grid">
    <div class="cdg-stat">
        <div class="label">Aktif Hizmetler</div>
        <div class="value"><?php echo $count_active_products; ?></div>
        <div class="meta"><a href="<?php echo cdg_link('ac-ps-products'); ?>">Tum hizmetler</a></div>
    </div>
    <div class="cdg-stat">
        <div class="label">Bekleyen Faturalar</div>
        <div class="value"><?php echo isset($statistic3) ? (int)$statistic3 : (isset($pending_invoices_count) ? (int)$pending_invoices_count : 0); ?></div>
        <div class="meta"><a href="<?php echo cdg_link('ac-ps-invoices'); ?>">Faturalarima git</a></div>
    </div>
    <div class="cdg-stat">
        <div class="label">Acik Talepler</div>
        <div class="value"><?php echo $count_open_tickets; ?></div>
        <div class="meta"><a href="<?php echo cdg_link('ac-ps-tickets'); ?>">Destek talepleri</a></div>
    </div>
    <div class="cdg-stat">
        <div class="label">Bakiyem</div>
        <div class="value" style="font-size:22px;"><?php echo $user_balance_str; ?></div>
        <div class="meta"><a href="<?php echo cdg_link('ac-ps-balance'); ?>">Yukleme yap</a></div>
    </div>
</div>

<div class="cdg-grid cdg-grid-2">

    <!-- Son Aktif Ürünlerim -->
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-box-seam"></i> Son Aktif Hizmetlerim</h3>
            <a href="<?php echo cdg_link('ac-ps-products'); ?>" style="font-size:13px;">Tumu &rarr;</a>
        </div>
        <?php if(isset($orders) && is_array($orders) && count($orders) > 0): ?>
            <div class="cdg-table-wrap">
                <table class="cdg-table">
                    <thead>
                        <tr>
                            <th>Hizmet</th>
                            <th style="text-align:right;">Tutar</th>
                            <th style="text-align:center;">Durum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $shown = 0;
                    foreach($orders as $row) {
                        if($shown >= 5) break;
                        $shown++;

                        $name = isset($row['name']) ? $row['name'] : '-';
                        $amount = '';
                        if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                            $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']);
                        }

                        $period = '';
                        if(class_exists('View') && method_exists('View', 'period') && isset($row['period_time']) && isset($row['period'])) {
                            $period = View::period($row['period_time'], $row['period']);
                        }

                        $status_html = '';
                        if(isset($product_situations) && isset($row['status']) && isset($product_situations[$row['status']])) {
                            $status_html = $product_situations[$row['status']];
                        }

                        // Sub-info (domain, hostname, ip, vs.)
                        $sub_info = '';
                        if(isset($row['options']['domain'])) $sub_info = $row['options']['domain'];
                        elseif(isset($row['options']['hostname'])) $sub_info = $row['options']['hostname'];
                        elseif(isset($row['options']['ip'])) $sub_info = $row['options']['ip'];
                        elseif(isset($row['options']['code'])) $sub_info = $row['options']['code'];
                    ?>
                        <tr>
                            <td>
                                <div style="font-weight:600;"><?php echo htmlspecialchars($name); ?></div>
                                <?php if($sub_info): ?>
                                    <div style="font-size:12px;color:var(--cdg-muted);"><?php echo htmlspecialchars($sub_info); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;font-weight:600;">
                                <?php echo $amount; ?>
                                <?php if($period): ?><span style="font-size:11px;color:var(--cdg-muted);font-weight:400;"><?php echo $period; ?></span><?php endif; ?>
                            </td>
                            <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                            <td style="text-align:right;">
                                <?php if(isset($row['detail_link']) && (!isset($row['status']) || !in_array($row['status'], ['waiting','inprocess','cancelled']))): ?>
                                    <a href="<?php echo $row['detail_link']; ?>" class="cdg-btn-icon" title="Yonet"><i class="bi bi-arrow-right"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="cdg-empty">
                <div class="icon"><i class="bi bi-box-seam"></i></div>
                <p>Henuz aktif hizmetiniz yok.</p>
                <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm mt-2">
                    <i class="bi bi-plus-lg"></i> Hizmet Al
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Son Destek Talepleri -->
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-headset"></i> Son Destek Talepleri</h3>
            <a href="<?php echo cdg_link('ac-ps-tickets'); ?>" style="font-size:13px;">Tumu &rarr;</a>
        </div>
        <?php if(isset($tickets) && is_array($tickets) && count($tickets) > 0): ?>
            <div class="cdg-table-wrap">
                <table class="cdg-table">
                    <thead>
                        <tr>
                            <th>Konu</th>
                            <th style="text-align:center;">Durum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $shown = 0;
                    foreach($tickets as $row) {
                        if($shown >= 5) break;
                        $shown++;
                        $title = isset($row['title']) ? $row['title'] : '-';
                        $bold = !isset($row['userunread']) || !$row['userunread'];

                        $status_html = '';
                        if(isset($ticket_situations) && isset($row['status']) && isset($ticket_situations[$row['status']])) {
                            $status_html = $ticket_situations[$row['status']];
                        }
                    ?>
                        <tr>
                            <td>
                                <?php if(isset($row['detail_link'])): ?>
                                    <a href="<?php echo $row['detail_link']; ?>" style="<?php echo $bold ? 'font-weight:600;' : ''; ?>color:var(--cdg-text);"><?php echo htmlspecialchars($title); ?></a>
                                <?php else: ?>
                                    <span style="<?php echo $bold ? 'font-weight:600;' : ''; ?>"><?php echo htmlspecialchars($title); ?></span>
                                <?php endif; ?>
                                <?php if(isset($row['service']) && $row['service']): ?>
                                    <div style="font-size:12px;color:var(--cdg-muted);"><?php echo htmlspecialchars($row['service']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                            <td style="text-align:right;">
                                <?php if(isset($row['detail_link'])): ?>
                                    <a href="<?php echo $row['detail_link']; ?>" class="cdg-btn-icon" title="Goruntule"><i class="bi bi-arrow-right"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="cdg-empty">
                <div class="icon"><i class="bi bi-headset"></i></div>
                <p>Henuz destek talebiniz yok.</p>
                <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm mt-2">
                    <i class="bi bi-plus-lg"></i> Talep Olustur
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php if(isset($domain_orders) && is_array($domain_orders) && !empty($domain_orders['all'])): ?>
<div class="cdg-card mt-3">
    <div class="cdg-card-head">
        <h3><i class="bi bi-globe2"></i> Domainlerim</h3>
        <a href="<?php echo cdg_link('ac-ps-products-t', ['domain']); ?>" style="font-size:13px;">Tumu &rarr;</a>
    </div>
    <div class="cdg-table-wrap">
        <table class="cdg-table">
            <thead>
                <tr>
                    <th>Alan Adi</th>
                    <th style="text-align:right;">Tutar</th>
                    <th style="text-align:center;">Bitis Tarihi</th>
                    <th style="text-align:center;">Durum</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $shown = 0;
            foreach($domain_orders['all'] as $row) {
                if($shown >= 5) break;
                $shown++;

                $name = isset($row['name']) ? $row['name'] : '-';
                $amount = '';
                if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                    $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']);
                }

                $duedate = isset($row['duedate']) ? $row['duedate'] : '';
                $duedate_format = '-';
                if($duedate && !in_array(substr($duedate,0,4), ['1881','1970'])) {
                    if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
                        $duedate_format = DateManager::format(Config::get("options/date-format"), $duedate);
                    } else {
                        $duedate_format = date('d.m.Y', strtotime($duedate));
                    }
                }

                $status_html = '';
                if(isset($product_situations) && isset($row['status']) && isset($product_situations[$row['status']])) {
                    $status_html = $product_situations[$row['status']];
                }
            ?>
                <tr>
                    <td>
                        <?php if(isset($row['detail_link']) && (!isset($row['status']) || !in_array($row['status'], ['waiting','inprocess','cancelled']))): ?>
                            <a href="<?php echo $row['detail_link']; ?>" style="font-weight:600;color:var(--cdg-primary);"><?php echo htmlspecialchars($name); ?></a>
                        <?php else: ?>
                            <span style="font-weight:600;"><?php echo htmlspecialchars($name); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right;font-weight:600;"><?php echo $amount; ?></td>
                    <td style="text-align:center;font-size:13px;"><?php echo $duedate_format; ?></td>
                    <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="cdg-grid cdg-grid-4 mt-3">
    <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-quickbtn">
        <i class="bi bi-hdd-network"></i>
        <span>Hosting Al</span>
    </a>
    <a href="<?php echo cdg_link('domain'); ?>" class="cdg-quickbtn">
        <i class="bi bi-globe2"></i>
        <span>Domain Al</span>
    </a>
    <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>" class="cdg-quickbtn">
        <i class="bi bi-headset"></i>
        <span>Destek Talebi</span>
    </a>
    <a href="<?php echo cdg_link('ac-ps-info'); ?>" class="cdg-quickbtn">
        <i class="bi bi-person"></i>
        <span>Hesabim</span>
    </a>
</div>
