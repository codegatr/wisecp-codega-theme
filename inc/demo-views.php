<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Demo Mode Görünümü
 *
 * Classic teması "Style Selector" (Agency/Corporate header + WClient/Basic clientArea) sunuyordu.
 * Codega tek bir tutarlı tasarım sunduğu için stil seçici yok — demo modunda
 * sadece tema bilgisi ve GitHub bağlantısı gösteriyoruz.
 *
 * WiseCP runtime: DEMO_MODE constant
 */

if(!defined('DEMO_MODE') || !DEMO_MODE) return;
?>

<div class="cdg-demo-badge" id="cdg-demo-badge">
    <button type="button" class="cdg-demo-toggle" onclick="cdgDemoToggle(this)" aria-label="Demo bilgisini gizle/göster">
        <i class="bi bi-chevron-up cdg-demo-chev"></i>
    </button>
    <div class="cdg-demo-body">
        <div class="cdg-demo-head">
            <i class="bi bi-palette-fill"></i>
            <strong>Codega Tema</strong>
        </div>
        <div class="cdg-demo-text">
            Bu sayfa <strong>Codega</strong> temasının demo görünümüdür.
            Tema; modern, hızlı ve tek tasarım dilinde geliştirilmiştir.
        </div>
        <a href="https://github.com/codegatr/wisecp-codega-theme" target="_blank" rel="noopener" class="cdg-demo-link">
            <i class="bi bi-github"></i> GitHub'da incele
        </a>
    </div>
</div>

<style>
.cdg-demo-badge {
    position: fixed;
    right: 18px;
    bottom: 18px;
    width: 280px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 12px 32px rgba(15,23,42,0.18);
    z-index: 99;
    overflow: hidden;
    transition: transform 0.35s ease, opacity 0.35s ease;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-demo-badge *, .cdg-demo-badge *::before, .cdg-demo-badge *::after { box-sizing: border-box; }
.cdg-demo-badge.cdg-demo-collapsed {
    transform: translateY(calc(100% - 32px));
}

.cdg-demo-toggle {
    width: 100%;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    border: 0;
    padding: 6px;
    cursor: pointer;
    display: flex; justify-content: center; align-items: center;
    font-size: 14px;
    transition: background 0.18s;
}
.cdg-demo-toggle:hover { background: linear-gradient(135deg, #1e3a8a, #2563eb); }
.cdg-demo-badge.cdg-demo-collapsed .cdg-demo-chev { transform: rotate(180deg); }

.cdg-demo-body {
    padding: 14px 16px 16px;
}
.cdg-demo-head {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px;
    color: #1e40af;
    margin-bottom: 8px;
}
.cdg-demo-head i { font-size: 16px; }
.cdg-demo-head strong { font-size: 14px; font-weight: 800; color: #0f172a; }

.cdg-demo-text {
    font-size: 12px;
    color: #475569;
    line-height: 1.5;
    margin-bottom: 12px;
}
.cdg-demo-text strong { color: #1e40af; }

.cdg-demo-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    background: #0f172a;
    color: #fff;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.18s;
}
.cdg-demo-link:hover { background: #1e293b; color: #fff; transform: translateY(-1px); }

@media (max-width: 480px) {
    .cdg-demo-badge { width: calc(100vw - 24px); right: 12px; }
}
</style>

<script>
function cdgDemoToggle(btn) {
    var badge = document.getElementById('cdg-demo-badge');
    if(!badge) return;
    badge.classList.toggle('cdg-demo-collapsed');
    try {
        var collapsed = badge.classList.contains('cdg-demo-collapsed');
        document.cookie = 'cdg_demo_collapsed=' + (collapsed ? '1' : '0') + ';path=/;max-age=2592000;samesite=lax';
    } catch(e) {}
}
(function(){
    try {
        if(document.cookie.indexOf('cdg_demo_collapsed=1') !== -1) {
            document.getElementById('cdg-demo-badge').classList.add('cdg-demo-collapsed');
        }
    } catch(e) {}
})();
</script>
