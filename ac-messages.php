<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Mesajlar / Bildirimler Listesi
 * WiseCP runtime: $messages, $links (ajax)
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        static $aliases = [
            'create-ticket-request'   => 'ac-ps-create-ticket-request',
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
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                if($url && strpos($url, '/(0)') === false && !preg_match('#/0/?$#', $url)) {
                    return $url;
                }
            } catch(\Throwable $e) {}
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$messages = isset($messages) && is_array($messages) ? $messages : [];
$links    = isset($links) && is_array($links) ? $links : [];
$ajax_url = $links['ajax'] ?? '';

function cdg_msglist_date($date) {
    if(!$date) return '';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        $fmt = Config::get("options/date-format") ?: 'd.m.Y';
        return DateManager::format($fmt . ' H:i', $date);
    }
    return date('d.m.Y H:i', is_numeric($date) ? (int)$date : strtotime((string)$date));
}
?>

<style>
.cdg-msl {
    --ml-primary: #1e40af;
    --ml-bg: #f8fafc;
    --ml-card: #fff;
    --ml-text: #0f172a;
    --ml-muted: #64748b;
    --ml-border: #e2e8f0;
    --ml-radius: 14px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--ml-text);
    background: var(--ml-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-msl *, .cdg-msl *::before, .cdg-msl *::after { box-sizing: border-box; }
.cdg-msl a { text-decoration: none; color: inherit; }
.cdg-msl-wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

.cdg-msl-hero {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 22px;
    display: flex; align-items: center; gap: 18px;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
}
.cdg-msl-hero-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 26px; flex-shrink: 0;
}
.cdg-msl-hero h1 { font-size: 24px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-msl-hero p { font-size: 13px; opacity: 0.88; margin: 0; }

.cdg-msl-list {
    display: flex; flex-direction: column; gap: 10px;
}
.cdg-msl-item {
    background: var(--ml-card);
    border: 1px solid var(--ml-border);
    border-radius: var(--ml-radius);
    padding: 16px 22px;
    display: grid;
    grid-template-columns: auto 1fr auto auto;
    gap: 16px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
    transition: all 0.18s;
}
.cdg-msl-item:hover {
    box-shadow: 0 8px 24px rgba(15,23,42,0.08);
    transform: translateY(-1px);
    border-color: var(--ml-primary);
}
.cdg-msl-item.unread { border-left: 4px solid var(--ml-primary); background: linear-gradient(90deg, #eff6ff, #fff 30%); }

.cdg-msl-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: grid; place-items: center;
    font-size: 19px; flex-shrink: 0;
}
.cdg-msl-item.unread .cdg-msl-icon {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

.cdg-msl-body { min-width: 0; }
.cdg-msl-subject {
    font-size: 14px;
    font-weight: 700;
    color: var(--ml-text);
    margin-bottom: 3px;
    word-break: break-word;
}
.cdg-msl-meta {
    font-size: 11px;
    color: var(--ml-muted);
    display: inline-flex; align-items: center; gap: 5px;
}
.cdg-msl-meta i { color: var(--ml-primary); }

.cdg-msl-date {
    font-size: 12px;
    color: var(--ml-muted);
    white-space: nowrap;
    font-weight: 600;
}
.cdg-msl-action {
    color: var(--ml-primary);
    font-size: 14px;
    flex-shrink: 0;
}

.cdg-msl-empty {
    text-align: center;
    padding: 60px 20px;
    background: var(--ml-card);
    border: 2px dashed var(--ml-border);
    border-radius: var(--ml-radius);
}
.cdg-msl-empty i {
    font-size: 56px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 12px;
}
.cdg-msl-empty h3 {
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 6px;
}
.cdg-msl-empty p {
    font-size: 13px;
    color: var(--ml-muted);
    margin: 0;
}

@media (max-width: 600px) {
    .cdg-msl-item { grid-template-columns: auto 1fr; gap: 12px; }
    .cdg-msl-date { grid-column: 2 / 3; font-size: 11px; }
    .cdg-msl-action { display: none; }
    .cdg-msl-hero { padding: 22px 20px; flex-direction: column; text-align: center; }
}
</style>

<div class="cdg-msl">
<div class="cdg-msl-wrap">

    <section class="cdg-msl-hero">
        <div class="cdg-msl-hero-icon"><i class="bi bi-envelope-fill"></i></div>
        <div>
            <h1>Mesajlarım</h1>
            <p>Sistemden gelen bildirimleriniz, duyurular ve önemli mesajlar burada listelenir.</p>
        </div>
    </section>

    <?php if(empty($messages)): ?>
    <div class="cdg-msl-empty">
        <i class="bi bi-inbox"></i>
        <h3>Mesajınız Yok</h3>
        <p>Sistem mesajları ve bildirimler burada görüntülenecek.</p>
    </div>
    <?php else: ?>
    <div class="cdg-msl-list">
        <?php foreach($messages as $msg):
            if(!is_array($msg)) continue;
            $m_id      = $msg['id'] ?? 0;
            $m_subject = $msg['subject'] ?? $msg['title'] ?? 'Mesaj';
            $m_date    = $msg['date'] ?? $msg['cdate'] ?? '';
            $m_unread  = !empty($msg['unread']) || (isset($msg['status']) && $msg['status'] === 'unread');
            $m_link    = $msg['detail_link'] ?? cdg_link('detail-message', [(int)$m_id]);
            $m_from    = $msg['from'] ?? $msg['sender'] ?? 'Sistem';
        ?>
        <a href="<?php echo htmlspecialchars($m_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-msl-item <?php echo $m_unread ? 'unread' : ''; ?>">
            <div class="cdg-msl-icon">
                <i class="bi bi-<?php echo $m_unread ? 'envelope' : 'envelope-open'; ?>"></i>
            </div>
            <div class="cdg-msl-body">
                <div class="cdg-msl-subject"><?php echo htmlspecialchars($m_subject, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <div class="cdg-msl-meta">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($m_from, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </div>
            </div>
            <div class="cdg-msl-date"><?php echo htmlspecialchars(cdg_msglist_date($m_date), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            <div class="cdg-msl-action">
                <i class="bi bi-chevron-right"></i>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</div>
