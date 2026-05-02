<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// === SAVUNMA: WiseCP'nin tanımlamadığı her şey için fallback ===
if(!isset($header_type) || !$header_type)         $header_type = 1;
if(!isset($clientArea_type) || !$clientArea_type) $clientArea_type = 1;
if(!isset($hoptions) || !is_array($hoptions))     $hoptions = [];
if(!isset($meta) || !is_array($meta))             $meta = ['title'=>'CODEGA','description'=>'','keywords'=>'','robots'=>''];
if(!isset($canonical_link)) $canonical_link = '';
if(!isset($favicon_link))   $favicon_link   = '';
if(!isset($meta_color))     $meta_color     = '#1e40af';
if(!isset($lang_list))      $lang_list      = [];

$_cdg_page = isset($hoptions["page"]) ? $hoptions["page"] : '';
?><!DOCTYPE html>
<html lang="<?php echo function_exists('___') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php include __DIR__.DS."inc".DS."main-head.php"; ?>
</head>

<?php
    if($_cdg_page == "index"){
        ?><body id="cdg-home"><?php
    }else{
        ?><body class="cdg-public"><?php
    }

    if(class_exists('Hook') && ($h_contents = Hook::run("ClientAreaBeginBody"))) {
        foreach($h_contents AS $h_content) if($h_content) echo $h_content;
    }
    if(defined('EOL')) echo EOL;

    // Header dosyası kontrolü — fallback ile
    $_header_file = __DIR__.DS."inc".DS."main-header-".$header_type.".php";
    if(!file_exists($_header_file)) $_header_file = __DIR__.DS."inc".DS."main-header-1.php";
    if(file_exists($_header_file)) include $_header_file;
?>

<main class="cdg-main">
    {get_content}
</main>

<?php
    $_footer_file = __DIR__.DS."inc".DS."main-footer.php";
    if(file_exists($_footer_file)) include $_footer_file;

    if(class_exists('View') && method_exists('View', 'footer_codes')) View::footer_codes();
?>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-rocket"></i></a>

<?php
    if(class_exists('Hook') && ($h_contents = Hook::run("ClientAreaEndBody"))) {
        foreach($h_contents AS $h_content) if($h_content) echo $h_content;
    }
?>

</body>
</html>
