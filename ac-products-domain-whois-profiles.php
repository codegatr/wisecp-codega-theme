<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - WHOIS Profilleri Listesi
 * WiseCP runtime: $profiles, $links
 */

if(isset($tpath) && file_exists($tpath . "common-needs.php")) {
    include $tpath . "common-needs.php";
}

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

$profiles = isset($profiles) && is_array($profiles) ? $profiles : [];
$links = isset($links) && is_array($links) ? $links : [];
$controller_url = $links['controller'] ?? '';
$create_url = cdg_link('products-domain-create-whois-profile');
$back_url = cdg_link('products-domain');
?>

<style>
.cdg-wp {
    --wp-primary: #1e40af;
    --wp-bg: #f8fafc;
    --wp-card: #fff;
    --wp-text: #0f172a;
    --wp-muted: #64748b;
    --wp-border: #e2e8f0;
    --wp-radius: 14px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--wp-text);
    background: var(--wp-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-wp *, .cdg-wp *::before, .cdg-wp *::after { box-sizing: border-box; }
.cdg-wp a { text-decoration: none; color: inherit; }
.cdg-wp-wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

.cdg-wp-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--wp-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--wp-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-wp-back:hover { border-color: var(--wp-primary); color: var(--wp-primary); }

.cdg-wp-hero {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    border-radius: 18px;
    padding: 26px 30px;
    color: #fff;
    margin-bottom: 22px;
    display: flex; align-items: center; gap: 18px;
    flex-wrap: wrap;
    box-shadow: 0 16px 40px rgba(99,102,241,0.20);
}
.cdg-wp-hero-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 26px;
    flex-shrink: 0;
}
.cdg-wp-hero-text { flex: 1; min-width: 200px; }
.cdg-wp-hero h1 { font-size: 24px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-wp-hero p { font-size: 13px; opacity: 0.88; margin: 0; }

.cdg-wp-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none;
    font-family: inherit;
    border: 0;
}
.cdg-wp-btn-gold {
    background: linear-gradient(135deg, #fde047, #facc15);
    color: #1e3a8a;
    box-shadow: 0 6px 18px rgba(252,211,77,0.30);
}
.cdg-wp-btn-gold:hover { transform: translateY(-1px); color: #1e3a8a; }
.cdg-wp-btn-outline {
    background: #fff;
    border: 1px solid var(--wp-border);
    color: var(--wp-text);
}
.cdg-wp-btn-outline:hover { border-color: var(--wp-primary); color: var(--wp-primary); }
.cdg-wp-btn-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
.cdg-wp-btn-danger:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
.cdg-wp-btn-sm { padding: 7px 12px; font-size: 12px; }

.cdg-wp-list { display: flex; flex-direction: column; gap: 12px; }
.cdg-wp-card {
    background: var(--wp-card);
    border: 1px solid var(--wp-border);
    border-radius: var(--wp-radius);
    padding: 18px 22px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 16px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
    transition: all 0.18s;
}
.cdg-wp-card:hover { box-shadow: 0 8px 24px rgba(15,23,42,0.08); transform: translateY(-1px); }
.cdg-wp-card-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    color: #fff;
    display: grid; place-items: center;
    font-size: 22px;
}
.cdg-wp-card-body { min-width: 0; }
.cdg-wp-card-name { font-size: 16px; font-weight: 800; color: var(--wp-text); margin-bottom: 4px; word-break: break-word; }
.cdg-wp-card-meta { font-size: 12px; color: var(--wp-muted); display: flex; gap: 14px; flex-wrap: wrap; }
.cdg-wp-card-meta span { display: inline-flex; align-items: center; gap: 5px; }

.cdg-wp-empty {
    text-align: center;
    padding: 60px 20px;
    background: var(--wp-card);
    border: 2px dashed var(--wp-border);
    border-radius: var(--wp-radius);
}
.cdg-wp-empty i { font-size: 56px; color: #cbd5e1; display: block; margin-bottom: 12px; }
.cdg-wp-empty h3 { font-size: 18px; font-weight: 800; margin: 0 0 6px; }
.cdg-wp-empty p { font-size: 14px; color: var(--wp-muted); margin: 0 0 18px; }

@media (max-width: 600px) {
    .cdg-wp-card { grid-template-columns: auto 1fr; gap: 12px; }
    .cdg-wp-card-action { grid-column: 1 / -1; display: flex; gap: 8px; flex-wrap: wrap; }
    .cdg-wp-hero { flex-direction: column; text-align: center; padding: 22px 20px; }
}
</style>

<div class="cdg-wp">
<div class="cdg-wp-wrap">

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-wp-back">
        <i class="bi bi-arrow-left"></i> Domainlerime Dön
    </a>

    <section class="cdg-wp-hero">
        <div class="cdg-wp-hero-icon"><i class="bi bi-person-vcard"></i></div>
        <div class="cdg-wp-hero-text">
            <h1>WHOIS Profillerim</h1>
            <p>Domain kayıtlarınızda kullanacağınız iletişim bilgilerini profil olarak yönetin.</p>
        </div>
        <a href="<?php echo htmlspecialchars($create_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-wp-btn cdg-wp-btn-gold">
            <i class="bi bi-plus-circle"></i> Yeni Profil
        </a>
    </section>

    <?php if(empty($profiles)): ?>
    <div class="cdg-wp-empty">
        <i class="bi bi-person-vcard"></i>
        <h3>Henüz WHOIS Profiliniz Yok</h3>
        <p>Domainlerinizde tekrar tekrar bilgi girmemek için bir profil oluşturun.</p>
        <a href="<?php echo htmlspecialchars($create_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-wp-btn cdg-wp-btn-gold">
            <i class="bi bi-plus-circle"></i> İlk Profili Oluştur
        </a>
    </div>
    <?php else: ?>
    <div class="cdg-wp-list">
        <?php foreach($profiles as $row):
            if(!is_array($row)) continue;
            $p_id = $row['id'] ?? 0;
            $p_name = $row['name'] ?? 'Profil';
            $p_pname = $row['person_name'] ?? '';
            $p_email = $row['person_email'] ?? '';
            $p_phone = $row['person_phone'] ?? '';
            $edit_url = $controller_url . '?page=edit_whois_profile&id=' . (int)$p_id;
        ?>
        <div class="cdg-wp-card" data-id="<?php echo (int)$p_id; ?>">
            <div class="cdg-wp-card-icon"><i class="bi bi-person-badge"></i></div>
            <div class="cdg-wp-card-body">
                <div class="cdg-wp-card-name"><?php echo htmlspecialchars($p_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                <div class="cdg-wp-card-meta">
                    <?php if($p_pname): ?>
                    <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($p_pname, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($p_email): ?>
                    <span><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($p_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if($p_phone): ?>
                    <span><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($p_phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cdg-wp-card-action" style="display:flex;gap:6px;">
                <a href="<?php echo htmlspecialchars($edit_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-wp-btn cdg-wp-btn-outline cdg-wp-btn-sm">
                    <i class="bi bi-pencil"></i> Düzenle
                </a>
                <button type="button" class="cdg-wp-btn cdg-wp-btn-danger cdg-wp-btn-sm" onclick="cdgWhoisProfileDelete(<?php echo (int)$p_id; ?>)">
                    <i class="bi bi-trash"></i> Sil
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
function cdgWhoisProfileDelete(id) {
    if(!confirm('Bu WHOIS profilini silmek istediğinize emin misiniz? Bu profile bağlı domainler etkilenebilir.')) return;
    if(typeof MioAjax !== 'function') return;
    MioAjax({
        url: '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',
        type: 'post',
        data: { operation: 'delete_whois_profile', id: id },
        result: function(r){
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Profil silindi', {timer: 1500});
                setTimeout(function(){ location.reload(); }, 1200);
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 3000});
            }
        }
    });
}
</script>
