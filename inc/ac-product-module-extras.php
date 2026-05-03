<?php
/**
 * ac-product-module-extras.php
 *
 * dev.wisecp.com - Hosting Panel/Sunucu/Urun Modulu Gelistirme dokumantasyonuna gore:
 *   - "Cok Amacli Musteri Alani Butonlari" -> $buttons array render
 *   - "Musteri Alani icin Ozel Sayfa ve Fonksiyon Tanimlamak" -> $m_page handler
 *   - "Single Sign-On" -> panel_links_for_client() butonu (universal)
 *   - run_transaction() JS fonksiyonu (modul AJAX cagrilari)
 *   - reload_module_content() JS fonksiyonu (modul sayfa gecisleri)
 *   - #clientArea-module-page container (modul HTML render)
 *
 * Bu dosya ac-product-detail-template.php'nin sonunda include edilir
 * ve hosting/server/software/special icin modul entegrasyonu saglar.
 *
 * WiseCP Runtime Variables:
 *   $buttons         - modulun ekledigi clientArea_buttons array
 *   $m_page          - aktif modul sayfasi adi (URL'den)
 *   $module_panel    - modulun render ettigi HTML icerigi
 *   $module_con      - modul controller objesi
 *   $links           - URL bilgileri (controller endpoint vb.)
 *   $proanse         - urun siparis kaydi (status, end_date vb.)
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

// Sadece urun detay sayfalarinda calissin
if(!isset($cdg_pd_kind) || !in_array($cdg_pd_kind, ['hosting', 'server', 'software', 'special'])) return;

// Aktif sipariste mi?
$_cdg_is_active = isset($proanse['status']) && $proanse['status'] === 'active';

// 1) MODUL BUTTONS - $buttons array (modulun cok amacli butonlari)
$cdg_module_buttons = (isset($buttons) && is_array($buttons)) ? $buttons : [];

// 2) MODUL CUSTOM PAGE - aktif sayfa
$cdg_active_module_page = $m_page ?? '';
$cdg_module_page_html   = $module_panel ?? '';

// 3) UNIVERSAL SSO - panel_links_for_client() (hosting'e ek olarak server/software/special icin)
$cdg_universal_sso_links = [];
if(isset($module_con) && is_object($module_con) && method_exists($module_con, 'panel_links_for_client')) {
    try {
        $sso_raw = @$module_con->panel_links_for_client();
        if(is_array($sso_raw)) {
            foreach($sso_raw as $type => $url) {
                if(!is_string($url) || !$url) continue;
                $cdg_universal_sso_links[$type] = [
                    'url'   => $url,
                    'label' => ucfirst(str_replace(['_', '-'], ' ', $type)),
                ];
            }
        }
    } catch(\Throwable $e) { /* sessiz */ }
}

// Hicbir entegrasyon yoksa cik
$_has_buttons      = !empty($cdg_module_buttons);
$_has_module_page  = !empty($cdg_module_page_html);
$_has_universal_sso = !empty($cdg_universal_sso_links) && $cdg_pd_kind !== 'hosting'; // hosting kendi SSO'sunu rendere ediyor

if(!$_has_buttons && !$_has_module_page && !$_has_universal_sso) return;

$_controller_url = $links['controller'] ?? '';
?>

<style>
/* ==================== Modul Entegrasyon Stilleri ==================== */
.cdg-mod-card{background:linear-gradient(135deg,#1e293b,#0f172a);border:1px solid #334155;border-radius:14px;padding:22px 24px;margin:14px 0;color:#e2e8f0;}
.cdg-mod-card h3{margin:0 0 16px;font-size:15px;color:#fff;display:flex;align-items:center;gap:10px;font-weight:700;}
.cdg-mod-card h3 i{color:#10b981;}

.cdg-mod-buttons{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;}
.cdg-mod-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;border-radius:8px;border:1px solid #475569;background:#334155;color:#fff;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;font-family:inherit;transition:all 0.18s;}
.cdg-mod-btn:hover{background:#475569;border-color:#64748b;transform:translateY(-1px);}
.cdg-mod-btn:disabled{opacity:0.5;cursor:not-allowed;}
.cdg-mod-btn-primary{background:linear-gradient(135deg,#3b82f6,#2563eb);border-color:transparent;}
.cdg-mod-btn-primary:hover{background:linear-gradient(135deg,#2563eb,#1d4ed8);}
.cdg-mod-btn-success{background:linear-gradient(135deg,#10b981,#059669);border-color:transparent;}
.cdg-mod-btn-warn{background:linear-gradient(135deg,#f59e0b,#d97706);border-color:transparent;}
.cdg-mod-btn-danger{background:linear-gradient(135deg,#dc2626,#b91c1c);border-color:transparent;}

.cdg-mod-sso{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;margin-top:12px;}
.cdg-mod-sso-link{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(5,150,105,0.05));border:1px solid #10b981;color:#34d399;text-decoration:none;font-size:13px;font-weight:600;transition:all 0.18s;}
.cdg-mod-sso-link:hover{background:linear-gradient(135deg,rgba(16,185,129,0.18),rgba(5,150,105,0.1));transform:translateY(-1px);color:#10b981;}
.cdg-mod-sso-link i{font-size:18px;}

#clientArea-module-page{background:#1e293b;border:1px solid #334155;border-radius:14px;padding:24px;margin:14px 0;color:#e2e8f0;min-height:200px;}
#clientArea-module-page .cdg-mod-loader{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 20px;color:#94a3b8;}
#clientArea-module-page .cdg-mod-loader-spin{width:40px;height:40px;border:3px solid #334155;border-top-color:#3b82f6;border-radius:50%;animation:cdgmodspin 0.9s linear infinite;margin-bottom:14px;}
@keyframes cdgmodspin{to{transform:rotate(360deg)}}

.cdg-mod-back{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:6px;background:#334155;color:#cbd5e1;text-decoration:none;font-size:12.5px;font-weight:600;margin-bottom:14px;cursor:pointer;border:0;font-family:inherit;}
.cdg-mod-back:hover{background:#475569;color:#fff;}

.cdg-mod-toast{position:fixed;top:20px;right:20px;background:#1e293b;border:1px solid #475569;color:#fff;padding:12px 18px;border-radius:8px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 8px 30px rgba(0,0,0,0.3);animation:cdgmodtoast 0.3s;}
.cdg-mod-toast.success{border-color:#10b981;color:#34d399;}
.cdg-mod-toast.error{border-color:#dc2626;color:#f87171;}
.cdg-mod-toast.info{border-color:#3b82f6;color:#60a5fa;}
@keyframes cdgmodtoast{from{opacity:0;transform:translateX(20px);}to{opacity:1;transform:translateX(0);}}
</style>

<?php if($_has_universal_sso): ?>
<!-- ==================== Universal SSO (server/software/special) ==================== -->
<div class="cdg-mod-card" id="cdg-mod-sso-card" data-tab="home">
    <h3><i class="bi bi-box-arrow-up-right"></i> Hızlı Erişim (Single Sign-On)</h3>
    <div class="cdg-mod-sso">
        <?php foreach($cdg_universal_sso_links as $type => $sso): ?>
        <a href="<?= htmlspecialchars($sso['url']) ?>" target="_blank" rel="noopener" class="cdg-mod-sso-link">
            <i class="bi bi-box-arrow-up-right"></i>
            <span><?= htmlspecialchars($sso['label']) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if($_has_buttons): ?>
<!-- ==================== Modul Custom Butonlari ==================== -->
<div class="cdg-mod-card" id="cdg-mod-buttons-card" data-tab="home">
    <h3><i class="bi bi-grid-3x3-gap-fill"></i> Modül İşlemleri</h3>
    <div class="cdg-mod-buttons">
        <?php
        // $buttons formati: ['btn_key' => 'Label'] veya ['btn_key' => ['label'=>'X', 'icon'=>'gear', 'data'=>{}]]
        // Her btn_key icin run_transaction(btn_key, btn_el, post_fields) cagrilir
        foreach($cdg_module_buttons as $btn_key => $btn_value):
            if(is_array($btn_value)) {
                $btn_label = $btn_value['label'] ?? $btn_key;
                $btn_icon  = $btn_value['icon'] ?? 'play-circle';
                $btn_class = $btn_value['class'] ?? '';
                $btn_data  = $btn_value['data'] ?? null;
                $btn_confirm = $btn_value['confirm'] ?? '';
            } else {
                $btn_label = (string)$btn_value;
                $btn_icon  = 'play-circle';
                $btn_class = '';
                $btn_data  = null;
                $btn_confirm = '';
            }
            $extra_class = '';
            if(strpos($btn_class, 'primary') !== false || in_array($btn_key, ['restart','reboot','login'])) $extra_class = 'cdg-mod-btn-primary';
            elseif(strpos($btn_class, 'success') !== false || in_array($btn_key, ['start','enable','activate'])) $extra_class = 'cdg-mod-btn-success';
            elseif(strpos($btn_class, 'warn') !== false || in_array($btn_key, ['suspend','pause','stop'])) $extra_class = 'cdg-mod-btn-warn';
            elseif(strpos($btn_class, 'danger') !== false || in_array($btn_key, ['terminate','delete','remove','shutdown'])) $extra_class = 'cdg-mod-btn-danger';
        ?>
        <button type="button"
                class="cdg-mod-btn <?= $extra_class ?>"
                data-btn-key="<?= htmlspecialchars($btn_key) ?>"
                <?php if($btn_data): ?>data-fields='<?= htmlspecialchars(json_encode($btn_data, JSON_UNESCAPED_SLASHES)) ?>'<?php endif; ?>
                <?php if($btn_confirm): ?>data-confirm="<?= htmlspecialchars($btn_confirm) ?>"<?php endif; ?>
                onclick="cdgModRunTransaction('<?= htmlspecialchars(addslashes($btn_key)) ?>', this);">
            <i class="bi bi-<?= htmlspecialchars($btn_icon) ?>"></i>
            <span><?= htmlspecialchars($btn_label) ?></span>
        </button>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ==================== Modul Custom Page Container ==================== -->
<div id="clientArea-module-page" data-tab="modulepage" style="display:<?= $cdg_active_module_page ? 'block' : 'none' ?>;">
    <?php if($cdg_active_module_page): ?>
    <button class="cdg-mod-back" onclick="cdgModBackHome()">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </button>
    <?php endif; ?>
    <div id="clientArea-module-content">
        <?= $cdg_module_page_html /* WiseCP modulu trusted HTML doner */ ?>
    </div>
</div>

<script>
(function(){
    'use strict';

    // Modul AJAX endpoint URL'i
    var CDG_MOD_CONTROLLER = <?= json_encode($_controller_url) ?>;
    var CDG_MOD_ACTIVE_PAGE = <?= json_encode($cdg_active_module_page) ?>;

    // === Toast helper ===
    function cdgModToast(msg, type){
        type = type || 'info';
        var t = document.createElement('div');
        t.className = 'cdg-mod-toast ' + type;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(function(){
            t.style.opacity = '0';
            t.style.transition = 'opacity 0.3s';
            setTimeout(function(){ t.remove(); }, 300);
        }, 3500);
    }

    // === run_transaction (dev.wisecp.com'da tanimli, modul butonlari icin) ===
    // URL: ?inc=panel_operation_method&method={btn_key}
    // Kullanim: clientArea_buttons array'inden gelen butonlar tiklaninca cagrilir
    window.cdgModRunTransaction = function(btn_key, btn_el, post_fields){
        // Confirm dialog
        var confirmMsg = btn_el && btn_el.getAttribute('data-confirm');
        if(confirmMsg && !confirm(confirmMsg)) return;

        // data-fields attribute'ten ek params
        var dataFields = {};
        try {
            var df = btn_el && btn_el.getAttribute('data-fields');
            if(df) dataFields = JSON.parse(df);
        } catch(e){}

        var data = Object.assign(
            { inc: 'panel_operation_method', method: btn_key },
            dataFields,
            post_fields || {}
        );

        // Buton durumunu pasifle
        var origText = btn_el ? btn_el.innerHTML : '';
        if(btn_el){
            btn_el.disabled = true;
            btn_el.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:cdgmodspin 0.8s linear infinite;display:inline-block;"></i> İşleniyor...';
        }

        // FormData icin URL params
        var params = new URLSearchParams();
        Object.keys(data).forEach(function(k){
            params.append(k, typeof data[k] === 'object' ? JSON.stringify(data[k]) : data[k]);
        });

        fetch(CDG_MOD_CONTROLLER, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: params.toString(),
            credentials: 'same-origin'
        })
        .then(function(r){ return r.text(); })
        .then(function(txt){
            // Buton restore
            if(btn_el){
                btn_el.disabled = false;
                btn_el.innerHTML = origText;
            }

            // JSON cevap mi?
            var resp = null;
            try { resp = JSON.parse(txt); } catch(e){}

            if(resp && typeof resp === 'object'){
                if(resp.status === 'successful' || resp.success === true || resp.ok === true){
                    cdgModToast(resp.message || resp.msg || 'İşlem başarılı', 'success');
                    if(resp.reload || resp.redirect){
                        setTimeout(function(){
                            if(resp.redirect) window.location.href = resp.redirect;
                            else window.location.reload();
                        }, 800);
                    }
                } else if(resp.status === 'error' || resp.success === false || resp.ok === false){
                    cdgModToast(resp.message || resp.msg || 'Hata oluştu', 'error');
                } else if(resp.message){
                    cdgModToast(resp.message, 'info');
                }
            } else if(txt && txt.length > 0){
                // HTML cevap - module page olarak goster
                cdgModRenderPage(btn_key, txt);
            }
        })
        .catch(function(err){
            if(btn_el){
                btn_el.disabled = false;
                btn_el.innerHTML = origText;
            }
            cdgModToast('Ağ hatası: ' + err.message, 'error');
        });
    };

    // === reload_module_content (dev.wisecp.com'da tanimli, modul ozel sayfalarini yukler) ===
    // URL: ?inc=clientArea_custom_page&m_page={page_name}
    window.cdgModReloadContent = function(page){
        if(page === undefined || page === '' || page === 'home'){
            cdgModBackHome();
            return;
        }

        // URL'i guncelle
        var url = new URL(window.location.href);
        url.searchParams.set('m_page', page);
        window.history.pushState('mp', document.title, url.toString());

        // Container'i goster, ana tab'lari gizle
        document.querySelectorAll('[data-tab="home"]').forEach(function(el){ el.style.display = 'none'; });
        var container = document.getElementById('clientArea-module-page');
        if(!container) return;
        container.style.display = 'block';
        var content = document.getElementById('clientArea-module-content');
        if(content){
            content.innerHTML = '<div class="cdg-mod-loader"><div class="cdg-mod-loader-spin"></div><div>Modül sayfası yükleniyor...</div></div>';
        }

        // Modul sayfasini cek
        var fetchUrl = new URL(CDG_MOD_CONTROLLER, window.location.origin);
        fetchUrl.searchParams.set('inc', 'clientArea_custom_page');
        fetchUrl.searchParams.set('m_page', page);

        fetch(fetchUrl.toString(), { credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(html){
                if(content){
                    content.innerHTML = html || '<div class="cdg-mod-loader">Sayfa içeriği boş.</div>';
                    // Geri butonu varsa kalsin, yoksa ekle
                    if(!container.querySelector('.cdg-mod-back')){
                        var back = document.createElement('button');
                        back.className = 'cdg-mod-back';
                        back.innerHTML = '<i class="bi bi-arrow-left"></i> Geri Dön';
                        back.onclick = cdgModBackHome;
                        container.insertBefore(back, content);
                    }
                }
            })
            .catch(function(err){
                if(content){
                    content.innerHTML = '<div class="cdg-mod-loader" style="color:#f87171;">Yükleme hatası: ' + err.message + '</div>';
                }
            });
    };

    // === Module page'i kapat, ana sayfaya don ===
    window.cdgModBackHome = function(){
        var container = document.getElementById('clientArea-module-page');
        if(container) container.style.display = 'none';
        document.querySelectorAll('[data-tab="home"]').forEach(function(el){ el.style.display = ''; });

        var url = new URL(window.location.href);
        url.searchParams.delete('m_page');
        window.history.pushState('home', document.title, url.toString());
    };

    // === HTML cevabini module page olarak goster ===
    function cdgModRenderPage(label, html){
        var container = document.getElementById('clientArea-module-page');
        if(!container) return;
        document.querySelectorAll('[data-tab="home"]').forEach(function(el){ el.style.display = 'none'; });
        container.style.display = 'block';
        var content = document.getElementById('clientArea-module-content');
        if(content) content.innerHTML = html;
        if(!container.querySelector('.cdg-mod-back')){
            var back = document.createElement('button');
            back.className = 'cdg-mod-back';
            back.innerHTML = '<i class="bi bi-arrow-left"></i> Geri Dön';
            back.onclick = cdgModBackHome;
            container.insertBefore(back, content);
        }
    }

    // === Geri uyumluluk: dev.wisecp.com dokumanlarinda gecen tam isimler ===
    window.run_transaction = window.cdgModRunTransaction;
    window.reload_module_content = window.cdgModReloadContent;

    // Sayfa yuklendiginde m_page parametresi varsa direkt olarak modul sayfasini ac
    if(CDG_MOD_ACTIVE_PAGE && CDG_MOD_ACTIVE_PAGE !== '' && CDG_MOD_ACTIVE_PAGE !== 'home'){
        document.querySelectorAll('[data-tab="home"]').forEach(function(el){ el.style.display = 'none'; });
    }
})();
</script>
