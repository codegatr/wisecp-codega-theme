<?php
/**
 * CODEGA Theme - Configuration
 * 
 * @package Codega_Theme
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

return [

    // Theme metadata
    "name"        => "CODEGA",
    "version"     => "1.0.0",
    "author"      => "CODEGA",
    "author_url"  => "https://codega.com.tr",
    "description" => "Premium Navy/Gold theme for CODEGA hosting & services. Integrated with codega.com.tr.",

    // Default settings - users can override from admin panel
    "settings"    => [
        "header-type"          => 1,           // 1: Solid dark, 2: Transparent on hero
        "clientArea-type"      => 1,           // 1: Sidebar, 2: Top nav
        "color1"               => "0a1628",    // Primary - Deep Navy
        "color2"               => "d4a574",    // Accent - Antique Gold
        "text-color"           => "1a2238",    // Body text
        "meta-color"           => "#0a1628",   // Browser theme color
        "sso_enabled"          => 1,           // Codega.com.tr SSO bridge
        "codega_shared_secret" => "",          // HMAC secret - SET IN ADMIN PANEL
        "codega_main_url"      => "https://codega.com.tr",
        "show_main_site_nav"   => 1,           // Show codega.com.tr nav links in header
    ],

    // Theme settings panel - exposed in admin
    "settings_form" => [
        "color1" => [
            "type"        => "color",
            "name"        => "Primary Color",
            "name_tr"     => "Ana Renk (Lacivert)",
            "default"     => "#0a1628",
        ],
        "color2" => [
            "type"        => "color",
            "name"        => "Accent Color",
            "name_tr"     => "Vurgu Rengi (Altın)",
            "default"     => "#d4a574",
        ],
        "text-color" => [
            "type"        => "color",
            "name"        => "Text Color",
            "name_tr"     => "Yazı Rengi",
            "default"     => "#1a2238",
        ],
        "header-type" => [
            "type"        => "select",
            "name"        => "Header Style",
            "name_tr"     => "Başlık Stili",
            "options"     => [
                1 => "Solid Dark / Koyu Sabit",
                2 => "Transparent on Hero / Hero Üzerinde Şeffaf",
            ],
        ],
        "clientArea-type" => [
            "type"        => "select",
            "name"        => "Client Area Layout",
            "name_tr"     => "Müşteri Paneli Düzeni",
            "options"     => [
                1 => "Sidebar / Yan Menü",
                2 => "Top Nav / Üst Menü",
            ],
        ],
        "sso_enabled" => [
            "type"        => "checkbox",
            "name"        => "Enable codega.com.tr SSO",
            "name_tr"     => "codega.com.tr SSO Aktif",
            "default"     => 1,
        ],
        "codega_main_url" => [
            "type"        => "text",
            "name"        => "codega.com.tr URL",
            "name_tr"     => "codega.com.tr Adresi",
            "default"     => "https://codega.com.tr",
        ],
        "codega_shared_secret" => [
            "type"        => "password",
            "name"        => "HMAC Shared Secret (32+ chars)",
            "name_tr"     => "HMAC Paylaşılan Anahtar (min 32 karakter)",
            "default"     => "",
            "description" => "This must match the secret on codega.com.tr side. Generate with: openssl rand -hex 32",
        ],
        "show_main_site_nav" => [
            "type"        => "checkbox",
            "name"        => "Show main site nav links in header",
            "name_tr"     => "Ana site menüsünü başlıkta göster",
            "default"     => 1,
        ],
    ],
];
