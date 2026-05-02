# Codega - WiseCP Tema

Modern, mavi agirlikli, lisanssiz, tamamen ozellestirilebilir WiseCP temasi.

## Ozellikler

- **Modern Tasarim**: Plus Jakarta Sans + Mavi gradient + Bootstrap Icons (lokal)
- **WiseCP CORE Entegrasyonu**: $orders, $tickets, $domain_orders, Money/View/DateManager
- **Musteri Paneli**: Sidebar navigasyon, dashboard stats, urun detay sayfalari
- **Hosting Detay**: Kullanim progress bar (disk, trafik, e-posta), DNS yonetim kartlari
- **Otomatik Guncelleme**: GitHub Releases uzerinden tek tikla update
- **Edge Tracking Prevention Bypass**: Bootstrap Icons lokal sunulur
- **CSRF Token Destegi**: Tum formlarda otomatik token

## Kurulum

1. ZIP'i indir
2. `templates/website/Codega/` klasorune kopyala
3. WiseCP Admin -> Tema Yonetimi -> Codega -> Aktif Et

## Otomatik Guncelleme

WiseCP admin panelinde tema sayfasinda "Guncelle" butonu otomatik gozukur (manifest.json checking_version_url uzerinden).

Manuel erisim:
```
https://yoursite.com/templates/website/Codega/update.php?key=<update_secret>
```

`update_secret`: `theme-config.php` icindeki settings -> update_secret alanindan okunur (ilk calistirmada otomatik uretilir).

## Gelistirici

CODEGA - https://codega.com.tr
