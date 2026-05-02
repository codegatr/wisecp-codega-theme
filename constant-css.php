<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sabit CSS Yüklemeleri & Tema-Genel Stiller
 * Head içinde yüklenir, tüm sayfalarda gerekli core stilleri sağlar
 *
 * WiseCP runtime: $baddress, License::get_version()
 */

$baddress = isset($baddress) ? $baddress : '';
$license_ver = '1';
if(class_exists('License') && method_exists('License','get_version')) {
    try { $license_ver = License::get_version(); } catch(\Throwable $e) { $license_ver = '1'; }
}
?>

<!-- WiseCP Core Stiller -->
<?php if($baddress): ?>
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress); ?>assets/style/theme.css?v=<?php echo htmlspecialchars($license_ver); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress); ?>assets/plugins/iziModal/css/iziModal.min.css?v=<?php echo htmlspecialchars($license_ver); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress); ?>assets/plugins/sweetalert2/dist/sweetalert2.min.css">
<?php endif; ?>

<!-- Bootstrap Icons (Codega tema CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Plus Jakarta Sans (Codega tema fontu) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style type="text/css">
/* === Codega: Tema-genel sabit stiller === */

/* WiseCP varsayılan miotab gizleme */
.miotab-content { display: none; }

/* SweetAlert2 Codega rengi */
.swal2-popup .swal2-title { font-family: 'Plus Jakarta Sans', sans-serif !important; font-weight: 800 !important; }
.swal2-popup .swal2-content { font-family: 'Plus Jakarta Sans', sans-serif !important; }
.swal2-styled.swal2-confirm {
    background: linear-gradient(135deg, #1e40af, #3b82f6) !important;
    border: 0 !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 6px 18px rgba(30,64,175,0.22) !important;
}
.swal2-styled.swal2-cancel {
    background: #fff !important;
    color: #0f172a !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
}

/* iziModal genel uyum */
.iziModal { font-family: 'Plus Jakarta Sans', sans-serif !important; }

/* SweetAlert2 alert_success / alert_error stilleri (webmio) */
.alert-info, .alert-success, .alert-error, .alert-warning {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

/* Tema-genel scroll davranışı */
html { scroll-behavior: smooth; }

/* Codega seçim rengi */
::selection { background: rgba(30,64,175,0.18); color: #0f172a; }
::-moz-selection { background: rgba(30,64,175,0.18); color: #0f172a; }
</style>
