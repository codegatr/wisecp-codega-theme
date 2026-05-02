<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Toplu Fatura Ödeme
 * WiseCP runtime: $invoices, $payment_methods, $links, $payment_screen, $selected_pmethod
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$wide_content = true;
$hoptions = ["jquery-ui"];

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

$invoices = isset($invoices) && is_array($invoices) ? $invoices : [];
$payment_methods = isset($payment_methods) && is_array($payment_methods) ? $payment_methods : [];
$links = isset($links) && is_array($links) ? $links : [];
$payment_screen = isset($payment_screen) ? $payment_screen : null;
$selected_pmethod = $selected_pmethod ?? 'none';

$controller_url = $links['controller'] ?? '';
$invoices_url = cdg_link('invoices');

function cdg_blk_money($a) {
    if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
        return Money::formatter_symbol($a);
    }
    return number_format((float)$a, 2, ',', '.');
}
function cdg_blk_date($d) {
    if(!$d) return '-';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        return DateManager::format(Config::get("options/date-format") ?: 'd.m.Y', $d);
    }
    return date('d.m.Y', strtotime((string)$d));
}
?>

<style>
.cdg-blk {
    --b-primary: #1e40af;
    --b-success: #10b981;
    --b-warning: #f59e0b;
    --b-bg: #f8fafc;
    --b-card: #fff;
    --b-text: #0f172a;
    --b-muted: #64748b;
    --b-border: #e2e8f0;
    --b-radius: 14px;
    --b-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--b-text);
    background: var(--b-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-blk *, .cdg-blk *::before, .cdg-blk *::after { box-sizing: border-box; }
.cdg-blk a { text-decoration: none; color: inherit; }
.cdg-blk-wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

.cdg-blk-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--b-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    margin-bottom: 18px;
    transition: all 0.18s;
}
.cdg-blk-back:hover { border-color: var(--b-primary); color: var(--b-primary); }

.cdg-blk-hero {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    display: flex; align-items: center; gap: 18px;
    flex-wrap: wrap;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
}
.cdg-blk-hero-icon {
    width: 60px; height: 60px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 28px;
    flex-shrink: 0;
}
.cdg-blk-hero-text { flex: 1; min-width: 200px; }
.cdg-blk-hero h1 { font-size: 26px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-blk-hero p { font-size: 13px; opacity: 0.88; margin: 0; }

.cdg-blk-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 20px;
}

.cdg-blk-card {
    background: #fff;
    border: 1px solid var(--b-border);
    border-radius: var(--b-radius);
    box-shadow: var(--b-shadow);
    overflow: hidden;
}
.cdg-blk-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--b-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-blk-card-head h3 {
    font-size: 14px; font-weight: 800; margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-blk-card-head h3 i { color: var(--b-primary); }
.cdg-blk-card-body { padding: 22px; }

/* INVOICE LIST */
.cdg-blk-invoice-row {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 14px;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid var(--b-border);
}
.cdg-blk-invoice-row:last-child { border-bottom: 0; padding-bottom: 0; }
.cdg-blk-invoice-row:first-child { padding-top: 0; }
.cdg-blk-checkbox {
    width: 22px; height: 22px;
    border: 2px solid var(--b-border);
    border-radius: 6px;
    cursor: pointer;
    accent-color: var(--b-primary);
    flex-shrink: 0;
}
.cdg-blk-invoice-info { min-width: 0; }
.cdg-blk-invoice-num { font-weight: 800; color: var(--b-text); font-size: 14px; }
.cdg-blk-invoice-meta {
    font-size: 12px;
    color: var(--b-muted);
    display: flex; gap: 12px; flex-wrap: wrap; margin-top: 3px;
}
.cdg-blk-invoice-meta span { display: inline-flex; align-items: center; gap: 4px; }
.cdg-blk-invoice-amount {
    font-size: 16px;
    font-weight: 900;
    color: var(--b-primary);
    text-align: right;
    white-space: nowrap;
}

/* SELECT ALL */
.cdg-blk-selectall {
    background: #f8fafc;
    padding: 12px 22px;
    border-bottom: 1px solid var(--b-border);
    display: flex; align-items: center; gap: 10px;
}
.cdg-blk-selectall label {
    font-size: 13px;
    font-weight: 700;
    color: var(--b-text);
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 8px;
}

/* PAYMENT METHODS */
.cdg-blk-pm {
    display: flex; flex-direction: column; gap: 8px;
}
.cdg-blk-pm-option {
    border: 2px solid var(--b-border);
    border-radius: 10px;
    padding: 12px 14px;
    cursor: pointer;
    transition: all 0.18s;
    display: flex; align-items: center; gap: 10px;
    background: #fff;
}
.cdg-blk-pm-option:hover { border-color: var(--b-primary); background: #f8fafc; }
.cdg-blk-pm-option input { accent-color: var(--b-primary); flex-shrink: 0; }
.cdg-blk-pm-option-name { font-weight: 700; font-size: 13px; }
.cdg-blk-pm-option-desc { font-size: 11px; color: var(--b-muted); margin-top: 2px; }
.cdg-blk-pm-option.selected { border-color: var(--b-primary); background: #eff6ff; }

/* SUMMARY */
.cdg-blk-summary { position: sticky; top: 20px; }
.cdg-blk-summary-row {
    display: flex; justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed var(--b-border);
    font-size: 14px;
}
.cdg-blk-summary-row:last-child { border-bottom: 0; }
.cdg-blk-summary-row.total {
    font-size: 18px;
    font-weight: 900;
    color: var(--b-primary);
    border-bottom: 0;
    border-top: 2px solid var(--b-primary);
    margin-top: 6px;
    padding-top: 14px;
}

.cdg-blk-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 14px 24px;
    border-radius: 10px;
    font-size: 14px; font-weight: 800;
    cursor: pointer;
    border: 0;
    transition: all 0.2s;
    font-family: inherit;
    text-decoration: none;
    width: 100%;
}
.cdg-blk-btn-primary {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 8px 24px rgba(16,185,129,0.25);
}
.cdg-blk-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-blk-btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.cdg-blk-empty {
    text-align: center;
    padding: 50px 20px;
    background: #fff;
    border: 2px dashed var(--b-border);
    border-radius: var(--b-radius);
}
.cdg-blk-empty i { font-size: 56px; color: #cbd5e1; display: block; margin-bottom: 12px; }
.cdg-blk-empty h3 { font-size: 18px; font-weight: 800; margin: 0 0 6px; }
.cdg-blk-empty p { font-size: 13px; color: var(--b-muted); margin: 0 0 16px; }

@media (max-width: 968px) {
    .cdg-blk-grid { grid-template-columns: 1fr; }
    .cdg-blk-summary { position: static; }
}
@media (max-width: 600px) {
    .cdg-blk-hero { flex-direction: column; text-align: center; padding: 22px 20px; }
    .cdg-blk-invoice-row { grid-template-columns: auto 1fr; }
    .cdg-blk-invoice-amount { grid-column: 1 / -1; text-align: right; }
}
</style>

<div class="cdg-blk">
<div class="cdg-blk-wrap">

    <a href="<?php echo htmlspecialchars($invoices_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-blk-back">
        <i class="bi bi-arrow-left"></i> Faturalara Dön
    </a>

    <section class="cdg-blk-hero">
        <div class="cdg-blk-hero-icon"><i class="bi bi-cash-stack"></i></div>
        <div class="cdg-blk-hero-text">
            <h1>Toplu Fatura Ödeme</h1>
            <p>Ödenmemiş faturalarınızı tek seferde, tek bir ödemeyle kapatın.</p>
        </div>
    </section>

    <?php if(empty($invoices)): ?>
    <div class="cdg-blk-empty">
        <i class="bi bi-check-circle"></i>
        <h3>Ödenmemiş Faturanız Yok</h3>
        <p>Tüm faturalarınız ödenmiş durumda. Tebrikler!</p>
        <a href="<?php echo htmlspecialchars($invoices_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-blk-btn cdg-blk-btn-primary" style="display:inline-flex;width:auto;padding:11px 20px;font-size:13px;">
            <i class="bi bi-receipt"></i> Faturalarımı Görüntüle
        </a>
    </div>
    <?php else: ?>
    <div class="cdg-blk-grid">

        <!-- SOL: Faturalar + Ödeme Yöntemi -->
        <div>
            <div class="cdg-blk-card" style="margin-bottom:18px;">
                <div class="cdg-blk-card-head">
                    <h3><i class="bi bi-receipt"></i> Ödenecek Faturalar (<?php echo count($invoices); ?>)</h3>
                </div>

                <div class="cdg-blk-selectall">
                    <input type="checkbox" id="checkedAll" class="cdg-blk-checkbox" checked>
                    <label for="checkedAll">Tümünü Seç / Bırak</label>
                </div>

                <div class="cdg-blk-card-body" id="cdg-blk-invoice-list">
                    <?php foreach($invoices as $invoice):
                        if(!is_array($invoice)) continue;
                        $i_id = $invoice['id'] ?? 0;
                        $i_total = $invoice['total'] ?? 0;
                        $i_due = $invoice['duedate'] ?? '';
                        $i_currency = $invoice['currency'] ?? 'TRY';
                        $u_data = $invoice['user_data'] ?? [];
                        $u_name = '';
                        if(is_array($u_data)) {
                            $u_name = trim(($u_data['name'] ?? '') . ' ' . ($u_data['surname'] ?? ''));
                            if(!$u_name && !empty($u_data['company_name'])) $u_name = $u_data['company_name'];
                        }
                    ?>
                    <div class="cdg-blk-invoice-row">
                        <input type="checkbox" class="cdg-blk-checkbox selected-invoices" value="<?php echo (int)$i_id; ?>" data-amount="<?php echo (float)$i_total; ?>" checked>
                        <div class="cdg-blk-invoice-info">
                            <div class="cdg-blk-invoice-num">Fatura #<?php echo htmlspecialchars($invoice['number'] ?? $i_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                            <div class="cdg-blk-invoice-meta">
                                <?php if($i_due): ?>
                                <span><i class="bi bi-calendar"></i> Son ödeme: <?php echo htmlspecialchars(cdg_blk_date($i_due), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <?php if($u_name): ?>
                                <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($u_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="cdg-blk-invoice-amount">
                            <?php echo htmlspecialchars(cdg_blk_money($i_total), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <small style="font-size:11px;font-weight:600;display:block;color:var(--b-muted);"><?php echo htmlspecialchars($i_currency, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cdg-blk-card">
                <div class="cdg-blk-card-head">
                    <h3><i class="bi bi-credit-card"></i> Ödeme Yöntemi</h3>
                </div>
                <div class="cdg-blk-card-body">
                    <div class="cdg-blk-pm" id="payment_methods">
                        <?php if(empty($payment_methods)): ?>
                        <div style="text-align:center;color:var(--b-muted);padding:20px;font-size:13px;">
                            <i class="bi bi-hourglass-split" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                            Ödeme yöntemleri yükleniyor...
                        </div>
                        <?php else: ?>
                            <?php foreach($payment_methods as $pm):
                                if(!is_array($pm)) continue;
                                $pm_method = $pm['method'] ?? '';
                                $pm_name = $pm['name'] ?? $pm_method;
                                $pm_desc = $pm['description'] ?? '';
                                $checked = ($pm_method === $selected_pmethod) ? 'checked' : '';
                            ?>
                            <label class="cdg-blk-pm-option <?php echo $checked ? 'selected' : ''; ?>">
                                <input type="radio" name="pmethod" value="<?php echo htmlspecialchars($pm_method, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $checked; ?>>
                                <div>
                                    <div class="cdg-blk-pm-option-name"><?php echo htmlspecialchars($pm_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                                    <?php if($pm_desc): ?>
                                    <div class="cdg-blk-pm-option-desc"><?php echo htmlspecialchars($pm_desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAĞ: Özet -->
        <div class="cdg-blk-summary">
            <div class="cdg-blk-card">
                <div class="cdg-blk-card-head">
                    <h3><i class="bi bi-calculator"></i> Ödeme Özeti</h3>
                </div>
                <div class="cdg-blk-card-body">
                    <div class="cdg-blk-summary-row">
                        <span>Seçili Fatura</span>
                        <strong id="cdg-blk-count">0</strong>
                    </div>
                    <div class="cdg-blk-summary-row">
                        <span>Ara Toplam</span>
                        <strong id="cdg-blk-subtotal">0,00</strong>
                    </div>
                    <div class="cdg-blk-summary-row total">
                        <span>TOPLAM</span>
                        <strong id="cdg-blk-total">0,00</strong>
                    </div>

                    <form id="payment_screen_redirect" method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="margin-top:18px;">
                        <input type="hidden" name="operation" value="payment-screen">
                        <input type="hidden" name="pmethod" value="<?php echo htmlspecialchars($selected_pmethod, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        <input type="hidden" name="invoices" value="">
                        <button type="button" id="continue_button" class="cdg-blk-btn cdg-blk-btn-primary">
                            <i class="bi bi-credit-card-2-back-fill"></i> Ödeme Sayfasına Geç
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>

</div>
</div>

<script>
(function(){
    var checkedAll = document.getElementById('checkedAll');
    var checkboxes = document.querySelectorAll('.selected-invoices');
    var pmRadios = document.querySelectorAll('input[name="pmethod"]');

    function updateSummary() {
        var count = 0;
        var total = 0;
        checkboxes.forEach(function(cb){
            if(cb.checked) {
                count++;
                total += parseFloat(cb.getAttribute('data-amount') || 0);
            }
        });

        var fmt = function(n){ return n.toLocaleString('tr-TR', {minimumFractionDigits:2,maximumFractionDigits:2}); };

        document.getElementById('cdg-blk-count').textContent = count;
        document.getElementById('cdg-blk-subtotal').textContent = fmt(total);
        document.getElementById('cdg-blk-total').textContent = fmt(total);

        var form = document.getElementById('payment_screen_redirect');
        var input = form ? form.querySelector('input[name=invoices]') : null;
        if(input) {
            var ids = [];
            checkboxes.forEach(function(cb){ if(cb.checked) ids.push(cb.value); });
            input.value = ids.join(',');
        }

        var btn = document.getElementById('continue_button');
        if(btn) btn.disabled = (count === 0);

        // Tümü seçili mi
        var allChecked = true;
        checkboxes.forEach(function(cb){ if(!cb.checked) allChecked = false; });
        if(checkedAll) checkedAll.checked = allChecked;
    }

    if(checkedAll) {
        checkedAll.addEventListener('change', function(){
            var v = this.checked;
            checkboxes.forEach(function(cb){ cb.checked = v; });
            updateSummary();
        });
    }

    checkboxes.forEach(function(cb){
        cb.addEventListener('change', updateSummary);
    });

    pmRadios.forEach(function(r){
        r.addEventListener('change', function(){
            document.querySelectorAll('.cdg-blk-pm-option').forEach(function(o){ o.classList.remove('selected'); });
            this.closest('.cdg-blk-pm-option').classList.add('selected');
            var form = document.getElementById('payment_screen_redirect');
            if(form) form.querySelector('input[name=pmethod]').value = this.value;
        });
    });

    var continueBtn = document.getElementById('continue_button');
    if(continueBtn) {
        continueBtn.addEventListener('click', function(){
            var pmInput = document.querySelector('input[name="pmethod"]:checked');
            if(!pmInput) {
                if(typeof alert_error === 'function') alert_error('Ödeme yöntemi seçin', {timer: 3000});
                else alert('Ödeme yöntemi seçin');
                return;
            }
            var form = document.getElementById('payment_screen_redirect');
            if(form) {
                form.querySelector('input[name=pmethod]').value = pmInput.value;
                this.disabled = true;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Yönlendiriliyor...';
                form.submit();
            }
        });
    }

    updateSummary();
})();
</script>
