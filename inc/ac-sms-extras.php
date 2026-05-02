<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - SMS Yönetim Modülü
 * Operations: submit_sms, new_origin_request, add_new_group, change_group_numbers,
 *             delete_group, update_black_list, get_credit, get_sms_report,
 *             sms_credit_renewal, update_cancel_link
 *
 * Tab yapısı:
 *  - SMS Gönder
 *  - Gönderici Adı (Origin) Talebi
 *  - Rehber (Gruplar)
 *  - Kara Liste
 *  - Raporlar
 *  - Kredi Yenileme
 *
 * WiseCP runtime: $proanse, $links, $origins, $groups, $dimensions, $blackList,
 *                 $reports, $credit_list, $sms_credit, $cancel_link_text, $invoice
 */

$d_status = strtolower($proanse['status'] ?? 'unknown');
if($d_status !== 'active') return;

$controller_url = $links['controller'] ?? '';
$origins = isset($origins) && is_array($origins) ? $origins : [];
$groups = isset($groups) && is_array($groups) ? $groups : [];
$dimensions = isset($dimensions) && is_array($dimensions) ? $dimensions : [];
$black_list = isset($blackList) && is_array($blackList) ? $blackList : [];
$reports = isset($reports) && is_array($reports) ? $reports : [];
$credit_list = isset($credit_list) && is_array($credit_list) ? $credit_list : [];
$sms_credit = $sms_credit ?? 0;
$cancel_link_text = $cancel_link_text ?? '';

$active_origins = array_filter($origins, function($o){ return ($o['status'] ?? '') === 'active'; });
?>

<style>
.cdg-sms { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin-top: 20px; }
.cdg-sms *, .cdg-sms *::before, .cdg-sms *::after { box-sizing: border-box; }

.cdg-sms-credit-bar {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: #fff;
    border-radius: 14px;
    padding: 16px 22px;
    margin-bottom: 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
.cdg-sms-credit-bar .label { font-size: 12px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px; }
.cdg-sms-credit-bar .value { font-size: 28px; font-weight: 800; line-height: 1.2; }
.cdg-sms-credit-bar .actions { display: flex; gap: 6px; }
.cdg-sms-credit-bar .btn {
    background: rgba(255,255,255,0.18); color: #fff !important; border: 0;
    padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;
    cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    font-family: inherit; transition: background 0.15s;
}
.cdg-sms-credit-bar .btn:hover { background: rgba(255,255,255,0.30); }

.cdg-sms-tabs {
    display: flex; gap: 4px; flex-wrap: wrap;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 18px;
}
.cdg-sms-tab {
    padding: 12px 18px;
    background: none; border: 0;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    color: #64748b; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; gap: 6px;
    transition: all 0.15s;
}
.cdg-sms-tab:hover { color: #06b6d4; }
.cdg-sms-tab.active { color: #06b6d4; border-bottom-color: #06b6d4; }
.cdg-sms-tab .badge {
    padding: 2px 8px; background: #e2e8f0; color: #475569;
    border-radius: 99px; font-size: 11px; font-weight: 700;
}
.cdg-sms-tab.active .badge { background: #06b6d4; color: #fff; }

.cdg-sms-pane { display: none; }
.cdg-sms-pane.active { display: block; }

.cdg-sms-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.04);
    margin-bottom: 16px;
}
.cdg-sms-card-title {
    font-size: 13px; font-weight: 800;
    color: #06b6d4;
    margin-bottom: 14px;
    text-transform: uppercase; letter-spacing: 0.5px;
    display: inline-flex; align-items: center; gap: 6px;
}
.cdg-sms-field { margin-bottom: 14px; }
.cdg-sms-label {
    display: block; font-size: 12px; font-weight: 700;
    color: #475569; margin-bottom: 6px;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.cdg-sms-input, .cdg-sms-select, .cdg-sms-textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px; color: #0f172a;
    font-family: inherit;
    background: #fff;
    outline: none;
    transition: border 0.15s;
}
.cdg-sms-input:focus, .cdg-sms-select:focus, .cdg-sms-textarea:focus {
    border-color: #06b6d4; box-shadow: 0 0 0 3px rgba(6,182,212,0.10);
}
.cdg-sms-textarea { resize: vertical; min-height: 100px; font-family: 'JetBrains Mono', 'Courier New', monospace; }

.cdg-sms-grid-2 {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
}
@media (max-width: 720px) { .cdg-sms-grid-2 { grid-template-columns: 1fr; } }

.cdg-sms-meta {
    display: flex; gap: 12px;
    font-size: 12px; color: #64748b;
    margin-top: 4px;
}
.cdg-sms-meta strong { color: #06b6d4; font-weight: 800; }

.cdg-sms-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 11px 18px;
    color: #fff;
    border: 0; border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: transform 0.15s;
}
.cdg-sms-btn:hover { transform: translateY(-1px); color: #fff; }
.cdg-sms-btn-primary { background: linear-gradient(135deg, #06b6d4, #0891b2); box-shadow: 0 4px 10px rgba(6,182,212,0.22); }
.cdg-sms-btn-success { background: linear-gradient(135deg, #10b981, #34d399); box-shadow: 0 4px 10px rgba(16,185,129,0.22); }
.cdg-sms-btn-danger  { background: linear-gradient(135deg, #ef4444, #f87171); box-shadow: 0 4px 10px rgba(239,68,68,0.22); }
.cdg-sms-btn-outline {
    background: #fff; color: #475569 !important;
    border: 1.5px solid #e2e8f0; box-shadow: none;
}
.cdg-sms-btn-outline:hover { border-color: #06b6d4; color: #06b6d4 !important; }

.cdg-sms-group-list {
    display: flex; flex-direction: column; gap: 8px; margin-top: 14px;
}
.cdg-sms-group-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
}
.cdg-sms-group-name { font-size: 13px; font-weight: 700; color: #0f172a; }
.cdg-sms-group-count { font-size: 11px; color: #64748b; margin-top: 2px; }
.cdg-sms-group-actions { display: flex; gap: 6px; }
.cdg-sms-group-action {
    width: 32px; height: 32px;
    border-radius: 7px;
    background: #fff; border: 1px solid #e2e8f0;
    color: #64748b; cursor: pointer;
    display: grid; place-items: center;
    font-size: 13px; font-family: inherit;
    transition: all 0.15s;
}
.cdg-sms-group-action:hover { border-color: #06b6d4; color: #06b6d4; }
.cdg-sms-group-action.danger:hover { border-color: #ef4444; color: #ef4444; }

.cdg-sms-checkbox-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 8px;
    max-height: 220px;
    overflow-y: auto;
    padding: 8px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
}
.cdg-sms-checkbox-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: #334155;
    cursor: pointer; padding: 6px 8px;
    border-radius: 6px;
    background: #fff;
    border: 1px solid #e2e8f0;
}
.cdg-sms-checkbox-item input { accent-color: #06b6d4; }
.cdg-sms-checkbox-item:hover { border-color: #06b6d4; }

.cdg-sms-preview {
    background: linear-gradient(135deg, #ecfeff, #cffafe);
    border: 1px solid #67e8f9;
    border-radius: 10px;
    padding: 16px;
    margin-top: 14px;
}
.cdg-sms-preview-row {
    display: grid; grid-template-columns: 140px 1fr;
    gap: 8px; padding: 6px 0;
    font-size: 12px;
    border-bottom: 1px dashed #a5f3fc;
}
.cdg-sms-preview-row:last-child { border-bottom: 0; }
.cdg-sms-preview-row strong { color: #0e7490; }

.cdg-sms-empty {
    text-align: center; padding: 30px;
    color: #94a3b8; font-size: 13px;
}
.cdg-sms-empty i { font-size: 36px; display: block; margin-bottom: 6px; }

.cdg-sms-table {
    width: 100%; border-collapse: collapse;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}
.cdg-sms-table th {
    background: #f8fafc;
    padding: 10px 12px;
    font-size: 11px; font-weight: 800;
    color: #475569;
    text-transform: uppercase; letter-spacing: 0.4px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-sms-table td {
    padding: 10px 12px;
    font-size: 12px; color: #0f172a;
    border-bottom: 1px solid #f1f5f9;
}
.cdg-sms-table tr:last-child td { border-bottom: 0; }
</style>

<div class="cdg-sms">

    <!-- Kredi Bar -->
    <div class="cdg-sms-credit-bar">
        <div>
            <div class="label">Mevcut SMS Kredisi</div>
            <div class="value"><span id="cdg-sms-credit-num"><?php echo (int)$sms_credit; ?></span> SMS</div>
        </div>
        <div class="actions">
            <button type="button" class="btn" onclick="cdgSmsRefreshCredit(this)"><i class="bi bi-arrow-clockwise"></i> Yenile</button>
            <button type="button" class="btn" onclick="cdgSmsTab('credit')"><i class="bi bi-plus-circle"></i> Kredi Yükle</button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="cdg-sms-tabs">
        <button type="button" class="cdg-sms-tab active" data-tab="send" onclick="cdgSmsTab('send')">
            <i class="bi bi-send"></i> SMS Gönder
        </button>
        <button type="button" class="cdg-sms-tab" data-tab="origin" onclick="cdgSmsTab('origin')">
            <i class="bi bi-person-badge"></i> Gönderici Adı
            <span class="badge"><?php echo count($active_origins); ?></span>
        </button>
        <button type="button" class="cdg-sms-tab" data-tab="groups" onclick="cdgSmsTab('groups')">
            <i class="bi bi-collection"></i> Rehber
            <span class="badge"><?php echo count($groups); ?></span>
        </button>
        <button type="button" class="cdg-sms-tab" data-tab="black" onclick="cdgSmsTab('black')">
            <i class="bi bi-shield-x"></i> Kara Liste
            <span class="badge"><?php echo count($black_list); ?></span>
        </button>
        <button type="button" class="cdg-sms-tab" data-tab="reports" onclick="cdgSmsTab('reports')">
            <i class="bi bi-bar-chart"></i> Raporlar
        </button>
        <button type="button" class="cdg-sms-tab" data-tab="credit" onclick="cdgSmsTab('credit')">
            <i class="bi bi-coin"></i> Kredi Yenile
        </button>
    </div>

    <!-- TAB: SMS GÖNDER -->
    <div class="cdg-sms-pane active" id="cdg-sms-pane-send">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-title"><i class="bi bi-send-fill"></i> Yeni SMS Gönder</div>

            <div class="cdg-sms-grid-2">
                <div class="cdg-sms-field">
                    <label class="cdg-sms-label">Gönderici Adı</label>
                    <select id="cdg-sms-origin" class="cdg-sms-select">
                        <?php if(empty($active_origins)): ?>
                        <option value="">Aktif gönderici adınız yok</option>
                        <?php else: foreach($active_origins as $o): ?>
                        <option value="<?php echo (int)($o['id'] ?? 0); ?>"><?php echo htmlspecialchars($o['name'] ?? ''); ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="cdg-sms-field">
                    <label class="cdg-sms-label">Toplam Karakter / Kredi</label>
                    <div style="padding:11px 14px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                        <strong id="cdg-sms-charnum">0</strong> karakter ·
                        <strong id="cdg-sms-credituse">0</strong> SMS kredisi
                    </div>
                </div>
            </div>

            <?php if(!empty($groups)): ?>
            <div class="cdg-sms-field">
                <label class="cdg-sms-label">Rehber Grupları (opsiyonel)</label>
                <div class="cdg-sms-checkbox-list">
                    <?php foreach($groups as $g):
                        $g_id = (int)($g['id'] ?? 0);
                        $g_name = $g['name'] ?? '';
                        $g_count = is_array($g['numbers'] ?? null) ? count($g['numbers']) : 0;
                    ?>
                    <label class="cdg-sms-checkbox-item">
                        <input type="checkbox" class="cdg-sms-group-cb" value="<?php echo $g_id; ?>" data-count="<?php echo $g_count; ?>">
                        <span><?php echo htmlspecialchars($g_name); ?> (<?php echo $g_count; ?>)</span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="cdg-sms-field">
                <label class="cdg-sms-label">Manuel Numaralar (her satıra bir numara, opsiyonel)</label>
                <textarea id="cdg-sms-numbers" class="cdg-sms-textarea" rows="4" placeholder="5301234567&#10;5302345678"></textarea>
                <div class="cdg-sms-meta">
                    Manuel numara: <strong id="cdg-sms-numcount">0</strong>
                </div>
            </div>

            <div class="cdg-sms-field">
                <label class="cdg-sms-label">Mesaj Metni</label>
                <textarea id="cdg-sms-message" class="cdg-sms-textarea" rows="5" placeholder="Mesaj içeriği..." onkeyup="cdgSmsCount()"><?php echo htmlspecialchars($cancel_link_text); ?></textarea>
            </div>

            <?php if(!empty($dimensions)): ?>
            <details style="margin-bottom:14px;">
                <summary style="cursor:pointer;font-size:12px;color:#64748b;font-weight:700;">📊 Karakter / Kredi Tablosu</summary>
                <table class="cdg-sms-table" style="margin-top:8px;">
                    <thead><tr><th>Karakter Aralığı</th><th>Kredi (SMS)</th></tr></thead>
                    <tbody>
                    <?php foreach($dimensions as $dim): ?>
                    <tr>
                        <td><?php echo (int)($dim['start'] ?? 0); ?> - <?php echo (int)($dim['end'] ?? 0); ?> karakter</td>
                        <td><strong><?php echo (int)($dim['credit'] ?? 1); ?></strong> SMS</td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
            <?php endif; ?>

            <button type="button" class="cdg-sms-btn cdg-sms-btn-primary" onclick="cdgSmsPreview()">
                <i class="bi bi-eye"></i> Ön İzle
            </button>
        </div>

        <!-- Preview -->
        <div class="cdg-sms-card" id="cdg-sms-preview-card" style="display:none;">
            <div class="cdg-sms-card-title"><i class="bi bi-check2-circle"></i> Gönderim Ön İzleme</div>
            <div class="cdg-sms-preview">
                <div class="cdg-sms-preview-row"><strong>Toplam Numara:</strong> <span id="cdg-sms-prev-count">0</span></div>
                <div class="cdg-sms-preview-row"><strong>Karakter:</strong> <span id="cdg-sms-prev-char">0</span></div>
                <div class="cdg-sms-preview-row"><strong>Toplam Kredi:</strong> <span id="cdg-sms-prev-credit"><strong style="color:#0e7490;">0</strong></span></div>
                <div class="cdg-sms-preview-row"><strong>Gönderici:</strong> <span id="cdg-sms-prev-origin"></span></div>
                <div class="cdg-sms-preview-row"><strong>Mesaj:</strong> <span id="cdg-sms-prev-message" style="white-space:pre-wrap;"></span></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:14px;">
                <button type="button" class="cdg-sms-btn cdg-sms-btn-outline" onclick="document.getElementById('cdg-sms-preview-card').style.display='none';">
                    <i class="bi bi-arrow-left"></i> Geri Dön
                </button>
                <button type="button" class="cdg-sms-btn cdg-sms-btn-success" onclick="cdgSmsSend(this)">
                    <i class="bi bi-send-fill"></i> SMS Gönder
                </button>
            </div>
        </div>
    </div>

    <!-- TAB: ORIGIN (Gönderici Adı Talebi) -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-origin">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-title"><i class="bi bi-person-badge-fill"></i> Yeni Gönderici Adı Talebi</div>
            <p style="font-size:13px;color:#64748b;margin:0 0 14px;">
                Gönderici adı (origin), SMS'in geldiği kaynaktır. En fazla 11 karakter olmalı.
                Talep onaylandıktan sonra SMS gönderiminde kullanılabilir.
            </p>
            <div class="cdg-sms-field">
                <label class="cdg-sms-label">Gönderici Adı (max 11 karakter)</label>
                <input type="text" id="cdg-sms-neworigin" class="cdg-sms-input" maxlength="11" placeholder="MARKAADI">
            </div>
            <button type="button" class="cdg-sms-btn cdg-sms-btn-primary" onclick="cdgSmsRequestOrigin(this)">
                <i class="bi bi-send"></i> Talep Gönder
            </button>
        </div>

        <?php if(!empty($origins)): ?>
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-title"><i class="bi bi-list-ul"></i> Mevcut Gönderici Adları</div>
            <table class="cdg-sms-table">
                <thead>
                    <tr><th>Gönderici Adı</th><th style="width:100px;">Durum</th><th style="width:140px;">Talep Tarihi</th></tr>
                </thead>
                <tbody>
                <?php foreach($origins as $o):
                    $st = $o['status'] ?? 'pending';
                    $st_label = ['active' => '✓ Aktif', 'pending' => '⏳ Onay Bekliyor', 'rejected' => '✗ Reddedildi'][$st] ?? $st;
                    $st_color = ['active' => '#10b981', 'pending' => '#f59e0b', 'rejected' => '#ef4444'][$st] ?? '#64748b';
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($o['name'] ?? ''); ?></strong></td>
                    <td><span style="font-size:11px;font-weight:700;color:<?php echo $st_color; ?>;"><?php echo $st_label; ?></span></td>
                    <td style="font-size:11px;color:#64748b;"><?php echo htmlspecialchars($o['cdate'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- TAB: GROUPS (Rehber) -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-groups">
        <div class="cdg-sms-card">
            <div class="cdg-sms-card-title"><i class="bi bi-folder-plus"></i> Yeni Grup Oluştur</div>
            <div style="display:grid;grid-template-columns:1fr auto;gap:8px;">
                <input type="text" id="cdg-sms-newgroup" class="cdg-sms-input" placeholder="Grup adı (örn: Müşteriler)">
                <button type="button" class="cdg-sms-btn cdg-sms-btn-primary" onclick="cdgSmsAddGroup(this)">
                    <i class="bi bi-plus-lg"></i> Ekle
                </button>
            </div>
        </div>

        <div class="cdg-sms-card">
            <div class="cdg-sms-card-title"><i class="bi bi-collection-fill"></i> Mevcut Gruplar</div>
            <?php if(!empty($groups)): ?>
            <div class="cdg-sms-group-list">
                <?php foreach($groups as $g):
                    $g_id = (int)($g['id'] ?? 0);
                    $g_name = $g['name'] ?? '';
                    $g_numbers = is_array($g['numbers'] ?? null) ? $g['numbers'] : [];
                ?>
                <div class="cdg-sms-group-item" id="cdg-sms-group-<?php echo $g_id; ?>" data-numbers="<?php echo htmlspecialchars(implode("\n", $g_numbers), ENT_QUOTES); ?>">
                    <div>
                        <div class="cdg-sms-group-name"><?php echo htmlspecialchars($g_name); ?></div>
                        <div class="cdg-sms-group-count"><?php echo count($g_numbers); ?> numara</div>
                    </div>
                    <div class="cdg-sms-group-actions">
                        <button type="button" class="cdg-sms-group-action" onclick="cdgSmsEditGroup(<?php echo $g_id; ?>, '<?php echo htmlspecialchars($g_name, ENT_QUOTES); ?>')" title="Numaraları Düzenle">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="cdg-sms-group-action danger" onclick="cdgSmsDeleteGroup(<?php echo $g_id; ?>)" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="cdg-sms-empty">
                <i class="bi bi-collection"></i>
                <div>Henüz grup oluşturulmamış</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Group Edit Modal (numara düzenleme) -->
        <div id="cdg-sms-group-edit" style="display:none;">
            <div class="cdg-sms-card">
                <div class="cdg-sms-card-title"><i class="bi bi-pencil-square"></i> Grup Numaralarını Düzenle: <span id="cdg-sms-edit-group-name"></span></div>
                <input type="hidden" id="cdg-sms-edit-group-id">
                <div class="cdg-sms-field">
                    <label class="cdg-sms-label">Numaralar (her satıra bir numara)</label>
                    <textarea id="cdg-sms-edit-numbers" class="cdg-sms-textarea" rows="8" placeholder="5301234567&#10;5302345678"></textarea>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="cdg-sms-btn cdg-sms-btn-outline" onclick="document.getElementById('cdg-sms-group-edit').style.display='none';">
                        <i class="bi bi-x-lg"></i> Vazgeç
                    </button>
                    <button type="button" class="cdg-sms-btn cdg-sms-btn-primary" onclick="cdgSmsSaveGroupNumbers(this)">
                        <i class="bi bi-save"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: PLACEHOLDER (kara liste/raporlar/kredi v3.3.2'de gelecek) -->
    <div class="cdg-sms-pane" id="cdg-sms-pane-black">
        <div class="cdg-sms-card" style="text-align:center;padding:40px;">
            <i class="bi bi-shield-x" style="font-size:48px;color:#94a3b8;"></i>
            <p style="margin:12px 0 0;color:#64748b;">Kara liste yönetimi v3.3.2'de eklenecek.</p>
        </div>
    </div>
    <div class="cdg-sms-pane" id="cdg-sms-pane-reports">
        <div class="cdg-sms-card" style="text-align:center;padding:40px;">
            <i class="bi bi-bar-chart" style="font-size:48px;color:#94a3b8;"></i>
            <p style="margin:12px 0 0;color:#64748b;">SMS raporları v3.3.2'de eklenecek.</p>
        </div>
    </div>
    <div class="cdg-sms-pane" id="cdg-sms-pane-credit">
        <div class="cdg-sms-card" style="text-align:center;padding:40px;">
            <i class="bi bi-coin" style="font-size:48px;color:#94a3b8;"></i>
            <p style="margin:12px 0 0;color:#64748b;">Kredi yenileme v3.3.2'de eklenecek.</p>
        </div>
    </div>

</div>

<script>
(function(){
    var cdgSmsUrl = '<?php echo htmlspecialchars($controller_url, ENT_QUOTES); ?>';
    var cdgSmsPid = <?php echo (int)($proanse['id'] ?? 0); ?>;
    var cdgSmsDimensions = <?php echo json_encode(array_values($dimensions), JSON_UNESCAPED_UNICODE); ?>;

    window.cdgSmsTab = function(tab) {
        document.querySelectorAll('.cdg-sms-tab').forEach(function(t){ t.classList.remove('active'); });
        document.querySelectorAll('.cdg-sms-pane').forEach(function(p){ p.classList.remove('active'); });
        var btn = document.querySelector('.cdg-sms-tab[data-tab="' + tab + '"]');
        var pane = document.getElementById('cdg-sms-pane-' + tab);
        if(btn) btn.classList.add('active');
        if(pane) pane.classList.add('active');
    };

    // Karakter sayısı + kredi hesaplama
    window.cdgSmsCount = function() {
        var msg = document.getElementById('cdg-sms-message').value;
        var len = msg.length;
        document.getElementById('cdg-sms-charnum').textContent = len;
        var credit = 1;
        if(cdgSmsDimensions && cdgSmsDimensions.length) {
            for(var i = 0; i < cdgSmsDimensions.length; i++) {
                var d = cdgSmsDimensions[i];
                if(len >= (d.start || 0) && len <= (d.end || 999)) {
                    credit = d.credit || 1;
                    break;
                }
            }
        }
        document.getElementById('cdg-sms-credituse').textContent = credit;
        return credit;
    };

    // Manuel numara sayısı
    var numbersEl = document.getElementById('cdg-sms-numbers');
    if(numbersEl) {
        numbersEl.addEventListener('keyup', function(){
            var lines = this.value.split('\n').filter(function(l){ return l.trim().length > 0; });
            document.getElementById('cdg-sms-numcount').textContent = lines.length;
        });
    }

    // Önizleme
    window.cdgSmsPreview = function() {
        var origin = document.getElementById('cdg-sms-origin');
        var msg = document.getElementById('cdg-sms-message').value.trim();
        var numbersText = (numbersEl && numbersEl.value || '').split('\n').filter(function(l){ return l.trim().length > 0; });
        var groupCbs = document.querySelectorAll('.cdg-sms-group-cb:checked');
        var totalCount = numbersText.length;
        groupCbs.forEach(function(cb){ totalCount += parseInt(cb.getAttribute('data-count')) || 0; });

        if(!origin || !origin.value) { if(typeof alert_error === 'function') alert_error('Gönderici adı seçin', {timer: 3000}); return; }
        if(!msg) { if(typeof alert_error === 'function') alert_error('Mesaj metni boş olamaz', {timer: 3000}); return; }
        if(totalCount === 0) { if(typeof alert_error === 'function') alert_error('Hiç numara seçili değil (manuel veya grup)', {timer: 3000}); return; }

        var credit = cdgSmsCount();
        var totalCredit = credit * totalCount;

        document.getElementById('cdg-sms-prev-count').textContent = totalCount;
        document.getElementById('cdg-sms-prev-char').textContent = msg.length;
        document.getElementById('cdg-sms-prev-credit').innerHTML = '<strong style="color:#0e7490;">' + totalCredit + '</strong> SMS';
        document.getElementById('cdg-sms-prev-origin').textContent = origin.options[origin.selectedIndex].text;
        document.getElementById('cdg-sms-prev-message').textContent = msg;

        document.getElementById('cdg-sms-preview-card').style.display = 'block';
        document.getElementById('cdg-sms-preview-card').scrollIntoView({behavior: 'smooth', block: 'nearest'});
    };

    // Send
    window.cdgSmsSend = function(btn) {
        var origin = document.getElementById('cdg-sms-origin').value;
        var msg = document.getElementById('cdg-sms-message').value.trim();
        var numbers = (numbersEl && numbersEl.value || '').trim();
        var groups = [];
        document.querySelectorAll('.cdg-sms-group-cb:checked').forEach(function(cb){ groups.push(cb.value); });

        if(typeof MioAjax !== 'function') return;
        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gönderiliyor...';

        MioAjax({
            url: cdgSmsUrl, type: 'post',
            data: { operation: 'submit_sms', id: cdgSmsPid, origin: origin, message: msg, numbers: numbers, 'groups[]': groups },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'SMS gönderildi', {timer: 2500});
                    setTimeout(function(){ window.location.reload(); }, 2000);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgSmsRefreshCredit = function(btn) {
        if(typeof MioAjax !== 'function') return;
        btn.disabled = true;
        MioAjax({
            url: cdgSmsUrl, type: 'post',
            data: { operation: 'get_credit', id: cdgSmsPid },
            result: function(r) {
                btn.disabled = false;
                if(r && (typeof r.credit !== 'undefined' || r.status === 'successful')) {
                    var c = (typeof r.credit !== 'undefined') ? r.credit : (r.data || 0);
                    document.getElementById('cdg-sms-credit-num').textContent = c;
                    if(typeof alert_success === 'function') alert_success('Kredi güncellendi', {timer: 1500});
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 3000});
                }
            }
        });
    };

    window.cdgSmsRequestOrigin = function(btn) {
        var inp = document.getElementById('cdg-sms-neworigin');
        var name = inp.value.trim();
        if(!name) { if(typeof alert_error === 'function') alert_error('Gönderici adı girin', {timer: 3000}); return; }
        if(name.length > 11) { if(typeof alert_error === 'function') alert_error('En fazla 11 karakter', {timer: 3000}); return; }
        if(typeof MioAjax !== 'function') return;

        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gönderiliyor...';

        MioAjax({
            url: cdgSmsUrl, type: 'post',
            data: { operation: 'new_origin_request', id: cdgSmsPid, origin: name },
            result: function(r) {
                btn.disabled = false; btn.innerHTML = orig;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Talep gönderildi', {timer: 2000});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgSmsAddGroup = function(btn) {
        var inp = document.getElementById('cdg-sms-newgroup');
        var name = inp.value.trim();
        if(!name) { if(typeof alert_error === 'function') alert_error('Grup adı girin', {timer: 3000}); return; }
        if(typeof MioAjax !== 'function') return;

        btn.disabled = true;
        MioAjax({
            url: cdgSmsUrl, type: 'post',
            data: { operation: 'add_new_group', id: cdgSmsPid, name: name },
            result: function(r) {
                btn.disabled = false;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Grup oluşturuldu', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgSmsEditGroup = function(gid, gname) {
        var item = document.getElementById('cdg-sms-group-' + gid);
        var numbers = item ? item.getAttribute('data-numbers') : '';
        document.getElementById('cdg-sms-edit-group-id').value = gid;
        document.getElementById('cdg-sms-edit-group-name').textContent = gname;
        document.getElementById('cdg-sms-edit-numbers').value = numbers;
        document.getElementById('cdg-sms-group-edit').style.display = 'block';
        document.getElementById('cdg-sms-group-edit').scrollIntoView({behavior: 'smooth'});
    };

    window.cdgSmsSaveGroupNumbers = function(btn) {
        var gid = document.getElementById('cdg-sms-edit-group-id').value;
        var numbers = document.getElementById('cdg-sms-edit-numbers').value;
        if(!gid) return;
        if(typeof MioAjax !== 'function') return;

        btn.disabled = true;
        MioAjax({
            url: cdgSmsUrl, type: 'post',
            data: { operation: 'change_group_numbers', id: cdgSmsPid, group_id: gid, numbers: numbers },
            result: function(r) {
                btn.disabled = false;
                if(r && r.status === 'successful') {
                    if(typeof alert_success === 'function') alert_success(r.message || 'Numaralar güncellendi', {timer: 1500});
                    setTimeout(function(){ window.location.reload(); }, 1500);
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    window.cdgSmsDeleteGroup = function(gid) {
        if(!confirm('Bu grubu silmek istediğinize emin misiniz? Tüm numaraları da silinecektir.')) return;
        if(typeof MioAjax !== 'function') return;
        MioAjax({
            url: cdgSmsUrl, type: 'post',
            data: { operation: 'delete_group', id: cdgSmsPid, group_id: gid },
            result: function(r) {
                if(r && r.status === 'successful') {
                    var item = document.getElementById('cdg-sms-group-' + gid);
                    if(item) item.remove();
                    if(typeof alert_success === 'function') alert_success(r.message || 'Grup silindi', {timer: 1500});
                } else if(r && r.message && typeof alert_error === 'function') {
                    alert_error(r.message, {timer: 4000});
                }
            }
        });
    };

    // İlk hesaplama
    cdgSmsCount();
})();
</script>
