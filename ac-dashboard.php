<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega Dashboard - Sıfırdan Yazıldı
 * Inline CSS ile self-contained (dış CSS'e bağımlı değil)
 * WiseCP runtime variables: $domain_orders, $orders, $tickets, $news, $links, $acsidebar_links
 */

$hoptions = ["datatables"];

// === Yardımcı: Link üretici ===
if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug . ($params ? '/' . implode('/', $params) : '');
    }
}

// === Kullanıcı bilgileri ===
$user_name    = 'Müşteri';
$user_email   = '';
$user_balance = '0,00';
$user_initial = 'M';

if(class_exists('User') && isset(User::$init->info)) {
    $info = User::$init->info;
    $first = isset($info['name']) ? trim($info['name']) : '';
    $last  = isset($info['surname']) ? trim($info['surname']) : '';
    $full  = trim($first . ' ' . $last);
    if(!$full) $full = isset($info['username']) ? $info['username'] : 'Müşteri';
    $user_name = $full;
    $user_email = $info['email'] ?? '';

    $bal_amount = isset($info['balance']) ? $info['balance'] : 0;
    $bal_cid = isset($info['balance_cid']) ? $info['balance_cid'] : 0;
    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $bal_cid) {
        $user_balance = Money::formatter_symbol($bal_amount, $bal_cid);
    } else {
        $user_balance = number_format((float)$bal_amount, 2, ',', '.') . ' ₺';
    }

    $user_initial = strtoupper(mb_substr($full, 0, 1, 'UTF-8'));
}

// === İstatistikler (WiseCP runtime'dan al, yoksa say) ===
$count_active_products = 0;
$count_total_products  = 0;
$count_unpaid_invoices = 0;
$count_open_tickets    = 0;

// Aktif ürünler
if(isset($orders) && is_array($orders)) {
    foreach($orders as $o) {
        $count_total_products++;
        if(isset($o['status']) && in_array($o['status'], ['active', 'Active', 'aktif'])) {
            $count_active_products++;
        }
    }
}

// Faturalar (WiseCP $unpaid_invoices set ediyorsa)
if(isset($unpaid_invoices) && is_array($unpaid_invoices)) {
    $count_unpaid_invoices = count($unpaid_invoices);
} elseif(isset($invoices) && is_array($invoices)) {
    foreach($invoices as $inv) {
        if(isset($inv['status']) && in_array($inv['status'], ['unpaid', 'Unpaid', 'odenmemis'])) {
            $count_unpaid_invoices++;
        }
    }
}

// Talepler
if(isset($tickets) && is_array($tickets)) {
    foreach($tickets as $t) {
        if(isset($t['status']) && in_array($t['status'], ['Customer-Reply', 'open', 'Open', 'acik', 'Answered'])) {
            $count_open_tickets++;
        }
    }
}

// === Selamlama ===
$hour = (int)date('H');
if($hour >= 5 && $hour < 12)       $greeting = 'Günaydın';
elseif($hour >= 12 && $hour < 18)  $greeting = 'İyi günler';
else                               $greeting = 'İyi akşamlar';

// === Linkler ===
$products_url = isset($acsidebar_links['products']) ? $acsidebar_links['products'] : cdg_link('products');
$invoices_url = isset($acsidebar_links['invoices']) ? $acsidebar_links['invoices'] : cdg_link('invoices');
$tickets_url  = isset($acsidebar_links['tickets'])  ? $acsidebar_links['tickets']  : cdg_link('tickets');
$balance_url  = isset($acsidebar_links['balance'])  ? $acsidebar_links['balance']  : cdg_link('balance');
$domains_url  = isset($acsidebar_links['domains'])  ? $acsidebar_links['domains']  : cdg_link('domains');
$shop_url     = cdg_link('products', ['hosting']);
$contact_url  = cdg_link('contact');
?>

<!-- ==================== INLINE CSS (self-contained) ==================== -->
<style>
.cdg-d {
    --d-primary: #1e40af;
    --d-primary-2: #3b82f6;
    --d-success: #10b981;
    --d-warning: #f59e0b;
    --d-danger: #ef4444;
    --d-info: #06b6d4;
    --d-purple: #8b5cf6;
    --d-bg: #f8fafc;
    --d-card: #ffffff;
    --d-text: #0f172a;
    --d-muted: #64748b;
    --d-border: #e2e8f0;
    --d-radius: 14px;
    --d-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --d-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--d-text);
    background: var(--d-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-d *, .cdg-d *::before, .cdg-d *::after { box-sizing: border-box; }
.cdg-d a { text-decoration: none; color: inherit; }

.cdg-d-wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* === HERO === */
.cdg-d-hero {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
    border-radius: 18px;
    padding: 32px 36px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
}
.cdg-d-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(252,211,77,0.20), transparent 70%);
    pointer-events: none;
}
.cdg-d-hero-row {
    display: flex; align-items: center; gap: 24px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-d-avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1e3a8a;
    display: grid; place-items: center;
    font-size: 28px; font-weight: 800;
    box-shadow: 0 8px 24px rgba(0,0,0,0.20);
    flex-shrink: 0;
    position: relative;
}
.cdg-d-avatar::after {
    content: '';
    position: absolute;
    inset: -4px;
    border: 2px solid rgba(255,255,255,0.30);
    border-radius: 50%;
}
.cdg-d-greet {
    flex: 1; min-width: 240px;
}
.cdg-d-greet-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 5px 12px;
    background: rgba(255,255,255,0.18);
    border-radius: 99px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    backdrop-filter: blur(10px);
}
.cdg-d-greet-eyebrow .dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #34d399;
    box-shadow: 0 0 8px #10b981;
    animation: cdgPulseDash 2s ease-in-out infinite;
}
@keyframes cdgPulseDash {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.85); }
}
.cdg-d-greet h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 4px;
    letter-spacing: -0.5px;
}
.cdg-d-greet p {
    font-size: 14px;
    opacity: 0.85;
    margin: 0;
}
.cdg-d-hero-actions {
    display: flex; gap: 10px; flex-wrap: wrap;
}
.cdg-d-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
}
.cdg-d-btn-gold {
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1e3a8a;
    box-shadow: 0 6px 18px rgba(252,211,77,0.30);
}
.cdg-d-btn-gold:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(252,211,77,0.45);
    color: #1e3a8a;
}
.cdg-d-btn-ghost {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.30);
    backdrop-filter: blur(10px);
}
.cdg-d-btn-ghost:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
}

/* === STATS GRID === */
.cdg-d-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.cdg-d-stat {
    background: var(--d-card);
    border: 1px solid var(--d-border);
    border-radius: var(--d-radius);
    padding: 20px;
    box-shadow: var(--d-shadow);
    transition: all 0.2s;
    display: flex; align-items: center; gap: 14px;
}
.cdg-d-stat:hover {
    box-shadow: var(--d-shadow-lg);
    transform: translateY(-2px);
}
.cdg-d-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: grid; place-items: center;
    color: #fff;
    font-size: 22px;
    flex-shrink: 0;
}
.cdg-d-stat-body { flex: 1; min-width: 0; }
.cdg-d-stat-label {
    font-size: 11px;
    color: var(--d-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 4px;
}
.cdg-d-stat-value {
    font-size: 24px;
    font-weight: 800;
    color: var(--d-text);
    line-height: 1;
}
.cdg-d-stat-link {
    font-size: 12px;
    color: var(--d-primary);
    font-weight: 600;
    margin-top: 6px;
    display: inline-block;
}
.cdg-d-stat-link:hover { color: var(--d-primary-2); }

/* === QUICK GRID === */
.cdg-d-quick {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
.cdg-d-quick-item {
    background: var(--d-card);
    border: 1px solid var(--d-border);
    border-radius: var(--d-radius);
    padding: 18px 12px;
    text-align: center;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}
.cdg-d-quick-item:hover {
    border-color: var(--d-primary);
    transform: translateY(-2px);
    box-shadow: var(--d-shadow-lg);
    color: var(--d-primary);
}
.cdg-d-quick-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: grid; place-items: center;
    font-size: 20px;
    color: #fff;
}
.cdg-d-quick-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--d-text);
}

/* === TWO-COL CARDS === */
.cdg-d-grid-2 {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
.cdg-d-card {
    background: var(--d-card);
    border: 1px solid var(--d-border);
    border-radius: var(--d-radius);
    box-shadow: var(--d-shadow);
    overflow: hidden;
}
.cdg-d-card-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 22px;
    border-bottom: 1px solid var(--d-border);
}
.cdg-d-card-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--d-text);
    margin: 0;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-d-card-title i { color: var(--d-primary); font-size: 18px; }
.cdg-d-card-link {
    font-size: 12px;
    color: var(--d-primary);
    font-weight: 600;
}
.cdg-d-card-body { padding: 18px 22px; }

/* === LIST STYLE === */
.cdg-d-list { list-style: none; padding: 0; margin: 0; }
.cdg-d-list li {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--d-border);
    font-size: 14px;
}
.cdg-d-list li:last-child { border-bottom: 0; padding-bottom: 0; }
.cdg-d-list li:first-child { padding-top: 0; }
.cdg-d-list-name {
    color: var(--d-text);
    font-weight: 600;
}
.cdg-d-list-meta {
    color: var(--d-muted);
    font-size: 12px;
}
.cdg-d-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.cdg-d-badge-success { background: #d1fae5; color: #065f46; }
.cdg-d-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-d-badge-danger  { background: #fee2e2; color: #991b1b; }
.cdg-d-badge-info    { background: #dbeafe; color: #1e40af; }

/* === EMPTY STATE === */
.cdg-d-empty {
    text-align: center;
    padding: 32px 20px;
    color: var(--d-muted);
}
.cdg-d-empty i {
    font-size: 36px;
    color: var(--d-border);
    margin-bottom: 10px;
}
.cdg-d-empty p { font-size: 14px; margin: 0; }

/* === PROMO === */
.cdg-d-promo {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1px solid #fcd34d;
    border-radius: var(--d-radius);
    padding: 20px 24px;
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 24px;
}
.cdg-d-promo-icon {
    font-size: 32px;
    color: #b45309;
    flex-shrink: 0;
}
.cdg-d-promo-text { flex: 1; }
.cdg-d-promo-text h3 {
    font-size: 16px;
    font-weight: 800;
    color: #78350f;
    margin: 0 0 4px;
}
.cdg-d-promo-text p {
    font-size: 13px;
    color: #92400e;
    margin: 0;
}

/* === RESPONSIVE === */
@media (max-width: 992px) {
    .cdg-d-stats { grid-template-columns: repeat(2, 1fr); }
    .cdg-d-quick { grid-template-columns: repeat(3, 1fr); }
    .cdg-d-grid-2 { grid-template-columns: 1fr; }
}
@media (max-width: 540px) {
    .cdg-d-hero { padding: 24px 20px; }
    .cdg-d-greet h1 { font-size: 22px; }
    .cdg-d-stats { grid-template-columns: 1fr; }
    .cdg-d-quick { grid-template-columns: repeat(2, 1fr); }
    .cdg-d-promo { flex-direction: column; text-align: center; }
}
</style>

<!-- ==================== HTML ==================== -->
<div class="cdg-d">
<div class="cdg-d-wrap">

    <!-- HERO -->
    <section class="cdg-d-hero">
        <div class="cdg-d-hero-row">
            <div class="cdg-d-avatar"><?php echo htmlspecialchars($user_initial); ?></div>
            <div class="cdg-d-greet">
                <span class="cdg-d-greet-eyebrow">
                    <span class="dot"></span>
                    <span><?php echo $count_active_products > 0 ? 'Aktif Üye' : 'Hoş Geldiniz'; ?></span>
                </span>
                <h1><?php echo $greeting; ?>, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p>Hizmetlerinizi ve faturalarınızı tek panelden yönetin.</p>
            </div>
            <div class="cdg-d-hero-actions">
                <a href="<?php echo htmlspecialchars($shop_url); ?>" class="cdg-d-btn cdg-d-btn-gold">
                    <i class="bi bi-bag-plus-fill"></i> Yeni Hizmet Al
                </a>
                <a href="<?php echo htmlspecialchars($tickets_url); ?>" class="cdg-d-btn cdg-d-btn-ghost">
                    <i class="bi bi-headset"></i> Destek Al
                </a>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="cdg-d-stats">
        <div class="cdg-d-stat">
            <div class="cdg-d-stat-icon" style="background:linear-gradient(135deg,#10b981,#34d399);">
                <i class="bi bi-hdd-network-fill"></i>
            </div>
            <div class="cdg-d-stat-body">
                <div class="cdg-d-stat-label">Aktif Hizmet</div>
                <div class="cdg-d-stat-value"><?php echo (int)$count_active_products; ?></div>
                <a href="<?php echo htmlspecialchars($products_url); ?>" class="cdg-d-stat-link">Tümünü Gör →</a>
            </div>
        </div>

        <div class="cdg-d-stat">
            <div class="cdg-d-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="cdg-d-stat-body">
                <div class="cdg-d-stat-label">Bekleyen Fatura</div>
                <div class="cdg-d-stat-value"><?php echo (int)$count_unpaid_invoices; ?></div>
                <a href="<?php echo htmlspecialchars($invoices_url); ?>" class="cdg-d-stat-link">Faturalarım →</a>
            </div>
        </div>

        <div class="cdg-d-stat">
            <div class="cdg-d-stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="cdg-d-stat-body">
                <div class="cdg-d-stat-label">Açık Talep</div>
                <div class="cdg-d-stat-value"><?php echo (int)$count_open_tickets; ?></div>
                <a href="<?php echo htmlspecialchars($tickets_url); ?>" class="cdg-d-stat-link">Destek Talepleri →</a>
            </div>
        </div>

        <div class="cdg-d-stat">
            <div class="cdg-d-stat-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee);">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="cdg-d-stat-body">
                <div class="cdg-d-stat-label">Bakiyem</div>
                <div class="cdg-d-stat-value" style="font-size:20px;"><?php echo htmlspecialchars($user_balance); ?></div>
                <a href="<?php echo htmlspecialchars($balance_url); ?>" class="cdg-d-stat-link">Yükleme Yap →</a>
            </div>
        </div>
    </section>

    <!-- QUICK ACTIONS -->
    <section class="cdg-d-quick">
        <a href="<?php echo htmlspecialchars($products_url); ?>" class="cdg-d-quick-item">
            <div class="cdg-d-quick-icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-hdd-stack"></i></div>
            <span class="cdg-d-quick-label">Hizmetlerim</span>
        </a>
        <a href="<?php echo htmlspecialchars($domains_url); ?>" class="cdg-d-quick-item">
            <div class="cdg-d-quick-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-globe2"></i></div>
            <span class="cdg-d-quick-label">Domainlerim</span>
        </a>
        <a href="<?php echo htmlspecialchars($invoices_url); ?>" class="cdg-d-quick-item">
            <div class="cdg-d-quick-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-receipt-cutoff"></i></div>
            <span class="cdg-d-quick-label">Faturalar</span>
        </a>
        <a href="<?php echo htmlspecialchars($tickets_url); ?>" class="cdg-d-quick-item">
            <div class="cdg-d-quick-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="bi bi-chat-dots"></i></div>
            <span class="cdg-d-quick-label">Destek</span>
        </a>
        <a href="<?php echo htmlspecialchars($balance_url); ?>" class="cdg-d-quick-item">
            <div class="cdg-d-quick-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee);"><i class="bi bi-wallet"></i></div>
            <span class="cdg-d-quick-label">Bakiye</span>
        </a>
        <a href="<?php echo htmlspecialchars($shop_url); ?>" class="cdg-d-quick-item">
            <div class="cdg-d-quick-icon" style="background:linear-gradient(135deg,#ef4444,#f87171);"><i class="bi bi-bag-plus"></i></div>
            <span class="cdg-d-quick-label">Yeni Sipariş</span>
        </a>
    </section>

    <!-- IKI SUTUNLU: Aktif Hizmetler + Son Aktiviteler -->
    <section class="cdg-d-grid-2">

        <!-- Aktif Hizmetler -->
        <div class="cdg-d-card">
            <div class="cdg-d-card-head">
                <h3 class="cdg-d-card-title"><i class="bi bi-hdd-network-fill"></i> Aktif Hizmetlerim</h3>
                <a href="<?php echo htmlspecialchars($products_url); ?>" class="cdg-d-card-link">Tümünü Gör →</a>
            </div>
            <div class="cdg-d-card-body">
                <?php
                $shown_orders = 0;
                if(isset($orders) && is_array($orders) && count($orders) > 0):
                ?>
                <ul class="cdg-d-list">
                    <?php foreach($orders as $order):
                        if($shown_orders >= 5) break;
                        $shown_orders++;

                        $order_name = isset($order['name']) ? $order['name'] : (isset($order['title']) ? $order['title'] : 'Hizmet #' . ($order['id'] ?? '?'));
                        $order_status = $order['status'] ?? 'unknown';

                        $badge_class = 'cdg-d-badge-info';
                        $status_text = $order_status;
                        if(in_array($order_status, ['active', 'Active', 'aktif'])) {
                            $badge_class = 'cdg-d-badge-success';
                            $status_text = 'Aktif';
                        } elseif(in_array($order_status, ['suspended', 'Suspended'])) {
                            $badge_class = 'cdg-d-badge-warning';
                            $status_text = 'Askıda';
                        } elseif(in_array($order_status, ['cancelled', 'Cancelled', 'expired'])) {
                            $badge_class = 'cdg-d-badge-danger';
                            $status_text = 'İptal';
                        } elseif(in_array($order_status, ['inprocess', 'pending'])) {
                            $badge_class = 'cdg-d-badge-warning';
                            $status_text = 'Onay Bekliyor';
                        }

                        $detail_link = $order['detail_link'] ?? '#';
                    ?>
                    <li>
                        <div>
                            <a href="<?php echo htmlspecialchars($detail_link); ?>" class="cdg-d-list-name"><?php echo htmlspecialchars($order_name); ?></a>
                            <?php if(!empty($order['duedate'])): ?>
                            <div class="cdg-d-list-meta">Bitiş: <?php echo htmlspecialchars($order['duedate']); ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="cdg-d-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status_text); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="cdg-d-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Henüz aktif hizmetiniz yok.</p>
                    <a href="<?php echo htmlspecialchars($shop_url); ?>" class="cdg-d-btn cdg-d-btn-gold" style="margin-top:14px;color:#1e3a8a;">
                        <i class="bi bi-bag-plus"></i> İlk Hizmetinizi Alın
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Son Talepler -->
        <div class="cdg-d-card">
            <div class="cdg-d-card-head">
                <h3 class="cdg-d-card-title"><i class="bi bi-chat-dots-fill"></i> Son Talepler</h3>
                <a href="<?php echo htmlspecialchars($tickets_url); ?>" class="cdg-d-card-link">Tümü →</a>
            </div>
            <div class="cdg-d-card-body">
                <?php
                $shown_tickets = 0;
                if(isset($tickets) && is_array($tickets) && count($tickets) > 0):
                ?>
                <ul class="cdg-d-list">
                    <?php foreach($tickets as $ticket):
                        if($shown_tickets >= 4) break;
                        $shown_tickets++;

                        $ticket_subject = $ticket['subject'] ?? $ticket['title'] ?? 'Talep #' . ($ticket['id'] ?? '?');
                        $ticket_status = $ticket['status'] ?? 'unknown';

                        $tbadge = 'cdg-d-badge-info';
                        $tstatus_text = $ticket_status;
                        if($ticket_status === 'Customer-Reply') { $tbadge = 'cdg-d-badge-warning'; $tstatus_text = 'Yanıt Bekliyor'; }
                        elseif($ticket_status === 'Answered')   { $tbadge = 'cdg-d-badge-success'; $tstatus_text = 'Yanıtlandı'; }
                        elseif($ticket_status === 'Closed')     { $tbadge = 'cdg-d-badge-info'; $tstatus_text = 'Kapalı'; }

                        $tdetail = $ticket['detail_link'] ?? '#';
                    ?>
                    <li>
                        <div>
                            <a href="<?php echo htmlspecialchars($tdetail); ?>" class="cdg-d-list-name"><?php echo htmlspecialchars($ticket_subject); ?></a>
                        </div>
                        <span class="cdg-d-badge <?php echo $tbadge; ?>"><?php echo htmlspecialchars($tstatus_text); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="cdg-d-empty">
                    <i class="bi bi-chat-square-text"></i>
                    <p>Açık destek talebiniz yok.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <!-- DUYURU PROMO -->
    <?php if(isset($news) && is_array($news) && count($news) > 0): ?>
    <section class="cdg-d-card" style="margin-bottom:24px;">
        <div class="cdg-d-card-head">
            <h3 class="cdg-d-card-title"><i class="bi bi-megaphone-fill"></i> Duyurular</h3>
        </div>
        <div class="cdg-d-card-body">
            <ul class="cdg-d-list">
                <?php $shown_news = 0; foreach($news as $n):
                    if($shown_news >= 3) break;
                    $shown_news++;
                    $n_title = $n['title'] ?? 'Duyuru';
                    $n_date  = $n['date'] ?? $n['created_at'] ?? '';
                    $n_link  = $n['link'] ?? $n['detail_link'] ?? '#';
                ?>
                <li>
                    <div>
                        <a href="<?php echo htmlspecialchars($n_link); ?>" class="cdg-d-list-name"><?php echo htmlspecialchars($n_title); ?></a>
                        <?php if($n_date): ?><div class="cdg-d-list-meta"><?php echo htmlspecialchars($n_date); ?></div><?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <!-- DESTEK PROMO -->
    <section class="cdg-d-promo">
        <div class="cdg-d-promo-icon"><i class="bi bi-headset"></i></div>
        <div class="cdg-d-promo-text">
            <h3>Yardıma mı ihtiyacınız var?</h3>
            <p>Uzman ekibimiz 7/24 destek sağlıyor. WhatsApp veya telefon ile hemen ulaşın.</p>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <a href="https://wa.me/905102204206" target="_blank" rel="noopener" class="cdg-d-btn" style="background:#25d366;color:#fff;">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </a>
            <a href="tel:+905102204206" class="cdg-d-btn" style="background:#1e40af;color:#fff;">
                <i class="bi bi-telephone-fill"></i> Ara
            </a>
        </div>
    </section>

</div>
</div>
