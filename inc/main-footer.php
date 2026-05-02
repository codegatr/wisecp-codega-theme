<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$config    = include __DIR__ . "/../theme-config.php";
$ts        = isset($config['settings']) ? $config['settings'] : [];
$social    = isset($ts['social'])  ? $ts['social']  : [];
$contact_i = isset($ts['contact']) ? $ts['contact'] : [];

if(!isset($pnumbers))   $pnumbers   = [];
if(!isset($eaddresses)) $eaddresses = [];

$year = date('Y');

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

// Telefon ve mail için fallback (WiseCP $pnumbers/$eaddresses dolduruyor)
$phone = !empty($pnumbers[0]) ? $pnumbers[0] : (isset($contact_i['phone']) ? $contact_i['phone'] : '');
$mail  = !empty($eaddresses[0]) ? $eaddresses[0] : (isset($contact_i['email']) ? $contact_i['email'] : '');
$addr  = isset($contact_i['address']) ? $contact_i['address'] : '';
?>
<footer class="cdg-footer">
    <div class="cdg-container">
        <div class="cdg-footer-grid">

            <div>
                <div class="brand">
                    <span class="cdg-logo-mark">C</span>
                    CODEGA
                </div>
                <p class="desc">
                    Modern PHP altyapısıyla web yazılım, hosting, domain ve özel yazılım çözümleri sunuyoruz.
                </p>
                <div class="social">
                    <?php if(!empty($social['instagram'])): ?><a href="<?php echo $social['instagram']; ?>" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a><?php endif; ?>
                    <?php if(!empty($social['linkedin'])):  ?><a href="<?php echo $social['linkedin'];  ?>" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                    <?php if(!empty($social['github'])):    ?><a href="<?php echo $social['github'];    ?>" target="_blank" rel="noopener"><i class="bi bi-github"></i></a><?php endif; ?>
                </div>
            </div>

            <div>
                <h4>Hizmetler</h4>
                <ul>
                    <li><a href="<?php echo cdg_link('products', ['hosting']); ?>">Hosting</a></li>
                    <li><a href="<?php echo cdg_link('domain'); ?>">Domain</a></li>
                </ul>
            </div>

            <div>
                <h4>Şirket</h4>
                <ul>
                    <li><a href="<?php echo cdg_link('references'); ?>">Referanslar</a></li>
                    <li><a href="<?php echo cdg_link('knowledgebase'); ?>">Bilgi Bankası</a></li>
                    <li><a href="<?php echo cdg_link('news'); ?>">Haberler</a></li>
                    <li><a href="<?php echo cdg_link('contact'); ?>">İletişim</a></li>
                </ul>
            </div>

            <div>
                <h4>İletişim</h4>
                <ul>
                    <?php if($phone): ?>
                        <li><i class="bi bi-telephone"></i> <a href="tel:<?php echo preg_replace('/[^0-9+]/','',$phone); ?>"><?php echo htmlspecialchars($phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a></li>
                    <?php endif; ?>
                    <?php if($mail): ?>
                        <li><i class="bi bi-envelope"></i> <a href="mailto:<?php echo $mail; ?>"><?php echo htmlspecialchars($mail, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a></li>
                    <?php endif; ?>
                    <?php if($addr): ?>
                        <li><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($addr, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <div class="cdg-footer-bottom">
            <div>&copy; <?php echo $year; ?> CODEGA - Tüm haklari saklidir.</div>
            <?php if(class_exists('View') && method_exists('View', 'show_brand')) View::show_brand(); ?>
        </div>
    </div>
</footer>
