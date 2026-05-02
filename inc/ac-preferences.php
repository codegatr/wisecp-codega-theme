<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Tercihler Modulu
 *
 * Operation: ModifyPreferences
 * Field'lar: two_factor, email_notifications, sms_notifications, currency, lang
 *
 * WiseCP runtime: $udata, $lang_list, $operation_link
 */

$udata = isset($udata) && is_array($udata) ? $udata : [];
$lang_list = isset($lang_list) && is_array($lang_list) ? $lang_list : [];
$op_link = isset($operation_link) ? $operation_link : (isset($links['controller']) ? $links['controller'] : '');

$two_factor = !empty($udata['two_factor']);
$email_notif = !isset($udata['email_notifications']) || $udata['email_notifications'] == 1;
$sms_notif = !empty($udata['sms_notifications']);
$user_currency = $udata['currency'] ?? '';
$user_lang = $udata['lang'] ?? '';

// 2FA destegi
$two_factor_enabled = false;
if(class_exists('Config') && method_exists('Config','get')) {
    try { $two_factor_enabled = (bool)Config::get("options/two-factor-verification"); } catch(\Throwable $e) {}
}

// Para birimleri
$currencies = [];
if(class_exists('Money') && method_exists('Money','getCurrencies')) {
    try { $currencies = Money::getCurrencies(); } catch(\Throwable $e) {}
}
?>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-sliders"></i> Bildirim ve Tercihler</h3>
    </div>

    <form id="ModifyPreferences" method="post" action="<?php echo htmlspecialchars($op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" autocomplete="off">
        <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
        <input type="hidden" name="operation" value="ModifyPreferences">

        <!-- 2FA -->
        <?php if($two_factor_enabled): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:12px;">
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:2px;">
                    <i class="bi bi-shield-lock-fill" style="color:#1e40af;"></i> Iki Faktorlu Dogrulama (2FA)
                </div>
                <div style="font-size:12px;color:#64748b;">Hesap girisleri icin ek guvenlik katmani.</div>
            </div>
            <label class="cdg-switch">
                <input type="checkbox" name="two_factor" value="1" <?php echo $two_factor ? 'checked' : ''; ?>>
                <span class="cdg-slider"></span>
            </label>
        </div>
        <?php endif; ?>

        <!-- E-posta bildirimleri -->
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:12px;">
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:2px;">
                    <i class="bi bi-envelope-fill" style="color:#3b82f6;"></i> E-posta Bildirimleri
                </div>
                <div style="font-size:12px;color:#64748b;">Önemli olaylar, fatura ve güncellemeler e-posta ile gelsin.</div>
            </div>
            <label class="cdg-switch">
                <input type="checkbox" name="email_notifications" value="1" <?php echo $email_notif ? 'checked' : ''; ?>>
                <span class="cdg-slider"></span>
            </label>
        </div>

        <!-- SMS bildirimleri -->
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:18px;">
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:2px;">
                    <i class="bi bi-phone-vibrate-fill" style="color:#10b981;"></i> SMS Bildirimleri
                </div>
                <div style="font-size:12px;color:#64748b;">Acil durumlar ve onemli bildirimler SMS ile.</div>
            </div>
            <label class="cdg-switch">
                <input type="checkbox" name="sms_notifications" value="1" <?php echo $sms_notif ? 'checked' : ''; ?>>
                <span class="cdg-slider"></span>
            </label>
        </div>

        <!-- Para birimi -->
        <?php if(!empty($currencies)): ?>
        <div class="cdg-form-group">
            <label class="cdg-form-label">
                <i class="bi bi-currency-exchange"></i> Tercih Edilen Para Birimi
            </label>
            <select name="currency" class="cdg-form-control">
                <?php foreach($currencies as $curr):
                    $c_id = $curr['id'] ?? '';
                    $c_name = $curr['name'] ?? '';
                    $c_code = $curr['code'] ?? '';
                ?>
                <option value="<?php echo htmlspecialchars($c_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $c_id == $user_currency ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c_name . ' (' . $c_code . ')', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Dil -->
        <?php if(!empty($lang_list)): ?>
        <div class="cdg-form-group">
            <label class="cdg-form-label">
                <i class="bi bi-translate"></i> Dil
            </label>
            <select name="lang" class="cdg-form-control">
                <?php foreach($lang_list as $lang):
                    $l_key = $lang['key'] ?? '';
                    $l_name = $lang['name'] ?? '';
                    $l_cname = $lang['cname'] ?? $l_name;
                ?>
                <option value="<?php echo htmlspecialchars($l_key, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" <?php echo $l_key == $user_lang ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($l_cname . ' (' . $l_name . ')', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div style="margin-top:20px;display:flex;justify-content:flex-end;">
            <button type="submit" class="cdg-btn cdg-btn-primary">
                <i class="bi bi-check2"></i> Tercihleri Kaydet
            </button>
        </div>
    </form>
</div>

<style>
.cdg-switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
    flex-shrink: 0;
}
.cdg-switch input { opacity: 0; width: 0; height: 0; }
.cdg-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s;
    border-radius: 24px;
}
.cdg-slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: #fff;
    transition: 0.3s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.cdg-switch input:checked + .cdg-slider { background-color: #1e40af; }
.cdg-switch input:checked + .cdg-slider:before { transform: translateX(22px); }
</style>
