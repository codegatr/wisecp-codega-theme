<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain DNSSEC Modal
 * Operations: add_dns_sec_record, delete_dns_sec_record
 * Liste URL: ?bring=dns-sec-records
 *
 * DNSSEC: Domain Name System Security Extensions
 * Required fields: digest, key_tag, digest_type, algorithm
 *
 * WiseCP runtime: $module_con (config: dns-digest-types, dns-algorithms)
 */

// Module config'den digest-types ve algorithms çek
$digest_types = [];
$algorithms = [];
if(is_object($module_con) && isset($module_con->config['settings'])) {
    $digest_types = $module_con->config['settings']['dns-digest-types'] ?? [];
    $algorithms   = $module_con->config['settings']['dns-algorithms'] ?? [];
}

// Yaygın varsayılanlar
if(empty($digest_types)) {
    $digest_types = [
        '1' => 'SHA-1',
        '2' => 'SHA-256',
        '3' => 'GOST R 34.11-94',
        '4' => 'SHA-384',
    ];
}
if(empty($algorithms)) {
    $algorithms = [
        '5'  => 'RSA/SHA-1',
        '7'  => 'RSASHA1-NSEC3-SHA1',
        '8'  => 'RSA/SHA-256',
        '10' => 'RSA/SHA-512',
        '13' => 'ECDSA Curve P-256',
        '14' => 'ECDSA Curve P-384',
    ];
}
?>

<!-- DNSSEC MODAL -->
<div class="cdg-dm-overlay" id="cdg-dnssec-modal" role="dialog" aria-modal="true">
    <div class="cdg-dm-modal" style="max-width:900px;">
        <div class="cdg-dm-head">
            <h3><i class="bi bi-shield-lock"></i> DNSSEC Yönetimi</h3>
            <button type="button" class="cdg-dm-close" onclick="cdgDomain.closeModal('cdg-dnssec-modal')" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-dm-body">
            <div class="cdg-dm-info">
                <i class="bi bi-shield-fill-check"></i>
                <div>
                    <strong>DNSSEC (DNS Security Extensions)</strong>, DNS yanıtlarının dijital imzalanmasıyla DNS önbellek zehirlenmesi (cache poisoning) saldırılarına karşı koruma sağlar.
                    DS (Delegation Signer) kayıtları, alt seviyedeki DNSSEC zincirini doğrular. Eklenecek değerler DNS sağlayıcınızdan alınır.
                </div>
            </div>

            <!-- Yeni DNSSEC Kaydı -->
            <div class="cdg-dm-form">
                <div style="font-size:12px;font-weight:800;color:#1e40af;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-plus-circle"></i> Yeni DS Kaydı Ekle
                </div>
                <div style="display:grid;grid-template-columns:1fr 110px 140px 1fr 60px;gap:8px;align-items:end;">
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Digest (Özet)</label>
                        <input type="text" id="DnsSecRecord_digest" class="cdg-dm-input" placeholder="64+ karakter hex değer">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Key Tag</label>
                        <input type="number" id="DnsSecRecord_key_tag" class="cdg-dm-input" placeholder="12345" min="0">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Digest Tipi</label>
                        <select id="DnsSecRecord_digest_type" class="cdg-dm-select">
                            <option value="">Seçin</option>
                            <?php foreach($digest_types as $k => $v): ?>
                            <option value="<?php echo htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Algoritma</label>
                        <select id="DnsSecRecord_algorithm" class="cdg-dm-select">
                            <option value="">Seçin</option>
                            <?php foreach($algorithms as $k => $v): ?>
                            <option value="<?php echo htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">&nbsp;</label>
                        <button type="button" class="cdg-dm-form-add-btn" onclick="cdgDomain.dnssecAdd()" title="Ekle">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mevcut DNSSEC Kayıtları -->
            <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                <i class="bi bi-list-ul"></i> Mevcut DS Kayıtları
            </div>
            <div class="cdg-dm-table-wrap">
                <table class="cdg-dm-table">
                    <thead>
                        <tr>
                            <th>Digest</th>
                            <th style="width:110px;">Key Tag</th>
                            <th style="width:140px;">Digest Tipi</th>
                            <th style="width:160px;">Algoritma</th>
                            <th style="width:80px;text-align:center;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="getDnsSecRecords_tbody">
                        <tr><td colspan="5"><div class="cdg-dm-loading"><i class="bi bi-arrow-clockwise"></i>DNSSEC kayıtları yükleniyor...</div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
