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
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug . ($params ? '/' . implode('/', $params) : '');
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
                <li><a href="<?php echo $home_link; ?>" class="<?php echo cdg_active(['index',''], $current_page); ?>">Anasayfa</a></li>
                <li><a href="<?php echo cdg_link('products', ['hosting']); ?>" class="<?php echo cdg_active(['products','hosting-products'], $current_page); ?>">Hosting</a></li>
                <li><a href="<?php echo cdg_link('domain'); ?>"                class="<?php echo cdg_active('domain', $current_page); ?>">Domain</a></li>
                <li><a href="<?php echo cdg_link('softwares'); ?>"             class="<?php echo cdg_active(['softwares','special-products'], $current_page); ?>">Yazilim</a></li>
                <li><a href="<?php echo cdg_link('knowledgebase'); ?>"         class="<?php echo cdg_active(['knowledgebase','articles','news'], $current_page); ?>">Bilgi</a></li>
                <li><a href="<?php echo cdg_link('contact'); ?>"               class="<?php echo cdg_active('contact', $current_page); ?>">Iletisim</a></li>
            </ul>

            <div class="cdg-nav-actions">
                <?php if($visibility_basket): ?>
                    <a href="<?php echo (isset($basket_link) && $basket_link && $basket_link != '#') ? $basket_link : cdg_link('basket'); ?>" class="cdg-btn cdg-btn-ghost cdg-btn-sm" title="Sepetim">
                        <i class="bi bi-cart"></i>
                    </a>
                <?php endif; ?>

                <?php if($sign_in): ?>
                    <a href="<?php echo (isset($my_account_link) && $my_account_link && $my_account_link != '#') ? $my_account_link : cdg_link('my-account'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">
                        <i class="bi bi-person-circle"></i> Hesabim
                    </a>
                    <a href="<?php echo (isset($logout_link) && $logout_link && $logout_link != '#') ? $logout_link : cdg_link('logout'); ?>" class="cdg-btn cdg-btn-ghost cdg-btn-sm" title="Cikis">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo (isset($login_link) && $login_link && $login_link != '#') ? $login_link : cdg_link('sign-in'); ?>"    class="cdg-btn cdg-btn-ghost cdg-btn-sm">Giris</a>
                    <a href="<?php echo (isset($register_link) && $register_link && $register_link != '#') ? $register_link : cdg_link('sign-up'); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">Kayit Ol</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>
