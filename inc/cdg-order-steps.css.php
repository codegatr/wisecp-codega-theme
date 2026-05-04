<?php
/**
 * cdg-order-steps.css.php
 *
 * Tum order-steps-*.php dosyalari icin ortak kurumsal CSS override.
 * Sari hero badge'i kaldirir, profesyonel paket ozet karti ekler.
 *
 * Kullanim (her order-steps-*.php basina):
 *   <?php include __DIR__ . '/inc/cdg-order-steps.css.php'; ?>
 *
 * Runtime variables expected:
 *   $product (paket bilgisi: name, title, category, price)
 *   $step (aktif adim)
 *   $steps (tum adimlar listesi)
 *   $cdg_os_kind (hosting/server/sms/software/special/domain)
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

// Paket bilgilerini cikart (her urun tipi farkli yapida olabilir)
$cdg_os_pkg_name     = '';
$cdg_os_pkg_category = '';
$cdg_os_pkg_subtitle = '';
$cdg_os_pkg_kind_label = '';

if(isset($product) && is_array($product)) {
    $cdg_os_pkg_name = $product['title']
        ?? $product['name']
        ?? $product['product_name']
        ?? '';
    $cdg_os_pkg_subtitle = $product['sub_title']
        ?? $product['subtitle']
        ?? '';
    if(isset($product['category']) && is_array($product['category'])) {
        $cdg_os_pkg_category = $product['category']['title']
            ?? $product['category']['name']
            ?? '';
    } elseif(isset($product['category_name'])) {
        $cdg_os_pkg_category = $product['category_name'];
    }
}

// Urun tipi etiketi
$cdg_kind_labels = [
    'hosting'  => ['Hosting', 'bi-hdd-network-fill', '#10b981'],
    'server'   => ['Sunucu', 'bi-server', '#3b82f6'],
    'software' => ['Yazılım', 'bi-code-square', '#8b5cf6'],
    'sms'      => ['SMS', 'bi-chat-dots-fill', '#f59e0b'],
    'special'  => ['Özel Ürün', 'bi-stars', '#ec4899'],
    'domain'   => ['Alan Adı', 'bi-globe2', '#06b6d4'],
];
$cdg_os_kind = $cdg_os_kind ?? 'hosting';
$cdg_os_kind_label = $cdg_kind_labels[$cdg_os_kind] ?? $cdg_kind_labels['hosting'];
?>

<style>
/* ============================================================
   CODEGA ORDER-STEPS — Kurumsal Override (Hosting/Server/SMS/Software/Special/Domain)
   Yapi: .ilanasamalar (adim bari) > .pakettitle (adim basligi) > .siparisbilgileri (icerik)
   ============================================================ */

/* SARI hero bombesini KALDIR */
.cdg-page-hero { display: none !important; }

/* Wrapper - sayfa BG */
#wrapper {
    background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    padding: 24px 0 70px;
    min-height: 70vh;
    margin-top: 0 !important;
}

/* === Codega Order-Steps Header (paket ozet karti) === */
.cdg-os-header {
    background: linear-gradient(135deg, #2E3B4E 0%, #1e293b 100%);
    border-radius: 16px;
    padding: 22px 28px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 8px 24px rgba(15,23,42,0.12);
}
.cdg-os-header::before {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(0,211,229,0.18) 0%, transparent 70%);
    pointer-events: none;
}
.cdg-os-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
}
.cdg-os-icon-box {
    width: 56px; height: 56px;
    background: rgba(0,211,229,0.18);
    border: 1px solid rgba(0,211,229,0.35);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cdg-os-icon-box i {
    font-size: 26px;
    color: #00D3E5;
}
.cdg-os-info { flex: 1 1 280px; min-width: 0; }
.cdg-os-kind-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: rgba(0,211,229,0.18);
    border: 1px solid rgba(0,211,229,0.35);
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    color: #67e8f9;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 8px;
}
.cdg-os-pkg-name {
    margin: 0 0 4px;
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
    letter-spacing: -0.3px;
}
.cdg-os-pkg-meta {
    margin: 0;
    font-size: 13px;
    color: #cbd5e1;
    line-height: 1.5;
}
.cdg-os-pkg-meta strong { color: #00D3E5; font-weight: 600; }
.cdg-os-trust {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: center;
    color: #cbd5e1;
    font-size: 11.5px;
}
.cdg-os-trust span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.cdg-os-trust i {
    font-size: 13px;
    color: #10b981;
}

/* === Adim cubugu (.ilanasamalar) === */
#wrapper .ilanasamalar {
    display: flex !important;
    gap: 0 !important;
    margin: 0 0 22px !important;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 24px !important;
    flex-wrap: nowrap !important;
    justify-content: stretch !important;
    align-items: center;
    position: relative;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
#wrapper .ilanasamalar .ilanasamax {
    flex: 1 1 0 !important;
    max-width: none !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    padding: 0 8px !important;
    text-align: center !important;
    transition: none !important;
    position: relative;
    box-shadow: none !important;
}
/* Adim numara dairesi */
#wrapper .ilanasamalar .ilanasamax h3 {
    background: #f1f5f9 !important;
    color: #94a3b8 !important;
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    margin: 0 auto 6px !important;
    border: 2px solid #e2e8f0;
}
/* Adim baslik metni */
#wrapper .ilanasamalar .ilanasamax > div {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    line-height: 1.3;
    text-align: center;
}
/* Aktif adim */
#wrapper .ilanasamalar #asamaaktif {
    box-shadow: none !important;
    border: 0 !important;
}
#wrapper .ilanasamalar #asamaaktif h3 {
    background: linear-gradient(135deg, #2E3B4E, #1e293b) !important;
    color: #fff !important;
    border-color: #2E3B4E !important;
    box-shadow: 0 4px 10px rgba(46,59,78,0.25);
}
#wrapper .ilanasamalar #asamaaktif > div {
    color: #0f172a !important;
    font-weight: 700 !important;
}
/* Adim arasi cizgi */
#wrapper .ilanasamalar .ilanasamax:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 18px;
    left: calc(50% + 22px);
    right: calc(-50% + 22px);
    height: 2px;
    background: #e2e8f0;
}
/* Tamamlanmis adim cizgisi (aktiften once gelenler) — basit yaklasim */
#wrapper .ilanasamalar .ilanasamax:has(~ #asamaaktif)::after,
#wrapper .ilanasamalar #asamaaktif ~ .ilanasamax::after {
    background: #e2e8f0;
}
#wrapper .asamaline { display: none !important; }

@media (max-width: 600px) {
    #wrapper .ilanasamalar { padding: 14px 12px !important; }
    #wrapper .ilanasamalar .ilanasamax > div { font-size: 11px !important; }
    #wrapper .ilanasamalar .ilanasamax h3 { width: 30px !important; height: 30px !important; font-size: 12px !important; }
}

/* === Adim basligi (.pakettitle) === */
#wrapper .pakettitle {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px 28px !important;
    margin-bottom: 0 !important;
    margin-top: 0 !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    border-bottom: 0 !important;
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
    margin: 0 !important;
    line-height: 1.6;
}
#wrapper .pakettitle .line { display: none !important; }

/* === Siparis bilgileri konteyneri === */
#wrapper .siparisbilgileri {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-top: 0 !important;
    border-radius: 0 0 14px 14px !important;
    padding: 28px !important;
    margin-bottom: 22px !important;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}

/* === Periyod blok kartlari (.orderperiodblock) === */
#wrapper .orderperiodblock-con {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
    gap: 14px !important;
    margin-bottom: 24px;
}
#wrapper .orderperiodblock {
    background: #fff !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 14px !important;
    padding: 22px 18px !important;
    text-align: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    position: relative;
    overflow: hidden;
}
#wrapper .orderperiodblock::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(46,59,78,0.04) 100%);
    opacity: 0;
    transition: opacity 0.2s;
    pointer-events: none;
}
#wrapper .orderperiodblock:hover {
    border-color: #2E3B4E !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(15,23,42,0.08);
}
#wrapper .orderperiodblock:hover::before { opacity: 1; }

#wrapper .orderperiodblock.active {
    border-color: #2E3B4E !important;
    background: linear-gradient(135deg, #fff 0%, #eff6ff 100%) !important;
    box-shadow: 0 8px 24px rgba(46,59,78,0.18) !important;
    border-width: 2px !important;
}

#wrapper .orderperiodblock h3 {
    color: #2E3B4E !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    margin: 0 0 10px !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    position: relative;
    z-index: 1;
}
#wrapper .orderperiodblock.active h3 { color: #2E3B4E !important; }

#wrapper .orderperiodblock h2 {
    color: #0f172a !important;
    font-size: 28px !important;
    font-weight: 800 !important;
    margin: 0 !important;
    line-height: 1.1;
    letter-spacing: -0.5px;
    position: relative;
    z-index: 1;
}

/* Periyod secim dairesi */
#wrapper .orderperiodblock .periodselectbox {
    width: 26px !important;
    height: 26px !important;
    border: 2px solid #cbd5e1 !important;
    border-radius: 50% !important;
    margin: 14px auto 0 !important;
    display: grid !important;
    place-items: center !important;
    transition: all 0.2s !important;
    position: relative;
    z-index: 1;
}
#wrapper .orderperiodblock.active .periodselectbox {
    background: #2E3B4E !important;
    border-color: #2E3B4E !important;
}
#wrapper .orderperiodblock.active .periodselectbox i {
    color: #fff !important;
    font-size: 13px;
}
#wrapper .orderperiodblock .periodselectbox i { color: transparent; font-size: 13px; }

/* Tasarruf rozet (ribbonperiod) */
#wrapper .ribbonperiod {
    position: absolute !important;
    top: -1px !important;
    right: -1px !important;
    z-index: 2;
}
#wrapper .ribbonperiod span {
    display: inline-block !important;
    background: linear-gradient(135deg, #10b981, #059669) !important;
    color: #fff !important;
    padding: 5px 12px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    border-radius: 0 14px 0 14px !important;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
#wrapper .setup-fee-period {
    display: block !important;
    color: #64748b !important;
    font-size: 11.5px !important;
    margin-top: 8px !important;
    font-weight: 600 !important;
    position: relative;
    z-index: 1;
}

/* === Devam butonu (gonderbtn) === */
#wrapper .siparisbilgileri form { width: 100%; }
#wrapper .gonderbtn,
#wrapper .btn.mio-ajax-submit {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    padding: 14px 32px !important;
    border-radius: 12px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.2s !important;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #fff !important;
    border: 0 !important;
    cursor: pointer !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 16px rgba(16,185,129,0.28);
    min-width: 180px;
}
#wrapper .gonderbtn:hover,
#wrapper .btn.mio-ajax-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(16,185,129,0.36);
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
}

/* Devam butonunu konteyner sagina hizala (genel kullanim) */
#wrapper .siparisbilgileri > div[style*="text-align"][style*="right"],
#wrapper .siparisbilgileri > div[align="right"] {
    text-align: right !important;
    margin-top: 14px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
}

/* === Form elemanlari === */
#wrapper .alanadisorgu {
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%) !important;
    padding: 24px !important;
    border-radius: 14px !important;
    border: 1px solid #e2e8f0 !important;
}
#wrapper .alanadisorgu input[type="text"] {
    width: 100% !important;
    padding: 14px 16px !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 10px !important;
    font-size: 14px !important;
    font-family: inherit !important;
    color: #0f172a;
    background: #fff;
}
#wrapper .alanadisorgu input[type="text"]:focus {
    border-color: #2E3B4E !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}
#wrapper .alanadisorgu h5 {
    font-size: 13px !important;
    color: #64748b !important;
    margin-top: 14px !important;
    font-weight: 500 !important;
    line-height: 1.7;
}
#wrapper .alanadisorgu .gonderbtn { margin-left: 10px; }

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
    background: linear-gradient(135deg, #2E3B4E, #1e293b) !important;
    color: #fff !important;
    border-color: #2E3B4E !important;
}
#wrapper #accordion .ui-accordion-content {
    border: 1px solid #e2e8f0;
    border-top: 0;
    border-radius: 0 0 10px 10px;
    padding: 18px;
    background: #fafbfc;
}

/* Checkbox/Radio */
#wrapper .checkbox-custom-label,
#wrapper .radio-custom-label {
    padding: 8px 0 !important;
    cursor: pointer !important;
    font-size: 13.5px !important;
    color: #0f172a !important;
}
#wrapper .checkbox-custom + label::before,
#wrapper .radio-custom + label::before {
    border: 2px solid #cbd5e1 !important;
}
#wrapper .checkbox-custom:checked + label::before,
#wrapper .radio-custom:checked + label::before {
    background: #2E3B4E !important;
    border-color: #2E3B4E !important;
}

/* === Domain check sonuc === */
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
#wrapper .tescilsonuc .error { color: #991b1b !important; }
#wrapper .tescilsonuc .success { color: #059669 !important; }

/* Hata kutusu */
#wrapper .error#result {
    padding: 14px 16px !important;
    background: #fef2f2 !important;
    border: 1px solid #fecaca !important;
    border-radius: 10px !important;
    color: #991b1b !important;
    font-weight: 600 !important;
    font-size: 13px;
}

/* Zorunlu yildiz */
#wrapper .zorunlu { color: #ef4444 !important; font-weight: 700 !important; }

/* Genel form input/select */
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
    font-size: 13px;
    color: #0f172a;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
}
#wrapper input:focus,
#wrapper select:focus,
#wrapper textarea:focus {
    border-color: #2E3B4E;
    outline: none;
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}

#wrapper .clear { clear: both; }

/* Eski cdg-domain-badge'i Codega icin gizle (sari hero) */
.cdg-domain-badge { display: none !important; }
</style>

<!-- ==================== CODEGA ORDER-STEPS HEADER ==================== -->
<div class="cdg-container" style="margin-top: 24px;">
    <div class="cdg-os-header">
        <div class="cdg-os-header-content">
            <div class="cdg-os-icon-box">
                <i class="bi <?php echo htmlspecialchars($cdg_os_kind_label[1]); ?>"></i>
            </div>
            <div class="cdg-os-info">
                <span class="cdg-os-kind-badge">
                    <i class="bi bi-cart-check-fill" style="font-size:11px;"></i>
                    <?php echo htmlspecialchars($cdg_os_kind_label[0]); ?> Sipariş Süreci
                </span>
                <h1 class="cdg-os-pkg-name">
                    <?php
                    if($cdg_os_pkg_name) {
                        echo htmlspecialchars($cdg_os_pkg_name);
                    } else {
                        echo htmlspecialchars($cdg_os_kind_label[0]) . ' Siparişi';
                    }
                    ?>
                </h1>
                <p class="cdg-os-pkg-meta">
                    <?php if($cdg_os_pkg_category): ?>
                        Kategori: <strong><?php echo htmlspecialchars($cdg_os_pkg_category); ?></strong>
                    <?php endif; ?>
                    <?php if($cdg_os_pkg_subtitle): ?>
                        <?php echo $cdg_os_pkg_category ? ' · ' : ''; ?><?php echo htmlspecialchars($cdg_os_pkg_subtitle); ?>
                    <?php endif; ?>
                    <?php if(!$cdg_os_pkg_category && !$cdg_os_pkg_subtitle): ?>
                        Siparişinizi tamamlamak için aşağıdaki adımları takip edin.
                    <?php endif; ?>
                </p>
            </div>
            <div class="cdg-os-trust">
                <span><i class="bi bi-shield-lock-fill"></i> SSL Korumalı</span>
                <span><i class="bi bi-credit-card-fill"></i> Güvenli Ödeme</span>
                <span><i class="bi bi-headset"></i> 7/24 Destek</span>
            </div>
        </div>
    </div>
</div>
