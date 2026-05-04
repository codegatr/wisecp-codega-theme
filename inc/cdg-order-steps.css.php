<?php
/**
 * cdg-order-steps.css.php
 *
 * Tum order-steps-*.php dosyalari icin Forte-tarzi kurumsal CSS override.
 * Yesil tema (#10b981/#059669), beyaz zemin, sade ve profesyonel.
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

$cdg_os_kind = $cdg_os_kind ?? 'hosting';
$cdg_step = $step ?? 1;

// 5 adimli stepper'da hangi step aktif?
$cdg_active_step = 3;
if($cdg_step === 'domain' || $cdg_step === 2) {
    $cdg_active_step = 2;
} elseif($cdg_step === 1 || $cdg_step === 'addons' || $cdg_step === 'profile') {
    $cdg_active_step = 3;
}

$cdg_section_title = 'Sepet';
$cdg_page_title = '';

if($cdg_step === 1) {
    $cdg_page_title = 'Hizmet Süresi Seçimi';
} elseif($cdg_step === 'domain' || $cdg_step === 2) {
    $cdg_page_title = '';
} elseif($cdg_step === 'addons') {
    $cdg_page_title = 'Ek Hizmetler';
} elseif($cdg_step === 'profile') {
    $cdg_page_title = 'Profil Bilgileri';
}

include __DIR__ . '/cdg-checkout-stepper.php';
?>

<style>
/* CODEGA ORDER-STEPS - Forte-tarzi Override */

/* Sari hero ve eski ilanasamalar gizle */
.cdg-page-hero,
.cdg-domain-badge { display: none !important; }
#wrapper .ilanasamalar,
#wrapper .asamaline { display: none !important; }

#wrapper {
    background: #f5f6f8;
    padding: 0 0 60px;
    min-height: 70vh;
    margin-top: 0 !important;
}

/* .pakettitle (adim basligi) Forte-stiline cevir */
#wrapper .pakettitle {
    background: transparent;
    border: 0;
    padding: 0 !important;
    margin: 0 0 8px !important;
    box-shadow: none;
}
#wrapper .pakettitle h1 {
    color: #0f172a !important;
    font-size: 22px !important;
    font-weight: 800 !important;
    margin: 0 0 6px !important;
    letter-spacing: -0.4px;
}
#wrapper .pakettitle h1 strong { font-weight: 800; }
#wrapper .pakettitle h2 {
    color: #64748b !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    margin: 0 0 18px !important;
    line-height: 1.6;
}
#wrapper .pakettitle .line { display: none !important; }

#wrapper > .cdg-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}

#wrapper .siparisbilgileri {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 28px !important;
    margin-bottom: 22px !important;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}

/* Periyod kartlari */
#wrapper .orderperiodblock-con {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
    gap: 14px !important;
    margin: 18px 0 !important;
}
#wrapper .orderperiodblock {
    background: #fff !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 22px 18px !important;
    text-align: left !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    position: relative;
    overflow: hidden;
}
#wrapper .orderperiodblock:hover {
    border-color: #10b981 !important;
    box-shadow: 0 4px 12px rgba(16,185,129,0.10);
}
#wrapper .orderperiodblock.active {
    border-color: #10b981 !important;
    background: linear-gradient(135deg, #fff 0%, #ecfdf5 100%) !important;
    box-shadow: 0 6px 16px rgba(16,185,129,0.18) !important;
}
#wrapper .orderperiodblock h3 {
    color: #0f172a !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    margin: 0 0 4px !important;
    text-transform: none !important;
    letter-spacing: 0;
}
#wrapper .orderperiodblock.active h3 { color: #047857 !important; }
#wrapper .orderperiodblock h2 {
    color: #475569 !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    margin: 0 !important;
    line-height: 1.4;
}
#wrapper .orderperiodblock.active h2 { color: #059669 !important; }

#wrapper .orderperiodblock .periodselectbox {
    width: 22px !important;
    height: 22px !important;
    border: 2px solid #cbd5e1 !important;
    border-radius: 50% !important;
    margin: 0 !important;
    display: grid !important;
    place-items: center !important;
    transition: all 0.2s !important;
    position: absolute !important;
    top: 18px !important;
    right: 18px !important;
}
#wrapper .orderperiodblock.active .periodselectbox {
    background: #10b981 !important;
    border-color: #10b981 !important;
}
#wrapper .orderperiodblock.active .periodselectbox i {
    color: #fff !important;
    font-size: 11px;
}
#wrapper .orderperiodblock .periodselectbox i { color: transparent; font-size: 11px; }

/* Tasarruf rozet - mor pill (Forte tarzı) */
#wrapper .ribbonperiod {
    position: absolute !important;
    top: -10px !important;
    right: 12px !important;
    z-index: 2;
    left: auto !important;
}
#wrapper .ribbonperiod span {
    display: inline-block !important;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
    color: #fff !important;
    padding: 4px 11px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    border-radius: 99px !important;
    letter-spacing: 0;
    text-transform: none !important;
    box-shadow: 0 4px 10px rgba(139,92,246,0.30);
}
#wrapper .setup-fee-period {
    display: block !important;
    color: #94a3b8 !important;
    font-size: 11.5px !important;
    margin-top: 6px !important;
    font-weight: 500 !important;
}

/* Devam butonu - yesil pill */
#wrapper .gonderbtn,
#wrapper .btn.mio-ajax-submit,
#wrapper .gonderbtn.mio-ajax-submit {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 13px 32px !important;
    border-radius: 99px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.2s !important;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #fff !important;
    border: 0 !important;
    cursor: pointer !important;
    box-shadow: 0 4px 12px rgba(16,185,129,0.28);
    min-width: 160px;
}
#wrapper .gonderbtn:hover,
#wrapper .btn.mio-ajax-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(16,185,129,0.36);
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
}

/* Domain check & form input */
#wrapper .alanadisorgu {
    background: #f8fafc !important;
    padding: 24px !important;
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
}
#wrapper .alanadisorgu input[type="text"] {
    width: 100% !important;
    padding: 13px 16px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    font-family: inherit !important;
    color: #0f172a;
    background: #fff;
}
#wrapper .alanadisorgu input[type="text"]:focus {
    border-color: #10b981 !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.10);
}
#wrapper .alanadisorgu h5 {
    font-size: 13px !important;
    color: #64748b !important;
    margin-top: 14px !important;
    font-weight: 500 !important;
    line-height: 1.7;
}

/* Domain step radio butonlari */
#wrapper .checkbox-custom-label,
#wrapper .radio-custom-label {
    padding: 8px 0 8px 4px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    color: #0f172a !important;
    font-weight: 600 !important;
}

/* Accordion (ek hizmetler) */
#wrapper #accordion h3 {
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 14px 18px !important;
    margin-bottom: 8px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}
#wrapper #accordion h3.ui-state-active {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    color: #fff !important;
    border-color: #10b981 !important;
}
#wrapper #accordion .ui-accordion-content {
    border: 1px solid #e2e8f0;
    border-top: 0;
    border-radius: 0 0 10px 10px;
    padding: 18px;
    background: #fafbfc;
}

/* Domain check sonuc */
#wrapper .tescilsonuc {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 20px !important;
    margin-top: 14px !important;
}
#wrapper .tescilsonuc h4 {
    color: #0f172a !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    margin: 0 0 8px !important;
}
#wrapper .tescilsonuc .error { color: #dc2626 !important; }
#wrapper .tescilsonuc .success { color: #059669 !important; }

#wrapper .error#result {
    padding: 14px 16px !important;
    background: #fef2f2 !important;
    border: 1px solid #fecaca !important;
    border-radius: 10px !important;
    color: #991b1b !important;
    font-weight: 600 !important;
    font-size: 13px;
}

#wrapper .blue-info,
#wrapper .info-box {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 10px !important;
    padding: 14px 18px !important;
    color: #1e40af !important;
    font-size: 13.5px;
    line-height: 1.6;
}

#wrapper .zorunlu { color: #ef4444 !important; font-weight: 700 !important; }

#wrapper input[type="text"],
#wrapper input[type="email"],
#wrapper input[type="tel"],
#wrapper input[type="password"],
#wrapper input[type="number"],
#wrapper select,
#wrapper textarea {
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-family: inherit;
    font-size: 13.5px;
    color: #0f172a;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
}
#wrapper input:focus,
#wrapper select:focus,
#wrapper textarea:focus {
    border-color: #10b981;
    outline: none;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.10);
}

#wrapper .clear { clear: both; }
</style>
