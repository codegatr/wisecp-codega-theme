<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Reseller / Bayi Paneli
 * WiseCP runtime: $info, $rates, $statistics, $product_groups, $links, $discounts
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$wide_content = true;
$hoptions = ["datatables", "iziModal", "select2"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            $url = Controllers::$init->CRLink($slug, $params);
            if($url && (strpos($url, '/(0)') !== false || preg_match('#/0/?$#', $url))) {
                $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
                return $base . '/' . $slug . ($params ? '/' . implode('/', $params) : '');
            }
            return $url;
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        return $base . '/' . $slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$info       = isset($info) && is_array($info) ? $info : [];
$rates      = isset($rates) && is_array($rates) ? $rates : ['default' => 0];
$statistics = isset($statistics) && is_array($statistics) ? $statistics : [];
$product_groups = isset($product_groups) && is_array($product_groups) ? $product_groups : (isset($groups) && is_array($groups) ? $groups : []);
$discounts  = isset($discounts) && is_array($discounts) ? $discounts : [];
$links      = isset($links) && is_array($links) ? $links : [];

$controller_url = $links['controller'] ?? '';
$only_credit_paid = !empty($info['only_credit_paid']);

$default_rate = $rates['default'] ?? 0;
$stat_currency = $statistics['currency'] ?? 'TRY';
$stat_discounts = $statistics['discounts'] ?? 0;

function cdg_res_money($a, $cid = 0) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol') && $cid) {
        return Money::formatter_symbol($a, $cid);
    }
    return number_format((float)$a, 2, ',', '.');
}
?>

<style>
.cdg-res {
    --r-primary: #1e40af;
    --r-success: #10b981;
    --r-warning: #f59e0b;
    --r-bg: #f8fafc;
    --r-card: #fff;
    --r-text: #0f172a;
    --r-muted: #64748b;
    --r-border: #e2e8f0;
    --r-radius: 14px;
    --r-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--r-text);
    background: var(--r-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-res *, .cdg-res *::before, .cdg-res *::after { box-sizing: border-box; }
.cdg-res a { text-decoration: none; color: inherit; }
.cdg-res-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-res-hero {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 50%, #fde047 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #1e3a8a;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(245,158,11,0.25);
}
.cdg-res-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(255,255,255,0.4), transparent 70%);
    pointer-events: none;
}
.cdg-res-hero-row {
    display: flex; align-items: center; gap: 18px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-res-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: #1e3a8a;
    color: #fde047;
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(30,58,138,0.30);
}
.cdg-res-hero-text { flex: 1; min-width: 220px; }
.cdg-res-hero h1 { font-size: 26px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-res-hero p { font-size: 13px; opacity: 0.85; margin: 0; }

.cdg-res-default-rate {
    background: rgba(30,58,138,0.18);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(30,58,138,0.20);
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 800;
    text-align: center;
    flex-shrink: 0;
}
.cdg-res-default-rate .lbl {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.75;
    margin-bottom: 2px;
}
.cdg-res-default-rate .val { font-size: 22px; }

.cdg-res-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.18s;
    text-decoration: none;
    font-family: inherit;
}
.cdg-res-btn-primary {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    box-shadow: 0 6px 18px rgba(30,64,175,0.22);
}
.cdg-res-btn-primary:hover { transform: translateY(-1px); color: #fff; }

/* STATS */
.cdg-res-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-bottom: 22px;
}
.cdg-res-stat-card {
    background: #fff;
    border: 1px solid var(--r-border);
    border-radius: var(--r-radius);
    padding: 18px 22px;
    box-shadow: var(--r-shadow);
    display: flex; align-items: center; gap: 14px;
}
.cdg-res-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
.cdg-res-stat-card-1 .cdg-res-stat-icon { background: linear-gradient(135deg, #10b981, #34d399); }
.cdg-res-stat-card-2 .cdg-res-stat-icon { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.cdg-res-stat-card-3 .cdg-res-stat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.cdg-res-stat-info { flex: 1; }
.cdg-res-stat-label {
    font-size: 11px;
    color: var(--r-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.cdg-res-stat-value {
    font-size: 20px;
    font-weight: 900;
    color: var(--r-text);
}

.cdg-res-card {
    background: #fff;
    border: 1px solid var(--r-border);
    border-radius: var(--r-radius);
    box-shadow: var(--r-shadow);
    margin-bottom: 18px;
    overflow: hidden;
}
.cdg-res-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--r-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-res-card-head h3 {
    font-size: 14px; font-weight: 800; margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-res-card-head h3 i { color: var(--r-primary); }

.cdg-res-card-body { padding: 22px; }

.cdg-res-info-banner {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border: 1px solid #93c5fd;
    border-radius: var(--r-radius);
    padding: 18px 22px;
    color: #1e3a8a;
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 18px;
}
.cdg-res-info-banner i { font-size: 24px; flex-shrink: 0; }
.cdg-res-info-banner div { line-height: 1.5; font-size: 13px; }
.cdg-res-info-banner strong { display: block; margin-bottom: 4px; font-size: 14px; }

.cdg-res-group { margin-bottom: 22px; }
.cdg-res-group:last-child { margin-bottom: 0; }
.cdg-res-group-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--r-text);
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--r-primary);
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-res-group-title i { color: var(--r-primary); }

.cdg-res-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.cdg-res-table th {
    text-align: left;
    padding: 10px 14px;
    background: #f8fafc;
    color: var(--r-muted);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    border-bottom: 1px solid var(--r-border);
}
.cdg-res-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--r-border);
}
.cdg-res-table tr:last-child td { border-bottom: 0; }

.cdg-res-price-old { color: var(--r-muted); text-decoration: line-through; font-size: 12px; }
.cdg-res-price-new { color: var(--r-success); font-weight: 800; font-size: 14px; }
.cdg-res-rate-badge {
    display: inline-block;
    padding: 3px 10px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
}

@media (max-width: 768px) {
    .cdg-res-hero-row { flex-direction: column; text-align: center; }
    .cdg-res-table { font-size: 12px; }
    .cdg-res-table th, .cdg-res-table td { padding: 9px 10px; }
}
</style>

<div class="cdg-res">
<div class="cdg-res-wrap">

    <!-- HERO -->
    <section class="cdg-res-hero">
        <div class="cdg-res-hero-row">
            <div class="cdg-res-hero-icon"><i class="bi bi-shop"></i></div>
            <div class="cdg-res-hero-text">
                <h1>Bayi Paneliniz</h1>
                <p>Özel indirimli fiyatlarla hizmetlerimizi son kullanıcılarınıza sunabilir, kazançlarınızı artırabilirsiniz.</p>
            </div>
            <div class="cdg-res-default-rate">
                <div class="lbl">Varsayılan İndirim</div>
                <div class="val">%<?php echo (float)$default_rate; ?></div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <?php if($statistics): ?>
    <div class="cdg-res-stats">
        <div class="cdg-res-stat-card cdg-res-stat-card-1">
            <div class="cdg-res-stat-icon"><i class="bi bi-percent"></i></div>
            <div class="cdg-res-stat-info">
                <div class="cdg-res-stat-label">Toplam Tasarruf</div>
                <div class="cdg-res-stat-value"><?php echo htmlspecialchars(cdg_res_money($stat_discounts)); ?> <small style="font-size:13px;font-weight:600;"><?php echo htmlspecialchars($stat_currency); ?></small></div>
            </div>
        </div>
        <div class="cdg-res-stat-card cdg-res-stat-card-2">
            <div class="cdg-res-stat-icon"><i class="bi bi-stars"></i></div>
            <div class="cdg-res-stat-info">
                <div class="cdg-res-stat-label">Bayi Statüsü</div>
                <div class="cdg-res-stat-value">Aktif</div>
            </div>
        </div>
        <?php if(!empty($info['credits'])): ?>
        <div class="cdg-res-stat-card cdg-res-stat-card-3">
            <div class="cdg-res-stat-icon"><i class="bi bi-coin"></i></div>
            <div class="cdg-res-stat-info">
                <div class="cdg-res-stat-label">Bayi Kontörü</div>
                <div class="cdg-res-stat-value"><?php echo htmlspecialchars(cdg_res_money($info['credits'])); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- INFO BANNER -->
    <?php if($only_credit_paid): ?>
    <div class="cdg-res-info-banner">
        <i class="bi bi-info-circle"></i>
        <div>
            <strong>Bayi Ödeme Politikası</strong>
            Bayi siparişleriniz yalnızca bayi kontörü ile ödenebilir. Ödeme yapmadan önce kontör yüklediğinizden emin olun.
        </div>
    </div>
    <?php endif; ?>

    <!-- INDIRIM ORANLARI / FIYATLAR -->
    <?php if(!empty($product_groups)): ?>
    <div class="cdg-res-card">
        <div class="cdg-res-card-head">
            <h3><i class="bi bi-tags"></i> Bayi Fiyat Listesi</h3>
        </div>
        <div class="cdg-res-card-body">
            <p style="font-size:13px;color:var(--r-muted);margin:0 0 18px;">Aşağıda her hizmet kategorisi için size tanımlı bayi indirim oranlarını görebilirsiniz.</p>

            <?php foreach($product_groups as $g):
                if(!is_array($g)) continue;
                $g_name = $g['name'] ?? 'Kategori';
                $g_products = $g['products'] ?? [];
                if(empty($g_products)) continue;
            ?>
            <div class="cdg-res-group">
                <div class="cdg-res-group-title">
                    <i class="bi bi-collection"></i>
                    <?php echo htmlspecialchars($g_name); ?>
                </div>

                <div style="overflow-x:auto;">
                <table class="cdg-res-table">
                    <thead>
                        <tr>
                            <th>Hizmet</th>
                            <th>Liste Fiyatı</th>
                            <th>İndirim</th>
                            <th>Bayi Fiyatı</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($g_products as $row):
                            if(!is_array($row)) continue;
                            $r_name = $row['name'] ?? '-';
                            $r_amount = $row['amount'] ?? 0;
                            $r_cid = $row['amount_cid'] ?? 0;
                            $r_rate = $row['rate'] ?? $default_rate;
                            $r_discount = (float)$r_amount * ((float)$r_rate / 100);
                            $r_new = $r_amount - $r_discount;
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($r_name); ?></strong></td>
                            <td><span class="cdg-res-price-old"><?php echo htmlspecialchars(cdg_res_money($r_amount, $r_cid)); ?></span></td>
                            <td><span class="cdg-res-rate-badge">-%<?php echo (float)$r_rate; ?></span></td>
                            <td><span class="cdg-res-price-new"><?php echo htmlspecialchars(cdg_res_money($r_new, $r_cid)); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- API ACCESS -->
    <?php if(!empty($info['api_key']) || !empty($links['api'])): ?>
    <div class="cdg-res-card">
        <div class="cdg-res-card-head">
            <h3><i class="bi bi-code-slash"></i> API Erişimi</h3>
        </div>
        <div class="cdg-res-card-body">
            <p style="font-size:13px;color:var(--r-muted);margin:0 0 14px;">Bayi olarak otomatik sipariş ve yönetim için API entegrasyonumuza erişebilirsiniz.</p>
            <a href="javascript:void(0);" onclick="if(typeof open_modal==='function') open_modal('api_access');" class="cdg-res-btn cdg-res-btn-primary">
                <i class="bi bi-code"></i> API Bilgilerine Eriş
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- INDIRIM GECMIS -->
    <?php if(!empty($discounts)): ?>
    <div class="cdg-res-card">
        <div class="cdg-res-card-head">
            <h3><i class="bi bi-clock-history"></i> Son İndirimler</h3>
        </div>
        <div style="overflow-x:auto;">
        <table class="cdg-res-table">
            <thead>
                <tr>
                    <th>Hizmet</th>
                    <th>Tutar</th>
                    <th>İndirim</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($discounts, 0, 30) as $d):
                    if(!is_array($d)) continue;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($d['name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars(cdg_res_money($d['amount'] ?? 0)); ?></td>
                    <td style="color:var(--r-success);font-weight:700;">-<?php echo htmlspecialchars(cdg_res_money($d['amountd'] ?? 0)); ?></td>
                    <td style="color:var(--r-muted);font-size:12px;"><?php echo htmlspecialchars(date('d.m.Y', strtotime($d['ctime'] ?? 'now'))); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>
