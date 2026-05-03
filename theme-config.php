<?php
return [
    'meta' => [
        'name'        => 'Codega',
        'description' => 'Modern, kurumsal CODEGA teması. Web yazılım, hosting, domain, SMS ve özel yazılım hizmetleri için tasarlanmıştır.',
        'version'     => '3.5.69',
        'provider'    => 'CODEGA',
        'website'     => 'https://codega.com.tr',
        'image'       => '',
    ],
    'checking-version-url' => 'https://raw.githubusercontent.com/codegatr/wisecp-codega-theme/master/version.json',
    'settings' => [
        'header-type'      => 1,
        'clientArea-type'  => 1,

        // Renkler — CODEGA klasik
        'color1'           => '1e40af',
        'color2'           => '3b82f6',
        'meta-color'       => '#2E3B4E',
        'text_color'       => '1e293b',

        // Banner içeriği
        'banner' => [
            'heading'      => 'Modern Yazılımla İşinizi Büyütün',
            'content'      => 'CODEGA olarak web yazılım, hosting, domain ve özel yazılım çözümleriyle markanızı dijitale taşıyoruz.',
            'button_text1' => 'Hizmetlerimiz',
            'button_link1' => '/hosting-products',
            'button_text2' => 'İletişim',
            'button_link2' => '/contact',
        ],

        // Bölüm açma/kapama
        'show_services' => 1,
        'show_features' => 1,
        'show_pricing'  => 1,
        'show_cta'      => 1,

        // Hizmetler
        'services' => [
            ['icon' => 'bi-code-slash',         'title' => 'Web Yazılım', 'text' => 'Kurumsal site, e-ticaret, ERP, CMS - modern PHP altyapısı.', 'link' => '/contact', 'color' => 'primary'],
            ['icon' => 'bi-hdd-network',        'title' => 'Hosting',     'text' => 'NVMe SSD diskli, LiteSpeed destekli hızlı barındırma.',      'link' => '/hosting-products', 'color' => 'info'],
            ['icon' => 'bi-globe2',             'title' => 'Domain',      'text' => 'Yüzlerce uzantıda domain tescili ve yönetimi.',              'link' => '/domain', 'color' => 'success'],
            ['icon' => 'bi-server',             'title' => 'Sunucu',      'text' => 'VDS / VPS / Dedicated sunucu çözümleri.',                    'link' => '/server-products', 'color' => 'warning'],
            ['icon' => 'bi-chat-square-dots',   'title' => 'Toplu SMS',   'text' => 'BTK onaylı toplu SMS gönderim altyapısı.',                   'link' => '/sms-products', 'color' => 'secondary'],
            ['icon' => 'bi-puzzle',             'title' => 'Özel Yazılım','text' => 'İşinize özel ERP, CRM, takip sistemi ve API entegrasyonları.','link' => '/contact', 'color' => 'danger'],
        ],

        // Avantajlar
        'features' => [
            ['icon' => 'bi-lightning-charge-fill', 'title' => 'Hızlı Teslim',     'text' => 'Projelerinizi planladığımız tarihte, eksiksiz teslim ederiz.'],
            ['icon' => 'bi-shield-check',          'title' => 'Güvenli Altyapi',  'text' => 'SSL, WAF, gunluk yedekleme - kurumsal seviyede güvenlik.'],
            ['icon' => 'bi-headset',                'title' => '7/24 Destek',     'text' => 'Türkçe teknik destek, telefon ve ticket sistemi.'],
            ['icon' => 'bi-graph-up-arrow',         'title' => 'Ölçeklenebilir',  'text' => 'Buyudukce buyuyen altyapı - paket degistirme tek tik.'],
        ],

        // Sosyal medya
        'social' => [
            'facebook'  => '',
            'twitter'   => '',
            'instagram' => 'https://instagram.com/codegatr',
            'linkedin'  => 'https://linkedin.com/company/codega',
            'youtube'   => '',
            'github'    => 'https://github.com/codegatr',
        ],

        // İletişim
        'contact' => [
            'phone'    => '+90 510 220 42 06',
            'whatsapp' => '905102204206',
            'email'    => 'info@codega.com.tr',
            'address'  => 'Konya, Türkiye',
            'hours'    => 'Pazartesi - Cuma: 09:00 - 18:00',
        ],

        // codega.com.tr ANA SİTE ENTEGRASYONU (SSO + JSON API bridge)
        // Bu ayarlar codega-sso.php ve codega-api.php endpoint'leri tarafından okunur.
        'codega_integration' => [
            'enabled'        => true,                            // Bridge'i aç/kapat
            'shared_secret'  => 'CHANGE_ME_64_CHAR_HEX_KEY_AT_LEAST_32_CHARS_LONG',  // Ana site config.php ile birebir aynı olmalı
            'allowed_origin' => 'https://codega.com.tr',         // CORS + Origin header kontrolü
            'time_window'    => 60,                              // ±saniye (replay protection)
            'sso_default'    => 'my-account',                    // SSO sonrası varsayılan return route
            'log_requests'   => false,                           // Debug için bridge istek log'u
        ],
    ],
];
