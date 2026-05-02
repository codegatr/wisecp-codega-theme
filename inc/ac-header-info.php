<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Müşteri Paneli Header Bilgi Widget'ı
 * Hoş geldin mesajı + bayilik rozeti + son giriş bilgisi
 * WiseCP runtime: $acheader_info (name, surname, full_name, last_login_date, last_login_ip, dealership[status])
 */

if(!isset($ac_header_info_inc)) {
    $ac_header_info_inc = true;

    $info = isset($acheader_info) && is_array($acheader_info) ? $acheader_info : [];
    $name      = $info['name'] ?? '';
    $surname   = $info['surname'] ?? '';
    $full_name = $info['full_name'] ?? trim($name . ' ' . $surname);
    $last_date = $info['last_login_date'] ?? '';
    $last_ip   = $info['last_login_ip'] ?? '';
    $is_reseller = isset($info['dealership']['status']) && $info['dealership']['status'] === 'active';

    $avatar_letter = '?';
    if($name) $avatar_letter = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
    elseif($full_name) $avatar_letter = mb_strtoupper(mb_substr($full_name, 0, 1, 'UTF-8'), 'UTF-8');

    $reseller_url = '#';
    if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
        $reseller_url = Controllers::$init->CRLink('reseller');
    }
?>
<style>
.cdg-hi {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 14px;
    padding: 16px 20px;
    color: #fff;
    margin-bottom: 18px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: 0 8px 24px rgba(30,64,175,0.16);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
    flex-wrap: wrap;
}
.cdg-hi *, .cdg-hi *::before, .cdg-hi *::after { box-sizing: border-box; }
.cdg-hi-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.22);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 18px;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
}
.cdg-hi-text { flex: 1; min-width: 200px; }
.cdg-hi-greet {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 2px;
}
.cdg-hi-greet strong { color: #fde047; }
.cdg-hi-meta {
    font-size: 12px;
    opacity: 0.88;
    display: flex; gap: 12px; flex-wrap: wrap;
}
.cdg-hi-meta span { display: inline-flex; align-items: center; gap: 5px; }
.cdg-hi-reseller {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1e3a8a;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(252,211,77,0.30);
    transition: all 0.18s;
    flex-shrink: 0;
}
.cdg-hi-reseller:hover { transform: translateY(-1px); color: #1e3a8a; }
@media (max-width: 600px) {
    .cdg-hi { flex-direction: column; text-align: center; align-items: center; }
    .cdg-hi-meta { justify-content: center; }
}
</style>

<div class="cdg-hi">
    <div class="cdg-hi-avatar"><?php echo htmlspecialchars($avatar_letter); ?></div>
    <div class="cdg-hi-text">
        <div class="cdg-hi-greet">
            Hoş geldin, <strong><?php echo htmlspecialchars($full_name ?: 'Müşteri'); ?></strong>!
        </div>
        <div class="cdg-hi-meta">
            <?php if($last_date): ?>
            <span><i class="bi bi-clock-history"></i> Son giriş: <?php echo htmlspecialchars($last_date); ?></span>
            <?php endif; ?>
            <?php if($last_ip): ?>
            <span><i class="bi bi-geo-alt"></i> IP: <?php echo htmlspecialchars($last_ip); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if($is_reseller): ?>
    <a href="<?php echo htmlspecialchars($reseller_url); ?>" class="cdg-hi-reseller" title="Aktif Bayi Hesabı">
        <i class="bi bi-shop-window"></i> AKTİF BAYİ
    </a>
    <?php endif; ?>
</div>
<?php
}
?>
