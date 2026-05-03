<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega Müşteri Paneli Footer (clientArea_type=1)
 *
 * Panel UX'ine uygun KOMPAKT footer.
 * Ana sayfa footer'ı (main-footer.php) panel için fazla büyük (4 sütun + 200px).
 * Burada sadece copyright + version + AKSOY GROUP rozeti gösterilir.
 */

// Theme version bilgisini oku
$cdg_theme_version = '';
$cdg_theme_date = '';
$cdg_theme_config_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'theme-config.php';
if(file_exists($cdg_theme_config_file)) {
    $cdg_theme_config = @include $cdg_theme_config_file;
    if(is_array($cdg_theme_config)) {
        $cdg_theme_version = $cdg_theme_config['version'] ?? '';
    }
}
$cdg_version_json = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'version.json';
if(file_exists($cdg_version_json)) {
    $vj = @json_decode(file_get_contents($cdg_version_json), true);
    if(is_array($vj)) {
        $cdg_theme_date = $vj['release_date'] ?? '';
    }
}
$cdg_year = date('Y');
?>
<style>
.cdg-ac-footer {
    flex-shrink: 0;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: #64748b;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
.cdg-ac-footer a { color: #475569; text-decoration: none; transition: color 0.15s ease; }
.cdg-ac-footer a:hover { color: #2E3B4E; }

.cdg-ac-footer-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cdg-ac-footer-left strong {
    color: #2E3B4E;
    font-weight: 700;
}
.cdg-ac-footer-version {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.3px;
}
.cdg-ac-footer-meta {
    color: #94a3b8;
    font-size: 11px;
}

.cdg-ac-footer-center {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}
.cdg-ac-footer-center a {
    font-size: 12px;
    font-weight: 600;
}

.cdg-ac-footer-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cdg-ac-footer-aksoy {
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.15s ease;
    text-decoration: none;
}
.cdg-ac-footer-aksoy:hover {
    background: #e0e7ff;
    border-color: #c7d2fe;
    transform: translateY(-1px);
}
.cdg-ac-footer-aksoy strong {
    font-size: 11px;
    font-weight: 800;
    color: #2E3B4E;
    letter-spacing: 0.4px;
}
.cdg-ac-footer-aksoy span {
    font-size: 10px;
    color: #94a3b8;
    font-weight: 500;
}

@media (max-width: 768px) {
    .cdg-ac-footer {
        flex-direction: column;
        text-align: center;
        padding: 12px 16px;
    }
    .cdg-ac-footer-left,
    .cdg-ac-footer-center,
    .cdg-ac-footer-right {
        justify-content: center;
        width: 100%;
    }
}
</style>

<footer class="cdg-ac-footer">
    <div class="cdg-ac-footer-left">
        <span>&copy; <?php echo $cdg_year; ?> <strong>CODEGA</strong></span>
        <?php if($cdg_theme_version): ?>
        <span class="cdg-ac-footer-version"><i class="bi bi-tag-fill" style="font-size:9px;"></i> v<?php echo htmlspecialchars($cdg_theme_version, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        <?php endif; ?>
        <?php if($cdg_theme_date): ?>
        <span class="cdg-ac-footer-meta">&middot; <?php echo htmlspecialchars(date('d.m.Y', strtotime($cdg_theme_date)), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        <?php endif; ?>
    </div>

    <div class="cdg-ac-footer-center">
        <a href="/kvkk-aydinlatma-metni.html" target="_blank">KVKK</a>
        <a href="/gizlilik-politikasi.html" target="_blank">Gizlilik</a>
        <a href="/cerez-politikasi.html" target="_blank">Çerez</a>
        <a href="/hizmet-sozlesmesi.html" target="_blank">Hizmet Sözleşmesi</a>
        <a href="/sistem-durumu.html" target="_blank"><i class="bi bi-activity"></i> Sistem Durumu</a>
    </div>

    <div class="cdg-ac-footer-right">
        <a href="https://aksoy.web.tr" target="_blank" rel="noopener" class="cdg-ac-footer-aksoy">
            <strong>AKSOY GROUP</strong><span>iştirakidir</span>
        </a>
        <?php if(class_exists('View') && method_exists('View', 'show_brand')) View::show_brand(); ?>
    </div>
</footer>

<!-- Tema JS - tab switcher, scroll handler, vb. (panel sayfalarda da gerekli) -->
<script src="<?php echo isset($tadress) ? $tadress : ''; ?>js/script.js?v=<?php echo file_exists(__DIR__ . '/../js/script.js') ? filemtime(__DIR__ . '/../js/script.js') : 1; ?>" defer></script>

<!-- Codega Panel Event Delegation (SES-uyumlu, inline garantisi) -->
<script>
(function(){
    "use strict";

    // === CDG-PD2 Tab Switcher (Hosting/Server/SMS/Software/Special) ===
    function pd2Switch(pane){
        if(!pane) return;
        try {
            var tabs = document.querySelectorAll(".cdg-pd2-tab");
            var panes = document.querySelectorAll(".cdg-pd2-pane");
            tabs.forEach(function(t){ t.classList.remove("active"); });
            panes.forEach(function(p){ p.classList.remove("active"); });
            var activeTab = document.querySelector(".cdg-pd2-tab[data-pane='" + pane + "']");
            var activePane = document.getElementById("cdg-pd2-pane-" + pane);
            if(activeTab) activeTab.classList.add("active");
            if(activePane) activePane.classList.add("active");
            try { history.replaceState(null, "", "#" + pane); } catch(e){}
        } catch(e){ console.error("[cdg-pd2] tab error:", e); }
    }

    // === CDG-PDM Tab Switcher (Domain) ===
    function pdmSwitch(pane){
        if(!pane) return;
        try {
            var tabs = document.querySelectorAll(".cdg-pdm-tab");
            var panes = document.querySelectorAll(".cdg-pdm-pane");
            tabs.forEach(function(t){ t.classList.remove("active"); });
            panes.forEach(function(p){ p.classList.remove("active"); });
            var activeTab = document.querySelector(".cdg-pdm-tab[data-pane='" + pane + "']");
            var activePane = document.getElementById("cdg-pdm-pane-" + pane);
            if(activeTab) activeTab.classList.add("active");
            if(activePane) activePane.classList.add("active");
            try { history.replaceState(null, "", "#" + pane); } catch(e){}
        } catch(e){ console.error("[cdg-pdm] tab error:", e); }
    }

    // === Sub-tab switcher (Transfer pane icinde) ===
    function subTabSwitch(target){
        if(!target) return;
        document.querySelectorAll(".cdg-pd2-subtab").forEach(function(t){
            t.classList.remove("active");
        });
        document.querySelectorAll(".cdg-pd2-subpane").forEach(function(p){
            p.style.display = "none";
        });
        var activeTab = document.querySelector(".cdg-pd2-subtab[data-cdg-target='" + target + "']");
        if(activeTab) activeTab.classList.add("active");
        var activePane = document.getElementById("cdg-pd2-subpane-" + target);
        if(activePane) activePane.style.display = "block";
    }

    // === Generic kopyala ===
    function copyText(text, btn){
        if(!text) return;
        var done = function(){
            if(!btn) return;
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i>';
            btn.classList.add("copied");
            setTimeout(function(){ btn.innerHTML = orig; btn.classList.remove("copied"); }, 1400);
        };
        if(navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText(text).then(done).catch(function(){
                fallbackCopy(text, done);
            });
        } else { fallbackCopy(text, done); }
    }
    function fallbackCopy(text, done){
        var ta = document.createElement("textarea");
        ta.value = text; ta.style.cssText = "position:fixed;opacity:0;";
        document.body.appendChild(ta); ta.select();
        try { document.execCommand("copy"); done(); } catch(e){}
        document.body.removeChild(ta);
    }

    // === Sifre toggle ===
    function togglePw(btn){
        var code = btn.previousElementSibling;
        if(!code || !code.dataset.pw) return;
        var icon = btn.querySelector("i");
        if(code.classList.contains("cdg-pd2-cred-masked")){
            code.textContent = code.dataset.pw;
            code.classList.remove("cdg-pd2-cred-masked");
            if(icon){ icon.classList.remove("bi-eye"); icon.classList.add("bi-eye-slash"); }
        } else {
            code.textContent = "\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022";
            code.classList.add("cdg-pd2-cred-masked");
            if(icon){ icon.classList.remove("bi-eye-slash"); icon.classList.add("bi-eye"); }
        }
    }

    // === MAIN EVENT DELEGATION ===
    document.addEventListener("click", function(ev){
        // Tab tiklama
        var tab = ev.target.closest(".cdg-pd2-tab");
        if(tab && tab.dataset.pane){
            ev.preventDefault();
            pd2Switch(tab.dataset.pane);
            return;
        }
        var pdmTab = ev.target.closest(".cdg-pdm-tab");
        if(pdmTab && pdmTab.dataset.pane){
            ev.preventDefault();
            pdmSwitch(pdmTab.dataset.pane);
            return;
        }

        // data-cdg-action
        var actEl = ev.target.closest("[data-cdg-action]");
        if(!actEl) return;
        var action = actEl.dataset.cdgAction;
        if(!action) return;

        switch(action){
            case "history-back":
                ev.preventDefault();
                if(window.history.length > 1){ window.history.back(); }
                else { var href = actEl.getAttribute("href"); if(href) window.location.href = href; }
                break;
            case "goto-tab":
                ev.preventDefault();
                pd2Switch(actEl.dataset.cdgTarget);
                try {
                    var tabs = document.querySelector(".cdg-pd2-tabs");
                    if(tabs) window.scrollTo({ top: tabs.offsetTop - 20, behavior: "smooth" });
                } catch(e){}
                break;
            case "sub-tab":
                ev.preventDefault();
                subTabSwitch(actEl.dataset.cdgTarget);
                break;
            case "copy-cred":
            case "copy-text":
            case "copy":
                ev.preventDefault();
                copyText(actEl.dataset.cdgText, actEl);
                break;
            case "toggle-pw":
                ev.preventDefault();
                togglePw(actEl);
                break;
            case "toggle-autopay":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.toggleAutoPay){
                    window.cdgPd2.toggleAutoPay(actEl.dataset.cdgState === "true");
                }
                break;
            case "server-action":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.serverAction){
                    window.cdgPd2.serverAction(actEl.dataset.cdgServerCmd);
                }
                break;
            case "add-addon":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.addAddon){
                    window.cdgPd2.addAddon(parseInt(actEl.dataset.cdgId, 10));
                }
                break;
            case "upgrade":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.upgrade){
                    window.cdgPd2.upgrade(parseInt(actEl.dataset.cdgId, 10));
                }
                break;
            case "remove-transfer":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.removeTransfer){
                    window.cdgPd2.removeTransfer(parseInt(actEl.dataset.cdgId, 10));
                }
                break;
            case "generate-password":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.generatePassword){
                    window.cdgPd2.generatePassword("cdg-pd2-newpass");
                    var p2 = document.getElementById("cdg-pd2-newpass2");
                    var p1 = document.getElementById("cdg-pd2-newpass");
                    if(p2 && p1) p2.value = p1.value;
                }
                break;
            case "order-renewal":
                ev.preventDefault();
                if(typeof cdgPd2OrderRenewal === "function") cdgPd2OrderRenewal(actEl);
                break;
            case "delete-email":
                ev.preventDefault();
                if(typeof window.cdgPd2 !== "undefined" && window.cdgPd2.deleteEmail){
                    window.cdgPd2.deleteEmail(actEl.dataset.cdgEmail);
                }
                break;
        }
    });

    // === Hash navigation - sayfa acildiginda URL'deki #pane'i aktif et ===
    function activateHash(){
        if(!location.hash) return;
        var h = location.hash.substring(1);
        if(!h) return;
        if(document.getElementById("cdg-pd2-pane-" + h)) pd2Switch(h);
        else if(document.getElementById("cdg-pdm-pane-" + h)) pdmSwitch(h);
    }
    if(document.readyState === "loading"){
        document.addEventListener("DOMContentLoaded", activateHash);
    } else { activateHash(); }
})();
</script>

<?php
// === CDG DEBUG PANEL (her panel sayfasinda yuklenir, F12 -> cdgDbg() ile acilir) ===
$_dbg_inc = __DIR__ . DIRECTORY_SEPARATOR . 'cdg-debug-panel.php';
if(file_exists($_dbg_inc)) include $_dbg_inc;
?>
