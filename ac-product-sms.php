<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - SMS Hizmet Yönetimi
 * Generic template + SMS modülü (gönder, origin, rehber, kara liste, raporlar, kredi)
 */

$cdg_pd_kind      = 'sms';
$cdg_pd_title     = 'SMS Yönetimi';
$cdg_pd_icon      = 'chat-dots-fill';
$cdg_pd_color     = '#06b6d4';
$cdg_pd_back_slug = 'products-sms';

include __DIR__ . DS . 'inc' . DS . 'ac-product-detail-template.php';

// SMS'e özel: gönderme paneli + origin + rehber (v3.3.1)
include __DIR__ . DS . 'inc' . DS . 'ac-sms-extras.php';
