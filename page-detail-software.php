<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
$cdg_pd_kind = 'software';
$cdg_pd_label = 'Yazilim';
$cdg_pd_icon = 'cpu';
$cdg_pd_list_link = '#';
include __DIR__ . DS . 'inc' . DS . 'page-detail-template.php';
