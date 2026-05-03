<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$cdg_list_kind     = 'sms';
$cdg_list_title    = 'SMS Paketlerim';
$cdg_list_subtitle = 'Toplu SMS paketlerinizi yönetin, kontör durumunuzu takip edin, yeni paket alın.';
$cdg_list_icon     = 'chat-square-text-fill';
$cdg_list_color    = '#00D3E5';
$cdg_list_shop_slug = 'products';
$cdg_list_shop_param = ['sms'];
include __DIR__ . DS . 'inc' . DS . 'ac-product-list-template.php';
