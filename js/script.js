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
