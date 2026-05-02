<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
$cdg_pd_kind = 'references';
$cdg_pd_label = 'Referanslar';
$cdg_pd_icon = 'star-fill';
$cdg_pd_list_link = (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('references') : '/references');
include __DIR__ . DS . 'inc' . DS . 'page-detail-template.php';
