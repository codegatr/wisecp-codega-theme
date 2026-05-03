<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Diagnostic mode
if(isset($_GET['_diag']) && $_GET['_diag'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== DOMAIN.PHP DIAGNOSTIC ===\n\n";
    echo "tldList exists: " . (isset($tldList) ? 'YES' : 'NO') . "\n";
    if(isset($tldList) && is_array($tldList)) {
        echo "tldList count: " . count($tldList) . "\n";
        foreach(array_slice($tldList, 0, 5) as $t) {
            echo "  - " . ($t['name'] ?? '?') . " (reg: " . ($t['reg_price']['amount'] ?? '?') . ")\n";
        }
    }
    echo "\nbox_tldList exists: " . (isset($box_tldList) ? 'YES' : 'NO') . "\n";
    if(isset($box_tldList) && is_array($box_tldList)) {
        echo "box_tldList count: " . count($box_tldList) . "\n";
    }
    echo "\nlinks: " . (isset($links) ? 'SET' : 'NOT SET') . "\n";
    if(isset($links) && is_array($links)) {
        foreach($links as $k => $v) echo "  - $k: $v\n";
    }
    echo "\ncaptcha: " . (isset($captcha) && $captcha ? 'YES (' . get_class($captcha) . ')' : 'NO') . "\n";
    echo "Validation class: " . (class_exists('Validation') ? 'YES' : 'NO') . "\n";
    echo "Money class: " . (class_exists('Money') ? 'YES' : 'NO') . "\n";
    exit;
}

$hoptions = [
    'page' => 'domain',
    'jquery-ui',
];

if(class_exists('Config') && method_exists('Config', 'get') && Config::get("theme/only-panel")) {
    $meta["robots"] = "NOINDEX,NOFOLLOW";
}

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        // Bu yuzden once alias+CRLink, $links sadece bilinmeyen slug'lar icin son fallback
        global $links;

        // CDG_LINK_HARDCODED - Yunus'un sitesinde KESIN dogru URL'ler (CRLink bypass)
        static $hardcoded = [
            'ac-ps-create-ticket-request' => '/hesabim/destek-talebi-olustur',
            'create-ticket-request'       => '/hesabim/destek-talebi-olustur',
            'create-ticket'               => '/hesabim/destek-talebi-olustur',
        ];
        if(isset($hardcoded[$slug])) {
            $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
            return $base . $hardcoded[$slug];
        }


        // 2) Kısa-isim -> WiseCP gerçek route alias map
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
            'ac-ps-create-ticket-request' => 'ac-ps-create-ticket-request',
            'tickets'                 => 'ac-ps-tickets',
            'my-tickets'              => 'ac-ps-tickets',
            'messages'                => 'ac-ps-messages',
            'detail-message'          => 'ac-ps-detail-message',
            'invoices'                => 'ac-ps-invoices',
            'detail-invoice'          => 'ac-ps-detail-invoice',
            'detail-invoice-pdf'      => 'ac-ps-detail-invoice',
            'balance'                 => 'ac-ps-balance',
            'balance-page'            => 'ac-ps-balance',
            'info'                    => 'ac-ps-info',
            'ac-info'                 => 'ac-ps-info',
            'products'                => 'ac-ps-products',
            'all-orders'              => 'ac-ps-products',
            'products-t'              => 'ac-ps-products-t',
            'product'                 => 'ac-ps-product',
            'sms'                     => 'ac-ps-sms',
            'affiliate'               => 'ac-affiliate',
            'ac-affiliate'            => 'ac-affiliate',
            'reseller'                => 'ac-reseller',
            'domains'                 => 'ac-products-domain',
            'products-domain'         => 'ac-products-domain',
            'whois-profiles'          => 'ac-products-domain-whois-profiles',
            'products-domain-whois-profiles' => 'ac-products-domain-whois-profiles',
            'create-whois-profile'    => 'ac-products-domain-create-whois-profile',
            'products-domain-create-whois-profile' => 'ac-products-domain-create-whois-profile',
            'login'                   => 'sign-in',
            'register'                => 'sign-up',
            'logout'                  => 'sign-out',
            'account'                 => 'my-account',
            'homepage'                => '',
            'home'                    => '',
        ];
        $real_slug = isset($aliases[$slug]) ? $aliases[$slug] : $slug;

        // 3) CRLink dene (gerçek WiseCP routing)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                // Bozuk URL kontrolü (boş ID parametresi vb.)
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) { /* fallback'e düş */ }
        }

        // 4) Son çare: APP_URI base + slug
        // Son care: $links bakilirsa kullan (sadece bilinmeyen slug'lar icin)
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$contact_url = cdg_link('contact');
$hosting_url = cdg_link('products', ['hosting']);

$currency_symbols = [];
if(class_exists('Money') && method_exists('Money', 'getCurrencies')) {
    foreach(Money::getCurrencies() AS $currency){
        $symbol = !empty($currency["prefix"]) ? trim($currency["prefix"]) : trim($currency["suffix"] ?? '');
        if(!$symbol) $symbol = $currency["code"] ?? '₺';
        $currency_symbols[] = $symbol;
    }
}

// EPP code support listesi
$epp_code_support = [];
if(isset($tldList) && $tldList) {
    foreach($tldList AS $t) {
        $epp_code_support[$t["name"]] = ($t["epp_code"] ?? 0) == 1;
    }
}
$tlds = $epp_code_support ? array_keys($epp_code_support) : [];
$override_usrcurrency = $override_usrcurrency ?? false;

// TLD count
$tld_count = isset($tldList) && is_array($tldList) ? count($tldList) : 0;

// Featured TLD'ler
$featured_tlds = isset($box_tldList) && is_array($box_tldList) ? $box_tldList : [];
?>

<style>
/* CODEGA - DOMAIN SORGULAMA HERO - Modern premium tasarim */
.cdg-domain-hero {
    position: relative;
    background: linear-gradient(135deg, #1A2332 0%, #1A2332 35%, #2E3B4E 70%, #485A75 100%);
    padding: 90px 0 110px;
    overflow: hidden;
    color: #fff;
}
.cdg-domain-hero-bg {
    position: absolute; inset: 0;
    overflow: hidden;
    pointer-events: none;
}
.cdg-mesh-gradient {
    position: absolute;
    top: -50%; left: -10%;
    width: 70%; height: 200%;
    background: radial-gradient(circle, rgba(96,165,250,0.35) 0%, transparent 60%);
    filter: blur(60px);
    animation: cdgFloat1 14s ease-in-out infinite;
}
.cdg-mesh-gradient::after {
    content: '';
    position: absolute;
    top: 30%; right: -50%;
    width: 60%; height: 80%;
    background: radial-gradient(circle, rgba(56,189,248,0.30) 0%, transparent 60%);
    filter: blur(80px);
    animation: cdgFloat2 18s ease-in-out infinite;
}
@keyframes cdgFloat1 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(8%, 6%); } }
@keyframes cdgFloat2 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(-8%, -6%); } }
.cdg-domain-hero-particles {
    position: absolute; inset: 0;
}
.cdg-domain-hero-particles span {
    position: absolute;
    display: block;
    width: 6px; height: 6px;
    background: rgba(255,255,255,0.50);
    border-radius: 50%;
    box-shadow: 0 0 12px rgba(96,165,250,0.80);
    animation: cdgParticle 12s linear infinite;
}
.cdg-domain-hero-particles span:nth-child(1) { left: 8%; top: 20%; animation-delay: 0s; }
.cdg-domain-hero-particles span:nth-child(2) { left: 18%; top: 70%; animation-delay: -2s; }
.cdg-domain-hero-particles span:nth-child(3) { left: 35%; top: 30%; animation-delay: -4s; width: 4px; height: 4px; }
.cdg-domain-hero-particles span:nth-child(4) { left: 55%; top: 80%; animation-delay: -6s; }
.cdg-domain-hero-particles span:nth-child(5) { left: 70%; top: 25%; animation-delay: -8s; width: 8px; height: 8px; }
.cdg-domain-hero-particles span:nth-child(6) { left: 85%; top: 65%; animation-delay: -10s; }
.cdg-domain-hero-particles span:nth-child(7) { left: 92%; top: 35%; animation-delay: -3s; width: 5px; height: 5px; }
@keyframes cdgParticle {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.5; }
    50% { transform: translateY(-30px) scale(1.4); opacity: 1; }
}

.cdg-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
}
.cdg-domain-hero-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}
.cdg-domain-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 22px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #422006;
    border-radius: 100px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 24px;
    box-shadow: 0 8px 24px rgba(251,191,36,0.40), 0 0 0 1px rgba(255,255,255,0.20) inset;
    animation: cdgBadgeFloat 3s ease-in-out infinite;
}
@keyframes cdgBadgeFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
.cdg-domain-hero h1 {
    font-size: clamp(34px, 5vw, 52px);
    font-weight: 900;
    line-height: 1.15;
    margin: 0 0 18px;
    color: #fff;
    text-shadow: 0 2px 20px rgba(0,0,0,0.20);
    letter-spacing: -0.02em;
}
.cdg-text-gradient-light {
    background: linear-gradient(135deg, #fbbf24 0%, #fde047 50%, #00D3E5 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
}
.cdg-domain-lead {
    font-size: 17px;
    line-height: 1.7;
    color: rgba(255,255,255,0.85);
    margin: 0 auto 36px;
    max-width: 620px;
}

/* DOMAIN INPUT WRAP */
.cdg-domain-hero .cdg-domain-search-wrap {
    margin: 0 auto;
    max-width: 760px;
    padding: 0;
}
.cdg-domain-hero .cdg-domain-search-input {
    display: flex !important;
    align-items: stretch !important;
    background: #fff !important;
    border-radius: 14px !important;
    box-shadow: 0 24px 60px rgba(0,0,0,0.30), 0 0 0 1px rgba(255,255,255,0.10) inset !important;
    overflow: hidden !important;
    transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.cdg-domain-hero .cdg-domain-search-input:hover {
    transform: translateY(-2px);
    box-shadow: 0 32px 80px rgba(0,0,0,0.40) !important;
}
.cdg-domain-hero .cdg-domain-search-input-main {
    flex: 1 1 auto !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 18px !important;
    gap: 12px !important;
    min-width: 0 !important;
    position: relative !important;
}
.cdg-domain-hero .cdg-domain-search-input-main > i,
.cdg-domain-hero .cdg-domain-search-input-main > .bi {
    color: #2E3B4E !important;
    font-size: 22px !important;
    flex-shrink: 0 !important;
    position: static !important;
}
.cdg-domain-hero .cdg-domain-search-input-main input[type=text],
.cdg-domain-hero .cdg-domain-search-input-main input {
    flex: 1 1 auto !important;
    border: 0 !important;
    outline: none !important;
    padding: 18px 4px !important;
    font-size: 17px !important;
    color: #0f172a !important;
    background: transparent !important;
    font-weight: 600 !important;
    width: 100% !important;
    min-width: 0 !important;
    box-shadow: none !important;
    height: auto !important;
    line-height: 1.4 !important;
    margin: 0 !important;
}
.cdg-domain-hero .cdg-domain-search-input-main input::placeholder {
    color: #94a3b8 !important;
    font-weight: 500 !important;
    opacity: 1 !important;
}
.cdg-domain-hero .cdg-domain-search-captcha {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 0 14px !important;
    border-left: 1px solid #e2e8f0 !important;
    flex-shrink: 0 !important;
}
.cdg-domain-hero .cdg-domain-captcha-img {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 4px;
    height: 38px;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}
.cdg-domain-hero .cdg-domain-captcha-img img {
    height: 30px !important;
    width: auto !important;
    border-radius: 3px;
    display: block;
}
.cdg-domain-hero .cdg-domain-search-captcha input[type=text] {
    width: 110px !important;
    padding: 11px 12px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    font-size: 13px !important;
    outline: none !important;
    transition: border-color 0.2s !important;
    background: #fff !important;
    color: #0f172a !important;
    flex-shrink: 0 !important;
}
.cdg-domain-hero .cdg-domain-search-captcha input:focus {
    border-color: #2E3B4E !important;
    box-shadow: 0 0 0 3px rgba(30,64,175,0.15) !important;
}
.cdg-domain-hero .cdg-btn-glow {
    flex-shrink: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 0 32px !important;
    background: linear-gradient(135deg, #485A75, #2E3B4E) !important;
    color: #fff !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    text-decoration: none !important;
    transition: all 0.3s !important;
    border: 0 !important;
    cursor: pointer !important;
    position: relative !important;
    overflow: hidden !important;
    min-height: 56px;
    white-space: nowrap;
}
.cdg-domain-hero .cdg-btn-glow::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #2E3B4E, #1A2332);
    opacity: 0;
    transition: opacity 0.3s;
}
.cdg-domain-hero .cdg-btn-glow:hover::before { opacity: 1; }
.cdg-domain-hero .cdg-btn-glow > * { position: relative; z-index: 1; }
.cdg-domain-hero .cdg-btn-glow i { font-size: 18px; }

.cdg-domain-transfer-toggle {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 18px;
    color: rgba(255,255,255,0.90);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}
.cdg-domain-transfer-toggle input[type=checkbox] {
    width: 18px; height: 18px;
    cursor: pointer;
    accent-color: #fbbf24;
}
.cdg-domain-epp {
    width: 100%;
    padding: 14px 18px;
    border: 0;
    border-radius: 10px;
    background: rgba(255,255,255,0.12);
    color: #fff;
    font-size: 14px;
    outline: none;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.20);
}
.cdg-domain-epp::placeholder { color: rgba(255,255,255,0.60); }
.cdg-domain-epp:focus {
    background: rgba(255,255,255,0.20);
    border-color: rgba(251,191,36,0.50);
}

/* FEATURED TLDs (öne çıkan uzantılar bar) */
.cdg-domain-featured {
    margin-top: 32px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    justify-content: center;
}
.cdg-domain-featured-label {
    color: rgba(255,255,255,0.70);
    font-size: 13px;
    font-weight: 600;
}
.cdg-domain-featured-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.20);
    border-radius: 100px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s;
}
.cdg-domain-featured-pill:hover {
    background: rgba(255,255,255,0.22);
    border-color: rgba(251,191,36,0.50);
    transform: translateY(-2px);
}
.cdg-domain-featured-pill .tld-name { color: #fde047; }
.cdg-domain-featured-pill .tld-price { color: #fbbf24; font-weight: 800; }

/* Mobile responsive */
@media (max-width: 768px) {
    .cdg-domain-hero { padding: 60px 0 80px; }
    .cdg-domain-hero .cdg-domain-search-input { flex-direction: column !important; }
    .cdg-domain-hero .cdg-domain-search-input-main { padding: 14px 18px !important; width: 100% !important; }
    .cdg-domain-hero .cdg-domain-search-captcha {
        padding: 12px 18px !important;
        border-left: 0 !important;
        border-top: 1px solid #e2e8f0 !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
    }
    .cdg-domain-hero .cdg-btn-glow {
        padding: 16px !important;
        width: 100% !important;
        justify-content: center !important;
        min-height: 52px;
    }
}
</style>

<!-- HERO + DOMAIN SORGU -->
<section class="cdg-domain-hero">
    <div class="cdg-domain-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-domain-hero-particles">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span>
        </div>
    </div>
    <div class="cdg-container">
        <div class="cdg-domain-hero-content">
            <div class="cdg-domain-badge"><i class="bi bi-globe2"></i> Domain Sorgulama</div>
            <h1>Hayalinizdeki <span class="cdg-text-gradient-light">alan adını</span> kaydedin</h1>
            <p class="cdg-domain-lead"><?php echo $tld_count > 0 ? $tld_count . '+' : '500+'; ?> uzantı desteği ile her ihtiyaca uygun domain. Fiyatlar panelden anlık güncellenir.</p>

            <!-- DOMAIN SORGULAMA INPUT -->
            <div class="cdg-domain-search-wrap">
                <div class="cdg-domain-search-input<?php echo (isset($captcha) && $captcha) ? ' cdg-domain-search-with-captcha' : ''; ?>">
                    <div class="cdg-domain-search-input-main">
                        <i class="bi bi-search"></i>
                        <input type="text" id="domainInput" value="<?php echo isset($_GET['domain']) ? htmlspecialchars(trim($_GET['domain']), ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" placeholder="alanadi.com" autocomplete="off" required onkeydown="if(event.keyCode==13){event.preventDefault();submitnow(document.getElementById('submitnow'));}">
                    </div>
                    <?php if(isset($captcha) && $captcha): ?>
                    <div class="cdg-domain-search-captcha">
                        <div class="cdg-domain-captcha-img"><?php echo $captcha->getOutput(); ?></div>
                        <?php if($captcha->input): ?>
                        <input id="captchainputs" name="<?php echo $captcha->input_name; ?>" type="text" placeholder="Güvenlik Kodu" autocomplete="off" maxlength="10">
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <a href="javascript:void(0);" id="submitnow" class="cdg-btn cdg-btn-primary cdg-btn-glow" onclick="submitnow(this);">
                        <i class="bi bi-search"></i> <span>Sorgula</span>
                    </a>
                </div>
                <label class="cdg-domain-transfer-toggle">
                    <input type="checkbox" id="transferCheckbox">
                    <span>Mevcut domainimi transfer etmek istiyorum</span>
                </label>
                <div class="transfercode" id="transfercode" style="display:none;margin-top:10px;">
                    <input type="text" placeholder="EPP / Auth Kodu" id="eppCode" class="cdg-domain-epp">
                </div>
            </div>

            <!-- WiseCP'NIN GERÇEK FORM YAPISI (Netwise'dan birebir) -->
            <form id="checkForm" action="<?php echo isset($links['controller']) ? $links['controller'] : ''; ?>" method="post" onsubmit="return false;">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('domain-check'); ?>
                <input type="hidden" name="operation" value="check">
                <input type="hidden" name="type" value="domain">
                <input type="hidden" name="domain" value="">
                <input type="hidden" name="tcode" value="">
                <?php /* Captcha search-input içine taşındı - WiseCP form'una JS ile aktarılacak */ ?>
            </form>

            <?php if(!empty($featured_tlds)): ?>
            <div class="cdg-domain-featured">
                <span class="cdg-domain-featured-label">Öne çıkan:</span>
                <?php foreach(array_slice($featured_tlds, 0, 5) as $tld):
                    $price_amount = '';
                    if(isset($tld['reg_price']['amount']) && isset($tld['reg_price']['cid'])) {
                        $price_amount = Money::formatter_symbol($tld['reg_price']['amount'], $tld['reg_price']['cid'], !$override_usrcurrency);
                    }
                ?>
                <span class="cdg-domain-featured-pill">
                    <span class="tld-name">.<?php echo htmlspecialchars($tld['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <span class="tld-price"><?php echo $price_amount; ?></span>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SORGU SONUC ALANI -->
<section class="cdg-section" id="search-results" style="display:none;">
    <div class="cdg-container">
        <div class="cdg-search-results-head">
            <h2 id="searchTitle"><span class="cdg-text-gradient">Sorgu Sonuçları</span></h2>
            <p>Aşağıdaki uzantıları siparişe ekleyebilirsiniz.</p>
        </div>

        <!-- Loading state -->
        <div id="searchLoading" class="cdg-search-result loading" style="display:none;">
            <div class="cdg-search-result-name">
                <i class="bi bi-hourglass-split" style="animation:cdgSpin 1s linear infinite;color:#2E3B4E;"></i>
                <strong>Sorgulanıyor...</strong>
            </div>
        </div>

        <!-- Sonuçlar buraya eklenecek -->
        <div id="searchResults" class="cdg-search-results"></div>

        <!-- Hata göstergesi -->
        <div id="searchError" style="display:none;"></div>
    </div>
</section>

<!-- ÖNE ÇIKAN TLD'LER -->
<?php if(!empty($featured_tlds)): ?>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Öne Çıkan</div>
            <h2><span class="cdg-text-gradient">Popüler uzantılarımız</span></h2>
            <p>Admin panelinde belirlenen öne çıkan uzantılar.</p>
        </div>
        <div class="cdg-tld-grid-full">
            <?php foreach($featured_tlds as $tld):
                $reg = '';
                $tra = '';
                $ren = '';
                $promo_reg = '';

                if(isset($tld['reg_price']['amount'])) {
                    $reg = Money::formatter_symbol($tld['reg_price']['amount'], $tld['reg_price']['cid'], !$override_usrcurrency);
                }
                if(isset($tld['tra_price']['amount'])) {
                    $tra = Money::formatter_symbol($tld['tra_price']['amount'], $tld['tra_price']['cid'], !$override_usrcurrency);
                }
                if(isset($tld['ren_price']['amount'])) {
                    $ren = Money::formatter_symbol($tld['ren_price']['amount'], $tld['ren_price']['cid'], !$override_usrcurrency);
                }

                $is_promo = !empty($tld['promo_status']) && (
                    substr($tld['promo_duedate'] ?? '', 0, 4) == '1881' ||
                    (class_exists('DateManager') && DateManager::strtotime(($tld['promo_duedate'] ?? '') . " 23:59:59") > DateManager::strtotime())
                );

                if($is_promo && !empty($tld['promo_register_price']) && $tld['promo_register_price'] > 0) {
                    $promo_reg = Money::formatter_symbol($tld['promo_register_price'], $tld['currency'] ?? 'TRY', !$override_usrcurrency);
                }
            ?>
            <div class="cdg-tld-card-pro<?php echo $is_promo ? ' promo' : ''; ?>">
                <?php if($is_promo): ?><div class="cdg-tld-promo-badge"><i class="bi bi-tag-fill"></i> Kampanya</div><?php endif; ?>
                <div class="cdg-tld-card-ext">.<?php echo htmlspecialchars($tld['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <?php if(!empty($tld['paperwork'])): ?>
                <div class="cdg-tld-paperwork" title="Bu uzantı için belge gereklidir">
                    <i class="bi bi-file-earmark-text"></i> Belge gerekli
                </div>
                <?php endif; ?>
                <div class="cdg-tld-card-prices">
                    <div class="cdg-tld-card-price-row">
                        <span class="lbl">Tescil</span>
                        <span class="val">
                            <?php if($promo_reg): ?>
                            <span style="text-decoration:line-through;color:#94a3b8;font-size:12px;"><?php echo $reg; ?></span>
                            <strong style="color:#10b981;"><?php echo $promo_reg; ?></strong>
                            <?php else: ?>
                            <?php echo $reg ?: '-'; ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if($tra): ?>
                    <div class="cdg-tld-card-price-row">
                        <span class="lbl">Transfer</span>
                        <span class="val"><?php echo $tra; ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($ren): ?>
                    <div class="cdg-tld-card-price-row">
                        <span class="lbl">Yenileme</span>
                        <span class="val"><?php echo $ren; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- TÜM UZANTILAR TABLOSU -->
<?php if(!empty($tldList) && is_array($tldList)): ?>
<section class="cdg-section" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Tam Liste</div>
            <h2>Sistemimizde <span class="cdg-text-gradient"><?php echo $tld_count; ?> uzantı</span> mevcut</h2>
            <p>Fiyatlar canlı olarak panelden çekilmektedir.</p>
        </div>

        <div class="cdg-tld-full-table">
            <div class="cdg-table-wrap">
                <table class="cdg-tld-table" id="tldTable">
                    <thead>
                        <tr>
                            <th>Uzantı</th>
                            <th>Tescil</th>
                            <th>Yenileme</th>
                            <th>Transfer</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tldList as $row):
                            $reg = isset($row['reg_price']['amount']) ? Money::formatter_symbol($row['reg_price']['amount'], $row['reg_price']['cid'], !$override_usrcurrency) : '-';
                            $tra = isset($row['tra_price']['amount']) ? Money::formatter_symbol($row['tra_price']['amount'], $row['tra_price']['cid'], !$override_usrcurrency) : '-';
                            $ren = isset($row['ren_price']['amount']) ? Money::formatter_symbol($row['ren_price']['amount'], $row['ren_price']['cid'], !$override_usrcurrency) : '-';

                            $is_promo = !empty($row['promo_status']) && (
                                substr($row['promo_duedate'] ?? '', 0, 4) == '1881' ||
                                (class_exists('DateManager') && DateManager::strtotime(($row['promo_duedate'] ?? '') . " 23:59:59") > DateManager::strtotime())
                            );

                            $promo_reg = '';
                            if($is_promo) {
                                if(!empty($row['promo_register_price']) && $row['promo_register_price'] > 0) {
                                    $promo_reg = Money::formatter_symbol($row['promo_register_price'], $row['currency'] ?? 'TRY', !$override_usrcurrency);
                                }
                            }
                        ?>
                        <tr<?php echo $is_promo ? ' style="background:#fef9c3;"' : ''; ?>>
                            <td>
                                <strong style="color:<?php echo $is_promo ? '#10b981' : '#2E3B4E'; ?>;font-size:15px;">.<?php echo htmlspecialchars($row['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
                                <?php if(!empty($row['paperwork'])): ?>
                                <i class="bi bi-file-earmark-text" style="color:#94a3b8;margin-left:6px;font-size:12px;" title="Belge gerekli"></i>
                                <?php endif; ?>
                                <?php if($is_promo): ?>
                                <span style="display:inline-block;background:#10b981;color:#fff;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:700;margin-left:6px;">KAMPANYA</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($promo_reg): ?>
                                <span style="text-decoration:line-through;color:#94a3b8;font-size:12px;"><?php echo $reg; ?></span>
                                <strong style="color:#10b981;"><?php echo $promo_reg; ?></strong>
                                <?php else: ?>
                                <?php echo $reg; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $ren; ?></td>
                            <td><?php echo $tra; ?></td>
                            <td>
                                <button type="button" onclick="document.getElementById('domainInput').value='alanadi.<?php echo htmlspecialchars($row['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';document.getElementById('domainInput').focus();window.scrollTo({top:0,behavior:'smooth'});" class="cdg-btn cdg-btn-outline cdg-btn-sm">
                                    <i class="bi bi-search"></i> Sorgula
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- AVANTAJLAR -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Domain Avantajları</div>
            <h2>Profesyonel <span class="cdg-text-gradient">domain hizmeti</span></h2>
        </div>
        <div class="cdg-adv-grid cdg-adv-grid-4">
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-lightning-charge-fill"></i></div><h3>Anında Aktivasyon</h3><p>Ödeme sonrası dakikalar içinde domain aktif.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-shield-fill-check"></i></div><h3>Whois Gizliliği</h3><p>Kişisel bilgileriniz herkese açık değil.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-arrow-clockwise"></i></div><h3>Otomatik Yenileme</h3><p>Domain'iniz süresi dolmadan otomatik yenilenir.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div><h3>Ücretsiz Transfer</h3><p>gTLD'lerde transfer ücretsiz, +1 yıl bonus.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-gear-fill"></i></div><h3>DNS Yönetimi</h3><p>A, AAAA, MX, TXT, CNAME — hepsi panelden.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-envelope-fill"></i></div><h3>E-posta Yönlendirme</h3><p>info@alanadiniz.com → kişisel e-postanız.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-headset"></i></div><h3>7/24 Destek</h3><p>WhatsApp, telefon, panel — daima hazır.</p></div>
            <div class="cdg-adv-card"><div class="cdg-adv-icon"><i class="bi bi-piggy-bank-fill"></i></div><h3>Şeffaf Fiyat</h3><p>Gizli ücret yok, yenileme fiyatı net.</p></div>
        </div>
    </div>
</section>

<!-- DOMAIN SORGULAMA SCRIPTI -->
<script type="text/javascript">
var situations = [], loading_template = '';
situations['unknown'] = '<?php echo function_exists("__") ? __("website/domain/situations-unknown") : "Belirsiz"; ?>';
situations['premium'] = '<?php echo function_exists("__") ? __("website/domain/situations-premium") : "Premium"; ?>';
situations['available'] = '<?php echo function_exists("__") ? __("website/domain/situations-available") : "Müsait"; ?>';
situations['unavailable'] = '<?php echo function_exists("__") ? __("website/domain/situations-unavailable") : "Alındı"; ?>';

var contact_button = '<a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">İletişim</a>';
var epp_code_support = <?php echo class_exists("Utility") && method_exists("Utility","jencode") ? Utility::jencode($epp_code_support) : json_encode($epp_code_support, JSON_UNESCAPED_UNICODE); ?>;
var tlds = <?php echo class_exists("Utility") && method_exists("Utility","jencode") ? Utility::jencode($tlds) : json_encode($tlds, JSON_UNESCAPED_UNICODE); ?>;
var disabled_style = "background:none; color:#333; cursor:no-drop; opacity:0.3;";

// Transfer toggle + URL ?domain= auto-submit
document.addEventListener('DOMContentLoaded', function(){
    var transferCb = document.getElementById('transferCheckbox');
    var transferBox = document.getElementById('transfercode');
    if(transferCb && transferBox) {
        transferCb.addEventListener('change', function(){
            transferBox.style.display = this.checked ? 'block' : 'none';
            var eppI = document.getElementById('eppCode');
            if(this.checked && eppI) eppI.focus();
        });
    }

    // Anasayfadan ?domain= ile geldiyse otomatik sorgula
    var urlParams = new URLSearchParams(window.location.search);
    var gDomain = urlParams.get('domain');

    // Captcha kodu da geliyorsa input'a yaz
    var captchaInput = document.getElementById('captchainputs');
    if(captchaInput) {
        // WiseCP captcha input_name'i biliyoruz: name attribute'un kontrol edelim
        var capName = captchaInput.getAttribute('name');
        var capValueFromUrl = urlParams.get(capName);
        if(capValueFromUrl) {
            captchaInput.value = capValueFromUrl;
        }
    }

    if(gDomain) {
        var input = document.getElementById('domainInput');
        if(input && input.value && input.value.length > 0) {
            // Captcha doluysa hemen submit, değilse focus
            if(captchaInput && !captchaInput.value) {
                // Captcha var ama dolmamış - kullanıcının doldurması gerek
                captchaInput.focus();
            } else {
                // Captcha yok ya da dolu - direkt sorgula
                setTimeout(function(){
                    submitnow(document.getElementById('submitnow'));
                }, 400);
            }
        }
    }
});

// JSON parse helper
function getJsonSafe(result) {
    try {
        if(typeof result === 'object') return result;
        return JSON.parse(result);
    } catch(e) {
        var match = (result || '').match(/\{[\s\S]*\}/);
        if(match) {
            try { return JSON.parse(match[0]); } catch(e2) { return false; }
        }
        return false;
    }
}

// HTML escape
function escHTML(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

// AJAX submit fonksiyonu - WiseCP MioAjax + jQuery yedek + fetch yedek
function submitnow(elem) {
    var btn = elem;
    if(btn.getAttribute('data-pending') === 'true') return false;
    btn.setAttribute('data-pending', 'true');

    var domainInput = document.getElementById('domainInput');
    var domain = (domainInput.value || '').trim().toLowerCase();
    if(!domain) {
        btn.removeAttribute('data-pending');
        domainInput.focus();
        return false;
    }

    // Form alanlarını doldur
    var form = document.getElementById('checkForm');
    if(form) {
        var domainField = form.querySelector('input[name=domain]');
        var tcodeField  = form.querySelector('input[name=tcode]');
        var typeField   = form.querySelector('input[name=type]');
        var transferCb  = document.getElementById('transferCheckbox');
        var eppInput    = document.getElementById('eppCode');

        if(domainField) domainField.value = domain;
        if(typeField)   typeField.value = 'domain';
        if(tcodeField)  tcodeField.value = (transferCb && transferCb.checked && eppInput) ? eppInput.value : '';

        // Captcha kodu - search-input içindeki captchainputs değerini form'a hidden olarak ekle
        var captchaInput = document.getElementById('captchainputs');
        if(captchaInput) {
            var capName = captchaInput.getAttribute('name');
            // Form'da bu name ile hidden var mı kontrol et
            var existingCap = form.querySelector('input[name="' + capName + '"]');
            if(!existingCap) {
                existingCap = document.createElement('input');
                existingCap.type = 'hidden';
                existingCap.name = capName;
                form.appendChild(existingCap);
            }
            existingCap.value = captchaInput.value || '';

            // Boşsa uyar
            if(!captchaInput.value || captchaInput.value.trim().length < 1) {
                btn.removeAttribute('data-pending');
                captchaInput.focus();
                captchaInput.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.40)';
                setTimeout(function(){ captchaInput.style.boxShadow = ''; }, 1500);
                return false;
            }
        }
    }

    // UI: loading state
    var resultsSection = document.getElementById('search-results');
    var resultsContainer = document.getElementById('searchResults');
    var searchTitle = document.getElementById('searchTitle');
    var loadingEl = document.getElementById('searchLoading');
    var errorEl = document.getElementById('searchError');

    resultsSection.style.display = 'block';
    searchTitle.innerHTML = '<span class="cdg-text-gradient">"' + escHTML(domain) + '"</span> için sonuçlar';
    resultsContainer.innerHTML = '';
    errorEl.style.display = 'none';
    loadingEl.style.display = 'flex';

    setTimeout(function(){
        resultsSection.scrollIntoView({behavior: 'smooth', block: 'start'});
    }, 100);

    // Yöntem 1: WiseCP MioAjax (CSRF dahil)
    if(typeof window.jQuery !== 'undefined' && typeof window.MioAjax !== 'undefined') {
        var request = window.MioAjax({
            action: window.jQuery('#checkForm').attr('action'),
            form:   window.jQuery('#checkForm'),
            method: 'POST',
        }, true, true);

        request.done(function(result){
            btn.removeAttribute('data-pending');
            handleResponse(result, domain);
        });
        request.fail(function(xhr, status, err){
            btn.removeAttribute('data-pending');
            showError('Sorgulama hatası: ' + (err || status || 'Bilinmeyen'));
        });
        return false;
    }

    // Yöntem 2: jQuery serialize + .post (MioAjax yoksa)
    if(typeof window.jQuery !== 'undefined') {
        window.jQuery.ajax({
            url: window.jQuery('#checkForm').attr('action'),
            type: 'POST',
            data: window.jQuery('#checkForm').serialize(),
            dataType: 'text',
        }).done(function(result){
            btn.removeAttribute('data-pending');
            handleResponse(result, domain);
        }).fail(function(xhr, status, err){
            btn.removeAttribute('data-pending');
            showError('Sorgulama hatası: ' + (err || status));
        });
        return false;
    }

    // Yöntem 3: vanilla fetch (jQuery de yoksa)
    var fd = new FormData(form);
    fetch(form.action || window.location.href, {
        method: 'POST',
        body: fd,
        credentials: 'same-origin',
    }).then(function(r){ return r.text(); })
    .then(function(text){
        btn.removeAttribute('data-pending');
        handleResponse(text, domain);
    }).catch(function(err){
        btn.removeAttribute('data-pending');
        showError('Sorgulama hatası: ' + err.message);
    });

    return false;
}

function handleResponse(result, query) {
    var loadingEl = document.getElementById('searchLoading');
    loadingEl.style.display = 'none';

    if(!result || result === '') {
        showError('Sunucudan boş yanıt geldi. WiseCP form action URL bulunamadı olabilir. Lütfen tekrar deneyin veya destek alın.');
        return;
    }

    var solve = getJsonSafe(result);
    if(!solve) {
        showError('Yanıt ayrıştırılamadı. Sunucudan beklenmeyen veri geldi.');
        console.log('Domain check raw response:', result);
        return;
    }

    if(solve.status === 'error') {
        showError(solve.message || 'Sorgulama sırasında hata oluştu.');
        return;
    }

    if(!solve.data || !solve.data.length) {
        showError('Sonuç bulunamadı.');
        return;
    }

    // Sonuçları render et
    var container = document.getElementById('searchResults');
    var html = '';

    solve.data.forEach(function(item, idx) {
        var fullDomain = (item.sld || '') + '.' + (item.tld || item.name || '');
        if(!item.sld && item.domain) fullDomain = item.domain;

        var status = item.status || 'unknown';
        var statusLabel = situations[status] || status;
        var statusColor = '#94a3b8';
        var statusIcon = 'bi-question-circle';
        if(status === 'available') { statusColor = '#10b981'; statusIcon = 'bi-check-circle-fill'; }
        else if(status === 'unavailable') { statusColor = '#ef4444'; statusIcon = 'bi-x-circle-fill'; }
        else if(status === 'premium') { statusColor = '#f59e0b'; statusIcon = 'bi-star-fill'; }

        // Fiyat
        var priceHTML = '<span style="color:#94a3b8;font-size:13px;">—</span>';
        if(item.reg_price && item.reg_price.length > 0 && item.reg_price[0].price) {
            priceHTML = '<span class="num">' + escHTML(item.reg_price[0].price.toString()) + '</span>';
            if(item.reg_price[0].period) {
                priceHTML += '<small style="color:#94a3b8;font-size:11px;margin-left:4px;">/' + escHTML(item.reg_price[0].period) + '</small>';
            }
        } else if(item.price) {
            priceHTML = '<span class="num">' + escHTML(item.price.toString()) + '</span>';
        }

        // Action
        var actionHTML = '';
        if(status === 'available') {
            var orderLink = item.order_link || '#';
            actionHTML = '<a href="' + escHTML(orderLink) + '" class="cdg-btn cdg-btn-primary cdg-btn-sm"><i class="bi bi-cart-plus"></i> Sepete Ekle</a>';
        } else if(status === 'unavailable') {
            actionHTML = '<button class="cdg-btn cdg-btn-outline cdg-btn-sm" disabled style="opacity:0.5;cursor:not-allowed;"><i class="bi bi-x"></i> Alındı</button>';
        } else if(status === 'premium') {
            actionHTML = contact_button;
        } else {
            actionHTML = '<span style="color:#94a3b8;font-size:13px;">—</span>';
        }

        var primaryClass = idx === 0 ? ' primary' : '';
        html += '<div class="cdg-search-result' + primaryClass + '">' +
            '<div class="cdg-search-result-name">' +
            '<i class="bi ' + statusIcon + '" style="color:' + statusColor + ';"></i> ' +
            '<strong>' + escHTML(fullDomain) + '</strong> ' +
            (idx === 0 ? '<span class="cdg-search-result-badge">İlk Tercih</span>' : '') +
            '<span style="color:' + statusColor + ';font-weight:600;font-size:13px;margin-left:8px;">' + escHTML(statusLabel) + '</span>' +
            '</div>' +
            '<div class="cdg-search-result-price">' + priceHTML + '</div>' +
            '<div>' + actionHTML + '</div>' +
        '</div>';
    });

    container.innerHTML = html;
}

function showError(msg) {
    var loadingEl = document.getElementById('searchLoading');
    var errorEl = document.getElementById('searchError');
    var resultsContainer = document.getElementById('searchResults');

    loadingEl.style.display = 'none';
    resultsContainer.innerHTML = '';

    errorEl.style.display = 'block';
    errorEl.innerHTML = '<div class="cdg-form-alert error" style="text-align:left;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:18px 22px;border-radius:14px;display:flex;align-items:center;gap:12px;">' +
        '<i class="bi bi-exclamation-triangle-fill" style="font-size:20px;"></i>' +
        '<span>' + escHTML(msg) + '</span>' +
    '</div>';
}

// CSS spinner
(function(){
    if(document.getElementById('cdgSpinStyle')) return;
    var st = document.createElement('style');
    st.id = 'cdgSpinStyle';
    st.textContent = '@keyframes cdgSpin { from{transform:rotate(0);} to{transform:rotate(360deg);} }';
    document.head.appendChild(st);
})();
</script>
