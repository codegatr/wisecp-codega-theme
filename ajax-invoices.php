<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Faturalar AJAX Endpoint (DataTables formatı)
 * Faturalar listesi DataTable'a AJAX response olarak döner
 *
 * WiseCP runtime: $list, $situations
 * Return: array of arrays (her satır bir fatura)
 */

$items = [];

if(!isset($list) || !is_array($list) || empty($list)) {
    return $items;
}

$situations = isset($situations) && is_array($situations) ? $situations : [];

// Status -> rozet HTML
function cdg_inv_badge($status, $situations) {
    $label = $situations[$status] ?? ucfirst((string)$status);
    $classes = [
        'paid'    => 'background:#d1fae5;color:#065f46;',
        'unpaid'  => 'background:#fef3c7;color:#92400e;',
        'waiting' => 'background:#CFFAFE;color:#2E3B4E;',
        'refund'  => 'background:#fee2e2;color:#991b1b;',
        'cancelled'=>'background:#fee2e2;color:#991b1b;',
    ];
    $st = strtolower((string)$status);
    $style = $classes[$st] ?? 'background:#f1f5f9;color:#475569;';

    return '<span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;' . $style . '">' . htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>';
}

foreach($list as $row) {
    if(!is_array($row)) continue;

    $r_id     = $row['id'] ?? 0;
    $r_number = $row['number'] ?? ('#' . $r_id);
    $r_cdate  = $row['creation_date'] ?? '';
    $r_due    = $row['due_date'] ?? '';
    $r_total  = $row['total'] ?? 0;
    $r_cid    = $row['currency'] ?? 0;
    $r_status = strtolower($row['status'] ?? 'unknown');
    $r_link   = $row['detail_link'] ?? '#';

    // Para formatı
    $amount_str = '';
    if(class_exists('Money') && method_exists('Money','formatter_symbol')) {
        $amount_str = Money::formatter_symbol($r_total, $r_cid);
    } else {
        $amount_str = number_format((float)$r_total, 2, ',', '.');
    }

    // Buton (Codega stili)
    $btn = '<a href="' . htmlspecialchars($r_link, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '" '
         . 'style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#fff;color:#0f172a;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;transition:all 0.18s;" '
         . 'onmouseover="this.style.borderColor=\'#2E3B4E\';this.style.color=\'#2E3B4E\';" '
         . 'onmouseout="this.style.borderColor=\'#e2e8f0\';this.style.color=\'#0f172a\';">'
         . '<i class="bi bi-eye"></i> Görüntüle</a>';

    // Durum unpaid ise hemen öde butonu da ekle
    if($r_status === 'unpaid') {
        $btn .= ' <a href="' . htmlspecialchars($r_link, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '" '
              . 'style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;margin-left:6px;background:linear-gradient(135deg,#10b981,#34d399);color:#fff;border:0;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;box-shadow:0 4px 10px rgba(16,185,129,0.22);">'
              . '<i class="bi bi-credit-card"></i> Öde</a>';
    }

    $item = [
        '<strong>' . htmlspecialchars($r_number, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</strong>',
        htmlspecialchars($r_cdate, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        htmlspecialchars($r_due, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        '<strong>' . htmlspecialchars($amount_str, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</strong>',
        cdg_inv_badge($r_status, $situations),
        $btn
    ];

    $items[] = $item;
}

return $items;
