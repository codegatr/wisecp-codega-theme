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

// Bakiye bilgisi
$user_balance = 0;
$user_bal_cid = 0;
$user_balance_str = '0,00';
if(class_exists('User') && isset(User::$init->info)) {
    $info = User::$init->info;
    $user_balance = isset($info['balance']) ? $info['balance'] : 0;
    $user_bal_cid = isset($info['balance_cid']) ? $info['balance_cid'] : 0;
    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $user_bal_cid) {
        $user_balance_str = Money::formatter_symbol($user_balance, $user_bal_cid);
    } else {
        $user_balance_str = number_format((float)$user_balance, 2, ',', '.');
    }
}

$transactions = isset($list) ? $list : (isset($transactions) ? $transactions : []);
?>
<?php
// Ek hesaplamalar
$controller_url_bal = isset($links['controller']) ? $links['controller'] : '';
$min_buy = (class_exists('Config') && method_exists('Config','get')) ? @Config::get('credit_settings/min_purchase') : 0;
$max_buy = (class_exists('Config') && method_exists('Config','get')) ? @Config::get('credit_settings/max_purchase') : 0;
$user_curr = isset($currency) && $currency ? $currency : 'TRY';

// Otomatik ödeme + min eşik
$auto_pay_active = false;
$balance_min_threshold = '0';
if(class_exists('User') && isset(User::$init->info)) {
    $auto_pay_active = !empty(User::$init->info['auto_payment_by_credit']);
    $balance_min_threshold = User::$init->info['balance_min'] ?? '0';
}

// İstatistikler
$total_loaded = 0;
$total_spent = 0;
$tx_count = 0;
if(!empty($transactions) && is_array($transactions)) {
    $tx_count = count($transactions);
    foreach($transactions as $row) {
        $type = $row['type'] ?? '';
        $amount = (float)($row['amount'] ?? 0);
        if(in_array($type, ['credit', 'add', 'income'])) $total_loaded += $amount;
        else $total_spent += $amount;
    }
}
?>

<!-- KURUMSAL BAKİYE PANEL -->
<div class="cdg-bal-shell">
    <div class="cdg-bal-shell-head">
        <div class="cdg-bal-shell-head-left">
            <div class="cdg-bal-shell-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <h1 class="cdg-bal-shell-title">Bakiyem</h1>
                <div class="cdg-bal-shell-sub">Hesabınızdaki bakiyeyi görüntüleyin, yükleyin ve geçmişi takip edin</div>
            </div>
        </div>
        <div class="cdg-bal-shell-actions">
            <a href="<?php echo cdg_link('ac-ps-invoices'); ?>" class="cdg-bal-btn">
                <i class="bi bi-receipt"></i> Faturalarım
            </a>
            <a href="<?php echo cdg_link('ac-dashboard'); ?>" class="cdg-bal-btn">
                <i class="bi bi-speedometer2"></i> Panele Dön
            </a>
        </div>
    </div>

    <div class="cdg-bal-shell-body">
        <!-- ÜST: 3'lü Stat -->
        <div class="cdg-bal-stats">
            <div class="cdg-bal-stat cdg-bal-stat-primary">
                <div class="cdg-bal-stat-label">Mevcut Bakiyeniz</div>
                <div class="cdg-bal-stat-value"><?php echo $user_balance_str; ?></div>
                <div class="cdg-bal-stat-sub">
                    <i class="bi bi-shield-check"></i> Güvenli olarak saklanır
                </div>
            </div>
            <div class="cdg-bal-stat">
                <div class="cdg-bal-stat-label">Toplam Yüklenen</div>
                <div class="cdg-bal-stat-value-sm" style="color:#15803d;">
                    +<?php
                    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $user_bal_cid) {
                        echo Money::formatter_symbol($total_loaded, $user_bal_cid);
                    } else {
                        echo number_format($total_loaded, 2, ',', '.') . ' ' . $user_curr;
                    }
                    ?>
                </div>
                <div class="cdg-bal-stat-sub"><i class="bi bi-arrow-down-circle"></i> <?php echo $tx_count; ?> işlem</div>
            </div>
            <div class="cdg-bal-stat">
                <div class="cdg-bal-stat-label">Toplam Harcama</div>
                <div class="cdg-bal-stat-value-sm" style="color:#dc2626;">
                    -<?php
                    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $user_bal_cid) {
                        echo Money::formatter_symbol($total_spent, $user_bal_cid);
                    } else {
                        echo number_format($total_spent, 2, ',', '.') . ' ' . $user_curr;
                    }
                    ?>
                </div>
                <div class="cdg-bal-stat-sub"><i class="bi bi-arrow-up-circle"></i> Faturalardan</div>
            </div>
        </div>

        <div class="cdg-bal-grid">
            <!-- BAKİYE YÜKLEME KARTI -->
            <div class="cdg-bal-card">
                <div class="cdg-bal-card-head">
                    <h3><i class="bi bi-plus-circle-fill" style="color:#10b981;"></i> Bakiye Yükle</h3>
                    <span class="cdg-bal-card-sub">Hızlı ödeme için bakiye yükleyin</span>
                </div>
                <div class="cdg-bal-card-body">
                    <!-- HIZLI MİKTAR BUTONLARI -->
                    <div class="cdg-bal-quick-amounts">
                        <button type="button" class="cdg-bal-quick" onclick="cdgBalSet(50)">50 ₺</button>
                        <button type="button" class="cdg-bal-quick" onclick="cdgBalSet(100)">100 ₺</button>
                        <button type="button" class="cdg-bal-quick cdg-bal-quick-popular" onclick="cdgBalSet(250)">
                            250 ₺
                            <span class="cdg-bal-popular-badge">Popüler</span>
                        </button>
                        <button type="button" class="cdg-bal-quick" onclick="cdgBalSet(500)">500 ₺</button>
                        <button type="button" class="cdg-bal-quick" onclick="cdgBalSet(1000)">1.000 ₺</button>
                        <button type="button" class="cdg-bal-quick" onclick="cdgBalSet(2500)">2.500 ₺</button>
                    </div>

                    <div class="cdg-bal-amount-input">
                        <label>Yüklenecek Tutar (<?php echo htmlspecialchars($user_curr, ENT_QUOTES); ?>)
                            <?php if($min_buy || $max_buy): ?>
                            <small>
                                <?php if($min_buy): ?>· Min: <?php echo $min_buy; ?><?php endif; ?>
                                <?php if($max_buy): ?>· Max: <?php echo $max_buy; ?><?php endif; ?>
                            </small>
                            <?php endif; ?>
                        </label>
                        <div class="cdg-bal-amount-row">
                            <span class="cdg-bal-currency-prefix"><?php echo htmlspecialchars($user_curr === 'TRY' ? '₺' : $user_curr, ENT_QUOTES); ?></span>
                            <input type="number" id="cdg-bal-amount" min="<?php echo $min_buy ?: 1; ?>" <?php if($max_buy) echo 'max="' . $max_buy . '"'; ?> step="1" placeholder="100">
                        </div>
                        <button type="button" class="cdg-bal-load-btn" onclick="cdgBalanceBuy(this)">
                            <i class="bi bi-shield-lock-fill"></i> Güvenli Ödeme ile Yükle
                        </button>
                    </div>

                    <div class="cdg-bal-trust">
                        <div class="cdg-bal-trust-item">
                            <i class="bi bi-shield-check"></i>
                            <span>SSL Güvenli</span>
                        </div>
                        <div class="cdg-bal-trust-item">
                            <i class="bi bi-lightning-charge"></i>
                            <span>Anında Yükleme</span>
                        </div>
                        <div class="cdg-bal-trust-item">
                            <i class="bi bi-credit-card-2-front"></i>
                            <span>Tüm Kartlar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OTOMATİK ÖDEME KARTI -->
            <div class="cdg-bal-card">
                <div class="cdg-bal-card-head">
                    <h3><i class="bi bi-gear-fill" style="color:#2E3B4E;"></i> Otomatik Ödeme</h3>
                    <span class="cdg-bal-card-sub">Bakiyeden otomatik fatura ödeme</span>
                </div>
                <div class="cdg-bal-card-body">
                    <form method="post" action="<?php echo isset($links['controller']) ? htmlspecialchars($links['controller'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" id="cdg-balance-settings">
                        <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
                        <input type="hidden" name="operation" value="update_settings">

                        <!-- Toggle Switch -->
                        <label class="cdg-bal-switch">
                            <input type="checkbox" name="auto_payment_by_credit" value="1" id="auto_payment_by_credit" <?php echo $auto_pay_active ? 'checked' : ''; ?>>
                            <span class="cdg-bal-switch-slider"></span>
                            <span class="cdg-bal-switch-text">
                                <strong>Otomatik ödeme</strong>
                                <small>Faturalarınız vade tarihinde bakiyenizden otomatik kesilir</small>
                            </span>
                        </label>

                        <!-- Min eşik -->
                        <div class="cdg-bal-field">
                            <label>Düşük Bakiye Uyarı Eşiği</label>
                            <div class="cdg-bal-amount-row">
                                <span class="cdg-bal-currency-prefix">₺</span>
                                <input type="number" min="0" step="1" name="balance_min"
                                    value="<?php echo htmlspecialchars($balance_min_threshold, ENT_QUOTES); ?>"
                                    placeholder="100">
                            </div>
                            <small><i class="bi bi-info-circle"></i> Bakiyeniz bu tutarın altına düştüğünde e-posta uyarısı gönderilir.</small>
                        </div>

                        <button type="submit" class="cdg-bal-save-btn">
                            <i class="bi bi-check2"></i> Ayarları Kaydet
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- BAKİYE HAREKETLERİ -->
        <div class="cdg-bal-card" style="margin-top:18px;">
            <div class="cdg-bal-card-head">
                <h3><i class="bi bi-clock-history"></i> Bakiye Hareketleri</h3>
                <?php if(!empty($transactions)): ?>
                <span class="cdg-bal-card-sub"><?php echo count($transactions); ?> işlem</span>
                <?php endif; ?>
            </div>
            <div class="cdg-bal-card-body" style="padding:0;">
                <?php if(!empty($transactions) && is_array($transactions)): ?>
                <table class="cdg-bal-table">
                    <thead>
                        <tr>
                            <th style="width:140px;">Tarih</th>
                            <th>Açıklama</th>
                            <th style="text-align:right;width:140px;">Tutar</th>
                            <th style="text-align:center;width:100px;">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($transactions as $row):
                        $date = isset($row['ctime']) ? $row['ctime'] : (isset($row['date']) ? $row['date'] : '');
                        $date_fmt = '';
                        if($date) {
                            if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
                                $date_fmt = DateManager::format(Config::get("options/date-format"), $date);
                            } else {
                                $date_fmt = date('d.m.Y H:i', strtotime($date));
                            }
                        }
                        $desc = isset($row['description']) ? $row['description'] : (isset($row['detail']) ? $row['detail'] : '-');
                        $amount = '';
                        if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                            $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']);
                        }
                        $is_credit = isset($row['type']) && in_array($row['type'], ['credit','add','income']);
                        $st = strtolower($row['status'] ?? 'completed');
                        $st_meta = [
                            'completed' => ['cls' => 'success', 'lbl' => 'Tamamlandı'],
                            'pending'   => ['cls' => 'warning', 'lbl' => 'Beklemede'],
                            'failed'    => ['cls' => 'danger',  'lbl' => 'Başarısız'],
                        ];
                        $sm = $st_meta[$st] ?? ['cls' => 'info', 'lbl' => ucfirst($st)];
                    ?>
                        <tr>
                            <td style="font-size:12.5px;color:#64748b;"><?php echo $date_fmt; ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="cdg-bal-tx-icon" style="background:<?php echo $is_credit ? '#dcfce7' : '#fee2e2'; ?>;color:<?php echo $is_credit ? '#15803d' : '#dc2626'; ?>;">
                                        <i class="bi bi-<?php echo $is_credit ? 'arrow-down-circle' : 'arrow-up-circle'; ?>"></i>
                                    </div>
                                    <span style="font-size:13px;color:#0f172a;"><?php echo htmlspecialchars($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <span style="font-weight:700;font-size:14px;color:<?php echo $is_credit ? '#15803d' : '#dc2626'; ?>;">
                                    <?php echo $is_credit ? '+' : '-'; ?> <?php echo $amount; ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="cdg-bal-badge cdg-bal-badge-<?php echo $sm['cls']; ?>"><?php echo $sm['lbl']; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="cdg-bal-empty">
                    <i class="bi bi-wallet2"></i>
                    <h4>Henüz hareket yok</h4>
                    <p>Bakiye yükleme veya ödeme işlemi yaptığınızda burada görünecek.</p>
                    <button type="button" onclick="document.getElementById('cdg-bal-amount').focus(); document.getElementById('cdg-bal-amount').scrollIntoView({behavior:'smooth', block:'center'});" class="cdg-bal-load-btn" style="margin-top:14px;">
                        <i class="bi bi-plus-circle"></i> İlk Yüklemenizi Yapın
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* === BAKIYE KURUMSAL SHELL === */
.cdg-bal-shell {
    max-width: 1280px;
    margin: 0 auto 24px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.04);
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
}
.cdg-bal-shell *, .cdg-bal-shell *::before, .cdg-bal-shell *::after { box-sizing: border-box; }
.cdg-bal-shell-head {
    padding: 22px 26px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-bottom: 1px solid #bbf7d0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.cdg-bal-shell-head-left { display: flex; align-items: center; gap: 18px; }
.cdg-bal-shell-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, #10b981, #16a34a);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 24px;
    box-shadow: 0 8px 20px rgba(16,185,129,0.30);
}
.cdg-bal-shell-title {
    margin: 0 0 3px;
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.cdg-bal-shell-sub { font-size: 13px; color: #15803d; font-weight: 500; }
.cdg-bal-shell-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.cdg-bal-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #fff;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #15803d;
    text-decoration: none;
    transition: all 0.18s;
}
.cdg-bal-btn:hover { background: #15803d; color: #fff; border-color: #15803d; }
.cdg-bal-shell-body { padding: 24px; }

/* === STATS === */
.cdg-bal-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 800px) { .cdg-bal-stats { grid-template-columns: 1fr; } }
.cdg-bal-stat {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
}
.cdg-bal-stat-primary {
    background: linear-gradient(135deg, #1A2332, #485A75);
    border-color: #1A2332;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.cdg-bal-stat-primary::before {
    content: '';
    position: absolute;
    top: -30%; right: -15%;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(253,224,71,0.12) 0%, transparent 60%);
}
.cdg-bal-stat-label {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 8px;
}
.cdg-bal-stat-primary .cdg-bal-stat-label { color: rgba(255,255,255,0.80); }
.cdg-bal-stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: 6px;
    position: relative;
    z-index: 1;
}
.cdg-bal-stat-primary .cdg-bal-stat-value { color: #fff; }
.cdg-bal-stat-value-sm {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 6px;
}
.cdg-bal-stat-sub {
    font-size: 12px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
}
.cdg-bal-stat-primary .cdg-bal-stat-sub { color: rgba(255,255,255,0.70); }

/* === GRID === */
.cdg-bal-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 18px;
}
@media (max-width: 900px) { .cdg-bal-grid { grid-template-columns: 1fr; } }

/* === CARD === */
.cdg-bal-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
}
.cdg-bal-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.cdg-bal-card-head h3 { margin: 0; font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.cdg-bal-card-sub { font-size: 11px; color: #64748b; font-weight: 600; }
.cdg-bal-card-body { padding: 20px; }

/* === HIZLI MİKTAR === */
.cdg-bal-quick-amounts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
    gap: 8px;
    margin-bottom: 18px;
}
.cdg-bal-quick {
    padding: 12px 8px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.18s;
    font-family: inherit;
    position: relative;
}
.cdg-bal-quick:hover {
    background: #fff;
    border-color: #10b981;
    color: #15803d;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(16,185,129,0.15);
}
.cdg-bal-quick-popular {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-color: #f59e0b;
    color: #92400e;
}
.cdg-bal-popular-badge {
    position: absolute;
    top: -7px;
    right: -2px;
    background: #f59e0b;
    color: #422006;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

/* === AMOUNT INPUT === */
.cdg-bal-amount-input { margin-bottom: 16px; }
.cdg-bal-amount-input > label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.cdg-bal-amount-input > label small { font-weight: 500; color: #94a3b8; }
.cdg-bal-amount-row {
    display: flex;
    align-items: stretch;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}
.cdg-bal-amount-row:focus-within { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.15); }
.cdg-bal-currency-prefix {
    padding: 0 14px;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    font-weight: 800;
    font-size: 16px;
    color: #475569;
}
.cdg-bal-amount-row input {
    flex: 1;
    border: 0;
    padding: 12px 14px;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    font-family: inherit;
    outline: 0;
}

.cdg-bal-load-btn {
    width: 100%;
    padding: 13px 22px;
    background: linear-gradient(135deg, #10b981, #16a34a);
    color: #fff;
    border: 0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.18s;
    box-shadow: 0 6px 16px rgba(16,185,129,0.25);
}
.cdg-bal-load-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(16,185,129,0.35); }

.cdg-bal-trust {
    display: flex;
    justify-content: space-around;
    gap: 8px;
    flex-wrap: wrap;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
}
.cdg-bal-trust-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    color: #15803d;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-bal-trust-item i { font-size: 14px; }

/* === SWITCH (Otomatik Ödeme Toggle) === */
.cdg-bal-switch {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    margin-bottom: 16px;
    transition: all 0.18s;
}
.cdg-bal-switch:has(input:checked) { background: #f0fdf4; border-color: #86efac; }
.cdg-bal-switch input { display: none; }
.cdg-bal-switch-slider {
    flex-shrink: 0;
    width: 42px;
    height: 24px;
    background: #cbd5e1;
    border-radius: 100px;
    position: relative;
    transition: all 0.2s;
    margin-top: 2px;
}
.cdg-bal-switch-slider::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.cdg-bal-switch input:checked + .cdg-bal-switch-slider { background: #10b981; }
.cdg-bal-switch input:checked + .cdg-bal-switch-slider::after { transform: translateX(18px); }
.cdg-bal-switch-text { display: flex; flex-direction: column; min-width: 0; }
.cdg-bal-switch-text strong { font-size: 13px; color: #0f172a; }
.cdg-bal-switch-text small { font-size: 12px; color: #64748b; margin-top: 2px; line-height: 1.4; }

.cdg-bal-field { margin-bottom: 14px; }
.cdg-bal-field > label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.cdg-bal-field small {
    display: block;
    font-size: 11px;
    color: #94a3b8;
    margin-top: 5px;
}
.cdg-bal-save-btn {
    width: 100%;
    padding: 11px 18px;
    background: #2E3B4E;
    color: #fff;
    border: 0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.18s;
}
.cdg-bal-save-btn:hover { background: #1A2332; transform: translateY(-1px); }

/* === TABLE === */
.cdg-bal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.cdg-bal-table thead th {
    padding: 12px 18px;
    background: #f8fafc;
    text-align: left;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-bal-table tbody td {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
}
.cdg-bal-table tbody tr:hover { background: #f8fafc; }
.cdg-bal-table tbody tr:last-child td { border-bottom: 0; }
.cdg-bal-tx-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.cdg-bal-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 100px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.cdg-bal-badge-success { background: #dcfce7; color: #15803d; }
.cdg-bal-badge-warning { background: #fef3c7; color: #92400e; }
.cdg-bal-badge-danger { background: #fee2e2; color: #991b1b; }
.cdg-bal-badge-info { background: #CFFAFE; color: #2E3B4E; }

.cdg-bal-empty {
    text-align: center;
    padding: 60px 20px;
}
.cdg-bal-empty i {
    font-size: 56px;
    color: #cbd5e1;
    margin-bottom: 14px;
    display: block;
}
.cdg-bal-empty h4 {
    margin: 0 0 8px;
    font-size: 16px;
    font-weight: 800;
    color: #475569;
}
.cdg-bal-empty p {
    margin: 0;
    font-size: 13px;
    color: #94a3b8;
}
</style>

<script>
(function(){
    var cdgBalUrl = '<?php echo htmlspecialchars($controller_url_bal, ENT_QUOTES); ?>';

    window.cdgBalSet = function(amt){
        var inp = document.getElementById('cdg-bal-amount');
        if(inp) {
            inp.value = amt;
            inp.focus();
        }
    };

    window.cdgBalanceBuy = function(btn) {
        var amt = parseFloat(document.getElementById('cdg-bal-amount').value);
        if(!amt || amt <= 0) {
            alert('Lütfen geçerli bir tutar girin');
            return;
        }
        if(!confirm(amt.toLocaleString('tr-TR') + ' <?php echo htmlspecialchars($user_curr, ENT_QUOTES); ?> bakiye yüklemek için sepete eklenecek. Devam edilsin mi?')) return;

        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        // MioAjax veya fetch fallback
        var doRequest = function(){
            if(typeof MioAjax === 'function') {
                MioAjax({
                    url: cdgBalUrl, type: 'post',
                    data: { operation: 'buy_credit', amount: amt },
                    result: function(r){
                        btn.disabled = false; btn.innerHTML = orig;
                        if(r && r.status === 'successful') {
                            if(r.redirect) window.location.href = r.redirect;
                            else window.location.reload();
                        } else {
                            alert((r && r.message) ? r.message : 'Bir hata oluştu');
                        }
                    }
                });
                return;
            }
            // Fallback: fetch
            var fd = new FormData();
            fd.append('operation', 'buy_credit');
            fd.append('amount', amt);
            fetch(cdgBalUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(function(r){ return r.text(); })
                .then(function(txt){
                    btn.disabled = false; btn.innerHTML = orig;
                    try {
                        var r = JSON.parse(txt);
                        if(r.status === 'successful') {
                            if(r.redirect) window.location.href = r.redirect;
                            else window.location.reload();
                        } else {
                            alert(r.message || 'Bir hata oluştu');
                        }
                    } catch(e) {
                        alert('Sunucu yanıtı işlenemedi');
                    }
                })
                .catch(function(){
                    btn.disabled = false; btn.innerHTML = orig;
                    alert('Bağlantı hatası');
                });
        };
        doRequest();
    };
})();
</script>
