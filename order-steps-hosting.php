<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "order-steps-hosting",
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
#wrapper .ilanasamalar .ilanasamax h3 { background:linear-gradient(135deg,#1e40af,#3b82f6); color:#fff; width:32px; height:32px; border-radius:50%; display:grid; place-items:center; font-size:14px; font-weight:800; margin:0 auto 8px; }
#wrapper .ilanasamalar #asamaaktif { border:2px solid #1e40af; box-shadow:0 8px 20px rgba(30,64,175,0.15); }
#wrapper .ilanasamalar #asamaaktif h3 { background:linear-gradient(135deg,#facc15,#f59e0b); color:#1e3a8a; }
#wrapper .asamaline { display:none; }

#wrapper .orderperiodblock-con { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; }
#wrapper .orderperiodblock { background:#f8fafc; border:2px solid #e2e8f0; border-radius:14px; padding:24px 18px; text-align:center; cursor:pointer; transition:all 0.2s; position:relative; }
#wrapper .orderperiodblock:hover { border-color:#1e40af; transform:translateY(-2px); }
#wrapper .orderperiodblock.active { border-color:#1e40af; background:linear-gradient(135deg,#fff,#eff6ff); box-shadow:0 8px 24px rgba(30,64,175,0.20); }
#wrapper .orderperiodblock h3 { color:#1e40af; font-size:14px; font-weight:700; margin:0 0 8px; text-transform:uppercase; letter-spacing:0.5px; }
#wrapper .orderperiodblock h2 { color:#0f172a; font-size:24px; font-weight:800; margin:0; }
#wrapper .orderperiodblock .periodselectbox { width:24px; height:24px; border:2px solid #cbd5e1; border-radius:50%; margin:12px auto 0; display:grid; place-items:center; transition:all 0.2s; }
#wrapper .orderperiodblock.active .periodselectbox { background:#1e40af; border-color:#1e40af; }
#wrapper .orderperiodblock.active .periodselectbox i { color:#fff; }
#wrapper .orderperiodblock .periodselectbox i { color:transparent; font-size:12px; }
#wrapper .ribbonperiod { position:absolute; top:-1px; right:-1px; }
#wrapper .ribbonperiod span { display:inline-block; background:linear-gradient(135deg,#10b981,#059669)!important; color:#fff!important; padding:5px 12px; font-size:11px; font-weight:700; border-radius:0 12px 0 12px; }
#wrapper .setup-fee-period { display:block; color:#64748b; font-size:11px; margin-top:6px; font-weight:600; }

#wrapper .btn, #wrapper .gonderbtn { display:inline-flex; align-items:center; gap:8px; padding:13px 26px; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none; transition:all 0.2s; background:linear-gradient(135deg,#1e40af,#3b82f6)!important; color:#fff!important; border:0; cursor:pointer; }
#wrapper .btn:hover, #wrapper .gonderbtn:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(30,64,175,0.25); }

#wrapper .alanadisorgu { background:#f8fafc; padding:20px; border-radius:12px; border:1px solid #e2e8f0; }
#wrapper .alanadisorgu input[type="text"] { width:60%; padding:12px 14px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; font-family:inherit; }
#wrapper .alanadisorgu h5 { font-size:13px; color:#64748b; margin-top:14px; font-weight:500; }

#wrapper #accordion h3 { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px 20px; margin-bottom:8px; cursor:pointer; font-size:15px; font-weight:700; color:#0f172a; }
#wrapper #accordion h3.ui-state-active { background:linear-gradient(135deg,#1e40af,#3b82f6); color:#fff; }

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
            <div class="cdg-domain-badge"><i class="bi bi-hdd-network-fill"></i> Hosting Sipariş Adımları</div>
        </div>
    </div>
</section>

<div class="cdg-container">

<script>
        $( function() {
            $( "#accordion" ).accordion({
                heightStyle: "content"
            });

            $(window).not("textarea").keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

        });
    </script>



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
            <h1><strong><?php echo __("website/osteps/service-time-selection"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/service-time-selection-note"); ?></h2>
        </div>



        <div class="siparisbilgileri">

            <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1">
                <?php echo Validation::get_csrf_token('order-steps'); ?>

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
.orderperiodblock h3 {
    color: var(--color-primary-two);
}
                </style>
                <div class="orderperiodblock-con">
                    <input type="hidden" name="selection" value="0">
                    <?php
                        $selectp = (int) substr(Filter::init("GET/select","rnumbers"),0,1);
                        if(isset($product["price"]) && $product["price"]){
                            foreach ($product["price"] AS $k=>$pe){
                                $amount     = Money::formatter_symbol($pe["amount"],$pe["cid"],!$product["override_usrcurrency"]);
                                $setup      = $pe["setup"] > 0.00 ? Money::formatter_symbol($pe["setup"],$pe["cid"],!$product["override_usrcurrency"]) : '';
                                $period     = View::period($pe["time"],$pe["period"]);
                                $discount   = $pe["discount"]>0 ? '<div class="ribbonperiod"><span>'.__("website/osteps/rate-discount",['{rate}' => $pe["discount"]]).'</span></div>' : NULL;
                                ?>
                                <div class="orderperiodblock<?php echo $setup ? ' setup-fee-period-block' : ''; ?>" id="price-<?php echo $pe["id"]; ?>" data-value="<?php echo $k; ?>">
                                    <?php echo $discount; ?>
                                    <h3><?php echo $period; ?></h3>
                                    <h2><?php echo $amount; ?></h2>
                                    <?php
                                        if($setup)
                                        {
                                            ?>
                                            <span class="setup-fee-period">+ <?php echo $setup; ?> <?php echo __("website/osteps/setup-fee"); ?></span>
                                            <?php
                                        }
                                    ?>
                                    <div class="periodselectbox"><i class="fa fa-check" aria-hidden="true"></i></div>
                                </div>
                                <?php
                            }
                        }
                    ?>
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $(".orderperiodblock").click(function(){
                                if($(this).hasClass("active")) return false;
                                $(".orderperiodblock").removeClass("active");
                                $(this).addClass("active");
                                $("#StepForm1 input[name=selection]").val($(this).data("value"));
                            });
                            var selected_price = <?php echo $selectp ? (string) $selectp : "0"; ?>;
                            $(".orderperiodblock:eq("+selected_price+") .periodselectbox").trigger("click");
                        });
                    </script>

                    <div class="clear"></div>
                    <div style="margin-top:55px;    margin-bottom: 25px;"><a style="float:none" href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'
                ><strong><?php echo __("website/osteps/continue-button"); ?> <i class="ion-android-arrow-dropright"></i></strong></a></div>
                </div>
                <div class="clear"></div>
                <div class="error" id="result" style="text-align: center; margin-top: 5px; display: none;"></div>
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
                                window.location.href = solve.redirect;
                            }
                        }else
                            console.log(result);
                    }
                }
            </script>

        </div>
    <?php endif; ?>

    <?php if($step == "domain"): ?>
        <div class="pakettitle" style="margin-top:0px;">
            <h1><strong><?php echo __("website/osteps/identify-your-domain"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/identify-your-domain-note2"); ?></h2>
        </div>
        <div class="clear"></div>
        <div class="siparisbilgileri">

            <div class="domainsec">
                <div id="accordion">

                    <?php if(isset($firstTLD) && is_array($firstTLD) && Config::get("options/pg-activation/domain")): ?>
                        <h3><strong><?php echo __("website/osteps/iwill-register-a-new-domain"); ?></strong></h3>
                        <div style="overflow: hidden;">
                            <table width="100%" border="0" align="center">
                                <tr>
                                    <td style="border:none;" colspan="2">
                                        <div class="alanadisorgu">
                                            <form action="<?php echo $links["domain_check"]; ?>" method="post" id="DomainCheck">
                                                <?php echo Validation::get_csrf_token('domain-check'); ?>
                                                <input type="hidden" name="operation" value="check">
                                                <input type="hidden" name="type" value="domain">
                                                <input type="hidden" name="from" value="order_steps">
                                                <input type="hidden" name="product_type" value="<?php echo $product["type"]; ?>">
                                                <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
                                                <input type="hidden" name="selected_period" value="<?php echo isset($selected_period) ? $selected_period["id"] : 0; ?>">
                                                <input name="domain" type="text" placeholder="<?php echo __("website/osteps/domain-placeholder"); ?>">
                                                <a href="javascript:void(0);" class="gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"DomainCheckSubmit","before_function":"DomainCheckBefore","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/osteps/check-it-button"); ?></a>
                                                <div class="clear"></div>
                                                <div class="error" id="result" style="display: none;margin-top:5px;"></div>
                                            </form>
                                            <script type="text/javascript">
                                                $(document).ready(function(){
                                                    $("#DomainCheck").keydown(function(event){
                                                        if(event.keyCode == 13){
                                                            $("#DomainCheck .mio-ajax-submit").trigger("click");
                                                        }
                                                    });
                                                });
                                                function DomainCheckBefore() {
                                                    $(".result-content").hide(1);
                                                    $("#tescilsonuc").slideUp(400);
                                                    return true;
                                                }
                                                function DomainCheckSubmit(result) {
                                                    if(result != ''){
                                                        var solve = getJson(result);
                                                        if(solve !== false){
                                                            if(solve.status == "error"){
                                                                var inputt = $("#DomainCheck input[name=domain]");
                                                                inputt.focus();
                                                                inputt.attr("style","border-bottom:2px solid red; color:red;");
                                                                inputt.change(function(){
                                                                    $(this).removeAttr("style");
                                                                });

                                                                if(solve.message != undefined && solve.message != '')
                                                                    alert_error(solve.message,{timer:3000});
                                                            }else if(solve.status == "successful"){

                                                                var ListData = solve.data != undefined ? solve.data : false;
                                                                if(ListData){
                                                                    var result = ListData[0];
                                                                    var status = result.status;
                                                                    if(status == "available"){
                                                                        $("#sadeckyinfo,#sadeckyinfo_free").css("display","none");
                                                                        $("#register-amount").html(result.reg_price[0].price);
                                                                        if(result.reg_price[0].price === 0)
                                                                            $("#sadeckyinfo_free").css("display","block");
                                                                        else
                                                                            $("#sadeckyinfo").css("display","block");
                                                                    }
                                                                    $("#"+status+"_content").show(1);
                                                                    $("#tescilsonuc").slideDown(400);
                                                                    $(".domain-name").html(result.domain);
                                                                }
                                                            }
                                                        }else
                                                            console.log(result);
                                                    }
                                                }
                                            </script>
                                            <?php
                                                $firstTLD_amount = $firstTLD["register"]["amount"];
                                                if($firstTLD["promo_status"] && (substr($firstTLD["promo_duedate"],0,4) == '1881' || DateManager::strtotime($firstTLD["promo_duedate"]." 23:59:59") > DateManager::strtotime()) && $firstTLD["promo_register_price"]>0) $firstTLD_amount = $firstTLD["promo_register_price"];
                                            ?>
                                            <h5><?php echo __("website/domain/slogan",['{price}' => Money::formatter_symbol($firstTLD_amount,$firstTLD["register"]["cid"],!$domain_override_usrcurrency)]); ?></h5>
                                        </div>



                                        <div class="tescilsonuc" id="tescilsonuc" style="display: none; transition-property: all; transition-duration: 0s; transition-timing-function: ease; opacity: 1;">
                                            <table width="80%" border="0" align="center">

                                                <tr class="result-content" id="unknown_content" style="display: none;">
                                                    <td align="center">
                                                        <div class="error"><?php echo __("website/osteps/unknown-message"); ?></div>
                                                    </td>
                                                </tr>

                                                <tr class="result-content" id="available_content" style="display: none;">
                                                    <td align="center">
                                                        <h4><strong><?php echo __("website/osteps/available-message1"); ?></strong></h4>
                                                        <span class="sadeckyinfo" id="sadeckyinfo" style="display: none;"><?php echo __("website/osteps/available-message2"); ?></span>
                                                        <span class="sadeckyinfo" id="sadeckyinfo_free" style="display: none;"><?php echo __("website/osteps/available-message3"); ?></span>
                                                        <a href="javascript:void(0);" onclick="GoStep('registrar',false,this);" class="gonderbtn"><?php echo __("website/osteps/select-and-continue"); ?></a>
                                                        <div class="clear"></div>
                                                    </td>
                                                </tr>

                                                <tr class="result-content" id="unavailable_content" style="display:none;">
                                                    <td align="center">
                                                        <h4 style="color:red;"><strong><i class="fa fa-ban" aria-hidden="true"></i> <?php echo __("website/osteps/unavailable-message1"); ?></strong></h4>
                                                        <span class="sadeckyinfo"><?php echo __("website/osteps/unavailable-message2"); ?></span>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>



                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php endif; ?>


                    <h3><strong><?php echo __("website/osteps/current-my-domain"); ?></strong></h3>
                    <div>
                        <table width="100%" border="0" align="center">
                            <tr>
                                <td style="border:none;" colspan="2">
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("#hosting_domain").keydown(function(event){
                                                if(event.keyCode == 13){
                                                    $("#button1").trigger("click");
                                                }
                                            });
                                        });
                                    </script>
                                    <div class="alanadisorgu">
                                        <input name="domain" id="hosting_domain" type="text" placeholder="<?php echo __("website/osteps/domain-placeholder"); ?>">
                                        <a href="javascript:void(0);" id="button1" onclick="GoStep('none',false,this);" class="gonderbtn"><?php echo __("website/osteps/use-button"); ?></a>
                                        <div class="clear"></div>
                                        <div id="result_dns">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            <p><?php echo __("website/osteps/define-domain-dns"); ?></p>
                                            <div id="dns_list">
                                                <?php
                                                    if(isset($dns_addresses) && $dns_addresses){
                                                        foreach($dns_addresses AS $dns){
                                                            ?>
                                                            <span><?php echo $dns; ?></span>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>


                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function GoStep(type,continuee,element){
                if(continuee === false) continuee = '';
                var buttonElement = $(element);
                if(type == "registrar")
                    var domain   = $("#DomainCheck input[name=domain]").val();
                else
                    var domain   = $("#hosting_domain").val();

                $("#result3").slideUp(300);

                var request = MioAjax({
                    button_element:buttonElement,
                    waiting_text:"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                    action: "<?php echo $links["step"]; ?>",
                    method: "POST",
                    data: {
                        type:type,
                        domain:domain,
                        continuee:continuee,
                        token:'<?php echo Validation::get_csrf_token('order-steps',false); ?>',
                    }
                },true,true);

                request.done(function(result){
                    if(!result){
                        console.log("HTTP Request failed.");
                    }else{
                        var solve = getJson(result);
                        if(solve){
                            if(solve.status == "error"){
                                if(solve.for != undefined && solve.for != ''){
                                    $(solve.for).focus();
                                    $(solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                    $(solve.for).change(function(){
                                        $(this).removeAttr("style");
                                    });
                                }
                                if(solve.message != undefined && solve.message != '')
                                    alert_error(solve.message,{timer:3000});

                            }else if(solve.status == "successful"){
                                if(solve.redirect != undefined && solve.redirect != '')
                                    window.location.href = solve.redirect;
                            }else if(solve.status == "continue"){

                                $(".result-domain").html(solve.domain);

                                $("#result_dns").html(solve.dns);

                                $("#result3").slideDown(300,function(){
                                    $(".result-content2").fadeOut(1,function(){
                                        $("#status_"+solve.domain_status).css("display","block");
                                    });
                                });

                            }
                        }else
                            console.log("Http request data could not be resolved.");
                    }
                });
            }
        </script>
    <?php endif; ?>

    <?php if ($step == "addons"): ?>
        <div class="pakettitle" style="margin-top:0px;">
            <h1><strong><?php echo __("website/osteps/additional-services"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/additional-services-note"); ?></h2>
        </div>


        <div class="siparisbilgileri">

            <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1" enctype="multipart/form-data">
                <?php echo Validation::get_csrf_token('order-steps'); ?>

                <table width="100%" border="0" align="center">

                    <tr>
                        <td bgcolor="#ebebeb"><strong><?php echo __("website/osteps/additional-service"); ?></strong></td>
                        <td bgcolor="#ebebeb"><strong><?php echo __("website/osteps/additional-service-amount"); ?></strong></td>
                    </tr>

                    <?php
                        foreach($addons AS $addon){
                            $options  = $addon["options"];
                            $properties = $addon["properties"];
                            $compulsory = isset($properties["compulsory"]) && $properties["compulsory"];
                            ?>
                            <tr>
                                <td width="50%">
                                    <?php if($compulsory): ?>
                                        <span class="zorunlu">*</span>
                                    <?php endif; ?>
                                    <label for="addon-<?php echo $addon["id"]; ?>">
                                        <strong><?php echo $addon["name"]; ?></strong>
                                        <?php if($addon["description"]): ?>
                                            <br>
                                            <span style="font-size: 14px;"><?php echo $addon["description"]; ?></span>
                                        <?php endif; ?>
                                    </label>
                                </td>
                                <td width="50%">
                                    <style>
                                        .radio-custom:checked+.radio-custom-label:before {
    background: var(--color-primary-two);
}
#rehber .checkbox-custom+.checkbox-custom-label:before, .radio-custom+.radio-custom-label:before {
    border-radius: 10%;
}
.checkbox-custom+.checkbox-custom-label:before, .radio-custom+.radio-custom-label:before {
    border: 1.5px solid var(--color-primary);
    line-height: 15px;
}
                                    </style>
                                    <?php
                                        if($addon["type"] == "radio"){
                                            ?>
                                            <?php if(!$compulsory): ?>
                                        <input checked id="addon-<?php echo $addon["id"]."-none"; ?>" class="radio-custom" name="addons[<?php echo $addon["id"]; ?>]" value="" type="radio">
                                            <label style="margin-right:30px;" for="addon-<?php echo $addon["id"]."-none"; ?>" class="radio-custom-label"><?php echo ___("needs/idont-want"); ?></label>
                                        <br>
                                        <?php endif; ?>
                                            <?php
                                        foreach ($options AS $k=>$opt){
                                            $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"],!$addon["override_usrcurrency"]);
                                            if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                            $periodic   = View::period($opt["period_time"],$opt["period"]);
                                            $name       = $opt["name"];
                                            $show_name  = $name." <strong>".$amount."</strong>";
                                            if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                $show_name .= " | <strong>".$periodic."</strong>";
                                            ?>
                                        <input<?php echo $compulsory && $k==0 ? ' checked' : ''; ?> id="addon-<?php echo $addon["id"]."-".$k; ?>" class="radio-custom" name="addons[<?php echo $addon["id"]; ?>]" value="<?php echo $opt["id"]; ?>" type="radio">
                                            <label style="margin-right:30px;" for="addon-<?php echo $addon["id"]."-".$k; ?>" class="radio-custom-label"><?php echo $show_name; ?></label>
                                        <br>
                                        <?php
                                            }
                                            }
                                            elseif($addon["type"] == "checkbox"){
                                        ?>
                                        <?php if(!$compulsory): ?>
                                        <input checked id="addon-<?php echo $addon["id"]."-none"; ?>" class="checkbox-custom" name="addons[<?php echo $addon["id"]; ?>]" value="" type="radio">
                                            <label style="margin-right:30px;" for="addon-<?php echo $addon["id"]."-none"; ?>" class="checkbox-custom-label"><?php echo ___("needs/idont-want"); ?></label>
                                        <br>
                                        <?php endif; ?>
                                            <?php
                                        foreach ($options AS $k=>$opt){
                                            $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"],!$addon["override_usrcurrency"]);
                                            if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                            $periodic = View::period($opt["period_time"],$opt["period"]);
                                            $name       = $opt["name"];
                                            $show_name  = $name." <strong>".$amount."</strong>";
                                            if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                $show_name .= " | <strong>".$periodic."</strong>";
                                            ?>
                                        <input<?php echo $compulsory && $k==0 ? ' checked' : ''; ?> id="addon-<?php echo $addon["id"]."-".$k; ?>" class="checkbox-custom" name="addons[<?php echo $addon["id"]; ?>]" value="<?php echo $opt["id"]; ?>" type="radio">
                                            <label style="margin-right:30px;" for="addon-<?php echo $addon["id"]."-".$k; ?>" class="checkbox-custom-label"><?php echo $show_name; ?></label>
                                        <br>
                                        <?php
                                            }
                                            }
                                            elseif($addon["type"] == "select"){
                                        ?>
                                            <select name="addons[<?php echo $addon["id"]; ?>]">
                                                <?php if(!$compulsory): ?>
                                                    <option value=""><?php echo ___("needs/idont-want"); ?></option>
                                                <?php endif; ?>
                                                <?php
                                                    foreach ($options AS $k=>$opt){
                                                        $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"],!$addon["override_usrcurrency"]);
                                                        if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                                        $periodic = View::period($opt["period_time"],$opt["period"]);
                                                        $name       = $opt["name"];
                                                        $show_name  = $name." <strong>".$amount."</strong>";
                                                        if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                            $show_name .= " | <strong>".$periodic."</strong>";
                                                        ?>
                                                        <option value="<?php echo $opt["id"]; ?>"><?php echo $show_name; ?></option>

                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        <?php
                                            }
                                            elseif($addon["type"] == "quantity"){
                                            $min = isset($properties["min"]) ? $properties["min"] : '0';
                                            $max = isset($properties["max"]) ? $properties["max"] : '0';
                                            $stp = isset($properties["step"]) ? $properties["step"] : '1';
                                            if($min == 0) $min = 1;
                                        ?>
                                            <select name="addons[<?php echo $addon["id"]; ?>]" id="addon-<?php echo $addon["id"]; ?>-selection" style="margin-bottom: 5px;">
                                                <?php if(!$compulsory): ?>
                                                    <option value=""><?php echo ___("needs/idont-want"); ?></option>
                                                <?php endif; ?>
                                                <?php
                                                    foreach ($options AS $k=>$opt){
                                                        $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"],!$addon["override_usrcurrency"]);
                                                        if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                                        $periodic = View::period($opt["period_time"],$opt["period"]);
                                                        $name       = $opt["name"];
                                                        $show_name  = $name." <strong>".$amount."</strong>";
                                                        if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                            $show_name .= " | <strong>".$periodic."</strong>";
                                                        ?>
                                                        <option value="<?php echo $opt["id"]; ?>"><?php echo $show_name; ?></option>

                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                            <script type="text/javascript">
                                                $(document).ready(function(){
                                                    $("#addon-<?php echo $addon["id"]; ?>-slider-value").ionRangeSlider({
                                                        min: <?php echo $min; ?>,
                                                        max: <?php echo $max; ?>,
                                                        from:<?php echo $min; ?>,
                                                        step:<?php echo $stp; ?>,
                                                        grid: true,
                                                        skin: "big",
                                                    });

                                                    $("#addon-<?php echo $addon["id"]; ?>-selection").change(function() {
                                                        if( $(this).val() === '') {
                                                            $('#addon-<?php echo $addon["id"]; ?>-slider-content').slideUp(250);
                                                        }else{
                                                            $('#addon-<?php echo $addon["id"]; ?>-slider-content').slideDown(250);
                                                        }

                                                    });
                                                });
                                            </script>
                                            <div id="addon-<?php echo $addon["id"]; ?>-slider-content" style="display: none;">
                                                <input id="addon-<?php echo $addon["id"]; ?>-slider-value" name="addons_values[<?php echo $addon["id"]; ?>]" type="range" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $stp; ?>" value="<?php echo $min; ?>">

                                            </div>
                                            <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>

                    <tr>
                        <td style="border:none;" align="center" colspan="2">
                            <a href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><strong><?php echo __("website/osteps/continue-button"); ?> <i class="ion-android-arrow-dropright"></i></strong></a>
                            <div class="clear"></div>
                            <div id="result" class="error" style="display: none;text-align: center;margin-top: 5px;"></div>
                        </td>
                    </tr>
                </table>
            </form>



        </div>
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
    <?php endif; ?>

    <?php if($step == "requirements"): ?>
        <div class="pakettitle" style="margin-top:0px;">
            <h1><strong><?php echo __("website/osteps/necessary-information2"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/necessary-information-note"); ?></h2>
        </div>



        <div class="siparisbilgileri">

            <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1" enctype="multipart/form-data">
                <?php echo Validation::get_csrf_token('order-steps'); ?>

                <table width="100%" border="0" align="center">
                    <tr>
                        <td colspan="2" bgcolor="#ebebeb"><strong><?php echo __("website/osteps/necessary-information3"); ?></strong></td>
                    </tr>

                    <?php
                        if(isset($requirements) && $requirements){
                            foreach($requirements AS $requirement){
                                $options    = $requirement["options"];
                                $properties = $requirement["properties"];
                                $wrap_invisible  = false;
                                if(isset($properties["wrap_visibility"]) && $properties["wrap_visibility"] == "invisible")
                                    $wrap_invisible = 'style="display:none;"';
                                ?>
                                <tr id="requirement-<?php echo $requirement["id"]; ?>-wrap" <?php echo $wrap_invisible; ?>>
                                    <td width="50%">
                                        <?php if(isset($properties["compulsory"]) && $properties["compulsory"]){ ?><span class="zorunlu">*</span><?php } ?>
                                        <label for="requirement-<?php echo $requirement["id"]; ?>">
                                            <strong><?php echo $requirement["name"]; ?></strong>
                                            <?php if($requirement["description"]): ?>
                                                <br>
                                                <span style="font-size: 14px;"><?php echo nl2br($requirement["description"]); ?></span>
                                            <?php endif; ?>
                                        </label>
                                    </td>
                                    <td width="50%">
                                        <?php
                                            if($requirement["type"] == "input"){
                                                ?>
                                                <input type="text" name="requirements[<?php echo $requirement["id"]; ?>]" id="requirement-<?php echo $requirement["id"]; ?>" placeholder="<?php echo isset($properties["placeholder"]) ? $properties["placeholder"] : ''; ?>">
                                                <?php
                                            }
                                            elseif($requirement["type"] == "password"){
                                                ?>
                                                <input type="password" name="requirements[<?php echo $requirement["id"]; ?>]" id="requirement-<?php echo $requirement["id"]; ?>" placeholder="<?php echo isset($properties["placeholder"]) ? $properties["placeholder"] : ''; ?>">
                                                <?php
                                            }
                                            elseif($requirement["type"] == "textarea"){
                                                ?>
                                                <textarea name="requirements[<?php echo $requirement["id"]; ?>]" id="requirement-<?php echo $requirement["id"]; ?>" placeholder="<?php echo isset($properties["placeholder"]) ? $properties["placeholder"] : ''; ?>"></textarea>
                                                <?php
                                            }
                                            elseif($requirement["type"] == "radio"){
                                                foreach ($options AS $k=>$opt){
                                                    ?>
                                                    <input id="requirement-<?php echo $requirement["id"]."-".$k; ?>" class="radio-custom" name="requirements[<?php echo $requirement["id"]; ?>]" value="<?php echo $opt["id"]; ?>" type="radio">
                                                    <label style="margin-right:30px;" for="requirement-<?php echo $requirement["id"]."-".$k; ?>" class="radio-custom-label"><span class="checktext"><?php echo $opt["name"]; ?></span></label>
                                                    <br>
                                                    <?php
                                                }
                                            }
                                            elseif($requirement["type"] == "checkbox"){
                                                foreach ($options AS $k=>$opt){
                                                    ?>
                                                    <input id="requirement-<?php echo $requirement["id"]."-".$k; ?>" class="checkbox-custom" name="requirements[<?php echo $requirement["id"]; ?>][]" value="<?php echo $opt["id"]; ?>" type="checkbox">
                                                    <label style="margin-right:30px;" for="requirement-<?php echo $requirement["id"]."-".$k; ?>" class="checkbox-custom-label"><span class="checktext"><?php echo $opt["name"]; ?></span></label>
                                                    <br>
                                                    <?php
                                                }
                                            }
                                            elseif($requirement["type"] == "select"){
                                                ?>
                                                <select name="requirements[<?php echo $requirement["id"]; ?>]" id="requirement-<?php echo $requirement["id"]; ?>">
                                                    <option value=""><?php echo __("website/osteps/select-your-option"); ?></option>
                                                    <?php
                                                        foreach ($options AS $k=>$opt){
                                                            ?>
                                                            <option value="<?php echo $opt["id"]; ?>"><?php echo $opt["name"]; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                                <?php
                                            }
                                            elseif($requirement["type"] == "file"){
                                                ?>
                                                <input type="file" name="requirement-<?php echo $requirement["id"]; ?>[]" id="requirement-<?php echo $requirement["id"]; ?>" multiple>
                                                <?php
                                            }

                                            if(isset($properties["define_end_of_element"]))
                                                if($properties["define_end_of_element"])
                                                    echo $properties["define_end_of_element"];
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    ?>


                    <tr>
                        <td style="border:none;" align="center" colspan="2">
                            <a href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>"}'><strong><?php echo __("website/osteps/continue-button"); ?></strong></a>
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

                $(document).ready(function(){
                    $("#StepForm1").keydown(function(event){
                        if(event.keyCode == 13){
                            $("#StepForm1 .mio-ajax-submit").trigger("click");
                        }
                    });
                });
            </script>



        </div>
    <?php endif; ?>

</div>
</div>
