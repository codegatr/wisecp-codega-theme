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
        // 1) Runtime $links[] kontrolü (WiseCP en güvenilir kaynak)
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }

        // 2) Kısa-isim -> WiseCP gerçek route alias map
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
            'ac-ps-create-ticket-request' => 'ac-ps-create-ticket-request',
            'tickets'                 => 'ac-ps-tickets',
            'my-tickets'              => 'ac-ps-tickets',
            'messages'                => 'ac-ps-messages',
            'detail-message'          => 'ac-ps-detail-message',
            'invoices'                => 'ac-ps-invoices',
            'detail-invoice'          => 'ac-ps-detail-invoice',
            'detail-invoice-pdf'      => 'ac-ps-detail-invoice',
            'balance'                 => 'ac-ps-balance',
            'balance-page'            => 'ac-ps-balance',
            'info'                    => 'ac-ps-info',
            'ac-info'                 => 'ac-ps-info',
            'products'                => 'ac-ps-products',
            'all-orders'              => 'ac-ps-products',
            'products-t'              => 'ac-ps-products-t',
            'product'                 => 'ac-ps-product',
            'sms'                     => 'ac-ps-sms',
            'domains'                 => 'ac-products-domain',
            'products-domain'         => 'ac-products-domain',
            'whois-profiles'          => 'ac-products-domain-whois-profiles',
            'products-domain-whois-profiles' => 'ac-products-domain-whois-profiles',
            'create-whois-profile'    => 'ac-products-domain-create-whois-profile',
            'products-domain-create-whois-profile' => 'ac-products-domain-create-whois-profile',
            'login'                   => 'sign-in',
            'register'                => 'sign-up',
            'logout'                  => 'sign-out',
            'account'                 => 'my-account',
            'homepage'                => '',
            'home'                    => '',
        ];
        $real_slug = isset($aliases[$slug]) ? $aliases[$slug] : $slug;

        // 3) CRLink dene (gerçek WiseCP routing)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                // Bozuk URL kontrolü (boş ID parametresi vb.)
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) { /* fallback'e düş */ }
        }

        // 4) Son çare: APP_URI base + slug
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
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
