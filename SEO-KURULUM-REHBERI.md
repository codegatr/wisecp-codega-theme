# Codega Tema - SEO Kurulum Rehberi

## Onemli! Robots.txt ve Sitemap.xml YERLESIMI

**Tema klasoru degil, public_html kokunde olmali!**

```
/domains/codega.com.tr/public_html/
+- robots.txt        <- buraya kopyalanmali
+- sitemap.xml       <- buraya kopyalanmali
+- sitemap.php       <- buraya kopyalanmali
+- templates/website/Codega/
   +- robots.txt     <- bu sadece kaynak (tasinacak)
   +- sitemap.xml    <- bu sadece kaynak (tasinacak)
```

## Adim 1: Dosyalari Yerlestir

DirectAdmin > File Manager:

1. `/templates/website/Codega/robots.txt` => `/public_html/robots.txt` olarak **kopyala**
2. `/templates/website/Codega/sitemap.xml` => `/public_html/sitemap.xml` olarak **kopyala**
3. `/templates/website/Codega/sitemap.php` => `/public_html/sitemap.php` olarak **kopyala**

**Not**: WiseCP zaten kendi sitemap'ini olusturabilir. Once o varsa kontrol et:
- `https://codega.com.tr/sitemap.xml` ac, eger WiseCP'nin sitemap'i geliyorsa, bizim dosyayi yedekle ve degistir.

## Adim 2: Robots.txt Erisim Kontrolu

Tarayicida ac:
```
https://codega.com.tr/robots.txt
```

Icerigin gorunur olmasi gerekir.

## Adim 3: Sitemap.xml Erisim Kontrolu

```
https://codega.com.tr/sitemap.xml
```

XML formatinda 22+ URL gozukmeli.

## Adim 4: Google Search Console'a Sitemap Ekle

1. https://search.google.com/search-console adresine git
2. `codega.com.tr` property'sini sec
3. Sol menu => **Site haritalari**
4. URL kismina: `sitemap.xml` yaz
5. **Gonder** butonuna tikla

Birkac saat icinde Google sitemap'i isler.

## Adim 5: Search Console - Mevcut Sorunlari Coz

Screenshot'inda gordugum sorunlar:

### "Dogru standart etikete sahip alternatif sayfa" (39 sayfa)

**Sebep:** Eski kurulumda `<link rel="canonical">` bos geliyordu. Bu surumde duzeltildi.

**Cozum:**
1. v3.5.68+ yukle
2. Search Console > Etkilenen 39 sayfayi sec
3. **DOGRULAMA ISTE** butonuna bas

### "Yonlendirmeli sayfa" (5 sayfa)

**Sebep:** Bu **NORMAL** - `ca.codega.com.tr` URL'leri 301 ile yeni domain'e yonleniyor. Google bunlari ayri indekleyemez (dogru).

**Cozum:** Gormezden gel, problem degil.

### "Tarandi, dizine eklenmis degil" (2 sayfa)

**Sebep:** Icerik kalitesi yetersiz veya duplicate content.

**Cozum:** Hangi sayfa oldugunu bulmak icin Search Console'da liste.

## Adim 6: Google Bot ile Sayfa Test

Search Console > **URL Denetleme araci**

Test edilecek URL'ler:
- `https://codega.com.tr/`
- `https://codega.com.tr/erp-yazilimi.html`
- `https://codega.com.tr/hosting-products.html`
- `https://codega.com.tr/hakkimizda.html`

Her sayfa icin:
1. URL'i gir, test et
2. **"Indekslenmeye uygun"** mu? Kontrol et.
3. Sorun varsa **"Indekslemeyi Iste"** butonuna tikla.

## Adim 7: Schema.org Test (Rich Results)

https://search.google.com/test/rich-results adresine git

Test URL: `https://codega.com.tr/`

Beklenen sonuc:
- Organization (CODEGA)
- WebSite (CODEGA)

Eger hata varsa Schema.org markup hatasidir, bana yaz.

## Adim 8: Sayfa Hizi Optimize

https://pagespeed.web.dev/ adresine git

Test URL: `https://codega.com.tr/`

Hedef:
- Performance: 80+
- Accessibility: 95+
- Best Practices: 95+
- SEO: 100

## Adim 9: Bing Webmaster Tools (Opsiyonel)

1. https://www.bing.com/webmasters
2. Site ekle: `codega.com.tr`
3. Sitemap submit: `sitemap.xml`

## Adim 10: Yandex Webmaster (Opsiyonel)

1. https://webmaster.yandex.com/
2. Site ekle: `codega.com.tr`
3. Sitemap submit

---

## Surekli Kontrol

### Haftalik:
- Search Console > Sayfa kapsami raporu
- Yeni indeksleme hatalari var mi?
- Sitemap isleniyor mu?

### Aylik:
- Performans raporu (organik tiklamalar)
- Hangi sayfalar trafik aliyor?
- Hangi anahtar kelimelerle bulunuyor?

---

## SEO icin Gelecek Onerileri

1. **Blog yazilari**: `/articles/` ve `/news/` sayfalarina haftalik icerik
2. **Schema.org Product**: Hosting paketleri icin urun markup'i
3. **FAQ Schema**: Knowledgebase sayfalarinda soru-cevap
4. **Local Business**: Konya merkezli oldugunuz icin local SEO
5. **HTTPS**: Zaten aktif, devam ettir
6. **Mobile-first**: Mobil tasarim optimizasyon (yapildi)

---

## Hizli Baslangic Checklist

Yunus icin 5 dakikalik checklist:

- [ ] `robots.txt` public_html'e kopyalandi
- [ ] `sitemap.xml` public_html'e kopyalandi
- [ ] Search Console > Sitemap submitted
- [ ] 39 "alternatif sayfa" icin dogrulama istendi
- [ ] Ana sayfa URL Denetleme'den gecti
- [ ] Schema.org markup test edildi
