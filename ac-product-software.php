<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Software Lisans Ürün Yönetimi
 * Generic template + lisans özel kartlar (domain değiştir + reissue)
 */

$cdg_pd_kind      = 'software';
$cdg_pd_title     = 'Lisans Yönetimi';
$cdg_pd_icon      = 'puzzle-fill';
$cdg_pd_color     = '#8b5cf6';
$cdg_pd_back_slug = 'softwares';

include __DIR__ . DS . 'inc' . DS . 'ac-product-detail-template.php';

// Software'e özel: domain değiştir + reissue
include __DIR__ . DS . 'inc' . DS . 'ac-software-extras.php';
