<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

/**
 * Ana sayfa router'ı
 *
 * - Login OLMAMIŞ kullanıcı: index.php (public landing)
 * - Login OLMUŞ kullanıcı: ac-dashboard.php (müşteri paneli)
 */

// Login durumunu farklı yöntemlerle tespit et (WiseCP versiyonuna göre)
$is_logged_in = false;

// Yöntem 1: User::$init->logged_in
if(class_exists('User') && isset(User::$init)) {
    if(isset(User::$init->logged_in) && User::$init->logged_in) {
        $is_logged_in = true;
    } elseif(isset(User::$init->info) && !empty(User::$init->info)) {
        $is_logged_in = true;
    }
}

// Yöntem 2: User::user_id() metodu varsa
if(!$is_logged_in && class_exists('User') && method_exists('User', 'user_id')) {
    $uid = @User::user_id();
    if($uid && $uid > 0) $is_logged_in = true;
}

// Yöntem 3: Session kontrolü (fallback)
if(!$is_logged_in && !empty($_SESSION['user_id'])) {
    $is_logged_in = true;
}

// Yönlendirme
if($is_logged_in) {
    // Login olmuş → müşteri paneli dashboard
    include __DIR__ . DS . 'ac-dashboard.php';
} else {
    // Login OLMAMIŞ → public landing page
    // master-content yerine theme'in kendi index.php'si tek başına çalışmalı
    $master_content_none = true;

    // Public sayfa için head + header
    ?><!DOCTYPE html>
    <html lang="<?php echo class_exists('Hook') ? ___("package/code") : 'tr'; ?>">
    <head>
        <?php
            $hoptions = ['page' => 'index'];
            include __DIR__ . DS . 'inc' . DS . 'main-head.php';
        ?>
    </head>
    <body id="cdg-public">
        <?php
            // Header
            $header_type = isset($theme_settings['header_type']) ? $theme_settings['header_type'] : 1;
            $hf = __DIR__ . DS . 'inc' . DS . 'main-header-' . $header_type . '.php';
            if(file_exists($hf)) include $hf;
            elseif(file_exists(__DIR__ . DS . 'inc' . DS . 'main-header.php')) include __DIR__ . DS . 'inc' . DS . 'main-header.php';
            elseif(file_exists(__DIR__ . DS . 'inc' . DS . 'main-header-1.php')) include __DIR__ . DS . 'inc' . DS . 'main-header-1.php';

            // Landing page içeriği
            include __DIR__ . DS . 'index.php';

            // Footer
            $ff = __DIR__ . DS . 'inc' . DS . 'main-footer.php';
            if(file_exists($ff)) include $ff;
        ?>
    </body>
    </html>
    <?php
}
