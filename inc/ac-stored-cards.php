<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Saklı Kart (Stored Cards) Yönetimi
 * Operations: stored_card_as_default, stored_card_remove, stored_card_auto_payment
 *
 * WiseCP runtime: $stored_cards (array), $auto_payment_active (bool/int), $operation_link
 */

$stored_cards_arr = isset($stored_cards) && is_array($stored_cards) ? $stored_cards : [];
$auto_payment_active = !empty($auto_payment_active);
$operation_link = isset($operation_link) ? $operation_link : (isset($links['controller']) ? $links['controller'] : '');
$csrf_token = '';
if(class_exists('Validation') && method_exists('Validation', 'get_csrf_token')) {
    try { $csrf_token = Validation::get_csrf_token('account', false); } catch(\Throwable $e) {}
}
?>

<style>
.cdg-cards-wrap {
    margin-top: 28px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}
.cdg-cards-wrap *, .cdg-cards-wrap *::before, .cdg-cards-wrap *::after { box-sizing: border-box; }
.cdg-cards-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
}
.cdg-cards-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-cards-head .icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    display: grid; place-items: center;
    font-size: 18px;
}
.cdg-cards-head h3 { font-size: 15px; font-weight: 800; margin: 0; color: #0f172a; }
.cdg-cards-head .subtitle { font-size: 12px; color: #64748b; margin-top: 2px; }

.cdg-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 14px;
}
.cdg-credit-card {
    position: relative;
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #fff;
    border-radius: 14px;
    padding: 22px;
    overflow: hidden;
    transition: transform 0.2s;
    min-height: 175px;
    display: flex; flex-direction: column;
}
.cdg-credit-card.is-default {
    background: linear-gradient(135deg, #0f172a, #1A2332);
    box-shadow: 0 8px 24px rgba(46,59,78,0.30);
}
.cdg-credit-card:hover { transform: translateY(-2px); }
.cdg-credit-card::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 150px; height: 150px;
    background: radial-gradient(circle, rgba(255,255,255,0.10), transparent 70%);
    border-radius: 50%;
}
.cdg-credit-card::after {
    content: '';
    position: absolute;
    bottom: -30px; left: -30px;
    width: 100px; height: 100px;
    background: radial-gradient(circle, rgba(59,130,246,0.20), transparent 70%);
    border-radius: 50%;
}
.cdg-credit-card-default-badge {
    position: absolute;
    top: 14px; right: 14px;
    background: rgba(16,185,129,0.20);
    color: #34d399;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.5px;
    border: 1px solid #10b981;
}
.cdg-credit-card-brand {
    font-size: 11px;
    letter-spacing: 1px;
    text-transform: uppercase;
    opacity: 0.7;
    margin-bottom: 6px;
}
.cdg-credit-card-number {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 1.5px;
    margin-bottom: auto;
}
.cdg-credit-card-bottom {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 12px;
    margin-top: 16px;
    position: relative;
    z-index: 1;
}
.cdg-credit-card-bottom .holder {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex: 1;
    word-break: break-word;
}
.cdg-credit-card-bottom .holder small {
    display: block;
    opacity: 0.6;
    font-size: 9px;
    margin-bottom: 2px;
    letter-spacing: 1px;
}
.cdg-credit-card-bottom .expiry {
    text-align: right;
}
.cdg-credit-card-actions {
    display: flex; gap: 6px;
    margin-top: 12px;
    position: relative;
    z-index: 1;
}
.cdg-card-action-btn {
    flex: 1;
    padding: 8px 10px;
    background: rgba(255,255,255,0.10);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.20);
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
    display: inline-flex; align-items: center; justify-content: center; gap: 4px;
}
.cdg-card-action-btn:hover { background: rgba(255,255,255,0.20); }
.cdg-card-action-btn.danger { background: rgba(239,68,68,0.20); border-color: rgba(239,68,68,0.40); color: #fca5a5; }
.cdg-card-action-btn.danger:hover { background: rgba(239,68,68,0.35); color: #fff; }
.cdg-card-action-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.cdg-cards-empty {
    text-align: center; padding: 40px;
    color: #94a3b8; font-size: 13px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
}
.cdg-cards-empty i { font-size: 48px; display: block; margin-bottom: 8px; }

.cdg-autopay-row {
    margin-top: 18px;
    padding: 14px 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    display: flex; align-items: center; gap: 12px;
}
.cdg-autopay-row .info { flex: 1; }
.cdg-autopay-row .info strong { font-size: 13px; color: #0f172a; display: block; margin-bottom: 3px; }
.cdg-autopay-row .info span { font-size: 12px; color: #64748b; }
.cdg-autopay-toggle {
    width: 50px; height: 26px;
    border-radius: 99px;
    background: #cbd5e1;
    position: relative;
    cursor: pointer;
    transition: background 0.2s;
    border: 0; padding: 0;
    flex-shrink: 0;
}
.cdg-autopay-toggle::before {
    content: '';
    position: absolute;
    top: 3px; left: 3px;
    width: 20px; height: 20px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
.cdg-autopay-toggle.active { background: #10b981; }
.cdg-autopay-toggle.active::before { transform: translateX(24px); }
</style>

<div class="cdg-cards-wrap">
    <div class="cdg-cards-section">
        <div class="cdg-cards-head">
            <div class="icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div>
                <h3>Saklı Kartlarım</h3>
                <div class="subtitle">Hızlı ödeme için kayıtlı kredi kartlarınızı yönetin</div>
            </div>
        </div>

        <?php if(!empty($stored_cards_arr)): ?>
        <div class="cdg-card-grid">
            <?php foreach($stored_cards_arr as $card):
                $card_id = (int)($card['id'] ?? 0);
                $is_default = !empty($card['default']) || !empty($card['is_default']);
                $card_no = $card['card_number'] ?? ($card['number'] ?? '****');
                // Mask kart numarası (eğer açıkça verilmemişse)
                if(strlen(str_replace(' ', '', $card_no)) > 6 && strpos($card_no, '*') === false) {
                    $cleaned = str_replace(' ', '', $card_no);
                    $card_no = substr($cleaned, 0, 4) . ' **** **** ' . substr($cleaned, -4);
                }
                $holder = $card['name'] ?? ($card['holder'] ?? ($card['card_holder'] ?? ''));
                $month = $card['month'] ?? ($card['expiry_month'] ?? '');
                $year = $card['year'] ?? ($card['expiry_year'] ?? '');
                if($year && strlen($year) === 4) $year = substr($year, -2);
                $brand = $card['brand'] ?? ($card['type'] ?? '');
                if(!$brand && $card_no) {
                    $first = substr(str_replace(' ', '', $card_no), 0, 1);
                    $brand = ($first === '4') ? 'Visa' : (($first === '5') ? 'Mastercard' : (($first === '3') ? 'Amex' : 'Card'));
                }
            ?>
            <div class="cdg-credit-card<?php echo $is_default ? ' is-default' : ''; ?>" data-card-id="<?php echo $card_id; ?>">
                <?php if($is_default): ?>
                <span class="cdg-credit-card-default-badge"><i class="bi bi-check-circle-fill"></i> VARSAYILAN</span>
                <?php endif; ?>

                <div class="cdg-credit-card-brand"><?php echo htmlspecialchars($brand, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <div class="cdg-credit-card-number"><?php echo htmlspecialchars($card_no, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>

                <div class="cdg-credit-card-bottom">
                    <div class="holder">
                        <small>KART SAHİBİ</small>
                        <?php echo htmlspecialchars($holder ?: '—', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                    <?php if($month && $year): ?>
                    <div class="expiry">
                        <small>SON KULLANMA</small>
                        <?php echo str_pad($month, 2, '0', STR_PAD_LEFT); ?>/<?php echo str_pad($year, 2, '0', STR_PAD_LEFT); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="cdg-credit-card-actions">
                    <?php if(!$is_default): ?>
                    <button type="button" class="cdg-card-action-btn" onclick="cdgCardSetDefault(<?php echo $card_id; ?>, this)">
                        <i class="bi bi-star"></i> Varsayılan Yap
                    </button>
                    <?php endif; ?>
                    <button type="button" class="cdg-card-action-btn danger" onclick="cdgCardRemove(<?php echo $card_id; ?>, this)">
                        <i class="bi bi-trash"></i> Sil
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Otomatik Ödeme Toggle -->
        <div class="cdg-autopay-row">
            <div class="info">
                <strong><i class="bi bi-arrow-repeat"></i> Otomatik Ödeme</strong>
                <span>Yenileme dönemlerinde varsayılan kartınızdan otomatik tahsilat yapılsın</span>
            </div>
            <button type="button" class="cdg-autopay-toggle <?php echo $auto_payment_active ? 'active' : ''; ?>" id="cdg-autopay-toggle" onclick="cdgCardAutoPayToggle(this)" title="Otomatik ödeme">
                <span class="visually-hidden"><?php echo $auto_payment_active ? 'Açık' : 'Kapalı'; ?></span>
            </button>
        </div>

        <?php else: ?>
        <div class="cdg-cards-empty">
            <i class="bi bi-credit-card"></i>
            <div><strong>Saklı kartınız bulunmuyor</strong></div>
            <div style="margin-top:6px;font-size:12px;">Bir ödeme yaparken "Kartı kaydet" seçeneğini işaretleyerek kart ekleyebilirsiniz.</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function(){
    var cdgCardUrl = '<?php echo htmlspecialchars($operation_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
    var cdgCardCsrf = '<?php echo htmlspecialchars($csrf_token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';

    window.cdgCardSetDefault = function(cardId, btn) {
        if(typeof MioAjax !== 'function') return;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        var data = { operation: 'stored_card_as_default', id: cardId };
        if(cdgCardCsrf) data.token = cdgCardCsrf;

        MioAjax({
            url: cdgCardUrl, type: 'post', data: data,
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Varsayılan kart güncellendi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1200);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgCardRemove = function(cardId, btn) {
        if(!confirm('Bu kartı kayıtlı kartlarınızdan silmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function') return;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Siliniyor...';

        var data = { operation: 'stored_card_remove', id: cardId };
        if(cdgCardCsrf) data.token = cdgCardCsrf;

        MioAjax({
            url: cdgCardUrl, type: 'post', data: data,
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    var card = document.querySelector('.cdg-credit-card[data-card-id="' + cardId + '"]');
                    if(card) card.remove();
                    if(typeof alert_success === 'function') alert_success(r.message || 'Kart silindi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1200);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgCardAutoPayToggle = function(btn) {
        if(typeof MioAjax !== 'function') return;
        var newStatus = btn.classList.contains('active') ? 0 : 1;

        // Optimistic UI
        btn.classList.toggle('active');

        var data = { operation: 'stored_card_auto_payment', status: newStatus };
        if(cdgCardCsrf) data.token = cdgCardCsrf;

        MioAjax({
            url: cdgCardUrl, type: 'post', data: data,
            result: function(r) {
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || (newStatus === 1 ? 'Otomatik ödeme aktif edildi' : 'Otomatik ödeme devre dışı bırakıldı'), {timer: 2000});
                } else {
                    // Geri al
                    btn.classList.toggle('active');
                    if(r && r.message && typeof alert_error === 'function') {
                        alert_error(r.message, {timer: 4000});
                    }
                }
            }
        });
    };
})();
</script>
