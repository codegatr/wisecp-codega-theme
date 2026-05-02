<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sabit JavaScript Değişkenleri & Plugin Yüklemeleri
 * Header sonunda yüklenir, tüm sayfalarda erişilebilir global değişkenler
 *
 * NOT: Cookie popup CSS'i constant-css.php'de tanımlı.
 *      Bu dosya sadece HTML şablonunu ve global değişkenleri sağlar.
 *
 * WiseCP runtime: Controllers::$init, UserManager, Config, License, Models, $baddress
 */

// my-account link (online güncelleme için)
$update_online_link = '#';
if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
    $update_online_link = Controllers::$init->CRLink('my-account');
}

// Login durumu
$is_logged = false;
if(class_exists('UserManager') && method_exists('UserManager','LoginCheck')) {
    $is_logged = UserManager::LoginCheck('member') ? true : false;
}

// Modal başlıkları (locale)
$warning_title = 'Uyarı';
$success_title = 'Başarılı';
if(function_exists('___')) {
    $w = ___('needs/warning-modal-title', ['"' => '\\"']);
    $s = ___('needs/success-modal-title', ['"' => '\\"']);
    if($w) $warning_title = $w;
    if($s) $success_title = $s;
}

// Cookie policy aktif mi?
$cookie_policy_active = false;
if(class_exists('Config') && Config::get('options/cookie-policy/status')) {
    $cookie_policy_active = true;
    $cookie_title = function_exists('__') ? __('website/index/cookie-policy-1') : 'Çerez Politikası';
    $cookie_btn   = function_exists('__') ? __('website/index/cookie-policy-3') : 'Anladım';

    // Cookie politika sayfası linki
    $page_link = '#';
    if(class_exists('Models') && isset(Models::$init) && method_exists(Models::$init, 'link_detector')) {
        $cp_page = Config::get('options/cookie-policy/page');
        if($cp_page) $page_link = Models::$init->link_detector('pages/' . $cp_page);
    }

    $cookie_text = function_exists('__')
        ? __('website/index/cookie-policy-2', ['{page_link}' => $page_link])
        : 'Bu site kullanıcı deneyimini iyileştirmek için çerezler kullanır. <a href="' . htmlspecialchars($page_link) . '">Çerez politikamız</a> hakkında daha fazla bilgi alabilirsiniz.';

    // JS string için escape (apostrof + newline)
    $cookie_title_js = str_replace(["'", "\n", "\r"], ['&#x27;', ' ', ''], $cookie_title);
    $cookie_text_js  = str_replace(["'", "\n", "\r"], ['&#x27;', ' ', ''], $cookie_text);
    $cookie_btn_js   = str_replace(["'", "\n", "\r"], ['&#x27;', ' ', ''], $cookie_btn);
}

// License version
$license_ver = '1';
if(class_exists('License') && method_exists('License','get_version')) {
    try { $license_ver = License::get_version(); } catch(\Throwable $e) { $license_ver = '1'; }
}
$baddress = isset($baddress) ? $baddress : '';
?>

<script type="text/javascript">
// === Codega: Global JS Değişkenleri ===
var update_online_link  = "<?php echo htmlspecialchars($update_online_link, ENT_QUOTES); ?>";
var is_logged           = <?php echo $is_logged ? 'true' : 'false'; ?>;
var warning_modal_title = "<?php echo $warning_title; ?>";
var success_modal_title = "<?php echo $success_title; ?>";

<?php if($cookie_policy_active): ?>
// === Codega: Cookie Policy Popup HTML ===
// CSS constant-css.php içinde tanımlı (#mio-cookie-popup)
// webmio.js bu HTML'i alır, ckplcyCheckCookie() ile gerekirse DOM'a ekler
var ckplcy_cookie_popup_html = '<div id="mio-cookie-popup">' +
    '<div class="cdg-cookie-card">' +
        '<div class="cdg-cookie-icon"><i class="bi bi-shield-check"></i></div>' +
        '<div class="cdg-cookie-content">' +
            '<h3><?php echo $cookie_title_js; ?></h3>' +
            '<p><?php echo $cookie_text_js; ?></p>' +
        '</div>' +
        '<button class="mio-cookie-popup__c-p-button cdg-cookie-btn" type="button"><i class="bi bi-check-lg"></i> <?php echo $cookie_btn_js; ?></button>' +
    '</div>' +
'</div>';

// DOM hazır olduktan sonra cookie kontrolünü başlat
// (webmio.js'in classList hatası DOM hazır olmadan tetiklenir)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function(){
        setTimeout(function(){
            try { if(typeof ckplcyCheckCookie === 'function') ckplcyCheckCookie(); } catch(e) { console.warn('Cookie popup init:', e); }
        }, 500);
    });
} else {
    setTimeout(function(){
        try { if(typeof ckplcyCheckCookie === 'function') ckplcyCheckCookie(); } catch(e) { console.warn('Cookie popup init:', e); }
    }, 500);
}
<?php endif; ?>
</script>

<!-- WiseCP Core Plugins -->
<?php if($baddress): ?>
<script src="<?php echo htmlspecialchars($baddress); ?>assets/plugins/iziModal/js/iziModal.min.js?v=<?php echo htmlspecialchars($license_ver); ?>"></script>
<script src="<?php echo htmlspecialchars($baddress); ?>assets/plugins/sweetalert2/dist/promise.min.js"></script>
<script src="<?php echo htmlspecialchars($baddress); ?>assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?php echo htmlspecialchars($baddress); ?>assets/javascript/jquery.form.min.js"></script>
<script src="<?php echo htmlspecialchars($baddress); ?>assets/javascript/webmio.js?v=<?php echo htmlspecialchars($license_ver); ?>"></script>
<?php endif; ?>
