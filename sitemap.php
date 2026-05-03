<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * CODEGA - Dinamik Sitemap Generator
 *
 * URL: /sitemap.xml -> sitemap.php (rewrite ile)
 * Format: XML sitemap (Google Search Console uyumlu)
 *
 * İçerik:
 *  1. Tema corporate sayfaları (statik liste - 18 sayfa)
 *  2. WiseCP Knowledgebase makaleleri (DB'den dinamik)
 *  3. WiseCP News makaleleri (DB'den)
 *  4. WiseCP Articles (blog) (DB'den)
 *  5. WiseCP Hosting paketleri (DB'den)
 *  6. WiseCP Server paketleri (DB'den)
 *  7. WiseCP SMS paketleri (DB'den)
 *  8. WiseCP Software paketleri (DB'den)
 *  9. WiseCP Domain extension fiyatları (sayfa)
 */

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex'); // Sitemap'in kendisi indekslenmesin

// Site URL'i
$site_url = '';
if (defined('APP_URI')) $site_url = rtrim(APP_URI, '/');
elseif (isset($_SERVER['HTTP_HOST'])) $site_url = 'https://' . $_SERVER['HTTP_HOST'];
else $site_url = 'https://codega.com.tr';

$today = date('Y-m-d');

// Yardımcı: URL temizle (XML escape)
$xml_safe = function($url) use ($site_url) {
    $full = strpos($url, 'http') === 0 ? $url : $site_url . $url;
    // XML special chars
    return htmlspecialchars($full, ENT_QUOTES | ENT_XML1, 'UTF-8');
};

// =====================================================
// 1. STATİK TEMA SAYFALARI
// =====================================================
$static_pages = [
    // Ana sayfa
    ['url' => '/', 'priority' => '1.0', 'freq' => 'daily'],

    // Hizmet sayfaları (yüksek öncelik)
    ['url' => '/hosting-products.html',  'priority' => '0.9', 'freq' => 'daily'],
    ['url' => '/domain-checker.html',    'priority' => '0.9', 'freq' => 'weekly'],
    ['url' => '/server-products.html',   'priority' => '0.9', 'freq' => 'weekly'],
    ['url' => '/sms-products.html',      'priority' => '0.8', 'freq' => 'weekly'],
    ['url' => '/softwares.html',         'priority' => '0.8', 'freq' => 'weekly'],
    ['url' => '/erp-yazilimi.html',      'priority' => '0.9', 'freq' => 'weekly'],

    // Bilgi/içerik sayfaları
    ['url' => '/knowledgebase.html',     'priority' => '0.8', 'freq' => 'weekly'],
    ['url' => '/news.html',              'priority' => '0.7', 'freq' => 'weekly'],
    ['url' => '/articles.html',          'priority' => '0.7', 'freq' => 'weekly'],

    // Kurumsal
    ['url' => '/hakkimizda.html',        'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/vizyon.html',            'priority' => '0.6', 'freq' => 'monthly'],
    ['url' => '/referanslarimiz.html',   'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/kariyer.html',           'priority' => '0.6', 'freq' => 'weekly'],
    ['url' => '/sosyal-sorumluluk.html', 'priority' => '0.5', 'freq' => 'monthly'],
    ['url' => '/surdurulebilirlik.html', 'priority' => '0.5', 'freq' => 'monthly'],

    // İletişim & Destek
    ['url' => '/contact.html',           'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/sistem-durumu.html',     'priority' => '0.6', 'freq' => 'daily'],

    // Yasal (düşük öncelik ama gerekli)
    ['url' => '/kvkk-aydinlatma-metni.html', 'priority' => '0.3', 'freq' => 'yearly'],
    ['url' => '/gizlilik-politikasi.html',   'priority' => '0.3', 'freq' => 'yearly'],
    ['url' => '/cerez-politikasi.html',      'priority' => '0.3', 'freq' => 'yearly'],
    ['url' => '/hizmet-sozlesmesi.html',     'priority' => '0.3', 'freq' => 'yearly'],
];

// =====================================================
// 2. WISECP DİNAMİK İÇERİKLER (DB'den)
// =====================================================
$dynamic_urls = [];

try {
    if (class_exists('DB') && isset(DB::$db)) {

        // 2.1 - Knowledgebase (Bilgi Bankası) makaleleri
        try {
            $kb_q = DB::$db->query("SELECT route, ulast_update FROM knowledgebase_lang WHERE lang='tr' AND route!='' LIMIT 500");
            if ($kb_q) {
                while ($kb = is_object($kb_q) && method_exists($kb_q, 'fetch') ? $kb_q->fetch() : null) {
                    if (!empty($kb['route'])) {
                        $dynamic_urls[] = [
                            'url' => '/knowledgebase/' . $kb['route'] . '.html',
                            'priority' => '0.6',
                            'freq' => 'monthly',
                            'lastmod' => !empty($kb['ulast_update']) ? date('Y-m-d', $kb['ulast_update']) : $today,
                        ];
                    }
                }
            }
        } catch(\Throwable $e) { /* tablo yoksa geç */ }

        // 2.2 - News (Haberler)
        try {
            $news_q = @DB::$db->query("SELECT route, ulast_update FROM news_lang WHERE lang='tr' AND route!='' LIMIT 500");
            if ($news_q) {
                while ($n = is_object($news_q) && method_exists($news_q, 'fetch') ? $news_q->fetch() : null) {
                    if (!empty($n['route'])) {
                        $dynamic_urls[] = [
                            'url' => '/news/' . $n['route'] . '.html',
                            'priority' => '0.6',
                            'freq' => 'monthly',
                            'lastmod' => !empty($n['ulast_update']) ? date('Y-m-d', $n['ulast_update']) : $today,
                        ];
                    }
                }
            }
        } catch(\Throwable $e) {}

        // 2.3 - Articles (Blog)
        try {
            $art_q = @DB::$db->query("SELECT route, ulast_update FROM articles_lang WHERE lang='tr' AND route!='' LIMIT 500");
            if ($art_q) {
                while ($a = is_object($art_q) && method_exists($art_q, 'fetch') ? $art_q->fetch() : null) {
                    if (!empty($a['route'])) {
                        $dynamic_urls[] = [
                            'url' => '/articles/' . $a['route'] . '.html',
                            'priority' => '0.6',
                            'freq' => 'monthly',
                            'lastmod' => !empty($a['ulast_update']) ? date('Y-m-d', $a['ulast_update']) : $today,
                        ];
                    }
                }
            }
        } catch(\Throwable $e) {}

        // 2.4 - Hosting Paketleri (eğer route'ları varsa)
        try {
            $hp_q = @DB::$db->query("SELECT category_id, route FROM hosting_products WHERE active=1 LIMIT 500");
            if ($hp_q) {
                while ($hp = is_object($hp_q) && method_exists($hp_q, 'fetch') ? $hp_q->fetch() : null) {
                    if (!empty($hp['route'])) {
                        $dynamic_urls[] = [
                            'url' => '/hosting/' . $hp['route'] . '.html',
                            'priority' => '0.7',
                            'freq' => 'weekly',
                            'lastmod' => $today,
                        ];
                    }
                }
            }
        } catch(\Throwable $e) {}

        // 2.5 - Server Paketleri
        try {
            $sp_q = @DB::$db->query("SELECT route FROM server_products WHERE active=1 LIMIT 500");
            if ($sp_q) {
                while ($sp = is_object($sp_q) && method_exists($sp_q, 'fetch') ? $sp_q->fetch() : null) {
                    if (!empty($sp['route'])) {
                        $dynamic_urls[] = [
                            'url' => '/server/' . $sp['route'] . '.html',
                            'priority' => '0.7',
                            'freq' => 'weekly',
                            'lastmod' => $today,
                        ];
                    }
                }
            }
        } catch(\Throwable $e) {}

        // 2.6 - Software Paketleri
        try {
            $sw_q = @DB::$db->query("SELECT route FROM softwares WHERE active=1 LIMIT 500");
            if ($sw_q) {
                while ($sw = is_object($sw_q) && method_exists($sw_q, 'fetch') ? $sw_q->fetch() : null) {
                    if (!empty($sw['route'])) {
                        $dynamic_urls[] = [
                            'url' => '/software/' . $sw['route'] . '.html',
                            'priority' => '0.7',
                            'freq' => 'monthly',
                            'lastmod' => $today,
                        ];
                    }
                }
            }
        } catch(\Throwable $e) {}

        // 2.7 - Custom Pages (admin tarafından eklenmiş ek sayfalar)
        try {
            $pg_q = @DB::$db->query("SELECT route FROM pages_lang WHERE lang='tr' AND route!='' LIMIT 500");
            if ($pg_q) {
                $static_routes = array_column($static_pages, 'url');
                while ($pg = is_object($pg_q) && method_exists($pg_q, 'fetch') ? $pg_q->fetch() : null) {
                    if (!empty($pg['route'])) {
                        $url = '/' . $pg['route'] . '.html';
                        if (!in_array($url, $static_routes)) {
                            $dynamic_urls[] = [
                                'url' => $url,
                                'priority' => '0.5',
                                'freq' => 'monthly',
                                'lastmod' => $today,
                            ];
                        }
                    }
                }
            }
        } catch(\Throwable $e) {}
    }
} catch (\Throwable $e) {
    // DB yoksa sadece statik
}

// =====================================================
// 3. XML ÇIKTI
// =====================================================
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($static_pages as $p): ?>
    <url>
        <loc><?php echo $xml_safe($p['url']); ?></loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq><?php echo $p['freq']; ?></changefreq>
        <priority><?php echo $p['priority']; ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($dynamic_urls as $p): ?>
    <url>
        <loc><?php echo $xml_safe($p['url']); ?></loc>
        <lastmod><?php echo $p['lastmod'] ?? $today; ?></lastmod>
        <changefreq><?php echo $p['freq']; ?></changefreq>
        <priority><?php echo $p['priority']; ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
