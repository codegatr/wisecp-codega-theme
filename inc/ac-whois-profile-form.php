<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - WHOIS Profil Form (Oluştur / Düzenle)
 * Caller dosya $cdg_whois_mode = 'create' | 'edit' set eder
 *
 * WiseCP runtime: $profile (edit modunda dolu), $links
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

$cdg_whois_mode = $cdg_whois_mode ?? 'create';
$is_edit = ($cdg_whois_mode === 'edit');

$profile = isset($profile) && is_array($profile) ? $profile : [];
$information = isset($profile['information']) && is_array($profile['information']) ? $profile['information'] : [];
$profile_id = $profile['id'] ?? 0;
$profile_name = $profile['name'] ?? '';

$links = isset($links) && is_array($links) ? $links : [];
$controller_url = $links['controller'] ?? '';
$back_url = cdg_link('products-domain-whois-profiles');

$operation = $is_edit ? 'edit_whois_profile' : 'create_whois_profile';
$btn_label = $is_edit ? 'Profili Güncelle' : 'Profili Oluştur';
$page_title = $is_edit ? 'WHOIS Profili Düzenle' : 'Yeni WHOIS Profili';

function cdg_whois_csrf($action) {
    if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) {
        return Validation::get_csrf_token($action);
    }
    return '';
}
?>

<style>
.cdg-wpf {
    --f-primary: #1e40af;
    --f-bg: #f8fafc;
    --f-card: #fff;
    --f-text: #0f172a;
    --f-muted: #64748b;
    --f-border: #e2e8f0;
    --f-radius: 14px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--f-text);
    background: var(--f-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-wpf *, .cdg-wpf *::before, .cdg-wpf *::after { box-sizing: border-box; }
.cdg-wpf a { text-decoration: none; color: inherit; }
.cdg-wpf-wrap { max-width: 900px; margin: 0 auto; padding: 0 20px; }

.cdg-wpf-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--f-border);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--f-text);
    margin-bottom: 18px;
    transition: all 0.18s;
}
.cdg-wpf-back:hover { border-color: var(--f-primary); color: var(--f-primary); }

.cdg-wpf-hero {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    border-radius: 18px;
    padding: 26px 30px;
    color: #fff;
    margin-bottom: 22px;
    display: flex; align-items: center; gap: 18px;
    box-shadow: 0 16px 40px rgba(99,102,241,0.20);
}
.cdg-wpf-hero-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 26px;
    flex-shrink: 0;
}
.cdg-wpf-hero h1 { font-size: 24px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.4px; }
.cdg-wpf-hero p { font-size: 13px; opacity: 0.88; margin: 0; }

.cdg-wpf-card {
    background: var(--f-card);
    border: 1px solid var(--f-border);
    border-radius: var(--f-radius);
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    overflow: hidden;
}
.cdg-wpf-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--f-border);
    background: linear-gradient(135deg, #f8fafc, #fff);
}
.cdg-wpf-card-head h3 {
    font-size: 14px; font-weight: 800; margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-wpf-card-head h3 i { color: var(--f-primary); }
.cdg-wpf-card-body { padding: 24px; }

.cdg-wpf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.cdg-wpf-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.cdg-wpf-field { margin-bottom: 14px; }
.cdg-wpf-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 7px;
    color: var(--f-text);
}
.cdg-wpf-label .req { color: #ef4444; margin-left: 2px; }
.cdg-wpf-input,
.cdg-wpf-select {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--f-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--f-text);
    background: #fff;
    outline: none;
    transition: all 0.18s;
    font-family: inherit;
}
.cdg-wpf-input:focus,
.cdg-wpf-select:focus {
    border-color: var(--f-primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,0.10);
}

.cdg-wpf-section {
    border-top: 1px dashed var(--f-border);
    padding-top: 18px;
    margin-top: 18px;
}
.cdg-wpf-section-title {
    font-size: 12px;
    font-weight: 800;
    color: var(--f-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

.cdg-wpf-check {
    display: inline-flex; align-items: center; gap: 10px;
    cursor: pointer;
    font-size: 13px;
    color: var(--f-text);
    margin: 8px 0;
}
.cdg-wpf-check input { transform: scale(1.15); accent-color: var(--f-primary); margin: 0; }

.cdg-wpf-actions {
    display: flex; justify-content: space-between; align-items: center;
    padding-top: 18px;
    border-top: 1px solid var(--f-border);
    margin-top: 20px;
    gap: 10px;
    flex-wrap: wrap;
}
.cdg-wpf-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    border: 0;
    font-family: inherit;
    text-decoration: none;
}
.cdg-wpf-btn-primary {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    box-shadow: 0 6px 18px rgba(30,64,175,0.22);
}
.cdg-wpf-btn-primary:hover { transform: translateY(-1px); color: #fff; }
.cdg-wpf-btn-cancel {
    background: #fff;
    color: var(--f-text);
    border: 1px solid var(--f-border);
}
.cdg-wpf-btn-cancel:hover { border-color: var(--f-muted); }

@media (max-width: 600px) {
    .cdg-wpf-row, .cdg-wpf-row-3 { grid-template-columns: 1fr; }
    .cdg-wpf-hero { flex-direction: column; text-align: center; padding: 22px 20px; }
}
</style>

<div class="cdg-wpf">
<div class="cdg-wpf-wrap">

    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-wpf-back">
        <i class="bi bi-arrow-left"></i> Profillere Dön
    </a>

    <section class="cdg-wpf-hero">
        <div class="cdg-wpf-hero-icon"><i class="bi bi-<?php echo $is_edit ? 'pencil-square' : 'plus-circle'; ?>"></i></div>
        <div>
            <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
            <p>Domainlerinizde kullanacağınız iletişim bilgilerini girin.</p>
        </div>
    </section>

    <div class="cdg-wpf-card">
        <div class="cdg-wpf-card-head">
            <h3><i class="bi bi-person-vcard"></i> WHOIS Bilgileri</h3>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" id="cdg-wpf-form">
            <?php echo cdg_whois_csrf($operation); ?>
            <input type="hidden" name="operation" value="<?php echo htmlspecialchars($operation, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <?php if($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo (int)$profile_id; ?>">
            <?php endif; ?>

            <div class="cdg-wpf-card-body">

                <!-- Profil Adı -->
                <div class="cdg-wpf-field">
                    <label class="cdg-wpf-label">Profil Adı <span class="req">*</span></label>
                    <input type="text" name="profile_name" class="cdg-wpf-input" required
                           value="<?php echo htmlspecialchars($profile_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                           placeholder="Örn: Şirket Profili / Kişisel">
                </div>

                <!-- Kişi Bilgileri -->
                <div class="cdg-wpf-section">
                    <div class="cdg-wpf-section-title">KİŞİ BİLGİLERİ</div>

                    <div class="cdg-wpf-row">
                        <div>
                            <label class="cdg-wpf-label">Ad Soyad <span class="req">*</span></label>
                            <input type="text" name="Name" class="cdg-wpf-input" required
                                   value="<?php echo htmlspecialchars($information['Name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                        <div>
                            <label class="cdg-wpf-label">Firma Adı</label>
                            <input type="text" name="Company" class="cdg-wpf-input"
                                   value="<?php echo htmlspecialchars($information['Company'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="cdg-wpf-field">
                        <label class="cdg-wpf-label">E-Posta <span class="req">*</span></label>
                        <input type="email" name="EMail" class="cdg-wpf-input" required
                               value="<?php echo htmlspecialchars($information['EMail'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                    </div>

                    <div class="cdg-wpf-row">
                        <div>
                            <label class="cdg-wpf-label">Telefon Ülke Kodu <span class="req">*</span></label>
                            <input type="text" name="PhoneCountryCode" class="cdg-wpf-input" required
                                   value="<?php echo htmlspecialchars($information['PhoneCountryCode'] ?? '+90', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                                   placeholder="+90">
                        </div>
                        <div>
                            <label class="cdg-wpf-label">Telefon <span class="req">*</span></label>
                            <input type="text" name="Phone" class="cdg-wpf-input" required
                                   value="<?php echo htmlspecialchars($information['Phone'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                                   placeholder="5XX XXX XX XX">
                        </div>
                    </div>

                    <div class="cdg-wpf-row">
                        <div>
                            <label class="cdg-wpf-label">Faks Ülke Kodu</label>
                            <input type="text" name="FaxCountryCode" class="cdg-wpf-input"
                                   value="<?php echo htmlspecialchars($information['FaxCountryCode'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                        <div>
                            <label class="cdg-wpf-label">Faks</label>
                            <input type="text" name="Fax" class="cdg-wpf-input"
                                   value="<?php echo htmlspecialchars($information['Fax'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                    </div>
                </div>

                <!-- Adres Bilgileri -->
                <div class="cdg-wpf-section">
                    <div class="cdg-wpf-section-title">ADRES BİLGİLERİ</div>

                    <div class="cdg-wpf-field">
                        <label class="cdg-wpf-label">Adres <span class="req">*</span></label>
                        <input type="text" name="Address" class="cdg-wpf-input" required
                               value="<?php echo htmlspecialchars($information['Address'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                    </div>

                    <div class="cdg-wpf-row-3">
                        <div>
                            <label class="cdg-wpf-label">Şehir <span class="req">*</span></label>
                            <input type="text" name="City" class="cdg-wpf-input" required
                                   value="<?php echo htmlspecialchars($information['City'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                        <div>
                            <label class="cdg-wpf-label">İl/Eyalet</label>
                            <input type="text" name="State" class="cdg-wpf-input"
                                   value="<?php echo htmlspecialchars($information['State'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                        <div>
                            <label class="cdg-wpf-label">Posta Kodu</label>
                            <input type="text" name="ZipCode" class="cdg-wpf-input"
                                   value="<?php echo htmlspecialchars($information['ZipCode'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="cdg-wpf-field">
                        <label class="cdg-wpf-label">Ülke Kodu (2 harf) <span class="req">*</span></label>
                        <input type="text" name="Country" class="cdg-wpf-input" required maxlength="2"
                               value="<?php echo htmlspecialchars($information['Country'] ?? 'TR', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                               placeholder="TR" style="text-transform:uppercase;">
                    </div>
                </div>

                <!-- Varsayılan Profil -->
                <div class="cdg-wpf-section">
                    <label class="cdg-wpf-check">
                        <input type="checkbox" name="detouse" value="1" <?php echo !empty($profile['detouse']) ? 'checked' : ''; ?>>
                        <span><i class="bi bi-star-fill" style="color:#f59e0b;"></i> Bu profili varsayılan olarak kullan</span>
                    </label>
                </div>

            </div>

            <div class="cdg-wpf-card-body" style="padding-top:0;">
                <div class="cdg-wpf-actions">
                    <a href="<?php echo htmlspecialchars($back_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-wpf-btn cdg-wpf-btn-cancel">
                        <i class="bi bi-x-lg"></i> İptal
                    </a>
                    <button type="submit" class="cdg-wpf-btn cdg-wpf-btn-primary">
                        <i class="bi bi-save"></i> <?php echo htmlspecialchars($btn_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
</div>
