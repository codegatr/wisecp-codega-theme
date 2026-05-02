<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'Domain Onerici';
$svc_subtitle = 'AI destekli domain isim onerici - mukemmel ismi bulun';
$svc_icon = 'magic';
$svc_color = '#7c3aed';
$svc_gradient = 'linear-gradient(135deg,#7c3aed,#a78bfa)';
$svc_breadcrumb = 'Domain Onerici';
$svc_features = [
    ['icon' => 'cpu-fill', 'title' => 'AI Destekli', 'desc' => 'Yapay zeka ile sirketiniz icin akilda kalici isimler.'],
    ['icon' => 'search', 'title' => 'Anlik Sorgu', 'desc' => 'Onerilerin musaitligi gercek zamanli olarak kontrol edilir.'],
    ['icon' => 'collection-fill', 'title' => 'Cesitli Uzantilar', 'desc' => '.com, .net, .org, .com.tr, .io, .co gibi 100+ uzanti.'],
    ['icon' => 'star-fill', 'title' => 'Akilli Kombinasyon', 'desc' => 'Anahtar kelimelerinize gore yaratici domain kombinasyonlari.'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Mukemmel Domain Adini Bulmak Hicbir Zaman Bu Kadar Kolay Olmamisti</h3>
<p>Sirketiniz, projeniz veya bir fikriniz icin akilda kalici, kisa ve etkili bir domain mi ariyorsunuz? Akilli onerici aracimiz, anahtar kelimelerinize gore size yaratici alternatifler sunar ve hangi domainlerin musait oldugunu aninda gosterir.</p>
<p>Tek yapmaniz gereken sektorunuze veya is alaninize uygun birkac kelime girmek - sistem size onlarca yaratici domain onerisi sunar. Onerileri filtreleme, fiyatlari karsilastirma ve favorilerinize ekleme gibi ozellikler de mevcuttur.</p>
';
$svc_cta_text = 'Domain Sorgulama';

include __DIR__ . '/../inc/service-page-template.php';
