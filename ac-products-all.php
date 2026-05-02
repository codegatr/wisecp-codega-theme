<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

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

$page_title = isset($page_title) ? $page_title : 'Tum Hizmetlerim';

// Kategori filtreleri için $list veya $orders kullan
$items = [];
if(isset($list) && is_array($list)) $items = $list;
elseif(isset($orders) && is_array($orders)) $items = $orders;
?>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-grid"></i> <?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
        <div style="display:flex;gap:8px;">
            <a href="<?php echo cdg_link('ac-ps-products-t', ['hosting']); ?>" class="cdg-chip"><i class="bi bi-hdd-network"></i> Hosting</a>
            <a href="<?php echo cdg_link('ac-ps-products-t', ['domain']); ?>" class="cdg-chip"><i class="bi bi-globe2"></i> Domain</a>
            <a href="<?php echo cdg_link('ac-ps-products-t', ['server']); ?>" class="cdg-chip"><i class="bi bi-server"></i> Sunucu</a>
            <a href="<?php echo cdg_link('ac-ps-products-t', ['sms']); ?>" class="cdg-chip"><i class="bi bi-chat-dots"></i> SMS</a>
        </div>
    </div>

    <?php if(!empty($items)): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hizmet</th>
                        <th style="text-align:right;">Tutar</th>
                        <th style="text-align:center;">Bitis Tarihi</th>
                        <th style="text-align:center;">Durum</th>
                        <th style="text-align:right;">Islem</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($items as $r => $row):
                    $name = isset($row['name']) ? $row['name'] : '-';
                    $oid  = isset($row['id']) ? $row['id'] : '-';

                    $amount = '';
                    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                        $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']);
                    }
                    $period = '';
                    if(class_exists('View') && method_exists('View', 'period') && isset($row['period_time']) && isset($row['period'])) {
                        $period = View::period($row['period_time'], $row['period']);
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

                    $sub_info = '';
                    if(isset($row['options']['domain'])) $sub_info = $row['options']['domain'];
                    elseif(isset($row['options']['hostname'])) $sub_info = $row['options']['hostname'];
                    elseif(isset($row['options']['ip'])) $sub_info = $row['options']['ip'];
                    elseif(isset($row['options']['code'])) $sub_info = $row['options']['code'];
                    elseif(isset($row['options']['identity'])) $sub_info = $row['options']['identity'];
                    elseif(isset($row['type']) && $row['type']=='special' && isset($row['options']['category_name'])) $sub_info = $row['options']['category_name'];

                    $can_manage = isset($row['detail_link']) && (!isset($row['status']) || !in_array($row['status'], ['waiting','inprocess','cancelled']));
                ?>
                    <tr>
                        <td><span style="font-family:monospace;color:var(--cdg-muted);font-size:12px;">#<?php echo $oid; ?></span></td>
                        <td>
                            <div style="font-weight:600;"><?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <?php if($sub_info): ?>
                                <div style="font-size:12px;color:var(--cdg-muted);"><?php echo htmlspecialchars($sub_info, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;font-weight:600;">
                            <?php echo $amount; ?>
                            <?php if($period): ?><div style="font-size:11px;color:var(--cdg-muted);font-weight:400;"><?php echo $period; ?></div><?php endif; ?>
                        </td>
                        <td style="text-align:center;font-size:13px;"><?php echo $duedate_format; ?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                        <td style="text-align:right;">
                            <?php if($can_manage): ?>
                                <a href="<?php echo $row['detail_link']; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
                                    <i class="bi bi-gear"></i> Yonet
                                </a>
                            <?php else: ?>
                                <span style="opacity:0.5;font-size:12px;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-box-seam"></i></div>
            <h3>Aktif hizmetiniz yok</h3>
            <p>Henuz hicbir hizmet satin almamissiniz.</p>
            <div style="display:flex;gap:10px;justify-content:center;margin-top:16px;">
                <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-hdd-network"></i> Hosting Al
                </a>
                <a href="<?php echo cdg_link('domain'); ?>" class="cdg-btn cdg-btn-outline">
                    <i class="bi bi-globe2"></i> Domain Al
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
