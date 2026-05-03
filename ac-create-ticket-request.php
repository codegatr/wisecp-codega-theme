<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Yeni Destek Talebi Oluştur
 * WiseCP runtime: $departments, $department, $services, $service, $custom_fields, $atachment_extensions, $links
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
$departments = isset($departments) && is_array($departments) ? $departments : [];
$department  = isset($department) ? $department : null;
$services    = isset($services) && is_array($services) ? $services : [];
$service     = isset($service) ? $service : null;
$custom_fields = isset($custom_fields) && is_array($custom_fields) ? $custom_fields : [];
$atachment_extensions = isset($atachment_extensions) ? $atachment_extensions : 'jpg, jpeg, png, pdf, doc, docx, txt, zip';
$links = isset($links) && is_array($links) ? $links : [];

$controller_url = $links['controller'] ?? '';
$tickets_url = cdg_link('tickets');
?>

<style>
.cdg-tk {
    --tk-primary: #2E3B4E;
    --tk-primary-2: #00D3E5;
    --tk-success: #10b981;
    --tk-warning: #f59e0b;
    --tk-danger: #ef4444;
    --tk-purple: #8b5cf6;
    --tk-bg: #f8fafc;
    --tk-card: #fff;
    --tk-text: #0f172a;
    --tk-muted: #64748b;
    --tk-border: #e2e8f0;
    --tk-radius: 14px;
    --tk-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04);
    --tk-shadow-lg: 0 8px 24px rgba(15,23,42,0.08);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
    color: var(--tk-text);
    background: var(--tk-bg);
    padding: 28px 0;
    min-height: 100vh;
    box-sizing: border-box;
}
.cdg-tk *, .cdg-tk *::before, .cdg-tk *::after { box-sizing: border-box; }
.cdg-tk a { text-decoration: none; color: inherit; }

.cdg-tk-wrap { max-width: 900px; margin: 0 auto; padding: 0 20px; }

/* TOP BAR */
.cdg-tk-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--tk-border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--tk-text);
    transition: all 0.18s;
    margin-bottom: 18px;
}
.cdg-tk-back:hover { border-color: var(--tk-primary); color: var(--tk-primary); }

/* HERO */
.cdg-tk-hero {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #00D3E5 100%);
    border-radius: 18px;
    padding: 26px 30px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(99,102,241,0.20);
    display: flex; align-items: center; gap: 18px;
}
.cdg-tk-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,0.16), transparent 70%);
    pointer-events: none;
}
.cdg-tk-hero-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    display: grid; place-items: center;
    font-size: 26px;
    flex-shrink: 0;
    position: relative; z-index: 1;
}
.cdg-tk-hero-text { position: relative; z-index: 1; }
.cdg-tk-hero-text h1 {
    font-size: 24px; font-weight: 800;
    margin: 0 0 4px;
    letter-spacing: -0.4px;
}
.cdg-tk-hero-text p {
    font-size: 13px; opacity: 0.88; margin: 0;
}

/* CARD */
.cdg-tk-card {
    background: var(--tk-card);
    border: 1px solid var(--tk-border);
    border-radius: var(--tk-radius);
    box-shadow: var(--tk-shadow);
    overflow: hidden;
}
.cdg-tk-card-body { padding: 26px 28px; }

/* FORM */
.cdg-tk-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
.cdg-tk-field { margin-bottom: 16px; }
.cdg-tk-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--tk-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 7px;
}
.cdg-tk-label .req { color: var(--tk-danger); margin-left: 2px; }
.cdg-tk-input,
.cdg-tk-select,
.cdg-tk-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--tk-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--tk-text);
    background: #fff;
    outline: none;
    transition: all 0.18s;
    font-family: inherit;
}
.cdg-tk-input:focus,
.cdg-tk-select:focus,
.cdg-tk-textarea:focus {
    border-color: var(--tk-primary);
    box-shadow: 0 0 0 3px rgba(46,59,78,0.10);
}
.cdg-tk-textarea {
    min-height: 180px;
    resize: vertical;
    line-height: 1.6;
}
.cdg-tk-select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2364748b' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
}
.cdg-tk-help {
    font-size: 12px;
    color: var(--tk-muted);
    margin-top: 6px;
    display: flex; align-items: flex-start; gap: 6px;
}
.cdg-tk-help i { color: var(--tk-primary); flex-shrink: 0; margin-top: 2px; }

/* PRIORITY CHIPS */
.cdg-tk-priority {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.cdg-tk-pri-chip {
    padding: 14px 16px;
    border: 2px solid var(--tk-border);
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.18s;
    background: #fff;
    display: flex; flex-direction: column; align-items: center; gap: 6px;
}
.cdg-tk-pri-chip input[type="radio"] { display: none; }
.cdg-tk-pri-chip i { font-size: 20px; }
.cdg-tk-pri-chip span {
    font-size: 12px; font-weight: 700;
    color: var(--tk-text);
}
.cdg-tk-pri-chip:hover { transform: translateY(-1px); box-shadow: var(--tk-shadow); }

.cdg-tk-pri-low.selected { border-color: var(--tk-success); background: #d1fae5; }
.cdg-tk-pri-low.selected i { color: var(--tk-success); }
.cdg-tk-pri-mid.selected { border-color: var(--tk-warning); background: #fef3c7; }
.cdg-tk-pri-mid.selected i { color: var(--tk-warning); }
.cdg-tk-pri-high.selected { border-color: var(--tk-danger); background: #fee2e2; }
.cdg-tk-pri-high.selected i { color: var(--tk-danger); }

.cdg-tk-pri-low i { color: #94a3b8; }
.cdg-tk-pri-mid i { color: #94a3b8; }
.cdg-tk-pri-high i { color: #94a3b8; }

/* FILE UPLOAD */
.cdg-tk-file {
    border: 2px dashed var(--tk-border);
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.18s;
    background: #fafbfc;
}
.cdg-tk-file:hover {
    border-color: var(--tk-primary);
    background: #eff6ff;
}
.cdg-tk-file i {
    font-size: 32px;
    color: var(--tk-primary);
    margin-bottom: 8px;
    display: block;
}
.cdg-tk-file-text {
    font-size: 13px;
    color: var(--tk-text);
    font-weight: 600;
    margin-bottom: 4px;
}
.cdg-tk-file-hint {
    font-size: 11px;
    color: var(--tk-muted);
}
.cdg-tk-file input[type="file"] { display: none; }
.cdg-tk-file-list {
    margin-top: 10px;
    text-align: left;
}
.cdg-tk-file-item {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px;
    background: #CFFAFE;
    color: #2E3B4E;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 600;
    margin: 4px 4px 0 0;
}
.cdg-tk-file-item .x {
    cursor: pointer;
    color: #2E3B4E;
    opacity: 0.6;
    font-weight: 700;
}
.cdg-tk-file-item .x:hover { opacity: 1; }

/* SUBMIT */
.cdg-tk-actions {
    display: flex; justify-content: space-between; align-items: center;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--tk-border);
    margin-top: 20px;
}
.cdg-tk-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 26px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    border: 0;
    transition: all 0.2s;
    text-decoration: none;
    font-family: inherit;
}
.cdg-tk-btn-cancel {
    background: #fff;
    color: var(--tk-text);
    border: 1px solid var(--tk-border);
}
.cdg-tk-btn-cancel:hover { border-color: var(--tk-muted); color: var(--tk-text); }
.cdg-tk-btn-send {
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    box-shadow: 0 6px 18px rgba(46,59,78,0.25);
}
.cdg-tk-btn-send:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(46,59,78,0.35);
    color: #fff;
}

/* CHECKBOX */
.cdg-tk-check {
    display: flex; align-items: center; gap: 10px;
    cursor: pointer;
    font-size: 13px;
    color: var(--tk-text);
}
.cdg-tk-check input { margin: 0; transform: scale(1.15); accent-color: var(--tk-primary); }

/* RESPONSIVE */
@media (max-width: 768px) {
    .cdg-tk-row { grid-template-columns: 1fr; }
    .cdg-tk-priority { grid-template-columns: 1fr; }
    .cdg-tk-card-body { padding: 22px 20px; }
    .cdg-tk-actions { flex-direction: column-reverse; align-items: stretch; }
    .cdg-tk-btn { justify-content: center; width: 100%; }
    .cdg-tk-hero { flex-direction: column; text-align: center; }
}
</style>

<div class="cdg-tk">
<div class="cdg-tk-wrap">

    <!-- BACK -->
    <a href="<?php echo htmlspecialchars($tickets_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-tk-back">
        <i class="bi bi-arrow-left"></i> Taleplerime Dön
    </a>

    <!-- HERO -->
    <section class="cdg-tk-hero">
        <div class="cdg-tk-hero-icon"><i class="bi bi-headset"></i></div>
        <div class="cdg-tk-hero-text">
            <h1>Yeni Destek Talebi</h1>
            <p>Sorununuzu detaylı anlatın, en kısa sürede uzman ekibimiz size dönüş yapacak.</p>
        </div>
    </section>

    <!-- FORM -->
    <div class="cdg-tk-card">
        <form method="post" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" enctype="multipart/form-data" id="cdg-tk-form">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('create-ticket'); ?>
            <input type="hidden" name="operation" value="create_ticket">

            <div class="cdg-tk-card-body">

                <!-- DEPARTMAN + HİZMET -->
                <div class="cdg-tk-row">
                    <div>
                        <label class="cdg-tk-label">Departman <span class="req">*</span></label>
                        <select name="department" class="cdg-tk-select" required>
                            <option value="">— Departman Seçin —</option>
                            <?php foreach($departments as $dep_id => $dep_data):
                                $dep_name = is_array($dep_data) ? ($dep_data['name'] ?? $dep_id) : $dep_data;
                                $dep_val  = is_array($dep_data) ? ($dep_data['id'] ?? $dep_id) : $dep_id;
                                $sel = (isset($department['id']) && $department['id'] == $dep_val) ? ' selected' : '';
                            ?>
                                <option value="<?php echo htmlspecialchars($dep_val, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php echo $sel; ?>><?php echo htmlspecialchars($dep_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="cdg-tk-help"><i class="bi bi-info-circle"></i> İlgili konuya göre departman seçin (Teknik, Satış, Faturalama vb.)</div>
                    </div>

                    <div>
                        <label class="cdg-tk-label">İlgili Hizmet</label>
                        <select name="service" class="cdg-tk-select">
                            <option value="">— Genel (Hizmet Seçilmedi) —</option>
                            <?php foreach($services as $group):
                                $group_name = is_array($group) ? ($group['name'] ?? '') : '';
                                $group_items = (is_array($group) && isset($group['items'])) ? $group['items'] : (is_array($group) ? $group : []);
                            ?>
                                <?php if($group_name): ?>
                                <optgroup label="<?php echo htmlspecialchars($group_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                                <?php endif; ?>
                                <?php foreach($group_items as $row):
                                    if(!is_array($row)) continue;
                                    $r_id   = $row['id'] ?? '';
                                    $r_name = $row['name'] ?? 'Hizmet';
                                    $sel = (isset($service['id']) && $service['id'] == $r_id) ? ' selected' : '';
                                ?>
                                    <option value="<?php echo htmlspecialchars($r_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php echo $sel; ?>><?php echo htmlspecialchars($r_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                                <?php if($group_name): ?>
                                </optgroup>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="cdg-tk-help"><i class="bi bi-info-circle"></i> Sorun belirli bir hizmetinizle ilgiliyse seçin.</div>
                    </div>
                </div>

                <!-- KONU -->
                <div class="cdg-tk-field">
                    <label class="cdg-tk-label">Konu <span class="req">*</span></label>
                    <input type="text" name="title" class="cdg-tk-input" required maxlength="150" placeholder="Sorununuzu kısaca özetleyin">
                </div>

                <!-- ÖNCELİK -->
                <div class="cdg-tk-field">
                    <label class="cdg-tk-label">Öncelik <span class="req">*</span></label>
                    <div class="cdg-tk-priority">
                        <label class="cdg-tk-pri-chip cdg-tk-pri-low">
                            <input type="radio" name="priority" value="1" checked>
                            <i class="bi bi-arrow-down-circle-fill"></i>
                            <span>Düşük</span>
                        </label>
                        <label class="cdg-tk-pri-chip cdg-tk-pri-mid selected">
                            <input type="radio" name="priority" value="2">
                            <i class="bi bi-dash-circle-fill"></i>
                            <span>Orta</span>
                        </label>
                        <label class="cdg-tk-pri-chip cdg-tk-pri-high">
                            <input type="radio" name="priority" value="3">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>Yüksek</span>
                        </label>
                    </div>
                </div>

                <!-- MESAJ -->
                <div class="cdg-tk-field">
                    <label class="cdg-tk-label">Mesajınız <span class="req">*</span></label>
                    <textarea name="message" class="cdg-tk-textarea" required placeholder="Sorununuzu detaylı şekilde açıklayın. Hata mesajları, ekran görüntüsü açıklamaları, ne zaman olduğu gibi bilgileri eklemeniz çözüm sürecini hızlandırır."></textarea>
                    <div class="cdg-tk-help"><i class="bi bi-lightbulb"></i> İpucu: Sorunun nasıl ortaya çıktığını adım adım anlatırsanız daha hızlı yardımcı olabiliriz.</div>
                </div>

                <!-- CUSTOM FIELDS (departmana göre özel) -->
                <?php if(!empty($custom_fields)): ?>
                    <?php foreach($custom_fields as $f_id => $field):
                        if(!is_array($field)) continue;
                        $f_name  = $field['name'] ?? $f_id;
                        $f_label = $field['label'] ?? $f_name;
                        $f_type  = $field['type'] ?? 'text';
                        $f_req   = !empty($field['required']);
                        $f_options = $field['options'] ?? [];
                    ?>
                    <div class="cdg-tk-field">
                        <label class="cdg-tk-label">
                            <?php echo htmlspecialchars($f_label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <?php if($f_req): ?><span class="req">*</span><?php endif; ?>
                        </label>
                        <?php if($f_type === 'textarea'): ?>
                            <textarea name="custom_field[<?php echo htmlspecialchars($f_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>]" class="cdg-tk-textarea" <?php echo $f_req ? 'required' : ''; ?> style="min-height:100px;"></textarea>
                        <?php elseif($f_type === 'select' && is_array($f_options)): ?>
                            <select name="custom_field[<?php echo htmlspecialchars($f_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>]" class="cdg-tk-select" <?php echo $f_req ? 'required' : ''; ?>>
                                <option value="">— Seçin —</option>
                                <?php foreach($f_options as $op_v => $op_l): ?>
                                    <option value="<?php echo htmlspecialchars($op_v, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($op_l, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="<?php echo htmlspecialchars($f_type, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" name="custom_field[<?php echo htmlspecialchars($f_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>]" class="cdg-tk-input" <?php echo $f_req ? 'required' : ''; ?>>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- DOSYA EKLEME -->
                <div class="cdg-tk-field">
                    <label class="cdg-tk-label">Dosya Eki (Opsiyonel)</label>
                    <label class="cdg-tk-file" id="cdg-tk-file-drop">
                        <input type="file" name="attachments[]" id="cdg-tk-file-input" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.zip,.gif,.xlsx,.xls">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <div class="cdg-tk-file-text">Dosya seçmek için tıklayın veya sürükleyin</div>
                        <div class="cdg-tk-file-hint">İzin verilen: <?php echo htmlspecialchars($atachment_extensions, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <div class="cdg-tk-file-list" id="cdg-tk-file-list"></div>
                    </label>
                </div>

                <!-- ŞİFRELEME -->
                <div class="cdg-tk-field">
                    <label class="cdg-tk-check">
                        <input type="checkbox" name="encrypt_message" value="1">
                        <span><i class="bi bi-shield-lock"></i> Mesajımı şifrele (hassas bilgi içeriyorsa)</span>
                    </label>
                </div>

            </div>

            <div class="cdg-tk-card-body" style="padding-top:0;">
                <div class="cdg-tk-actions">
                    <a href="<?php echo htmlspecialchars($tickets_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-tk-btn cdg-tk-btn-cancel">
                        <i class="bi bi-x-lg"></i> İptal
                    </a>
                    <button type="submit" class="cdg-tk-btn cdg-tk-btn-send">
                        <i class="bi bi-send-fill"></i> Talebi Gönder
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
</div>

<script>
(function(){
    // Öncelik seçimi
    document.querySelectorAll('.cdg-tk-pri-chip').forEach(function(chip){
        chip.addEventListener('click', function(e){
            document.querySelectorAll('.cdg-tk-pri-chip').forEach(function(c){ c.classList.remove('selected'); });
            this.classList.add('selected');
            var radio = this.querySelector('input[type="radio"]');
            if(radio) radio.checked = true;
        });
    });

    // Dosya yükleme - sürükle bırak + listeleme
    var fileInput = document.getElementById('cdg-tk-file-input');
    var fileDrop  = document.getElementById('cdg-tk-file-drop');
    var fileList  = document.getElementById('cdg-tk-file-list');

    if(fileInput && fileList) {
        fileInput.addEventListener('change', function(){
            updateFileList(this.files);
        });

        if(fileDrop) {
            ['dragenter','dragover'].forEach(function(ev){
                fileDrop.addEventListener(ev, function(e){
                    e.preventDefault(); e.stopPropagation();
                    this.style.borderColor = '#2E3B4E';
                    this.style.background = '#eff6ff';
                });
            });
            ['dragleave','drop'].forEach(function(ev){
                fileDrop.addEventListener(ev, function(e){
                    e.preventDefault(); e.stopPropagation();
                    this.style.borderColor = '';
                    this.style.background = '';
                });
            });
            fileDrop.addEventListener('drop', function(e){
                if(e.dataTransfer && e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    updateFileList(e.dataTransfer.files);
                }
            });
        }
    }

    function updateFileList(files) {
        if(!fileList) return;
        fileList.innerHTML = '';
        for(var i = 0; i < files.length; i++) {
            var item = document.createElement('span');
            item.className = 'cdg-tk-file-item';
            item.innerHTML = '<i class="bi bi-file-earmark"></i> ' +
                escapeHtml(files[i].name) +
                ' <span style="opacity:0.7;font-size:11px;">(' + formatBytes(files[i].size) + ')</span>';
            fileList.appendChild(item);
        }
    }
    function formatBytes(b) {
        if(b < 1024) return b + ' B';
        if(b < 1048576) return (b/1024).toFixed(1) + ' KB';
        return (b/1048576).toFixed(1) + ' MB';
    }
    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
})();
</script>
