# CODEGA WiseCP Theme

> Premium Navy/Gold theme for **WiseCP 3.x** with full **codega.com.tr** integration  
> SSO bridge · Server-to-server data API · Cormorant Garamond + Inter typography

**Version:** 1.0.0  
**Compatibility:** WiseCP ≥ 3.0, PHP ≥ 7.4  
**Author:** CODEGA — codega.com.tr

---

## Özellikler

- **Tam marka kimliği** — Navy `#0a1628` + Gold `#d4a574`, Cormorant Garamond display, Inter body
- **codega.com.tr SSO köprüsü** — Kullanıcılar ana siteden tek tıkla WiseCP'ye giriş yapar
- **Server-to-server API** — codega.com.tr backend'i müşterinin hizmet/fatura verisini WiseCP'den çekebilir
- **Replay korumalı imza** — HMAC-SHA256, ±60s timestamp skew, nonce uniqueness
- **Müşteri paneli** — Dashboard, hizmetler, faturalar, destek talepleri
- **Mağaza ön yüzü** — Hosting paketleri, domain arama, sepet
- **Responsive** — 5 breakpoint, mobil sidebar toggle, hero stats grid'i kırılmıyor
- **Update sistemi** — `version.json` + GitHub Releases ile otomatik güncelleme

---

## Dosya Yapısı

```
templates/website/codega/
├── theme.php                  # WiseCP'nin require ettiği ana sınıf (Codega_Theme)
├── theme-config.php           # Renkler, ayar şeması, varsayılanlar
├── theme-settings.php         # Admin > Themes > CODEGA > Settings paneli
├── version.json               # Sürüm ve uyumluluk metadata
│
├── css/
│   └── wisecp.php            # Dinamik CSS (PHP ile renkler inject ediliyor)
│
├── js/
│   └── codega.js             # Mobile sidebar toggle, smooth interactions
│
├── inc/
│   ├── meta.php              # <head> tagleri, OG/Twitter, favicon
│   ├── header.php            # Üst navigasyon
│   ├── footer.php            # 4 sütunlu footer
│   ├── client-sidebar.php    # Müşteri paneli yan menüsü
│   └── codega-bridge.php     # SSO + API bridge sınıfı (Codega_Bridge)
│
├── pages/
│   ├── index.php             # Anasayfa — hero, features, pricing, CTA
│   ├── login.php             # Giriş (e-posta + codega.com.tr SSO butonu)
│   ├── register.php          # Kayıt
│   ├── lostpassword.php      # Şifre sıfırlama
│   ├── client-home.php       # Müşteri paneli ana ekranı
│   ├── client-services.php   # Hizmetler tablosu
│   ├── client-invoices.php   # Faturalar tablosu
│   ├── 404.php
│   └── common-needs.php      # Sipariş/ödeme/talep durum gösterimi
│
├── api/
│   ├── codega-sso.php        # SSO endpoint (kullanıcı doğrulama + giriş)
│   └── codega-api.php        # Veri API'si (services/invoices/summary/ping)
│
├── locale/
│   ├── tr.php                # Türkçe stringler
│   └── en.php                # İngilizce stringler
│
└── img/
    ├── logo.svg              # Tam logo (koyu metin)
    ├── logo-light.svg        # Beyaz/altın logo (dark hero üstü)
    ├── favicon.svg           # 64×64 favicon
    └── og-cover.svg          # 1200×630 sosyal medya kartı
```

---

## Kurulum

### 1) Tema dosyalarını kopyala

ZIP içeriğini WiseCP'nin kurulu olduğu sunucuda şu yola çıkar:

```
/path/to/wisecp/templates/website/codega/
```

### 2) Temayı aktif et

WiseCP Admin → **Themes** → **CODEGA** → **Aktif Et**.

### 3) Marka rengini ve SSO secret'ini ayarla

WiseCP Admin → **Themes** → **CODEGA** → **Settings**:

- **Ana Renk:** `#0a1628` (lacivert)
- **Vurgu Rengi:** `#d4a574` (altın)
- **codega.com.tr SSO Aktif:** ☑
- **codega.com.tr URL:** `https://codega.com.tr`
- **HMAC Paylaşılan Anahtar:** **Sunucuda şu komutla üret ve buraya yapıştır:**

```bash
openssl rand -hex 32
```

Üretilen anahtarı **mutlaka** codega.com.tr tarafına da girmelisin (aşağıdaki entegrasyon bölümüne bak).

### 4) DNS ve SSL

ca.codega.com.tr için DirectAdmin/Plesk/cPanel üzerinden A record + Let's Encrypt SSL.

### 5) URL test

- `https://ca.codega.com.tr/` → Anasayfa (CODEGA marka kimliği görmelisin)
- `https://ca.codega.com.tr/login` → Login sayfası, "codega.com.tr ile Giriş Yap" butonu görünmeli
- `https://ca.codega.com.tr/codega-api/ping` → POST'a 401 dönmeli (imzasız istek)

---

## codega.com.tr Tarafı Entegrasyon Kodu

### A) Ortam değişkeni

codega.com.tr'nin `.env` veya `config.php` dosyasına:

```php
define('CODEGA_WISECP_URL',    'https://ca.codega.com.tr');
define('CODEGA_WISECP_SECRET', 'BURADA_AYNI_HMAC_SECRET'); // openssl rand -hex 32
```

### B) "Müşteri Paneli" linki — SSO redirect

codega.com.tr'de oturum açmış kullanıcı "Müşteri Panelime Git" butonuna bastığında:

```php
<?php
function codega_wisecp_sso_url($email, $redirect_to = '/clientarea')
{
    $ts    = time();
    $nonce = bin2hex(random_bytes(16));
    $sig   = hash_hmac('sha256', "{$email}|{$ts}|{$nonce}", CODEGA_WISECP_SECRET);

    $params = http_build_query([
        'email'    => $email,
        'ts'       => $ts,
        'nonce'    => $nonce,
        'sig'      => $sig,
        'redirect' => $redirect_to,
    ]);

    return CODEGA_WISECP_URL . '/codega-sso?' . $params;
}

// Örnek kullanım:
$url = codega_wisecp_sso_url($user->email, '/clientarea');
echo '<a href="' . htmlspecialchars($url) . '">Müşteri Paneline Git</a>';
```

> **Önemli:** Token tek kullanımlık (60 saniye geçerli, nonce uniqueness). Aynı token tekrar denenirse `403 Forbidden` döner.

### C) Veri çekme — server-to-server API

codega.com.tr backend'i kullanıcı dashboard'unda WiseCP'deki güncel hizmet/fatura sayılarını göstermek istediğinde:

```php
<?php
function codega_wisecp_api($endpoint, array $body = [])
{
    $path  = '/codega-api/' . $endpoint;
    $url   = CODEGA_WISECP_URL . $path;

    $body_json = json_encode($body, JSON_UNESCAPED_UNICODE);
    $ts        = time();
    $nonce     = bin2hex(random_bytes(16));
    $body_hash = hash('sha256', $body_json);

    $payload   = "POST|{$path}|{$ts}|{$nonce}|{$body_hash}";
    $signature = hash_hmac('sha256', $payload, CODEGA_WISECP_SECRET);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body_json,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-Codega-Timestamp: ' . $ts,
            'X-Codega-Nonce: '     . $nonce,
            'X-Codega-Signature: ' . $signature,
        ],
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http !== 200) return null;
    $data = json_decode($resp, true);
    return $data['ok'] ? $data['data'] : null;
}

// === Örnek kullanımlar ===

// Health check
$ping = codega_wisecp_api('ping');
// → ['pong' => true, 'time' => 1714560000]

// Müşteri özeti
$summary = codega_wisecp_api('summary', ['email' => 'musteri@firma.com']);
// → [
//     'user'   => ['id' => 42, 'name' => 'Ali Veli', ...],
//     'counts' => ['active_services' => 3, 'unpaid_invoices' => 1, 'open_tickets' => 0]
// ]

// Hizmet listesi
$services = codega_wisecp_api('services', ['email' => 'musteri@firma.com']);
// → ['services' => [ {id,name,status,total,due_date,...}, ... ]]

// Fatura listesi
$invoices = codega_wisecp_api('invoices', ['email' => 'musteri@firma.com']);
```

### D) Endpoint Referansı

| Endpoint | Body | Yanıt |
|---|---|---|
| `POST /codega-api/ping` | `{}` | `{pong: true, time: <unix>}` |
| `POST /codega-api/summary` | `{email}` | `{user, counts: {active_services, unpaid_invoices, open_tickets}}` |
| `POST /codega-api/services` | `{email}` | `{services: [...]}` |
| `POST /codega-api/invoices` | `{email}` | `{invoices: [...]}` |

**Headers (her istekte zorunlu):**
- `X-Codega-Timestamp` — Unix timestamp
- `X-Codega-Nonce` — Random unique string (16+ chars)
- `X-Codega-Signature` — `HMAC-SHA256(secret, "POST|{path}|{ts}|{nonce}|{sha256(body)}")`

**Hata kodları:**
- `401` — İmza geçersiz, timestamp süresi dolmuş, veya nonce daha önce kullanılmış
- `400` — Eksik parametre
- `404` — Kullanıcı bulunamadı
- `405` — POST dışında metod
- `500` — DB hatası

---

## SSO Akış Şeması

```
┌──────────────┐                                    ┌──────────────────┐
│ codega.com.tr│                                    │ ca.codega.com.tr │
│ (Ana Site)   │                                    │ (WiseCP)         │
└──────┬───────┘                                    └────────┬─────────┘
       │                                                     │
       │ 1. Kullanıcı "Müşteri Paneli" butonuna basar       │
       │                                                     │
       │ 2. SSO URL üret:                                    │
       │    sig = HMAC-SHA256(secret, "email|ts|nonce")     │
       │    /codega-sso?email=...&ts=...&nonce=...&sig=...  │
       │                                                     │
       │ ───────────  3. 302 Redirect ──────────────────────▶│
       │                                                     │
       │                                  4. Codega_Bridge:: │
       │                                     validateSso()   │
       │                                  - HMAC doğrula     │
       │                                  - timestamp ±60s   │
       │                                  - nonce unique mi  │
       │                                                     │
       │                                  5. WiseCP'de       │
       │                                     user'ı bul      │
       │                                     (yoksa register'a yönlendir)
       │                                                     │
       │                                  6. Session başlat  │
       │                                                     │
       │                                  7. /clientarea'ya  │
       │                                     redirect        │
       │                                                     │
       │                                                     │
```

---

## Geliştirme Notları

### Tema renklerini değiştir

WiseCP Admin > Themes > CODEGA > Settings panelinden ayarlanır. Veya doğrudan:

```php
// theme-config.php içinde
"settings" => [
    "color1"     => "0a1628",    // Lacivert (primary)
    "color2"     => "d4a574",    // Altın (accent)
    "text-color" => "1a2238",    // Yazı rengi
    ...
]
```

CSS otomatik olarak `--cg-navy`, `--cg-gold`, `--cg-text` CSS değişkenlerine inject edilir.

### Yeni sayfa ekle

`pages/yeni-sayfa.php` oluştur, içinde:

```php
<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$title = "Yeni Sayfa";
include __DIR__ . '/../inc/meta.php';
?>
<body>
<?php include __DIR__ . '/../inc/header.php'; ?>

<!-- içerik -->

<?php include __DIR__ . '/../inc/footer.php'; ?>
```

URL: `https://ca.codega.com.tr/yeni-sayfa`

### Logo değiştir

`img/logo.svg` ve `img/logo-light.svg` dosyalarını değiştir. Header bunları otomatik kullanır (şu anda inline SVG ile çiziliyor — değiştirmek istersen `inc/header.php`'deki `.cg-logo` div'ini güncelle).

---

## Güvenlik

- **HMAC-SHA256** imzalama, ±60 saniye timestamp skew (replay önleme)
- **Nonce uniqueness** — son 1000 nonce dosyada saklanır, 2x skew sonrası temizlenir
- **Constant-time** string karşılaştırma (`hash_equals`)
- **Sadece HTTPS** — production'da TLS şart
- **CSRF tokenleri** — login/register/lostpassword formlarında WiseCP'nin kendi mekanizmasıyla
- **Same-origin redirect** — SSO sonrası `redirect` parametresi sadece `/path` formatında kabul edilir

---

## Sorun Giderme

| Sorun | Çözüm |
|---|---|
| SSO 403 dönüyor | İki tarafta da aynı `secret` mi? Sunucu saatleri eşleşiyor mu? |
| API 401 dönüyor | İmza payload'unu kontrol et: `POST|/codega-api/X|TS|NONCE|sha256(body)` |
| CSS yüklenmiyor | `/templates/website/codega/css/wisecp.css` route'una giderek test et |
| Logo görünmüyor | `img/` klasörü yüklü mü? File permissions 644 olmalı |
| Nonce cache hatası | `CACHE_DIR` veya `/tmp` yazılabilir mi? |

---

## Lisans

Proprietary — CODEGA dahili kullanım. Dış paylaşım yasaktır.

---

**CODEGA** · Konya, Türkiye · 2026  
[codega.com.tr](https://codega.com.tr)
