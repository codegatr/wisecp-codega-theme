<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Talep Yanıtları AJAX Endpoint
 * Talep detay sayfasında yeni gelen yanıtları döner (HTML olarak)
 * ac-detail-ticket-request.php ile aynı görsel dilde
 *
 * WiseCP runtime: $replies, $get_last_reply_id, $zone
 */

if(!isset($replies) || !is_array($replies)) return;

// Tarih formatla
function cdg_atr_date($date) {
    if(!$date) return '';
    if(class_exists('UserManager') && method_exists('UserManager','formatTimeZone') && class_exists('Config')) {
        try {
            $tz = (isset($zone)) ? $zone : 'Europe/Istanbul';
            return UserManager::formatTimeZone($date, $tz, Config::get("options/date-format") . " H:i");
        } catch(\Throwable $e) {}
    }
    return date('d.m.Y H:i', is_numeric($date) ? (int)$date : strtotime((string)$date));
}

$last_reply_id = isset($get_last_reply_id) ? (int)$get_last_reply_id : 0;

foreach($replies as $reply):
    if(!is_array($reply)) continue;

    $message = $reply['message'] ?? '';
    if(class_exists('Validation') && method_exists('Validation','isHTML')) {
        if(!Validation::isHTML($message)) $message = nl2br($message);
    } else {
        $message = nl2br(htmlspecialchars($message, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
    if(class_exists('Filter') && method_exists('Filter','link_convert')) {
        $message = Filter::link_convert($message, true);
    }

    $is_admin   = !empty($reply['admin']);
    $is_new     = ($last_reply_id > 0);
    $r_name     = $reply['name'] ?? 'Kullanıcı';
    $r_date     = $reply['ctime'] ?? '';
    $r_enc      = !empty($reply['encrypted']);
    $r_attachs  = isset($reply['attachments']) && is_array($reply['attachments']) ? $reply['attachments'] : [];
    $r_id       = $reply['id'] ?? '';

    $msg_class    = $is_admin ? 'from-staff' : 'from-customer';
    $avatar       = mb_strtoupper(mb_substr($r_name, 0, 1, 'UTF-8'), 'UTF-8');
    $role_label   = $is_admin ? 'Destek Ekibi' : 'Müşteri';
    $role_icon    = $is_admin ? 'shield-check' : 'person-circle';
?>
<div class="cdg-td-msg <?php echo $msg_class; ?><?php echo $is_new ? ' new-reply' : ''; ?>" <?php if($r_id): ?>data-reply-id="<?php echo htmlspecialchars($r_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php endif; ?>>
    <div class="cdg-td-msg-head">
        <div class="cdg-td-msg-author">
            <div class="cdg-td-msg-avatar"><?php echo htmlspecialchars($avatar, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
            <div class="cdg-td-msg-info">
                <span class="cdg-td-msg-name"><?php echo htmlspecialchars($r_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <span class="cdg-td-msg-role">
                    <i class="bi bi-<?php echo $role_icon; ?>"<?php echo $is_admin ? ' style="color:var(--td-primary);"' : ''; ?>></i>
                    <?php echo htmlspecialchars($role_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </span>
            </div>
        </div>
        <span class="cdg-td-msg-time"><?php echo htmlspecialchars(cdg_atr_date($r_date), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
    </div>
    <div class="cdg-td-msg-body">
        <?php echo $message; ?>

        <?php if($r_enc): ?>
        <div style="margin-top:10px;display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:#CFFAFE;color:#1A2332;border-radius:99px;font-size:11px;font-weight:700;">
            <i class="bi bi-shield-lock-fill"></i> Şifrelenmiş Mesaj
        </div>
        <?php endif; ?>

        <?php if(!empty($r_attachs)): ?>
        <div style="margin-top:12px;">
            <?php if($is_admin): ?>
                <strong style="font-size:12px;color:var(--td-muted);display:block;margin-bottom:6px;">EK DOSYALAR</strong>
                <?php foreach($r_attachs as $att):
                    if(!is_array($att)) continue;
                    $att_url  = $att['link'] ?? $att['url'] ?? '#';
                    $att_name = $att['file_name'] ?? $att['name'] ?? 'dosya';
                ?>
                <a href="<?php echo htmlspecialchars($att_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" rel="noopener" class="cdg-td-msg-attach">
                    <i class="bi bi-paperclip"></i> <?php echo htmlspecialchars($att_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="cdg-td-msg-attach" style="cursor:default;">
                    <i class="bi bi-paperclip"></i> Dosya eklendi
                </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<script>
(function(){
    // Yeni mesajların yumuşak görünmesi için fade animasyon
    var newMsgs = document.querySelectorAll('.new-reply');
    newMsgs.forEach(function(el){
        el.style.opacity = '0';
        el.style.transform = 'translateY(8px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        setTimeout(function(){
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 50);
        // Vurgulama efekti
        setTimeout(function(){
            el.classList.remove('new-reply');
        }, 2000);
    });
})();
</script>
