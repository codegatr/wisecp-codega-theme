<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sabit JavaScript Değişkenleri & Plugin Yüklemeleri
 * Header sonunda yüklenir, tüm sayfalarda erişilebilir global değişkenler
 *
 * Cookie popup KAPALI - tema render etmiyor.
 * WiseCP admin panelinden cookie özelliği aktifse bile temada gözükmeyecek.
 *
 * WiseCP runtime: Controllers::$init, UserManager, Config, License, $baddress
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

// License version
$license_ver = '1';
if(class_exists('License') && method_exists('License','get_version')) {
    try { $license_ver = License::get_version(); } catch(\Throwable $e) { $license_ver = '1'; }
}
$baddress = isset($baddress) ? $baddress : '';
?>

<script type="text/javascript">
// === Codega: Global JS Değişkenleri ===
var update_online_link  = "<?php echo htmlspecialchars($update_online_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>";
var is_logged           = <?php echo $is_logged ? 'true' : 'false'; ?>;
var warning_modal_title = "<?php echo $warning_title; ?>";
var success_modal_title = "<?php echo $success_title; ?>";

// Cookie popup HTML'i bos string - webmio.js render etmez
var ckplcy_cookie_popup_html = '';
</script>

<!-- WiseCP Core Plugins -->
<?php if($baddress): ?>
<script src="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/plugins/iziModal/js/iziModal.min.js?v=<?php echo htmlspecialchars($license_ver, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/plugins/sweetalert2/dist/promise.min.js"></script>
<script src="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/javascript/jquery.form.min.js"></script>
<script src="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/javascript/webmio.js?v=<?php echo htmlspecialchars($license_ver, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></script>
<?php endif; ?>
