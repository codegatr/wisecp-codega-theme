<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain DNS Records Yönetim Modal
 * ac-product-domain.php tarafından include edilir, JS ile aç/kapa
 *
 * Operations: add_dns_record, update_dns_record, delete_dns_record
 * Records URL: ?bring=dns-records (sayfanın kendisinde GET ile)
 *
 * WiseCP runtime: $proanse, $module_con, $links
 */

if(!isset($cdg_domain_modals_loaded)) $cdg_domain_modals_loaded = ['css' => false, 'js' => false];

$d_id = $proanse['id'] ?? 0;
$controller_url = isset($links['controller']) ? $links['controller'] : '';
$current_page_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

// TTL seçenekleri (saniye → label)
$ttl_options = [
    60 => '1 dk', 120 => '2 dk', 300 => '5 dk', 600 => '10 dk',
    900 => '15 dk', 1800 => '30 dk', 3600 => '1 sa', 7200 => '2 sa',
    18000 => '5 sa', 43200 => '12 sa', 86400 => '1 gün',
];
?>

<?php if(!$cdg_domain_modals_loaded['css']): $cdg_domain_modals_loaded['css'] = true; ?>
<style>
/* === Codega Domain Modal Genel Stiller === */
.cdg-dm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.55);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9000;
    padding: 20px;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    animation: cdgDmFade 0.22s ease;
}
.cdg-dm-overlay *, .cdg-dm-overlay *::before, .cdg-dm-overlay *::after { box-sizing: border-box; }
.cdg-dm-overlay.cdg-dm-open { display: flex; }
@keyframes cdgDmFade { from { opacity: 0; } to { opacity: 1; } }

.cdg-dm-modal {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 24px 60px rgba(15,23,42,0.30);
    width: 100%;
    max-width: 980px;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: cdgDmSlide 0.28s ease;
}
@keyframes cdgDmSlide { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.cdg-dm-head {
    padding: 18px 24px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: #fff;
    display: flex; justify-content: space-between; align-items: center;
    flex-shrink: 0;
}
.cdg-dm-head h3 {
    font-size: 17px; font-weight: 800; margin: 0;
    display: inline-flex; align-items: center; gap: 10px;
}
.cdg-dm-close {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    color: #fff;
    border: 0;
    cursor: pointer;
    font-size: 14px;
    display: grid; place-items: center;
    transition: background 0.15s;
}
.cdg-dm-close:hover { background: rgba(255,255,255,0.30); }

.cdg-dm-body { padding: 24px; overflow-y: auto; flex: 1; }

.cdg-dm-info {
    background: #dbeafe;
    color: #1e3a8a;
    border: 1px solid #93c5fd;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 18px;
    font-size: 13px;
    display: flex; align-items: flex-start; gap: 8px;
    line-height: 1.5;
}
.cdg-dm-info i { color: #1e40af; flex-shrink: 0; margin-top: 2px; font-size: 16px; }

.cdg-dm-form {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 18px;
}
.cdg-dm-form-row {
    display: grid;
    grid-template-columns: 110px 1fr 2fr 100px 60px;
    gap: 8px;
    align-items: end;
}
.cdg-dm-form .cdg-dm-field { margin: 0; }
.cdg-dm-field-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}
.cdg-dm-input,
.cdg-dm-select {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    color: #0f172a;
    background: #fff;
    outline: none;
    transition: border 0.15s;
    font-family: inherit;
}
.cdg-dm-input:focus,
.cdg-dm-select:focus {
    border-color: #1e40af;
    box-shadow: 0 0 0 3px rgba(30,64,175,0.10);
}
.cdg-dm-form-add-btn {
    padding: 9px 12px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    border: 0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(16,185,129,0.22);
    font-family: inherit;
    transition: transform 0.15s;
}
.cdg-dm-form-add-btn:hover { transform: translateY(-1px); }

.cdg-dm-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
.cdg-dm-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.cdg-dm-table thead th {
    background: #f8fafc;
    color: #64748b;
    padding: 10px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-dm-table tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #e2e8f0;
    color: #0f172a;
    vertical-align: middle;
}
.cdg-dm-table tbody tr:last-child td { border-bottom: 0; }
.cdg-dm-table .dns-record-type {
    font-weight: 700;
    color: #1e40af;
    background: #eff6ff;
    padding: 4px 10px !important;
    border-radius: 6px;
    display: inline-block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-dm-table .show-wrap-ttl,
.cdg-dm-table .show-wrap-priority {
    color: #64748b;
    font-size: 12px;
}

.cdg-dm-row-btn {
    width: 32px; height: 32px;
    border-radius: 7px;
    display: inline-grid; place-items: center;
    background: #fff;
    color: #64748b;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.15s;
    margin-right: 4px;
    font-family: inherit;
}
.cdg-dm-row-btn:hover { border-color: #1e40af; color: #1e40af; }
.cdg-dm-row-btn-danger { color: #ef4444; border-color: #fecaca; }
.cdg-dm-row-btn-danger:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
.cdg-dm-row-btn-success { color: #10b981; border-color: #bbf7d0; }
.cdg-dm-row-btn-success:hover { background: #10b981; color: #fff; border-color: #10b981; }

.cdg-dm-empty {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
}
.cdg-dm-empty i { font-size: 38px; color: #cbd5e1; display: block; margin-bottom: 8px; }

.cdg-dm-loading { text-align: center; padding: 30px; color: #64748b; font-size: 13px; }
.cdg-dm-loading i { font-size: 22px; animation: cdgDmSpin 1s linear infinite; display: block; margin-bottom: 6px; color: #1e40af; }
@keyframes cdgDmSpin { from { transform: rotate(0); } to { transform: rotate(360deg); } }

@media (max-width: 720px) {
    .cdg-dm-form-row { grid-template-columns: 1fr 1fr; }
    .cdg-dm-form-row > *:nth-child(3) { grid-column: 1 / -1; }
    .cdg-dm-form-row > *:nth-child(4) { grid-column: 1 / 2; }
    .cdg-dm-form-row > *:nth-child(5) { grid-column: 2 / -1; }
}
</style>
<?php endif; ?>

<!-- DNS RECORDS MODAL -->
<div class="cdg-dm-overlay" id="cdg-dns-records-modal" role="dialog" aria-modal="true">
    <div class="cdg-dm-modal">
        <div class="cdg-dm-head">
            <h3><i class="bi bi-list-task"></i> DNS Kayıtları Yönetimi</h3>
            <button type="button" class="cdg-dm-close" onclick="cdgDomain.closeModal('cdg-dns-records-modal')" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-dm-body">
            <div class="cdg-dm-info">
                <i class="bi bi-info-circle-fill"></i>
                <div>A, AAAA, CNAME, MX, TXT, SRV, NS gibi DNS kayıtlarını buradan yönetebilirsiniz. Değişikliklerin yayılması 24-48 saat sürebilir.</div>
            </div>

            <!-- Yeni Kayıt Ekleme Formu -->
            <div class="cdg-dm-form">
                <div style="font-size:12px;font-weight:800;color:#1e40af;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-plus-circle"></i> Yeni Kayıt Ekle
                </div>
                <div class="cdg-dm-form-row">
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Tip</label>
                        <select id="DnsRecord_type" class="cdg-dm-select">
                            <option value="A">A</option>
                            <option value="AAAA">AAAA</option>
                            <option value="CNAME">CNAME</option>
                            <option value="MX">MX</option>
                            <option value="TXT">TXT</option>
                            <option value="SRV">SRV</option>
                            <option value="NS">NS</option>
                        </select>
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Ad / Subdomain</label>
                        <input type="text" id="DnsRecord_name" class="cdg-dm-input" placeholder="@ veya www">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Değer</label>
                        <input type="text" id="DnsRecord_value" class="cdg-dm-input" placeholder="The IPV4 Address">
                    </div>
                    <div class="cdg-dm-field" id="DnsRecord_priority_wrap" style="display:none;">
                        <label class="cdg-dm-field-label">Öncelik</label>
                        <input type="number" id="DnsRecord_priority" class="cdg-dm-input" value="10" min="0">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">TTL</label>
                        <select id="DnsRecord_ttl" class="cdg-dm-select">
                            <option value="">Auto</option>
                            <?php foreach($ttl_options as $sec => $label): ?>
                            <option value="<?php echo $sec; ?>"><?php echo htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">&nbsp;</label>
                        <button type="button" class="cdg-dm-form-add-btn" onclick="cdgDomain.dnsRecordAdd()" title="Ekle">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mevcut Kayıtlar Tablosu -->
            <div class="cdg-dm-table-wrap">
                <table class="cdg-dm-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">Tip</th>
                            <th>Ad</th>
                            <th>Değer</th>
                            <th style="width:140px;">TTL</th>
                            <th style="width:120px;text-align:center;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="getDnsRecords_tbody">
                        <?php
                        $cdg_dns_records = isset($cdg_dns_records) && is_array($cdg_dns_records) ? $cdg_dns_records : [];
                        if(empty($cdg_dns_records)):
                        ?>
                        <tr><td colspan="5"><div class="cdg-dm-empty" style="padding:24px;text-align:center;color:#64748b;"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:6px;opacity:0.4;"></i><p style="margin:0;font-size:13px;">Henuz DNS kaydi yok</p></div></td></tr>
                        <?php else:
                            foreach($cdg_dns_records as $k => $r):
                                $r_type = $r['type'] ?? '';
                                $r_name = $r['name'] ?? '';
                                $r_value = $r['value'] ?? '';
                                $r_ttl = (int)($r['ttl'] ?? 3600);
                                $r_identity = $r['identity'] ?? '';

                                // TTL formatlanmasi
                                $ttl_text = $r_ttl . ' sn';
                                if($r_ttl >= 86400) $ttl_text = round($r_ttl / 86400) . ' gun';
                                elseif($r_ttl >= 3600) $ttl_text = round($r_ttl / 3600) . ' sa';
                                elseif($r_ttl >= 60) $ttl_text = round($r_ttl / 60) . ' dk';
                        ?>
                        <tr id="DnsRecord_<?php echo (int)$k; ?>" data-record-key="<?php echo (int)$k; ?>">
                            <td style="font-weight:700;color:#1e40af;"><?php echo htmlspecialchars($r_type, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td style="font-family:monospace;font-size:13px;"><?php echo htmlspecialchars($r_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td style="font-family:monospace;font-size:13px;word-break:break-all;"><?php echo htmlspecialchars($r_value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ttl_text, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td style="text-align:center;">
                                <button type="button" onclick="cdgDomain.dnsRecordEdit(<?php echo (int)$k; ?>, <?php echo htmlspecialchars(json_encode([
                                    'type' => $r_type, 'name' => $r_name, 'value' => $r_value, 'ttl' => $r_ttl, 'identity' => $r_identity
                                ], JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>)" title="Duzenle" style="background:#3b82f6;color:#fff;border:0;padding:6px 10px;border-radius:6px;cursor:pointer;margin-right:4px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" onclick="cdgDomain.dnsRecordDelete(<?php echo (int)$k; ?>, '<?php echo htmlspecialchars(addslashes($r_identity), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>', '<?php echo htmlspecialchars(addslashes($r_type), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>', '<?php echo htmlspecialchars(addslashes($r_name), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>')" title="Sil" style="background:#ef4444;color:#fff;border:0;padding:6px 10px;border-radius:6px;cursor:pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
