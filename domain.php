<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Diagnostic mode: ?_diag=1 ile global'leri kontrol et
if(isset($_GET['_diag']) && $_GET['_diag'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== CODEGA DOMAIN DIAGNOSTIC ===\n\n";
    echo "tldList exists: " . (isset($tldList) ? 'YES' : 'NO') . "\n";
    if(isset($tldList) && is_array($tldList)) {
        echo "tldList count: " . count($tldList) . "\n";
        echo "First 3 TLDs:\n";
        foreach(array_slice($tldList, 0, 3) as $t) {
            echo "  - " . ($t['name'] ?? '?') . " (reg: " . ($t['reg_price']['amount'] ?? '?') . ")\n";
        }
    }
    echo "\nbox_tldList exists: " . (isset($box_tldList) ? 'YES' : 'NO') . "\n";
    if(isset($box_tldList) && is_array($box_tldList)) {
        echo "box_tldList count: " . count($box_tldList) . "\n";
    }
    echo "\nDomains class exists: " . (class_exists('Domains') ? 'YES' : 'NO') . "\n";
    echo "Tld class exists: " . (class_exists('Tld') ? 'YES' : 'NO') . "\n";
    echo "Money class exists: " . (class_exists('Money') ? 'YES' : 'NO') . "\n";
    echo "\nHosting:\n";
    echo "  showCategory: " . (isset($showCategory) ? 'YES (' . ($showCategory['title'] ?? '?') . ')' : 'NO') . "\n";
    echo "  category: " . (isset($category) ? 'YES (' . ($category['title'] ?? '?') . ')' : 'NO') . "\n";
    echo "  get_list: " . (isset($get_list) && is_callable($get_list) ? 'YES' : 'NO') . "\n";
    echo "  get_categories: " . (isset($get_categories) && is_callable($get_categories) ? 'YES' : 'NO') . "\n";
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
            <div class="cdg-domain-badge"><i class="bi bi-globe2"></i> Domain Tescil</div>
            <h1>Hayalinizdeki <span class="cdg-text-gradient-light">alan adınız</span> sizi bekliyor</h1>
            <p class="cdg-domain-lead"><?php echo $tld_count > 0 ? $tld_count . '+' : '500+'; ?> uzantı desteği · Anlık sorgu · Anında aktivasyon</p>

            <form id="checkForm" action="<?php echo isset($links["controller"]) ? $links["controller"] : ""; ?>" method="POST" class="cdg-domain-search-form mio-ajax-form">
                <input type="hidden" name="type" value="domain">
                <input type="hidden" name="domain" id="hiddenDomain">
                <input type="hidden" name="tcode" id="hiddenTcode" value="">
                <?php if(class_exists("CSRF")) echo CSRF::input(); ?>

                <div class="cdg-domain-search-input">
                    <i class="bi bi-search"></i>
                    <input type="text" id="domainInput" placeholder="alanadi.com" autocomplete="off" required>
                    <a href="javascript:void(0);" id="submitnow" class="cdg-btn cdg-btn-primary cdg-btn-glow">
                        <i class="bi bi-search"></i> <span>Sorgula</span>
                    </a>
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

<!-- DOMAIN SORGULAMA SCRIPTI - WiseCP MioAjax Pattern -->
<script type="text/javascript">
var situations = {};
situations['unknown'] = '<?php echo function_exists("__") ? __("website/domain/situations-unknown") : "Belirsiz"; ?>';
situations['premium'] = '<?php echo function_exists("__") ? __("website/domain/situations-premium") : "Premium"; ?>';
situations['available'] = '<?php echo function_exists("__") ? __("website/domain/situations-available") : "Müsait"; ?>';
situations['unavailable'] = '<?php echo function_exists("__") ? __("website/domain/situations-unavailable") : "Alındı"; ?>';

var epp_code_support = <?php echo class_exists("Utility") && method_exists("Utility","jencode") ? Utility::jencode($epp_code_support) : json_encode($epp_code_support); ?>;
var tlds = <?php echo class_exists("Utility") && method_exists("Utility","jencode") ? Utility::jencode($tlds) : json_encode($tlds); ?>;

(function(){
    var ready = function(fn){
        if(document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    };

    ready(function(){
        var transferCb  = document.getElementById('transferCheckbox');
        var transferBox = document.querySelector('.transfercode');
        var btn         = document.getElementById('submitnow');
        var input       = document.getElementById('domainInput');
        var hiddenInp   = document.getElementById('hiddenDomain');
        var hiddenTcode = document.getElementById('hiddenTcode');

        if(transferCb && transferBox) {
            transferCb.addEventListener('change', function(){
                transferBox.style.display = this.checked ? 'block' : 'none';
                var eppI = document.getElementById('eppCode');
                if(this.checked && eppI) eppI.focus();
            });
        }

        // Submit button click
        if(btn) {
            btn.addEventListener('click', function(){
                cdgDoSubmit();
            });
        }

        // Enter tuşu
        if(input) {
            input.addEventListener('keydown', function(e){
                if(e.key === 'Enter') {
                    e.preventDefault();
                    cdgDoSubmit();
                }
            });
        }

        // ?domain= ile geldiyse otomatik sorgula
        var urlParams = new URLSearchParams(window.location.search);
        var gDomain = urlParams.get('domain');
        if(gDomain && input) {
            input.value = gDomain;
            setTimeout(cdgDoSubmit, 300);
        }
    });

    window.cdgDoSubmit = function(){
        var btn       = document.getElementById('submitnow');
        var input     = document.getElementById('domainInput');
        var hiddenInp = document.getElementById('hiddenDomain');
        var hiddenTc  = document.getElementById('hiddenTcode');
        var eppInput  = document.getElementById('eppCode');
        var transferCb= document.getElementById('transferCheckbox');

        if(!input) return;
        var domain = (input.value || '').trim().toLowerCase();
        if(!domain) {
            input.focus();
            return;
        }

        if(btn && btn.getAttribute('data-pending') === 'true') return;
        if(btn) btn.setAttribute('data-pending', 'true');

        if(hiddenInp) hiddenInp.value = domain;
        if(hiddenTc)  hiddenTc.value  = (transferCb && transferCb.checked && eppInput) ? eppInput.value : '';

        var resultsSection = document.getElementById('search-results');
        var resultsContainer = document.getElementById('searchResults');
        var searchTitle = document.getElementById('searchTitle');

        resultsSection.style.display = 'block';
        searchTitle.innerHTML = '<span class="cdg-text-gradient">"' + cdgEscapeHTML(domain) + '"</span> için sonuçlar';
        resultsContainer.innerHTML = '<div class="cdg-search-result loading"><div class="cdg-search-result-name"><i class="bi bi-hourglass-split" style="animation:cdgSpin 1s linear infinite;"></i> <strong>Sorgulanıyor...</strong></div></div>';

        setTimeout(function(){
            resultsSection.scrollIntoView({behavior: 'smooth', block: 'start'});
        }, 100);

        // jQuery + MioAjax kullan
        if(typeof window.jQuery !== 'undefined' && typeof window.MioAjax !== 'undefined') {
            var request = window.MioAjax({
                action: window.jQuery('#checkForm').attr('action'),
                form:   window.jQuery('#checkForm'),
                method: 'POST',
            }, true, true);

            request.done(function(result){
                if(btn) btn.removeAttribute('data-pending');
                cdgRenderResults(result, domain);
            });

            request.fail(function(xhr, status, err){
                if(btn) btn.removeAttribute('data-pending');
                cdgRenderError('Sorgulama sırasında hata oluştu: ' + (err || status));
            });

        } else if(typeof window.jQuery !== 'undefined') {
            // jQuery var ama MioAjax yok - native ajax
            window.jQuery.post(
                window.jQuery('#checkForm').attr('action'),
                window.jQuery('#checkForm').serialize()
            ).done(function(result){
                if(btn) btn.removeAttribute('data-pending');
                cdgRenderResults(result, domain);
            }).fail(function(xhr, status, err){
                if(btn) btn.removeAttribute('data-pending');
                cdgRenderError('Sorgulama hatasi: ' + (err || status));
            });

        } else {
            // jQuery yok - vanilla fetch
            var formData = new FormData(document.getElementById('checkForm'));
            fetch(document.getElementById('checkForm').action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            })
            .then(function(r){ return r.text(); })
            .then(function(text){
                if(btn) btn.removeAttribute('data-pending');
                cdgRenderResults(text, domain);
            })
            .catch(function(err){
                if(btn) btn.removeAttribute('data-pending');
                cdgRenderError('Sorgulama hatasi: ' + err.message);
            });
        }
    };

    window.cdgRenderResults = function(result, query){
        var container = document.getElementById('searchResults');
        if(!result) {
            cdgRenderError('Sunucudan boş yanıt geldi.');
            return;
        }

        var solve = null;
        try {
            if(typeof result === 'string') {
                solve = JSON.parse(result);
            } else {
                solve = result;
            }
        } catch(e) {
            // JSON değil - HTML içinde olabilir
            var match = (result || '').match(/\{[\s\S]*\}/);
            if(match) {
                try { solve = JSON.parse(match[0]); } catch(e2) {}
            }
        }

        if(!solve) {
            cdgRenderError('Yanit ayrıştırılamadı. Lütfen tekrar deneyin.');
            return;
        }

        if(solve.status === 'error') {
            cdgRenderError(solve.message || 'Bilinmeyen hata.');
            return;
        }

        if(!solve.data || !solve.data.length) {
            container.innerHTML = '<div class="cdg-search-result"><div class="cdg-search-result-name"><i class="bi bi-info-circle"></i> Sonuç bulunamadı.</div></div>';
            return;
        }

        var html = '';
        solve.data.forEach(function(item, idx){
            var name = (item.domain || item.name || '').toString();
            var status = item.status || 'unknown';
            var statusLabel = situations[status] || status;
            var statusColor = '#94a3b8';
            var statusIcon = 'bi-question-circle';
            if(status === 'available') { statusColor = '#10b981'; statusIcon = 'bi-check-circle-fill'; }
            else if(status === 'unavailable') { statusColor = '#ef4444'; statusIcon = 'bi-x-circle-fill'; }
            else if(status === 'premium') { statusColor = '#f59e0b'; statusIcon = 'bi-star-fill'; }

            var priceHTML = '';
            if(item.price) priceHTML = '<span class="num">' + cdgEscapeHTML(item.price.toString()) + '</span>';
            else if(item.amount) priceHTML = '<span class="num">' + cdgEscapeHTML(item.amount.toString()) + '</span>';

            var actionHTML = '';
            if(status === 'available' && item.order_link) {
                actionHTML = '<a href="' + cdgEscapeHTML(item.order_link) + '" class="cdg-btn cdg-btn-primary cdg-btn-sm"><i class="bi bi-cart-plus"></i> Sepete Ekle</a>';
            } else if(status === 'unavailable') {
                actionHTML = '<button class="cdg-btn cdg-btn-outline cdg-btn-sm" disabled><i class="bi bi-x"></i> Alındı</button>';
            } else if(status === 'premium') {
                actionHTML = '<a href="' + cdgEscapeHTML(item.order_link || '#') + '" class="cdg-btn cdg-btn-outline cdg-btn-sm" style="border-color:#f59e0b;color:#f59e0b;"><i class="bi bi-star-fill"></i> Premium</a>';
            } else {
                actionHTML = '<span style="color:#94a3b8;font-size:13px;">—</span>';
            }

            var primaryClass = idx === 0 ? ' primary' : '';
            html += '<div class="cdg-search-result' + primaryClass + '">' +
                '<div class="cdg-search-result-name">' +
                '<i class="bi ' + statusIcon + '" style="color:' + statusColor + ';"></i> ' +
                '<strong>' + cdgEscapeHTML(name) + '</strong>' +
                (idx === 0 ? '<span class="cdg-search-result-badge">İlk Tercih</span>' : '') +
                '<span style="color:' + statusColor + ';font-weight:600;font-size:13px;margin-left:8px;">' + cdgEscapeHTML(statusLabel) + '</span>' +
                '</div>' +
                '<div class="cdg-search-result-price">' + priceHTML + '</div>' +
                '<div>' + actionHTML + '</div>' +
            '</div>';
        });

        container.innerHTML = html;
    };

    window.cdgRenderError = function(msg){
        var container = document.getElementById('searchResults');
        container.innerHTML = '<div class="cdg-search-result" style="border-color:#fca5a5;background:#fee2e2;">' +
            '<div class="cdg-search-result-name" style="color:#991b1b;">' +
            '<i class="bi bi-exclamation-triangle-fill"></i> ' +
            '<strong>' + cdgEscapeHTML(msg) + '</strong>' +
            '</div>' +
            '<div></div>' +
            '<div></div>' +
        '</div>';
    };

    window.cdgEscapeHTML = function(s){
        return String(s == null ? '' : s).replace(/[&<>"\']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    };
})();

// Spinner animation
(function(){
    if(!document.getElementById('cdgSpinStyle')) {
        var st = document.createElement('style');
        st.id = 'cdgSpinStyle';
        st.textContent = '@keyframes cdgSpin { from{transform:rotate(0);} to{transform:rotate(360deg);} }';
        document.head.appendChild(st);
    }
})();
</script>
