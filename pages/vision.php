<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$pageTitle = 'Vizyon & Değerlerimiz | CODEGA';
$pageDescription = 'CODEGA\'nın temel ilkeleri, uzun vadeli hedefleri ve her kararı yönlendiren değerler. 2005\'ten bugüne 20 yıllık dijital altyapı yolculuğu.';

$values = [
    ['icon' => 'bi-shield-fill-check', 'baslik' => 'Güvenilirlik', 'icerik' => 'Söz verdiğimiz uptime\'ı, söz verdiğimiz destekle destekliyoruz. %99.9 SLA garantimiz pazarlama değil, taahhüttür.'],
    ['icon' => 'bi-eye-fill', 'baslik' => 'Şeffaflık', 'icerik' => 'Gizli ücret yok. Sürpriz fiyat artışı yok. Sistem durumunu her zaman kamuya açık uptime sayfamızdan takip edebilirsiniz.'],
    ['icon' => 'bi-gem', 'baslik' => 'Teknik Mükemmellik', 'icerik' => 'Kodumuzu, altyapımızı ve süreçlerimizi sürekli iyileştiriyoruz. Müşterilerimizin problemleri bizi daha iyi mühendis yapıyor.'],
    ['icon' => 'bi-people-fill', 'baslik' => 'Müşteri Odaklılık', 'icerik' => 'Her karar müşteri deneyimine etkisiyle değerlendirilir. Destek taleplerinde ortalama yanıt süremiz 28 dakikadır.'],
    ['icon' => 'bi-arrow-up-circle-fill', 'baslik' => 'Sürdürülebilir Büyüme', 'icerik' => 'Hızlı büyümeden önce sağlam büyümeyi tercih ederiz. Her yeni müşteri, kaliteden ödün vermeden onboard edilir.'],
];

$milestones = [
    ['yil' => '2005', 'baslik' => 'mIRC Altyapısı', 'aciklama' => 'Selçuk Üniversitesi Bilgisayar Merkezi\'nde kurulan resmi sohbet odası yönetimi ile yolculuğumuz başladı.', 'icon' => 'bi-rocket'],
    ['yil' => '2008', 'baslik' => 'Truvahost Kuruluşu', 'aciklama' => 'Birikim ve deneyim Truvahost çatısı altında kurumsal kimliğe kavuştu. Bölgesinde güvenilir hosting sağlayıcı.', 'icon' => 'bi-buildings'],
    ['yil' => '2021', 'baslik' => 'Sisyatek Dönüşümü', 'aciklama' => 'Sistem Yazılım Teknolojileri adıyla yeniden yapılandı. Yazılım geliştirme + sistem yönetimi entegrasyonu.', 'icon' => 'bi-arrow-clockwise'],
    ['yil' => '2026', 'baslik' => 'CODEGA Markası', 'aciklama' => 'Küresel pazarlara açılma vizyonuyla CODEGA. PHP yazılım + ERP sistemleri uluslararası standartlarda.', 'icon' => 'bi-globe2'],
];
?>

<?php
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php';
$contact_url = class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact';
?>

<section class="cdg-vision-hero">
    <div class="cdg-vision-hero-grid"></div>
    <div class="cdg-container">
        <div class="cdg-vision-hero-content">
            <h1>Vizyon &amp; <span>Değerlerimiz</span></h1>
            <p>Codega'nın temel ilkeleri, uzun vadeli hedefleri ve her kararı yönlendiren değerler. 2005'ten bugüne 20 yıllık dijital altyapı yolculuğumuz.</p>
        </div>
    </div>
</section>

<!-- VİZYON + MİSYON -->
<section class="cdg-vision-vm">
    <div class="cdg-container">
        <div class="cdg-vision-vm-grid">
            <div class="cdg-vision-vm-card">
                <div class="cdg-vision-vm-icon"><i class="bi bi-eye-fill"></i></div>
                <div class="cdg-vision-vm-label">Vizyonumuz</div>
                <h2>Türkiye'nin en güvenilir altyapı sağlayıcısı</h2>
                <p>Türkiye'nin en güvenilir yazılım ve hosting altyapısı sağlayıcısı olarak, her ölçekteki işletmenin dijital dönüşümüne öncülük etmek.</p>
            </div>
            <div class="cdg-vision-vm-card">
                <div class="cdg-vision-vm-icon"><i class="bi bi-bullseye"></i></div>
                <div class="cdg-vision-vm-label">Misyonumuz</div>
                <h2>Kurumsal kalite, erişilebilir fiyatla</h2>
                <p>PHP yazılım geliştirme, yönetilen hosting ve domain hizmetlerinde kurumsal kaliteyi erişilebilir fiyatlarla sunarak, müşterilerimizin teknoloji altyapısını güvenli, hızlı ve ölçeklenebilir bir şekilde yönetmelerini sağlamak.</p>
            </div>
        </div>
    </div>
</section>

<!-- 5 DEĞER -->
<section class="cdg-vision-values">
    <div class="cdg-container">
        <div class="cdg-vision-section-head">
            <span class="cdg-vision-section-eyebrow">💎 5 Temel Değerimiz</span>
            <h2>Her kararı yönlendiren ilkeler</h2>
            <p>Şirket kültürümüzün ve müşteri ilişkilerimizin temelini oluşturan değerler.</p>
        </div>
        <div class="cdg-vision-values-grid">
            <?php foreach ($values as $v): ?>
            <div class="cdg-vision-value">
                <div class="cdg-vision-value-icon"><i class="<?php echo $v['icon']; ?>"></i></div>
                <h3><?php echo htmlspecialchars($v['baslik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($v['icerik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TARİHÇE TIMELINE -->
<section class="cdg-vision-timeline">
    <div class="cdg-container">
        <div class="cdg-vision-section-head">
            <span class="cdg-vision-section-eyebrow">📜 Yolculuğumuz</span>
            <h2>20 Yıllık Birikim</h2>
            <p>2005'ten bugüne, sürekli evrilen ama temel değerlerinden vazgeçmeyen bir teknoloji firması.</p>
        </div>
        <div class="cdg-vision-timeline-list">
            <?php foreach ($milestones as $m): ?>
            <div class="cdg-vision-milestone">
                <div class="cdg-vision-milestone-icon"><i class="<?php echo $m['icon']; ?>"></i></div>
                <div class="cdg-vision-milestone-content">
                    <span class="cdg-vision-milestone-year"><?php echo $m['yil']; ?></span>
                    <h3><?php echo htmlspecialchars($m['baslik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($m['aciklama'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cdg-vision-cta">
    <div class="cdg-container">
        <div class="cdg-vision-cta-card">
            <h3>Bizi yakından <span style="color:#00E5FF;">tanımak ister misiniz?</span></h3>
            <p>Ekibimizle tanışın, çözümlerimizi inceleyin, sorularınızı sorun. Konya'dan tüm Türkiye'ye ve dünyaya hizmet veriyoruz.</p>
            <div class="cdg-vision-cta-actions">
                <a href="<?php echo $contact_url; ?>" class="cdg-vision-cta-btn cdg-vision-cta-btn-primary">
                    <i class="bi bi-envelope-paper-fill"></i> Bize Ulaşın
                </a>
            </div>
        </div>
    </div>
</section>
