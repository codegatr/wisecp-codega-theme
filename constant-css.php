<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sabit CSS Yüklemeleri & Tema-Genel Stiller
 * Head içinde yüklenir, tüm sayfalarda gerekli core stilleri sağlar
 *
 * NOT: Bootstrap Icons LOKAL olarak inc/main-head.php tarafından yükleniyor.
 *      CDN kullanmıyoruz çünkü Edge Tracking Prevention bloke ediyor.
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
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/style/theme.css?v=<?php echo htmlspecialchars($license_ver, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/plugins/iziModal/css/iziModal.min.css?v=<?php echo htmlspecialchars($license_ver, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>assets/plugins/sweetalert2/dist/sweetalert2.min.css">
<?php endif; ?>

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

/* SweetAlert2 alert_success / alert_error stilleri */
.alert-info, .alert-success, .alert-error, .alert-warning {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

/* Scroll davranışı */
html { scroll-behavior: smooth; }

/* Codega seçim rengi */
::selection { background: rgba(30,64,175,0.18); color: #0f172a; }
::-moz-selection { background: rgba(30,64,175,0.18); color: #0f172a; }

/* Public header - Sepet butonu badge */
.cdg-cart-btn {
    position: relative;
}
.cdg-cart-btn .cdg-cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    line-height: 18px;
    text-align: center;
    border-radius: 100px;
    box-shadow: 0 2px 6px rgba(220,38,38,0.40);
    border: 2px solid #fff;
    box-sizing: content-box;
}
.cdg-cart-btn:hover { transform: translateY(-1px); }

/* Codega buton variant'lari (header + butun temada kullaniliyor) */
.cdg-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    line-height: 1;
    white-space: nowrap;
    font-family: inherit;
}
.cdg-btn-sm {
    padding: 8px 14px;
    font-size: 13px;
    border-radius: 8px;
}
.cdg-btn-primary {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: #fff !important;
    border-color: transparent;
}
.cdg-btn-primary:hover {
    background: linear-gradient(135deg, #1e40af, #1e3a8a);
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(30,64,175,0.30);
}
.cdg-btn-outline {
    background: transparent;
    color: #0f172a !important;
    border-color: #cbd5e1;
}
.cdg-btn-outline:hover {
    background: #f8fafc;
    border-color: #1e40af;
    color: #1e40af !important;
}
.cdg-btn-ghost {
    background: transparent;
    color: #0f172a !important;
    border-color: transparent;
}
.cdg-btn-ghost:hover {
    background: #f1f5f9;
    color: #1e40af !important;
}
.cdg-btn-ghost i, .cdg-btn-outline i, .cdg-btn-primary i { font-size: 16px; }

/* Header nav actions container */
.cdg-nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

</style>
