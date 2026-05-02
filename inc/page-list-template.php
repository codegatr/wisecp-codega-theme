<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Generic Liste Sablonu (news, references icin ortak)
 *
 * Caller variables:
 *  - $cdg_list_kind, $cdg_list_label, $cdg_list_icon, $cdg_list_color
 *
 * WiseCP runtime: $list, $category, $pagination, $categories
 */

$plist = isset($list) && is_array($list) ? $list : [];
$pcat = isset($category) && is_array($category) ? $category : null;
$ppag = isset($pagination) ? $pagination : '';
$pcats = isset($categories) && is_array($categories) ? $categories : [];

$plabel = $cdg_list_label ?? 'Sayfa';
$picon = $cdg_list_icon ?? 'file-earmark';
$pcolor = $cdg_list_color ?? '#1e40af';
$pgrad = $cdg_list_gradient ?? "linear-gradient(135deg,$pcolor,#3b82f6)";
$page_title = $pcat['title'] ?? $plabel;
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><i class="bi bi-<?php echo htmlspecialchars($picon); ?>"></i> <?php echo htmlspecialchars($page_title); ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>">Anasayfa</a>
            <span class="sep">/</span>
            <span><?php echo htmlspecialchars($page_title); ?></span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-list-grid" style="display:grid;grid-template-columns:1fr 280px;gap:24px;">

            <main>
                <?php if(!empty($plist)): ?>
                <div class="cdg-list-items" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;">
                    <?php foreach($plist as $row):
                        $a_title = $row['short_title'] ?? ($row['title'] ?? 'Baslik yok');
                        $a_content = $row['short_content'] ?? '';
                        $a_route = $row['route'] ?? '#';
                        $a_image = $row['image'] ?? '';
                        $a_cat_name = $row['category_name'] ?? '';
                        $a_date = $row['cdate'] ?? ($row['date'] ?? '');
                    ?>
                    <article class="cdg-list-card cdg-card" style="overflow:hidden;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
                        <a href="<?php echo htmlspecialchars($a_route); ?>" style="display:block;height:180px;overflow:hidden;background:<?php echo htmlspecialchars($pgrad); ?>;position:relative;">
                            <?php if($a_image): ?>
                            <img src="<?php echo htmlspecialchars($a_image); ?>" alt="<?php echo htmlspecialchars($a_title); ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#fff;font-size:48px;opacity:0.3;">
                                <i class="bi bi-<?php echo htmlspecialchars($picon); ?>"></i>
                            </div>
                            <?php endif; ?>
                        </a>
                        <div style="padding:18px;flex:1;display:flex;flex-direction:column;">
                            <?php if($a_cat_name): ?>
                            <span style="display:inline-block;margin-bottom:8px;font-size:11px;font-weight:700;color:<?php echo htmlspecialchars($pcolor); ?>;text-transform:uppercase;letter-spacing:0.5px;">
                                <i class="bi bi-folder"></i> <?php echo htmlspecialchars($a_cat_name); ?>
                            </span>
                            <?php endif; ?>
                            <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 8px;line-height:1.4;">
                                <a href="<?php echo htmlspecialchars($a_route); ?>" style="color:inherit;text-decoration:none;">
                                    <?php echo htmlspecialchars($a_title); ?>
                                </a>
                            </h3>
                            <?php if($a_content): ?>
                            <p style="font-size:13px;color:#64748b;margin:0 0 14px;line-height:1.5;flex:1;"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($a_content), 0, 130, '...')); ?></p>
                            <?php endif; ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;border-top:1px solid #f1f5f9;">
                                <?php if($a_date): ?>
                                <span style="font-size:11px;color:#94a3b8;">
                                    <i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($a_date); ?>
                                </span>
                                <?php else: ?>
                                <span></span>
                                <?php endif; ?>
                                <a href="<?php echo htmlspecialchars($a_route); ?>" style="font-size:12px;font-weight:700;color:<?php echo htmlspecialchars($pcolor); ?>;text-decoration:none;">
                                    Devamini oku <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <?php if($ppag): ?>
                <div style="margin-top:24px;text-align:center;">
                    <?php
                        if(is_array($ppag) && isset($ppag['html'])) echo $ppag['html'];
                        elseif(is_string($ppag)) echo $ppag;
                    ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="cdg-card" style="padding:48px 32px;text-align:center;">
                    <div style="width:72px;height:72px;border-radius:50%;background:#f1f5f9;color:#94a3b8;display:inline-grid;place-items:center;font-size:32px;margin-bottom:14px;">
                        <i class="bi bi-<?php echo htmlspecialchars($picon); ?>"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">Henuz icerik eklenmemis</h3>
                    <p style="font-size:13px;color:#64748b;margin:0 0 18px;">Yakinda yeni icerikler eklenecektir.</p>
                </div>
                <?php endif; ?>
            </main>

            <aside class="cdg-list-sidebar">
                <?php if(!empty($pcats)): ?>
                <div class="cdg-card" style="padding:20px;margin-bottom:16px;">
                    <h4 style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 14px;">
                        <i class="bi bi-folder2-open" style="color:<?php echo htmlspecialchars($pcolor); ?>;"></i> Kategoriler
                    </h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:4px;">
                        <?php foreach($pcats as $cat):
                            $c_title = $cat['title'] ?? '';
                            $c_route = $cat['route'] ?? '#';
                            $c_count = $cat['article_count'] ?? ($cat['count'] ?? 0);
                            $is_active = $pcat && isset($pcat['id']) && isset($cat['id']) && $pcat['id'] == $cat['id'];
                        ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($c_route); ?>" style="display:flex;justify-content:space-between;padding:8px 12px;text-decoration:none;color:<?php echo $is_active ? htmlspecialchars($pcolor) : '#334155'; ?>;background:<?php echo $is_active ? '#f1f5f9' : 'transparent'; ?>;font-size:13px;font-weight:<?php echo $is_active ? '700' : '500'; ?>;border-radius:6px;">
                                <span><?php echo htmlspecialchars($c_title); ?></span>
                                <?php if($c_count): ?>
                                <span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:99px;color:#94a3b8;"><?php echo (int)$c_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="cdg-card" style="padding:20px;background:<?php echo htmlspecialchars($pgrad); ?>;color:#fff;">
                    <i class="bi bi-chat-dots" style="font-size:28px;"></i>
                    <h4 style="font-size:14px;font-weight:800;margin:8px 0 6px;">Sorunuz mu var?</h4>
                    <p style="font-size:12px;opacity:0.9;margin:0 0 12px;">Iletisime gecin, hizmetlerimiz hakkinda bilgi alin.</p>
                    <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;color:<?php echo htmlspecialchars($pcolor); ?>;text-decoration:none;border-radius:6px;font-size:12px;font-weight:700;">
                        <i class="bi bi-envelope"></i> Bize Yazin
                    </a>
                </div>
            </aside>

        </div>
    </div>
</section>

<style>
.cdg-list-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(15,23,42,0.10); }
@media (max-width: 900px) {
    .cdg-list-grid { grid-template-columns: 1fr !important; }
}
</style>
