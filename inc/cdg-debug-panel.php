<?php /* CDG-DEBUG-PANEL: F12 console -> cdgDbg() ile aktif */ ?>
<style>
#cdg-dbg {
    display: none;
    position: fixed; top: 12px; right: 12px;
    width: 460px; max-height: 80vh;
    background: #0f172a; color: #4ade80;
    font-family: "JetBrains Mono", Consolas, monospace;
    font-size: 11px; line-height: 1.55;
    border: 2px solid #4ade80;
    border-radius: 10px; padding: 10px 12px;
    z-index: 999999; overflow-y: auto;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}
#cdg-dbg.open { display: block; }
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
    var dbgEl = document.getElementById('cdg-dbg');
    var logEl = document.getElementById('cdg-dbg-log');
    var closeBtn = document.getElementById('cdg-dbg-close');

    function log(msg, cls){
        var d = document.createElement('div');
        d.className = cls || 'dbg-info';
        var t = new Date().toTimeString().substr(0, 8);
        d.textContent = '[' + t + '] ' + msg;
        logEl.appendChild(d);
        if(window.console && console.log) console.log('[CDG]', msg);
    }

    function diagnose(){
        logEl.innerHTML = '';

        log('▶ DEBUG aktif', 'dbg-ok');

        var hasSES = (typeof lockdown !== 'undefined') || (typeof Compartment !== 'undefined') || (typeof harden !== 'undefined');
        log('SES Lockdown global: ' + (hasSES ? 'AKTİF' : 'YOK'), hasSES ? 'dbg-warn' : 'dbg-info');

        var sesRunning = false;
        try { sesRunning = Object.isFrozen(Object.prototype); } catch(e){ sesRunning = true; }
        log('Object.prototype donmus mu: ' + sesRunning, sesRunning ? 'dbg-warn' : 'dbg-info');

        log('Lokasyon: ' + window.location.pathname, 'dbg-data');

        var pd2Tabs = document.querySelectorAll('.cdg-pd2-tab');
        var pd2Panes = document.querySelectorAll('.cdg-pd2-pane');
        var pdmTabs = document.querySelectorAll('.cdg-pdm-tab');
        var pdmPanes = document.querySelectorAll('.cdg-pdm-pane');

        log('---- TAB DOM ----', 'dbg-warn');
        log('cdg-pd2-tab: ' + pd2Tabs.length, pd2Tabs.length ? 'dbg-data' : 'dbg-err');
        log('cdg-pd2-pane: ' + pd2Panes.length, pd2Panes.length ? 'dbg-data' : 'dbg-err');
        log('cdg-pdm-tab: ' + pdmTabs.length, 'dbg-data');
        log('cdg-pdm-pane: ' + pdmPanes.length, 'dbg-data');

        for(var i = 0; i < Math.min(pd2Tabs.length, 8); i++){
            var t = pd2Tabs[i];
            var on = t.getAttribute('onclick') ? ' [onclick:VAR!]' : '';
            log('  tab[' + i + '] data-pane=' + t.getAttribute('data-pane') + on, 'dbg-data');
        }
        for(var j = 0; j < Math.min(pd2Panes.length, 8); j++){
            var p = pd2Panes[j];
            log('  pane[' + j + '] id=' + p.id + ' display=' + getComputedStyle(p).display, 'dbg-data');
        }

        log('---- JS YUKLEMESI ----', 'dbg-warn');
        var scripts = document.querySelectorAll('script[src*="script.js"]');
        log('script.js src yuklemeleri: ' + scripts.length, scripts.length > 0 ? 'dbg-info' : 'dbg-err');
        for(var k = 0; k < scripts.length; k++){
            var src = scripts[k].src;
            log('  ' + src.substring(src.lastIndexOf('/') + 1), 'dbg-data');
        }
        log('Toplam <script> blok: ' + document.querySelectorAll('script').length, 'dbg-data');

        log('window.cdgPd2Switch: ' + typeof window.cdgPd2Switch, 'dbg-data');
        log('window.cdgPdmSwitch: ' + typeof window.cdgPdmSwitch, 'dbg-data');
        log('window.cdgPd2: ' + typeof window.cdgPd2, 'dbg-data');
        log('window.cdgToast: ' + typeof window.cdgToast, 'dbg-data');

        log('═════════════════════════════', 'dbg-info');
        log('Tablara tikla, log dussun.', 'dbg-warn');
    }

    document.addEventListener('click', function(ev){
        if(!dbgEl.classList.contains('open')) return;
        var node = ev.target;
        var depth = 0;
        while(node && node !== document.body && depth < 10){
            var cls = node.className;
            if(typeof cls === 'string' && (cls.indexOf('cdg-pd2-tab') !== -1 || cls.indexOf('cdg-pdm-tab') !== -1)){
                var pane = node.getAttribute('data-pane');
                log('● TAB CLICK: ' + pane, 'dbg-act');
                log('  defaultPrevented: ' + ev.defaultPrevented, 'dbg-data');

                setTimeout(function(){
                    var activeTabs = document.querySelectorAll('.cdg-pd2-tab.active, .cdg-pdm-tab.active');
                    var activePanes = document.querySelectorAll('.cdg-pd2-pane.active, .cdg-pdm-pane.active');
                    log('  100ms sonra aktif tab=' + activeTabs.length + ' pane=' + activePanes.length, 'dbg-warn');
                    if(activeTabs[0]) log('     tab: ' + activeTabs[0].getAttribute('data-pane'), 'dbg-data');
                    if(activePanes[0]) log('     pane: ' + activePanes[0].id, 'dbg-data');
                }, 100);
                return;
            }
            node = node.parentNode;
            depth++;
        }
    }, true);

    window.addEventListener('error', function(e){
        if(!dbgEl.classList.contains('open')) return;
        log('JS ERROR: ' + e.message + ' @ ' + (e.filename || '?') + ':' + (e.lineno || '?'), 'dbg-err');
    });

    document.addEventListener('click', function(ev){
        if(!dbgEl.classList.contains('open')) return;
        var node = ev.target;
        var depth = 0;
        while(node && node !== document.body && depth < 10){
            var cls = node.className;
            if(typeof cls === 'string' && cls.indexOf('cdg-pd2-tab') !== -1){
                ev.preventDefault();
                var pane = node.getAttribute('data-pane');
                if(!pane) return;
                var tabs = document.querySelectorAll('.cdg-pd2-tab');
                var panes = document.querySelectorAll('.cdg-pd2-pane');
                for(var i = 0; i < tabs.length; i++){
                    tabs[i].className = (tabs[i].getAttribute('data-pane') === pane) ? 'cdg-pd2-tab active' : 'cdg-pd2-tab';
                }
                for(i = 0; i < panes.length; i++){
                    panes[i].className = (panes[i].id === 'cdg-pd2-pane-' + pane) ? 'cdg-pd2-pane active' : 'cdg-pd2-pane';
                }
                log('  Fallback handler aktive etti: ' + pane, 'dbg-ok');
                return;
            }
            node = node.parentNode;
            depth++;
        }
    }, false);

    closeBtn.onclick = function(){ dbgEl.classList.remove('open'); };

    window.cdgDbg = function(){
        dbgEl.classList.add('open');
        diagnose();
        return 'Debug panel acildi';
    };

    if(window.console && console.log){
        console.log('%c🔧 CDG Debug yuklendi. Acmak icin: cdgDbg()',
                    'background:#0f172a;color:#4ade80;padding:8px 14px;border-radius:6px;font-weight:bold;');
    }
})();
</script>
