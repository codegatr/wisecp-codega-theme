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

// Kullanıcı bilgileri
$user_name    = 'Musteri';
$user_balance = 0; $user_bal_cid = 0; $user_balance_str = '0,00';
if(class_exists('User') && isset(User::$init->info)) {
    $info = User::$init->info;
    $user_name = (isset($info['name']) ? $info['name'] : '') . (isset($info['surname']) ? ' ' . $info['surname'] : '');
    if(!trim($user_name)) $user_name = isset($info['username']) ? $info['username'] : 'Musteri';
    $user_balance = isset($info['balance']) ? $info['balance'] : 0;
    $user_bal_cid = isset($info['balance_cid']) ? $info['balance_cid'] : 0;
    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $user_bal_cid) {
        $user_balance_str = Money::formatter_symbol($user_balance, $user_bal_cid);
    }
}

// Sayım
$count_active_products = 0;
if(isset($orders) && is_array($orders)) $count_active_products += count($orders);
if(isset($domain_orders['active']) && is_array($domain_orders['active'])) $count_active_products += count($domain_orders['active']);

$count_open_tickets = 0;
if(isset($tickets) && is_array($tickets)) {
    foreach($tickets as $t) {
        if(isset($t['status']) && in_array($t['status'], ['open','answered','customer-reply','waiting'])) $count_open_tickets++;
    }
}

$pending_invoices = isset($statistic3) ? (int)$statistic3 : (isset($pending_invoices_count) ? (int)$pending_invoices_count : 0);

// Modüller aktif mi?
$mod_hosting = isset($pg_activation['hosting']) ? !empty($pg_activation['hosting']) : true;
$mod_server  = isset($pg_activation['server'])  ? !empty($pg_activation['server'])  : true;
$mod_domain  = isset($pg_activation['domain'])  ? !empty($pg_activation['domain'])  : true;
$mod_sms     = isset($pg_activation['sms'])     ? !empty($pg_activation['sms'])     : true;

$has_services = $count_active_products > 0;
?>

<!-- ÜST KARŞILAMA HERO -->
<div class="cdg-dash-hero">
    <div class="cdg-dash-hero-bg">
        <div class="cdg-dash-hero-glow"></div>
    </div>
    <div class="cdg-dash-hero-inner">
        <div>
            <div class="cdg-dash-greeting">
                <i class="bi bi-sun-fill"></i>
                <span>Hosgeldiniz, <strong><?php echo htmlspecialchars($user_name); ?></strong></span>
            </div>
            <h1>Hizmetlerinizi <span class="cdg-dash-hero-accent">tek panelden</span> yonetin</h1>
            <p>Hosting, domain, sunucu ve diger tum hizmetlerinize hizli erisim. Faturalarinizi anlik gorun, destek alin.</p>
        </div>
        <div class="cdg-dash-hero-actions">
            <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-white">
                <i class="bi bi-plus-lg"></i> Yeni Hizmet Al
            </a>
            <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>" class="cdg-btn cdg-btn-ghost">
                <i class="bi bi-headset"></i> Destek Al
            </a>
        </div>
    </div>
</div>

<!-- 4 STAT KARTI -->
<div class="cdg-stat-grid">
    <a href="<?php echo cdg_link('ac-ps-products'); ?>" class="cdg-stat cdg-stat-link">
        <div class="cdg-stat-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="cdg-stat-body">
            <div class="label">Aktif Hizmetler</div>
            <div class="value"><?php echo $count_active_products; ?></div>
            <div class="meta">Tum hizmetlerim &rarr;</div>
        </div>
    </a>
    <a href="<?php echo cdg_link('ac-ps-invoices'); ?>" class="cdg-stat cdg-stat-link">
        <div class="cdg-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
            <i class="bi bi-receipt"></i>
        </div>
        <div class="cdg-stat-body">
            <div class="label">Bekleyen Faturalar</div>
            <div class="value"><?php echo $pending_invoices; ?></div>
            <div class="meta">Faturalarima git &rarr;</div>
        </div>
    </a>
    <a href="<?php echo cdg_link('ac-ps-tickets'); ?>" class="cdg-stat cdg-stat-link">
        <div class="cdg-stat-icon" style="background:linear-gradient(135deg,#10b981,#34d399);">
            <i class="bi bi-headset"></i>
        </div>
        <div class="cdg-stat-body">
            <div class="label">Acik Talepler</div>
            <div class="value"><?php echo $count_open_tickets; ?></div>
            <div class="meta">Destek talepleri &rarr;</div>
        </div>
    </a>
    <a href="<?php echo cdg_link('ac-ps-balance'); ?>" class="cdg-stat cdg-stat-link">
        <div class="cdg-stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
            <i class="bi bi-wallet2"></i>
        </div>
        <div class="cdg-stat-body">
            <div class="label">Bakiyem</div>
            <div class="value" style="font-size:22px;"><?php echo $user_balance_str; ?></div>
            <div class="meta">Yukleme yap &rarr;</div>
        </div>
    </a>
</div>

<?php if($has_services): ?>
<!-- AKTİF HİZMETLER + DESTEK -->
<div class="cdg-grid cdg-grid-2 mt-3">
    <!-- Aktif hizmetler -->
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-box-seam"></i> Son Aktif Hizmetlerim</h3>
            <a href="<?php echo cdg_link('ac-ps-products'); ?>" style="font-size:13px;">Tumu &rarr;</a>
        </div>
        <?php if(isset($orders) && is_array($orders) && count($orders) > 0): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead><tr><th>Hizmet</th><th style="text-align:right;">Tutar</th><th style="text-align:center;">Durum</th><th></th></tr></thead>
                <tbody>
                <?php
                $shown = 0;
                foreach($orders as $row) {
                    if($shown >= 4) break; $shown++;
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
                    $sub_info = '';
                    if(isset($row['options']['domain'])) $sub_info = $row['options']['domain'];
                    elseif(isset($row['options']['hostname'])) $sub_info = $row['options']['hostname'];
                    elseif(isset($row['options']['ip'])) $sub_info = $row['options']['ip'];
                ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;"><?php echo htmlspecialchars($name); ?></div>
                            <?php if($sub_info): ?><div style="font-size:12px;color:var(--cdg-muted);"><?php echo htmlspecialchars($sub_info); ?></div><?php endif; ?>
                        </td>
                        <td style="text-align:right;font-weight:600;font-size:13px;"><?php echo $amount; ?> <?php if($period): ?><span style="font-size:11px;color:var(--cdg-muted);font-weight:400;"><?php echo $period; ?></span><?php endif; ?></td>
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
        <div class="cdg-empty"><div class="icon"><i class="bi bi-box-seam"></i></div><p>Henuz aktif hizmetiniz yok.</p></div>
        <?php endif; ?>
    </div>

    <!-- Son destek talepleri -->
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-headset"></i> Son Destek Talepleri</h3>
            <a href="<?php echo cdg_link('ac-ps-tickets'); ?>" style="font-size:13px;">Tumu &rarr;</a>
        </div>
        <?php if(isset($tickets) && is_array($tickets) && count($tickets) > 0): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead><tr><th>Konu</th><th style="text-align:center;">Durum</th><th></th></tr></thead>
                <tbody>
                <?php
                $shown = 0;
                foreach($tickets as $row) {
                    if($shown >= 4) break; $shown++;
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
                            <?php if(isset($row['service']) && $row['service']): ?><div style="font-size:12px;color:var(--cdg-muted);"><?php echo htmlspecialchars($row['service']); ?></div><?php endif; ?>
                        </td>
                        <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                        <td style="text-align:right;">
                            <?php if(isset($row['detail_link'])): ?><a href="<?php echo $row['detail_link']; ?>" class="cdg-btn-icon" title="Goruntule"><i class="bi bi-arrow-right"></i></a><?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-emoji-smile"></i></div>
            <p>Acik destek talebiniz yok.</p>
            <a href="<?php echo cdg_link('ac-ps-create-ticket-request'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm mt-2"><i class="bi bi-plus-lg"></i> Talep Olustur</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- HİZMETLERİMİZ - SERGİ BÖLÜMÜ -->
<div class="cdg-section-head mt-4" style="text-align:left;margin-bottom:24px;max-width:none;">
    <div class="cdg-eyebrow"><?php echo $has_services ? 'Daha Fazlasi' : 'Hizmetlerimiz'; ?></div>
    <h2 style="font-size:28px;"><?php echo $has_services ? 'Yeni hizmetler kesfedin' : 'Isletmenizin ihtiyaci olan tum cozumler'; ?></h2>
    <p>Hosting'den domain'e, sunucudan SMS'e kadar her seyi tek panelden yonetin.</p>
</div>

<div class="cdg-services-grid">
    <?php if($mod_hosting): ?>
    <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-service-card">
        <div class="cdg-service-icon"><i class="bi bi-hdd-network"></i></div>
        <h3>Web Hosting</h3>
        <p>NVMe SSD, LiteSpeed, ucretsiz SSL ve gunluk yedekleme dahil paketler.</p>
        <div class="cdg-service-cta">Paketleri Incele <i class="bi bi-arrow-right"></i></div>
    </a>
    <?php endif; ?>

    <?php if($mod_domain): ?>
    <a href="<?php echo cdg_link('domain'); ?>" class="cdg-service-card">
        <div class="cdg-service-icon"><i class="bi bi-globe2"></i></div>
        <h3>Alan Adi</h3>
        <p>500+ uzanti destegi. .com, .com.tr, .net, .org ve daha fazlasi uygun fiyatlarla.</p>
        <div class="cdg-service-cta">Domain Sorgula <i class="bi bi-arrow-right"></i></div>
    </a>
    <?php endif; ?>

    <?php if($mod_server): ?>
    <a href="<?php echo cdg_link('products', ['server']); ?>" class="cdg-service-card">
        <div class="cdg-service-icon"><i class="bi bi-server"></i></div>
        <h3>Sanal Sunucu</h3>
        <p>Yuksek performansli VPS ve dedicated sunucular. KVM, AMD EPYC, dedicated IP.</p>
        <div class="cdg-service-cta">Sunuculari Gor <i class="bi bi-arrow-right"></i></div>
    </a>
    <?php endif; ?>

    <a href="<?php echo cdg_link('ssl'); ?>" class="cdg-service-card">
        <div class="cdg-service-icon"><i class="bi bi-shield-lock"></i></div>
        <h3>SSL Sertifikalari</h3>
        <p>DV, OV, EV ve Wildcard sertifikalar. Sitenizin guvenligi ve SEO icin kritik.</p>
        <div class="cdg-service-cta">SSL Cesitleri <i class="bi bi-arrow-right"></i></div>
    </a>

    <?php if($mod_sms): ?>
    <a href="<?php echo cdg_link('products', ['sms']); ?>" class="cdg-service-card">
        <div class="cdg-service-icon"><i class="bi bi-chat-dots"></i></div>
        <h3>SMS Hizmetleri</h3>
        <p>Toplu SMS gonderimi, OTP, bilgilendirme. Yuksek teslimat orani ve API destegi.</p>
        <div class="cdg-service-cta">SMS Paketleri <i class="bi bi-arrow-right"></i></div>
    </a>
    <?php endif; ?>

    <a href="<?php echo cdg_link('contact'); ?>" class="cdg-service-card">
        <div class="cdg-service-icon"><i class="bi bi-stars"></i></div>
        <h3>Ozel Cozumler</h3>
        <p>Kurumsal ihtiyaclariniza ozel hosting, sunucu ve yazilim cozumleri sunuyoruz.</p>
        <div class="cdg-service-cta">Iletisime Gec <i class="bi bi-arrow-right"></i></div>
    </a>
</div>

<?php if(!$has_services): ?>
<!-- AKTİF HİZMET YOKSA: HER PAKET'TEN BİR ÖRNEK -->
<div class="cdg-card mt-4" style="background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;border:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;padding:16px 0;">
        <div>
            <div style="font-size:13px;text-transform:uppercase;letter-spacing:1px;opacity:0.85;font-weight:600;margin-bottom:8px;">
                <i class="bi bi-rocket-takeoff"></i> Hosting Yolculugunuz Basliyor
            </div>
            <h3 style="font-size:24px;font-weight:700;margin:0 0 8px;color:#fff;">Ilk hizmetinizi alin, %30 kazanin</h3>
            <p style="opacity:0.9;margin:0;">30 gun para iade garantisi · Ucretsiz SSL · Ucretsiz tasima</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-white">
                <i class="bi bi-hdd-network"></i> Hosting Al
            </a>
            <a href="<?php echo cdg_link('domain'); ?>" class="cdg-btn cdg-btn-ghost">
                <i class="bi bi-globe2"></i> Domain Al
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- DOMAINLER (varsa, küçük versiyonu) -->
<?php if(isset($domain_orders) && is_array($domain_orders) && !empty($domain_orders['all'])): ?>
<div class="cdg-card mt-3">
    <div class="cdg-card-head">
        <h3><i class="bi bi-globe2"></i> Domainlerim</h3>
        <a href="<?php echo cdg_link('ac-ps-products-t', ['domain']); ?>" style="font-size:13px;">Tumu &rarr;</a>
    </div>
    <div class="cdg-table-wrap">
        <table class="cdg-table">
            <thead><tr><th>Alan Adi</th><th style="text-align:center;">Bitis Tarihi</th><th style="text-align:center;">Durum</th></tr></thead>
            <tbody>
            <?php
            $shown = 0;
            foreach($domain_orders['all'] as $row) {
                if($shown >= 4) break; $shown++;
                $name = isset($row['name']) ? $row['name'] : '-';
                $duedate = isset($row['duedate']) ? $row['duedate'] : '';
                $duedate_format = '-';
                if($duedate && !in_array(substr($duedate,0,4), ['1881','1970'])) {
                    if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
                        $duedate_format = DateManager::format(Config::get("options/date-format"), $duedate);
                    } else { $duedate_format = date('d.m.Y', strtotime($duedate)); }
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
                    <td style="text-align:center;font-size:13px;"><?php echo $duedate_format; ?></td>
                    <td style="text-align:center;font-size:12px;"><?php echo $status_html; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
