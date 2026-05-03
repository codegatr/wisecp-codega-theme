<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';
/**
 * Codega - Genel Sayfa Detayı (about us, terms, privacy vs)
 *
 * WiseCP runtime: $page (title, content), $sidebar_status, $sidebar
 */

// === KURUMSAL SAYFA ROUTER (Codega) ===
// Eğer URL slug bizim hazır kurumsal sayfalarımızdan biriyse, doğrudan tema dosyasını yükle
$cdg_corp_pages = [
    'hakkimizda'           => 'hakkimizda.php',
    'hakkimda'             => 'hakkimizda.php',
    'sosyal-sorumluluk'    => 'sosyal-sorumluluk.php',
    'surdurulebilirlik'    => 'surdurulebilirlik.php',
    'kariyer'              => 'kariyer.php',
    'kvkk'                 => 'kvkk.php',
    'kvkk-aydinlatma'      => 'kvkk.php',
    'kvkk-aydinlatma-metni' => 'kvkk.php',
    'cerez-politikasi'     => 'cerez-politikasi.php',
    'gizlilik-politikasi'  => 'gizlilik-politikasi.php',
    'gizlilik-sozlesmesi'  => 'gizlilik-politikasi.php',
    'hizmet-sozlesmesi'    => 'hizmet-sozlesmesi.php',
    // v3.5.53 codega.com.tr migration
    'referanslarimiz'      => 'references.php',
    'our-references'       => 'references.php',
    'vizyon'               => 'vision.php',
    'vision'               => 'vision.php',
    'sistem-durumu'        => 'system-status.php',
    'system-status'        => 'system-status.php',
    'erp-yazilimi'         => 'erp.php',
    'erp-software'         => 'erp.php',
    'erp'                  => 'erp.php',
];

$cdg_corp_slug = '';
// 1) WiseCP'nin $page['slug'] varsa al
if(isset($page) && is_array($page) && !empty($page['slug'])) {
    $cdg_corp_slug = strtolower(trim($page['slug']));
}
// 2) URL'den parse et (fallback)
if(!$cdg_corp_slug && isset($_SERVER['REQUEST_URI'])) {
    $cdg_uri_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $cdg_uri_segments = explode('/', $cdg_uri_path);
    $cdg_corp_slug = strtolower(end($cdg_uri_segments));
}
// .html / .htm uzantısını strip et (WiseCP page URL formatı)
$cdg_corp_slug = preg_replace('/\.html?$/i', '', $cdg_corp_slug);

// 3) WiseCP $page_data['route']'dan da kontrol et
if(!isset($cdg_corp_pages[$cdg_corp_slug]) && isset($page) && is_array($page) && !empty($page['route'])) {
    $cdg_corp_slug = strtolower(trim($page['route']));
    $cdg_corp_slug = preg_replace('/\.html?$/i', '', $cdg_corp_slug);
}

if(isset($cdg_corp_pages[$cdg_corp_slug])) {
    $cdg_corp_file = __DIR__ . DIRECTORY_SEPARATOR . $cdg_corp_pages[$cdg_corp_slug];
    if(file_exists($cdg_corp_file)) {
        include $cdg_corp_file;
        return;
    }
}
// === END KURUMSAL ROUTER ===

$page_data = isset($page) && is_array($page) ? $page : [];
$page_title = $page_data['title'] ?? 'Sayfa';
$page_content = $page_data['content'] ?? '';
$pd_sidebar = isset($sidebar_status) && $sidebar_status;
$pd_sidebar_items = isset($sidebar) && is_array($sidebar) ? $sidebar : [];
?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>">Anasayfa</a>
            <span class="sep">/</span>
            <span><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-page-detail-grid" style="display:grid;grid-template-columns:<?php echo $pd_sidebar ? '1fr 280px' : '1fr'; ?>;gap:24px;">

            <article class="cdg-card cdg-pd-content" style="padding:36px;font-family:'Plus Jakarta Sans',sans-serif;">
                <?php if($page_content): ?>
                <div class="cdg-pd-content-body" style="font-size:15px;line-height:1.7;color:#334155;">
                    <?php echo $page_content; ?>
                </div>
                <?php else: ?>
                <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                    <i class="bi bi-file-earmark-text" style="font-size:48px;display:block;margin-bottom:8px;opacity:0.5;"></i>
                    <p style="font-size:14px;margin:0;">Bu sayfa için henüz içerik eklenmemiş.</p>
                </div>
                <?php endif; ?>
            </article>

            <?php if($pd_sidebar && !empty($pd_sidebar_items)): ?>
            <aside class="cdg-pd-sidebar" style="display:flex;flex-direction:column;gap:16px;">
                <div class="cdg-card" style="padding:20px;">
                    <h4 style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 14px;display:flex;align-items:center;gap:6px;">
                        <i class="bi bi-list-ul" style="color:#2E3B4E;"></i> Ilgili Sayfalar
                    </h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:4px;">
                        <?php foreach($pd_sidebar_items as $side):
                            $s_title = $side['title'] ?? '';
                            $s_link = $side['link'] ?? '#';
                            if(!$s_title) continue;
                        ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($s_link, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" style="display:flex;gap:8px;padding:8px 12px;text-decoration:none;color:#334155;font-size:13px;border-radius:6px;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc';this.style.color='#2E3B4E';" onmouseout="this.style.background='';this.style.color='#334155';">
                                <i class="bi bi-arrow-right-short" style="color:#2E3B4E;flex-shrink:0;font-size:16px;"></i>
                                <span><?php echo htmlspecialchars($s_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="cdg-card" style="padding:20px;background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;">
                    <i class="bi bi-chat-square-text" style="font-size:28px;"></i>
                    <h4 style="font-size:14px;font-weight:800;margin:8px 0 6px;">Sorulariniz mi var?</h4>
                    <p style="font-size:12px;opacity:0.9;margin:0 0 12px;">Bizimle iletişime geçin, hizmetlerimiz hakkında bilgi alın.</p>
                    <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null,'CRLink') ? Controllers::$init->CRLink('contact') : '/contact'); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;color:#2E3B4E;text-decoration:none;border-radius:6px;font-size:12px;font-weight:700;">
                        <i class="bi bi-envelope"></i> İletişim
                    </a>
                </div>
            </aside>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.cdg-pd-content-body h1, .cdg-pd-content-body h2, .cdg-pd-content-body h3, .cdg-pd-content-body h4, .cdg-pd-content-body h5, .cdg-pd-content-body h6 { color: #0f172a; font-weight: 800; margin: 22px 0 12px; line-height: 1.3; }
.cdg-pd-content-body h1 { font-size: 26px; }
.cdg-pd-content-body h2 { font-size: 22px; }
.cdg-pd-content-body h3 { font-size: 19px; }
.cdg-pd-content-body h4 { font-size: 16px; }
.cdg-pd-content-body p { margin: 0 0 14px; }
.cdg-pd-content-body code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; color: #be185d; }
.cdg-pd-content-body pre { background: #0f172a; color: #f1f5f9; padding: 14px 18px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
.cdg-pd-content-body ul, .cdg-pd-content-body ol { padding-left: 24px; margin: 0 0 14px; }
.cdg-pd-content-body ul li, .cdg-pd-content-body ol li { margin-bottom: 6px; }
.cdg-pd-content-body blockquote { border-left: 3px solid #00D3E5; background: #eff6ff; padding: 12px 16px; margin: 14px 0; border-radius: 0 8px 8px 0; color: #2E3B4E; }
.cdg-pd-content-body img { max-width: 100%; height: auto; border-radius: 8px; }
.cdg-pd-content-body table { width: 100%; border-collapse: collapse; margin: 14px 0; }
.cdg-pd-content-body table th, .cdg-pd-content-body table td { padding: 8px 12px; border: 1px solid #e2e8f0; font-size: 13px; }
.cdg-pd-content-body table th { background: #f8fafc; font-weight: 700; }
.cdg-pd-content-body a { color: #2E3B4E; text-decoration: underline; }
.cdg-pd-content-body hr { border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0; }

@media (max-width: 900px) {
    .cdg-page-detail-grid { grid-template-columns: 1fr !important; }
}
</style>
