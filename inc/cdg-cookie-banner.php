<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - KVKK Cookie Consent Banner
 *
 * Sayfanın altında çıkan cookie izin banner'ı.
 * Kullanıcı kabul edince localStorage'da saklanır, bir daha gösterilmez.
 * KVKK uyumu için yasal zorunluluk.
 */
?>

<div id="cdg-cookie-banner" class="cdg-cookie-banner" style="display:none;">
    <div class="cdg-cookie-content">
        <div class="cdg-cookie-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="cdg-cookie-text">
            <strong>Çerez Kullanımı</strong>
            <p>Web sitemizde size daha iyi hizmet sunabilmek için çerezler kullanıyoruz. Sitemizi kullanmaya devam ederek <a href="/cerez-politikasi.html">Çerez Politikamızı</a> ve <a href="/kvkk-aydinlatma-metni.html">KVKK Aydınlatma Metni</a>'ni kabul etmiş sayılırsınız.</p>
        </div>
        <div class="cdg-cookie-actions">
            <button type="button" class="cdg-cookie-btn cdg-cookie-btn-accept" onclick="cdgCookieAccept()">Kabul Et</button>
            <a href="/cerez-politikasi.html" class="cdg-cookie-btn cdg-cookie-btn-info">Detay</a>
        </div>
    </div>
</div>

<style>
.cdg-cookie-banner {
    position: fixed;
    bottom: 16px;
    left: 16px;
    right: 16px;
    max-width: 980px;
    margin: 0 auto;
    z-index: 9000;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
    padding: 16px 20px;
    animation: cdgCookieSlide 0.5s ease-out;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
}
@keyframes cdgCookieSlide {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.cdg-cookie-content {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.cdg-cookie-icon {
    flex-shrink: 0;
    width: 44px; height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: grid;
    place-items: center;
    font-size: 22px;
}
.cdg-cookie-text { flex: 1; min-width: 240px; }
.cdg-cookie-text strong { display: block; color: #0f172a; font-size: 14px; font-weight: 800; margin-bottom: 4px; }
.cdg-cookie-text p { margin: 0; color: #64748b; font-size: 13px; line-height: 1.55; }
.cdg-cookie-text a { color: #1e40af; text-decoration: underline; font-weight: 600; }
.cdg-cookie-text a:hover { color: #3b82f6; }
.cdg-cookie-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
.cdg-cookie-btn {
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    border: 0;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
}
.cdg-cookie-btn-accept {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
}
.cdg-cookie-btn-accept:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(30, 64, 175, 0.35); }
.cdg-cookie-btn-info {
    background: #f1f5f9;
    color: #475569;
}
.cdg-cookie-btn-info:hover { background: #e2e8f0; color: #1e293b; }

@media (max-width: 640px) {
    .cdg-cookie-banner { left: 8px; right: 8px; bottom: 8px; padding: 14px; }
    .cdg-cookie-content { gap: 12px; }
    .cdg-cookie-icon { width: 36px; height: 36px; font-size: 18px; }
    .cdg-cookie-actions { width: 100%; justify-content: flex-end; }
}
</style>

<script>
(function(){
    var banner = document.getElementById('cdg-cookie-banner');
    if(!banner) return;

    // localStorage uyumlu, fallback cookie
    var accepted = false;
    try {
        accepted = localStorage.getItem('cdg_cookies_accepted') === '1';
    } catch(e) {
        // localStorage yoksa cookie kontrol
        accepted = document.cookie.indexOf('cdg_cookies_accepted=1') !== -1;
    }

    if(!accepted) {
        // 1.5 saniye sonra göster (sayfa yüklensin diye)
        setTimeout(function(){ banner.style.display = 'block'; }, 1500);
    }

    window.cdgCookieAccept = function(){
        try { localStorage.setItem('cdg_cookies_accepted', '1'); }
        catch(e) {
            var d = new Date();
            d.setTime(d.getTime() + 365*24*60*60*1000);
            document.cookie = 'cdg_cookies_accepted=1;expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
        }
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(20px)';
        setTimeout(function(){ banner.style.display = 'none'; }, 300);
    };
})();
</script>
