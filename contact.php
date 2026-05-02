<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

if(!function_exists('cdg_link')) {
    function cdg_link($slug, $params = []) {
        if(class_exists('Controllers') && isset(Controllers::$init)) {
            return Controllers::$init->CRLink($slug, $params);
        }
        return '/' . $slug;
    }
}

// Iletişim bilgileri (theme-config'den çek, yoksa fallback)
$config    = include __DIR__ . DS . 'theme-config.php';
$ts        = isset($config['settings']) ? $config['settings'] : [];
$contact_i = isset($ts['contact']) ? $ts['contact'] : [];

$company_address = !empty($contact_i['address']) ? $contact_i['address'] : 'Konya, Türkiye';
$company_phone   = !empty($contact_i['phone']) ? $contact_i['phone'] : '+90 332 909 9656';
$company_email   = !empty($contact_i['email']) ? $contact_i['email'] : 'info@codega.com.tr';
$company_wa      = !empty($contact_i['whatsapp']) ? $contact_i['whatsapp'] : '903329099656';
$company_hours   = !empty($contact_i['hours']) ? $contact_i['hours'] : 'Pazartesi - Cuma: 09:00 - 18:00';

// Form gonderim
$form_msg = '';
$form_success = false;
if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cdg_contact_form'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if(!$name || !$email || !$message) {
        $form_msg = 'Lutfen ad, e-posta ve mesaj alanlarini doldurun.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_msg = 'Gecerli bir e-posta adresi girin.';
    } else {
        // WiseCP destek talebi olarak kayit (Tickets sinifi varsa)
        if(class_exists('Tickets')) {
            try {
                $r = @Tickets::add([
                    'name' => $name, 'email' => $email, 'phone' => $phone,
                    'subject' => $subject ?: 'Iletisim Formu', 'message' => $message,
                    'department' => 1, 'priority' => 'normal'
                ]);
                if($r) { $form_success = true; $form_msg = 'Mesajiniz alindi. En kisa surede size donus yapacagiz.'; }
            } catch(Exception $e) { $form_msg = 'Mesaj gonderilemedi: ' . $e->getMessage(); }
        }
        if(!$form_success) {
            // Email fallback
            $body = "Iletisim Formu\n\nAd: {$name}\nE-posta: {$email}\nTelefon: {$phone}\nKonu: {$subject}\n\nMesaj:\n{$message}";
            @mail($company_email, '[CODEGA Iletisim] ' . ($subject ?: 'Yeni Mesaj'), $body, "From: noreply@codega.com.tr\r\nReply-To: {$email}");
            $form_success = true;
            $form_msg = 'Mesajiniz alindi. En kisa surede size donus yapacagiz.';
        }
    }
}
?>

<!-- 1. PAGE HERO -->
<section class="cdg-page-hero">
    <div class="cdg-page-hero-bg">
        <div class="cdg-mesh-gradient"></div>
        <div class="cdg-hero-grid-pattern"></div>
    </div>
    <div class="cdg-container">
        <div class="cdg-page-hero-content">
            <div class="cdg-eyebrow cdg-eyebrow-glow"><i class="bi bi-chat-heart-fill"></i> Iletisim</div>
            <h1>Sizinle <span class="cdg-text-gradient">tanismaktan</span> mutluluk duyariz</h1>
            <p>Hosting, domain, yazilim cozumlerimiz veya destek hakkinda her konuda bize ulasabilirsiniz. Ortalama yanit suremiz <strong>5 dakikadan az</strong>.</p>
            <div class="cdg-page-hero-cta">
                <a href="https://wa.me/<?php echo $company_wa; ?>" target="_blank" rel="noopener" class="cdg-btn cdg-btn-success cdg-btn-lg cdg-btn-glow"><i class="bi bi-whatsapp"></i> WhatsApp Destek</a>
                <a href="tel:<?php echo str_replace(' ', '', $company_phone); ?>" class="cdg-btn cdg-btn-outline cdg-btn-lg"><i class="bi bi-telephone-fill"></i> <?php echo htmlspecialchars($company_phone); ?></a>
            </div>
        </div>
    </div>
</section>

<!-- 2. CHANNELS GRID -->
<section class="cdg-section cdg-contact-channels">
    <div class="cdg-container">
        <div class="cdg-channel-grid">
            <a href="https://wa.me/<?php echo $company_wa; ?>" target="_blank" rel="noopener" class="cdg-channel-card">
                <div class="cdg-channel-icon" style="background:linear-gradient(135deg,#25d366,#128c7e);"><i class="bi bi-whatsapp"></i></div>
                <h3>WhatsApp</h3>
                <p>Anlik mesajlasma, dosya paylasimi.</p>
                <div class="cdg-channel-meta">7/24 aktif <i class="bi bi-circle-fill" style="color:#10b981;font-size:8px;"></i></div>
                <div class="cdg-channel-cta">Mesaj Gonder <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="tel:<?php echo str_replace(' ', '', $company_phone); ?>" class="cdg-channel-card">
                <div class="cdg-channel-icon" style="background:linear-gradient(135deg,#1e40af,#3b82f6);"><i class="bi bi-telephone-fill"></i></div>
                <h3>Telefon</h3>
                <p>Hemen aramak icin tikla.</p>
                <div class="cdg-channel-meta"><?php echo htmlspecialchars($company_hours); ?></div>
                <div class="cdg-channel-cta">Ara <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="mailto:<?php echo $company_email; ?>" class="cdg-channel-card">
                <div class="cdg-channel-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="bi bi-envelope-fill"></i></div>
                <h3>E-posta</h3>
                <p>Detayli sorular ve dokumanlar icin.</p>
                <div class="cdg-channel-meta"><?php echo htmlspecialchars($company_email); ?></div>
                <div class="cdg-channel-cta">E-posta At <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="<?php echo cdg_link('contact'); ?>#cdg-form" class="cdg-channel-card">
                <div class="cdg-channel-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="bi bi-headset"></i></div>
                <h3>Destek Talebi</h3>
                <p>Teknik konular icin bilet acin.</p>
                <div class="cdg-channel-meta">Ortalama yanit: 5 dk</div>
                <div class="cdg-channel-cta">Talep Olustur <i class="bi bi-arrow-right"></i></div>
            </a>
        </div>
    </div>
</section>

<!-- 3. FORM + INFO -->
<section class="cdg-section" id="cdg-form" style="background:#f8fafc;">
    <div class="cdg-container">
        <div class="cdg-contact-grid">
            <div class="cdg-contact-form-wrap">
                <div class="cdg-eyebrow">Iletisim Formu</div>
                <h2>Bize <span class="cdg-text-gradient">mesaj gonderin</span></h2>
                <p>Formu doldurun, ekibimiz <strong>en gec 1 saat icinde</strong> size donus yapsin.</p>

                <?php if($form_msg): ?>
                <div class="cdg-form-alert <?php echo $form_success ? 'success' : 'error'; ?>">
                    <i class="bi bi-<?php echo $form_success ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?>"></i>
                    <span><?php echo htmlspecialchars($form_msg); ?></span>
                </div>
                <?php endif; ?>

                <form method="post" class="cdg-contact-form">
                    <input type="hidden" name="cdg_contact_form" value="1">
                    <div class="cdg-form-row">
                        <div class="cdg-form-group">
                            <label>Adiniz Soyadiniz <span class="req">*</span></label>
                            <input type="text" name="name" required placeholder="Ornek Yilmaz">
                        </div>
                        <div class="cdg-form-group">
                            <label>E-posta <span class="req">*</span></label>
                            <input type="email" name="email" required placeholder="ornek@email.com">
                        </div>
                    </div>
                    <div class="cdg-form-row">
                        <div class="cdg-form-group">
                            <label>Telefon</label>
                            <input type="tel" name="phone" placeholder="+90 5XX XXX XX XX">
                        </div>
                        <div class="cdg-form-group">
                            <label>Konu</label>
                            <select name="subject">
                                <option value="">Konu secin...</option>
                                <option>Hosting Hakkinda</option>
                                <option>Domain Hakkinda</option>
                                <option>Yazilim Hizmetleri</option>
                                <option>Teknik Destek</option>
                                <option>Faturalama</option>
                                <option>Bayilik / Reseller</option>
                                <option>Diger</option>
                            </select>
                        </div>
                    </div>
                    <div class="cdg-form-group">
                        <label>Mesajiniz <span class="req">*</span></label>
                        <textarea name="message" required rows="5" placeholder="Bize nasil yardimci olabilecegimizi yazin..."></textarea>
                    </div>
                    <button type="submit" class="cdg-btn cdg-btn-primary cdg-btn-lg cdg-btn-glow">
                        <i class="bi bi-send-fill"></i> Mesaji Gonder
                    </button>
                    <p class="cdg-form-privacy"><i class="bi bi-shield-check"></i> Bilgileriniz KVKK kapsaminda korunmaktadir, ucuncu sahislarla paylasilmaz.</p>
                </form>
            </div>

            <div class="cdg-contact-info-wrap">
                <div class="cdg-info-card">
                    <div class="cdg-info-card-head">
                        <i class="bi bi-building"></i>
                        <h3>CODEGA Yazilim</h3>
                    </div>
                    <div class="cdg-info-list">
                        <div class="cdg-info-row">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div><strong>Adres</strong><span><?php echo htmlspecialchars($company_address); ?></span></div>
                        </div>
                        <div class="cdg-info-row">
                            <i class="bi bi-telephone-fill"></i>
                            <div><strong>Telefon</strong><a href="tel:<?php echo str_replace(' ', '', $company_phone); ?>"><?php echo htmlspecialchars($company_phone); ?></a></div>
                        </div>
                        <div class="cdg-info-row">
                            <i class="bi bi-envelope-fill"></i>
                            <div><strong>E-posta</strong><a href="mailto:<?php echo $company_email; ?>"><?php echo htmlspecialchars($company_email); ?></a></div>
                        </div>
                        <div class="cdg-info-row">
                            <i class="bi bi-clock-fill"></i>
                            <div><strong>Calisma Saatleri</strong><span><?php echo htmlspecialchars($company_hours); ?></span></div>
                        </div>
                    </div>
                </div>

                <div class="cdg-info-card cdg-info-card-dark">
                    <div class="cdg-info-card-head">
                        <i class="bi bi-headset"></i>
                        <h3>7/24 Teknik Destek</h3>
                    </div>
                    <p>Hosting ve sunucu sorunlariniz icin destek ekibimiz <strong>haftanin 7 gunu, gunun 24 saati</strong> hazir.</p>
                    <div class="cdg-stats-mini">
                        <div><span class="num">5dk</span><span class="lbl">Ortalama yanit</span></div>
                        <div><span class="num">%99</span><span class="lbl">Memnuniyet</span></div>
                        <div><span class="num">24/7</span><span class="lbl">Aktif destek</span></div>
                    </div>
                    <a href="https://wa.me/<?php echo $company_wa; ?>" target="_blank" rel="noopener" class="cdg-btn cdg-btn-white cdg-btn-block">
                        <i class="bi bi-whatsapp"></i> WhatsApp Destek Hatti
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. MAP -->
<section class="cdg-map-section">
    <div class="cdg-container">
        <div class="cdg-map-wrap">
            <iframe
                src="https://www.openstreetmap.org/export/embed.html?bbox=32.45%2C37.83%2C32.60%2C37.93&amp;layer=mapnik&amp;marker=37.8746%2C32.4932"
                style="width:100%;height:420px;border:0;border-radius:18px;"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <div class="cdg-map-overlay">
                <i class="bi bi-geo-alt-fill"></i>
                <div>
                    <strong>CODEGA Yazilim</strong>
                    <span>Konya, Türkiye</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. FAQ MINI -->
<section class="cdg-section">
    <div class="cdg-container">
        <div class="cdg-section-head">
            <div class="cdg-eyebrow">Sik Sorulan</div>
            <h2>Iletisim hakkinda <span class="cdg-text-gradient">sik sorulan sorular</span></h2>
        </div>
        <div class="cdg-faq-list" style="max-width:780px;margin:32px auto 0;">
            <details class="cdg-faq-item" open>
                <summary><span>Mesajima ne zaman donus yapilir?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Iletisim formundan gelen mesajlar 1 saat icinde, WhatsApp uzerinden gelen mesajlar 5-15 dakika icinde yanitlanir. Acil durumlarda telefonla aramaniz onerilir.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Ofisinizi ziyaret edebilir miyim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Tabii ki! Konya'daki ofisimizi onceden randevu alarak ziyaret edebilirsiniz. Telefon veya e-posta ile randevu olusturabilirsiniz.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Yazilim projeleri icin nasil teklif alabilirim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Iletisim formunda "Yazilim Hizmetleri" konusunu secin, projenizin ozetini yazin. 24 saat icinde detayli teklif gonderiyoruz. Buyuk projeler icin yuz yuze gorusme ayarlanabilir.</div>
            </details>
            <details class="cdg-faq-item">
                <summary><span>Hosting tasimasi icin ne yapmaliyim?</span><i class="bi bi-plus-lg"></i></summary>
                <div class="cdg-faq-answer">Hosting paketinizi aldiktan sonra "Tasima Talebi" formunu doldurun. Mevcut hosting bilgilerinizi paylasin, ekibimiz veri kaybi olmadan tasimayi yapsin. 5 sitee kadar UCRETSIZ tasiriz.</div>
            </details>
        </div>
    </div>
</section>
