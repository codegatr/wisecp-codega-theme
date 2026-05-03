<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Domain Modallari Ortak CSS
 * Bu dosya ana ac-product-domain.php tarafindan herhangi bir modal include
 * edilmeden ONCE include edilmelidir. Boyle olmazsa modallar styling-siz gozukur.
 */

if(!isset($cdg_domain_modals_loaded)) $cdg_domain_modals_loaded = ['css' => false, 'js' => false];
if($cdg_domain_modals_loaded['css']) return;
$cdg_domain_modals_loaded['css'] = true;
?>
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
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    flex-shrink: 0;
}
.cdg-dm-head h3 {
    margin: 0; font-size: 17px; font-weight: 800;
    display: flex; align-items: center; gap: 10px;
}
.cdg-dm-head h3 i { font-size: 20px; }
.cdg-dm-close {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.30);
    color: #fff;
    width: 32px; height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: grid; place-items: center;
    transition: background 0.2s;
}
.cdg-dm-close:hover { background: rgba(255,255,255,0.30); }

.cdg-dm-body {
    padding: 20px 22px;
    overflow-y: auto;
    flex: 1;
}

.cdg-dm-info {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 14px;
    background: #eff6ff;
    border-left: 4px solid #00D3E5;
    border-radius: 8px;
    color: #1A2332;
    font-size: 13px;
    margin-bottom: 18px;
}
.cdg-dm-info i { color: #00D3E5; font-size: 18px; flex-shrink: 0; margin-top: 2px; }

.cdg-dm-empty {
    padding: 30px 20px; text-align: center;
    color: #64748b;
}
.cdg-dm-empty i { font-size: 36px; display: block; margin-bottom: 8px; opacity: 0.4; }
.cdg-dm-empty p { margin: 0; font-size: 13px; }

.cdg-dm-loading {
    padding: 24px 20px; text-align: center;
    color: #64748b;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.cdg-dm-loading i { animation: cdgDmSpin 1s linear infinite; }
@keyframes cdgDmSpin { from { transform: rotate(0); } to { transform: rotate(360deg); } }

.cdg-dm-form {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 16px;
}
.cdg-dm-field { margin-bottom: 10px; }
.cdg-dm-field-label {
    display: block;
    font-size: 12px; font-weight: 700; color: #475569;
    margin-bottom: 5px;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.cdg-dm-input,
.cdg-dm-select {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    font-family: inherit; font-size: 13px;
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
}
.cdg-dm-input:focus,
.cdg-dm-select:focus {
    border-color: #00D3E5;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    outline: none;
}

.cdg-dm-form-add-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    border: 0;
    border-radius: 7px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    text-decoration: none;
    white-space: nowrap;
}
.cdg-dm-form-add-btn:hover { opacity: .9; transform: translateY(-1px); }
.cdg-dm-form-add-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.cdg-dm-table-wrap { overflow-x: auto; }
.cdg-dm-table {
    width: 100%;
    border-collapse: collapse;
}
.cdg-dm-table thead th {
    background: #f1f5f9;
    color: #475569;
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.5px;
    text-align: left; padding: 10px 12px;
    border-bottom: 1px solid #e2e8f0;
}
.cdg-dm-table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
    color: #1e293b;
}
.cdg-dm-table tbody tr:hover {
    background: #f8fafc;
}

.cdg-dm-btn-icon {
    background: #00D3E5;
    color: #fff;
    border: 0;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 4px;
    transition: opacity .2s;
}
.cdg-dm-btn-icon:hover { opacity: 0.85; }

@media (max-width: 768px) {
    .cdg-dm-head { padding: 14px 16px; }
    .cdg-dm-head h3 { font-size: 15px; }
    .cdg-dm-body { padding: 14px 16px; }
}
</style>
