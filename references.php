<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Referans verilerini yükle (codega.com.tr migration)
$refs_data = require __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'references.php';
$refs = $refs_data['referanslar'] ?? [];

// Sektör listesini topla (filtre için)
$sectors = [];
foreach ($refs as $r) {
    $s = $r['sektor'] ?? 'Diğer';
    if (!isset($sectors[$s])) $sectors[$s] = 0;
    $sectors[$s]++;
}
arsort($sectors);

// Premium müşteriler (özel yazılım / ERP / web tasarım almış olanlar)
$premium_keywords = ['ERP', 'Özel Yazılım', 'Özel Web', 'Yazılım Hizmetleri', 'VPS', 'Web Yazılım'];
$premium = [];
$standard = [];
foreach ($refs as $r) {
    $hizmet = $r['hizmet'] ?? '';
    $is_premium = false;
    foreach ($premium_keywords as $kw) {
        if (stripos($hizmet, $kw) !== false) { $is_premium = true; break; }
    }
    if ($is_premium) $premium[] = $r; else $standard[] = $r;
}

// Renk havuzu - logo placeholder için
$logo_colors = [
    ['#2E3B4E', '#485A75'],
    ['#00D3E5', '#00E5FF'],
    ['#0EA5E9', '#06B6D4'],
    ['#8B5CF6', '#A78BFA'],
    ['#10B981', '#34D399'],
    ['#F59E0B', '#FBBF24'],
    ['#EF4444', '#F87171'],
    ['#EC4899', '#F472B6'],
];
function cdg_refs_logo_color($name, $colors) {
    $hash = crc32($name);
    return $colors[abs($hash) % count($colors)];
}
function cdg_refs_initials($name) {
    $name = trim(preg_replace('/\.(com|net|org|tr|com\.tr|me)$/i', '', $name));
    $parts = preg_split('/[\s\-\.]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
    if (count($parts) >= 2) {
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    }
    return mb_strtoupper(mb_substr($parts[0], 0, 2));
}

$pageTitle = 'Referanslarımız | CODEGA';
$pageDescription = 'Türkiye\'nin önde gelen kurumları Codega\'ya güveniyor. ' . count($refs) . '+ aktif müşteri, ' . count($sectors) . ' farklı sektör.';
?>

<?php
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php'))
    include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-migration-pages-styles.php';
?>

<!-- HERO -->
<?php
$contact_url = class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact';
?>

<section class="cdg-refs-hero">
    <div class="cdg-refs-hero-grid"></div>
    <div class="cdg-container">
        <div class="cdg-refs-hero-content">
            <div class="cdg-refs-eyebrow">
                <span class="dot"></span>
                <span><?php echo count($refs); ?>+ AKTİF MÜŞTERİ</span>
            </div>
            <h1>Bize güvenen <span>markalar</span></h1>
            <p>Türkiye'nin önde gelen kurumları altyapı, hosting ve özel yazılım için Codega'yı tercih ediyor.</p>
            <div class="cdg-refs-stats">
                <div class="cdg-refs-stat">
                    <div class="cdg-refs-stat-num"><?php echo count($refs); ?>+</div>
                    <div class="cdg-refs-stat-lbl">Aktif Müşteri</div>
                </div>
                <div class="cdg-refs-stat">
                    <div class="cdg-refs-stat-num"><?php echo count($sectors); ?></div>
                    <div class="cdg-refs-stat-lbl">Farklı Sektör</div>
                </div>
                <div class="cdg-refs-stat">
                    <div class="cdg-refs-stat-num"><?php echo count($premium); ?></div>
                    <div class="cdg-refs-stat-lbl">Özel Yazılım</div>
                </div>
                <div class="cdg-refs-stat">
                    <div class="cdg-refs-stat-num">%99.99</div>
                    <div class="cdg-refs-stat-lbl">Uptime SLA</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FİLTRE -->
<section class="cdg-refs-filter">
    <div class="cdg-container">
        <div class="cdg-refs-chips" id="cdgRefsChips">
            <button type="button" class="cdg-refs-chip active" data-sector="all">
                Tümü <span class="count"><?php echo count($refs); ?></span>
            </button>
            <?php foreach ($sectors as $sector => $count): ?>
            <button type="button" class="cdg-refs-chip" data-sector="<?php echo htmlspecialchars($sector, ENT_QUOTES); ?>">
                <?php echo htmlspecialchars($sector); ?>
                <span class="count"><?php echo $count; ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PREMIUM MÜŞTERİLER -->
<?php if (count($premium) > 0): ?>
<section class="cdg-refs-premium">
    <div class="cdg-container">
        <div class="cdg-refs-section-head">
            <span class="cdg-refs-section-eyebrow">⭐ Öne Çıkan</span>
            <h2>Özel yazılım müşterilerimiz</h2>
            <p>Kurumsal düzeyde özel yazılım, ERP ve web tasarım hizmeti aldığımız markalar.</p>
        </div>
        <div class="cdg-refs-premium-grid" id="cdgRefsPremiumGrid">
            <?php foreach ($premium as $r):
                $color = cdg_refs_logo_color($r['ad'], $logo_colors);
                $initials = cdg_refs_initials($r['ad']);
                $sector = htmlspecialchars($r['sektor'], ENT_QUOTES);
            ?>
            <div class="cdg-refs-premium-card cdg-refs-item" data-sector="<?php echo $sector; ?>">
                <span class="cdg-refs-premium-badge">Premium</span>
                <div class="cdg-refs-premium-head">
                    <div class="cdg-refs-premium-logo" style="background:linear-gradient(135deg,<?php echo $color[0]; ?>,<?php echo $color[1]; ?>);">
                        <?php echo $initials; ?>
                    </div>
                    <div class="cdg-refs-premium-info">
                        <h3 class="cdg-refs-premium-name"><?php echo htmlspecialchars($r['ad']); ?></h3>
                        <span class="cdg-refs-premium-sector"><?php echo $sector; ?></span>
                    </div>
                </div>
                <div class="cdg-refs-premium-service">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?php echo htmlspecialchars($r['hizmet']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- STANDART MÜŞTERİLER (Hosting) -->
<?php if (count($standard) > 0): ?>
<section class="cdg-refs-standard">
    <div class="cdg-container">
        <div class="cdg-refs-section-head">
            <span class="cdg-refs-section-eyebrow">🌐 Hosting & Domain</span>
            <h2>Bize güvenen <?php echo count($standard); ?>+ marka</h2>
            <p>Web sitelerini Codega altyapısı üzerinde yayınlayan kurumlar ve markalar.</p>
        </div>
        <div class="cdg-refs-standard-grid" id="cdgRefsStandardGrid">
            <?php foreach ($standard as $r):
                $color = cdg_refs_logo_color($r['ad'], $logo_colors);
                $initials = cdg_refs_initials($r['ad']);
                $sector = htmlspecialchars($r['sektor'], ENT_QUOTES);
            ?>
            <div class="cdg-refs-card cdg-refs-item" data-sector="<?php echo $sector; ?>" title="<?php echo htmlspecialchars($r['hizmet'], ENT_QUOTES); ?>">
                <div class="cdg-refs-logo" style="background:linear-gradient(135deg,<?php echo $color[0]; ?>,<?php echo $color[1]; ?>);">
                    <?php echo $initials; ?>
                </div>
                <div class="cdg-refs-info">
                    <h4 class="cdg-refs-name"><?php echo htmlspecialchars($r['ad']); ?></h4>
                    <div class="cdg-refs-sector"><?php echo $sector; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="cdg-refs-empty" id="cdgRefsEmpty">
            <i class="bi bi-search"></i>
            <p>Bu sektörde henüz referans bulunmuyor.</p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cdg-refs-cta">
    <div class="cdg-container">
        <div class="cdg-refs-cta-card">
            <h3>Sıradaki marka <span style="color:#00E5FF;">siz</span> olun</h3>
            <p><?php echo count($refs); ?>+ markanın güvendiği altyapıyla işinizi büyütün. Domain, hosting ve özel yazılım için ücretsiz danışmanlık.</p>
            <div class="cdg-refs-cta-actions">
                <?php
                    $contact_url = class_exists('Controllers') ? Controllers::$init->CRLink('contact') : '/contact';
                    $hosting_url = class_exists('Controllers') ? Controllers::$init->CRLink('hosting-products') : '/hosting-products';
                ?>
                <a href="<?php echo $hosting_url; ?>" class="cdg-refs-cta-btn cdg-refs-cta-btn-primary">
                    <i class="bi bi-rocket-takeoff-fill"></i> Paketleri İncele
                </a>
                <a href="<?php echo $contact_url; ?>" class="cdg-refs-cta-btn cdg-refs-cta-btn-outline">
                    <i class="bi bi-envelope-paper-fill"></i> Bize Ulaşın
                </a>
            </div>
        </div>
    </div>
</section>

