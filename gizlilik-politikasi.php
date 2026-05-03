<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
?>

<section class="cdg-section" style="padding-top:48px;padding-bottom:64px;">
    <div class="cdg-container" style="max-width:1100px;">
<div style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);border-radius:16px;padding:32px;margin-bottom:32px;border-left:4px solid #4338ca;">
    <span style="display:inline-block;background:#4338ca;color:#fff;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:800;letter-spacing:0.5px;margin-bottom:12px;">GİZLİLİK POLİTİKASI</span>
    <h1 style="font-size:30px;font-weight:800;color:#312e81;margin:0 0 12px;">🔒 Gizlilik Politikamız</h1>
    <p style="font-size:14px;color:#3730a3;margin:0;line-height:1.7;">Veri işleme ve gizlilik yaklaşımımızı açıklar.<br><strong>Son güncelleme: 03.05.2026</strong></p>
</div>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">1. Hangi Verileri Topluyoruz?</h2>
<p style="font-size:15px;line-height:1.8;color:#475569;">Sizinle ilgili topladığımız veriler 4 kaynak grubuna ayrılır:</p>
<ul style="font-size:15px;line-height:1.9;color:#475569;padding-left:20px;">
    <li><strong>Doğrudan Verdikleriniz:</strong> Hesap oluştururken, sipariş verirken (ad, e-posta, adres, kimlik no)</li>
    <li><strong>Otomatik Toplananlar:</strong> IP adresiniz, tarayıcı bilgileri, ziyaret tarih/saat</li>
    <li><strong>Üçüncü Taraflardan:</strong> Ödeme sağlayıcı (kart numarasını biz görmeyiz, yalnızca son 4 hane), Google ile giriş yaparsanız e-posta</li>
    <li><strong>Hizmet Kullanımı:</strong> Sunucu kullanım istatistikleri, destek ticket geçmişi</li>
</ul>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">2. Verilerinizi Ne İçin Kullanıyoruz?</h2>
<p style="font-size:15px;line-height:1.8;color:#475569;">Verileriniz yalnızca aşağıdaki amaçlarla kullanılır:</p>
<ul style="font-size:15px;line-height:1.9;color:#475569;padding-left:20px;">
    <li>Sipariş verdiğiniz hizmetin sağlanması (hosting kurulumu, domain tescili)</li>
    <li>Faturalama ve yasal mali yükümlülükler</li>
    <li>Teknik destek hizmetinin verilmesi</li>
    <li>Hesap güvenliğinizin sağlanması (oturum kontrolü, fraud önleme)</li>
    <li>Yasal yetkili kurumlardan gelen taleplere yanıt</li>
</ul>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">3. Verilerinizi Kimlerle Paylaşıyoruz?</h2>
<p style="font-size:15px;line-height:1.8;color:#475569;background:#fffbeb;border-left:3px solid #f59e0b;padding:14px 18px;border-radius:8px;">⚠ <strong>CODEGA, kişisel verilerinizi pazarlama amacıyla satmaz veya üçüncü taraflarla paylaşmaz.</strong> Veri paylaşımı yalnızca hizmet zorunluluğu için yapılır.</p>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">4. Verileriniz Ne Kadar Saklanır?</h2>
<table style="width:100%;border-collapse:collapse;font-size:14px;margin-bottom:14px;">
    <thead>
        <tr style="background:#f1f5f9;"><th style="padding:12px;text-align:left;border:1px solid #e2e8f0;">Veri Türü</th><th style="padding:12px;text-align:left;border:1px solid #e2e8f0;">Saklama Süresi</th></tr>
    </thead>
    <tbody>
        <tr><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">Hesap bilgileri (aktif)</td><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">Hesap aktif olduğu sürece</td></tr>
        <tr><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">Faturalama kayıtları</td><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">Vergi yasaları gereği <strong>10 yıl</strong></td></tr>
        <tr><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">Oturum logları</td><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">90 gün</td></tr>
        <tr><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">Destek ticket geçmişi</td><td style="padding:12px;border:1px solid #e2e8f0;color:#475569;">3 yıl</td></tr>
    </tbody>
</table>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">5. Güvenlik Tedbirleri</h2>
<ul style="font-size:15px;line-height:1.9;color:#475569;padding-left:20px;">
    <li>Şifreniz veritabanında <strong>bcrypt</strong> ile hash'lenir, hiçbir Codega çalışanı şifrenizi göremez</li>
    <li>Tüm bağlantılar TLS 1.3 ile şifrelenir (HTTPS zorunlu)</li>
    <li>Veri merkezlerimiz ISO 27001 sertifikalıdır</li>
    <li>Düzenli sızma testleri ve güvenlik denetimleri yapılır</li>
    <li>2FA (iki faktörlü kimlik doğrulama) tüm hesaplar için ücretsiz</li>
</ul>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">6. Çocukların Gizliliği</h2>
<p style="font-size:15px;line-height:1.8;color:#475569;">CODEGA hizmetleri 18 yaş ve üstü için tasarlanmıştır. 18 yaş altı kişilere bilerek hizmet sunmuyoruz.</p>

<h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:24px 0 12px;">7. İletişim</h2>
<p style="font-size:15px;line-height:1.8;color:#475569;">Gizlilik konularında sorularınız için:<br>
<strong>E-posta:</strong> <a href="mailto:gizlilik@codega.com.tr" style="color:#00D3E5;">gizlilik@codega.com.tr</a><br>
<strong>KVKK Sorumlusu:</strong> <a href="mailto:kvkk@codega.com.tr" style="color:#00D3E5;">kvkk@codega.com.tr</a></p>
    </div>
</section>

<?php
/* === DEFANSIVE FALLBACK ===
 * Eğer WiseCP master-content uygulamadıysa, sayfa header/footer ile sarılmamış olur.
 * $_cdg_in_master_content flag master-content.php tarafından set edilir.
 */
if(empty($_cdg_in_master_content) && !headers_sent()) {
    if(file_exists(__DIR__ . "/inc/main-footer.php")) {
        include __DIR__ . "/inc/main-footer.php";
    }
    if(class_exists("View") && method_exists("View", "footer_codes")) View::footer_codes();
}
?>
