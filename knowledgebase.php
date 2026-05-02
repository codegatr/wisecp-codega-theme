<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

$contact_url = cdg_link('contact');
$hosting_url = cdg_link('products', ['hosting']);
$domain_url  = cdg_link('domain');
$soft_url    = cdg_link('softwares');

// === HOSTING + YAZILIM SSS ===
$kb_categories = [
    [
        'id' => 'hosting',
        'icon' => 'bi-hdd-network-fill',
        'color' => '#1e40af',
        'name' => 'Hosting Hizmetleri',
        'desc' => 'Web hosting paketleri, kurulum, performans ve sorun giderme',
        'questions' => [
            ['q' => 'Hosting nedir, ne ise yarar?', 'a' => 'Hosting (barindirma), web sitenizin internet uzerinde 7/24 erisilebilir olmasini saglayan hizmettir. Web sitenizin dosyalarini bizim sunucularimiza yukluyorsunuz, biz de dunya genelinden gelen ziyaretcileri sitenize yonlendiriyoruz. Hosting olmadan web sitesi calistiramazsiniz.'],
            ['q' => 'NVMe SSD ile SATA SSD arasindaki fark nedir?', 'a' => 'NVMe SSD\'ler PCIe arabirimi uzerinden direkt CPU\'ya baglanir, SATA SSD\'lere gore <strong>5-7 kat daha hizli</strong> okuma/yazma yapar. Bizim sunucularimizda %100 NVMe SSD kullaniyoruz, bu sayede sitenin yuklenme suresi onemli olcude azaliyor.'],
            ['q' => 'LiteSpeed Enterprise nedir? Apache\'den fark?', 'a' => 'LiteSpeed Enterprise, Apache\'nin yerine gecen yuksek performansli web server\'dir. <strong>9 kat daha hizli</strong>, daha az kaynak tuketir. Ozellikle WordPress, Magento gibi populer CMS\'ler icin ozel optimizasyonlari vardir. Tum hosting paketlerimizde standart olarak gelir.'],
            ['q' => 'Mevcut sitemi CODEGA\'ya nasil tasirim?', 'a' => 'Hosting paketinizi aldiktan sonra panel uzerinden "Tasima Talebi" olusturun. Mevcut hosting bilgilerinizi (cPanel kullanici adi/sifre veya FTP+veritabani) paylasin, uzman ekibimiz veri kaybi olmadan sitenizi tasir. <strong>5 sitee kadar UCRETSIZ tasiriz</strong>. Tasima sirasinda sitenizin kesintisi 5-15 dakika arasi olur.'],
            ['q' => 'Hangi hosting paketini secmeliyim?', 'a' => 'Tek bir kucuk site icin <strong>Linux Hosting 1 (150₺/yil)</strong>, hobi projeleriniz icin <strong>Linux Hosting 2 (289₺/yil)</strong>, kurumsal siteler icin <strong>Profesyonel 1 (450₺/yil)</strong>, yuksek trafikli siteler icin <strong>Profesyonel 2 (750₺/yil)</strong> oneririz. Eminseniz bize danisin, ihtiyaciniza gore yonlendirelim.'],
            ['q' => 'Hosting paketim doldugunda ne olur?', 'a' => 'Disk doldugunda yeni dosya yuklenememesi disinda site calismaya devam eder, ancak veritabani yazma islemleri (yorum, siparis vb.) hata verebilir. Panel uzerinden anlik dolulugu gorebilir, doldugunda 1 tikla ust pakete gecebilirsiniz.'],
            ['q' => 'cPanel\'e nasil giris yaparim?', 'a' => 'Hosting siparisi tamamlandigi an, e-postaniza cPanel giris bilgileri gelir. <code>https://cpanel.yourdomain.com</code> veya panel uzerinden "Tek Tikla Giris" butonu ile sifresiz girebilirsiniz.'],
            ['q' => 'Domain ile hosting ayni firmadan olmali mi?', 'a' => 'Hayir, ayni firmadan olmasi zorunlu degil. Ama ayni firmadan alinmasi yonetimi kolaylastirir, DNS ayarlari otomatik yapilir, fatura tek yere gelir. CODEGA\'dan hosting alir, domain\'iniz baska firmadaysa name server (NS) bilgilerini guncelleyerek bizimle calisabilirsiniz.'],
        ],
    ],
    [
        'id' => 'domain',
        'icon' => 'bi-globe2',
        'color' => '#10b981',
        'name' => 'Alan Adi (Domain)',
        'desc' => 'Domain alimi, transferi, NS ayarlari ve uzantilar',
        'questions' => [
            ['q' => 'Hangi domain uzantisini secmeliyim?', 'a' => '<strong>.com.tr</strong> Turkiye\'deki sirketler icin en prestijli secimdir, vergi levhasi gerekir. <strong>.com</strong> en yaygin, uluslararasi tanin. <strong>.tr</strong> 2022\'den beri herkes alabilir, kisa ve hatirlanir. <strong>.net/.org</strong> teknolojik veya kurumsal. Marka korumasi icin birden fazla uzanti almak iyi bir stratejidir.'],
            ['q' => 'Domain transferi ucretsiz mi?', 'a' => 'Evet! .com .net .org gibi gTLD uzantilarinda transfer ucretsizdir, ayrica 1 yil suresine ekleme yapilir. Yani $14\'a 1 yillik domain alirsaniz, transfer sonrasi sureniz 2 yila ciker. .com.tr gibi ulke uzantilarinda transfer islemi tarifeye gore degisir.'],
            ['q' => 'Name Server (NS) nedir, nasil degistiririm?', 'a' => 'Name Server, domain\'inizin hangi sunucuya yonlendirilecegini belirleyen DNS sunucusudur. CODEGA\'nin NS\'leri: <code>ns1.codega.com.tr</code> ve <code>ns2.codega.com.tr</code>. Domain\'inizin alindigi panelden NS bolumune girip bunlari yazin. Degisiklik 1-24 saat icinde aktif olur.'],
            ['q' => 'Domain whois bilgilerimi nasil gizlerim?', 'a' => 'Whois Gizliligi (Privacy Protection) ile iletisim bilgileriniz halka acik whois sorgularinda gizlenir. Bu hizmet .com .net .org gibi gTLD\'lerde mevcuttur, .com.tr gibi ccTLD\'lerde whois zaten kismi gizlidir. Panel uzerinden bu hizmeti aktif edebilirsiniz.'],
            ['q' => 'Domain kayit suresinin bitmesine ne kadar kaldi?', 'a' => 'Panel > Domain Yonetimi sekmesinde tum domain\'lerinizin sona erme tarihlerini gorebilirsiniz. Sona ermesine 30 gun kala otomatik e-posta uyarisi gelir. Sureyi bitmeden uzatmak onemli, aksi takdirde baska biri tarafindan satin alinabilir.'],
            ['q' => 'Domain alirken nelere dikkat etmeliyim?', 'a' => '1) <strong>Marka uyusmazligi</strong> olmasin (TPMK\'da kontrol edin), 2) Kisa ve akilda kalici olsun, 3) Yazimi kolay olsun, 4) Tire (-) icermesin mumkunse, 5) Anlami acik olsun, 6) Birden fazla uzanti rezerve edin (rakip almasin).'],
        ],
    ],
    [
        'id' => 'security',
        'icon' => 'bi-shield-shaded',
        'color' => '#ef4444',
        'name' => 'Guvenlik ve SSL',
        'desc' => 'SSL sertifikalari, gunbencel saldiri korumasi ve veri guvenligi',
        'questions' => [
            ['q' => 'SSL sertifikasi nedir, neden gerekli?', 'a' => 'SSL (Secure Sockets Layer), sitenize gelen ziyaretcilerin verilerini sifreleyerek 3. kisilerden korur. Tarayicida <strong>https://</strong> ve yesil kilit ikonu olarak gorunur. Google SEO\'da SSL\'siz siteleri "Guvenli Degil" olarak isaretler ve siralamasi dusurur. Tum hosting paketlerimizde Let\'s Encrypt SSL UCRETSIZdir.'],
            ['q' => 'Let\'s Encrypt SSL ile ucretli SSL farki?', 'a' => '<strong>Let\'s Encrypt</strong>: Ucretsiz, otomatik yenilenir, 90 gun gecerli, alan dogrulama (DV) yapar. <strong>Ucretli SSL</strong>: Yillik 100-2000 ₺ arasi, 1-2 yil gecerli, sirket dogrulama (OV) veya genis dogrulama (EV) yapar, sirket logosu gosterir. Kurumsal/e-ticaret siteleri icin ucretli SSL\'in daha guvenilir oldugu kabul edilir.'],
            ['q' => 'DDoS saldirisi nedir, nasil korunuruz?', 'a' => 'DDoS (Distributed Denial of Service), bir siteye milyonlarca sahte istek gondererek calismaz hale getirme saldirisidir. Bizim altyapida <strong>Cloudflare DDoS koruma</strong> ve sunucu seviyesinde <strong>Imunify360 firewall</strong> kullaniliyor. Bu sayede 847.000+ saldiri her ay otomatik olarak engelleniyor.'],
            ['q' => 'Sitemde malware var ne yapmaliyim?', 'a' => 'Imunify360 her hosting paketinde aktiftir ve dakikalik tarama yapar. Bir malware tespit edilirse panel uzerinden bildirim gelir, dosyalar otomatik karantinaya alinir. Manuel temizlik isterseniz destek talebi olusturun, ekibimiz ucretsiz inceler.'],
            ['q' => '2FA (Iki Faktorlu Dogrulama) nasil aktif edilir?', 'a' => 'Hesabim > Guvenlik bolumune girin, "Iki Faktorlu Dogrulama" sekmesinden Google Authenticator veya Authy gibi bir uygulama ile QR kod tarayin. Boylece sifrenize ek olarak 6 haneli kod istenir, hesabinizi koruma alti­na almis olursunuz.'],
            ['q' => 'KVKK kapsaminda verilerim guvende mi?', 'a' => 'Evet! CODEGA <strong>KVKK uyumludur</strong>, sunucularimiz Turkiye, Almanya ve Hollanda\'da Tier-3 veri merkezlerinde bulunur. Tum siteler arasinda veri sizmasi imkansizdir, dosyalariniz sifrelidir, audit log\'lar tutulur ve isteginize gore tum verileriniz silinebilir.'],
        ],
    ],
    [
        'id' => 'wordpress',
        'icon' => 'bi-wordpress',
        'color' => '#21759b',
        'name' => 'WordPress',
        'desc' => 'WordPress kurulum, eklenti, hizlandirma ve sorun giderme',
        'questions' => [
            ['q' => 'WordPress\'i tek tikla nasil kurulur?', 'a' => 'cPanel > Softaculous Apps Installer\'a girin, WordPress\'i secip "Install Now" butonuna basin. Site adi, admin kullanici adi ve sifre belirleyin, dakikalar icinde kurulum tamamlanir. Veritabani otomatik olusturulur, ekstra is yok.'],
            ['q' => 'WordPress sitemi nasil hizlandiririm?', 'a' => '1) <strong>LiteSpeed Cache</strong> eklentisini kurun (bizim sunucularda otomatik hizli), 2) Resimleri WebP formatina cevirin, 3) Gereksiz eklentileri silin, 4) Tema seciminizi optimize edin, 5) CDN (Cloudflare) kullanin. Ekibimiz ucretsiz performans audit yapabilir.'],
            ['q' => 'Hangi WordPress eklentilerini onerirsiniz?', 'a' => 'Onerilenler: <strong>LiteSpeed Cache</strong> (hizlandirma), <strong>Wordfence</strong> (guvenlik), <strong>Yoast SEO</strong> (SEO), <strong>UpdraftPlus</strong> (yedekleme), <strong>WPForms</strong> (form), <strong>Elementor</strong> (sayfa kurucu). 30+ eklenti gerektigi durumlarda hosting paketinizi yukseltin.'],
            ['q' => 'Beyaz ekran (WSOD) hatasini nasil cozerim?', 'a' => 'Beyaz ekran hatasi genelde bir eklenti veya tema cakismasindan kaynaklanir. cPanel > File Manager > <code>wp-content/plugins</code> klasorunu yeniden adlandirin (<code>plugins-bak</code>). Site acilirsa eklentilerden biri sorunlu, teker teker aktif edip bulun.'],
            ['q' => 'WordPress otomatik guncelleme nasil aktif edilir?', 'a' => 'wp-config.php\'a su satiri ekleyin: <code>define("WP_AUTO_UPDATE_CORE", true);</code>. Boylece WordPress core, eklenti ve temalar otomatik guncellenir. Onerilen yontem: Onemli guncellemelerden once yedek alin, staging\'de test edin.'],
        ],
    ],
    [
        'id' => 'email',
        'icon' => 'bi-envelope-fill',
        'color' => '#f59e0b',
        'name' => 'E-posta Hizmetleri',
        'desc' => 'E-posta hesabi olusturma, kurulum ve sorun giderme',
        'questions' => [
            ['q' => '@domainim.com e-posta hesabini nasil olustururum?', 'a' => 'cPanel > E-posta Hesaplari sekmesine girin, "Yeni Hesap Olustur" tikla­yip kullanici adi ve sifreyi belirleyin. <code>info@domainim.com</code>, <code>destek@domainim.com</code> gibi sinirsiz hesap olusturabilirsiniz (paket limitine gore).'],
            ['q' => 'Outlook/Gmail\'a e-postami nasil baglarim?', 'a' => 'Sunucu bilgileri: <strong>IMAP</strong>: imap.codega.com.tr (port 993, SSL), <strong>SMTP</strong>: smtp.codega.com.tr (port 465, SSL). Outlook/Gmail/Apple Mail\'da "Manuel Kurulum" secin, bu bilgileri girin. cPanel\'den otomatik konfigurasyon dosyasi da indirebilirsiniz.'],
            ['q' => 'E-posta gonderirken spam\'a dusuyor ne yapmaliyim?', 'a' => '1) <strong>SPF kaydi</strong> ekleyin (DNS sekmesinde), 2) <strong>DKIM</strong> aktif edin (cPanel > E-posta > Email Authentication), 3) <strong>DMARC</strong> kaydi olusturun, 4) E-posta icerigini optimize edin (resim/link orani), 5) Mail-tester.com\'da test edin. Bu islemleri biz de ucretsiz yapariz, talep edebilirsiniz.'],
            ['q' => 'Toplu e-posta (newsletter) gonderebilir miyim?', 'a' => 'Hosting paketleri kucuk olcekli (gunluk 100-500 mail) icin uygundur. Toplu mail (1000+) icin <strong>SendGrid, Mailgun, Amazon SES</strong> gibi profesyonel SMTP servislerini oneririz. Bunlar deliverability\'yi artirir, IP itibarinizi korur.'],
            ['q' => 'E-postalar dolu, sunucu yer kalmadi ne yapmaliyim?', 'a' => 'Roundcube webmail\'e girin, Trash/Spam klasorlerini bosaltin. Eski yuksek boyutlu mailleri yerel bilgisayara IMAP ile indirip sunucudan silin. Ya da paketinizi yukseltin, ek disk alin.'],
        ],
    ],
    [
        'id' => 'erp-software',
        'icon' => 'bi-building-fill',
        'color' => '#8b5cf6',
        'name' => 'ERP & Yazilim Hizmetleri',
        'desc' => 'CodeGa ERP, ozel yazilim, entegrasyonlar ve danismanlik',
        'questions' => [
            ['q' => 'CodeGa ERP nedir, hangi modulleri var?', 'a' => '<strong>CodeGa ERP</strong>, sirket operasyonlarinizi tek panelden yoneten bulut tabanli kurumsal kaynak planlama sistemidir. <strong>96+ entegre modul</strong>: Cari hesaplar, Stok, Faturalama, Banka, Muhasebe, IK, Bordro, PDKS (GPS), Uretim, CRM, Satis, Satin Alma, Sevkiyat, E-Fatura, Cari Risk Analizi, Egitim Katalogu ve daha fazlasi.'],
            ['q' => 'CodeGa ERP\'yi nasil deneyebilirim?', 'a' => '<strong>Ucretsiz demo hesabi</strong> talep edebilirsiniz. Iletisim formundan veya WhatsApp uzerinden bildirin, 24 saat icinde size demo URL ve kullanici bilgileri gonderelim. Demo 30 gun sureli, tum modulleri kapsar.'],
            ['q' => 'ERP icin hangi hosting yeterli?', 'a' => 'Kucuk-orta sirketler icin (5-20 kullanici, 10K kayit) <strong>Profesyonel 2 (750₺/yil)</strong> yeterlidir. Buyuk sirketler (50+ kullanici, 100K+ kayit) icin <strong>Profesyonel 3 (1.200₺/yil)</strong> veya VPS oneririz. Detayli analiz icin satis ekibimize ulasin.'],
            ['q' => 'Mevcut yazilimimdan ERP\'ye veri tasiyabilir miyim?', 'a' => 'Evet! Excel, CSV, Wolvox, Mikro, Logo, NetCAD gibi sistemlerden veri import\'u yapabiliyoruz. Cari kartlar, stok, sermaye hesaplari, hareketler eksiksiz tasiniyor. Tasima ucretsiz, 1-3 is gunu surer.'],
            ['q' => 'Ozel modul gelistirme yapiyor musunuz?', 'a' => 'Evet! Sirketinize ozel ihtiyaclar icin modul gelistiriyoruz. Ornek: ozel rapor ekranlari, harici sistem entegrasyonlari (Logo, Mikro, banka API), ozel formuller, sektorel ozelleskirilmis gorevler. Ucret modulun karmasikligina gore degisir, ortalama <strong>5.000-15.000 ₺</strong> arasi.'],
            ['q' => 'API entegrasyonlari yapiyor musunuz?', 'a' => 'Tabii! GIB e-Fatura/e-Arsiv, banka API\'leri (Garanti, IsBank, Akbank), kargo (Aras, Yurtici, MNG), SMS gateway\'leri, sosyal medya API\'leri, odeme sistemleri (iyzico, PayTR, Stripe), CRM\'ler vb. Mevcut sistem ile yeni sistem arasinda gerceck-zamanli senkronizasyon kurabiliyoruz.'],
            ['q' => 'Yazilim projeleri nedir maliyeti?', 'a' => 'Standart e-ticaret 25.000-75.000 ₺, kurumsal web sitesi 8.000-20.000 ₺, ozel ERP modulleri 5.000-15.000 ₺, mobil uygulama 30.000-100.000 ₺ arasidir. Detayli teklif icin iletisim formundan projenizi anlatin, 24 saat icinde donus yaparuz.'],
            ['q' => 'Yazilim projesi suresi ne kadar?', 'a' => 'Standart projeler <strong>2-4 hafta</strong>, orta olcekli <strong>1-3 ay</strong>, buyuk olcekli (ERP gibi) <strong>3-12 ay</strong> surer. Agile metodoloji ile haftalik demo\'lar yapariz, geri bildirimleri suje al­arak ilerleriz.'],
        ],
    ],
    [
        'id' => 'billing',
        'icon' => 'bi-credit-card-fill',
        'color' => '#06b6d4',
        'name' => 'Fatura ve Odemeler',
        'desc' => 'Faturalandirma, odeme yontemleri ve iade politikasi',
        'questions' => [
            ['q' => 'Hangi odeme yontemlerini kabul ediyorsunuz?', 'a' => 'Kredi karti (Visa, Mastercard, AMEX), banka havalesi/EFT (T.C. tum bankalar), Papara, ininal, kripto para (BTC, ETH, USDT). Kurumsal musteriler icin Sirket Karti ile fatura odenebilir.'],
            ['q' => 'Fatura otomatik geliyor mu?', 'a' => 'Evet! Tum siparislerde otomatik e-Fatura veya e-Arsiv kesilir, e-postaniza ulasir. Panel > Faturalarim sekmesinden de PDF olarak indirebilirsiniz.'],
            ['q' => 'Iade nasil isliyor?', 'a' => '<strong>30 gun icinde kosulsuz iade</strong> garantisi sunuyoruz. Hosting hizmetinden memnun kalmadiysaniz, panel > Destek > Iade Talebi olusturun. Talep 1 is gunu icinde sonuclandirilir, paranizin tamami orijinal odeme yontemine geri yatirilir.'],
            ['q' => 'Yenileme zamani geldi, otomatik mi yenilenir?', 'a' => 'Evet, hosting ve domain hizmetleriniz <strong>otomatik yenilenir</strong> (eger paneldeki "Otomatik Yenileme" aktifse). Sona ermesine 30 gun kala fatura kesilir, 7 gun once odenmediginde uyari gelir. Otomatik yenilemeyi panelden devre disi birakabilirsiniz.'],
            ['q' => 'Toplu odeme yapabilir miyim?', 'a' => 'Evet! Birden fazla faturanizi tek seferde odemek icin Faturalarim sekmesinde "Toplu Odeme" butonuna basin. Tum aktif faturalar listelenir, odemek istediklerinizi secin.'],
        ],
    ],
];

$total_questions = 0;
foreach($kb_categories as $cat) $total_questions += count($cat['questions']);
?>

<!-- 1. PAGE HERO -->
<section class="cdg-page-hero">
    <div class="cdg-page-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-page-hero-content">
            <div class="cdg-eyebrow cdg-eyebrow-glow"><i class="bi bi-book-half"></i> Bilgi Bankasi</div>
            <h1>Aklinizdaki <span class="cdg-text-gradient">tum sorulara</span> cevap burada</h1>
            <p>Hosting, domain, e-posta, WordPress, ERP ve yazilim hizmetlerimiz hakkinda <strong><?php echo $total_questions; ?>+ soru ve cevap</strong>. Aradiginizi bulamazsaniz bize sorun, eklemeye devam ederiz.</p>

            <!-- Search box -->
            <div class="cdg-kb-search">
                <i class="bi bi-search"></i>
                <input type="text" id="cdg-kb-search-input" placeholder="Soru ara: 'SSL nasil kurulur', 'WordPress hizlandirma'...">
            </div>

            <!-- Quick stats -->
            <div class="cdg-kb-stats">
                <div><strong><?php echo $total_questions; ?>+</strong><span>Soru &amp; Cevap</span></div>
                <div><strong><?php echo count($kb_categories); ?></strong><span>Kategori</span></div>
                <div><strong>5dk</strong><span>Yanit Suresi</span></div>
            </div>
        </div>
    </div>
</section>

<!-- 2. CATEGORIES NAV -->
<section class="cdg-section" style="padding-top:48px;padding-bottom:0;">
    <div class="cdg-container">
        <div class="cdg-kb-cat-grid">
            <?php foreach($kb_categories as $cat): ?>
            <a href="#cat-<?php echo $cat['id']; ?>" class="cdg-kb-cat-card">
                <div class="cdg-kb-cat-icon" style="background:linear-gradient(135deg,<?php echo $cat['color']; ?>,<?php echo $cat['color']; ?>cc);">
                    <i class="bi <?php echo $cat['icon']; ?>"></i>
                </div>
                <div class="cdg-kb-cat-body">
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <p><?php echo htmlspecialchars($cat['desc']); ?></p>
                    <div class="cdg-kb-cat-count"><?php echo count($cat['questions']); ?> soru</div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. CATEGORIES + QUESTIONS -->
<section class="cdg-section" id="cdg-kb-questions">
    <div class="cdg-container">
        <?php foreach($kb_categories as $cat): ?>
        <div class="cdg-kb-cat-section" id="cat-<?php echo $cat['id']; ?>">
            <div class="cdg-kb-cat-head">
                <div class="cdg-kb-cat-head-icon" style="background:linear-gradient(135deg,<?php echo $cat['color']; ?>,<?php echo $cat['color']; ?>cc);">
                    <i class="bi <?php echo $cat['icon']; ?>"></i>
                </div>
                <div>
                    <h2><?php echo htmlspecialchars($cat['name']); ?></h2>
                    <p><?php echo htmlspecialchars($cat['desc']); ?></p>
                </div>
            </div>
            <div class="cdg-kb-questions">
                <?php foreach($cat['questions'] as $i => $q): ?>
                <details class="cdg-faq-item cdg-kb-faq" data-q="<?php echo htmlspecialchars(strtolower($q['q'])); ?>" data-a="<?php echo htmlspecialchars(strtolower(strip_tags($q['a']))); ?>"<?php if($i===0) echo ' open'; ?>>
                    <summary>
                        <span><?php echo htmlspecialchars($q['q']); ?></span>
                        <i class="bi bi-plus-lg"></i>
                    </summary>
                    <div class="cdg-faq-answer"><?php echo $q['a']; /* HTML tags allowed */ ?></div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- 4. STILL HAVE QUESTIONS CTA -->
<section class="cdg-final-cta">
    <div class="cdg-container">
        <div class="cdg-final-cta-content">
            <div class="cdg-eyebrow">Cevabini Bulamadin mi?</div>
            <h2>Sorunuzu cevapsiz <span class="cdg-text-gradient">birakmayalim</span></h2>
            <p>Burada cevap bulamadiysaniz, ekibimize 7/24 ulasabilirsiniz. Ortalama yanit sureniz <strong>5 dakikadan az</strong>.</p>
            <div class="cdg-final-cta-actions">
                <a href="<?php echo $contact_url; ?>" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow"><i class="bi bi-chat-dots-fill"></i> Soru Sor</a>
                <a href="https://wa.me/903329099656" target="_blank" rel="noopener" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-whatsapp"></i> WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<!-- Search Script -->
<script>
(function(){
    var input = document.getElementById('cdg-kb-search-input');
    if(!input) return;
    var allFaqs = document.querySelectorAll('.cdg-kb-faq');
    var allCats = document.querySelectorAll('.cdg-kb-cat-section');

    input.addEventListener('input', function(){
        var term = input.value.toLowerCase().trim();
        if(term.length < 2){
            allFaqs.forEach(function(f){ f.style.display = ''; });
            allCats.forEach(function(c){ c.style.display = ''; });
            return;
        }

        var matchedCats = new Set();
        allFaqs.forEach(function(f){
            var q = f.getAttribute('data-q') || '';
            var a = f.getAttribute('data-a') || '';
            if(q.indexOf(term) !== -1 || a.indexOf(term) !== -1){
                f.style.display = '';
                f.open = true;
                var cat = f.closest('.cdg-kb-cat-section');
                if(cat) matchedCats.add(cat);
            } else {
                f.style.display = 'none';
            }
        });

        allCats.forEach(function(c){
            c.style.display = matchedCats.has(c) ? '' : 'none';
        });
    });
})();
</script>
