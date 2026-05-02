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
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress); ?>assets/style/theme.css?v=<?php echo htmlspecialchars($license_ver); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress); ?>assets/plugins/iziModal/css/iziModal.min.css?v=<?php echo htmlspecialchars($license_ver); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars($baddress); ?>assets/plugins/sweetalert2/dist/sweetalert2.min.css">
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

/* === Codega Cookie Policy Popup (Sağ-alt kompakt toast) === */
/* WiseCP webmio.js'in beklediği #mio-cookie-popup ID'siyle uyumlu */
#mio-cookie-popup {
    position: fixed;
    right: 20px !important;
    bottom: 20px !important;
    left: auto !important;
    top: auto !important;
    transform: none !important;
    max-width: 340px;
    width: calc(100vw - 40px);
    z-index: 9998;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    animation: cdgCookieIn 0.3s ease;
    box-sizing: border-box;
}
#mio-cookie-popup *, #mio-cookie-popup *::before, #mio-cookie-popup *::after { box-sizing: border-box; }
@keyframes cdgCookieIn {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
#mio-cookie-popup .cdg-cookie-card {
    background: #fff;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 12px 32px rgba(15,23,42,0.18);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    flex-direction: row;
    flex-wrap: nowrap;
}
#mio-cookie-popup .cdg-cookie-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: grid; place-items: center;
    font-size: 16px;
    flex-shrink: 0;
}
#mio-cookie-popup .cdg-cookie-content { flex: 1; min-width: 0; }
#mio-cookie-popup .cdg-cookie-content h3 {
    font-size: 12px; font-weight: 800;
    margin: 0 0 2px;
    color: #0f172a;
    line-height: 1.3;
}
#mio-cookie-popup .cdg-cookie-content p {
    font-size: 11px; color: #64748b;
    margin: 0 0 8px; line-height: 1.45;
}
#mio-cookie-popup .cdg-cookie-content a {
    color: #1e40af; font-weight: 700; text-decoration: none;
}
#mio-cookie-popup .cdg-cookie-content a:hover { text-decoration: underline; }
#mio-cookie-popup .mio-cookie-popup__c-p-button,
#mio-cookie-popup .cdg-cookie-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: #1e40af;
    color: #fff !important;
    border: 0;
    border-radius: 6px;
    font-size: 11px; font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
    box-shadow: none;
}
#mio-cookie-popup .mio-cookie-popup__c-p-button:hover,
#mio-cookie-popup .cdg-cookie-btn:hover { background: #1e3a8a; color: #fff !important; }
#mio-cookie-popup .mio-cookie-popup__c-p-button i,
#mio-cookie-popup .cdg-cookie-btn i { font-size: 12px; }
@media (max-width: 480px) {
    #mio-cookie-popup {
        right: 10px !important;
        bottom: 10px !important;
        max-width: calc(100vw - 20px);
    }
}
</style>
