<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - E-posta/GSM Dogrulama Modal
 *
 * Operations: verifyEmail, verifyGSM
 * Field'lar: code (kod), send (1=kod gonder, 0=kontrol et)
 *
 * Profil tab'inda dogrulanmamis email/gsm yaninda "Dogrula" butonu modal acar.
 */

$op_link = isset($operation_link) ? $operation_link : (isset($links['controller']) ? $links['controller'] : '');
$csrf_token = '';
if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) {
    try { $csrf_token = Validation::get_csrf_token('account', false); } catch(\Throwable $e) {}
}
?>

<!-- E-POSTA / GSM DOGRULAMA MODAL -->
<div class="cdg-vrf-overlay" id="cdg-vrf-modal" role="dialog" aria-modal="true">
    <div class="cdg-vrf-modal">
        <div class="cdg-vrf-head">
            <h3 id="cdg-vrf-title"><i class="bi bi-shield-check"></i> Dogrulama</h3>
            <button type="button" class="cdg-vrf-close" onclick="cdgVrfClose()" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-vrf-body">
            <div class="cdg-vrf-icon">
                <i class="bi bi-shield-check" id="cdg-vrf-icon"></i>
            </div>

            <p class="cdg-vrf-info" id="cdg-vrf-info">
                Adresinize bir dogrulama kodu gondermek icin "Kod Gonder" butonuna tiklayin.
            </p>

            <form id="cdg-vrf-form" onsubmit="return false;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" id="cdg-vrf-csrf">
                <input type="hidden" name="operation" value="verifyEmail" id="cdg-vrf-operation">
                <input type="hidden" name="send" value="0" id="cdg-vrf-send">

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Dogrulama Kodu</label>
                    <input type="text" name="code" id="cdg-vrf-code" class="cdg-form-control" placeholder="6 haneli kod" maxlength="10" autocomplete="off" style="font-size:18px;text-align:center;letter-spacing:6px;font-weight:700;">
                </div>

                <div id="cdg-vrf-result" style="display:none;padding:10px;border-radius:8px;font-size:13px;margin-bottom:10px;"></div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <button type="button" class="cdg-btn cdg-btn-outline" onclick="cdgVrfSend()" id="cdg-vrf-send-btn">
                        <i class="bi bi-send"></i> Kod Gonder
                    </button>
                    <button type="button" class="cdg-btn cdg-btn-success" onclick="cdgVrfCheck()" id="cdg-vrf-check-btn">
                        <i class="bi bi-check2-circle"></i> Dogrula
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cdg-vrf-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15,23,42,0.55);
    z-index: 9998;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.cdg-vrf-overlay.open { display: flex; animation: cdgVrfFade 0.2s ease; }
@keyframes cdgVrfFade { from { opacity: 0; } to { opacity: 1; } }
.cdg-vrf-modal {
    background: #fff;
    border-radius: 14px;
    width: 100%;
    max-width: 460px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.cdg-vrf-head {
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.cdg-vrf-head h3 { font-size: 16px; margin: 0; font-weight: 800; }
.cdg-vrf-close {
    background: rgba(255,255,255,0.18);
    border: 0; color: #fff;
    width: 30px; height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: grid; place-items: center;
}
.cdg-vrf-body { padding: 28px 24px 20px; }
.cdg-vrf-icon {
    text-align: center;
    margin-bottom: 16px;
}
.cdg-vrf-icon i {
    width: 70px; height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    display: inline-grid;
    place-items: center;
    font-size: 32px;
}
.cdg-vrf-info {
    font-size: 13px;
    color: #475569;
    text-align: center;
    margin: 0 0 18px;
    line-height: 1.5;
}
</style>

<script>
window.cdgVrfOpen = function(type, target) {
    var modal = document.getElementById('cdg-vrf-modal');
    var title = document.getElementById('cdg-vrf-title');
    var icon = document.getElementById('cdg-vrf-icon');
    var info = document.getElementById('cdg-vrf-info');
    var op = document.getElementById('cdg-vrf-operation');
    var code = document.getElementById('cdg-vrf-code');
    var result = document.getElementById('cdg-vrf-result');

    if(!modal) return;

    code.value = '';
    result.style.display = 'none';

    if(type === 'gsm') {
        title.innerHTML = '<i class="bi bi-phone"></i> Cep Telefonu Dogrulama';
        icon.className = 'bi bi-phone';
        info.innerHTML = '<strong>' + (target || 'Cep telefonunuza') + '</strong> bir dogrulama kodu SMS olarak gonderilecektir.';
        op.value = 'verifyGSM';
    } else {
        title.innerHTML = '<i class="bi bi-envelope"></i> E-posta Dogrulama';
        icon.className = 'bi bi-envelope';
        info.innerHTML = '<strong>' + (target || 'E-posta adresinize') + '</strong> bir dogrulama kodu gonderilecektir.';
        op.value = 'verifyEmail';
    }

    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
};

window.cdgVrfClose = function() {
    var modal = document.getElementById('cdg-vrf-modal');
    if(modal) modal.classList.remove('open');
    document.body.style.overflow = '';
};

window.cdgVrfSend = function() {
    var btn = document.getElementById('cdg-vrf-send-btn');
    var op = document.getElementById('cdg-vrf-operation').value;
    var send = document.getElementById('cdg-vrf-send');
    var result = document.getElementById('cdg-vrf-result');

    if(typeof MioAjax !== 'function') {
        cdgVrfShowResult('error', 'AJAX kullanılamıyor.');
        return;
    }

    send.value = '1';
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gonderiliyor...';

    MioAjax({
        url: '<?php echo htmlspecialchars($op_link, ENT_QUOTES); ?>',
        type: 'post',
        data: { operation: op, send: '1' },
        result: function(r) {
            btn.disabled = false; btn.innerHTML = orig;
            send.value = '0';
            if(r && r.status === 'successful') {
                cdgVrfShowResult('success', r.message || 'Dogrulama kodu gonderildi.');
                document.getElementById('cdg-vrf-code').focus();
            } else {
                cdgVrfShowResult('error', (r && r.message) || 'Kod gonderilemedi.');
            }
        }
    });
};

window.cdgVrfCheck = function() {
    var btn = document.getElementById('cdg-vrf-check-btn');
    var op = document.getElementById('cdg-vrf-operation').value;
    var code = document.getElementById('cdg-vrf-code').value.trim();

    if(!code) {
        cdgVrfShowResult('error', 'Lutfen dogrulama kodunu girin.');
        return;
    }

    if(typeof MioAjax !== 'function') return;

    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Dogrulaniyor...';

    MioAjax({
        url: '<?php echo htmlspecialchars($op_link, ENT_QUOTES); ?>',
        type: 'post',
        data: { operation: op, send: '0', code: code },
        result: function(r) {
            btn.disabled = false; btn.innerHTML = orig;
            if(r && r.status === 'successful') {
                cdgVrfShowResult('success', r.message || 'Dogrulama basarili!');
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else {
                cdgVrfShowResult('error', (r && r.message) || 'Kod hatali veya suresi dolmus.');
            }
        }
    });
};

window.cdgVrfShowResult = function(type, msg) {
    var result = document.getElementById('cdg-vrf-result');
    if(!result) return;
    result.style.display = 'block';
    if(type === 'success') {
        result.style.background = '#f0fdf4';
        result.style.color = '#15803d';
        result.style.border = '1px solid #86efac';
        result.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + msg;
    } else {
        result.style.background = '#fef2f2';
        result.style.color = '#b91c1c';
        result.style.border = '1px solid #fca5a5';
        result.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + msg;
    }
};

document.getElementById('cdg-vrf-modal').addEventListener('click', function(e) {
    if(e.target === this) cdgVrfClose();
});
</script>
