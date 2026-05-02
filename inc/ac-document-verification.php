<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Belge Dogrulama (SubmitDocumentVerification)
 * .tr domainleri ve diger ulkelere ozel kimlik dogrulama
 *
 * WiseCP runtime: $remainingVerifications, $u_lang, $operation_link
 *   $remainingVerifications['document_filters'] - dinamik form yapisi
 *   field_types: input, textarea, select, radio, checkbox, file
 */

$remainingVerifications = isset($remainingVerifications) && is_array($remainingVerifications) ? $remainingVerifications : [];
$doc_filters = $remainingVerifications['document_filters'] ?? [];
$u_lang = $u_lang ?? (defined('LANG') ? LANG : 'tr');
$operation_link = $operation_link ?? ($links['controller'] ?? '');

// Belge dogrulama yoksa, modulu hic gosterme
if(empty($doc_filters)) return;

// Form alanlari var mi kontrol et
$has_fields = false;
$show_submit = false;
foreach($doc_filters as $f_id => $f) {
    if(isset($f['fields'][$u_lang])) {
        $has_fields = true;
        break;
    }
}
if(!$has_fields) return;
?>

<style>
.cdg-docvrf {
    margin-top: 20px;
    background: #fff;
    border: 1px solid #fed7aa;
    border-left: 4px solid #f59e0b;
    border-radius: 12px;
    padding: 22px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.cdg-docvrf-head {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 18px; padding-bottom: 14px;
    border-bottom: 1px dashed #fed7aa;
}
.cdg-docvrf-head-icon {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff; font-size: 22px;
    display: grid; place-items: center;
    border-radius: 12px;
}
.cdg-docvrf-head-text h3 {
    font-size: 17px; font-weight: 800; color: #92400e;
    margin: 0 0 4px;
}
.cdg-docvrf-head-text p {
    font-size: 13px; color: #b45309;
    margin: 0;
}

.cdg-docvrf-section {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 14px;
}
.cdg-docvrf-section-title {
    font-size: 14px; font-weight: 800; color: #78350f;
    margin: 0 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #fde68a;
    display: flex; align-items: center; gap: 8px;
}

.cdg-docvrf-field {
    margin-bottom: 14px;
}
.cdg-docvrf-field-label {
    display: block;
    font-size: 13px; font-weight: 700; color: #475569;
    margin-bottom: 6px;
}
.cdg-docvrf-field-input,
.cdg-docvrf-field-select,
.cdg-docvrf-field-textarea {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-family: inherit; font-size: 14px;
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
}
.cdg-docvrf-field-input:focus,
.cdg-docvrf-field-select:focus,
.cdg-docvrf-field-textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    outline: none;
}
.cdg-docvrf-field-textarea { min-height: 80px; resize: vertical; }
.cdg-docvrf-field-file {
    display: block;
    width: 100%;
    padding: 12px;
    background: #fff;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    font-size: 13px; color: #475569;
    cursor: pointer;
    transition: border-color .2s;
}
.cdg-docvrf-field-file:hover { border-color: #3b82f6; }
.cdg-docvrf-field-file-info {
    display: block;
    font-size: 11px; color: #92400e;
    margin-top: 4px;
}

.cdg-docvrf-radio,
.cdg-docvrf-checkbox {
    display: flex; flex-wrap: wrap; gap: 10px;
}
.cdg-docvrf-radio label,
.cdg-docvrf-checkbox label {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: all .2s;
}
.cdg-docvrf-radio input,
.cdg-docvrf-checkbox input { margin: 0; }
.cdg-docvrf-radio label:hover,
.cdg-docvrf-checkbox label:hover { border-color: #3b82f6; }

.cdg-docvrf-status-pending {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px; font-weight: 700; color: #78350f;
}
.cdg-docvrf-status-rejected {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px; font-weight: 700; color: #991b1b;
}
.cdg-docvrf-status-approved {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px; font-weight: 700; color: #14532d;
}

.cdg-docvrf-submit {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 24px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    border: 0;
    border-radius: 9px;
    font-size: 14px; font-weight: 800;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
}
.cdg-docvrf-submit:hover { opacity: .9; transform: translateY(-1px); }
.cdg-docvrf-submit:disabled { opacity: .5; cursor: not-allowed; transform: none; }
</style>

<div class="cdg-docvrf" id="cdg-docvrf">
    <div class="cdg-docvrf-head">
        <div class="cdg-docvrf-head-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="cdg-docvrf-head-text">
            <h3>Kimlik / Belge Doğrulama</h3>
            <p>Bazı domain uzantıları için kimlik veya kurumsal bilgi doğrulaması zorunludur. Aşağıdaki belgeleri yükleyerek doğrulamayı tamamlayabilirsiniz.</p>
        </div>
    </div>

    <form action="<?php echo htmlspecialchars($operation_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" method="post" id="SubmitDocumentVerification" enctype="multipart/form-data">
        <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
        <input type="hidden" name="operation" value="SubmitDocumentVerification">

        <?php
        foreach($doc_filters as $f_id => $f):
            if(!isset($f['fields'][$u_lang])) continue;
            $fields = $f['fields'][$u_lang];
            $section_title = $f['title'][$u_lang] ?? ($f['name'] ?? 'Doğrulama Belgesi');
        ?>
        <div class="cdg-docvrf-section">
            <h4 class="cdg-docvrf-section-title">
                <i class="bi bi-file-earmark-text"></i>
                <?php echo htmlspecialchars($section_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </h4>

            <?php foreach($fields as $f_k => $field):
                $record = $field['record'] ?? [];
                $field_name = $field['name'] ?? $f_k;
                $field_type = $field['type'] ?? 'input';
                $field_status = $record['status'] ?? '';
                $r_value = $record ? ($record['field_value'] ?? '') : '';
                $base_name = "documents[{$f_id}][fields][{$f_k}]";
            ?>
            <div class="cdg-docvrf-field">
                <label class="cdg-docvrf-field-label">
                    <?php echo htmlspecialchars($field_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    <?php if($field_status === 'awaiting'): ?>
                        <span class="cdg-docvrf-status-pending"><i class="bi bi-clock"></i> Onay Bekliyor</span>
                    <?php elseif($field_status === 'unverified'): ?>
                        <span class="cdg-docvrf-status-rejected"><i class="bi bi-x-circle"></i> Reddedildi
                        <?php if(!empty($record['status_msg'])): ?>: <?php echo htmlspecialchars($record['status_msg'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?><?php endif; ?>
                        </span>
                    <?php elseif($field_status === 'verified'): ?>
                        <span class="cdg-docvrf-status-approved"><i class="bi bi-check-circle"></i> Onaylandı</span>
                    <?php endif; ?>
                </label>

                <?php
                // Onaylanmis veya bekleyen field disinda formu goster
                if(!$record || $field_status === 'unverified'):
                    $show_submit = true;
                ?>
                    <?php if($field_type === 'input'): ?>
                        <input type="text" name="<?php echo $base_name; ?>" value="<?php echo htmlspecialchars($r_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-docvrf-field-input">

                    <?php elseif($field_type === 'textarea'): ?>
                        <textarea name="<?php echo $base_name; ?>" class="cdg-docvrf-field-textarea"><?php echo htmlspecialchars($r_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></textarea>

                    <?php elseif($field_type === 'select'): ?>
                        <select name="<?php echo $base_name; ?>" class="cdg-docvrf-field-select">
                            <?php foreach(($field['options'] ?? []) as $opt): ?>
                            <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php echo $opt == $r_value ? ' selected' : ''; ?>><?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif($field_type === 'radio'): ?>
                        <div class="cdg-docvrf-radio">
                            <?php foreach(($field['options'] ?? []) as $k => $opt):
                                $rid = "f_{$f_id}_{$f_k}_{$k}";
                            ?>
                            <label for="<?php echo $rid; ?>">
                                <input id="<?php echo $rid; ?>" type="radio" name="<?php echo $base_name; ?>" value="<?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php echo $opt == $r_value ? ' checked' : ''; ?>>
                                <?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </label>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif($field_type === 'checkbox'):
                        $d_value = [];
                        if($r_value) {
                            if(class_exists('Utility') && method_exists('Utility','jdecode')) {
                                try { $d_value = Utility::jdecode($r_value, true); } catch(\Throwable $e) {}
                            }
                            if(empty($d_value)) {
                                $tmp = json_decode($r_value, true);
                                if(is_array($tmp)) $d_value = $tmp;
                            }
                        }
                        if(!is_array($d_value)) $d_value = [];
                    ?>
                        <div class="cdg-docvrf-checkbox">
                            <?php foreach(($field['options'] ?? []) as $k => $opt):
                                $cid = "f_{$f_id}_{$f_k}_{$k}";
                            ?>
                            <label for="<?php echo $cid; ?>">
                                <input id="<?php echo $cid; ?>" type="checkbox" name="<?php echo $base_name; ?>[]" value="<?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"<?php echo in_array($opt, $d_value) ? ' checked' : ''; ?>>
                                <?php echo htmlspecialchars($opt, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            </label>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif($field_type === 'file'): ?>
                        <input type="file" name="documents-<?php echo $f_id; ?>-fields-<?php echo $f_k; ?>" class="cdg-docvrf-field-file">
                        <?php if(!empty($field['allowed_ext'])): ?>
                        <span class="cdg-docvrf-field-file-info">
                            <i class="bi bi-info-circle"></i> İzin verilen formatlar: <?php echo htmlspecialchars($field['allowed_ext'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php elseif($field_status === 'awaiting'): ?>
                    <div style="padding: 8px 12px; background:#fef3c7; border-radius:6px; font-size:13px; color:#78350f;">
                        <i class="bi bi-clock"></i> Bu belge yönetici tarafından inceleniyor. Onay sonrası size bildirim gelecektir.
                    </div>
                <?php elseif($field_status === 'verified'): ?>
                    <div style="padding: 8px 12px; background:#f0fdf4; border-radius:6px; font-size:13px; color:#14532d;">
                        <i class="bi bi-check-circle-fill"></i> Bu alan başarıyla doğrulandı.
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php if($show_submit): ?>
        <div style="text-align:right; margin-top:18px;">
            <button type="submit" class="cdg-docvrf-submit" id="cdg-docvrf-submit-btn">
                <i class="bi bi-cloud-upload"></i> Belgeleri Gönder
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<script>
(function(){
    var form = document.getElementById('SubmitDocumentVerification');
    if(!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if(typeof MioAjax !== 'function') {
            alert('AJAX motoru yüklenemedi. Sayfayı yenileyin.');
            return;
        }

        var btn = document.getElementById('cdg-docvrf-submit-btn');
        if(btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Gönderiliyor...'; }

        var formData = new FormData(form);

        // jQuery ile AJAX (file upload icin)
        if(typeof jQuery !== 'undefined') {
            jQuery.ajax({
                url: form.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    var solve = null;
                    try { solve = (typeof getJson === 'function') ? getJson(result) : JSON.parse(result); } catch(e) {}
                    if(solve && solve.status === 'successful') {
                        if(typeof alert_success === 'function') alert_success(solve.message || 'Belgeler başarıyla gönderildi', {timer: 2500});
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        var msg = (solve && solve.message) ? solve.message : 'Belge gönderilirken hata oluştu';
                        if(typeof alert_error === 'function') alert_error(msg, {timer: 4000});
                        else alert(msg);
                        if(btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload"></i> Belgeleri Gönder'; }
                    }
                },
                error: function() {
                    alert('Sunucu hatası. Tekrar deneyin.');
                    if(btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload"></i> Belgeleri Gönder'; }
                }
            });
        } else {
            form.submit(); // jQuery yoksa normal submit
        }
    });
})();
</script>
