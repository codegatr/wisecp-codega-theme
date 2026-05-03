<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Adres Yonetimi Tab + Modal
 *
 * Operations:
 *  - addNewAddress: Yeni adres ekle
 *  - editAddress: Adres duzenle
 *  - DeleteAddress: Adres sil
 *  - updateDefaultAddress: Varsayilan adresi degistir
 *
 * WiseCP runtime:
 *  - $acAddresses: Adresler dizisi (id, name, kind, full_name, company_*, email, gsm, identity, country_id, city, counti, zipcode, address, address_line, detouse)
 *  - $countryList: Ulke listesi
 *  - $operation_link
 */

$ac_addresses = isset($acAddresses) && is_array($acAddresses) ? $acAddresses : [];
$ac_country_list = isset($countryList) && is_array($countryList) ? $countryList : [];
$ac_op_link = isset($operation_link) ? $operation_link : (isset($links['controller']) ? $links['controller'] : '');
?>

<style>
.cdg-addr-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}
.cdg-addr-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
    transition: all 0.15s;
    position: relative;
}
.cdg-addr-card:hover { border-color: #2E3B4E; box-shadow: 0 4px 12px rgba(46,59,78,0.08); }
.cdg-addr-card.is-default { border-color: #10b981; background: linear-gradient(180deg, #f0fdf4 0%, #fff 30%); }
.cdg-addr-card.is-default::before {
    content: 'VARSAYILAN';
    position: absolute;
    top: -8px; right: 14px;
    background: #10b981;
    color: #fff;
    font-size: 9px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 99px;
    letter-spacing: 0.6px;
}
.cdg-addr-card-name { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.cdg-addr-card-kind {
    display: inline-block;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    background: #eff6ff;
    color: #2E3B4E;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cdg-addr-card-kind.corporate { background: #fef3c7; color: #92400e; }
.cdg-addr-card-line { font-size: 12px; color: #64748b; line-height: 1.5; margin-bottom: 12px; min-height: 36px; }
.cdg-addr-card-actions { display: flex; gap: 6px; flex-wrap: wrap; }
.cdg-addr-card-btn {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 6px 12px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
    transition: all 0.15s;
    font-family: inherit;
}
.cdg-addr-card-btn:hover { border-color: #2E3B4E; color: #2E3B4E; }
.cdg-addr-card-btn.danger { color: #ef4444; border-color: #fecaca; }
.cdg-addr-card-btn.danger:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
.cdg-addr-card-btn.success { color: #10b981; border-color: #bbf7d0; }
.cdg-addr-card-btn.success:hover { background: #10b981; color: #fff; border-color: #10b981; }

/* Modal */
.cdg-addr-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15,23,42,0.55);
    z-index: 9998;
    display: none;
    align-items: flex-start;
    justify-content: center;
    overflow-y: auto;
    padding: 30px 16px;
}
.cdg-addr-overlay.open { display: flex; animation: cdgAddrFade 0.2s ease; }
@keyframes cdgAddrFade { from { opacity: 0; } to { opacity: 1; } }
.cdg-addr-modal {
    background: #fff;
    border-radius: 14px;
    width: 100%;
    max-width: 640px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.20);
    margin: auto;
}
.cdg-addr-modal-head {
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.cdg-addr-modal-head h3 { font-size: 16px; margin: 0; font-weight: 800; }
.cdg-addr-modal-close {
    background: rgba(255,255,255,0.18);
    border: 0;
    color: #fff;
    width: 30px; height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: grid;
    place-items: center;
}
.cdg-addr-modal-body { padding: 24px; max-height: calc(100vh - 200px); overflow-y: auto; }
.cdg-addr-modal-foot {
    padding: 12px 22px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

/* Default address selector accordion */
.cdg-addr-default-box {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 1px solid #86efac;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 18px;
}
</style>

<div class="cdg-card">
    <div class="cdg-card-head" style="display:flex;justify-content:space-between;align-items:center;">
        <h3><i class="bi bi-geo-alt-fill"></i> Adres Yonetimi</h3>
        <button type="button" class="cdg-btn cdg-btn-primary cdg-btn-sm" onclick="cdgAddrOpen('add')">
            <i class="bi bi-plus-lg"></i> Yeni Adres
        </button>
    </div>

    <?php if(!empty($ac_addresses)): ?>
    <!-- Varsayilan Adres Secici -->
    <div class="cdg-addr-default-box">
        <h4 style="font-size:13px;font-weight:800;color:#15803d;margin:0 0 10px;">
            <i class="bi bi-bookmark-star"></i> Varsayilan Adres
        </h4>
        <p style="font-size:12px;color:#166534;margin:0 0 12px;">Faturalarinizda bu adres bilgileri kullanilir.</p>

        <form method="post" action="<?php echo htmlspecialchars($ac_op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" id="cdgAddrDefaultForm">
            <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
            <input type="hidden" name="operation" value="updateDefaultAddress">

            <div style="display:grid;grid-template-columns:1fr auto;gap:8px;">
                <select name="address_id" class="cdg-form-control" style="font-size:13px;">
                    <?php foreach($ac_addresses as $addr):
                        $a_id = $addr['id'] ?? 0;
                        $a_name = $addr['name'] ?? 'Adres';
                        $a_line = $addr['address_line'] ?? '';
                        $is_def = !empty($addr['detouse']);
                    ?>
                    <option value="<?php echo (int)$a_id; ?>" <?php echo $is_def ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($a_name . ($a_line ? ' - ' . mb_strimwidth($a_line, 0, 50, '...') : ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="cdg-btn cdg-btn-success">
                    <i class="bi bi-check2"></i> Kaydet
                </button>
            </div>

            <label style="display:flex;align-items:center;gap:6px;margin-top:10px;font-size:12px;color:#15803d;cursor:pointer;">
                <input type="checkbox" name="overwritenadoninv" value="1">
                Mevcut faturalarima da uygulansin
            </label>
        </form>
    </div>

    <!-- Adres Listesi -->
    <div class="cdg-addr-grid">
        <?php foreach($ac_addresses as $addr):
            $a_id = $addr['id'] ?? 0;
            $a_name = $addr['name'] ?? 'Adres';
            $a_line = $addr['address_line'] ?? '';
            $a_kind = $addr['kind'] ?? 'individual';
            $a_default = !empty($addr['detouse']);
            $a_full_name = $addr['full_name'] ?? '';
            $a_company = $addr['company_name'] ?? '';
            $a_email = $addr['email'] ?? '';
            $a_phone = $addr['phone'] ?? ($addr['gsm'] ?? '');
        ?>
        <div class="cdg-addr-card<?php echo $a_default ? ' is-default' : ''; ?>" data-addr-id="<?php echo (int)$a_id; ?>"
             data-addr-payload='<?php echo htmlspecialchars(json_encode($addr, JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>'>
            <div class="cdg-addr-card-name">
                <?php echo htmlspecialchars($a_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                <span class="cdg-addr-card-kind <?php echo $a_kind === 'corporate' ? 'corporate' : ''; ?>">
                    <?php echo $a_kind === 'corporate' ? 'Kurumsal' : 'Bireysel'; ?>
                </span>
            </div>

            <?php if($a_kind === 'corporate' && $a_company): ?>
            <div style="font-size:11px;color:#92400e;font-weight:600;margin-bottom:4px;">
                <i class="bi bi-building"></i> <?php echo htmlspecialchars($a_company, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </div>
            <?php elseif($a_full_name): ?>
            <div style="font-size:11px;color:#475569;margin-bottom:4px;">
                <i class="bi bi-person"></i> <?php echo htmlspecialchars($a_full_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
            </div>
            <?php endif; ?>

            <div class="cdg-addr-card-line"><?php echo htmlspecialchars($a_line, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>

            <?php if($a_email || $a_phone): ?>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:10px;display:flex;flex-direction:column;gap:2px;">
                <?php if($a_email): ?><span><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($a_email, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><?php endif; ?>
                <?php if($a_phone): ?><span><i class="bi bi-phone"></i> <?php echo htmlspecialchars($a_phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span><?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="cdg-addr-card-actions">
                <button type="button" class="cdg-addr-card-btn" onclick="cdgAddrOpen('edit', <?php echo (int)$a_id; ?>)">
                    <i class="bi bi-pencil"></i> Duzenle
                </button>
                <?php if(!$a_default): ?>
                <button type="button" class="cdg-addr-card-btn danger" onclick="cdgAddrDelete(<?php echo (int)$a_id; ?>)">
                    <i class="bi bi-trash"></i> Sil
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <div style="text-align:center;padding:36px 20px;color:#94a3b8;">
        <i class="bi bi-geo-alt" style="font-size:42px;display:block;margin-bottom:8px;opacity:0.5;"></i>
        <p style="font-size:14px;margin:0 0 14px;">Henuz adres eklenmemis.</p>
        <button type="button" class="cdg-btn cdg-btn-primary" onclick="cdgAddrOpen('add')">
            <i class="bi bi-plus-lg"></i> Ilk Adresimi Ekle
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- ADRES MODAL (Ekleme + Duzenleme) -->
<div class="cdg-addr-overlay" id="cdg-addr-modal" role="dialog" aria-modal="true">
    <div class="cdg-addr-modal">
        <div class="cdg-addr-modal-head">
            <h3 id="cdg-addr-modal-title"><i class="bi bi-geo-alt-fill"></i> Yeni Adres Ekle</h3>
            <button type="button" class="cdg-addr-modal-close" onclick="cdgAddrClose()" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-addr-modal-body">
            <form id="cdgAddrForm" method="post" action="<?php echo htmlspecialchars($ac_op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" autocomplete="off">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('account'); ?>
                <input type="hidden" name="operation" value="addNewAddress" id="cdg-addr-operation">
                <input type="hidden" name="id" value="0" id="cdg-addr-id">

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Adres Etiketi <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" id="cdg-addr-name" class="cdg-form-control" placeholder="Örnek: Ev, Is Yeri, Fatura Adresi" required>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Tur <span style="color:#ef4444;">*</span></label>
                    <div style="display:flex;gap:8px;">
                        <label style="flex:1;padding:8px 12px;border:2px solid #2E3B4E;border-radius:8px;background:#eff6ff;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;">
                            <input type="radio" name="kind" value="individual" id="cdg-addr-kind-ind" checked onchange="cdgAddrKind(this.value)">
                            <i class="bi bi-person"></i> Bireysel
                        </label>
                        <label style="flex:1;padding:8px 12px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;">
                            <input type="radio" name="kind" value="corporate" id="cdg-addr-kind-cor" onchange="cdgAddrKind(this.value)">
                            <i class="bi bi-building"></i> Kurumsal
                        </label>
                    </div>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Ad Soyad <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="full_name" id="cdg-addr-full_name" class="cdg-form-control" required>
                </div>

                <!-- BIREYSEL: TC Kimlik -->
                <div class="cdg-form-group cdg-addr-individual">
                    <label class="cdg-form-label">TC Kimlik No</label>
                    <input type="text" name="identity" id="cdg-addr-identity" class="cdg-form-control" maxlength="11" placeholder="11 haneli">
                </div>

                <!-- KURUMSAL: Sirket bilgileri -->
                <div class="cdg-addr-corporate" style="display:none;">
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Şirket Adı <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="company_name" id="cdg-addr-company_name" class="cdg-form-control">
                    </div>
                    <div class="cdg-form-row">
                        <div class="cdg-form-group">
                            <label class="cdg-form-label">Vergi Dairesi</label>
                            <input type="text" name="company_tax_office" id="cdg-addr-company_tax_office" class="cdg-form-control">
                        </div>
                        <div class="cdg-form-group">
                            <label class="cdg-form-label">Vergi No</label>
                            <input type="text" name="company_tax_number" id="cdg-addr-company_tax_number" class="cdg-form-control" maxlength="11">
                        </div>
                    </div>
                </div>

                <div class="cdg-form-row">
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">E-posta</label>
                        <input type="email" name="email" id="cdg-addr-email" class="cdg-form-control">
                    </div>
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Telefon</label>
                        <input type="text" name="gsm" id="cdg-addr-gsm" class="cdg-form-control" placeholder="+90 555 ...">
                    </div>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Ülke</label>
                    <?php if(!empty($ac_country_list)): ?>
                    <select name="country_id" id="cdg-addr-country_id" class="cdg-form-control">
                        <option value="">Ülke seçiniz</option>
                        <?php foreach($ac_country_list as $c):
                            $c_id = is_array($c) ? ($c['id'] ?? '') : (string)$c;
                            $c_name = is_array($c) ? ($c['name'] ?? $c_id) : (string)$c;
                        ?>
                        <option value="<?php echo htmlspecialchars($c_id, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php echo htmlspecialchars($c_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php else: ?>
                    <input type="text" name="country_id" id="cdg-addr-country_id" class="cdg-form-control" value="215" placeholder="Ulke ID">
                    <?php endif; ?>
                </div>

                <div class="cdg-form-row">
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Şehir</label>
                        <input type="text" name="city" id="cdg-addr-city" class="cdg-form-control">
                    </div>
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Ilce</label>
                        <input type="text" name="counti" id="cdg-addr-counti" class="cdg-form-control">
                    </div>
                    <div class="cdg-form-group">
                        <label class="cdg-form-label">Posta Kodu</label>
                        <input type="text" name="zipcode" id="cdg-addr-zipcode" class="cdg-form-control">
                    </div>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Adres</label>
                    <textarea name="address" id="cdg-addr-address" class="cdg-form-control" rows="3"></textarea>
                </div>

                <div class="cdg-form-group" style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="detouse" value="1" id="cdg-addr-detouse">
                    <label for="cdg-addr-detouse" style="font-size:13px;cursor:pointer;color:#475569;">Bu adresi varsayilan olarak ayarla</label>
                </div>
            </form>
        </div>
        <div class="cdg-addr-modal-foot">
            <button type="button" class="cdg-btn cdg-btn-outline" onclick="cdgAddrClose()">Iptal</button>
            <button type="submit" form="cdgAddrForm" class="cdg-btn cdg-btn-primary">
                <i class="bi bi-check2"></i> Kaydet
            </button>
        </div>
    </div>
</div>

<script>
window.cdgAddrOpen = function(mode, addrId) {
    var modal = document.getElementById('cdg-addr-modal');
    var title = document.getElementById('cdg-addr-modal-title');
    var op = document.getElementById('cdg-addr-operation');
    var idInp = document.getElementById('cdg-addr-id');
    var form = document.getElementById('cdgAddrForm');
    if(!modal || !form) return;

    // Form sifirla
    form.reset();
    cdgAddrKind('individual');
    document.getElementById('cdg-addr-kind-ind').checked = true;

    if(mode === 'edit' && addrId) {
        title.innerHTML = '<i class="bi bi-pencil"></i> Adresi Duzenle';
        op.value = 'editAddress';
        idInp.value = addrId;

        // Karttan veriyi al
        var card = document.querySelector('.cdg-addr-card[data-addr-id="' + addrId + '"]');
        if(card) {
            try {
                var data = JSON.parse(card.getAttribute('data-addr-payload'));
                ['name','full_name','identity','company_name','company_tax_office','company_tax_number','email','gsm','country_id','city','counti','zipcode','address'].forEach(function(k){
                    var el = document.getElementById('cdg-addr-' + k);
                    if(el && data[k] !== undefined) el.value = data[k] || '';
                });
                if(!document.getElementById('cdg-addr-gsm').value && data.phone) {
                    document.getElementById('cdg-addr-gsm').value = data.phone;
                }
                var kind = data.kind || 'individual';
                if(kind === 'corporate') {
                    document.getElementById('cdg-addr-kind-cor').checked = true;
                    cdgAddrKind('corporate');
                }
                document.getElementById('cdg-addr-detouse').checked = !!data.detouse;
            } catch(e) { console.error('Adres parse hatasi:', e); }
        }
    } else {
        title.innerHTML = '<i class="bi bi-plus-circle"></i> Yeni Adres Ekle';
        op.value = 'addNewAddress';
        idInp.value = '0';
    }

    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
};

window.cdgAddrClose = function() {
    var modal = document.getElementById('cdg-addr-modal');
    if(modal) modal.classList.remove('open');
    document.body.style.overflow = '';
};

window.cdgAddrKind = function(kind) {
    var ind = document.querySelectorAll('.cdg-addr-individual');
    var cor = document.querySelectorAll('.cdg-addr-corporate');
    if(kind === 'corporate') {
        ind.forEach(function(e){ e.style.display = 'none'; });
        cor.forEach(function(e){ e.style.display = 'block'; });
    } else {
        ind.forEach(function(e){ e.style.display = 'block'; });
        cor.forEach(function(e){ e.style.display = 'none'; });
    }
    document.querySelectorAll('input[name="kind"]').forEach(function(r){
        var label = r.closest('label');
        if(!label) return;
        if(r.checked) {
            label.style.borderColor = '#2E3B4E';
            label.style.background = '#eff6ff';
        } else {
            label.style.borderColor = '#e2e8f0';
            label.style.background = '#fff';
        }
    });
};

window.cdgAddrDelete = function(addrId) {
    if(!confirm('Bu adresi silmek istediginize emin misiniz?')) return;
    if(typeof MioAjax !== 'function') {
        // Fallback form submit
        var f = document.createElement('form');
        f.method = 'post';
        f.action = '<?php echo htmlspecialchars($ac_op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>';
        f.innerHTML = '<input type="hidden" name="operation" value="DeleteAddress"><input type="hidden" name="id" value="'+addrId+'">';
        document.body.appendChild(f);
        f.submit();
        return;
    }

    MioAjax({
        url: '<?php echo htmlspecialchars($ac_op_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',
        type: 'post',
        data: { operation: 'DeleteAddress', id: addrId },
        result: function(r) {
            if(r && r.status === 'successful') {
                if(typeof alert_success === 'function') alert_success(r.message || 'Adres silindi', {timer: 1500});
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else if(r && r.message && typeof alert_error === 'function') {
                alert_error(r.message, {timer: 3000});
            }
        }
    });
};

// Outside click ile kapatma
document.getElementById('cdg-addr-modal').addEventListener('click', function(e) {
    if(e.target === this) cdgAddrClose();
});
</script>
