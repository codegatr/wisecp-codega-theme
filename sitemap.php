<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA - Dinamik Sitemap Generator
 *
 * Kullanım: /sitemap.xml URL'i bu dosyayı çalıştırır (rewrite gerekir)
 * VEYA: theme-config.php'de routes ile tanımlanır
 *
 * Çıktı: XML sitemap (Google Search Console için)
 */

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

// Site URL'i (theme-config.php'den veya runtime)
$site_url = '';
if (defined('APP_URI')) $site_url = rtrim(APP_URI, '/');
elseif (isset($_SERVER['HTTP_HOST'])) $site_url = 'https://' . $_SERVER['HTTP_HOST'];
else $site_url = 'https://codega.com.tr';

$today = date('Y-m-d');

// Statik tema sayfaları
$static_pages = [
    ['url' => '/', 'priority' => '1.0', 'freq' => 'daily'],
    ['url' => '/hosting-products.html', 'priority' => '0.9', 'freq' => 'daily'],
    ['url' => '/domain-checker.html', 'priority' => '0.9', 'freq' => 'weekly'],
    ['url' => '/softwares.html', 'priority' => '0.8', 'freq' => 'weekly'],
    ['url' => '/erp-yazilimi.html', 'priority' => '0.9', 'freq' => 'weekly'],
    ['url' => '/referanslarimiz.html', 'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/vizyon.html', 'priority' => '0.6', 'freq' => 'monthly'],
    ['url' => '/hakkimizda.html', 'priority' => '0.6', 'freq' => 'monthly'],
    ['url' => '/sistem-durumu.html', 'priority' => '0.7', 'freq' => 'daily'],
    ['url' => '/kariyer.html', 'priority' => '0.6', 'freq' => 'weekly'],
    ['url' => '/sosyal-sorumluluk.html', 'priority' => '0.5', 'freq' => 'monthly'],
    ['url' => '/surdurulebilirlik.html', 'priority' => '0.5', 'freq' => 'monthly'],
    ['url' => '/contact.html', 'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/knowledgebase.html', 'priority' => '0.8', 'freq' => 'weekly'],
    // Yasal
    ['url' => '/kvkk-aydinlatma-metni.html', 'priority' => '0.3', 'freq' => 'yearly'],
    ['url' => '/gizlilik-politikasi.html', 'priority' => '0.3', 'freq' => 'yearly'],
    ['url' => '/cerez-politikasi.html', 'priority' => '0.3', 'freq' => 'yearly'],
    ['url' => '/hizmet-sozlesmesi.html', 'priority' => '0.3', 'freq' => 'yearly'],
];

// WiseCP DB'den dinamik sayfaları çek (varsa)
$dynamic_urls = [];
try {
    if (class_exists('DB') && isset(DB::$db)) {
        // Knowledgebase makaleleri
        $kb_q = DB::$db->select('knowledgebase_lang')
            ->where('lang', '=', 'tr')
            ->get();
        if (is_array($kb_q)) {
            foreach ($kb_q as $kb) {
                if (!empty($kb['route'])) {
                    $dynamic_urls[] = [
                        'url' => '/knowledgebase/' . $kb['route'] . '.html',
                        'priority' => '0.6',
                        'freq' => 'monthly'
                    ];
                }
            }
        }

        // Pages (tema-yönetilen ek olarak admin-eklenmiş)
        $page_q = DB::$db->select('pages_lang')
            ->where('lang', '=', 'tr')
            ->get();
        if (is_array($page_q)) {
            foreach ($page_q as $pg) {
                if (!empty($pg['route'])) {
                    $url = '/' . $pg['route'] . '.html';
                    // Statik listede yoksa ekle
                    $static_routes = array_column($static_pages, 'url');
                    if (!in_array($url, $static_routes)) {
                        $dynamic_urls[] = [
                            'url' => $url,
                            'priority' => '0.5',
                            'freq' => 'monthly'
                        ];
                    }
                }
            }
        }
    }
} catch (\Throwable $e) {
    // DB hatası - sadece statik sayfalar listelenir
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($static_pages as $p): ?>
    <url>
        <loc><?php echo $site_url . $p['url']; ?></loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq><?php echo $p['freq']; ?></changefreq>
        <priority><?php echo $p['priority']; ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($dynamic_urls as $p): ?>
    <url>
        <loc><?php echo $site_url . $p['url']; ?></loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq><?php echo $p['freq']; ?></changefreq>
        <priority><?php echo $p['priority']; ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
