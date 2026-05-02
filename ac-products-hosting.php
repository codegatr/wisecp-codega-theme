<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$cdg_list_kind     = 'hosting';
$cdg_list_title    = 'Hosting Hizmetlerim';
$cdg_list_subtitle = 'Web hosting paketlerinizi yönetin, kontrol panellerine erişin, yenileme ve yükseltme işlemleri yapın.';
$cdg_list_icon     = 'hdd-network-fill';
$cdg_list_color    = '#10b981';
$cdg_list_shop_slug = 'products';
$cdg_list_shop_param = ['hosting'];
include __DIR__ . DS . 'inc' . DS . 'ac-product-list-template.php';
