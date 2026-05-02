<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain Documents (Belge Yükleme) Modal
 * Operations: add_domain_doc, sent_domain_doc
 *
 * Genelde ülke kodlu domainler (.tr, .ru, .ua vb.) için zorunlu belge yükleme.
 * 3 tip input: text (TC kimlik vb.), select (örn. unvan), file (PDF/JPG)
 *
 * WiseCP runtime: $info_docs (doc_id => [name, type, options])
 */

$info_docs = isset($info_docs) && is_array($info_docs) ? $info_docs : [];
$d_name = $proanse['name'] ?? ($options['domain'] ?? 'domain.com');
$controller_url = isset($links['controller']) ? $links['controller'] : '';

if(empty($info_docs)) return; // Belge gerektirmeyen domainler için modal yok
?>

<!-- DOCUMENTS MODAL -->
<div class="cdg-dm-overlay" id="cdg-documents-modal" role="dialog" aria-modal="true">
    <div class="cdg-dm-modal" style="max-width:760px;">
        <div class="cdg-dm-head">
            <h3><i class="bi bi-file-earmark-text"></i> Belge Yönetimi</h3>
            <button type="button" class="cdg-dm-close" onclick="cdgDomain.closeModal('cdg-documents-modal')" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-dm-body">
            <div class="cdg-dm-info">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <strong><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong> domaini için kayıt firmasının istediği belgeleri buradan yükleyebilirsiniz.
                    Belgeleriniz onaylandıktan sonra domain aktif olur.
                </div>
            </div>

            <!-- Belge Ekleme -->
            <form id="addDomainDoc" enctype="multipart/form-data" action="<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" method="post" class="cdg-dm-form">
                <input type="hidden" name="operation" value="add_domain_doc">
                <input type="hidden" name="id" value="<?php echo (int)($proanse['id'] ?? 0); ?>">

                <div style="font-size:12px;font-weight:800;color:#1e40af;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-plus-circle"></i> Yeni Belge Ekle
                </div>
                <div class="cdg-dm-field" style="margin-bottom:12px;">
                    <label class="cdg-dm-field-label">Belge Türü</label>
                    <select name="doc_id" id="cdg-doc-id" class="cdg-dm-select" onchange="cdgDomain.docTypeChange(this)">
                        <option value="0">Belge türü seçin</option>
                        <?php foreach($info_docs as $d_id => $d):
                            $type = $d['type'] ?? 'text';
                            $name = $d['name'] ?? 'Belge';
                        ?>
                        <option data-type="<?php echo htmlspecialchars($type, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($d_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Text input -->
                <div class="cdg-dm-field cdg-doc-input" id="cdg-doc-text" style="display:none;margin-bottom:12px;">
                    <label class="cdg-dm-field-label">Değer</label>
                    <input type="text" name="text" class="cdg-dm-input" placeholder="Lütfen değeri girin">
                </div>

                <!-- Select inputs (her select için ayrı, doc_id bazlı) -->
                <?php foreach($info_docs as $d_k => $d):
                    if(($d['type'] ?? '') !== 'select') continue;
                    $options_arr = $d['options'] ?? [];
                ?>
                <div class="cdg-dm-field cdg-doc-input cdg-doc-select-wrap" id="cdg-doc-select-<?php echo htmlspecialchars($d_k, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:none;margin-bottom:12px;">
                    <label class="cdg-dm-field-label">Seçim</label>
                    <select name="select" class="cdg-dm-select" disabled>
                        <option value="-1">Lütfen seçin</option>
                        <?php foreach($options_arr as $op_k => $op): ?>
                        <option value="<?php echo htmlspecialchars($op_k, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($op, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>

                <!-- File upload -->
                <div class="cdg-dm-field cdg-doc-input" id="cdg-doc-file" style="display:none;margin-bottom:12px;">
                    <label class="cdg-dm-field-label">Dosya Seç (PDF/JPG/PNG)</label>
                    <input type="file" name="file" class="cdg-dm-input" accept=".pdf,.jpg,.jpeg,.png" style="padding:8px;">
                </div>

                <div style="text-align:right;">
                    <a href="javascript:void 0;" class="cdg-dm-form-add-btn" onclick="cdgDomain.docAdd(this)" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;text-decoration:none;">
                        <i class="bi bi-cloud-upload"></i> Belgeyi Ekle
                    </a>
                </div>
            </form>

            <!-- Hazır olduğunda gönder butonu -->
            <div style="margin-top:16px;padding:14px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;text-align:center;">
                <p style="margin:0 0 10px;color:#15803d;font-size:13px;">
                    <strong><i class="bi bi-check-circle"></i> Tüm belgeler eklendiğinde</strong> aşağıdaki butonla kayıt firmasına gönderebilirsiniz.
                </p>
                <a href="javascript:void 0;" onclick="cdgDomain.docSend(this)" class="cdg-dm-form-add-btn" style="background:linear-gradient(135deg,#15803d,#22c55e);display:inline-flex;align-items:center;gap:6px;padding:10px 22px;text-decoration:none;">
                    <i class="bi bi-send"></i> Belgeleri Gönder
                </a>
            </div>
        </div>
    </div>
</div>
