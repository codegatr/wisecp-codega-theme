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
        // 1) WiseCP Controllers::CRLink (Türkçe slug + ID resolution)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            $url = Controllers::$init->CRLink($slug, $params);

            // Hatalı (0) sonucunu yakala - WiseCP "hosting" gibi string'i ID'ye çeviremezse 0 döner
            // Bu durumda manuel slug URL'sine fallback yap
            if($url && (strpos($url, '/(0)') !== false || preg_match('#/0/?$#', $url) || preg_match('#/0\?#', $url))) {
                // Manuel: WiseCP base URL + slug + params
                $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
                return $base . '/' . $slug . ($params ? '/' . implode('/', $params) : '');
            }
            return $url;
        }
        // 2) Fallback: manuel URL
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        return $base . '/' . $slug . ($params ? '/' . implode('/', $params) : '');
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
                        <li><i class="bi bi-telephone"></i> <a href="tel:<?php echo preg_replace('/[^0-9+]/','',$phone); ?>"><?php echo htmlspecialchars($phone); ?></a></li>
                    <?php endif; ?>
                    <?php if($mail): ?>
                        <li><i class="bi bi-envelope"></i> <a href="mailto:<?php echo $mail; ?>"><?php echo htmlspecialchars($mail); ?></a></li>
                    <?php endif; ?>
                    <?php if($addr): ?>
                        <li><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($addr); ?></li>
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
