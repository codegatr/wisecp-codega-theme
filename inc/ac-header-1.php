<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!isset($basket_link)) $basket_link = '#';
if(!isset($logout_link)) $logout_link = '#';

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$user_name = '';
if(class_exists('User') && method_exists('User', 'logged_in') && User::logged_in()) {
    if(isset(User::$init->info)) {
        $info = User::$init->info;
        $user_name = trim((isset($info['name']) ? $info['name'] : '') . ' ' . (isset($info['surname']) ? $info['surname'] : ''));
    }
}
if(!$user_name) $user_name = 'Musteri';
?>
<div class="cdg-ac-topbar">
    <div>
        <h1><?php echo isset($page_title) ? $page_title : 'Hos geldiniz, ' . htmlspecialchars($user_name); ?></h1>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?php echo (isset($basket_link) && $basket_link && $basket_link != '#') ? $basket_link : cdg_link('basket'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm" title="Sepetim">
            <i class="bi bi-cart"></i>
        </a>
        <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">
            <i class="bi bi-plus-lg"></i> Yeni Siparis
        </a>
        <div style="width:38px;height:38px;border-radius:50%;background:var(--cdg-gradient);color:white;display:grid;place-items:center;font-weight:700;font-size:14px;">
            <?php echo strtoupper(mb_substr($user_name, 0, 1)); ?>
        </div>
    </div>
</div>
