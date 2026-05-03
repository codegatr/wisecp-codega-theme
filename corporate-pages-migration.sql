-- ====================================================================
-- CODEGA WiseCP Kurumsal Sayfa Migration
-- ====================================================================
-- BU SQL DOSYASI: 8 kurumsal sayfayı WiseCP'nin pages tablosuna ekler.
-- İçeriği "BOŞ" olarak kayıt yapar; tema otomatik olarak hazır içeriği
-- (tema klasörü içindeki PHP dosyalarından) yükler.
--
-- KULLANIM:
-- 1) phpMyAdmin'e gir
-- 2) WiseCP veritabanını seç
-- 3) SQL sekmesine yapıştır ve "Çalıştır" tıkla
-- 4) Site refresh yap, mega menüden test et
--
-- NOT: WiseCP'nin pages tablosu adı genellikle 'pages' veya 'tbl_pages'.
-- Eğer hata alırsan tablo adını kontrol et ve değiştir.
-- ====================================================================

-- Önce mevcut kurumsal sayfaları temizle (varsa)
DELETE FROM pages WHERE slug IN (
    'hakkimizda', 'sosyal-sorumluluk', 'surdurulebilirlik', 'kariyer',
    'kvkk', 'cerez-politikasi', 'gizlilik-politikasi', 'hizmet-sozlesmesi'
);

-- 8 kurumsal sayfa ekle
INSERT INTO pages (title, slug, content, lang, status, creation_date) VALUES
    ('Hakkımızda',          'hakkimizda',          '', 'tr', 'active', NOW()),
    ('Sosyal Sorumluluk',   'sosyal-sorumluluk',   '', 'tr', 'active', NOW()),
    ('Sürdürülebilirlik',   'surdurulebilirlik',   '', 'tr', 'active', NOW()),
    ('Kariyer',             'kariyer',             '', 'tr', 'active', NOW()),
    ('KVKK Aydınlatma Metni', 'kvkk',              '', 'tr', 'active', NOW()),
    ('Çerez Politikası',    'cerez-politikasi',    '', 'tr', 'active', NOW()),
    ('Gizlilik Politikası', 'gizlilik-politikasi', '', 'tr', 'active', NOW()),
    ('Hizmet Sözleşmesi',   'hizmet-sozlesmesi',   '', 'tr', 'active', NOW());

-- Bitti! Aşağıdaki URL'lerden test edebilirsin:
-- /hakkimizda
-- /sosyal-sorumluluk
-- /surdurulebilirlik
-- /kariyer
-- /kvkk
-- /cerez-politikasi
-- /gizlilik-politikasi
-- /hizmet-sozlesmesi
