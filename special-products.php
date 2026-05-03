<?php
/**
 * Ozel Paketler (Kategori bazli ozel urunler)
 * WiseCP'den gercek paketleri ceker, her birinin buy_link runtime field'ini kullanir.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$cdg_pt = [
    'type'       => 'special',
    'page_title' => 'Özel Paketler',
    'page_icon'  => 'bi-stars',
    'hero_title' => 'Özel Çözüm Paketlerimiz',
    'hero_desc'  => 'Standart hosting/sunucu paketleri dışında, ihtiyaca özel hazırlanmış çözüm paketlerimiz.',
    'color'      => '#f59e0b',
    'singular'   => 'Özel Paket',
];

include __DIR__ . '/inc/cdg-product-list-template.php';
