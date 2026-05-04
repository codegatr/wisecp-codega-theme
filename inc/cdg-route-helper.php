<?php
/**
 * cdg-route-helper.php
 *
 * WiseCP route slug helper - aktif dil/lokalizasyona gore dogru URL uretir.
 *
 * Sorun: CRLink slug'i bilmedigi durumlarda ham slug dondurur (ornek: 'order-steps-p').
 * Cozum: WiseCP'nin website-routes lokalizasyon haritasini direkt okuyup URL'i biz insa ediyoruz.
 *
 * TR locale: 'order-steps-p' => 'siparis/(?)/(?)/(?)'  -> /siparis/hosting/103/1
 * EN locale: 'order-steps-p' => 'order-steps/(?)/(?)/(?)' -> /order-steps/hosting/103/1
 *
 * Kullanim:
 *   $url = cdg_route('order-steps-p', ['hosting', 103, 1]);
 *   $url = cdg_buy_link('hosting', 103);
 *   $url = cdg_basket_link();
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_route')) {
    /**
     * WiseCP slug -> URL ceviri (aktif dile gore)
     *
     * @param string $slug    'order-steps-p', 'basket', 'home' vb.
     * @param array  $params  URL parametreleri ['hosting', 103, 1]
     * @return string         Tam URL (http://site.com/siparis/hosting/103/1)
     */
    function cdg_route($slug, $params = []) {
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';

        // 1) CRLink dene (WiseCP'nin kendi yontemi)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = @Controllers::$init->CRLink($slug, $params);
                // CRLink slug'i bilmiyorsa ham slug dondurur, kontrol et
                if($url && strpos($url, $slug) === false) {
                    return $url;
                }
            } catch(\Throwable $e) { /* fallback'e dus */ }
        }

        // 2) MANUEL inşa: website-routes haritasini oku
        $routes = cdg_get_website_routes();

        if(isset($routes[$slug]) && is_array($routes[$slug]) && isset($routes[$slug][0])) {
            $pattern = $routes[$slug][0]; // 'siparis/(?)/(?)/(?)'

            // (?) yerine parametreleri sirayla yerlestir
            $url_path = $pattern;
            foreach($params as $param) {
                $url_path = preg_replace('/\(\?\)/', (string)$param, $url_path, 1);
            }
            // Yerlestirilemeyen (?) varsa temizle
            $url_path = preg_replace('/\/\(\?\)/', '', $url_path);

            return $base . '/' . ltrim($url_path, '/');
        }

        // 3) Son care: bilinen slug'lar icin hardcoded fallback
        static $hardcoded_fallbacks = [
            'order-steps-p' => '/siparis/{0}/{1}',     // params: [type, id, step]
            'order-steps'   => '/siparis/{0}/{1}',     // params: [type, id]
            'basket'        => '/sepet',
            'home'          => '/anasayfa',
        ];

        if(isset($hardcoded_fallbacks[$slug])) {
            $url = $hardcoded_fallbacks[$slug];
            foreach($params as $i => $param) {
                $url = str_replace('{' . $i . '}', (string)$param, $url);
            }
            // Bos placeholder temizle
            $url = preg_replace('/\/\{\d+\}/', '', $url);
            return $base . $url;
        }

        return '';
    }
}

if(!function_exists('cdg_get_website_routes')) {
    /**
     * Aktif dilin website-routes haritasini al, statik cache et
     */
    function cdg_get_website_routes() {
        static $cache = null;
        if($cache !== null) return $cache;

        // 1) WiseCP'nin ___ helper'i ile aktif dilin route'larini al
        if(function_exists('___')) {
            try {
                $routes = @___("website-routes/website-routes", false);
                if(is_array($routes) && !empty($routes)) {
                    $cache = $routes;
                    return $cache;
                }
            } catch(\Throwable $e) {}
        }

        // 2) Direkt dosyadan oku (fallback)
        $lang = 'tr'; // varsayilan TR (Codega TR site)
        if(defined('SLANG')) $lang = SLANG;
        elseif(defined('LANG_FOLDER')) {
            // SLANG yoksa active language tespit et
            if(class_exists('Filter') && method_exists('Filter', 'init')) {
                $detected = @Filter::init('REQUEST/lang', 'letters');
                if($detected) $lang = $detected;
            }
        }

        $routes_file = '';
        if(defined('LANG_DIR')) {
            $routes_file = LANG_DIR . $lang . DS . 'website-routes.php';
            if(!file_exists($routes_file)) {
                $routes_file = LANG_DIR . $lang . DS . 'website-routes-default.php';
            }
        }
        if(!$routes_file) {
            // ROOTDIR uzerinden fallback
            $root = defined('ROOTDIR') ? ROOTDIR : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4));
            $routes_file = $root . '/coremio/locale/' . $lang . '/website-routes.php';
            if(!file_exists($routes_file)) {
                $routes_file = $root . '/coremio/locale/' . $lang . '/website-routes-default.php';
            }
        }

        if(file_exists($routes_file)) {
            $loaded = @include $routes_file;
            if(is_array($loaded) && isset($loaded['website-routes']) && is_array($loaded['website-routes'])) {
                $cache = $loaded['website-routes'];
                return $cache;
            }
        }

        // Son fallback: TR routes hardcoded
        $cache = [
            'order-steps-p' => ['siparis/(?)/(?)/(?)', 'order_steps/(1)/(2)/(3)'],
            'order-steps'   => ['siparis/(?)/(?)',     'order_steps/(1)/(2)/1'],
            'basket'        => ['sepet',               'basket'],
            'home'          => ['anasayfa',            'index'],
        ];
        return $cache;
    }
}

if(!function_exists('cdg_buy_link')) {
    /**
     * Urun satin alma linki (sepete yonlendirme)
     *
     * @param string $type  'hosting', 'server', 'sms', 'software', 'special'
     * @param int    $id    Urun ID
     * @param int    $step  Hangi step'ten baslasin (varsayilan 1)
     * @return string       Tam URL
     */
    function cdg_buy_link($type, $id, $step = 1) {
        // Once 3-parametreli order-steps-p
        $url = cdg_route('order-steps-p', [$type, (int)$id, (int)$step]);
        if($url) return $url;

        // 2-parametreli fallback
        $url = cdg_route('order-steps', [$type, (int)$id]);
        if($url) return $url;

        // Manuel TR fallback
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        return $base . '/siparis/' . $type . '/' . (int)$id;
    }
}

if(!function_exists('cdg_basket_link')) {
    /**
     * Sepet sayfasi linki
     */
    function cdg_basket_link() {
        $url = cdg_route('basket', []);
        if($url) return $url;
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        return $base . '/sepet';
    }
}
