<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain Child Nameserver (CNS) Modal
 * Operations: domain_add_cns, domain_modify_cns, domain_delete_cns
 * Liste URL: ?bring=cns-list
 */

$d_id = $proanse['id'] ?? 0;
$d_name = $proanse['name'] ?? ($options['domain'] ?? 'domain.com');
?>

<!-- CHILD NAMESERVER MODAL -->
<div class="cdg-dm-overlay" id="cdg-cns-modal" role="dialog" aria-modal="true">
    <div class="cdg-dm-modal" style="max-width:780px;">
        <div class="cdg-dm-head">
            <h3><i class="bi bi-server"></i> Child Nameserver Yönetimi</h3>
            <button type="button" class="cdg-dm-close" onclick="cdgDomain.closeModal('cdg-cns-modal')" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-dm-body">
            <div class="cdg-dm-info">
                <i class="bi bi-info-circle-fill"></i>
                <div>Child Nameserver (özel NS), domain kayıt firmasında kayıtlı kendi nameserver'larınızdır. Örnek: <code style="background:rgba(30,64,175,0.10);padding:2px 6px;border-radius:4px;font-family:monospace;">ns1.<?php echo htmlspecialchars($d_name); ?></code> ve bu hostname'in IP adresi.</div>
            </div>

            <!-- Yeni CNS Ekleme -->
            <div class="cdg-dm-form">
                <div style="font-size:12px;font-weight:800;color:#1e40af;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-plus-circle"></i> Yeni Child Nameserver
                </div>
                <div style="display:grid;grid-template-columns:1.5fr 1fr 60px;gap:8px;align-items:end;">
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Nameserver Hostname</label>
                        <input type="text" id="CNS_ns" class="cdg-dm-input" placeholder="ns1.<?php echo htmlspecialchars($d_name); ?>">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">IP Adresi</label>
                        <input type="text" id="CNS_ip" class="cdg-dm-input" placeholder="192.168.1.1">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">&nbsp;</label>
                        <button type="button" class="cdg-dm-form-add-btn" onclick="cdgDomain.cnsAdd()" title="Ekle">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mevcut CNS Listesi -->
            <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                <i class="bi bi-list-ul"></i> Mevcut Child Nameserver'lar
            </div>
            <div class="cdg-dm-table-wrap">
                <table class="cdg-dm-table">
                    <thead>
                        <tr>
                            <th>Nameserver</th>
                            <th style="width:200px;">IP Adresi</th>
                            <th style="width:120px;text-align:center;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="cns-wrap">
                        <tr><td colspan="3"><div class="cdg-dm-loading"><i class="bi bi-arrow-clockwise"></i>CNS listesi yükleniyor...</div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
