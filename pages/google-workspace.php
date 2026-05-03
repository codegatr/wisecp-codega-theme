<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'Google Workspace';
$svc_subtitle = 'Gmail, Drive, Docs ve daha fazlasi - kurumsal cozum';
$svc_icon = 'google';
$svc_color = '#4285F4';
$svc_gradient = 'linear-gradient(135deg,#4285F4,#0f9d58)';
$svc_breadcrumb = 'Google Workspace';
$svc_features = [
    ['icon' => 'envelope-paper-fill', 'title' => 'Gmail Kurumsal', 'desc' => 'Kendi domaininizle Gmail (info@sirketiniz.com).'],
    ['icon' => 'cloud-fill', 'title' => 'Google Drive', 'desc' => 'Kullanici basina 30 GB ila 5 TB arasi bulut depolama.'],
    ['icon' => 'people-fill', 'title' => 'Meet & Calendar', 'desc' => 'Video toplantilar, takvim paylasimi, ekip is birligi.'],
    ['icon' => 'file-earmark-text-fill', 'title' => 'Docs, Sheets, Slides', 'desc' => 'Esz amanli is birligi ile dokuman duzenleme.'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Kucuk Isletmelerden Buyuk Sirketlere</h3>
<p>Google Workspace (eski adiyla G Suite), modern is hayatinin tum ihtiyaclarini karsilayan eksiksiz bir uretkenlik paketidir. Gmail, Drive, Docs, Sheets, Meet, Calendar ve daha bircok arac tek bir abonelikle sunulur.</p>
<p>Yetkili Google partneri olarak, hesap kurulumu, domain yonlendirmesi, kullanici yonetimi ve teknik destegi Turkce olarak saglariz. Business Starter, Standard, Plus ve Enterprise paketleri için en uygun fiyatlari sunuyoruz.</p>
';
$svc_cta_text = 'Workspace Paketleri';

include __DIR__ . '/../inc/service-page-template.php';
