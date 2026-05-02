<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Generic Sayfa Detay Sablonu (page-detail-articles, news, references, software icin ortak)
 *
 * Caller variables:
 *  - $cdg_pd_kind   : 'articles' | 'news' | 'references' | 'software'
 *  - $cdg_pd_label  : breadcrumb etiketi (ornek: 'Makaleler', 'Haberler')
 *  - $cdg_pd_icon   : Bootstrap icon (ornek: 'newspaper', 'star')
 *  - $cdg_pd_list_link : Liste sayfasinin linki
 *
 * WiseCP runtime:
 *  - $page (title, content, image, cdate, visit_count)
 *  - $sidebar_status, $sidebar
 */

$page_data = isset($page) && is_array($page) ? $page : [];
$page_title = $page_data['title'] ?? 'Sayfa';
$page_content = $page_data['content'] ?? '';
$page_image = $page_data['image'] ?? '';
$page_date = $page_data['cdate'] ?? ($page_data['date'] ?? '');
$page_views = $page_data['visit_count'] ?? 0;

$pd_label = $cdg_pd_label ?? 'Sayfa';
$pd_icon = $cdg_pd_icon ?? 'file-earmark';
$pd_list_link = $cdg_pd_list_link ?? '#';
$pd_sidebar = isset($sidebar_status) && $sidebar_status;
$pd_sidebar_items = isset($sidebar) && is_array($sidebar) ? $sidebar : [];
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-<?php echo htmlspecialchars($pd_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i> <?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>">Anasayfa</a>
            <span class="sep">/</span>
            <a href="<?php echo htmlspecialchars($pd_list_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($pd_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a>
            <span class="sep">/</span>
            <span><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-pd-grid" style="display:grid;grid-template-columns:<?php echo $pd_sidebar ? '1fr 280px' : '1fr'; ?>;gap:24px;">

            <article class="cdg-card cdg-pd-content" style="padding:0;overflow:hidden;font-family:'Plus Jakarta Sans',sans-serif;">

                <?php if($page_image): ?>
                <div style="height:280px;overflow:hidden;background:linear-gradient(135deg,#1e40af,#3b82f6);">
                    <img src="<?php echo htmlspecialchars($page_image, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php endif; ?>

                <div style="padding:32px;">
                    <header style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
                        <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin:0 0 12px;line-height:1.3;">
                            <?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </h2>
                        <?php if($page_date || $page_views): ?>
                        <div style="display:flex;gap:14px;font-size:12px;color:#64748b;flex-wrap:wrap;">
                            <?php if($page_date): ?>
                            <span><i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($page_date, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <?php if($page_views): ?>
                            <span><i class="bi bi-eye"></i> <?php echo (int)$page_views; ?> goruntulenme</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </header>

                    <div class="cdg-pd-content-body" style="font-size:15px;line-height:1.7;color:#334155;">
                        <?php if($page_content): ?>
                            <?php echo $page_content; ?>
                        <?php else: ?>
                        <div style="text-align:center;padding:30px 20px;color:#94a3b8;">
                            <i class="bi bi-file-earmark-text" style="font-size:48px;display:block;margin-bottom:8px;opacity:0.5;"></i>
                            <p style="font-size:14px;margin:0;">Icerik henuz eklenmemis.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top:32px;padding-top:18px;border-top:1px solid #e2e8f0;display:flex;gap:8px;flex-wrap:wrap;">
                        <a href="<?php echo htmlspecialchars($pd_list_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-btn cdg-btn-outline">
                            <i class="bi bi-arrow-left"></i> Tum <?php echo htmlspecialchars($pd_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </a>
                        <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-primary">
                            <i class="bi bi-chat-dots"></i> İletişime Gec
                        </a>
                    </div>
                </div>
            </article>

            <?php if($pd_sidebar && !empty($pd_sidebar_items)): ?>
            <aside class="cdg-pd-sidebar" style="display:flex;flex-direction:column;gap:16px;">
                <div class="cdg-card" style="padding:20px;">
                    <h4 style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 14px;display:flex;align-items:center;gap:6px;">
                        <i class="bi bi-list-ul" style="color:#1e40af;"></i> Diger <?php echo htmlspecialchars($pd_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:4px;">
                        <?php foreach($pd_sidebar_items as $side):
                            $s_title = $side['title'] ?? '';
                            $s_link = $side['link'] ?? '#';
                            if(!$s_title) continue;
                        ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($s_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:flex;gap:8px;padding:8px 12px;text-decoration:none;color:#334155;font-size:13px;border-radius:6px;">
                                <i class="bi bi-arrow-right-short" style="color:#1e40af;flex-shrink:0;font-size:16px;"></i>
                                <span><?php echo htmlspecialchars($s_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.cdg-pd-content-body h1, .cdg-pd-content-body h2, .cdg-pd-content-body h3, .cdg-pd-content-body h4, .cdg-pd-content-body h5, .cdg-pd-content-body h6 { color: #0f172a; font-weight: 800; margin: 22px 0 12px; line-height: 1.3; }
.cdg-pd-content-body h2 { font-size: 22px; }
.cdg-pd-content-body h3 { font-size: 19px; }
.cdg-pd-content-body p { margin: 0 0 14px; }
.cdg-pd-content-body ul, .cdg-pd-content-body ol { padding-left: 24px; margin: 0 0 14px; }
.cdg-pd-content-body ul li, .cdg-pd-content-body ol li { margin-bottom: 6px; }
.cdg-pd-content-body img { max-width: 100%; height: auto; border-radius: 8px; }
.cdg-pd-content-body blockquote { border-left: 3px solid #3b82f6; background: #eff6ff; padding: 12px 16px; margin: 14px 0; border-radius: 0 8px 8px 0; color: #1e40af; }
.cdg-pd-content-body a { color: #1e40af; text-decoration: underline; }

@media (max-width: 900px) {
    .cdg-pd-grid { grid-template-columns: 1fr !important; }
}
</style>
