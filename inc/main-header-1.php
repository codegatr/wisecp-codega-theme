<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Brand kimligi defansif yukle (logo SVG icin)
if(!function_exists('cdg_logo_svg')) {
    $_brand_inc = __DIR__ . DIRECTORY_SEPARATOR . 'cdg-brand.php';
    if(file_exists($_brand_inc)) include_once $_brand_inc;
}

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

        // CDG_LINK_HARDCODED - Yunus'un sitesinde KESIN dogru URL'ler (CRLink bypass)
        static $hardcoded = [
            'ac-ps-create-ticket-request' => '/hesabim/destek-talebi-olustur',
            'create-ticket-request'       => '/hesabim/destek-talebi-olustur',
            'create-ticket'               => '/hesabim/destek-talebi-olustur',
        ];
        if(isset($hardcoded[$slug])) {
            $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
            return $base . $hardcoded[$slug];
        }

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
            'public-hosting'          => 'hosting-products',
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

            <a href="<?php echo $home_link; ?>" class="cdg-logo cdg-logo-v2" aria-label="CODEGA Anasayfa">
                <?php if(function_exists('cdg_logo_svg')) {
                    echo cdg_logo_svg('full', 38);
                } else { ?>
                <span class="cdg-logo-mark">C</span>
                <span>CODEGA</span>
                <?php } ?>
            </a>

            <button type="button" class="cdg-mobile-toggle" aria-label="Menu">
                <span></span>
            </button>

            <?php
            // === Hosting URL defansif hesaplama ===
            // 1. WiseCP runtime $links global'inden dene (admin panel'de tanimlanmis ise)
            $cdg_hosting_url = '';
            if(isset($links) && is_array($links)) {
                foreach(['hosting-products','products-hosting','hosting','public-hosting'] as $_k) {
                    if(!empty($links[$_k]) && $links[$_k] !== '#') { $cdg_hosting_url = $links[$_k]; break; }
                }
            }
            // 2. Yoksa anasayfa + #paketler anchor (anasayfada paketler bolumu var)
            if(!$cdg_hosting_url) {
                $_home = isset($home_link) && $home_link ? $home_link : (defined('APP_URI') ? APP_URI : '/');
                $cdg_hosting_url = rtrim($_home, '/') . '#paketler';
            }
            ?>
            <ul class="cdg-nav">
                <li><a href="<?php echo $home_link; ?>" class="<?php echo cdg_active(['index',''], $current_page); ?>">Ana Sayfa</a></li>
                <li class="cdg-nav-mega-parent">
                    <a href="javascript:void(0);" class="cdg-nav-mega-toggle">Kurumsal <i class="bi bi-chevron-down"></i></a>
                    <div class="cdg-nav-mega">
                        <div class="cdg-nav-mega-grid cdg-nav-mega-grid-3">
                            <div class="cdg-nav-mega-col">
                                <div class="cdg-nav-mega-title">Hakkımızda</div>
                                <a href="/vizyon.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;"><i class="bi bi-bullseye"></i></span>
                                    <span><strong>Vizyon &amp; Değerlerimiz</strong><small>Misyonumuz ve ilkelerimiz</small></span>
                                </a>
                                <a href="/hakkimizda.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);color:#4338ca;"><i class="bi bi-buildings-fill"></i></span>
                                    <span><strong>Şirket Hikayemiz</strong><small>2005'ten bugüne yolculuk</small></span>
                                </a>
                                <a href="/referanslarimiz.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#b45309;"><i class="bi bi-star-fill"></i></span>
                                    <span><strong>Referanslarımız</strong><small>Bize güvenen 59+ marka</small></span>
                                </a>
                                <a href="/kariyer.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;"><i class="bi bi-briefcase"></i></span>
                                    <span><strong>Kariyer</strong><small>Açık pozisyonlar</small></span>
                                </a>
                            </div>
                            <div class="cdg-nav-mega-col">
                                <div class="cdg-nav-mega-title">Sorumluluk</div>
                                <a href="/sosyal-sorumluluk.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#15803d;"><i class="bi bi-tree"></i></span>
                                    <span><strong>Sosyal Sorumluluk</strong><small>Toplum ve çevre projeleri</small></span>
                                </a>
                                <a href="/surdurulebilirlik.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#dcfce7,#86efac);color:#16a34a;"><i class="bi bi-recycle"></i></span>
                                    <span><strong>Sürdürülebilirlik</strong><small>Yeşil hosting taahhüdümüz</small></span>
                                </a>
                                <a href="/sistem-durumu.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;"><i class="bi bi-activity"></i></span>
                                    <span><strong>Sistem Durumu</strong><small>Gerçek zamanlı uptime</small></span>
                                </a>
                            </div>
                            <div class="cdg-nav-mega-col">
                                <div class="cdg-nav-mega-title">Yasal</div>
                                <a href="/kvkk-aydinlatma-metni.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;"><i class="bi bi-shield-lock"></i></span>
                                    <span><strong>KVKK</strong><small>Kişisel verilerin korunması</small></span>
                                </a>
                                <a href="/cerez-politikasi.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fce7f3,#fbcfe8);color:#9d174d;"><i class="bi bi-cookie"></i></span>
                                    <span><strong>Çerez Politikası</strong><small>Cookie kullanımımız</small></span>
                                </a>
                                <a href="/gizlilik-politikasi.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);color:#4338ca;"><i class="bi bi-file-earmark-text"></i></span>
                                    <span><strong>Gizlilik Sözleşmesi</strong><small>Veri işleme politikamız</small></span>
                                </a>
                                <a href="/hizmet-sozlesmesi.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#cffafe,#a5f3fc);color:#0e7490;"><i class="bi bi-file-earmark-check"></i></span>
                                    <span><strong>Hizmet Sözleşmesi</strong><small>Üyelik koşullarımız</small></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="<?php echo $cdg_hosting_url; ?>" class="<?php echo cdg_active(['products','hosting-products','products-hosting'], $current_page); ?>">Hosting</a></li>
                <li><a href="<?php echo cdg_link('domain'); ?>"                class="<?php echo cdg_active('domain', $current_page); ?>">Domain</a></li>
                <li class="cdg-nav-mega-parent">
                    <a href="javascript:void(0);" class="cdg-nav-mega-toggle <?php echo cdg_active(['softwares','special-products','erp'], $current_page); ?>">Yazılım <i class="bi bi-chevron-down"></i></a>
                    <div class="cdg-nav-mega">
                        <div class="cdg-nav-mega-grid">
                            <div class="cdg-nav-mega-col">
                                <div class="cdg-nav-mega-title">Hazır Çözümler</div>
                                <a href="/erp-yazilimi.html" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1e40af;"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                                    <span><strong>CODEGA ERP</strong><small>9 modül entegre kurumsal kaynak planlaması</small></span>
                                </a>
                                <a href="<?php echo cdg_link('softwares'); ?>" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#b45309;"><i class="bi bi-box-seam-fill"></i></span>
                                    <span><strong>Tüm Yazılımlar</strong><small>Hazır yazılım ürün kataloğu</small></span>
                                </a>
                            </div>
                            <div class="cdg-nav-mega-col">
                                <div class="cdg-nav-mega-title">Özel Geliştirme</div>
                                <a href="<?php echo cdg_link('softwares'); ?>" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);color:#4338ca;"><i class="bi bi-code-slash"></i></span>
                                    <span><strong>Özel PHP Yazılım</strong><small>İşletmenize özel yazılım geliştirme</small></span>
                                </a>
                                <a href="<?php echo cdg_link('contact'); ?>?subject=demo" class="cdg-nav-mega-link">
                                    <span class="cdg-nav-mega-ico" style="background:linear-gradient(135deg,#fce7f3,#fbcfe8);color:#9d174d;"><i class="bi bi-rocket-takeoff-fill"></i></span>
                                    <span><strong>Demo Talep Et</strong><small>14 gün ücretsiz deneme</small></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="<?php echo cdg_link('knowledgebase'); ?>"         class="<?php echo cdg_active(['knowledgebase','articles','news'], $current_page); ?>">Bilgi</a></li>
                <li><a href="<?php echo cdg_link('contact'); ?>"               class="<?php echo cdg_active('contact', $current_page); ?>">İletişim</a></li>

                <!-- MOBİL MENÜ EKLERİ (sadece mobilde görünür, .cdg-nav.open içinde) -->
                <li class="cdg-nav-mobile-only"><hr style="border:0;border-top:1px solid #e2e8f0;margin:8px 0;" /></li>
                <?php if($sign_in): ?>
                    <li class="cdg-nav-mobile-only"><a href="<?php echo (isset($my_account_link) && $my_account_link && $my_account_link != '#') ? $my_account_link : cdg_link('my-account'); ?>"><i class="bi bi-person-circle"></i> Hesabım</a></li>
                    <li class="cdg-nav-mobile-only"><a href="<?php echo (isset($logout_link) && $logout_link && $logout_link != '#') ? $logout_link : cdg_link('logout'); ?>" style="color:#dc2626;"><i class="bi bi-box-arrow-right"></i> Çıkış Yap</a></li>
                <?php else: ?>
                    <li class="cdg-nav-mobile-only"><a href="<?php echo (isset($login_link) && $login_link && $login_link != '#') ? $login_link : cdg_link('sign-in'); ?>"><i class="bi bi-box-arrow-in-right"></i> Giriş Yap</a></li>
                    <li class="cdg-nav-mobile-only"><a href="<?php echo (isset($register_link) && $register_link && $register_link != '#') ? $register_link : cdg_link('sign-up'); ?>" style="color:#1e40af;font-weight:700;"><i class="bi bi-person-plus"></i> Kayıt Ol</a></li>
                <?php endif; ?>
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
                        <i class="bi bi-person-circle"></i> <span>Hesabım</span>
                    </a>
                    <a href="<?php echo (isset($logout_link) && $logout_link && $logout_link != '#') ? $logout_link : cdg_link('logout'); ?>" class="cdg-btn cdg-btn-ghost cdg-btn-sm" title="Çıkış">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo (isset($login_link) && $login_link && $login_link != '#') ? $login_link : cdg_link('sign-in'); ?>"    class="cdg-btn cdg-btn-ghost cdg-btn-sm"><i class="bi bi-box-arrow-in-right"></i> <span>Giriş</span></a>
                    <a href="<?php echo (isset($register_link) && $register_link && $register_link != '#') ? $register_link : cdg_link('sign-up'); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm"><i class="bi bi-person-plus"></i> <span>Kayıt Ol</span></a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>
