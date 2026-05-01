/**
 * CODEGA Theme - Frontend JS
 * Minimal vanilla JS for interactions.
 */
(function () {
    'use strict';

    // Mobile sidebar toggle
    var sidebar = document.getElementById('cgSidebar');
    if (sidebar) {
        var toggleBtn = document.querySelector('[data-cg-toggle="sidebar"]');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                sidebar.classList.toggle('open');
            });
        }

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', function (e) {
            if (window.innerWidth > 992) return;
            if (!sidebar.classList.contains('open')) return;
            if (sidebar.contains(e.target)) return;
            if (e.target.closest('[data-cg-toggle="sidebar"]')) return;
            sidebar.classList.remove('open');
        });
    }

    // Smooth scroll for in-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var href = a.getAttribute('href');
            if (href.length <= 1) return;
            var target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // Color input sync (admin theme settings)
    document.querySelectorAll('.cgs-color input[type="color"]').forEach(function (picker) {
        var text = picker.nextElementSibling;
        if (!text) return;
        picker.addEventListener('input', function () { text.value = picker.value; });
        text.addEventListener('input', function () {
            if (/^#[0-9a-fA-F]{6}$/.test(text.value)) picker.value = text.value;
        });
    });

    // Fade-up animation observer (progressive enhancement)
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('cg-fade-up');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('[data-cg-animate]').forEach(function (el) {
            io.observe(el);
        });
    }

    // Console signature
    if (window.console && console.log) {
        console.log('%cCODEGA%c · Tema v1.0.0',
            'background:#0a1628;color:#d4a574;font-family:Cormorant Garamond;font-size:18px;font-weight:600;padding:6px 12px;border-radius:4px;letter-spacing:0.1em;',
            'color:#5a6478;font-family:system-ui;font-size:12px;margin-left:8px;');
    }
})();
