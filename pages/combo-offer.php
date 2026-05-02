<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'Avantajli Paketler';
$svc_subtitle = 'Domain + Hosting + SSL paket teklifleri ile tasarruf edin';
$svc_icon = 'gift-fill';
$svc_color = '#ec4899';
$svc_gradient = 'linear-gradient(135deg,#db2777,#ec4899)';
$svc_breadcrumb = 'Avantajli Paketler';
$svc_features = [
    ['icon' => 'piggy-bank-fill', 'title' => 'Buyuk Tasarruf', 'desc' => 'Tekil hizmetlere kiyasla %40\'a varan indirimli paketler.'],
    ['icon' => 'lightning-charge-fill', 'title' => 'Tek Tikla Kurulum', 'desc' => 'Domain, hosting, SSL hepsi birlikte aktive olur.'],
    ['icon' => 'gem', 'title' => 'Premium Ozellikler', 'desc' => 'Paketlerde otomatik yedekleme, CDN ve premium destek dahildir.'],
    ['icon' => 'shield-check', 'title' => '30 Gun Iade', 'desc' => 'Memnun kalmazsaniz 30 gun icinde tam iade garantisi.'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Tek Bir Pakette Her Sey</h3>
<p>Domain, hosting, SSL sertifikasi, e-posta ve daha fazlasini ayri ayri satin almak yerine bizim hazirladigimiz avantajli paketleri tercih edin. Hem zamanidan hem de butcenizden tasarruf edin.</p>
<p>Yeni baslayanlar icin "Starter" paketi, kucuk isletmeler icin "Business", e-ticaret siteleri icin "E-Commerce" paketi gibi farkli ihtiyaclara uygun cozumler sunuyoruz. Her pakette ucretsiz domain (.com, .net, .org), unlimited SSL ve 7/24 Turkce teknik destek dahildir.</p>
';
$svc_cta_text = 'Paketleri Incele';

include __DIR__ . '/../inc/service-page-template.php';
