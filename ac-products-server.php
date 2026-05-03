<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$cdg_list_kind     = 'server';
$cdg_list_title    = 'Sunucularım';
$cdg_list_subtitle = 'VPS ve dedicated sunucularınızı yönetin, yeniden başlatın, performans takibi yapın.';
$cdg_list_icon     = 'server';
$cdg_list_color    = '#2E3B4E';
$cdg_list_shop_slug = 'products';
$cdg_list_shop_param = ['server'];
include __DIR__ . DS . 'inc' . DS . 'ac-product-list-template.php';
