<?php
/**
 * Sunucu (Server/VPS/Dedicated) Paketleri
 * WiseCP'den gercek paketleri ceker, her birinin buy_link runtime field'ini kullanir.
 * Tikla -> order-steps-server -> cycle/option secimi -> sepete ekle -> odeme
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$cdg_pt = [
    'type'       => 'server',
    'page_title' => 'Sunucu Paketleri',
    'page_icon'  => 'bi-hdd-rack-fill',
    'hero_title' => 'Profesyonel Sunucu Çözümleri',
    'hero_desc'  => 'VPS ve Dedicated sunucu paketlerimiz ile yüksek performans ve tam kontrol. Kendi sunucunuza dakikalar içinde sahip olun.',
    'color'      => '#7c3aed',
    'singular'   => 'Sunucu',
];

include __DIR__ . '/inc/cdg-product-list-template.php';
