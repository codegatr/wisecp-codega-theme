<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Brand kimligi defansif yukle (logo SVG icin)
if(!function_exists('cdg_logo_svg')) {
    $_brand_inc = __DIR__ . DIRECTORY_SEPARATOR . 'cdg-brand.php';
    if(file_exists($_brand_inc)) include_once $_brand_inc;
}

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

<style>
/* === FOOTER INLINE FALLBACK CSS ===
 * style.css ve wisecp.css yüklenmezse bile footer görünsün diye.
 * Bu kurallar düşük specificity'de, normal CSS yüklenirse override edilir.
 */
footer.cdg-footer {
    background: #0f172a;
    color: rgba(255,255,255,0.7);
    padding: 70px 0 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    margin-top: 60px;
}
footer.cdg-footer .cdg-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
footer.cdg-footer .cdg-footer-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr 1.1fr 1.1fr 1fr;
    gap: 40px;
    margin-bottom: 50px;
}
@media (max-width: 1100px) { footer.cdg-footer .cdg-footer-grid { grid-template-columns: 1.4fr 1fr 1fr; gap: 32px; } footer.cdg-footer .cdg-footer-grid > div:nth-child(1) { grid-column: 1 / -1; } }
@media (max-width: 880px) { footer.cdg-footer .cdg-footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; } footer.cdg-footer .cdg-footer-grid > div:nth-child(1) { grid-column: 1 / -1; } }
@media (max-width: 540px) { footer.cdg-footer .cdg-footer-grid { grid-template-columns: 1fr; } footer.cdg-footer .cdg-footer-grid > div:nth-child(1) { grid-column: auto; } }
footer.cdg-footer h4 { color: #fff; font-size: 14px; margin-bottom: 16px; letter-spacing: 0.02em; font-weight: 700; }
footer.cdg-footer ul { list-style: none; padding: 0; margin: 0; }
footer.cdg-footer li { margin-bottom: 8px; }
footer.cdg-footer a { color: rgba(255,255,255,0.6); font-size: 14px; text-decoration: none; transition: color .15s; }
footer.cdg-footer a:hover { color: #fff; }
footer.cdg-footer .brand { display: flex; align-items: center; gap: 10px; color: #fff; font-size: 22px; font-weight: 800; margin-bottom: 16px; }
footer.cdg-footer .desc { font-size: 14px; line-height: 1.65; max-width: 320px; margin-bottom: 18px; }
footer.cdg-footer .social { display: flex; gap: 8px; }
footer.cdg-footer .social a {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    display: grid; place-items: center;
    color: rgba(255,255,255,0.6);
    transition: all .2s;
}
footer.cdg-footer .social a:hover { background: #1e40af; color: #fff; transform: translateY(-2px); }
footer.cdg-footer .cdg-footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 22px 0;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    font-size: 13px; color: rgba(255,255,255,0.5);
}
footer.cdg-footer .cdg-footer-bottom > div { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
footer.cdg-footer .cdg-footer-bottom-center { justify-content: center; }
footer.cdg-footer .cdg-footer-bottom-right { justify-content: flex-end; }
footer.cdg-footer .cdg-footer-version {
    display: inline-flex; align-items: center; gap: 4px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    padding: 3px 9px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.3px;
}
footer.cdg-footer .cdg-footer-version-meta { color: rgba(255,255,255,0.4); font-size: 12px; }
footer.cdg-footer .cdg-footer-aksoy {
    display: inline-flex; align-items: baseline; gap: 6px;
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: #fff;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}
footer.cdg-footer .cdg-footer-aksoy strong { color: #fff; font-weight: 800; }
footer.cdg-footer .cdg-footer-aksoy span { color: rgba(255,255,255,0.85); font-weight: 500; }
@media (max-width: 768px) {
    footer.cdg-footer .cdg-footer-bottom { flex-direction: column; align-items: center; text-align: center; gap: 14px; }
    footer.cdg-footer .cdg-footer-bottom > div { justify-content: center; }
}
</style>

<footer class="cdg-footer">
    <div class="cdg-container">
        <div class="cdg-footer-grid">

            <div>
                <div class="brand cdg-footer-brand">
                    <?php if(function_exists('cdg_logo_svg')) {
                        echo cdg_logo_svg('white', 42);
                    } else { ?>
                    <span class="cdg-logo-mark">C</span>
                    CODEGA
                    <?php } ?>
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
                    <?php
                        // Hosting URL defansif hesaplama (header ile ayni mantik)
                        if(!isset($cdg_hosting_url) || !$cdg_hosting_url) {
                            $cdg_hosting_url = '';
                            if(isset($links) && is_array($links)) {
                                foreach(['hosting-products','products-hosting','hosting','public-hosting'] as $_k) {
                                    if(!empty($links[$_k]) && $links[$_k] !== '#') { $cdg_hosting_url = $links[$_k]; break; }
                                }
                            }
                            if(!$cdg_hosting_url) {
                                $_home = isset($home_link) && $home_link ? $home_link : (defined('APP_URI') ? APP_URI : '/');
                                $cdg_hosting_url = rtrim($_home, '/') . '#paketler';
                            }
                        }
                        ?>
                    <li><a href="<?php echo $cdg_hosting_url; ?>">Hosting</a></li>
                    <li><a href="<?php echo cdg_link('domain'); ?>">Domain</a></li>
                    <li><a href="/erp-yazilimi.html">CODEGA ERP</a></li>
                    <li><a href="<?php echo cdg_link('softwares'); ?>">Özel Yazılım</a></li>
                </ul>
            </div>

            <div>
                <h4>Şirket</h4>
                <ul>
                    <li><a href="/vizyon.html">Vizyon &amp; Değerlerimiz</a></li>
                    <li><a href="/hakkimizda.html">Hakkımızda</a></li>
                    <li><a href="/referanslarimiz.html">Referanslarımız</a></li>
                    <li><a href="/kariyer.html">Kariyer</a></li>
                    <li><a href="/sosyal-sorumluluk.html">Sosyal Sorumluluk</a></li>
                    <li><a href="/surdurulebilirlik.html">Sürdürülebilirlik</a></li>
                    <li><a href="/sistem-durumu.html">Sistem Durumu</a></li>
                    <li><a href="<?php echo cdg_link('knowledgebase'); ?>">Bilgi Bankası</a></li>
                </ul>
            </div>

            <div>
                <h4>Yasal</h4>
                <ul>
                    <li><a href="/kvkk-aydinlatma-metni.html">KVKK Aydınlatma Metni</a></li>
                    <li><a href="/gizlilik-politikasi.html">Gizlilik Politikası</a></li>
                    <li><a href="/cerez-politikasi.html">Çerez Politikası</a></li>
                    <li><a href="/hizmet-sozlesmesi.html">Hizmet Sözleşmesi</a></li>
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
            <div class="cdg-footer-bottom-left">
                <div>&copy; <?php echo $year; ?> CODEGA. Tüm hakları saklıdır.</div>
            </div>

            <div class="cdg-footer-bottom-center">
                <?php
                $cdg_theme_version = '';
                $cdg_theme_date = '';
                $cdg_theme_config_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'theme-config.php';
                if(file_exists($cdg_theme_config_file)) {
                    $cdg_theme_config = @include $cdg_theme_config_file;
                    if(is_array($cdg_theme_config)) {
                        $cdg_theme_version = $cdg_theme_config['version'] ?? '';
                    }
                }
                $cdg_version_json = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'version.json';
                if(file_exists($cdg_version_json)) {
                    $vj = @json_decode(file_get_contents($cdg_version_json), true);
                    if(is_array($vj)) {
                        $cdg_theme_date = $vj['release_date'] ?? '';
                    }
                }
                ?>
                <?php if($cdg_theme_version): ?>
                <span class="cdg-footer-version">v<?php echo htmlspecialchars($cdg_theme_version, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <?php endif; ?>
                <?php if($cdg_theme_date): ?>
                <span class="cdg-footer-version-meta">&middot; Güncelleme: <?php echo htmlspecialchars(date('d.m.Y', strtotime($cdg_theme_date)), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>

            <div class="cdg-footer-bottom-right">
                <a href="https://aksoy.web.tr" target="_blank" rel="noopener" class="cdg-footer-aksoy">
                    <strong>AKSOY GROUP</strong><span>iştirakidir</span>
                </a>
                <?php if(class_exists('View') && method_exists('View', 'show_brand')) View::show_brand(); ?>
            </div>
        </div>
    </div>
</footer>

<!-- CODEGA Frontend JS - mobile menu, scroll, smooth scroll, mega menu accordion -->
<script src="<?php echo isset($tadress) ? $tadress : ''; ?>js/script.js?v=<?php echo file_exists(__DIR__ . '/../js/script.js') ? filemtime(__DIR__ . '/../js/script.js') : 1; ?>" defer></script>
