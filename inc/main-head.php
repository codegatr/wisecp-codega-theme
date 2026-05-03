<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// === TÜRKÇE KARAKTER DESTEĞİ - UTF-8 ZORUNLU ===
// htmlspecialchars(, ENT_QUOTES | ENT_HTML5, 'UTF-8'), htmlentities() ve diğer string fonksiyonları için default UTF-8
@ini_set('default_charset', 'UTF-8');
if(function_exists('mb_internal_encoding')) {
    @mb_internal_encoding('UTF-8');
}
if(function_exists('mb_http_output')) {
    @mb_http_output('UTF-8');
}
if(function_exists('mb_regex_encoding')) {
    @mb_regex_encoding('UTF-8');
}

if(!isset($hoptions) || !is_array($hoptions)) $hoptions = [];
if(!isset($meta) || !is_array($meta)) $meta = [];
if(!isset($tadress)) $tadress = defined('THEMES_URL') && defined('THEMENAME') ? THEMES_URL . THEMENAME . '/' : '/templates/website/Codega/';

if(isset($hoptions["page"]) && $hoptions["page"] != "index" && isset($meta["title"]) && function_exists('__'))
{
    $suffix     = @__("website/index/meta/title-suffix");
    $home_title = @__("website/index/meta/title");
    if($suffix && strlen($suffix) > 1)
        $meta["title"] = str_replace(['{home_title}','{page_title}'],[$home_title,$meta["title"]],$suffix);
}
?>
<!-- Meta Tags -->
<title><?php echo isset($meta["title"]) ? $meta["title"] : 'CODEGA'; ?></title>
<meta name="keywords" content="<?php echo isset($meta["keywords"]) ? $meta["keywords"] : '';?>" />
<meta name="description" content="<?php echo isset($meta["description"]) ? $meta["description"] : '';?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="<?php echo isset($meta["robots"]) ? $meta["robots"] : 'all'; ?>" />

<?php if(class_exists('View') && method_exists('View', 'main_meta')) View::main_meta(); ?>

<?php
// CANONICAL URL - boş olursa current URL kullan (SEO için kritik)
if(!isset($canonical_link) || !$canonical_link) {
    $_proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $_host  = $_SERVER['HTTP_HOST'] ?? 'codega.com.tr';
    $_uri   = strtok($_SERVER['REQUEST_URI'] ?? '/', '?'); // Query string'i çıkar
    $canonical_link = $_proto . '://' . $_host . $_uri;
}
?>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
<link rel="icon" type="image/png" href="<?php echo (isset($favicon_link) && $favicon_link) ? $favicon_link : $tadress.'images/favicon.png'; ?>" />
<meta name="theme-color" content="<?php echo isset($meta_color) ? $meta_color : '#2E3B4E'; ?>">
<meta name="color-scheme" content="light">

<?php if(isset($page) && isset($page["mockup"]) && $page["mockup"] != ''): ?>
    <meta property="og:image" content="<?php echo $page["mockup"]; ?>">
<?php endif; ?>

<!-- Open Graph (Facebook, LinkedIn) -->
<meta property="og:title" content="<?php echo isset($meta["title"]) ? htmlspecialchars($meta["title"], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'CODEGA'; ?>" />
<meta property="og:description" content="<?php echo isset($meta["description"]) ? htmlspecialchars($meta["description"], ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo isset($canonical_link) ? $canonical_link : ''; ?>" />
<meta property="og:site_name" content="CODEGA" />
<meta property="og:locale" content="tr_TR" />

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo isset($meta["title"]) ? htmlspecialchars($meta["title"], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'CODEGA'; ?>" />
<meta name="twitter:description" content="<?php echo isset($meta["description"]) ? htmlspecialchars($meta["description"], ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" />
<?php if(isset($page) && isset($page["mockup"]) && $page["mockup"] != ''): ?>
    <meta name="twitter:image" content="<?php echo $page["mockup"]; ?>">
<?php endif; ?>

<?php
    if(isset($lang_list) && $lang_list && is_array($lang_list)){
        foreach($lang_list AS $l_row){
            if(!isset($l_row["link"]) || !isset($l_row["key"])) continue;
            $l_link = str_replace(["?chl=true","&chl=true"], "", $l_row["link"]);
            ?><link rel="alternate" hreflang="<?php echo $l_row["key"]; ?>" href="<?php echo $l_link; ?>" />
<?php
        }
    }
?>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Bootstrap Icons (lokal - Tracking Prevention bypass) -->
<link rel="stylesheet" href="<?php echo $tadress; ?>css/bootstrap-icons.css?v=1.11.3">
<!-- CDN fallback -->
<script>
(function(){
    setTimeout(function(){
        var test = document.createElement('span');
        test.className = 'bi bi-check';
        test.style.cssText = 'position:absolute;visibility:hidden;font-family:bootstrap-icons!important;';
        document.body.appendChild(test);
        var loaded = window.getComputedStyle(test, ':before').getPropertyValue('content');
        document.body.removeChild(test);
        if(!loaded || loaded === 'none' || loaded === '""'){
            var l = document.createElement('link');
            l.rel = 'stylesheet';
            l.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css';
            document.head.appendChild(l);
        }
    }, 500);
})();
</script>

<!-- WiseCP main style -->
<?php if(class_exists('View') && method_exists('View', 'main_style')) View::main_style(); ?>

<!-- Codega CSS -->
<link rel="stylesheet" href="<?php echo $tadress; ?>css/wisecp.css?v=<?php echo file_exists(__DIR__ . '/../css/wisecp.css') ? filemtime(__DIR__ . '/../css/wisecp.css') : (file_exists(__DIR__ . '/../css/wisecp.php') ? filemtime(__DIR__ . '/../css/wisecp.php') : 1); ?>" />
<link rel="stylesheet" href="<?php echo $tadress; ?>css/style.css?v=<?php echo file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : 1; ?>" />

<!-- CODEGA Kurumsal Kimlik (logo + palet + tipografi) -->
<?php $brand_inc = __DIR__ . DIRECTORY_SEPARATOR . 'cdg-brand.php'; if(file_exists($brand_inc)) include $brand_inc; ?>

<!-- CODEGA Migration Runner (idempotent, sadece yeni SQL varsa uygular) -->
<?php
    $cdg_migration_inc = __DIR__ . DIRECTORY_SEPARATOR . 'cdg-migration-runner.php';
    if(file_exists($cdg_migration_inc)) include_once $cdg_migration_inc;
?>

<!-- WiseCP main script -->
<script>var template_address = "<?php echo $tadress; ?>";</script>

<!-- jQuery - WiseCP MioAjax ve diger global JS bunu gerektirir -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>

<!-- WiseCP main script (MioAjax, alert_error, getJson, open_modal vs.) -->
<?php if(class_exists('View') && method_exists('View', 'main_script')) View::main_script(); ?>

<!-- Classic temadan kalan icin jquery alias'leri (bazi pluginler bekler) -->
<script>if(typeof jQuery!=='undefined' && typeof window.$==='undefined') window.$=jQuery;</script>

<!-- ====================================================
     JSON-LD Schema.org Structured Data (SEO için kritik)
     Google rich results için Organization + WebSite
     ==================================================== -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "@id": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>/#organization",
    "name": "CODEGA",
    "alternateName": "CODEGA Yazılım ve İletişim Hizmetleri",
    "url": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>",
    "logo": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>/templates/website/Codega/images/favicon.png",
    "description": "Modern PHP altyapısıyla web yazılım, hosting, domain ve özel yazılım çözümleri. AKSOY GROUP iştiraki.",
    "foundingDate": "2020",
    "parentOrganization": {
        "@type": "Organization",
        "name": "AKSOY GROUP",
        "url": "https://aksoy.web.tr"
    },
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "TR",
        "addressLocality": "Konya"
    },
    "sameAs": [
        "https://instagram.com/codegatr",
        "https://linkedin.com/company/codega",
        "https://github.com/codegatr"
    ]
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "@id": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>/#website",
    "url": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>",
    "name": "CODEGA",
    "description": "<?php echo isset($meta['description']) ? htmlspecialchars($meta['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'Web yazılım, hosting, domain ve özel yazılım çözümleri'; ?>",
    "inLanguage": "tr-TR",
    "publisher": {
        "@id": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>/#organization"
    },
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "<?php echo defined('APP_URI') ? APP_URI : 'https://codega.com.tr'; ?>/knowledgebase?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>
