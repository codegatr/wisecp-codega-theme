<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

$page_data = isset($page) && is_array($page) ? $page : [];
$page_title = $page_data['title'] ?? 'Makale';
$page_content = $page_data['content'] ?? '';
$visit_count = $page_data['visit_count'] ?? 0;
$useful_count = $page_data['useful'] ?? 0;
$show_ticket = !empty($visibility_ticket);
$kb_tickets_link = isset($tickets_link) ? $tickets_link : (isset($links['tickets']) ? $links['tickets'] : '#');
$kb_canonical = isset($canonical_link) ? $canonical_link : '';
$kb_sidebar = isset($sidebar_status) ? !empty($sidebar_status) : true;
$kb_similar = isset($similar) && is_array($similar) ? $similar : [];
$kb_category = isset($category) && is_array($category) ? $category : null;

$kb_csrf = '';
if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) {
    try { $kb_csrf = Validation::get_csrf_token('kbase', false); } catch(\Throwable $e) {}
}
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>">Anasayfa</a>
            <span class="sep">/</span>
            <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('knowledgebase') : '/knowledgebase'); ?>">Bilgi Bankasi</a>
            <span class="sep">/</span>
            <span><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-kb-detail-grid" style="display:grid;grid-template-columns:<?php echo $kb_sidebar ? '1fr 320px' : '1fr'; ?>;gap:24px;">

            <article class="cdg-kb-article cdg-card" style="padding:32px;font-family:'Plus Jakarta Sans',sans-serif;">
                <header style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
                    <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin:0 0 12px;line-height:1.3;">
                        <?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </h2>
                    <div style="display:flex;gap:14px;font-size:12px;color:#64748b;flex-wrap:wrap;">
                        <span><i class="bi bi-eye"></i> <?php echo (int)$visit_count; ?> goruntulenme</span>
                        <span><i class="bi bi-hand-thumbs-up"></i> <?php echo (int)$useful_count; ?> faydali</span>
                        <?php if($kb_category && !empty($kb_category['title'])): ?>
                        <span><i class="bi bi-folder"></i> <a href="<?php echo htmlspecialchars($kb_category['route'] ?? '#', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="color:#1e40af;text-decoration:none;"><?php echo htmlspecialchars($kb_category['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a></span>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="cdg-kb-content" style="font-size:15px;line-height:1.7;color:#334155;">
                    <?php echo $page_content; ?>
                </div>

                <?php if($show_ticket): ?>
                <div style="margin-top:32px;padding:24px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #93c5fd;border-radius:12px;text-align:center;">
                    <div style="width:54px;height:54px;border-radius:50%;background:#fff;color:#1e40af;display:inline-grid;place-items:center;font-size:22px;margin-bottom:10px;box-shadow:0 4px 8px rgba(30,64,175,0.10);">
                        <i class="bi bi-life-preserver"></i>
                    </div>
                    <h4 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px;">Bu makale sorununuzu cozemedi mi?</h4>
                    <p style="font-size:13px;color:#475569;margin:0 0 14px;">Destek ekibimiz size yardimci olmak icin hazir.</p>
                    <a href="<?php echo htmlspecialchars($kb_tickets_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn cdg-btn-primary">
                        <i class="bi bi-life-preserver"></i> Destek Talebi Olustur
                    </a>
                </div>
                <?php endif; ?>

                <div style="margin-top:32px;padding:20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                    <div id="cdg-kb-voting">
                        <h4 style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 12px;text-align:center;">
                            <i class="bi bi-question-circle"></i> Bu makale faydali oldu mu?
                        </h4>
                        <div style="display:flex;gap:8px;justify-content:center;">
                            <button type="button" class="cdg-btn cdg-btn-success" onclick="cdgKbVote('useful', this)" style="padding:10px 20px;">
                                <i class="bi bi-hand-thumbs-up"></i> Evet, faydali
                            </button>
                            <button type="button" class="cdg-btn cdg-btn-outline" onclick="cdgKbVote('useless', this)" style="padding:10px 20px;color:#ef4444;border-color:#fca5a5;">
                                <i class="bi bi-hand-thumbs-down"></i> Faydasiz
                            </button>
                        </div>
                    </div>
                    <div id="cdg-kb-vote-success-1" style="display:none;text-align:center;color:#10b981;font-weight:700;font-size:14px;">
                        <i class="bi bi-check-circle-fill"></i> Geri bildiriminiz icin tesekkurler!
                    </div>
                    <div id="cdg-kb-vote-success-2" style="display:none;text-align:center;color:#f59e0b;font-weight:700;font-size:14px;">
                        <i class="bi bi-info-circle-fill"></i> Geri bildiriminiz alindi. Iyilestirmeye calisacagiz.
                    </div>
                    <div id="cdg-kb-vote-success-3" style="display:none;text-align:center;color:#64748b;font-weight:700;font-size:14px;">
                        <i class="bi bi-exclamation-circle"></i> Bu makaleye zaten oy verdiniz.
                    </div>
                </div>
            </article>

            <?php if($kb_sidebar): ?>
            <aside class="cdg-kb-sidebar" style="display:flex;flex-direction:column;gap:16px;">

                <?php if(!empty($kb_similar)): ?>
                <div class="cdg-card" style="padding:20px;">
                    <h4 style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 14px;display:flex;align-items:center;gap:6px;">
                        <i class="bi bi-collection" style="color:#1e40af;"></i> Ilgili Makaleler
                    </h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px;">
                        <?php foreach($kb_similar as $s):
                            $s_title = $s['title'] ?? ($s['short_title'] ?? '');
                            $s_route = $s['route'] ?? '#';
                            if(!$s_title) continue;
                        ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($s_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:flex;gap:8px;padding:8px 10px;text-decoration:none;color:#334155;font-size:13px;border-radius:6px;">
                                <i class="bi bi-arrow-right-short" style="color:#1e40af;flex-shrink:0;font-size:16px;"></i>
                                <span><?php echo htmlspecialchars($s_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="cdg-card" style="padding:20px;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;">
                    <i class="bi bi-headset" style="font-size:28px;"></i>
                    <h4 style="font-size:14px;font-weight:800;margin:8px 0 6px;">Yardim Lazim mi?</h4>
                    <p style="font-size:12px;opacity:0.9;margin:0 0 12px;">7/24 destek ekibimiz yardimci olmaktan mutluluk duyar.</p>
                    <a href="<?php echo htmlspecialchars($kb_tickets_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;color:#1e40af;text-decoration:none;border-radius:6px;font-size:12px;font-weight:700;">
                        <i class="bi bi-chat-dots"></i> Bize Yazin
                    </a>
                </div>

            </aside>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.cdg-kb-content h1, .cdg-kb-content h2, .cdg-kb-content h3, .cdg-kb-content h4 { color: #0f172a; font-weight: 800; margin: 18px 0 10px; }
.cdg-kb-content h2 { font-size: 20px; }
.cdg-kb-content h3 { font-size: 17px; }
.cdg-kb-content h4 { font-size: 15px; }
.cdg-kb-content p { margin: 0 0 14px; }
.cdg-kb-content code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; color: #be185d; }
.cdg-kb-content pre { background: #0f172a; color: #f1f5f9; padding: 14px 18px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
.cdg-kb-content pre code { background: none; color: inherit; padding: 0; }
.cdg-kb-content ul, .cdg-kb-content ol { padding-left: 24px; margin: 0 0 14px; }
.cdg-kb-content ul li, .cdg-kb-content ol li { margin-bottom: 6px; }
.cdg-kb-content blockquote { border-left: 3px solid #3b82f6; background: #eff6ff; padding: 12px 16px; margin: 14px 0; border-radius: 0 8px 8px 0; color: #1e40af; }
.cdg-kb-content img { max-width: 100%; height: auto; border-radius: 8px; }
.cdg-kb-content table { width: 100%; border-collapse: collapse; margin: 14px 0; }
.cdg-kb-content table th, .cdg-kb-content table td { padding: 8px 12px; border: 1px solid #e2e8f0; font-size: 13px; }
.cdg-kb-content table th { background: #f8fafc; font-weight: 700; }
.cdg-kb-content a { color: #1e40af; text-decoration: underline; }

@media (max-width: 900px) {
    .cdg-kb-detail-grid { grid-template-columns: 1fr !important; }
}
</style>

<script>
(function(){
    var cdgKbCanonical = '<?php echo htmlspecialchars($kb_canonical, ENT_QUOTES); ?>';
    var cdgKbCsrf = '<?php echo htmlspecialchars($kb_csrf, ENT_QUOTES); ?>';

    window.cdgKbVote = function(type, btn) {
        if(!cdgKbCanonical) return;
        var url = cdgKbCanonical + (cdgKbCanonical.indexOf('?') !== -1 ? '&' : '?') + 'vote=' + type + '&token=' + cdgKbCsrf;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gonderiliyor...';

        fetch(url, { method: 'GET', credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(t){
                btn.disabled = false; btn.innerHTML = orig;
                var solve = null;
                try { solve = JSON.parse(t); } catch(e){}
                if(solve && solve.status) {
                    if(solve.status === 'error') {
                        if(typeof alert_error === 'function') alert_error(solve.message || 'Bir hata olustu', {timer: 3000});
                    } else {
                        var voting = document.getElementById('cdg-kb-voting');
                        if(voting) voting.style.display = 'none';
                        var success = document.getElementById('cdg-kb-vote-' + solve.status);
                        if(success) success.style.display = 'block';
                    }
                }
            })
            .catch(function(){
                btn.disabled = false; btn.innerHTML = orig;
                if(typeof alert_error === 'function') alert_error('Baglanti hatasi', {timer: 3000});
            });
    };
})();
</script>
