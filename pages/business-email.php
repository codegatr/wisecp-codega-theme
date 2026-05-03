<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$svc_title = 'Kurumsal E-posta';
$svc_subtitle = 'Kendi domaininizle profesyonel e-posta hizmeti';
$svc_icon = 'envelope-at-fill';
$svc_color = '#2E3B4E';
$svc_gradient = 'linear-gradient(135deg,#2E3B4E,#00D3E5)';
$svc_breadcrumb = 'Kurumsal E-posta';
$svc_features = [
    ['icon' => 'shield-fill-check', 'title' => 'Yuksek Guvenlik', 'desc' => 'SPF, DKIM, DMARC ve TLS sifreleme ile e-postalariniz korunur.'],
    ['icon' => 'cloud-upload-fill', 'title' => 'Genis Saklama Alani', 'desc' => 'Hesap basina 10 GB ile 50 GB arasi disk alani secenekleri.'],
    ['icon' => 'phone-fill', 'title' => 'Mobil Uyumlu', 'desc' => 'IMAP, POP3, SMTP ve Webmail destegi - tum cihazlardan erisim.'],
    ['icon' => 'shield-slash-fill', 'title' => 'Spam Koruma', 'desc' => 'Akilli filtreler ile spam ve oltalama saldirilarina karsi koruma.'],
];
$svc_description = '
<h3 style="color:#0f172a;font-size:18px;font-weight:800;margin:0 0 12px;">Profesyonel Iletisim Icin Profesyonel E-posta</h3>
<p>Kendi alan adinizla (ornegin: <strong>info@sirketiniz.com</strong>) e-posta hesaplari olusturun, musterilerinizle ve is ortaklarinizla profesyonel bir iletisim kurun. Gmail veya Hotmail gibi ucretsiz servisler yerine kendi marka kimliginizi yansitan e-posta adresleri ile guven kazanin.</p>
<p>Webmail panelimiz, Outlook, Thunderbird gibi e-posta istemcileri ve mobil cihazlarinizla tam uyumlu calisir. Onemli e-postalariniz icin otomatik yedekleme ve felaket kurtarma cozumleri de paket icindedir.</p>
';

include __DIR__ . '/../inc/service-page-template.php';
