<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Hosting Özel İşlemler (Tek-Tıkla Giriş + Şifre Değiştirme)
 * ac-product-hosting.php tarafından template sonrası include edilir
 *
 * Operations: hosting_change_password
 * WiseCP runtime: $proanse, $supported, $buttons, $panel_name, $panel_logo, $server, $links
 */

$d_status = strtolower($proanse['status'] ?? 'unknown');
$is_active = ($d_status === 'active' && isset($server) && ($server['status'] ?? '') === 'active');
$supported = isset($supported) && is_array($supported) ? $supported : [];
$buttons = isset($buttons) && is_array($buttons) ? $buttons : [];
$panel_name = $proanse['panel_name'] ?? ($panel_name ?? 'Kontrol Paneli');
$panel_logo = $panel_logo ?? '';
$controller_url = $links['controller'] ?? '';
$can_change_pw = ($is_active && in_array('change-password', $supported));
$has_buttons   = ($is_active && !empty($buttons));
$can_manage_email = ($is_active && in_array('manage-email-account', $supported));

if(!$can_change_pw && !$has_buttons && !$can_manage_email) return; // Hiçbir özellik yoksa kart bile gösterme
?>

<style>
.cdg-host-extras {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 16px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-host-extras *, .cdg-host-extras *::before, .cdg-host-extras *::after { box-sizing: border-box; }
.cdg-host-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
}
.cdg-host-card-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-host-card-head .icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    display: grid; place-items: center;
    font-size: 18px;
}
.cdg-host-card-head h3 {
    font-size: 15px; font-weight: 800; margin: 0;
    color: #0f172a;
}
.cdg-host-panel-logo {
    width: 100%; max-width: 200px; height: 50px;
    object-fit: contain;
    margin-bottom: 12px;
    display: block;
}
.cdg-host-btn-list {
    display: flex; flex-direction: column; gap: 8px;
}
.cdg-host-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 16px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff !important;
    border: 0;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: transform 0.15s;
    box-shadow: 0 4px 10px rgba(16,185,129,0.22);
    font-family: inherit;
    text-align: center; justify-content: center;
}
.cdg-host-btn:hover { transform: translateY(-1px); color: #fff !important; }
.cdg-host-btn-primary {
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    box-shadow: 0 4px 10px rgba(46,59,78,0.22);
}
.cdg-host-pw-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 8px;
    align-items: stretch;
}
.cdg-host-pw-input {
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px; color: #0f172a;
    font-family: inherit;
    outline: none;
    transition: border 0.15s;
}
.cdg-host-pw-input:focus {
    border-color: #2E3B4E;
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}
.cdg-host-pw-toggle, .cdg-host-pw-gen {
    width: 42px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    color: #64748b;
    cursor: pointer;
    display: grid; place-items: center;
    transition: all 0.15s;
}
.cdg-host-pw-toggle:hover, .cdg-host-pw-gen:hover {
    border-color: #2E3B4E;
    color: #2E3B4E;
}
.cdg-host-pw-warning {
    margin: 12px 0;
    padding: 10px 14px;
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 8px;
    font-size: 12px; color: #92400e;
    line-height: 1.5;
}
.cdg-host-pw-warning i { color: #f59e0b; margin-right: 4px; }
</style>

<div class="cdg-host-extras">

    <?php if($has_buttons): ?>
    <!-- Panel Tek-Tıkla Giriş Kartı -->
    <div class="cdg-host-card">
        <div class="cdg-host-card-head">
            <div class="icon"><i class="bi bi-box-arrow-in-right"></i></div>
            <h3><?php echo htmlspecialchars($panel_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> Erişim</h3>
        </div>
        <?php if($panel_logo): ?>
        <img src="<?php echo htmlspecialchars($panel_logo, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($panel_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-host-panel-logo">
        <?php endif; ?>
        <p style="font-size:13px;color:#64748b;margin:0 0 14px;">
            Kontrol panelinize tek tıkla, şifre girmeden giriş yapabilirsiniz.
        </p>
        <div class="cdg-host-btn-list">
            <?php foreach($buttons as $b_type => $b_value):
                $url = $b_value['url'] ?? '#';
                $name = $b_value['name'] ?? $b_type;
                $icon = ($b_type === 'cpanel') ? 'bi-server' : (($b_type === 'webmail') ? 'bi-envelope' : 'bi-box-arrow-up-right');
            ?>
            <a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-host-btn">
                <i class="bi <?php echo $icon; ?>"></i> <?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if($can_change_pw): ?>
    <!-- Şifre Değiştirme Kartı -->
    <div class="cdg-host-card">
        <div class="cdg-host-card-head">
            <div class="icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-key-fill"></i></div>
            <h3><?php echo htmlspecialchars($panel_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> Şifre Değiştir</h3>
        </div>
        <p style="font-size:13px;color:#64748b;margin:0 0 14px;">
            Yeni şifrenizi belirleyin. Güçlü şifre en az 10 karakter, harf, sayı ve özel karakter içermelidir.
        </p>

        <form id="cdg-host-pw-form" onsubmit="return false;">
            <input type="hidden" name="operation" value="hosting_change_password">
            <input type="hidden" name="id" value="<?php echo (int)($proanse['id'] ?? 0); ?>">

            <div class="cdg-host-pw-row">
                <input type="password" name="password" id="cdg-host-pw-input"
                    class="cdg-host-pw-input" placeholder="Yeni şifre" autocomplete="new-password" minlength="6">
                <button type="button" class="cdg-host-pw-toggle" onclick="cdgHostPwToggle()" title="Göster/Gizle">
                    <i class="bi bi-eye" id="cdg-host-pw-eye"></i>
                </button>
                <button type="button" class="cdg-host-pw-gen" onclick="cdgHostPwGenerate()" title="Otomatik Oluştur">
                    <i class="bi bi-magic"></i>
                </button>
            </div>

            <div class="cdg-host-pw-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Şifrenizi değiştirdikten sonra <strong>tüm aktif oturumlardan</strong> çıkış yapılır.
                FTP / e-posta yapılandırmalarınızı yeni şifre ile güncellemeyi unutmayın.
            </div>

            <button type="button" class="cdg-host-btn cdg-host-btn-primary" style="width:100%;" onclick="cdgHostPwChange(this)">
                <i class="bi bi-check2-circle"></i> Şifreyi Değiştir
            </button>
        </form>
    </div>
    <?php endif; ?>

    <?php if($can_manage_email): ?>
    <!-- Email Hesapları Kartı -->
    <div class="cdg-host-card">
        <div class="cdg-host-card-head">
            <div class="icon" style="background:linear-gradient(135deg,#00D3E5,#00E5FF);"><i class="bi bi-envelope-at"></i></div>
            <h3>E-Posta Hesapları</h3>
        </div>
        <p style="font-size:13px;color:#64748b;margin:0 0 14px;">
            Hosting paketinize bağlı e-posta hesaplarını yönetin. Yeni hesap oluşturun, şifre değiştirin veya yönlendirme tanımlayın.
        </p>
        <button type="button" class="cdg-host-btn" style="background:linear-gradient(135deg,#00D3E5,#00E5FF);box-shadow:0 4px 10px rgba(59,130,246,0.22);" onclick="cdgHemOpen()">
            <i class="bi bi-gear"></i> E-Posta Hesaplarını Yönet
        </button>
    </div>
    <?php endif; ?>

</div>

<script>
function cdgHostPwToggle() {
    var inp = document.getElementById('cdg-host-pw-input');
    var eye = document.getElementById('cdg-host-pw-eye');
    if(!inp || !eye) return;
    if(inp.type === 'password') {
        inp.type = 'text';
        eye.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        eye.className = 'bi bi-eye';
    }
}

function cdgHostPwGenerate() {
    var charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%&*';
    var pw = '';
    for(var i = 0; i < 14; i++) pw += charset.charAt(Math.floor(Math.random() * charset.length));
    var inp = document.getElementById('cdg-host-pw-input');
    if(inp) {
        inp.value = pw;
        inp.type = 'text';
        var eye = document.getElementById('cdg-host-pw-eye');
        if(eye) eye.className = 'bi bi-eye-slash';
    }
}

function cdgHostPwChange(btn) {
    var inp = document.getElementById('cdg-host-pw-input');
    if(!inp || !inp.value) {
        if(typeof alert_error === 'function') alert_error('Lütfen yeni şifre girin', {timer: 3000});
        return;
    }
    if(inp.value.length < 6) {
        if(typeof alert_error === 'function') alert_error('Şifre en az 6 karakter olmalı', {timer: 3000});
        return;
    }
    if(!confirm('Hosting şifrenizi değiştirmek istediğinize emin misiniz?')) return;

    var url = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES); ?>';
    var pid = <?php echo (int)($proanse['id'] ?? 0); ?>;

    if(typeof MioAjax !== 'function') {
        if(typeof alert_error === 'function') alert_error('AJAX motoru yüklenemedi', {timer: 3000});
        return;
    }

    var origText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

    MioAjax({
        url: url, type: 'post',
        data: { operation: 'hosting_change_password', id: pid, password: inp.value },
        result: function(r) {
            btn.disabled = false;
            btn.innerHTML = origText;
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Şifre başarıyla değiştirildi', {timer: 2500});
                inp.value = '';
                inp.type = 'password';
                var eye = document.getElementById('cdg-host-pw-eye');
                if(eye) eye.className = 'bi bi-eye';
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 4000});
            }
        }
    });
}
</script>
