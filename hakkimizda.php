<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php')) include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'cdg-public-styles.php';

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}
$contact_url  = cdg_link('contact');
$hosting_url  = cdg_link('products', ['hosting']);
?>

<section class="cdg-section" style="padding-top:48px;padding-bottom:64px;">
    <div class="cdg-container" style="max-width:1100px;">

        <!-- ===== HERO ===== -->
        <div class="cdg-corp-hero" style="background:linear-gradient(135deg,#2E3B4E,#1A2332);color:#fff;padding:54px 32px;border-radius:20px;margin-bottom:36px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-40px;right:-40px;width:240px;height:240px;background:radial-gradient(circle,rgba(0,229,255,0.18),transparent);"></div>
            <span style="display:inline-block;background:rgba(0,229,255,0.15);color:#00E5FF;padding:6px 16px;border-radius:99px;font-size:12px;font-weight:700;letter-spacing:0.5px;margin-bottom:18px;">HAKKIMIZDA</span>
            <h1 style="font-size:36px;font-weight:800;line-height:1.2;margin:0 0 16px;color:#fff;">Konya'dan iş ortaklarımıza<br><span style="background:linear-gradient(135deg,#00D3E5,#00E5FF);-webkit-background-clip:text;background-clip:text;color:transparent;">teknoloji çözümleri sunuyoruz</span></h1>
            <p style="font-size:16px;color:rgba(255,255,255,0.78);line-height:1.7;max-width:720px;margin:0;">CODEGA, AKSOY GROUP iştiraki olarak Konya merkezli bir <strong>web yazılım ve hosting şirketidir</strong>. Bölgemizdeki KOBİ'ler, mali müşavirler ve kurumsal işletmeler için özelleştirilmiş web siteleri, e-Ticaret sistemleri ve yönetim yazılımları geliştiriyoruz.</p>
        </div>

        <!-- ===== KURUMSAL KİMLİK ===== -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-bottom:40px;">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <i class="bi bi-building" style="font-size:22px;color:#2E3B4E;"></i>
                    <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Kuruluş</h3>
                </div>
                <div style="font-size:13.5px;color:#475569;line-height:1.7;">
                    AKSOY GROUP bünyesinde dijital hizmetler birimi olarak <strong>Konya</strong>'da faaliyet gösteriyoruz.
                </div>
            </div>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <i class="bi bi-geo-alt-fill" style="font-size:22px;color:#00D3E5;"></i>
                    <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Lokasyon</h3>
                </div>
                <div style="font-size:13.5px;color:#475569;line-height:1.7;">
                    <strong>Konya</strong> ofisimizden Türkiye genelindeki müşterilerimize hizmet veriyoruz. Sunucularımız Türkiye merkezli veri merkezinde barındırılır.
                </div>
            </div>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <i class="bi bi-people-fill" style="font-size:22px;color:#10b981;"></i>
                    <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Hizmet Alanı</h3>
                </div>
                <div style="font-size:13.5px;color:#475569;line-height:1.7;">
                    Konya bölgesi ağırlıklı olmak üzere <strong>KOBİ'ler, mali müşavirler, sanayi firmaları ve dernekler</strong> için projeler üretiyoruz.
                </div>
            </div>
        </div>

        <!-- ===== HİKAYE ===== -->
        <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:0 0 16px;">Hakkımızda</h2>
        <p style="font-size:15.5px;line-height:1.85;color:#475569;margin-bottom:16px;">
            CODEGA, <strong>Aksoy Group</strong>'un dijital teknoloji birimi olarak hayata geçirilmiş bir web yazılım ve hosting markasıdır. Konya'da konumlanmış ekibimiz, bölgesel işletmelerin <em>"sade, hızlı ve sürdürülebilir"</em> dijital çözümlere ulaşmasını sağlamak amacıyla çalışmaktadır.
        </p>
        <p style="font-size:15.5px;line-height:1.85;color:#475569;margin-bottom:16px;">
            Yurtdışı sağlayıcıların dil ve destek bariyerleri, büyük yerli sağlayıcıların ise standart paket dayatmaları karşısında, müşterilerimize <strong>birebir ihtiyaç temelli</strong> projeler hazırlıyoruz. Her iş ortağımızla tek tek görüşüp, ihtiyacına özel kurumsal site, e-Ticaret altyapısı veya yönetim yazılımı geliştiriyoruz.
        </p>
        <p style="font-size:15.5px;line-height:1.85;color:#475569;margin-bottom:32px;">
            Yazılım tarafında müşterilerimize hizmet etmenin yanı sıra, kendi geliştirdiğimiz <strong>CodeGa ERP</strong> sistemini, <strong>e-Fatura/e-Tebligat entegrasyonlarımızı</strong> ve sektörel çözümlerimizi de aynı çatı altında sunuyoruz.
        </p>

        <!-- ===== HİZMETLERİMİZ ===== -->
        <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:32px 0 18px;">Hizmetlerimiz</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-bottom:36px;">
            <div style="background:#f8fafc;border-left:4px solid #00D3E5;padding:18px 20px;border-radius:0 12px 12px 0;">
                <h3 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;"><i class="bi bi-globe2" style="color:#00D3E5;"></i> Web Sitesi &amp; E-Ticaret</h3>
                <p style="font-size:13.5px;color:#64748b;margin:0;line-height:1.7;">Kurumsal sitelerden çok dilli e-Ticaret platformlarına kadar her ölçekte web çözümü. PHP 8 / MariaDB altyapısı, mobil uyumlu tasarım, SEO uyumlu yapı.</p>
            </div>
            <div style="background:#f8fafc;border-left:4px solid #2E3B4E;padding:18px 20px;border-radius:0 12px 12px 0;">
                <h3 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;"><i class="bi bi-server" style="color:#2E3B4E;"></i> Hosting &amp; Domain</h3>
                <p style="font-size:13.5px;color:#64748b;margin:0;line-height:1.7;">DirectAdmin paneli, LiteSpeed sunucu, NVMe SSD diskler ve günlük yedekleme dahil ekonomik ve profesyonel hosting paketleri. Domain kayıt ve transfer.</p>
            </div>
            <div style="background:#f8fafc;border-left:4px solid #10b981;padding:18px 20px;border-radius:0 12px 12px 0;">
                <h3 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;"><i class="bi bi-bar-chart-line" style="color:#10b981;"></i> CodeGa ERP</h3>
                <p style="font-size:13.5px;color:#64748b;margin:0;line-height:1.7;">Kendi geliştirdiğimiz işletme yönetim yazılımı. Cari, fatura, stok, e-Fatura, e-Tebligat, PDKS, bordro modülleriyle KOBİ'lere özel hazırlanmış.</p>
            </div>
            <div style="background:#f8fafc;border-left:4px solid #f59e0b;padding:18px 20px;border-radius:0 12px 12px 0;">
                <h3 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;"><i class="bi bi-receipt" style="color:#f59e0b;"></i> e-Fatura / e-Tebligat</h3>
                <p style="font-size:13.5px;color:#64748b;margin:0;line-height:1.7;">GİB Özel Entegratör altyapısına yönelik geliştirmelerimiz devam etmektedir. XAdES-BES dijital imza, e-Tebligat otomasyonu mali müşavir entegrasyonu.</p>
            </div>
            <div style="background:#f8fafc;border-left:4px solid #8b5cf6;padding:18px 20px;border-radius:0 12px 12px 0;">
                <h3 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;"><i class="bi bi-tools" style="color:#8b5cf6;"></i> Özel Yazılım</h3>
                <p style="font-size:13.5px;color:#64748b;margin:0;line-height:1.7;">İhtiyaca özel CMS, B2B portalları, online randevu sistemleri, iç kullanıma yönelik panel uygulamaları. Talebe göre proje bazlı geliştirme.</p>
            </div>
            <div style="background:#f8fafc;border-left:4px solid #ef4444;padding:18px 20px;border-radius:0 12px 12px 0;">
                <h3 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;"><i class="bi bi-headset" style="color:#ef4444;"></i> Teknik Destek</h3>
                <p style="font-size:13.5px;color:#64748b;margin:0;line-height:1.7;">Müşterilerimize doğrudan iletişim üzerinden Türkçe destek sağlıyoruz. Ticket, e-posta veya telefon ile mesai saatleri içinde hızlı yanıt.</p>
            </div>
        </div>

        <!-- ===== ÇALIŞMA YAKLAŞIMIMIZ ===== -->
        <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:32px 0 16px;">Çalışma Yaklaşımımız</h2>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:32px;">
            <ul style="list-style:none;padding:0;margin:0;display:grid;gap:14px;">
                <li style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="flex-shrink:0;width:32px;height:32px;border-radius:8px;background:#00D3E5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">1</div>
                    <div>
                        <strong style="color:#0f172a;font-size:14.5px;">Görüşme &amp; analiz</strong>
                        <p style="margin:4px 0 0;font-size:13.5px;color:#64748b;line-height:1.7;">Her projeden önce ihtiyacı yüz yüze veya telefon ile dinleriz. Hazır şablon değil, size uygun çözüm planlanır.</p>
                    </div>
                </li>
                <li style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="flex-shrink:0;width:32px;height:32px;border-radius:8px;background:#2E3B4E;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">2</div>
                    <div>
                        <strong style="color:#0f172a;font-size:14.5px;">Şeffaf fiyatlandırma</strong>
                        <p style="margin:4px 0 0;font-size:13.5px;color:#64748b;line-height:1.7;">Proje öncesinde net fiyat ve süre verilir. Sonradan eklenen kalem yoktur; her ek talep yazılı onayla yürütülür.</p>
                    </div>
                </li>
                <li style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="flex-shrink:0;width:32px;height:32px;border-radius:8px;background:#10b981;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">3</div>
                    <div>
                        <strong style="color:#0f172a;font-size:14.5px;">Geliştirme &amp; test</strong>
                        <p style="margin:4px 0 0;font-size:13.5px;color:#64748b;line-height:1.7;">Modern PHP 8.3+, GitHub sürüm yönetimi ve düzenli yedeklerle geliştirme yapılır. Test sürümü müşterinin onayından sonra yayınlanır.</p>
                    </div>
                </li>
                <li style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="flex-shrink:0;width:32px;height:32px;border-radius:8px;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">4</div>
                    <div>
                        <strong style="color:#0f172a;font-size:14.5px;">Yayın sonrası bakım</strong>
                        <p style="margin:4px 0 0;font-size:13.5px;color:#64748b;line-height:1.7;">Teslimden sonra da yanınızdayız. Güncellemeler, ufak değişiklikler ve teknik problemler için süreklilik temelli destek sunarız.</p>
                    </div>
                </li>
            </ul>
        </div>

        <!-- ===== KURUMSAL BİLGİLER ===== -->
        <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:32px 0 16px;">Kurumsal Bilgiler</h2>
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:0;overflow:hidden;margin-bottom:32px;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tbody>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 18px;color:#64748b;font-weight:600;width:200px;background:#f8fafc;">Marka adı</td>
                        <td style="padding:14px 18px;color:#0f172a;font-weight:700;">CODEGA</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 18px;color:#64748b;font-weight:600;background:#f8fafc;">Bağlı olduğu grup</td>
                        <td style="padding:14px 18px;color:#0f172a;">AKSOY GROUP</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 18px;color:#64748b;font-weight:600;background:#f8fafc;">Faaliyet alanı</td>
                        <td style="padding:14px 18px;color:#0f172a;">Web yazılım, hosting hizmetleri, e-Fatura, ERP</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 18px;color:#64748b;font-weight:600;background:#f8fafc;">Merkez</td>
                        <td style="padding:14px 18px;color:#0f172a;">Konya, Türkiye</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 18px;color:#64748b;font-weight:600;background:#f8fafc;">Web</td>
                        <td style="padding:14px 18px;color:#0f172a;"><a href="https://codega.com.tr" style="color:#00D3E5;font-weight:600;text-decoration:none;">codega.com.tr</a></td>
                    </tr>
                    <tr>
                        <td style="padding:14px 18px;color:#64748b;font-weight:600;background:#f8fafc;">İletişim</td>
                        <td style="padding:14px 18px;color:#0f172a;"><a href="<?php echo $contact_url; ?>" style="color:#00D3E5;font-weight:600;text-decoration:none;">İletişim sayfası &rsaquo;</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ===== CTA ===== -->
        <div style="background:linear-gradient(135deg,#2E3B4E,#1A2332);color:#fff;padding:36px 32px;border-radius:16px;text-align:center;">
            <h3 style="margin:0 0 10px;font-size:22px;font-weight:800;">Bir proje mi planlıyorsunuz?</h3>
            <p style="margin:0 0 20px;font-size:14.5px;color:rgba(255,255,255,0.78);max-width:600px;margin-left:auto;margin-right:auto;line-height:1.7;">İhtiyacınızı dinleyip size uygun bir çözüm planlayalım. Görüşme ücretsiz ve bağlayıcı değildir.</p>
            <a href="<?php echo $contact_url; ?>" style="display:inline-flex;align-items:center;gap:8px;background:#00D3E5;color:#0f172a;padding:12px 26px;border-radius:10px;font-weight:700;text-decoration:none;font-size:14px;">
                <i class="bi bi-envelope-fill"></i> İletişime Geçin
            </a>
            <a href="<?php echo $hosting_url; ?>" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.1);color:#fff;padding:12px 26px;border-radius:10px;font-weight:700;text-decoration:none;font-size:14px;margin-left:8px;border:1px solid rgba(255,255,255,0.2);">
                <i class="bi bi-server"></i> Hosting Paketleri
            </a>
        </div>

    </div>
</section>

<?php
/* === DEFANSIVE FALLBACK ===
 * Eğer WiseCP master-content uygulamadıysa, sayfa header/footer ile sarılmamış olur.
 */
if(empty($_cdg_in_master_content) && !headers_sent()) {
    if(file_exists(__DIR__ . "/inc/main-footer.php")) {
        include __DIR__ . "/inc/main-footer.php";
    }
    if(class_exists("View") && method_exists("View", "footer_codes")) View::footer_codes();
}
?>
