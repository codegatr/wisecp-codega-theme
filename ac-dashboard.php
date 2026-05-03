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

// === Kullanıcı bilgileri ===
$user_name    = 'Müşteri';
$user_email   = '';
$user_balance = '0,00';
$user_initial = 'M';

// WiseCP runtime: $udata primary (Classic standardi), User::$init->info fallback
$cdg_uinfo = isset($udata) && is_array($udata) ? $udata : [];
if(empty($cdg_uinfo) && class_exists('User') && isset(User::$init->info)) {
    $cdg_uinfo = User::$init->info;
}

if(!empty($cdg_uinfo)) {
    // full_name primary, name+surname fallback, ad+soyad, vs.
    $full = isset($cdg_uinfo['full_name']) ? trim($cdg_uinfo['full_name']) : '';
    if(!$full) {
        $first = isset($cdg_uinfo['name']) ? trim($cdg_uinfo['name']) : '';
        $last  = isset($cdg_uinfo['surname']) ? trim($cdg_uinfo['surname']) : '';
        $full  = trim($first . ' ' . $last);
    }
    if(!$full) {
        $first = isset($cdg_uinfo['firstname']) ? trim($cdg_uinfo['firstname']) : '';
        $last  = isset($cdg_uinfo['lastname']) ? trim($cdg_uinfo['lastname']) : '';
        $full  = trim($first . ' ' . $last);
    }
    if(!$full) $full = isset($cdg_uinfo['username']) ? $cdg_uinfo['username'] : 'Müşteri';
    $user_name = $full;
    $user_email = $cdg_uinfo['email'] ?? '';

    $bal_amount = isset($cdg_uinfo['balance']) ? $cdg_uinfo['balance'] : 0;
    $bal_cid = isset($cdg_uinfo['balance_cid']) ? $cdg_uinfo['balance_cid'] : 0;
    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $bal_cid) {
        $user_balance = Money::formatter_symbol($bal_amount, $bal_cid);
    } else {
        $user_balance = number_format((float)$bal_amount, 2, ',', '.') . ' ₺';
    }

    $user_initial = mb_strtoupper(mb_substr($full, 0, 1, 'UTF-8'), 'UTF-8');
}

// === İstatistikler (WiseCP runtime'dan al, yoksa say) ===
$count_total_products  = 0;
if(isset($orders) && is_array($orders)) {
    $count_total_products = count($orders);
}

// === Classic-uyumlu statistic1-4 runtime variables (PRIMARY) ===
// $statistic1 = aktif urun sayisi
// $statistic2 = domain sayisi
// $statistic3 = bekleyen fatura sayisi
// $statistic4 = acik ticket sayisi
$count_active_products = isset($statistic1) ? (int)$statistic1 : 0;
$count_active_domains  = isset($statistic2) ? (int)$statistic2 : 0;
$count_unpaid_invoices = isset($statistic3) ? (int)$statistic3 : 0;
$count_open_tickets    = isset($statistic4) ? (int)$statistic4 : 0;

// Statistic'lar yok ise array'lerden manuel say (fallback)
if($count_active_products === 0 && isset($orders) && is_array($orders)) {
    foreach($orders as $o) {
        if(isset($o['status']) && in_array($o['status'], ['active', 'Active', 'aktif'])) {
            $count_active_products++;
        }
    }
}
if($count_active_domains === 0 && isset($domain_orders) && is_array($domain_orders)) {
    foreach($domain_orders as $do) {
        if(isset($do['status']) && in_array($do['status'], ['active', 'Active', 'aktif'])) {
            $count_active_domains++;
        }
    }
}

// Faturalar (WiseCP $unpaid_invoices set ediyorsa)
if($count_unpaid_invoices === 0) {
    if(isset($unpaid_invoices) && is_array($unpaid_invoices)) {
        $count_unpaid_invoices = count($unpaid_invoices);
    } elseif(isset($invoices) && is_array($invoices)) {
        foreach($invoices as $inv) {
            if(isset($inv['status']) && in_array($inv['status'], ['unpaid', 'Unpaid', 'odenmemis'])) {
                $count_unpaid_invoices++;
            }
        }
    }
}

// Talepler
if($count_open_tickets === 0 && isset($tickets) && is_array($tickets)) {
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
// Quick action linkleri - sidebar ile birebir ayni slug'lar (alias map bypass)
// $links['all-orders'] WiseCP runtime'da bazen yanlis URL doner (/products-hosting)
$products_url = cdg_link('ac-ps-products');     // Tum Urunler (sidebar ile ayni)
$invoices_url = cdg_link('ac-ps-invoices');
$tickets_url  = cdg_link('ac-ps-tickets');
$balance_url  = cdg_link('ac-ps-balance');
$domains_url  = cdg_link('ac-products-domain');
$shop_url     = cdg_link('products', ['hosting']);
$contact_url  = cdg_link('contact');

// === Yaklaşan ödemeler (en yakın 30 gün, max 5) ===
$upcoming_items = [];
$now_ts = time();
$next30 = $now_ts + (30 * 86400);
$all_orders_combined = [];
if(isset($orders) && is_array($orders))         $all_orders_combined = array_merge($all_orders_combined, $orders);
if(isset($domain_orders) && is_array($domain_orders)) $all_orders_combined = array_merge($all_orders_combined, $domain_orders);

foreach($all_orders_combined as $o) {
    if(!isset($o['duedate'])) continue;
    $duedate = $o['duedate'];
    if(in_array(substr((string)$duedate,0,4), ['1881','1970','0000'])) continue;
    $ts = strtotime((string)$duedate);
    if(!$ts || $ts > $next30) continue;
    $status = $o['status'] ?? '';
    if(!in_array($status, ['active', 'Active', 'aktif'])) continue;
    $upcoming_items[] = [
        'name'    => $o['name'] ?? '-',
        'duedate' => $duedate,
        'days'    => max(0, (int)(($ts - $now_ts) / 86400)),
        'id'      => $o['id'] ?? 0,
        'link'    => $o['detail_link'] ?? '#',
        'type'    => $o['type'] ?? '',
    ];
}
usort($upcoming_items, function($a,$b){ return $a['days'] - $b['days']; });
$upcoming_items = array_slice($upcoming_items, 0, 5);

// === Son destek talepleri (max 5) ===
$recent_tickets = [];
if(isset($tickets) && is_array($tickets)) {
    $recent_tickets = array_slice($tickets, 0, 5);
}

// Ticket status meta
$tk_meta = [
    'Customer-Reply' => ['cls' => 'warning', 'lbl' => 'Yanıt Bekliyor', 'icon' => 'hourglass-split'],
    'open'           => ['cls' => 'info',    'lbl' => 'Açık',           'icon' => 'circle-fill'],
    'Open'           => ['cls' => 'info',    'lbl' => 'Açık',           'icon' => 'circle-fill'],
    'Answered'       => ['cls' => 'info',    'lbl' => 'Yanıtlandı',     'icon' => 'reply-fill'],
    'Closed'         => ['cls' => 'success', 'lbl' => 'Çözüldü',        'icon' => 'check-circle-fill'],
    'closed'         => ['cls' => 'success', 'lbl' => 'Çözüldü',        'icon' => 'check-circle-fill'],
    'cozuldu'        => ['cls' => 'success', 'lbl' => 'Çözüldü',        'icon' => 'check-circle-fill'],
];

// Date format helper
$cdg_date_fmt = function($d) {
    if(!$d || in_array(substr((string)$d,0,4), ['1881','1970','0000'])) return '-';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        return DateManager::format(Config::get("options/date-format") ?: 'd.m.Y', $d);
    }
    return date('d.m.Y', strtotime((string)$d));
};

$tickets_url   = cdg_link('ac-ps-tickets');
$create_ticket_url = cdg_link('ac-ps-create-ticket-request');
$invoices_url  = cdg_link('ac-ps-invoices');
$balance_url   = cdg_link('ac-ps-balance');
?>

<style>
/* === CODEGA DASHBOARD - Kurumsal LNW-Style === */
.cdg-d {
    --d-primary: #2E3B4E;
    --d-primary-deep: #1A2332;
    --d-gold: #f59e0b;
    --d-gold-light: #fbbf24;
    --d-success: #10b981;
    --d-warning: #f59e0b;
    --d-danger: #ef4444;
    --d-info: #00D3E5;
    --d-purple: #8b5cf6;
    --d-bg: #f5f7fb;
    --d-card: #ffffff;
    --d-text: #0f172a;
    --d-muted: #64748b;
    --d-border: #e2e8f0;
    --d-radius: 14px;
    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
    color: var(--d-text);
    /* Parent (.cdg-ac-content) zaten 100vh fixed, biz onun içinde flex column */
    display: flex;
    flex-direction: column;
    gap: 12px;
    height: 100%;
    min-height: 0;
    box-sizing: border-box;
}
/* Greeting + Quick auto, Main 1fr (kalan alan) */
.cdg-d > .cdg-d-main { flex: 1; min-height: 0; }
.cdg-d *, .cdg-d *::before, .cdg-d *::after { box-sizing: border-box; }

/* === KURUMSAL SHELL - Tüm dashboard panel içinde === */
.cdg-d-shell {
    max-width: 1280px;
    margin: 0 auto;
    width: 100%;
    background: #ffffff;
    border: 1px solid var(--d-border);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.04);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    height: 100%;
    overflow: auto;
}
.cdg-d-shell > .cdg-d-main { flex: 1; min-height: 0; }
@media (max-width: 768px) { .cdg-d-shell { padding: 14px; border-radius: 12px; } }
.cdg-d a { text-decoration: none; color: inherit; }

/* === GREETING (kompakt, viewport adaptive) === */
.cdg-d-greet {
    background: linear-gradient(135deg, #1A2332 0%, #2E3B4E 45%, #1A4F5C 80%, #00D3E5 110%);
    border-radius: 14px;
    padding: clamp(12px, 1.6vh, 18px) clamp(16px, 2vw, 24px);
    color: #fff;
    overflow: hidden;
    position: relative;
    margin-bottom: 0;
    flex-shrink: 0;
}
.cdg-d-greet::before {
    content: '';
    position: absolute; top: -50%; right: -10%;
    width: 50%; height: 200%;
    background: radial-gradient(circle, rgba(251,191,36,0.20), transparent 60%);
    filter: blur(60px);
    pointer-events: none;
}
.cdg-d-greet::after {
    content: '';
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}
.cdg-d-greet-row {
    position: relative; z-index: 1;
    display: flex; align-items: center; gap: 14px;
    flex-wrap: wrap;
}
.cdg-d-greet-avatar {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, #00E5FF, #00D3E5);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 800;
    color: #1A2332;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(0,229,255,0.35);
}
.cdg-d-greet-text { flex: 1; min-width: 200px; }
.cdg-d-greet-eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 10px; font-weight: 700;
    color: #00E5FF;
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 1px;
}
.cdg-d-greet h1 {
    font-size: clamp(15px, 2vh, 19px); font-weight: 800;
    margin: 0;
    color: #fff; letter-spacing: -0.01em;
    line-height: 1.2;
}
.cdg-d-greet h1 strong { color: #00E5FF; }
.cdg-d-greet-meta {
    font-size: 12px;
    color: rgba(255,255,255,0.80);
    margin-top: 3px;
}
.cdg-d-greet-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.30);
    border-radius: 9px;
    color: #fff;
    font-size: 12px; font-weight: 700;
    backdrop-filter: blur(10px);
    transition: all 0.2s;
    white-space: nowrap;
}
.cdg-d-greet-cta:hover { background: rgba(255,255,255,0.25); transform: translateY(-1px); color: #00E5FF; }

/* === QUICK CARDS (3'lü grid, kompakt) === */
.cdg-d-quick {
    display: grid;
    grid-template-columns: 1fr 1fr 1.4fr;
    gap: 10px;
    margin-bottom: 0;
    flex-shrink: 0;
}
@media (max-width: 980px) { .cdg-d-quick { grid-template-columns: 1fr 1fr; } .cdg-d-quick > :last-child { grid-column: 1 / -1; } }
@media (max-width: 540px) { .cdg-d-quick { grid-template-columns: 1fr; } }

.cdg-d-quick-card {
    background: var(--d-card);
    border: 1px solid var(--d-border);
    border-radius: 12px;
    padding: clamp(11px, 1.4vh, 16px) clamp(14px, 1.6vw, 18px);
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}
.cdg-d-quick-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15,23,42,0.08);
    border-color: var(--d-primary);
}
.cdg-d-quick-card-text {
    flex: 1; min-width: 0;
}
.cdg-d-quick-card-title {
    font-size: 13px; font-weight: 800;
    color: var(--d-text);
    text-transform: uppercase; letter-spacing: 0.4px;
    margin: 0 0 2px;
    line-height: 1.2;
}
.cdg-d-quick-card-sub {
    font-size: 11px;
    color: var(--d-muted);
    margin: 0;
    line-height: 1.3;
}
.cdg-d-quick-card-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    position: relative;
}
.cdg-d-quick-card-badge {
    position: absolute;
    top: -4px; right: -4px;
    min-width: 18px; height: 18px;
    padding: 0 4px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    font-size: 10px; font-weight: 800;
    line-height: 18px; text-align: center;
    border-radius: 100px;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(245,158,11,0.40);
}

/* Status (Gecikmiş ödeme) */
.cdg-d-quick-status {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border-color: #6ee7b7;
}
.cdg-d-quick-status.warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-color: #fcd34d;
}
.cdg-d-quick-status.danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-color: #fca5a5;
}
.cdg-d-quick-status .cdg-d-quick-card-title { color: #065f46; font-size: 13px; }
.cdg-d-quick-status.warning .cdg-d-quick-card-title { color: #92400e; }
.cdg-d-quick-status.danger .cdg-d-quick-card-title { color: #991b1b; }
.cdg-d-quick-status .cdg-d-quick-card-icon { background: rgba(255,255,255,0.60); }
.cdg-d-quick-status .cdg-d-quick-card-icon i { color: #065f46; }
.cdg-d-quick-status.warning .cdg-d-quick-card-icon i { color: #92400e; }
.cdg-d-quick-status.danger .cdg-d-quick-card-icon i { color: #991b1b; }

/* === ANA GRID (panel'ler, kalan dikey alanı doldurur) === */
.cdg-d-main {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 0;
    min-height: 0; /* kritik: grid içinde overflow düzgün çalışsın */
    overflow: hidden;
}
@media (max-width: 880px) { .cdg-d-main { grid-template-columns: 1fr; overflow: auto; } }

.cdg-d-panel {
    background: var(--d-card);
    border: 1px solid var(--d-border);
    border-radius: var(--d-radius);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.cdg-d-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 11px 16px;
    color: #fff;
    background: linear-gradient(135deg, #1A2332, #2E3B4E);
    flex-shrink: 0;
}
.cdg-d-panel-head.gold {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.cdg-d-panel-head.deep {
    background: linear-gradient(135deg, #1A2332, #1A2332);
}
.cdg-d-panel-head h3 {
    margin: 0;
    font-size: 13px; font-weight: 800;
    letter-spacing: 0.3px;
    display: flex; align-items: center; gap: 7px;
}
.cdg-d-panel-head h3 i { font-size: 16px; }
.cdg-d-panel-head a {
    color: #fff;
    font-size: 11px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px;
    border-radius: 7px;
    background: rgba(255,255,255,0.18);
    transition: all 0.2s;
}
.cdg-d-panel-head a:hover { background: rgba(255,255,255,0.30); }

.cdg-d-panel-body {
    padding: 0;
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
}
.cdg-d-panel-body.padded { padding: 12px 16px; }
/* Custom scrollbar */
.cdg-d-panel-body::-webkit-scrollbar { width: 6px; }
.cdg-d-panel-body::-webkit-scrollbar-track { background: transparent; }
.cdg-d-panel-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 100px; }
.cdg-d-panel-body::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* List items */
.cdg-d-list { list-style: none; margin: 0; padding: 0; }
.cdg-d-list li {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 16px;
}
.cdg-d-list li:not(:last-child) {
    border-bottom: 1px solid #f1f5f9;
}
.cdg-d-list li:hover {
    background: #fafbfd;
}
.cdg-d-list-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.cdg-d-list-text {
    flex: 1; min-width: 0;
}
.cdg-d-list-title {
    font-size: 13px; font-weight: 700;
    color: var(--d-text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1.3;
}
.cdg-d-list-sub {
    font-size: 11px;
    color: var(--d-muted);
    margin-top: 1px;
}

/* Progress (kalan gün) */
.cdg-d-progress {
    flex-shrink: 0;
    width: 90px;
    text-align: center;
}
.cdg-d-progress-bar {
    height: 4px;
    background: #e2e8f0;
    border-radius: 100px;
    overflow: hidden;
    margin-bottom: 4px;
}
.cdg-d-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    border-radius: 100px;
    transition: width 0.4s ease;
}
.cdg-d-progress-fill.danger { background: linear-gradient(90deg, #ef4444, #dc2626); }
.cdg-d-progress-fill.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
.cdg-d-progress-num {
    font-size: 14px; font-weight: 800;
    color: var(--d-text);
}
.cdg-d-progress-lbl {
    font-size: 10px;
    color: var(--d-muted);
    text-transform: uppercase;
}

.cdg-d-renew {
    flex-shrink: 0;
    padding: 7px 16px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff !important;
    border-radius: 8px;
    font-size: 12px; font-weight: 700;
    transition: all 0.2s;
}
.cdg-d-renew:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(16,185,129,0.30); color: #fff !important; }

/* Ticket badge */
.cdg-d-tk-badge {
    flex-shrink: 0;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 11px;
    border-radius: 100px;
    font-size: 11px; font-weight: 700;
}
.cdg-d-tk-badge.success { background: linear-gradient(135deg,#d1fae5,#a7f3d0); color: #065f46; }
.cdg-d-tk-badge.warning { background: linear-gradient(135deg,#fef3c7,#fde68a); color: #92400e; }
.cdg-d-tk-badge.info    { background: linear-gradient(135deg,#CFFAFE,#A5F3FC); color: #2E3B4E; }
.cdg-d-tk-badge.danger  { background: linear-gradient(135deg,#fee2e2,#fecaca); color: #991b1b; }
.cdg-d-tk-badge i { font-size: 10px; }

/* Empty state */
.cdg-d-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--d-muted);
}
.cdg-d-empty i { font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 8px; }
.cdg-d-empty-text { font-size: 13px; }

/* News mini section */
.cdg-d-news-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
@media (max-width: 640px) { .cdg-d-news-grid { grid-template-columns: 1fr; } }
.cdg-d-news-item {
    padding: 14px 16px;
    border: 1px solid var(--d-border);
    border-radius: 10px;
    transition: all 0.2s;
    background: #fff;
}
.cdg-d-news-item:hover {
    border-color: var(--d-primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(46,59,78,0.10);
}
.cdg-d-news-item-title {
    font-size: 13px; font-weight: 700;
    color: var(--d-text);
    line-height: 1.4;
}
.cdg-d-news-item-date {
    font-size: 11px;
    color: var(--d-muted);
    margin-top: 4px;
    display: flex; align-items: center; gap: 4px;
}
</style>

<div class="cdg-d">
    <div class="cdg-d-shell">

    <!-- GREETING -->
    <section class="cdg-d-greet">
        <div class="cdg-d-greet-row">
            <div class="cdg-d-greet-avatar">
                <?php
                $first_letter = '?';
                if(isset($user_initial) && $user_initial) $first_letter = $user_initial;
                elseif(isset($user_name) && $user_name) $first_letter = mb_strtoupper(mb_substr($user_name, 0, 1, 'UTF-8'), 'UTF-8');
                elseif(isset($cdg_uinfo['firstname']) && $cdg_uinfo['firstname']) $first_letter = mb_strtoupper(mb_substr($cdg_uinfo['firstname'], 0, 1, 'UTF-8'), 'UTF-8');
                echo htmlspecialchars($first_letter, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                ?>
            </div>
            <div class="cdg-d-greet-text">
                <div class="cdg-d-greet-eyebrow"><i class="bi bi-sun-fill"></i> <?php echo $greeting; ?></div>
                <h1>Hoş geldin, <strong><?php echo htmlspecialchars(isset($user_name) && $user_name ? $user_name : 'Müşteri', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong></h1>
                <div class="cdg-d-greet-meta">
                    <i class="bi bi-calendar-check"></i> <?php echo date('d.m.Y'); ?>
                    <?php if(isset($cdg_uinfo['id'])): ?>
                    &nbsp;·&nbsp; <i class="bi bi-person-badge"></i> Müşteri No: <strong><?php echo (int)$cdg_uinfo['id']; ?></strong>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?php echo $create_ticket_url; ?>" class="cdg-d-greet-cta">
                <i class="bi bi-plus-circle-fill"></i> Yeni Talep Aç
            </a>
        </div>
    </section>

    <!-- QUICK CARDS -->
    <div class="cdg-d-quick">
        <a href="<?php echo $invoices_url; ?>" class="cdg-d-quick-card" style="text-decoration:none;">
            <div class="cdg-d-quick-card-text">
                <div class="cdg-d-quick-card-title">Faturalarım</div>
                <p class="cdg-d-quick-card-sub">Bekleyen ve geçmiş ödemeler</p>
            </div>
            <div class="cdg-d-quick-card-icon" style="background:linear-gradient(135deg,#CFFAFE,#A5F3FC);color:#2E3B4E;">
                <i class="bi bi-receipt"></i>
                <?php if($count_unpaid_invoices > 0): ?>
                <span class="cdg-d-quick-card-badge"><?php echo $count_unpaid_invoices; ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo $create_ticket_url; ?>" class="cdg-d-quick-card" style="text-decoration:none;">
            <div class="cdg-d-quick-card-text">
                <div class="cdg-d-quick-card-title">Yeni Destek Talebi</div>
                <p class="cdg-d-quick-card-sub">Sorunlarınızı paylaşın</p>
            </div>
            <div class="cdg-d-quick-card-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;">
                <i class="bi bi-headset"></i>
            </div>
        </a>

        <?php
        $status_class = 'cdg-d-quick-status';
        $status_icon  = 'check-circle-fill';
        $status_title = 'Gecikmiş ödemeniz bulunmamaktadır';
        $status_sub   = 'Tüm faturalarınız zamanında ödenmiş';
        if($count_unpaid_invoices > 0) {
            $status_class .= ' warning';
            $status_icon = 'exclamation-circle-fill';
            $status_title = $count_unpaid_invoices . ' bekleyen fatura';
            $status_sub = 'Lütfen vadesi geçmeden ödeyin';
        }
        ?>
        <div class="cdg-d-quick-card <?php echo $status_class; ?>">
            <div class="cdg-d-quick-card-text">
                <div class="cdg-d-quick-card-title"><?php echo $status_title; ?></div>
                <p class="cdg-d-quick-card-sub"><?php echo $status_sub; ?></p>
            </div>
            <div class="cdg-d-quick-card-icon">
                <i class="bi bi-<?php echo $status_icon; ?>"></i>
            </div>
        </div>
    </div>

    <!-- ANA GRID: Yaklaşan Ödemeler + Son Talepler -->
    <div class="cdg-d-main">

        <!-- Ödemesi Yaklaşan Ürünleriniz -->
        <div class="cdg-d-panel">
            <div class="cdg-d-panel-head deep">
                <h3><i class="bi bi-clock-history"></i> Ödemesi Yaklaşan Hizmetler</h3>
                <a href="<?php echo $products_url; ?>">Tümü <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="cdg-d-panel-body">
                <?php if(!empty($upcoming_items)): ?>
                <ul class="cdg-d-list">
                    <?php foreach($upcoming_items as $item):
                        $type = $item['type'];
                        $type_icons = [
                            'hosting' => ['hdd-network-fill', '#10b981'],
                            'server'  => ['server', '#8b5cf6'],
                            'domain'  => ['globe2', '#00D3E5'],
                            'sms'     => ['chat-dots-fill', '#f59e0b'],
                            'software'=> ['code-square', '#ec4899'],
                            'special' => ['star-fill', '#2E3B4E'],
                        ];
                        $ti = $type_icons[$type] ?? ['box-seam', '#64748b'];
                        $progress_pct = max(0, min(100, ($item['days'] / 30) * 100));
                        $progress_cls = $item['days'] <= 7 ? 'danger' : ($item['days'] <= 14 ? 'warning' : '');
                    ?>
                    <li>
                        <div class="cdg-d-list-icon" style="background:<?php echo $ti[1]; ?>15;color:<?php echo $ti[1]; ?>;">
                            <i class="bi bi-<?php echo $ti[0]; ?>"></i>
                        </div>
                        <div class="cdg-d-list-text">
                            <div class="cdg-d-list-title"><?php echo htmlspecialchars($item['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <div class="cdg-d-list-sub"><i class="bi bi-calendar-check"></i> <?php echo $cdg_date_fmt($item['duedate']); ?></div>
                        </div>
                        <div class="cdg-d-progress">
                            <div class="cdg-d-progress-bar">
                                <div class="cdg-d-progress-fill <?php echo $progress_cls; ?>" style="width:<?php echo $progress_pct; ?>%;"></div>
                            </div>
                            <div class="cdg-d-progress-num"><?php echo $item['days']; ?></div>
                            <div class="cdg-d-progress-lbl">gün</div>
                        </div>
                        <a href="<?php echo htmlspecialchars($item['link'], ENT_QUOTES); ?>" class="cdg-d-renew">
                            <i class="bi bi-arrow-clockwise"></i> Yenile
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="cdg-d-empty">
                    <i class="bi bi-calendar-check"></i>
                    <div class="cdg-d-empty-text">Yaklaşan ödemesi olan hizmet bulunmuyor.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Destek Taleplerim -->
        <div class="cdg-d-panel">
            <div class="cdg-d-panel-head gold">
                <h3><i class="bi bi-chat-square-text-fill"></i> Destek Taleplerim</h3>
                <a href="<?php echo $tickets_url; ?>">Tümü <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="cdg-d-panel-body">
                <?php if(!empty($recent_tickets)): ?>
                <ul class="cdg-d-list">
                    <?php foreach($recent_tickets as $tk):
                        $tk_status = $tk['status'] ?? '';
                        $tkm = $tk_meta[$tk_status] ?? ['cls' => 'info', 'lbl' => 'İşleniyor', 'icon' => 'circle'];
                        $tk_subject = $tk['subject'] ?? ($tk['title'] ?? '-');
                        $tk_link = $tk['detail_link'] ?? ($tk['link'] ?? '#');
                        $tk_date = $tk['cdate'] ?? ($tk['date'] ?? '');
                    ?>
                    <li>
                        <div class="cdg-d-list-icon" style="background:rgba(245,158,11,0.10);color:#f59e0b;">
                            <i class="bi bi-chat-left-text"></i>
                        </div>
                        <div class="cdg-d-list-text">
                            <div class="cdg-d-list-title"><a href="<?php echo htmlspecialchars($tk_link, ENT_QUOTES); ?>" style="color:inherit;"><?php echo htmlspecialchars($tk_subject, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a></div>
                            <?php if($tk_date): ?>
                            <div class="cdg-d-list-sub"><i class="bi bi-clock"></i> <?php echo $cdg_date_fmt($tk_date); ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="cdg-d-tk-badge <?php echo $tkm['cls']; ?>">
                            <i class="bi bi-<?php echo $tkm['icon']; ?>"></i> <?php echo htmlspecialchars($tkm['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="cdg-d-empty">
                    <i class="bi bi-headset"></i>
                    <div class="cdg-d-empty-text">Henüz destek talebiniz yok.<br><a href="<?php echo $create_ticket_url; ?>" style="color:#2E3B4E;font-weight:700;">İlk talebinizi oluşturun</a></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    </div><!-- /cdg-d-shell -->
</div>
