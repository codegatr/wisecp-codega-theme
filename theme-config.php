<?php
return [
    'meta' => [
        'name'        => 'Codega',
        'description' => 'Modern, mavi agirlikli CODEGA klasik temasi. Web yazilim, hosting, domain, SMS ve ozel yazilim hizmetleri icin tasarlanmistir.',
        'version'     => '3.1.2',
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
        'meta-color'       => '#1e40af',
        'text_color'       => '1e293b',

        // Banner içeriği
        'banner' => [
            'heading'      => 'Modern Yazilimla Isinizi Buyutun',
            'content'      => 'CODEGA olarak web yazilim, hosting, domain ve ozel yazilim cozumleriyle markanizi dijitale tasiyoruz.',
            'button_text1' => 'Hizmetlerimiz',
            'button_link1' => '/hosting-products',
            'button_text2' => 'Iletisim',
            'button_link2' => '/contact',
        ],

        // Bölüm açma/kapama
        'show_services' => 1,
        'show_features' => 1,
        'show_pricing'  => 1,
        'show_cta'      => 1,

        // Hizmetler
        'services' => [
            ['icon' => 'bi-code-slash',         'title' => 'Web Yazilim', 'text' => 'Kurumsal site, e-ticaret, ERP, CMS - modern PHP altyapisi.', 'link' => '/contact', 'color' => 'primary'],
            ['icon' => 'bi-hdd-network',        'title' => 'Hosting',     'text' => 'NVMe SSD diskli, LiteSpeed destekli hizli barindirma.',      'link' => '/hosting-products', 'color' => 'info'],
            ['icon' => 'bi-globe2',             'title' => 'Domain',      'text' => 'Yuzlerce uzantida domain tescili ve yonetimi.',              'link' => '/domain', 'color' => 'success'],
            ['icon' => 'bi-server',             'title' => 'Sunucu',      'text' => 'VDS / VPS / Dedicated sunucu cozumleri.',                    'link' => '/server-products', 'color' => 'warning'],
            ['icon' => 'bi-chat-square-dots',   'title' => 'Toplu SMS',   'text' => 'BTK onayli toplu SMS gonderim altyapisi.',                   'link' => '/sms-products', 'color' => 'secondary'],
            ['icon' => 'bi-puzzle',             'title' => 'Ozel Yazilim','text' => 'Isinize ozel ERP, CRM, takip sistemi ve API entegrasyonlari.','link' => '/contact', 'color' => 'danger'],
        ],

        // Avantajlar
        'features' => [
            ['icon' => 'bi-lightning-charge-fill', 'title' => 'Hizli Teslim',     'text' => 'Projelerinizi planladigimiz tarihte, eksiksiz teslim ederiz.'],
            ['icon' => 'bi-shield-check',          'title' => 'Guvenli Altyapi',  'text' => 'SSL, WAF, gunluk yedekleme - kurumsal seviyede guvenlik.'],
            ['icon' => 'bi-headset',                'title' => '7/24 Destek',     'text' => 'Turkce teknik destek, telefon ve ticket sistemi.'],
            ['icon' => 'bi-graph-up-arrow',         'title' => 'Olceklenebilir',  'text' => 'Buyudukce buyuyen altyapi - paket degistirme tek tik.'],
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
    ],
];
