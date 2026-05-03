<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Sosyal Paylaşım Butonları
 * Facebook, X (Twitter), LinkedIn, WhatsApp, Telegram + Link kopyalama
 * WiseCP runtime: $canonical_link
 */

$share_url = isset($canonical_link) ? $canonical_link : '';
if(!$share_url && isset($_SERVER['REQUEST_URI'])) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $share_url = $proto . '://' . $host . $_SERVER['REQUEST_URI'];
}
$share_url_enc = urlencode($share_url);
$page_title = isset($page_title) ? $page_title : (isset($title) ? $title : '');
$page_title_enc = urlencode((string)$page_title);
?>

<div class="cdg-share">
    <a class="cdg-share-btn cdg-share-fb"
       href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url_enc; ?>"
       target="_blank" rel="noopener nofollow"
       onclick="cdgShareWindow(this.href);return false;"
       title="Facebook'ta paylaş" aria-label="Facebook'ta paylaş">
        <i class="bi bi-facebook"></i>
    </a>

    <a class="cdg-share-btn cdg-share-x"
       href="https://twitter.com/intent/tweet?text=<?php echo $page_title_enc; ?>&url=<?php echo $share_url_enc; ?>"
       target="_blank" rel="noopener nofollow"
       onclick="cdgShareWindow(this.href);return false;"
       title="X'te (Twitter) paylaş" aria-label="X'te paylaş">
        <i class="bi bi-twitter-x"></i>
    </a>

    <a class="cdg-share-btn cdg-share-li"
       href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url_enc; ?>"
       target="_blank" rel="noopener nofollow"
       onclick="cdgShareWindow(this.href);return false;"
       title="LinkedIn'de paylaş" aria-label="LinkedIn'de paylaş">
        <i class="bi bi-linkedin"></i>
    </a>

    <a class="cdg-share-btn cdg-share-wa"
       href="https://wa.me/?text=<?php echo $share_url_enc; ?>"
       target="_blank" rel="noopener nofollow"
       title="WhatsApp ile paylaş" aria-label="WhatsApp ile paylaş">
        <i class="bi bi-whatsapp"></i>
    </a>

    <a class="cdg-share-btn cdg-share-tg"
       href="https://t.me/share/url?url=<?php echo $share_url_enc; ?>&text=<?php echo $page_title_enc; ?>"
       target="_blank" rel="noopener nofollow"
       onclick="cdgShareWindow(this.href);return false;"
       title="Telegram ile paylaş" aria-label="Telegram ile paylaş">
        <i class="bi bi-telegram"></i>
    </a>

    <button class="cdg-share-btn cdg-share-copy" type="button"
            onclick="cdgShareCopy(this, '<?php echo htmlspecialchars($share_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>')"
            title="Linki kopyala" aria-label="Linki kopyala">
        <i class="bi bi-link-45deg"></i>
    </button>
</div>

<style>
.cdg-share {
    display: inline-flex;
    gap: 8px;
    flex-wrap: wrap;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-share *, .cdg-share *::before, .cdg-share *::after { box-sizing: border-box; }
.cdg-share-btn {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: grid; place-items: center;
    color: #fff;
    text-decoration: none;
    border: 0;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.18s;
    font-family: inherit;
    box-shadow: 0 2px 6px rgba(15,23,42,0.12);
}
.cdg-share-btn:hover { transform: translateY(-2px); color: #fff; box-shadow: 0 6px 14px rgba(15,23,42,0.18); }
.cdg-share-btn.cdg-share-fb { background: #1877f2; }
.cdg-share-btn.cdg-share-x  { background: #0f1419; }
.cdg-share-btn.cdg-share-li { background: #0a66c2; }
.cdg-share-btn.cdg-share-wa { background: #25d366; }
.cdg-share-btn.cdg-share-tg { background: #229ed9; }
.cdg-share-btn.cdg-share-copy { background: #64748b; }
.cdg-share-btn.cdg-share-copy.cdg-copied { background: #10b981; }
</style>

<script>
function cdgShareWindow(url) {
    var w = 600, h = 540;
    var l = (screen.width - w) / 2;
    var t = (screen.height - h) / 2;
    window.open(url, 'cdg_share', 'width=' + w + ',height=' + h + ',top=' + t + ',left=' + l + ',scrollbars=yes,resizable=yes');
}
function cdgShareCopy(btn, url) {
    if(navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){
            btn.classList.add('cdg-copied');
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i>';
            setTimeout(function(){
                btn.classList.remove('cdg-copied');
                btn.innerHTML = orig;
            }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
    }
}
</script>
