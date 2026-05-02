
<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "basket-pay",
    ];

    $currency_symbols = [];
    foreach(Money::getCurrencies() AS $currency){
        $symbol = $currency["prefix"] != '' ? trim($currency["prefix"]) : trim($currency["suffix"]);
        if(!$symbol) $symbol = $currency["code"];
        $currency_symbols[$symbol] = $currency["id"];
    }

?>
<style>
/* Basket Codega Override CSS */
#wrapper, .basket_wrapper { background:#f8fafc; padding:32px 0 60px; min-height:60vh; }
#wrapper .pakettitle, .basket-title { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:24px 28px; margin-bottom:20px; }
#wrapper .pakettitle h1 { color:#0f172a; font-size:24px; font-weight:800; margin:0 0 6px; }
#wrapper .pakettitle h2 { color:#64748b; font-size:14px; font-weight:500; margin:0; }

#wrapper .siparisbilgileri, #wrapper .basket-content { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:24px; margin-bottom:20px; }

#wrapper .btn, #wrapper .gonderbtn, #wrapper button[type=submit] { display:inline-flex; align-items:center; gap:8px; padding:13px 26px; border-radius:10px; font-size:14px; font-weight:700; background:linear-gradient(135deg,#1e40af,#3b82f6)!important; color:#fff!important; border:0; cursor:pointer; text-decoration:none; }
#wrapper .btn:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(30,64,175,0.25); }

#wrapper table { background:#fff; border-radius:12px; overflow:hidden; }
#wrapper table th { background:#f8fafc; color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:2px solid #e2e8f0; }
#wrapper table td { padding:14px 16px; border-bottom:1px solid #f1f5f9; color:#0f172a; }

#wrapper input[type="text"], #wrapper input[type="email"], #wrapper input[type="tel"], #wrapper input[type="password"], #wrapper select, #wrapper textarea { width:100%; padding:12px 14px; border:2px solid #e2e8f0; border-radius:10px; font-family:inherit; font-size:14px; color:#0f172a; background:#fff; }
#wrapper input:focus, #wrapper select:focus, #wrapper textarea:focus { border-color:#1e40af; outline:none; box-shadow:0 0 0 3px rgba(30,64,175,0.10); }

#wrapper label { font-size:13px; font-weight:600; color:#0f172a; }
#wrapper .clear { clear:both; }
#wrapper .error { padding:12px; background:#fee2e2; border:1px solid #fecaca; border-radius:8px; color:#991b1b; font-weight:600; margin:8px 0; }
#wrapper .info, #wrapper .success { padding:12px; background:#dcfce7; border:1px solid #bbf7d0; border-radius:8px; color:#14532d; }
</style>

<section class="cdg-page-hero" style="padding:40px 0;">
    <div class="cdg-page-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-page-hero-content" style="text-align:center;">
            <div class="cdg-domain-badge"><i class="bi bi-shield-lock-fill"></i> Ödeme</div>
        </div>
    </div>
</section>

<div class="cdg-container">

<style type="text/css">
    .header {
        z-index:99;
    }
</style>
<script type="text/javascript">
    var currency_symbols = <?php echo Utility::jencode($currency_symbols); ?>;

    function amount_divider(str){
        var visible_amount      = str;
        var split_amount        = visible_amount.split(" ");
        var amount_symbol       = '';
        var amount_symbol_pos   = '';
        var split_amount_last   = split_amount.length-1;

        if(currency_symbols[split_amount[0]]){
            amount_symbol_pos   = 'left';
            amount_symbol       = split_amount[0];
            split_amount.shift();
            visible_amount      = split_amount.join(" ");
        }else if(currency_symbols[split_amount[split_amount_last]]){
            amount_symbol_pos   = 'right';
            amount_symbol       = split_amount[split_amount_last];
            split_amount.pop();
            visible_amount      = split_amount.join(" ");
        }
        return {
            amount      : visible_amount,
            symbol_pos  : amount_symbol_pos,
            symbol      : amount_symbol
        };
    }

    function OrderSummary(options) {

        var address = "<?php echo $checkout["data"]["user_data"]["address"]["id"]; ?>";
        var sendbta = <?php echo $checkout["data"]["sendbta"] ? "1" : "0"; ?>;
        var pmethod = "<?php echo $checkout["data"]["pmethod"]; ?>";
        var pay = 0;

        var request = MioAjax({
            action: "<?php echo $links["bring"]."order-summary"; ?>",
            method: "POST",
            data: {
                payment:1,
                address:address,
                sendbta:sendbta,
                pmethod:pmethod,
                pay:pay
            },
        },true,true);

        request.done(function(result){
            var solve = false,content = '';
            if(result){
                solve = getJson(result);
                if(solve){

                    if(solve.type != undefined && solve.type == "pay"){

                        if(solve.status == "successful"){
                            if(solve.redirect != undefined && solve.redirect != '')
                                window.location.href = solve.redirect;
                        }else if(solve.status == "error"){
                            if(solve.message != undefined && solve.message != ''){
                                $("#pay_result").fadeIn(200).html(solve.message);
                            }else
                                $("#pay_result").fadeOut(200).html('');
                        }
                    }else{

                        if(solve.total_amount != undefined){
                            var amount_info = amount_divider(solve.total_amount);
                            $("#total-amount").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                        }
                        else $("#total-amount").html('-');

                        if(solve.dealership_discounts != undefined && solve.dealership_discounts.length){
                            $("#dealership_discounts").html('').fadeIn(1);
                            var d_content = '';
                            var d_see = '<?php echo __("website/basket/dealership-discount", ['']); ?>';
                            var d_seee = '';
                            $(solve.dealership_discounts).each(function(dkey,ditem){
                                var amount_info = amount_divider(ditem.amount);
                                d_content  = '<tr>';
                                d_seee     = d_see.replace('{rate}',ditem.rate);
                                d_content += '<td><strong>'+d_seee+'</strong>'+(ditem.name !== null ? '<br>('+ditem.name+')' : '')+' </td>';
                                d_content += '<td align="right"><h5><div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> -'+amount_info.amount+'</div></h5></td>';
                                d_content += '</tr>';
                                $("#dealership_discounts").append(d_content);
                            });
                        }else $("#dealership_discounts").html('').fadeOut(1);

                        if(solve.coupon_discounts != undefined && solve.coupon_discounts.length){
                            $("#coupon_discounts").html('').fadeIn(1);
                            var d_content = '';
                            var d_see = '<?php echo __("website/basket/coupon-discount", ['']); ?>';
                            var d_seee = '';
                            $(solve.coupon_discounts).each(function(dkey,ditem){
                                d_content  = '<tr>';
                                d_seee     = d_see.replace('{value}',ditem.dvalue);
                                d_content += '<td><strong>'+d_seee+'</strong><br>('+ditem.name+')</td>';
                                var amount_info = amount_divider(ditem.amount);
                                d_content += '<td align="right"><h5><div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> -'+amount_info.amount+'</div></h5></td>';
                                d_content += '</tr>';
                                $("#coupon_discounts").append(d_content);
                            });
                        }else $("#coupon_discounts").html('').fadeOut(1);


                        if(solve.taxation != undefined && solve.taxation){
                            $("#tax_content").fadeIn(1);
                            var see,see_text;
                            see     = $("#tax-see");
                            see_text = see.html();
                            see_text = see_text.replace('{rates}',solve.tax_rates ?? '');
                            see_text = see_text.replace('{rate}',solve.tax_rate);
                            see.html(see_text);
                            if(solve.total_tax_amount != undefined){
                                var amount_info = amount_divider(solve.total_tax_amount);
                                $("#tax-amount").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                            }else $("#tax-amount").html('-');
                        }else $("#tax_content").fadeOut(1);

                        if(solve.total_amount_payable != undefined){
                            var amount_info = amount_divider(solve.total_amount_payable);
                            $("#total-amount-payable").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                        }
                        else
                            $("#total-amount-payable").html('-');

                        if(solve.sendbta_visible != undefined && solve.sendbta_visible){
                            if(solve.sendbta_price != undefined && solve.sendbta_price != ''){
                                $("#sendbta_amount").html("(+"+solve.sendbta_price+")");

                                if(solve.sendbta_selected != undefined && solve.sendbta_selected){
                                    $("#sendbta_select").fadeIn(100);
                                    var amount_info = amount_divider(solve.sendbta_price);
                                    $("#sendbta_amount2").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                                }else{
                                    $("#sendbta-checkbox").prop("checked",false);
                                    $("#sendbta_select").fadeOut(100);
                                }

                            }
                            $("#sendbta").fadeIn(100);
                        }else{
                            $("#sendbta_select").fadeOut(100);
                            $("#sendbta-checkbox").prop("checked",false);
                            $("#sendbta").fadeOut(1);
                        }

                        if(solve.pm_commission != undefined && solve.pm_commission != ''){
                            $("#pmethod_name").html(solve.pm_commission);
                            var amount_info = amount_divider(solve.pm_commission_amount);

                            $("#pm_commission_amount").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                            $("#pmethod_select").fadeIn(100);
                        }else{
                            $("#pmethod_select").fadeOut(100);
                            $("#pmethod_name").html('');
                            $("#pm_commission_amount").html('');
                        }

                        if(address == null || address == '' || pmethod == null || pmethod == ''){
                            $("#pay_button").fadeOut(250,function(){
                                $("#block_button").fadeIn(250);
                            });
                        }else{
                            $("#block_button").fadeOut(250,function(){
                                $("#pay_button").fadeIn(250);
                            });
                        }

                    }

                }else console.log("Can not resolved : "+result);
            }else console.log("Failed loaded in basket to order summary");
        });

    }

    $(document).ready(function(){
        OrderSummary();
    });
</script>

<style>
.sepetsipinfo h5 {
    color: var(--color-primary-two);
}
.sepetsipinfo .totalamountinfo h5 {
    color: var(--color-primary);
}
.sepetbaslik {
    background: var(--color-primary-two);
    -webkit-border-radius: 10px;
    -moz-border-radius: 2px;
    border-radius: 5px;
}
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
.checkbox-custom:checked+.checkbox-custom-label:before {
    background: var(--color-primary-two);
}
.yesilbtn {
    background: var(--color-primary-two);
}
.gonderbtn {
    border-radius: 10px;
    background: var(--color-primary);
}
.sepetright .gonderbtn {
    background: var(--color-primary);
}
.yesilbtn:hover {
    background: var(--color-primary-two);
}
.sepetright .gonderbtn:hover {
    background: var(--color-primary-two);
}
.lbtn {
    border: 2px solid var(--color-primary-two);
    color: var(--color-primary-two);
}
.lbtn:hover {
    border: 2px solid var(--color-primary-two);
    color: #ffffff;
    background: var(--color-primary-two);
}
</style>

<div id="wrapper" style="margin-top: 30px;">

    <div class="sepet">

        <div class="sepetleft">

            <div class="sepetbaslik">
                <div style="padding:0px 15px;">
                    <div class="yuzde70"><?php echo $module->lang["option-name"]; ?></div>
                </div>
            </div>


            <div class="sepetlist">
                <div class="sepetlistcon">
                    <div class="faturabilgisi">

                        <?php echo $page; ?>

                        <div class="clear"></div>

                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="paymentlogos">
            <img class="plogos1" src="<?php echo $tadress;?>images/credit-cards.png">
            <img class="plogos2" src="<?php echo $tadress;?>images/ssl-secure.svg">
            <div class="clear"></div>
            <span><?php echo __("website/basket/basketsecnotice"); ?></span>
            </div>

        </div>

        <?php if($module): ?>
            <div class="sepetright">
                <div class="sepetrightshadow">
                    <div class="sepetbaslik">
                        <div style="padding:0px 12px;text-align:right;">
                            <?php echo __("website/basket/order-summary"); ?>
                        </div>
                    </div>

                    <div class="sepetrightcon">

                        <table class="sepetsipinfo" width="100%" border="0">
                            <tr>
                                <td><strong><?php echo __("website/basket/total-order-amount"); ?></strong></td>
                                <td align="right"><h5 id="total-amount"><?php echo Money::formatter_symbol(); ?></h5></td>
                            </tr>

                            <tbody id="dealership_discounts"></tbody>

                            <tbody id="coupon_discounts" style="display: none;"></tbody>

                            <tr id="sendbta_select" style="display: none;">
                                <td><strong><?php echo __("website/basket/send-bill-to-address"); ?></strong></td>
                                <td align="right"><h5 id="sendbta_amount2"></h5></td>
                            </tr>

                            <tr id="pmethod_select" style="display: none;">
                                <td><strong id="pmethod_name"></strong></td>
                                <td align="right"><h5 id="pm_commission_amount"></h5></td>
                            </tr>

                            <tr id="tax_content" style="display:none;">
                                <td><strong id="tax-see"><?php echo __("website/basket/tax-amount",['']); ?></strong></td>
                                <td align="right"><h5 id="tax-amount">0</h5></td>
                            </tr>

                            <tr>
                                <td class="totalamountinfo" align="center" colspan="2">
                                    <strong><?php echo __("website/basket/total-amount-payable"); ?></strong><br>
                                    <h5 id="total-amount-payable">0</h5>
                                </td>
                            </tr>

                        </table>

                        <?php if($module->name == "BankTransfer"){ ?>
                            <hr>
                            <form action="<?php echo $module->links["notification"]; ?>" method="post" id="NotificationForm">
                                <div>
                                    <select name="bank">
                                        <option value=""><?php echo $_LANG["text1"]; ?></option>
                                        <?php
                                            $list = $module->accounts();
                                            foreach($list AS $item){
                                                ?><option value="<?php echo $item["id"]; ?>"><?php echo $item["name"]; ?></option><?php
                                            }
                                        ?>
                                    </select>
                                    <input name="sender_name" type="text" placeholder="<?php echo $_LANG["text2"]; ?>">
                                </div><div class="clear"></div>
                                <a class="green gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"NotificationForm_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}' href="javascript:void(0);"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $_LANG["text5"]; ?></a>
                                <br><span style="font-size: 14px;font-weight: bold;text-align: center;width: 100%;display: inline-block;margin: 25px 0px;"><?php echo $_LANG["text6"]; ?></span>

                                <div class="clear"></div>
                                <div id="result" class="error" style="text-align: center; display: none; margin-top: 5px;"></div>
                            </form>
                            <script type="text/javascript">
                                function NotificationForm_submit(result) {
                                    if(result != ''){
                                        var solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error"){
                                                if(solve.for != undefined && solve.for != ''){
                                                    $("#NotificationForm "+solve.for).focus();
                                                    $("#NotificationForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                    $("#NotificationForm "+solve.for).change(function(){
                                                        $(this).removeAttr("style");
                                                    });
                                                }
                                                if(solve.message != undefined && solve.message != '')
                                                    $("#NotificationForm #result").fadeIn(300).html(solve.message);
                                                else
                                                    $("#NotificationForm #result").fadeOut(300).html('');
                                            }else if(solve.status == "successful"){
                                                if(solve.redirect != undefined && solve.redirect != '') window.location.href = solve.redirect;
                                            }
                                        }else
                                            console.log(result);
                                    }
                                }
                            </script>
                        <?php } ?>
                        <div class="clear"></div>
                    </div>

                </div>

                <?php if(isset($links["back"])){ ?><br><a class="lbtn" href="<?php echo $links["back"]; ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo __("website/basket/turn-back"); ?></a><?php } ?>

                <div class="clear"></div>
            </div>
        <?php endif; ?>

    </div>
</div>
</div>
