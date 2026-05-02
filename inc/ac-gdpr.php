<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - KVKK / GDPR Talepleri
 * Operation: gdpr_request (action: remove | anonymize | cancel)
 *
 * WiseCP runtime: $gdpr_request (mevcut bekleyen talep), $operation_link, Validation::get_csrf_token
 */

$gdpr_existing = isset($gdpr_request) && is_array($gdpr_request) ? $gdpr_request : null;
$has_pending = ($gdpr_existing && (($gdpr_existing['status'] ?? '') !== 'cancelled'));
$operation_link = isset($operation_link) ? $operation_link : (isset($links['controller']) ? $links['controller'] : '');
$csrf_token = '';
if(class_exists('Validation') && method_exists('Validation', 'get_csrf_token')) {
    try { $csrf_token = Validation::get_csrf_token('account', false); } catch(\Throwable $e) {}
}
?>

<style>
.cdg-gdpr-wrap { margin-top: 28px; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
.cdg-gdpr-wrap *, .cdg-gdpr-wrap *::before, .cdg-gdpr-wrap *::after { box-sizing: border-box; }
.cdg-gdpr-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
}
.cdg-gdpr-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-gdpr-head .icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6b21a8, #a855f7);
    color: #fff;
    display: grid; place-items: center;
    font-size: 18px;
}
.cdg-gdpr-head h3 { font-size: 15px; font-weight: 800; margin: 0; color: #0f172a; }
.cdg-gdpr-head .subtitle { font-size: 12px; color: #64748b; margin-top: 2px; }

.cdg-gdpr-info-box {
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 16px;
    display: flex; gap: 10px;
}
.cdg-gdpr-info-box i { color: #15803d; font-size: 20px; flex-shrink: 0; margin-top: 2px; }
.cdg-gdpr-info-box p { margin: 0; font-size: 13px; color: #15803d; line-height: 1.5; }
.cdg-gdpr-info-box strong { color: #166534; }

.cdg-gdpr-pending {
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 16px;
    display: flex; gap: 10px;
    align-items: flex-start;
}
.cdg-gdpr-pending i { color: #f59e0b; font-size: 20px; flex-shrink: 0; margin-top: 2px; }
.cdg-gdpr-pending .content { flex: 1; }
.cdg-gdpr-pending strong { display: block; color: #92400e; font-size: 13px; margin-bottom: 4px; }
.cdg-gdpr-pending small { color: #78350f; font-size: 12px; }
.cdg-gdpr-pending .actions { margin-top: 10px; }

.cdg-gdpr-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 14px;
}
.cdg-gdpr-action-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
    background: #fff;
    transition: all 0.2s;
}
.cdg-gdpr-action-card:hover { border-color: #a855f7; box-shadow: 0 8px 20px rgba(168,85,247,0.10); }
.cdg-gdpr-action-card.danger:hover { border-color: #ef4444; box-shadow: 0 8px 20px rgba(239,68,68,0.10); }
.cdg-gdpr-action-card h4 {
    font-size: 14px; font-weight: 800; color: #0f172a;
    margin: 0 0 8px;
    display: flex; align-items: center; gap: 6px;
}
.cdg-gdpr-action-card p {
    font-size: 12px; color: #64748b;
    margin: 0 0 14px;
    line-height: 1.5;
    min-height: 50px;
}
.cdg-gdpr-action-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px;
    color: #fff;
    border: 0; border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: transform 0.15s;
    width: 100%;
    justify-content: center;
}
.cdg-gdpr-action-btn:hover { transform: translateY(-1px); color: #fff; }
.cdg-gdpr-action-btn.danger { background: linear-gradient(135deg, #ef4444, #f87171); box-shadow: 0 4px 10px rgba(239,68,68,0.20); }
.cdg-gdpr-action-btn.warn { background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 10px rgba(245,158,11,0.20); }
.cdg-gdpr-action-btn.success { background: linear-gradient(135deg, #10b981, #34d399); box-shadow: 0 4px 10px rgba(16,185,129,0.20); }
</style>

<div class="cdg-gdpr-wrap">
    <div class="cdg-gdpr-section">
        <div class="cdg-gdpr-head">
            <div class="icon"><i class="bi bi-shield-lock"></i></div>
            <div>
                <h3>KVKK / Veri Koruma</h3>
                <div class="subtitle">Kişisel Verilerin Korunması Kanunu kapsamındaki haklarınızı kullanın</div>
            </div>
        </div>

        <div class="cdg-gdpr-info-box">
            <i class="bi bi-info-circle-fill"></i>
            <p>
                <strong>KVKK 11. Madde gereği</strong> aşağıdaki haklara sahipsiniz: kişisel verilerinizin işlenip işlenmediğini öğrenmek,
                eksik/yanlış işlenmişse düzeltilmesini istemek, silinmesini veya yok edilmesini istemek.
                Bu hakları aşağıdaki butonlarla kullanabilirsiniz.
            </p>
        </div>

        <?php if($has_pending):
            $pending_type = $gdpr_existing['type'] ?? 'remove';
            $pending_date = $gdpr_existing['created_at'] ?? '';
            $pending_status = $gdpr_existing['status'] ?? 'pending';
            $pending_note = $gdpr_existing['status_note'] ?? '';
            $pending_label = ($pending_type === 'remove') ? 'Hesap silme' : 'Anonimleştirme';
        ?>
        <div class="cdg-gdpr-pending">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div class="content">
                <strong><?php echo $pending_label; ?> talebiniz işlemde</strong>
                <small>
                    <?php if($pending_date): ?>
                    Talep tarihi: <?php echo htmlspecialchars($pending_date); ?>
                    <?php endif; ?>
                    · Durum: <strong><?php echo htmlspecialchars($pending_status); ?></strong>
                </small>
                <?php if($pending_note): ?>
                <div style="margin-top:6px;font-size:12px;color:#78350f;background:#fff;padding:8px 10px;border-radius:6px;">
                    <strong>Operatör Notu:</strong> <?php echo htmlspecialchars($pending_note); ?>
                </div>
                <?php endif; ?>
                <div class="actions">
                    <button type="button" class="cdg-gdpr-action-btn success" style="width:auto;display:inline-flex;" onclick="cdgGdprRequest(this, 'cancel')">
                        <i class="bi bi-x-circle"></i> Talebi İptal Et
                    </button>
                </div>
            </div>
        </div>
        <?php else: ?>

        <div class="cdg-gdpr-actions">
            <div class="cdg-gdpr-action-card danger">
                <h4><i class="bi bi-trash3-fill" style="color:#ef4444;"></i> Hesabımı Sil</h4>
                <p>Hesabınız ve tüm verileriniz <strong>kalıcı olarak silinir</strong>. Bu işlem geri alınamaz. Aktif hizmetleriniz varsa önce iptal edilmelidir.</p>
                <button type="button" class="cdg-gdpr-action-btn danger" onclick="cdgGdprRequest(this, 'remove')">
                    <i class="bi bi-trash3"></i> Hesap Silme Talebi
                </button>
            </div>
            <div class="cdg-gdpr-action-card">
                <h4><i class="bi bi-eye-slash-fill" style="color:#f59e0b;"></i> Verilerimi Anonimleştir</h4>
                <p>Hesabınız korunur ancak <strong>kişisel bilgileriniz anonim hale getirilir</strong>. Geçmiş işlem kayıtlarınız tutulurken kimliğinizle ilişkilendirilemez.</p>
                <button type="button" class="cdg-gdpr-action-btn warn" onclick="cdgGdprRequest(this, 'anonymize')">
                    <i class="bi bi-eye-slash"></i> Anonimleştirme Talebi
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function(){
    var cdgGdprUrl = '<?php echo htmlspecialchars($operation_link, ENT_QUOTES); ?>';
    var cdgGdprCsrf = '<?php echo htmlspecialchars($csrf_token, ENT_QUOTES); ?>';

    window.cdgGdprRequest = function(btn, action) {
        var msg = '';
        if(action === 'remove') {
            msg = 'HESAP SILME talebi oluşturuyorsunuz!\n\n' +
                  'Bu işlem onaylandığında:\n' +
                  '• Hesabınız ve tüm verileriniz kalıcı olarak silinir\n' +
                  '• Geçmiş siparişleriniz, faturalar, talepler silinir\n' +
                  '• Bu işlem GERİ ALINAMAZ\n\n' +
                  'Devam etmek istediğinize emin misiniz?';
        } else if(action === 'anonymize') {
            msg = 'Verilerinizi anonimleştirmek istediğinize emin misiniz?\n\n' +
                  'Hesabınız korunur ama kişisel bilgileriniz silinir.';
        } else if(action === 'cancel') {
            msg = 'Mevcut KVKK talebinizi iptal etmek istediğinize emin misiniz?';
        }

        if(!confirm(msg)) return;
        if(typeof MioAjax !== 'function') return;

        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        var data = { operation: 'gdpr_request', action: action };
        if(cdgGdprCsrf) data.token = cdgGdprCsrf;

        MioAjax({
            url: cdgGdprUrl, type: 'post', data: data,
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Talebiniz alındı', {timer: 2500});
                    setTimeout(function(){ window.location.href = cdgGdprUrl + '?tab=gdpr'; }, 2000);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };
})();
</script>
