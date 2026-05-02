<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Hosting Ürün Yönetimi
 * Generic template + hosting'e özel kartlar (cPanel, şifre değiştir, email yönetimi)
 */

$cdg_pd_kind      = 'hosting';
$cdg_pd_title     = 'Hosting Yönetimi';
$cdg_pd_icon      = 'hdd-network-fill';
$cdg_pd_color     = '#10b981';
$cdg_pd_back_slug = 'products-hosting';

// Generic template (Özet, Ödeme, Detaylar, İptal vs)
include __DIR__ . DS . 'inc' . DS . 'ac-product-detail-template.php';

// Hosting'e özel kartlar (cPanel/Plesk panel girişi + şifre değiştirme + email butonu)
include __DIR__ . DS . 'inc' . DS . 'ac-hosting-extras.php';

// Email yönetim modal'ı (manage-email-account destekleniyorsa)
include __DIR__ . DS . 'inc' . DS . 'ac-hosting-emails.php';
