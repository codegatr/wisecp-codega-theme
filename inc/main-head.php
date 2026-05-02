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
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="robots" content="<?php echo isset($meta["robots"]) ? $meta["robots"] : 'all'; ?>" />

<?php if(class_exists('View') && method_exists('View', 'main_meta')) View::main_meta(); ?>

<link rel="canonical" href="<?php echo isset($canonical_link) ? $canonical_link : ''; ?>" />
<link rel="icon" type="image/png" href="<?php echo (isset($favicon_link) && $favicon_link) ? $favicon_link : $tadress.'images/favicon.png'; ?>" />
<meta name="theme-color" content="<?php echo isset($meta_color) ? $meta_color : '#1e40af'; ?>">

<?php if(isset($page) && isset($page["mockup"]) && $page["mockup"] != ''): ?>
    <meta property="og:image" content="<?php echo $page["mockup"]; ?>">
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
<link rel="stylesheet" href="<?php echo $tadress; ?>css/wisecp.css?v=<?php echo time(); ?>" />
<link rel="stylesheet" href="<?php echo $tadress; ?>css/style.css?v=<?php echo file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : 1; ?>" />

<!-- WiseCP main script -->
<script>var template_address = "<?php echo $tadress; ?>";</script>

<!-- jQuery - WiseCP MioAjax ve diger global JS bunu gerektirir -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>

<!-- WiseCP main script (MioAjax, alert_error, getJson, open_modal vs.) -->
<?php if(class_exists('View') && method_exists('View', 'main_script')) View::main_script(); ?>

<!-- Classic temadan kalan icin jquery alias'leri (bazi pluginler bekler) -->
<script>if(typeof jQuery!=='undefined' && typeof window.$==='undefined') window.$=jQuery;</script>
