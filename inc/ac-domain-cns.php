<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain Child Nameserver (CNS) Modal
 * .tr / .com.tr / .com gibi uzantilarda kayit firmasinda CNS yonetimi
 *
 * Operations:
 *   - domain_add_cns   (POST: id=domain_id, ns, ip)
 *   - domain_modify_cns (POST: id=cns_id, ns, ip)  <-- DIKKAT: id = CNS_ID, NOT domain_id!
 *   - domain_delete_cns (POST: id=cns_id)
 *
 * WiseCP runtime: $proanse, $options, $module_con, $links
 *   $cdg_cns_list - parent dosya tarafindan preload edildi (PHP-side)
 */

$d_id = (int)($proanse['id'] ?? 0);
$d_name = $proanse['name'] ?? ($options['domain'] ?? 'domain.com');
$cdg_cns_list = isset($cdg_cns_list) && is_array($cdg_cns_list) ? $cdg_cns_list : [];
$controller_url = $links['controller'] ?? '';
?>

<!-- CHILD NAMESERVER MODAL -->
<div class="cdg-dm-overlay" id="cdg-cns-modal" role="dialog" aria-modal="true">
    <div class="cdg-dm-modal" style="max-width:820px;">
        <div class="cdg-dm-head">
            <h3><i class="bi bi-server"></i> Child Nameserver Yönetimi</h3>
            <button type="button" class="cdg-dm-close" onclick="cdgDomain.closeModal('cdg-cns-modal')" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-dm-body">
            <div class="cdg-dm-info">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    Child Nameserver (özel NS), domain kayıt firmasında <strong><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong> domaininize bağlı tanımladığınız kendi nameserver'larınızdır.<br>
                    Örnek: <code style="background:rgba(46,59,78,0.10);padding:2px 6px;border-radius:4px;font-family:monospace;">ns1.<?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code> + IP adresi
                </div>
            </div>

            <!-- Yeni CNS Ekleme -->
            <div class="cdg-dm-form" style="margin-bottom:16px;">
                <div style="font-size:12px;font-weight:800;color:#2E3B4E;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-plus-circle"></i> Yeni Child Nameserver Ekle
                </div>
                <div style="display:grid;grid-template-columns:1.5fr 1fr 80px;gap:8px;align-items:end;">
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Nameserver Hostname</label>
                        <input type="text" id="CNS_ns" class="cdg-dm-input" placeholder="ns1.<?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">IP Adresi</label>
                        <input type="text" id="CNS_ip" class="cdg-dm-input" placeholder="192.168.1.1">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">&nbsp;</label>
                        <button type="button" class="cdg-dm-form-add-btn" onclick="cdgDomain.cnsAdd()" title="Ekle" style="width:100%;">
                            <i class="bi bi-plus-lg"></i> Ekle
                        </button>
                    </div>
                </div>
                <div style="font-size:11px;color:#64748b;margin-top:6px;">
                    <i class="bi bi-info-circle"></i> Hostname olarak yalnızca domaininize ait subdomain kullanılabilir (ör: <code><?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></code> alt alanları). IP adresi yetkili sunucu IP'si olmalıdır.
                </div>
            </div>

            <!-- Mevcut CNS Listesi -->
            <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                <i class="bi bi-list-ul"></i> Mevcut Child Nameserver'lar
                <?php if(!empty($cdg_cns_list)): ?>
                <span style="background:#CFFAFE;color:#2E3B4E;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:800;margin-left:6px;"><?php echo count($cdg_cns_list); ?></span>
                <?php endif; ?>
            </div>

            <div class="cdg-dm-table-wrap" id="cns-list-wrap">
                <?php if(empty($cdg_cns_list)): ?>
                <div class="cdg-dm-empty" style="padding:30px 20px;text-align:center;color:#64748b;">
                    <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                    <p style="margin:0;font-size:13px;">Henüz tanımlı bir Child Nameserver yok</p>
                    <p style="margin:4px 0 0;font-size:11px;color:#94a3b8;">Yukarıdaki formdan ekleyebilirsiniz</p>
                </div>
                <?php else: ?>
                <table class="cdg-dm-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Nameserver Hostname</th>
                            <th style="width:200px;">IP Adresi</th>
                            <th style="width:140px;text-align:center;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="cns-wrap">
                        <?php foreach($cdg_cns_list as $cns_id => $row):
                            $row_ns = $row['ns'] ?? '';
                            $row_ip = $row['ip'] ?? '';
                        ?>
                        <tr id="cns-row-<?php echo (int)$cns_id; ?>" data-cns-id="<?php echo (int)$cns_id; ?>">
                            <td>
                                <input type="text" id="cns-ns-<?php echo (int)$cns_id; ?>" value="<?php echo htmlspecialchars($row_ns, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-dm-input" style="width:100%;font-family:monospace;font-size:13px;" placeholder="ns1.<?php echo htmlspecialchars($d_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                            </td>
                            <td>
                                <input type="text" id="cns-ip-<?php echo (int)$cns_id; ?>" value="<?php echo htmlspecialchars($row_ip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-dm-input" style="width:100%;font-family:monospace;font-size:13px;" placeholder="192.168.1.1">
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="cdg-dm-btn-icon" onclick="cdgDomain.cnsModify(<?php echo (int)$cns_id; ?>)" title="Güncelle" style="background:#00D3E5;color:#fff;border:0;padding:6px 10px;border-radius:6px;cursor:pointer;margin-right:4px;">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" class="cdg-dm-btn-icon" onclick="cdgDomain.cnsDelete(<?php echo (int)$cns_id; ?>)" title="Sil" style="background:#ef4444;color:#fff;border:0;padding:6px 10px;border-radius:6px;cursor:pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
