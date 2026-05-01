<?php
/**
 * CODEGA Theme for WiseCP 3.x
 * 
 * Navy/Gold premium theme with codega.com.tr SSO + API integration.
 * 
 * @package    Codega_Theme
 * @author     CODEGA <info@codega.com.tr>
 * @copyright  Copyright (c) 2026 CODEGA - codega.com.tr
 * @license    Proprietary - Internal use only
 * @version    1.0.0
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

class Codega_Theme {

    public $config   = [];
    public $name     = 'codega';
    public $error    = NULL;
    public $language;
    public $languages;

    public function __construct()
    {
        if (!$this->languages) $this->languages = View::$init->theme_language_loader($this->name);
        if (!$this->language)  $this->language  = View::$init->theme_lang(Bootstrap::$lang->clang, $this->languages);

        $config_file = __DIR__ . DS . "theme-config.php";
        if (file_exists($config_file)) {
            $this->config = include $config_file;
        }
    }

    /**
     * Router - WiseCP calls this to resolve theme pages
     */
    public function router($params = [])
    {
        $raw  = implode("/", $params);
        $page = (class_exists('Filter') && method_exists('Filter', 'folder'))
              ? Filter::folder(isset($params[0]) ? $params[0] : '')
              : preg_replace('/[^a-z0-9_-]/i', '', $params[0] ?? '');

        // Dynamic CSS endpoint
        if ($raw == "templates/website/" . $this->name . "/css/wisecp.css") {
            $this->main_css();
            return true;
        }

        // SSO + API endpoints (delegated to api/ folder)
        if ($page === 'codega-sso' || $page === 'codega-api') {
            $api_file = __DIR__ . DS . "api" . DS . $page . ".php";
            if (file_exists($api_file)) {
                return ['include_file' => $api_file];
            }
        }

        // Standard page resolution
        if ($page && file_exists(__DIR__ . DS . "pages" . DS . $page . ".php")) {
            return ['include_file' => __DIR__ . DS . "pages" . DS . $page . ".php"];
        }

        // Homepage fallback — when no page is specified, render index.php
        if (empty($page) && file_exists(__DIR__ . DS . "pages" . DS . "index.php")) {
            return ['include_file' => __DIR__ . DS . "pages" . DS . "index.php"];
        }
    }

    /**
     * Dynamic CSS - merges theme settings (colors) with stylesheet
     */
    public function main_css()
    {
        $color1      = ltrim(Config::get("theme/color1"), "#");
        $color2      = ltrim(Config::get("theme/color2"), "#");
        $text_color  = ltrim(Config::get("theme/text-color"), "#");

        $config_theme = CONFIG_DIR . "theme.php";
        $config_file  = __DIR__ . DS . "theme-config.php";
        $css_file     = __DIR__ . DS . "css" . DS . "wisecp.php";

        $lastModified1 = file_exists($config_theme) ? filemtime($config_theme) : 0;
        $lastModified2 = file_exists($config_file)  ? filemtime($config_file)  : 0;
        $lastModified3 = file_exists($css_file)     ? filemtime($css_file)     : 0;

        $lastModified = max($lastModified1, $lastModified2, $lastModified3);

        header("Content-Type: text/css; charset=UTF-8");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
        header('Cache-Control: public, max-age=3600');

        include $css_file;
    }

    /**
     * Admin theme settings save handler
     */
    public function change_settings()
    {
        $settings        = isset($this->config["settings"]) ? $this->config["settings"] : [];
        $header_type     = (int) Filter::init("POST/header_type", "numbers");
        $clientArea_type = (int) Filter::init("POST/clientArea_type", "numbers");
        $color1          = ltrim(Filter::init("POST/color1"), "#");
        $color2          = ltrim(Filter::init("POST/color2"), "#");
        $tcolor          = ltrim(Filter::init("POST/text_color"), "#");
        $sso_enabled     = (int) Filter::init("POST/sso_enabled", "numbers");
        $codega_secret   = Filter::init("POST/codega_shared_secret");

        if ($header_type     != ($settings["header-type"] ?? 0))      $settings["header-type"]      = $header_type;
        if ($clientArea_type != ($settings["clientArea-type"] ?? 0))  $settings["clientArea-type"]  = $clientArea_type;

        if ($color1 != ($settings["color1"] ?? '')) {
            $settings["color1"]     = $color1;
            $settings["meta-color"] = "#" . $color1;
        }
        if ($color2 != ($settings["color2"] ?? ''))      $settings["color2"]     = $color2;
        if ($tcolor != ($settings["text-color"] ?? ''))  $settings["text-color"] = $tcolor;

        // CODEGA-specific settings
        $settings["sso_enabled"]          = $sso_enabled ? 1 : 0;
        if ($codega_secret) {
            $settings["codega_shared_secret"] = $codega_secret;
        }

        return $settings;
    }
}
