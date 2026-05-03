# 🚀 CODEGA Migration Yol Haritası

**Hedef:** `codega.com.tr` (ana site) içeriklerini `ca.codega.com.tr` (WiseCP)'ye aktarmak, sonra subdomain'i kaldırıp tek domain ile devam etmek.

**Tarih:** 2026-05-03 itibariyle planlanmıştır.

---

## 📊 Mevcut Durum Analizi

### codega.com.tr (ana site) içerikleri:

| Kategori | İçerik | Boyut | Migration Önceliği |
|---|---|---|---|
| Anasayfa | hero, otomasyon, fiyatlar, neden biz, entegrasyonlar | `data/anasayfa.json` (7KB) | **P1** Kısmen tamamlandı |
| Referanslar | 59 firma, 7 sektör | `data/referanslar.json` (15KB) | **✅ TAMAMLANDI v3.5.52** |
| ERP Yazılımı | Detaylı tanıtım sayfası | `pages/erp.php` (1011 satır) | **P1** Bekliyor |
| Bilgi Bankası | Kategoriler + makaleler | `data/bilgi_bankasi.json` (26KB) | **P2** WiseCP KB'da var |
| Kurumsal | KVKK, gizlilik, çerez, kariyer, sürdürülebilirlik | 6 JSON dosyası | **✅ TAMAMLANDI v3.5.46** |
| Vizyon | Şirket vizyonu | `data/kurumsal/vizyon.json` (3KB) | **P2** Bekliyor |
| Sosyal Sorumluluk | CSR sayfası | `data/kurumsal/sorumluluk.json` (1.6KB) | **✅ TAMAMLANDI v3.5.43** |
| Sistem Durum | Uptime monitor sayfası | `pages/sistem_durum.php` | **P3** Bekliyor |
| Hakkımızda | Şirket hikayesi | `pages/hakkimizda.php` | **✅ TAMAMLANDI v3.5.43** |
| Programs | ETA backup software | `programs/eta/` | **P3** İhtiyaç olursa |

### ca.codega.com.tr (WiseCP) zaten içerenler:

- ✅ Hosting / Domain ürünler (WiseCP core)
- ✅ Sepet / Ödeme / Fatura (WiseCP core)
- ✅ Kullanıcı Yönetimi
- ✅ Talep / Destek (WiseCP core)
- ✅ Yazılımlar (`softwares.php` + sektörel çözümler)
- ✅ Kurumsal sayfalar (8 adet, v3.5.43-46)
- ✅ Auth (sign-in, sign-up, forget) (v3.5.49-51)
- ✅ Anasayfa zengin 15 bölüm
- ✅ **Referanslar (v3.5.52 - YENİ)**

---

## 🎯 Migration Aşamaları

### **AŞAMA 1: İçerik Tamamlama (1-2 hafta)**

#### ADIM 1: ✅ Referanslar (v3.5.52 - TAMAMLANDI)

`/references` URL'inden erişim. Admin panel WiseCP page kayıt:
- **Tür:** normal page
- **Slug:** `references` veya `referanslar`
- **TR:** "Referanslarımız"
- **EN:** "Our References"

#### ADIM 2: ⏳ ERP Yazılımı Detay Sayfası

Ana sitedeki `pages/erp.php` (1011 satır) → tema kökünde `programs/erp.php` veya `software-erp.php`.

**İçerik özeti:**
- Hero: "Codega ERP — İşinizi tek panelden yönetin"
- 12 modül kartı: Cari, Stok, Fatura, Çek/Senet, Üretim, vs.
- Ekran görüntüleri carousel
- Özellikler grid (oto faturalama, e-fatura, e-irsaliye)
- Fiyatlandırma 3 paket
- Demo talep formu
- SSS (8-10 soru)

**Sonraki turda:** "ERP sayfası taşı" derseniz tek seferde yaparız.

#### ADIM 3: ⏳ Anasayfa Otomasyon Bölümü

Mevcut tema anasayfasına yeni bölüm eklenecek (15. bölüm sonrası):

```
[ÜST] -> Mevcut hero
[YENİ] -> Otomasyon Süreci Bölümü
   4 Adım: Sipariş & Ödeme → İşlem Kuyruğu → Provisioning → Aktivasyon
   Animasyonlu connector çizgi, ikonlar
[ALT] -> Mevcut bölümler devam
```

#### ADIM 4: ⏳ Vizyon Sayfası

`/vizyon` veya mevcut `hakkimizda` sayfasına bölüm olarak eklenebilir.

#### ADIM 5: ⏳ Sistem Durum Sayfası

`/system-status` URL'inden erişim. UptimeRobot API entegrasyonu veya statik servisler listesi:
- Hosting servisleri
- Domain DNS
- E-posta sunucuları
- Ödeme gateway

---

### **AŞAMA 2: Cross-domain Geçiş Hazırlığı (3-5 gün)**

#### A) DirectAdmin'de Domain Yapılandırması

```
Yeni durum:
- codega.com.tr           → public_html (ESKI: ana site)
- ca.codega.com.tr        → public_html (yeni: WiseCP teması)
- www.codega.com.tr       → 301 redirect → codega.com.tr
```

**İki seçenek:**

**SEÇENEK A — codega.com.tr ana domain olur (Önerilen):**
```
codega.com.tr ana ←→ WiseCP burada
ca.codega.com.tr  → 301 redirect → codega.com.tr
```
- Avantaj: SEO codega.com.tr alanında kalır (yıllarca emek)
- Dezavantaj: WiseCP'yi ana domain'e geçirmek gerek (DirectAdmin işlemi)

**SEÇENEK B — ca.codega.com.tr kalır:**
```
codega.com.tr     → 301 redirect → ca.codega.com.tr
ca.codega.com.tr  → WiseCP burada
```
- Avantaj: WiseCP yerinde kalır, az iş
- Dezavantaj: ca. SEO başlangıç (yeni domain gibi)

**Önerim: SEÇENEK A** — codega.com.tr ana domain (SEO korunur).

#### B) DNS Yönlendirme

```bash
# DirectAdmin → DNS Records
ca.codega.com.tr → CNAME → codega.com.tr (geçici, eski URL'ler için)
```

#### C) .htaccess Redirect (codega.com.tr public_html'de)

```apache
# Eski codega.com.tr URL'lerini WiseCP slug'larına yönlendir
RewriteEngine On
RewriteRule ^pages/erp\.php$ /programs/erp [R=301,L]
RewriteRule ^pages/referanslar\.php$ /references [R=301,L]
RewriteRule ^pages/hosting\.php$ /hosting-products [R=301,L]
RewriteRule ^pages/domain\.php$ /domain-checker [R=301,L]
RewriteRule ^pages/iletisim\.php$ /contact [R=301,L]
RewriteRule ^pages/hakkimizda\.php$ /about-us [R=301,L]
RewriteRule ^pages/fiyatlar\.php$ /hosting-products [R=301,L]
RewriteRule ^pages/bilgi_bankasi\.php$ /knowledgebase [R=301,L]
```

---

### **AŞAMA 3: Cutover (1 gün)**

**Adımlar:**
1. **YEDEK AL** — codega.com.tr public_html ZIP, DB dump
2. **codega.com.tr public_html** → WiseCP dosyaları taşınır (DirectAdmin file mover)
3. **codega_wisecp2027** DB → WiseCP yeni `wp_options.siteurl` güncellemesi (gerekirse)
4. **theme-config.php** → `'site_url' => 'https://codega.com.tr'` güncelle
5. **DNS güncellemesi** (TTL 5dk düşür önceden)
6. **ca.codega.com.tr** → 301 redirect kuralı public_html'de
7. **Test:**
   - codega.com.tr → WiseCP açılıyor mu
   - ca.codega.com.tr → codega.com.tr'ye yönleniyor mu
   - Eski URL'ler → yeni slug'lara redirect oluyor mu
   - Ödeme gateway (iyzico/PayTR) callback URL'leri güncellendi mi
   - SSL sertifikası geçerli mi

---

### **AŞAMA 4: Subdomain Kaldırma (1 gün, AŞAMA 3'ten 1 ay sonra)**

1 ay 301 redirect çalıştıktan sonra (Google indeksinin yeni URL'lere geçmesi için):

1. **DirectAdmin → Subdomain Manager** → `ca.codega.com.tr` SİL
2. **DNS Records** → `ca` A/CNAME kayıt SİL
3. **Test:** `ca.codega.com.tr` artık çalışmamalı

---

## ⚠️ Dikkat Edilecekler

### Ödeme Gateway Callback URL'leri
- iyzico: Merchant panelinde `https://ca.codega.com.tr/...` → `https://codega.com.tr/...`
- PayTR: Merchant panelinde aynı şekilde
- Bu yapılmazsa ödemeler ÇALIŞMAZ.

### E-posta SMTP Yapılandırması
- WiseCP `Settings → System → Mail` → from-email kontrol
- DKIM/SPF/DMARC kayıtları → codega.com.tr için doğru olmalı

### Cron Joblar
- DirectAdmin Cron Manager → mevcut cron'lar `/home/codega/domains/codega.com.tr/public_html/...` mı yoksa eski path'mi kontrol

### Google Search Console
- `codega.com.tr` (mevcut) → property kalır
- `ca.codega.com.tr` → eski property kalır (301 redirect'i Google'a bildirir)
- Sitemap güncelle: `codega.com.tr/sitemap.xml` → tüm WiseCP slug'ları

### SEO 301 Redirect İzleme
- Google Search Console → Coverage → 301 redirect'lerin işlenmesi 4-8 hafta sürer
- Bu süre boyunca AŞAMA 4 (subdomain kaldırma) ERTELENMELİ

---

## 📋 Yapılacaklar Listesi (Sıralı)

- [x] **v3.5.52 — Referanslar sayfası** (TAMAMLANDI)
- [ ] **v3.5.53 — ERP Yazılımı detay sayfası** (sonraki tur)
- [ ] **v3.5.54 — Anasayfa otomasyon bölümü** (sonraki tur)
- [ ] **v3.5.55 — Vizyon + Sistem Durum sayfaları** (sonraki tur)
- [ ] **AŞAMA 2** — DNS / .htaccess yapılandırma (Yunus + Claude beraber)
- [ ] **AŞAMA 3** — Cutover (DirectAdmin işlemi, planlı bakım penceresi)
- [ ] **1 AY BEKLE** — Google indeks geçişi
- [ ] **AŞAMA 4** — `ca.` subdomain kaldır

---

## 🤝 Sonraki Adım

1. **v3.5.52 zip'i yükle**, `/references` sayfasını test et
2. WiseCP admin panel → Pages → "References" sayfasını **2 dilde** ekle (TR + EN)
3. Mega menüye link ekle: Kurumsal → Referanslarımız
4. Onay verirsen **ADIM 2 (ERP sayfası)** ile devam edelim

**Sorular varsa:** Her aşama için "Bunu nasıl yaparım?" sorabilirsin, adım adım rehber yazarım.
