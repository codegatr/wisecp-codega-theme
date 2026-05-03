<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

/**
 * CODEGA ERP - codega.com.tr/pages/erp.php migration
 *
 * 9 modül, sektörel çözümler, avantajlar, fiyat planları, demo CTA
 */

$pageTitle = 'CODEGA ERP - Modüler Kurumsal Kaynak Planlaması | CODEGA';
$pageDescription = 'CODEGA ERP ile finanstan üretime, satıştan İK\'ya tek panelden işletme yönetimi. 9 entegre modül, e-fatura, mobil uygulama, %50 daha uygun fiyat.';

// 9 ERP modülü
$erp_modules = [
    [
        'icon' => 'bi-cash-coin',
        'color' => '#0057e7',
        'bg' => '#e8f0fe',
        'baslik' => 'Finans & Muhasebe',
        'aciklama' => 'Genel muhasebe, bütçeleme, maliyet muhasebesi ve finansal raporlama.',
        'ozellikler' => ['Genel muhasebe & yevmiye', 'Bütçe ve nakit akışı', 'Maliyet muhasebesi', 'e-Fatura / e-Arşiv', 'Bilanço & gelir tablosu'],
    ],
    [
        'icon' => 'bi-box-seam-fill',
        'color' => '#059669',
        'bg' => '#ecfdf5',
        'baslik' => 'Stok & Depo Yönetimi',
        'aciklama' => 'Ürün giriş-çıkış, minimum stok uyarıları ve çok depo desteği.',
        'ozellikler' => ['Ürün kartı & kategori yönetimi', 'Stok giriş/çıkış hareketleri', 'Minimum stok uyarı sistemi', 'Çok depo & lokasyon desteği', 'Barkod & QR entegrasyonu'],
    ],
    [
        'icon' => 'bi-people-fill',
        'color' => '#d97706',
        'bg' => '#fff7ed',
        'baslik' => 'Satış & CRM',
        'aciklama' => 'Müşteri yönetimi, sipariş takibi, teklif hazırlama ve satış analitiği.',
        'ozellikler' => ['Müşteri kartı & segmentasyon', 'Teklif & sipariş yönetimi', 'Fatura oluşturma & takip', 'Satış analitiği & raporlar', 'Cari hesap yönetimi'],
    ],
    [
        'icon' => 'bi-gear-wide-connected',
        'color' => '#7c3aed',
        'bg' => '#faf5ff',
        'baslik' => 'Üretim & MRP',
        'aciklama' => 'Üretim emirleri, BOM (malzeme listesi) ve kapasite planlaması.',
        'ozellikler' => ['Ürün ağacı (BOM) tanımları', 'Üretim emri oluşturma', 'Malzeme ihtiyaç planlama (MRP)', 'İş istasyonu & kapasite takibi', 'Fire ve kalite kontrol'],
    ],
    [
        'icon' => 'bi-cart-fill',
        'color' => '#0891b2',
        'bg' => '#eff6ff',
        'baslik' => 'Satın Alma',
        'aciklama' => 'Tedarikçi yönetimi, sipariş onay süreçleri ve maliyet analizi.',
        'ozellikler' => ['Tedarikçi kart yönetimi', 'Satın alma talebi & onay akışı', 'Sipariş takibi', 'Fiyat karşılaştırma analizi', 'Tedarikçi performans raporu'],
    ],
    [
        'icon' => 'bi-person-workspace',
        'color' => '#b45309',
        'bg' => '#fef3c7',
        'baslik' => 'İnsan Kaynakları',
        'aciklama' => 'Personel bilgileri, maaş, izin ve eğitim takibi.',
        'ozellikler' => ['Personel özlük dosyası', 'Puantaj & bordro hesaplama', 'İzin & devamsızlık takibi', 'SGK bildirge entegrasyonu', 'Eğitim & sertifika takibi'],
    ],
    [
        'icon' => 'bi-graph-up-arrow',
        'color' => '#0891b2',
        'bg' => '#ecfeff',
        'baslik' => 'Raporlama & İş Zekası',
        'aciklama' => 'Gerçek zamanlı dashboardlar, özel raporlar ve veri analitiği.',
        'ozellikler' => ['Yönetici dashboard', 'KPI takip paneli', 'Özelleştirilebilir raporlar', 'Excel / PDF export', 'Grafiksel analiz araçları'],
    ],
    [
        'icon' => 'bi-plug-fill',
        'color' => '#16a34a',
        'bg' => '#f0fdf4',
        'baslik' => 'API & Entegrasyonlar',
        'aciklama' => 'e-Ticaret, muhasebe ve ödeme sistemleriyle tam entegrasyon.',
        'ozellikler' => ['Trendyol / Hepsiburada', 'iyzico / PayTR ödeme', 'Logo / Mikro bağlantı', 'GİB e-Dönüşüm paketi', 'REST API & webhook'],
    ],
    [
        'icon' => 'bi-phone-fill',
        'color' => '#9333ea',
        'bg' => '#fdf4ff',
        'baslik' => 'Mobil Uygulama',
        'aciklama' => 'iOS ve Android üzerinden her yerden erişim ve bildirim sistemi.',
        'ozellikler' => ['Mobil sipariş yönetimi', 'Stok sorgulama & hareketler', 'Anlık bildirimler & uyarılar', 'Offline çalışma desteği', 'Barkod okuyucu'],
    ],
];

// 6 sektörel çözüm
$erp_sectors = [
    ['icon' => 'bi-shop', 'baslik' => 'Üretim & İmalat', 'aciklama' => 'BOM, MRP, üretim emri, fire takibi, kalite kontrol modülleri.'],
    ['icon' => 'bi-cart-check', 'baslik' => 'Toptan & Perakende', 'aciklama' => 'Çoklu lokasyon, fiyat listesi, kampanya yönetimi, POS entegrasyonu.'],
    ['icon' => 'bi-bag-fill', 'baslik' => 'E-Ticaret', 'aciklama' => 'Pazaryeri entegrasyonu, otomatik fiyat eşitleme, kargo takibi.'],
    ['icon' => 'bi-truck', 'baslik' => 'Lojistik & Depo', 'aciklama' => 'WMS, sevkiyat planlama, rota optimizasyonu, GPS takibi.'],
    ['icon' => 'bi-buildings-fill', 'baslik' => 'İnşaat & Taahhüt', 'aciklama' => 'Hakediş, taşeron yönetimi, proje maliyetleri, kademeli ödeme.'],
    ['icon' => 'bi-wrench-adjustable-circle-fill', 'baslik' => 'Servis & Bakım', 'aciklama' => 'İş emri, periyodik bakım takvimi, garanti, parça stok yönetimi.'],
];

// Avantajlar
$erp_advantages = [
    ['icon' => 'bi-cash-stack', 'baslik' => '%50 Daha Uygun', 'aciklama' => 'Logo, Mikro, Netsis gibi rakip ERP\'lerin yarı fiyatına aynı özellikler.'],
    ['icon' => 'bi-cloud-check-fill', 'baslik' => 'Cloud Tabanlı', 'aciklama' => 'Sunucu yatırımı yok. Tarayıcıdan girin, her yerden çalışın.'],
    ['icon' => 'bi-puzzle-fill', 'baslik' => 'Modüler Yapı', 'aciklama' => 'Sadece ihtiyacınız olan modülleri seçin, gerektikçe ekleyin.'],
    ['icon' => 'bi-arrow-repeat', 'baslik' => 'Sürekli Güncelleme', 'aciklama' => 'Mevzuat değişiklikleri, yeni özellikler otomatik gelir.'],
    ['icon' => 'bi-shield-fill-check', 'baslik' => 'KVKK Uyumlu', 'aciklama' => 'Türkiye\'de hosted, SOC2 standartlarında veri güvenliği.'],
    ['icon' => 'bi-headset', 'baslik' => '7/24 Destek', 'aciklama' => 'Kuruluş, eğitim, kullanım — her aşamada Türkçe destek.'],
];
?>
<!DOCTYPE html>
<html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php
        $hoptions = [ 'page' => "erp" ];
        $meta = [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'keywords' => 'erp, kurumsal kaynak planlaması, codega erp, üretim yönetimi, stok yönetimi, e-fatura',
            'robots' => 'index,follow',
        ];
        include __DIR__.DS."inc".DS."main-head.php";
    ?>
    <style>
    /* CODEGA ERP sayfası - codega.com.tr/pages/erp.php migration */
    .cdg-erp-hero {
        position: relative;
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-erp-hero::before {
        content: '';
        position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-erp-hero::after {
        content: '';
        position: absolute;
        bottom: -120px; left: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,211,229,0.18) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-erp-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
    }
    .cdg-erp-hero-content { position: relative; z-index: 1; text-align: center; max-width: 820px; margin: 0 auto; }
    .cdg-erp-eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 16px;
        background: rgba(0,229,255,0.10);
        border: 1px solid rgba(0,229,255,0.30);
        border-radius: 100px;
        color: #00E5FF;
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.05em; text-transform: uppercase;
        margin-bottom: 22px;
    }
    .cdg-erp-eyebrow .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #00E5FF;
        box-shadow: 0 0 8px #00E5FF;
        animation: cdgErpPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgErpPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .cdg-erp-hero h1 {
        font-size: clamp(32px, 5vw, 56px);
        font-weight: 800;
        margin: 0 0 18px;
        letter-spacing: -0.02em;
        line-height: 1.1;
        color: #fff;
    }
    .cdg-erp-hero h1 span {
        background: linear-gradient(135deg, #00D3E5 0%, #00E5FF 50%, #67E8F9 100%);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .cdg-erp-hero p {
        font-size: 18px;
        color: rgba(255,255,255,0.78);
        line-height: 1.65;
        margin: 0 0 32px;
        max-width: 640px;
        margin-left: auto; margin-right: auto;
    }
    .cdg-erp-hero-actions {
        display: flex; justify-content: center; gap: 12px;
        flex-wrap: wrap;
    }
    .cdg-erp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-erp-btn-primary {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        color: #0f172a !important;
        box-shadow: 0 12px 28px rgba(0,229,255,0.35);
    }
    .cdg-erp-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(0,229,255,0.50);
        color: #0f172a !important;
    }
    .cdg-erp-btn-outline {
        background: rgba(255,255,255,0.10);
        color: #fff !important;
        border: 2px solid rgba(255,255,255,0.20);
        backdrop-filter: blur(8px);
    }
    .cdg-erp-btn-outline:hover {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.40);
    }

    /* Section başlık */
    .cdg-erp-section {
        padding: 80px 0;
        background: #fff;
    }
    .cdg-erp-section.alt { background: linear-gradient(180deg, #f8fafc 0%, #fff 100%); }
    .cdg-erp-section-head {
        text-align: center;
        max-width: 720px;
        margin: 0 auto 56px;
    }
    .cdg-erp-section-eyebrow {
        display: inline-block;
        padding: 6px 14px;
        background: rgba(0,211,229,0.10);
        border: 1px solid rgba(0,211,229,0.25);
        border-radius: 100px;
        color: #00D3E5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .cdg-erp-section-head h2 {
        font-size: clamp(28px, 3.5vw, 38px);
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 14px;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .cdg-erp-section-head h2 span {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cdg-erp-section-head p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.65;
        margin: 0;
    }

    /* Modül grid */
    .cdg-erp-modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }
    .cdg-erp-module {
        position: relative;
        padding: 28px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .cdg-erp-module:hover {
        transform: translateY(-4px);
        border-color: #00D3E5;
        box-shadow: 0 20px 40px rgba(46,59,78,0.08);
    }
    .cdg-erp-module-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 26px;
        margin-bottom: 18px;
    }
    .cdg-erp-module h3 {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
        letter-spacing: -0.01em;
    }
    .cdg-erp-module-desc {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 18px;
    }
    .cdg-erp-module-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .cdg-erp-module-features li {
        position: relative;
        padding: 6px 0 6px 22px;
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
    }
    .cdg-erp-module-features li::before {
        content: '';
        position: absolute;
        left: 0; top: 11px;
        width: 14px; height: 14px;
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E") center/contain no-repeat;
        -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E") center/contain no-repeat;
    }

    /* Sektör grid */
    .cdg-erp-sectors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }
    .cdg-erp-sector {
        padding: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-erp-sector:hover {
        border-color: #00D3E5;
        box-shadow: 0 12px 28px rgba(0,211,229,0.10);
        transform: translateY(-2px);
    }
    .cdg-erp-sector-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(0,211,229,0.10), rgba(0,229,255,0.05));
        color: #00D3E5;
        display: grid;
        place-items: center;
        font-size: 24px;
        margin-bottom: 14px;
    }
    .cdg-erp-sector h3 {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cdg-erp-sector p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.55;
        margin: 0;
    }

    /* Avantajlar */
    .cdg-erp-advantages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }
    .cdg-erp-advantage {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 22px 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-erp-advantage:hover {
        border-color: #00D3E5;
        background: #f0fdff;
    }
    .cdg-erp-advantage-icon {
        width: 44px; height: 44px;
        flex-shrink: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 20px;
        box-shadow: 0 8px 18px rgba(46,59,78,0.20);
    }
    .cdg-erp-advantage-content { flex: 1; min-width: 0; }
    .cdg-erp-advantage h3 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .cdg-erp-advantage p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.55;
        margin: 0;
    }

    /* Karşılaştırma */
    .cdg-erp-compare {
        max-width: 760px;
        margin: 0 auto;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 32px rgba(15,23,42,0.05);
    }
    .cdg-erp-compare table {
        width: 100%;
        border-collapse: collapse;
    }
    .cdg-erp-compare th {
        padding: 18px 16px;
        background: linear-gradient(135deg, #f8fafc, #fff);
        text-align: center;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        border-bottom: 2px solid #e2e8f0;
    }
    .cdg-erp-compare th.highlight {
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        position: relative;
    }
    .cdg-erp-compare th.highlight::after {
        content: 'EN İYİ';
        position: absolute;
        top: 6px; right: 6px;
        padding: 2px 8px;
        background: #fbbf24;
        color: #0f172a;
        font-size: 9px;
        font-weight: 800;
        border-radius: 100px;
        letter-spacing: 0.05em;
    }
    .cdg-erp-compare td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }
    .cdg-erp-compare tr:last-child td { border-bottom: 0; }
    .cdg-erp-compare td:first-child {
        text-align: left;
        font-weight: 600;
        color: #0f172a;
    }
    .cdg-erp-compare .ok i { color: #10b981; font-size: 18px; }
    .cdg-erp-compare .no i { color: #ef4444; font-size: 18px; opacity: 0.5; }
    .cdg-erp-compare .partial i { color: #f59e0b; font-size: 18px; }

    /* CTA */
    .cdg-erp-cta {
        padding: 80px 0;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        position: relative;
        overflow: hidden;
        text-align: center;
        color: #fff;
    }
    .cdg-erp-cta::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-erp-cta-content {
        position: relative;
        z-index: 1;
        max-width: 720px;
        margin: 0 auto;
    }
    .cdg-erp-cta h2 {
        font-size: clamp(28px, 3.5vw, 40px);
        font-weight: 800;
        margin: 0 0 14px;
        letter-spacing: -0.02em;
    }
    .cdg-erp-cta h2 span {
        background: linear-gradient(135deg, #00D3E5, #00E5FF, #67E8F9);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cdg-erp-cta p {
        font-size: 17px;
        color: rgba(255,255,255,0.80);
        line-height: 1.65;
        margin: 0 0 32px;
    }
    .cdg-erp-cta-actions {
        display: flex; justify-content: center; gap: 12px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .cdg-erp-hero { padding: 50px 0 40px; }
        .cdg-erp-section { padding: 56px 0; }
        .cdg-erp-modules-grid { grid-template-columns: 1fr; }
        .cdg-erp-module { padding: 22px; }
        .cdg-erp-compare { font-size: 12px; }
        .cdg-erp-compare th, .cdg-erp-compare td { padding: 10px 8px; }
        .cdg-erp-compare th.highlight::after { display: none; }
    }
    </style>
</head>
<body>

<?php include __DIR__.DS."inc".DS."lang-currency-modal.php"; ?>
<?php
    $header_type = isset($theme_settings['header_type']) ? $theme_settings['header_type'] : 1;
    $hf = __DIR__.DS."inc".DS."main-header-".$header_type.".php";
    if(file_exists($hf)) include $hf;

    $contact_url = class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact';
?>

<!-- HERO -->
<section class="cdg-erp-hero">
    <div class="cdg-erp-hero-grid"></div>
    <div class="cdg-container">
        <div class="cdg-erp-hero-content">
            <div class="cdg-erp-eyebrow">
                <span class="dot"></span>
                <span>KURUMSAL KAYNAK PLANLAMASI</span>
            </div>
            <h1>İşinizi tek panelden <span>akıllı yönetim</span> ile büyütün</h1>
            <p>Finanstan üretime, satıştan İK'ya 9 entegre modül. Modüler yapı, %50 daha uygun fiyat, anlık destek. Logo veya Mikro yerine modern bulut çözümü.</p>
            <div class="cdg-erp-hero-actions">
                <a href="<?php echo $contact_url; ?>?subject=erp-demo" class="cdg-erp-btn cdg-erp-btn-primary">
                    <i class="bi bi-rocket-takeoff-fill"></i> 14 Gün Ücretsiz Dene
                </a>
                <a href="#moduller" class="cdg-erp-btn cdg-erp-btn-outline">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Modülleri İncele
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 9 MODÜL -->
<section class="cdg-erp-section" id="moduller">
    <div class="cdg-container">
        <div class="cdg-erp-section-head">
            <span class="cdg-erp-section-eyebrow">📦 9 Entegre Modül</span>
            <h2>İşletmenize uygun <span>modülleri seçin</span></h2>
            <p>Hepsi birbirleriyle entegre çalışır. Sadece ihtiyacınız olan modüller için ödeyin, gerektiğinde yenilerini ekleyin.</p>
        </div>
        <div class="cdg-erp-modules-grid">
            <?php foreach ($erp_modules as $m): ?>
            <div class="cdg-erp-module">
                <div class="cdg-erp-module-icon" style="background: <?php echo $m['bg']; ?>; color: <?php echo $m['color']; ?>;">
                    <i class="<?php echo $m['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($m['baslik']); ?></h3>
                <p class="cdg-erp-module-desc"><?php echo htmlspecialchars($m['aciklama']); ?></p>
                <ul class="cdg-erp-module-features">
                    <?php foreach ($m['ozellikler'] as $oz): ?>
                    <li><?php echo htmlspecialchars($oz); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- AVANTAJLAR -->
<section class="cdg-erp-section alt">
    <div class="cdg-container">
        <div class="cdg-erp-section-head">
            <span class="cdg-erp-section-eyebrow">⭐ Neden CODEGA ERP</span>
            <h2>İşletmenize <span>kattığı değer</span></h2>
            <p>Geleneksel ERP'lerin maliyet ve karmaşıklığını ortadan kaldıran modern çözüm.</p>
        </div>
        <div class="cdg-erp-advantages-grid">
            <?php foreach ($erp_advantages as $a): ?>
            <div class="cdg-erp-advantage">
                <div class="cdg-erp-advantage-icon">
                    <i class="<?php echo $a['icon']; ?>"></i>
                </div>
                <div class="cdg-erp-advantage-content">
                    <h3><?php echo htmlspecialchars($a['baslik']); ?></h3>
                    <p><?php echo htmlspecialchars($a['aciklama']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SEKTÖRLER -->
<section class="cdg-erp-section">
    <div class="cdg-container">
        <div class="cdg-erp-section-head">
            <span class="cdg-erp-section-eyebrow">🏭 Sektörel Çözümler</span>
            <h2>Her sektöre <span>özel ERP</span></h2>
            <p>Hazır şablonlardan değil, sektörünüzün dinamiklerine göre özelleştirilen çözümlerden bahsediyoruz.</p>
        </div>
        <div class="cdg-erp-sectors-grid">
            <?php foreach ($erp_sectors as $s): ?>
            <div class="cdg-erp-sector">
                <div class="cdg-erp-sector-icon">
                    <i class="<?php echo $s['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($s['baslik']); ?></h3>
                <p><?php echo htmlspecialchars($s['aciklama']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- KARŞILAŞTIRMA -->
<section class="cdg-erp-section alt">
    <div class="cdg-container">
        <div class="cdg-erp-section-head">
            <span class="cdg-erp-section-eyebrow">📊 Karşılaştırma</span>
            <h2><span>Diğer ERP'ler</span> ile farkımız</h2>
            <p>Aynı özelliklerle %50 daha uygun fiyat. Bulut tabanlı, güncellemeli, modüler.</p>
        </div>
        <div class="cdg-erp-compare">
            <table>
                <thead>
                    <tr>
                        <th>Özellik</th>
                        <th>Geleneksel ERP</th>
                        <th class="highlight">CODEGA ERP</th>
                        <th>Diğer Bulut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sunucu yatırımı</td>
                        <td class="no"><i class="bi bi-x-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>Modüler ödeme</td>
                        <td class="no"><i class="bi bi-x-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="partial"><i class="bi bi-dash-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>Türkçe destek 7/24</td>
                        <td class="partial"><i class="bi bi-dash-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="no"><i class="bi bi-x-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>e-Fatura / e-Arşiv</td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>KVKK uyumlu (TR hosted)</td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="no"><i class="bi bi-x-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>Mobil uygulama</td>
                        <td class="no"><i class="bi bi-x-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>Açık API & webhook</td>
                        <td class="no"><i class="bi bi-x-circle-fill"></i></td>
                        <td class="ok"><i class="bi bi-check-circle-fill"></i></td>
                        <td class="partial"><i class="bi bi-dash-circle-fill"></i></td>
                    </tr>
                    <tr>
                        <td>Aylık başlangıç maliyeti</td>
                        <td>~₺3.500</td>
                        <td><strong style="color:#00D3E5;">₺1.500</strong></td>
                        <td>~₺2.800</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cdg-erp-cta">
    <div class="cdg-container">
        <div class="cdg-erp-cta-content">
            <h2>İşletmeniz için <span>dijital dönüşüm</span> başlasın</h2>
            <p>14 gün ücretsiz deneme + kurulum + ilk ay eğitim dahil. Sözleşme yok, dilediğinizde iptal edin.</p>
            <div class="cdg-erp-cta-actions">
                <a href="<?php echo $contact_url; ?>?subject=erp-demo" class="cdg-erp-btn cdg-erp-btn-primary">
                    <i class="bi bi-rocket-takeoff-fill"></i> 14 Gün Ücretsiz Dene
                </a>
                <a href="https://wa.me/905102204206" target="_blank" rel="noopener" class="cdg-erp-btn cdg-erp-btn-outline">
                    <i class="bi bi-whatsapp"></i> WhatsApp ile İletişim
                </a>
            </div>
        </div>
    </div>
</section>

<?php
    $footer_file = __DIR__.DS."inc".DS."main-footer.php";
    if(file_exists($footer_file)) include $footer_file;
?>

</body>
</html>
