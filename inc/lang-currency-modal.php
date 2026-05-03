<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
/**
 * Codega - Dil & Para Birimi Seçim Modalları
 * close_modal() ve open_modal() global JS fonksiyonları temadan gelir
 * WiseCP runtime: $lang_count, $lang_list, $currencies_count, $currencies, $selected_currency, $canonical_link
 */

$lang_count       = isset($lang_count) ? (int)$lang_count : 0;
$lang_list        = isset($lang_list) && is_array($lang_list) ? $lang_list : [];
$currencies_count = isset($currencies_count) ? (int)$currencies_count : 0;
$currencies       = isset($currencies) && is_array($currencies) ? $currencies : [];
$selected_currency = isset($selected_currency) ? $selected_currency : null;
$canonical_link   = isset($canonical_link) ? $canonical_link : '';
?>

<style>
.cdg-lcm {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: min(440px, calc(100vw - 32px));
    max-height: 80vh;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 24px 60px rgba(15,23,42,0.30);
    z-index: 9999;
    overflow: hidden;
    display: none;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-sizing: border-box;
}
.cdg-lcm *, .cdg-lcm *::before, .cdg-lcm *::after { box-sizing: border-box; }
.cdg-lcm-head {
    padding: 18px 22px;
    border-bottom: 1px solid #e2e8f0;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    display: flex; justify-content: space-between; align-items: center;
}
.cdg-lcm-head h3 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    display: inline-flex; align-items: center; gap: 8px;
}
.cdg-lcm-close {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    color: #fff;
    border: 0;
    cursor: pointer;
    font-size: 14px;
    display: grid; place-items: center;
    text-decoration: none;
    transition: background 0.15s;
}
.cdg-lcm-close:hover { background: rgba(255,255,255,0.30); color: #fff; }
.cdg-lcm-body {
    padding: 14px;
    overflow-y: auto;
    max-height: calc(80vh - 70px);
}
.cdg-lcm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 8px;
}
.cdg-lcm-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    text-decoration: none;
    color: #0f172a;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.18s;
    background: #fff;
}
.cdg-lcm-item:hover {
    border-color: #2E3B4E;
    background: #eff6ff;
    color: #2E3B4E;
}
.cdg-lcm-item.active {
    border-color: #2E3B4E;
    background: linear-gradient(135deg, #2E3B4E, #00D3E5);
    color: #fff;
    cursor: default;
}
.cdg-lcm-item img {
    width: 22px; height: 22px;
    border-radius: 4px;
    object-fit: cover;
}
.cdg-lcm-item .symbol {
    width: 28px; height: 28px;
    background: #f1f5f9;
    border-radius: 6px;
    display: grid; place-items: center;
    font-weight: 800;
    color: #2E3B4E;
    flex-shrink: 0;
}
.cdg-lcm-item.active .symbol { background: rgba(255,255,255,0.22); color: #fff; }
.cdg-lcm[id="selectLang"]:target,
.cdg-lcm[id="selectCurrency"]:target { display: block; }
</style>

<?php if($lang_count > 1): ?>
<div id="selectLang" class="cdg-lcm">
    <div class="cdg-lcm-head">
        <h3><i class="bi bi-translate"></i> Dil Seçin</h3>
        <a class="cdg-lcm-close" href="javascript:close_modal('selectLang');void 0;" aria-label="Kapat">
            <i class="bi bi-x-lg"></i>
        </a>
    </div>
    <div class="cdg-lcm-body">
        <div class="cdg-lcm-grid">
            <?php foreach($lang_list as $row):
                if(!is_array($row)) continue;
                $selected = !empty($row['selected']);
                $link = $row['link'] ?? '#';
                if($link !== '#' && !str_contains($link, '?')) $link .= '?chl=true';
                $cname = $row['cname'] ?? '';
                $name = $row['name'] ?? '';
                $flag = $row['flag-img'] ?? '';
                $label = $cname . ($name && $name !== $cname ? ' (' . $name . ')' : '');
            ?>
            <?php if($selected): ?>
            <span class="cdg-lcm-item active">
                <?php if($flag): ?><img src="<?php echo htmlspecialchars($flag, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php endif; ?>
                <span><?php echo htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </span>
            <?php else: ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-lcm-item" rel="nofollow">
                <?php if($flag): ?><img src="<?php echo htmlspecialchars($flag, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><?php endif; ?>
                <span><?php echo htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($currencies_count > 1): ?>
<div id="selectCurrency" class="cdg-lcm">
    <div class="cdg-lcm-head">
        <h3><i class="bi bi-currency-exchange"></i> Para Birimi Seçin</h3>
        <a class="cdg-lcm-close" href="javascript:close_modal('selectCurrency');void 0;" aria-label="Kapat">
            <i class="bi bi-x-lg"></i>
        </a>
    </div>
    <div class="cdg-lcm-body">
        <div class="cdg-lcm-grid">
            <?php foreach($currencies as $row):
                if(!is_array($row)) continue;
                $cid = $row['id'] ?? '';
                $selected = ($cid == $selected_currency);
                $name = $row['name'] ?? '';
                $code = $row['code'] ?? '';

                // Sembol
                $symbol = '';
                if(class_exists('Money') && method_exists('Money','getSymbol')) {
                    $symbol_info = Money::getSymbol($cid);
                    $symbol = $symbol_info['symbol'] ?? '';
                }
                if(!$symbol) $symbol = $code;

                // Link
                $link = $canonical_link;
                if($link !== '#' && $link !== '') {
                    if(str_contains($link, '?')) {
                        $parts = explode('?', $link, 2);
                        $link = $parts[0] . '?' . $parts[1] . '&chc=' . $cid;
                    } else {
                        $link .= '?chc=' . $cid;
                    }
                } else {
                    $link = '?chc=' . $cid;
                }
            ?>
            <?php if($selected): ?>
            <span class="cdg-lcm-item active">
                <span class="symbol"><?php echo htmlspecialchars($symbol, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <span><?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </span>
            <?php else: ?>
            <a href="<?php echo htmlspecialchars($link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" class="cdg-lcm-item" rel="nofollow">
                <span class="symbol"><?php echo htmlspecialchars($symbol, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                <span><?php echo htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
