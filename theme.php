<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega Theme for WiseCP
 *
 * @author      CODEGA <info@codega.com.tr>
 * @link        https://codega.com.tr
 * @version     2.0.0
 * @license     MIT
 */

if(!defined('SITE_URL')) define('SITE_URL', defined('APP_URI') ? APP_URI . '/' : '/');

Class Codega_Theme
{
    public $config = [], $name = 'Codega', $error = NULL, $language, $languages;

    function __construct()
    {
        if(!$this->languages) $this->languages = View::$init->theme_language_loader($this->name);
        if(!$this->language)  $this->language  = View::$init->theme_lang(Bootstrap::$lang->clang, $this->languages);
        $this->config = include __DIR__ . DS . "theme-config.php";
    }

    public function router($params = [])
    {
        $raw  = implode("/", $params);
        $page = Filter::folder(isset($params[0]) ? $params[0] : '');

        if($raw == "templates/website/" . $this->name . "/css/wisecp.css") {
            $this->main_css();
            return true;
        }
        elseif($page && file_exists(__DIR__ . DS . "pages" . DS . $page . ".php")) {
            return ['include_file' => __DIR__ . DS . "pages" . DS . $page . ".php"];
        }
        return false;
    }

    public function main_css()
    {
        $color1 = ltrim(Config::get("theme/color1"), "#");
        $color2 = ltrim(Config::get("theme/color2"), "#");
        $tcolor = ltrim(Config::get("theme/text-color"), "#");

        $config_theme = CONFIG_DIR . "theme.php";
        $css_file     = __DIR__ . DS . "css" . DS . "wisecp.php";

        $lastModified = max(
            file_exists($config_theme) ? filemtime($config_theme) : 0,
            file_exists($css_file)     ? filemtime($css_file)     : 0
        );

        header("Content-Type: text/css; charset=UTF-8");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
        header('Cache-Control: public, max-age=3600');
        include $css_file;
    }

    public function get_css_url()
    {
        return THEMES_URL . THEMENAME . "/css/wisecp.css";
    }

    public function change_settings()
    {
        $settings = isset($this->config["settings"]) ? $this->config["settings"] : [];

        $header_type     = (int) Filter::init("POST/header_type", "numbers");
        $clientArea_type = (int) Filter::init("POST/clientArea_type", "numbers");
        $color1          = ltrim(Filter::init("POST/color1"), "#");
        $color2          = ltrim(Filter::init("POST/color2"), "#");
        $tcolor          = ltrim(Filter::init("POST/text_color"), "#");

        if($header_type)     $settings["header-type"]     = $header_type;
        if($clientArea_type) $settings["clientArea-type"] = $clientArea_type;
        if($color1) {
            $settings["color1"]     = $color1;
            $settings["meta-color"] = "#" . $color1;
        }
        if($color2) $settings["color2"]     = $color2;
        if($tcolor) $settings["text_color"] = $tcolor;

        // Banner
        if(!isset($settings['banner']) || !is_array($settings['banner'])) $settings['banner'] = [];
        $settings['banner']['heading']      = Filter::init("POST/banner_heading");
        $settings['banner']['content']      = Filter::init("POST/banner_content");
        $settings['banner']['button_text1'] = Filter::init("POST/banner_button_text1");
        $settings['banner']['button_link1'] = Filter::init("POST/banner_button_link1");
        $settings['banner']['button_text2'] = Filter::init("POST/banner_button_text2");
        $settings['banner']['button_link2'] = Filter::init("POST/banner_button_link2");

        $settings['show_services']  = (int) Filter::init("POST/show_services",  "numbers") ? 1 : 0;
        $settings['show_features']  = (int) Filter::init("POST/show_features",  "numbers") ? 1 : 0;
        $settings['show_pricing']   = (int) Filter::init("POST/show_pricing",   "numbers") ? 1 : 0;
        $settings['show_cta']       = (int) Filter::init("POST/show_cta",       "numbers") ? 1 : 0;

        return $settings;
    }
}
