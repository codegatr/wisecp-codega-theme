<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = [
    'page' => 'domain',
    'jquery-ui',
];

if(class_exists('Config') && method_exists('Config', 'get') && Config::get("theme/only-panel")) {
    $meta["robots"] = "NOINDEX,NOFOLLOW";
}

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

// Currency symbol cache
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

// Featured TLD'ler ($box_tldList) - admin panelinden seçilenler
$featured_tlds = isset($box_tldList) && is_array($box_tldList) ? $box_tldList : [];
?>

<!-- HERO + DOMAIN SORGU -->
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
            <p class="cdg-domain-lead"><?php echo $tld_count > 0 ? $tld_count . '+' : '500+'; ?> uzantı desteği · Anlık sorgu · Anında aktivasyon</p>

            <form id="domainSearchForm" class="cdg-domain-search-form" onsubmit="return cdgDomainSearch(this);">
                <div class="cdg-domain-search-input">
                    <i class="bi bi-search"></i>
                    <input type="text" id="domainInput" name="domain" placeholder="alanadi.com" autocomplete="off" required>
                    <button type="submit" id="submitnow" class="cdg-btn cdg-btn-primary cdg-btn-glow">
                        <i class="bi bi-search"></i> <span>Sorgula</span>
                    </button>
                </div>
                <label class="cdg-domain-transfer-toggle">
                    <input type="checkbox" id="transferCheckbox">
                    <span>Mevcut domainimi transfer etmek istiyorum</span>
                </label>
                <div class="transfercode" style="display:none;margin-top:10px;">
                    <input type="text" placeholder="EPP / Auth Kodu" id="eppCode" class="cdg-domain-epp">
                </div>
            </form>

            <?php if(!empty($featured_tlds)): ?>
            <div class="cdg-domain-quick">
                <span class="muted">Öne çıkan:</span>
                <?php foreach(array_slice($featured_tlds, 0, 5) as $tld):
                    $price_amount = '';
                    if(isset($tld['reg_price']['amount']) && isset($tld['reg_price']['cid'])) {
                        $price_amount = Money::formatter_symbol($tld['reg_price']['amount'], $tld['reg_price']['cid'], !$override_usrcurrency);
                    }
                ?>
                <span class="cdg-domain-chip">.<?php echo htmlspecialchars($tld['name']); ?> <strong><?php echo $price_amount; ?></strong></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SORGU SONUC ALANI (JavaScript ile doldurulacak) -->
<section class="cdg-section" id="search-results" style="display:none;">
    <div class="cdg-container">
        <div class="cdg-search-results-head">
            <h2 id="searchTitle"><span class="cdg-text-gradient">Sorgu Sonuçları</span></h2>
            <p>Aşağıdaki uzantıları siparişe ekleyebilirsiniz.</p>
        </div>
        <div id="searchResults" class="cdg-search-results">
            <!-- AJAX results -->
        </div>
    </div>
</section>

<!-- LOADING TEMPLATE (görünmez) -->
<div id="loading-template" style="display:none;">
    <div class="cdg-search-result loading">
        <div class="cdg-search-result-name">
            <i class="bi bi-hourglass-split" style="animation:cdgSpin 1s linear infinite;"></i>
            <strong class="domain-name">domain.com</strong>
        </div>
        <div class="cdg-search-result-price">
            <span style="color:#94a3b8;font-size:13px;">Sorgulanıyor...</span>
        </div>
        <div></div>
    </div>
</div>

<!-- ÖNE ÇIKAN TLD'LER (kart görünümü) -->
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
                $promo_tra = '';

                if(isset($tld['reg_price']['amount'])) {
                    $reg = Money::formatter_symbol($tld['reg_price']['amount'], $tld['reg_price']['cid'], !$override_usrcurrency);
                }
                if(isset($tld['tra_price']['amount'])) {
                    $tra = Money::formatter_symbol($tld['tra_price']['amount'], $tld['tra_price']['cid'], !$override_usrcurrency);
                }
                if(isset($tld['ren_price']['amount'])) {
                    $ren = Money::formatter_symbol($tld['ren_price']['amount'], $tld['ren_price']['cid'], !$override_usrcurrency);
                }

                // Promosyon kontrolü
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
                <div class="cdg-tld-card-ext">.<?php echo htmlspecialchars($tld['name']); ?></div>
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
                            $promo_tra = '';
                            if($is_promo) {
                                if(!empty($row['promo_register_price']) && $row['promo_register_price'] > 0) {
                                    $promo_reg = Money::formatter_symbol($row['promo_register_price'], $row['currency'] ?? 'TRY', !$override_usrcurrency);
                                }
                                if(!empty($row['promo_transfer_price']) && $row['promo_transfer_price'] > 0) {
                                    $promo_tra = Money::formatter_symbol($row['promo_transfer_price'], $row['currency'] ?? 'TRY', !$override_usrcurrency);
                                }
                            }
                        ?>
                        <tr<?php echo $is_promo ? ' style="background:#fef9c3;"' : ''; ?>>
                            <td>
                                <strong style="color:<?php echo $is_promo ? '#10b981' : '#1e40af'; ?>;font-size:15px;">.<?php echo htmlspecialchars($row['name']); ?></strong>
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
                            <td>
                                <?php if($promo_tra): ?>
                                <span style="text-decoration:line-through;color:#94a3b8;font-size:12px;"><?php echo $tra; ?></span>
                                <strong style="color:#10b981;"><?php echo $promo_tra; ?></strong>
                                <?php else: ?>
                                <?php echo $tra; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="document.getElementById('domainInput').value='alanadi.<?php echo htmlspecialchars($row['name']); ?>';document.getElementById('domainInput').focus();window.scrollTo({top:0,behavior:'smooth'});" class="cdg-btn cdg-btn-outline cdg-btn-sm">
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

<!-- SSS -->
<section class="cdg-section" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sık Sorulan</div>
            <h2>Domain hakkında <span class="cdg-text-gradient">sık sorulan sorular</span></h2>
        </div>
        <div class="cdg-faq-list" style="max-width:780px;margin:32px auto 0;">
            <details class="cdg-faq-item" open>
                <summary><span>Domain alımı ne kadar sürede aktif olur?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Ödeme onayından sonra <strong>dakikalar içinde</strong> domain'iniz aktif olur.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Mevcut domain'imi nasıl transfer ederim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Mevcut sağlayıcınızdan <strong>EPP (auth) kodu</strong> alın, yukarıdaki sorgu kutusunda transfer kutusunu işaretleyip kodu girin.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Hangi domain uzantısı benim için uygun?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Türkiye odaklı işletme: <strong>.com.tr</strong> veya <strong>.tr</strong>. Uluslararası: <strong>.com</strong>. Marka korumak için birden fazla uzantı almanızı öneririz.</div>
            </details>
        </div>
    </div>
</section>

<!-- DOMAIN SORGULAMA SCRIPTI -->
<script type="text/javascript">
var disabled_style = "background:none; color:#333; cursor:no-drop; opacity:0.3;";
var situations = [], loading_template;
situations['unknown'] = '<?php echo class_exists("Hook") ? __("website/domain/situations-unknown") : "Belirsiz"; ?>';
situations['premium'] = '<?php echo class_exists("Hook") ? __("website/domain/situations-premium") : "Premium"; ?>';
situations['available'] = '<?php echo class_exists("Hook") ? __("website/domain/situations-available") : "Müsait"; ?>';
situations['unavailable'] = '<?php echo class_exists("Hook") ? __("website/domain/situations-unavailable") : "Alınmış"; ?>';

var contact_button = '<a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm">İletişim</a>';
var epp_code_support = <?php echo class_exists("Utility") && method_exists("Utility","jencode") ? Utility::jencode($epp_code_support) : json_encode($epp_code_support); ?>;
var tlds = <?php echo class_exists("Utility") && method_exists("Utility","jencode") ? Utility::jencode($tlds) : json_encode($tlds); ?>;

document.addEventListener('DOMContentLoaded', function(){
    var transferCb = document.getElementById('transferCheckbox');
    var transferBox = document.querySelector('.transfercode');

    if(transferCb && transferBox) {
        transferCb.addEventListener('change', function(){
            transferBox.style.display = this.checked ? 'block' : 'none';
        });
    }

    // URL'den ?domain= parametresi
    var urlParams = new URLSearchParams(window.location.search);
    var gDomain = urlParams.get('domain');
    if(gDomain) {
        document.getElementById('domainInput').value = gDomain;
        document.getElementById('domainSearchForm').dispatchEvent(new Event('submit'));
    }
});

function cdgDomainSearch(form) {
    var input = document.getElementById('domainInput');
    var domain = input.value.trim().toLowerCase();
    if(!domain) return false;

    var resultsSection = document.getElementById('search-results');
    var resultsContainer = document.getElementById('searchResults');
    var searchTitle = document.getElementById('searchTitle');

    resultsSection.style.display = 'block';
    resultsSection.scrollIntoView({behavior: 'smooth'});

    searchTitle.innerHTML = '<span class="cdg-text-gradient">"' + escapeHTML(domain) + '"</span> için sonuçlar';
    resultsContainer.innerHTML = '<div class="cdg-search-result loading"><div class="cdg-search-result-name"><i class="bi bi-hourglass-split" style="animation:cdgSpin 1s linear infinite;"></i> <strong>Sorgulanıyor...</strong></div></div>';

    // WiseCP sorgu API'si - eğer sayfa zaten ?domain= ile yüklendiyse browser'a yönlendir
    if(window.location.search.indexOf('domain=' + encodeURIComponent(domain)) === -1) {
        window.location.href = '?domain=' + encodeURIComponent(domain);
        return false;
    }

    return false;
}

function escapeHTML(s) {
    return String(s).replace(/[&<>"']/g, function(c){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

// CSS animation for spinner
var st = document.createElement('style');
st.textContent = '@keyframes cdgSpin { from{transform:rotate(0);} to{transform:rotate(360deg);} }';
document.head.appendChild(st);
</script>
