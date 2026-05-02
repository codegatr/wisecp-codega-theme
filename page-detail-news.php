<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$cdg_pd_kind = 'news';
$cdg_pd_label = 'Haberler';
$cdg_pd_icon = 'newspaper';
$cdg_pd_list_link = (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('news') : '/news');
include __DIR__ . DS . 'inc' . DS . 'page-detail-template.php';
