<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
$hoptions = ["datatables"];

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

// Bakiye bilgisi
$user_balance = 0;
$user_bal_cid = 0;
$user_balance_str = '0,00';
if(class_exists('User') && isset(User::$init->info)) {
    $info = User::$init->info;
    $user_balance = isset($info['balance']) ? $info['balance'] : 0;
    $user_bal_cid = isset($info['balance_cid']) ? $info['balance_cid'] : 0;
    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && $user_bal_cid) {
        $user_balance_str = Money::formatter_symbol($user_balance, $user_bal_cid);
    } else {
        $user_balance_str = number_format((float)$user_balance, 2, ',', '.');
    }
}

$transactions = isset($list) ? $list : (isset($transactions) ? $transactions : []);
?>

<div class="cdg-grid cdg-grid-3" style="margin-bottom:20px;">
    <div class="cdg-card" style="text-align:center;">
        <div style="color:var(--cdg-muted);font-size:13px;margin-bottom:8px;">Mevcut Bakiyeniz</div>
        <div style="font-size:32px;font-weight:700;color:var(--cdg-primary);"><?php echo $user_balance_str; ?></div>
    </div>
    <div class="cdg-card">
        <h4 style="margin-bottom:12px;"><i class="bi bi-plus-circle"></i> Bakiye Yukleme</h4>
        <p style="font-size:13px;color:var(--cdg-muted);margin-bottom:12px;">Hesabiniza bakiye yuklemek icin asagidaki butonu kullanin.</p>
        <button type="button" onclick="if(typeof iziModal=='function'){$('#balance-modal').iziModal('open');}else{alert('Bakiye yukleme: WiseCP modul aktif olmalidir.');}" class="cdg-btn cdg-btn-primary" style="width:100%;">
            <i class="bi bi-credit-card"></i> Yukleme Yap
        </button>
    </div>
    <div class="cdg-card">
        <h4 style="margin-bottom:12px;"><i class="bi bi-gear"></i> Otomatik Odeme</h4>
        <p style="font-size:13px;color:var(--cdg-muted);">Faturalariniz vade tarihinde bakiyenizden otomatik odensin.</p>
        <label class="cdg-checkbox" style="margin-top:10px;">
            <input type="checkbox" name="auto_payment_by_credit" id="auto_payment_by_credit"
                <?php echo (class_exists('User') && isset(User::$init->info['auto_payment_by_credit']) && User::$init->info['auto_payment_by_credit']) ? 'checked' : ''; ?>>
            <span>Otomatik odemeyi etkinlestir</span>
        </label>
    </div>
</div>

<div class="cdg-card">
    <div class="cdg-card-head">
        <h3><i class="bi bi-list-ul"></i> Bakiye Hareketleri</h3>
    </div>

    <?php if(!empty($transactions) && is_array($transactions)): ?>
        <div class="cdg-table-wrap">
            <table class="cdg-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Aciklama</th>
                        <th style="text-align:right;">Tutar</th>
                        <th style="text-align:center;">Durum</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($transactions as $row):
                    $date = isset($row['ctime']) ? $row['ctime'] : (isset($row['date']) ? $row['date'] : '');
                    $date_fmt = '';
                    if($date) {
                        if(class_exists('DateManager') && method_exists('DateManager', 'format') && class_exists('Config')) {
                            $date_fmt = DateManager::format(Config::get("options/date-format"), $date);
                        } else {
                            $date_fmt = date('d.m.Y H:i', strtotime($date));
                        }
                    }
                    $desc = isset($row['description']) ? $row['description'] : (isset($row['detail']) ? $row['detail'] : '-');
                    $amount = '';
                    if(class_exists('Money') && method_exists('Money', 'formatter_symbol') && isset($row['amount']) && isset($row['amount_cid'])) {
                        $amount = Money::formatter_symbol($row['amount'], $row['amount_cid']);
                    }
                    $is_credit = isset($row['type']) && in_array($row['type'], ['credit','add','income']);
                ?>
                    <tr>
                        <td style="font-size:13px;"><?php echo $date_fmt; ?></td>
                        <td><?php echo htmlspecialchars($desc); ?></td>
                        <td style="text-align:right;font-weight:600;color:<?php echo $is_credit ? '#10b981' : '#ef4444'; ?>;">
                            <?php echo $is_credit ? '+' : '-'; ?> <?php echo $amount; ?>
                        </td>
                        <td style="text-align:center;font-size:12px;"><?php echo isset($row['status']) ? htmlspecialchars($row['status']) : ''; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="cdg-empty">
            <div class="icon"><i class="bi bi-wallet2"></i></div>
            <h3>Henuz hareket yok</h3>
            <p>Bakiye yukleme veya odeme islemi yaptiginizda burada gorunecek.</p>
        </div>
    <?php endif; ?>
</div>
