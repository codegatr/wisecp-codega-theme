<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$master_content_none = true;
$connectionButtons   = class_exists('Hook') ? Hook::run("ClientAreaConnectionButtons","login") : [];

if(class_exists('Config') && Config::get("options/crtacwshop")) $sign_up = false;
?><!DOCTYPE html>
<html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php
        $contact_link = class_exists('Controllers') ? Controllers::$init->CRLink("contact") : '/contact';
        $hoptions = [
            'page' => "sign-in",
            'jquery.countdown',
        ];
        include __DIR__.DS."inc".DS."main-head.php";
    ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#Signin_Form input:first").focus();
            $("#Signin_Form").bind("keypress", function(e) {
                if(e.keyCode == 13) $("#Signin_Form .mio-ajax-submit").click();
            });
            $("#Signforget_Form").bind("keypress", function(e){
                if (e.keyCode == 13) $("#Signforget_Form .mio-ajax-submit").click();
            });
        });
    </script>
</head>
<body id="cdg-auth">

<!-- Two-factor verification modal -->
<div id="two-factor-verification" style="display: none;">
    <script type="text/javascript">
        $(document).ready(function(){
            $("#TwoFactorForm").bind("keypress", function(e) { if(e.keyCode == 13) $("#btn_check").click(); });
            $("#btn_check").click(function(){
                $("#TwoFactorForm input[name=action]").val("two-factor-check");
                MioAjaxElement(this,{ waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>", form: $("#TwoFactorForm"), result:"signin_submit" });
            });
            $("#btn_resend").click(function(){
                $("#TwoFactorForm input[name=action]").val("two-factor-resend");
                MioAjaxElement(this,{ waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>", form: $("#TwoFactorForm"), result:"signin_submit" });
            });
        });
    </script>
    <div class="cdg-auth-modal-content">
        <h1><i class="bi bi-shield-check"></i><br><?php echo __("website/sign/security-check"); ?></h1>
        <p><?php echo __("website/sign/security-check-text1"); ?></p>
        <p><?php echo __("website/sign/security-check-text2"); ?><br><strong id="two_factor_phone">*********0000</strong></p>
        <form action="<?php echo $login_link;?>" method="post" id="TwoFactorForm">
            <?php echo Validation::get_csrf_token('sign'); ?>
            <div class="cdg-form-group">
                <input type="text" name="code" placeholder="<?php echo __("website/sign/security-check-text3"); ?>" class="cdg-form-control">
            </div>
            <div class="cdg-countdown"><i class="bi bi-clock"></i> <span id="countdown1">00:00</span></div>
            <input type="hidden" name="action" value="two-factor-check">
        </form>
        <div class="cdg-auth-modal-actions">
            <a class="cdg-btn cdg-btn-primary" id="btn_check" href="javascript:void 0;"><?php echo __("website/sign/security-check-text4"); ?></a>
            <a class="cdg-btn cdg-btn-outline" id="btn_resend" href="javascript:void 0;" style="display: none;"><?php echo __("website/sign/security-check-text5"); ?></a>
        </div>
    </div>
</div>

<!-- Location verification modal -->
<div id="location-verification" style="display: none;">
    <script type="text/javascript">
        $(document).ready(function(){
            $("#Location_Verification_Form").bind("keypress", function(e) { if (e.keyCode == 13) $("#btn_continue").click(); });
            $("#btn_continue").click(function(){
                if($("#Location_Verification_Form #method_selections").css("display") == "block")
                    $("#Location_Verification_Form input[name=apply]").val("selection");
                else
                    $("#Location_Verification_Form input[name=apply]").val("check");
                MioAjaxElement(this,{ waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>", form: $("#Location_Verification_Form"), result:"signin_submit" });
            });
            $("#btn_resend2").click(function(){
                $("#Location_Verification_Form input[name=apply]").val("resend");
                MioAjaxElement(this,{ waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>", form: $("#Location_Verification_Form"), result:"signin_submit" });
            });
        });
    </script>
    <div class="cdg-auth-modal-content">
        <h1><i class="bi bi-lock"></i><br><?php echo __("website/sign/security-check"); ?></h1>
        <p><?php echo __("website/sign/security-check-text7"); ?></p>
        <p><?php echo __("website/sign/security-check-text8"); ?></p>
        <form action="<?php echo $login_link; ?>" method="post" id="Location_Verification_Form">
            <?php echo Validation::get_csrf_token('sign'); ?>
            <div id="method_selections" style="display: none;">
                <label class="cdg-radio">
                    <input id="method_security_question" name="selected_method" value="security_question" type="radio">
                    <span><?php echo __("website/sign/security-check-text9"); ?></span>
                </label>
                <label class="cdg-radio">
                    <input id="method_phone" name="selected_method" value="phone" type="radio">
                    <span><?php echo __("website/sign/security-check-text10"); ?></span>
                </label>
            </div>
            <div id="method_security_question_con" style="display: none;">
                <p><strong id="security_question_text">*****?</strong></p>
                <input type="text" name="security_question_answer" placeholder="<?php echo __("website/sign/security-check-text11"); ?>" class="cdg-form-control">
            </div>
            <div id="method_phone_con" style="display: none;">
                <p><?php echo __("website/sign/security-check-text2"); ?><br><strong id="phone_text">*********0000</strong></p>
                <input type="text" name="code" placeholder="<?php echo __("website/sign/security-check-text3"); ?>" class="cdg-form-control">
                <div class="cdg-countdown"><i class="bi bi-clock"></i> <span id="countdown2">00:00</span></div>
            </div>
            <input type="hidden" name="action" value="location-verification">
            <input type="hidden" name="apply" value="selection">
        </form>
        <div class="cdg-auth-modal-actions">
            <a class="cdg-btn cdg-btn-primary" id="btn_continue" href="javascript:void 0;"><?php echo __("website/sign/security-check-text4"); ?></a>
            <a class="cdg-btn cdg-btn-outline" id="btn_resend2" href="javascript:void 0;" style="display: none;"><?php echo __("website/sign/security-check-text5"); ?></a>
        </div>
    </div>
</div>

<?php include __DIR__.DS."inc".DS."lang-currency-modal.php"; ?>
<?php
    // main-header'i include et eğer varsa
    $header_type = isset($theme_settings['header_type']) ? $theme_settings['header_type'] : 1;
    $hf = __DIR__.DS."inc".DS."main-header-".$header_type.".php";
    if(file_exists($hf)) include $hf;
    elseif(file_exists(__DIR__.DS."inc".DS."main-header.php")) include __DIR__.DS."inc".DS."main-header.php";
    elseif(file_exists(__DIR__.DS."inc".DS."main-header-1.php")) include __DIR__.DS."inc".DS."main-header-1.php";
?>

<!-- AUTH SAYFA -->
<section class="cdg-royal-section cdg-royal-auth">
    <div class="cdg-royal-bg">
        <div class="cdg-royal-bg-gradient"></div>
        <div class="cdg-royal-bg-pattern"></div>
        <div class="cdg-royal-orb cdg-royal-orb-1"></div>
        <div class="cdg-royal-orb cdg-royal-orb-2"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-royal-grid">

            <!-- SOL: KRALIYET PROMO -->
            <div class="cdg-royal-promo">
                <div class="cdg-royal-crown-wrap">
                    <div class="cdg-royal-crown">
                        <svg viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#fde047"/>
                                    <stop offset="50%" style="stop-color:#facc15"/>
                                    <stop offset="100%" style="stop-color:#ca8a04"/>
                                </linearGradient>
                            </defs>
                            <path d="M10,50 L15,20 L30,35 L50,10 L70,35 L85,20 L90,50 Z" fill="url(#goldGrad)" stroke="#854d0e" stroke-width="1"/>
                            <circle cx="15" cy="20" r="4" fill="#dc2626" stroke="#7f1d1d" stroke-width="0.5"/>
                            <circle cx="50" cy="10" r="5" fill="#dc2626" stroke="#7f1d1d" stroke-width="0.5"/>
                            <circle cx="85" cy="20" r="4" fill="#dc2626" stroke="#7f1d1d" stroke-width="0.5"/>
                            <rect x="10" y="50" width="80" height="6" fill="url(#goldGrad)" stroke="#854d0e" stroke-width="0.5"/>
                        </svg>
                    </div>
                    <div class="cdg-royal-stars">
                        <span>★</span><span>★</span><span>★</span>
                    </div>
                </div>
                <div class="cdg-royal-eyebrow">
                    <i class="bi bi-gem"></i>
                    <span>CODEGA Kraliyet Sarayı</span>
                </div>
                <h1>Sarayınıza <span class="cdg-text-gold">hoş geldiniz</span>, Majesteleri</h1>
                <p>Tüm hizmetlerinizin krallığı tek panelden yönetilir. Hosting, domain, fatura ve destek — hepsi parmaklarınızın ucunda.</p>
                <ul class="cdg-royal-features">
                    <li><i class="bi bi-stars"></i> Kraliyet seviyesinde güvenlik</li>
                    <li><i class="bi bi-stars"></i> İki adımlı doğrulama desteği</li>
                    <li><i class="bi bi-stars"></i> Anlık fatura ve hizmet kontrolü</li>
                    <li><i class="bi bi-stars"></i> 7/24 sadık destek ekibi</li>
                </ul>
                <?php if(isset($sign_up) && $sign_up): ?>
                <div class="cdg-royal-cta">
                    <p>Henüz tebamız değil mişiniz?</p>
                    <a href="<?php echo $register_link; ?>" class="cdg-btn cdg-btn-gold">
                        <i class="bi bi-person-plus-fill"></i> Kraliyet Ailesine Katıl
                    </a>
                </div>
                <?php endif; ?>
                <div class="cdg-royal-seal">
                    <i class="bi bi-shield-fill-check"></i>
                    <span>SSL Korumalı · KVKK Uyumlu</span>
                </div>
            </div>

            <!-- SAĞ: Form -->
            <div class="cdg-auth-form-wrap">
                <div class="cdg-auth-card">

                    <?php if(!Filter::GET("open") || Filter::GET("open") == "login"): ?>
                    <!-- LOGIN FORMU -->
                    <form action="<?php echo $login_link;?>" method="POST" class="mio-ajax-form" id="Signin_Form">
                        <?php echo Validation::get_csrf_token('sign'); ?>

                        <div class="cdg-auth-card-head">
                            <h2><?php echo __("website/sign/in"); ?></h2>
                            <p>Hesabınıza giriş yaparak hizmetlerinizi yönetin</p>
                        </div>

                        <?php if($connectionButtons): ?>
                        <div class="cdg-social-connect">
                            <?php foreach($connectionButtons AS $button) echo $button; ?>
                        </div>
                        <div class="cdg-auth-divider"><span>veya</span></div>
                        <?php endif; ?>

                        <div class="cdg-form-group">
                            <label class="cdg-form-label"><?php echo __("website/sign/in-form-email"); ?></label>
                            <div class="cdg-input-icon">
                                <i class="bi bi-envelope"></i>
                                <input name="email" type="text" placeholder="ornek@firma.com" class="cdg-form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="cdg-form-group">
                            <div class="cdg-form-label-row">
                                <label class="cdg-form-label"><?php echo __("website/sign/in-form-password"); ?></label>
                                <a class="cdg-form-link" href="javascript:void(0);" onclick="forget_password();"><?php echo __("website/sign/in-form-forget"); ?></a>
                            </div>
                            <div class="cdg-input-icon">
                                <i class="bi bi-lock"></i>
                                <input name="password" type="password" placeholder="••••••••" class="cdg-form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="cdg-form-group">
                            <label class="cdg-checkbox">
                                <input id="checkbox-4" name="remember" value="1" type="checkbox">
                                <span><?php echo __("website/sign/in-form-remember"); ?></span>
                            </label>
                        </div>

                        <?php if(isset($captcha_sign_in) && $captcha_sign_in): ?>
                        <div class="cdg-form-group cdg-captcha">
                            <?php echo $captcha_sign_in->getOutput(); ?>
                            <?php if($captcha_sign_in->input): ?>
                                <input class="cdg-form-control" name="<?php echo $captcha_sign_in->input_name; ?>" type="text" placeholder="<?php echo ___("needs/form-captcha-label"); ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <button class="cdg-btn cdg-btn-primary cdg-btn-block mio-ajax-submit" type="button" mio-ajax-options='{"waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","result":"signin_submit"}'>
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span><?php echo __("website/sign/in-form-submit"); ?></span>
                        </button>
                    </form>
                    <?php endif; ?>

                    <!-- ŞİFREMİ UNUTTUM FORMU -->
                    <?php if(((!isset($captcha_sign_forget) || !isset($captcha_sign_in)) && !Filter::GET("open")) || Filter::GET("open") == "forget"): ?>
                    <form action="<?php echo $forget_password_link;?>" method="POST" class="mio-ajax-form" id="Signforget_Form"<?php echo Filter::GET("open") == "forget" ? '' : ' style="display: none;"'; ?>>
                        <?php echo Validation::get_csrf_token('sign'); ?>

                        <div class="cdg-auth-card-head">
                            <h2><?php echo __("website/sign/forget-form-title"); ?></h2>
                            <p>Sifrenizi sifirlamak icin e-posta adresinizi girin</p>
                        </div>

                        <div class="cdg-form-group">
                            <label class="cdg-form-label"><?php echo __("website/sign/forget-form-email"); ?></label>
                            <div class="cdg-input-icon">
                                <i class="bi bi-envelope"></i>
                                <input name="email" type="text" placeholder="ornek@firma.com" class="cdg-form-control">
                            </div>
                        </div>

                        <?php if(isset($captcha_sign_forget) && $captcha_sign_forget): ?>
                        <div class="cdg-form-group cdg-captcha">
                            <?php echo $captcha_sign_forget->getOutput(); ?>
                            <?php if($captcha_sign_forget->input): ?>
                                <input class="cdg-form-control" name="<?php echo $captcha_sign_forget->input_name; ?>" type="text" placeholder="<?php echo ___("needs/form-captcha-label"); ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <button class="cdg-btn cdg-btn-primary cdg-btn-block mio-ajax-submit" type="button" mio-ajax-options='{"waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","result":"forget_submit"}'>
                            <i class="bi bi-envelope-arrow-up"></i>
                            <span><?php echo __("website/sign/forget-form-submit"); ?></span>
                        </button>

                        <div class="cdg-form-group" style="text-align:center;margin-top:14px;">
                            <a href="javascript:void(0);" onclick="login();" class="cdg-form-link">
                                <i class="bi bi-arrow-left"></i> <?php echo __("website/sign/forget-form-login"); ?>
                            </a>
                        </div>

                        <div class="error" id="Signforget_Form_output" style="display:none;text-align:center;color:#ef4444;margin-top:10px;font-weight:600;"></div>
                    </form>

                    <!-- Forget success div -->
                    <div id="forget_success" style="display:none;text-align:center;padding:30px 0;">
                        <i class="bi bi-check-circle" style="font-size:64px;color:#10b981;"></i>
                        <h3 style="margin:16px 0 8px;font-weight:700;"><?php echo __("website/sign/forget-success-title"); ?></h3>
                        <p style="color:#64748b;"><?php echo __("website/sign/forget-success-content"); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Login success div -->
                    <div id="Success_Div" style="display:none;text-align:center;padding:30px 0;">
                        <i class="bi bi-check-circle-fill" style="font-size:64px;color:#10b981;"></i>
                        <h3 style="margin:16px 0 8px;font-weight:700;"><?php echo __("website/sign/in-success-title"); ?></h3>
                        <p style="color:#64748b;"><?php echo __("website/sign/in-success-content"); ?></p>
                    </div>

                    <script type="text/javascript">
                        function forget_password() {
                            <?php if(isset($captcha_sign_in) && isset($captcha_sign_forget)): ?>
                            window.location.href = "<?php echo $login_link; ?>?open=forget";
                            <?php else: ?>
                            $("#Signin_Form").fadeOut(100, function () { $("#Signforget_Form").fadeIn(100); });
                            <?php endif; ?>
                        }
                        function login() {
                            <?php if(isset($captcha_sign_in) && isset($captcha_sign_forget)): ?>
                            window.location.href = "<?php echo $login_link; ?>?open=login";
                            <?php else: ?>
                            $("#Signforget_Form").fadeOut(100, function () { $("#Signin_Form").fadeIn(100); });
                            <?php endif; ?>
                        }
                        function signin_submit(result) {
                            <?php if(isset($captcha_sign_in)) echo $captcha_sign_in->submit_after_js(); ?>
                            if(result != ''){
                                var solve = getJson(result);
                                if(solve !== false && solve != undefined && typeof(solve) == "object"){
                                    if(solve.status == "error"){
                                        if(solve.js != undefined && solve.js != '') eval(solve.js);
                                        if(solve.for != undefined && solve.for != ''){
                                            $("#Signin_Form "+solve.for).focus();
                                            $("#Signin_Form "+solve.for).attr("style","border-color:#ef4444!important;");
                                            $("#Signin_Form "+solve.for).change(function(){ $(this).removeAttr("style"); });
                                        }
                                        if(solve.message != undefined && solve.message != '') alert_error(solve.message,5000);
                                    }
                                    else if(solve.status === "two-factor"){
                                        if($("#two-factor-verification").css("display") !== "block") open_modal("two-factor-verification");
                                        $('#two-factor-verification #countdown1').countdown(solve.expire)
                                            .on('update.countdown', function(event){ $(this).html(event.strftime('%M:%S')); })
                                            .on('finish.countdown', function(event){ $(this).html(event.strftime('%M:%S')); $("#two-factor-verification #btn_resend").fadeIn(500); });
                                        $("#two-factor-verification #two_factor_phone").html(solve.phone);
                                        $("#two-factor-verification #btn_resend").fadeOut(500);
                                    }
                                    else if(solve.status === "location-verification"){
                                        if($("#location-verification").css("display") !== "block") open_modal("location-verification");
                                        var s_method = solve.selected_method;
                                        $("#method_selections,#method_phone_con,#method_security_question_con").css("display","none");
                                        if(s_method === false) $("#method_selections").css("display","block");
                                        else if(s_method === "phone"){
                                            $("#method_phone_con").css("display","block");
                                            $('#location-verification #countdown2').countdown(solve.expire)
                                                .on('update.countdown', function(event){ $(this).html(event.strftime('%M:%S')); })
                                                .on('finish.countdown', function(event){ $(this).html(event.strftime('%M:%S')); $("#location-verification #btn_resend2").fadeIn(500); });
                                            $("#location-verification #phone_text").html(solve.phone);
                                            $("#location-verification #btn_resend2").fadeOut(500);
                                        } else if(s_method == "security_question"){
                                            $("#method_security_question_con").css("display","block");
                                            $("#location-verification #security_question_text").html(solve.security_question);
                                        }
                                    }
                                    else if(solve.status == "successful"){ window.location.href = solve.redirect; }
                                }
                            }
                        }
                        function forget_submit(result) {
                            <?php if(isset($captcha_sign_forget)) echo $captcha_sign_forget->submit_after_js(); ?>
                            if(result != ''){
                                var solve = getJson(result);
                                if(solve !== false && solve != undefined && typeof(solve) == "object"){
                                    if(solve.status == "error"){
                                        if(solve.js != undefined && solve.js != '') eval(solve.js);
                                        if(solve.for != undefined && solve.for != ''){
                                            $("#Signforget_Form "+solve.for).focus();
                                            $("#Signforget_Form "+solve.for).attr("style","border-color:#ef4444!important;");
                                            $("#Signforget_Form "+solve.for).change(function(){ $(this).removeAttr("style"); });
                                        }
                                        if(solve.message != undefined && solve.message != '') alert_error(solve.message,{timer:4000});
                                    } else if(solve.status == "sent"){
                                        $("#Signforget_Form").fadeOut(750, function() { $("#forget_success").fadeIn(750); });
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
                // Sonuc handler'ini cagir (signin_submit / forget_submit / signup_submit)
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
