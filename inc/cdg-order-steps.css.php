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

/* === 2 SUTUNLU LAYOUT (sol icerik / sag sticky ozet) === */
#wrapper .cdg-os-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 24px;
    align-items: start;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}
#wrapper .cdg-os-main { min-width: 0; }
#wrapper .cdg-os-aside {
    position: sticky;
    top: 24px;
}
@media (max-width: 1024px) {
    #wrapper .cdg-os-grid { grid-template-columns: 1fr; }
    #wrapper .cdg-os-aside { position: relative; top: 0; }
}

/* === SAG OZET KARTI (Forte stili) === */
#wrapper .cdg-os-summary {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(15,23,42,0.06);
}
#wrapper .cdg-os-summary-head {
    background: #fff;
    color: #0f172a;
    padding: 18px 22px;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #e2e8f0;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}
#wrapper .cdg-os-summary-head i { color: #10b981; font-size: 18px; }

#wrapper .cdg-os-summary-body { padding: 22px 22px 22px; }

/* Kategori badge (yesil pill - "EKONOMIK SSD HOSTING" gibi) */
#wrapper .cdg-os-pkg-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #d1fae5;
    color: #047857;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 12px;
}
#wrapper .cdg-os-pkg-badge::before {
    content: '\F26B';
    font-family: 'bootstrap-icons';
    font-size: 12px;
    color: #10b981;
}

/* Paket basligi (Linux Hosting 1) */
#wrapper .cdg-os-pkg-title {
    margin: 0 0 4px;
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    letter-spacing: -0.3px;
}

/* Alt baslik (Bireysel siteler) */
#wrapper .cdg-os-pkg-subtitle {
    margin: 0 0 18px;
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
}

/* Ozellik listesi (1 Web Sitesi, 5 GB NVMe SSD vb. - tikli) */
#wrapper .cdg-os-features {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 0 20px !important;
    border-top: 1px dashed #e2e8f0;
    padding-top: 16px !important;
}
#wrapper .cdg-os-features li {
    display: flex !important;
    align-items: flex-start;
    gap: 10px;
    padding: 9px 0 !important;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
    color: #334155;
    line-height: 1.5;
    list-style: none !important;
}
#wrapper .cdg-os-features li:last-child { border-bottom: 0; }
#wrapper .cdg-os-features li i {
    color: #10b981;
    font-size: 16px;
    flex-shrink: 0;
    margin-top: 1px;
}
#wrapper .cdg-os-features li span {
    flex: 1;
    color: #334155;
    font-weight: 500;
}
#wrapper .cdg-os-features li strong {
    color: #0f172a;
    font-weight: 700;
}

#wrapper .cdg-os-continue-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff !important;
    font-size: 14px;
    font-weight: 700;
    border-radius: 99px;
    text-decoration: none !important;
    border: 0;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(16,185,129,0.35);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
#wrapper .cdg-os-continue-btn:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 22px rgba(16,185,129,0.42);
    color: #fff !important;
}
#wrapper .cdg-os-continue-btn i { font-size: 16px; }

#wrapper .cdg-os-summary-trust {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px dashed #e2e8f0;
}
#wrapper .cdg-os-summary-trust span {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}
#wrapper .cdg-os-summary-trust i { color: #10b981; font-size: 13px; }

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

#wrapper .siparisbilgileri {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 28px !important;
    margin-bottom: 22px !important;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}

/* === Periyod kartlari (Forte tarzi - sol radio + sag icerik) === */
#wrapper .orderperiodblock-con {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
    gap: 14px !important;
    margin: 18px 0 !important;
}
#wrapper .orderperiodblock {
    background: #fff !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 18px 18px 18px 56px !important;
    text-align: left !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    position: relative;
    min-height: 96px;
    display: flex;
    align-items: center;
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

/* Sol radio dairesi */
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
    top: 50% !important;
    left: 18px !important;
    transform: translateY(-50%) !important;
    background: #fff;
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

/* Sag icerik (cdg-period-content) */
#wrapper .orderperiodblock .cdg-period-content {
    flex: 1;
    min-width: 0;
}
#wrapper .orderperiodblock .cdg-period-name,
#wrapper .orderperiodblock h3 {
    color: #0f172a !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    margin: 0 0 6px !important;
    text-transform: none !important;
    letter-spacing: 0;
    line-height: 1.2;
}
#wrapper .orderperiodblock.active .cdg-period-name,
#wrapper .orderperiodblock.active h3 { color: #047857 !important; }

#wrapper .orderperiodblock .cdg-period-price,
#wrapper .orderperiodblock h2 {
    color: #0f172a !important;
    font-size: 20px !important;
    font-weight: 800 !important;
    margin: 0 !important;
    line-height: 1.1;
    letter-spacing: -0.3px;
}
#wrapper .orderperiodblock.active .cdg-period-price,
#wrapper .orderperiodblock.active h2 { color: #059669 !important; }

/* Tasarruf rozet - mor pill */
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

/* === Eski form ici Devam butonu (.gonderbtn) - artik sag karta tasidik
   ama domain step'inde hala kullaniliyor === */
#wrapper .gonderbtn,
#wrapper .btn.mio-ajax-submit:not(.cdg-os-continue-btn),
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
#wrapper .btn.mio-ajax-submit:not(.cdg-os-continue-btn):hover {
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
