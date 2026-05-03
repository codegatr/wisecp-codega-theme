<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Destek Talebi Detayı + Yanıt
 * WiseCP runtime: $ticket, $replies, $department, $priorities, $situations, $statuses, $custom, $links
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        // NOT: $links global'i bazen yanlis URL doner ($links['products']=/products-hosting gibi)
        global $links;

        // CDG_LINK_HARDCODED - Yunus'un sitesinde KESIN dogru URL'ler (CRLink bypass)
        static $hardcoded = [
            'ac-ps-create-ticket-request' => '/hesabim/destek-talebi-olustur',
            'create-ticket-request'       => '/hesabim/destek-talebi-olustur',
            'create-ticket'               => '/hesabim/destek-talebi-olustur',
        ];
        if(isset($hardcoded[$slug])) {
            $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
            return $base . $hardcoded[$slug];
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
            'affiliate'               => 'ac-affiliate',
            'ac-affiliate'            => 'ac-affiliate',
            'reseller'                => 'ac-reseller',
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
        // Son care: $links bakilirsa kullan
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) {
            return $links[$slug];
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        if(!$real_slug) return $base ?: '/';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

// === Defansif defaults ===
$ticket     = isset($ticket) && is_array($ticket) ? $ticket : [];
$replies    = isset($replies) && is_array($replies) ? $replies : [];
$department = isset($department) ? $department : [];
$priorities = isset($priorities) && is_array($priorities) ? $priorities : [
    1 => 'Düşük', 2 => 'Orta', 3 => 'Yüksek'
];
$situations = isset($situations) && is_array($situations) ? $situations : [];
$statuses   = isset($statuses) && is_array($statuses) ? $statuses : [];
$custom     = isset($custom) && is_array($custom) ? $custom : [];
$links      = isset($links) && is_array($links) ? $links : [];

$controller_url = $links['controller'] ?? '';
$tickets_url    = cdg_link('tickets');

$t_id       = $ticket['id'] ?? 0;
$t_title    = $ticket['title'] ?? 'Destek Talebi';
$t_status   = $ticket['status'] ?? 'unknown';
$t_priority = $ticket['priority'] ?? 2;
$t_locked   = !empty($ticket['locked']);
$t_lastreply = $ticket['lastreply'] ?? '';

// Status meta
function cdg_t_status_meta($status) {
    $map = [
        'open'           => ['cls' => 'cdg-tk-badge-info',    'lbl' => 'Açık',          'icon' => 'envelope-open'],
        'answered'       => ['cls' => 'cdg-tk-badge-success', 'lbl' => 'Yanıtlandı',    'icon' => 'reply-fill'],
        'customer-reply' => ['cls' => 'cdg-tk-badge-warning', 'lbl' => 'Yanıt Bekliyor','icon' => 'hourglass-split'],
        'inprocess'      => ['cls' => 'cdg-tk-badge-warning', 'lbl' => 'İşlemde',       'icon' => 'gear-fill'],
        'solved'         => ['cls' => 'cdg-tk-badge-success', 'lbl' => 'Çözüldü',       'icon' => 'check-circle-fill'],
        'closed'         => ['cls' => 'cdg-tk-badge-info',    'lbl' => 'Kapalı',        'icon' => 'lock-fill'],
    ];
    $key = strtolower($status);
    return $map[$key] ?? ['cls' => 'cdg-tk-badge-info', 'lbl' => ucfirst($status), 'icon' => 'chat-dots'];
}
$st_meta = cdg_t_status_meta($t_status);

// Priority meta
function cdg_t_priority_meta($pri) {
    $map = [
        1 => ['cls' => 'cdg-tk-pri-low',  'lbl' => 'Düşük',  'icon' => 'arrow-down-circle-fill', 'color' => '#10b981'],
        2 => ['cls' => 'cdg-tk-pri-mid',  'lbl' => 'Orta',   'icon' => 'dash-circle-fill',       'color' => '#f59e0b'],
        3 => ['cls' => 'cdg-tk-pri-high', 'lbl' => 'Yüksek', 'icon' => 'exclamation-circle-fill','color' => '#ef4444'],
    ];
    return $map[(int)$pri] ?? $map[2];
}
$pri_meta = cdg_t_priority_meta($t_priority);

// Tarih formatla
function cdg_t_date($date) {
    if(!$date) return '';
    if(class_exists('UserManager') && method_exists('UserManager','formatTimeZone') && class_exists('Config')) {
        try {
            $zone = (class_exists('User') && isset(User::$init->info['timezone'])) ? User::$init->info['timezone'] : 'Europe/Istanbul';
            return UserManager::formatTimeZone($date, $zone, Config::get("options/date-format") . " H:i");
        } catch(\Throwable $e) {}
    }
    return date('d.m.Y H:i', is_numeric($date) ? (int)$date : strtotime($date));
}
?>

<style>
.cdg-td {
    --td-primary: #1e40af;
    --td-success: #10b981;
    --td-warning: #f59e0b;
    --td-danger: #ef4444;
    --td-info: #06b6d4;
    --td-purple: #8b5cf6;
    --td-bg: #f8fafc;
    --td-card: #fff;
    --td-text: #0f172a;
    --td-muted: #64748b;
    --td-border: #e2e8f0;
    --td-radius: 14px;
    --td-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --td-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--td-text);
    background: var(--td-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-td *, .cdg-td *::before, .cdg-td *::after { box-sizing: border-box; }
.cdg-td a { text-decoration: none; color: inherit; }

.cdg-td-wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

/* TOP BAR */
.cdg-td-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--td-border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--td-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-td-back:hover { border-color: var(--td-primary); color: var(--td-primary); }

/* HERO */
.cdg-td-hero {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #3b82f6 100%);
    border-radius: 18px;
    padding: 26px 30px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(99,102,241,0.20);
}
.cdg-td-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,0.16), transparent 70%);
    pointer-events: none;
}
.cdg-td-hero-row {
    display: flex; align-items: center; gap: 18px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.cdg-td-hero-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 24px;
    flex-shrink: 0;
}
.cdg-td-hero-text { flex: 1; min-width: 240px; }
.cdg-td-hero-num {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 2px;
}
.cdg-td-hero-text h1 {
    font-size: 22px; font-weight: 800;
    margin: 0;
    letter-spacing: -0.3px;
    word-break: break-word;
}
.cdg-td-hero-status {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    background: rgba(255,255,255,0.22);
    backdrop-filter: blur(10px);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    flex-shrink: 0;
}

/* META BAR */
.cdg-td-meta {
    background: #fff;
    border: 1px solid var(--td-border);
    border-radius: var(--td-radius);
    padding: 16px 22px;
    box-shadow: var(--td-shadow);
    margin-bottom: 18px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}
.cdg-td-meta-item { display: flex; flex-direction: column; gap: 4px; }
.cdg-td-meta-label {
    font-size: 11px;
    color: var(--td-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.cdg-td-meta-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--td-text);
    display: inline-flex; align-items: center; gap: 6px;
}

/* MESSAGES THREAD */
.cdg-td-thread {
    display: flex; flex-direction: column; gap: 14px;
    margin-bottom: 18px;
}
.cdg-td-msg {
    background: #fff;
    border: 1px solid var(--td-border);
    border-radius: var(--td-radius);
    box-shadow: var(--td-shadow);
    overflow: hidden;
}
.cdg-td-msg.from-staff {
    border-left: 4px solid var(--td-primary);
    background: linear-gradient(135deg, #eff6ff 0%, #fff 50%);
}
.cdg-td-msg.from-customer {
    border-left: 4px solid var(--td-success);
}
.cdg-td-msg-head {
    padding: 12px 20px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid var(--td-border);
    background: rgba(248,250,252,0.5);
    flex-wrap: wrap;
    gap: 8px;
}
.cdg-td-msg-author {
    display: flex; align-items: center; gap: 10px;
}
.cdg-td-msg-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: grid; place-items: center;
    font-size: 14px; font-weight: 800;
    color: #fff;
    flex-shrink: 0;
}
.cdg-td-msg.from-staff .cdg-td-msg-avatar {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
}
.cdg-td-msg.from-customer .cdg-td-msg-avatar {
    background: linear-gradient(135deg, #10b981, #34d399);
}
.cdg-td-msg-info { display: flex; flex-direction: column; gap: 2px; }
.cdg-td-msg-name {
    font-size: 13px;
    font-weight: 700;
    color: var(--td-text);
}
.cdg-td-msg-role {
    font-size: 11px;
    color: var(--td-muted);
    font-weight: 600;
    display: inline-flex; align-items: center; gap: 4px;
}
.cdg-td-msg-time {
    font-size: 12px;
    color: var(--td-muted);
    font-weight: 500;
}
.cdg-td-msg-body {
    padding: 18px 22px;
    font-size: 14px;
    line-height: 1.7;
    color: var(--td-text);
    word-wrap: break-word;
}
.cdg-td-msg-body p { margin: 0 0 10px; }
.cdg-td-msg-body p:last-child { margin-bottom: 0; }

.cdg-td-msg-attach {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 12px;
    padding: 8px 14px;
    background: #f1f5f9;
    border: 1px solid var(--td-border);
    border-radius: 8px;
    font-size: 12px;
    color: var(--td-text);
    text-decoration: none;
    transition: all 0.18s;
}
.cdg-td-msg-attach:hover { border-color: var(--td-primary); color: var(--td-primary); }
.cdg-td-msg-attach i { color: var(--td-primary); }

/* REPLY FORM */
.cdg-td-reply {
    background: #fff;
    border: 1px solid var(--td-border);
    border-radius: var(--td-radius);
    box-shadow: var(--td-shadow);
    overflow: hidden;
}
.cdg-td-reply-head {
    padding: 14px 22px;
    border-bottom: 1px solid var(--td-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
}
.cdg-td-reply-head h3 {
    font-size: 14px;
    font-weight: 800;
    margin: 0;
    color: var(--td-text);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-td-reply-head h3 i { color: var(--td-primary); }
.cdg-td-reply-body { padding: 20px 22px; }
.cdg-td-textarea {
    width: 100%;
    min-height: 140px;
    padding: 14px;
    border: 1.5px solid var(--td-border);
    border-radius: 10px;
    font-size: 14px;
    line-height: 1.6;
    color: var(--td-text);
    background: #fff;
    outline: none;
    resize: vertical;
    font-family: inherit;
    transition: all 0.18s;
}
.cdg-td-textarea:focus {
    border-color: var(--td-primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,0.10);
}
.cdg-td-reply-actions {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 14px;
    flex-wrap: wrap; gap: 10px;
}
.cdg-td-check {
    display: inline-flex; align-items: center; gap: 8px;
    cursor: pointer;
    font-size: 12px;
    color: var(--td-muted);
}
.cdg-td-check input { transform: scale(1.1); accent-color: var(--td-primary); margin: 0; }
.cdg-td-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    border: 0;
    transition: all 0.2s;
    font-family: inherit;
    text-decoration: none;
}
.cdg-td-btn-send {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    box-shadow: 0 6px 18px rgba(30,64,175,0.25);
}
.cdg-td-btn-send:hover { transform: translateY(-1px); color: #fff; }
.cdg-td-btn-close {
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: #fff;
    box-shadow: 0 6px 18px rgba(239,68,68,0.20);
}
.cdg-td-btn-close:hover { transform: translateY(-1px); color: #fff; }
.cdg-td-btn-reopen {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.20);
}
.cdg-td-btn-reopen:hover { transform: translateY(-1px); color: #fff; }

/* LOCKED NOTICE */
.cdg-td-locked {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1px solid #fcd34d;
    border-radius: var(--td-radius);
    padding: 18px 22px;
    display: flex; align-items: center; gap: 14px;
    color: #78350f;
}
.cdg-td-locked i { font-size: 24px; flex-shrink: 0; }
.cdg-td-locked h4 { font-size: 14px; font-weight: 800; margin: 0 0 2px; }
.cdg-td-locked p { font-size: 12px; margin: 0; opacity: 0.9; }

/* BADGES */
.cdg-tk-pri-low  { color: #10b981; }
.cdg-tk-pri-mid  { color: #f59e0b; }
.cdg-tk-pri-high { color: #ef4444; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .cdg-td-hero-row { flex-direction: column; text-align: center; align-items: center; }
    .cdg-td-meta { grid-template-columns: 1fr 1fr; }
    .cdg-td-msg-head { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
    .cdg-td-meta { grid-template-columns: 1fr; }
}
</style>

<div class="cdg-td">
<div class="cdg-td-wrap">

    <!-- BACK -->
    <a href="<?php echo htmlspecialchars($tickets_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-td-back">
        <i class="bi bi-arrow-left"></i> Taleplerime Dön
    </a>

    <!-- HERO -->
    <section class="cdg-td-hero">
        <div class="cdg-td-hero-row">
            <div class="cdg-td-hero-icon"><i class="bi bi-headset"></i></div>
            <div class="cdg-td-hero-text">
                <div class="cdg-td-hero-num">TALEP #<?php echo htmlspecialchars($t_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <h1><?php echo htmlspecialchars($t_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
            </div>
            <div class="cdg-td-hero-status">
                <i class="bi bi-<?php echo htmlspecialchars($st_meta['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"></i>
                <?php echo htmlspecialchars($st_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </div>
        </div>
    </section>

    <!-- META BAR -->
    <div class="cdg-td-meta">
        <div class="cdg-td-meta-item">
            <span class="cdg-td-meta-label">Departman</span>
            <span class="cdg-td-meta-value">
                <i class="bi bi-people-fill" style="color:var(--td-primary);"></i>
                <?php echo htmlspecialchars(is_array($department) ? ($department['name'] ?? 'Genel') : 'Genel', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </span>
        </div>
        <div class="cdg-td-meta-item">
            <span class="cdg-td-meta-label">Öncelik</span>
            <span class="cdg-td-meta-value">
                <i class="bi bi-<?php echo htmlspecialchars($pri_meta['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="color:<?php echo $pri_meta['color']; ?>;"></i>
                <?php echo htmlspecialchars($pri_meta['lbl'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </span>
        </div>
        <div class="cdg-td-meta-item">
            <span class="cdg-td-meta-label">Son Yanıt</span>
            <span class="cdg-td-meta-value" id="get_lastreply">
                <i class="bi bi-clock-history" style="color:var(--td-muted);"></i>
                <?php echo $t_lastreply ? htmlspecialchars(cdg_t_date($t_lastreply), ENT_QUOTES | ENT_HTML5, 'UTF-8') : '-'; ?>
            </span>
        </div>
        <div class="cdg-td-meta-item">
            <span class="cdg-td-meta-label">Talep No</span>
            <span class="cdg-td-meta-value">
                <i class="bi bi-hash" style="color:var(--td-muted);"></i>
                #<?php echo htmlspecialchars($t_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </span>
        </div>
    </div>

    <!-- MESSAGES THREAD -->
    <div class="cdg-td-thread" id="cdg-td-thread">
        <?php if(empty($replies)): ?>
        <div class="cdg-td-msg from-customer">
            <div class="cdg-td-msg-head">
                <div class="cdg-td-msg-author">
                    <div class="cdg-td-msg-avatar"><i class="bi bi-person"></i></div>
                    <div class="cdg-td-msg-info">
                        <span class="cdg-td-msg-name">Talep Sahibi</span>
                        <span class="cdg-td-msg-role"><i class="bi bi-person-circle"></i> Müşteri</span>
                    </div>
                </div>
                <span class="cdg-td-msg-time"><?php echo htmlspecialchars(cdg_t_date($ticket['cdate'] ?? $t_lastreply), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </div>
            <div class="cdg-td-msg-body">
                <?php echo nl2br(htmlspecialchars($ticket['message'] ?? 'Talep mesajı bulunamadı.', ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
            </div>
        </div>
        <?php else: ?>
            <?php foreach($replies as $reply):
                if(!is_array($reply)) continue;
                $r_message = $reply['message'] ?? '';
                $r_date    = $reply['cdate'] ?? $reply['date'] ?? '';
                $r_isstaff = !empty($reply['staff']) || !empty($reply['admin']) || (isset($reply['type']) && $reply['type'] === 'staff');
                $r_name    = $reply['name'] ?? ($reply['author'] ?? 'Kullanıcı');
                $r_attachments = isset($reply['attachments']) && is_array($reply['attachments']) ? $reply['attachments'] : [];
                $r_id      = $reply['id'] ?? '';

                $msg_class = $r_isstaff ? 'from-staff' : 'from-customer';
                $avatar_letter = mb_strtoupper(mb_substr($r_name, 0, 1, 'UTF-8'), 'UTF-8');
            ?>
            <div class="cdg-td-msg <?php echo $msg_class; ?>" <?php if($r_id): ?>data-reply-id="<?php echo htmlspecialchars($r_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php endif; ?>>
                <div class="cdg-td-msg-head">
                    <div class="cdg-td-msg-author">
                        <div class="cdg-td-msg-avatar"><?php echo htmlspecialchars($avatar_letter, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <div class="cdg-td-msg-info">
                            <span class="cdg-td-msg-name"><?php echo htmlspecialchars($r_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            <span class="cdg-td-msg-role">
                                <?php if($r_isstaff): ?>
                                <i class="bi bi-shield-check" style="color:var(--td-primary);"></i> Destek Ekibi
                                <?php else: ?>
                                <i class="bi bi-person-circle"></i> Müşteri
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <span class="cdg-td-msg-time"><?php echo htmlspecialchars(cdg_t_date($r_date), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                </div>
                <div class="cdg-td-msg-body">
                    <?php echo nl2br(htmlspecialchars($r_message, ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>

                    <?php if(!empty($r_attachments)): ?>
                    <div style="margin-top:12px;">
                        <?php foreach($r_attachments as $att):
                            $att_url  = is_array($att) ? ($att['url'] ?? $att['link'] ?? '#') : $att;
                            $att_name = is_array($att) ? ($att['name'] ?? basename($att_url)) : basename($att);
                        ?>
                        <a href="<?php echo htmlspecialchars($att_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-td-msg-attach">
                            <i class="bi bi-paperclip"></i> <?php echo htmlspecialchars($att_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- REPLY FORM (eğer kilitli değilse) -->
    <?php if($t_locked): ?>
    <div class="cdg-td-locked">
        <i class="bi bi-lock-fill"></i>
        <div>
            <h4>Bu talep kilitlenmiştir</h4>
            <p>Bu talebe yeni mesaj eklenemez. Yeni bir konu için lütfen yeni talep oluşturun.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="cdg-td-reply" id="cdg-td-reply">
        <div class="cdg-td-reply-head">
            <h3><i class="bi bi-reply-fill"></i> Yanıt Yazın</h3>
        </div>
        <form id="cdg-td-reply-form" method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" enctype="multipart/form-data" onsubmit="return false;">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('reply-ticket'); ?>
            <input type="hidden" name="operation" value="reply">
            <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($t_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">

            <div class="cdg-td-reply-body">
                <textarea
                    name="message"
                    class="cdg-td-textarea"
                    placeholder="Yanıtınızı buraya yazın... Sorununuzu en iyi şekilde anlamamız için detay verebilirsiniz."
                    required></textarea>

                <!-- Dosya Eki -->
                <div style="margin-top:14px;padding:14px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:10px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <i class="bi bi-paperclip" style="font-size:20px;color:#1e40af;"></i>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:700;color:#0f172a;">Dosya Ekle (Opsiyonel)</div>
                            <div style="font-size:11px;color:#64748b;" id="cdg-td-attachments-info">Birden fazla dosya secebilirsiniz. Maks 5 MB.</div>
                        </div>
                        <input type="file" name="attachments[]" id="cdg-td-attachments" multiple style="display:none;" onchange="cdgTdShowFiles(this)">
                        <button type="button" class="cdg-td-btn" style="background:#fff;border:1px solid #e2e8f0;color:#475569;padding:6px 14px;font-size:12px;" onclick="document.getElementById('cdg-td-attachments').click()">
                            <i class="bi bi-folder2-open"></i> Dosya Sec
                        </button>
                    </label>
                    <div id="cdg-td-files-list" style="margin-top:10px;display:none;font-size:12px;"></div>
                </div>

                <div class="cdg-td-reply-actions">
                    <label class="cdg-td-check">
                        <input type="checkbox" name="encrypt_message" value="1">
                        <span><i class="bi bi-shield-lock"></i> Mesajımı şifrele</span>
                    </label>

                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <?php if(strtolower($t_status) !== 'solved' && strtolower($t_status) !== 'closed'): ?>
                        <button type="button" id="cdg-td-close-btn" class="cdg-td-btn cdg-td-btn-close">
                            <i class="bi bi-check2-circle"></i> Sorun Çözüldü
                        </button>
                        <?php else: ?>
                        <button type="button" id="cdg-td-reopen-btn" class="cdg-td-btn cdg-td-btn-reopen">
                            <i class="bi bi-arrow-clockwise"></i> Yeniden Aç
                        </button>
                        <?php endif; ?>
                        <button type="submit" id="cdg-td-send-btn" class="cdg-td-btn cdg-td-btn-send">
                            <i class="bi bi-send-fill"></i> Yanıt Gönder
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
// Dosya secimi gosterimi
window.cdgTdShowFiles = function(input) {
    var list = document.getElementById('cdg-td-files-list');
    if(!list) return;
    if(!input.files || input.files.length === 0) {
        list.style.display = 'none';
        return;
    }
    var html = '<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">';
    var totalSize = 0;
    for(var i = 0; i < input.files.length; i++) {
        var f = input.files[i];
        totalSize += f.size;
        var sizeStr = (f.size / 1024).toFixed(1) + ' KB';
        if(f.size > 1024 * 1024) sizeStr = (f.size / 1024 / 1024).toFixed(1) + ' MB';
        html += '<span style="background:#eff6ff;color:#1e40af;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;">';
        html += '<i class="bi bi-file-earmark"></i> ' + f.name + ' (' + sizeStr + ')</span>';
    }
    html += '</div>';
    if(totalSize > 5 * 1024 * 1024) {
        html += '<div style="margin-top:6px;color:#ef4444;font-size:11px;font-weight:700;"><i class="bi bi-exclamation-triangle"></i> Toplam boyut 5 MB sinirini astı!</div>';
    }
    list.innerHTML = html;
    list.style.display = 'block';
};
</script>

<script>
(function(){
    var ticketId = <?php echo (int)$t_id; ?>;
    var form = document.getElementById('cdg-td-reply-form');
    var sendBtn = document.getElementById('cdg-td-send-btn');
    var textarea = form ? form.querySelector('textarea[name=message]') : null;

    // Otomatik kaydet (localStorage)
    var autoKey = 'cdg_ticket_' + ticketId;
    if(textarea) {
        try {
            var saved = localStorage.getItem(autoKey);
            if(saved && !textarea.value) textarea.value = saved;
        } catch(e) {}

        textarea.addEventListener('input', function(){
            try { localStorage.setItem(autoKey, this.value); } catch(e) {}
        });
    }

    // Yanıt gönderme - MioAjax kullan
    if(form && sendBtn) {
        sendBtn.addEventListener('click', function(e){
            e.preventDefault();
            if(!textarea || !textarea.value.trim()) {
                if(typeof alert_error === 'function') {
                    alert_error('Yanıt mesajını yazın.', {timer: 3000});
                } else {
                    alert('Yanıt mesajını yazın.');
                }
                return;
            }

            if(typeof MioAjaxElement === 'function' && window.jQuery) {
                MioAjaxElement(jQuery(this), {
                    waiting_text: 'Gönderiliyor...',
                    result: 'cdgTicketReplyHandler'
                });
            } else {
                form.removeAttribute('onsubmit');
                form.submit();
            }
        });
    }

    // Çöz / Yeniden aç butonları
    var closeBtn = document.getElementById('cdg-td-close-btn');
    if(closeBtn) {
        closeBtn.addEventListener('click', function(){
            if(!confirm('Talebinizi çözüldü olarak işaretlemek istediğinize emin misiniz?')) return;
            cdgTicketStatus('solved');
        });
    }
    var reopenBtn = document.getElementById('cdg-td-reopen-btn');
    if(reopenBtn) {
        reopenBtn.addEventListener('click', function(){
            cdgTicketStatus('open');
        });
    }

    function cdgTicketStatus(newStatus) {
        if(typeof MioAjax === 'function') {
            MioAjax({
                url: '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',
                type: 'post',
                data: {
                    operation: 'change-status',
                    ticket_id: ticketId,
                    status: newStatus
                },
                result: function(r){
                    if(r && r.status === 'successful') {
                        if(typeof alert_success === 'function') alert_success(r.message || 'Durum güncellendi', {timer:1500});
                        setTimeout(function(){ location.reload(); }, 1200);
                    } else if(r && r.message && typeof alert_error === 'function') {
                        alert_error(r.message, {timer:3000});
                    }
                }
            });
        }
    }
})();

// MioAjax callback
function cdgTicketReplyHandler(result) {
    if(typeof getJson === 'function' && result) {
        var solve = getJson(result);
        if(solve !== false) {
            if(solve.status === 'error' && typeof alert_error === 'function') {
                alert_error(solve.message, {timer:4000});
            } else if(solve.status === 'successful') {
                if(typeof alert_success === 'function') {
                    alert_success(solve.message || 'Yanıtınız gönderildi', {timer:2000});
                }
                // Cleanup localStorage
                try { localStorage.removeItem('cdg_ticket_<?php echo (int)$t_id; ?>'); } catch(e) {}
                setTimeout(function(){ location.reload(); }, 1500);
            }
        }
    }
}
</script>
