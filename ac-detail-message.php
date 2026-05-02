<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Mesaj Detayı (sistem bildirim/duyuru detay)
 * WiseCP runtime: $message (addresses, content, date, subject, ...)
 */

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            $url = Controllers::$init->CRLink($slug, $params);
            if($url && (strpos($url, '/(0)') !== false || preg_match('#/0/?$#', $url))) {
                $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
                return $base . '/' . $slug . ($params ? '/' . implode('/', $params) : '');
            }
            return $url;
        }
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        return $base . '/' . $slug . ($params ? '/' . implode('/', $params) : '');
    }
}

$message = isset($message) && is_array($message) ? $message : [];
$msg_subject = $message['subject'] ?? $message['title'] ?? 'Mesaj';
$msg_content = $message['content'] ?? $message['body'] ?? '';
$msg_date = $message['date'] ?? $message['cdate'] ?? '';
$msg_addresses = $message['addresses'] ?? '';
$msg_from = $message['from'] ?? $message['sender'] ?? '';

function cdg_msg_date($date) {
    if(!$date) return '';
    if(class_exists('DateManager') && method_exists('DateManager','format') && class_exists('Config')) {
        $fmt = Config::get("options/date-format") ?: 'd.m.Y';
        return DateManager::format($fmt . ' H:i', $date);
    }
    return date('d.m.Y H:i', is_numeric($date) ? (int)$date : strtotime((string)$date));
}

$messages_url = cdg_link('messages');
?>

<style>
.cdg-msg {
    --m-primary: #1e40af;
    --m-bg: #f8fafc;
    --m-card: #fff;
    --m-text: #0f172a;
    --m-muted: #64748b;
    --m-border: #e2e8f0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--m-text);
    background: var(--m-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-msg *, .cdg-msg *::before, .cdg-msg *::after { box-sizing: border-box; }
.cdg-msg a { text-decoration: none; color: inherit; }
.cdg-msg-wrap { max-width: 900px; margin: 0 auto; padding: 0 20px; }

.cdg-msg-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--m-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--m-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-msg-back:hover { border-color: var(--m-primary); color: var(--m-primary); }

.cdg-msg-card {
    background: var(--m-card);
    border: 1px solid var(--m-border);
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    overflow: hidden;
}
.cdg-msg-head {
    padding: 22px 28px;
    background: linear-gradient(135deg, #f8fafc, #fff);
    border-bottom: 1px solid var(--m-border);
}
.cdg-msg-eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 99px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}
.cdg-msg-head h1 {
    font-size: 22px;
    font-weight: 800;
    margin: 0 0 12px;
    letter-spacing: -0.3px;
    word-break: break-word;
}
.cdg-msg-meta {
    display: flex; gap: 16px; flex-wrap: wrap;
    font-size: 12px;
    color: var(--m-muted);
}
.cdg-msg-meta span { display: inline-flex; align-items: center; gap: 5px; }
.cdg-msg-meta i { color: var(--m-primary); }

.cdg-msg-body {
    padding: 26px 28px;
    font-size: 14px;
    line-height: 1.7;
    color: var(--m-text);
}
.cdg-msg-body p { margin: 0 0 12px; }
.cdg-msg-body p:last-child { margin-bottom: 0; }
.cdg-msg-body img { max-width: 100%; height: auto; border-radius: 8px; }

.cdg-msg-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--m-muted);
}
.cdg-msg-empty i {
    font-size: 48px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 10px;
}

@media (max-width: 600px) {
    .cdg-msg-head { padding: 20px 22px; }
    .cdg-msg-body { padding: 22px; }
    .cdg-msg-head h1 { font-size: 19px; }
}
</style>

<div class="cdg-msg">
<div class="cdg-msg-wrap">

    <a href="<?php echo htmlspecialchars($messages_url); ?>" class="cdg-msg-back">
        <i class="bi bi-arrow-left"></i> Mesajlara Dön
    </a>

    <article class="cdg-msg-card">
        <header class="cdg-msg-head">
            <span class="cdg-msg-eyebrow">
                <i class="bi bi-envelope-open"></i> Bildirim
            </span>
            <h1><?php echo htmlspecialchars($msg_subject); ?></h1>
            <div class="cdg-msg-meta">
                <?php if($msg_date): ?>
                <span><i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars(cdg_msg_date($msg_date)); ?></span>
                <?php endif; ?>
                <?php if($msg_from): ?>
                <span><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($msg_from); ?></span>
                <?php endif; ?>
                <?php if($msg_addresses): ?>
                <span><i class="bi bi-at"></i> <?php echo htmlspecialchars($msg_addresses); ?></span>
                <?php endif; ?>
            </div>
        </header>

        <div class="cdg-msg-body">
            <?php if($msg_content): ?>
                <?php echo $msg_content; /* WiseCP HTML olarak hazırlar - güvenli */ ?>
            <?php else: ?>
                <div class="cdg-msg-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Mesaj içeriği bulunamadı.</p>
                </div>
            <?php endif; ?>
        </div>
    </article>

</div>
</div>
