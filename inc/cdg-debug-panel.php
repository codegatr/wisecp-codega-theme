<?php /* CDG-DEBUG-PANEL: ?debug=1 ile aktiveye olur */ ?>
<?php if(!empty($_GET['debug'])): ?>
<style>
#cdg-dbg {
    position: fixed; top: 12px; right: 12px;
    width: 420px; max-height: 80vh;
    background: #0f172a; color: #4ade80;
    font-family: "JetBrains Mono", Consolas, monospace;
    font-size: 11px; line-height: 1.55;
    border: 2px solid #4ade80;
    border-radius: 10px; padding: 10px 12px;
    z-index: 999999; overflow-y: auto;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}
#cdg-dbg h4 { color: #fff; margin: 0 0 8px; font-size: 13px; padding-bottom: 6px; border-bottom: 1px solid #334155; }
#cdg-dbg h4 button { float: right; background: #ef4444; color: #fff; border: 0; padding: 2px 8px; border-radius: 4px; font-size: 10px; cursor: pointer; }
#cdg-dbg-log div { padding: 1px 0; word-break: break-all; }
.dbg-info { color: #4ade80; }
.dbg-data { color: #67e8f9; }
.dbg-warn { color: #fbbf24; }
.dbg-err  { color: #f87171; }
.dbg-act  { color: #f0abfc; font-weight: 600; }
.dbg-ok   { color: #fff; }
</style>
<div id="cdg-dbg">
    <h4>🔧 CDG DEBUG <button type="button" id="cdg-dbg-close">×</button></h4>
    <div id="cdg-dbg-log"></div>
</div>
<script>
(function(){
    var logEl = document.getElementById('cdg-dbg-log');
    var closeBtn = document.getElementById('cdg-dbg-close');
    var dbgEl = document.getElementById('cdg-dbg');

    function log(msg, cls){
        var d = document.createElement('div');
        d.className = cls || 'dbg-info';
        var t = new Date().toTimeString().substr(0, 8);
        d.textContent = '[' + t + '] ' + msg;
        logEl.appendChild(d);
        if(window.console && console.log) console.log('[CDG]', msg);
    }

    closeBtn.onclick = function(){ dbgEl.style.display = 'none'; };

    // === BAŞLANGIÇ TEŞHİSİ ===
    log('▶ DEBUG aktif', 'dbg-ok');

    // SES tespit
    var hasSES = (typeof lockdown !== 'undefined') || (typeof Compartment !== 'undefined') || (typeof harden !== 'undefined');
    log('SES Lockdown: ' + (hasSES ? 'AKTİF ⚠️' : 'YOK'), hasSES ? 'dbg-warn' : 'dbg-info');

    // Tab/Pane sayıları
    var tabs = document.querySelectorAll('.cdg-pd2-tab');
    var panes = document.querySelectorAll('.cdg-pd2-pane');
    log('Tab DOM: ' + tabs.length + ' eleman', 'dbg-data');
    log('Pane DOM: ' + panes.length + ' eleman', 'dbg-data');

    // Tab listesi
    for(var i = 0; i < tabs.length; i++){
        log('  ↳ tab[' + i + '] data-pane=' + tabs[i].getAttribute('data-pane'), 'dbg-data');
    }
    for(var j = 0; j < panes.length; j++){
        log('  ↳ pane[' + j + '] id=' + panes[j].id, 'dbg-data');
    }

    // Global function tespit
    log('window.cdgPd2Switch: ' + typeof window.cdgPd2Switch, 'dbg-data');
    log('window.cdgPd2: ' + typeof window.cdgPd2, 'dbg-data');

    // External script.js kontrol
    var scripts = document.querySelectorAll('script[src*="script.js"]');
    log('script.js yüklemeleri: ' + scripts.length, scripts.length > 0 ? 'dbg-info' : 'dbg-err');
    for(var k = 0; k < scripts.length; k++){
        log('  ' + scripts[k].src.substring(scripts[k].src.lastIndexOf('/')+1), 'dbg-data');
    }

    // Inline <script> sayısı (yaklaşık)
    log('Toplam <script> blok: ' + document.querySelectorAll('script').length, 'dbg-data');

    // === CLICK TRACKER (capture phase ile herkesten önce yakala) ===
    function onAnyClick(ev){
        var node = ev.target;
        var depth = 0;
        while(node && node !== document.body && depth < 10){
            var cls = node.className;
            if(typeof cls === 'string' && cls.indexOf('cdg-pd2-tab') !== -1){
                var pane = node.getAttribute('data-pane');
                log('● TAB CLICK: ' + pane, 'dbg-act');
                log('  defaultPrevented: ' + ev.defaultPrevented, 'dbg-data');

                // 100ms sonra DOM durumunu kontrol et
                setTimeout(function(){
                    var activeTabs = document.querySelectorAll('.cdg-pd2-tab.active');
                    var activePanes = document.querySelectorAll('.cdg-pd2-pane.active');
                    log('  → 100ms sonra: aktif tab=' + activeTabs.length + ' aktif pane=' + activePanes.length, 'dbg-warn');
                    if(activeTabs.length > 0) log('     ' + activeTabs[0].getAttribute('data-pane'), 'dbg-data');
                    if(activePanes.length > 0) log('     ' + activePanes[0].id, 'dbg-data');
                }, 100);
                return;
            }
            node = node.parentNode;
            depth++;
        }
    }
    document.addEventListener('click', onAnyClick, true); // capture phase

    // === MİNİMAL TAB HANDLER (FALLBACK) ===
    // Eğer ana script yüklenmediyse veya çalışmıyorsa, BURADA GARANTİ ÇALIŞIR
    function activateMinimal(pane){
        if(!pane) return;
        var i;
        for(i = 0; i < tabs.length; i++){
            tabs[i].className = (tabs[i].getAttribute('data-pane') === pane)
                ? 'cdg-pd2-tab active'
                : 'cdg-pd2-tab';
        }
        for(i = 0; i < panes.length; i++){
            panes[i].className = (panes[i].id === 'cdg-pd2-pane-' + pane)
                ? 'cdg-pd2-pane active'
                : 'cdg-pd2-pane';
        }
        log('  ✓ Fallback handler aktive: ' + pane, 'dbg-ok');
    }
    document.addEventListener('click', function(ev){
        var node = ev.target;
        var depth = 0;
        while(node && node !== document.body && depth < 10){
            var cls = node.className;
            if(typeof cls === 'string' && cls.indexOf('cdg-pd2-tab') !== -1){
                ev.preventDefault();
                activateMinimal(node.getAttribute('data-pane'));
                return;
            }
            node = node.parentNode;
            depth++;
        }
    }, false); // bubble phase

    log('✓ Click tracker + fallback handler kayıtlı', 'dbg-ok');
    log('═════════════════════════════', 'dbg-info');
    log('Tab\'lara tıkla, log düşsün.', 'dbg-warn');
})();
</script>
<?php endif; ?>
