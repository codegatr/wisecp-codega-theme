<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Fallback'ler
if(!isset($home_link))     $home_link     = defined('APP_URI') ? APP_URI . '/' : '/';
if(!isset($login_link))    $login_link    = '#';
if(!isset($logout_link))   $logout_link   = '#';
if(!isset($register_link)) $register_link = '#';
if(!isset($basket_link))   $basket_link   = '#';
if(!isset($tickets_link))  $tickets_link  = '#';
if(!isset($my_account_link)) $my_account_link = '#';
if(!isset($sign_in))       $sign_in       = false;
if(!isset($visibility_basket)) $visibility_basket = true;
if(!isset($pnumbers))      $pnumbers      = [];
if(!isset($eaddresses))    $eaddresses    = [];
if(!isset($header_logo_link)) $header_logo_link = '';
if(!isset($currencies_count)) $currencies_count = 1;
if(!isset($lang_count))    $lang_count    = 1;
if(!isset($selected_c))    $selected_c    = ['code' => 'TRY'];
if(!isset($selected_l))    $selected_l    = ['cname' => 'Türkçe', 'name' => 'TR'];

// Helper for active page
$current_page = isset($hoptions["page"]) ? $hoptions["page"] : '';
if(!function_exists('cdg_active')) {
    function cdg_active($pages, $current) {
        if(is_string($pages)) $pages = [$pages];
        return in_array($current, $pages) ? ' active' : '';
    }
}

// Slug-tabanlı link üretici (CRLink fallback)
if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        global $links;
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
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
            'products-hosting'        => 'products-hosting',
            'hosting-products'        => 'products-hosting',
            'public-hosting'          => 'products-hosting',
            'products'                => 'ac-ps-products',
            'all-orders'              => 'ac-ps-products',
            'products-t'              => 'ac-ps-products-t',
            'product'                 => 'ac-ps-product',
            'sms'                     => 'ac-ps-sms',
            'affiliate'               => 'ac-affiliate',
            'ac-affiliate'            => 'ac-affiliate',
            'reseller'                => 'ac-reseller',
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
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) {}
        }
        // Son care: $links bakilirsa kullan
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}
?>

<header class="cdg-header">
    <div class="cdg-container">
        <div class="cdg-header-inner">

            <a href="<?php echo $home_link; ?>" class="cdg-logo">
                <span class="cdg-logo-mark">C</span>
                <span>CODEGA</span>
            </a>

            <button type="button" class="cdg-mobile-toggle" aria-label="Menu">
                <span></span>
            </button>

            <ul class="cdg-nav">
                <li><a href="<?php echo $home_link; ?>" class="<?php echo cdg_active(['index',''], $current_page); ?>">Ana Sayfa</a></li>
                <li><a href="<?php echo cdg_link('products-hosting'); ?>" class="<?php echo cdg_active(['products','hosting-products','products-hosting'], $current_page); ?>">Hosting</a></li>
                <li><a href="<?php echo cdg_link('domain'); ?>"                class="<?php echo cdg_active('domain', $current_page); ?>">Domain</a></li>
                <li><a href="<?php echo cdg_link('softwares'); ?>"             class="<?php echo cdg_active(['softwares','special-products'], $current_page); ?>">Yazılım</a></li>
                <li><a href="<?php echo cdg_link('knowledgebase'); ?>"         class="<?php echo cdg_active(['knowledgebase','articles','news'], $current_page); ?>">Bilgi</a></li>
                <li><a href="<?php echo cdg_link('contact'); ?>"               class="<?php echo cdg_active('contact', $current_page); ?>">İletişim</a></li>
            </ul>

            <div class="cdg-nav-actions">
                <?php
                // Sepet item sayisi - WiseCP'nin farkli versiyonlari icin defansif
                $cdg_basket_count = 0;
                if(class_exists('Basket')) {
                    if(method_exists('Basket', 'count')) {
                        try { $cdg_basket_count = (int) Basket::count(); } catch(\Throwable $e) {}
                    } elseif(isset(Basket::$items) && is_array(Basket::$items)) {
                        $cdg_basket_count = count(Basket::$items);
                    } elseif(method_exists('Basket', 'getItems')) {
                        try { $items = Basket::getItems(); $cdg_basket_count = is_array($items) ? count($items) : 0; } catch(\Throwable $e) {}
                    }
                }
                if(!$cdg_basket_count && isset($basket_count)) $cdg_basket_count = (int)$basket_count;
                if(!$cdg_basket_count && isset($_SESSION['Basket']) && is_array($_SESSION['Basket'])) {
                    $cdg_basket_count = count($_SESSION['Basket']);
                }
                ?>
                <a href="<?php echo (isset($basket_link) && $basket_link && $basket_link != '#') ? $basket_link : cdg_link('basket'); ?>" class="cdg-btn cdg-btn-ghost cdg-btn-sm cdg-cart-btn" title="Sepetim">
                    <i class="bi bi-cart3"></i>
                    <?php if($cdg_basket_count > 0): ?>
                    <span class="cdg-cart-badge"><?php echo (int)$cdg_basket_count; ?></span>
                    <?php endif; ?>
                </a>

                <?php if($sign_in): ?>
                    <a href="<?php echo (isset($my_account_link) && $my_account_link && $my_account_link != '#') ? $my_account_link : cdg_link('my-account'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
                        <i class="bi bi-person-circle"></i> Hesabım
                    </a>
                    <a href="<?php echo (isset($logout_link) && $logout_link && $logout_link != '#') ? $logout_link : cdg_link('logout'); ?>" class="cdg-btn cdg-btn-ghost cdg-btn-sm" title="Çıkış">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo (isset($login_link) && $login_link && $login_link != '#') ? $login_link : cdg_link('sign-in'); ?>"    class="cdg-btn cdg-btn-ghost cdg-btn-sm">Giriş</a>
                    <a href="<?php echo (isset($register_link) && $register_link && $register_link != '#') ? $register_link : cdg_link('sign-up'); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">Kayıt Ol</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>
