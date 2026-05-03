<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<div id="cdg-toast-wrap" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;"></div>
<script>
(function(){
    'use strict';
    window.cdgToast = function(msg, type){
        type = type || 'info';
        var w = document.getElementById('cdg-toast-wrap'); if(!w) return;
        var c = {success:{bg:'#dcfce7',col:'#166534',icon:'check-circle'},error:{bg:'#fef2f2',col:'#991b1b',icon:'exclamation-circle'},warning:{bg:'#fffbeb',col:'#92400e',icon:'exclamation-triangle'},info:{bg:'#eff6ff',col:'#2E3B4E',icon:'info-circle'}};
        var k = c[type] || c.info;
        var t = document.createElement('div');
        t.style.cssText = 'background:'+k.bg+';color:'+k.col+';padding:12px 18px;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.08);font-size:14px;display:flex;gap:10px;align-items:center;min-width:280px;';
        t.innerHTML = '<i class="bi bi-'+k.icon+'" style="font-size:18px;"></i><span>'+msg+'</span>';
        w.appendChild(t);
        setTimeout(function(){ t.style.transition='opacity .3s,transform .3s'; t.style.opacity='0'; t.style.transform='translateX(100%)'; setTimeout(function(){ t.remove(); }, 300); }, 4000);
    };
})();
</script>
