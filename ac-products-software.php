<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$cdg_list_kind     = 'software';
$cdg_list_title    = 'Yazılım Lisanslarım';
$cdg_list_subtitle = 'Aktif yazılım lisanslarınızı yönetin, lisans anahtarlarına erişin, yeni lisans satın alın.';
$cdg_list_icon     = 'box-seam-fill';
$cdg_list_color    = '#8b5cf6';
$cdg_list_shop_slug = 'softwares';
$cdg_list_shop_param = [];
include __DIR__ . DS . 'inc' . DS . 'ac-product-list-template.php';
