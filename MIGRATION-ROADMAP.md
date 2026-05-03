# 🚀 CODEGA Migration Yol Haritası

**Hedef:** `codega.com.tr` (ana site) içeriklerini `ca.codega.com.tr` (WiseCP)'ye aktarmak, sonra subdomain'i kaldırıp tek domain ile devam etmek.

**Son Güncelleme:** 2026-05-03 — v3.5.54

---

## 📊 Mevcut Durum

### ✅ TAMAMLANDI

| # | İçerik | Sürüm | Durum |
|---|---|---|---|
| 1 | KVKK, Gizlilik, Çerez, Kariyer, Sürdürülebilirlik, Sosyal Sorumluluk | v3.5.43-46 | ✅ |
| 2 | Hakkımızda | v3.5.43 | ✅ |
| 3 | Referanslar (59 firma) | v3.5.52 | ✅ |
| 4 | ERP Yazılımı detay (1011 satır içerik) | v3.5.53 | ✅ |
| 5 | Vizyon & Değerlerimiz | v3.5.53 | ✅ |
| 6 | Sistem Durumu | v3.5.53 | ✅ |
| 7 | Anasayfa otomasyon süreci | v3.5.53 | ✅ |
| 8 | Mega menü 3-col upgrade | v3.5.53 | ✅ |
| 9 | Migration SQL runner (otomatik DB sync) | v3.5.53 | ✅ |
| 10 | **Bilgi Bankası migration** (5 kategori, 19 makale) | **v3.5.54** | **✅** |
| 11 | **Footer link güncellemeleri** | **v3.5.54** | **✅** |
| 12 | **.htaccess 301 redirect kuralları** | **v3.5.54** | **✅** |

### ⏳ KALANLAR

| # | İçerik | Tahmini Sürüm | Önem |
|---|---|---|---|
| 13 | Yazılım menüsüne ERP detay link | v3.5.55 | 🟢 Düşük |
| 14 | sitemap.xml otomatik güncelleme | v3.5.55 | 🟡 Orta |
| 15 | Hero başlık güncellemesi (ana site pozisyonu) | v3.5.55 | 🟡 Orta |
| 16 | İletişim sayfası zenginleştirme (form + harita) | v3.5.56 | 🟢 Düşük |
| 17 | **AŞAMA 2 — Cross-domain hazırlık** | manuel | 🔴 Kritik |
| 18 | **AŞAMA 3 — Cutover** | manuel | 🔴 Kritik |
| 19 | **AŞAMA 4 — Subdomain kaldırma** | manuel +1 ay | 🔴 Kritik |

---

## 🔄 Migration Runner Otomasyonu

### Çalışma Mantığı

`inc/cdg-migration-runner.php` her sayfa yüklenmesinde otomatik kontrol eder:

```
İstek → main-head.php boot → CdgMigrationRunner::autoRun()
   ↓
   migration.sql hash'i değişti mi? (data/migration-applied.json)
   ↓
   EVET → SQL'i parse et, sırayla çalıştır
       → İdempotent NOT EXISTS pattern, duplicate'ı skip
       → Hata logla (sys_get_temp_dir/codega-migration.log)
       → Yeni hash kaydet (data/migration-applied.json)
   HAYIR → Atla, frontend'e devam
```

### Eklenen Veriler (v3.5.54)

`migration.sql` çalıştığında otomatik eklenir:

**4 sayfa (`pages` + `pages_lang` TR+EN):**
- `referanslarimiz` / `our-references`
- `vizyon` / `vision`
- `sistem-durumu` / `system-status`
- `erp-yazilimi` / `erp-software`

**5 KB kategorisi (`categories` + `categories_lang` TR):**
- Hosting (5 makale)
- Domain (3 makale)
- ERP Yazılımı (4 makale)
- MRP Yazılımı (3 makale)
- PHP Yazılımlar (4 makale)

**19 makale (`knowledgebase` + `knowledgebase_lang` TR):**
- Web Hosting Nedir, E-posta Hesabı Nasıl Oluşturulur, FTP Hesabı, vs.
- Domain Nedir, DNS Yönetimi, Domain Transfer
- ERP Nedir, Stok Yönetimi, Üretim Modülü, Finans Raporları
- MRP Nedir, Üretim Emri, BOM Yönetimi
- Neden Özel PHP Yazılım, API Entegrasyonu, Yazılım Bakım, PHP Güvenlik

---

## 🎯 AŞAMA 2: Cross-domain Hazırlık

Bu adımları içerikler tamamlandıktan sonra Yunus + Claude beraber yapar.

### A) DirectAdmin Yapılandırması

**SEÇENEK A — Önerilen (codega.com.tr ana domain):**
```
codega.com.tr           → public_html (yeni: WiseCP teması)
ca.codega.com.tr        → 301 redirect → codega.com.tr  
www.codega.com.tr       → 301 redirect → codega.com.tr
```

**SEÇENEK B — ca. kalır:**
```
codega.com.tr     → 301 redirect → ca.codega.com.tr
ca.codega.com.tr  → WiseCP burada
```

**Önerim: SEÇENEK A** — SEO codega.com.tr alanında kalır (20 yıl emek).

### B) DNS Yapılandırması

DirectAdmin → DNS Records:
```
ca.codega.com.tr → A → SUNUCU_IP (geçici, eski URL'ler için)
www              → CNAME → codega.com.tr
codega.com.tr    → A → SUNUCU_IP (mevcut)
```

TTL'leri 5 dakikaya düşür (cutover öncesi 24 saat).

### C) .htaccess Redirect (codega.com.tr public_html'de)

`.htaccess` dosyası **zaten tema kökünde hazır** (v3.5.54). 

İçerikler:
- ✅ codega.com.tr eski `?page=X` URL'leri → yeni slug'lar
- ✅ Eski `/pages/X.php` URL'leri → yeni slug'lar
- ⏳ ca.codega.com.tr → codega.com.tr blok (yorumlu, cutover günü açılır)

---

## 🚀 AŞAMA 3: Cutover (Tek Gün - Bakım Penceresi)

### 1. YEDEK
```bash
# DirectAdmin → File Manager → public_html (mevcut codega.com.tr)
# ZIP indir, güvenli yere koy
# DB Backup: codega_database2027 → SQL dump
```

### 2. WiseCP Tema Aktarım
```bash
# DirectAdmin'de codega.com.tr public_html temizlenir
# WiseCP teması (ca.codega.com.tr public_html) → codega.com.tr public_html'e taşınır
# theme-config.php → 'site_url' => 'https://codega.com.tr' güncelle
```

### 3. DNS Cutover
```bash
# DirectAdmin → DNS Records
# codega.com.tr A kaydı yeni public_html'e işaret etmeli (zaten ediyor)
# ca.codega.com.tr → 301 redirect kuralı .htaccess'e ekle
```

### 4. Ödeme Gateway Callback URL'leri
```
iyzico merchant panel → callback URL: https://ca.codega.com.tr/... → https://codega.com.tr/...
PayTR merchant panel → aynı şekilde
GİB e-Fatura/e-Arşiv → callback URL kontrol
```

### 5. SMTP / E-posta
```
WiseCP Settings → System → Mail
  from-email: noreply@codega.com.tr (kontrol)
  SPF/DKIM/DMARC kayıtları codega.com.tr için doğrulanmış olmalı
```

### 6. Cron Joblar
```bash
# DirectAdmin Cron Manager
# Mevcut cron'ların path'leri kontrol:
#   /home/codega/domains/codega.com.tr/public_html/cron.php  ← yeni
#   eski: /home/codega/domains/ca.codega.com.tr/public_html/...
# Path güncelle gerekirse
```

### 7. Test
- [ ] codega.com.tr açılıyor → WiseCP teması
- [ ] ca.codega.com.tr → 301 redirect codega.com.tr'ye
- [ ] Eski URL'ler (`?page=erp`) → yeni slug'lara redirect
- [ ] Sepet, ödeme, fatura akışı çalışıyor
- [ ] e-posta gönderimi çalışıyor
- [ ] Migration runner DB'de eksik sayfaları ekledi mi (data/migration-applied.json kontrol)
- [ ] SSL sertifikası geçerli (codega.com.tr için)
- [ ] Footer link'ler doğru çalışıyor
- [ ] Mega menü 3-col gözüküyor

### 8. Google Search Console
- `codega.com.tr` (eski property) → kalır, redirect'leri tanır
- `ca.codega.com.tr` → eski property, 4-8 hafta içinde Google indeks geçişi
- Yeni sitemap.xml gönder: `https://codega.com.tr/sitemap.xml`

---

## ⏳ AŞAMA 4: Subdomain Kaldırma (+1 Ay Sonra)

301 redirect'ler 4-8 hafta çalıştıktan sonra Google indeks tamamen geçer:

```bash
# DirectAdmin → Subdomain Manager
# ca.codega.com.tr → SİL

# DNS Records  
# ca → A/CNAME kayıt SİL

# Test
# ca.codega.com.tr artık çalışmamalı
# codega.com.tr tek başına devam ediyor
```

---

## 🔧 Teknik Notlar

### Migration Runner Log
```bash
# Hata kontrolü:
tail -50 /tmp/codega-migration.log

# Manuel re-run:
# data/migration-applied.json dosyasını sil → bir sonraki sayfa açılışında yeniden uygulanır
```

### .htaccess Hassas Korumalar
- `migration.sql` web erişimi engelli (gizli)
- `data/` klasörü dış erişime kapalı
- `.git`, `.env`, `.bak` uzantıları gizli

### Yedek Stratejisi
- Cutover öncesi DirectAdmin'de **tam yedek** alınır
- 7 gün içinde sorunsuz çalışırsa eski public_html silinir
- 30 gün sonra ca. subdomain kaldırılır

---

## 📞 İletişim

Sorun olduğunda:
1. `tail -50 /tmp/codega-migration.log` (migration loglar)
2. WiseCP Logs → Settings → Logs (uygulama loglar)
3. DirectAdmin → Error Logs (Apache loglar)

