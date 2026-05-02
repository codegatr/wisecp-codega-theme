<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $haveStock = $product["haveStock"];
    $hoptions = [
        'page' => "order-steps-server",
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
            <div class="cdg-domain-badge"><i class="bi bi-server"></i> Sunucu Sipariş Adımları</div>
        </div>
    </div>
</section>

<div class="cdg-container">

<script type="text/javascript">
    $(document).ready(function(){
        $( "#accordion" ).accordion({
            heightStyle: "content"
        });
    });
</script>
<div id="wrapper">
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


    <?php

        if(!$haveStock){
            ?>
            <!-- out of stock -->
            <div style="margin-top: 70px;margin-bottom:70px;text-align:center;display: inline-block;width: 100%;">
                <i style="font-size:70px;margin-bottom: 15px;" class="fa fa-info-circle"></i>
                <h2 style="font-weight:bold;"><?php echo __("website/osteps/out-of-stock-1"); ?></h2>
                <br>
                <h4><?php echo __("website/osteps/out-of-stock-2"); ?>  <br> <br><strong><?php echo __("website/osteps/out-of-stock-3"); ?></strong></h4>
            </div>
            <!-- out of stock end-->
            <?php
        }
    ?>

    <?php if($haveStock && $step == 1): ?>
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

    <?php if($haveStock && $step == "configuration"): ?>
        <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1" enctype="multipart/form-data" onsubmit="return false;">
            <?php echo Validation::get_csrf_token('order-steps'); ?>

            <div class="sunucukonfigurasyonu">

                <div class="sungenbil">

                    <?php if(Config::get("options/hidsein")): ?>
                        <div class="skonfiginfo" id="configInfo" style="margin-bottom:20px;">
                            <div style="padding:20px;">
                                <h4><?php echo __("website/osteps/server-set-informations"); ?></h4>
                                <table width="100%" border="0">

                                    <tr>
                                        <td width="30%">
                                            <label for="hostname">
                                                <?php echo __("website/osteps/hostname"); ?>
                                            </label>
                                        </td>
                                        <td width="70%">
                                            <input type="text" name="hostname" id="hostname" placeholder="<?php echo __("website/osteps/ex-placeholder"); ?>: server.example.com">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="30%">
                                            <label for="ns1">
                                                <?php echo __("website/osteps/server-ns1"); ?>
                                            </label>
                                        </td>
                                        <td width="70%">
                                            <input type="text" name="ns1" id="ns1" placeholder="<?php echo __("website/osteps/ex-placeholder"); ?>: ns1.example.com">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="30%">
                                            <label for="ns2">
                                                <?php echo __("website/osteps/server-ns2"); ?>
                                            </label>
                                        </td>
                                        <td width="70%">
                                            <input type="text" name="ns2" id="ns2" placeholder="<?php echo __("website/osteps/ex-placeholder"); ?>: ns2.example.com">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="30%">
                                            <label for="password">
                                                <?php echo __("website/osteps/server-password"); ?>
                                            </label>
                                        </td>
                                        <td width="70%">
                                            <input type="text" name="password" id="password" placeholder="<?php echo __("website/osteps/server-password-info"); ?>">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($addons): ?>
                        <div class="skonfiginfo" style="margin-bottom:20px;">
                            <div style="padding:20px;">
                                <h4><?php echo __("website/osteps/adjustable-options"); ?></h4>
                                <table width="100%" border="0">

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
                                                        <strong>  <?php echo $addon["name"]; ?></strong>
                                                        <?php if($addon["description"]): ?>
                                                            <br>
                                                            <span style="font-size: 14px;"><?php echo $addon["description"]; ?></span>
                                                        <?php endif; ?>
                                                    </label>
                                                </td>
                                                <td width="50%">
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
                                                                    $("#addon-<?php echo $addon["id"]; ?>-selection").change(function() {
                                                                        if( $(this).val() === '') {
                                                                            $('#addon-<?php echo $addon["id"]; ?>-slider-content').slideUp(250);
                                                                        }else{
                                                                            $('#addon-<?php echo $addon["id"]; ?>-slider-content').slideDown(250);
                                                                        }
                                                                    });
                                                                    $("#addon-<?php echo $addon["id"]; ?>-slider-value").ionRangeSlider({
                                                                        min: <?php echo $min; ?>,
                                                                        max: <?php echo $max; ?>,
                                                                        from:<?php echo $min; ?>,
                                                                        step:<?php echo $stp; ?>,
                                                                        grid: true,
                                                                        skin: "big",
                                                                    });
                                                                });
                                                            </script>
                                                            <div id="addon-<?php echo $addon["id"]; ?>-slider-content" style="<?php echo $compulsory ? '' : 'display: none;'; ?>">
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
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($requirements) && $requirements): ?>

                        <div class="skonfiginfo" style="margin-bottom:20px;">
                            <div style="padding:20px;">
                                <h4><?php echo __("website/osteps/necessary-information2"); ?></h4>

                                <table width="100%" border="0" align="center">
                                    <?php
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
                                    ?>
                                </table>

                            </div>
                        </div>

                    <?php endif; ?>

                </div>

                <?php
                    $price     = $step1_data["selection"];
                    if($price){
                        $amount     = Money::formatter_symbol($price["amount"],$price["cid"],!$product["override_usrcurrency"]);
                        $period     = View::period($price["time"],$price["period"]);
                    }else{
                        $amount = ___("needs/free-amount");
                        $period = NULL;
                    }
                ?>
                <div class="sunucusipside">
                    <div class="skonfigside" style="width: 100%;">
                        <div style="padding:20px;">
                            <h4><?php echo __("website/osteps/order-summary"); ?></h4>
                            <strong><?php echo $product["title"]; echo $period ? ' | '.$period : ''; ?></strong>
                            <strong style="float: right"><?php echo $amount; ?></strong>
                            <br>
                            <?php echo $product["category_title"]; ?>
                            <div class="line"></div>
                            <div id="service_amounts"></div>
                            <div class="line"></div>
                            <div class="sunucretler">
                                <h3><span><?php echo __("website/osteps/total-amount"); ?>: <strong id="total_amount">0</strong></span></h3>
                            </div>
                        </div>
                    </div>

                    <a class="gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>"}' href="javascript:void(0);"><?php echo __("website/osteps/continue-button2"); ?></a>
                    <div class="clear"></div>
                    <div style="text-align: center; margin-top: 5px; display: none;" id="result" class="error"></div>
                </div>
                <script type="text/javascript">
                    $(document).ready(function(){
                        var changes = true;
                        ReloadOrderSummary();

                        $("#StepForm1").change(function(){
                            changes = true;
                        });
                        setInterval(function(){
                            if(changes)
                            {
                                ReloadOrderSummary();
                                changes = false;
                            }
                        },500);
                    });

                    function ReloadOrderSummary(){
                        var form_data = $("#StepForm1").serialize();
                        form_data = "OrderSummary=1&"+form_data;
                        var request = MioAjax({
                            action: "<?php echo $links["step"]; ?>",
                            method: "POST",
                            data:form_data
                        },true,true);

                        request.done(function (result){
                            if(result){
                                var solve = getJson(result),content='';
                                if(solve){
                                    if(solve.status == "successful"){
                                        $("#service_amounts").html('');
                                        if(solve.data != undefined){
                                            $(solve.data).each(function(key,item){
                                                content = '<span>- ';
                                                content += item.name;
                                                content += '\t<strong>'+item.amount+'</strong>';
                                                content += '</span>';
                                                $("#service_amounts").append(content);
                                            });
                                        }

                                        if(solve.total_amount != undefined)
                                            $("#total_amount").html(solve.total_amount);
                                    }else
                                        console.log(solve);
                                }else console.log(result);
                            }else console.log("Result not found");
                        });

                    }
                </script>

            </div>
        </form>
        <script type="text/javascript">
            function StepForm1_submit(result) {
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
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
                        }else if(solve.status == "successful")
                            window.location.href = solve.redirect;
                    }else
                        console.log(result);
                }
            }
        </script>
    <?php endif; ?>
</div>
</div>
