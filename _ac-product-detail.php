<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Generic ürün detay sayfası — hosting/server/domain/sms/special/software hepsi bunu include eder
 *
 * WiseCP'nin inject ettiği global'ler:
 *   $amount, $options, $p_options, $module_con, $server, $buttons (panel linkleri),
 *   $product, $product_type ($pt), $duedate, $period, $period_time, $status
 */

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

// Ürün bilgileri (WiseCP'den ne geldiyse al, yoksa boş)
$product_type = isset($product_type) ? $product_type : (isset($pt) ? $pt : 'product');
$_options     = isset($options) && is_array($options) ? $options : [];
$_poptions    = isset($p_options) && is_array($p_options) ? $p_options : [];
$_buttons     = isset($buttons) && is_array($buttons) ? $buttons : [];
$_server      = isset($server) && is_array($server) ? $server : [];

// Gösterilecek başlık
$title = '';
if(!empty($_options['domain'])) $title = $_options['domain'];
elseif(!empty($_options['hostname'])) $title = $_options['hostname'];
elseif(!empty($_options['ip'])) $title = $_options['ip'];
elseif(!empty($_options['name'])) $title = $_options['name'];
elseif(isset($product['name'])) $title = $product['name'];
elseif(isset($header_title)) $title = $header_title;

// Ürün adı (paket)
$package_name = isset($product['name']) ? $product['name'] : (isset($_options['package_name']) ? $_options['package_name'] : '');

// Tutar
$amount_str = '';
if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($amount) && isset($amount_cid)) {
    $amount_str = Money::formatter_symbol($amount, $amount_cid);
}
$period_str = '';
if(class_exists('View') && method_exists('View', 'period') && isset($period_time) && isset($period)) {
    $period_str = View::period($period_time, $period);
}

// Vade
$duedate_str = '';
if(isset($duedate) && $duedate && !in_array(substr($duedate,0,4), ['1881','1970'])) {
    if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
        $duedate_str = DateManager::format(Config::get("options/date-format"), $duedate);
    } else {
        $duedate_str = date('d.m.Y', strtotime($duedate));
    }
}

// Durum
$status_html = '';
if(isset($product_situations) && isset($status) && isset($product_situations[$status])) {
    $status_html = $product_situations[$status];
}

// Kotalar + kullanım yüzdeleri
$quotas = [];

function _cdg_format_size($mb) {
    $mb = (int) $mb;
    if($mb >= 1024) return round($mb/1024, 1) . ' GB';
    return $mb . ' MB';
}
function _cdg_quota($options, $limit_key, $used_key, $icon, $label, $unit_mb = false) {
    if(empty($options[$limit_key])) return null;
    $limit = class_exists('Filter') ? Filter::numbers($options[$limit_key]) : (int)$options[$limit_key];
    $used  = isset($options[$used_key]) ? (class_exists('Filter') ? Filter::numbers($options[$used_key]) : (int)$options[$used_key]) : null;
    $value = $unit_mb ? _cdg_format_size($limit) : $limit;
    $value_used = ($used !== null && $unit_mb) ? _cdg_format_size($used) : $used;
    $percent = ($used !== null && $limit > 0) ? min(100, round(($used / $limit) * 100)) : null;
    return [
        'icon' => $icon, 'label' => $label,
        'value' => $value, 'used' => $value_used, 'percent' => $percent
    ];
}

if($q = _cdg_quota($_options, 'disk_limit',      'disk_used',      'bi-hdd',          'Disk',         true))  $quotas[] = $q;
if($q = _cdg_quota($_options, 'bandwidth_limit', 'bandwidth_used', 'bi-graph-up',     'Trafik',       true))  $quotas[] = $q;
if($q = _cdg_quota($_options, 'email_limit',     'email_used',     'bi-envelope',     'E-posta',      false)) $quotas[] = $q;
if($q = _cdg_quota($_options, 'database_limit',  'database_used',  'bi-database',     'Veritabani',   false)) $quotas[] = $q;
if($q = _cdg_quota($_options, 'subdomain_limit', 'subdomain_used', 'bi-diagram-3',    'Alt Alan Adi', false)) $quotas[] = $q;
if($q = _cdg_quota($_options, 'ftp_limit',       'ftp_used',       'bi-cloud-upload', 'FTP',          false)) $quotas[] = $q;
if($q = _cdg_quota($_options, 'addons_limit',    'addons_used',    'bi-puzzle',       'Eklenti',      false)) $quotas[] = $q;

// DNS bilgileri
$dns_list = [];
if(!empty($_options['dns']) && is_array($_options['dns'])) $dns_list = $_options['dns'];
elseif(!empty($_server)) {
    foreach(['ns1','ns2','ns3','ns4'] as $ns) {
        if(!empty($_server[$ns])) $dns_list[$ns] = $_server[$ns];
    }
}

// IP & hostname
$server_ip       = isset($_options['ip']) ? $_options['ip'] : (isset($_server['ip']) ? $_server['ip'] : '');
$server_hostname = isset($_options['hostname']) ? $_options['hostname'] : '';
$server_user     = isset($_options['username']) ? $_options['username'] : (isset($_options['user']) ? $_options['user'] : '');
$panel_type      = isset($_options['panel_type']) ? $_options['panel_type'] : (isset($_poptions['panel_type']) ? $_poptions['panel_type'] : '');

// Panel butonları için ikon mapping
$button_icons = [
    'panel'    => 'bi-box-arrow-up-right',
    'mail'     => 'bi-envelope-paper',
    'reset'    => 'bi-key',
    'transfer' => 'bi-arrow-left-right',
    'manage'   => 'bi-gear',
    'whois'    => 'bi-search',
    'dns'      => 'bi-diagram-3',
];
?>

<!-- ÜRÜN DETAY BAŞLIK KARTI -->
<div class="cdg-detail-hero">
    <div class="cdg-detail-hero-inner">
        <div>
            <div class="eyebrow">
                <i class="bi bi-<?php echo $product_type=='hosting' ? 'hdd-network' : ($product_type=='domain' ? 'globe2' : ($product_type=='server' ? 'server' : 'box-seam')); ?>"></i>
                <?php echo ucfirst($product_type); ?> Hizmeti
                <?php if($status_html): ?> &middot; <?php echo $status_html; ?><?php endif; ?>
            </div>
            <h1><?php echo htmlspecialchars($title ?: '-'); ?></h1>
            <?php if($package_name): ?>
                <p class="package"><?php echo htmlspecialchars($package_name); ?></p>
            <?php endif; ?>
        </div>

        <div class="cdg-detail-hero-meta">
            <?php if($amount_str): ?>
                <div class="meta-item">
                    <div class="lbl">Tutar</div>
                    <div class="val"><?php echo $amount_str; ?> <?php if($period_str): ?><span class="muted"><?php echo $period_str; ?></span><?php endif; ?></div>
                </div>
            <?php endif; ?>
            <?php if($duedate_str): ?>
                <div class="meta-item">
                    <div class="lbl">Bitis Tarihi</div>
                    <div class="val"><i class="bi bi-calendar-event"></i> <?php echo $duedate_str; ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- HIZLI EYLEM BUTONLARI (panel, mail, vs.) -->
<?php if(!empty($_buttons) && is_array($_buttons)): ?>
<div class="cdg-grid cdg-grid-4 mt-3">
    <?php foreach($_buttons as $key => $btn):
        $url = isset($btn['url']) ? $btn['url'] : '#';
        $name = isset($btn['name']) ? $btn['name'] : ucfirst($key);
        $icon = isset($button_icons[$key]) ? $button_icons[$key] : 'bi-arrow-right-circle';
    ?>
        <a href="<?php echo $url; ?>" class="cdg-quickbtn" <?php echo strpos($url,'http')===0 ? 'target="_blank" rel="noopener"' : ''; ?>>
            <i class="bi <?php echo $icon; ?>"></i>
            <span><?php echo htmlspecialchars($name); ?></span>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="cdg-grid cdg-grid-2 mt-3">

    <!-- KOTALAR / OZELLIKLER -->
    <?php if(!empty($quotas)): ?>
    <div class="cdg-card">
        <div class="cdg-card-head"><h3><i class="bi bi-bar-chart"></i> Paket Ozellikleri</h3></div>
        <div class="cdg-spec-grid">
            <?php foreach($quotas as $q):
                $has_usage = isset($q['percent']) && $q['percent'] !== null;
                $color = 'var(--cdg-primary)';
                if($has_usage) {
                    if($q['percent'] >= 90) $color = '#ef4444';
                    elseif($q['percent'] >= 70) $color = '#f59e0b';
                    else $color = '#10b981';
                }
            ?>
                <div class="cdg-spec-item">
                    <div class="icon"><i class="bi <?php echo $q['icon']; ?>"></i></div>
                    <div style="flex:1;min-width:0;">
                        <div class="lbl"><?php echo $q['label']; ?></div>
                        <?php if($has_usage): ?>
                            <div class="val"><?php echo $q['used']; ?> / <?php echo $q['value']; ?></div>
                            <div class="cdg-progress" title="%<?php echo $q['percent']; ?> kullaniliyor">
                                <span style="width:<?php echo $q['percent']; ?>%;background:<?php echo $color; ?>;"></span>
                            </div>
                            <div class="cdg-progress-label" style="color:<?php echo $color; ?>;">%<?php echo $q['percent']; ?></div>
                        <?php else: ?>
                            <div class="val"><?php echo $q['value']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- SUNUCU / BAGLANTI BILGILERI -->
    <?php if($server_ip || $server_hostname || $server_user || $panel_type): ?>
    <div class="cdg-card">
        <div class="cdg-card-head"><h3><i class="bi bi-hdd-rack"></i> Baglanti Bilgileri</h3></div>
        <table class="cdg-info-table">
            <?php if($panel_type): ?>
                <tr><td>Panel</td><td><strong><?php echo htmlspecialchars($panel_type); ?></strong></td></tr>
            <?php endif; ?>
            <?php if($server_hostname): ?>
                <tr><td>Hostname</td><td><code><?php echo htmlspecialchars($server_hostname); ?></code></td></tr>
            <?php endif; ?>
            <?php if($server_ip): ?>
                <tr><td>IP Adresi</td><td><code><?php echo htmlspecialchars($server_ip); ?></code></td></tr>
            <?php endif; ?>
            <?php if($server_user): ?>
                <tr><td>Kullanici Adi</td><td><code><?php echo htmlspecialchars($server_user); ?></code></td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- DNS / NAMESERVER -->
<?php if(!empty($dns_list)): ?>
<div class="cdg-card mt-3">
    <div class="cdg-card-head"><h3><i class="bi bi-diagram-3"></i> Nameserver / DNS</h3></div>
    <div class="cdg-grid cdg-grid-4">
        <?php foreach($dns_list as $key => $ns):
            if(!$ns) continue;
        ?>
            <div class="cdg-ns-card">
                <div class="lbl"><?php echo strtoupper($key); ?></div>
                <code><?php echo htmlspecialchars($ns); ?></code>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- BOŞ DURUM -->
<?php if(empty($_options) && empty($_buttons) && empty($quotas) && empty($dns_list) && !$server_ip): ?>
<div class="cdg-card mt-3">
    <div class="cdg-empty">
        <div class="icon"><i class="bi bi-info-circle"></i></div>
        <h3>Hizmet detaylari hazirlaniyor</h3>
        <p>Bu hizmet icin detay bilgileri WiseCP modülünüz tarafindan saglanir. Eger bilgi gormuyorsaniz hizmet aktif olmamis veya modül baglantisi kurulmamistir.</p>
        <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>" class="cdg-btn cdg-btn-primary mt-3">
            <i class="bi bi-headset"></i> Destek Al
        </a>
    </div>
</div>
<?php endif; ?>

<!-- HOSTING İŞLEMLERİ (DNS, Email, DB, vb.) -->
<?php if($product_type == 'hosting' || $product_type == 'server'):
    $panel_url = isset($_buttons['panel']['url']) ? $_buttons['panel']['url'] : '';
?>
<div class="cdg-card mt-3">
    <div class="cdg-card-head">
        <h3><i class="bi bi-tools"></i> Hizmet Islemleri</h3>
        <?php if($panel_url): ?>
            <span style="font-size:12px;color:var(--cdg-muted);">DirectAdmin/cPanel uzerinden gerceklesir</span>
        <?php endif; ?>
    </div>
    <div class="cdg-grid cdg-grid-3" style="gap:12px;">
        <a href="<?php echo $panel_url ?: '#'; ?>" <?php echo $panel_url ? 'target="_blank" rel="noopener"' : ''; ?> class="cdg-action-card">
            <div class="ico"><i class="bi bi-diagram-3"></i></div>
            <div class="ttl">DNS Yonetimi</div>
            <div class="dsc">Nameserver, A, CNAME, MX kayitlari</div>
        </a>
        <a href="<?php echo $panel_url ?: '#'; ?>" <?php echo $panel_url ? 'target="_blank" rel="noopener"' : ''; ?> class="cdg-action-card">
            <div class="ico"><i class="bi bi-envelope-at"></i></div>
            <div class="ttl">E-posta Hesaplari</div>
            <div class="dsc">Olustur, sifre degistir, yonlendirme</div>
        </a>
        <a href="<?php echo $panel_url ?: '#'; ?>" <?php echo $panel_url ? 'target="_blank" rel="noopener"' : ''; ?> class="cdg-action-card">
            <div class="ico"><i class="bi bi-database"></i></div>
            <div class="ttl">Veritabani</div>
            <div class="dsc">MySQL, PostgreSQL yonetimi</div>
        </a>
        <a href="<?php echo $panel_url ?: '#'; ?>" <?php echo $panel_url ? 'target="_blank" rel="noopener"' : ''; ?> class="cdg-action-card">
            <div class="ico"><i class="bi bi-folder"></i></div>
            <div class="ttl">Dosya Yoneticisi</div>
            <div class="dsc">FTP/SFTP, dosya islemleri</div>
        </a>
        <a href="<?php echo $panel_url ?: '#'; ?>" <?php echo $panel_url ? 'target="_blank" rel="noopener"' : ''; ?> class="cdg-action-card">
            <div class="ico"><i class="bi bi-shield-lock"></i></div>
            <div class="ttl">SSL Sertifika</div>
            <div class="dsc">Let's Encrypt, ozel SSL</div>
        </a>
        <a href="<?php echo $panel_url ?: '#'; ?>" <?php echo $panel_url ? 'target="_blank" rel="noopener"' : ''; ?> class="cdg-action-card">
            <div class="ico"><i class="bi bi-clock-history"></i></div>
            <div class="ttl">Yedekleme</div>
            <div class="dsc">Otomatik ve manuel yedekler</div>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- ALT EYLEMLER -->
<div class="cdg-card mt-3">
    <div class="cdg-card-head"><h3><i class="bi bi-list-check"></i> Hizmet Yonetimi</h3></div>
    <div style="display:flex;flex-wrap:wrap;gap:10px;padding:10px 0;">
        <a href="<?php echo cdg_link('ac-ps-products'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
            <i class="bi bi-arrow-left"></i> Tum Hizmetler
        </a>
        <a href="<?php echo cdg_link('ac-ps-invoices'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
            <i class="bi bi-receipt"></i> Faturalar
        </a>
        <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
            <i class="bi bi-headset"></i> Destek Talebi
        </a>
    </div>
</div>
