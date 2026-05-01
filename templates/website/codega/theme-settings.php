<?php
/**
 * CODEGA Theme - Admin Settings Panel
 * 
 * Rendered inside WiseCP admin > Themes > CODEGA > Settings.
 * The form fields are derived from theme-config.php's `settings_form` array.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$_settings = $this->config['settings']      ?? [];
$_form     = $this->config['settings_form'] ?? [];

// Use language from WiseCP context
$lang = Bootstrap::$lang->clang ?? 'tr';
$t = function ($field, $key) use ($lang) {
    if ($lang === 'tr' && isset($field[$key . '_tr'])) return $field[$key . '_tr'];
    return $field[$key] ?? '';
};
?>

<style>
.cgs-wrap { max-width: 920px; padding: 24px; font-family: 'Inter', -apple-system, sans-serif; }
.cgs-header { background: linear-gradient(135deg, #0a1628 0%, #142042 100%); color: white; padding: 32px; border-radius: 12px; margin-bottom: 24px; position: relative; overflow: hidden; }
.cgs-header::before { content: ''; position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(212,165,116,0.2), transparent 70%); }
.cgs-header h2 { color: white; font-family: 'Cormorant Garamond', serif; font-weight: 500; font-size: 1.75rem; margin: 0 0 6px; position: relative; }
.cgs-header h2 em { color: #d4a574; font-style: italic; }
.cgs-header p { color: rgba(255,255,255,0.7); font-size: 14px; margin: 0; position: relative; }
.cgs-section { background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; margin-bottom: 18px; }
.cgs-section h3 { color: #0a1628; font-family: 'Cormorant Garamond', serif; font-weight: 500; font-size: 1.25rem; margin: 0 0 4px; }
.cgs-section .desc { color: #5a6478; font-size: 13px; margin-bottom: 22px; }
.cgs-row { display: grid; grid-template-columns: 220px 1fr; gap: 20px; padding: 16px 0; border-top: 1px solid #f0f1f3; }
.cgs-row:first-of-type { border-top: 0; padding-top: 0; }
.cgs-label { font-size: 13px; font-weight: 600; color: #1a2238; }
.cgs-label .help { font-size: 11px; color: #8a92a5; font-weight: 400; margin-top: 4px; line-height: 1.5; display: block; }
.cgs-input, .cgs-select { width: 100%; max-width: 400px; padding: 9px 12px; font-size: 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit; }
.cgs-input:focus, .cgs-select:focus { outline: none; border-color: #d4a574; box-shadow: 0 0 0 3px rgba(212,165,116,0.15); }
.cgs-color { display: flex; gap: 10px; align-items: center; }
.cgs-color input[type=color] { width: 48px; height: 36px; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; padding: 0; }
.cgs-color input[type=text] { width: 130px; }
.cgs-checkbox { display: flex; align-items: center; gap: 10px; }
.cgs-checkbox input { width: 18px; height: 18px; cursor: pointer; accent-color: #d4a574; }
.cgs-actions { display: flex; gap: 12px; padding-top: 12px; }
.cgs-btn { padding: 11px 22px; font-size: 14px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; transition: all 200ms; font-family: inherit; }
.cgs-btn-primary { background: #d4a574; color: #0a1628; }
.cgs-btn-primary:hover { background: #b8895a; }
.cgs-btn-secondary { background: white; color: #1a2238; border: 1px solid #e5e7eb; }
</style>

<div class="cgs-wrap">

    <div class="cgs-header">
        <h2>CODEGA <em>Tema Ayarları</em></h2>
        <p>Marka renkleri, layout ve codega.com.tr SSO entegrasyonunu yönetin.</p>
    </div>

    <form method="POST" action="">
        <?php
        // CSRF token if available
        if (class_exists('CSRF') && method_exists('CSRF', 'token')) {
            echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(CSRF::token()) . '">';
        }
        ?>

        <!-- Visual section -->
        <div class="cgs-section">
            <h3>Görsel Kimlik</h3>
            <div class="desc">Marka renkleri ve görünüm tercihleri.</div>

            <?php foreach (['color1', 'color2', 'text-color', 'header-type', 'clientArea-type'] as $key): ?>
                <?php if (!isset($_form[$key])) continue; ?>
                <?php $field = $_form[$key]; $value = $_settings[$key] ?? ($field['default'] ?? ''); ?>
                <div class="cgs-row">
                    <label class="cgs-label">
                        <?= htmlspecialchars($t($field, 'name')) ?>
                        <?php if (!empty($field['description'])): ?>
                            <span class="help"><?= htmlspecialchars($field['description']) ?></span>
                        <?php endif; ?>
                    </label>
                    <div>
                        <?php if ($field['type'] === 'color'): ?>
                            <?php $hex = '#' . ltrim($value, '#'); ?>
                            <div class="cgs-color">
                                <input type="color" value="<?= htmlspecialchars($hex) ?>"
                                       oninput="this.nextElementSibling.value = this.value;">
                                <input type="text" name="<?= $key === 'text-color' ? 'text_color' : $key ?>"
                                       value="<?= htmlspecialchars($hex) ?>" class="cgs-input"
                                       oninput="this.previousElementSibling.value = this.value;">
                            </div>
                        <?php elseif ($field['type'] === 'select'): ?>
                            <select name="<?= str_replace('-', '_', $key) ?>" class="cgs-select">
                                <?php foreach (($field['options'] ?? []) as $opt_val => $opt_label): ?>
                                    <option value="<?= htmlspecialchars((string)$opt_val) ?>"
                                            <?= (string)$value === (string)$opt_val ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($opt_label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Integration section -->
        <div class="cgs-section">
            <h3>codega.com.tr Entegrasyonu</h3>
            <div class="desc">SSO ve API köprüsü için ayarlar. Aynı anahtar codega.com.tr tarafına da girilmelidir.</div>

            <?php foreach (['sso_enabled', 'codega_main_url', 'codega_shared_secret', 'show_main_site_nav'] as $key): ?>
                <?php if (!isset($_form[$key])) continue; ?>
                <?php $field = $_form[$key]; $value = $_settings[$key] ?? ($field['default'] ?? ''); ?>
                <div class="cgs-row">
                    <label class="cgs-label">
                        <?= htmlspecialchars($t($field, 'name')) ?>
                        <?php if (!empty($field['description'])): ?>
                            <span class="help"><?= htmlspecialchars($field['description']) ?></span>
                        <?php endif; ?>
                    </label>
                    <div>
                        <?php if ($field['type'] === 'checkbox'): ?>
                            <div class="cgs-checkbox">
                                <input type="checkbox" name="<?= $key ?>" value="1"
                                       id="cgs-<?= $key ?>"
                                       <?= !empty($value) ? 'checked' : '' ?>>
                                <label for="cgs-<?= $key ?>" style="cursor:pointer; font-size:13px; color:#5a6478;">Aktif</label>
                            </div>
                        <?php elseif ($field['type'] === 'password'): ?>
                            <input type="password" name="<?= $key ?>" class="cgs-input"
                                   value="<?= htmlspecialchars($value) ?>"
                                   placeholder="openssl rand -hex 32 ile üretin"
                                   style="font-family:'JetBrains Mono', monospace; font-size:12px;">
                        <?php else: ?>
                            <input type="text" name="<?= $key ?>" class="cgs-input"
                                   value="<?= htmlspecialchars($value) ?>"
                                   placeholder="<?= htmlspecialchars($field['default'] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cgs-actions">
            <button type="submit" name="save_settings" value="1" class="cgs-btn cgs-btn-primary">Ayarları Kaydet</button>
            <a href="javascript:history.back()" class="cgs-btn cgs-btn-secondary">İptal</a>
        </div>

    </form>

</div>
