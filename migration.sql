-- ====================================================================
-- CODEGA WiseCP Theme - Migration SQL (v3.5.53 → güncelleme sistemi)
-- ====================================================================
-- Bu dosya WiseCP update sistemi tarafından otomatik çalıştırılır.
-- Her INSERT idempotent (duplicate key korumalı), tekrarlı çalıştırma güvenli.
--
-- Eklenen sayfalar (codega.com.tr migration):
--   - referanslarimiz.html (Referanslar, 59 firma)
--   - vizyon.html (Vizyon & Değerlerimiz)
--   - sistem-durumu.html (Sistem Durumu)
--   - erp-yazilimi.html (CODEGA ERP detay sayfası)
-- ====================================================================

-- 1. REFERANSLAR SAYFASI
-- ====================================================================
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `pages_lang` WHERE `route` = 'referanslarimiz' OR `route` = 'our-references'
)
LIMIT 1;
SET @ref_id = LAST_INSERT_ID();

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @ref_id, 'tr', 'Referanslarımız', '<p>Türkiye\'nin önde gelen kurumları altyapı, hosting ve özel yazılım için Codega\'ya güveniyor. 59+ aktif müşteri, 7 farklı sektör.</p>', 'referanslarimiz'
FROM DUAL
WHERE @ref_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @ref_id AND `lang` = 'tr');

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @ref_id, 'en', 'Our References', '<p>Leading Turkish institutions trust Codega for infrastructure, hosting and custom software. 59+ active clients, 7 different sectors.</p>', 'our-references'
FROM DUAL
WHERE @ref_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @ref_id AND `lang` = 'en');


-- 2. VİZYON SAYFASI
-- ====================================================================
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `pages_lang` WHERE `route` = 'vizyon' OR `route` = 'vision'
)
LIMIT 1;
SET @vis_id = LAST_INSERT_ID();

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @vis_id, 'tr', 'Vizyon & Değerlerimiz', '<p>Codega\'nın temel ilkeleri, uzun vadeli hedefleri ve her kararı yönlendiren değerler. 2005\'ten bugüne 20 yıllık dijital altyapı yolculuğumuz.</p>', 'vizyon'
FROM DUAL
WHERE @vis_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @vis_id AND `lang` = 'tr');

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @vis_id, 'en', 'Vision & Values', '<p>Codega\'s fundamental principles, long-term goals and the values that guide every decision. Our 20-year digital infrastructure journey since 2005.</p>', 'vision'
FROM DUAL
WHERE @vis_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @vis_id AND `lang` = 'en');


-- 3. SİSTEM DURUMU SAYFASI
-- ====================================================================
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `pages_lang` WHERE `route` = 'sistem-durumu' OR `route` = 'system-status'
)
LIMIT 1;
SET @sys_id = LAST_INSERT_ID();

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @sys_id, 'tr', 'Sistem Durumu', '<p>Hosting, domain, e-posta ve ödeme servislerinin gerçek zamanlı durumu. Şeffaflık değerimizdir.</p>', 'sistem-durumu'
FROM DUAL
WHERE @sys_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @sys_id AND `lang` = 'tr');

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @sys_id, 'en', 'System Status', '<p>Real-time status of hosting, domain, email and payment services. Transparency is our value.</p>', 'system-status'
FROM DUAL
WHERE @sys_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @sys_id AND `lang` = 'en');


-- 4. ERP YAZILIMI SAYFASI
-- ====================================================================
INSERT INTO `pages` (`type`, `creation_date`)
SELECT 'normal', NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `pages_lang` WHERE `route` = 'erp-yazilimi' OR `route` = 'erp-software'
)
LIMIT 1;
SET @erp_id = LAST_INSERT_ID();

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @erp_id, 'tr', 'CODEGA ERP', '<p>Finanstan üretime, satıştan İK\'ya tek panelden işletme yönetimi. 9 entegre modül, e-fatura, mobil uygulama, %50 daha uygun fiyat.</p>', 'erp-yazilimi'
FROM DUAL
WHERE @erp_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @erp_id AND `lang` = 'tr');

INSERT INTO `pages_lang` (`owner_id`, `lang`, `title`, `content`, `route`)
SELECT @erp_id, 'en', 'CODEGA ERP', '<p>Manage your business from finance to production, sales to HR — all from one panel. 9 integrated modules, e-invoice, mobile app, 50% lower price.</p>', 'erp-software'
FROM DUAL
WHERE @erp_id > 0
  AND NOT EXISTS (SELECT 1 FROM `pages_lang` WHERE `owner_id` = @erp_id AND `lang` = 'en');
