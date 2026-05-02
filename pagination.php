<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
// Sayfalama yardımcısı (WiseCP $pagination değişkenini bekliyor)
if(isset($pagination) && is_array($pagination) && isset($pagination['html'])) {
    echo $pagination['html'];
}
