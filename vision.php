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
<!DOCTYPE html>
<html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php
        $hoptions = [ 'page' => "vision" ];
        $meta = [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'keywords' => 'codega vizyon, misyon, şirket değerleri, codega tarihçe, kurumsal',
            'robots' => 'index,follow',
        ];
        include __DIR__.DS."inc".DS."main-head.php";
    ?>
    <style>
    .cdg-vision-hero {
        position: relative;
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-vision-hero::before {
        content: ''; position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-vision-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
    }
    .cdg-vision-hero-content { position: relative; z-index: 1; text-align: center; max-width: 720px; margin: 0 auto; }
    .cdg-vision-hero h1 {
        font-size: clamp(32px, 4.5vw, 48px);
        font-weight: 800; margin: 0 0 16px;
        letter-spacing: -0.02em; line-height: 1.15; color: #fff;
    }
    .cdg-vision-hero h1 span {
        background: linear-gradient(135deg, #00D3E5, #00E5FF, #67E8F9);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cdg-vision-hero p {
        font-size: 17px;
        color: rgba(255,255,255,0.78);
        line-height: 1.65; margin: 0;
    }

    /* Vizyon + Misyon kartlar */
    .cdg-vision-vm {
        padding: 80px 0;
        background: #fff;
    }
    .cdg-vision-vm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .cdg-vision-vm-card {
        position: relative;
        padding: 36px 32px;
        background: linear-gradient(180deg, #f8fafc, #fff);
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        transition: all 0.3s;
    }
    .cdg-vision-vm-card:hover {
        transform: translateY(-4px);
        border-color: #00D3E5;
        box-shadow: 0 24px 60px rgba(46,59,78,0.10);
    }
    .cdg-vision-vm-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2E3B4E, #00D3E5);
        border-radius: 20px 20px 0 0;
    }
    .cdg-vision-vm-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 26px;
        margin-bottom: 18px;
        box-shadow: 0 12px 24px rgba(46,59,78,0.20);
    }
    .cdg-vision-vm-label {
        font-size: 12px;
        font-weight: 800;
        color: #00D3E5;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 10px;
    }
    .cdg-vision-vm-card h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 14px;
        letter-spacing: -0.01em;
        line-height: 1.25;
    }
    .cdg-vision-vm-card p {
        font-size: 15px;
        color: #475569;
        line-height: 1.7;
        margin: 0;
    }
    @media (max-width: 768px) {
        .cdg-vision-vm-grid { grid-template-columns: 1fr; }
    }

    /* Değerler */
    .cdg-vision-values {
        padding: 80px 0;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .cdg-vision-section-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 48px;
    }
    .cdg-vision-section-eyebrow {
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
        margin-bottom: 14px;
    }
    .cdg-vision-section-head h2 {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -0.02em;
    }
    .cdg-vision-section-head p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    .cdg-vision-values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }
    .cdg-vision-value {
        padding: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-vision-value:hover {
        border-color: #00D3E5;
        background: #f0fdff;
        transform: translateY(-2px);
    }
    .cdg-vision-value-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(0,211,229,0.10), rgba(0,229,255,0.05));
        color: #00D3E5;
        display: grid;
        place-items: center;
        font-size: 22px;
        margin-bottom: 16px;
    }
    .cdg-vision-value h3 {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
    }
    .cdg-vision-value p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }

    /* Tarihçe timeline */
    .cdg-vision-timeline {
        padding: 80px 0;
        background: #fff;
    }
    .cdg-vision-timeline-list {
        max-width: 720px;
        margin: 0 auto;
        position: relative;
    }
    .cdg-vision-timeline-list::before {
        content: '';
        position: absolute;
        top: 30px; bottom: 30px;
        left: 30px;
        width: 2px;
        background: linear-gradient(180deg, #00D3E5, #2E3B4E);
        opacity: 0.2;
    }
    .cdg-vision-milestone {
        position: relative;
        display: flex;
        gap: 24px;
        padding: 16px 0 32px;
    }
    .cdg-vision-milestone:last-child { padding-bottom: 0; }
    .cdg-vision-milestone-icon {
        position: relative;
        z-index: 1;
        width: 60px; height: 60px;
        flex-shrink: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 24px;
        box-shadow: 0 12px 28px rgba(46,59,78,0.20);
    }
    .cdg-vision-milestone-content {
        flex: 1;
        padding: 18px 22px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-vision-milestone-content:hover {
        border-color: #00D3E5;
        background: #fff;
        box-shadow: 0 12px 28px rgba(0,211,229,0.10);
    }
    .cdg-vision-milestone-year {
        display: inline-block;
        padding: 3px 10px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        border-radius: 100px;
        margin-bottom: 8px;
    }
    .cdg-vision-milestone h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cdg-vision-milestone p {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    @media (max-width: 640px) {
        .cdg-vision-timeline-list::before { left: 24px; }
        .cdg-vision-milestone-icon { width: 48px; height: 48px; font-size: 20px; }
    }

    /* CTA */
    .cdg-vision-cta {
        padding: 60px 0 80px;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        text-align: center;
    }
    .cdg-vision-cta-card {
        max-width: 720px;
        margin: 0 auto;
        padding: 44px 32px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #fff;
    }
    .cdg-vision-cta-card::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
    }
    .cdg-vision-cta-card h3 {
        position: relative;
        font-size: 26px;
        font-weight: 800;
        margin: 0 0 10px;
        color: #fff;
    }
    .cdg-vision-cta-card p {
        position: relative;
        font-size: 15px;
        color: rgba(255,255,255,0.80);
        margin: 0 0 24px;
        line-height: 1.6;
    }
    .cdg-vision-cta-actions {
        position: relative;
        display: inline-flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .cdg-vision-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-vision-cta-btn-primary {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        color: #0f172a !important;
        box-shadow: 0 8px 22px rgba(0,229,255,0.30);
    }
    .cdg-vision-cta-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,229,255,0.45);
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
                <h3><?php echo htmlspecialchars($v['baslik']); ?></h3>
                <p><?php echo htmlspecialchars($v['icerik']); ?></p>
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
                    <h3><?php echo htmlspecialchars($m['baslik']); ?></h3>
                    <p><?php echo htmlspecialchars($m['aciklama']); ?></p>
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

<?php
    $footer_file = __DIR__.DS."inc".DS."main-footer.php";
    if(file_exists($footer_file)) include $footer_file;
?>

</body>
</html>
