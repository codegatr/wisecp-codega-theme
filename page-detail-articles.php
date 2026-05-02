<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$cdg_pd_kind = 'articles';
$cdg_pd_label = 'Makaleler';
$cdg_pd_icon = 'file-earmark-richtext';
$cdg_pd_list_link = (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('articles') : '/articles');
include __DIR__ . DS . 'inc' . DS . 'page-detail-template.php';
