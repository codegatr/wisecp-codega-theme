<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
$cdg_list_kind = 'news';
$cdg_list_label = 'Haberler';
$cdg_list_icon = 'newspaper';
$cdg_list_color = '#7c3aed';
$cdg_list_gradient = 'linear-gradient(135deg,#7c3aed,#a78bfa)';
include __DIR__ . DS . 'inc' . DS . 'page-list-template.php';
