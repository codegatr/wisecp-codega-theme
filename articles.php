<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';

$articles_list = isset($list) && is_array($list) ? $list : [];
$articles_category = isset($category) && is_array($category) ? $category : null;
$articles_pagination = isset($pagination) ? $pagination : '';
$articles_categories = isset($categories) && is_array($categories) ? $categories : [];

$page_title = $articles_category['title'] ?? 'Makaleler';
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>">Anasayfa</a>
            <span class="sep">/</span>
            <?php if($articles_category): ?>
                <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('articles') : '/articles'); ?>">Makaleler</a>
                <span class="sep">/</span>
                <span><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            <?php else: ?>
                <span>Makaleler</span>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-articles-grid" style="display:grid;grid-template-columns:1fr 280px;gap:24px;">

            <main>
                <?php if(!empty($articles_list)): ?>
                <div class="cdg-articles-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;">
                    <?php foreach($articles_list as $row):
                        $a_title = $row['short_title'] ?? ($row['title'] ?? 'Makale');
                        $a_content = $row['short_content'] ?? '';
                        $a_route = $row['route'] ?? '#';
                        $a_image = $row['image'] ?? '';
                        $a_cat_name = $row['category_name'] ?? '';
                        $a_cat_route = $row['category_route'] ?? '';
                        $a_date = $row['cdate'] ?? ($row['date'] ?? '');
                    ?>
                    <article class="cdg-article-card cdg-card" style="overflow:hidden;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
                        <a href="<?php echo htmlspecialchars($a_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:block;height:180px;overflow:hidden;background:linear-gradient(135deg,#2E3B4E,#00D3E5);position:relative;">
                            <?php if($a_image): ?>
                            <img src="<?php echo htmlspecialchars($a_image, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($a_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#fff;font-size:48px;opacity:0.3;">
                                <i class="bi bi-file-earmark-richtext"></i>
                            </div>
                            <?php endif; ?>
                        </a>
                        <div style="padding:18px;flex:1;display:flex;flex-direction:column;">
                            <?php if($a_cat_name): ?>
                            <a href="<?php echo htmlspecialchars($a_cat_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:inline-block;margin-bottom:8px;font-size:11px;font-weight:700;color:#2E3B4E;text-decoration:none;text-transform:uppercase;letter-spacing:0.5px;">
                                <i class="bi bi-folder"></i> <?php echo htmlspecialchars($a_cat_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </a>
                            <?php endif; ?>
                            <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 8px;line-height:1.4;">
                                <a href="<?php echo htmlspecialchars($a_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="color:inherit;text-decoration:none;">
                                    <?php echo htmlspecialchars($a_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                </a>
                            </h3>
                            <?php if($a_content): ?>
                            <p style="font-size:13px;color:#64748b;margin:0 0 14px;line-height:1.5;flex:1;"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($a_content), 0, 130, '...'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                            <?php endif; ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;border-top:1px solid #f1f5f9;">
                                <?php if($a_date): ?>
                                <span style="font-size:11px;color:#94a3b8;">
                                    <i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($a_date, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                </span>
                                <?php else: ?>
                                <span></span>
                                <?php endif; ?>
                                <a href="<?php echo htmlspecialchars($a_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="font-size:12px;font-weight:700;color:#2E3B4E;text-decoration:none;">
                                    Devamini oku <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <?php if($articles_pagination): ?>
                <div style="margin-top:24px;text-align:center;">
                    <?php
                        if(is_array($articles_pagination) && isset($articles_pagination['html'])) echo $articles_pagination['html'];
                        elseif(is_string($articles_pagination)) echo $articles_pagination;
                    ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="cdg-card" style="padding:48px 32px;text-align:center;">
                    <div style="width:72px;height:72px;border-radius:50%;background:#f1f5f9;color:#94a3b8;display:inline-grid;place-items:center;font-size:32px;margin-bottom:14px;">
                        <i class="bi bi-file-earmark-richtext"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">
                        Henuz makale eklenmemis
                    </h3>
                    <p style="font-size:13px;color:#64748b;margin:0 0 18px;">
                        Bu kategoride yayinlanmis makale bulunmuyor. Yakinda yeni icerikler eklenecektir.
                    </p>
                    <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" class="cdg-btn cdg-btn-primary">
                        <i class="bi bi-chat-dots"></i> İletişime Gec
                    </a>
                </div>
                <?php endif; ?>

                <?php if($articles_category && !empty($articles_category['content'])):
                    $cat_content_clean = '';
                    if(class_exists('Filter') && method_exists('Filter','html_clear')) {
                        try { $cat_content_clean = Filter::html_clear($articles_category['content']); } catch(\Throwable $e) { $cat_content_clean = strip_tags($articles_category['content']); }
                    }
                    if($cat_content_clean):
                ?>
                <div class="cdg-card" style="margin-top:24px;padding:24px;">
                    <h4 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 12px;"><i class="bi bi-info-circle"></i> Kategori Hakkında</h4>
                    <div style="font-size:14px;color:#475569;line-height:1.6;"><?php echo $articles_category['content']; ?></div>
                </div>
                <?php endif; endif; ?>
            </main>

            <aside class="cdg-articles-sidebar">
                <?php if(!empty($articles_categories)): ?>
                <div class="cdg-card" style="padding:20px;margin-bottom:16px;">
                    <h4 style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 14px;">
                        <i class="bi bi-folder2-open" style="color:#2E3B4E;"></i> Kategoriler
                    </h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:4px;">
                        <?php foreach($articles_categories as $cat):
                            $c_title = $cat['title'] ?? '';
                            $c_route = $cat['route'] ?? '#';
                            $c_count = $cat['article_count'] ?? ($cat['count'] ?? 0);
                            $is_active = $articles_category && isset($articles_category['id']) && isset($cat['id']) && $articles_category['id'] == $cat['id'];
                        ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($c_route, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;text-decoration:none;color:<?php echo $is_active ? '#2E3B4E' : '#334155'; ?>;background:<?php echo $is_active ? '#eff6ff' : 'transparent'; ?>;font-size:13px;font-weight:<?php echo $is_active ? '700' : '500'; ?>;border-radius:6px;">
                                <span><?php echo htmlspecialchars($c_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                                <?php if($c_count): ?>
                                <span style="font-size:11px;color:#94a3b8;background:<?php echo $is_active ? '#CFFAFE' : '#f1f5f9'; ?>;padding:2px 8px;border-radius:99px;">
                                    <?php echo (int)$c_count; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="cdg-card" style="padding:20px;background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;">
                    <i class="bi bi-life-preserver" style="font-size:32px;"></i>
                    <h4 style="font-size:14px;font-weight:800;margin:10px 0 6px;">Cevap aradığınız sorunuz mu var?</h4>
                    <p style="font-size:12px;opacity:0.9;margin:0 0 12px;">Destek ekibimiz size yardımcı olmak için hazır.</p>
                    <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('knowledgebase') : '/knowledgebase'); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;color:#2E3B4E;text-decoration:none;border-radius:6px;font-size:12px;font-weight:700;">
                        <i class="bi bi-book"></i> Bilgi Bankasi
                    </a>
                </div>
            </aside>

        </div>
    </div>
</section>

<style>
.cdg-article-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(15,23,42,0.10); }
@media (max-width: 900px) {
    .cdg-articles-grid { grid-template-columns: 1fr !important; }
}
</style>
