<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "order-steps-sms",
        'jquery-ui',
        'ion.rangeSlider',
        'intlTelInput',
    ];
?>
<style>
/* Order Steps Codega Override CSS */
#wrapper { background:#f8fafc; padding:32px 0 60px; min-height:60vh; }
#wrapper .pakettitle { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:24px 28px; margin-bottom:20px; }
#wrapper .pakettitle h1 { color:#0f172a; font-size:24px; font-weight:800; margin:0 0 6px; letter-spacing:-0.5px; }
#wrapper .pakettitle h2 { color:#64748b; font-size:14px; font-weight:500; margin:0; }
#wrapper .pakettitle .line { display:none; }

#wrapper .siparisbilgileri { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:28px; margin-bottom:20px; }

#wrapper .ilanasamalar { display:flex; gap:16px; margin-bottom:24px; flex-wrap:wrap; justify-content:center; }
#wrapper .ilanasamalar .ilanasamax { flex:1 1 140px; max-width:200px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px; text-align:center; transition:all 0.2s; position:relative; }
#wrapper .ilanasamalar .ilanasamax h3 { background:linear-gradient(135deg,#2E3B4E,#00D3E5); color:#fff; width:32px; height:32px; border-radius:50%; display:grid; place-items:center; font-size:14px; font-weight:800; margin:0 auto 8px; }
#wrapper .ilanasamalar #asamaaktif { border:2px solid #2E3B4E; box-shadow:0 8px 20px rgba(30,64,175,0.15); }
#wrapper .ilanasamalar #asamaaktif h3 { background:linear-gradient(135deg,#facc15,#f59e0b); color:#1A2332; }
#wrapper .asamaline { display:none; }

#wrapper .orderperiodblock-con { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; }
#wrapper .orderperiodblock { background:#f8fafc; border:2px solid #e2e8f0; border-radius:14px; padding:24px 18px; text-align:center; cursor:pointer; transition:all 0.2s; position:relative; }
#wrapper .orderperiodblock:hover { border-color:#2E3B4E; transform:translateY(-2px); }
#wrapper .orderperiodblock.active { border-color:#2E3B4E; background:linear-gradient(135deg,#fff,#eff6ff); box-shadow:0 8px 24px rgba(30,64,175,0.20); }
#wrapper .orderperiodblock h3 { color:#2E3B4E; font-size:14px; font-weight:700; margin:0 0 8px; text-transform:uppercase; letter-spacing:0.5px; }
#wrapper .orderperiodblock h2 { color:#0f172a; font-size:24px; font-weight:800; margin:0; }
#wrapper .orderperiodblock .periodselectbox { width:24px; height:24px; border:2px solid #cbd5e1; border-radius:50%; margin:12px auto 0; display:grid; place-items:center; transition:all 0.2s; }
#wrapper .orderperiodblock.active .periodselectbox { background:#2E3B4E; border-color:#2E3B4E; }
#wrapper .orderperiodblock.active .periodselectbox i { color:#fff; }
#wrapper .orderperiodblock .periodselectbox i { color:transparent; font-size:12px; }
#wrapper .ribbonperiod { position:absolute; top:-1px; right:-1px; }
#wrapper .ribbonperiod span { display:inline-block; background:linear-gradient(135deg,#10b981,#059669)!important; color:#fff!important; padding:5px 12px; font-size:11px; font-weight:700; border-radius:0 12px 0 12px; }
#wrapper .setup-fee-period { display:block; color:#64748b; font-size:11px; margin-top:6px; font-weight:600; }

#wrapper .btn, #wrapper .gonderbtn { display:inline-flex; align-items:center; gap:8px; padding:13px 26px; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none; transition:all 0.2s; background:linear-gradient(135deg,#2E3B4E,#00D3E5)!important; color:#fff!important; border:0; cursor:pointer; }
#wrapper .btn:hover, #wrapper .gonderbtn:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(30,64,175,0.25); }

#wrapper .alanadisorgu { background:#f8fafc; padding:20px; border-radius:12px; border:1px solid #e2e8f0; }
#wrapper .alanadisorgu input[type="text"] { width:60%; padding:12px 14px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; font-family:inherit; }
#wrapper .alanadisorgu h5 { font-size:13px; color:#64748b; margin-top:14px; font-weight:500; }

#wrapper #accordion h3 { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px 20px; margin-bottom:8px; cursor:pointer; font-size:15px; font-weight:700; color:#0f172a; }
#wrapper #accordion h3.ui-state-active { background:linear-gradient(135deg,#2E3B4E,#00D3E5); color:#fff; }

#wrapper .checkbox-custom-label, #wrapper .radio-custom-label { padding:8px 0; cursor:pointer; font-size:14px; color:#0f172a; }
#wrapper .zorunlu { color:#ef4444; font-weight:700; }

#wrapper .tescilsonuc { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px; margin-top:12px; }
#wrapper .tescilsonuc .error { color:#991b1b; }
#wrapper .tescilsonuc h4 { color:#0f172a; font-size:16px; font-weight:700; }

#wrapper .error#result { padding:12px; background:#fee2e2; border:1px solid #fecaca; border-radius:8px; color:#991b1b; font-weight:600; }
#wrapper .clear { clear:both; }
</style>

<section class="cdg-page-hero" style="padding:40px 0;">
    <div class="cdg-page-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-page-hero-content" style="text-align:center;">
            <div class="cdg-domain-badge"><i class="bi bi-chat-dots-fill"></i> SMS Paket Sipariş Adımları</div>
        </div>
    </div>
</section>

<div class="cdg-container">

<script>
        $( function() {
            $( "#accordion" ).accordion({
                heightStyle: "content"
            });
        });
    </script>

<style>
                    .orderperiodblock.active {
    box-shadow: 0 0 7px var(--color-primary-two);
    border: 2px solid var(--color-primary-two);
}

.active .periodselectbox {
    border: 2px solid var(--color-primary-two);
    background: var(--color-primary-two);
}

.orderperiodblock h3 {
    color: var(--color-primary-two);
}

.ribbonperiod span {
    background: linear-gradient(var(--color-primary-two) 0%, var(--color-primary-two) 100%);
}

.ribbonperiod span::before {
    border-left: 3px solid var(--color-primary-two);
    border-top: 3px solid var(--color-primary-two);
}

.ribbonperiod span::after {
    border-right: 3px solid var(--color-primary-two);
    border-top: 3px solid var(--color-primary-two);
}
</style>

<div id="wrapper" style="margin-top: 30px;">

    <?php if(isset($steps) && sizeof($steps)>0): ?>
        <div class="asamaline"></div>
        <div class="ilanasamalar">
            <?php
                foreach ($steps AS $r=>$s){
                    $rank = $r+1;
                    ?>
                    <div class="ilanasamax"<?php echo $step == $s["id"] ? 'id="asamaaktif"' : ''; ?>><div align="center"><h3><?php echo $rank; ?></h3><div class="clear"></div><?php echo $s["name"]; ?></div></div>
                    <?php
                }
            ?>
        </div>
    <?php endif; ?>

    <?php if($step == 1): ?>
        <div class="pakettitle" style="margin-top:0px;">
            <h1><strong><?php echo __("website/osteps/compulsory-information2"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/compulsory-information-note"); ?></h2>
        </div>



        <div class="siparisbilgileri">

            <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1" enctype="multipart/form-data">
                <?php echo Validation::get_csrf_token('order-steps'); ?>

                <table width="100%" border="0" align="center">
                    <tr>
                        <td colspan="2" bgcolor="#ebebeb"><strong><?php echo __("website/osteps/compulsory-information3"); ?></strong></td>
                    </tr>

                    <tr>
                        <td width="30%">
                            <label for="requirement-">
                                <span class="zorunlu">*</span> <strong>Adınız Soyadınız</strong>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="name">
                        </td>
                    </tr>

                    <tr>
                        <td width="30%">
                            <span class="zorunlu">*</span> <strong>Doğum Tarihi</strong>
                        </td>
                        <td>
                            <script type="text/javascript" src="<?php echo $sadress."assets/plugins/js/i18n/datepicker-tr.js"; ?>"></script>

                            <script type="text/javascript"></script>
                            <script>
                                $(function(){
                                    $( "#birthday" ).datepicker({
                                        yearRange: "-100:+0",
                                        dateFormat:"dd/mm/yy",
                                        changeDay:true,
                                        changeMonth: true,
                                        changeYear: true
                                    });
                                });
                            </script>
                            <input type="text" name="birthday" id="birthday">
                        </td>
                    </tr>

                    <tr>
                        <td width="30%"><span class="zorunlu">*</span> <strong>T.C Kimlik Numarası</strong></td>
                        <td>
                            <input type="text" name="identity" value="" maxlength="11" onkeypress='return event.charCode>= 48 &&event.charCode<= 57'>
                        </td>
                    </tr>


                    <tr>
                        <td style="border:none;" align="center" colspan="2">
                            <a href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>"}'><strong><?php echo __("website/osteps/continue-button"); ?> <i class="ion-android-arrow-dropright"></i></strong></a>

                            <div style="text-align: left; margin-top: 5px; display: none;" id="result" class="error"></div>
                        </td>
                    </tr>
                </table>
            </form>
            <script type="text/javascript">
                function StepForm1_submit(result) {
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "error"){
                                if(solve.for != undefined && solve.for != ''){
                                    $("#StepForm1 "+solve.for).focus();
                                    $("#StepForm1 "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                    $("#StepForm1 "+solve.for).change(function(){
                                        $(this).removeAttr("style");
                                    });
                                }
                                if(solve.message != undefined && solve.message != '')
                                    $("#StepForm1 #result").fadeIn(300).html(solve.message);
                                else
                                    $("#StepForm1 #result").fadeOut(300).html('');
                            }else if(solve.status == "successful"){
                                $("#StepForm1 #result").fadeOut(300).html('');
                                if(solve.redirect != undefined && solve.redirect != '') window.location.href = solve.redirect;
                            }
                        }else
                            console.log(result);
                    }
                }
            </script>
        </div>
    <?php endif; ?>

    <?php if($step == "origin"): ?>
        <div class="pakettitle" style="margin-top:0px;">
            <h1><strong><?php echo __("website/osteps/set-your-origin"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/set-your-origin-note"); ?></h2>
        </div>



        <div class="siparisbilgileri">

            <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1" enctype="multipart/form-data">
                <?php echo Validation::get_csrf_token('order-steps'); ?>

                <table width="100%" border="0" align="center">
                    <tr>
                        <td colspan="2" bgcolor="#ebebeb"><strong><?php echo __("website/osteps/origin-informations"); ?></strong></td>
                    </tr>

                    <tr>
                        <td  colspan="2"><SPAN style="font-size:15px; color:red"><?php echo __("website/osteps/set-your-origin-note2"); ?></SPAN></td>
                    </tr>

                    <tr>
                        <td width="30%"><span class="zorunlu">*</span> <?php echo __("website/osteps/sender-title"); ?>: </td>
                        <td><input class="notr" name="origin" type="text" placeholder="<?php echo __("website/osteps/origin-placeholder"); ?>" size="11" maxlength="11" style="text-transform:uppercase"></td>
                    </tr>

                    <script type="text/javascript">
                        // başlık tanımlamalarında türkçe karakter kullanılmasın..
                        $(".notr").on("ready load keyup keydown keypress change", function () {
                            var baslik = $(this).val().substr(0, 11).toUpperCase().replace(/Ö/g, "O").replace(/Ç/g, "C").replace(/Ş/g, "S").replace(/İ/g, "i").replace(/Ğ/g, "G").replace(/Ü/g, "U").replace(/([^a-zA-Z0-9 \.\-_])/g, "");
                            $(this).val(baslik);
                            if (baslik.length > 3) {
                                $("li.baslikkarakter i").removeClass("fa-dot-circle-o").addClass("fa-check");
                            }else {
                                $("li.baslikkarakter i").removeClass("fa-check").addClass("fa-dot-circle-o");
                            }
                            if (baslik.replace(/([0-9]+)/, "").length != 0) {
                                $("li.baslikrakam i").removeClass("fa-dot-circle-o").addClass("fa-check");
                                titleInNumber = false;
                            }else {
                                $("li.baslikrakam i").removeClass("fa-check").addClass("fa-dot-circle-o");
                                titleInNumber = true;
                            }
                            $(".total").html(11 - baslik.length);
                        });
                    </script>

                    <tr>
                        <td width="30%"><span class="zorunlu">*</span> <?php echo __("website/osteps/origin-attachments"); ?>: </td>
                        <td><input style="font-size:15px;" name="attachments[]" type="file">
                            <SPAN style="font-size:15px; color:green"><?php echo __("website/osteps/origin-attachments-note"); ?></SPAN>
                        </td>
                    </tr>

                    <tr>
                        <td style="border:none;" align="center" colspan="2">
                            <a href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>"}'><strong><?php echo __("website/osteps/continue-button"); ?> <i class="ion-android-arrow-dropright"></i></strong></a>

                            <div style="text-align: left; margin-top: 5px; display: none;" id="result" class="error"></div>
                        </td>
                    </tr>
                </table>
            </form>
            <script type="text/javascript">
                function StepForm1_submit(result) {
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "error"){
                                if(solve.for != undefined && solve.for != ''){
                                    $("#StepForm1 "+solve.for).focus();
                                    $("#StepForm1 "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                    $("#StepForm1 "+solve.for).change(function(){
                                        $(this).removeAttr("style");
                                    });
                                }
                                if(solve.message != undefined && solve.message != '')
                                    $("#StepForm1 #result").fadeIn(300).html(solve.message);
                                else
                                    $("#StepForm1 #result").fadeOut(300).html('');
                            }else if(solve.status == "successful"){
                                $("#StepForm1 #result").fadeOut(300).html('');
                                if(solve.redirect != undefined && solve.redirect != '') window.location.href = solve.redirect;
                            }
                        }else
                            console.log(result);
                    }
                }
            </script>

        </div>
    <?php endif; ?>

</div>
</div>
