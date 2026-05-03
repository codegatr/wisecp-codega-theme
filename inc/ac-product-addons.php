<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Ürün Ek Hizmet (Addon) Satın Alma Modülü
 * Hosting / Server / Software / Special için ortak
 *
 * Operation: buy_addons_summary (addons[addon_id] = option_id)
 *
 * WiseCP runtime:
 *  - $product_addons - alınabilir ek hizmetler (alt → option seçimi)
 *  - $addons         - zaten alınmış (aktif) ek hizmetler
 *  - Her addon: id, name, description, type (radio/checkbox/select/quantity), options[], compulsory
 *  - Her option: id, name, amount, cid, period, period_time
 */

$d_status = strtolower($proanse['status'] ?? 'unknown');
$is_active = ($d_status === 'active');
$controller_url = $links['controller'] ?? '';

$available_addons = isset($product_addons) && is_array($product_addons) ? $product_addons : [];
$active_addons = isset($addons) && is_array($addons) ? $addons : [];
$has_available = ($is_active && !empty($available_addons));
$has_active = !empty($active_addons);

if(!$has_available && !$has_active) return;
?>

<style>
.cdg-addon-wrap {
    margin-top: 20px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}
.cdg-addon-wrap *, .cdg-addon-wrap *::before, .cdg-addon-wrap *::after { box-sizing: border-box; }

.cdg-addon-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    margin-bottom: 16px;
}
.cdg-addon-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-addon-head .icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ec4899, #f472b6);
    color: #fff;
    display: grid; place-items: center;
    font-size: 18px;
}
.cdg-addon-head h3 { font-size: 15px; font-weight: 800; margin: 0; color: #0f172a; }
.cdg-addon-head .subtitle { font-size: 12px; color: #64748b; margin-top: 2px; }

.cdg-addon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
}
.cdg-addon-item {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    background: #fff;
    transition: all 0.2s;
    display: flex; flex-direction: column;
}
.cdg-addon-item:hover {
    border-color: #ec4899;
    box-shadow: 0 8px 20px rgba(236,72,153,0.08);
}
.cdg-addon-item.compulsory {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb, #fff);
}
.cdg-addon-item.compulsory::before {
    content: 'ZORUNLU';
    position: absolute;
    top: 8px; right: 8px;
    background: #f59e0b;
    color: #fff;
    padding: 2px 8px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.cdg-addon-item-name {
    font-size: 14px; font-weight: 800; color: #0f172a;
    margin: 0 0 6px;
    display: flex; align-items: flex-start; gap: 6px;
}
.cdg-addon-item-desc {
    font-size: 12px; color: #64748b;
    margin: 0 0 12px;
    line-height: 1.5;
    min-height: 30px;
}
.cdg-addon-options-wrap { margin-top: auto; }
.cdg-addon-select {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px; color: #0f172a;
    background: #fff;
    font-family: inherit;
    outline: none;
    transition: border 0.15s;
}
.cdg-addon-select:focus { border-color: #ec4899; box-shadow: 0 0 0 3px rgba(236,72,153,0.10); }

.cdg-addon-summary {
    margin-top: 18px;
    padding: 16px 18px;
    background: linear-gradient(135deg, #fdf2f8, #fce7f3);
    border: 1px solid #f9a8d4;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.cdg-addon-summary-label {
    font-size: 13px; font-weight: 700; color: #831843;
    display: flex; align-items: center; gap: 8px;
}
.cdg-addon-summary-amount {
    font-size: 22px; font-weight: 800; color: #be185d;
}
.cdg-addon-summary-btn {
    background: linear-gradient(135deg, #ec4899, #f472b6);
    color: #fff;
    border: 0;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    box-shadow: 0 4px 10px rgba(236,72,153,0.25);
    font-family: inherit;
    transition: transform 0.15s;
}
.cdg-addon-summary-btn:hover { transform: translateY(-1px); }
.cdg-addon-summary-btn:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none; }

/* Aktif (zaten alınmış) addon listesi */
.cdg-addon-active-list {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}
.cdg-addon-active-item {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
}
.cdg-addon-active-item:last-child { border-bottom: 0; }
.cdg-addon-active-name { font-size: 13px; font-weight: 700; color: #0f172a; }
.cdg-addon-active-meta { font-size: 11px; color: #64748b; margin-top: 2px; }
.cdg-addon-active-status {
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
}
.cdg-addon-active-status.s-active { background: #dcfce7; color: #15803d; }
.cdg-addon-active-status.s-inprocess { background: #fef3c7; color: #92400e; }
.cdg-addon-active-status.s-cancelled, .cdg-addon-active-status.s-canceled { background: #fee2e2; color: #991b1b; }
.cdg-addon-active-status.s-suspended { background: #f3e8ff; color: #6b21a8; }
</style>

<div class="cdg-addon-wrap">

    <?php if($has_active): ?>
    <!-- Aktif Ek Hizmetler -->
    <div class="cdg-addon-card">
        <div class="cdg-addon-head">
            <div class="icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="bi bi-check2-square"></i></div>
            <div>
                <h3>Aktif Ek Hizmetler</h3>
                <div class="subtitle">Şu an satın aldığınız ek hizmetler</div>
            </div>
        </div>

        <div class="cdg-addon-active-list">
            <?php foreach($active_addons as $a):
                $a_name = $a['name'] ?? '';
                $a_status = strtolower($a['status'] ?? 'active');
                $a_status_label = ['active'=>'✓ Aktif', 'inprocess'=>'⏳ İşleniyor', 'waiting'=>'⏳ Beklemede', 'suspended'=>'⊘ Askıda', 'cancelled'=>'✗ İptal', 'canceled'=>'✗ İptal'][$a_status] ?? ucfirst($a_status);
                $a_amount = $a['amount'] ?? 0;
                $a_cid = $a['cid'] ?? 'TRY';
                $a_period = '';
                if(class_exists('View') && method_exists('View','period')) {
                    try { $a_period = View::period($a['period_time'] ?? 1, $a['period'] ?? 'm'); } catch(\Throwable $e) {}
                }
                $a_amount_str = '';
                if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
                    try { $a_amount_str = Money::formatter_symbol($a_amount, $a_cid, true); } catch(\Throwable $e) { $a_amount_str = $a_amount . ' ' . $a_cid; }
                } else {
                    $a_amount_str = $a_amount . ' ' . $a_cid;
                }
            ?>
            <div class="cdg-addon-active-item">
                <div>
                    <div class="cdg-addon-active-name"><?php echo htmlspecialchars($a_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                    <?php if($a_amount > 0): ?>
                    <div class="cdg-addon-active-meta"><?php echo htmlspecialchars($a_amount_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php echo $a_period ? ' / ' . htmlspecialchars($a_period, ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?></div>
                    <?php endif; ?>
                </div>
                <span class="cdg-addon-active-status s-<?php echo htmlspecialchars($a_status, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo $a_status_label; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if($has_available): ?>
    <!-- Ek Hizmet Satın Al -->
    <div class="cdg-addon-card">
        <div class="cdg-addon-head">
            <div class="icon"><i class="bi bi-bag-plus"></i></div>
            <div>
                <h3>Ek Hizmet Satın Al</h3>
                <div class="subtitle">Hizmetinizi geliştirmek için ek özellikler ekleyin</div>
            </div>
        </div>

        <form id="cdg-addon-form" onsubmit="return false;">
            <input type="hidden" name="operation" value="buy_addons_summary">
            <input type="hidden" name="id" value="<?php echo (int)($proanse['id'] ?? 0); ?>">

            <div class="cdg-addon-grid">
                <?php foreach($available_addons as $addon):
                    $addon_id = (int)($addon['id'] ?? 0);
                    $addon_name = $addon['name'] ?? ('Eklenti #' . $addon_id);
                    $addon_desc = $addon['description'] ?? '';
                    $addon_type = $addon['type'] ?? 'select';
                    $addon_compulsory = !empty($addon['compulsory']);
                    $addon_options = $addon['options'] ?? [];
                    $override_curr = !empty($addon['override_usrcurrency']);
                    if(empty($addon_options)) continue;
                ?>
                <div class="cdg-addon-item<?php echo $addon_compulsory ? ' compulsory' : ''; ?>" style="position:relative;">
                    <h4 class="cdg-addon-item-name">
                        <i class="bi bi-stars" style="color:#ec4899;flex-shrink:0;margin-top:3px;"></i>
                        <span><?php echo htmlspecialchars($addon_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    </h4>

                    <?php if($addon_desc): ?>
                    <p class="cdg-addon-item-desc"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($addon_desc), 0, 110, '...'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                    <?php else: ?>
                    <p class="cdg-addon-item-desc"></p>
                    <?php endif; ?>

                    <div class="cdg-addon-options-wrap">
                        <select name="addons[<?php echo $addon_id; ?>]" class="cdg-addon-select cdg-addon-option-select" data-addon-id="<?php echo $addon_id; ?>" onchange="cdgAddonRecalc()">
                            <?php if(!$addon_compulsory): ?>
                            <option value="" data-amount="0">Almak İstemiyorum</option>
                            <?php endif; ?>
                            <?php foreach($addon_options as $k => $opt):
                                $opt_id = $opt['id'] ?? 0;
                                $opt_name = $opt['name'] ?? '';
                                $opt_amount = $opt['amount'] ?? 0;
                                $opt_cid = $opt['cid'] ?? 'TRY';
                                $opt_amount_str = '';
                                if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
                                    try { $opt_amount_str = Money::formatter_symbol($opt_amount, $opt_cid, !$override_curr); } catch(\Throwable $e) { $opt_amount_str = $opt_amount . ' ' . $opt_cid; }
                                } else {
                                    $opt_amount_str = $opt_amount . ' ' . $opt_cid;
                                }
                                $opt_period = '';
                                if(class_exists('View') && method_exists('View','period')) {
                                    try { $opt_period = View::period($opt['period_time'] ?? 1, $opt['period'] ?? 'none'); } catch(\Throwable $e) {}
                                }
                                $label = $opt_name . ' — ' . $opt_amount_str . ($opt_period && (($opt['period'] ?? 'none') !== 'none' || $opt_amount > 0) ? ' / ' . $opt_period : '');
                                $selected_attr = ($addon_compulsory && $k === 0) ? ' selected' : '';
                            ?>
                            <option value="<?php echo (int)$opt_id; ?>" data-amount="<?php echo (float)$opt_amount; ?>" data-cid="<?php echo htmlspecialchars($opt_cid, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php echo $selected_attr; ?>><?php echo htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cdg-addon-summary">
                <div class="cdg-addon-summary-label">
                    <i class="bi bi-cart-fill"></i> Tahmini Toplam:
                    <span class="cdg-addon-summary-amount" id="cdg-addon-total">0</span>
                </div>
                <button type="button" class="cdg-addon-summary-btn" id="cdg-addon-submit-btn" onclick="cdgAddonSubmit(this)" disabled>
                    <i class="bi bi-cart-check"></i> Sepete Ekle ve Devam Et
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>

<?php if($has_available): ?>
<script>
(function(){
    var cdgAddonUrl = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
    var cdgAddonPid = <?php echo (int)($proanse['id'] ?? 0); ?>;

    window.cdgAddonRecalc = function() {
        var total = 0;
        var primaryCid = '';
        var anySelected = false;
        document.querySelectorAll('.cdg-addon-option-select').forEach(function(sel){
            var opt = sel.options[sel.selectedIndex];
            if(!opt) return;
            var amt = parseFloat(opt.getAttribute('data-amount') || 0);
            var cid = opt.getAttribute('data-cid') || 'TRY';
            if(opt.value && amt >= 0) {
                if(opt.value !== '') anySelected = true;
                total += amt;
                if(!primaryCid && amt > 0) primaryCid = cid;
            }
        });
        var totalEl = document.getElementById('cdg-addon-total');
        if(totalEl) {
            // Basit format - WiseCP server-side hesaplayacak gerçek toplamı
            var formatted = total.toFixed(2).replace(/\.00$/, '') + ' ' + (primaryCid || 'TRY');
            totalEl.textContent = formatted;
        }
        var btn = document.getElementById('cdg-addon-submit-btn');
        if(btn) {
            btn.disabled = !anySelected;
        }
    };

    window.cdgAddonSubmit = function(btn) {
        var form = document.getElementById('cdg-addon-form');
        if(!form) return;

        // FormData topla
        var data = { operation: 'buy_addons_summary', id: cdgAddonPid };
        var hasSelection = false;
        document.querySelectorAll('.cdg-addon-option-select').forEach(function(sel){
            if(sel.value) {
                data['addons[' + sel.getAttribute('data-addon-id') + ']'] = sel.value;
                hasSelection = true;
            }
        });
        if(!hasSelection) {
            if(typeof alert_error === 'function') alert_error('Lütfen en az bir ek hizmet seçin', {timer: 3000});
            return;
        }
        if(typeof MioAjax !== 'function') return;

        if(!confirm('Seçtiğiniz ek hizmetleri satın almak için sepete eklenecek. Devam edelim mi?')) return;

        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        MioAjax({
            url: cdgAddonUrl, type: 'post', data: data,
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(r.redirect) {
                        if(typeof alert_success === 'function') alert_success(r.message || 'Ödeme sayfasına yönlendiriliyorsunuz...', {timer: 1500});
                        setTimeout(function(){ window.location.href = r.redirect; }, 1200);
                    } else {
                        if(typeof alert_success === 'function') alert_success(r.message || 'Ek hizmetler eklendi', {timer: 2000});
                        setTimeout(function(){ window.location.reload(); }, 1500);
                    }
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    // İlk hesaplama
    cdgAddonRecalc();
})();
</script>
<?php endif; ?>
