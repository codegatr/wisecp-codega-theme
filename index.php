<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$config = include __DIR__ . DS . 'theme-config.php';
$ts     = isset($config['settings']) ? $config['settings'] : [];

$banner   = isset($ts['banner'])   ? $ts['banner']   : [];
$services = isset($ts['services']) ? $ts['services'] : [];
$features = isset($ts['features']) ? $ts['features'] : [];
$show_services = isset($ts['show_services']) ? $ts['show_services'] : 1;
$show_features = isset($ts['show_features']) ? $ts['show_features'] : 1;
$show_pricing  = isset($ts['show_pricing'])  ? $ts['show_pricing']  : 1;
$show_cta      = isset($ts['show_cta'])      ? $ts['show_cta']      : 1;

$tlds = ['.com', '.com.tr', '.net', '.org', '.io', '.dev', '.tr', '.co'];
?>

<!-- HERO -->
<section class="cdg-hero">
    <div class="cdg-container">
        <div class="cdg-hero-inner">

            <div>
                <div class="cdg-hero-eyebrow">
                    <span class="dot"></span>
                    Turkiye lokasyonlu altyapi
                </div>

                <h1>
                    <?php
                    $heading = isset($banner['heading']) ? $banner['heading'] : 'Modern Yazilimla Isinizi Buyutun';
                    $parts = explode(' ', strip_tags($heading, '<br>'));
                    if(count($parts) > 1) {
                        $last = array_pop($parts);
                        $heading = implode(' ', $parts) . ' <strong>' . $last . '</strong>';
                    }
                    echo $heading;
                    ?>
                </h1>

                <p><?php echo isset($banner['content']) ? $banner['content'] : ''; ?></p>

                <div class="cdg-hero-actions">
                    <a href="<?php echo isset($banner['button_link1']) ? $banner['button_link1'] : (class_exists('Controllers') ? Controllers::$init->CRLink('products', ['hosting']) : '/hosting-products'); ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg">
                        <i class="bi bi-rocket-takeoff"></i>
                        <?php echo isset($banner['button_text1']) ? $banner['button_text1'] : 'Hizmetlerimiz'; ?>
                    </a>
                    <a href="<?php echo isset($banner['button_link2']) ? $banner['button_link2'] : (class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg">
                        <i class="bi bi-chat-dots"></i>
                        <?php echo isset($banner['button_text2']) ? $banner['button_text2'] : 'Iletisim'; ?>
                    </a>
                </div>
            </div>

            <div class="cdg-hero-visual">
                <div class="glow"></div>

                <div class="cdg-hero-card cdg-hero-card-1">
                    <div class="head">
                        <div class="icon"><i class="bi bi-speedometer2"></i></div>
                        <div>
                            <div class="title">Sunucu Performansi</div>
                            <div class="label" style="font-size:11px;color:var(--cdg-muted);">son 24 saat</div>
                        </div>
                    </div>
                    <div class="stat">99.98%</div>
                    <div class="label">Uptime</div>
                    <div class="bar"><span></span></div>
                </div>

                <div class="cdg-hero-card cdg-hero-card-2">
                    <div class="head">
                        <div class="icon" style="background:rgba(16,185,129,0.10);color:#10b981;"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <div class="title">SSL Aktif</div>
                            <div class="label" style="font-size:11px;color:#10b981;font-weight:600;">Korumali</div>
                        </div>
                    </div>
                    <div class="stat" style="font-size:18px;">codega.com.tr</div>
                    <div class="label" style="margin-top:6px;">Gecerlilik: 12 ay</div>
                </div>
            </div>
        </div>

        <div class="cdg-domain-check">
            <h3><i class="bi bi-search"></i> Markaniz icin en uygun domain'i bulun</h3>
            <form action="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('domain') : '/domain'); ?>" method="get" class="form">
                <input type="text" name="domain" placeholder="ornek-domain.com" autocomplete="off">
                <button type="submit" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-search"></i> Sorgula
                </button>
            </form>
            <div class="tlds">
                <span style="font-size:12px;color:var(--cdg-muted);align-self:center;margin-right:4px;">Populer:</span>
                <?php foreach($tlds as $t): ?>
                    <span class="tld"><?php echo $t; ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


<?php if($show_services && !empty($services)): ?>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head cdg-reveal">
            <span class="cdg-section-eyebrow">Hizmetlerimiz</span>
            <h2>Dijital ihtiyaciniz tek catida</h2>
        </div>
        <div class="cdg-grid cdg-grid-3">
            <?php foreach($services as $i => $s):
                $color = isset($s['color']) ? $s['color'] : 'primary';
            ?>
                <div class="cdg-service-card color-<?php echo $color; ?> cdg-reveal" style="transition-delay: <?php echo $i * 60; ?>ms;">
                    <div class="icon-wrap"><i class="bi <?php echo isset($s['icon']) ? $s['icon'] : 'bi-square'; ?>"></i></div>
                    <h3><?php echo htmlspecialchars(isset($s['title']) ? $s['title'] : ''); ?></h3>
                    <p><?php echo htmlspecialchars(isset($s['text']) ? $s['text'] : ''); ?></p>
                    <a href="<?php echo isset($s['link']) ? $s['link'] : '#'; ?>" class="more">Incele</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($show_features && !empty($features)): ?>
<section class="cdg-section cdg-section-alt">
    <div class="cdg-container">
        <div class="cdg-section-head cdg-reveal">
            <span class="cdg-section-eyebrow">Neden CODEGA?</span>
            <h2>Surdurulebilir, olceklenebilir, guvenli</h2>
        </div>
        <div class="cdg-grid cdg-grid-4">
            <?php foreach($features as $i => $f): ?>
                <div class="cdg-feature cdg-reveal" style="transition-delay: <?php echo $i * 60; ?>ms;">
                    <div class="icon"><i class="bi <?php echo isset($f['icon']) ? $f['icon'] : 'bi-star'; ?>"></i></div>
                    <h3><?php echo htmlspecialchars(isset($f['title']) ? $f['title'] : ''); ?></h3>
                    <p><?php echo htmlspecialchars(isset($f['text']) ? $f['text'] : ''); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($show_pricing): ?>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head cdg-reveal">
            <span class="cdg-section-eyebrow">Fiyatlandirma</span>
            <h2>Her ihtiyaca uygun hosting</h2>
        </div>
        <div class="cdg-grid cdg-grid-3" style="align-items:stretch;">

            <div class="cdg-price-card cdg-reveal">
                <div class="name">Baslangic</div>
                <div class="amount">&#8378;49<small> /ay</small></div>
                <div class="cycle">Yillik odemede &#8378;588</div>
                <ul>
                    <li>5 GB NVMe SSD</li>
                    <li>50 GB Trafik</li>
                    <li>5 E-posta</li>
                    <li>Ucretsiz SSL</li>
                </ul>
                <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('products', ['hosting']) : '/hosting-products'); ?>" class="cdg-btn cdg-btn-outline">Detaylar</a>
            </div>

            <div class="cdg-price-card featured cdg-reveal" style="transition-delay:80ms;">
                <span class="badge">Populer</span>
                <div class="name">Profesyonel</div>
                <div class="amount">&#8378;99<small> /ay</small></div>
                <div class="cycle">Yillik odemede &#8378;1.188</div>
                <ul>
                    <li>20 GB NVMe SSD</li>
                    <li>Sinirsiz Trafik</li>
                    <li>50 E-posta</li>
                    <li>SSL + WAF</li>
                    <li>LiteSpeed</li>
                </ul>
                <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('products', ['hosting']) : '/hosting-products'); ?>" class="cdg-btn cdg-btn-primary">Hemen Basla</a>
            </div>

            <div class="cdg-price-card cdg-reveal" style="transition-delay:160ms;">
                <div class="name">Kurumsal</div>
                <div class="amount">&#8378;249<small> /ay</small></div>
                <div class="cycle">Yillik odemede &#8378;2.988</div>
                <ul>
                    <li>100 GB NVMe SSD</li>
                    <li>Sinirsiz Trafik</li>
                    <li>Sinirsiz E-posta</li>
                    <li>Premium SSL</li>
                </ul>
                <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('products', ['hosting']) : '/hosting-products'); ?>" class="cdg-btn cdg-btn-outline">Detaylar</a>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>


<?php if($show_cta): ?>
<section class="cdg-container">
    <div class="cdg-cta">
        <div class="cdg-cta-inner">
            <h2>Projeniz icin dogru cozumu bulalim</h2>
            <p>Web yazilim, ERP, e-ticaret veya ozel projeleriniz icin hemen iletisime gecin.</p>
            <div class="cdg-hero-actions" style="justify-content:center;">
                <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg">
                    <i class="bi bi-chat-square-text"></i> Teklif Alin
                </a>
                <a href="<?php echo (class_exists('Controllers') ? Controllers::$init->CRLink('sign-up') : '/sign-up'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg">
                    <i class="bi bi-person-plus"></i> Uye Ol
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
