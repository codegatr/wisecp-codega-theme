<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = ["datatables"];

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

<div class="cdg-grid cdg-grid-3" style="margin-bottom:20px;">
    <div class="cdg-card" style="text-align:center;">
        <div style="color:var(--cdg-muted);font-size:13px;margin-bottom:8px;">Mevcut Bakiyeniz</div>
        <div style="font-size:32px;font-weight:700;color:var(--cdg-primary);"><?php echo $user_balance_str; ?></div>
    </div>
    <div class="cdg-card">
        <h4 style="margin-bottom:12px;"><i class="bi bi-plus-circle"></i> Bakiye Yükleme</h4>
        <p style="font-size:13px;color:var(--cdg-muted);margin-bottom:12px;">Hesabınıza bakiye yüklemek için aşağıdaki butonu kullanın.</p>
        <button type="button" onclick="if(typeof iziModal=='function'){$('#balance-modal').iziModal('open');}else{alert('Bakiye yukleme: WiseCP modul aktif olmalidir.');}" class="cdg-btn cdg-btn-primary" style="width:100%;">
            <i class="bi bi-credit-card"></i> Yükleme Yap
        </button>
    </div>
    <div class="cdg-card">
        <h4 style="margin-bottom:12px;"><i class="bi bi-gear"></i> Otomatik Ödeme Ayarları</h4>
        <p style="font-size:13px;color:var(--cdg-muted);margin-bottom:14px;">Faturalariniz vade tarihinde bakiyenizden otomatik odensin.</p>

        <form method="post" action="<?php echo isset($links['controller']) ? htmlspecialchars($links['controller'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" id="cdg-balance-settings">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
            <input type="hidden" name="operation" value="update_settings">

            <label class="cdg-checkbox" style="margin-bottom:12px;display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="auto_payment_by_credit" value="1" id="auto_payment_by_credit"
                    <?php echo (class_exists('User') && isset(User::$init->info['auto_payment_by_credit']) && User::$init->info['auto_payment_by_credit']) ? 'checked' : ''; ?>>
                <span style="font-weight:600;">Otomatik odemeyi etkinlestir</span>
            </label>

            <div class="cdg-form-group">
                <label class="cdg-form-label" style="font-size:12px;">Minimum Bakiye Uyari Esigi</label>
                <input type="number" min="0" step="0.01" name="balance_min" class="cdg-form-control"
                    value="<?php echo htmlspecialchars(isset(User::$init->info['balance_min']) ? User::$init->info['balance_min'] : '0', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                    placeholder="Örnek: 100">
                <small style="font-size:11px;color:var(--cdg-muted);">Bakiyeniz bu degerin altina dustugunde uyari gelir.</small>
            </div>

            <button type="submit" class="cdg-btn cdg-btn-primary cdg-btn-sm" style="width:100%;">
                <i class="bi bi-check2"></i> Ayarları Kaydet
            </button>
        </form>
    </div>
</div>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-list-ul"></i> Bakiye Hareketleri</h3>
    </div>

    <?php if(!empty($transactions) && is_array($transactions)): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Açıklama</th>
                        <th style="text-align:right;">Tutar</th>
                        <th style="text-align:center;">Durum</th>
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
                ?>
                    <tr>
                        <td style="font-size:13px;"><?php echo $date_fmt; ?></td>
                        <td><?php echo htmlspecialchars($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        <td style="text-align:right;font-weight:600;color:<?php echo $is_credit ? '#10b981' : '#ef4444'; ?>;">
                            <?php echo $is_credit ? '+' : '-'; ?> <?php echo $amount; ?>
                        </td>
                        <td style="text-align:center;font-size:12px;"><?php echo isset($row['status']) ? htmlspecialchars($row['status'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-wallet2"></i></div>
            <h3>Henuz hareket yok</h3>
            <p>Bakiye yükleme veya ödeme işlemi yaptığınızda burada görünecek.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// === Bakiye Yükleme Kartı ===
$controller_url_bal = isset($links['controller']) ? $links['controller'] : '';
$min_buy = (class_exists('Config') && method_exists('Config','get'))
    ? @Config::get('credit_settings/min_purchase')
    : 0;
$max_buy = (class_exists('Config') && method_exists('Config','get'))
    ? @Config::get('credit_settings/max_purchase')
    : 0;
$user_curr = isset($currency) && $currency ? $currency : 'TRY';
?>

<div class="cdg-balance-buy-card" style="margin-top:24px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;box-shadow:0 4px 12px rgba(15,23,42,0.04);font-family:'Plus Jakarta Sans',sans-serif;box-sizing:border-box;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #e2e8f0;">
        <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#10b981,#34d399);color:#fff;display:grid;place-items:center;font-size:18px;">
            <i class="bi bi-cash-coin"></i>
        </div>
        <div>
            <h3 style="font-size:15px;font-weight:800;margin:0;color:#0f172a;">Bakiye Yükle</h3>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">Hesabınıza bakiye yükleyerek hızlı ödeme yapabilirsiniz</div>
        </div>
    </div>

    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;">
        <i class="bi bi-info-circle-fill" style="color:#15803d;font-size:18px;flex-shrink:0;"></i>
        <p style="margin:0;font-size:13px;color:#15803d;line-height:1.5;">
            Bakiyeniz hesabinizda saklanir ve istediginiz zaman fatura odemelerinde kullanilabilir.
            Yükleme işlemi anındadır.
        </p>
    </div>

    <div style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:end;">
        <div>
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.4px;">
                Yuklenecek Tutar (<?php echo htmlspecialchars($user_curr, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>)
                <?php if($min_buy || $max_buy): ?>
                <span style="font-weight:400;color:#94a3b8;">
                    <?php if($min_buy): ?>· Min: <?php echo $min_buy; ?><?php endif; ?>
                    <?php if($max_buy): ?>· Max: <?php echo $max_buy; ?><?php endif; ?>
                </span>
                <?php endif; ?>
            </label>
            <input type="number" id="cdg-bal-amount" min="<?php echo $min_buy ?: 1; ?>" <?php if($max_buy) echo 'max="' . $max_buy . '"'; ?> step="0.01"
                style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;"
                placeholder="100">
        </div>
        <button type="button" onclick="cdgBalanceBuy(this)" style="padding:11px 22px;background:linear-gradient(135deg,#10b981,#34d399);color:#fff;border:0;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;box-shadow:0 4px 10px rgba(16,185,129,0.22);">
            <i class="bi bi-cart-plus"></i> Sepete Ekle
        </button>
    </div>

    <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
        <button type="button" onclick="document.getElementById('cdg-bal-amount').value=50;" style="padding:6px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:99px;font-size:12px;color:#475569;cursor:pointer;font-family:inherit;">50</button>
        <button type="button" onclick="document.getElementById('cdg-bal-amount').value=100;" style="padding:6px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:99px;font-size:12px;color:#475569;cursor:pointer;font-family:inherit;">100</button>
        <button type="button" onclick="document.getElementById('cdg-bal-amount').value=250;" style="padding:6px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:99px;font-size:12px;color:#475569;cursor:pointer;font-family:inherit;">250</button>
        <button type="button" onclick="document.getElementById('cdg-bal-amount').value=500;" style="padding:6px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:99px;font-size:12px;color:#475569;cursor:pointer;font-family:inherit;">500</button>
        <button type="button" onclick="document.getElementById('cdg-bal-amount').value=1000;" style="padding:6px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:99px;font-size:12px;color:#475569;cursor:pointer;font-family:inherit;">1000</button>
    </div>
</div>

<script>
(function(){
    var cdgBalUrl = '<?php echo htmlspecialchars($controller_url_bal, ENT_QUOTES); ?>';
    window.cdgBalanceBuy = function(btn) {
        var amt = document.getElementById('cdg-bal-amount').value;
        amt = parseFloat(amt);
        if(!amt || amt <= 0) {
            if(typeof alert_error === 'function') alert_error('Geçerli bir tutar girin', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;
        if(!confirm(amt + ' <?php echo htmlspecialchars($user_curr, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> bakiye yüklemek için sepete eklenecek. Devam edilsin mi?')) return;

        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Isleniyor...';

        MioAjax({
            url: cdgBalUrl, type: 'post',
            data: { operation: 'buy_credit', amount: amt },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(r.redirect) {
                        if(typeof alert_success === 'function') alert_success('Ödeme sayfasına yönlendiriliyorsunuz...', {timer: 1500});
                        setTimeout(function(){ window.location.href = r.redirect; }, 1200);
                    } else {
                        if(typeof alert_success === 'function') alert_success(r.message || 'Bakiye yuklendi', {timer: 2000});
                        setTimeout(function(){ window.location.reload(); }, 1500);
                    }
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };
})();
</script>
