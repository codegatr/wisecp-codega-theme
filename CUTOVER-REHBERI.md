# 🎯 CODEGA Cutover Rehberi (DirectAdmin Adım Adım)

> **Hedef**: `ca.codega.com.tr` → `codega.com.tr` taşı, eski siteyi yedekle, tek domain ile devam et.

> **Süre**: ~2 saat aktif iş + 1 ay Google indeks geçişi bekleme.

> **Risk Seviyesi**: 🟡 Orta (yedek alındıysa düşük). Cutover sırasında ~15 dk kesinti olur.

---

## ⏰ Cutover Penceresi (Önerilen Zaman)

**En iyi zaman**: Pazar gecesi 03:00-05:00 (Türkiye saati)
- En az kullanıcı trafiği
- En az ödeme akışı
- Hata olursa pazartesi sabah toparlanır

---

## 🔒 ADIM 0: Önceden Hazırlık (Cutover'dan 1 hafta önce)

### A) DNS TTL Düşürme
Cutover günü DNS değişikliklerinin hızla yayılması için TTL'leri **5 dakikaya** düşür.

**DirectAdmin → DNS Records:**
```
codega.com.tr      A     SUNUCU_IP    TTL: 300 (5 dk)
ca                 A     SUNUCU_IP    TTL: 300
www                CNAME codega.com.tr TTL: 300
```

### B) Kod Tabanını Hazır Tut
- WiseCP teması v3.5.55+ yüklü olmalı (`ca.codega.com.tr` üzerinde test edilmiş)
- `migration.sql` runner çalışmış olmalı (`data/migration-applied.json` mevcut)
- `.htaccess` redirect kuralları tema kökünde

### C) Yedek Stratejisi
**Cutover sabahı (önce):**
1. **codega.com.tr public_html** → ZIP indir, güvenli yere koy
2. **codega_database2027 (ana site DB)** → SQL dump
3. **codega_wisecp2027 (WiseCP DB)** → SQL dump
4. **DirectAdmin Backup**: tam hesap yedeği (`/admin/admin-backup`)

### D) Ödeme Gateway Hazırlığı
**iyzico Merchant Panel** → Sandbox URL'leri test:
- Eski: `https://ca.codega.com.tr/payment/callback`
- Yeni: `https://codega.com.tr/payment/callback`

**PayTR Merchant Panel** → callback URL kontrol:
- Sandbox'ta her iki URL ile ödeme test et

---

## 🚀 ADIM 1: Cutover Anı (15 dk kesinti)

### 1.1) WiseCP Bakım Modu Aç
WiseCP Admin Panel:
```
Ayarlar → Genel → Bakım Modu: AÇIK
Mesaj: "Sistem güncellemesi yapılıyor, 15 dakika içinde geri döneceğiz."
```

### 1.2) DirectAdmin'de Domain Taşıma

**DirectAdmin → File Manager:**

```
1. ca.codega.com.tr/public_html → tüm dosyaları seç
2. Sağ click → Move to → /home/codega/domains/codega.com.tr/public_html_NEW
   (yeni klasöre taşı, eski silmeden)

3. codega.com.tr/public_html → backup klasörüne yeniden adlandır:
   public_html → public_html_OLD_2026_05_03

4. public_html_NEW → public_html olarak yeniden adlandır
```

### 1.3) theme-config.php Güncellemesi

**DirectAdmin → File Manager → /codega.com.tr/public_html/theme-config.php**

Bul ve değiştir:
```php
// ESKI:
'site_url' => 'https://ca.codega.com.tr',

// YENI:
'site_url' => 'https://codega.com.tr',
```

### 1.4) WiseCP Settings DB Güncellemesi

**DirectAdmin → phpMyAdmin → codega_wisecp2027 → settings:**

```sql
-- Site URL'i güncelle
UPDATE settings SET value = 'https://codega.com.tr' WHERE name = 'site_url';
UPDATE settings SET value = 'https://codega.com.tr' WHERE name = 'home_url';

-- Cookie domain (varsa)
UPDATE settings SET value = '.codega.com.tr' WHERE name = 'cookie_domain';
```

### 1.5) ca.codega.com.tr → codega.com.tr Redirect

**DirectAdmin → Subdomain Manager → ca.codega.com.tr → Public HTML klasörü**

`/home/codega/domains/ca.codega.com.tr/public_html/.htaccess`:
```apache
RewriteEngine On
RewriteRule ^(.*)$ https://codega.com.tr/$1 [R=301,L]
```

veya **daha kalıcı (vhost-level)**: DirectAdmin'de subdomain'in kendi public_html'ini boşalt + 301 yaz.

### 1.6) Test Listesi (Cutover Sonrası)

- [ ] `https://codega.com.tr` → WiseCP teması açılıyor
- [ ] `https://ca.codega.com.tr` → `https://codega.com.tr`'ye 301 yönleniyor
- [ ] `https://codega.com.tr/erp-yazilimi.html` → ERP sayfası
- [ ] `https://codega.com.tr/referanslarimiz.html` → Referanslar
- [ ] `https://codega.com.tr/sistem-durumu.html` → Sistem Durumu
- [ ] `https://codega.com.tr/vizyon.html` → Vizyon
- [ ] `https://codega.com.tr/hosting-products.html` → Hosting paketleri
- [ ] **Eski URL test**: `https://codega.com.tr/?page=erp` → `/erp-yazilimi.html` 301
- [ ] **Eski URL test**: `https://codega.com.tr/pages/referanslar.php` → `/referanslarimiz.html` 301
- [ ] Sepet → ürün ekle → ödeme akışı (sandbox/test)
- [ ] WiseCP admin paneli giriş çalışıyor
- [ ] E-posta gönderim test (kayıt formu)
- [ ] **Migration runner**: `data/migration-applied.json` dosyası mevcut

### 1.7) WiseCP Bakım Modu Kapat

```
Ayarlar → Genel → Bakım Modu: KAPALI
```

---

## 📊 ADIM 2: Cutover Sonrası (İlk Hafta)

### 2.1) Google Search Console
**Yeni property**: `https://codega.com.tr` (zaten varsa kalır)
- Sitemap.xml gönder: `https://codega.com.tr/sitemap.xml`
- URL Inspection → ana sayfa indeksleme talep et
- Coverage raporu: 24-48 saatte 301 redirect'leri görmeye başlamalı

**Eski property**: `https://ca.codega.com.tr` (kalır 1 ay)
- 301 redirect'leri Google tanıyacak
- Bu süre kapatma

### 2.2) Ödeme Gateway Callback Güncellemesi
- iyzico merchant panel → callback URL **kalıcı** olarak yeni URL
- PayTR merchant panel → aynı şekilde
- GİB e-Fatura/e-Arşiv → Özel Entegratör portal callback URL

### 2.3) Cron Joblar
**DirectAdmin → Cron Manager** → mevcut cron'ların path'lerini kontrol:
```
ESKI: /home/codega/domains/ca.codega.com.tr/public_html/cron.php
YENI: /home/codega/domains/codega.com.tr/public_html/cron.php
```

### 2.4) E-posta SPF/DKIM/DMARC Kontrol
**DirectAdmin → DNS → MX/TXT records:**
```
SPF:   v=spf1 include:_spf.codega.com.tr ~all
DKIM:  default._domainkey.codega.com.tr  TXT  k=rsa; p=...
DMARC: _dmarc.codega.com.tr  TXT  v=DMARC1; p=quarantine; ...
```

### 2.5) SSL Sertifikası
**DirectAdmin → SSL Certificates → codega.com.tr:**
- Let's Encrypt otomatik yenileme aktif olmalı
- SAN'lar (alternative names) içermeli: `codega.com.tr`, `www.codega.com.tr`, `ca.codega.com.tr`
- Sertifika geçerlilik kontrol: 90 günden az kalmadıysa OK

---

## 📈 ADIM 3: Aylık İzleme (1 ay)

### Hafta 1
- 301 redirect'lerin Google tarafından işlendiğini kontrol et (GSC)
- Ödeme akışında sorun olup olmadığını kontrol et (WiseCP fatura listesi)
- E-posta teslim oranı (bounce/spam reports)

### Hafta 2-3
- Eski URL'lere gelen trafik düşüyor mu (Analytics)
- Yeni URL'ler indekslenmeye başladı mı (GSC)
- Backlink'ler eski URL'e gidiyor mu (Ahrefs/SEMrush)

### Hafta 4
- Trafiğin %95'i yeni URL'lere geçti mi
- Eski URL'lerden gelen trafik %5'in altına düştü mü

---

## 🗑️ ADIM 4: Subdomain Kaldırma (1 ay sonra)

301 redirect'lerin Google tarafından tamamen tanındığını GSC'den teyit ettikten sonra:

### 4.1) Subdomain Sil
**DirectAdmin → Subdomain Manager → ca.codega.com.tr → Delete**

### 4.2) DNS Kaydı Sil
**DirectAdmin → DNS Records → ca → A/CNAME → SİL**

### 4.3) Test
```bash
curl -I https://ca.codega.com.tr/
# Beklenen: 502/503 veya DNS resolution hatası
```

### 4.4) Eski Public HTML'i Sil
**Yedek varsa** (DirectAdmin Backup) → eski public_html_OLD_2026_05_03 klasörünü sil.

---

## 🚨 Acil Durum Senaryoları

### Senaryo 1: Cutover sırasında hata
**Çözüm**: Bakım modunu açık tut, eski public_html'i geri al:
```
public_html_OLD_2026_05_03 → public_html (rename geri)
WiseCP Settings → site_url eski değere geri al
```

### Senaryo 2: Ödeme akışı bozuldu
**Çözüm**: iyzico/PayTR merchant panel callback URL'leri kontrol et. Sandbox'ta test et.

### Senaryo 3: E-posta gönderemiyor
**Çözüm**: SPF/DKIM kayıtları kontrol. WiseCP → Tools → Mail Test ile test et.

### Senaryo 4: Migration runner SQL hatası
**Çözüm**:
```bash
tail -50 /tmp/codega-migration.log
# Hata mesajına göre migration.sql'i düzelt
# data/migration-applied.json dosyasını sil → bir sonraki istekte yeniden uygulanır
```

---

## ✅ Cutover Onay Listesi

Cutover'a başlamadan önce **HEPSİ** ✓ olmalı:

- [ ] Tema v3.5.55+ kurulu ve test edildi
- [ ] migration.sql çalıştırıldı (data/migration-applied.json mevcut)
- [ ] DirectAdmin tam yedek alındı
- [ ] DNS TTL 300'e düşürüldü, 24 saat geçti
- [ ] Ödeme gateway sandbox'ta yeni URL test edildi
- [ ] Bakım modu mesajı hazır
- [ ] Cutover saati seçildi (gece, az trafik)
- [ ] Yunus + 1 ekip arkadaşı hazır (4 göz)

**HEPSİ ✓ ise Cutover başlat.** 🚀
