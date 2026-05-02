<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Hosting Kullanim Grafikleri
 * Disk + Bandwidth kullanim yuzdesi (AJAX ile yuklenir)
 *
 * AJAX: get_hosting_informations operation -> response.usage
 *   - disk_used_percent, disk_used_format, disk_limit_format
 *   - bandwidth_used_percent, bandwidth_used_format, bandwidth_limit_format
 *
 * WiseCP runtime: $proanse, $supported, $module_con, $links
 */

$d_status = isset($proanse['status']) ? strtolower($proanse['status']) : 'unknown';
$is_active_or_suspended = ($d_status === 'active' || $d_status === 'suspended');
$supported = isset($supported) && is_array($supported) ? $supported : [];
$controller_url = $links['controller'] ?? '';

// Disk veya bandwidth usage destekleniyor mu?
$has_disk_usage = $is_active_or_suspended && in_array('disk-bandwidth-usage', $supported, true);
$has_bw_usage  = $has_disk_usage; // ikisi de ayni feature flag

// Kullanim ozelligi yoksa kart gosterme
if(!$has_disk_usage) return;

$d_id = (int)($proanse['id'] ?? 0);
?>

<style>
.cdg-host-usage {
    margin-top: 20px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.cdg-host-usage-head {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 18px;
    padding-bottom: 14px; border-bottom: 1px solid #e2e8f0;
}
.cdg-host-usage-head h3 {
    font-size: 16px; font-weight: 800; color: #1e293b;
    display: flex; align-items: center; gap: 8px;
    margin: 0;
}
.cdg-host-usage-head h3 i { color: #3b82f6; font-size: 18px; }
.cdg-host-usage-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 18px;
}
.cdg-host-usage-item {
    background: linear-gradient(135deg, #f8fafc, #fff);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
}
.cdg-host-usage-item-head {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 12px;
}
.cdg-host-usage-item-title {
    font-size: 13px; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.cdg-host-usage-item-title i { margin-right: 6px; }
.cdg-host-usage-percent {
    font-size: 22px; font-weight: 900; color: #1e40af;
    font-variant-numeric: tabular-nums;
}
.cdg-host-usage-percent.warning { color: #f59e0b; }
.cdg-host-usage-percent.danger  { color: #ef4444; }

.cdg-host-usage-bar {
    height: 10px; background: #f1f5f9; border-radius: 5px; overflow: hidden;
    margin-bottom: 8px;
}
.cdg-host-usage-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
    border-radius: 5px;
    transition: width .6s ease;
    width: 0%;
}
.cdg-host-usage-bar-fill.warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.cdg-host-usage-bar-fill.danger  { background: linear-gradient(90deg, #ef4444, #f87171); }

.cdg-host-usage-detail {
    display: flex; justify-content: space-between;
    font-size: 12px; color: #64748b;
    font-variant-numeric: tabular-nums;
}
.cdg-host-usage-detail strong { color: #1e293b; }

.cdg-host-usage-loading {
    display: flex; align-items: center; gap: 8px;
    padding: 14px; color: #64748b; font-size: 13px;
}
.cdg-host-usage-loading-dots {
    display: inline-flex; gap: 3px;
}
.cdg-host-usage-loading-dots span {
    width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;
    animation: cdgUsageDot 1.4s infinite both;
}
.cdg-host-usage-loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.cdg-host-usage-loading-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes cdgUsageDot {
    0%, 80%, 100% { opacity: 0.3; }
    40% { opacity: 1; }
}

.cdg-host-usage-error {
    display: flex; align-items: center; gap: 10px;
    padding: 12px; background: #fef2f2; border: 1px solid #fecaca;
    border-radius: 8px; color: #991b1b; font-size: 13px;
}
</style>

<div class="cdg-host-usage" id="cdg-host-usage">
    <div class="cdg-host-usage-head">
        <h3><i class="bi bi-speedometer2"></i> Kullanım Bilgileri</h3>
        <button type="button" id="cdg-host-usage-refresh" class="cdg-pd2-btn cdg-pd2-btn-outline" style="font-size:12px;padding:6px 12px;" onclick="cdgHostUsage.load()">
            <i class="bi bi-arrow-clockwise"></i> Yenile
        </button>
    </div>

    <div id="cdg-host-usage-content">
        <div class="cdg-host-usage-loading">
            <div class="cdg-host-usage-loading-dots"><span></span><span></span><span></span></div>
            <span>Kullanım verileri yükleniyor...</span>
        </div>
    </div>
</div>

<script>
window.cdgHostUsage = {
    productId: <?php echo (int)$d_id; ?>,
    controllerUrl: '<?php echo htmlspecialchars($controller_url, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>',

    load: function() {
        var content = document.getElementById('cdg-host-usage-content');
        if(!content) return;
        content.innerHTML = '<div class="cdg-host-usage-loading"><div class="cdg-host-usage-loading-dots"><span></span><span></span><span></span></div><span>Kullanım verileri yükleniyor...</span></div>';

        if(typeof MioAjax !== 'function') {
            content.innerHTML = '<div class="cdg-host-usage-error"><i class="bi bi-exclamation-triangle"></i><span>AJAX motoru yüklenemedi</span></div>';
            return;
        }

        MioAjax({
            url: this.controllerUrl,
            type: 'post',
            data: { inc: 'get_hosting_informations', m_page: 'usage' },
            result: function(r) {
                if(!r || !r.usage) {
                    content.innerHTML = '<div class="cdg-host-usage-error"><i class="bi bi-info-circle"></i><span>Kullanım bilgileri sunucudan alınamadı.</span></div>';
                    return;
                }
                var usage = r.usage;
                var html = '<div class="cdg-host-usage-grid">';

                // Disk
                if(usage.disk_used_percent !== undefined) {
                    var dp = parseFloat(usage.disk_used_percent) || 0;
                    var dCls = dp >= 90 ? 'danger' : (dp >= 75 ? 'warning' : '');
                    html += '<div class="cdg-host-usage-item">';
                    html += '<div class="cdg-host-usage-item-head">';
                    html += '<span class="cdg-host-usage-item-title"><i class="bi bi-hdd"></i> Disk Kullanımı</span>';
                    html += '<span class="cdg-host-usage-percent ' + dCls + '">%' + dp + '</span>';
                    html += '</div>';
                    html += '<div class="cdg-host-usage-bar"><div class="cdg-host-usage-bar-fill ' + dCls + '" style="width:' + Math.min(100, dp) + '%;"></div></div>';
                    html += '<div class="cdg-host-usage-detail"><span>Kullanılan: <strong>' + (usage.disk_used_format || '-') + '</strong></span><span>Toplam: <strong>' + (usage.disk_limit_format || '-') + '</strong></span></div>';
                    html += '</div>';
                }

                // Bandwidth
                if(usage.bandwidth_used_percent !== undefined) {
                    var bp = parseFloat(usage.bandwidth_used_percent) || 0;
                    var bCls = bp >= 90 ? 'danger' : (bp >= 75 ? 'warning' : '');
                    html += '<div class="cdg-host-usage-item">';
                    html += '<div class="cdg-host-usage-item-head">';
                    html += '<span class="cdg-host-usage-item-title"><i class="bi bi-bar-chart"></i> Bandwidth (Aylık)</span>';
                    html += '<span class="cdg-host-usage-percent ' + bCls + '">%' + bp + '</span>';
                    html += '</div>';
                    html += '<div class="cdg-host-usage-bar"><div class="cdg-host-usage-bar-fill ' + bCls + '" style="width:' + Math.min(100, bp) + '%;"></div></div>';
                    html += '<div class="cdg-host-usage-detail"><span>Kullanılan: <strong>' + (usage.bandwidth_used_format || '-') + '</strong></span><span>Toplam: <strong>' + (usage.bandwidth_limit_format || '-') + '</strong></span></div>';
                    html += '</div>';
                }

                html += '</div>';
                content.innerHTML = html;
            }
        });
    }
};

// Sayfa yuklendikten sonra otomatik yukle
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if(window.cdgHostUsage) cdgHostUsage.load();
    }, 500);
});
</script>
