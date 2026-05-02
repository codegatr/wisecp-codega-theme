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

/* === Codega Cookie Policy Popup === */
/* WiseCP webmio.js'in beklediği #mio-cookie-popup ID'siyle uyumlu */
#mio-cookie-popup {
    position: fixed;
    left: 50% !important;
    bottom: 20px !important;
    transform: translateX(-50%) !important;
    max-width: 540px;
    width: calc(100% - 32px);
    z-index: 9998;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    animation: cdgCookieIn 0.4s ease;
    box-sizing: border-box;
}
#mio-cookie-popup *, #mio-cookie-popup *::before, #mio-cookie-popup *::after { box-sizing: border-box; }
@keyframes cdgCookieIn {
    from { opacity: 0; transform: translate(-50%, 16px); }
    to   { opacity: 1; transform: translate(-50%, 0); }
}
#mio-cookie-popup .cdg-cookie-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 16px 40px rgba(15,23,42,0.25);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
#mio-cookie-popup .cdg-cookie-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
#mio-cookie-popup .cdg-cookie-content { flex: 1; min-width: 200px; }
#mio-cookie-popup .cdg-cookie-content h3 {
    font-size: 14px; font-weight: 800;
    margin: 0 0 4px;
    color: #0f172a;
}
#mio-cookie-popup .cdg-cookie-content p {
    font-size: 12px; color: #64748b;
    margin: 0; line-height: 1.5;
}
#mio-cookie-popup .cdg-cookie-content a {
    color: #1e40af; font-weight: 700; text-decoration: none;
}
#mio-cookie-popup .cdg-cookie-content a:hover { text-decoration: underline; }
#mio-cookie-popup .mio-cookie-popup__c-p-button,
#mio-cookie-popup .cdg-cookie-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff !important;
    border: 0;
    border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(16,185,129,0.22);
    transition: transform 0.18s;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}
#mio-cookie-popup .mio-cookie-popup__c-p-button:hover,
#mio-cookie-popup .cdg-cookie-btn:hover { transform: translateY(-1px); color: #fff !important; }
@media (max-width: 600px) {
    #mio-cookie-popup .cdg-cookie-card { flex-direction: column; text-align: center; }
    #mio-cookie-popup .cdg-cookie-btn,
    #mio-cookie-popup .mio-cookie-popup__c-p-button { width: 100%; justify-content: center; }
}
</style>
