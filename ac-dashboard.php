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

// Kullanici bilgileri
$user_name    = 'Müşteri';
$user_email   = '';
$user_balance = 0; $user_bal_cid = 0; $user_balance_str = '0,00';
if(class_exists('User') && isset(User::$init->info)) {
    $info = User::$init->info;
    $user_name = (isset($info['name']) ? $info['name'] : '') . (isset($info['surname']) ? ' ' . $info['surname'] : '');
    if(!trim($user_name)) $user_name = isset($info['username']) ? $info['username'] : 'Müşteri';
    $user_email = $info['email'] ?? '';
    $user_balance = isset($info['balance']) ? $info['balance'] : 0;
    $user_bal_cid = isset($info['balance_cid']) ? $info['balance_cid'] : 0;
    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $user_bal_cid) {
        $user_balance_str = Money::formatter_symbol($user_balance, $user_bal_cid);
    }
}

// İstatistikler
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

$mod_hosting = isset($pg_activation['hosting']) ? !empty($pg_activation['hosting']) : true;
$mod_domain  = isset($pg_activation['domain'])  ? !empty($pg_activation['domain'])  : true;

$has_services = $count_active_products > 0;

// İlk harf (avatar için)
$user_initial = mb_strtoupper(mb_substr($user_name, 0, 1, 'UTF-8'), 'UTF-8');
$user_greeting_time = (int)date('H');
if($user_greeting_time < 12)      $greeting = 'Günaydın';
elseif($user_greeting_time < 18)  $greeting = 'İyi günler';
else                               $greeting = 'İyi akşamlar';
?>

<!-- 1. PREMIUM HERO + KARŞILAMA -->
<section class="cdg-dash-hero">
    <div class="cdg-dash-hero-bg">
        <div class="cdg-dash-mesh"></div>
        <div class="cdg-dash-pattern"></div>
        <div class="cdg-dash-orb cdg-dash-orb-gold"></div>
        <div class="cdg-dash-orb cdg-dash-orb-blue"></div>
    </div>
    <div class="cdg-dash-hero-inner">
        <div class="cdg-dash-greeting">
            <div class="cdg-dash-avatar">
                <span><?php echo htmlspecialchars($user_initial); ?></span>
                <div class="cdg-dash-avatar-ring"></div>
            </div>
            <div class="cdg-dash-greeting-text">
                <div class="cdg-dash-eyebrow">
                    <i class="bi bi-gem"></i>
                    <span><?php echo $greeting; ?>, <?php echo $has_services ? 'Premium Üye' : 'Hoş Geldiniz'; ?></span>
                </div>
                <h1><?php echo $greeting; ?>, <span class="cdg-dash-name"><?php echo htmlspecialchars($user_name); ?></span></h1>
                <p>Hizmetleriniz, faturalarınız ve destek talepleriniz <strong>tek panelden</strong> yönetilir.</p>
            </div>
        </div>
        <div class="cdg-dash-hero-actions">
            <?php if($mod_hosting): ?>
            <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-dash-action-btn primary">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Yeni Hizmet Al</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo cdg_link('contact'); ?>" class="cdg-dash-action-btn outline">
                <i class="bi bi-headset"></i>
                <span>Destek Al</span>
            </a>
        </div>
    </div>
</section>

<!-- 2. STAT CARDS (PREMIUM GLASSMORPHISM) -->
<section class="cdg-dash-stats">
    <div class="cdg-dash-stat-card">
        <a href="<?php echo cdg_link('my-products'); ?>" class="cdg-dash-stat-link">
            <div class="cdg-dash-stat-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="cdg-dash-stat-body">
                <div class="cdg-dash-stat-label">Aktif Hizmetler</div>
                <div class="cdg-dash-stat-value"><?php echo $count_active_products; ?></div>
                <div class="cdg-dash-stat-meta">
                    <i class="bi bi-arrow-right"></i> Tüm hizmetlerim
                </div>
            </div>
        </a>
    </div>

    <div class="cdg-dash-stat-card">
        <a href="<?php echo cdg_link('invoices'); ?>" class="cdg-dash-stat-link">
            <div class="cdg-dash-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="cdg-dash-stat-body">
                <div class="cdg-dash-stat-label">Bekleyen Faturalar</div>
                <div class="cdg-dash-stat-value"><?php echo $pending_invoices; ?></div>
                <div class="cdg-dash-stat-meta">
                    <i class="bi bi-arrow-right"></i> Faturalarıma git
                </div>
            </div>
        </a>
    </div>

    <div class="cdg-dash-stat-card">
        <a href="<?php echo cdg_link('tickets'); ?>" class="cdg-dash-stat-link">
            <div class="cdg-dash-stat-icon" style="background:linear-gradient(135deg,#10b981,#34d399);">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="cdg-dash-stat-body">
                <div class="cdg-dash-stat-label">Açık Talepler</div>
                <div class="cdg-dash-stat-value"><?php echo $count_open_tickets; ?></div>
                <div class="cdg-dash-stat-meta">
                    <i class="bi bi-arrow-right"></i> Destek talepleri
                </div>
            </div>
        </a>
    </div>

    <div class="cdg-dash-stat-card">
        <a href="<?php echo cdg_link('balance'); ?>" class="cdg-dash-stat-link">
            <div class="cdg-dash-stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                <i class="bi bi-wallet-fill"></i>
            </div>
            <div class="cdg-dash-stat-body">
                <div class="cdg-dash-stat-label">Bakiyem</div>
                <div class="cdg-dash-stat-value cdg-dash-stat-balance"><?php echo htmlspecialchars($user_balance_str); ?></div>
                <div class="cdg-dash-stat-meta">
                    <i class="bi bi-arrow-right"></i> Yükleme yap
                </div>
            </div>
        </a>
    </div>
</section>

<!-- 3. AKTIF HIZMETLERIM (varsa) -->
<?php if($has_services && isset($orders) && is_array($orders) && count($orders) > 0): ?>
<section class="cdg-dash-section">
    <div class="cdg-dash-section-head">
        <div>
            <h2><i class="bi bi-collection-fill"></i> Aktif Hizmetlerim</h2>
            <p>Hizmetlerinizi yönetin, yenileyin veya yükseltin.</p>
        </div>
        <a href="<?php echo cdg_link('my-products'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
            Tümünü Gör <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="cdg-dash-services-grid">
        <?php
        $shown = 0;
        foreach($orders as $oid => $row) {
            if($shown >= 4) break;
            $name = isset($row['name']) ? $row['name'] : 'Hizmet';
            $sub_info = isset($row['options']['category_name']) ? $row['options']['category_name'] : '';
            $duedate = isset($row['duedate']) ? $row['duedate'] : 0;
            $duedate_format = $duedate ? date('d.m.Y', $duedate) : '—';
            $status = $row['status'] ?? 'active';
            $status_color = ['active' => '#10b981', 'expired' => '#ef4444', 'pending' => '#f59e0b', 'suspended' => '#94a3b8'];
            $color = $status_color[$status] ?? '#10b981';
            $shown++;
        ?>
        <div class="cdg-dash-service-card">
            <div class="cdg-dash-service-status" style="background:<?php echo $color; ?>;"></div>
            <div class="cdg-dash-service-head">
                <div class="cdg-dash-service-icon">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h3><?php echo htmlspecialchars($name); ?></h3>
                    <small><?php echo htmlspecialchars($sub_info); ?></small>
                </div>
            </div>
            <div class="cdg-dash-service-meta">
                <div class="cdg-dash-service-meta-row">
                    <span class="lbl">Durum</span>
                    <span class="val" style="color:<?php echo $color; ?>;font-weight:700;">
                        <?php echo strtoupper($status); ?>
                    </span>
                </div>
                <div class="cdg-dash-service-meta-row">
                    <span class="lbl">Bitiş</span>
                    <span class="val"><?php echo $duedate_format; ?></span>
                </div>
                <div class="cdg-dash-service-meta-row">
                    <span class="lbl">Sipariş</span>
                    <span class="val">#<?php echo $oid; ?></span>
                </div>
            </div>
            <?php if(isset($row['detail_link'])): ?>
            <a href="<?php echo $row['detail_link']; ?>" class="cdg-btn cdg-btn-primary cdg-btn-block cdg-btn-sm">
                <i class="bi bi-gear-fill"></i> Yönet
            </a>
            <?php endif; ?>
        </div>
        <?php } ?>
    </div>
</section>
<?php endif; ?>

<!-- 4. BOŞ DURUM (Hizmet yoksa) -->
<?php if(!$has_services): ?>
<section class="cdg-dash-section">
    <div class="cdg-dash-empty">
        <div class="cdg-dash-empty-icon">
            <i class="bi bi-rocket-takeoff"></i>
        </div>
        <h2>Yolculuğunuzu <span class="cdg-text-gradient">başlatın</span></h2>
        <p>Henüz aktif hizmetiniz yok. Hosting, domain veya yazılım hizmetlerimizle profesyonel yolculuğunuzu başlatın.</p>
        <div class="cdg-dash-empty-actions">
            <?php if($mod_hosting): ?>
            <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-primary cdg-btn-glow">
                <i class="bi bi-hdd-network"></i> Hosting Paketleri
            </a>
            <?php endif; ?>
            <?php if($mod_domain): ?>
            <a href="<?php echo cdg_link('domain'); ?>" class="cdg-btn cdg-btn-outline">
                <i class="bi bi-globe2"></i> Domain Sorgula
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. HIZLI ERIŞIM KARTLARI -->
<section class="cdg-dash-section">
    <div class="cdg-dash-section-head">
        <div>
            <h2><i class="bi bi-grid-fill"></i> Hızlı Erişim</h2>
            <p>En sık kullandığınız bölümlere tek tıkla erişin.</p>
        </div>
    </div>
    <div class="cdg-dash-quick-grid">
        <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-dash-quick-card">
            <div class="cdg-dash-quick-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-hdd-network"></i></div>
            <h4>Hosting Al</h4>
            <p>NVMe SSD + LiteSpeed paketleri</p>
        </a>
        <a href="<?php echo cdg_link('domain'); ?>" class="cdg-dash-quick-card">
            <div class="cdg-dash-quick-icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-globe2"></i></div>
            <h4>Domain Sorgula</h4>
            <p>500+ uzantı + ücretsiz transfer</p>
        </a>
        <a href="<?php echo cdg_link('invoices'); ?>" class="cdg-dash-quick-card">
            <div class="cdg-dash-quick-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-receipt"></i></div>
            <h4>Faturalarım</h4>
            <p>Bekleyen ve ödenmiş faturalar</p>
        </a>
        <a href="<?php echo cdg_link('balance'); ?>" class="cdg-dash-quick-card">
            <div class="cdg-dash-quick-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="bi bi-wallet-fill"></i></div>
            <h4>Bakiye Yükle</h4>
            <p>Hızlı yenileme için bakiye</p>
        </a>
        <a href="<?php echo cdg_link('tickets'); ?>" class="cdg-dash-quick-card">
            <div class="cdg-dash-quick-icon" style="background:linear-gradient(135deg,#ec4899,#f472b6);"><i class="bi bi-headset"></i></div>
            <h4>Destek Talebi</h4>
            <p>Teknik konularda yardım</p>
        </a>
        <a href="<?php echo cdg_link('my-account'); ?>" class="cdg-dash-quick-card">
            <div class="cdg-dash-quick-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee);"><i class="bi bi-person-circle"></i></div>
            <h4>Hesap Bilgilerim</h4>
            <p>Profil ve güvenlik ayarları</p>
        </a>
    </div>
</section>

<!-- 6. PREMIUM TANITIM BANTLARI -->
<section class="cdg-dash-promo-section">
    <div class="cdg-dash-promo-grid">
        <div class="cdg-dash-promo cdg-dash-promo-1">
            <div class="cdg-dash-promo-content">
                <div class="cdg-dash-promo-badge"><i class="bi bi-stars"></i> Avantaj</div>
                <h3>Tüm hizmetlerinizi tek panelden yönetin</h3>
                <p>Hosting, domain, fatura, destek — hepsi parmaklarınızın ucunda.</p>
            </div>
            <div class="cdg-dash-promo-shape"></div>
        </div>
        <div class="cdg-dash-promo cdg-dash-promo-2">
            <div class="cdg-dash-promo-content">
                <div class="cdg-dash-promo-badge"><i class="bi bi-shield-fill-check"></i> Güvenlik</div>
                <h3>İki adımlı doğrulama desteği</h3>
                <p>Hesabınızı 2FA ile koruyun, hesap bilgilerinizden ayarlayın.</p>
            </div>
            <div class="cdg-dash-promo-shape"></div>
        </div>
    </div>
</section>
