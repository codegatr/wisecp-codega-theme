/*!
 * Codega Theme — Frontend JS
 */
(function(){
    'use strict';

    // ========== Mobile menu toggle ==========
    var toggle = document.querySelector('.cdg-mobile-toggle');
    var nav    = document.querySelector('.cdg-nav');
    if(toggle && nav){
        toggle.addEventListener('click', function(){
            nav.classList.toggle('open');
        });
    }

    // ========== Mobile mega menu accordion (Kurumsal dropdown) ==========
    var megaToggles = document.querySelectorAll('.cdg-nav-mega-toggle');
    megaToggles.forEach(function(mt){
        mt.addEventListener('click', function(ev){
            // Sadece mobilde (992px altı) açıp kapama yap
            if(window.innerWidth > 992) return;
            ev.preventDefault();
            ev.stopPropagation();
            var mega = mt.parentElement.querySelector('.cdg-nav-mega');
            if(mega) mega.classList.toggle('open');
        });
    });

    // ========== Scroll reveal (IntersectionObserver) ==========
    if('IntersectionObserver' in window){
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(e){
                if(e.isIntersecting){
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.10, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.cdg-reveal').forEach(function(el){ io.observe(el); });
    } else {
        document.querySelectorAll('.cdg-reveal').forEach(function(el){ el.classList.add('is-visible'); });
    }

    // ========== Header background on scroll ==========
    var header = document.querySelector('.cdg-header');
    if(header){
        var lastScroll = 0;
        window.addEventListener('scroll', function(){
            var s = window.scrollY;
            if(s > 8) header.classList.add('is-scrolled');
            else      header.classList.remove('is-scrolled');
            lastScroll = s;
        }, { passive: true });
    }

    // ========== Smooth scroll for hash links ==========
    document.querySelectorAll('a[href^="#"]').forEach(function(a){
        a.addEventListener('click', function(e){
            var href = this.getAttribute('href');
            if(href === '#' || href.length < 2) return;
            var target = document.querySelector(href);
            if(target){
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ========== Domain checker form (basit yönlendirme) ==========
    var domainForm = document.querySelector('.cdg-domain-check form');
    if(domainForm){
        domainForm.addEventListener('submit', function(e){
            var input = this.querySelector('input[name="domain"]');
            if(!input || !input.value.trim()){
                e.preventDefault();
                input && input.focus();
            }
        });
    }

})();

// ========== Back-to-top button scroll handler ==========
(function(){
    var btn = document.querySelector('.back-to-top');
    if(!btn) return;

    function check() {
        if(window.scrollY > 300) btn.classList.add('active');
        else btn.classList.remove('active');
    }
    window.addEventListener('scroll', check, { passive: true });

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    check();
})();

// ========== Product Detail Tab Switcher (cdg-pd2-*) ==========
// Hosting/Server/SMS/Software/Special ürün detay sayfalarında tab değiştirme
// Inline onclick yerine event delegation ile çalışır - daha güvenilir
(function(){
    'use strict';

    // === HOSTING/SERVER/SMS PRODUCT DETAIL TABS ===
    function switchPd2Tab(pane) {
        if(!pane) return false;
        try {
            // Active class'ları sıfırla
            var tabs = document.querySelectorAll('.cdg-pd2-tab');
            var panes = document.querySelectorAll('.cdg-pd2-pane');
            if(tabs.length === 0 || panes.length === 0) return false;

            tabs.forEach(function(t){ t.classList.remove('active'); });
            panes.forEach(function(p){ p.classList.remove('active'); });

            // Hedef tab'ı aktif yap
            var targetTab = document.querySelector('.cdg-pd2-tab[data-pane="' + pane + '"]');
            if(targetTab) targetTab.classList.add('active');

            // Hedef pane'i aktif yap
            var targetPane = document.getElementById('cdg-pd2-pane-' + pane);
            if(targetPane) {
                targetPane.classList.add('active');
            } else {
                console.warn('[cdgPd2] Pane bulunamadı: cdg-pd2-pane-' + pane);
            }

            // URL hash güncelle
            try { history.replaceState(null, '', '#' + pane); } catch(e) {}

            // Custom event
            try { document.dispatchEvent(new CustomEvent('cdgPd2TabChanged', { detail: { pane: pane } })); } catch(e) {}

            return true;
        } catch(err) {
            console.error('[cdgPd2] Tab switch error:', err);
            return false;
        }
    }

    // Global olarak expose et (inline onclick'ler için geriye dönük uyumluluk)
    window.cdgPd2Switch = function(btn, pane) {
        switchPd2Tab(pane);
    };

    // Event delegation - daha güvenilir
    document.addEventListener('click', function(ev){
        var tab = ev.target.closest('.cdg-pd2-tab');
        if(!tab) return;
        ev.preventDefault();
        var pane = tab.getAttribute('data-pane');
        if(pane) switchPd2Tab(pane);
    });

    // Sayfa yüklendiğinde URL hash'le geldiyse o tab'ı aç
    function activatePd2HashTab() {
        if(!location.hash) return;
        var hash = location.hash.substring(1);
        if(!hash) return;
        // Hash'in valid bir pane olup olmadığını kontrol et
        var targetPane = document.getElementById('cdg-pd2-pane-' + hash);
        if(targetPane) {
            switchPd2Tab(hash);
        }
    }
    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', activatePd2HashTab);
    } else {
        activatePd2HashTab();
    }

    // === DOMAIN PRODUCT DETAIL TABS (cdg-pdm-*) ===
    function switchPdmTab(pane) {
        if(!pane) return false;
        try {
            var tabs = document.querySelectorAll('.cdg-pdm-tab');
            var panes = document.querySelectorAll('.cdg-pdm-pane');
            if(tabs.length === 0 || panes.length === 0) return false;

            tabs.forEach(function(t){ t.classList.remove('active'); });
            panes.forEach(function(p){ p.classList.remove('active'); });

            var targetTab = document.querySelector('.cdg-pdm-tab[data-pane="' + pane + '"]');
            if(targetTab) targetTab.classList.add('active');

            var targetPane = document.getElementById('cdg-pdm-pane-' + pane);
            if(targetPane) targetPane.classList.add('active');

            try { history.replaceState(null, '', '#' + pane); } catch(e) {}
            return true;
        } catch(err) {
            console.error('[cdgPdm] Tab switch error:', err);
            return false;
        }
    }

    window.cdgPdmSwitch = function(btn, pane) {
        switchPdmTab(pane);
    };

    document.addEventListener('click', function(ev){
        var tab = ev.target.closest('.cdg-pdm-tab');
        if(!tab) return;
        ev.preventDefault();
        var pane = tab.getAttribute('data-pane');
        if(pane) switchPdmTab(pane);
    });

    function activatePdmHashTab() {
        if(!location.hash) return;
        var hash = location.hash.substring(1);
        if(!hash) return;
        var targetPane = document.getElementById('cdg-pdm-pane-' + hash);
        if(targetPane) {
            switchPdmTab(hash);
        }
    }
    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', activatePdmHashTab);
    } else {
        activatePdmHashTab();
    }
})();
