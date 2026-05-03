<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Header Breadcrumb (Sayfa Yolu)
 * Site genelinde header üstünde gözükür
 * WiseCP runtime: $breadcrumb (array of [title, link])
 */

if(!isset($breadcrumb) || !is_array($breadcrumb) || empty($breadcrumb)) return;

$home_url = '/';
if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
    $home_url = Controllers::$init->CRLink('homepage');
} elseif(defined('APP_URI')) {
    $home_url = APP_URI;
}
?>

<nav class="cdg-bc" aria-label="Sayfa yolu">
    <ol class="cdg-bc-list">
        <li class="cdg-bc-item">
            <a href="<?php echo htmlspecialchars($home_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-bc-home">
                <i class="bi bi-house-door"></i>
                <span class="cdg-bc-home-text">Ana Sayfa</span>
            </a>
        </li>
        <?php foreach($breadcrumb as $i => $crumb):
            if(!is_array($crumb)) continue;
            $title = $crumb['title'] ?? '';
            $link  = $crumb['link'] ?? '';
            if(!$title) continue;
            $is_last = ($i === count($breadcrumb) - 1);
        ?>
        <li class="cdg-bc-sep" aria-hidden="true"><i class="bi bi-chevron-right"></i></li>
        <li class="cdg-bc-item<?php echo $is_last ? ' cdg-bc-current' : ''; ?>">
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
.cdg-bc {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    padding: 8px 0;
    box-sizing: border-box;
}
.cdg-bc *, .cdg-bc *::before, .cdg-bc *::after { box-sizing: border-box; }
.cdg-bc-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex; align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: 12px;
}
.cdg-bc-item {
    display: inline-flex; align-items: center;
}
.cdg-bc-item a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 6px;
    border-radius: 5px;
}
.cdg-bc-item a:hover { color: #2E3B4E; background: rgba(46,59,78,0.06); }
.cdg-bc-item.cdg-bc-current span {
    color: #0f172a;
    font-weight: 700;
    padding: 3px 6px;
}
.cdg-bc-home i { font-size: 14px; }
.cdg-bc-sep {
    color: #cbd5e1;
    font-size: 10px;
    display: inline-flex; align-items: center;
}
@media (max-width: 480px) {
    .cdg-bc-home-text { display: none; }
}
</style>
