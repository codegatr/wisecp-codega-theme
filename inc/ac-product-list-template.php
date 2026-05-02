<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Ortak Hizmet Listesi Template
 * Kullanici dosyalari (ac-products-hosting/server/sms/software/special) bu dosyayi include eder
 *
 * Beklenen variable'lar (caller dosya tarafindan tanımlanmalı):
 *   $cdg_list_kind    : 'hosting' | 'server' | 'sms' | 'software' | 'special'
 *   $cdg_list_title   : 'Hosting Hizmetlerim'
 *   $cdg_list_subtitle: 'Tüm hosting paketlerinizi yönetin...'
 *   $cdg_list_icon    : 'hdd-network-fill' (Bootstrap Icons)
 *   $cdg_list_color   : '#10b981' (gradient için)
 *   $cdg_list_shop_slug : 'products' (yeni hizmet al butonu için)
 *
 * WiseCP runtime: $products, $filter_counts, $links
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
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
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) {}
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

// === Defansif defaults ===
$products       = isset($products) && is_array($products) ? $products : [];
$filter_counts  = isset($filter_counts) && is_array($filter_counts) ? $filter_counts : [];
$links          = isset($links) && is_array($links) ? $links : [];

$cdg_list_kind     = $cdg_list_kind ?? 'hosting';
$cdg_list_title    = $cdg_list_title ?? 'Hizmetlerim';
$cdg_list_subtitle = $cdg_list_subtitle ?? '';
$cdg_list_icon     = $cdg_list_icon ?? 'hdd-network-fill';
$cdg_list_color    = $cdg_list_color ?? '#1e40af';
$cdg_list_shop_slug = $cdg_list_shop_slug ?? 'products';
$cdg_list_shop_param = $cdg_list_shop_param ?? [$cdg_list_kind];

$cnt_all       = (int)($filter_counts['all'] ?? 0);
$cnt_active    = (int)($filter_counts['active'] ?? 0);
$cnt_inprocess = (int)($filter_counts['inprocess'] ?? 0);
$cnt_waiting   = (int)($filter_counts['waiting'] ?? 0);
$cnt_suspended = (int)($filter_counts['suspended'] ?? 0);
$cnt_cancelled = (int)($filter_counts['cancelled'] ?? 0);
$cnt_expired   = (int)($filter_counts['expired'] ?? 0);

$shop_url = cdg_link($cdg_list_shop_slug, $cdg_list_shop_param);

// Status -> rozet
function cdg_pl_status_class($status) {
    $map = [
        'active'    => 'cdg-pl-badge-success',
        'inprocess' => 'cdg-pl-badge-warning',
        'waiting'   => 'cdg-pl-badge-info',
        'suspended' => 'cdg-pl-badge-warning',
        'cancelled' => 'cdg-pl-badge-danger',
        'expired'   => 'cdg-pl-badge-danger',
    ];
    return $map[strtolower($status)] ?? 'cdg-pl-badge-info';
}
function cdg_pl_status_label($status) {
    $map = [
        'active'    => 'Aktif',
        'inprocess' => 'İşlemde',
        'waiting'   => 'Onay Bekliyor',
        'suspended' => 'Askıda',
        'cancelled' => 'İptal',
        'expired'   => 'Süresi Doldu',
    ];
    return $map[strtolower($status)] ?? ucfirst($status);
}
?>

<style>
.cdg-pl {
    --pl-primary: #1e40af;
    --pl-success: #10b981;
    --pl-warning: #f59e0b;
    --pl-danger: #ef4444;
    --pl-info: #06b6d4;
    --pl-bg: #f8fafc;
    --pl-card: #fff;
    --pl-text: #0f172a;
    --pl-muted: #64748b;
    --pl-border: #e2e8f0;
    --pl-radius: 14px;
    --pl-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --pl-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--pl-text);
    background: var(--pl-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-pl *, .cdg-pl *::before, .cdg-pl *::after { box-sizing: border-box; }
.cdg-pl a { text-decoration: none; color: inherit; }
.cdg-pl-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-pl-hero {
    background: linear-gradient(135deg, <?php echo $cdg_list_color; ?> 0%, #3b82f6 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
}
.cdg-pl-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-pl-hero-row {
    display: flex; align-items: center; gap: 20px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-pl-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
}
.cdg-pl-hero-text { flex: 1; min-width: 240px; }
.cdg-pl-hero-text h1 { font-size: 26px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-pl-hero-text p { font-size: 14px; opacity: 0.85; margin: 0; }

.cdg-pl-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
    font-family: inherit;
}
.cdg-pl-btn-gold {
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1e3a8a;
    box-shadow: 0 6px 18px rgba(252,211,77,0.30);
}
.cdg-pl-btn-gold:hover { transform: translateY(-1px); color: #1e3a8a; }
.cdg-pl-btn-outline {
    background: #fff;
    color: var(--pl-text);
    border: 1px solid var(--pl-border);
}
.cdg-pl-btn-outline:hover { border-color: var(--pl-primary); color: var(--pl-primary); }
.cdg-pl-btn-sm { padding: 7px 14px; font-size: 12px; }

.cdg-pl-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
    flex-wrap: wrap;
    background: #fff;
    border-radius: var(--pl-radius);
    padding: 8px;
    box-shadow: var(--pl-shadow);
    border: 1px solid var(--pl-border);
}
.cdg-pl-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--pl-muted);
    cursor: pointer;
    transition: all 0.18s;
    background: transparent;
    border: 0;
    font-family: inherit;
}
.cdg-pl-tab:hover { background: var(--pl-bg); color: var(--pl-text); }
.cdg-pl-tab.active {
    background: var(--pl-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(30,64,175,0.22);
}
.cdg-pl-tab .count {
    background: rgba(0,0,0,0.10);
    padding: 2px 8px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
}
.cdg-pl-tab.active .count { background: rgba(255,255,255,0.22); }

.cdg-pl-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
.cdg-pl-card {
    background: var(--pl-card);
    border: 1px solid var(--pl-border);
    border-radius: var(--pl-radius);
    padding: 18px 22px;
    box-shadow: var(--pl-shadow);
    transition: all 0.18s;
    display: grid;
    grid-template-columns: auto 1fr auto auto;
    gap: 18px;
    align-items: center;
}
.cdg-pl-card:hover { box-shadow: var(--pl-shadow-lg); transform: translateY(-1px); }
.cdg-pl-card-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, <?php echo $cdg_list_color; ?>, #3b82f6);
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
.cdg-pl-card-body { min-width: 0; }
.cdg-pl-card-name {
    font-size: 16px; font-weight: 800;
    color: var(--pl-text);
    margin-bottom: 4px;
    word-break: break-word;
}
.cdg-pl-card-meta {
    font-size: 12px;
    color: var(--pl-muted);
    display: flex; gap: 14px; flex-wrap: wrap;
}
.cdg-pl-card-meta span { display: inline-flex; align-items: center; gap: 5px; }
.cdg-pl-card-meta i { font-size: 13px; color: #94a3b8; }

.cdg-pl-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}
.cdg-pl-badge-success { background: #d1fae5; color: #065f46; }
.cdg-pl-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-pl-badge-danger  { background: #fee2e2; color: #991b1b; }
.cdg-pl-badge-info    { background: #dbeafe; color: #1e40af; }

.cdg-pl-empty {
    text-align: center;
    padding: 60px 20px;
    background: var(--pl-card);
    border: 2px dashed var(--pl-border);
    border-radius: var(--pl-radius);
}
.cdg-pl-empty-icon { font-size: 56px; color: #cbd5e1; margin-bottom: 12px; }
.cdg-pl-empty h3 { font-size: 18px; font-weight: 800; color: var(--pl-text); margin: 0 0 6px; }
.cdg-pl-empty p { font-size: 14px; color: var(--pl-muted); margin: 0 0 18px; }

@media (max-width: 768px) {
    .cdg-pl-card { grid-template-columns: auto 1fr; gap: 14px; }
    .cdg-pl-card-status, .cdg-pl-card-action { grid-column: 1 / -1; }
    .cdg-pl-card-action { display: flex; gap: 8px; }
    .cdg-pl-hero { padding: 22px 20px; }
    .cdg-pl-hero-text h1 { font-size: 22px; }
}
</style>

<div class="cdg-pl">
<div class="cdg-pl-wrap">

    <section class="cdg-pl-hero">
        <div class="cdg-pl-hero-row">
            <div class="cdg-pl-hero-icon"><i class="bi bi-<?php echo htmlspecialchars($cdg_list_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i></div>
            <div class="cdg-pl-hero-text">
                <h1><?php echo htmlspecialchars($cdg_list_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
                <p><?php echo htmlspecialchars($cdg_list_subtitle, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="<?php echo htmlspecialchars($shop_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pl-btn cdg-pl-btn-gold">
                    <i class="bi bi-plus-circle"></i> Yeni Sipariş
                </a>
            </div>
        </div>
    </section>

    <div class="cdg-pl-tabs">
        <button class="cdg-pl-tab active" data-filter="all">
            <i class="bi bi-grid-3x3-gap"></i> Tümü <span class="count"><?php echo $cnt_all; ?></span>
        </button>
        <?php if($cnt_active > 0): ?>
        <button class="cdg-pl-tab" data-filter="active">
            <i class="bi bi-check-circle"></i> Aktif <span class="count"><?php echo $cnt_active; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_inprocess > 0): ?>
        <button class="cdg-pl-tab" data-filter="inprocess">
            <i class="bi bi-clock-history"></i> İşlemde <span class="count"><?php echo $cnt_inprocess; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_waiting > 0): ?>
        <button class="cdg-pl-tab" data-filter="waiting">
            <i class="bi bi-hourglass-split"></i> Onay <span class="count"><?php echo $cnt_waiting; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_suspended > 0): ?>
        <button class="cdg-pl-tab" data-filter="suspended">
            <i class="bi bi-pause-circle"></i> Askıda <span class="count"><?php echo $cnt_suspended; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_cancelled > 0): ?>
        <button class="cdg-pl-tab" data-filter="cancelled">
            <i class="bi bi-x-circle"></i> İptal <span class="count"><?php echo $cnt_cancelled; ?></span>
        </button>
        <?php endif; ?>
        <?php if($cnt_expired > 0): ?>
        <button class="cdg-pl-tab" data-filter="expired">
            <i class="bi bi-calendar-x"></i> Süresi Doldu <span class="count"><?php echo $cnt_expired; ?></span>
        </button>
        <?php endif; ?>
    </div>

    <?php if(empty($products)): ?>
    <div class="cdg-pl-empty">
        <div class="cdg-pl-empty-icon"><i class="bi bi-<?php echo htmlspecialchars($cdg_list_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i></div>
        <h3>Henüz <?php echo htmlspecialchars($cdg_list_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> yok</h3>
        <p>İhtiyaçlarınıza uygun pakete bakarak hemen sipariş verebilirsiniz.</p>
        <a href="<?php echo htmlspecialchars($shop_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pl-btn cdg-pl-btn-gold">
            <i class="bi bi-plus-circle"></i> Hemen Sipariş Ver
        </a>
    </div>
    <?php else: ?>
    <div class="cdg-pl-grid" id="cdg-pl-list">
        <?php foreach($products as $row):
            $row_id     = $row['id'] ?? 0;
            $row_name   = $row['name'] ?? 'Hizmet';
            $row_status = strtolower($row['status'] ?? 'unknown');
            $row_due    = $row['duedate'] ?? '';
            $row_period = $row['period'] ?? '';
            $row_ptime  = $row['period_time'] ?? '';
            $row_amt    = $row['amount'] ?? 0;
            $row_cid    = $row['amount_cid'] ?? 0;
            $row_link   = $row['detail_link'] ?? '#';
            $row_options = isset($row['options']) && is_array($row['options']) ? $row['options'] : [];
            $row_extra  = '';
            if(isset($row_options['domain'])) $row_extra = $row_options['domain'];
            elseif(isset($row_options['hostname'])) $row_extra = $row_options['hostname'];
            elseif(isset($row_options['title'])) $row_extra = $row_options['title'];

            $price_str = '';
            if($row_amt && $row_cid && class_exists('Money') && method_exists('Money','formatter_symbol')) {
                $price_str = Money::formatter_symbol($row_amt, $row_cid);
            }
            $period_text = '';
            if($row_period && $row_ptime) $period_text = $row_period . ' ' . $row_ptime;

            $blocked = isset($row_options['block_access']);
        ?>
        <div class="cdg-pl-card" data-status="<?php echo htmlspecialchars($row_status, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <div class="cdg-pl-card-icon"><i class="bi bi-<?php echo htmlspecialchars($cdg_list_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i></div>
            <div class="cdg-pl-card-body">
                <div class="cdg-pl-card-name"><?php echo htmlspecialchars($row_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <?php if($row_extra): ?>
                <div style="font-size:13px;color:var(--pl-primary);font-weight:600;margin-bottom:4px;">
                    <i class="bi bi-link-45deg"></i> <?php echo htmlspecialchars($row_extra, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </div>
                <?php endif; ?>
                <div class="cdg-pl-card-meta">
                    <?php if($row_due): ?>
                    <span><i class="bi bi-calendar-check"></i> Bitiş: <?php echo htmlspecialchars($row_due, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($period_text): ?>
                    <span><i class="bi bi-clock"></i> <?php echo htmlspecialchars($period_text, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($price_str): ?>
                    <span><i class="bi bi-tag"></i> <?php echo htmlspecialchars($price_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cdg-pl-card-status">
                <span class="cdg-pl-badge <?php echo cdg_pl_status_class($row_status); ?>">
                    <?php echo htmlspecialchars(cdg_pl_status_label($row_status), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </span>
            </div>
            <div class="cdg-pl-card-action">
                <?php if($blocked || in_array($row_status, ['waiting','inprocess','cancelled'])): ?>
                <span class="cdg-pl-btn cdg-pl-btn-outline cdg-pl-btn-sm" style="opacity:0.5;cursor:not-allowed;">
                    <i class="bi bi-gear"></i> Yönet
                </span>
                <?php else: ?>
                <a href="<?php echo htmlspecialchars($row_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pl-btn cdg-pl-btn-outline cdg-pl-btn-sm">
                    <i class="bi bi-gear"></i> Yönet
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
(function(){
    document.querySelectorAll('.cdg-pl-tab').forEach(function(tab){
        tab.addEventListener('click', function(e){
            e.preventDefault();
            var filter = (this.getAttribute('data-filter') || '').toLowerCase();
            document.querySelectorAll('.cdg-pl-tab').forEach(function(t){ t.classList.remove('active'); });
            this.classList.add('active');

            var visible = 0;
            document.querySelectorAll('.cdg-pl-card').forEach(function(card){
                var s = (card.getAttribute('data-status') || '').toLowerCase();
                if(filter === 'all' || s === filter) {
                    card.style.display = 'grid';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });

            var grid = document.getElementById('cdg-pl-list');
            var emptyMsg = document.getElementById('cdg-pl-no-result');
            if(visible === 0 && grid) {
                if(!emptyMsg) {
                    emptyMsg = document.createElement('div');
                    emptyMsg.id = 'cdg-pl-no-result';
                    emptyMsg.style.cssText = 'text-align:center;padding:32px;color:#64748b;font-size:14px;background:#fff;border:2px dashed #e2e8f0;border-radius:14px;margin-top:12px;';
                    emptyMsg.innerHTML = '<i class="bi bi-funnel" style="font-size:32px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>Bu filtreye uygun hizmet bulunamadı.';
                    grid.parentNode.insertBefore(emptyMsg, grid.nextSibling);
                }
                emptyMsg.style.display = '';
            } else if(emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        });
    });
})();
</script>
