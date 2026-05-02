<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // 1) Runtime $links[] kontrolü (WiseCP en güvenilir kaynak)
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }

        // 2) Kısa-isim -> WiseCP gerçek route alias map
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
            'ac-ps-create-ticket-request' => 'ac-ps-create-ticket-request',
            'tickets'                 => 'ac-ps-tickets',
            'my-tickets'              => 'ac-ps-tickets',
            'messages'                => 'ac-ps-messages',
            'detail-message'          => 'ac-ps-detail-message',
            'invoices'                => 'ac-ps-invoices',
            'detail-invoice'          => 'ac-ps-detail-invoice',
            'detail-invoice-pdf'      => 'ac-ps-detail-invoice',
            'balance'                 => 'ac-ps-balance',
            'balance-page'            => 'ac-ps-balance',
            'info'                    => 'ac-ps-info',
            'ac-info'                 => 'ac-ps-info',
            'products'                => 'ac-ps-products',
            'all-orders'              => 'ac-ps-products',
            'products-t'              => 'ac-ps-products-t',
            'product'                 => 'ac-ps-product',
            'sms'                     => 'ac-ps-sms',
            'domains'                 => 'ac-products-domain',
            'products-domain'         => 'ac-products-domain',
            'whois-profiles'          => 'ac-products-domain-whois-profiles',
            'products-domain-whois-profiles' => 'ac-products-domain-whois-profiles',
            'create-whois-profile'    => 'ac-products-domain-create-whois-profile',
            'products-domain-create-whois-profile' => 'ac-products-domain-create-whois-profile',
            'login'                   => 'sign-in',
            'register'                => 'sign-up',
            'logout'                  => 'sign-out',
            'account'                 => 'my-account',
            'homepage'                => '',
            'home'                    => '',
        ];
        $real_slug = isset($aliases[$slug]) ? $aliases[$slug] : $slug;

        // 3) CRLink dene (gerçek WiseCP routing)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                // Bozuk URL kontrolü (boş ID parametresi vb.)
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) { /* fallback'e düş */ }
        }

        // 4) Son çare: APP_URI base + slug
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
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
                    // WiseCP runtime: row['number'] (fatura no), row['id'], row['cdate'] (olusturulma tarihi)
                    $iid = isset($row['id']) ? $row['id'] : '';
                    $num = isset($row['number']) ? $row['number'] : (isset($row['num']) ? $row['num'] : $iid);

                    // Tutar: WiseCP'de farkli surumlerde farkli key adlari olabilir
                    // total / amount / value / fee
                    $r_total = $row['total'] ?? ($row['amount'] ?? ($row['value'] ?? ($row['fee'] ?? null)));
                    $r_cid   = $row['currency'] ?? ($row['cid'] ?? ($row['amount_cid'] ?? null));

                    $amount = '';
                    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $r_total !== null && $r_cid !== null) {
                        $amount = Money::formatter_symbol($r_total, $r_cid);
                    } elseif($r_total !== null) {
                        // Money sinifi yoksa basit format
                        $amount = number_format((float)$r_total, 2, ',', '.');
                        if($r_cid && is_string($r_cid)) $amount .= ' ' . $r_cid;
                    }

                    // Classic: cdate (olusturulma), duedate (son odeme), datepaid (odendi)
                    $ctime = isset($row['cdate']) ? $row['cdate']
                           : (isset($row['ctime']) ? $row['ctime']
                           : (isset($row['creation_date']) ? $row['creation_date'] : ''));
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
                        <td><span style="font-family:monospace;font-size:13px;">#<?php echo htmlspecialchars($num, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></td>
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
