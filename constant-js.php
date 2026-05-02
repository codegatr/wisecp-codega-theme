<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sabit JavaScript Değişkenleri & Plugin Yüklemeleri
 * Header sonunda yüklenir, tüm sayfalarda erişilebilir global değişkenler
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
$cookie_html = '';
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

    // JS string için escape
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
// === Codega: Cookie Policy Popup ===
var ckplcy_cookie_popup_html = '<div id="mio-cookie-popup" class="cdg-cookie-popup">' +
    '<div class="cdg-cookie-card">' +
        '<div class="cdg-cookie-icon"><i class="bi bi-shield-check"></i></div>' +
        '<div class="cdg-cookie-content">' +
            '<h3><?php echo $cookie_title_js; ?></h3>' +
            '<p><?php echo $cookie_text_js; ?></p>' +
        '</div>' +
        '<button class="mio-cookie-popup__c-p-button cdg-cookie-btn"><i class="bi bi-check-lg"></i> <?php echo $cookie_btn_js; ?></button>' +
    '</div>' +
'</div>';

// Cookie popup için Codega stili
(function(){
    var style = document.createElement('style');
    style.textContent = '.cdg-cookie-popup{position:fixed;left:50%;bottom:20px;transform:translateX(-50%);max-width:520px;width:calc(100% - 32px);z-index:9998;font-family:"Plus Jakarta Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;animation:cdgCookieIn 0.4s ease;}'
        + '@keyframes cdgCookieIn{from{opacity:0;transform:translate(-50%,16px)}to{opacity:1;transform:translate(-50%,0)}}'
        + '.cdg-cookie-card{background:#fff;border-radius:14px;padding:18px 20px;box-shadow:0 16px 40px rgba(15,23,42,0.25);border:1px solid #e2e8f0;display:flex;align-items:center;gap:14px;flex-wrap:wrap;}'
        + '.cdg-cookie-icon{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;display:grid;place-items:center;font-size:22px;flex-shrink:0;}'
        + '.cdg-cookie-content{flex:1;min-width:200px;}'
        + '.cdg-cookie-content h3{font-size:14px;font-weight:800;margin:0 0 4px;color:#0f172a;}'
        + '.cdg-cookie-content p{font-size:12px;color:#64748b;margin:0;line-height:1.5;}'
        + '.cdg-cookie-content a{color:#1e40af;font-weight:700;text-decoration:none;}'
        + '.cdg-cookie-btn{display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:linear-gradient(135deg,#10b981,#34d399);color:#fff;border:0;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,0.22);transition:transform 0.18s;font-family:inherit;}'
        + '.cdg-cookie-btn:hover{transform:translateY(-1px);}'
        + '@media(max-width:600px){.cdg-cookie-card{flex-direction:column;text-align:center;}.cdg-cookie-btn{width:100%;justify-content:center;}}';
    document.head.appendChild(style);
})();

setTimeout(function(){
    if(typeof ckplcyCheckCookie === 'function') ckplcyCheckCookie();
}, 1000);
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
