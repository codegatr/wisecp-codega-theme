<?php
/**
 * cdg-checkout-stepper.php
 *
 * Forte tarzi 5 adimli sepet/siparis timeline'i.
 *
 * Adimlar:
 *   1. Hizmet & Urun Secimi   (paket secimi - anasayfa, products listesi)
 *   2. Alan Adi Yapilandirmasi (order-steps-domain veya hosting step "domain")
 *   3. Hizmet Konfigurasyonu   (order-steps hosting/server vb. step 1, addons)
 *   4. Siparis Ozeti & Sepet   (basket.php)
 *   5. Siparisi Tamamla        (basket-account.php, basket-payment.php)
 *
 * Kullanim:
 *   <?php
 *   $cdg_active_step = 3; // 1-5
 *   $cdg_page_title = 'Ayarla';
 *   $cdg_section_title = 'Sepet';
 *   include __DIR__ . '/inc/cdg-checkout-stepper.php';
 *   ?>
 */

defined('CORE_FOLDER') OR exit('You can not get in here!');

$cdg_active_step  = $cdg_active_step  ?? 1;
$cdg_section_title = $cdg_section_title ?? 'Sepet';
$cdg_page_title   = $cdg_page_title   ?? '';

$cdg_checkout_steps = [
    ['id' => 1, 'name' => 'Hizmet & Ürün Seçimi'],
    ['id' => 2, 'name' => 'Alan Adı Yapılandırması'],
    ['id' => 3, 'name' => 'Hizmet Konfigürasyonu'],
    ['id' => 4, 'name' => 'Sipariş Özeti & Sepet'],
    ['id' => 5, 'name' => 'Siparişi Tamamla'],
];
?>

<style>
/* ============================================================
   CODEGA CHECKOUT STEPPER (Forte-style)
   5-adimli timeline, sepete ve siparis adimlarina ortak
   Renk: Yesil (#10b981 / #059669) tamamlanmis, gri (#cbd5e1) gelecek
   ============================================================ */

.cdg-co-wrap {
    background: #fff;
    border-radius: 18px;
    padding: 36px 40px;
    margin: 24px auto 28px;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
    max-width: 1200px;
}

.cdg-co-section-title {
    margin: 0 0 28px;
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.5px;
    line-height: 1.1;
}

.cdg-co-stepper {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    margin: 0 auto 8px;
    max-width: 1000px;
    counter-reset: cdgstep;
}

/* Tek bir adim */
.cdg-co-step {
    flex: 1 1 0;
    position: relative;
    text-align: center;
    z-index: 2;
    min-width: 0;
}

/* Adim numara/check dairesi */
.cdg-co-step .cdg-co-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    background: #fff;
    color: #94a3b8;
    border: 2px solid #cbd5e1;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}
.cdg-co-step .cdg-co-circle i { font-size: 16px; }

/* Adim baslik metni */
.cdg-co-step .cdg-co-name {
    font-size: 13px;
    font-weight: 600;
    color: #94a3b8;
    line-height: 1.4;
    transition: color 0.3s ease;
    padding: 0 4px;
}

/* Tamamlanmis adim (yesil + tik) */
.cdg-co-step.done .cdg-co-circle {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    border-color: #10b981;
    box-shadow: 0 4px 10px rgba(16,185,129,0.30);
}
.cdg-co-step.done .cdg-co-name { color: #475569; }

/* Aktif adim (yesil + numara) */
.cdg-co-step.active .cdg-co-circle {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    border-color: #10b981;
    box-shadow: 0 6px 14px rgba(16,185,129,0.35), 0 0 0 4px rgba(16,185,129,0.12);
    transform: scale(1.08);
}
.cdg-co-step.active .cdg-co-name {
    color: #0f172a;
    font-weight: 700;
}

/* Pasif (gelecek) adim - default style */

/* Adim arasi cizgi (each step's right side) */
.cdg-co-step::after {
    content: '';
    position: absolute;
    top: 17px;
    left: calc(50% + 22px);
    right: calc(-50% + 22px);
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
    border-radius: 2px;
    transition: background 0.4s ease;
}
.cdg-co-step:last-child::after { display: none; }

/* Tamamlanmis adimlardan sonraki cizgi yesil */
.cdg-co-step.done::after {
    background: linear-gradient(90deg, #10b981 0%, #10b981 100%);
}

/* === Mobile responsive === */
@media (max-width: 768px) {
    .cdg-co-wrap { padding: 24px 20px; border-radius: 14px; }
    .cdg-co-section-title { font-size: 24px; margin-bottom: 22px; }
    .cdg-co-stepper { gap: 0; }
    .cdg-co-step .cdg-co-circle { width: 32px; height: 32px; font-size: 13px; }
    .cdg-co-step .cdg-co-circle i { font-size: 14px; }
    .cdg-co-step .cdg-co-name { font-size: 11px; line-height: 1.3; }
    .cdg-co-step::after { top: 15px; left: calc(50% + 18px); right: calc(-50% + 18px); height: 2px; }
}
@media (max-width: 480px) {
    .cdg-co-section-title { font-size: 20px; }
    .cdg-co-step .cdg-co-name { font-size: 10px; }
}

/* === Page-title (alt baslik) - Forte'deki "Ayarla" gibi === */
.cdg-co-page-title {
    margin: 24px 0 6px;
    font-size: 32px;
    font-weight: 800;
    color: #2563eb;
    letter-spacing: -0.5px;
    line-height: 1.1;
}
.cdg-co-page-subtitle {
    margin: 0 0 24px;
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
}

/* Eski WiseCP adim cubugunu gizle (ilanasamalar) */
.cdg-co-wrap + #wrapper .ilanasamalar,
body.has-cdg-stepper .ilanasamalar { display: none !important; }

/* Eski sari hero da bu sayfalarda kalkar */
.cdg-co-wrap ~ .cdg-page-hero,
body.has-cdg-stepper .cdg-page-hero { display: none !important; }
</style>

<div class="cdg-co-wrap">
    <h1 class="cdg-co-section-title"><?php echo htmlspecialchars($cdg_section_title); ?></h1>

    <div class="cdg-co-stepper">
        <?php foreach($cdg_checkout_steps as $stp):
            $cls = '';
            if($stp['id'] < $cdg_active_step) $cls = 'done';
            elseif($stp['id'] == $cdg_active_step) $cls = 'active';
        ?>
        <div class="cdg-co-step <?php echo $cls; ?>">
            <div class="cdg-co-circle">
                <?php if($cls === 'done'): ?>
                    <i class="bi bi-check-lg"></i>
                <?php else: ?>
                    <?php echo $stp['id']; ?>
                <?php endif; ?>
            </div>
            <div class="cdg-co-name"><?php echo htmlspecialchars($stp['name']); ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if($cdg_page_title): ?>
        <h2 class="cdg-co-page-title"><?php echo htmlspecialchars($cdg_page_title); ?></h2>
    <?php endif; ?>
</div>
