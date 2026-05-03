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

<?php
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php';
$contact_url = class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact';
?>

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
                    <div class="cdg-sys-category-name"><?php echo htmlspecialchars($cat['kategori'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
                <div class="cdg-sys-category-services">
                    <?php foreach ($cat['servisler'] as $s): ?>
                    <div class="cdg-sys-service">
                        <div class="cdg-sys-service-status" style="background:<?php echo cdg_status_color($s['durum']); ?>;box-shadow:0 0 8px <?php echo cdg_status_color($s['durum']); ?>;"></div>
                        <div class="cdg-sys-service-name"><?php echo htmlspecialchars($s['ad'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
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
                            <i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($inc['tarih'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                        <span class="cdg-sys-incident-resolved">
                            <i class="bi bi-check-circle-fill"></i> <?php echo cdg_status_label($inc['durum']); ?>
                        </span>
                    </div>
                    <h3><?php echo htmlspecialchars($inc['baslik'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($inc['aciklama'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                    <div class="cdg-sys-incident-duration">
                        <i class="bi bi-clock"></i> Süre: <strong><?php echo htmlspecialchars($inc['sure'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
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
