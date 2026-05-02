<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'CodeGuard Site Yedekleme';
$svc_subtitle = 'Otomatik web sitesi ve veritabani yedekleme servisi';
$svc_icon = 'shield-fill-plus';
$svc_color = '#10b981';
$svc_gradient = 'linear-gradient(135deg,#059669,#10b981)';
$svc_breadcrumb = 'CodeGuard';
$svc_features = [
    ['icon' => 'arrow-clockwise', 'title' => 'Otomatik Yedek', 'desc' => 'Gunluk, haftalik veya aylik otomatik yedekleme planlari.'],
    ['icon' => 'cloud-arrow-down-fill', 'title' => 'Tek Tikla Geri Yukleme', 'desc' => 'Herhangi bir tarihteki yedeginize hizlica donus yapin.'],
    ['icon' => 'eye-fill', 'title' => 'Degisiklik Algilama', 'desc' => 'Sitenizdeki suphe uyandiran degisiklikler aninda bildirilir.'],
    ['icon' => 'database-fill-check', 'title' => 'DB + Dosya', 'desc' => 'Hem MySQL/MariaDB hem de tum site dosyalarinin yedegi alinir.'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Sitenizin Verileri Cok Degerli</h3>
<p>Bir sunucu cokmesi, hack saldirisi veya yanlis bir tiklama tum sitenizi yok edebilir. CodeGuard, sitenizi otomatik olarak yedekleyerek bu tur felaketlere karsi tam koruma saglar. Hem dosyalariniz hem de veritabani gunluk olarak yedeklenir, istediginiz tarihteki veriye geri donebilirsiniz.</p>
<p>WordPress, Joomla, Drupal, Magento gibi tum populer CMS sistemleri ile uyumlu calisir. Yedekleriniz harici sunucularimizda guvenle saklanir ve site sunucunuz cokse bile yedeklerinize erisebilirsiniz.</p>
';

include __DIR__ . '/../inc/service-page-template.php';
