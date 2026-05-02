<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$domain_check_url = cdg_link('domain');
$hosting_buy_url  = cdg_link('products', ['hosting']);
$server_buy_url   = cdg_link('products', ['server']);
$contact_url      = cdg_link('contact');

$domain_first_price = '';
if(isset($first_tld_price) && is_array($first_tld_price)) {
    $amt = $first_tld_price['register']['amount'] ?? 0;
    $cid = $first_tld_price['register']['cid'] ?? 0;
    if($amt && class_exists('Money') && method_exists('Money', 'formatter_symbol')) {
        $domain_first_price = Money::formatter_symbol($amt, $cid);
    }
}

$mod_hosting = !empty($pg_activation['hosting']);
$mod_server  = !empty($pg_activation['server']);
$mod_domain  = !empty($pg_activation['domain']);
$mod_sms     = !empty($pg_activation['sms']);
?>

<!-- HERO -->
<section class="cdg-hero">
    <div class="cdg-hero-bg">
        <div class="cdg-hero-glow"></div>
        <div class="cdg-hero-dots"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-hero-grid">
            <div class="cdg-hero-content">
                <div class="cdg-hero-badge">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Yeni nesil hosting altyapisi</span>
                </div>
                <h1 class="cdg-hero-title">
                    Hayalinizdeki <span class="cdg-text-gradient">web siteniz</span> icin guclu hosting
                </h1>
                <p class="cdg-hero-subtitle">
                    NVMe SSD diskler, %99.9 calisma garantisi, ucretsiz SSL ve 7/24 destek.
                    Profesyonel barindirma cozumlerimizle isletmenizi guvenle buyutun.
                </p>

                <form action="<?php echo $domain_check_url; ?>" method="get" class="cdg-domain-search">
                    <div class="cdg-domain-input-wrap">
                        <i class="bi bi-globe2"></i>
                        <input type="text" name="domain" placeholder="ornek-alanadi.com" autocomplete="off" required>
                    </div>
                    <button type="submit" class="cdg-btn cdg-btn-primary cdg-btn-lg">
                        <i class="bi bi-search"></i>
                        <span>Sorgula</span>
                    </button>
                </form>

                <div class="cdg-domain-pricing">
                    <span class="cdg-tld-chip">.com</span>
                    <span class="cdg-tld-chip">.com.tr</span>
                    <span class="cdg-tld-chip">.net</span>
                    <span class="cdg-tld-chip">.org</span>
                    <?php if($domain_first_price): ?>
                        <span class="cdg-domain-price-info">Yillik <strong><?php echo $domain_first_price; ?></strong>'den</span>
                    <?php endif; ?>
                </div>

                <div class="cdg-hero-trust">
                    <div class="cdg-trust-item"><i class="bi bi-shield-check"></i><span>Ucretsiz <strong>SSL</strong></span></div>
                    <div class="cdg-trust-item"><i class="bi bi-clock-history"></i><span><strong>%99.9</strong> Uptime</span></div>
                    <div class="cdg-trust-item"><i class="bi bi-headset"></i><span>7/24 <strong>Destek</strong></span></div>
                </div>
            </div>

            <div class="cdg-hero-visual">
                <div class="cdg-hero-card cdg-hero-card-1">
                    <div class="cdg-hero-card-icon"><i class="bi bi-hdd-network"></i></div>
                    <div><div class="cdg-hero-card-title">NVMe SSD</div><div class="cdg-hero-card-sub">10x daha hizli</div></div>
                </div>
                <div class="cdg-hero-card cdg-hero-card-2">
                    <div class="cdg-hero-card-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <div><div class="cdg-hero-card-title">Imunify360</div><div class="cdg-hero-card-sub">Aktif koruma</div></div>
                </div>
                <div class="cdg-hero-card cdg-hero-card-3">
                    <div class="cdg-hero-card-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                    <div><div class="cdg-hero-card-title">Yedekleme</div><div class="cdg-hero-card-sub">Gunluk JetBackup</div></div>
                </div>
                <div class="cdg-hero-card cdg-hero-card-4">
                    <div class="cdg-hero-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div><div class="cdg-hero-card-title">LiteSpeed</div><div class="cdg-hero-card-sub">PHP/X / LSCache</div></div>
                </div>
                <div class="cdg-hero-orb"></div>
            </div>
        </div>
    </div>
</section>

<!-- HİZMETLER -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hizmetlerimiz</div>
            <h2>Isletmenizin ihtiyaci olan tum cozumler</h2>
            <p>Web hosting'den sunucu kiralamaya, domain'den SMS pazarlamaya kadar tek noktadan yonetin.</p>
        </div>

        <div class="cdg-services-grid">
            <?php if($mod_hosting): ?>
            <a href="<?php echo $hosting_buy_url; ?>" class="cdg-service-card">
                <div class="cdg-service-icon"><i class="bi bi-hdd-network"></i></div>
                <h3>Web Hosting</h3>
                <p>NVMe SSD, LiteSpeed, ucretsiz SSL ve gunluk yedekleme dahil paketler.</p>
                <div class="cdg-service-cta">Paketleri Incele <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endif; ?>

            <?php if($mod_server): ?>
            <a href="<?php echo $server_buy_url; ?>" class="cdg-service-card">
                <div class="cdg-service-icon"><i class="bi bi-server"></i></div>
                <h3>Sanal Sunucu</h3>
                <p>Yuksek performansli VPS ve dedicated sunucular. KVM, AMD EPYC, dedicated IP.</p>
                <div class="cdg-service-cta">Sunuculari Gor <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endif; ?>

            <?php if($mod_domain): ?>
            <a href="<?php echo $domain_check_url; ?>" class="cdg-service-card">
                <div class="cdg-service-icon"><i class="bi bi-globe2"></i></div>
                <h3>Alan Adi (Domain)</h3>
                <p>500+ uzanti destegi. .com, .com.tr, .net, .org ve daha fazlasi uygun fiyatlarla.</p>
                <div class="cdg-service-cta">Alan Adi Sorgula <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endif; ?>

            <a href="<?php echo cdg_link('ssl'); ?>" class="cdg-service-card">
                <div class="cdg-service-icon"><i class="bi bi-shield-lock"></i></div>
                <h3>SSL Sertifikalari</h3>
                <p>DV, OV, EV ve Wildcard sertifikalar. Sitenizin guvenligi ve SEO icin kritik.</p>
                <div class="cdg-service-cta">SSL Cesitleri <i class="bi bi-arrow-right"></i></div>
            </a>

            <?php if($mod_sms): ?>
            <a href="<?php echo cdg_link('products', ['sms']); ?>" class="cdg-service-card">
                <div class="cdg-service-icon"><i class="bi bi-chat-dots"></i></div>
                <h3>SMS Hizmetleri</h3>
                <p>Toplu SMS gonderimi, OTP, bilgilendirme. Yuksek teslimat orani ve API destegi.</p>
                <div class="cdg-service-cta">SMS Paketleri <i class="bi bi-arrow-right"></i></div>
            </a>
            <?php endif; ?>

            <a href="<?php echo $contact_url; ?>" class="cdg-service-card">
                <div class="cdg-service-icon"><i class="bi bi-stars"></i></div>
                <h3>Ozel Cozumler</h3>
                <p>Kurumsal ihtiyaclariniza ozel hosting, sunucu ve yazilim cozumleri sunuyoruz.</p>
                <div class="cdg-service-cta">Iletisime Gec <i class="bi bi-arrow-right"></i></div>
            </a>
        </div>
    </div>
</section>

<!-- NEDEN BIZ -->
<section class="cdg-section cdg-section-alt">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Neden Codega?</div>
            <h2>Modern altyapi, profesyonel yaklasim</h2>
            <p>Sadece hosting saglayicisi degil, dijital donusum yolculugunuzda is ortaginiziz.</p>
        </div>

        <div class="cdg-features-grid">
            <div class="cdg-feature">
                <div class="cdg-feature-icon"><i class="bi bi-lightning-charge"></i></div>
                <h4>Yildirim Hizinda</h4>
                <p>NVMe SSD, LiteSpeed Web Server ve LSCache ile sayfalariniz gozlerinizi kamastiracak.</p>
            </div>
            <div class="cdg-feature">
                <div class="cdg-feature-icon"><i class="bi bi-shield-check"></i></div>
                <h4>Maksimum Guvenlik</h4>
                <p>Imunify360 koruma, otomatik malware tarama, DDoS koruma ve gunluk yedekleme.</p>
            </div>
            <div class="cdg-feature">
                <div class="cdg-feature-icon"><i class="bi bi-headset"></i></div>
                <h4>Gercek Destek</h4>
                <p>Bot degil, gercek muhendisler. 7/24 e-posta, telefon ve canli destek.</p>
            </div>
            <div class="cdg-feature">
                <div class="cdg-feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <h4>%99.9 Uptime</h4>
                <p>Yedekli ag altyapisi ve kararli sunucularla siteniz her zaman erisilebilir.</p>
            </div>
            <div class="cdg-feature">
                <div class="cdg-feature-icon"><i class="bi bi-arrow-repeat"></i></div>
                <h4>Ucretsiz Tasima</h4>
                <p>Mevcut hosting'inizi tum dosyalari, e-postalari ve veritabanlariyla biz tasiyalim.</p>
            </div>
            <div class="cdg-feature">
                <div class="cdg-feature-icon"><i class="bi bi-cash-coin"></i></div>
                <h4>30 Gun Iade</h4>
                <p>Memnun kalmazsaniz 30 gun icinde kosulsuz iade. Riskinizi biz ustleniyoruz.</p>
            </div>
        </div>
    </div>
</section>

<!-- HOSTING PAKETLERİ -->
<?php if($mod_hosting): ?>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Hosting Paketleri</div>
            <h2>Her ihtiyaca uygun bir paket var</h2>
            <p>Kucuk blogdan kurumsal e-ticarete kadar her olcek icin optimize edilmis paketler.</p>
        </div>

        <div class="cdg-pricing-grid">
            <div class="cdg-pricing-card">
                <div class="cdg-pricing-tier">Baslangic</div>
                <div class="cdg-pricing-icon"><i class="bi bi-rocket"></i></div>
                <div class="cdg-pricing-price"><small>aylik</small><strong>Uygun</strong></div>
                <ul class="cdg-pricing-features">
                    <li><i class="bi bi-check-lg"></i> 5 GB NVMe SSD Disk</li>
                    <li><i class="bi bi-check-lg"></i> 50 GB Aylik Trafik</li>
                    <li><i class="bi bi-check-lg"></i> 10 E-posta Hesabi</li>
                    <li><i class="bi bi-check-lg"></i> 5 MySQL Veritabani</li>
                    <li><i class="bi bi-check-lg"></i> Ucretsiz SSL</li>
                    <li><i class="bi bi-check-lg"></i> Gunluk Yedek</li>
                </ul>
                <a href="<?php echo $hosting_buy_url; ?>" class="cdg-btn cdg-btn-outline">Detaylar</a>
            </div>

            <div class="cdg-pricing-card cdg-pricing-popular">
                <div class="cdg-pricing-popular-badge"><i class="bi bi-star-fill"></i> En Cok Tercih</div>
                <div class="cdg-pricing-tier">Profesyonel</div>
                <div class="cdg-pricing-icon"><i class="bi bi-stars"></i></div>
                <div class="cdg-pricing-price"><small>aylik</small><strong>Populer</strong></div>
                <ul class="cdg-pricing-features">
                    <li><i class="bi bi-check-lg"></i> 25 GB NVMe SSD Disk</li>
                    <li><i class="bi bi-check-lg"></i> 250 GB Aylik Trafik</li>
                    <li><i class="bi bi-check-lg"></i> Sinirsiz E-posta</li>
                    <li><i class="bi bi-check-lg"></i> Sinirsiz Veritabani</li>
                    <li><i class="bi bi-check-lg"></i> Ucretsiz SSL Wildcard</li>
                    <li><i class="bi bi-check-lg"></i> Gunluk + Haftalik Yedek</li>
                    <li><i class="bi bi-check-lg"></i> LiteSpeed + LSCache</li>
                </ul>
                <a href="<?php echo $hosting_buy_url; ?>" class="cdg-btn cdg-btn-primary">Hemen Sec</a>
            </div>

            <div class="cdg-pricing-card">
                <div class="cdg-pricing-tier">Kurumsal</div>
                <div class="cdg-pricing-icon"><i class="bi bi-building"></i></div>
                <div class="cdg-pricing-price"><small>aylik</small><strong>Premium</strong></div>
                <ul class="cdg-pricing-features">
                    <li><i class="bi bi-check-lg"></i> 100 GB NVMe SSD Disk</li>
                    <li><i class="bi bi-check-lg"></i> 1 TB Aylik Trafik</li>
                    <li><i class="bi bi-check-lg"></i> Sinirsiz E-posta</li>
                    <li><i class="bi bi-check-lg"></i> Sinirsiz Veritabani</li>
                    <li><i class="bi bi-check-lg"></i> Ucretsiz SSL Premium</li>
                    <li><i class="bi bi-check-lg"></i> Gercek Zamanli Yedek</li>
                    <li><i class="bi bi-check-lg"></i> Oncelikli Destek</li>
                </ul>
                <a href="<?php echo $hosting_buy_url; ?>" class="cdg-btn cdg-btn-outline">Detaylar</a>
            </div>
        </div>

        <div class="cdg-pricing-note">
            <i class="bi bi-info-circle"></i>
            Tum paketlerimiz <strong>30 gun para iade garantisi</strong> ile gelir. Kurulum ve tasima ucretsizdir.
        </div>
    </div>
</section>
<?php endif; ?>

<!-- İSTATİSTİKLER -->
<section class="cdg-section-stats">
    <div class="cdg-container">
        <div class="cdg-stats-grid">
            <div class="cdg-stat-block"><div class="cdg-stat-number">10K+</div><div class="cdg-stat-label">Mutlu Musteri</div></div>
            <div class="cdg-stat-block"><div class="cdg-stat-number">99.9%</div><div class="cdg-stat-label">Uptime Garantisi</div></div>
            <div class="cdg-stat-block"><div class="cdg-stat-number">7/24</div><div class="cdg-stat-label">Turkce Destek</div></div>
            <div class="cdg-stat-block"><div class="cdg-stat-number">15+</div><div class="cdg-stat-label">Yil Deneyim</div></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cdg-cta">
    <div class="cdg-container">
        <div class="cdg-cta-inner">
            <h2>Bugun baslayin, fark yarin gorulsun</h2>
            <p>30 gun iade garantisi ile risksiz deneyin. Mevcut sitenizi <strong>ucretsiz</strong> tasiyalim.</p>
            <div class="cdg-cta-actions">
                <?php if($mod_hosting): ?>
                <a href="<?php echo $hosting_buy_url; ?>" class="cdg-btn cdg-btn-white cdg-btn-lg">
                    <i class="bi bi-rocket-takeoff"></i><span>Hosting Paketi Sec</span>
                </a>
                <?php endif; ?>
                <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-ghost cdg-btn-lg">
                    <i class="bi bi-chat-square-text"></i><span>Bize Ulasin</span>
                </a>
            </div>
        </div>
    </div>
</section>
