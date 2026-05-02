<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
$cdg_list_kind = 'references';
$cdg_list_label = 'Referanslar';
$cdg_list_icon = 'star-fill';
$cdg_list_color = '#f59e0b';
$cdg_list_gradient = 'linear-gradient(135deg,#f59e0b,#fbbf24)';
include __DIR__ . DS . 'inc' . DS . 'page-list-template.php';
