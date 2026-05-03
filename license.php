<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
/**
 * Codega - Lisans Dogrulama Sayfasi
 *
 * Operation: check (domain + captcha)
 * WiseCP runtime: $captcha, $links
 */

$controller_link = isset($links['controller']) ? $links['controller'] : '';
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-shield-check"></i> Lisans Dogrulama</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Lisans Dogrulama</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:600px;">
        <div class="cdg-card" style="padding:36px;">
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;display:inline-grid;place-items:center;font-size:28px;margin-bottom:12px;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h2 style="margin:0 0 6px;font-size:22px;font-weight:800;">Lisans Sorgulama</h2>
                <p class="text-muted" style="font-size:13px;margin:0;">
                    Yazilim lisansinizin gecerliligini sorgulamak icin domain adinizi girin.
                </p>
            </div>

            <form action="<?php echo htmlspecialchars($controller_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" method="post" id="cdg-license-form" onsubmit="return false;">
                <input type="hidden" name="operation" value="check">
                <input type="hidden" name="hash" id="cdg-license-hash" value="">

                <div class="cdg-form-group">
                    <label class="cdg-form-label">
                        <i class="bi bi-globe"></i> Domain Adi
                    </label>
                    <input type="text" name="domain" id="cdg-license-domain" class="cdg-form-control" placeholder="ornek.com" required>
                </div>

                <?php if(isset($captcha) && $captcha): ?>
                <div class="cdg-form-group">
                    <label class="cdg-form-label"><i class="bi bi-shield-lock"></i> Guvenlik Dogrulamasi</label>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;text-align:center;margin-bottom:8px;">
                        <?php echo $captcha->getOutput(); ?>
                    </div>
                    <?php if($captcha->input): ?>
                    <input type="text" name="<?php echo htmlspecialchars($captcha->input_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-form-control" placeholder="Resimdeki kodu girin" required>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <button type="button" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;" id="cdg-license-submit-btn" onclick="cdgLicenseCheck()">
                    <i class="bi bi-search"></i> Lisansi Sorgula
                </button>
            </form>

            <!-- Sonuc Bolumu -->
            <div id="cdg-license-result-ok" style="display:none;margin-top:20px;padding:18px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:10px;text-align:center;">
                <div style="width:54px;height:54px;border-radius:50%;background:#10b981;color:#fff;display:inline-grid;place-items:center;font-size:24px;margin-bottom:10px;">
                    <i class="bi bi-check2"></i>
                </div>
                <h4 style="font-size:16px;font-weight:800;color:#15803d;margin:0 0 6px;">Lisans Aktif</h4>
                <p style="font-size:13px;color:#166534;margin:0;">Sorgulanan domain için geçerli bir lisans mevcuttur.</p>
            </div>

            <div id="cdg-license-result-fail" style="display:none;margin-top:20px;padding:18px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1px solid #fca5a5;border-radius:10px;text-align:center;">
                <div style="width:54px;height:54px;border-radius:50%;background:#ef4444;color:#fff;display:inline-grid;place-items:center;font-size:24px;margin-bottom:10px;">
                    <i class="bi bi-x"></i>
                </div>
                <h4 style="font-size:16px;font-weight:800;color:#b91c1c;margin:0 0 6px;">Lisans Bulunamadı</h4>
                <p style="font-size:13px;color:#7f1d1d;margin:0 0 12px;">Bu domain için geçerli bir lisans bulunamadı veya süresi dolmuş.</p>
                <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-danger" style="font-size:13px;">
                    <i class="bi bi-flag"></i> Korsanligi Bildir
                </a>
            </div>
        </div>

        <div style="text-align:center;margin-top:14px;font-size:12px;color:#94a3b8;">
            <i class="bi bi-info-circle"></i> Bu sayfa lisans doğrulama için kullanılır, satın alma için <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" style="color:#2E3B4E;font-weight:700;">iletisime gecin</a>.
        </div>
    </div>
</section>

<script>
window.cdgLicenseCheck = function() {
    var btn = document.getElementById('cdg-license-submit-btn');
    var domain = document.getElementById('cdg-license-domain').value.trim();
    if(!domain) {
        if(typeof alert_error === 'function') alert_error('Lutfen bir domain girin', {timer: 3000});
        return;
    }

    // Sonuçları gizle
    document.getElementById('cdg-license-result-ok').style.display = 'none';
    document.getElementById('cdg-license-result-fail').style.display = 'none';

    if(typeof MioAjax !== 'function') {
        // Fallback: form submit
        document.getElementById('cdg-license-form').submit();
        return;
    }

    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Sorgulaniyor...';

    var formData = new FormData(document.getElementById('cdg-license-form'));
    var data = {};
    formData.forEach(function(v, k){ data[k] = v; });

    MioAjax({
        url: '<?php echo htmlspecialchars($controller_link, ENT_QUOTES); ?>',
        type: 'post',
        data: data,
        result: function(r) {
            btn.disabled = false; btn.innerHTML = orig;
            if(r && r.status === 'successful') {
                document.getElementById('cdg-license-result-ok').style.display = 'block';
            } else {
                document.getElementById('cdg-license-result-fail').style.display = 'block';
                if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        }
    });
};
</script>
