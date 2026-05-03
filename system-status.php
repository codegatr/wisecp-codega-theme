<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$pageTitle = 'Sistem Durumu | CODEGA';
$pageDescription = 'CODEGA altyapı durumu — hosting, domain, e-posta, ödeme servisleri. Gerçek zamanlı uptime takibi.';

// Servis kategorileri (gerçek monitoring entegrasyonu için ileride API)
$services = [
    [
        'kategori' => 'Hosting Altyapısı',
        'icon' => 'bi-hdd-network-fill',
        'servisler' => [
            ['ad' => 'Web Sunucuları (LiteSpeed)', 'durum' => 'operational', 'uptime' => '99.99%'],
            ['ad' => 'MySQL / MariaDB Cluster', 'durum' => 'operational', 'uptime' => '99.99%'],
            ['ad' => 'PHP-FPM (8.3)', 'durum' => 'operational', 'uptime' => '99.98%'],
            ['ad' => 'NVMe Storage', 'durum' => 'operational', 'uptime' => '99.99%'],
        ],
    ],
    [
        'kategori' => 'Domain & DNS',
        'icon' => 'bi-globe2',
        'servisler' => [
            ['ad' => 'DNS Çözümleme', 'durum' => 'operational', 'uptime' => '100%'],
            ['ad' => 'WHOIS Sorgulama', 'durum' => 'operational', 'uptime' => '99.95%'],
            ['ad' => 'Domain Kayıt Sistemi', 'durum' => 'operational', 'uptime' => '99.97%'],
        ],
    ],
    [
        'kategori' => 'E-Posta',
        'icon' => 'bi-envelope-fill',
        'servisler' => [
            ['ad' => 'SMTP Gönderim', 'durum' => 'operational', 'uptime' => '99.96%'],
            ['ad' => 'IMAP/POP3 Erişim', 'durum' => 'operational', 'uptime' => '99.98%'],
            ['ad' => 'Webmail (Roundcube)', 'durum' => 'operational', 'uptime' => '99.95%'],
            ['ad' => 'SpamAssassin Filtreleme', 'durum' => 'operational', 'uptime' => '99.99%'],
        ],
    ],
    [
        'kategori' => 'Ödeme & Fatura',
        'icon' => 'bi-credit-card-2-front-fill',
        'servisler' => [
            ['ad' => 'iyzico Entegrasyonu', 'durum' => 'operational', 'uptime' => '99.92%'],
            ['ad' => 'PayTR Entegrasyonu', 'durum' => 'operational', 'uptime' => '99.94%'],
            ['ad' => 'GİB e-Fatura/e-Arşiv', 'durum' => 'operational', 'uptime' => '99.90%'],
        ],
    ],
    [
        'kategori' => 'Müşteri Paneli',
        'icon' => 'bi-person-workspace',
        'servisler' => [
            ['ad' => 'Client Area Erişimi', 'durum' => 'operational', 'uptime' => '99.99%'],
            ['ad' => 'Provisioning API', 'durum' => 'operational', 'uptime' => '99.97%'],
            ['ad' => 'Destek Talep Sistemi', 'durum' => 'operational', 'uptime' => '100%'],
        ],
    ],
    [
        'kategori' => 'Veri Merkezi',
        'icon' => 'bi-server',
        'servisler' => [
            ['ad' => 'Konya / Türkiye (Tier-3)', 'durum' => 'operational', 'uptime' => '99.99%'],
            ['ad' => 'Yedekleme Sistemi', 'durum' => 'operational', 'uptime' => '100%'],
            ['ad' => 'CDN (CloudFlare)', 'durum' => 'operational', 'uptime' => '99.98%'],
        ],
    ],
];

// Son olaylar (örnek - ileride DB tablosundan)
$incidents = [
    ['tarih' => '2026-04-22', 'baslik' => 'Planlı Bakım — DNS Sunucu Güncellemesi', 'durum' => 'resolved', 'sure' => '15 dakika', 'aciklama' => 'BIND9 → PowerDNS geçişi tamamlandı. Hizmet kesintisi yaşanmadı.'],
    ['tarih' => '2026-04-15', 'baslik' => 'PHP 8.3 Güvenlik Güncellemesi', 'durum' => 'resolved', 'sure' => '5 dakika', 'aciklama' => 'CVE-2026-3823 yamalı PHP 8.3.34 sürümüne otomatik geçiş yapıldı.'],
    ['tarih' => '2026-03-08', 'baslik' => 'SSL Sertifikası Yenileme', 'durum' => 'resolved', 'sure' => '0 dakika', 'aciklama' => 'Tüm wildcard sertifikalar Let\'s Encrypt üzerinden otomatik yenilendi.'],
];

// Genel durum
$overall_status = 'operational'; // operational | degraded | outage

function cdg_status_label($s) {
    return [
        'operational' => 'Çalışıyor',
        'degraded'    => 'Kısmi Sorun',
        'outage'      => 'Kesinti',
        'maintenance' => 'Bakım',
        'resolved'    => 'Çözüldü',
    ][$s] ?? 'Bilinmiyor';
}
function cdg_status_color($s) {
    return [
        'operational' => '#10b981',
        'degraded'    => '#f59e0b',
        'outage'      => '#ef4444',
        'maintenance' => '#3b82f6',
        'resolved'    => '#10b981',
    ][$s] ?? '#64748b';
}
?>
<!DOCTYPE html>
<html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php
        $hoptions = [ 'page' => "system-status" ];
        $meta = [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'keywords' => 'sistem durumu, uptime, codega status, hosting durumu',
            'robots' => 'index,follow',
        ];
        include __DIR__.DS."inc".DS."main-head.php";
    ?>
    <style>
    .cdg-sys-hero {
        position: relative;
        padding: 60px 0 40px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-sys-hero::before {
        content: ''; position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-sys-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
    }
    .cdg-sys-hero-content { position: relative; z-index: 1; text-align: center; max-width: 720px; margin: 0 auto; }
    .cdg-sys-hero h1 {
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800; margin: 0 0 14px;
        letter-spacing: -0.02em; color: #fff;
    }
    .cdg-sys-hero p {
        font-size: 16px;
        color: rgba(255,255,255,0.78);
        margin: 0 0 24px;
    }

    .cdg-sys-overall {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 14px 24px;
        background: rgba(16,185,129,0.10);
        border: 2px solid rgba(16,185,129,0.40);
        border-radius: 100px;
        font-size: 15px;
        font-weight: 700;
        color: #34d399;
    }
    .cdg-sys-overall .dot {
        width: 12px; height: 12px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 12px #10b981;
        animation: cdgSysPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgSysPulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.2); } }
    .cdg-sys-overall.degraded { background: rgba(245,158,11,0.10); border-color: rgba(245,158,11,0.40); color: #fbbf24; }
    .cdg-sys-overall.degraded .dot { background: #f59e0b; box-shadow: 0 0 12px #f59e0b; }
    .cdg-sys-overall.outage { background: rgba(239,68,68,0.10); border-color: rgba(239,68,68,0.40); color: #f87171; }
    .cdg-sys-overall.outage .dot { background: #ef4444; box-shadow: 0 0 12px #ef4444; }

    /* Servisler */
    .cdg-sys-services {
        padding: 60px 0;
        background: #fff;
    }
    .cdg-sys-services-list {
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .cdg-sys-category {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .cdg-sys-category:hover {
        border-color: #00D3E5;
        box-shadow: 0 12px 28px rgba(0,211,229,0.05);
    }
    .cdg-sys-category-head {
        padding: 18px 22px;
        background: linear-gradient(180deg, #f8fafc, #fff);
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cdg-sys-category-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .cdg-sys-category-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        flex: 1;
    }
    .cdg-sys-category-services {
        padding: 8px 0;
    }
    .cdg-sys-service {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 22px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .cdg-sys-service:last-child { border-bottom: 0; }
    .cdg-sys-service-status {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .cdg-sys-service-name {
        flex: 1;
        color: #0f172a;
        font-weight: 500;
    }
    .cdg-sys-service-uptime {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .cdg-sys-service-label {
        padding: 4px 10px;
        background: rgba(16,185,129,0.10);
        color: #059669;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Olaylar */
    .cdg-sys-incidents {
        padding: 60px 0;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .cdg-sys-section-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 36px;
    }
    .cdg-sys-section-head h2 {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 10px;
        letter-spacing: -0.02em;
    }
    .cdg-sys-section-head p {
        font-size: 15px;
        color: #64748b;
        margin: 0;
    }
    .cdg-sys-incidents-list {
        max-width: 760px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .cdg-sys-incident {
        padding: 18px 22px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .cdg-sys-incident:hover {
        border-color: #00D3E5;
        box-shadow: 0 8px 20px rgba(0,211,229,0.05);
    }
    .cdg-sys-incident-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }
    .cdg-sys-incident-date {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .cdg-sys-incident-resolved {
        padding: 3px 10px;
        background: rgba(16,185,129,0.10);
        color: #059669;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .cdg-sys-incident h3 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cdg-sys-incident p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.55;
        margin: 0 0 8px;
    }
    .cdg-sys-incident-duration {
        font-size: 12px;
        color: #475569;
    }
    .cdg-sys-incident-duration i { color: #00D3E5; }

    /* Boş olay durumu */
    .cdg-sys-no-incidents {
        text-align: center;
        padding: 36px 24px;
        background: rgba(16,185,129,0.05);
        border: 1px dashed rgba(16,185,129,0.30);
        border-radius: 14px;
        color: #059669;
    }
    .cdg-sys-no-incidents i { font-size: 48px; margin-bottom: 10px; display: block; }

    /* CTA */
    .cdg-sys-cta {
        padding: 60px 0;
        background: #fff;
        text-align: center;
    }
    .cdg-sys-cta-card {
        max-width: 640px;
        margin: 0 auto;
        padding: 32px;
        background: linear-gradient(135deg, #f8fafc, #fff);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .cdg-sys-cta h3 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
    }
    .cdg-sys-cta p {
        font-size: 14px;
        color: #64748b;
        margin: 0 0 20px;
    }
    .cdg-sys-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        background: linear-gradient(135deg, #2E3B4E, #1e293b);
        color: #fff !important;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-sys-cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(46,59,78,0.30);
    }

    @media (max-width: 640px) {
        .cdg-sys-service { flex-wrap: wrap; padding: 12px 16px; }
        .cdg-sys-service-uptime { width: 100%; padding-left: 24px; }
        .cdg-sys-incident-head { flex-direction: column; align-items: flex-start; gap: 6px; }
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
<section class="cdg-sys-hero">
    <div class="cdg-sys-hero-grid"></div>
    <div class="cdg-container">
        <div class="cdg-sys-hero-content">
            <h1>Sistem Durumu</h1>
            <p>Hosting, domain, e-posta ve ödeme servislerinin gerçek zamanlı durumu. Şeffaflık değerimizdir.</p>
            <div class="cdg-sys-overall <?php echo $overall_status; ?>">
                <span class="dot"></span>
                <span>
                    <?php if ($overall_status === 'operational'): ?>
                        Tüm Sistemler Sorunsuz Çalışıyor
                    <?php elseif ($overall_status === 'degraded'): ?>
                        Bazı Servislerde Sorun Var
                    <?php else: ?>
                        Aktif Kesinti
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- SERVİSLER -->
<section class="cdg-sys-services">
    <div class="cdg-container">
        <div class="cdg-sys-services-list">
            <?php foreach ($services as $cat): ?>
            <div class="cdg-sys-category">
                <div class="cdg-sys-category-head">
                    <div class="cdg-sys-category-icon">
                        <i class="<?php echo $cat['icon']; ?>"></i>
                    </div>
                    <div class="cdg-sys-category-name"><?php echo htmlspecialchars($cat['kategori']); ?></div>
                </div>
                <div class="cdg-sys-category-services">
                    <?php foreach ($cat['servisler'] as $s): ?>
                    <div class="cdg-sys-service">
                        <div class="cdg-sys-service-status" style="background:<?php echo cdg_status_color($s['durum']); ?>;box-shadow:0 0 8px <?php echo cdg_status_color($s['durum']); ?>;"></div>
                        <div class="cdg-sys-service-name"><?php echo htmlspecialchars($s['ad']); ?></div>
                        <span class="cdg-sys-service-uptime"><?php echo $s['uptime']; ?> uptime</span>
                        <span class="cdg-sys-service-label"><?php echo cdg_status_label($s['durum']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SON OLAYLAR -->
<section class="cdg-sys-incidents">
    <div class="cdg-container">
        <div class="cdg-sys-section-head">
            <h2>Son 30 Gün</h2>
            <p>Planlı bakımlar, çözülen sorunlar ve geçmiş olaylar.</p>
        </div>
        <div class="cdg-sys-incidents-list">
            <?php if (count($incidents) > 0): ?>
                <?php foreach ($incidents as $inc): ?>
                <div class="cdg-sys-incident">
                    <div class="cdg-sys-incident-head">
                        <span class="cdg-sys-incident-date">
                            <i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($inc['tarih']); ?>
                        </span>
                        <span class="cdg-sys-incident-resolved">
                            <i class="bi bi-check-circle-fill"></i> <?php echo cdg_status_label($inc['durum']); ?>
                        </span>
                    </div>
                    <h3><?php echo htmlspecialchars($inc['baslik']); ?></h3>
                    <p><?php echo htmlspecialchars($inc['aciklama']); ?></p>
                    <div class="cdg-sys-incident-duration">
                        <i class="bi bi-clock"></i> Süre: <strong><?php echo htmlspecialchars($inc['sure']); ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="cdg-sys-no-incidents">
                    <i class="bi bi-shield-fill-check"></i>
                    <strong>Son 30 günde olay kaydı yok.</strong>
                    <p style="margin-top: 6px; color: #64748b;">Tüm sistemler kesintisiz çalışıyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cdg-sys-cta">
    <div class="cdg-container">
        <div class="cdg-sys-cta-card">
            <h3>Bir sorun mu yaşıyorsunuz?</h3>
            <p>Yukarıdaki listede yansımayan bir kesinti tespit ederseniz veya destek almak isterseniz bizimle iletişime geçin.</p>
            <a href="<?php echo $contact_url; ?>" class="cdg-sys-cta-btn">
                <i class="bi bi-headset"></i> Destek Talep Et
            </a>
        </div>
    </div>
</section>

<?php
    $footer_file = __DIR__.DS."inc".DS."main-footer.php";
    if(file_exists($footer_file)) include $footer_file;
?>

</body>
</html>
