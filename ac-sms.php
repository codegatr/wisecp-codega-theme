<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - SMS Paneli (5 tab: Özet, Gönder, Başlık, Rehber, Raporlar)
 * WiseCP runtime: $product, $proanse, $options, $module_con, $origins, $contacts, $groups, $reports, $links
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$wide_content = true;
$hoptions = ["datatables", "iziModal", "select2"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        global $links;
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
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) {}
        }
        // Son care: $links bakilirsa kullan
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$product   = isset($product) && is_array($product) ? $product : [];
$proanse   = isset($proanse) && is_array($proanse) ? $proanse : $product;
$options   = isset($options) && is_array($options) ? $options : (isset($proanse['options']) ? $proanse['options'] : []);
$origins   = isset($origins) && is_array($origins) ? $origins : [];
$contacts  = isset($contacts) && is_array($contacts) ? $contacts : [];
$groups    = isset($groups) && is_array($groups) ? $groups : [];
$reports   = isset($reports) && is_array($reports) ? $reports : [];
$dimension = isset($dimension) && is_array($dimension) ? $dimension : [];
$last      = isset($last) && is_array($last) ? $last : [];
$links     = isset($links) && is_array($links) ? $links : [];

$controller_url = $links['controller'] ?? '';
$d_id   = $proanse['id'] ?? 0;
$d_name = $proanse['name'] ?? 'SMS Paketi';
$d_status = strtolower($proanse['status'] ?? 'active');

// Krediler / paket bilgisi
$credit_total = $options['total'] ?? ($options['credit'] ?? 0);
$credit_used  = $options['used'] ?? 0;
$credit_remaining = max(0, (int)$credit_total - (int)$credit_used);
$credit_pct = $credit_total > 0 ? round(($credit_used / $credit_total) * 100) : 0;
?>

<style>
.cdg-sms {
    --s-primary: #06b6d4;
    --s-primary-2: #0891b2;
    --s-success: #10b981;
    --s-warning: #f59e0b;
    --s-danger: #ef4444;
    --s-bg: #f8fafc;
    --s-card: #fff;
    --s-text: #0f172a;
    --s-muted: #64748b;
    --s-border: #e2e8f0;
    --s-radius: 14px;
    --s-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--s-text);
    background: var(--s-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-sms *, .cdg-sms *::before, .cdg-sms *::after { box-sizing: border-box; }
.cdg-sms a { text-decoration: none; color: inherit; }
.cdg-sms-wrap { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

.cdg-sms-hero {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #1e40af 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(6,182,212,0.22);
}
.cdg-sms-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-sms-hero-row {
    display: flex; align-items: center; gap: 18px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-sms-hero-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 30px;
    flex-shrink: 0;
}
.cdg-sms-hero-text { flex: 1; min-width: 200px; }
.cdg-sms-hero h1 { font-size: 26px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-sms-hero p { font-size: 13px; opacity: 0.88; margin: 0; }

/* CREDIT METER */
.cdg-sms-credit {
    background: rgba(255,255,255,0.14);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.22);
    padding: 14px 20px;
    border-radius: 14px;
    text-align: center;
    flex-shrink: 0;
    min-width: 200px;
}
.cdg-sms-credit-lbl {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.85;
    margin-bottom: 4px;
}
.cdg-sms-credit-val {
    font-size: 26px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 6px;
}
.cdg-sms-credit-bar {
    height: 6px;
    background: rgba(255,255,255,0.20);
    border-radius: 99px;
    overflow: hidden;
}
.cdg-sms-credit-bar-fill {
    height: 100%;
    background: #fde047;
    border-radius: 99px;
    transition: width 0.5s ease;
}
.cdg-sms-credit-meta {
    font-size: 11px;
    opacity: 0.85;
    margin-top: 6px;
}

/* TAB NAV */
.cdg-sms-tabs {
    background: #fff;
    border: 1px solid var(--s-border);
    border-radius: var(--s-radius);
    padding: 8px;
    box-shadow: var(--s-shadow);
    margin-bottom: 18px;
    display: flex;
    gap: 4px;
    overflow-x: auto;
}
.cdg-sms-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--s-muted);
    cursor: pointer;
    background: transparent;
    border: 0;
    font-family: inherit;
    white-space: nowrap;
    transition: all 0.18s;
}
.cdg-sms-tab:hover { background: var(--s-bg); color: var(--s-text); }
.cdg-sms-tab.active {
    background: var(--s-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(6,182,212,0.25);
}

.cdg-sms-pane { display: none; }
.cdg-sms-pane.active { display: block; }

.cdg-sms-card {
    background: #fff;
    border: 1px solid var(--s-border);
    border-radius: var(--s-radius);
    box-shadow: var(--s-shadow);
    margin-bottom: 18px;
    overflow: hidden;
}
.cdg-sms-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--s-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-sms-card-head h3 {
    font-size: 14px; font-weight: 800; margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-sms-card-head h3 i { color: var(--s-primary); }
.cdg-sms-card-body { padding: 22px; }

.cdg-sms-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
}
.cdg-sms-info-tile {
    background: #f8fafc;
    border: 1px solid var(--s-border);
    border-radius: 10px;
    padding: 14px 16px;
    text-align: center;
}
.cdg-sms-info-tile-lbl {
    font-size: 11px;
    color: var(--s-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    font-weight: 600;
}
.cdg-sms-info-tile-val {
    font-size: 22px;
    font-weight: 900;
    color: var(--s-primary-2);
}

.cdg-sms-input,
.cdg-sms-select,
.cdg-sms-textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--s-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--s-text);
    background: #fff;
    outline: none;
    transition: all 0.18s;
    font-family: inherit;
}
.cdg-sms-input:focus,
.cdg-sms-select:focus,
.cdg-sms-textarea:focus {
    border-color: var(--s-primary);
    box-shadow: 0 0 0 3px rgba(6,182,212,0.10);
}
.cdg-sms-textarea { min-height: 140px; resize: vertical; line-height: 1.6; }

.cdg-sms-field { margin-bottom: 14px; }
.cdg-sms-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--s-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 7px;
}

.cdg-sms-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; border: 0;
    transition: all 0.18s;
    font-family: inherit;
    text-decoration: none;
}
.cdg-sms-btn-primary {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: #fff;
    box-shadow: 0 6px 18px rgba(6,182,212,0.25);
}
.cdg-sms-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-sms-btn-success {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.20);
}
.cdg-sms-btn-success:hover { transform: translateY(-1px); color: #fff; }
.cdg-sms-btn-outline {
    background: #fff;
    color: var(--s-text);
    border: 1px solid var(--s-border);
}
.cdg-sms-btn-outline:hover { border-color: var(--s-primary); color: var(--s-primary-2); }

.cdg-sms-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.cdg-sms-table th {
    background: #f8fafc;
    padding: 10px 14px;
    text-align: left;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: var(--s-muted);
    border-bottom: 1px solid var(--s-border);
}
.cdg-sms-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--s-border);
}
.cdg-sms-table tr:last-child td { border-bottom: 0; }

.cdg-sms-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-sms-badge-success { background: #d1fae5; color: #065f46; }
.cdg-sms-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-sms-badge-danger  { background: #fee2e2; color: #991b1b; }

.cdg-sms-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--s-muted);
}
.cdg-sms-empty i {
    font-size: 48px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 8px;
}

.cdg-sms-counter {
    background: #f8fafc;
    border: 1px solid var(--s-border);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12px;
    color: var(--s-muted);
    display: flex; justify-content: space-between;
    margin-top: 6px;
}
.cdg-sms-counter strong { color: var(--s-primary-2); }

@media (max-width: 768px) {
    .cdg-sms-hero-row { flex-direction: column; text-align: center; }
    .cdg-sms-credit { width: 100%; }
}
</style>

<div class="cdg-sms">
<div class="cdg-sms-wrap">

    <!-- HERO -->
    <section class="cdg-sms-hero">
        <div class="cdg-sms-hero-row">
            <div class="cdg-sms-hero-icon"><i class="bi bi-chat-square-text-fill"></i></div>
            <div class="cdg-sms-hero-text">
                <h1>SMS Paneli</h1>
                <p>Toplu SMS gönderimi yapın, gönderici başlıklarınızı yönetin, raporlara ulaşın.</p>
            </div>
            <div class="cdg-sms-credit">
                <div class="cdg-sms-credit-lbl">Kalan Kredi</div>
                <div class="cdg-sms-credit-val"><?php echo number_format($credit_remaining, 0, ',', '.'); ?></div>
                <div class="cdg-sms-credit-bar">
                    <div class="cdg-sms-credit-bar-fill" style="width: <?php echo max(0, 100 - $credit_pct); ?>%;"></div>
                </div>
                <div class="cdg-sms-credit-meta">
                    <?php echo number_format($credit_used, 0, ',', '.'); ?> kullanıldı / <?php echo number_format($credit_total, 0, ',', '.'); ?> toplam
                </div>
            </div>
        </div>
    </section>

    <!-- TAB NAV -->
    <div class="cdg-sms-tabs">
        <button class="cdg-sms-tab active" data-pane="summary">
            <i class="bi bi-info-circle"></i> Özet
        </button>
        <button class="cdg-sms-tab" data-pane="send">
            <i class="bi bi-send"></i> SMS Gönder
        </button>
        <button class="cdg-sms-tab" data-pane="origins">
            <i class="bi bi-card-text"></i> Başlık İşlemleri
        </button>
        <button class="cdg-sms-tab" data-pane="contacts">
            <i class="bi bi-person-rolodex"></i> Rehber
        </button>
        <button class="cdg-sms-tab" data-pane="reports">
            <i class="bi bi-bar-chart"></i> Raporlar
        </button>
    </div>

    <!-- PANE: ÖZET -->
    <div class="cdg-sms-pane active" id="cdg-sms-pane-summary">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-head">
                <h3><i class="bi bi-info-circle"></i> Hizmet Özeti</h3>
            </div>
            <div class="cdg-sms-card-body">
                <div class="cdg-sms-info-grid">
                    <div class="cdg-sms-info-tile">
                        <div class="cdg-sms-info-tile-lbl">Paket</div>
                        <div class="cdg-sms-info-tile-val" style="font-size:14px;"><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                    </div>
                    <div class="cdg-sms-info-tile">
                        <div class="cdg-sms-info-tile-lbl">Toplam Kredi</div>
                        <div class="cdg-sms-info-tile-val"><?php echo number_format($credit_total, 0, ',', '.'); ?></div>
                    </div>
                    <div class="cdg-sms-info-tile">
                        <div class="cdg-sms-info-tile-lbl">Kullanılan</div>
                        <div class="cdg-sms-info-tile-val"><?php echo number_format($credit_used, 0, ',', '.'); ?></div>
                    </div>
                    <div class="cdg-sms-info-tile">
                        <div class="cdg-sms-info-tile-lbl">Kalan</div>
                        <div class="cdg-sms-info-tile-val" style="color:#10b981;"><?php echo number_format($credit_remaining, 0, ',', '.'); ?></div>
                    </div>
                    <div class="cdg-sms-info-tile">
                        <div class="cdg-sms-info-tile-lbl">Onaylı Başlık</div>
                        <div class="cdg-sms-info-tile-val"><?php echo (int)count($origins); ?></div>
                    </div>
                    <div class="cdg-sms-info-tile">
                        <div class="cdg-sms-info-tile-lbl">Rehber Kayıt</div>
                        <div class="cdg-sms-info-tile-val"><?php echo (int)count($contacts); ?></div>
                    </div>
                </div>

                <?php if(!empty($last)): ?>
                <div style="margin-top:18px;padding:14px 18px;background:#dbeafe;border:1px solid #93c5fd;border-radius:10px;color:#1e3a8a;font-size:13px;">
                    <strong><i class="bi bi-clock-history"></i> Son Gönderim:</strong>
                    <?php echo htmlspecialchars($last['part'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> -
                    <?php echo htmlspecialchars($last['end'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- PANE: GÖNDER -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-send">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-head">
                <h3><i class="bi bi-send"></i> SMS Gönderimi</h3>
            </div>
            <div class="cdg-sms-card-body">
                <form id="cdg-sms-send-form" onsubmit="return false;">
                    <div class="cdg-sms-field">
                        <label class="cdg-sms-label">Gönderici Başlığı</label>
                        <select id="cdg-sms-origin" class="cdg-sms-select">
                            <option value="">— Onaylı Başlık Seçin —</option>
                            <?php foreach($origins as $origin):
                                if(!is_array($origin)) continue;
                                if(strtolower($origin['status'] ?? '') !== 'approved' && strtolower($origin['status'] ?? '') !== 'active') continue;
                            ?>
                            <option value="<?php echo htmlspecialchars($origin['id'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($origin['name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="cdg-sms-field">
                        <label class="cdg-sms-label">Alıcılar (her satıra bir numara)</label>
                        <textarea id="cdg-sms-recipients" class="cdg-sms-textarea" placeholder="5XX XXX XX XX&#10;5XX XXX XX XX&#10;..." style="font-family:'JetBrains Mono', monospace;font-size:13px;"></textarea>
                    </div>

                    <div class="cdg-sms-field">
                        <label class="cdg-sms-label">Veya Rehber'den Grup Seç</label>
                        <select id="cdg-sms-group" class="cdg-sms-select">
                            <option value="">— Grup Seçin (opsiyonel) —</option>
                            <?php foreach($groups as $group):
                                if(!is_array($group)) continue;
                            ?>
                            <option value="<?php echo htmlspecialchars($group['id'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($group['name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                <?php if(isset($group['numbers'])): ?>(<?php echo count((array)$group['numbers']); ?> kişi)<?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="cdg-sms-field">
                        <label class="cdg-sms-label">Mesaj İçeriği</label>
                        <textarea id="cdg-sms-message" class="cdg-sms-textarea" placeholder="Mesajınızı buraya yazın..." maxlength="918" oninput="cdgSmsCount()"></textarea>
                        <div class="cdg-sms-counter">
                            <span><strong id="cdg-sms-char">0</strong> karakter</span>
                            <span><strong id="cdg-sms-parts">0</strong> SMS parçası</span>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;">
                        <button type="button" class="cdg-sms-btn cdg-sms-btn-primary" onclick="cdgSmsSend()">
                            <i class="bi bi-send-fill"></i> SMS Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PANE: BAŞLIK -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-origins">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-head">
                <h3><i class="bi bi-card-text"></i> Gönderici Başlıkları</h3>
                <button type="button" class="cdg-sms-btn cdg-sms-btn-primary cdg-sms-btn-sm" onclick="cdgSmsNewOrigin()" style="padding:7px 14px;font-size:12px;">
                    <i class="bi bi-plus"></i> Yeni Başlık
                </button>
            </div>
            <?php if(empty($origins)): ?>
            <div class="cdg-sms-empty">
                <i class="bi bi-card-text"></i>
                <p>Henüz onaylı gönderici başlığınız yok. Yeni başlık talep edebilirsiniz.</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="cdg-sms-table">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Durum</th>
                        <th>Onay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($origins as $origin):
                        if(!is_array($origin)) continue;
                        $st = strtolower($origin['status'] ?? '');
                        $prereg = !empty($origin['prereg']);
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($origin['name'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong></td>
                        <td>
                            <?php if($st === 'approved' || $st === 'active'): ?>
                            <span class="cdg-sms-badge cdg-sms-badge-success">Aktif</span>
                            <?php elseif($st === 'pending' || $st === 'waiting'): ?>
                            <span class="cdg-sms-badge cdg-sms-badge-warning">Bekliyor</span>
                            <?php else: ?>
                            <span class="cdg-sms-badge cdg-sms-badge-danger">Reddedildi</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $prereg ? '<i class="bi bi-check-circle" style="color:#10b981;"></i> Önkayıtlı' : 'Standart'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PANE: REHBER -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-contacts">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-head">
                <h3><i class="bi bi-person-rolodex"></i> Rehber Grupları</h3>
                <button type="button" class="cdg-sms-btn cdg-sms-btn-primary" onclick="cdgSmsNewGroup()" style="padding:7px 14px;font-size:12px;">
                    <i class="bi bi-plus"></i> Yeni Grup
                </button>
            </div>
            <?php if(empty($groups)): ?>
            <div class="cdg-sms-empty">
                <i class="bi bi-person-rolodex"></i>
                <p>Henüz rehber grubunuz yok. Toplu gönderim için grup oluşturun.</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="cdg-sms-table">
                <thead>
                    <tr>
                        <th>Grup Adı</th>
                        <th>Kişi Sayısı</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($groups as $group):
                        if(!is_array($group)) continue;
                        $g_count = isset($group['numbers']) ? count((array)$group['numbers']) : 0;
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($group['name'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong></td>
                        <td><?php echo $g_count; ?></td>
                        <td>
                            <button type="button" class="cdg-sms-btn cdg-sms-btn-outline" style="padding:6px 12px;font-size:12px;" onclick="cdgSmsEditGroup(<?php echo (int)($group['id'] ?? 0); ?>)">
                                <i class="bi bi-pencil"></i> Düzenle
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PANE: RAPORLAR -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-reports">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-head">
                <h3><i class="bi bi-bar-chart"></i> Gönderim Raporları</h3>
            </div>
            <?php if(empty($reports)): ?>
            <div class="cdg-sms-empty">
                <i class="bi bi-graph-up"></i>
                <p>Henüz gönderim raporunuz yok. SMS göndermeye başladığınızda burada listelenecek.</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="cdg-sms-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Başlık</th>
                        <th>Mesaj</th>
                        <th>Alıcı</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($reports, 0, 50) as $report):
                        if(!is_array($report)) continue;
                        $r_status = strtolower($report['status'] ?? 'sent');
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($report['ctime'] ?? 'now')), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($report['origin'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td style="max-width:300px;font-size:12px;color:var(--s-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($report['message'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($report['count'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td>
                            <?php if($r_status === 'delivered' || $r_status === 'sent'): ?>
                            <span class="cdg-sms-badge cdg-sms-badge-success">Gönderildi</span>
                            <?php elseif($r_status === 'pending'): ?>
                            <span class="cdg-sms-badge cdg-sms-badge-warning">Bekliyor</span>
                            <?php else: ?>
                            <span class="cdg-sms-badge cdg-sms-badge-danger">Hata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>

<script>
// Tab switching
(function(){
    document.querySelectorAll('.cdg-sms-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            var pane = this.getAttribute('data-pane');
            document.querySelectorAll('.cdg-sms-tab').forEach(function(t){ t.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.cdg-sms-pane').forEach(function(p){ p.classList.remove('active'); });
            var target = document.getElementById('cdg-sms-pane-' + pane);
            if(target) target.classList.add('active');
            try { history.replaceState(null, '', '#' + pane); } catch(e) {}
        });
    });
    if(location.hash) {
        var hash = location.hash.substring(1);
        var t = document.querySelector('.cdg-sms-tab[data-pane="' + hash + '"]');
        if(t) t.click();
    }
})();

// Karakter sayacı
function cdgSmsCount() {
    var msg = document.getElementById('cdg-sms-message');
    if(!msg) return;
    var len = msg.value.length;
    document.getElementById('cdg-sms-char').textContent = len;
    var parts = 0;
    if(len === 0) parts = 0;
    else if(len <= 160) parts = 1;
    else parts = Math.ceil(len / 153);
    document.getElementById('cdg-sms-parts').textContent = parts;
}

function cdgSmsSend() {
    var origin = document.getElementById('cdg-sms-origin').value;
    var recipients = document.getElementById('cdg-sms-recipients').value;
    var message = document.getElementById('cdg-sms-message').value;
    var groupId = document.getElementById('cdg-sms-group').value;

    if(!origin) {
        if(typeof alert_error === 'function') alert_error('Gönderici başlığı seçin', {timer: 3000});
        return;
    }
    if(!recipients && !groupId) {
        if(typeof alert_error === 'function') alert_error('Alıcı veya grup seçin', {timer: 3000});
        return;
    }
    if(!message.trim()) {
        if(typeof alert_error === 'function') alert_error('Mesaj boş olamaz', {timer: 3000});
        return;
    }
    if(!confirm('Mesaj gönderilecek. Devam edilsin mi?')) return;
    if(typeof MioAjax !== 'function') return;

    MioAjax({
        url: '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',
        type: 'post',
        data: {
            operation: 'send_sms',
            id: <?php echo (int)$d_id; ?>,
            origin: origin,
            recipients: recipients,
            message: message,
            group_id: groupId
        },
        result: function(r){
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'SMS gönderildi', {timer: 2500});
                document.getElementById('cdg-sms-message').value = '';
                document.getElementById('cdg-sms-recipients').value = '';
                cdgSmsCount();
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 3000});
            }
        }
    });
}

function cdgSmsNewOrigin() {
    if(typeof alert_info === 'function') alert_info('Başlık talep formu yakında eklenecek', {timer: 2500});
}
function cdgSmsNewGroup() {
    if(typeof alert_info === 'function') alert_info('Yeni grup formu yakında eklenecek', {timer: 2500});
}
function cdgSmsEditGroup(id) {
    if(typeof alert_info === 'function') alert_info('Grup düzenleme yakında eklenecek', {timer: 2500});
}
</script>
