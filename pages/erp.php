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

<?php
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php';
$contact_url = class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact';
?>

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
                <h3><?php echo htmlspecialchars($m['baslik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                <p class="cdg-erp-module-desc"><?php echo htmlspecialchars($m['aciklama'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                <ul class="cdg-erp-module-features">
                    <?php foreach ($m['ozellikler'] as $oz): ?>
                    <li><?php echo htmlspecialchars($oz, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></li>
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
                    <h3><?php echo htmlspecialchars($a['baslik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($a['aciklama'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
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
                <h3><?php echo htmlspecialchars($s['baslik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($s['aciklama'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
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
