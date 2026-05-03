<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        // Bu yuzden once alias+CRLink, $links sadece bilinmeyen slug'lar icin son fallback
        global $links;

        // CDG_LINK_HARDCODED - Yunus'un sitesinde KESIN dogru URL'ler (CRLink bypass)
        static $hardcoded = [
            'ac-ps-create-ticket-request' => '/hesabim/destek-talebi-olustur',
            'create-ticket-request'       => '/hesabim/destek-talebi-olustur',
            'create-ticket'               => '/hesabim/destek-talebi-olustur',
        ];
        if(isset($hardcoded[$slug])) {
            $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
            return $base . $hardcoded[$slug];
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
            'affiliate'               => 'ac-affiliate',
            'ac-affiliate'            => 'ac-affiliate',
            'reseller'                => 'ac-reseller',
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
        // Son care: $links bakilirsa kullan (sadece bilinmeyen slug'lar icin)
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
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
        <!-- ARAMA + FİLTRE + TOPLAM -->
        <?php
        $unpaid_total = 0;
        $unpaid_count = 0;
        $paid_count = 0;
        foreach($items as $inv) {
            $status = strtolower($inv['status'] ?? '');
            if($status === 'unpaid' || $status === 'overdue') {
                $unpaid_total += (float)($inv['amount'] ?? 0);
                $unpaid_count++;
            } elseif($status === 'paid') {
                $paid_count++;
            }
        }
        ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:14px;">
            <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #fcd34d;border-radius:10px;padding:14px;">
                <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                    <i class="bi bi-hourglass-split"></i> Bekleyen Faturalar
                </div>
                <div style="font-size:18px;font-weight:800;color:#0f172a;"><?php echo $unpaid_count; ?> adet</div>
                <?php if($unpaid_total > 0): ?>
                <div style="font-size:13px;color:#92400e;margin-top:2px;">
                    <?php echo number_format($unpaid_total, 2, ',', '.'); ?> ₺ tutarında
                </div>
                <?php endif; ?>
            </div>
            <div style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);border:1px solid #86efac;border-radius:10px;padding:14px;">
                <div style="font-size:11px;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                    <i class="bi bi-check-circle-fill"></i> Ödenen Faturalar
                </div>
                <div style="font-size:18px;font-weight:800;color:#0f172a;"><?php echo $paid_count; ?> adet</div>
            </div>
            <div style="background:linear-gradient(135deg,#CFFAFE,#A5F3FC);border:1px solid #67E8F9;border-radius:10px;padding:14px;">
                <div style="font-size:11px;font-weight:700;color:#2E3B4E;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                    <i class="bi bi-receipt"></i> Toplam
                </div>
                <div style="font-size:18px;font-weight:800;color:#0f172a;"><?php echo count($items); ?> fatura</div>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;align-items:center;">
            <div style="flex:1;min-width:200px;position:relative;">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                <input type="text" id="cdg-inv-search" placeholder="Fatura no veya açıklama ara..." onkeyup="cdgInvFilter()" style="width:100%;padding:10px 12px 10px 36px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;">
            </div>
            <select id="cdg-inv-status-filter" onchange="cdgInvFilter()" style="padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                <option value="">Tüm Durumlar</option>
                <option value="unpaid">Bekleyen</option>
                <option value="overdue">Gecikmiş</option>
                <option value="paid">Ödenen</option>
                <option value="cancelled">İptal</option>
            </select>
        </div>

        <div class="cdg-table-wrap">
            <table class="cdg-table" id="cdg-inv-table">
                <thead>
                    <tr>
                        <th>Fatura No</th>
                        <th style="text-align:center;">Oluşturma</th>
                        <th style="text-align:center;">Vade</th>
                        <th style="text-align:right;">Tutar</th>
                        <th style="text-align:center;">Durum</th>
                        <th style="text-align:right;">İşlem</th>
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
                        // Turkce label fallback
                        $st = isset($row['status']) ? $row['status'] : '';
                        $st_labels = [
                            'paid'      => ['cls' => 'success', 'lbl' => 'Ödendi'],
                            'unpaid'    => ['cls' => 'warning', 'lbl' => 'Ödenmemiş'],
                            'waiting'   => ['cls' => 'info',    'lbl' => 'Onay Bekliyor'],
                            'refund'    => ['cls' => 'danger',  'lbl' => 'İade Edildi'],
                            'cancelled' => ['cls' => 'muted',   'lbl' => 'İptal Edildi'],
                        ];
                        $st_info = $st_labels[$st] ?? ['cls' => 'warning', 'lbl' => $st ?: '-'];
                        $status_html = '<span class="cdg-badge cdg-badge-' . $st_info['cls'] . '">' . htmlspecialchars($st_info['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>';
                    }
                ?>
                    <tr data-search="<?php echo htmlspecialchars(strtolower($num . ' ' . ($row['description'] ?? '') . ' ' . ($row['title'] ?? '')), ENT_QUOTES); ?>" data-status="<?php echo htmlspecialchars(strtolower($row['status'] ?? ''), ENT_QUOTES); ?>">
                        <td><span style="font-family:monospace;font-size:13px;">#<?php echo htmlspecialchars($num, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span></td>
                        <td style="text-align:center;font-size:13px;"><?php echo $ctime_fmt; ?></td>
                        <td style="text-align:center;font-size:13px;"><?php echo $duedate_fmt ?: '-'; ?></td>
                        <td style="text-align:right;font-weight:600;"><?php echo $amount; ?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                        <td style="text-align:right;">
                            <?php if(isset($row['detail_link'])): ?>
                                <a href="<?php echo $row['detail_link']; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
                                    <i class="bi bi-eye"></i> Görüntüle
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
        window.cdgInvFilter = function(){
            var search = (document.getElementById('cdg-inv-search').value || '').toLowerCase().trim();
            var status = (document.getElementById('cdg-inv-status-filter').value || '').toLowerCase();
            var rows = document.querySelectorAll('#cdg-inv-table tbody tr');
            rows.forEach(function(tr){
                var ds = (tr.getAttribute('data-search') || '');
                var st = (tr.getAttribute('data-status') || '');
                var matchSearch = !search || ds.indexOf(search) !== -1;
                var matchStatus = !status || st === status;
                tr.style.display = (matchSearch && matchStatus) ? '' : 'none';
            });
        };
        </script>
    <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-receipt"></i></div>
            <h3>Fatura bulunmuyor</h3>
            <p>Henüz hiçbir faturanız oluşturulmamış.</p>
        </div>
    <?php endif; ?>
</div>
