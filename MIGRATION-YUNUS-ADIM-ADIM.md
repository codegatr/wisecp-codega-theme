# 🚀 ca.codega.com.tr → codega.com.tr Migration
## Yunus için Adım-Adım Komut Listesi (DirectAdmin)

**Hedef**: WiseCP'yi `codega.com.tr` köküne taşı, `ca.` subdomain'i sil
**Süre**: 30dk - 1 saat
**Zaman**: Cuma 23:00 - Cumartesi 02:00 arası önerilir

---

## 🎯 ÖZET: Ne Yapacağız?

```
ŞU AN:                          OLACAK:
codega.com.tr/   = Eski PHP    codega.com.tr/   = WiseCP (yeni!)
ca.codega.com.tr = WiseCP       ca.codega.com.tr = SİLİNDİ
```

---

## ⚙️ ÖN HAZIRLIK (Migration ÖNCESİ)

### ✅ Adım 0.1: WiseCP Lisans Domain Kontrol

WiseCP Admin Panel'e gir:
```
Sistem > Lisans
```

**Kontrol et**: Hangi domain için aktif?
- Eğer `ca.codega.com.tr` ise → **Şimdi WiseCP destek ekibine yaz**:
  > "ca.codega.com.tr → codega.com.tr domain değişikliği talep ediyorum"
  
- Eğer `codega.com.tr` zaten ise → **Tamamdır, devam et**

⚠️ **Lisans değişmeden migration yaparsan WiseCP "Lisans hatası" verir!**

### ✅ Adım 0.2: SSL Sertifikası Kontrol

DirectAdmin > **SSL Certificates** menüsüne git.

`codega.com.tr` için sertifika var mı?
- ❌ Yok ise: **Free & automatic certificate from Let's Encrypt** seç
- ✅ Var ise: Süresi yeterli mi (30 gün+)? Yoksa "Renew" yap

### ✅ Adım 0.3: v3.5.58'i Yükle

WiseCP > Codega Tema Update Merkezi > v3.5.58 yükle (henüz yüklemediysen)

Bu sürümde **`.htaccess`'te `.html` → `.php` rewrite** var. Migration sonrası corporate sayfalar (kvkk, hakkımızda, vb.) çalışsın diye gerekli.

---

## 💾 BACKUP (KESİN!)

### ✅ Adım 1.1: Database Yedek

DirectAdmin > **MySQL Management** > phpMyAdmin

1. Sol menüden WiseCP DB'yi seç (genellikle `*_wisecp` formatında)
2. Üst menüden **Export** sekmesine geç
3. **Quick** seçili kalsın, **Format: SQL** olsun
4. **Go** butonuna bas
5. İndirilen `.sql` dosyasını **güvenli yere kaydet** (PC + cloud)

### ✅ Adım 1.2: Dosya Yedek

DirectAdmin > **File Manager** veya FTP

İki klasör yedeklensin:
- `/domains/codega.com.tr/public_html/` → Eski corporate site
- `/domains/codega.com.tr/subdomains/ca/` → WiseCP

**File Manager'da ZIP yedeği:**
1. Klasöre sağ tık > Compress > ZIP
2. ZIP'leri indir veya `/home/USERNAME/backups/` klasörüne taşı

### ✅ Adım 1.3: WiseCP config.php Kayıt Et

DirectAdmin > File Manager:
```
/domains/codega.com.tr/subdomains/ca/config.php
```

**Aç, içeriğini kopyala, bir not defterinde sakla** (geri alacağımız ihtimal için).

İçinde aradığımız satır:
```php
define('APP_URI', 'https://ca.codega.com.tr');
```

Bu satır var mı? **Not al**: var/yok.

---

## 🔧 MIGRATION (Asıl Süreç)

### ✅ Adım 2.1: Bakım Modu AÇ

WiseCP Admin Panel > **Sistem > Bakım Modu**

`Açık` olarak işaretle. Müşteriler bu sırada hata almasın.

### ✅ Adım 2.2: Eski Corporate Site'ı Arşivle

DirectAdmin > **File Manager** > `/domains/codega.com.tr/`

1. `public_html` klasörünün adını **`old-corporate-archive`** olarak değiştir (sağ tık > Rename)
2. Yeni boş bir `public_html` klasörü oluştur (sağ tık > Create New > Folder)

### ✅ Adım 2.3: ca/ İçeriğini public_html'e Taşı

DirectAdmin > File Manager > `/domains/codega.com.tr/subdomains/ca/`

1. **Tüm dosya ve klasörleri seç** (Ctrl+A veya "Select All")
2. **Move** butonuna bas
3. Hedef: `/domains/codega.com.tr/public_html/`
4. **Move** onayla

⚠️ **Gizli dosyaları unutma**: `.htaccess`, `.gitignore` gibi dosyalar varsa onları da taşı.
File Manager'da gizli dosyaları görmek için: Settings > **Show Hidden Files** ✓

### ✅ Adım 2.4: config.php URL Güncelle

DirectAdmin > File Manager > `/domains/codega.com.tr/public_html/config.php`

**Bul ve değiştir**:
```php
// ESKİ:
define('APP_URI', 'https://ca.codega.com.tr');

// YENİ:
define('APP_URI', 'https://codega.com.tr');
```

Aynı şekilde varsa:
```php
$APP_URI = 'https://ca.codega.com.tr';   // → https://codega.com.tr
$_DOMAIN = 'ca.codega.com.tr';            // → codega.com.tr
```

### ✅ Adım 2.5: WiseCP DB URL Güncelle

DirectAdmin > **MySQL Management** > phpMyAdmin > WiseCP DB

**Üst menüden SQL sekmesi** > Aşağıdaki SQL'leri **TEK TEK** çalıştır:

```sql
-- 1. Önce settings tablosu yapısını gör
SHOW TABLES LIKE '%setting%';
```

Çıkan tablo adına göre (örn: `cms_settings` veya `wisecp_settings`):

```sql
-- 2. Site URL ayarlarını güncelle (TABLO ADINI DÜZELT!)
UPDATE cms_settings 
SET value = REPLACE(value, 'ca.codega.com.tr', 'codega.com.tr') 
WHERE value LIKE '%ca.codega.com.tr%';

-- 3. Hangi ayarların değiştiğini gör
SELECT name, value FROM cms_settings 
WHERE value LIKE '%codega.com.tr%' 
LIMIT 50;
```

**Diğer tablolarda da URL referansı olabilir:**

```sql
-- 4. Articles (Bilgi Bankası, Haberler) içeriklerinde URL referansları
UPDATE cms_kb_articles 
SET content = REPLACE(content, 'ca.codega.com.tr', 'codega.com.tr');

UPDATE cms_news 
SET content = REPLACE(content, 'ca.codega.com.tr', 'codega.com.tr');
```

⚠️ **Tablo adlarını mutlaka kontrol et!** WiseCP versiyonuna göre değişebilir:
```sql
-- Tüm tabloları gör
SHOW TABLES;
```

### ✅ Adım 2.6: ca Subdomain'i SİL

DirectAdmin > **Subdomain Management**

`ca` subdomain'in yanındaki **Delete** butonuna bas. Onayla.

✅ Bu işlemden sonra:
- `ca.codega.com.tr` artık DNS olarak çözülmeyecek
- Subdomain klasörü `/subdomains/ca/` boş kaldı (zaten taşımıştık)

### ✅ Adım 2.7: SSL Sertifikası Yenile

DirectAdmin > **SSL Certificates**

1. **Free & automatic certificate from Let's Encrypt** seç
2. ☑️ `codega.com.tr` 
3. ☑️ `www.codega.com.tr`
4. **Save** butonuna bas

⏱️ ~30 saniye bekleyince tamamlanır.

### ✅ Adım 2.8: ca → codega 301 Redirect Ekle

⚠️ Bu adım **opsiyonel ama önerilir** — eski email/whatsapp/Google'da `ca.codega.com.tr/login` linki tıklayanları yönlendirir.

Ana `.htaccess`'in **EN BAŞINA** ekle:

DirectAdmin > File Manager > `/domains/codega.com.tr/public_html/.htaccess`

Dosyanın başına (RewriteEngine On'dan SONRA, ilk RewriteRule'dan ÖNCE):

```apache
# ca.codega.com.tr → codega.com.tr 301 redirect
RewriteCond %{HTTP_HOST} ^ca\.codega\.com\.tr$ [NC]
RewriteRule ^(.*)$ https://codega.com.tr/$1 [R=301,L]
```

**Not**: Eğer 0.3'te v3.5.58 yüklediysen bu kısım `.htaccess`'in alt tarafında **yorum satırı** olarak duruyor. Yorumu kaldırarak (`#` işaretlerini sil) aktive edebilirsin.

### ✅ Adım 2.9: Bakım Modu KAPAT

WiseCP Admin Panel > **Sistem > Bakım Modu** → `Kapalı`

---

## 🧪 TEST (Migration SONRASI)

### ✅ Adım 3.1: Tarayıcı Testleri

**İncognito modda** test et (cache'siz):

| URL | Beklenen |
|-----|----------|
| `https://codega.com.tr/` | Codega ana sayfa (landing) |
| `https://codega.com.tr/sign-in` | Login sayfası |
| `https://codega.com.tr/hesabim/dashboard` | Dashboard (login sonrası) |
| `https://codega.com.tr/hakkimizda.html` | Hakkımızda (rewrite test) |
| `https://codega.com.tr/kvkk-aydinlatma-metni.html` | KVKK |
| `https://codega.com.tr/sistem-durumu.html` | Sistem durumu |
| `https://codega.com.tr/hosting-products.html` | Hosting paketleri |
| `https://codega.com.tr/erp-yazilimi.html` | CODEGA ERP |
| `https://codega.com.tr/kariyer.html` | Kariyer |
| `https://ca.codega.com.tr/` | 301 → codega.com.tr |

### ✅ Adım 3.2: WiseCP Fonksiyon Testleri

**Login → Dashboard** akışı:
- [ ] Login çalışıyor mu?
- [ ] Dashboard'da müşteri verileri görünüyor mu (mevcut domainler, hosting'ler)?
- [ ] Sipariş/fatura listesi geliyor mu?

**Footer kontrolleri:**
- [ ] Müşteri panelinde 3 sütunlu yeni footer var mı?
- [ ] Ana sayfada main-footer var mı?
- [ ] Mobile'da footer dikey hizalanıyor mu?

**Dosya/Asset testi:**
- [ ] CSS yükleniyor mu? (sayfa düzgün stillenmiş mi?)
- [ ] Logo görünüyor mu?
- [ ] Dashboard ikonları var mı?

### ✅ Adım 3.3: Mixed Content Hatası Var Mı?

Tarayıcıda F12 > Console tab > Sayfa yenile.

**Hatalar var mı?**
- ❌ `Mixed Content: The page was loaded over HTTPS, but requested an insecure resource...`
- ❌ `404 Not Found: /assets/...`

**Varsa**: Adım 4.1'e bak.

---

## 🛠️ SORUN ÇÖZÜM (Olası Sorunlar)

### ❌ Sorun 4.1: "Mixed Content" Hatası

**Sebep**: HTML içinde `http://ca.codega.com.tr/...` hardcoded URL var.

**Çözüm**: phpMyAdmin'de SQL:

```sql
-- Tüm metin/içerik tablolarında değiştir
UPDATE cms_kb_articles SET content = REPLACE(content, 'http://ca.codega.com.tr', 'https://codega.com.tr');
UPDATE cms_kb_articles SET content = REPLACE(content, 'https://ca.codega.com.tr', 'https://codega.com.tr');

UPDATE cms_news SET content = REPLACE(content, 'http://ca.codega.com.tr', 'https://codega.com.tr');
UPDATE cms_news SET content = REPLACE(content, 'https://ca.codega.com.tr', 'https://codega.com.tr');

UPDATE cms_pages SET content = REPLACE(content, 'http://ca.codega.com.tr', 'https://codega.com.tr');
UPDATE cms_pages SET content = REPLACE(content, 'https://ca.codega.com.tr', 'https://codega.com.tr');

-- E-mail şablonlarında
UPDATE cms_email_templates SET content = REPLACE(content, 'ca.codega.com.tr', 'codega.com.tr');
```

### ❌ Sorun 4.2: WiseCP "Lisans Hatası"

**Sebep**: Lisans hala `ca.codega.com.tr` için aktif.

**Çözüm**: WiseCP destek > "Lisans domain değişikliği" talep et. Genelde 24 saat içinde halledilir.

**Geçici çözüm**: Adım 5'te rollback yap, lisans değiştikten sonra yeniden migration yap.

### ❌ Sorun 4.3: 404 - .html URL'leri çalışmıyor

**Sebep**: `.htaccess` rewrite çalışmıyor (mod_rewrite kapalı veya `.htaccess` taşınmamış).

**Çözüm**:
1. `/public_html/.htaccess` dosyası mevcut mu kontrol et
2. DirectAdmin > Custom HTTPD Configurations > AllowOverride All olduğunu kontrol et
3. v3.5.58'i tekrar yükle (rewrite kuralları içerir)

### ❌ Sorun 4.4: Login sonrası boş sayfa veya hata

**Sebep**: `wisecp_settings`'te eski URL ayarları kaldı.

**Çözüm**:
```sql
-- Tüm URL referanslarını gör
SELECT name, value FROM cms_settings WHERE name LIKE '%url%' OR name LIKE '%uri%' OR name LIKE '%domain%';

-- Hepsini güncelle
UPDATE cms_settings SET value = 'https://codega.com.tr' WHERE name = 'app_uri';
UPDATE cms_settings SET value = 'https://codega.com.tr' WHERE name = 'site_url';
UPDATE cms_settings SET value = 'https://codega.com.tr' WHERE name = 'home_url';
```

### ❌ Sorun 4.5: CSS/JS yüklenmiyor

**Sebep**: LiteSpeed cache eski URL'leri tutuyor.

**Çözüm**:
1. DirectAdmin > **LiteSpeed Cache Manager** > Purge All
2. Browser cache temizle (Ctrl+Shift+Delete)
3. Yeniden test

---

## 🔙 ROLLBACK (Acil Geri Dönüş - Sorun Çıkarsa)

Eğer migration yarım kalır veya kritik hata varsa:

### 1. Eski Corporate Site'ı Geri Yükle
DirectAdmin > File Manager > `/domains/codega.com.tr/`
- `public_html` adını `wisecp-temp` yap
- `old-corporate-archive` adını `public_html` yap

### 2. WiseCP'yi ca/'a Geri Taşı
- DirectAdmin > Subdomain Management > Add: `ca` subdomain (yeniden oluştur)
- File Manager > `wisecp-temp/` içeriğini `subdomains/ca/`'ya taşı
- ca/config.php'i ESKİ haline geri al (notlardaki orijinal içerik)

### 3. DB Geri Yükle (Adım 1.1'deki backup)
- phpMyAdmin > WiseCP DB > Import > .sql dosyası seç > Go

✅ Eski hale dönüldü.

---

## 🎯 MIGRATION SONRASI (Sonraki günler)

### Hafta İçi Yapılacaklar:

1. **Google Search Console**
   - `codega.com.tr` property ekle (varsa kontrol et)
   - Sitemap submit: `https://codega.com.tr/sitemap.xml`
   - URL inspection ile birkaç URL test et

2. **Google Analytics**
   - Property settings > URL güncelle: `codega.com.tr`

3. **Müşteri Bildirimi**
   - Email template'i kullanarak müşterilere bildir:
     > "Hesap panelimiz artık `codega.com.tr` adresindedir. `ca.codega.com.tr` bookmarkları otomatik yönleniyor."

4. **Robots.txt Kontrol**
   - `https://codega.com.tr/robots.txt` doğru mu?

5. **DNS - Eski MX Kayıtları Kontrol**
   - DirectAdmin > DNS Management
   - `ca.` ile başlayan eski MX/A kayıtları varsa sil

6. **Eski Corporate Site Sil** (1 hafta sonra, sorun çıkmazsa)
   - `/domains/codega.com.tr/old-corporate-archive/` klasörünü sil

---

## ✅ Migration Bitti!

Bu plana göre adım adım yaparsan, **30dk-1 saat** içinde migration tamamlanır.

**Sorun yaşadığın anda hemen yaz** — birlikte çözelim.

**Hazırlayan**: Codega Tema v3.5.58  
**Tarih**: 2026-05-03
