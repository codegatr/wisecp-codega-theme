<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "basket",
    ];

    $currency_symbols = [];
    foreach(Money::getCurrencies() AS $currency){
        $symbol = $currency["prefix"] != '' ? trim($currency["prefix"]) : trim($currency["suffix"]);
        if(!$symbol) $symbol = $currency["code"];
        $currency_symbols[$symbol] = $currency["id"];
    }
?>
<style>
<style>
/* ============================================================
   CODEGA SEPET — Kurumsal Override CSS
   Yapı: .sepet > .sepetleft (item list) + .sepetright (özet)
   Item:  .sepetlist > .sepetlistcon > .uhinfo / .uhperiyod / .uhtutar / .uhsil
   ============================================================ */

/* Wrapper - sayfa arka planı */
#wrapper, .basket_wrapper {
    background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    padding: 24px 0 70px;
    min-height: 70vh;
}

/* === SARI "SEPETIM" buton bombesini KALDIR === */
.cdg-page-hero { display: none !important; }

/* === Layout: 2 sütunlu grid === */
#wrapper .sepet {
    display: grid;
    grid-template-columns: minmax(0,1fr) 380px;
    gap: 24px;
    align-items: start;
}
#wrapper .sepetleft, #wrapper .sepetright { float: none !important; width: auto !important; }
#wrapper .sepetright { position: sticky; top: 90px; }

@media (max-width: 1024px) {
    #wrapper .sepet { grid-template-columns: 1fr; }
    #wrapper .sepetright { position: relative; top: 0; }
}

/* === Sol sütun (item list) kart yapısı === */
#wrapper .sepetleft {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}

/* === Tablo başlığı (Ürün/Hizmet, Periyod, Tutar) === */
#wrapper .sepetleft > .sepetbaslik {
    background: linear-gradient(135deg, #2E3B4E 0%, #1e293b 100%);
    color: #fff;
    padding: 18px 24px !important;
    border-radius: 16px 16px 0 0;
}
#wrapper .sepetleft > .sepetbaslik > div {
    padding: 0 !important;
    display: grid;
    grid-template-columns: 1.5fr 0.8fr 0.6fr 40px;
    gap: 12px;
    align-items: center;
    color: #fff;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
#wrapper .sepetleft > .sepetbaslik .uhinfo,
#wrapper .sepetleft > .sepetbaslik .uhperiyod,
#wrapper .sepetleft > .sepetbaslik .uhtutar {
    color: #cbd5e1;
    width: auto !important;
    float: none !important;
    padding: 0 !important;
    text-align: left;
}
#wrapper .sepetleft > .sepetbaslik .uhperiyod { text-align: center; }
#wrapper .sepetleft > .sepetbaslik .uhtutar { text-align: right; }

/* === Sepet item kartları === */
#wrapper #item_list { padding: 0 !important; }
#wrapper .sepetlist {
    background: #fff;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    margin: 0 !important;
    padding: 0 !important;
    transition: background 0.15s ease;
    position: relative;
}
#wrapper .sepetlist:hover { background: #f8fafc; }
#wrapper .sepetlist:last-child { border-bottom: 0; }
#wrapper .sepetlistcon {
    display: grid;
    grid-template-columns: 1.5fr 0.8fr 0.6fr 40px;
    gap: 12px;
    align-items: center;
    padding: 18px 24px;
}

/* Ürün bilgisi (.uhinfo) */
#wrapper .sepetlist .uhinfo {
    width: auto !important;
    float: none !important;
    padding: 0 !important;
    text-align: left;
}
#wrapper .sepetlist .uhinfo h5 {
    margin: 0 0 4px;
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}
#wrapper .sepetlist .uhinfo h5 strong { font-weight: 700; color: #0f172a; }
#wrapper .sepetlist .uhinfo h4 {
    margin: 0 0 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    line-height: 1.3;
}
#wrapper .sepetlist .uhinfo h4 a {
    color: #2E3B4E;
    text-decoration: none;
    border-bottom: 1px dashed #cbd5e1;
    transition: color 0.15s;
}
#wrapper .sepetlist .uhinfo h4 a:hover { color: #00D3E5; border-color: #00D3E5; }

/* Domain/IP göstergesi */
#wrapper .sepetlist .uhinfo > .clear + * {
    display: inline-block;
    margin-top: 4px;
    padding: 3px 10px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 99px;
    font-size: 12px;
    color: #475569;
    font-family: ui-monospace, monospace;
}

/* Cart additional details (whois privacy, dns vb.) */
#wrapper .sepetlist .cart-additional-details {
    margin-top: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    align-items: center;
}
#wrapper .sepetlist .cart-additional-details a {
    font-size: 12px;
    color: #2E3B4E;
    text-decoration: none;
    padding: 4px 10px;
    border-radius: 6px;
    background: #f1f5f9;
    transition: background 0.15s;
}
#wrapper .sepetlist .cart-additional-details a:hover { background: #e2e8f0; }
#wrapper .sepetlist .cart-additional-details label { font-size: 12.5px !important; color: #475569 !important; }

/* Adds (eklentiler) */
#wrapper .sepetlist .uhinfo p {
    margin: 8px 0 0;
    font-size: 12.5px;
    color: #64748b;
    line-height: 1.7;
    background: #f8fafc;
    border-left: 3px solid #00D3E5;
    padding: 8px 12px;
    border-radius: 4px;
}
#wrapper .sepetlist .uhinfo p span { color: #0f172a; font-weight: 600; }

/* Periyod (.uhperiyod) - select kutusu */
#wrapper .sepetlist .uhperiyod {
    width: auto !important;
    float: none !important;
    padding: 0 !important;
    text-align: center;
}
#wrapper .sepetlist .uhperiyod select {
    width: 100% !important;
    padding: 9px 12px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #fff !important;
    font-size: 13px !important;
    color: #0f172a !important;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6'><path fill='%2364748b' d='M0 0l5 6 5-6z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 28px !important;
}
#wrapper .sepetlist .uhperiyod select:focus { border-color: #2E3B4E !important; outline: none; box-shadow: 0 0 0 3px rgba(46,59,78,0.08); }
#wrapper .sepetlist .uhperiyod h5 {
    margin: 0;
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    text-align: center;
}

/* Tutar (.uhtutar) */
#wrapper .sepetlist .uhtutar {
    width: auto !important;
    float: none !important;
    padding: 0 !important;
    text-align: right;
}
#wrapper .sepetlist .uhtutar h4 {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
#wrapper .sepetlist .uhtutar h4 strong { font-weight: 800; }
#wrapper .sepetlist .uhtutar .currposleft,
#wrapper .sepetlist .uhtutar .currposright {
    font-size: 14px;
    font-weight: 700;
    color: #64748b;
    margin-right: 2px;
    font-style: normal;
}
#wrapper .sepetlist .uhtutar .currposright { margin-left: 2px; margin-right: 0; }

/* Sil butonu (.uhsil) */
#wrapper .sepetlist .uhsil {
    width: auto !important;
    float: none !important;
    padding: 0 !important;
    text-align: center;
}
#wrapper .sepetlist .uhsil a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: transparent;
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.15s;
}
#wrapper .sepetlist .uhsil a:hover {
    background: #fee2e2;
    color: #dc2626;
    transform: scale(1.05);
}
#wrapper .sepetlist .uhsil a i { font-size: 14px; }

/* Row label (indirim/promosyon etiketi) */
#wrapper .sepetlist .row-label {
    position: absolute;
    top: 8px;
    right: 60px;
    padding: 3px 9px;
    border-radius: 99px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 1;
}
#wrapper .sepetlist .row-label.green-label {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
}

/* === Boş sepet === */
#wrapper #empty_list {
    background: #fff !important;
    border: none !important;
    color: #64748b !important;
    text-align: center;
    padding: 60px 20px !important;
    margin: 0 !important;
}
#wrapper #empty_list i {
    font-size: 56px;
    color: #cbd5e1;
    margin-bottom: 16px;
    display: block;
}
#wrapper #empty_list h4 {
    font-size: 16px;
    font-weight: 600;
    color: #475569;
    margin: 0;
}

/* Loader */
#wrapper #basket_loader { padding: 40px 0; }
#wrapper .spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #2E3B4E;
    border-radius: 50%;
    animation: cdgBspin 0.9s linear infinite;
    margin: 0 auto;
    display: block;
}
@keyframes cdgBspin { to { transform: rotate(360deg); } }

/* === "Alışverişe devam et" butonu === */
#wrapper #continueshopbtn {
    display: inline-flex !important;
    align-items: center;
    gap: 8px;
    padding: 11px 22px !important;
    background: #fff !important;
    color: #2E3B4E !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.15s !important;
    margin: 14px 24px 18px !important;
}
#wrapper #continueshopbtn:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15,23,42,0.05);
}

/* === Ödeme logoları === */
#wrapper .paymentlogos {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 14px 24px;
    text-align: center;
    border-radius: 0 0 16px 16px;
}
#wrapper .paymentlogos img { height: 22px; margin: 0 6px; vertical-align: middle; opacity: 0.8; }
#wrapper .paymentlogos .plogos1, #wrapper .paymentlogos .plogos2 { display: inline-block; }
#wrapper .paymentlogos span {
    display: block;
    margin-top: 8px;
    font-size: 11.5px;
    color: #94a3b8;
}

/* === Sağ sütun (sipariş özeti) === */
#wrapper .sepetright .sepetrightshadow {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
#wrapper .sepetright > .sepetrightshadow > .sepetbaslik {
    background: linear-gradient(135deg, #2E3B4E 0%, #1e293b 100%);
    color: #fff;
    padding: 16px 20px !important;
}
#wrapper .sepetright > .sepetrightshadow > .sepetbaslik > div {
    color: #fff !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    text-align: left !important;
    padding: 0 !important;
}
#wrapper .sepetright > .sepetrightshadow > .sepetbaslik > div i {
    color: #00D3E5 !important;
    font-size: 16px;
    margin-right: 8px !important;
}

#wrapper .sepetrightcon { padding: 18px 20px !important; }

/* Sipariş özet tablosu */
#wrapper .sepetsipinfo {
    width: 100%;
    border-collapse: collapse;
    background: transparent !important;
}
#wrapper .sepetsipinfo tr td {
    padding: 11px 0 !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: transparent !important;
    font-size: 13.5px;
    color: #475569;
}
#wrapper .sepetsipinfo tr td strong { color: #0f172a; font-weight: 600; }
#wrapper .sepetsipinfo tr td h5 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}
#wrapper .sepetsipinfo tr td h5 .currposleft,
#wrapper .sepetsipinfo tr td h5 .currposright {
    font-size: 12px;
    color: #64748b;
    margin: 0 2px;
    font-style: normal;
}

/* Toplam ödenecek tutar - büyük vurgu */
#wrapper .totalamountinfo {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
    border-radius: 12px;
    padding: 18px 16px !important;
    border-bottom: 0 !important;
    margin-top: 8px;
}
#wrapper .totalamountinfo strong {
    display: block;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #0c4a6e !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
}
#wrapper .totalamountinfo h5 {
    margin: 0 !important;
    font-size: 26px !important;
    font-weight: 800 !important;
    color: #0c4a6e !important;
    line-height: 1.1;
}
#wrapper .totalamountinfo h5 .currposleft,
#wrapper .totalamountinfo h5 .currposright {
    font-size: 18px !important;
    color: #0369a1 !important;
    font-weight: 700;
    margin: 0 3px;
    font-style: normal;
}

/* Kupon kodu */
#wrapper #use_coupon td {
    padding: 14px 0 !important;
    border-bottom: 0 !important;
    text-align: center;
}
#wrapper #use_coupon a {
    color: #0369a1;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
#wrapper #use_coupon a:hover { color: #2E3B4E; }
#wrapper .kuponkodu {
    margin-top: 10px;
    padding: 12px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
}
#wrapper #coupon_code {
    width: 100% !important;
    padding: 10px 14px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    font-size: 13px !important;
    font-family: ui-monospace, monospace !important;
    text-transform: uppercase;
}

/* Dealership / coupon discount satırları */
#wrapper #dealership_discounts td,
#wrapper #coupon_discounts td {
    color: #059669 !important;
}
#wrapper #dealership_discounts td strong,
#wrapper #coupon_discounts td strong { color: #047857 !important; }
#wrapper #dealership_discounts h5,
#wrapper #coupon_discounts h5 { color: #059669 !important; }

/* Tax */
#wrapper #tax_content td { color: #64748b !important; font-size: 12.5px; }
#wrapper #tax_content td strong { color: #475569 !important; }

/* OrderSummary loader */
#wrapper #OrderSummary_loader { padding: 30px 0; text-align: center; }

/* === DEVAM ET BUTONLARI - tek aktif/pasif === */
#wrapper #continue_go,
#wrapper #continue_block {
    display: block !important;
    width: 100% !important;
    margin: 14px 0 0 !important;
    padding: 14px 22px !important;
    border-radius: 12px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    text-align: center;
    text-decoration: none !important;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    transition: all 0.18s !important;
    box-sizing: border-box;
}
#wrapper #continue_go {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #fff !important;
    border: 0 !important;
    box-shadow: 0 6px 16px rgba(16,185,129,0.28);
}
#wrapper #continue_go:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(16,185,129,0.36);
}
#wrapper #continue_go i { color: #d1fae5 !important; font-size: 16px; margin-right: 6px; }

#wrapper #continue_block {
    background: #f1f5f9 !important;
    color: #94a3b8 !important;
    border: 1.5px dashed #cbd5e1 !important;
    cursor: not-allowed !important;
    pointer-events: none;
    box-shadow: none !important;
}
#wrapper #continue_block:hover { transform: none !important; box-shadow: none !important; }
#wrapper #continue_block i { color: #94a3b8 !important; }

/* Genel form/select/btn override (gerekirse) */
#wrapper input[type="text"], #wrapper input[type="email"], #wrapper input[type="tel"],
#wrapper input[type="password"], #wrapper select, #wrapper textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-family: inherit;
    font-size: 13px;
    color: #0f172a;
    background: #fff;
}
#wrapper input:focus, #wrapper select:focus, #wrapper textarea:focus {
    border-color: #2E3B4E;
    outline: none;
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}

/* Hata/info kutuları */
#wrapper .error {
    padding: 12px 14px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #991b1b;
    font-size: 12.5px;
    font-weight: 600;
}
#wrapper .blue-info, #wrapper .red-info {
    padding: 14px;
    border-radius: 10px;
    margin-bottom: 14px;
}
#wrapper .blue-info { background: #eff6ff; border: 1px solid #dbeafe; color: #1e40af; }
#wrapper .red-info  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

/* Modal stilleri */
#wrapper .modal-foot-btn {
    padding: 14px 20px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    text-align: right;
}
#wrapper .padding20 { padding: 20px; }
#wrapper .padding15 { padding: 15px; }
#wrapper .green {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    color: #fff !important;
}
#wrapper .lbtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

/* Custom checkbox (Whois Privacy) */
#wrapper .checkbox-custom {
    width: 18px !important;
    height: 18px !important;
    accent-color: #2E3B4E;
    cursor: pointer;
}
#wrapper .checkbox-custom-label {
    cursor: pointer;
    user-select: none;
    margin-left: 6px;
}

/* clear */
.clear { clear: both; }
</style>

<!-- ==================== SEPET HEADER (CODEGA) ==================== -->
<div class="cdg-container" style="margin-top:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:14px;">
        <div>
            <h1 style="margin:0 0 4px;font-size:22px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-cart-fill" style="color:#00D3E5;"></i>
                Sepetim
                <span class="basket-count" style="display:inline-flex;align-items:center;justify-content:center;background:#2E3B4E;color:#fff;font-size:12px;font-weight:700;width:26px;height:26px;border-radius:99px;margin-left:4px;">0</span>
            </h1>
            <p style="margin:0;font-size:13px;color:#64748b;">Siparişinizi gözden geçirin ve ödeme adımına ilerleyin</p>
        </div>
        <div style="display:flex;align-items:center;gap:18px;font-size:12px;color:#64748b;">
            <div style="display:flex;align-items:center;gap:6px;"><i class="bi bi-shield-lock" style="color:#10b981;font-size:14px;"></i> SSL Korumalı</div>
            <div style="display:flex;align-items:center;gap:6px;"><i class="bi bi-credit-card" style="color:#2E3B4E;font-size:14px;"></i> Güvenli Ödeme</div>
        </div>
    </div>
</div>

<div class="cdg-container">

<script type="text/javascript">
    var currency_symbols = <?php echo Utility::jencode($currency_symbols); ?>;
    var ns_details      = {};
    var whois_details   = {};
    var domain_names    = {};

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

    function set_wprivacy(element,id){
        var check = $(element).prop("checked");
        var request = MioAjax({
            action:"<?php echo ($links["bring"] ?? '')."set-wprivacy"; ?>",
            method: "POST",
            data:{id:id,check:check}
        },true,true);
        request.done(function(){
            OrderSummary();
        });
    }
    function change_selection_period(element,id){
        var value = $(element).val();
        var request = MioAjax({
            action:"<?php echo $links["bring"]."change-selection-period"; ?>",
            method: "POST",
            data:{id:id,selection:value}
        },true,true);
        request.done(function(){
            ItemList();
            OrderSummary();
        });
    }
    function change_selection_year(element,id){
        var value = $(element).val();
        var request = MioAjax({
            action:"<?php echo $links["bring"]."change-selection-year"; ?>",
            method: "POST",
            data:{id:id,selection:value}
        },true,true);
        request.done(function(){
            ItemList();
            OrderSummary();
        });
    }
    function ItemList(){
        $("#item_list").html('');
        $("#basket_loader").fadeIn(200);
        var request = MioAjax({
            action: "<?php echo $links["bring"]."item-list"; ?>",
            method: "POST"
        },true,true);

        request.done(function(result){
            var solve = false,content = '';
            if(result){
                $("#basket_loader").fadeOut(1);
                solve = getJson(result);
                if(solve){
                    if(solve.status == "none"){
                        $("#item_list").fadeOut(400).html('');
                        $("#empty_list").fadeIn(400);
                        $(".basket-count").html('0');
                        $("#coupon_code").attr("disabled",true);
                    }else if(solve.status == "listing"){
                        $("#coupon_code").attr("disabled",false);
                        if(solve.count != undefined) $(".basket-count").html(solve.count);
                        if(solve.data != undefined){
                            $("#item_list").fadeOut(1).html('');
                            var size = solve.data.length;
                            var rank = 0;
                            var selection_period = '';

                            $(solve.data).each(function(key,item){
                                rank++;
                                selection_period = '';

                                if(item.ns_details !== undefined) ns_details[item.id] = item.ns_details;
                                if(item.whois_details !== undefined) whois_details[item.id] = item.whois_details;
                                if(item.product_type !== undefined && item.product_type === "domain")
                                    domain_names[item.id] = item.name;

                                if(item.selection_period !== undefined){
                                    selection_period +=
                                        '<select onchange="change_selection_period(this,'+item.id+');">';
                                    $(item.selection_period).each(function(k,v){
                                        selection_period += '<option value="'+k+'"';
                                        if(item.selected_period !== undefined && item.selected_period === v.id)
                                            selection_period += ' selected';
                                        selection_period += '>';
                                        selection_period += v.period;
                                        selection_period +='</option>';
                                    });
                                    selection_period += '</select>';
                                }
                                else if(item.selection_year !== undefined){
                                    selection_period +=
                                        '<select onchange="change_selection_year(this,'+item.id+');">';
                                    $(item.selection_year).each(function(k,v){
                                        var k_i = k+1;
                                        selection_period += '<option value="'+v.year+'"';
                                        if(item.year !== undefined && parseInt(v.year) === parseInt(item.year))
                                            selection_period += ' selected';
                                        selection_period += '>';
                                        selection_period += v.period;
                                        selection_period +='</option>';
                                    });
                                    selection_period += '</select>';
                                }

                                content  = '<div class="sepetlist" id="basket-item-'+key+'">';
                                if(item.reduced != undefined && item.reduced != 0)
                                    content += '<div class="row-label green-label"><?php echo __("website/basket/reduced"); ?></div>';
                                if(item.promotion_applied != undefined)
                                    content += '<div class="row-label green-label"><?php echo __("website/basket/promotion-applied"); ?></div>';
                                content += '<div class="sepetlistcon">';
                                content += '<div class="uhinfo">';
                                content += '<h5><strong>'+item.name+'</strong></h5>';
                                if(item.category != undefined && item.category_route != undefined)
                                    content += '<h4><a href="'+item.category_route+'" target="_blank">'+item.category+'</a></h4>';
                                if(item.domain != undefined && item.domain != '')
                                    content += '<div class="clear"></div>('+item.domain+')';
                                else if(item.ip != undefined && item.ip != '')
                                    content += '<div class="clear"></div>('+item.ip+')';

                                if(item.visible_wprivacy != undefined && item.visible_wprivacy){
                                    var wprivacy_active = '';
                                    if(item.wprivacy != undefined && item.wprivacy)
                                        wprivacy_active = ' checked';

                                    content += '<div class="cart-additional-details">';

                                    content += '<input'+wprivacy_active+' class="checkbox-custom" type="checkbox" id="whois_privacy_'+key+'" onchange="set_wprivacy(this,'+item.id+');"><label for="whois_privacy_'+key+'" class="checkbox-custom-label" style="font-size:14px;"><?php echo __("website/basket/whois-privacy"); ?> (<strong>'+item.wprivacy_price+'</strong>)</label>';

                                    content += '</div>';

                                }

                                if(item.product_type !== undefined && item.product_type === 'domain')
                                {
                                    if(item.event_name !== undefined && (item.event_name === 'DomainNameRegisterOrder' || item.event_name === 'DomainNameTransferRegisterOrder'))
                                    {
                                        content += '<div class="cart-additional-details">';
                                        content += '<a href="javascript:open_whois_info('+item.id+');"><?php echo __("website/basket/whois-tx3"); ?> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                        content += '<a href="javascript:open_ns_info('+item.id+');"><?php echo __("website/basket/dns-tx3"); ?> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                        content += '</div>';
                                    }
                                }

                                if(item.adds != undefined && item.adds.length){
                                    content += "<div class='clear'></div><p>";
                                    $(item.adds).each(function(a_key,a_item){
                                        var pername = a_item.period;
                                        pername =  pername != '' ? " | "+pername : '';
                                        content += '- '+a_item.name+' <span>'+a_item.amount+''+pername+'</span><br>';
                                    });
                                    content += "</p>";
                                }

                                content += '</div>';
                                content += '<div class="uhperiyod">';
                                if(selection_period !== '')
                                    content += selection_period;
                                else
                                    content += '<H5>'+item.period_name+'</H5>';

                                if(item.reduced != undefined && item.reduced != 0){
                                    var replace1 = '<?php echo __("website/basket/reduced2"); ?>';
                                    replace1 = replace1.replace('{rate}',item.reduced);
                                    content += '<span STYLE="color:#81bc00;font-weight:bold;">('+replace1+')</span>';
                                }
                                content += '</div>';

                                content += '<div class="uhtutar">';
                                if(item.amount !== undefined) {
                                    var is_free = '';

                                    if(item.amount === '<?php echo ___("needs/free-amount"); ?>')
                                        is_free = ' style="color:#81bc00;"';

                                    var amount_info = amount_divider(item.amount);
                                    content += '<h4'+is_free+' class="amount_spot_view"><strong><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i>' + amount_info.amount + '</strong></h4>';
                                }
                                content += '</div>';

                                content += '<div class="uhsil">';
                                content += '<a title="<?php echo __("website/basket/delete-item"); ?>" href="javascript:deleteItem('+key+','+item.id+');void 0;"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                content += '</div>';

                                content += '<div class="clear"></div>';
                                content += '</div>';
                                content += '</div>';
                                $("#item_list").append(content);
                            });

                            if(size == rank){
                                $("#empty_list").slideUp(400);
                                $("#item_list").fadeIn(500);
                            }

                        }else $("#item_list").fadeIn(100).html("Empty List");
                    }
                }else console.log("Can not resolved : "+result);

            }else $("#basket_loader").addClass("error").html("Failed loaded in basket to item list");
        });

    }

    function deleteItem(index,id){
        var item = $("#basket-item-"+index);
        item.animate({opacity: 4}, 300);
        var request = MioAjax({
            action: "<?php echo $links["bring"]."delete-item"; ?>",
            method: "POST",
            data: {id:id}
        },true,true);

        request.done(function(result){
            if(result){
                var solve = getJson(result);
                if(solve){
                    if(solve.status == "successful"){

                        item.animate({backgroundColor:'#EEE',opacity:0}, 500,function () {
                            item.remove();
                            if($(".sepetlist").length==0){

                                $("#item_list").fadeOut(400).html('');
                                $("#empty_list").fadeIn(400);
                                $(".basket-count").html('0');
                                $("#coupon_code").attr("disabled",true);
                            }
                        });

                        OrderSummary();
                    }else if(solve.status == "error"){
                        swal('<?php echo __("website/basket/modal-error"); ?>',solve.message,'error');
                    }
                }else console.log("Result cannot resolved.");
            }else console.log("Basket item not deleted.");
        });
    }

    function OrderSummary() {

        $("#OrderSummary_loader").css("display","block");
        $("#OrderSummaryContent").css("display","none");

        var request = MioAjax({
            action: "<?php echo $links["bring"]."order-summary"; ?>",
            method: "POST"
        },true,true);

        request.done(function (result) {

            $("#OrderSummary_loader").fadeOut(500,function(){
                $("#OrderSummaryContent").fadeIn();
            });

            var solve = false,content = '';
            if(result){
                solve = getJson(result);
                if(solve){

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
                            d_content += '<td><strong>'+d_seee+'</strong><br>('+ditem.name+') <a style="color:#777;margin-left:5px;" title="<?php echo __("website/basket/delete-coupon"); ?>" href="javascript:deleteCoupon('+ditem.id+');void 0;"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
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
                        $("#continue_block").fadeOut(100,function(){
                            $("#continue_go").fadeIn(100);
                        });
                        var amount_info = amount_divider(solve.total_amount_payable);
                        $("#total-amount-payable").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                    }else{
                        $("#continue_go").fadeOut(100,function(){
                            $("#continue_block").fadeIn(100);
                        });
                        $("#total-amount-payable").html('-');
                    }

                    if(solve.use_coupon != undefined){
                        if(solve.use_coupon) $("#use_coupon").fadeIn(1);
                        else $("#use_coupon").fadeOut(1);
                    }else $("#use_coupon").fadeOut(1);


                }else console.log("Can not resolved : "+result);
            }else console.log("Failed loaded in basket to order summary");
        });
    }

    function coupon_check(value){
        if(value != '' && value.length>=3){
            var request = MioAjax({
                action: "<?php echo $links["bring"]."coupon-check"; ?>",
                method: "POST",
                data:{code:value}
            },true,true);

            request.done(function (result) {
                if(result){
                    var solve = getJson(result);
                    if(solve){

                        if(solve.status == "error"){

                            $("#coupon_result").html(solve.message).fadeIn(200);

                        }else if(solve.status == "successful"){

                            $("#coupon_result").html('').fadeOut(1);
                            $("#kuponkodu").slideUp(400,function(){
                                $("#coupon_code").val('');
                                OrderSummary();
                            });
                        }else{
                            $("#coupon_result").html('').fadeOut(1);
                        }
                    }else{
                        $("#coupon_result").html('').fadeOut(1);
                        console.log(result);
                    }
                }else{
                    $("#coupon_result").html('').fadeOut(1);
                    console.log("Coupon check result is empty");
                }
            });
        }
    }

    function deleteCoupon(id){
        if(id != 0 && id != null){

            var request = MioAjax({
                action: "<?php echo $links["bring"]."delete-coupon"; ?>",
                method: "POST",
                data:{coupon_id:id}
            },true,true);

            request.done(function (result) {
                if(result){
                    var solve = getJson(result);
                    if(solve){
                        if(solve.status == "successful"){
                            OrderSummary();
                        }
                    }else console.log(result);
                }else console.log("Coupon check result is empty");
            });
        }
    }

    function open_ns_info(id)
    {

        var _title = '<?php echo __("website/basket/dns-tx1"); ?>';

        var ns_info = ns_details[id];

        open_modal('cart-ns-details',{
            width:'800px',
            title: _title.replace("{domain}",domain_names[id]),
        });

        $("#cart-ns-details input[name=item_id]").val(id);


        for(var i = 1; i <= 4; i++)
        {
            if(ns_info["ns"+i] !== undefined)
            {
                var x = i-1;
                $("#cart-ns-details input[name='dns[]']").eq(x).val(ns_info["ns"+i]);
            }
        }

    }

    function open_whois_info(id)
    {

        var _title = '<?php echo __("website/basket/whois-tx1"); ?>';

        $("#cart-whois-details input[name=item_id]").val(id);

        $("#cart-whois-details .iziModal-header-title").html(_title.replace("{domain}",domain_names[id]));
        $("#cart-whois-details").iziModal('open');

        var info            = whois_details[id];
        var contact_types   = Object.keys(info);

        $(contact_types).each(function(k,contact_type){
            var whois_info_keys = Object.keys(info[contact_type]);
            $(whois_info_keys).each(function(k,w_name){
                var w_value = info[contact_type][w_name];

                if(w_name === "profile_id")
                {
                    if(parseInt(w_value) > 0)
                    {
                        $("select[name='profile_id["+contact_type+"]']").val(w_value);
                    }
                    console.log(w_value);
                }
                else $(".whois-"+contact_type+"-"+w_name).val(w_value);
            });
        });


    }

    $(document).ready(function(){
        var tab_ct;
        ItemList();
        OrderSummary();

        $("#cart-whois-details").iziModal({
            title: '???',
            width:800,
            restoreDefaultContent:false,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOutDown',
            bodyOverflow: true,
            history:false,
            appendTo:false
        });

        $("#coupon_code").keyup(function(e){
            var ithis = this;
            var isBackspaceOrDelete = (e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 46 || e.keyCode == 32);
            var check = isBackspaceOrDelete || (e.keyCode>=33 && e.keyCode < 254);
            var inputValue = $(ithis).val();
            if(inputValue.length<3) $("#coupon_result").html('').fadeOut(1);
            if(check && inputValue.length>=3){
                var ithis = this;
                var ie    = e;
                setTimeout(function(){
                    coupon_check(inputValue);
                },600);
            }
        });

        $("#coupon_code").bind("paste", function(e){
            var pastedData = e.originalEvent.clipboardData.getData('text');
            coupon_check(pastedData);
        });

        $("#cart-ns-details").on("click","#DomainDnsChangeForm_submit",function(){
            MioAjaxElement($(this),{
                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                result: "DomainDnsChangeForm_handler"
            });
        });

        $("#cart-whois-details").on("click","#DomainWhoisChangeForm_submit",function(){
            MioAjaxElement($(this),{
                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                result: "DomainWhoisChangeForm_handler"
            });
        });

        $(".select-whois-profile").change(function(){
            tab_ct              = gGET("contact-type");

            if(tab_ct === "" || tab_ct === null) tab_ct = "registrant";

            var profile         =  $(this).val();
            var profile_o       = $("option[value="+profile+"]",$(this));
            var wrap            = $("#contact-type-"+tab_ct);

            $(".profile-name-wrap",wrap).css("display","none");

            if(profile === "new")
            {
                $(".profile-name-wrap",wrap).css("display","block");
                $(".profile-name-wrap input",wrap).focus();
            }
            else
            {
                var info            = profile_o.data("information");
                var info_keys       = Object.keys(info);

                $(info_keys).each(function(k,v){
                    $(".whois-"+tab_ct+"-"+v).val(info[v]);
                });
            }
        });

    });

    function DomainDnsChangeForm_handler(result)
    {
        if(result !== ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status === "error")
                    alert_error(solve.message,{timer:3000});
                else if(solve.status === "successful")
                {
                    alert_success(solve.message,{timer:2000});
                    ItemList();
                    OrderSummary();
                }
            }else
                console.log(result);
        }
    }

    function DomainWhoisChangeForm_handler(result)
    {
        if(result !== ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status === "error")
                    alert_error(solve.message,{timer:3000});
                else if(solve.status === "successful")
                {
                    $("#cart-whois-details").iziModal('close');

                    alert_success(solve.message,{timer:2000});
                    ItemList();
                }
            }else
                console.log(result);
        }
    }
</script>

<style>
/* CODEGA - Sepet sayfası modern stil */
#wrapper, .basket_wrapper {
    background: #f8fafc;
    padding: 32px 0 60px;
    min-height: 60vh;
}
.cdg-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Sepet GRID (sol: items, sağ: order summary) */
.sepet {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: flex-start;
}
@media (max-width: 900px) {
    .sepet { grid-template-columns: 1fr; }
}

/* Sol panel (items) */
.sepetleft {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0;
    overflow: hidden;
    width: 100% !important;
    float: none !important;
}

/* Tablo başlıkları */
.sepetbaslik {
    background: linear-gradient(135deg, #2E3B4E, #485A75);
    color: #fff;
    padding: 14px 20px;
    border-radius: 0 !important;
}
.sepetbaslik > div { display: flex !important; gap: 12px; align-items: center; padding: 0 !important; }
.uhinfo, .uhperiyod, .uhtutar {
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.uhinfo { flex: 1; }
.uhperiyod { width: 120px; text-align: center; }
.uhtutar { width: 140px; text-align: right; }

/* Boş sepet mesajı */
.info#empty_list {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border: 1px solid #6ee7b7;
    border-radius: 12px;
    padding: 24px;
    margin: 24px 20px;
    text-align: center;
    color: #065f46;
}
.info#empty_list i {
    display: block;
    font-size: 48px;
    color: #10b981;
    margin-bottom: 12px;
}
.info#empty_list h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #065f46;
    line-height: 1.6;
}

/* Item list container */
#item_list { padding: 0 20px; }

/* "Alışverişe Devam Et" butonu */
#continueshopbtn,
.lbtn.gonderbtn {
    display: inline-flex !important;
    align-items: center;
    gap: 8px;
    padding: 14px 28px !important;
    background: linear-gradient(135deg, #485A75, #2E3B4E) !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 10px !important;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    margin: 12px 20px 20px;
    transition: all 0.2s;
}
#continueshopbtn:hover,
.lbtn.gonderbtn:hover {
    background: linear-gradient(135deg, #2E3B4E, #1A2332) !important;
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(30,64,175,0.20);
}

/* Payment logos / SSL notice - güvenlik bandı */
.paymentlogos {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 18px 20px;
    margin: 20px -20px -20px;
    text-align: center;
}
.paymentlogos img { display: none !important; }
.paymentlogos::before {
    content: '';
    display: inline-block;
    width: 22px;
    height: 22px;
    margin-right: 8px;
    vertical-align: middle;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310b981'><path d='M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z'/></svg>");
    background-size: contain;
    background-repeat: no-repeat;
}
.paymentlogos span {
    color: #475569;
    font-size: 13px;
    font-weight: 500;
    vertical-align: middle;
}

/* Sağ panel: Sipariş özeti */
.sepetright {
    width: 100% !important;
    float: none !important;
    position: sticky;
    top: 90px;
}
.sepetrightshadow {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}
.sepetright .sepetbaslik {
    background: linear-gradient(135deg, #1A2332, #2E3B4E);
    padding: 16px 20px;
    border-radius: 0;
}
.sepetright .sepetbaslik > div {
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    text-align: center !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0 !important;
}

.sepetrightcon { padding: 20px; }
.sepetsipinfo { width: 100%; border-collapse: collapse; }
.sepetsipinfo td {
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    color: #475569;
}
.sepetsipinfo td:last-child { text-align: right; font-weight: 700; color: #0f172a; }
.sepetsipinfo tr:last-child td { border-bottom: 0; }
.sepetsipinfo .totalamountinfo {
    text-align: center !important;
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border-radius: 10px;
    padding: 16px !important;
    border-bottom: 0 !important;
}
.sepetsipinfo .totalamountinfo strong {
    display: block;
    color: #0f172a;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 6px;
}
.sepetsipinfo .totalamountinfo h5 {
    margin: 0;
    font-size: 24px;
    font-weight: 900;
    color: #2E3B4E;
}

/* "Devam Et" butonları (sağ panel altı) */
.gonderbtn {
    display: block !important;
    text-align: center !important;
    margin: 14px 20px 20px;
    padding: 14px 24px !important;
    background: linear-gradient(135deg, #2E3B4E, #485A75) !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 10px !important;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(30,64,175,0.20);
    transition: all 0.2s;
}
.gonderbtn:hover {
    background: linear-gradient(135deg, #1A2332, #2E3B4E) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(30,64,175,0.30) !important;
    color: #fff !important;
}
#continue_block.gonderbtn,
#continue_block.graybtn {
    background: #cbd5e1 !important;
    color: #94a3b8 !important;
    cursor: not-allowed !important;
    box-shadow: none !important;
}
#continue_block.gonderbtn:hover { transform: none !important; box-shadow: none !important; }

/* Loaders */
#OrderSummary_loader, #basket_loader {
    text-align: center;
    padding: 30px 0;
}
.spinner {
    width: 32px;
    height: 32px;
    border: 3px solid #e2e8f0;
    border-top-color: #2E3B4E;
    border-radius: 50%;
    margin: 0 auto;
    animation: cdg-spin 0.8s linear infinite;
}
@keyframes cdg-spin { to { transform: rotate(360deg); } }

/* Renk override (Classic değişkenler) */
.green-label { background-color: #10b981 !important; color: #fff !important; }
.yesilbtn { background: #10b981 !important; }
.yesilbtn:hover { background: #059669 !important; }
.lbtn {
    border: 2px solid #2E3B4E !important;
    color: #2E3B4E !important;
}
.lbtn:hover {
    border-color: #1A2332 !important;
    color: #fff !important;
    background: #2E3B4E !important;
}
.row-label.green-label {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}

/* Checkbox renk override */
.checkbox-custom:checked + .checkbox-custom-label:before {
    background: #2E3B4E !important;
    border-color: #2E3B4E !important;
}
.checkbox-custom + .checkbox-custom-label:before,
.radio-custom + .radio-custom-label:before {
    border: 1.5px solid #cbd5e1 !important;
}

/* clear */
.clear { clear: both; }
</style>

<div id="wrapper" style="margin-top: 30px;">
    <div class="cdg-container">

    <div class="sepet">

        <div class="sepetleft">

            <div class="sepetbaslik">
                <div style="padding:0px 15px;">
                    <div class="uhinfo"><?php echo __("website/basket/name2"); ?></div>
                    <div class="uhperiyod"><?php echo __("website/basket/period"); ?></div>
                    <div class="uhtutar"><?php echo __("website/basket/amount"); ?></div>
                </div>
            </div>

            <div class="clear"></div>
            <div class="info" id="empty_list">
                <i class="bi bi-cart-x"></i>
                <h4><?php echo __("website/basket/empty-list"); ?></h4>
            </div>
            <div id="basket_loader" style="margin-top: 7%;    margin-bottom: 40px; text-align: center;">
                <div class="spinner"></div>
            </div>
            <div class="clear"></div>
            <div id="item_list" style="display: none;"></div>

            <div align="center" style="margin-top:8px;"><a class="lbtn gonderbtn" id="continueshopbtn"  href="<?php echo $home_link ?? ''; ?>"><i class="bi bi-arrow-left"></i> <?php echo __("website/basket/continue-to-shopping"); ?></a></div>

            <div class="paymentlogos">
                <img class="plogos1" src="<?php echo $tadress ?? ''; ?>images/credit-cards.svg">
                <img class="plogos2" src="<?php echo $tadress ?? '';?>images/ssl-secure.svg">
                <div class="clear"></div>
                <span><?php echo __("website/basket/basketsecnotice"); ?></span>
            </div>


        </div>

        <div class="sepetright">
            <div class="sepetrightshadow">
                <div class="sepetbaslik">
                    <div style="padding:0;text-align:center;">
                        <i class="bi bi-receipt" style="margin-right:6px;"></i>
                        <?php echo __("website/basket/order-summary"); ?>
                    </div>
                </div>

                <div class="sepetrightcon" id="OrderSummaryContent" style="display: none;">

                    <table class="sepetsipinfo" width="100%" border="0">
                        <tr>
                            <td><strong><?php echo __("website/basket/total-order-amount"); ?></strong></td>
                            <td align="right"><h5 id="total-amount">0</h5></td>
                        </tr>

                        <tbody id="dealership_discounts"></tbody>

                        <tbody id="coupon_discounts" style="display: none;"></tbody>

                        <tr id="tax_content" style="display:none;">
                            <td><strong id="tax-see"><?php echo __("website/basket/tax-amount",['']); ?></strong></td>
                            <td align="right"><h5 id="tax-amount">0</h5></td>
                        </tr>

                        <tr id="use_coupon" style="display: none;">
                            <td colspan="2" align="center">
                                <a href="javascript:$('#kuponkodu').slideToggle();void 0"><i class="fa fa-ticket" aria-hidden="true"></i> <?php echo __("website/basket/use-coupon-code"); ?></a>
                                <div class="kuponkodu" id="kuponkodu" style="display: none; transition-property: all; transition-duration: 0s; transition-timing-function: ease; opacity: 1;">
                                    <input id="coupon_code" name="coupon_code" type="text" placeholder="<?php echo __("website/basket/coupon-code-pholder"); ?>" onchange="coupon_check($(this).val());">
                                    <div style="text-align: center; margin-top: 5px; display: none;" class="error" id="coupon_result"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="totalamountinfo" align="center" colspan="2">
                                <strong><?php echo __("website/basket/total-amount-payable"); ?></strong><br>
                                <h5 id="total-amount-payable">0</h5>
                            </td>
                        </tr>

                    </table>



                    <div class="clear"></div>
                </div>

                <div class="clear"></div>

                <div id="OrderSummary_loader">
                    <div class="spinner"></div>
                    <div class="clear"></div>
                    <br>
                </div>
                <div class="clear"></div>


            </div>
            <a href="<?php echo $links["payment"]; ?>" style="display: none;" class="gonderbtn" id="continue_go"><i class="bi bi-arrow-right-circle"></i> <?php echo __("website/basket/continue-button"); ?></a>
            <a class="graybtn gonderbtn" id="continue_block"><i class="bi bi-lock"></i> <?php echo __("website/basket/continue-button"); ?></a>
        </div>

    </div>
    </div><!-- /.cdg-container -->
</div>

<div id="cart-ns-details" style="display: none">
    <form action="<?php echo $links["bring"]; ?>change-domain-ns" method="post" id="DomainDnsChangeForm">
        <input type="hidden" name="item_id" value="">

        <div class="padding20">
            <div class="red-info">
                <div class="padding15">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo __("website/basket/dns-tx2"); ?></p>
                </div>
            </div>

            <div style="width:100%;text-align:center;">
                <div class="clear"></div>
                <?php
                    for($i=1;$i<=4;$i++)
                    {
                        ?>
                        <input name="dns[]" value="" type="text" class="" placeholder="ns<?php echo $i; ?>.example.com">
                        <div class="clear"></div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <div class="modal-foot-btn">
            <a href="javascript:void(0);" id="DomainDnsChangeForm_submit" class="green lbtn"><?php echo ___("needs/button-update"); ?></a>
        </div>
    </form>
</div>

<div id="cart-whois-details" style="display: none">

    <form action="<?php echo $links["bring"]; ?>change-domain-whois" method="post" id="DomainWhoisChangeForm">
        <input type="hidden" name="item_id" value="">


        <div class="padding20">
            <div class="blue-info">
                <div class="padding15">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    <p><?php echo __("website/basket/whois-tx2"); ?></p>
                </div>
            </div>


            <div id="tab-contact-type">
                <ul class="tab">
                    <?php
                        if(isset($contact_types) && $contact_types)
                        {
                            foreach($contact_types AS $k => $v)
                            {
                                ?>
                                <li><a href="javascript:void 0;" class="tablinks<?php echo $k == "registrant" ? ' active' : ''; ?>" onclick="open_tab(this,'<?php echo $k; ?>','contact-type');" data-tab="<?php echo $k; ?>"><?php echo $v; ?></a></li>
                                <?php
                            }
                        }
                    ?>
                    <a style="float:right;margin: 14px 15px 0 0;" href="<?php echo $links["whois-profiles"]; ?>" class="green lbtn"><i style="margin-right: 5px;" class="far fa-id-card"></i> <?php echo __("website/account_products/domain-whois-tx6"); ?></a>
                </ul>

                <?php
                    if(isset($contact_types) && $contact_types)
                    {
                        foreach($contact_types AS $k => $v)
                        {
                            ?>
                            <div id="contact-type-<?php echo $k; ?>" class="tabcontent" style="<?php echo $k == "registrant" ? 'display:block;' : ''; ?>">

                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx18"); ?></div>
                                    <div class="yuzde70">

                                        <?php if(isset($udata) && $udata): ?>
                                            <select class="select-whois-profile" name="profile_id[<?php echo $k; ?>]">
                                                <option data-information='<?php echo Utility::jencode($user_whois_info ?? []); ?>' value="0"><?php echo __("website/account_products/domain-whois-tx20"); ?></option>
                                                <?php
                                                    if(isset($whois_profiles) && $whois_profiles)
                                                    {
                                                        foreach($whois_profiles AS $pf)
                                                        {
                                                            $s_pf = $whois[$k]["profile_id"] ?? 0;
                                                            $pf_s = $s_pf == $pf["id"];
                                                            ?>
                                                            <option<?php echo $pf_s ? ' selected' : ''; ?> data-information='<?php echo $pf["information"]; ?>' value="<?php echo $pf["id"]; ?>"><?php echo $pf["name"].' - '.$pf["person_name"]." - ".$pf["person_email"]." - ".$pf["person_phone"];  ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                                <option value="new">+ <?php echo __("website/account_products/domain-whois-tx19"); ?></option>
                                            </select>
                                        <?php endif; ?>

                                        <div class="formcon profile-name-wrap" style="display: none;">
                                            <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx7"); ?></div>
                                            <div class="yuzde70">
                                                <input name="profile_name[<?php echo $k; ?>]" value="" type="text" placeholder="<?php echo __("website/account_products/domain-whois-tx7"); ?>" style="padding: 8px;width: 100%;">
                                            </div>
                                        </div>

                                        <div style="margin-top: 15px;display: inline-block;">
                                            <input type="checkbox" name="apply_to_all[<?php echo $k; ?>]" value="1" class="checkbox-custom" id="apply_to_all_<?php echo $k; ?>">
                                            <label class="checkbox-custom-label" for="apply_to_all_<?php echo $k; ?>"><?php echo __("website/account_products/domain-whois-tx21"); ?></label>
                                        </div>

                                    </div>
                                </div>


                                <input name="info[<?php echo $k; ?>][Name]" value="<?php echo $whois[$k]["Name"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-Name" placeholder="<?php echo __("website/account_products/whois-full_name"); ?>">
                                <input name="info[<?php echo $k; ?>][Company]" value="<?php echo $whois[$k]["Company"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-Company" placeholder="<?php echo __("website/account_products/whois-company_name"); ?>">
                                <input name="info[<?php echo $k; ?>][EMail]" value="<?php echo $whois[$k]["EMail"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-EMail" placeholder="<?php echo __("website/account_products/whois-email"); ?>">
                                <input name="info[<?php echo $k; ?>][PhoneCountryCode]" value="<?php echo $whois[$k]["PhoneCountryCode"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-PhoneCountryCode" placeholder="<?php echo __("website/account_products/whois-phoneCountryCode"); ?>">
                                <input name="info[<?php echo $k; ?>][Phone]" type="text" value="<?php echo $whois[$k]["Phone"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Phone" placeholder="<?php echo __("website/account_products/whois-phone"); ?>">
                                <input name="info[<?php echo $k; ?>][FaxCountryCode]" type="text" value="<?php echo $whois[$k]["FaxCountryCode"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-FaxCountryCode" placeholder="<?php echo __("website/account_products/whois-faxCountryCode"); ?>">
                                <input name="info[<?php echo $k; ?>][Fax]" type="text" value="<?php echo $whois[$k]["Fax"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Fax" placeholder="<?php echo __("website/account_products/whois-fax"); ?>">
                                <input name="info[<?php echo $k; ?>][City]" type="text" value="<?php echo $whois[$k]["City"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-City" placeholder="<?php echo __("website/account_products/whois-city"); ?>">
                                <input name="info[<?php echo $k; ?>][State]" type="text" value="<?php echo $whois[$k]["State"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-State" placeholder="<?php echo __("website/account_products/whois-state"); ?>">
                                <input name="info[<?php echo $k; ?>][Address]" type="text" value="<?php echo $whois[$k]["Address"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Address" placeholder="<?php echo __("website/account_products/whois-address"); ?>">
                                <input name="info[<?php echo $k; ?>][Country]" type="text" value="<?php echo $whois[$k]["Country"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Country" placeholder="<?php echo __("website/account_products/whois-CountryCode"); ?>">
                                <input name="info[<?php echo $k; ?>][ZipCode]" type="text" value="<?php echo $whois[$k]["ZipCode"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-ZipCode" placeholder="<?php echo __("website/account_products/whois-zipcode"); ?>">
                            </div>
                            <?php
                        }
                    }
                ?>

            </div>


        </div>
        <div class="modal-foot-btn">
            <a href="javascript:void(0);" id="DomainWhoisChangeForm_submit" class="green lbtn"><?php echo ___("needs/button-update"); ?></a>
        </div>

    </form>
</div>
</div>
