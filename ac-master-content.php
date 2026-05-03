<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

// Flag: panel sayfaları master-content uygulanıp uygulanmadığını kontrol etmek için kullanır
$_cdg_in_master_content = true;

// === SAVUNMA ===
if(!isset($header_type) || !$header_type)         $header_type = 1;
if(!isset($clientArea_type) || !$clientArea_type) $clientArea_type = 1;
if(!isset($hoptions) || !is_array($hoptions))     $hoptions = [];
if(!isset($meta) || !is_array($meta))             $meta = ['title'=>'CODEGA','description'=>'','keywords'=>'','robots'=>''];
if(!isset($canonical_link)) $canonical_link = '';
if(!isset($favicon_link))   $favicon_link   = '';
if(!isset($meta_color))     $meta_color     = '#2E3B4E';
if(!isset($lang_list))      $lang_list      = [];
?><!DOCTYPE html>
<html lang="<?php echo function_exists('___') ? ___("package/code") : 'tr'; ?>">
<head>
    <?php include __DIR__.DS."inc".DS."main-head.php"; ?>
</head>

<body id="codegapanel">

<?php
    if(class_exists('Hook') && ($h_contents = Hook::run("ClientAreaBeginBody"))) {
        foreach($h_contents AS $h_content) if($h_content) echo $h_content;
    }
    if(defined('EOL')) echo EOL;
?>

<div class="cdg-ac-wrap">

    <?php
        $_sidebar_file = __DIR__.DS."inc".DS."ac-sidebar-".$clientArea_type.".php";
        if(!file_exists($_sidebar_file)) $_sidebar_file = __DIR__.DS."inc".DS."ac-sidebar-1.php";
        if(file_exists($_sidebar_file)) include $_sidebar_file;
    ?>

    <main class="cdg-ac-main">

        <?php
            $_acheader_file = __DIR__.DS."inc".DS."ac-header-".$clientArea_type.".php";
            if(!file_exists($_acheader_file)) $_acheader_file = __DIR__.DS."inc".DS."ac-header-1.php";
            if(file_exists($_acheader_file)) include $_acheader_file;
        ?>

        <div class="cdg-ac-content">
            {get_content}
        </div>

        <?php
            $_acfooter_file = __DIR__.DS."inc".DS."ac-footer-".$clientArea_type.".php";
            if(!file_exists($_acfooter_file)) $_acfooter_file = __DIR__.DS."inc".DS."ac-footer-1.php";
            if(file_exists($_acfooter_file)) include $_acfooter_file;
        ?>
    </main>
</div>

<?php
    if(class_exists('View') && method_exists('View', 'footer_codes')) View::footer_codes();

    if(class_exists('Hook') && ($h_contents = Hook::run("ClientAreaEndBody"))) {
        foreach($h_contents AS $h_content) if($h_content) echo $h_content;
    }
?>

</body>
</html>
