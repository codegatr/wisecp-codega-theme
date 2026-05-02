<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "order-steps-domain",
        'jquery-ui',
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
            <div class="cdg-domain-badge"><i class="bi bi-globe2"></i> Domain Sipariş Adımları</div>
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

                    <?php
                        if(isset($tlds) && $tlds)
                        foreach($tlds AS $t)
                        {
                            if(isset($t['requirements']) && $t['requirements'])
                            {
                                ?>
                                <tr>
                                    <td colspan="2" bgcolor="#ebebeb"><strong><?php echo $t["domain"]; ?></strong> - <?php echo __("website/osteps/necessary-information3"); ?></td>
                                </tr>
                                <?php
                                if(isset($t["tinfo"]["required_docs_info"]) && $t["tinfo"]["required_docs_info"])
                                {
                                    ?>
                                    <tr>
                                        <td colspan="2" style="border: none;">
                                            <?php echo $t["tinfo"]["required_docs_info"]; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                foreach($t["requirements"] AS $req_id => $requirement)
                                {
                                    ?>
                                    <tr>
                                        <td width="50%">
                                            <?php if(isset($requirement["required"]) && $requirement["required"]){ ?><span class="zorunlu">*</span><?php } ?>

                                            <label for="requirement-<?php echo $req_id; ?>">
                                                <strong><?php echo $requirement["name"]; ?></strong>
                                                <?php if(isset($requirement["description"]) && $requirement["description"]): ?>
                                                    <br>
                                                    <span style="font-size: 14px;"><?php echo nl2br($requirement["description"]); ?></span>
                                                <?php endif; ?>
                                            </label>

                                        </td>
                                        <td width="50%">

                                            <?php
                                                if($requirement["type"] == "text"){
                                                    ?>
                                                    <input type="text" name="requirements[<?php echo $t["tld"]; ?>][<?php echo $req_id; ?>]" id="requirement-<?php echo $t["tld"]; ?>-<?php echo $req_id; ?>">
                                                    <?php
                                                }
                                                elseif($requirement["type"] == "password"){
                                                    ?>
                                                    <input type="password" name="requirements[<?php echo $t["tld"]; ?>][<?php echo $req_id; ?>]" id="requirement-<?php echo $t["tld"]; ?>-<?php echo $req_id; ?>">
                                                    <?php
                                                }
                                                elseif($requirement["type"] == "file")
                                                {
                                                    ?>
                                                    <input type="file" name="requirement-<?php echo $t["tld"]; ?>-<?php echo $req_id; ?>" id="requirement-<?php echo $t["tld"]; ?>-<?php echo $req_id; ?>">
                                                    <?php
                                                }
                                                elseif($requirement["type"] == "select")
                                                {
                                                    ?>
                                                    <select name="requirements[<?php echo $t["tld"]; ?>][<?php echo $req_id; ?>]" id="requirement-<?php echo $t["tld"]; ?>-<?php echo $req_id; ?>">
                                                        <option value=""><?php echo __("website/osteps/select-your-option"); ?></option>
                                                        <?php
                                                            foreach ($requirement["options"] AS $k=>$v)
                                                            {
                                                                ?>
                                                                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                                <?php
                                                            }
                                                        ?>
                                                    </select>
                                                    <?php
                                                }
                                            ?>

                                        </td>
                                    </tr>
                                    <?php
                                }

                            }
                        }
                    ?>





                    <tr>
                        <td style="border:none;" align="center" colspan="2">
                            <a href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>"}'><strong><?php echo __("website/osteps/continue-button"); ?> <i class="ion-android-arrow-dropright"></i></strong></a>
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
                                    alert_error(solve.message,{timer:3000});
                            }else if(solve.status == "successful"){
                                if(solve.redirect != undefined && solve.redirect != '') window.location.href = solve.redirect;
                            }
                        }else
                            console.log(result);
                    }
                }
            </script>



        </div>
    <?php endif; ?>

    <?php if($step == "hosting"): ?>
        <div class="pakettitle" style="margin-top:0px;">
            <h1><strong><?php echo __("website/osteps/identify-your-web-hosting"); ?></strong></h1>
            <div class="line"></div>
            <h2><?php echo __("website/osteps/identify-your-web-hosting-note2"); ?></h2>
        </div>


        <div class="clear"></div>
        <div class="siparisbilgileri">

            <div class="domainsec">
                <div id="accordion">


                    <h3><strong><?php echo __("website/osteps/our-web-hosting-packets"); ?></strong></h3>
                    <div>
                        <table width="100%" border="0" align="center">
                            <tr>
                                <td style="border:none;" colspan="2">
                                    <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm1">
                                        <?php echo Validation::get_csrf_token('order-steps'); ?>

                                        <input type="hidden" name="type" value="selection">
                                        <div class="alanadisorgu">
                                            <select name="selection">
                                                <option value=""><?php echo __("website/osteps/select-your-hosting"); ?></option>
                                                <?php
                                                    if(isset($hosting_list) && is_array($hosting_list) && $hosting_list){
                                                        foreach($hosting_list AS $row){
                                                            $products = isset($row["products"]) ? $row["products"] : [];
                                                            if($products){
                                                                ?>
                                                                <optgroup label="<?php echo $row["title"]; ?>">
                                                                    <?php
                                                                        foreach($products AS $p){
                                                                            $creation_info = isset($p["module_data"]["create_account"]) ? $p["module_data"]["create_account"] : $p["module_data"];
                                                                            $reseller   = isset($creation_info["reseller"]);
                                                                            $d_limit    = isset($creation_info["disk_limit"]) ? $creation_info["disk_limit"] : 'unlimited';
                                                                            $dlimit = __("website/osteps/unlimited-disk");
                                                                            if($reseller)
                                                                                $dlimit = $d_limit != "unlimited" && $d_limit != 0 ? FileManager::formatByte(FileManager::converByte(((int) $d_limit)."MB")) : __("website/osteps/unlimited-disk");
                                                                            elseif(isset($p["options"]["disk_limit"]))
                                                                                $dlimit = $p["options"]["disk_limit"] != "unlimited" && $p["options"]["disk_limit"] != 0 ? FileManager::formatByte(FileManager::converByte(( (int) $p["options"]["disk_limit"])."MB")) : __("website/osteps/unlimited-disk");
                                                                            $price = Money::formatter_symbol($p["price"]["amount"],$p["price"]["cid"],!$p["override_usrcurrency"]);
                                                                            if($p["price"]["amount"]<=0)
                                                                                $price = ___("needs/free-amount");
                                                                            $disk   = isset($p["options"]["disk_limit"]) ? $dlimit." - " : '';
                                                                            $name = $p["title"]." (".$disk.$price.")";
                                                                            ?>
                                                                            <option value="<?php echo $p["id"]; ?>"><?php echo $name; ?></option>
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                </optgroup>
                                                                <?php
                                                            }
                                                            if(isset($row["categories"]) && is_array($row["categories"]) && $row["categories"]){
                                                                foreach($row["categories"] AS $r){
                                                                    $products2 = isset($r["products"]) ? $r["products"] : [];
                                                                    ?>
                                                                    <optgroup label="<?php echo $row["title"]." - ".$r["title"]; ?>">
                                                                        <?php
                                                                            foreach($products2 AS $p2){
                                                                                $creation_info = isset($p2["module_data"]["create_account"]) ? $p2["module_data"]["create_account"] : $p2["module_data"];
                                                                                $reseller   = isset($creation_info["reseller"]);
                                                                                $d_limit    = isset($creation_info["disk_limit"]) ? $creation_info["disk_limit"] : 'unlimited';
                                                                                $dlimit = __("website/osteps/unlimited-disk");
                                                                                if($reseller)
                                                                                    $dlimit = $d_limit != "unlimited" && $d_limit != 0 ? FileManager::formatByte(FileManager::converByte((int) ($d_limit)."MB")) : __("website/osteps/unlimited-disk");
                                                                                elseif(isset($p2["options"]["disk_limit"]))
                                                                                    $dlimit = $p2["options"]["disk_limit"] != "unlimited" && $p2["options"]["disk_limit"] != 0 ? FileManager::formatByte(FileManager::converByte(((int) $p2["options"]["disk_limit"])."MB")) : __("website/osteps/unlimited-disk");
                                                                                $price = Money::formatter_symbol($p2["price"]["amount"],$p2["price"]["cid"],!$p2["override_usrcurrency"]);
                                                                                if($p2["price"]["amount"]<=0)
                                                                                    $price = ___("needs/free-amount");
                                                                                $disk   = isset($p2["options"]["disk_limit"]) ? $dlimit." - " : '';
                                                                                $name = $p2["title"]." (".$disk.$price.")";
                                                                                ?>
                                                                                <option value="<?php echo $p2["id"]; ?>"><?php echo $name; ?></option>
                                                                                <?php
                                                                            }
                                                                        ?>
                                                                    </optgroup>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <a href="javascript:void(0);" class="gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"StepForm1_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/osteps/use-button"); ?></a>

                                            <h5><a href="<?php echo $hosting_link; ?>" target="_blank"><?php echo __("website/osteps/all-hosting-packages"); ?></a></h5>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <h3><strong><?php echo __("website/osteps/idont-want-hosting"); ?></strong></h3>
                    <div>
                        <table width="100%" border="0" align="center">
                            <tr>
                                <td style="border:none;" colspan="2">
                                    <form action="<?php echo $links["step"]; ?>" method="post" id="StepForm2">
                                        <?php echo Validation::get_csrf_token('order-steps'); ?>

                                        <input type="hidden" name="type" value="none">
                                        <table width="100%" border="0" align="center">
                                            <tr>
                                                <td style="border:none;" colspan="2">
                                                    <div class="alanadisorgu">
                                                        <h5 style="float:none;"><?php echo __("website/osteps/idont-want-hosting-note"); ?></h5>
                                                        <input name="ns1" type="text"  placeholder="<?php echo __("website/osteps/nohostingns1"); ?>" value="<?php echo isset($dns["ns1"]) ? $dns["ns1"] : ''; ?>">
                                                        <input name="ns2" type="text"  placeholder="<?php echo __("website/osteps/nohostingns2"); ?>" value="<?php echo isset($dns["ns2"]) ? $dns["ns2"] : ''; ?>">
                                                        <input name="ns3" type="text"  placeholder="<?php echo __("website/osteps/nohostingns3"); ?>" value="<?php echo isset($dns["ns3"]) ? $dns["ns3"] : ''; ?>">
                                                        <input name="ns4" type="text"  placeholder="<?php echo __("website/osteps/nohostingns4"); ?>" value="<?php echo isset($dns["ns4"]) ? $dns["ns4"] : ''; ?>">
                                                        <strong><a href="javascript:void(0);" class="btn mio-ajax-submit" mio-ajax-options='{"result":"StepForm2_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/osteps/nohostingbtn"); ?></a></strong>

                                                        <div class="error" id="result" style="display: none;  margin-top: 15px;"></div>

                                                    </div>
                                                </td>
                                            </tr>
                                        </table>


                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

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
                                alert_error(solve.message,{timer:3000});
                        }else if(solve.status == "successful"){
                            if(solve.redirect != undefined && solve.redirect != '')
                                window.location.href = solve.redirect;
                        }
                    }else
                        console.log(result);
                }
            }

            function StepForm2_submit(result) {
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "error"){
                            if(solve.for != undefined && solve.for != ''){
                                $("#StepForm2 "+solve.for).focus();
                                $("#StepForm2 "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                $("#StepForm2 "+solve.for).change(function(){
                                    $(this).removeAttr("style");
                                });
                            }
                            if(solve.message != undefined && solve.message != '')
                                alert_error(solve.message,{timer:3000});
                        }else if(solve.status == "successful"){
                            if(solve.redirect != undefined && solve.redirect != '')
                                window.location.href = solve.redirect;
                        }
                    }else
                        console.log(result);
                }
            }
        </script>
    <?php endif; ?>


</div>
</div>
