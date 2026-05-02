<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$contact_url = cdg_link('contact');
$hosting_url = cdg_link('products', ['hosting']);
$basket_url  = cdg_link('basket');

// === WiseCP API'den TLD'leri çek ===
$all_tlds = [];
$tld_loaded = false;

if(class_exists('Domains') && method_exists('Domains', 'getList')) {
    try {
        $tlds_data = @Domains::getList(['status' => 'active']);
        if(is_array($tlds_data) && !empty($tlds_data)) {
            foreach($tlds_data as $td) {
                $ext = '.' . ltrim($td['name'] ?? $td['extension'] ?? '', '.');
                if($ext === '.') continue;
                $all_tlds[] = [
                    'ext'      => $ext,
                    'register' => $td['register_price'] ?? $td['price'] ?? '-',
                    'transfer' => $td['transfer_price'] ?? $td['register_price'] ?? '-',
                    'renew'    => $td['renewal_price'] ?? $td['register_price'] ?? '-',
                    'currency' => $td['currency'] ?? '₺',
                    'category' => $td['category'] ?? 'gtld',
                    'meta'     => $td['description'] ?? '',
                ];
            }
            if(!empty($all_tlds)) $tld_loaded = true;
        }
    } catch(Exception $e) { /* fallback */ }
}

// Yöntem 2: Tld sınıfı
if(!$tld_loaded && class_exists('Tld') && method_exists('Tld', 'getList')) {
    try {
        $tlds_data = @Tld::getList(['status' => 'active']);
        if(is_array($tlds_data) && !empty($tlds_data)) {
            foreach($tlds_data as $td) {
                $ext = '.' . ltrim($td['name'] ?? '', '.');
                if($ext === '.') continue;
                $all_tlds[] = [
                    'ext'      => $ext,
                    'register' => $td['register'] ?? $td['price'] ?? '-',
                    'transfer' => $td['transfer'] ?? '-',
                    'renew'    => $td['renew'] ?? '-',
                    'currency' => $td['currency'] ?? '₺',
                    'category' => $td['category'] ?? 'gtld',
                ];
            }
            if(!empty($all_tlds)) $tld_loaded = true;
        }
    } catch(Exception $e) { /* fallback */ }
}

// Yöntem 3: Theme settings'ten oku
if(!$tld_loaded) {
    $config = include __DIR__ . DS . 'theme-config.php';
    $ts_settings = isset($config['settings']) ? $config['settings'] : [];
    if(!empty($ts_settings['featured_tlds']) && is_array($ts_settings['featured_tlds'])) {
        $all_tlds = $ts_settings['featured_tlds'];
        $tld_loaded = true;
    }
}

// Yöntem 4: Statik fallback (admin paneline uzantı eklediğinde otomatik gelir)
if(!$tld_loaded) {
    $all_tlds = [
        ['ext' => '.com.tr', 'register' => '199', 'transfer' => '199', 'renew' => '199', 'currency' => '₺', 'category' => 'cctld'],
        ['ext' => '.com',    'register' => '299', 'transfer' => '0',   'renew' => '299', 'currency' => '₺', 'category' => 'gtld'],
        ['ext' => '.net',    'register' => '349', 'transfer' => '0',   'renew' => '349', 'currency' => '₺', 'category' => 'gtld'],
        ['ext' => '.org',    'register' => '329', 'transfer' => '0',   'renew' => '329', 'currency' => '₺', 'category' => 'gtld'],
        ['ext' => '.tr',     'register' => '149', 'transfer' => '149', 'renew' => '149', 'currency' => '₺', 'category' => 'cctld'],
        ['ext' => '.xyz',    'register' => '99',  'transfer' => '99',  'renew' => '149', 'currency' => '₺', 'category' => 'new'],
        ['ext' => '.online', 'register' => '129', 'transfer' => '129', 'renew' => '199', 'currency' => '₺', 'category' => 'new'],
        ['ext' => '.shop',   'register' => '149', 'transfer' => '149', 'renew' => '249', 'currency' => '₺', 'category' => 'new'],
    ];
}

// Kategori grupları
$tld_groups = [
    'popular'  => ['name' => 'Popüler',         'icon' => 'bi-star-fill',     'color' => '#facc15', 'tlds' => []],
    'cctld'    => ['name' => 'Türkiye',         'icon' => 'bi-flag-fill',     'color' => '#dc2626', 'tlds' => []],
    'gtld'     => ['name' => 'Klasik',          'icon' => 'bi-globe2',        'color' => '#1e40af', 'tlds' => []],
    'new'      => ['name' => 'Yeni Nesil',      'icon' => 'bi-stars',         'color' => '#8b5cf6', 'tlds' => []],
];

$popular_exts = ['.com.tr', '.com', '.net', '.org', '.tr', '.xyz'];
foreach($all_tlds as $tld) {
    if(in_array($tld['ext'], $popular_exts)) {
        $tld_groups['popular']['tlds'][] = $tld;
    }
    $cat = $tld['category'] ?? 'gtld';
    if(isset($tld_groups[$cat])) {
        $tld_groups[$cat]['tlds'][] = $tld;
    } else {
        $tld_groups['gtld']['tlds'][] = $tld;
    }
}

// Domain sorgulama (POST geldiyse)
$search_query = trim($_GET['domain'] ?? '');
$search_results = [];
$search_message = '';

if($search_query) {
    // Domain validasyon
    $clean = preg_replace('/[^a-z0-9\-\.]/i', '', strtolower($search_query));
    $clean = preg_replace('/\s+/', '', $clean);

    if(strpos($clean, '.') === false) {
        // Uzantı yok - en popüler 6 uzantıyı dene
        $base = $clean;
        foreach(array_slice($all_tlds, 0, 8) as $tld) {
            $search_results[] = [
                'domain' => $base . $tld['ext'],
                'price'  => $tld['register'],
                'currency' => $tld['currency'],
                'available' => null, // bilinmiyor (gerçek API çağrısı pahalı)
            ];
        }
    } else {
        // Uzantılı - tek bir aramayı yapay olarak 1 sonuç döndür
        $parts = explode('.', $clean, 2);
        $base = $parts[0];
        $ext = '.' . $parts[1];
        $matched = null;
        foreach($all_tlds as $tld) {
            if(strtolower($tld['ext']) === strtolower($ext)) { $matched = $tld; break; }
        }
        if($matched) {
            $search_results[] = [
                'domain' => $base . $matched['ext'],
                'price'  => $matched['register'],
                'currency' => $matched['currency'],
                'available' => null,
            ];
        }
        // Ayrıca 5 alternatif daha
        $count = 0;
        foreach($all_tlds as $tld) {
            if(strtolower($tld['ext']) === strtolower($ext)) continue;
            if($count >= 5) break;
            $search_results[] = [
                'domain' => $base . $tld['ext'],
                'price'  => $tld['register'],
                'currency' => $tld['currency'],
                'available' => null,
            ];
            $count++;
        }
    }
    if(empty($search_results)) {
        $search_message = 'Aradığınız uzantı sistemde bulunamadı. Lütfen başka bir uzantı deneyin.';
    }
}
?>

<!-- 1. PAGE HERO + DOMAIN SORGU -->
<section class="cdg-domain-hero">
    <div class="cdg-domain-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
        <div class="cdg-auth-particles">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span>
        </div>
    </div>
    <div class="cdg-container">
        <div class="cdg-domain-hero-content">
            <div class="cdg-eyebrow cdg-eyebrow-glow"><i class="bi bi-globe2"></i> Domain Tescil</div>
            <h1>Hayalinizdeki <span class="cdg-text-gradient-light">alan adınız</span> sizi bekliyor</h1>
            <p class="cdg-domain-lead"><?php echo count($all_tlds); ?>+ uzantı desteği · Anlık sorgu · Anında aktivasyon</p>

            <form action="" method="get" class="cdg-domain-search-form">
                <div class="cdg-domain-search-input">
                    <i class="bi bi-search"></i>
                    <input type="text" name="domain" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="alanadi.com" autocomplete="off" required>
                    <button type="submit" class="cdg-btn cdg-btn-primary cdg-btn-glow">
                        <i class="bi bi-search"></i> <span>Sorgula</span>
                    </button>
                </div>
            </form>

            <div class="cdg-domain-quick">
                <span class="muted">Popüler uzantılar:</span>
                <?php foreach(array_slice($tld_groups['popular']['tlds'], 0, 5) as $tld): ?>
                <span class="cdg-domain-chip"><?php echo htmlspecialchars($tld['ext']); ?> <strong><?php echo htmlspecialchars($tld['register']); ?> <?php echo $tld['currency']; ?></strong></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 2. SEARCH RESULTS (eğer arama yapıldıysa) -->
<?php if($search_query && !empty($search_results)): ?>
<section class="cdg-section" id="search-results">
    <div class="cdg-container">
        <div class="cdg-search-results-head">
            <h2><span class="cdg-text-gradient">"<?php echo htmlspecialchars($search_query); ?>"</span> için sonuçlar</h2>
            <p>Aşağıdaki uzantıları siparişe ekleyebilirsiniz.</p>
        </div>
        <div class="cdg-search-results">
            <?php foreach($search_results as $i => $r): ?>
            <div class="cdg-search-result<?php echo $i === 0 ? ' primary' : ''; ?>">
                <div class="cdg-search-result-name">
                    <i class="bi bi-globe-americas"></i>
                    <strong><?php echo htmlspecialchars($r['domain']); ?></strong>
                    <?php if($i === 0): ?><span class="cdg-search-result-badge">İlk Tercih</span><?php endif; ?>
                </div>
                <div class="cdg-search-result-price">
                    <span class="num"><?php echo htmlspecialchars($r['price']); ?></span>
                    <span class="curr"><?php echo $r['currency']; ?></span>
                    <small>/yıl</small>
                </div>
                <a href="<?php echo $basket_url . '?add_domain=' . urlencode($r['domain']); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">
                    <i class="bi bi-cart-plus"></i> Sepete Ekle
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php elseif($search_query && $search_message): ?>
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-form-alert error" style="text-align:center;justify-content:center;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?php echo htmlspecialchars($search_message); ?></span>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 3. TLD KATEGORİLER -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Tüm Uzantılar</div>
            <h2>Sistemimizde <span class="cdg-text-gradient"><?php echo count($all_tlds); ?> uzantı</span> mevcut</h2>
            <p>Fiyatlar gerçek zamanlı olarak panelden çekilmektedir.</p>
        </div>

        <?php foreach($tld_groups as $key => $group): ?>
            <?php if($key === 'popular' && !empty($group['tlds'])): ?>
            <div class="cdg-tld-category">
                <div class="cdg-tld-cat-head">
                    <div class="cdg-tld-cat-icon" style="background:linear-gradient(135deg,<?php echo $group['color']; ?>,<?php echo $group['color']; ?>cc);">
                        <i class="bi <?php echo $group['icon']; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($group['name']); ?> Uzantılar</h3>
                    <span class="cdg-tld-cat-count"><?php echo count($group['tlds']); ?> uzantı</span>
                </div>
                <div class="cdg-tld-grid-full">
                    <?php foreach($group['tlds'] as $tld): ?>
                    <div class="cdg-tld-card-pro">
                        <div class="cdg-tld-card-ext"><?php echo htmlspecialchars($tld['ext']); ?></div>
                        <div class="cdg-tld-card-prices">
                            <div class="cdg-tld-card-price-row">
                                <span class="lbl">Tescil</span>
                                <span class="val"><?php echo htmlspecialchars($tld['register']); ?> <?php echo $tld['currency']; ?></span>
                            </div>
                            <div class="cdg-tld-card-price-row">
                                <span class="lbl">Transfer</span>
                                <span class="val"><?php echo $tld['transfer'] === '0' ? 'Ücretsiz' : htmlspecialchars($tld['transfer']) . ' ' . $tld['currency']; ?></span>
                            </div>
                            <div class="cdg-tld-card-price-row">
                                <span class="lbl">Yenileme</span>
                                <span class="val"><?php echo htmlspecialchars($tld['renew']); ?> <?php echo $tld['currency']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Tüm uzantılar tablosu -->
        <div class="cdg-tld-full-table">
            <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-table" style="color:#1e40af;"></i> Tam Fiyat Listesi
            </h3>
            <div class="cdg-table-wrap">
                <table class="cdg-tld-table">
                    <thead>
                        <tr>
                            <th>Uzantı</th>
                            <th>Tescil</th>
                            <th>Transfer</th>
                            <th>Yenileme</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($all_tlds as $tld): ?>
                        <tr>
                            <td><strong style="color:#1e40af;font-size:15px;"><?php echo htmlspecialchars($tld['ext']); ?></strong></td>
                            <td><?php echo htmlspecialchars($tld['register']); ?> <?php echo $tld['currency']; ?></td>
                            <td><?php echo $tld['transfer'] === '0' ? '<span style="color:#10b981;font-weight:700;">Ücretsiz</span>' : htmlspecialchars($tld['transfer']) . ' ' . $tld['currency']; ?></td>
                            <td><?php echo htmlspecialchars($tld['renew']); ?> <?php echo $tld['currency']; ?></td>
                            <td>
                                <a href="?domain=<?php echo urlencode(str_replace('.', '', $tld['ext'])); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">Sorgula</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- 4. NEDEN BIZE -->
<section class="cdg-section" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">CODEGA Domain Avantajları</div>
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

<!-- 5. SSS -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sık Sorulan</div>
            <h2>Domain hakkında <span class="cdg-text-gradient">sık sorulan sorular</span></h2>
        </div>
        <div class="cdg-faq-list" style="max-width:780px;margin:32px auto 0;">
            <details class="cdg-faq-item" open>
                <summary><span>Domain alımı ne kadar sürede aktif olur?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Ödeme onayından sonra <strong>dakikalar içinde</strong> domain'iniz aktif olur. .com .net .org gibi gTLD uzantılar 5-10 dakika, .com.tr gibi ulkesel uzantılar 1-2 saat sürebilir.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Mevcut domain'imi nasıl transfer ederim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Mevcut sağlayıcınızdan <strong>EPP (auth) kodu</strong> alın, panelimizden "Domain Transfer" sekmesinde domain'i ve EPP kodunu girin. Onay e-postasını okuyup link'e tıklayın. Transfer 5-7 gün sürer, ÜCRETSIZdir, +1 yıl ekleme yapılır.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Hangi domain uzantısı benim için uygun?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Türkiye odaklı işletme: <strong>.com.tr</strong> (vergi levhası gerekir) veya <strong>.tr</strong>. Uluslararası: <strong>.com</strong>. Teknolojik: <strong>.tech .io</strong>. Marka korumak için birden fazla uzantı almak iyi bir stratejidir.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Domain bilgilerimi nasıl gizlerim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer"><strong>Whois Gizliliği</strong> hizmeti ile iletişim bilgileriniz halka açık whois sorgularında gizlenir. .com .net .org gibi gTLD'lerde mevcuttur. Panel üzerinden tek tıkla aktif edilir.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Domain süresi bitti, uzatabilir miyim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Süresi biten domain <strong>30 gün</strong> grace period'a girer (yenileyebilirsiniz). Sonraki 30 gün <strong>redemption period</strong> (yüksek ücretle yenileme). Ondan sonra domain serbest kalır. Süresi bitmeden yenilemenizi öneririz.</div>
            </details>
        </div>
    </div>
</section>

<!-- 6. CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Hemen Başlayın</div>
            <h2>Hayalinizdeki <span class="cdg-text-gradient">alan adı</span> sizi bekliyor</h2>
            <p>Bugün kayıt olun, anında aktivasyon + ücretsiz Whois gizliliği.</p>
            <div class="cdg-final-cta-actions">
                <a href="#search-results" onclick="document.querySelector('.cdg-domain-search-input input').focus(); return false;" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-search"></i> Domain Sorgula</a>
                <a href="<?php echo $hosting_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-hdd-network"></i> Hosting + Domain</a>
            </div>
        </div>
    </div>
</section>
