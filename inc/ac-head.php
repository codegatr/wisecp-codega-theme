<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
// ac-head.php — Client Area için head bölümü
// FALLBACK: main-head.php çağırılmazsa diye kendi başına CSS yüklemesi de yapar

if(!isset($tadress)) $tadress = defined('THEMES_URL') && defined('THEMENAME') ? THEMES_URL . THEMENAME . '/' : '/templates/website/Codega/';
if(!isset($hoptions) || !is_array($hoptions)) $hoptions = [];
if(!isset($meta) || !is_array($meta)) $meta = [];
if(!isset($meta_color)) $meta_color = '#1e40af';
if(!isset($favicon_link)) $favicon_link = $tadress . 'images/favicon.png';
if(!isset($canonical_link)) $canonical_link = '';

// main-head.php varsa onu çağır - tüm WiseCP standardı CSS, JS, meta'lar oradan gelir
$main_head = __DIR__ . DS . 'main-head.php';
if(file_exists($main_head)) {
    include $main_head;
} else {
    // Acil fallback - main-head.php yoksa minimal CSS yine yüklensin
    ?>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="<?php echo htmlspecialchars($meta_color); ?>">
    <title><?php echo isset($meta["title"]) ? htmlspecialchars($meta["title"]) : 'CODEGA Hesap Paneli'; ?></title>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_link); ?>" />
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($favicon_link); ?>" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?php echo $tadress; ?>css/bootstrap-icons.css?v=1.11.3">

    <!-- WiseCP main style -->
    <?php if(class_exists('View') && method_exists('View', 'main_style')) View::main_style(); ?>

    <!-- Codega CSS -->
    <link rel="stylesheet" href="<?php echo $tadress; ?>css/wisecp.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="<?php echo $tadress; ?>css/style.css?v=<?php echo time(); ?>" />

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>

    <!-- WiseCP main script -->
    <?php if(class_exists('View') && method_exists('View', 'main_script')) View::main_script(); ?>
    <?php
}
