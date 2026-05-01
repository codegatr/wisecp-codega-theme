<?php
/**
 * CODEGA Theme - Turkish language strings
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

return [
    // General
    'theme_name'        => 'CODEGA',
    'site_tagline'      => 'Premium Hosting ve Web Çözümleri',

    // Navigation
    'nav_home'          => 'Anasayfa',
    'nav_about'         => 'Hakkımızda',
    'nav_services'      => 'Hizmetler',
    'nav_hosting'       => 'Hosting',
    'nav_domain'        => 'Domain',
    'nav_support'       => 'Destek',
    'nav_login'         => 'Giriş Yap',
    'nav_register'      => 'Kayıt Ol',
    'nav_panel'         => 'Müşteri Paneli',
    'nav_logout'        => 'Çıkış',

    // Hero
    'hero_eyebrow'      => 'Konya · CODEGA · 2026',
    'hero_title'        => 'İşinizin omurgasını kuran premium altyapı.',
    'hero_lead'         => 'Kurumsal projeleriniz için saniyeler içinde devreye giren hosting, alan adı ve özel yazılım hizmetleri.',
    'hero_btn_main'     => 'Hosting Paketleri',
    'hero_btn_alt'      => 'Domain Sorgula',

    // Stats
    'stat_uptime'       => 'Uptime Garantisi',
    'stat_support'      => 'Türkçe Destek',
    'stat_response'     => 'Sunucu Tepki Süresi',
    'stat_experience'   => 'Yıl Tecrübe',

    // Auth
    'login_title'       => 'Tekrar hoş geldiniz',
    'login_lead'        => 'Müşteri panelinize giriş yapın.',
    'login_email'       => 'E-posta Adresi',
    'login_password'    => 'Şifre',
    'login_remember'    => 'Beni hatırla',
    'login_submit'      => 'Giriş Yap',
    'login_forgot'      => 'Şifremi unuttum',
    'login_no_account'  => 'Hesabınız yok mu?',
    'login_register_link' => 'Hemen kaydolun',
    'login_codega_sso'  => 'codega.com.tr ile Giriş Yap',
    'login_or_email'    => 'veya e-posta ile',

    'register_title'    => 'Hesap oluştur',
    'register_lead'     => 'Birkaç saniye içinde tamam.',
    'register_name'     => 'Ad',
    'register_surname'  => 'Soyad',
    'register_phone'    => 'Telefon',
    'register_password' => 'Şifre',
    'register_password_confirm' => 'Şifre Tekrar',
    'register_submit'   => 'Hesap Oluştur',
    'register_terms'    => 'Kullanım Şartları ve Gizlilik Politikası\'nı okudum, kabul ediyorum.',
    'register_have_account' => 'Zaten hesabınız var mı?',
    'register_login_link' => 'Giriş yapın',

    'lostpass_title'    => 'Şifre sıfırla',
    'lostpass_lead'     => 'E-posta adresinize sıfırlama bağlantısı göndereceğiz.',
    'lostpass_submit'   => 'Sıfırlama Bağlantısı Gönder',

    // Client area
    'client_welcome'    => 'hoş geldiniz',
    'client_summary'    => 'Hesabınızla ilgili özet bilgiler aşağıdadır.',
    'client_active_services' => 'Aktif Hizmetler',
    'client_unpaid_invoices' => 'Ödenmemiş Faturalar',
    'client_open_tickets'    => 'Açık Destek Talepleri',
    'client_domains'    => 'Alan Adlarım',
    'client_view_all'   => 'Tümünü gör',
    'client_add_service' => '+ Hizmet Ekle',
    'client_new_ticket' => 'Destek Talebi',

    // Tables
    'table_product'     => 'Ürün / Plan',
    'table_domain'      => 'Domain',
    'table_status'      => 'Durum',
    'table_renewal'     => 'Yenileme',
    'table_price'       => 'Ücret',
    'table_invoice_no'  => 'Fatura No',
    'table_date'        => 'Tarih',
    'table_due'         => 'Vade',
    'table_amount'      => 'Tutar',
    'table_manage'      => 'Yönet',
    'table_pay'         => 'Öde',
    'table_view'        => 'Görüntüle',

    // Status pills
    'status_active'     => 'Aktif',
    'status_pending'    => 'Beklemede',
    'status_cancelled'  => 'İptal',
    'status_suspended'  => 'Askıya alındı',
    'status_paid'       => 'Ödendi',
    'status_unpaid'     => 'Ödenmemiş',
    'status_refunded'   => 'İade',

    // Footer
    'footer_services'   => 'Hizmetler',
    'footer_support'    => 'Destek',
    'footer_corporate'  => 'Kurumsal',
    'footer_about'      => 'Hakkımızda',
    'footer_contact'    => 'İletişim',
    'footer_terms'      => 'Kullanım Şartları',
    'footer_privacy'    => 'Gizlilik Politikası',
    'footer_sla'        => 'SLA Sözleşmesi',
    'footer_copyright'  => 'Tüm hakları saklıdır.',

    // Errors
    'err_404_title'     => 'Bulamadık.',
    'err_404_message'   => 'Aradığınız sayfa kaldırılmış, taşınmış veya hiç var olmamış olabilir.',
    'err_404_btn'       => 'Anasayfaya Dön',

    // SSO errors
    'sso_invalid_title' => 'Oturum Doğrulanamadı',
    'sso_invalid_msg'   => 'codega.com.tr üzerinden gönderilen oturum bağlantısı geçersiz, süresi dolmuş veya zaten kullanılmış.',
];
