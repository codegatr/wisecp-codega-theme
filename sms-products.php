<?php
/**
 * SMS Paketleri
 * WiseCP'den gercek paketleri ceker, her birinin buy_link runtime field'ini kullanir.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$cdg_pt = [
    'type'       => 'sms',
    'page_title' => 'SMS Paketleri',
    'page_icon'  => 'bi-chat-dots-fill',
    'hero_title' => 'Toplu SMS Paketlerimiz',
    'hero_desc'  => 'Müşterilerinize hızlı ulaşın. Kampanya, bilgilendirme ve OTP SMS\'leri için ekonomik paketlerimizi keşfedin.',
    'color'      => '#10b981',
    'singular'   => 'SMS',
];

include __DIR__ . '/inc/cdg-product-list-template.php';
