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
        global $links;
        if(isset($links) && is_array($links) && isset($links[$slug]) && $links[$slug]) return $links[$slug];
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

<!-- TAB NAVIGATION -->
<div class="cdg-info-tabs" style="display:flex;gap:4px;margin-bottom:20px;background:#f8fafc;padding:6px;border-radius:10px;flex-wrap:wrap;">
    <button type="button" class="cdg-info-tab active" data-tab="profile" onclick="cdgInfoTab('profile')">
        <i class="bi bi-person-badge"></i> Profil Bilgileri
    </button>
    <button type="button" class="cdg-info-tab" data-tab="addresses" onclick="cdgInfoTab('addresses')">
        <i class="bi bi-geo-alt"></i> Adreslerim
    </button>
    <button type="button" class="cdg-info-tab" data-tab="preferences" onclick="cdgInfoTab('preferences')">
        <i class="bi bi-sliders"></i> Tercihler
    </button>
    <button type="button" class="cdg-info-tab" data-tab="password" onclick="cdgInfoTab('password')">
        <i class="bi bi-shield-lock"></i> Sifre Degistir
    </button>
    <button type="button" class="cdg-info-tab" data-tab="security" onclick="cdgInfoTab('security')">
        <i class="bi bi-key"></i> Guvenlik
    </button>
    <?php
    // Belge dogrulama gerekiyorsa tab goster
    $cdg_show_docvrf = isset($remainingVerifications) && is_array($remainingVerifications) && !empty($remainingVerifications['document_filters']);
    if($cdg_show_docvrf):
    ?>
    <button type="button" class="cdg-info-tab" data-tab="docvrf" onclick="cdgInfoTab('docvrf')" style="background:#fef3c7;color:#92400e;">
        <i class="bi bi-shield-check"></i> Belge Dogrulama
        <span style="display:inline-block;background:#ef4444;color:#fff;font-size:10px;padding:1px 6px;border-radius:8px;margin-left:4px;font-weight:700;">!</span>
    </button>
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
                    <label style="display:flex;align-items:center;gap:6px;padding:10px 14px;border:2px solid <?php echo !$is_corporate ? '#1e40af' : '#e2e8f0'; ?>;border-radius:8px;cursor:pointer;flex:1;background:<?php echo !$is_corporate ? '#eff6ff' : '#fff'; ?>;">
                        <input type="radio" name="kind" value="individual" <?php echo !$is_corporate ? 'checked' : ''; ?> onchange="cdgInfoKind(this.value)">
                        <i class="bi bi-person"></i> <span style="font-size:13px;font-weight:600;">Bireysel</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;padding:10px 14px;border:2px solid <?php echo $is_corporate ? '#1e40af' : '#e2e8f0'; ?>;border-radius:8px;cursor:pointer;flex:1;background:<?php echo $is_corporate ? '#eff6ff' : '#fff'; ?>;">
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
                    <button type="button" onclick="cdgVrfOpen('email', <?php echo json_encode($u('email'), JSON_UNESCAPED_UNICODE); ?>)" style="margin-left:8px;background:#1e40af;color:#fff;border:0;padding:3px 10px;font-size:11px;font-weight:700;border-radius:5px;cursor:pointer;">
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
                    <button type="button" onclick="cdgVrfOpen('gsm', <?php echo json_encode($gsm_full, JSON_UNESCAPED_UNICODE); ?>)" style="margin-left:8px;background:#1e40af;color:#fff;border:0;padding:3px 10px;font-size:11px;font-weight:700;border-radius:5px;cursor:pointer;">
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
                    <i class="bi bi-building"></i> Sirket Bilgileri
                </h4>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Sirket Adi</label>
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
                <label class="cdg-form-label">Ulke</label>
                <?php if(!empty($country_list)): ?>
                <select name="country_id" class="cdg-form-control" id="cdg-info-country" onchange="cdgInfoLoadCities(this.value)">
                    <option value="">Ulke seciniz</option>
                    <?php foreach($country_list as $c):
                        $c_id = is_array($c) ? ($c['id'] ?? '') : (string)$c;
                        $c_name = is_array($c) ? ($c['name'] ?? $c_id) : (string)$c;
                        $sel = ($u('country_id') == $c_id) ? 'selected' : '';
                    ?>
                    <option value="<?php echo htmlspecialchars($c_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($c_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <input type="text" name="country_id" class="cdg-form-control" value="<?php echo htmlspecialchars($u('country_id', '215'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Ulke ID (varsayilan: Turkiye)">
                <?php endif; ?>
            </div>

            <!-- Sehir / Ilce -->
            <div class="cdg-form-row">
                <div class="cdg-form-group">
                    <label class="cdg-form-label">Sehir</label>
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
            <h3><i class="bi bi-shield-lock"></i> Sifre Degistir</h3>
        </div>

        <div class="cdg-alert cdg-alert-info" style="margin-bottom:16px;">
            <i class="bi bi-info-circle"></i>
            Yeni sifreniz en az 6 karakter olmalidir. Guclu bir sifre icin buyuk-kucuk harf, rakam ve ozel karakter kullanin.
        </div>

        <form id="ModifyPassword" method="post" action="<?php echo htmlspecialchars($op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" autocomplete="off">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
            <input type="hidden" name="operation" value="ModifyPassword">

            <div class="cdg-form-group">
                <label class="cdg-form-label">Yeni Sifre <span style="color:#ef4444;">*</span></label>
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
                <div id="cdg-info-pw-text" style="font-size:11px;color:#64748b;margin-top:4px;">Sifre gucu: -</div>
            </div>

            <div class="cdg-form-group">
                <label class="cdg-form-label">Yeni Sifre (Tekrar) <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password_again" id="cdg-info-pw2" class="cdg-form-control" placeholder="Sifreyi tekrar girin" required minlength="6" autocomplete="new-password" oninput="cdgInfoPwMatch()">
                    <button type="button" onclick="cdgInfoPwToggle('cdg-info-pw2','cdg-info-eye2')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:#64748b;cursor:pointer;padding:6px;">
                        <i class="bi bi-eye" id="cdg-info-eye2"></i>
                    </button>
                </div>
                <div id="cdg-info-pw-match" style="font-size:11px;margin-top:4px;display:none;"></div>
            </div>

            <div style="margin-top:24px;display:flex;justify-content:flex-end;">
                <button type="submit" class="cdg-btn cdg-btn-success" style="padding:12px 24px;">
                    <i class="bi bi-shield-check"></i> Sifreyi Degistir
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
                <input type="text" name="security_question" class="cdg-form-control" value="<?php echo htmlspecialchars($u('security_question'), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" placeholder="Ornek: Ilk evcil hayvanimin ismi nedir?">
            </div>

            <div class="cdg-form-group">
                <label class="cdg-form-label">Cevap</label>
                <input type="password" name="security_question_answer" class="cdg-form-control" placeholder="Cevabiniz (sifreli saklanir)" autocomplete="new-password">
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

<style>
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
.cdg-info-tab.active { background: #fff; color: #1e40af; box-shadow: 0 2px 6px rgba(15,23,42,0.06); }
.cdg-info-tab i { margin-right: 4px; }
</style>

<script>
window.cdgInfoTab = function(tab) {
    document.querySelectorAll('.cdg-info-tab').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.cdg-info-pane').forEach(function(p){ p.style.display = 'none'; });
    var btn = document.querySelector('.cdg-info-tab[data-tab="'+tab+'"]');
    var pane = document.getElementById('cdg-info-pane-'+tab);
    if(btn) btn.classList.add('active');
    if(pane) pane.style.display = 'block';
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
            label.style.borderColor = '#1e40af';
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
    text.textContent = 'Sifre gucu: ' + (pw.length === 0 ? '-' : labels[score] || '-');
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
// === Sakli Kart Yonetimi ===
$stored_cards_inc = __DIR__ . DS . 'inc' . DS . 'ac-stored-cards.php';
if(file_exists($stored_cards_inc)) include $stored_cards_inc;

// === KVKK / GDPR Talepleri ===
$gdpr_inc = __DIR__ . DS . 'inc' . DS . 'ac-gdpr.php';
if(file_exists($gdpr_inc)) include $gdpr_inc;

// === E-posta / GSM Dogrulama Modal ===
$verify_inc = __DIR__ . DS . 'inc' . DS . 'ac-verify-modal.php';
if(file_exists($verify_inc)) include $verify_inc;
