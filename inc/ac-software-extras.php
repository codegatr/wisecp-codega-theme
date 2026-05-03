<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Software Lisans Yönetimi
 * Operations: change_software_domain, reissue_software
 *
 * WiseCP runtime: $proanse, $options, $links, $change_domain_limit, $change_domain_used,
 *                 $change_domain_has_expired, $current_domain, $license_parameters
 */

$d_status = strtolower($proanse['status'] ?? 'unknown');
$is_active = ($d_status === 'active');
$controller_url = $links['controller'] ?? '';

$cd_limit = isset($change_domain_limit) ? (int)$change_domain_limit : 0;
$cd_used = isset($change_domain_used) ? (int)$change_domain_used : 0;
$cd_remaining = max(0, $cd_limit - $cd_used);
$cd_expired = !empty($change_domain_has_expired);
$can_change_domain = ($is_active && !$cd_expired);
$current_domain_str = $current_domain ?? ($proanse['domain'] ?? '');

// === LİSANS BİLGİLERİ (WiseCP runtime: $options) ===
$sw_options = isset($options) && is_array($options) ? $options : ($proanse['options'] ?? []);
$license_code = $sw_options['code'] ?? '';
$license_ip = $sw_options['ip'] ?? '';
$license_params = isset($license_parameters) && is_array($license_parameters) ? $license_parameters : [];
$license_params_values = $sw_options['license_parameters'] ?? ($sw_options['parameters'] ?? []);

if(!$is_active) return; // Aktif değilse hiç gösterme
?>

<!-- LİSANS BİLGİLERİ KARTI -->
<div style="margin-top:20px;font-family:'Plus Jakarta Sans',sans-serif;">
    <div class="cdg-pd2-card">
        <div class="cdg-pd2-card-head">
            <h3><i class="bi bi-key-fill"></i> Lisans Bilgileri</h3>
        </div>
        <div class="cdg-pd2-card-body">
            <ul class="cdg-pd2-info">
                <?php if($current_domain_str): ?>
                <li>
                    <span class="cdg-pd2-info-label">Lisans Domaini</span>
                    <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:700;color:#2E3B4E;">
                        <?php echo htmlspecialchars($current_domain_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </span>
                </li>
                <?php endif; ?>

                <?php if($license_code): ?>
                <li>
                    <span class="cdg-pd2-info-label">Lisans Kodu</span>
                    <span class="cdg-pd2-info-value">
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <code id="cdg-sw-license-code" style="background:#f1f5f9;padding:4px 10px;border-radius:6px;font-size:12px;color:#be185d;font-weight:600;letter-spacing:0.5px;">
                                <?php echo htmlspecialchars($license_code, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </code>
                            <button type="button" onclick="cdgSwCopy('cdg-sw-license-code', this)" class="cdg-pd2-btn cdg-pd2-btn-outline" style="padding:4px 8px;font-size:11px;" title="Kopyala">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </span>
                    </span>
                </li>
                <?php endif; ?>

                <?php if($license_ip): ?>
                <li>
                    <span class="cdg-pd2-info-label">Lisans IP</span>
                    <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:600;">
                        <?php echo htmlspecialchars($license_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </span>
                </li>
                <?php endif; ?>

                <?php
                // Özel lisans parametreleri
                if(!empty($license_params) && is_array($license_params)):
                    foreach($license_params as $param):
                        $p_key = is_array($param) ? ($param['key'] ?? ($param['name'] ?? '')) : '';
                        $p_label = is_array($param) ? ($param['title'] ?? ($param['label'] ?? $p_key)) : '';
                        $p_value = '';
                        if(is_array($license_params_values) && $p_key && isset($license_params_values[$p_key])) {
                            $p_value = $license_params_values[$p_key];
                        } elseif(is_array($param) && isset($param['value'])) {
                            $p_value = $param['value'];
                        }
                        if(!$p_label || !$p_value) continue;
                ?>
                <li>
                    <span class="cdg-pd2-info-label"><?php echo htmlspecialchars($p_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <span class="cdg-pd2-info-value" style="font-family:'Courier New',monospace;font-weight:600;">
                        <?php echo htmlspecialchars($p_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </span>
                </li>
                <?php endforeach; endif; ?>
            </ul>

            <?php if(!$license_code && !$license_ip): ?>
            <div style="text-align:center;padding:24px;color:#94a3b8;">
                <i class="bi bi-key" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.5;"></i>
                <p style="font-size:13px;margin:0;">Lisans bilgileriniz henüz oluşturulmamış veya gösterilemiyor.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
window.cdgSwCopy = function(elemId, btn) {
    var el = document.getElementById(elemId);
    if(!el) return;
    var text = el.textContent.trim();
    if(navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function(){
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i>';
            btn.style.color = '#10b981';
            setTimeout(function(){ btn.innerHTML = orig; btn.style.color = ''; }, 1500);
        });
    } else {
        // Fallback
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); btn.innerHTML = '<i class="bi bi-check2"></i>'; } catch(e){}
        document.body.removeChild(ta);
        setTimeout(function(){ btn.innerHTML = '<i class="bi bi-clipboard"></i>'; }, 1500);
    }
};
</script>

<style>
.cdg-sw-extras {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 16px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}
.cdg-sw-extras *, .cdg-sw-extras *::before, .cdg-sw-extras *::after { box-sizing: border-box; }
.cdg-sw-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
}
.cdg-sw-card-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-sw-card-head .icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    color: #fff;
    display: grid; place-items: center;
    font-size: 18px;
}
.cdg-sw-card-head h3 { font-size: 15px; font-weight: 800; margin: 0; color: #0f172a; }
.cdg-sw-current {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 12px;
    margin-bottom: 14px;
}
.cdg-sw-current strong {
    display: block;
    font-size: 11px; color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}
.cdg-sw-current code {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 13px;
    color: #0f172a;
    background: #fff;
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
.cdg-sw-limit {
    font-size: 12px; color: #64748b;
    margin-top: 8px;
    display: flex; align-items: center; gap: 6px;
}
.cdg-sw-limit-num { font-weight: 800; color: #f59e0b; font-size: 14px; }
.cdg-sw-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px; color: #0f172a;
    font-family: inherit;
    outline: none;
    margin-bottom: 12px;
    transition: border 0.15s;
}
.cdg-sw-input:focus { border-color: #2E3B4E; box-shadow: 0 0 0 3px rgba(46,59,78,0.10); }
.cdg-sw-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 11px 18px;
    color: #fff;
    border: 0;
    border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: transform 0.15s;
    width: 100%;
    justify-content: center;
}
.cdg-sw-btn:hover { transform: translateY(-1px); }
.cdg-sw-btn-primary { background: linear-gradient(135deg, #2E3B4E, #00D3E5); box-shadow: 0 4px 10px rgba(46,59,78,0.22); }
.cdg-sw-btn-warn { background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 10px rgba(245,158,11,0.22); }
.cdg-sw-btn:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none; }
.cdg-sw-warn-box {
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 12px; color: #92400e;
    margin-bottom: 12px;
    display: flex; gap: 8px;
}
.cdg-sw-warn-box i { flex-shrink: 0; color: #f59e0b; }
.cdg-sw-info-box {
    background: #CFFAFE;
    border: 1px solid #67E8F9;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 12px; color: #2E3B4E;
    margin-bottom: 12px;
    display: flex; gap: 8px;
}
.cdg-sw-info-box i { flex-shrink: 0; color: #485A75; }
</style>

<div class="cdg-sw-extras">

    <!-- Domain Değiştirme Kartı -->
    <div class="cdg-sw-card">
        <div class="cdg-sw-card-head">
            <div class="icon" style="background:linear-gradient(135deg,#2E3B4E,#00D3E5);"><i class="bi bi-globe2"></i></div>
            <h3>Lisans Domain'i Değiştir</h3>
        </div>

        <?php if($current_domain_str): ?>
        <div class="cdg-sw-current">
            <strong>Mevcut Domain</strong>
            <code><?php echo htmlspecialchars($current_domain_str, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code>
        </div>
        <?php endif; ?>

        <?php if($cd_limit > 0): ?>
        <div class="cdg-sw-limit">
            <i class="bi bi-info-circle"></i>
            Kalan değişiklik hakkı: <span class="cdg-sw-limit-num"><?php echo $cd_remaining; ?></span> / <?php echo $cd_limit; ?>
        </div>
        <?php endif; ?>

        <?php if($cd_expired): ?>
        <div class="cdg-sw-warn-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>Domain değiştirme limitiniz dolmuştur. Bir sonraki yenileme döneminde tekrar kullanılabilir olacaktır.</div>
        </div>
        <?php endif; ?>

        <?php if($can_change_domain): ?>
        <p style="font-size:13px;color:#64748b;margin:14px 0 12px;">
            Lisansınızın bağlı olduğu domain'i değiştirebilirsiniz. Yeni domain'in lisans operasyonları doğrulamadan geçtikten sonra aktif olacaktır.
        </p>

        <input type="text" id="cdg-sw-newdomain" class="cdg-sw-input" placeholder="yeni-domain.com" autocomplete="off">

        <button type="button" class="cdg-sw-btn cdg-sw-btn-primary" onclick="cdgSwChangeDomain(this)">
            <i class="bi bi-arrow-repeat"></i> Domain Değiştir
        </button>
        <?php endif; ?>
    </div>

    <!-- Reissue Kartı -->
    <div class="cdg-sw-card">
        <div class="cdg-sw-card-head">
            <div class="icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-arrow-clockwise"></i></div>
            <h3>Lisansı Yeniden Bas</h3>
        </div>

        <div class="cdg-sw-info-box">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                Lisans bilgilerinizi yenilemek (reissue) işlemi mevcut lisans dosyanızı/anahtarınızı yeniden oluşturur.
                Genellikle <strong>kurulumla ilgili sorun yaşıyorsanız</strong> veya lisans dosyası bozulduysa kullanılır.
            </div>
        </div>

        <p style="font-size:13px;color:#64748b;margin:14px 0 12px;">
            Bu işlem mevcut kurulumlarınızda lisans yenilenmesini gerektirebilir. Devam etmek istediğinizden emin misiniz?
        </p>

        <button type="button" class="cdg-sw-btn cdg-sw-btn-warn" onclick="cdgSwReissue(this)">
            <i class="bi bi-arrow-clockwise"></i> Lisansı Yeniden Bas
        </button>
    </div>

</div>

<script>
(function(){
    var cdgSwUrl = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
    var cdgSwPid = <?php echo (int)($proanse['id'] ?? 0); ?>;

    window.cdgSwChangeDomain = function(btn) {
        var inp = document.getElementById('cdg-sw-newdomain');
        if(!inp || !inp.value.trim()) {
            if(typeof alert_error === 'function') alert_error('Yeni domain girin', {timer: 3000});
            return;
        }
        var newDomain = inp.value.trim().toLowerCase();
        // Basit domain validation
        if(!/^([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/.test(newDomain)) {
            if(typeof alert_error === 'function') alert_error('Geçerli bir domain girin (örn: example.com)', {timer: 3000});
            return;
        }
        if(!confirm('Lisansınızın domain\'ini ' + newDomain + ' olarak değiştirmek istediğinize emin misiniz?')) return;
        if(typeof MioAjax !== 'function') return;

        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        MioAjax({
            url: cdgSwUrl, type: 'post',
            data: { operation: 'change_software_domain', id: cdgSwPid, domain: newDomain },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Domain başarıyla değiştirildi', {timer: 2500});
                    setTimeout(function(){ window.location.reload(); }, 2000);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgSwReissue = function(btn) {
        if(!confirm('Lisansı yeniden basmak istediğinize emin misiniz? Mevcut kurulumlarınızda lisans yenilemesi gerekebilir.')) return;
        if(typeof MioAjax !== 'function') return;

        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        MioAjax({
            url: cdgSwUrl, type: 'post',
            data: { operation: 'reissue_software', id: cdgSwPid },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Lisans yeniden basıldı', {timer: 2500});
                    setTimeout(function(){ window.location.href = cdgSwUrl; }, 1800);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };
})();
</script>
