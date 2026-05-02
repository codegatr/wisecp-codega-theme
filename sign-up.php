<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$master_content_none = true;
$connectionButtons   = class_exists('Hook') ? Hook::run("ClientAreaConnectionButtons","register") : [];
?><!DOCTYPE html>
<html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php
        $hoptions = [
            'page' => "sign-in",
            'intlTelInput',
            'voucher_codes',
        ];
        include __DIR__.DS."inc".DS."main-head.php";
    ?>
    <script type="text/javascript">
        var countryCode;
        $(document).ready(function(){
            countryCode = '<?php if(class_exists('UserManager') && $ipInfo = UserManager::ip_info()) echo $ipInfo["countryCode"]; else echo 'tr'; ?>';
            if($('select[name=country]').length) {
                $('select[name=country] option').prop('selected',false);
                $("select[name=country] option[data-code='"+(countryCode.toUpperCase())+"']").prop('selected',true).parent().trigger('change');
            }
        });
    </script>

    <?php if(isset($kind_status) && $kind_status): ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $("input[name='kind']").change(function(){
                var id = $(this).attr("id");
                $(".kind-content").fadeOut(100,function () { $("."+id).fadeIn(100); });
            });
            $("input[name='kind']:checked").each(function () {
                var id = $(this).attr("id");
                $(".kind-content").fadeOut(100,function () { $("."+id).fadeIn(100); });
            });
        });
    </script>
    <?php endif; ?>
</head>
<body id="cdg-auth">

<?php include __DIR__.DS."inc".DS."lang-currency-modal.php"; ?>
<?php
    $header_type = isset($theme_settings['header_type']) ? $theme_settings['header_type'] : 1;
    $hf = __DIR__.DS."inc".DS."main-header-".$header_type.".php";
    if(file_exists($hf)) include $hf;
    elseif(file_exists(__DIR__.DS."inc".DS."main-header.php")) include __DIR__.DS."inc".DS."main-header.php";
    elseif(file_exists(__DIR__.DS."inc".DS."main-header-1.php")) include __DIR__.DS."inc".DS."main-header-1.php";
?>

<section class="cdg-auth-section cdg-auth-premium">
    <div class="cdg-auth-bg">
        <div class="cdg-auth-mesh"></div>
        <div class="cdg-auth-grid-pattern"></div>
        <div class="cdg-auth-glow cdg-auth-glow-1"></div>
        <div class="cdg-auth-glow cdg-auth-glow-2"></div>
        <div class="cdg-auth-particles">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span>
        </div>
    </div>
    <div class="cdg-container">
        <div class="cdg-auth-grid">

            <div class="cdg-auth-promo cdg-auth-promo-v2">
                <!-- Animated background -->
                <div class="cdg-auth-aurora">
                    <div class="cdg-aurora-blob cdg-aurora-blob-1"></div>
                    <div class="cdg-aurora-blob cdg-aurora-blob-2"></div>
                    <div class="cdg-aurora-blob cdg-aurora-blob-3"></div>
                </div>

                <!-- Brand mark -->
                <div class="cdg-auth-brand">
                    <div class="cdg-auth-brand-logo">
                        <span>C</span>
                    </div>
                    <div class="cdg-auth-brand-text">
                        <strong>CODEGA</strong>
                        <small>Profesyonel Hizmet Platformu</small>
                    </div>
                </div>

                <!-- Hero metni -->
                <div class="cdg-auth-hero-text">
                    <div class="cdg-auth-pill">
                        <span class="cdg-auth-pill-dot"></span>
                        <span>30 saniyede üyelik · 30 gün para iade garantisi</span>
                    </div>
                    <h1>Profesyonel <span class="cdg-text-gradient-light">hosting deneyimi</span> sizi bekliyor</h1>
                    <p class="cdg-auth-lead">Hesap oluşturarak hosting paketlerine, ücretsiz domain araçlarına ve öncelikli desteğe anında erişim kazanın.</p>
                </div>

                <!-- Avantajlar (büyük ikonlar) -->
                <div class="cdg-auth-benefits">
                    <div class="cdg-auth-benefit">
                        <div class="cdg-auth-benefit-icon" style="background:linear-gradient(135deg,#10b981,#34d399);">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <strong>Anında Aktivasyon</strong>
                            <small>Üyelik sonrası dakikalar içinde hizmetler aktif olur.</small>
                        </div>
                    </div>
                    <div class="cdg-auth-benefit">
                        <div class="cdg-auth-benefit-icon" style="background:linear-gradient(135deg,#facc15,#f59e0b);">
                            <i class="bi bi-piggy-bank-fill"></i>
                        </div>
                        <div>
                            <strong>30 Gün İade Garantisi</strong>
                            <small>Memnun kalmazsanız koşulsuz tam iade imkanı.</small>
                        </div>
                    </div>
                    <div class="cdg-auth-benefit">
                        <div class="cdg-auth-benefit-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <div>
                            <strong>Yedekli Altyapı</strong>
                            <small>Tier-3 veri merkezi, %99.99 uptime SLA garantisi.</small>
                        </div>
                    </div>
                    <div class="cdg-auth-benefit">
                        <div class="cdg-auth-benefit-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee);">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div>
                            <strong>7/24 Öncelikli Destek</strong>
                            <small>WhatsApp, telefon ve panel — her zaman ulaşılabilir.</small>
                        </div>
                    </div>
                </div>

                <!-- Trust + CTA -->
                <div class="cdg-auth-bottom">
                    <div class="cdg-auth-cta-card">
                        <div>
                            <small>Zaten hesabınız var mı?</small>
                            <strong>Anında giriş yapın</strong>
                        </div>
                        <a href="<?php echo $login_link; ?>" class="cdg-auth-cta-btn">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Giriş Yap</span>
                        </a>
                    </div>

                    <div class="cdg-auth-trust-row">
                        <div class="cdg-auth-trust-item">
                            <i class="bi bi-shield-fill-check"></i>
                            <span>SSL Korumalı</span>
                        </div>
                        <div class="cdg-auth-trust-item">
                            <i class="bi bi-file-earmark-lock-fill"></i>
                            <span>KVKK Uyumlu</span>
                        </div>
                        <div class="cdg-auth-trust-item">
                            <i class="bi bi-award-fill"></i>
                            <span>ISO 27001</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cdg-auth-form-wrap">
                <div class="cdg-auth-card cdg-auth-card-wide">

                    <form action="<?php echo $register_link;?>" method="POST" class="mio-ajax-form" id="Signup_Form">
                        <?php echo Validation::get_csrf_token('sign'); ?>
                        <input type="hidden" name="stage" value="1">

                        <div class="cdg-auth-card-head">
                            <h2><?php echo __("website/sign/up"); ?></h2>
                            <p>Bilgilerinizi girerek hesabınızı oluşturun</p>
                        </div>

                        <?php if($connectionButtons): ?>
                        <div class="cdg-social-connect">
                            <?php foreach($connectionButtons AS $button) echo $button; ?>
                        </div>
                        <div class="cdg-auth-divider"><span>veya</span></div>
                        <?php endif; ?>

                        <?php if(isset($kind_status) && $kind_status): ?>
                        <div class="cdg-form-group">
                            <label class="cdg-form-label">Hesap Tipi</label>
                            <div class="cdg-radio-group">
                                <label class="cdg-radio-card">
                                    <input id="kind_1" name="kind" value="individual" type="radio" checked>
                                    <i class="bi bi-person"></i>
                                    <span><?php echo __("website/sign/up-form-kind-1"); ?></span>
                                </label>
                                <label class="cdg-radio-card">
                                    <input id="kind_2" name="kind" value="corporate" type="radio">
                                    <i class="bi bi-building"></i>
                                    <span><?php echo __("website/sign/up-form-kind-2"); ?></span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="cdg-form-row">
                            <div class="cdg-form-group">
                                <label class="cdg-form-label"><?php echo __("website/sign/up-form-name"); ?></label>
                                <input name="name" type="text" class="cdg-form-control" placeholder="Adiniz" required>
                            </div>
                            <div class="cdg-form-group">
                                <label class="cdg-form-label"><?php echo __("website/sign/up-form-surname"); ?></label>
                                <input name="surname" type="text" class="cdg-form-control" placeholder="Soyadiniz" required>
                            </div>
                        </div>

                        <div class="cdg-form-group">
                            <label class="cdg-form-label">E-posta</label>
                            <div class="cdg-input-icon">
                                <i class="bi bi-envelope"></i>
                                <input name="email" type="email" class="cdg-form-control" placeholder="ornek@firma.com" required>
                            </div>
                        </div>

                        <?php if(isset($kind_status) && $kind_status): ?>
                        <div class="kind-content kind_2" style="display:none;">
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Şirket Adi</label>
                                <input name="company_name" type="text" class="cdg-form-control" placeholder="Şirket Adi">
                            </div>
                            <div class="cdg-form-row">
                                <div class="cdg-form-group">
                                    <label class="cdg-form-label">Vergi Dairesi</label>
                                    <input name="company_tax_office" type="text" class="cdg-form-control">
                                </div>
                                <div class="cdg-form-group">
                                    <label class="cdg-form-label">Vergi Numarasi</label>
                                    <input name="company_tax_number" type="text" class="cdg-form-control">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="cdg-form-row">
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Şifre</label>
                                <div class="cdg-input-icon">
                                    <i class="bi bi-lock"></i>
                                    <input name="password" type="password" id="password_primary" class="cdg-form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Şifre (Tekrar)</label>
                                <div class="cdg-input-icon">
                                    <i class="bi bi-lock-fill"></i>
                                    <input name="password_again" type="password" class="cdg-form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <?php if(isset($countryList) && is_array($countryList)): ?>
                        <div class="cdg-form-row">
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Ulke</label>
                                <select name="country" class="cdg-form-control" onchange="if(typeof getCities=='function') getCities(this.options[this.selectedIndex].value);">
                                    <?php foreach($countryList as $country): ?>
                                        <option value="<?php echo $country['id'] ?? $country['code'] ?? ''; ?>" data-code="<?php echo $country['code'] ?? ''; ?>"><?php echo $country['name'] ?? ''; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Sehir</label>
                                <input type="text" name="city" class="cdg-form-control" placeholder="Sehir">
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="cdg-form-row">
                            <div class="cdg-form-group">
                                <label class="cdg-form-label">Posta Kodu</label>
                                <input name="zipcode" type="text" class="cdg-form-control">
                            </div>
                            <div class="cdg-form-group" style="flex:2;">
                                <label class="cdg-form-label">Adres</label>
                                <input name="address" type="text" class="cdg-form-control">
                            </div>
                        </div>

                        <?php if(isset($captcha_sign_up) && $captcha_sign_up): ?>
                        <div class="cdg-form-group cdg-captcha">
                            <?php echo $captcha_sign_up->getOutput(); ?>
                            <?php if($captcha_sign_up->input): ?>
                                <input class="cdg-form-control" name="<?php echo $captcha_sign_up->input_name; ?>" type="text" placeholder="<?php echo ___("needs/form-captcha-label"); ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="cdg-form-group">
                            <label class="cdg-checkbox">
                                <input id="checkbox-5" name="contract" value="1" type="checkbox" required>
                                <span><?php echo __("website/sign/up-form-contract"); ?></span>
                            </label>
                        </div>

                        <button class="cdg-btn cdg-btn-primary cdg-btn-block mio-ajax-submit" type="button" mio-ajax-options='{"waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","result":"signup_submit"}'>
                            <i class="bi bi-person-plus"></i>
                            <span><?php echo __("website/sign/up-form-submit"); ?></span>
                        </button>
                    </form>

                    <div id="Success_div" style="display:none;text-align:center;padding:30px 0;">
                        <i class="bi bi-check-circle-fill" style="font-size:64px;color:#10b981;"></i>
                        <h3 style="margin:16px 0 8px;font-weight:700;"><?php echo __("website/sign/up-success-title"); ?></h3>
                        <p style="color:#64748b;"><?php echo __("website/sign/up-success-content"); ?></p>
                    </div>

                    <script type="text/javascript">
                        function signup_submit(result) {
                            <?php if(isset($captcha_sign_up)) echo $captcha_sign_up->submit_after_js(); ?>
                            if(result != ''){
                                var solve = getJson(result);
                                if(solve !== false && solve != undefined && typeof(solve) == "object"){
                                    if(solve.status == "error"){
                                        if(solve.js != undefined && solve.js != '') eval(solve.js);
                                        if(solve.for != undefined && solve.for != ''){
                                            $("#Signup_Form "+solve.for).focus();
                                            $("#Signup_Form "+solve.for).attr("style","border-color:#ef4444!important;");
                                            $("#Signup_Form "+solve.for).change(function(){ $(this).removeAttr("style"); });
                                        }
                                        if(solve.message != undefined && solve.message != '') alert_error(solve.message,5000);
                                    } else if(solve.status == "successful"){
                                        $("#Signup_Form").slideUp(500,function(){
                                            $("#Success_div").slideDown(500);
                                            if(solve.redirect != undefined) setTimeout(function(){ window.location.href = solve.redirect; }, 2500);
                                        });
                                    }
                                }
                            }
                        }
                    </script>

                </div>
            </div>

        </div>
    </div>
</section>

<?php
    $ff = __DIR__.DS."inc".DS."main-footer.php";
    if(file_exists($ff)) include $ff;
    $sf = __DIR__.DS."inc".DS."sign-footer.php";
    if(file_exists($sf)) include $sf;
?>

<!-- MIO-AJAX FALLBACK: WiseCP global JS yuklenmemisse manuel submit -->
<script type="text/javascript">
$(document).ready(function(){
    // mio-ajax-submit butonlarina click handler (event delegation)
    $(document).on('click', '.mio-ajax-submit', function(e){
        e.preventDefault();
        var btn = $(this);
        if(btn.prop('disabled')) return false;

        var optsStr = btn.attr('mio-ajax-options') || '{}';
        var opts = {};
        try { opts = JSON.parse(optsStr); } catch(err) { console.error('mio-ajax-options parse:', err); }

        var form = btn.closest('form');
        if(form.length === 0){ console.error('Form bulunamadi'); return false; }

        // 1) MioAjaxElement varsa orijinal yontemi kullan
        if(typeof MioAjaxElement === 'function'){
            try {
                MioAjaxElement(btn[0], $.extend({form: form}, opts));
                return false;
            } catch(err) { console.warn('MioAjaxElement hata, fallback kullaniliyor:', err); }
        }

        // 2) Manuel jQuery AJAX fallback
        var origHtml = btn.html();
        btn.prop('disabled', true).html((opts.waiting_text || 'Bekleyiniz...') + ' <i class="bi bi-three-dots"></i>');

        $.ajax({
            url: form.attr('action') || window.location.href,
            method: form.attr('method') || 'POST',
            data: form.serialize(),
            dataType: 'text',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(result){
                btn.prop('disabled', false).html(origHtml);
                // Sonuç handler'ini cagir (signin_submit / forget_submit / signup_submit)
                if(opts.result && typeof window[opts.result] === 'function'){
                    window[opts.result](result);
                } else {
                    // Default: JSON parse + redirect
                    try {
                        var data = JSON.parse(result);
                        if(data.status === 'successful' && data.redirect){ window.location.href = data.redirect; }
                        else if(data.status === 'error' && data.message){ alert(data.message); }
                    } catch(err) { console.log('Response:', result); }
                }
            },
            error: function(xhr, status, err){
                btn.prop('disabled', false).html(origHtml);
                console.error('AJAX hata:', status, err, xhr.responseText);
                alert('Baglanti hatasi: ' + (xhr.responseText || err || 'Bilinmeyen hata'));
            }
        });

        return false;
    });

    // getJson helper (Classic tema bekler)
    if(typeof window.getJson !== 'function'){
        window.getJson = function(s){ try { return JSON.parse(s); } catch(e){ return false; } };
    }
    // alert_error fallback
    if(typeof window.alert_error !== 'function'){
        window.alert_error = function(msg, t){ alert(msg); };
    }
    // open_modal fallback
    if(typeof window.open_modal !== 'function'){
        window.open_modal = function(id){ $('#'+id).fadeIn(200).css('display','flex'); };
    }
});
</script>
</body>
</html>
