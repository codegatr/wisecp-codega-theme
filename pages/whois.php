<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'Whois Sorgulama';
$svc_subtitle = 'Domain sahibi, kayit tarihi ve nameserver bilgilerini sorgulayin';
$svc_icon = 'search';
$svc_color = '#0891b2';
$svc_gradient = 'linear-gradient(135deg,#0e7490,#0891b2)';
$svc_breadcrumb = 'Whois Sorgu';
$svc_features = [
    ['icon' => 'person-vcard-fill', 'title' => 'Sahip Bilgileri', 'desc' => 'Domain kayit sahibi, e-posta, telefon ve adres bilgileri.'],
    ['icon' => 'calendar-event-fill', 'title' => 'Kayit Tarihleri', 'desc' => 'Kayit, son kullanma ve guncelleme tarihleri.'],
    ['icon' => 'hdd-network-fill', 'title' => 'Nameserver Bilgisi', 'desc' => 'Domainin hangi DNS sunucularina yonlendirildigi.'],
    ['icon' => 'building-fill', 'title' => 'Registrar Bilgisi', 'desc' => 'Domainin kayitli oldugu kayit kurumu (registrar).'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Domain Hakkinda Detayli Bilgi</h3>
<p>Bir domainin sahibini, kayit tarihini, ne zaman sona erecegini ve hangi sunucularda barindirildigini ogrenmek istiyorsaniz Whois sorgu aracimiz tam size gore. Tum populer uzantilar (.com, .net, .org, .com.tr, .io vs.) icin gercek zamanli sorgulama yapabilirsiniz.</p>
<p><strong>Not:</strong> KVKK ve GDPR kapsaminda bireysel kayitlarda sahibin kisisel bilgileri "Whois Privacy" ile gizlenmis olabilir. Bu durumda sadece registrar (kayit kurumu) bilgileri gorunur. Kurumsal domainlerin Whois bilgileri ise genellikle aciktir.</p>
';
$svc_cta_text = 'Domain Sorgu';

include __DIR__ . '/../inc/service-page-template.php';
