<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Hosting Email Hesapları Yönetim Modal
 * Operations: hosting_add_new_email, hosting_update_email, hosting_delete_email,
 *             hosting_add_new_email_forward, hosting_delete_email_forward
 *
 * WiseCP runtime: $mailDomains, $emailAccounts, $emailForwards, $supported, $links
 */

$d_status = strtolower($proanse['status'] ?? 'unknown');
$server_active = (isset($server) && ($server['status'] ?? '') === 'active');
$can_manage = ($d_status === 'active' && $server_active && in_array('manage-email-account', $supported ?? []));

if(!$can_manage) return;

$mail_domains = isset($mailDomains) && is_array($mailDomains) ? $mailDomains : [];
$email_accounts = isset($emailAccounts) && is_array($emailAccounts) ? $emailAccounts : [];
$email_forwards = isset($emailForwards) && is_array($emailForwards) ? $emailForwards : [];
$controller_url = $links['controller'] ?? '';
$no_unlimited = in_array('no-unlimited-email-account', $supported ?? []);
?>

<style>
/* === Codega Hosting Email Modal === */
.cdg-hem-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.55);
    backdrop-filter: blur(4px);
    display: none; align-items: center; justify-content: center;
    z-index: 9000;
    padding: 20px;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    animation: cdgHemFade 0.22s ease;
}
.cdg-hem-overlay *, .cdg-hem-overlay *::before, .cdg-hem-overlay *::after { box-sizing: border-box; }
.cdg-hem-overlay.cdg-hem-open { display: flex; }
@keyframes cdgHemFade { from { opacity: 0; } to { opacity: 1; } }
.cdg-hem-modal {
    background: #fff; border-radius: 16px;
    box-shadow: 0 24px 60px rgba(15,23,42,0.30);
    width: 100%; max-width: 920px;
    max-height: calc(100vh - 40px);
    display: flex; flex-direction: column;
    overflow: hidden;
}
.cdg-hem-head {
    padding: 18px 24px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-hem-head h3 {
    font-size: 17px; font-weight: 800; margin: 0;
    display: inline-flex; align-items: center; gap: 10px;
}
.cdg-hem-close {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    color: #fff;
    border: 0; cursor: pointer;
    font-size: 14px;
    display: grid; place-items: center;
    transition: background 0.15s;
}
.cdg-hem-close:hover { background: rgba(255,255,255,0.30); }

.cdg-hem-tabs {
    display: flex; gap: 4px;
    padding: 0 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.cdg-hem-tab {
    padding: 14px 20px;
    background: none;
    border: 0;
    border-bottom: 3px solid transparent;
    color: #64748b;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    transition: all 0.15s;
    font-family: inherit;
}
.cdg-hem-tab:hover { color: #10b981; }
.cdg-hem-tab.active { color: #10b981; border-bottom-color: #10b981; }
.cdg-hem-tab .badge {
    padding: 2px 8px;
    background: #e2e8f0;
    color: #475569;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
}
.cdg-hem-tab.active .badge { background: #10b981; color: #fff; }

.cdg-hem-body { padding: 22px; overflow-y: auto; flex: 1; }
.cdg-hem-pane { display: none; }
.cdg-hem-pane.active { display: block; }

.cdg-hem-form {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
}
.cdg-hem-form-title {
    font-size: 12px; font-weight: 800;
    color: #10b981;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.cdg-hem-field { margin-bottom: 10px; }
.cdg-hem-field label {
    display: block;
    font-size: 11px; font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}
.cdg-hem-input, .cdg-hem-select {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px; color: #0f172a;
    background: #fff;
    outline: none;
    transition: border 0.15s;
    font-family: inherit;
}
.cdg-hem-input:focus, .cdg-hem-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.10);
}
.cdg-hem-row {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 8px;
    align-items: end;
}
.cdg-hem-at { font-size: 14px; padding-bottom: 12px; color: #94a3b8; }

.cdg-hem-checkbox {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 6px;
    font-size: 12px; color: #475569;
    cursor: pointer;
}

.cdg-hem-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px;
    border: 0;
    border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    transition: transform 0.15s;
}
.cdg-hem-btn-primary { background: linear-gradient(135deg, #10b981, #34d399); color: #fff; box-shadow: 0 4px 10px rgba(16,185,129,0.22); }
.cdg-hem-btn-primary:hover { transform: translateY(-1px); color: #fff; }

.cdg-hem-list {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}
.cdg-hem-list-item {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 12px;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-hem-list-item:last-child { border-bottom: 0; }
.cdg-hem-list-addr { font-size: 13px; font-weight: 700; color: #0f172a; }
.cdg-hem-list-quota { font-size: 11px; color: #64748b; margin-top: 2px; }
.cdg-hem-list-action {
    width: 32px; height: 32px;
    border-radius: 7px;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #64748b;
    cursor: pointer;
    display: grid; place-items: center;
    font-size: 13px;
    transition: all 0.15s;
    font-family: inherit;
}
.cdg-hem-list-action:hover { border-color: #1e40af; color: #1e40af; }
.cdg-hem-list-action.danger { color: #ef4444; border-color: #fecaca; }
.cdg-hem-list-action.danger:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

.cdg-hem-empty {
    text-align: center; padding: 30px;
    color: #94a3b8; font-size: 13px;
}
.cdg-hem-empty i { font-size: 36px; display: block; margin-bottom: 6px; }
</style>

<div class="cdg-hem-overlay" id="cdg-hosting-emails-modal" role="dialog" aria-modal="true">
    <div class="cdg-hem-modal">
        <div class="cdg-hem-head">
            <h3><i class="bi bi-envelope-at"></i> E-Posta Hesapları</h3>
            <button type="button" class="cdg-hem-close" onclick="cdgHemClose()" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="cdg-hem-tabs">
            <button type="button" class="cdg-hem-tab active" onclick="cdgHemTab('accounts')" data-tab="accounts">
                <i class="bi bi-person-circle"></i> Hesaplar
                <span class="badge"><?php echo count($email_accounts); ?></span>
            </button>
            <button type="button" class="cdg-hem-tab" onclick="cdgHemTab('forwards')" data-tab="forwards">
                <i class="bi bi-forward"></i> Yönlendirmeler
                <span class="badge"><?php echo count($email_forwards); ?></span>
            </button>
        </div>

        <div class="cdg-hem-body">
            <!-- HESAPLAR TAB -->
            <div class="cdg-hem-pane active" id="cdg-hem-pane-accounts">
                <!-- Yeni Email Hesabı Ekleme -->
                <div class="cdg-hem-form">
                    <div class="cdg-hem-form-title"><i class="bi bi-plus-circle"></i> Yeni E-Posta Hesabı</div>
                    <div class="cdg-hem-row" style="grid-template-columns:1fr auto 1.2fr;">
                        <div class="cdg-hem-field" style="margin:0;">
                            <label>Kullanıcı Adı</label>
                            <input type="text" id="cdg-hem-username" class="cdg-hem-input" placeholder="info" autocomplete="off">
                        </div>
                        <div class="cdg-hem-at">@</div>
                        <div class="cdg-hem-field" style="margin:0;">
                            <label>Domain</label>
                            <select id="cdg-hem-domain" class="cdg-hem-select">
                                <?php foreach($mail_domains as $md): ?>
                                <option value="<?php echo htmlspecialchars($md); ?>"><?php echo htmlspecialchars($md); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="cdg-hem-field" style="margin-top:10px;">
                        <label>Şifre</label>
                        <div style="display:grid;grid-template-columns:1fr auto;gap:6px;">
                            <input type="text" id="cdg-hem-password" class="cdg-hem-input" placeholder="Güçlü şifre" autocomplete="new-password">
                            <button type="button" class="cdg-hem-btn cdg-hem-btn-primary" onclick="cdgHemPwGen()" style="background:#64748b;box-shadow:none;">
                                <i class="bi bi-magic"></i>
                            </button>
                        </div>
                    </div>

                    <div class="cdg-hem-row" style="grid-template-columns:1fr 1fr;margin-top:10px;">
                        <div class="cdg-hem-field" style="margin:0;">
                            <label>Kota (MB)</label>
                            <input type="number" id="cdg-hem-quota" class="cdg-hem-input" placeholder="500" min="1">
                        </div>
                        <?php if(!$no_unlimited): ?>
                        <div class="cdg-hem-field" style="margin:0;display:flex;align-items:flex-end;">
                            <label class="cdg-hem-checkbox">
                                <input type="checkbox" id="cdg-hem-unlimited" value="1">
                                <span>Sınırsız kota</span>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top:12px;">
                        <button type="button" class="cdg-hem-btn cdg-hem-btn-primary" onclick="cdgHemAddAccount(this)">
                            <i class="bi bi-plus-lg"></i> Hesap Oluştur
                        </button>
                    </div>
                </div>

                <!-- Mevcut Email Hesapları -->
                <div class="cdg-hem-form-title"><i class="bi bi-list-ul"></i> Mevcut Hesaplar</div>
                <?php if(!empty($email_accounts)): ?>
                <div class="cdg-hem-list">
                    <?php foreach($email_accounts as $acc):
                        $addr = $acc['address'] ?? ($acc['email'] ?? '');
                        $quota = $acc['quota'] ?? '';
                        $used = $acc['used'] ?? '';
                        if(!$addr) continue;
                    ?>
                    <div class="cdg-hem-list-item">
                        <div>
                            <div class="cdg-hem-list-addr"><?php echo htmlspecialchars($addr); ?></div>
                            <?php if($quota || $used): ?>
                            <div class="cdg-hem-list-quota">
                                Kota: <?php echo htmlspecialchars($quota ?: '∞'); ?>
                                <?php if($used): ?> · Kullanılan: <?php echo htmlspecialchars($used); ?><?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="cdg-hem-list-action" onclick="cdgHemUpdatePw('<?php echo htmlspecialchars($addr, ENT_QUOTES); ?>')" title="Şifre değiştir">
                            <i class="bi bi-key"></i>
                        </button>
                        <button type="button" class="cdg-hem-list-action danger" onclick="cdgHemDelete('<?php echo htmlspecialchars($addr, ENT_QUOTES); ?>')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="cdg-hem-empty">
                    <i class="bi bi-inbox"></i>
                    <div>Henüz e-posta hesabı oluşturulmamış</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- YÖNLENDİRMELER TAB -->
            <div class="cdg-hem-pane" id="cdg-hem-pane-forwards">
                <div class="cdg-hem-form">
                    <div class="cdg-hem-form-title"><i class="bi bi-plus-circle"></i> Yeni Yönlendirme</div>
                    <div class="cdg-hem-row" style="grid-template-columns:1fr auto 1fr auto 1fr;">
                        <div class="cdg-hem-field" style="margin:0;">
                            <label>Kaynak</label>
                            <input type="text" id="cdg-hem-fwd-prefix" class="cdg-hem-input" placeholder="info">
                        </div>
                        <div class="cdg-hem-at">@</div>
                        <div class="cdg-hem-field" style="margin:0;">
                            <label>Domain</label>
                            <select id="cdg-hem-fwd-domain" class="cdg-hem-select">
                                <?php foreach($mail_domains as $md): ?>
                                <option value="<?php echo htmlspecialchars($md); ?>"><?php echo htmlspecialchars($md); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="cdg-hem-at"><i class="bi bi-arrow-right"></i></div>
                        <div class="cdg-hem-field" style="margin:0;">
                            <label>Hedef E-Posta</label>
                            <input type="email" id="cdg-hem-fwd-target" class="cdg-hem-input" placeholder="hedef@gmail.com">
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <button type="button" class="cdg-hem-btn cdg-hem-btn-primary" onclick="cdgHemAddForward(this)">
                            <i class="bi bi-plus-lg"></i> Yönlendirme Ekle
                        </button>
                    </div>
                </div>

                <div class="cdg-hem-form-title"><i class="bi bi-list-ul"></i> Mevcut Yönlendirmeler</div>
                <?php if(!empty($email_forwards)): ?>
                <div class="cdg-hem-list">
                    <?php foreach($email_forwards as $fwd):
                        $dest = $fwd['dest'] ?? ($fwd['source'] ?? '');
                        $forward = $fwd['forward'] ?? ($fwd['target'] ?? '');
                        if(!$dest || !$forward) continue;
                    ?>
                    <div class="cdg-hem-list-item" style="grid-template-columns:1fr auto;">
                        <div>
                            <div class="cdg-hem-list-addr"><?php echo htmlspecialchars($dest); ?> <i class="bi bi-arrow-right" style="color:#94a3b8;font-size:11px;"></i> <?php echo htmlspecialchars($forward); ?></div>
                        </div>
                        <button type="button" class="cdg-hem-list-action danger" onclick="cdgHemDeleteForward('<?php echo htmlspecialchars($dest, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($forward, ENT_QUOTES); ?>')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="cdg-hem-empty">
                    <i class="bi bi-inbox"></i>
                    <div>Henüz yönlendirme yok</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
var cdgHemUrl = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES); ?>';
var cdgHemPid = <?php echo (int)($proanse['id'] ?? 0); ?>;

function cdgHemOpen() {
    var m = document.getElementById('cdg-hosting-emails-modal');
    if(m) { m.classList.add('cdg-hem-open'); document.body.style.overflow = 'hidden'; }
}
function cdgHemClose() {
    var m = document.getElementById('cdg-hosting-emails-modal');
    if(m) { m.classList.remove('cdg-hem-open'); document.body.style.overflow = ''; }
}
function cdgHemTab(tab) {
    document.querySelectorAll('.cdg-hem-tab').forEach(function(t){ t.classList.remove('active'); });
    document.querySelectorAll('.cdg-hem-pane').forEach(function(p){ p.classList.remove('active'); });
    var btn = document.querySelector('.cdg-hem-tab[data-tab="' + tab + '"]');
    var pane = document.getElementById('cdg-hem-pane-' + tab);
    if(btn) btn.classList.add('active');
    if(pane) pane.classList.add('active');
}
function cdgHemPwGen() {
    var charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!#$';
    var pw = '';
    for(var i = 0; i < 12; i++) pw += charset.charAt(Math.floor(Math.random() * charset.length));
    var inp = document.getElementById('cdg-hem-password');
    if(inp) inp.value = pw;
}
function cdgHemAddAccount(btn) {
    var u = document.getElementById('cdg-hem-username').value.trim();
    var d = document.getElementById('cdg-hem-domain').value;
    var p = document.getElementById('cdg-hem-password').value;
    var q = document.getElementById('cdg-hem-quota').value;
    var unl = document.getElementById('cdg-hem-unlimited');
    var unlimited = unl && unl.checked ? '1' : '';

    if(!u || !d || !p) {
        if(typeof alert_error === 'function') alert_error('Kullanıcı adı, domain ve şifre zorunludur', {timer: 3000});
        return;
    }
    if(!unlimited && !q) {
        if(typeof alert_error === 'function') alert_error('Kota girin veya sınırsız işaretleyin', {timer: 3000});
        return;
    }
    if(typeof MioAjax !== 'function') return;

    btn.disabled = true;
    var orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Oluşturuluyor...';

    MioAjax({
        url: cdgHemUrl, type: 'post',
        data: { operation: 'hosting_add_new_email', id: cdgHemPid, username: u, domain: d, password: p, quota: q, unlimited: unlimited },
        result: function(r) {
            btn.disabled = false; btn.innerHTML = orig;
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Hesap oluşturuldu', {timer: 2000});
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
}
function cdgHemUpdatePw(addr) {
    var pw = prompt('Yeni şifre (' + addr + ' için):');
    if(!pw) return;
    if(pw.length < 6) { alert('Şifre en az 6 karakter olmalı'); return; }
    if(typeof MioAjax !== 'function') return;
    MioAjax({
        url: cdgHemUrl, type: 'post',
        data: { operation: 'hosting_update_email', id: cdgHemPid, address: addr, password: pw },
        result: function(r) {
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Şifre güncellendi', {timer: 1500});
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
}
function cdgHemDelete(addr) {
    if(!confirm(addr + ' hesabını silmek istediğinize emin misiniz? Bu işlem geri alınamaz.')) return;
    if(typeof MioAjax !== 'function') return;
    MioAjax({
        url: cdgHemUrl, type: 'post',
        data: { operation: 'hosting_delete_email', id: cdgHemPid, address: addr },
        result: function(r) {
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Hesap silindi', {timer: 1500});
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
}
function cdgHemAddForward(btn) {
    var p = document.getElementById('cdg-hem-fwd-prefix').value.trim();
    var d = document.getElementById('cdg-hem-fwd-domain').value;
    var t = document.getElementById('cdg-hem-fwd-target').value.trim();

    if(!p || !d || !t) {
        if(typeof alert_error === 'function') alert_error('Tüm alanlar zorunludur', {timer: 3000});
        return;
    }
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(t)) {
        if(typeof alert_error === 'function') alert_error('Geçerli bir hedef e-posta girin', {timer: 3000});
        return;
    }
    if(typeof MioAjax !== 'function') return;

    btn.disabled = true;
    var orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Ekleniyor...';

    MioAjax({
        url: cdgHemUrl, type: 'post',
        data: { operation: 'hosting_add_new_email_forward', id: cdgHemPid, username: p, domain: d, target: t },
        result: function(r) {
            btn.disabled = false; btn.innerHTML = orig;
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme eklendi', {timer: 1500});
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
}
function cdgHemDeleteForward(dest, forward) {
    if(!confirm('Bu yönlendirmeyi silmek istediğinize emin misiniz?')) return;
    if(typeof MioAjax !== 'function') return;
    MioAjax({
        url: cdgHemUrl, type: 'post',
        data: { operation: 'hosting_delete_email_forward', id: cdgHemPid, dest: dest, forward: forward },
        result: function(r) {
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Yönlendirme silindi', {timer: 1500});
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
}
// ESC ile kapatma
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        var m = document.getElementById('cdg-hosting-emails-modal');
        if(m && m.classList.contains('cdg-hem-open')) cdgHemClose();
    }
});
// Outside click
(function(){
    var m = document.getElementById('cdg-hosting-emails-modal');
    if(m) m.addEventListener('click', function(e){ if(e.target === this) cdgHemClose(); });
})();
</script>
