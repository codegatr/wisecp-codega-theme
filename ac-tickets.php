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

<?php
// Hook: TicketClientAreaViewList - üçüncü taraf entegrasyonlar için
if(class_exists('Hook') && method_exists('Hook', 'run')) {
    $h_contents = Hook::run("TicketClientAreaViewList");
    if($h_contents && is_array($h_contents)) {
        foreach($h_contents as $h_content) {
            if($h_content) echo $h_content;
        }
    }
}
?>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-headset"></i> Destek Talepleri</h3>
        <a href="<?php echo $create_link; ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">
            <i class="bi bi-plus-lg"></i> Yeni Talep
        </a>
    </div>

    <?php if(!empty($items)): ?>
        <!-- ARAMA + FİLTRE -->
        <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;align-items:center;">
            <div style="flex:1;min-width:200px;position:relative;">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                <input type="text" id="cdg-tk-search" placeholder="Konu, ID veya departman ara..." onkeyup="cdgTkFilter()" style="width:100%;padding:10px 12px 10px 36px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;">
            </div>
            <select id="cdg-tk-status-filter" onchange="cdgTkFilter()" style="padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                <option value="">Tüm Durumlar</option>
                <option value="open">Açık</option>
                <option value="customer-reply">Yanıt Bekliyor</option>
                <option value="answered">Yanıtlandı</option>
                <option value="closed">Kapalı</option>
            </select>
            <div style="font-size:13px;color:#64748b;">
                <span id="cdg-tk-count"><?php echo count($items); ?></span> talep
            </div>
        </div>

        <div class="cdg-table-wrap">
            <table class="cdg-table" id="cdg-tk-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Konu</th>
                        <th style="text-align:center;">Durum</th>
                        <th style="text-align:right;">İşlem</th>
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
                    <tr data-search="<?php echo htmlspecialchars(strtolower($tid . ' ' . $title . ' ' . ($row['service'] ?? '') . ' ' . ($row['department'] ?? '')), ENT_QUOTES); ?>" data-status="<?php echo htmlspecialchars(strtolower($row['status'] ?? ''), ENT_QUOTES); ?>">
                        <td><span style="font-family:monospace;color:var(--cdg-muted);font-size:12px;">#<?php echo $tid; ?></span></td>
                        <td>
                            <?php if(isset($row['detail_link'])): ?>
                                <a href="<?php echo $row['detail_link']; ?>" style="<?php echo $bold ? 'font-weight:600;' : ''; ?>color:var(--cdg-text);"><?php echo htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a>
                            <?php else: ?>
                                <span style="<?php echo $bold ? 'font-weight:600;' : ''; ?>"><?php echo htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <?php if(!empty($row['service'])): ?>
                                <div style="font-size:12px;color:var(--cdg-muted);"><i class="bi bi-link-45deg"></i> <?php echo htmlspecialchars($row['service'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;font-size:12px;"><?php echo $status_str; ?></td>
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
        window.cdgTkFilter = function(){
            var search = (document.getElementById('cdg-tk-search').value || '').toLowerCase().trim();
            var status = (document.getElementById('cdg-tk-status-filter').value || '').toLowerCase();
            var rows = document.querySelectorAll('#cdg-tk-table tbody tr');
            var visible = 0;
            rows.forEach(function(tr){
                var ds = (tr.getAttribute('data-search') || '');
                var st = (tr.getAttribute('data-status') || '');
                var matchSearch = !search || ds.indexOf(search) !== -1;
                var matchStatus = !status || st === status;
                if(matchSearch && matchStatus) { tr.style.display = ''; visible++; }
                else { tr.style.display = 'none'; }
            });
            document.getElementById('cdg-tk-count').textContent = visible;
        };
        </script>
    <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-emoji-smile"></i></div>
            <h3>Acik destek talebiniz yok</h3>
            <p>Bir sorununuz veya soru oldugunda destek ekibimize ulasabilirsiniz.</p>
            <a href="<?php echo $create_link; ?>" class="cdg-btn cdg-btn-primary mt-3">
                <i class="bi bi-plus-lg"></i> Yeni Talep Oluştur
            </a>
        </div>
    <?php endif; ?>
</div>
