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
<!DOCTYPE html>
<html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php
        $hoptions = [ 'page' => "references" ];
        $meta = [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'keywords' => 'codega referansları, müşteri, hosting müşterileri, erp, kurumsal yazılım',
            'robots' => 'index,follow',
        ];
        include __DIR__.DS."inc".DS."main-head.php";
    ?>
    <style>
    /* CODEGA - Referanslar sayfası (codega.com.tr migration) */
    .cdg-refs-hero {
        position: relative;
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-refs-hero::before {
        content: '';
        position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
        pointer-events: none;
    }
    .cdg-refs-hero::after {
        content: '';
        position: absolute;
        bottom: -120px; left: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,211,229,0.18) 0%, transparent 70%);
        filter: blur(80px);
        pointer-events: none;
    }
    .cdg-refs-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
    }
    .cdg-refs-hero-content { position: relative; z-index: 1; text-align: center; max-width: 760px; margin: 0 auto; }
    .cdg-refs-eyebrow {
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
    .cdg-refs-eyebrow .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #00E5FF;
        box-shadow: 0 0 8px #00E5FF;
        animation: cdgRefsPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgRefsPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .cdg-refs-hero h1 {
        font-size: clamp(32px, 4.5vw, 48px);
        font-weight: 800;
        margin: 0 0 16px;
        letter-spacing: -0.02em;
        line-height: 1.15;
        color: #fff;
    }
    .cdg-refs-hero h1 span {
        background: linear-gradient(135deg, #00D3E5 0%, #00E5FF 50%, #67E8F9 100%);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .cdg-refs-hero p {
        font-size: 17px;
        color: rgba(255,255,255,0.78);
        line-height: 1.65;
        margin: 0 0 36px;
    }
    .cdg-refs-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        max-width: 720px;
        margin: 0 auto;
    }
    .cdg-refs-stat {
        padding: 18px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        backdrop-filter: blur(8px);
    }
    .cdg-refs-stat-num {
        font-size: 30px;
        font-weight: 800;
        color: #00E5FF;
        line-height: 1;
        margin-bottom: 5px;
    }
    .cdg-refs-stat-lbl {
        font-size: 12px;
        color: rgba(255,255,255,0.65);
        font-weight: 500;
    }
    @media (max-width: 640px) {
        .cdg-refs-stats { grid-template-columns: repeat(2, 1fr); }
        .cdg-refs-stat { padding: 14px 10px; }
        .cdg-refs-stat-num { font-size: 24px; }
    }

    /* Filtre chip'leri */
    .cdg-refs-filter {
        padding: 24px 0 12px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }
    .cdg-refs-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }
    .cdg-refs-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: inherit;
    }
    .cdg-refs-chip:hover {
        border-color: #00D3E5;
        color: #2E3B4E;
        background: #f0fdff;
    }
    .cdg-refs-chip.active {
        background: linear-gradient(135deg, #2E3B4E, #1e293b);
        border-color: #2E3B4E;
        color: #fff;
        box-shadow: 0 6px 16px rgba(46,59,78,0.25);
    }
    .cdg-refs-chip .count {
        display: inline-block;
        padding: 1px 7px;
        background: rgba(0,0,0,0.08);
        color: inherit;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
    }
    .cdg-refs-chip.active .count {
        background: rgba(0,229,255,0.20);
        color: #00E5FF;
    }

    /* Premium müşteri kartı */
    .cdg-refs-premium {
        padding: 60px 0 40px;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .cdg-refs-section-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 40px;
    }
    .cdg-refs-section-eyebrow {
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
    .cdg-refs-section-head h2 {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -0.02em;
    }
    .cdg-refs-section-head p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    .cdg-refs-premium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 18px;
    }
    .cdg-refs-premium-card {
        position: relative;
        padding: 28px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }
    .cdg-refs-premium-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #2E3B4E 0%, #00D3E5 50%, #00E5FF 100%);
    }
    .cdg-refs-premium-card:hover {
        transform: translateY(-4px);
        border-color: #00D3E5;
        box-shadow: 0 20px 50px rgba(46,59,78,0.10);
    }
    .cdg-refs-premium-head {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .cdg-refs-premium-logo {
        width: 60px; height: 60px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.02em;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .cdg-refs-premium-info { min-width: 0; flex: 1; }
    .cdg-refs-premium-name {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.3;
    }
    .cdg-refs-premium-sector {
        font-size: 12px;
        color: #00D3E5;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .cdg-refs-premium-service {
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .cdg-refs-premium-service i {
        color: #00D3E5;
        margin-top: 2px;
        flex-shrink: 0;
    }
    .cdg-refs-premium-badge {
        position: absolute;
        top: 16px; right: 16px;
        padding: 4px 10px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-radius: 100px;
        box-shadow: 0 4px 10px rgba(251,191,36,0.35);
    }

    /* Standart müşteri grid */
    .cdg-refs-standard {
        padding: 40px 0 80px;
        background: #fff;
    }
    .cdg-refs-standard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }
    .cdg-refs-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .cdg-refs-card:hover {
        border-color: #00D3E5;
        background: #f0fdff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,211,229,0.12);
    }
    .cdg-refs-logo {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .cdg-refs-info { min-width: 0; flex: 1; }
    .cdg-refs-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cdg-refs-sector {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Boş sonuç */
    .cdg-refs-empty {
        display: none;
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    .cdg-refs-empty i { font-size: 48px; color: #cbd5e1; margin-bottom: 12px; display: block; }

    /* CTA */
    .cdg-refs-cta {
        padding: 60px 0;
        background: #f8fafc;
        text-align: center;
    }
    .cdg-refs-cta-card {
        max-width: 720px;
        margin: 0 auto;
        padding: 44px 32px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #fff;
    }
    .cdg-refs-cta-card::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        pointer-events: none;
    }
    .cdg-refs-cta-card h3 {
        position: relative;
        font-size: 28px;
        font-weight: 800;
        margin: 0 0 10px;
        letter-spacing: -0.01em;
        color: #fff;
    }
    .cdg-refs-cta-card p {
        position: relative;
        font-size: 15px;
        color: rgba(255,255,255,0.80);
        margin: 0 0 24px;
        line-height: 1.6;
    }
    .cdg-refs-cta-actions {
        position: relative;
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .cdg-refs-cta-btn {
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
    .cdg-refs-cta-btn-primary {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        color: #0f172a !important;
        box-shadow: 0 8px 22px rgba(0,229,255,0.30);
    }
    .cdg-refs-cta-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,229,255,0.45);
    }
    .cdg-refs-cta-btn-outline {
        background: rgba(255,255,255,0.10);
        color: #fff !important;
        border: 2px solid rgba(255,255,255,0.20);
    }
    .cdg-refs-cta-btn-outline:hover {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.40);
    }

    @media (max-width: 768px) {
        .cdg-refs-hero { padding: 50px 0 40px; }
        .cdg-refs-section-head h2 { font-size: 24px; }
        .cdg-refs-premium-card { padding: 22px; }
        .cdg-refs-standard-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
        .cdg-refs-cta-card { padding: 32px 22px; }
        .cdg-refs-cta-card h3 { font-size: 22px; }
    }
    </style>
</head>
<body>

<?php include __DIR__.DS."inc".DS."lang-currency-modal.php"; ?>
<?php
    $header_type = isset($theme_settings['header_type']) ? $theme_settings['header_type'] : 1;
    $hf = __DIR__.DS."inc".DS."main-header-".$header_type.".php";
    if(file_exists($hf)) include $hf;
?>

<!-- HERO -->
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

<?php
    $footer_file = __DIR__.DS."inc".DS."main-footer.php";
    if(file_exists($footer_file)) include $footer_file;
?>

<script>
(function(){
    'use strict';
    var chips = document.querySelectorAll('#cdgRefsChips .cdg-refs-chip');
    var items = document.querySelectorAll('.cdg-refs-item');
    var emptyEl = document.getElementById('cdgRefsEmpty');

    chips.forEach(function(chip){
        chip.addEventListener('click', function(){
            chips.forEach(function(c){ c.classList.remove('active'); });
            this.classList.add('active');
            var sector = this.getAttribute('data-sector');
            var visible = 0;
            items.forEach(function(it){
                if (sector === 'all' || it.getAttribute('data-sector') === sector) {
                    it.style.display = '';
                    visible++;
                } else {
                    it.style.display = 'none';
                }
            });
            if (emptyEl) emptyEl.style.display = visible === 0 ? 'block' : 'none';
            // Smooth scroll to filter
            var filterEl = document.querySelector('.cdg-refs-filter');
            if (filterEl) window.scrollTo({ top: filterEl.offsetTop - 80, behavior: 'smooth' });
        });
    });
})();
</script>

</body>
</html>
