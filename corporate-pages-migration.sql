-- ====================================================================
-- CODEGA WiseCP Kurumsal Sayfa Migration v2 (codega_wisecp2027 şemasına göre)
-- ====================================================================
-- Mevcut veritabanı analizi:
--   id=1  -> "Hakkımızda" (route: hakkimizda)         => ZATEN VAR
--   id=67 -> "Çerez Politikası" (route: cerez-politikasi) => ZATEN VAR
--
-- Bu SQL şu 6 yeni sayfayı ekler:
--   sosyal-sorumluluk, surdurulebilirlik, kariyer,
--   kvkk, gizlilik-politikasi, hizmet-sozlesmesi
--
-- İçerik BOŞ olarak eklenir; tema slug router otomatik
-- olarak hakkimizda.php / kariyer.php gibi tema dosyalarını yükler.
--
-- KULLANIM:
-- 1) phpMyAdmin -> WiseCP veritabanını seç
-- 2) SQL sekmesi -> bu dosyayı yapıştır -> Çalıştır
-- ====================================================================

-- ÖNCE: Önceki başarısız denemelerden kalan kayıtları temizle
DELETE FROM `pages_lang` WHERE `route` IN (
    'sosyal-sorumluluk', 'surdurulebilirlik', 'kariyer',
    'kvkk', 'gizlilik-politikasi', 'hizmet-sozlesmesi'
);

-- 1. Sosyal Sorumluluk
INSERT INTO `pages` (`type`, `category`, `categories`, `sidebar`, `status`, `visibility`, `visible_to_user`, `rank`, `override_usrcurrency`, `taxexempt`, `addons`, `requirements`, `options`, `affiliate_disable`, `affiliate_rate`, `ctime`, `module`, `module_data`, `notes`, `subdomains`)
VALUES ('normal', 0, NULL, '', 'active', 'visible', 0, 0, 0, 0, NULL, NULL, '[]', 0, 0.00, NOW(), NULL, NULL, NULL, NULL);
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`, `seo_title`, `seo_keywords`, `seo_description`, `options`)
VALUES (LAST_INSERT_ID(), 'tr', 'Sosyal Sorumluluk', '', 'sosyal-sorumluluk', 'Sosyal Sorumluluk - CODEGA', 'sosyal sorumluluk, csr, kurumsal sorumluluk', 'CODEGA olarak yıllık gelirimizin %2sini sosyal sorumluluk projelerine ayırıyoruz', '');

-- 2. Sürdürülebilirlik
INSERT INTO `pages` (`type`, `category`, `categories`, `sidebar`, `status`, `visibility`, `visible_to_user`, `rank`, `override_usrcurrency`, `taxexempt`, `addons`, `requirements`, `options`, `affiliate_disable`, `affiliate_rate`, `ctime`, `module`, `module_data`, `notes`, `subdomains`)
VALUES ('normal', 0, NULL, '', 'active', 'visible', 0, 0, 0, 0, NULL, NULL, '[]', 0, 0.00, NOW(), NULL, NULL, NULL, NULL);
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`, `seo_title`, `seo_keywords`, `seo_description`, `options`)
VALUES (LAST_INSERT_ID(), 'tr', 'Sürdürülebilirlik', '', 'surdurulebilirlik', 'Sürdürülebilirlik - CODEGA', 'yeşil hosting, sürdürülebilirlik, çevre', '2030a kadar 100 yenilenebilir enerji veri merkezi hedefimiz', '');

-- 3. Kariyer
INSERT INTO `pages` (`type`, `category`, `categories`, `sidebar`, `status`, `visibility`, `visible_to_user`, `rank`, `override_usrcurrency`, `taxexempt`, `addons`, `requirements`, `options`, `affiliate_disable`, `affiliate_rate`, `ctime`, `module`, `module_data`, `notes`, `subdomains`)
VALUES ('normal', 0, NULL, '', 'active', 'visible', 0, 0, 0, 0, NULL, NULL, '[]', 0, 0.00, NOW(), NULL, NULL, NULL, NULL);
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`, `seo_title`, `seo_keywords`, `seo_description`, `options`)
VALUES (LAST_INSERT_ID(), 'tr', 'Kariyer', '', 'kariyer', 'Kariyer - CODEGA', 'kariyer, iş ilanları, codega kariyer, php developer', 'CODEGAda açık pozisyonlar ve sunduğumuz imkanlar', '');

-- 4. KVKK Aydınlatma Metni
INSERT INTO `pages` (`type`, `category`, `categories`, `sidebar`, `status`, `visibility`, `visible_to_user`, `rank`, `override_usrcurrency`, `taxexempt`, `addons`, `requirements`, `options`, `affiliate_disable`, `affiliate_rate`, `ctime`, `module`, `module_data`, `notes`, `subdomains`)
VALUES ('normal', 0, NULL, '', 'active', 'visible', 0, 0, 0, 0, NULL, NULL, '[]', 0, 0.00, NOW(), NULL, NULL, NULL, NULL);
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`, `seo_title`, `seo_keywords`, `seo_description`, `options`)
VALUES (LAST_INSERT_ID(), 'tr', 'KVKK Aydınlatma Metni', '', 'kvkk', 'KVKK Aydınlatma Metni - CODEGA', 'kvkk, kişisel verilerin korunması, 6698 sayılı kanun', 'KVKK 6698 Sayılı Kanun kapsamında veri sorumlusu sıfatıyla aydınlatma metnimiz', '');

-- 5. Gizlilik Politikası
INSERT INTO `pages` (`type`, `category`, `categories`, `sidebar`, `status`, `visibility`, `visible_to_user`, `rank`, `override_usrcurrency`, `taxexempt`, `addons`, `requirements`, `options`, `affiliate_disable`, `affiliate_rate`, `ctime`, `module`, `module_data`, `notes`, `subdomains`)
VALUES ('normal', 0, NULL, '', 'active', 'visible', 0, 0, 0, 0, NULL, NULL, '[]', 0, 0.00, NOW(), NULL, NULL, NULL, NULL);
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`, `seo_title`, `seo_keywords`, `seo_description`, `options`)
VALUES (LAST_INSERT_ID(), 'tr', 'Gizlilik Politikası', '', 'gizlilik-politikasi', 'Gizlilik Politikası - CODEGA', 'gizlilik, privacy, veri koruma, gdpr', 'CODEGA gizlilik politikası ve veri işleme uygulamalarımız', '');

-- 6. Hizmet Sözleşmesi
INSERT INTO `pages` (`type`, `category`, `categories`, `sidebar`, `status`, `visibility`, `visible_to_user`, `rank`, `override_usrcurrency`, `taxexempt`, `addons`, `requirements`, `options`, `affiliate_disable`, `affiliate_rate`, `ctime`, `module`, `module_data`, `notes`, `subdomains`)
VALUES ('normal', 0, NULL, '', 'active', 'visible', 0, 0, 0, 0, NULL, NULL, '[]', 0, 0.00, NOW(), NULL, NULL, NULL, NULL);
INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`, `seo_title`, `seo_keywords`, `seo_description`, `options`)
VALUES (LAST_INSERT_ID(), 'tr', 'Hizmet Sözleşmesi', '', 'hizmet-sozlesmesi', 'Hizmet Sözleşmesi - CODEGA', 'hizmet sözleşmesi, kullanıcı sözleşmesi, üyelik koşulları', 'CODEGA genel hizmet sözleşmesi ve müşteri yükümlülükleri', '');

-- BİTTİ. Aşağıdaki URL'ler artık aktif:
-- /hakkimizda            (mevcut - id=1, içerik tema dosyasından)
-- /sosyal-sorumluluk     (yeni)
-- /surdurulebilirlik     (yeni)
-- /kariyer               (yeni)
-- /kvkk                  (yeni)
-- /cerez-politikasi      (mevcut - id=67, içerik tema dosyasından)
-- /gizlilik-politikasi   (yeni)
-- /hizmet-sozlesmesi     (yeni)
