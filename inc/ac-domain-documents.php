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

                <div style="font-size:12px;font-weight:800;color:#2E3B4E;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
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

            <?php
            // Yuklenmis belgeler listesi (uploaded_docs runtime variable)
            $uploaded_docs = isset($uploaded_docs) && is_array($uploaded_docs) ? $uploaded_docs : [];
            $has_unsent = false;
            if(!empty($uploaded_docs)):
                $doc_status_meta = [
                    'unsent'    => ['lbl' => 'Henüz gönderilmedi', 'cls' => 'cdg-doc-st-unsent', 'icon' => 'clock', 'color' => '#64748b'],
                    'pending'   => ['lbl' => 'Onay bekliyor',      'cls' => 'cdg-doc-st-pending', 'icon' => 'hourglass-split', 'color' => '#f59e0b'],
                    'declined'  => ['lbl' => 'Reddedildi',         'cls' => 'cdg-doc-st-declined', 'icon' => 'x-circle', 'color' => '#ef4444'],
                    'verified'  => ['lbl' => 'Onaylandı',          'cls' => 'cdg-doc-st-verified', 'icon' => 'check-circle', 'color' => '#10b981'],
                ];
            ?>
            <div style="margin-top:18px;padding:16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
                <div style="font-size:12px;font-weight:800;color:#2E3B4E;margin-bottom:12px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-files"></i> Yüklenmiş Belgeler (<?php echo count($uploaded_docs); ?>)
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <?php foreach($uploaded_docs as $ud):
                        $st = $ud['status'] ?? 'unsent';
                        if($st === 'unsent') $has_unsent = true;
                        $st_meta = $doc_status_meta[$st] ?? $doc_status_meta['unsent'];
                        $ud_id = (int)($ud['id'] ?? 0);
                    ?>
                    <div style="display:grid;grid-template-columns:1fr 1.5fr auto;gap:10px;align-items:center;padding:10px 12px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;">
                        <div style="font-size:13px;font-weight:700;color:#1e293b;"><?php echo htmlspecialchars($ud['name'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
                        <div style="font-size:12px;color:#475569;word-break:break-all;">
                            <?php if(!empty($ud['file']) && is_array($ud['file'])): ?>
                                <i class="bi bi-paperclip"></i> <?php echo htmlspecialchars($ud['file']['name'] ?? 'dosya', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                <a href="<?php echo htmlspecialchars($controller_url . '?operation=download_domain_doc_file&id=' . $ud_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" target="_blank" style="margin-left:6px;color:#2E3B4E;" title="İndir">
                                    <i class="bi bi-download"></i>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($ud['value'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                            <?php endif; ?>
                        </div>
                        <div style="text-align:right;">
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:<?php echo $st_meta['color']; ?>15;color:<?php echo $st_meta['color']; ?>;border:1px solid <?php echo $st_meta['color']; ?>40;border-radius:6px;font-size:11px;font-weight:700;">
                                <i class="bi bi-<?php echo $st_meta['icon']; ?>"></i> <?php echo $st_meta['lbl']; ?>
                            </span>
                            <?php if(!empty($ud['status_msg'])): ?>
                            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">(<?php echo htmlspecialchars($ud['status_msg'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>)</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Hazır olduğunda gönder butonu -->
            <?php if(empty($uploaded_docs) || $has_unsent): ?>
            <div style="margin-top:16px;padding:14px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;text-align:center;">
                <p style="margin:0 0 10px;color:#15803d;font-size:13px;">
                    <strong><i class="bi bi-check-circle"></i> Tüm belgeler eklendiğinde</strong> aşağıdaki butonla kayıt firmasına gönderebilirsiniz.
                </p>
                <a href="javascript:void 0;" onclick="cdgDomain.docSend(this)" class="cdg-dm-form-add-btn" style="background:linear-gradient(135deg,#15803d,#22c55e);display:inline-flex;align-items:center;gap:6px;padding:10px 22px;text-decoration:none;">
                    <i class="bi bi-send"></i> Belgeleri Gönder
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
