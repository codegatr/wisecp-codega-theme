<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Ürün Paket Yükseltme (Upgrade) Modülü
 * Hosting / Server / Software / Special için ortak
 *
 * Operation: set_upgrade_product (product_id + pirce_id)
 * WiseCP runtime: $upgrade (yükseltilebilir paket listesi), $proanse, $links
 *
 * $upgrade array yapısı (WiseCP):
 * [
 *   product_id => [
 *     'name' => 'Paket Adı',
 *     'description' => '...',
 *     'features' => [...],
 *     'options' => [
 *       price_id => ['name'=>'1 Aylık', 'amount'=>99, 'cid'=>'TRY', 'period'=>'m', 'period_time'=>1, 'payable'=>'99 TL']
 *     ]
 *   ]
 * ]
 */

$d_status = strtolower($proanse['status'] ?? 'unknown');
$upgrade_list = isset($upgrade) && is_array($upgrade) ? $upgrade : [];
$has_upgrade = ($d_status === 'active' && !empty($upgrade_list) && ($proanse['period'] ?? 'none') !== 'none');
$controller_url = $links['controller'] ?? '';

if(!$has_upgrade) return;
?>

<style>
/* === Codega Upgrade Card === */
.cdg-upg-wrap {
    margin-top: 20px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-upg-wrap *, .cdg-upg-wrap *::before, .cdg-upg-wrap *::after { box-sizing: border-box; }
.cdg-upg-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-upg-head .icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    display: grid; place-items: center;
    font-size: 18px;
}
.cdg-upg-head h3 { font-size: 15px; font-weight: 800; margin: 0; color: #0f172a; }
.cdg-upg-head .subtitle { font-size: 12px; color: #64748b; margin-top: 3px; }

.cdg-upg-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
}
.cdg-upg-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
    background: #fff;
    transition: all 0.2s;
    display: flex; flex-direction: column;
}
.cdg-upg-card:hover {
    border-color: #f59e0b;
    box-shadow: 0 8px 20px rgba(245,158,11,0.10);
    transform: translateY(-2px);
}
.cdg-upg-card-name {
    font-size: 16px; font-weight: 800; color: #0f172a;
    margin: 0 0 6px;
}
.cdg-upg-card-desc {
    font-size: 12px; color: #64748b;
    margin: 0 0 12px;
    line-height: 1.5;
    min-height: 36px;
}
.cdg-upg-card-features {
    list-style: none; padding: 0; margin: 0 0 14px;
    border-top: 1px solid #f1f5f9;
    padding-top: 10px;
}
.cdg-upg-card-features li {
    display: flex; align-items: flex-start; gap: 6px;
    font-size: 12px; color: #475569;
    padding: 3px 0;
}
.cdg-upg-card-features li i { color: #10b981; flex-shrink: 0; margin-top: 2px; }

.cdg-upg-period-select {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px; color: #0f172a;
    background: #fff;
    margin-bottom: 10px;
    font-family: inherit;
    outline: none;
    transition: border 0.15s;
}
.cdg-upg-period-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.12); }

.cdg-upg-card-btn {
    margin-top: auto;
    padding: 11px 14px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    border: 0;
    border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: transform 0.15s;
    font-family: inherit;
    box-shadow: 0 4px 10px rgba(245,158,11,0.22);
}
.cdg-upg-card-btn:hover { transform: translateY(-1px); }

/* Confirmation modal */
.cdg-upg-confirm-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.55);
    backdrop-filter: blur(4px);
    display: none; align-items: center; justify-content: center;
    z-index: 9050;
    padding: 20px;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.cdg-upg-confirm-overlay.cdg-upg-open { display: flex; }
.cdg-upg-confirm-modal {
    background: #fff; border-radius: 16px;
    box-shadow: 0 24px 60px rgba(15,23,42,0.30);
    width: 100%; max-width: 480px;
    overflow: hidden;
}
.cdg-upg-confirm-head {
    padding: 18px 22px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    display: flex; align-items: center; gap: 10px;
}
.cdg-upg-confirm-head h3 { font-size: 17px; font-weight: 800; margin: 0; }
.cdg-upg-confirm-body { padding: 22px; }
.cdg-upg-confirm-body p { font-size: 14px; color: #334155; line-height: 1.6; margin: 0 0 12px; }
.cdg-upg-confirm-body p strong { color: #0f172a; }
.cdg-upg-confirm-actions {
    padding: 14px 22px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex; gap: 8px; justify-content: flex-end;
}
.cdg-upg-confirm-actions button {
    padding: 9px 18px;
    border: 0; border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    font-family: inherit;
}
.cdg-upg-cancel { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.cdg-upg-cancel:hover { border-color: #94a3b8; }
.cdg-upg-ok { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff; box-shadow: 0 4px 10px rgba(245,158,11,0.22); }
</style>

<div class="cdg-upg-wrap">
    <div class="cdg-upg-head">
        <div class="icon"><i class="bi bi-arrow-up-circle"></i></div>
        <div>
            <h3>Paket Yükseltme</h3>
            <div class="subtitle">Daha fazla kaynak veya özellik için paketinizi yükseltin</div>
        </div>
    </div>

    <div class="cdg-upg-grid">
        <?php foreach($upgrade_list as $up_id => $up):
            $up_id = (int)$up_id;
            $up_name = $up['name'] ?? ('Paket #' . $up_id);
            $up_desc = $up['description'] ?? '';
            $up_features = $up['features'] ?? [];
            $up_options = $up['options'] ?? [];
            if(empty($up_options)) continue;
        ?>
        <div class="cdg-upg-card" data-upg-id="<?php echo $up_id; ?>">
            <h4 class="cdg-upg-card-name"><?php echo htmlspecialchars($up_name); ?></h4>
            <?php if($up_desc): ?>
            <p class="cdg-upg-card-desc"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($up_desc), 0, 100, '...')); ?></p>
            <?php endif; ?>

            <?php if(!empty($up_features) && is_array($up_features)): ?>
            <ul class="cdg-upg-card-features">
                <?php foreach(array_slice($up_features, 0, 5) as $feat): ?>
                <li><i class="bi bi-check-circle-fill"></i> <span><?php echo htmlspecialchars(is_array($feat) ? ($feat['name'] ?? '') : $feat); ?></span></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <input type="hidden" id="product_<?php echo $up_id; ?>_name" value="<?php echo htmlspecialchars($up_name, ENT_QUOTES); ?>">
            <select class="cdg-upg-period-select" id="product_<?php echo $up_id; ?>_price">
                <?php foreach($up_options as $opt):
                    $opt_id = $opt['id'] ?? 0;
                    $opt_name = $opt['name'] ?? '';
                    $opt_payable = '';
                    $amount = $opt['amount'] ?? 0;
                    $cid = $opt['cid'] ?? 'TRY';
                    if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
                        try {
                            $opt_payable = Money::formatter_symbol($amount, $cid, !($opt['override_usrcurrency'] ?? false));
                        } catch(\Throwable $e) { $opt_payable = $amount . ' ' . $cid; }
                    } else {
                        $opt_payable = $amount . ' ' . $cid;
                    }
                    $period_label = '';
                    if(class_exists('View') && method_exists('View','period')) {
                        try { $period_label = View::period($opt['period_time'] ?? 1, $opt['period'] ?? 'm'); } catch(\Throwable $e) {}
                    }
                    $label = $opt_name . ' — ' . $opt_payable . ($period_label ? ' / ' . $period_label : '');
                ?>
                <option value="<?php echo (int)$opt_id; ?>" data-payable="<?php echo htmlspecialchars($opt_payable, ENT_QUOTES); ?>"><?php echo htmlspecialchars($label); ?></option>
                <?php endforeach; ?>
            </select>

            <button type="button" class="cdg-upg-card-btn" onclick="cdgUpgradeRequest(<?php echo $up_id; ?>)">
                <i class="bi bi-arrow-up-circle"></i> Bu Pakete Yükselt
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="cdg-upg-confirm-overlay" id="cdg-upg-confirm" role="dialog" aria-modal="true">
    <div class="cdg-upg-confirm-modal">
        <div class="cdg-upg-confirm-head">
            <i class="bi bi-arrow-up-circle"></i>
            <h3>Yükseltme Onayı</h3>
        </div>
        <div class="cdg-upg-confirm-body">
            <p id="cdg-upg-confirm-text">Yükseltmek üzeresiniz...</p>
            <p style="font-size:12px;color:#64748b;background:#fef3c7;border:1px solid #fcd34d;padding:10px 12px;border-radius:8px;">
                <i class="bi bi-info-circle"></i>
                Yükseltme onaylandığında ödeme için faturalarım sayfasına yönlendirileceksiniz. Ödeme yapıldığında paket otomatik olarak değişecektir.
            </p>
        </div>
        <div class="cdg-upg-confirm-actions">
            <button type="button" class="cdg-upg-cancel" onclick="cdgUpgradeCancel()">Vazgeç</button>
            <button type="button" class="cdg-upg-ok" id="cdg-upg-confirm-ok">
                <i class="bi bi-check-lg"></i> Onayla ve Devam Et
            </button>
        </div>
    </div>
</div>

<script>
(function(){
    var cdgUpgUrl = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES); ?>';
    var cdgUpgPid = <?php echo (int)($proanse['id'] ?? 0); ?>;
    var pendingId = null, pendingPrice = null;

    window.cdgUpgradeRequest = function(id) {
        var sel = document.getElementById('product_' + id + '_price');
        var nameInp = document.getElementById('product_' + id + '_name');
        if(!sel || !nameInp) return;

        pendingId = id;
        pendingPrice = sel.value;

        var name = nameInp.value;
        var payable = sel.options[sel.selectedIndex].getAttribute('data-payable') || '';

        var txt = '<strong>' + name + '</strong> paketine yükseltmek istediğinize emin misiniz?<br><br>Ödenecek tutar: <strong>' + payable + '</strong>';
        document.getElementById('cdg-upg-confirm-text').innerHTML = txt;
        document.getElementById('cdg-upg-confirm').classList.add('cdg-upg-open');
        document.body.style.overflow = 'hidden';
    };

    window.cdgUpgradeCancel = function() {
        document.getElementById('cdg-upg-confirm').classList.remove('cdg-upg-open');
        document.body.style.overflow = '';
        pendingId = pendingPrice = null;
    };

    document.getElementById('cdg-upg-confirm-ok').addEventListener('click', function() {
        if(!pendingId || !pendingPrice) { cdgUpgradeCancel(); return; }
        if(typeof MioAjax !== 'function') return;

        var btn = this;
        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        MioAjax({
            url: cdgUpgUrl, type: 'post',
            data: {
                operation: 'set_upgrade_product',
                id: cdgUpgPid,
                product_id: pendingId,
                pirce_id: pendingPrice  // WiseCP API field — typo intentionally preserved (Classic uyumluluk)
            },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Yükseltme talebi oluşturuldu', {timer: 2000});
                    if(r.redirect) {
                        setTimeout(function(){ window.location.href = r.redirect; }, 1500);
                    } else {
                        cdgUpgradeCancel();
                        setTimeout(function(){ window.location.reload(); }, 1500);
                    }
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 5000});
                }
            }
        });
    });

    // ESC kapatma
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape') {
            var m = document.getElementById('cdg-upg-confirm');
            if(m && m.classList.contains('cdg-upg-open')) cdgUpgradeCancel();
        }
    });
    // Outside click
    var ov = document.getElementById('cdg-upg-confirm');
    if(ov) ov.addEventListener('click', function(e){ if(e.target === this) cdgUpgradeCancel(); });
})();
</script>
