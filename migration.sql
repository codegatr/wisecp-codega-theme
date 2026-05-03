-- ====================================================================
-- CODEGA WiseCP Theme - Migration SQL
-- Tema güncellemesi sonrası WiseCP boot'unda otomatik çalıştırılır.
-- Pattern: Single-statement, idempotent, NOT EXISTS guard.
-- SET @var multi-statement YOK (driver bağımsız).
-- ====================================================================

-- ============= 1) KURUMSAL SAYFALAR (pages) =============

-- Referanslarımız (referanslarimiz)
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` IN ('referanslarimiz', 'our-references'));

-- Referanslarımız TR dil (yeni eklenen pages'ı MAX(id) ile bul)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT MAX(p.id) FROM `pages` p WHERE p.type='normal' AND NOT EXISTS (SELECT 1 FROM `pages_lang` pl WHERE pl.owner_id = p.id)),
  'tr', 'Referanslarımız', '<p>Türkiye\'nin önde gelen kurumları altyapı, hosting ve özel yazılım için Codega\'ya güveniyor. 59+ aktif müşteri, 7 farklı sektör.</p>', 'referanslarimiz'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'referanslarimiz');

-- Our References EN dil (TR'nin owner_id'sini al)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT owner_id FROM `pages_lang` WHERE `route` = 'referanslarimiz' LIMIT 1),
  'en', 'Our References', '<p>Leading Turkish institutions trust Codega for infrastructure, hosting and custom software. 59+ active clients, 7 different sectors.</p>', 'our-references'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'our-references');

-- Vizyon & Değerlerimiz (vizyon)
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` IN ('vizyon', 'vision'));

-- Vizyon & Değerlerimiz TR dil (yeni eklenen pages'ı MAX(id) ile bul)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT MAX(p.id) FROM `pages` p WHERE p.type='normal' AND NOT EXISTS (SELECT 1 FROM `pages_lang` pl WHERE pl.owner_id = p.id)),
  'tr', 'Vizyon & Değerlerimiz', '<p>Codega\'nın temel ilkeleri, uzun vadeli hedefleri ve her kararı yönlendiren değerler. 2005\'ten bugüne 20 yıllık dijital altyapı yolculuğumuz.</p>', 'vizyon'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'vizyon');

-- Vision & Values EN dil (TR'nin owner_id'sini al)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT owner_id FROM `pages_lang` WHERE `route` = 'vizyon' LIMIT 1),
  'en', 'Vision & Values', '<p>Codega\'s fundamental principles, long-term goals and the values that guide every decision. Our 20-year digital infrastructure journey since 2005.</p>', 'vision'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'vision');

-- Sistem Durumu (sistem-durumu)
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` IN ('sistem-durumu', 'system-status'));

-- Sistem Durumu TR dil (yeni eklenen pages'ı MAX(id) ile bul)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT MAX(p.id) FROM `pages` p WHERE p.type='normal' AND NOT EXISTS (SELECT 1 FROM `pages_lang` pl WHERE pl.owner_id = p.id)),
  'tr', 'Sistem Durumu', '<p>Hosting, domain, e-posta ve ödeme servislerinin gerçek zamanlı durumu. Şeffaflık değerimizdir.</p>', 'sistem-durumu'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'sistem-durumu');

-- System Status EN dil (TR'nin owner_id'sini al)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT owner_id FROM `pages_lang` WHERE `route` = 'sistem-durumu' LIMIT 1),
  'en', 'System Status', '<p>Real-time status of hosting, domain, email and payment services. Transparency is our value.</p>', 'system-status'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'system-status');

-- CODEGA ERP (erp-yazilimi)
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` IN ('erp-yazilimi', 'erp-software'));

-- CODEGA ERP TR dil (yeni eklenen pages'ı MAX(id) ile bul)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT MAX(p.id) FROM `pages` p WHERE p.type='normal' AND NOT EXISTS (SELECT 1 FROM `pages_lang` pl WHERE pl.owner_id = p.id)),
  'tr', 'CODEGA ERP', '<p>Finanstan üretime, satıştan İK\'ya tek panelden işletme yönetimi. 9 entegre modül, e-fatura, mobil uygulama, %50 daha uygun fiyat.</p>', 'erp-yazilimi'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'erp-yazilimi');

-- CODEGA ERP EN dil (TR'nin owner_id'sini al)
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT
  (SELECT owner_id FROM `pages_lang` WHERE `route` = 'erp-yazilimi' LIMIT 1),
  'en', 'CODEGA ERP', '<p>Manage your business from finance to production, sales to HR — all from one panel. 9 integrated modules, e-invoice, mobile app, 50% lower price.</p>', 'erp-software'
WHERE NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `route` = 'erp-software');


-- ============= 2) BİLGİ BANKASI (knowledgebase + categories) =============
-- 5 kategori, 19 makale - codega.com.tr/data/bilgi_bankasi.json

-- KATEGORİ: Hosting (hosting)
-- 1) categories tablosuna kategori ekle
INSERT INTO `categories` (`parent`, `type`, `kind_id`, `status`, `visibility`, `rank`, `options`, `ctime`)
SELECT 0, 'knowledgebase', 0, 'active', 'visible', 100, '[]', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `categories_lang` cl JOIN `categories` c ON cl.owner_id = c.id
  WHERE c.type = 'knowledgebase' AND cl.route = 'hosting'
);

-- 2) categories_lang tablosuna TR dil ekle
INSERT INTO `categories_lang` (`owner_id`, `lang`, `title`, `route`, `sub_title`, `content`)
SELECT
  (SELECT MAX(c.id) FROM `categories` c WHERE c.type='knowledgebase' AND NOT EXISTS (SELECT 1 FROM `categories_lang` cl WHERE cl.owner_id = c.id)),
  'tr', 'Hosting', 'hosting', 'Web hosting kurulum, yönetim ve sorun giderme rehberleri', ''
WHERE NOT EXISTS (SELECT 1 FROM `categories_lang` WHERE `route` = 'hosting' AND `lang` = 'tr');

-- MAKALE: Web Hosting Nedir?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='hosting' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'web-hosting-nedir');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'web-hosting-nedir', 'Web Hosting Nedir?',
  '<p>Web hosting, web sitenizin dosyalarının ve verilerinin internet üzerinden erişilebilir sunucularda barındırılması hizmetidir. Bir web sitesi yayınlamak için hosting zorunludur.</p>
<h3>Hosting Türleri</h3>
<ul><li>Paylaşımlı Hosting: Birden fazla sitenin aynı sunucuyu paylaştığı, ekonomik çözüm</li><li>VPS Hosting: Sanal özel sunucu, daha fazla kaynak ve kontrol</li><li>Dedicated Server: Tüm sunucunun tek kullanıcıya tahsis edildiği premium çözüm</li><li>Cloud Hosting: Bulut altyapısında esnek ve ölçeklenebilir barındırma</li></ul>
<p>Codega\'nın sunduğu paylaşımlı hosting paketleri, küçük ve orta ölçekli işletmeler için yüksek performans ve %99.9 uptime garantisiyle ideal çözümdür.</p>',
  'temel, başlangıç'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'web-hosting-nedir' AND `lang` = 'tr');

-- MAKALE: E-posta Hesabı Nasıl Oluşturulur?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='hosting' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'e-posta-hesabi-nasil-olusturulur');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'e-posta-hesabi-nasil-olusturulur', 'E-posta Hesabı Nasıl Oluşturulur?',
  '<p>DirectAdmin paneli üzerinden kolayca profesyonel e-posta hesapları oluşturabilirsiniz.</p>
<h3>Adım Adım E-posta Oluşturma</h3>
<p></p>
<p>Profesyonel ipucu: E-posta şifrenizi en az 12 karakter, büyük/küçük harf ve rakam içerecek şekilde belirleyin.</p>',
  'e-posta, directadmin'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'e-posta-hesabi-nasil-olusturulur' AND `lang` = 'tr');

-- MAKALE: FTP ile Dosya Yükleme
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='hosting' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'ftp-ile-dosya-yukleme');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'ftp-ile-dosya-yukleme', 'FTP ile Dosya Yükleme',
  '<p>FTP (File Transfer Protocol), web sunucunuza dosya yüklemek için kullanılan standart protokoldür. FileZilla gibi ücretsiz araçlarla kolayca kullanabilirsiniz.</p>
<h3>FileZilla Bağlantı Bilgileri</h3>
<p></p>
<p>Güvenlik için FTP yerine SFTP (port 22) kullanmanızı öneririz. SFTP, verilerinizi şifreleyerek iletir.</p>',
  'ftp, dosya'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'ftp-ile-dosya-yukleme' AND `lang` = 'tr');

-- MAKALE: SSL Sertifikası Nasıl Kurulur?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='hosting' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'ssl-sertifikasi-nasil-kurulur');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'ssl-sertifikasi-nasil-kurulur', 'SSL Sertifikası Nasıl Kurulur?',
  '<p>SSL sertifikası, siteniz ile ziyaretçileriniz arasındaki veri iletişimini şifreler ve tarayıcıda yeşil kilit ikonunu gösterir. Google, SSL\'li siteleri arama sonuçlarında üst sıralarda gösterir.</p>
<h3>Let\'s Encrypt ile Ücretsiz SSL</h3>
<p></p>
<p>Let\'s Encrypt sertifikaları 90 günde bir otomatik yenilenir. Herhangi bir işlem yapmanıza gerek yoktur.</p>',
  'ssl, güvenlik, https'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'ssl-sertifikasi-nasil-kurulur' AND `lang` = 'tr');

-- MAKALE: PHP Sürümü Nasıl Değiştirilir?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='hosting' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'php-surumu-nasil-degistirilir');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'php-surumu-nasil-degistirilir', 'PHP Sürümü Nasıl Değiştirilir?',
  '<p>Farklı PHP sürümleri, uygulamalarınızın performans ve uyumluluk gereksinimlerini karşılar. DirectAdmin üzerinden kolayca değiştirebilirsiniz.</p>
<h3>PHP Sürüm Değiştirme</h3>
<p></p>
<p>PHP sürümünü değiştirmeden önce mevcut uygulamanızın yeni sürümle uyumlu olduğunu kontrol edin. Test ortamında denemenizi öneririz.</p>',
  'php, directadmin'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'php-surumu-nasil-degistirilir' AND `lang` = 'tr');


-- KATEGORİ: Domain (domain)
-- 1) categories tablosuna kategori ekle
INSERT INTO `categories` (`parent`, `type`, `kind_id`, `status`, `visibility`, `rank`, `options`, `ctime`)
SELECT 0, 'knowledgebase', 0, 'active', 'visible', 100, '[]', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `categories_lang` cl JOIN `categories` c ON cl.owner_id = c.id
  WHERE c.type = 'knowledgebase' AND cl.route = 'domain'
);

-- 2) categories_lang tablosuna TR dil ekle
INSERT INTO `categories_lang` (`owner_id`, `lang`, `title`, `route`, `sub_title`, `content`)
SELECT
  (SELECT MAX(c.id) FROM `categories` c WHERE c.type='knowledgebase' AND NOT EXISTS (SELECT 1 FROM `categories_lang` cl WHERE cl.owner_id = c.id)),
  'tr', 'Domain', 'domain', 'Alan adı kayıt, transfer ve DNS yönetimi rehberleri', ''
WHERE NOT EXISTS (SELECT 1 FROM `categories_lang` WHERE `route` = 'domain' AND `lang` = 'tr');

-- MAKALE: Domain (Alan Adı) Nedir?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='domain' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'domain-alan-adi-nedir');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'domain-alan-adi-nedir', 'Domain (Alan Adı) Nedir?',
  '<p>Domain (alan adı), internet üzerinde web sitenizin adresidir. IP adresi yerine insanların hatırlayabileceği kelimelerden oluşur. Örneğin codega.com.tr bir domain adıdır.</p>
<h3>Domain Uzantıları</h3>
<ul><li>.com.tr → Türkiye\'de kurumsal işletmeler için en güvenilir uzantı</li><li>.com → Uluslararası alanda en yaygın uzantı</li><li>.net → Teknoloji ve ağ şirketleri için ideal</li><li>.org → Sivil toplum kuruluşları için uygun</li><li>.net.tr → Türkiye\'deki ağ ve teknoloji firmaları için</li></ul>
<p>Türkiye\'de faaliyet gösteren işletmeler için .com.tr uzantısı müşterilere güven verir ve yerel SEO açısından avantaj sağlar.</p>',
  'temel, başlangıç'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'domain-alan-adi-nedir' AND `lang` = 'tr');

-- MAKALE: DNS Yönetimi ve Kayıtlar
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='domain' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'dns-yonetimi-ve-kayitlar');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'dns-yonetimi-ve-kayitlar', 'DNS Yönetimi ve Kayıtlar',
  '<p>DNS (Domain Name System), alan adınızı IP adreslerine çeviren sistemdir. Doğru DNS kayıtları, e-posta ve web hizmetlerinizin düzgün çalışması için kritiktir.</p>
<h3>Temel DNS Kayıt Türleri</h3>
<p></p>
<p>DNS değişiklikleri 24-48 saat içinde tüm dünyaya yayılır (propagasyon). Bu süreçte eski ve yeni ayarlar birlikte görünebilir.</p>',
  'dns, teknik'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'dns-yonetimi-ve-kayitlar' AND `lang` = 'tr');

-- MAKALE: Domain Transfer Nasıl Yapılır?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='domain' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'domain-transfer-nasil-yapilir');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'domain-transfer-nasil-yapilir', 'Domain Transfer Nasıl Yapılır?',
  '<p>Alan adınızı başka bir kayıt firmasından Codega\'ya transfer etmek için aşağıdaki adımları izleyin. Transfer süreci genellikle 5-7 iş günü sürer.</p>
<h3>Transfer Adımları</h3>
<p></p>
<p>Transfer işlemi için domainin son kayıt tarihinden en az 15 gün geçmiş olması gerekir. Transfer sırasında 1 yıl ek süre eklenir.</p>',
  'transfer, taşıma'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'domain-transfer-nasil-yapilir' AND `lang` = 'tr');


-- KATEGORİ: ERP Yazılımı (erp-yazilimi)
-- 1) categories tablosuna kategori ekle
INSERT INTO `categories` (`parent`, `type`, `kind_id`, `status`, `visibility`, `rank`, `options`, `ctime`)
SELECT 0, 'knowledgebase', 0, 'active', 'visible', 100, '[]', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `categories_lang` cl JOIN `categories` c ON cl.owner_id = c.id
  WHERE c.type = 'knowledgebase' AND cl.route = 'erp-yazilimi'
);

-- 2) categories_lang tablosuna TR dil ekle
INSERT INTO `categories_lang` (`owner_id`, `lang`, `title`, `route`, `sub_title`, `content`)
SELECT
  (SELECT MAX(c.id) FROM `categories` c WHERE c.type='knowledgebase' AND NOT EXISTS (SELECT 1 FROM `categories_lang` cl WHERE cl.owner_id = c.id)),
  'tr', 'ERP Yazılımı', 'erp-yazilimi', 'Kurumsal kaynak planlama sistemi kullanım ve yönetim rehberleri', ''
WHERE NOT EXISTS (SELECT 1 FROM `categories_lang` WHERE `route` = 'erp-yazilimi' AND `lang` = 'tr');

-- MAKALE: ERP Nedir? Neden Kullanılır?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='erp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'erp-nedir-neden-kullanilir');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'erp-nedir-neden-kullanilir', 'ERP Nedir? Neden Kullanılır?',
  '<p>ERP (Enterprise Resource Planning — Kurumsal Kaynak Planlama), bir işletmenin tüm iş süreçlerini tek bir entegre sistem üzerinden yönetmesini sağlayan yazılım çözümüdür.</p>
<h3>ERP ile Neler Yönetilir?</h3>
<ul><li>Muhasebe ve finans: Gelir/gider takibi, fatura, vergi raporları</li><li>Stok yönetimi: Depo takibi, ürün giriş/çıkış, minimum stok uyarıları</li><li>Satış ve CRM: Müşteri yönetimi, sipariş takibi, teklif hazırlama</li><li>Satın alma: Tedarikçi yönetimi, sipariş onay süreçleri</li><li>İnsan kaynakları: Personel bilgileri, maaş, izin takibi</li><li>Üretim: İş emirleri, kapasite planlaması, maliyet hesaplama</li></ul>
<p>Codega ERP çözümleri, PHP tabanlı ve tamamen özelleştirilebilir yapıdadır. İşletmenizin özel ihtiyaçlarına göre modüler olarak yapılandırılır.</p>',
  'temel, başlangıç'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'erp-nedir-neden-kullanilir' AND `lang` = 'tr');

-- MAKALE: Stok Yönetimi Modülü Kullanımı
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='erp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'stok-yonetimi-modulu-kullanimi');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'stok-yonetimi-modulu-kullanimi', 'Stok Yönetimi Modülü Kullanımı',
  '<p>Stok yönetimi modülü, ürünlerinizin depo hareketlerini gerçek zamanlı takip etmenizi sağlar. Kritik stok seviyelerinde otomatik uyarı alırsınız.</p>
<h3>Stok Giriş İşlemi</h3>
<p></p>
<h3>Minimum Stok Uyarısı</h3>
<p>Her ürün için minimum stok seviyesi tanımlayabilirsiniz. Stok bu seviyenin altına düştüğünde sistem otomatik olarak sorumlu kişilere e-posta/SMS uyarısı gönderir.</p>',
  'stok, depo'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'stok-yonetimi-modulu-kullanimi' AND `lang` = 'tr');

-- MAKALE: Fatura Oluşturma ve PDF Çıktısı
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='erp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'fatura-olusturma-ve-pdf-ciktisi');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'fatura-olusturma-ve-pdf-ciktisi', 'Fatura Oluşturma ve PDF Çıktısı',
  '<p>ERP sistemi, satış işlemlerinizden otomatik olarak fatura oluşturur. Oluşturulan faturalar PDF olarak indirilebilir ve e-posta ile gönderilebilir.</p>
<p></p>
<p>Faturalarınız sistem üzerinde arşivlenir. Vergi denetimi için 10 yıl geriye dönük fatura geçmişine erişebilirsiniz.</p>',
  'fatura, muhasebe'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'fatura-olusturma-ve-pdf-ciktisi' AND `lang` = 'tr');

-- MAKALE: Raporlama ve Analitik
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='erp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'raporlama-ve-analitik');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'raporlama-ve-analitik', 'Raporlama ve Analitik',
  '<p>ERP raporlama modülü, işletmenizin tüm verilerini anlamlı grafikler ve tablolar halinde sunar. Günlük, haftalık, aylık ve yıllık karşılaştırmalar yapabilirsiniz.</p>
<h3>Hazır Rapor Şablonları</h3>
<ul><li>Aylık Satış Raporu: Ürün/müşteri bazlı satış performansı</li><li>Kâr-Zarar Raporu: Gelir-gider özeti ve net kâr analizi</li><li>Stok Durum Raporu: Mevcut stok ve değer analizi</li><li>Müşteri Analizi: En çok satış yapılan müşteriler</li><li>Tedarikçi Raporu: Satın alma tutarları ve teslimat performansı</li><li>KDV Beyanname Raporu: Muhasebe için hazır çıktı</li></ul>',
  'rapor, analitik'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'raporlama-ve-analitik' AND `lang` = 'tr');


-- KATEGORİ: MRP Yazılımı (mrp-yazilimi)
-- 1) categories tablosuna kategori ekle
INSERT INTO `categories` (`parent`, `type`, `kind_id`, `status`, `visibility`, `rank`, `options`, `ctime`)
SELECT 0, 'knowledgebase', 0, 'active', 'visible', 100, '[]', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `categories_lang` cl JOIN `categories` c ON cl.owner_id = c.id
  WHERE c.type = 'knowledgebase' AND cl.route = 'mrp-yazilimi'
);

-- 2) categories_lang tablosuna TR dil ekle
INSERT INTO `categories_lang` (`owner_id`, `lang`, `title`, `route`, `sub_title`, `content`)
SELECT
  (SELECT MAX(c.id) FROM `categories` c WHERE c.type='knowledgebase' AND NOT EXISTS (SELECT 1 FROM `categories_lang` cl WHERE cl.owner_id = c.id)),
  'tr', 'MRP Yazılımı', 'mrp-yazilimi', 'Malzeme ihtiyaç planlama sistemi rehberleri', ''
WHERE NOT EXISTS (SELECT 1 FROM `categories_lang` WHERE `route` = 'mrp-yazilimi' AND `lang` = 'tr');

-- MAKALE: MRP Nedir? ERP'den Farkı Ne?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='mrp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'mrp-nedir-erp-den-farki-ne');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'mrp-nedir-erp-den-farki-ne', 'MRP Nedir? ERP\'den Farkı Ne?',
  '<p>MRP (Material Requirements Planning — Malzeme İhtiyaç Planlaması), üretim süreçlerinde hangi malzemelerin ne zaman ve ne kadar gerektiğini hesaplayan sistemdir.</p>
<h3>MRP ile ERP Farkı</h3>
<p></p>
<p>Codega MRP çözümü, ERP sistemiyle entegre çalışır. Siparişler ERP\'ye girildiğinde MRP otomatik olarak malzeme ihtiyacını hesaplar.</p>',
  'temel, başlangıç'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'mrp-nedir-erp-den-farki-ne' AND `lang` = 'tr');

-- MAKALE: Üretim Emri Oluşturma
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='mrp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'uretim-emri-olusturma');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'uretim-emri-olusturma', 'Üretim Emri Oluşturma',
  '<p>Üretim emirleri, hangi ürünün ne kadar üretileceğini ve bunun için hangi malzemelerin kullanılacağını tanımlar.</p>
<p></p>
<p>Üretim emri oluşturmadan önce ilgili ürünün BOM (Bill of Materials — Malzeme Listesi) kaydının sisteme girilmiş olması gerekir.</p>',
  'üretim, iş emri'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'uretim-emri-olusturma' AND `lang` = 'tr');

-- MAKALE: BOM (Malzeme Listesi) Yönetimi
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='mrp-yazilimi' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'bom-malzeme-listesi-yonetimi');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'bom-malzeme-listesi-yonetimi', 'BOM (Malzeme Listesi) Yönetimi',
  '<p>BOM (Bill of Materials), bir ürünü üretmek için gereken tüm hammadde, yarı mamul ve bileşenlerin listesidir. MRP sisteminin temel verisidir.</p>
<h3>BOM Oluşturma</h3>
<p></p>
<p>Çok seviyeli BOM desteği sayesinde, yarı mamulün de kendi BOM\'u varsa sistem tüm malzeme hiyerarşisini otomatik olarak çözer.</p>',
  'bom, malzeme listesi'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'bom-malzeme-listesi-yonetimi' AND `lang` = 'tr');


-- KATEGORİ: PHP Yazılımlar (php-yazilimlar)
-- 1) categories tablosuna kategori ekle
INSERT INTO `categories` (`parent`, `type`, `kind_id`, `status`, `visibility`, `rank`, `options`, `ctime`)
SELECT 0, 'knowledgebase', 0, 'active', 'visible', 100, '[]', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `categories_lang` cl JOIN `categories` c ON cl.owner_id = c.id
  WHERE c.type = 'knowledgebase' AND cl.route = 'php-yazilimlar'
);

-- 2) categories_lang tablosuna TR dil ekle
INSERT INTO `categories_lang` (`owner_id`, `lang`, `title`, `route`, `sub_title`, `content`)
SELECT
  (SELECT MAX(c.id) FROM `categories` c WHERE c.type='knowledgebase' AND NOT EXISTS (SELECT 1 FROM `categories_lang` cl WHERE cl.owner_id = c.id)),
  'tr', 'PHP Yazılımlar', 'php-yazilimlar', 'Özel PHP yazılım geliştirme, bakım ve teknik rehberler', ''
WHERE NOT EXISTS (SELECT 1 FROM `categories_lang` WHERE `route` = 'php-yazilimlar' AND `lang` = 'tr');

-- MAKALE: Neden Özel PHP Yazılım?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='php-yazilimlar' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'neden-ozel-php-yazilim');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'neden-ozel-php-yazilim', 'Neden Özel PHP Yazılım?',
  '<p>Hazır yazılımlar genellikle genel ihtiyaçları karşılar. Oysa her işletmenin kendine özgü süreçleri vardır. Özel PHP yazılımlar, tam olarak sizin iş modelinize göre tasarlanır.</p>
<h3>Özel Yazılımın Avantajları</h3>
<ul><li>Tam esneklik: İstediğiniz her özellik eklenebilir, değiştirilebilir</li><li>Lisans maliyeti yok: Bir kez yazılır, ömür boyu sizindir</li><li>Entegrasyon: Mevcut sistemlerinizle (ERP, muhasebe, e-ticaret) kolayca bağlanır</li><li>Ölçeklenebilirlik: İşletmeniz büyüdükçe yazılım da büyür</li><li>Güvenlik: Kaynak kodu size ait, dışarıya açık değil</li><li>Destek: Geliştiriciyle doğrudan iletişim, hızlı çözüm</li></ul>
<p>Codega, 5+ yıllık PHP geliştirme deneyimiyle kurumsal düzeyde, güvenli ve sürdürülebilir yazılımlar üretmektedir.</p>',
  'temel, başlangıç'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'neden-ozel-php-yazilim' AND `lang` = 'tr');

-- MAKALE: API Entegrasyonu Nedir?
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='php-yazilimlar' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'api-entegrasyonu-nedir');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'api-entegrasyonu-nedir', 'API Entegrasyonu Nedir?',
  '<p>API (Application Programming Interface), farklı yazılımların birbirleriyle konuşmasını sağlayan arayüzdür. Örneğin e-ticaret sitenizin kargo firmasıyla otomatik iletişim kurması API ile olur.</p>
<h3>Sık Yapılan API Entegrasyonları</h3>
<ul><li>Ödeme: iyzico, PayTR, Stripe — online ödeme alma</li><li>Kargo: Yurtiçi, Aras, MNG — otomatik gönderi oluşturma</li><li>SMS: Netgsm, Twilio — bildirim ve doğrulama</li><li>E-fatura: GIB entegrasyonu — yasal fatura düzenleme</li><li>Muhasebe: Logo, Mikro — çift taraflı veri senkronizasyonu</li><li>E-ticaret: Trendyol, Hepsiburada, Amazon — çok kanal satış</li></ul>
<p>Codega, REST ve SOAP API standartlarını destekler. Mevcut sisteminize hangi entegrasyon gerektiğini belirlemek için ücretsiz teknik danışmanlık alabilirsiniz.</p>',
  'api, entegrasyon, teknik'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'api-entegrasyonu-nedir' AND `lang` = 'tr');

-- MAKALE: Yazılım Bakım ve Güncelleme
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='php-yazilimlar' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'yazilim-bakim-ve-guncelleme');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'yazilim-bakim-ve-guncelleme', 'Yazılım Bakım ve Güncelleme',
  '<p>PHP yazılımlarınızın güvenli ve performanslı çalışması için düzenli bakım zorunludur. Codega bakım paketi kapsamında neler yapıldığını öğrenin.</p>
<h3>Aylık Bakım Kapsamı</h3>
<ul><li>PHP ve bağımlılık güncellemeleri (güvenlik yamaları)</li><li>Veritabanı optimizasyonu ve temizleme</li><li>Hata loglarının incelenmesi ve giderilmesi</li><li>Performans izleme ve iyileştirme</li><li>Yedekleme doğrulama ve test</li><li>Güvenlik taraması (açık port, SQL injection, XSS kontrolü)</li></ul>
<p>PHP 7.4 ve altı sürümler artık resmi güvenlik güncellemesi almamaktadır. PHP 8.1 veya üstüne geçiş için destek ekibimizle iletişime geçin.</p>',
  'bakım, güncelleme'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'yazilim-bakim-ve-guncelleme' AND `lang` = 'tr');

-- MAKALE: PHP Güvenlik En İyi Pratikleri
INSERT INTO `knowledgebase` (`category`, `categories`, `sidebar`, `visit_count`, `useful`, `useless`, `private`, `status`, `rank`, `ctime`)
SELECT
  (SELECT c.id FROM `categories` c JOIN `categories_lang` cl ON cl.owner_id = c.id WHERE c.type='knowledgebase' AND cl.route='php-yazilimlar' LIMIT 1),
  NULL, '', 0, 0, 0, 0, 1, 0, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'php-guvenlik-en-i-yi-pratikleri');

INSERT INTO `knowledgebase_lang` (`owner_id`, `lang`, `route`, `title`, `content`, `tags`)
SELECT
  (SELECT MAX(k.id) FROM `knowledgebase` k WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` kl WHERE kl.owner_id = k.id)),
  'tr', 'php-guvenlik-en-i-yi-pratikleri', 'PHP Güvenlik En İyi Pratikleri',
  '<p>Güvenli bir PHP uygulaması için temel güvenlik kurallarına uymak kritiktir. Codega, tüm yazılımlarda bu standartları uygulamaktadır.</p>
<h3>Temel Güvenlik Önlemleri</h3>
<ul><li>SQL Injection koruması: PDO prepared statements kullanımı</li><li>XSS koruması: Kullanıcı girdilerinin htmlspecialchars() ile temizlenmesi</li><li>CSRF koruması: Form tokenları</li><li>Şifre güvenliği: bcrypt/Argon2 hash algoritmaları</li><li>Oturum güvenliği: HttpOnly, Secure, SameSite cookie bayrakları</li><li>Dosya yükleme: MIME type doğrulama ve sandbox ortamı</li></ul>
<p>Codega, her yeni yazılım projesinde OWASP Top 10 güvenlik standartlarını temel alır ve kod tesliminden önce güvenlik denetimi gerçekleştirir.</p>',
  'güvenlik, teknik'
WHERE NOT EXISTS (SELECT 1 FROM `knowledgebase_lang` WHERE `route` = 'php-guvenlik-en-i-yi-pratikleri' AND `lang` = 'tr');

