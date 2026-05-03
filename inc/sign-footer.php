<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sign-in/up Footer
 * Back-to-top butonu + footer kodları + body kapanışı
 */
?>

<a href="#top" class="cdg-back-top" aria-label="Yukarı çık" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">
    <i class="bi bi-arrow-up"></i>
</a>

<style>
.cdg-back-top {
    position: fixed;
    right: 22px;
    bottom: 22px;
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    display: grid; place-items: center;
    text-decoration: none;
    font-size: 18px;
    box-shadow: 0 8px 22px rgba(46,59,78,0.30);
    opacity: 0;
    pointer-events: none;
    transform: translateY(8px);
    transition: opacity 0.25s, transform 0.25s;
    z-index: 100;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.cdg-back-top.cdg-show { opacity: 1; pointer-events: auto; transform: translateY(0); }
.cdg-back-top:hover { transform: translateY(-2px); color: #fff; }
</style>

<script>
(function(){
    var btn = document.querySelector('.cdg-back-top');
    if(!btn) return;
    function check(){
        if(window.scrollY > 240) btn.classList.add('cdg-show');
        else btn.classList.remove('cdg-show');
    }
    window.addEventListener('scroll', check, { passive: true });
    check();
})();
</script>

<?php
if(class_exists('View') && method_exists('View','footer_codes')) {
    View::footer_codes();
}
?>

</body>
</html>
