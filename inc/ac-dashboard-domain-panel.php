<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Dashboard Domain Arama Paneli
 * Müşteri panelinin dashboard'unda kompakt domain arama kutusu
 * WiseCP runtime: $first_tld_price, $domain_check_post, $domain_override_uscurrency
 */

// Domain sistem aktif değilse veya dashboard widget kapalıysa gösterme
if(class_exists('Config')) {
    if(!Config::get("options/pg-activation/domain") || !Config::get("options/domain-dashboard-widget")) return false;
}

// Para birimi sembollerini topla
$currency_symbols = [];
if(class_exists('Money') && method_exists('Money','getCurrencies')) {
    foreach(Money::getCurrencies() as $currency) {
        $symbol = isset($currency["prefix"]) && $currency["prefix"] !== '' ? trim($currency["prefix"]) : trim($currency["suffix"] ?? '');
        if(!$symbol) $symbol = $currency["code"] ?? '';
        if($symbol) $currency_symbols[] = $symbol;
    }
}

// İlk TLD fiyatını işle
$first_price = '';
$first_price_symbol = '';
$first_price_amount = '';
if(isset($first_tld_price) && is_array($first_tld_price)) {
    $first_tld_price_amount = $first_tld_price["register"]["amount"] ?? 0;

    // Promosyon aktifse promo fiyatı kullan
    if(!empty($first_tld_price["promo_status"]) && !empty($first_tld_price["promo_register_price"])) {
        $promo_due = $first_tld_price["promo_duedate"] ?? '';
        $is_lifetime = (substr((string)$promo_due, 0, 4) === '1881');
        $is_active = false;
        if(class_exists('DateManager') && method_exists('DateManager','strtotime')) {
            try {
                $is_active = ($is_lifetime || DateManager::strtotime($promo_due . " 23:59:59") > DateManager::strtotime());
            } catch(\Throwable $e) { $is_active = $is_lifetime; }
        }
        if($is_active) {
            $first_tld_price_amount = $first_tld_price["promo_register_price"];
        }
    }

    if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
        $cid = $first_tld_price["register"]["cid"] ?? 0;
        $override_us = !empty($domain_override_uscurrency);
        $first_price_str = Money::formatter_symbol($first_tld_price_amount, $cid, !$override_us);

        if($first_price_str) {
            $split_amount = explode(" ", $first_price_str);
            if(in_array(current($split_amount), $currency_symbols)) {
                $first_price_symbol = current($split_amount);
                array_shift($split_amount);
                $first_price_amount = implode(" ", $split_amount);
            } elseif(in_array(end($split_amount), $currency_symbols)) {
                $first_price_symbol = end($split_amount);
                array_pop($split_amount);
                $first_price_amount = implode(" ", $split_amount);
            } else {
                $first_price_amount = $first_price_str;
            }
        }
    }
}

$first_tld_name = '';
if(isset($first_tld_price['name'])) $first_tld_name = '.' . ltrim((string)$first_tld_price['name'], '.');
elseif(isset($first_tld_price['tld'])) $first_tld_name = '.' . ltrim((string)$first_tld_price['tld'], '.');

$action_url = $domain_check_post ?? '#';
?>

<style>
.cdg-ddp {
    background: linear-gradient(135deg, #2E3B4E 0%, #00D3E5 50%, #00D3E5 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(46,59,78,0.20);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-ddp *, .cdg-ddp *::before, .cdg-ddp *::after { box-sizing: border-box; }
.cdg-ddp::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 360px; height: 360px;
    background: radial-gradient(circle, rgba(252,211,77,0.18), transparent 70%);
    pointer-events: none;
}
.cdg-ddp-row {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 18px;
    align-items: center;
    position: relative; z-index: 1;
}
.cdg-ddp-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 26px;
    flex-shrink: 0;
}
.cdg-ddp-info { min-width: 0; }
.cdg-ddp-title {
    font-size: 20px;
    font-weight: 800;
    margin: 0 0 4px;
    letter-spacing: -0.3px;
}
.cdg-ddp-title .price-badge {
    display: inline-flex; align-items: center;
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1A2332;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 14px;
    font-weight: 800;
    margin-left: 8px;
    vertical-align: middle;
    box-shadow: 0 2px 8px rgba(252,211,77,0.30);
    white-space: nowrap;
}
.cdg-ddp-title .price-badge .symbol { font-size: 12px; margin-right: 3px; }
.cdg-ddp-subtitle {
    font-size: 13px;
    opacity: 0.88;
    margin: 0;
}

.cdg-ddp-form {
    margin-top: 18px;
    display: flex;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(15,23,42,0.20);
    position: relative; z-index: 1;
}
.cdg-ddp-form input[type=text] {
    flex: 1;
    padding: 14px 18px;
    border: 0;
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    background: #fff;
    outline: none;
    font-family: inherit;
    min-width: 0;
}
.cdg-ddp-form input[type=text]::placeholder { color: #94a3b8; }
.cdg-ddp-form button {
    padding: 12px 22px;
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1A2332;
    border: 0;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s;
    font-family: inherit;
    white-space: nowrap;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-ddp-form button:hover { background: linear-gradient(135deg, #facc15, #eab308); }

@media (max-width: 600px) {
    .cdg-ddp { padding: 22px 22px; }
    .cdg-ddp-row { grid-template-columns: 1fr; text-align: center; }
    .cdg-ddp-icon { margin: 0 auto; }
    .cdg-ddp-title { font-size: 18px; }
    .cdg-ddp-form { flex-direction: column; }
    .cdg-ddp-form button { justify-content: center; }
}
</style>

<div class="cdg-ddp">
    <div class="cdg-ddp-row">
        <div class="cdg-ddp-icon"><i class="bi bi-globe2"></i></div>
        <div class="cdg-ddp-info">
            <div class="cdg-ddp-title">
                Domain Sahibi Olun
                <?php if($first_price_amount): ?>
                <span class="price-badge">
                    <?php if($first_price_symbol): ?><span class="symbol"><?php echo htmlspecialchars($first_price_symbol, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><?php endif; ?>
                    <?php echo htmlspecialchars($first_price_amount, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    <?php if($first_tld_name): ?> / <?php echo htmlspecialchars($first_tld_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                    'den başlayan fiyatlarla
                </span>
                <?php endif; ?>
            </div>
            <p class="cdg-ddp-subtitle">İstediğiniz domain adresini sorgulayın, hemen alın.</p>
        </div>
    </div>

    <form id="checkForm" class="cdg-ddp-form" action="<?php echo htmlspecialchars($action_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" method="get">
        <input type="text" name="domain" placeholder="domain-adresinizi-yazin.com" autocomplete="off">
        <button type="submit" id="checkButton">
            <i class="bi bi-search"></i> Sorgula
        </button>
    </form>
</div>
