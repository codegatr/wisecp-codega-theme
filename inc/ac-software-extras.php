<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Software Lisans Yönetimi
 * Operations: change_software_domain, reissue_software
 *
 * WiseCP runtime: $proanse, $links, $change_domain_limit, $change_domain_used,
 *                 $change_domain_has_expired, $current_domain
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

if(!$is_active) return; // Aktif değilse hiç gösterme
?>

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
.cdg-sw-input:focus { border-color: #1e40af; box-shadow: 0 0 0 3px rgba(30,64,175,0.10); }
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
.cdg-sw-btn-primary { background: linear-gradient(135deg, #1e40af, #3b82f6); box-shadow: 0 4px 10px rgba(30,64,175,0.22); }
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
    background: #dbeafe;
    border: 1px solid #93c5fd;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 12px; color: #1e40af;
    margin-bottom: 12px;
    display: flex; gap: 8px;
}
.cdg-sw-info-box i { flex-shrink: 0; color: #2563eb; }
</style>

<div class="cdg-sw-extras">

    <!-- Domain Değiştirme Kartı -->
    <div class="cdg-sw-card">
        <div class="cdg-sw-card-head">
            <div class="icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-globe2"></i></div>
            <h3>Lisans Domain'i Değiştir</h3>
        </div>

        <?php if($current_domain_str): ?>
        <div class="cdg-sw-current">
            <strong>Mevcut Domain</strong>
            <code><?php echo htmlspecialchars($current_domain_str); ?></code>
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
    var cdgSwUrl = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES); ?>';
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
