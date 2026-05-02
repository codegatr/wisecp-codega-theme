<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'SSL Sertifikasi';
$svc_subtitle = 'Sitenizi HTTPS ile guvenli hale getirin, SEO\'da yukselin';
$svc_icon = 'shield-lock-fill';
$svc_color = '#059669';
$svc_gradient = 'linear-gradient(135deg,#047857,#10b981)';
$svc_breadcrumb = 'SSL Sertifikasi';
$svc_features = [
    ['icon' => 'lock-fill', 'title' => '256-bit Sifreleme', 'desc' => 'Banka duzeyinde guclu sifreleme ile veri trafigi korunur.'],
    ['icon' => 'arrow-up-right-circle-fill', 'title' => 'SEO Avantaji', 'desc' => 'Google HTTPS sitelere oncelik verir, siralamada yukselin.'],
    ['icon' => 'patch-check-fill', 'title' => 'Yesil Kilit', 'desc' => 'Tarayicida guven veren yesil kilit ile musteri guveni artar.'],
    ['icon' => 'lightning-fill', 'title' => 'Hizli Kurulum', 'desc' => 'Otomatik dogrulama ile dakikalar icinde aktif olur.'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Modern Web\'in Olmazsa Olmazi</h3>
<p>2025 itibariyle SSL sertifikasi olmayan siteler tarayicilarda <em>"Guvensiz"</em> uyarisi alir ve Google aramalarinda asagi siralanir. Ayrica e-ticaret yapan siteler icin <strong>yasal bir zorunluluktur</strong>. SSL ile sitenizin tum trafigi sifrelenir ve hassas veriler (sifreler, kart bilgileri vs.) korunmus olur.</p>
<p>Domain Validation (DV), Organization Validation (OV) ve Extended Validation (EV) sertifikalari arasinda ihtiyaciniza uygun olani secebilirsiniz. Single domain, wildcard ve multi-domain sertifikalari ile esnek cozumler sunuyoruz.</p>
';

include __DIR__ . '/../inc/service-page-template.php';
