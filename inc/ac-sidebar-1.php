<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!isset($home_link))   $home_link   = defined('APP_URI') ? APP_URI . '/' : '/';
if(!isset($logout_link)) $logout_link = '#';

$current_page = isset($hoptions["page"]) ? $hoptions["page"] : '';

if(!function_exists('cdg_ac_active')) {
    function cdg_ac_active($pages, $current) {
        if(is_string($pages)) $pages = [$pages];
        return in_array($current, $pages) ? ' active' : '';
    }
}

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug . ($params ? '/' . implode('/', $params) : '');
    }
}
?>
<aside class="cdg-ac-sidebar">

    <a href="<?php echo $home_link; ?>" class="cdg-ac-brand">
        <span class="cdg-logo-mark">C</span>
        CODEGA
    </a>

    <ul class="cdg-ac-menu">
        <li><a href="<?php echo cdg_link('my-account'); ?>" class="<?php echo cdg_ac_active(['my-account','ac-dashboard'], $current_page); ?>">
            <span class="icon"><i class="bi bi-speedometer2"></i></span> Panelim
        </a></li>

        <li class="group">Urunlerim</li>

        <li><a href="<?php echo cdg_link('ac-ps-products'); ?>" class="<?php echo cdg_ac_active(['ac-ps-products','ac-products-all'], $current_page); ?>">
            <span class="icon"><i class="bi bi-grid"></i></span> Tum Urunler
        </a></li>
        <li><a href="<?php echo cdg_link('ac-ps-products-t', ['hosting']); ?>" class="<?php echo cdg_ac_active(['ac-ps-products-t','ac-products-hosting'], $current_page); ?>">
            <span class="icon"><i class="bi bi-hdd-network"></i></span> Hosting
        </a></li>
        <li><a href="<?php echo cdg_link('ac-ps-products-t', ['server']); ?>" class="<?php echo cdg_ac_active(['ac-products-server'], $current_page); ?>">
            <span class="icon"><i class="bi bi-server"></i></span> Sunucu
        </a></li>
        <li><a href="<?php echo cdg_link('ac-ps-products-t', ['domain']); ?>" class="<?php echo cdg_ac_active(['ac-products-domain'], $current_page); ?>">
            <span class="icon"><i class="bi bi-globe2"></i></span> Domain
        </a></li>
        <li><a href="<?php echo cdg_link('ac-ps-products-t', ['sms']); ?>" class="<?php echo cdg_ac_active(['ac-products-sms','ac-ps-sms'], $current_page); ?>">
            <span class="icon"><i class="bi bi-chat-square-dots"></i></span> SMS
        </a></li>

        <li class="group">Finansal</li>

        <li><a href="<?php echo cdg_link('ac-ps-invoices'); ?>" class="<?php echo cdg_ac_active(['ac-ps-invoices','ac-invoices'], $current_page); ?>">
            <span class="icon"><i class="bi bi-receipt"></i></span> Faturalarim
        </a></li>
        <li><a href="<?php echo cdg_link('ac-ps-balance'); ?>" class="<?php echo cdg_ac_active(['ac-ps-balance','ac-balance'], $current_page); ?>">
            <span class="icon"><i class="bi bi-wallet2"></i></span> Bakiyem
        </a></li>
        <li><a href="<?php echo cdg_link('ac-affiliate'); ?>" class="<?php echo cdg_ac_active(['ac-affiliate','ac-affiliate'], $current_page); ?>">
            <span class="icon"><i class="bi bi-people"></i></span> Tavsiyelerim
        </a></li>

        <li class="group">Destek</li>

        <li><a href="<?php echo cdg_link('ac-ps-tickets'); ?>" class="<?php echo cdg_ac_active(['ac-ps-tickets','ac-tickets'], $current_page); ?>">
            <span class="icon"><i class="bi bi-headset"></i></span> Destek Talepleri
        </a></li>
        <li><a href="<?php echo cdg_link('ac-ps-messages'); ?>" class="<?php echo cdg_ac_active(['ac-ps-messages','ac-messages'], $current_page); ?>">
            <span class="icon"><i class="bi bi-envelope"></i></span> Mesajlar
        </a></li>

        <li class="group">Hesap</li>

        <li><a href="<?php echo cdg_link('ac-ps-info'); ?>" class="<?php echo cdg_ac_active(['ac-ps-info','ac-info'], $current_page); ?>">
            <span class="icon"><i class="bi bi-person"></i></span> Hesap Bilgilerim
        </a></li>
        <li><a href="<?php echo (isset($logout_link) && $logout_link && $logout_link != '#') ? $logout_link : cdg_link('logout'); ?>" style="color:#ef4444;">
            <span class="icon"><i class="bi bi-box-arrow-right"></i></span> Cikis
        </a></li>
    </ul>
</aside>
