<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        // Bu yuzden once alias+CRLink, $links sadece bilinmeyen slug'lar icin son fallback
        global $links;

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

$page_title = isset($page_title) ? $page_title : 'Tum Hizmetlerim';

// Kategori filtreleri için $list veya $orders kullan
$items = [];
if(isset($list) && is_array($list)) $items = $list;
elseif(isset($orders) && is_array($orders)) $items = $orders;
?>

<?php
// === Istatistik hesapla ===
$stats = [
    'total'   => count($items),
    'hosting' => 0,
    'domain'  => 0,
    'server'  => 0,
    'sms'     => 0,
    'active'  => 0,
];
foreach($items as $row) {
    $type = $row['type'] ?? '';
    if(isset($stats[$type])) $stats[$type]++;
    if(($row['status'] ?? '') === 'active') $stats['active']++;
}

// Kategori meta map (ikon + renk + Türkçe etiket)
$type_meta = [
    'hosting'      => ['icon' => 'hdd-network-fill',     'color' => '#10b981', 'lbl' => 'Hosting'],
    'server'       => ['icon' => 'server',                'color' => '#8b5cf6', 'lbl' => 'Sunucu'],
    'domain'       => ['icon' => 'globe2',                'color' => '#06b6d4', 'lbl' => 'Domain'],
    'sms'          => ['icon' => 'chat-dots-fill',        'color' => '#f59e0b', 'lbl' => 'SMS'],
    'software'     => ['icon' => 'code-square',           'color' => '#ec4899', 'lbl' => 'Yazılım'],
    'special'      => ['icon' => 'star-fill',             'color' => '#1e40af', 'lbl' => 'Özel'],
    'subscription' => ['icon' => 'arrow-repeat',          'color' => '#3b82f6', 'lbl' => 'Abonelik'],
];

// Status meta map (renkli badge)
$status_meta_map = [
    'active'      => ['cls' => 'success', 'lbl' => 'Aktif',          'icon' => 'check-circle-fill'],
    'waiting'     => ['cls' => 'info',    'lbl' => 'Sırada',          'icon' => 'hourglass-split'],
    'inprocess'   => ['cls' => 'info',    'lbl' => 'İşleniyor',       'icon' => 'arrow-repeat'],
    'expired'     => ['cls' => 'danger',  'lbl' => 'Süresi Doldu',    'icon' => 'x-circle-fill'],
    'suspended'   => ['cls' => 'warning', 'lbl' => 'Askıya Alındı',   'icon' => 'pause-circle-fill'],
    'cancelled'   => ['cls' => 'muted',   'lbl' => 'İptal',           'icon' => 'ban'],
    'fraud'       => ['cls' => 'danger',  'lbl' => 'Sahte',           'icon' => 'shield-x'],
    'transferred' => ['cls' => 'muted',   'lbl' => 'Transfer Edildi', 'icon' => 'arrow-right-circle'],
];
?>

<style>
/* === ÜRÜN VE HİZMETLERİM - Kurumsal Tasarım === */
.cdg-pl {
    --pl-primary: #1e40af;
    --pl-primary-soft: #3b82f6;
    --pl-text: #0f172a;
    --pl-muted: #64748b;
    --pl-border: #e2e8f0;
    --pl-bg: #f8fafc;
    --pl-card: #ffffff;
    --pl-radius: 14px;
    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
}
.cdg-pl *, .cdg-pl *::before, .cdg-pl *::after { box-sizing: border-box; }
.cdg-pl a { text-decoration: none; }

/* HERO */
.cdg-pl-hero {
    position: relative;
    background: linear-gradient(135deg, #0a1f44 0%, #1e3a8a 50%, #2563eb 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    overflow: hidden;
    margin-bottom: 20px;
}
.cdg-pl-hero::before {
    content: '';
    position: absolute; top: -30%; right: -10%;
    width: 60%; height: 200%;
    background: radial-gradient(circle, rgba(251,191,36,0.20) 0%, transparent 60%);
    filter: blur(60px);
    pointer-events: none;
}
.cdg-pl-hero::after {
    content: '';
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}
.cdg-pl-hero-row {
    position: relative; z-index: 1;
    display: flex; align-items: center; gap: 24px;
    flex-wrap: wrap;
}
.cdg-pl-hero-icon {
    width: 64px; height: 64px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.20);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #fde047;
    flex-shrink: 0;
    backdrop-filter: blur(10px);
}
.cdg-pl-hero-text { flex: 1; min-width: 220px; }
.cdg-pl-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 11px;
    background: rgba(251,191,36,0.20);
    border: 1px solid rgba(251,191,36,0.40);
    border-radius: 100px;
    color: #fde047;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 6px;
}
.cdg-pl-hero h1 {
    font-size: 24px; font-weight: 800;
    margin: 0 0 4px;
    color: #fff; letter-spacing: -0.01em;
}
.cdg-pl-hero p {
    font-size: 13px;
    color: rgba(255,255,255,0.80);
    margin: 0;
}
.cdg-pl-hero-action {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 18px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #422006 !important;
    border-radius: 10px;
    font-size: 13px; font-weight: 800;
    box-shadow: 0 6px 18px rgba(251,191,36,0.35);
    transition: all 0.2s;
}
.cdg-pl-hero-action:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(251,191,36,0.50); }

/* STATS */
.cdg-pl-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
@media (max-width: 980px) { .cdg-pl-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 540px) { .cdg-pl-stats { grid-template-columns: repeat(2, 1fr); } }
.cdg-pl-stat {
    background: var(--pl-card);
    border: 1px solid var(--pl-border);
    border-radius: 12px;
    padding: 14px 16px;
    transition: all 0.2s;
    cursor: pointer;
    user-select: none;
}
.cdg-pl-stat:hover {
    border-color: var(--pl-primary);
    box-shadow: 0 6px 18px rgba(30,64,175,0.10);
    transform: translateY(-2px);
}
.cdg-pl-stat.active {
    border-color: var(--pl-primary);
    background: linear-gradient(135deg, rgba(30,64,175,0.06), rgba(30,64,175,0.02));
    box-shadow: 0 6px 18px rgba(30,64,175,0.12);
}
.cdg-pl-stat-row {
    display: flex; align-items: center; gap: 10px;
}
.cdg-pl-stat-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.cdg-pl-stat-num {
    font-size: 22px; font-weight: 800;
    line-height: 1;
    color: var(--pl-text);
}
.cdg-pl-stat-lbl {
    font-size: 11px; font-weight: 600;
    color: var(--pl-muted);
    text-transform: uppercase; letter-spacing: 0.5px;
    margin-top: 3px;
}

/* TOOLBAR */
.cdg-pl-toolbar {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.cdg-pl-search {
    position: relative;
    flex: 1; min-width: 240px;
}
.cdg-pl-search i {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: var(--pl-muted); font-size: 16px;
    pointer-events: none;
}
.cdg-pl-search input {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 1.5px solid var(--pl-border);
    border-radius: 10px;
    font-size: 14px;
    background: var(--pl-card);
    color: var(--pl-text);
    font-family: inherit;
    outline: none;
    transition: all 0.2s;
}
.cdg-pl-search input:focus {
    border-color: var(--pl-primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,0.10);
}
.cdg-pl-search-clear {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: var(--pl-bg);
    border: 0;
    width: 26px; height: 26px;
    border-radius: 6px;
    display: none;
    align-items: center; justify-content: center;
    cursor: pointer;
    color: var(--pl-muted);
}
.cdg-pl-search-clear.show { display: flex; }
.cdg-pl-search-clear:hover { background: #f1f5f9; color: var(--pl-text); }

/* TABLE */
.cdg-pl-table-wrap {
    background: var(--pl-card);
    border: 1px solid var(--pl-border);
    border-radius: var(--pl-radius);
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
.cdg-pl-table {
    width: 100%;
    border-collapse: collapse;
}
.cdg-pl-table thead th {
    text-align: left;
    padding: 13px 16px;
    background: var(--pl-bg);
    border-bottom: 1px solid var(--pl-border);
    font-size: 11px; font-weight: 700;
    color: var(--pl-muted);
    text-transform: uppercase; letter-spacing: 0.5px;
    white-space: nowrap;
}
.cdg-pl-table thead th:first-child { padding-left: 22px; }
.cdg-pl-table thead th:last-child  { padding-right: 22px; text-align: right; }
.cdg-pl-table tbody tr {
    transition: background 0.15s;
}
.cdg-pl-table tbody tr:hover {
    background: linear-gradient(90deg, rgba(30,64,175,0.025), transparent);
}
.cdg-pl-table tbody tr:not(:last-child) {
    border-bottom: 1px solid #f1f5f9;
}
.cdg-pl-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    font-size: 14px;
    color: var(--pl-text);
}
.cdg-pl-table tbody td:first-child { padding-left: 22px; }
.cdg-pl-table tbody td:last-child  { padding-right: 22px; text-align: right; }

/* Hizmet hücresi */
.cdg-pl-svc {
    display: flex; align-items: center; gap: 12px;
}
.cdg-pl-svc-icon {
    width: 38px; height: 38px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.cdg-pl-svc-name {
    font-weight: 700;
    color: var(--pl-text);
    line-height: 1.3;
    font-size: 14px;
}
.cdg-pl-svc-sub {
    font-size: 12px;
    color: var(--pl-muted);
    margin-top: 2px;
    display: flex; align-items: center; gap: 4px;
}
.cdg-pl-svc-sub i { font-size: 11px; }

/* ID badge */
.cdg-pl-id {
    display: inline-flex; align-items: center;
    padding: 3px 9px;
    background: var(--pl-bg);
    border: 1px solid var(--pl-border);
    border-radius: 6px;
    font-family: 'JetBrains Mono', 'SF Mono', Monaco, monospace;
    font-size: 11px; font-weight: 600;
    color: var(--pl-muted);
    letter-spacing: 0.3px;
}

/* Tutar */
.cdg-pl-amount {
    text-align: right;
    font-weight: 700;
    color: var(--pl-text);
    font-size: 14px;
}
.cdg-pl-amount-period {
    font-size: 11px;
    color: var(--pl-muted);
    font-weight: 500;
    margin-top: 1px;
}

/* Tarih + Kalan Gün */
.cdg-pl-date {
    text-align: center;
    color: var(--pl-text);
    font-size: 13px;
    font-weight: 600;
}
.cdg-pl-date-empty {
    color: var(--pl-muted);
    font-weight: 400;
}
.cdg-pl-due {
    display: inline-block;
    min-width: 110px;
    text-align: center;
    line-height: 1.2;
}
.cdg-pl-due-date {
    font-size: 13px;
    font-weight: 700;
    color: var(--pl-text);
    margin-bottom: 5px;
}
.cdg-pl-due-bar {
    height: 4px;
    background: #e2e8f0;
    border-radius: 100px;
    overflow: hidden;
    margin-bottom: 5px;
}
.cdg-pl-due-fill {
    height: 100%;
    border-radius: 100px;
    transition: width 0.4s ease;
}
.cdg-pl-due-normal   .cdg-pl-due-fill { background: linear-gradient(90deg, #10b981, #059669); }
.cdg-pl-due-soon     .cdg-pl-due-fill { background: linear-gradient(90deg, #f59e0b, #d97706); }
.cdg-pl-due-critical .cdg-pl-due-fill { background: linear-gradient(90deg, #ef4444, #dc2626); animation: cdgPlPulse 1.6s ease-in-out infinite; }
.cdg-pl-due-expired  .cdg-pl-due-fill { background: linear-gradient(90deg, #991b1b, #7f1d1d); }
@keyframes cdgPlPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.55; } }
.cdg-pl-due-lbl {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 100px;
    line-height: 1.4;
}
.cdg-pl-due-lbl i { font-size: 11px; }
.cdg-pl-due-normal   .cdg-pl-due-lbl { color: #065f46; background: linear-gradient(135deg,#d1fae5,#a7f3d0); }
.cdg-pl-due-soon     .cdg-pl-due-lbl { color: #92400e; background: linear-gradient(135deg,#fef3c7,#fde68a); }
.cdg-pl-due-critical .cdg-pl-due-lbl { color: #991b1b; background: linear-gradient(135deg,#fee2e2,#fecaca); }
.cdg-pl-due-expired  .cdg-pl-due-lbl { color: #fff;    background: linear-gradient(135deg,#dc2626,#991b1b); }

/* Status badge */
.cdg-pl-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px;
    border-radius: 100px;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.2px;
    border: 1px solid transparent;
}
.cdg-pl-badge i { font-size: 11px; }
.cdg-pl-badge-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; border-color: #6ee7b7; }
.cdg-pl-badge-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; border-color: #fcd34d; }
.cdg-pl-badge-info    { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; border-color: #93c5fd; }
.cdg-pl-badge-danger  { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; border-color: #fca5a5; }
.cdg-pl-badge-muted   { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }

/* Yönet butonu */
.cdg-pl-manage {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: #fff !important;
    border-radius: 8px;
    font-size: 12px; font-weight: 700;
    transition: all 0.2s;
    border: 0;
    cursor: pointer;
    text-decoration: none;
}
.cdg-pl-manage:hover {
    background: linear-gradient(135deg, #1e40af, #1e3a8a);
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(30,64,175,0.25);
    color: #fff !important;
}
.cdg-pl-manage i { font-size: 13px; }
.cdg-pl-manage-disabled {
    display: inline-flex;
    padding: 8px 14px;
    color: var(--pl-muted);
    font-size: 12px;
    opacity: 0.6;
}

/* Empty state */
.cdg-pl-empty {
    text-align: center;
    padding: 60px 20px;
    background: var(--pl-card);
    border: 1px solid var(--pl-border);
    border-radius: var(--pl-radius);
}
.cdg-pl-empty-icon {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border-radius: 20px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #1e40af;
    font-size: 36px;
    margin-bottom: 18px;
}
.cdg-pl-empty h3 { font-size: 18px; font-weight: 800; color: var(--pl-text); margin: 0 0 6px; }
.cdg-pl-empty p { color: var(--pl-muted); font-size: 14px; margin: 0 0 20px; }
.cdg-pl-empty-actions {
    display: flex; gap: 10px; justify-content: center;
    flex-wrap: wrap;
}
.cdg-pl-empty-search {
    text-align: center;
    padding: 50px 20px;
    color: var(--pl-muted);
}
.cdg-pl-empty-search i { font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 10px; }

/* Mobile */
@media (max-width: 768px) {
    .cdg-pl-hero { padding: 22px 20px; }
    .cdg-pl-hero h1 { font-size: 20px; }
    .cdg-pl-table thead { display: none; }
    .cdg-pl-table tbody tr {
        display: block;
        padding: 12px;
        border: 1px solid var(--pl-border);
        border-radius: 10px;
        margin-bottom: 8px;
    }
    .cdg-pl-table tbody td {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0;
        border: 0 !important;
    }
    .cdg-pl-table tbody td::before {
        content: attr(data-lbl);
        font-size: 11px; font-weight: 700;
        color: var(--pl-muted);
        text-transform: uppercase;
    }
    .cdg-pl-table tbody td:first-child, .cdg-pl-table tbody td:last-child { padding-left: 0; padding-right: 0; }
}
</style>

<div class="cdg-pl">

    <!-- HERO -->
    <section class="cdg-pl-hero">
        <div class="cdg-pl-hero-row">
            <div class="cdg-pl-hero-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div class="cdg-pl-hero-text">
                <div class="cdg-pl-hero-eyebrow"><i class="bi bi-shield-fill-check"></i> Hesabım</div>
                <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
                <p>Aktif aboneliklerinizi, hosting paketlerinizi ve domainlerinizi yönetin.</p>
            </div>
            <a href="<?php echo cdg_link('hosting-products'); ?>" class="cdg-pl-hero-action">
                <i class="bi bi-plus-lg"></i> Yeni Hizmet Al
            </a>
        </div>
    </section>

    <!-- STATS / FILTRELER -->
    <div class="cdg-pl-stats">
        <div class="cdg-pl-stat active" data-filter="all">
            <div class="cdg-pl-stat-row">
                <div class="cdg-pl-stat-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1e40af;"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                <div>
                    <div class="cdg-pl-stat-num"><?php echo $stats['total']; ?></div>
                    <div class="cdg-pl-stat-lbl">Tümü</div>
                </div>
            </div>
        </div>
        <div class="cdg-pl-stat" data-filter="hosting">
            <div class="cdg-pl-stat-row">
                <div class="cdg-pl-stat-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#10b981;"><i class="bi bi-hdd-network-fill"></i></div>
                <div>
                    <div class="cdg-pl-stat-num"><?php echo $stats['hosting']; ?></div>
                    <div class="cdg-pl-stat-lbl">Hosting</div>
                </div>
            </div>
        </div>
        <div class="cdg-pl-stat" data-filter="domain">
            <div class="cdg-pl-stat-row">
                <div class="cdg-pl-stat-icon" style="background:linear-gradient(135deg,#cffafe,#a5f3fc);color:#06b6d4;"><i class="bi bi-globe2"></i></div>
                <div>
                    <div class="cdg-pl-stat-num"><?php echo $stats['domain']; ?></div>
                    <div class="cdg-pl-stat-lbl">Domain</div>
                </div>
            </div>
        </div>
        <div class="cdg-pl-stat" data-filter="server">
            <div class="cdg-pl-stat-row">
                <div class="cdg-pl-stat-icon" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#8b5cf6;"><i class="bi bi-server"></i></div>
                <div>
                    <div class="cdg-pl-stat-num"><?php echo $stats['server']; ?></div>
                    <div class="cdg-pl-stat-lbl">Sunucu</div>
                </div>
            </div>
        </div>
        <div class="cdg-pl-stat" data-filter="sms">
            <div class="cdg-pl-stat-row">
                <div class="cdg-pl-stat-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#f59e0b;"><i class="bi bi-chat-dots-fill"></i></div>
                <div>
                    <div class="cdg-pl-stat-num"><?php echo $stats['sms']; ?></div>
                    <div class="cdg-pl-stat-lbl">SMS</div>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($items)): ?>

    <!-- ARAMA -->
    <div class="cdg-pl-toolbar">
        <div class="cdg-pl-search">
            <i class="bi bi-search"></i>
            <input type="text" id="cdgPlSearch" placeholder="Hizmet adı, domain, ID ara..." autocomplete="off">
            <button type="button" class="cdg-pl-search-clear" id="cdgPlSearchClear" title="Temizle">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <!-- TABLO -->
    <div class="cdg-pl-table-wrap">
        <table class="cdg-pl-table" id="cdgPlTable">
            <thead>
                <tr>
                    <th style="width:80px;">#</th>
                    <th>Hizmet</th>
                    <th style="text-align:right;width:120px;">Tutar</th>
                    <th style="text-align:center;width:160px;">Kalan Süre</th>
                    <th style="text-align:center;width:140px;">Durum</th>
                    <th style="width:120px;">İşlem</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($items as $r => $row):
                $name = $row['name'] ?? '-';
                $oid  = $row['id'] ?? '-';
                $type = $row['type'] ?? '';

                $amount = '';
                if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                    try { $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']); } catch(\Throwable $e) {}
                }
                $period = '';
                if(class_exists('View') && method_exists('View', 'period') && isset($row['period_time']) && isset($row['period'])) {
                    try { $period = View::period($row['period_time'], $row['period']); } catch(\Throwable $e) {}
                }

                $duedate = $row['duedate'] ?? '';
                $duedate_format = '-';
                $days_left = null;       // null = bilinmiyor
                $days_status = 'normal'; // normal | soon | critical | expired
                $days_lbl = '';
                $progress_pct = 0;       // 30 günlük progress için (0-100)
                if($duedate && !in_array(substr($duedate,0,4), ['1881','1970','0000'])) {
                    if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
                        $duedate_format = DateManager::format(Config::get("options/date-format"), $duedate);
                    } else {
                        $duedate_format = date('d.m.Y', strtotime($duedate));
                    }
                    $diff_seconds = strtotime($duedate) - time();
                    $days_left = (int)floor($diff_seconds / 86400);

                    if($days_left < 0) {
                        $days_status = 'expired';
                        $days_lbl = abs($days_left) . ' gün geçti';
                        $progress_pct = 100;
                    } elseif($days_left <= 7) {
                        $days_status = 'critical';
                        $days_lbl = $days_left . ' gün kaldı';
                        $progress_pct = max(0, min(100, ($days_left / 30) * 100));
                    } elseif($days_left <= 30) {
                        $days_status = 'soon';
                        $days_lbl = $days_left . ' gün kaldı';
                        $progress_pct = ($days_left / 30) * 100;
                    } else {
                        $days_status = 'normal';
                        $days_lbl = $days_left . ' gün kaldı';
                        $progress_pct = 100;
                    }
                }

                $status = $row['status'] ?? 'unknown';
                $st = $status_meta_map[$status] ?? ['cls' => 'muted', 'lbl' => ucfirst($status), 'icon' => 'question-circle'];

                $tm = $type_meta[$type] ?? ['icon' => 'box-seam', 'color' => '#64748b', 'lbl' => 'Hizmet'];

                $sub_info = '';
                if(isset($row['options']['domain']))         $sub_info = $row['options']['domain'];
                elseif(isset($row['options']['hostname']))   $sub_info = $row['options']['hostname'];
                elseif(isset($row['options']['ip']))         $sub_info = $row['options']['ip'];
                elseif(isset($row['options']['code']))       $sub_info = $row['options']['code'];
                elseif(isset($row['options']['identity']))   $sub_info = $row['options']['identity'];
                elseif($type === 'special' && isset($row['options']['category_name'])) $sub_info = $row['options']['category_name'];

                $can_manage = isset($row['detail_link']) && !in_array($status, ['waiting','inprocess','cancelled']);

                // Search index için tüm aranabilir alanlar
                $search_terms = strtolower($name . ' ' . $oid . ' ' . $sub_info . ' ' . $tm['lbl']);
            ?>
                <tr data-type="<?php echo htmlspecialchars($type, ENT_QUOTES); ?>" data-search="<?php echo htmlspecialchars($search_terms, ENT_QUOTES); ?>">
                    <td data-lbl="ID">
                        <span class="cdg-pl-id">#<?php echo htmlspecialchars((string)$oid, ENT_QUOTES); ?></span>
                    </td>
                    <td data-lbl="Hizmet">
                        <div class="cdg-pl-svc">
                            <div class="cdg-pl-svc-icon" style="background:<?php echo $tm['color']; ?>15;color:<?php echo $tm['color']; ?>;">
                                <i class="bi bi-<?php echo $tm['icon']; ?>"></i>
                            </div>
                            <div>
                                <div class="cdg-pl-svc-name"><?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                                <?php if($sub_info): ?>
                                <div class="cdg-pl-svc-sub">
                                    <i class="bi bi-link-45deg"></i> <?php echo htmlspecialchars($sub_info, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td data-lbl="Tutar" class="cdg-pl-amount">
                        <?php echo htmlspecialchars($amount, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        <?php if($period): ?><div class="cdg-pl-amount-period"><?php echo htmlspecialchars($period, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div><?php endif; ?>
                    </td>
                    <td data-lbl="Bitiş" class="cdg-pl-date">
                        <?php if($days_left === null): ?>
                            <span class="cdg-pl-date-empty">—</span>
                        <?php else: ?>
                            <div class="cdg-pl-due cdg-pl-due-<?php echo $days_status; ?>">
                                <div class="cdg-pl-due-date"><?php echo htmlspecialchars($duedate_format, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                                <div class="cdg-pl-due-bar">
                                    <div class="cdg-pl-due-fill" style="width:<?php echo $progress_pct; ?>%;"></div>
                                </div>
                                <div class="cdg-pl-due-lbl">
                                    <i class="bi bi-<?php echo $days_status === 'expired' ? 'exclamation-triangle-fill' : ($days_status === 'critical' ? 'exclamation-circle-fill' : ($days_status === 'soon' ? 'clock-history' : 'check-circle-fill')); ?>"></i>
                                    <?php echo htmlspecialchars($days_lbl, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td data-lbl="Durum" style="text-align:center;">
                        <span class="cdg-pl-badge cdg-pl-badge-<?php echo $st['cls']; ?>">
                            <i class="bi bi-<?php echo $st['icon']; ?>"></i> <?php echo htmlspecialchars($st['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </td>
                    <td data-lbl="İşlem" style="text-align:right;">
                        <?php if($can_manage): ?>
                            <a href="<?php echo $row['detail_link']; ?>" class="cdg-pl-manage">
                                <i class="bi bi-gear-fill"></i> Yönet
                            </a>
                        <?php else: ?>
                            <span class="cdg-pl-manage-disabled">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cdg-pl-empty-search" id="cdgPlNoResults" style="display:none;">
            <i class="bi bi-search"></i>
            <div>Aramanızla eşleşen hizmet bulunamadı.</div>
        </div>
    </div>

    <?php else: ?>

    <!-- BOŞ DURUM -->
    <div class="cdg-pl-empty">
        <div class="cdg-pl-empty-icon"><i class="bi bi-box-seam"></i></div>
        <h3>Henüz hizmetiniz yok</h3>
        <p>Hosting, domain, sunucu veya SMS paketi alarak hemen başlayın.</p>
        <div class="cdg-pl-empty-actions">
            <a href="<?php echo cdg_link('hosting-products'); ?>" class="cdg-pl-manage" style="padding:11px 20px;font-size:13px;">
                <i class="bi bi-hdd-network-fill"></i> Hosting Al
            </a>
            <a href="<?php echo cdg_link('domain'); ?>" class="cdg-pl-manage" style="padding:11px 20px;font-size:13px;background:linear-gradient(135deg,#06b6d4,#0891b2);">
                <i class="bi bi-globe2"></i> Domain Al
            </a>
        </div>
    </div>

    <?php endif; ?>

</div>

<script>
(function(){
    var stats   = document.querySelectorAll('.cdg-pl-stat');
    var rows    = document.querySelectorAll('#cdgPlTable tbody tr');
    var search  = document.getElementById('cdgPlSearch');
    var clearBtn = document.getElementById('cdgPlSearchClear');
    var noRes   = document.getElementById('cdgPlNoResults');
    var tableWrap = document.querySelector('.cdg-pl-table-wrap table');

    var currentFilter = 'all';
    var currentSearch = '';

    function applyFilters(){
        var visible = 0;
        rows.forEach(function(row){
            var type = row.getAttribute('data-type') || '';
            var srch = row.getAttribute('data-search') || '';
            var typeMatch = (currentFilter === 'all' || type === currentFilter);
            var searchMatch = (!currentSearch || srch.indexOf(currentSearch) !== -1);
            if(typeMatch && searchMatch){
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });
        if(noRes){
            if(visible === 0 && rows.length > 0){
                noRes.style.display = 'block';
                if(tableWrap) tableWrap.style.display = 'none';
            } else {
                noRes.style.display = 'none';
                if(tableWrap) tableWrap.style.display = '';
            }
        }
    }

    // Stat filtreleme
    stats.forEach(function(s){
        s.addEventListener('click', function(){
            stats.forEach(function(x){ x.classList.remove('active'); });
            s.classList.add('active');
            currentFilter = s.getAttribute('data-filter');
            applyFilters();
        });
    });

    // Arama
    if(search){
        search.addEventListener('input', function(){
            currentSearch = search.value.toLowerCase().trim();
            if(clearBtn) clearBtn.classList.toggle('show', currentSearch.length > 0);
            applyFilters();
        });
    }
    if(clearBtn){
        clearBtn.addEventListener('click', function(){
            search.value = '';
            currentSearch = '';
            clearBtn.classList.remove('show');
            applyFilters();
            search.focus();
        });
    }
})();
</script>
