# 🚀 ca.codega.com.tr → codega.com.tr Migration Planı

## Hedef
- `codega.com.tr` ana domain'inde **WiseCP doğrudan / kökünde** çalışacak
- `ca.codega.com.tr` subdomain'i tamamen silinecek
- Eski `codega.com.tr` corporate site'ı silinecek
- Müşteri verileri (DB) korunacak

## Avantajlar
- **Codega temasında corporate sayfalar zaten mevcut**: hakkimizda, kariyer, kvkk, gizlilik, vb.
- **`index.php` 1105 satır** — landing page tamamen Codega'da var
- **`.htaccess` migration için hazırlanmış** — `.html` URL'leri `.php` dosyalara rewrite ediliyor (v3.5.57'de eklendi)

---

## ✅ Pre-flight (Migration ÖNCESİ - Mutlaka)

### 1. Backup
```bash
# DirectAdmin > File Manager veya SSH

# WiseCP DB tam yedek (DirectAdmin > MySQL > phpMyAdmin > Export)
# Tablo: wisecp_xxxxx (mevcut DB adını öğren)
# Format: SQL, Custom: Add DROP TABLE, Add IF NOT EXISTS

# Dosya yedek
cd /home/USERNAME/domains/codega.com.tr/
tar -czf ~/backup-ca-$(date +%Y%m%d).tar.gz subdomains/ca/

cd /home/USERNAME/domains/codega.com.tr/
tar -czf ~/backup-corporate-$(date +%Y%m%d).tar.gz public_html/
```

### 2. WiseCP Lisans Kontrolü
- WiseCP Admin Panel > Sistem > Lisans
- **Domain**: `ca.codega.com.tr` mi `codega.com.tr` mi?
- Eğer `ca.codega.com.tr` ise: WiseCP destek üzerinden domain değişikliği talep et
- Lisans tek domain ise: bu adım yapılmadan migration yapılırsa lisans sorunu çıkabilir

### 3. config.php URL Kontrolü
```php
# /domains/codega.com.tr/subdomains/ca/config.php
# Bu satırlardan biri var mı?

define('APP_URI', 'https://ca.codega.com.tr');
$APP_URI = 'https://ca.codega.com.tr';

# Migration sonrası: 'https://codega.com.tr' olarak değiştirilecek
```

### 4. SSL Sertifikası
- DirectAdmin > Let's Encrypt
- `codega.com.tr` ve `www.codega.com.tr` için sertifika hazır olmalı
- Eğer sertifika sadece `ca.` için ise, ana domain için yenile

---

## 🔄 Migration Süreci (Downtime: ~30dk-1saat)

### ADIM 1: Maintenance Mode
```bash
# WiseCP Admin Panel > Sistem > Bakım Modu
# ON yap
# Müşterilerin yeni siparişleri durur
```

### ADIM 2: Eski Corporate Site Arşivle
```bash
cd /home/USERNAME/domains/codega.com.tr/public_html/
mkdir -p ../old-corporate-archive
mv * ../old-corporate-archive/
mv .htaccess ../old-corporate-archive/ 2>/dev/null
mv .* ../old-corporate-archive/ 2>/dev/null

# Şimdi public_html boş
ls -la
```

### ADIM 3: ca/ İçeriğini Ana public_html'e Taşı
```bash
cd /home/USERNAME/domains/codega.com.tr/

# ca subdomain'in dosyalarını ana public_html'e taşı
mv subdomains/ca/* public_html/
mv subdomains/ca/.htaccess public_html/ 2>/dev/null
mv subdomains/ca/.* public_html/ 2>/dev/null

# Permissions kontrol
chmod -R 755 public_html/
find public_html/ -type f -name "*.php" -exec chmod 644 {} \;
```

### ADIM 4: config.php URL Güncelle
```bash
# Ana config (varsa)
nano /home/USERNAME/domains/codega.com.tr/public_html/config.php

# Veya
nano /home/USERNAME/domains/codega.com.tr/public_html/system/config.php

# Bul:
# define('APP_URI', 'https://ca.codega.com.tr');
# Değiştir:
# define('APP_URI', 'https://codega.com.tr');

# Veya:
# $APP_URI = 'https://ca.codega.com.tr';
# $APP_URI = 'https://codega.com.tr';
```

### ADIM 5: WiseCP Database URL Güncelle
```sql
-- phpMyAdmin > WiseCP DB > SQL

-- Settings tablosunda URL ayarları
UPDATE wisecp_settings SET value = 'https://codega.com.tr'
  WHERE name IN ('app_uri', 'app_url', 'site_url', 'home_url');

-- Eski URL'lere referansları güncelle
UPDATE wisecp_settings SET value = REPLACE(value, 'ca.codega.com.tr', 'codega.com.tr')
  WHERE value LIKE '%ca.codega.com.tr%';

-- DİKKAT: Tablo isimleri farklı olabilir, önce yapıyı kontrol et:
-- SHOW TABLES LIKE '%setting%';
```

### ADIM 6: Subdomain Sil
```bash
# DirectAdmin > Subdomain Management
# ca.codega.com.tr - Delete

# Dosya artık ana public_html'de, subdomain'de boş kaldı
# subdomain silinince DNS otomatik güncelleniyor
```

### ADIM 7: SSL Yenile
```bash
# DirectAdmin > SSL Certificates > Free & automatic certificate from Let's Encrypt
# Wildcard: codega.com.tr + www.codega.com.tr
# Apply
```

### ADIM 8: 301 Redirect (Eski ca.* URL'lerini koruyalım)
```bash
# Apache vhost'ta veya .htaccess'in en başında:
# (DirectAdmin'de: Custom HTTPD Configurations)

# Eski ca.codega.com.tr/* linklerini ana domain'e yönlendir
# Bu DirectAdmin > Custom HTTPD Configurations > Customize'da yapılır
```

veya `.htaccess`'te (daha basit):
```apache
# Eski ca. subdomain'den gelenleri yönlendir
RewriteCond %{HTTP_HOST} ^ca\.codega\.com\.tr$ [NC]
RewriteRule ^(.*)$ https://codega.com.tr/$1 [R=301,L]
```

### ADIM 9: Test
```bash
# Tarayıcı testleri:
1. https://codega.com.tr/                    → Landing page
2. https://codega.com.tr/sign-in             → Login
3. https://codega.com.tr/hesabim/dashboard   → Dashboard (login sonrası)
4. https://codega.com.tr/hakkimizda.html     → Hakkımızda (.html→.php rewrite test)
5. https://codega.com.tr/kvkk.html           → KVKK
6. https://codega.com.tr/sistem-durumu.html  → System status
7. https://ca.codega.com.tr/                 → 301 → https://codega.com.tr/
```

### ADIM 10: Maintenance Mode OFF
```bash
# WiseCP Admin Panel > Sistem > Bakım Modu OFF
```

---

## ⚠️ Olası Sorunlar ve Çözümler

### Sorun 1: WiseCP "Lisans hatası"
- Sebep: Lisans `ca.` domain için kayıtlı
- Çözüm: WiseCP destek > domain değişikliği (genelde 24 saat içinde)

### Sorun 2: SSL hatası (Mixed Content)
- Sebep: HTML içinde hardcoded `http://ca.codega.com.tr/...` var
- Çözüm: phpMyAdmin'de tüm tablolarda find&replace:
  ```sql
  UPDATE wisecp_articles SET content = REPLACE(content, 'http://ca.codega.com.tr', 'https://codega.com.tr');
  UPDATE wisecp_news SET content = REPLACE(content, 'http://ca.codega.com.tr', 'https://codega.com.tr');
  -- ... diğer tablolar
  ```

### Sorun 3: Müşteri panelde görüntü bozulmuş
- Sebep: CSS/JS asset'leri eski URL'den yükleniyor
- Çözüm: Browser cache temizle, sonra DirectAdmin > LiteSpeed Cache Manager > Purge All

### Sorun 4: 404 hatası `.html` URL'lerinde
- Sebep: `.htaccess` rewrite çalışmıyor
- Çözüm:
  ```bash
  # apache_module mod_rewrite enabled mi?
  # DirectAdmin > Custom HTTPD Configurations > Apache Modules
  ```

### Sorun 5: Login sonrası yanlış URL'e yönlenme
- Sebep: WiseCP DB'de kayıtlı "redirect_after_login" eski URL
- Çözüm: `wisecp_settings` tablosunda `*url*` LIKE araması, hepsini güncelle

---

## 🎯 Migration Sonrası Yapılacaklar

1. **Google Search Console**: `codega.com.tr` property'i ekle, sitemap submit
2. **Google Analytics**: Property URL güncelle
3. **DNS**: Eski `ca.` MX kayıtları varsa silin
4. **Müşteri Bildirimi**: Email + Dashboard duyuru — "Hesap panelimiz artık codega.com.tr"
5. **Sitemap**: `codega.com.tr/sitemap.xml` test, Google'a submit
6. **Robots.txt**: `codega.com.tr/robots.txt` güncel mi?

---

## 📞 Acil Durum (Geri Dönüş Planı)

Eğer migration yarım kalır veya site çalışmazsa:

```bash
# 1. Eski corporate site'ı geri yükle
cd /home/USERNAME/domains/codega.com.tr/
mv public_html/* /tmp/wisecp-temp/
mv old-corporate-archive/* public_html/

# 2. WiseCP'yi ca subdomain'e geri taşı (subdomain'i yeniden oluştur)
# DirectAdmin > Subdomain Management > Add: ca.codega.com.tr
mv /tmp/wisecp-temp/* /home/USERNAME/domains/codega.com.tr/subdomains/ca/

# 3. config.php'de URL'i ca.codega.com.tr'a geri al
# 4. DB'de URL ayarlarını ca.codega.com.tr'a geri al
```

---

**Migration Tarih Önerisi**: Hafta sonu sabah erken (Cuma 23:00 - Cumartesi 02:00 arası en az trafik)

**Tahmini Süre**: 30dk - 1 saat (sorun çıkmazsa)

**Hazırlayan**: Codega Tema v3.5.57 + Yunus Aksoy
**Tarih**: 2026-05-03
