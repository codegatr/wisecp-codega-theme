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

<?php /* Modal CSS artik inc/ac-domain-modals-css.php icinde yuklenir */ ?>

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
                                ], JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>)" title="Düzenle" style="background:#3b82f6;color:#fff;border:0;padding:6px 10px;border-radius:6px;cursor:pointer;margin-right:4px;">
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
