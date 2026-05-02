<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain Email Forwards Modal
 * Operations: add_email_forward, update_email_forward, delete_email_forward
 * Liste URL: ?bring=email-forwards
 *
 * Email forwarding: prefix@domain.com → target@external.com
 *
 * WiseCP runtime: $proanse, $module_con
 */

$d_name = $proanse['name'] ?? ($options['domain'] ?? 'domain.com');
?>

<!-- EMAIL FORWARDS MODAL -->
<div class="cdg-dm-overlay" id="cdg-email-forwards-modal" role="dialog" aria-modal="true">
    <div class="cdg-dm-modal" style="max-width:880px;">
        <div class="cdg-dm-head">
            <h3><i class="bi bi-envelope-arrow-up"></i> E-Posta Yönlendirme</h3>
            <button type="button" class="cdg-dm-close" onclick="cdgDomain.closeModal('cdg-email-forwards-modal')" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdg-dm-body">
            <div class="cdg-dm-info">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    Domain'inize gelen e-postaları başka bir hesaba yönlendirebilirsiniz.
                    Örnek: <code style="background:rgba(30,64,175,0.10);padding:2px 6px;border-radius:4px;font-family:monospace;">info@<?php echo htmlspecialchars($d_name); ?></code> → <code style="background:rgba(30,64,175,0.10);padding:2px 6px;border-radius:4px;font-family:monospace;">size@gmail.com</code>
                </div>
            </div>

            <!-- Yeni Yönlendirme Ekleme -->
            <div class="cdg-dm-form">
                <div style="font-size:12px;font-weight:800;color:#1e40af;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="bi bi-plus-circle"></i> Yeni Yönlendirme
                </div>
                <div style="display:grid;grid-template-columns:1fr auto 2fr 60px;gap:8px;align-items:end;">
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Prefix (kaynak)</label>
                        <div style="display:flex;align-items:center;background:#fff;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                            <input type="text" id="EmailForward_prefix" class="cdg-dm-input" style="border:0;border-radius:0;flex:1;" placeholder="info">
                            <span style="padding:0 10px;color:#94a3b8;font-size:13px;font-weight:600;white-space:nowrap;">@<?php echo htmlspecialchars($d_name); ?></span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:end;padding-bottom:10px;color:#94a3b8;">
                        <i class="bi bi-arrow-right" style="font-size:18px;"></i>
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">Hedef E-Posta</label>
                        <input type="email" id="EmailForward_target" class="cdg-dm-input" placeholder="size@gmail.com">
                    </div>
                    <div class="cdg-dm-field">
                        <label class="cdg-dm-field-label">&nbsp;</label>
                        <button type="button" class="cdg-dm-form-add-btn" onclick="cdgDomain.emailForwardAdd()" title="Ekle">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mevcut Yönlendirmeler -->
            <div style="font-size:12px;font-weight:800;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                <i class="bi bi-list-ul"></i> Mevcut Yönlendirmeler
            </div>
            <div class="cdg-dm-table-wrap">
                <table class="cdg-dm-table">
                    <thead>
                        <tr>
                            <th>Kaynak</th>
                            <th style="width:30px;text-align:center;"></th>
                            <th>Hedef</th>
                            <th style="width:120px;text-align:center;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="getEmailForwards_tbody">
                        <tr><td colspan="4"><div class="cdg-dm-loading"><i class="bi bi-arrow-clockwise"></i>Yönlendirmeler yükleniyor...</div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
