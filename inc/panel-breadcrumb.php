<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Müşteri Paneli Breadcrumb
 * WiseCP runtime: $panel_breadcrumb (array of [title, link])
 */

if(!isset($panel_breadcrumb) || !is_array($panel_breadcrumb) || empty($panel_breadcrumb)) return;

$dashboard_url = '#';
if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
    $dashboard_url = Controllers::$init->CRLink('account');
}
?>

<nav class="cdg-pbc" aria-label="Panel sayfa yolu">
    <ol class="cdg-pbc-list">
        <li class="cdg-pbc-item">
            <a href="<?php echo htmlspecialchars($dashboard_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-pbc-home">
                <i class="bi bi-grid-fill"></i>
                <span class="cdg-pbc-home-text">Panel</span>
            </a>
        </li>
        <?php foreach($panel_breadcrumb as $i => $crumb):
            if(!is_array($crumb)) continue;
            $title = $crumb['title'] ?? '';
            $link  = $crumb['link'] ?? '';
            if(!$title) continue;
            $is_last = ($i === count($panel_breadcrumb) - 1);
        ?>
        <li class="cdg-pbc-sep" aria-hidden="true"><i class="bi bi-chevron-right"></i></li>
        <li class="cdg-pbc-item<?php echo $is_last ? ' cdg-pbc-current' : ''; ?>">
            <?php if($link && !$is_last): ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></a>
            <?php else: ?>
            <span aria-current="page"><?php echo htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ol>
</nav>

<style>
.cdg-pbc {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin-bottom: 14px;
    box-sizing: border-box;
}
.cdg-pbc *, .cdg-pbc *::before, .cdg-pbc *::after { box-sizing: border-box; }
.cdg-pbc-list {
    list-style: none;
    margin: 0; padding: 0;
    display: flex; align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: 12px;
}
.cdg-pbc-item {
    display: inline-flex; align-items: center;
}
.cdg-pbc-item a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 600;
}
.cdg-pbc-item a:hover { color: #1e40af; background: rgba(30,64,175,0.06); }
.cdg-pbc-item.cdg-pbc-current span {
    color: #1e40af;
    font-weight: 800;
    padding: 3px 8px;
    background: rgba(30,64,175,0.08);
    border-radius: 6px;
}
.cdg-pbc-home i { font-size: 13px; color: #1e40af; }
.cdg-pbc-sep {
    color: #cbd5e1;
    font-size: 10px;
}
@media (max-width: 480px) {
    .cdg-pbc-home-text { display: none; }
}
</style>
