<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php'; ?>

<style>
.cdg-404-wrap{position:relative;padding:80px 0 100px;min-height:calc(100vh - 200px);display:flex;align-items:center;background:linear-gradient(135deg,#f8fafc 0%,#eff6ff 100%);overflow:hidden}
.cdg-404-wrap::before{content:'';position:absolute;top:-100px;right:-100px;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(0,229,255,0.08) 0%,transparent 70%);pointer-events:none}
.cdg-404-wrap::after{content:'';position:absolute;bottom:-150px;left:-150px;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(46,59,78,0.05) 0%,transparent 70%);pointer-events:none}
.cdg-404-inner{position:relative;z-index:1;max-width:980px;margin:0 auto;text-align:center;padding:0 24px}
.cdg-404-num{font-size:clamp(120px,18vw,220px);font-weight:900;line-height:0.9;letter-spacing:-0.05em;background:linear-gradient(135deg,#2E3B4E 0%,#00D3E5 60%,#00E5FF 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent;margin:0 0 12px;display:inline-block;position:relative}
.cdg-404-num::after{content:'';position:absolute;left:0;right:0;bottom:8px;height:8px;background:rgba(0,229,255,0.15);filter:blur(20px);z-index:-1}
.cdg-404-icon{display:inline-flex;align-items:center;justify-content:center;width:88px;height:88px;border-radius:24px;background:linear-gradient(135deg,#2E3B4E 0%,#1e293b 100%);box-shadow:0 20px 50px rgba(46,59,78,0.25),inset 0 1px 0 rgba(255,255,255,0.1);margin-bottom:24px}
.cdg-404-icon i{font-size:38px;color:#00E5FF}
.cdg-404-title{font-size:clamp(24px,4vw,36px);font-weight:800;color:#0f172a;margin:0 0 14px;letter-spacing:-0.02em}
.cdg-404-subtitle{font-size:17px;color:#64748b;line-height:1.6;max-width:560px;margin:0 auto 36px}
.cdg-404-actions{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-bottom:48px}
.cdg-404-btn{display:inline-flex;align-items:center;gap:10px;padding:14px 28px;border-radius:12px;font-weight:600;font-size:15px;text-decoration:none;transition:all 0.2s;border:none;cursor:pointer}
.cdg-404-btn-primary{background:linear-gradient(135deg,#2E3B4E 0%,#1e293b 100%);color:white;box-shadow:0 8px 20px rgba(46,59,78,0.25)}
.cdg-404-btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(46,59,78,0.35);color:white}
.cdg-404-btn-secondary{background:white;color:#2E3B4E;border:2px solid rgba(46,59,78,0.12)}
.cdg-404-btn-secondary:hover{border-color:#00D3E5;color:#00D3E5;transform:translateY(-2px)}
.cdg-404-popular{background:white;border:1px solid rgba(46,59,78,0.08);border-radius:20px;padding:32px;box-shadow:0 8px 30px rgba(15,23,42,0.05)}
.cdg-404-popular-title{font-size:13px;font-weight:700;color:#94a3b8;letter-spacing:0.1em;text-transform:uppercase;margin:0 0 20px}
.cdg-404-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
.cdg-404-link{display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:10px;background:#f8fafc;color:#334155;text-decoration:none;font-weight:600;font-size:14px;transition:all 0.2s;border:1px solid transparent}
.cdg-404-link:hover{background:linear-gradient(135deg,rgba(0,211,229,0.08) 0%,rgba(0,229,255,0.05) 100%);border-color:rgba(0,211,229,0.3);color:#0f172a;transform:translateX(4px)}
.cdg-404-link i{color:#00D3E5;font-size:18px;flex-shrink:0}
@media (max-width:640px){.cdg-404-actions{flex-direction:column;align-items:stretch}.cdg-404-btn{justify-content:center}.cdg-404-popular{padding:24px 20px}.cdg-404-links{grid-template-columns:1fr}}
</style>

<section class="cdg-404-wrap">
    <div class="cdg-404-inner">
        <div class="cdg-404-icon">
            <i class="bi bi-compass"></i>
        </div>
        <div class="cdg-404-num">404</div>
        <h1 class="cdg-404-title">Aradığınız sayfayı bulamadık</h1>
        <p class="cdg-404-subtitle">Sayfa kaldırılmış, taşınmış veya hiç var olmamış olabilir. Sorunun çözüm yolunu birlikte bulalım.</p>

        <div class="cdg-404-actions">
            <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>" class="cdg-404-btn cdg-404-btn-primary">
                <i class="bi bi-house-door"></i> Anasayfaya Dön
            </a>
            <a href="javascript:history.back()" class="cdg-404-btn cdg-404-btn-secondary">
                <i class="bi bi-arrow-left"></i> Geri Git
            </a>
        </div>

        <div class="cdg-404-popular">
            <div class="cdg-404-popular-title">Popüler Sayfalar</div>
            <div class="cdg-404-links">
                <a href="<?php echo defined('APP_URI') ? APP_URI . '/' : '/'; ?>#paketler" class="cdg-404-link">
                    <i class="bi bi-hdd-network"></i> Hosting Paketleri
                </a>
                <a href="<?php echo (defined('APP_URI') ? APP_URI : '') . '/domain'; ?>" class="cdg-404-link">
                    <i class="bi bi-globe2"></i> Domain Sorgula
                </a>
                <a href="<?php echo (defined('APP_URI') ? APP_URI : '') . '/knowledgebase'; ?>" class="cdg-404-link">
                    <i class="bi bi-book"></i> Bilgi Bankası
                </a>
                <a href="<?php echo (defined('APP_URI') ? APP_URI : '') . '/contact'; ?>" class="cdg-404-link">
                    <i class="bi bi-headset"></i> İletişim
                </a>
                <a href="/hakkimizda.html" class="cdg-404-link">
                    <i class="bi bi-building"></i> Hakkımızda
                </a>
                <a href="<?php echo (defined('APP_URI') ? APP_URI : '') . '/sign-in'; ?>" class="cdg-404-link">
                    <i class="bi bi-person-circle"></i> Hesabım
                </a>
            </div>
        </div>
    </div>
</section>
