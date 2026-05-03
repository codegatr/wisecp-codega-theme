<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Hesap Bilgilerim Sayfasi
 *
 * Operations:
 *  - ModifyAccountInfo: Kisisel/kurumsal bilgi guncellemesi
 *  - ModifyPassword: Sifre degisikligi
 *
 * WiseCP runtime:
 *  - $udata: kullanici verileri (full_name, email, gsm, identity, kind, company_*, country_id, city, counti, zipcode, address, birthday, security_question, verified-email, verified-gsm, gsm_cc)
 *  - $editable: degistirilebilir alanlar (full_name, email, gsm, landline_phone, identity, birthday, kind)
 *  - $countryList: ulke listesi
 *  - $cfields: ozel alanlar (custom fields)
 *  - $operation_link, $links
 */

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
            'create-ticket-request' => 'ac-ps-create-ticket-request',
            'tickets' => 'ac-ps-tickets', 'invoices' => 'ac-ps-invoices',
            'detail-invoice' => 'ac-ps-detail-invoice', 'balance' => 'ac-ps-balance',
            'info' => 'ac-ps-info', 'products' => 'ac-ps-products',
            'product' => 'ac-ps-product', 'sms' => 'ac-ps-sms',
            'domains' => 'ac-products-domain', 'login' => 'sign-in',
            'register' => 'sign-up', 'logout' => 'sign-out',
        ];
        $real_slug = isset($aliases[$slug]) ? $aliases[$slug] : $slug;
        if(class_exists('Controllers') && isset(Controllers::$init) && method_exists(Controllers::$init, 'CRLink')) {
            try {
                $url = Controllers::$init->CRLink($real_slug, $params);
                if($url && strpos($url, '/(0)') === false) return $url;
            } catch(\Throwable $e) {}
        }
        // Son care: $links bakilirsa kullan
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) return $links[$slug];
        $base = defined('APP_URI') ? rtrim(APP_URI, '/') : '';
        return $base . '/' . $real_slug . ($params ? '/' . implode('/', $params) : '');
    }
}

// === WiseCP runtime'dan veri al ===
$udata = isset($udata) && is_array($udata) ? $udata : [];

// Geri uyumluluk için $info'dan da al
if(empty($udata) && class_exists('User') && isset(User::$init->info)) {
    $udata = User::$init->info;
}

$editable = isset($editable) && is_array($editable) ? $editable : [
    'full_name' => true, 'email' => true, 'gsm' => true, 'landline_phone' => true,
    'identity' => true, 'birthday' => true, 'kind' => true
];
$country_list = isset($countryList) && is_array($countryList) ? $countryList : [];
$cfields_list = isset($cfields) && is_array($cfields) ? $cfields : [];
$op_link = isset($operation_link) ? $operation_link : (isset($links['controller']) ? $links['controller'] : '');

// Helper
$u = function($key, $default = '') use ($udata) {
    return isset($udata[$key]) ? $udata[$key] : $default;
};

$kind = $u('kind', 'individual');
$is_corporate = ($kind === 'corporate');
$verified_email = !empty($udata['verified-email']);
$verified_gsm   = !empty($udata['verified-gsm']);

// Cep numarasi
$gsm_full = '';
if(!empty($udata['gsm'])) {
    $gsm_full = '+' . ($udata['gsm_cc'] ?? '90') . $udata['gsm'];
}
?>

<!-- KURUMSAL HESAP BİLGİLERİ SHELL -->
<div class="cdg-info-shell">
    <div class="cdg-info-shell-head">
        <div class="cdg-info-shell-head-left">
            <div class="cdg-info-shell-icon">
                <?php
                $initial = '';
                if(!empty($udata['name'])) $initial .= mb_strtoupper(mb_substr($udata['name'], 0, 1));
                if(!empty($udata['surname'])) $initial .= mb_strtoupper(mb_substr($udata['surname'], 0, 1));
                if(!$initial && !empty($udata['email'])) $initial = mb_strtoupper(mb_substr($udata['email'], 0, 1));
                if(!$initial) $initial = '?';
                ?>
                <span><?php echo htmlspecialchars($initial, ENT_QUOTES); ?></span>
            </div>
            <div style="min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <h1 class="cdg-info-shell-title">
                        <?php echo htmlspecialchars(trim(($udata['name'] ?? '') . ' ' . ($udata['surname'] ?? '')) ?: 'Hesap', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </h1>
                    <?php if($verified_email && $verified_gsm): ?>
                    <span class="cdg-info-verified-chip"><i class="bi bi-patch-check-fill"></i> Doğrulandı</span>
                    <?php elseif(!$verified_email || !$verified_gsm): ?>
                    <span class="cdg-info-pending-chip"><i class="bi bi-exclamation-circle"></i> Doğrulama Bekliyor</span>
                    <?php endif; ?>
                </div>
                <div class="cdg-info-shell-sub">
                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($udata['email'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    <?php if($gsm_full): ?>
                    <span style="margin:0 6px;color:#cbd5e1;">·</span>
                    <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($gsm_full, ENT_QUOTES); ?>
                    <?php endif; ?>
                    <?php if($is_corporate && !empty($udata['company_name'])): ?>
                    <span style="margin:0 6px;color:#cbd5e1;">·</span>
                    <i class="bi bi-building"></i> <?php echo htmlspecialchars($udata['company_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="cdg-info-shell-right">
            <a href="<?php echo cdg_link('ac-dashboard'); ?>" class="cdg-info-shell-btn">
                <i class="bi bi-speedometer2"></i> Panele Dön
            </a>
        </div>
    </div>

    <div class="cdg-info-shell-body">
        <!-- SOL: SIDEBAR NAVIGATION -->
        <aside class="cdg-info-side">
            <div class="cdg-info-side-group">
                <div class="cdg-info-side-title">Hesap</div>
                <button type="button" class="cdg-info-side-link active" data-tab="profile" onclick="cdgInfoTab('profile')">
                    <i class="bi bi-person-badge"></i> Profil Bilgileri
                </button>
                <button type="button" class="cdg-info-side-link" data-tab="addresses" onclick="cdgInfoTab('addresses')">
                    <i class="bi bi-geo-alt"></i> Adreslerim
                </button>
                <button type="button" class="cdg-info-side-link" data-tab="preferences" onclick="cdgInfoTab('preferences')">
                    <i class="bi bi-sliders"></i> Tercihler
                </button>
            </div>

            <div class="cdg-info-side-group">
                <div class="cdg-info-side-title">Güvenlik</div>
                <button type="button" class="cdg-info-side-link" data-tab="password" onclick="cdgInfoTab('password')">
                    <i class="bi bi-shield-lock"></i> Şifre Değiştir
                </button>
                <button type="button" class="cdg-info-side-link" data-tab="security" onclick="cdgInfoTab('security')">
                    <i class="bi bi-key"></i> Güvenlik Sorusu
                </button>
                <button type="button" class="cdg-info-side-link" data-tab="twofa" onclick="cdgInfoTab('twofa')">
                    <i class="bi bi-shield-shaded"></i> 2 Adımlı Doğrulama
                </button>
            </div>

            <div class="cdg-info-side-group">
                <div class="cdg-info-side-title">Ödeme</div>
                <button type="button" class="cdg-info-side-link" data-tab="cards" onclick="cdgInfoTab('cards')">
                    <i class="bi bi-credit-card"></i> Kayıtlı Kartlar
                </button>
            </div>

            <div class="cdg-info-side-group">
                <div class="cdg-info-side-title">Yasal</div>
                <button type="button" class="cdg-info-side-link" data-tab="kvkk" onclick="cdgInfoTab('kvkk')">
                    <i class="bi bi-file-earmark-shield"></i> KVKK / GDPR
                </button>
                <?php
                $cdg_show_docvrf = isset($remainingVerifications) && is_array($remainingVerifications) && !empty($remainingVerifications['document_filters']);
                if($cdg_show_docvrf):
                ?>
                <button type="button" class="cdg-info-side-link cdg-info-side-link-warning" data-tab="docvrf" onclick="cdgInfoTab('docvrf')">
                    <i class="bi bi-shield-exclamation"></i> Belge Doğrulama
                    <span class="cdg-info-side-badge">!</span>
                </button>
                <?php endif; ?>
            </div>
        </aside>

        <!-- SAĞ: CONTENT AREA -->
        <main class="cdg-info-main">

<!-- ESKİ TAB NAV (gizli — fallback için kalsın) -->
<div class="cdg-info-tabs" style="display:none;">
    <button type="button" class="cdg-info-tab active" data-tab="profile"></button>
    <button type="button" class="cdg-info-tab" data-tab="addresses"></button>
    <button type="button" class="cdg-info-tab" data-tab="preferences"></button>
    <button type="button" class="cdg-info-tab" data-tab="password"></button>
    <button type="button" class="cdg-info-tab" data-tab="security"></button>
    <button type="button" class="cdg-info-tab" data-tab="twofa"></button>
    <button type="button" class="cdg-info-tab" data-tab="cards"></button>
    <button type="button" class="cdg-info-tab" data-tab="kvkk"></button>
    <?php
    $cdg_show_docvrf = isset($remainingVerifications) && is_array($remainingVerifications) && !empty($remainingVerifications['document_filters']);
    if($cdg_show_docvrf):
    ?>
    <button type="button" class="cdg-info-tab" data-tab="docvrf"></button>
    <?php endif; ?>
</div>

<!-- TAB: PROFIL BILGILERI -->
<div class="cdg-info-pane" id="cdg-info-pane-profile" style="display:block;">
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-person-badge"></i> Profil Bilgileri</h3>
            <span style="font-size:11px;color:#94a3b8;">#<?php echo (int)($udata['id'] ?? 0); ?></span>
        </div>

        <?php if(isset($error) && $error): ?>
        <div class="cdg-alert cdg-alert-error" style="margin-bottom:14px;">
            <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        <?php if(isset($success) && $success): ?>
        <div class="cdg-alert cdg-alert-success" style="margin-bottom:14px;">
            <i class="bi bi-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <form id="ModifyAccountInfo" method="post" action="<?php echo htmlspecialchars($op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" autocomplete="off">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
            <input type="hidden" name="operation" value="ModifyAccountInfo">

            <!-- Hesap turu -->
            <?php if(!empty($editable['kind']) || $kind): ?>
            <div class="cdg-form-group">
                <label class="cdg-form-label">Hesap Turu</label>
                <div style="display:flex;gap:10px;">
                    <label style="display:flex;align-items:center;gap:6px;padding:10px 14px;border:2px solid <?php echo !$is_corporate ? '#2E3B4E' : '#e2e8f0'; ?>;border-radius:8px;cursor:pointer;flex:1;background:<?php echo !$is_corporate ? '#eff6ff' : '#fff'; ?>;">
                        <input type="radio" name="kind" value="individual" <?php echo !$is_corporate ? 'checked' : ''; ?> onchange="cdgInfoKind(this.value)">
                        <i class="bi bi-person"></i> <span style="font-size:13px;font-weight:600;">Bireysel</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;padding:10px 14px;border:2px solid <?php echo $is_corporate ? '#2E3B4E' : '#e2e8f0'; ?>;border-radius:8px;cursor:pointer;flex:1;background:<?php echo $is_corporate ? '#eff6ff' : '#fff'; ?>;">
                        <input type="radio" name="kind" value="corporate" <?php echo $is_corporate ? 'checked' : ''; ?> onchange="cdgInfoKind(this.value)">
                        <i class="bi bi-building"></i> <span style="font-size:13px;font-weight:600;">Kurumsal</span>
                    </label>
                </div>
            </div>
            <?php else: ?>
            <input type="hidden" name="kind" value="<?php echo htmlspecialchars($kind, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <?php endif; ?>

            <!-- Ad Soyad / Tam Ad -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">Ad Soyad <span style="color:#ef4444;">*</span></label>
                <input type="text" name="full_name" class="cdg-form-control" value="<?php echo htmlspecialchars($u('full_name'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo (!$editable['full_name'] && $u('full_name')) ? 'disabled' : ''; ?> required>
            </div>

            <!-- E-posta -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">
                    E-posta <span style="color:#ef4444;">*</span>
                    <?php if($verified_email): ?>
                    <span style="display:inline-block;margin-left:6px;color:#10b981;font-size:11px;font-weight:700;">
                        <i class="bi bi-patch-check-fill"></i> Onaylandi
                    </span>
                    <?php else: ?>
                    <span style="display:inline-block;margin-left:6px;color:#f59e0b;font-size:11px;font-weight:700;">
                        <i class="bi bi-exclamation-triangle-fill"></i> Onaylanmadi
                    </span>
                    <button type="button" onclick="cdgVrfOpen('email', <?php echo json_encode($u('email'), JSON_UNESCAPED_UNICODE); ?>)" style="margin-left:8px;background:#2E3B4E;color:#fff;border:0;padding:3px 10px;font-size:11px;font-weight:700;border-radius:5px;cursor:pointer;">
                        <i class="bi bi-shield-check"></i> Dogrula
                    </button>
                    <?php endif; ?>
                </label>
                <input type="email" name="email" class="cdg-form-control" value="<?php echo htmlspecialchars($u('email'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo (!$editable['email'] && $u('email')) ? 'disabled' : ''; ?> required>
            </div>

            <!-- Cep Telefonu (gsm) -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">
                    Cep Telefonu
                    <?php if($verified_gsm): ?>
                    <span style="display:inline-block;margin-left:6px;color:#10b981;font-size:11px;font-weight:700;">
                        <i class="bi bi-patch-check-fill"></i> Onaylandi
                    </span>
                    <?php elseif($gsm_full): ?>
                    <span style="display:inline-block;margin-left:6px;color:#f59e0b;font-size:11px;font-weight:700;">
                        <i class="bi bi-exclamation-triangle-fill"></i> Onaylanmadi
                    </span>
                    <button type="button" onclick="cdgVrfOpen('gsm', <?php echo json_encode($gsm_full, JSON_UNESCAPED_UNICODE); ?>)" style="margin-left:8px;background:#2E3B4E;color:#fff;border:0;padding:3px 10px;font-size:11px;font-weight:700;border-radius:5px;cursor:pointer;">
                        <i class="bi bi-shield-check"></i> Dogrula
                    </button>
                    <?php endif; ?>
                </label>
                <input type="text" id="cdg-info-gsm" class="cdg-form-control" value="<?php echo htmlspecialchars($gsm_full, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="+90 555 123 45 67" <?php echo (!$editable['gsm'] && $gsm_full) ? 'disabled' : ''; ?>>
                <input type="hidden" name="gsm" value="<?php echo htmlspecialchars($gsm_full, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            </div>

            <!-- Sabit Hat -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">Sabit Hat (Opsiyonel)</label>
                <input type="text" name="landline_phone" class="cdg-form-control" value="<?php echo htmlspecialchars($u('landline_phone'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="(0312) 123 45 67" <?php echo (!$editable['landline_phone'] && $u('landline_phone')) ? 'disabled' : ''; ?>>
            </div>

            <!-- Dogum Tarihi -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">Dogum Tarihi</label>
                <input type="date" name="birthday" class="cdg-form-control" value="<?php echo htmlspecialchars($u('birthday'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo (!$editable['birthday'] && $u('birthday')) ? 'disabled' : ''; ?>>
            </div>

            <!-- TC Kimlik No -->
            <div class="cdg-form-group cdg-info-individual" <?php echo $is_corporate ? 'style="display:none;"' : ''; ?>>
                <label class="cdg-form-label">TC Kimlik No</label>
                <input type="text" name="identity" class="cdg-form-control" value="<?php echo htmlspecialchars($u('identity'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" maxlength="11" pattern="[0-9]{11}" placeholder="11 haneli TC kimlik no" <?php echo (!$editable['identity'] && $u('identity')) ? 'disabled' : ''; ?>>
            </div>

            <!-- KURUMSAL ALANLAR -->
            <div class="cdg-info-corporate" <?php echo !$is_corporate ? 'style="display:none;"' : ''; ?>>
                <h4 style="margin:20px 0 12px;color:#0f172a;font-size:15px;font-weight:800;">
                    <i class="bi bi-building"></i> Şirket Bilgileri
                </h4>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Şirket Adı</label>
                    <input type="text" name="company_name" class="cdg-form-control" value="<?php echo htmlspecialchars($u('company_name'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                </div>

                <div class="cdg-form-row">
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Vergi Dairesi</label>
                        <input type="text" name="company_tax_office" class="cdg-form-control" value="<?php echo htmlspecialchars($u('company_tax_office'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                    </div>
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Vergi Numarasi</label>
                        <input type="text" name="company_tax_number" class="cdg-form-control" value="<?php echo htmlspecialchars($u('company_tax_number'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" maxlength="11">
                    </div>
                </div>
            </div>

            <!-- ADRES BILGILERI -->
            <h4 style="margin:24px 0 12px;color:#0f172a;font-size:15px;font-weight:800;">
                <i class="bi bi-geo-alt"></i> Adres Bilgileri
            </h4>

            <!-- Ulke -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">Ülke</label>
                <?php if(!empty($country_list)): ?>
                <select name="country_id" class="cdg-form-control" id="cdg-info-country" onchange="cdgInfoLoadCities(this.value)">
                    <option value="">Ülke seçiniz</option>
                    <?php foreach($country_list as $c):
                        $c_id = is_array($c) ? ($c['id'] ?? '') : (string)$c;
                        $c_name = is_array($c) ? ($c['name'] ?? $c_id) : (string)$c;
                        $sel = ($u('country_id') == $c_id) ? 'selected' : '';
                    ?>
                    <option value="<?php echo htmlspecialchars($c_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($c_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <input type="text" name="country_id" class="cdg-form-control" value="<?php echo htmlspecialchars($u('country_id', '215'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Ülke ID (varsayılan: Türkiye)">
                <?php endif; ?>
            </div>

            <!-- Şehir / İlçe -->
            <div class="cdg-form-row">
                <div class="cdg-form-group">
                    <label class="cdg-form-label">Şehir</label>
                    <input type="text" name="city" id="cdg-info-city" class="cdg-form-control" value="<?php echo htmlspecialchars($u('city'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                </div>
                <div class="cdg-form-group">
                    <label class="cdg-form-label">Ilce</label>
                    <input type="text" name="counti" id="cdg-info-counti" class="cdg-form-control" value="<?php echo htmlspecialchars($u('counti'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                </div>
                <div class="cdg-form-group">
                    <label class="cdg-form-label">Posta Kodu</label>
                    <input type="text" name="zipcode" class="cdg-form-control" value="<?php echo htmlspecialchars($u('zipcode'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Adres -->
            <div class="cdg-form-group">
                <label class="cdg-form-label">Adres</label>
                <textarea name="address" class="cdg-form-control" rows="3"><?php echo htmlspecialchars($u('address'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></textarea>
            </div>

            <!-- OZEL ALANLAR (cfields) -->
            <?php if(!empty($cfields_list)): ?>
            <h4 style="margin:24px 0 12px;color:#0f172a;font-size:15px;font-weight:800;">
                <i class="bi bi-list-check"></i> Ek Bilgiler
            </h4>
            <?php foreach($cfields_list as $field):
                $f_id = $field['id'] ?? 0;
                $f_title = $field['title'] ?? '';
                $f_type = $field['type'] ?? 'text';
                $f_required = !empty($field['required']);
                $f_value = $field['value'] ?? '';
                if(!$f_id || !$f_title) continue;
            ?>
            <div class="cdg-form-group">
                <label class="cdg-form-label"><?php echo htmlspecialchars($f_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?> <?php if($f_required): ?><span style="color:#ef4444;">*</span><?php endif; ?></label>
                <?php if($f_type === 'textarea'): ?>
                <textarea name="cfields[<?php echo $f_id; ?>]" class="cdg-form-control" rows="3" <?php echo $f_required ? 'required' : ''; ?>><?php echo htmlspecialchars($f_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></textarea>
                <?php elseif($f_type === 'select' && !empty($field['options'])): ?>
                <select name="cfields[<?php echo $f_id; ?>]" class="cdg-form-control" <?php echo $f_required ? 'required' : ''; ?>>
                    <option value="">Seciniz</option>
                    <?php foreach($field['options'] as $opt): ?>
                    <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $opt == $f_value ? 'selected' : ''; ?>><?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <input type="text" name="cfields[<?php echo $f_id; ?>]" class="cdg-form-control" value="<?php echo htmlspecialchars($f_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $f_required ? 'required' : ''; ?>>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <div style="margin-top:24px;display:flex;justify-content:flex-end;">
                <button type="submit" class="cdg-btn cdg-btn-primary" style="padding:12px 24px;">
                    <i class="bi bi-check2"></i> Bilgileri Guncelle
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB: ADRESLERIM -->
<div class="cdg-info-pane" id="cdg-info-pane-addresses" style="display:none;">
    <?php
    $addr_inc = __DIR__ . DS . 'inc' . DS . 'ac-address-management.php';
    if(file_exists($addr_inc)) {
        include $addr_inc;
    } else {
        echo '<div class="cdg-card"><div class="cdg-card-head"><h3><i class="bi bi-geo-alt"></i> Adreslerim</h3></div>';
        echo '<div style="text-align:center;padding:30px;color:#94a3b8;">Adres yonetimi modulu yuklenemedi.</div></div>';
    }
    ?>
</div>

<!-- TAB: TERCIHLER -->
<div class="cdg-info-pane" id="cdg-info-pane-preferences" style="display:none;">
    <?php
    $pref_inc = __DIR__ . DS . 'inc' . DS . 'ac-preferences.php';
    if(file_exists($pref_inc)) {
        include $pref_inc;
    } else {
        echo '<div class="cdg-card"><div class="cdg-card-head"><h3><i class="bi bi-sliders"></i> Tercihler</h3></div>';
        echo '<div style="text-align:center;padding:30px;color:#94a3b8;">Tercihler modulu yuklenemedi.</div></div>';
    }
    ?>
</div>

<!-- TAB: SIFRE DEGISTIR -->
<div class="cdg-info-pane" id="cdg-info-pane-password" style="display:none;">
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-shield-lock"></i> Şifre Değiştir</h3>
        </div>

        <div class="cdg-alert cdg-alert-info" style="margin-bottom:16px;">
            <i class="bi bi-info-circle"></i>
            Yeni sifreniz en az 6 karakter olmalidir. Guclu bir sifre icin buyuk-kucuk harf, rakam ve ozel karakter kullanin.
        </div>

        <form id="ModifyPassword" method="post" action="<?php echo htmlspecialchars($op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" autocomplete="off">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
            <input type="hidden" name="operation" value="ModifyPassword">

            <div class="cdg-form-group">
                <label class="cdg-form-label">Yeni Şifre <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password" id="cdg-info-pw1" class="cdg-form-control" placeholder="En az 6 karakter" required minlength="6" autocomplete="new-password" oninput="cdgInfoPwStrength()">
                    <button type="button" onclick="cdgInfoPwToggle('cdg-info-pw1','cdg-info-eye1')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:#64748b;cursor:pointer;padding:6px;">
                        <i class="bi bi-eye" id="cdg-info-eye1"></i>
                    </button>
                </div>
                <div id="cdg-info-pw-bars" style="display:flex;gap:4px;margin-top:6px;">
                    <div class="cdg-info-pw-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                    <div class="cdg-info-pw-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                    <div class="cdg-info-pw-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                    <div class="cdg-info-pw-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                </div>
                <div id="cdg-info-pw-text" style="font-size:11px;color:#64748b;margin-top:4px;">Şifre gücü: -</div>
            </div>

            <div class="cdg-form-group">
                <label class="cdg-form-label">Yeni Şifre (Tekrar) <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password_again" id="cdg-info-pw2" class="cdg-form-control" placeholder="Şifreyi tekrar girin" required minlength="6" autocomplete="new-password" oninput="cdgInfoPwMatch()">
                    <button type="button" onclick="cdgInfoPwToggle('cdg-info-pw2','cdg-info-eye2')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:#64748b;cursor:pointer;padding:6px;">
                        <i class="bi bi-eye" id="cdg-info-eye2"></i>
                    </button>
                </div>
                <div id="cdg-info-pw-match" style="font-size:11px;margin-top:4px;display:none;"></div>
            </div>

            <div style="margin-top:24px;display:flex;justify-content:flex-end;">
                <button type="submit" class="cdg-btn cdg-btn-success" style="padding:12px 24px;">
                    <i class="bi bi-shield-check"></i> Şifreyi Değiştir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB: GUVENLIK -->
<div class="cdg-info-pane" id="cdg-info-pane-security" style="display:none;">
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-key"></i> Guvenlik Sorusu</h3>
        </div>

        <div class="cdg-alert cdg-alert-warning" style="margin-bottom:16px;">
            <i class="bi bi-info-circle"></i>
            Sifrenizi unuttugunuzda hesabinizi kurtarmak icin guvenlik sorusu kullanilir. Sadece sizin bildiginiz bir cevap secin.
        </div>

        <form method="post" action="<?php echo htmlspecialchars($op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" autocomplete="off">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
            <input type="hidden" name="operation" value="ModifyAccountInfo">
            <input type="hidden" name="kind" value="<?php echo htmlspecialchars($kind, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($u('full_name'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($u('email'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="gsm" value="<?php echo htmlspecialchars($gsm_full, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="country_id" value="<?php echo htmlspecialchars($u('country_id'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="city" value="<?php echo htmlspecialchars($u('city'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="counti" value="<?php echo htmlspecialchars($u('counti'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="zipcode" value="<?php echo htmlspecialchars($u('zipcode'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
            <input type="hidden" name="address" value="<?php echo htmlspecialchars($u('address'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">

            <div class="cdg-form-group">
                <label class="cdg-form-label">Guvenlik Sorusu</label>
                <input type="text" name="security_question" class="cdg-form-control" value="<?php echo htmlspecialchars($u('security_question'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Örnek: İlk evcil hayvanımın ismi nedir?">
            </div>

            <div class="cdg-form-group">
                <label class="cdg-form-label">Güvenlik Sorusu Cevabı</label>
                <input type="password" name="security_question_answer" class="cdg-form-control" placeholder="Cevabınız (şifreli saklanır)" autocomplete="new-password">
            </div>

            <div style="margin-top:20px;display:flex;justify-content:flex-end;">
                <button type="submit" class="cdg-btn cdg-btn-primary">
                    <i class="bi bi-check2"></i> Guvenligi Guncelle
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB: BELGE DOGRULAMA -->
<?php if(isset($remainingVerifications) && is_array($remainingVerifications) && !empty($remainingVerifications['document_filters'])): ?>
<div class="cdg-info-pane" id="cdg-info-pane-docvrf" style="display:none;">
    <?php include __DIR__ . DS . 'inc' . DS . 'ac-document-verification.php'; ?>
</div>
<?php endif; ?>

<!-- TAB: 2 ADIMLI DOĞRULAMA (2FA) -->
<div class="cdg-info-pane" id="cdg-info-pane-twofa" style="display:none;">
    <?php
    $tfa_enabled = isset($udata['two_factor_status']) ? (bool)$udata['two_factor_status'] : (isset($two_factor_status) ? (bool)$two_factor_status : false);
    $tfa_method = $udata['two_factor_method'] ?? ($two_factor_method ?? 'totp');
    ?>
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-shield-shaded"></i> 2 Adımlı Doğrulama</h3>
            <span class="cdg-pd2-badge cdg-pd2-badge-<?php echo $tfa_enabled ? 'success' : 'info'; ?>">
                <i class="bi bi-<?php echo $tfa_enabled ? 'check-circle-fill' : 'x-circle'; ?>"></i> <?php echo $tfa_enabled ? 'Aktif' : 'Pasif'; ?>
            </span>
        </div>
        <div style="padding:18px;">
            <div class="cdg-pd2-alert cdg-pd2-alert-<?php echo $tfa_enabled ? 'success' : 'warning'; ?>" style="margin-bottom:18px;">
                <i class="bi bi-<?php echo $tfa_enabled ? 'shield-fill-check' : 'shield-exclamation'; ?>"></i>
                <div>
                    <?php if($tfa_enabled): ?>
                    <strong>Hesabınız 2 adımlı doğrulama ile korunuyor.</strong><br>
                    Giriş yaparken Google Authenticator veya benzeri bir uygulamadan kod girmeniz gerekir.
                    <?php else: ?>
                    <strong>Hesap güvenliğiniz için 2FA önerilir!</strong><br>
                    2 adımlı doğrulama, şifreniz çalınsa bile hesabınızı korur. Google Authenticator, Authy, Microsoft Authenticator veya benzeri bir uygulama kullanın.
                    <?php endif; ?>
                </div>
            </div>

            <?php if(!$tfa_enabled): ?>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:18px;text-align:center;">
                <div style="margin-bottom:14px;">
                    <i class="bi bi-shield-shaded" style="font-size:48px;color:#2E3B4E;"></i>
                </div>
                <h4 style="margin:0 0 8px;font-size:16px;font-weight:700;">2FA Şimdi Etkinleştir</h4>
                <p style="margin:0 0 16px;color:#64748b;font-size:13.5px;line-height:1.6;">
                    Aktivasyon için bir kez QR kodu okuyup uygulamadan tek seferlik kod girersiniz.
                </p>
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary" onclick="cdgInfo2FA.enable()">
                    <i class="bi bi-plus-circle"></i> 2FA'yı Etkinleştir
                </button>
            </div>
            <?php else: ?>
            <ul class="cdg-pd2-info">
                <li><span class="cdg-pd2-info-label">Yöntem</span><span class="cdg-pd2-info-value">
                    <?php
                    $method_labels = ['totp' => 'TOTP (Authenticator App)', 'sms' => 'SMS', 'email' => 'E-posta'];
                    echo htmlspecialchars($method_labels[$tfa_method] ?? $tfa_method, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    ?>
                </span></li>
                <li><span class="cdg-pd2-info-label">Aktivasyon Tarihi</span><span class="cdg-pd2-info-value">
                    <?php echo isset($udata['two_factor_date']) ? htmlspecialchars(date('d.m.Y', strtotime($udata['two_factor_date'])), ENT_QUOTES) : '-'; ?>
                </span></li>
            </ul>
            <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;">
                <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline" onclick="cdgInfo2FA.regenerate()">
                    <i class="bi bi-arrow-repeat"></i> Yedek Kodları Yenile
                </button>
                <button type="button" class="cdg-pd2-btn" style="background:#dc2626;color:#fff;border:0;" onclick="cdgInfo2FA.disable()">
                    <i class="bi bi-shield-slash"></i> 2FA'yı Devre Dışı Bırak
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- TAB: KAYITLI KARTLAR -->
<div class="cdg-info-pane" id="cdg-info-pane-cards" style="display:none;">
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-credit-card"></i> Kayıtlı Kartlarım</h3>
        </div>
        <div style="padding:18px;">
            <div class="cdg-pd2-alert cdg-pd2-alert-info" style="margin-bottom:14px;">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    Kayıtlı kartlarınızı buradan görüntüleyebilir, varsayılan kartı belirleyebilir veya otomatik ödemeyi açabilirsiniz.
                    Kart bilgileri <strong>PCI-DSS uyumlu ödeme sağlayıcımızda</strong> şifrelenerek saklanır.
                </div>
            </div>

            <?php if(isset($stored_cards) && is_array($stored_cards) && !empty($stored_cards)): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
                <?php foreach($stored_cards as $card):
                    $card_id = $card['id'] ?? 0;
                    $card_brand = strtolower($card['brand'] ?? ($card['type'] ?? 'visa'));
                    $card_last4 = $card['last4'] ?? ($card['number'] ?? '****');
                    $card_holder = $card['holder'] ?? ($card['name'] ?? '');
                    $card_default = !empty($card['default']);
                    $card_autopay = !empty($card['auto_payment']);
                    $card_exp = $card['exp_month'] ?? '';
                    $card_exp .= $card_exp && !empty($card['exp_year']) ? '/' . substr($card['exp_year'], -2) : '';
                ?>
                <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:14px;padding:20px;color:#fff;position:relative;overflow:hidden;<?php echo $card_default ? 'box-shadow:0 0 0 3px #f59e0b;' : ''; ?>">
                    <?php if($card_default): ?>
                    <span style="position:absolute;top:10px;right:10px;background:#f59e0b;color:#422006;font-size:10px;font-weight:800;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.5px;">Varsayılan</span>
                    <?php endif; ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                        <i class="bi bi-credit-card-2-front" style="font-size:28px;color:#fde047;"></i>
                        <span style="font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.80);">
                            <?php echo htmlspecialchars(ucfirst($card_brand), ENT_QUOTES); ?>
                        </span>
                    </div>
                    <div style="font-size:18px;font-weight:700;letter-spacing:2px;margin-bottom:14px;font-family:monospace;">
                        •••• •••• •••• <?php echo htmlspecialchars(substr((string)$card_last4, -4), ENT_QUOTES); ?>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-end;">
                        <div>
                            <div style="font-size:10px;color:rgba(255,255,255,0.50);text-transform:uppercase;letter-spacing:0.5px;">Kart Sahibi</div>
                            <div style="font-size:13px;font-weight:600;"><?php echo htmlspecialchars($card_holder ?: '—', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        </div>
                        <?php if($card_exp): ?>
                        <div>
                            <div style="font-size:10px;color:rgba(255,255,255,0.50);text-transform:uppercase;letter-spacing:0.5px;">SKT</div>
                            <div style="font-size:13px;font-weight:600;"><?php echo htmlspecialchars($card_exp, ENT_QUOTES); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.10);display:flex;gap:6px;flex-wrap:wrap;">
                        <?php if(!$card_default): ?>
                        <button type="button" onclick="cdgInfoCard.setDefault(<?php echo (int)$card_id; ?>)" style="background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.30);color:#fff;font-size:11px;padding:5px 10px;border-radius:6px;cursor:pointer;">
                            <i class="bi bi-star"></i> Varsayılan Yap
                        </button>
                        <?php endif; ?>
                        <button type="button" onclick="cdgInfoCard.toggleAutopay(<?php echo (int)$card_id; ?>)" style="background:<?php echo $card_autopay ? '#f59e0b' : 'rgba(255,255,255,0.10)'; ?>;border:1px solid <?php echo $card_autopay ? '#f59e0b' : 'rgba(255,255,255,0.30)'; ?>;color:#fff;font-size:11px;padding:5px 10px;border-radius:6px;cursor:pointer;">
                            <i class="bi bi-arrow-repeat"></i> Otomatik <?php echo $card_autopay ? 'Açık' : 'Kapalı'; ?>
                        </button>
                        <button type="button" onclick="cdgInfoCard.remove(<?php echo (int)$card_id; ?>)" style="background:rgba(220,38,38,0.20);border:1px solid rgba(220,38,38,0.50);color:#fca5a5;font-size:11px;padding:5px 10px;border-radius:6px;cursor:pointer;margin-left:auto;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="cdg-pd2-empty">
                <i class="bi bi-credit-card-2-front"></i>
                <h4>Kayıtlı Kartınız Yok</h4>
                <p>Bir sonraki ödemenizde "Kartı kaydet" seçeneğini işaretleyerek kart ekleyebilirsiniz.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // === Saklı Kart Detaylı Yönetim ===
    $stored_cards_inc = __DIR__ . DS . 'inc' . DS . 'ac-stored-cards.php';
    if(file_exists($stored_cards_inc)) include $stored_cards_inc;
    ?>
</div>

<!-- TAB: KVKK / GDPR -->
<div class="cdg-info-pane" id="cdg-info-pane-kvkk" style="display:none;">
    <div class="cdg-card">
        <div class="cdg-card-head">
            <h3><i class="bi bi-file-earmark-shield"></i> KVKK / GDPR Veri Hakları</h3>
        </div>
        <div style="padding:18px;">
            <div class="cdg-pd2-alert cdg-pd2-alert-info" style="margin-bottom:18px;">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <strong>Kişisel Verilerin Korunması Kanunu (KVKK)</strong> ve <strong>AB Genel Veri Koruma Tüzüğü (GDPR)</strong> kapsamında, hesabınızla ilgili aşağıdaki taleplerde bulunabilirsiniz.
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;">
                <!-- Veri İndir -->
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:18px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:42px;height:42px;background:linear-gradient(135deg,#CFFAFE,#A5F3FC);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2E3B4E;font-size:20px;flex-shrink:0;">
                            <i class="bi bi-download"></i>
                        </div>
                        <h4 style="margin:0;font-size:14px;font-weight:700;">Verilerimi İndir</h4>
                    </div>
                    <p style="margin:0 0 12px;font-size:12.5px;color:#64748b;line-height:1.6;">
                        Hesabınızla ilgili tüm verilerin (profil, faturalar, hizmetler, talepler) ZIP dosyasını indirin.
                    </p>
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-primary cdg-pd2-btn-sm" onclick="cdgInfoKvkk.export()">
                        <i class="bi bi-cloud-arrow-down"></i> İndirme Talebi Oluştur
                    </button>
                </div>

                <!-- Veri Düzeltme -->
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:18px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:42px;height:42px;background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#92400e;font-size:20px;flex-shrink:0;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h4 style="margin:0;font-size:14px;font-weight:700;">Verilerimi Düzelt</h4>
                    </div>
                    <p style="margin:0 0 12px;font-size:12.5px;color:#64748b;line-height:1.6;">
                        Hatalı veya eksik kişisel verilerinizin düzeltilmesini talep edin.
                    </p>
                    <button type="button" class="cdg-pd2-btn cdg-pd2-btn-outline cdg-pd2-btn-sm" onclick="cdgInfoKvkk.correction()">
                        <i class="bi bi-pencil"></i> Düzeltme Talebi
                    </button>
                </div>

                <!-- Hesap Sil -->
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:18px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:42px;height:42px;background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:20px;flex-shrink:0;">
                            <i class="bi bi-person-x"></i>
                        </div>
                        <h4 style="margin:0;font-size:14px;font-weight:700;color:#991b1b;">Unutulma Hakkı</h4>
                    </div>
                    <p style="margin:0 0 12px;font-size:12.5px;color:#7f1d1d;line-height:1.6;">
                        Hesabınızın ve tüm verilerinizin <strong>kalıcı olarak silinmesini</strong> talep edin. Aktif hizmetler iptal edilecektir.
                    </p>
                    <button type="button" class="cdg-pd2-btn" style="background:#dc2626;color:#fff;border:0;font-size:12px;padding:6px 12px;" onclick="cdgInfoKvkk.deleteAccount()">
                        <i class="bi bi-trash"></i> Hesabımı Sil
                    </button>
                </div>

                <!-- Pazarlama İletilerini Durdur -->
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:18px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:42px;height:42px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#065f46;font-size:20px;flex-shrink:0;">
                            <i class="bi bi-bell-slash"></i>
                        </div>
                        <h4 style="margin:0;font-size:14px;font-weight:700;">Pazarlama İletilerini Durdur</h4>
                    </div>
                    <p style="margin:0 0 12px;font-size:12.5px;color:#64748b;line-height:1.6;">
                        Promosyon, kampanya ve duyuru e-postalarını almayı durdurun. (Kritik hizmet uyarıları gelmeye devam eder.)
                    </p>
                    <a href="#cdg-info-pane-preferences" class="cdg-pd2-btn cdg-pd2-btn-outline cdg-pd2-btn-sm" onclick="cdgInfoTab('preferences');return false;">
                        <i class="bi bi-sliders"></i> Tercihlere Git
                    </a>
                </div>
            </div>

            <div style="margin-top:18px;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;color:#64748b;line-height:1.6;">
                <i class="bi bi-info-circle"></i> Talepleriniz <strong>30 iş günü</strong> içinde değerlendirilir. Detaylı bilgi için
                <a href="/gizlilik-politikasi.html" style="color:#2E3B4E;font-weight:600;">Gizlilik Politikamızı</a>
                veya
                <a href="/kvkk.html" style="color:#2E3B4E;font-weight:600;">KVKK Aydınlatma Metnimizi</a>
                inceleyin.
            </div>
        </div>
    </div>

    <?php
    // === KVKK / GDPR Detaylı Talepler (Hesap Sil / Anonimleştir) ===
    $gdpr_inc = __DIR__ . DS . 'inc' . DS . 'ac-gdpr.php';
    if(file_exists($gdpr_inc)) include $gdpr_inc;
    ?>
</div>

        </main><!-- /cdg-info-main -->
    </div><!-- /cdg-info-shell-body -->
</div><!-- /cdg-info-shell -->

<script>
// 2FA İşlemleri
window.cdgInfo2FA = {
    _post: function(op, data){
        var fd = new FormData();
        fd.append('operation', op);
        if(data) Object.keys(data).forEach(function(k){ fd.append(k, data[k]); });
        return fetch('<?php echo htmlspecialchars($links["controller"] ?? "", ENT_QUOTES); ?>', { method: 'POST', body: fd, credentials: 'same-origin' }).then(function(r){ return r.text(); }).then(function(t){ try { return JSON.parse(t); } catch(e) { return null; } });
    },
    enable: function(){
        if(!confirm('2 Adımlı Doğrulama (2FA) etkinleştirilsin mi? Aktif olduktan sonra her girişte uygulamadan kod gireceksiniz.')) return;
        this._post('enable_two_factor').then(function(r){
            if(r && r.qr_url) {
                window.open(r.qr_url, '_blank');
                alert('Açılan sayfada QR kodu Google Authenticator vb. uygulamayla okutun ve verilen 6 haneli kodu girin.');
            } else if(r && r.message) alert(r.message);
            else location.reload();
        });
    },
    disable: function(){
        if(!confirm('2FA devre dışı bırakılsın mı? Hesabınız sadece şifreyle korunacak.')) return;
        this._post('disable_two_factor').then(function(r){ location.reload(); });
    },
    regenerate: function(){
        if(!confirm('Yedek kodları yenileyelim mi? Eski kodlar geçersiz olacak.')) return;
        this._post('regenerate_two_factor_backup').then(function(r){
            if(r && r.codes) prompt('Yeni yedek kodlarınız (güvenli yere kaydedin):', r.codes.join(', '));
            else location.reload();
        });
    }
};

// Kayıtlı Kart İşlemleri
window.cdgInfoCard = {
    _post: function(op, cardId){
        var fd = new FormData();
        fd.append('operation', op);
        fd.append('card_id', cardId);
        return fetch('<?php echo htmlspecialchars($links["controller"] ?? "", ENT_QUOTES); ?>', { method: 'POST', body: fd, credentials: 'same-origin' }).then(function(r){ return r.text(); });
    },
    setDefault: function(id){
        if(!confirm('Bu kart varsayılan ödeme kartı olarak ayarlansın mı?')) return;
        this._post('stored_card_as_default', id).then(function(){ location.reload(); });
    },
    toggleAutopay: function(id){
        this._post('stored_card_auto_payment', id).then(function(){ location.reload(); });
    },
    remove: function(id){
        if(!confirm('Bu kartı silmek istediğinize emin misiniz?')) return;
        this._post('stored_card_remove', id).then(function(){ location.reload(); });
    }
};

// KVKK / GDPR İşlemleri
window.cdgInfoKvkk = {
    _post: function(type){
        var fd = new FormData();
        fd.append('operation', 'gdpr_request');
        fd.append('type', type);
        return fetch('<?php echo htmlspecialchars($links["controller"] ?? "", ENT_QUOTES); ?>', { method: 'POST', body: fd, credentials: 'same-origin' }).then(function(r){ return r.text(); });
    },
    export: function(){
        if(!confirm('Tüm verilerinizin ZIP arşivinin oluşturulması için talep gönderilsin mi? Hazırlandığında e-posta ile bildirilecek.')) return;
        this._post('export').then(function(){ alert('Talebiniz alındı. Hazırlandığında e-postanıza bildirim gelecek.'); });
    },
    correction: function(){
        var note = prompt('Lütfen düzeltilmesini istediğiniz bilgiyi yazın:');
        if(!note || note.trim().length < 5) return;
        var fd = new FormData();
        fd.append('operation', 'gdpr_request');
        fd.append('type', 'correction');
        fd.append('note', note);
        fetch('<?php echo htmlspecialchars($links["controller"] ?? "", ENT_QUOTES); ?>', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(){ alert('Düzeltme talebiniz alındı. 30 iş günü içinde işleme alınacak.'); });
    },
    deleteAccount: function(){
        var confirm1 = confirm('⚠️ DİKKAT: Hesabınızı ve TÜM VERİLERİNİZİ kalıcı olarak silmek üzeresiniz!\n\n• Aktif hizmetler iptal edilecek\n• Faturalar ve geçmiş silinecek\n• Bu işlem GERİ ALINAMAZ\n\nDevam etmek istiyor musunuz?');
        if(!confirm1) return;
        var typed = prompt('Onaylamak için "HESABIMI SIL" yazın:');
        if(typed !== 'HESABIMI SIL') return alert('Onay metni hatalı, işlem iptal edildi.');
        this._post('delete_account').then(function(){ alert('Talebiniz alındı. 30 iş günü içinde hesabınız silinecek.'); });
    }
};
</script>

<style>
/* === KURUMSAL HESAP BİLGİLERİ SHELL === */
.cdg-info-shell {
    max-width: 100%;
    margin: 0 auto 24px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.04);
    overflow: hidden;
}
.cdg-info-shell-head {
    padding: 22px 26px;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.cdg-info-shell-head-left {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
}
.cdg-info-shell-icon {
    width: 60px; height: 60px;
    background: linear-gradient(135deg, #1A2332, #485A75);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(46,59,78,0.25);
    position: relative;
    overflow: hidden;
}
.cdg-info-shell-icon::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(253,224,71,0.15) 0%, transparent 60%);
}
.cdg-info-shell-icon span {
    color: #fff;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 1px;
    z-index: 1;
}
.cdg-info-shell-title {
    margin: 0 0 4px;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.cdg-info-shell-sub {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}
.cdg-info-shell-sub i { color: #94a3b8; }
.cdg-info-verified-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: #dcfce7;
    color: #15803d;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-info-pending-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-info-shell-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    text-decoration: none;
    transition: all 0.18s;
}
.cdg-info-shell-btn:hover {
    border-color: #2E3B4E;
    color: #2E3B4E;
    transform: translateY(-1px);
}
.cdg-info-shell-body {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 0;
    min-height: 600px;
}

/* === SOL SIDEBAR === */
.cdg-info-side {
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    padding: 18px 12px;
}
.cdg-info-side-group { margin-bottom: 18px; }
.cdg-info-side-group:last-child { margin-bottom: 0; }
.cdg-info-side-title {
    font-size: 10px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0 12px 8px;
    margin-bottom: 4px;
}
.cdg-info-side-link {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 12px;
    background: transparent;
    border: 0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
    text-align: left;
    margin-bottom: 2px;
}
.cdg-info-side-link i {
    font-size: 15px;
    color: #94a3b8;
    width: 18px;
    flex-shrink: 0;
}
.cdg-info-side-link:hover {
    background: #fff;
    color: #2E3B4E;
}
.cdg-info-side-link:hover i { color: #2E3B4E; }
.cdg-info-side-link.active {
    background: #fff;
    color: #2E3B4E;
    box-shadow: 0 2px 8px rgba(46,59,78,0.10);
    font-weight: 700;
}
.cdg-info-side-link.active i { color: #2E3B4E; }
.cdg-info-side-link-warning { color: #92400e !important; }
.cdg-info-side-link-warning i { color: #f59e0b !important; }
.cdg-info-side-badge {
    margin-left: auto;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 8px;
    font-weight: 800;
}

/* === SAĞ CONTENT === */
.cdg-info-main {
    padding: 24px 28px;
    background: #fff;
    min-width: 0;
}
.cdg-info-main .cdg-info-pane { animation: cdgInfoFadeIn 0.25s ease; }
@keyframes cdgInfoFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* === MOBILE: Sidebar üste taşır === */
@media (max-width: 900px) {
    .cdg-info-shell-body {
        grid-template-columns: 1fr;
    }
    .cdg-info-side {
        border-right: 0;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px;
        overflow-x: auto;
    }
    .cdg-info-side-group {
        display: inline-block;
        margin-right: 12px;
        margin-bottom: 0;
        vertical-align: top;
    }
    .cdg-info-side-title { display: none; }
    .cdg-info-side-link {
        display: inline-flex;
        width: auto;
        white-space: nowrap;
        margin: 4px;
    }
    .cdg-info-main { padding: 16px; }
}

<?php /* Eski TAB css - mobile fallback için */ ?>
.cdg-info-tab {
    flex: 1;
    min-width: 130px;
    padding: 10px 14px;
    background: transparent;
    border: 0;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}
.cdg-info-tab:hover { background: #fff; color: #0f172a; }
.cdg-info-tab.active { background: #fff; color: #2E3B4E; box-shadow: 0 2px 6px rgba(15,23,42,0.06); }
.cdg-info-tab i { margin-right: 4px; }
</style>

<script>
window.cdgInfoTab = function(tab) {
    document.querySelectorAll('.cdg-info-tab').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.cdg-info-side-link').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.cdg-info-pane').forEach(function(p){ p.style.display = 'none'; });
    var btn = document.querySelector('.cdg-info-tab[data-tab="'+tab+'"]');
    var sideBtn = document.querySelector('.cdg-info-side-link[data-tab="'+tab+'"]');
    var pane = document.getElementById('cdg-info-pane-'+tab);
    if(btn) btn.classList.add('active');
    if(sideBtn) sideBtn.classList.add('active');
    if(pane) pane.style.display = 'block';
    // Mobile: aktif tab'ın görünür olmasını sağla
    if(window.innerWidth < 900 && pane) {
        pane.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.cdgInfoKind = function(kind) {
    var ind = document.querySelectorAll('.cdg-info-individual');
    var cor = document.querySelectorAll('.cdg-info-corporate');
    if(kind === 'corporate') {
        ind.forEach(function(e){ e.style.display = 'none'; });
        cor.forEach(function(e){ e.style.display = 'block'; });
    } else {
        ind.forEach(function(e){ e.style.display = 'block'; });
        cor.forEach(function(e){ e.style.display = 'none'; });
    }
    // Radio kart vurgu
    document.querySelectorAll('input[name="kind"]').forEach(function(r){
        var label = r.closest('label');
        if(!label) return;
        if(r.checked) {
            label.style.borderColor = '#2E3B4E';
            label.style.background = '#eff6ff';
        } else {
            label.style.borderColor = '#e2e8f0';
            label.style.background = '#fff';
        }
    });
};

// GSM input -> hidden gsm sync
(function(){
    var gsmInput = document.getElementById('cdg-info-gsm');
    var gsmHidden = gsmInput ? gsmInput.parentNode.querySelector('input[type="hidden"][name="gsm"]') : null;
    if(gsmInput && gsmHidden) {
        gsmInput.addEventListener('input', function(){
            gsmHidden.value = gsmInput.value;
        });
    }
})();

// Sifre toggle/strength/match
window.cdgInfoPwToggle = function(inpId, eyeId) {
    var inp = document.getElementById(inpId);
    var eye = document.getElementById(eyeId);
    if(!inp || !eye) return;
    if(inp.type === 'password') { inp.type = 'text'; eye.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; eye.className = 'bi bi-eye'; }
};
window.cdgInfoPwStrength = function() {
    var pw = document.getElementById('cdg-info-pw1').value;
    var bars = document.querySelectorAll('.cdg-info-pw-bar');
    var text = document.getElementById('cdg-info-pw-text');
    var score = 0;
    if(pw.length >= 6) score++;
    if(pw.length >= 10) score++;
    if(/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    if(/[0-9]/.test(pw) && /[^A-Za-z0-9]/.test(pw)) score++;
    var labels = ['Cok zayif', 'Zayif', 'Orta', 'Iyi', 'Cok guclu'];
    var colors = ['#ef4444', '#f59e0b', '#fbbf24', '#10b981', '#059669'];
    bars.forEach(function(b, i) { b.style.background = (i < score) ? colors[score] : '#e2e8f0'; });
    text.textContent = 'Şifre gücü: ' + (pw.length === 0 ? '-' : labels[score] || '-');
    text.style.color = pw.length === 0 ? '#64748b' : colors[score];
};
window.cdgInfoPwMatch = function() {
    var pw1 = document.getElementById('cdg-info-pw1').value;
    var pw2 = document.getElementById('cdg-info-pw2').value;
    var match = document.getElementById('cdg-info-pw-match');
    if(!pw2) { match.style.display = 'none'; return; }
    match.style.display = 'block';
    if(pw1 === pw2) {
        match.innerHTML = '<i class="bi bi-check-circle"></i> Sifreler eslesiyor';
        match.style.color = '#10b981';
    } else {
        match.innerHTML = '<i class="bi bi-x-circle"></i> Sifreler eslesmiyor';
        match.style.color = '#ef4444';
    }
};
// Sifre formu submit kontrolü
(function(){
    var pwForm = document.getElementById('ModifyPassword');
    if(pwForm) {
        pwForm.addEventListener('submit', function(e){
            var pw1 = document.getElementById('cdg-info-pw1').value;
            var pw2 = document.getElementById('cdg-info-pw2').value;
            if(pw1 !== pw2) { e.preventDefault(); alert('Yeni sifreler eslesmiyor!'); return false; }
            if(pw1.length < 6) { e.preventDefault(); alert('Sifre en az 6 karakter olmali!'); return false; }
        });
    }
})();

// Cities/counties dynamic load
window.cdgInfoLoadCities = function(country_id) {
    if(!country_id || typeof MioAjax !== 'function') return;
    MioAjax({
        url: '<?php echo htmlspecialchars($op_link, ENT_QUOTES); ?>',
        type: 'post',
        data: { operation: 'getCities', country: country_id },
        result: function(r) {
            // City list dropdown'u bizim text input. WiseCP'nin select select istiyorsa burada doldurulur.
        }
    });
};
</script>

<?php
// === E-posta / GSM Dogrulama Modal === (sayfa sonu - modal)
$verify_inc = __DIR__ . DS . 'inc' . DS . 'ac-verify-modal.php';
if(file_exists($verify_inc)) include $verify_inc;
?>
