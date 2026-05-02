<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!isset($basket_link)) $basket_link = '#';
if(!isset($logout_link)) $logout_link = '#';

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // 1) Runtime $links[] kontrolü (WiseCP en güvenilir kaynak)
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }

        // 2) Kısa-isim -> WiseCP gerçek route alias map
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
            'ac-ps-create-ticket-request' => 'ac-ps-create-ticket-request',
            'tickets'                 => 'ac-ps-tickets',
            'my-tickets'              => 'ac-ps-tickets',
            'messages'                => 'ac-ps-messages',
            'detail-message'          => 'ac-ps-detail-message',
            'invoices'                => 'ac-ps-invoices',
            'detail-invoice'          => 'ac-ps-detail-invoice',
            'detail-invoice-pdf'      => 'ac-ps-detail-invoice',
            'balance'                 => 'ac-ps-balance',
            'balance-page'            => 'ac-ps-balance',
            'info'                    => 'ac-ps-info',
            'ac-info'                 => 'ac-ps-info',
            'products'                => 'ac-ps-products',
            'all-orders'              => 'ac-ps-products',
            'products-t'              => 'ac-ps-products-t',
            'product'                 => 'ac-ps-product',
            'sms'                     => 'ac-ps-sms',
            'domains'                 => 'ac-products-domain',
            'products-domain'         => 'ac-products-domain',
            'whois-profiles'          => 'ac-products-domain-whois-profiles',
            'products-domain-whois-profiles' => 'ac-products-domain-whois-profiles',
            'create-whois-profile'    => 'ac-products-domain-create-whois-profile',
            'products-domain-create-whois-profile' => 'ac-products-domain-create-whois-profile',
            'login'                   => 'sign-in',
            'register'                => 'sign-up',
            'logout'                  => 'sign-out',
            'account'                 => 'my-account',
            'homepage'                => '',
            'home'                    => '',
        ];
        $real_slug = isset($aliases[$slug]) ? $aliases[$slug] : $slug;

        // 3) CRLink dene (gerçek WiseCP routing)
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                // Bozuk URL kontrolü (boş ID parametresi vb.)
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) { /* fallback'e düş */ }
        }

        // 4) Son çare: APP_URI base + slug
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$user_name = '';
if(class_exists('User') && method_exists('User', 'logged_in') && User::logged_in()) {
    if(isset(User::$init->info)) {
        $info = User::$init->info;
        $user_name = isset($info['full_name']) && $info['full_name'] ? trim($info['full_name']) : trim((isset($info['name']) ? $info['name'] : '') . ' ' . (isset($info['surname']) ? $info['surname'] : ''));
    }
}
if(!$user_name) $user_name = 'Musteri';
?>
<?php
// Bildirim verilerini WiseCP runtime'dan al
$cdg_notifications = ['bubble_count' => 0, 'items' => []];
if(class_exists('User') && method_exists('User', 'getNotifications')) {
    try {
        $tmp = User::getNotifications();
        if(is_array($tmp)) $cdg_notifications = array_merge($cdg_notifications, $tmp);
    } catch(\Throwable $e) {}
}
$cdg_notif_count = (int)($cdg_notifications['bubble_count'] ?? 0);
$cdg_notif_items = isset($cdg_notifications['items']) && is_array($cdg_notifications['items']) ? $cdg_notifications['items'] : [];
$cdg_notif_url = isset($links['controller']) ? $links['controller'] : (isset($operation_link) ? $operation_link : '');
?>

<style>
.cdg-notif-wrap { position: relative; }
.cdg-notif-btn {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    color: #475569;
    cursor: pointer;
    display: grid; place-items: center;
    font-size: 16px;
    font-family: inherit;
    transition: all 0.15s;
    position: relative;
}
.cdg-notif-btn:hover { border-color: #1e40af; color: #1e40af; }
.cdg-notif-bubble {
    position: absolute;
    top: -4px; right: -4px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 99px;
    border: 2px solid #fff;
    min-width: 20px;
    line-height: 1.2;
    text-align: center;
}
.cdg-notif-dropdown {
    position: absolute;
    top: calc(100% + 8px); right: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(15,23,42,0.15);
    width: 380px;
    max-width: 92vw;
    z-index: 100;
    display: none;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}
.cdg-notif-dropdown.open { display: block; animation: cdgNotifSlide 0.2s ease; }
@keyframes cdgNotifSlide { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
.cdg-notif-head {
    padding: 14px 18px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-notif-head h4 { font-size: 14px; font-weight: 800; margin: 0; }
.cdg-notif-list { max-height: 380px; overflow-y: auto; }
.cdg-notif-item {
    display: flex; gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: background 0.15s;
}
.cdg-notif-item:hover { background: #f8fafc; }
.cdg-notif-item.unread { background: #eff6ff; }
.cdg-notif-item.unread:hover { background: #dbeafe; }
.cdg-notif-icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
    color: #1e40af;
    display: grid; place-items: center;
    flex-shrink: 0;
    font-size: 14px;
}
.cdg-notif-content { flex: 1; min-width: 0; }
.cdg-notif-text {
    font-size: 13px;
    color: #0f172a;
    line-height: 1.4;
    margin: 0 0 4px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.cdg-notif-date { font-size: 11px; color: #94a3b8; }
.cdg-notif-empty {
    text-align: center;
    padding: 32px 20px;
    color: #94a3b8;
    font-size: 13px;
}
.cdg-notif-empty i { font-size: 36px; display: block; margin-bottom: 8px; opacity: 0.5; }
.cdg-notif-foot {
    padding: 10px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    text-align: center;
}
.cdg-notif-foot button {
    background: none; border: 0;
    font-size: 12px; font-weight: 700;
    color: #1e40af;
    cursor: pointer;
    font-family: inherit;
    padding: 4px 10px;
    border-radius: 6px;
    transition: background 0.15s;
}
.cdg-notif-foot button:hover { background: #dbeafe; }
</style>

<div class="cdg-ac-topbar">
    <div>
        <h1><?php echo isset($page_title) ? $page_title : 'Hos geldiniz, ' . htmlspecialchars($user_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        <!-- Bildirim Bell -->
        <div class="cdg-notif-wrap">
            <button type="button" class="cdg-notif-btn" onclick="cdgNotifToggle(event)" title="Bildirimler">
                <i class="bi bi-bell"></i>
                <?php if($cdg_notif_count > 0): ?>
                <span class="cdg-notif-bubble" id="cdg-notif-bubble"><?php echo $cdg_notif_count > 99 ? '99+' : $cdg_notif_count; ?></span>
                <?php endif; ?>
            </button>
            <div class="cdg-notif-dropdown" id="cdg-notif-dropdown">
                <div class="cdg-notif-head">
                    <h4><i class="bi bi-bell-fill"></i> Bildirimler</h4>
                    <span style="font-size:11px;opacity:0.85;"><span id="cdg-notif-count-text"><?php echo $cdg_notif_count; ?></span> okunmamış</span>
                </div>
                <div class="cdg-notif-list" id="cdg-notif-list">
                    <?php if(!empty($cdg_notif_items)):
                        foreach($cdg_notif_items as $n):
                            $n_text = $n['content'] ?? ($n['text'] ?? ($n['message'] ?? ''));
                            $n_link = $n['link'] ?? ($n['url'] ?? '#');
                            $n_unread = !empty($n['unread']);
                            $n_date = $n['cdate'] ?? ($n['date'] ?? '');
                            if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config') && $n_date) {
                                try { $n_date = DateManager::format(Config::get("options/date-format") . " H:i", $n_date); } catch(\Throwable $e) {}
                            }
                            $n_icon = $n['icon'] ?? 'bi-info-circle';
                            if(strpos($n_icon, 'bi-') !== 0) $n_icon = 'bi-info-circle';
                    ?>
                    <a href="<?php echo htmlspecialchars($n_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-notif-item<?php echo $n_unread ? ' unread' : ''; ?>">
                        <div class="cdg-notif-icon"><i class="bi <?php echo htmlspecialchars($n_icon, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i></div>
                        <div class="cdg-notif-content">
                            <p class="cdg-notif-text"><?php echo htmlspecialchars(strip_tags($n_text), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></p>
                            <span class="cdg-notif-date"><?php echo htmlspecialchars($n_date, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                        </div>
                    </a>
                    <?php endforeach; else: ?>
                    <div class="cdg-notif-empty">
                        <i class="bi bi-bell-slash"></i>
                        <div>Bildirim yok</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if(!empty($cdg_notif_items) && $cdg_notif_count > 0): ?>
                <div class="cdg-notif-foot">
                    <button type="button" onclick="cdgNotifReadAll(this)">
                        <i class="bi bi-check2-all"></i> Tümünü Okundu İşaretle
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <a href="<?php echo (isset($basket_link) && $basket_link && $basket_link != '#') ? $basket_link : cdg_link('basket'); ?>" class="cdg-btn cdg-btn-outline cdg-btn-sm" title="Sepetim">
            <i class="bi bi-cart"></i>
        </a>
        <a href="<?php echo cdg_link('products', ['hosting']); ?>" class="cdg-btn cdg-btn-primary cdg-btn-sm">
            <i class="bi bi-plus-lg"></i> Yeni Siparis
        </a>
        <div style="width:38px;height:38px;border-radius:50%;background:var(--cdg-gradient);color:white;display:grid;place-items:center;font-weight:700;font-size:14px;">
            <?php echo mb_strtoupper(mb_substr($user_name, 0, 1, 'UTF-8'), 'UTF-8'); ?>
        </div>
    </div>
</div>

<script>
(function(){
    var cdgNotifUrl = '<?php echo htmlspecialchars($cdg_notif_url, ENT_QUOTES); ?>';

    window.cdgNotifToggle = function(e) {
        if(e) { e.stopPropagation(); }
        var dd = document.getElementById('cdg-notif-dropdown');
        if(dd) dd.classList.toggle('open');
    };

    window.cdgNotifReadAll = function(btn) {
        if(typeof MioAjax !== 'function') return;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> İşleniyor...';

        MioAjax({
            url: cdgNotifUrl, type: 'post',
            data: { operation: 'read_all_notifications' },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    document.querySelectorAll('.cdg-notif-item.unread').forEach(function(el){ el.classList.remove('unread'); });
                    var bubble = document.getElementById('cdg-notif-bubble');
                    if(bubble) bubble.remove();
                    var ct = document.getElementById('cdg-notif-count-text');
                    if(ct) ct.textContent = '0';
                    btn.parentNode.style.display = 'none';
                    if(typeof alert_success === 'function') alert_success(r.message || 'Tüm bildirimler okundu işaretlendi', {timer: 1500});
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    };

    // Dışarı tıklamayla kapat
    document.addEventListener('click', function(e) {
        var wrap = document.querySelector('.cdg-notif-wrap');
        if(!wrap) return;
        var dd = document.getElementById('cdg-notif-dropdown');
        if(!dd || !dd.classList.contains('open')) return;
        if(!wrap.contains(e.target)) dd.classList.remove('open');
    });
})();
</script>
