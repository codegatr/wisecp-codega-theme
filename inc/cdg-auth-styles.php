<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<style>
    /* CODEGA - Auth sayfasi (sign-in / sign-up) modern tasarim */
    body#cdg-auth { margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; background: #1A2332; color: #fff; -webkit-font-smoothing: antialiased; }
    body#cdg-auth * { box-sizing: border-box; }
    body#cdg-auth a { text-decoration: none; }

    .cdg-auth-section {
        min-height: 100vh;
        position: relative;
        background: linear-gradient(135deg, #1A2332 0%, #1A2332 50%, #485A75 100%);
        padding: 40px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .cdg-auth-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
    .cdg-auth-mesh, .cdg-auth-aurora {
        position: absolute;
        top: -20%; left: -20%;
        width: 80%; height: 80%;
        background: radial-gradient(circle, rgba(96,165,250,0.30) 0%, transparent 60%);
        filter: blur(60px);
        animation: cdgAuthFloat 18s ease-in-out infinite;
    }
    .cdg-auth-glow {
        position: absolute;
        width: 400px; height: 400px;
        border-radius: 50%;
        filter: blur(100px);
        pointer-events: none;
    }
    .cdg-auth-glow-1 { top: 10%; right: -100px; background: rgba(251,191,36,0.20); animation: cdgAuthFloat 14s ease-in-out infinite; }
    .cdg-auth-glow-2 { bottom: 10%; left: -100px; background: rgba(56,189,248,0.25); animation: cdgAuthFloat 16s ease-in-out infinite reverse; }
    @keyframes cdgAuthFloat { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(8%, 6%) scale(1.1); } }
    .cdg-auth-grid-pattern {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
    }
    .cdg-auth-particles { position: absolute; inset: 0; pointer-events: none; }
    .cdg-auth-particles span {
        position: absolute;
        width: 6px; height: 6px;
        background: rgba(255,255,255,0.50);
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(96,165,250,0.80);
        animation: cdgAuthParticle 12s linear infinite;
    }
    .cdg-auth-particles span:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; }
    .cdg-auth-particles span:nth-child(2) { left: 25%; top: 70%; animation-delay: -2s; }
    .cdg-auth-particles span:nth-child(3) { left: 45%; top: 30%; animation-delay: -4s; width: 4px; height: 4px; }
    .cdg-auth-particles span:nth-child(4) { left: 65%; top: 80%; animation-delay: -6s; }
    .cdg-auth-particles span:nth-child(5) { left: 80%; top: 25%; animation-delay: -8s; width: 8px; height: 8px; }
    .cdg-auth-particles span:nth-child(6) { left: 90%; top: 65%; animation-delay: -10s; }
    @keyframes cdgAuthParticle { 0%,100% { transform: translateY(0) scale(1); opacity: 0.5; } 50% { transform: translateY(-30px) scale(1.4); opacity: 1; } }

    /* GRID layout (sol tanitim, sag form) */
    .cdg-auth-container {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }
    .cdg-auth-grid {
        display: grid;
        grid-template-columns: 1fr 480px;
        gap: 60px;
        align-items: center;
    }
    @media (max-width: 980px) {
        .cdg-auth-grid { grid-template-columns: 1fr; gap: 40px; max-width: 480px; margin: 0 auto; }
        .cdg-auth-promo { display: none !important; }
    }

    /* Sol tanitim panel */
    .cdg-auth-promo { color: #fff; }
    .cdg-auth-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 16px;
        background: rgba(251,191,36,0.15);
        border: 1px solid rgba(251,191,36,0.40);
        border-radius: 100px;
        color: #fde047;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }
    .cdg-auth-pill-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #fbbf24;
        box-shadow: 0 0 8px #fbbf24;
        animation: cdgAuthPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgAuthPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .cdg-auth-promo h1, .cdg-auth-hero-text h1 {
        font-size: clamp(32px, 4vw, 44px);
        font-weight: 900;
        line-height: 1.15;
        margin: 0 0 16px;
        color: #fff;
        letter-spacing: -0.02em;
    }
    .cdg-auth-promo h1 .cdg-text-gradient,
    .cdg-auth-hero-text .cdg-text-gradient {
        background: linear-gradient(135deg, #fbbf24, #fde047, #00D3E5);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
    }
    .cdg-auth-lead { font-size: 16px; line-height: 1.7; color: rgba(255,255,255,0.85); margin: 0 0 28px; max-width: 480px; }

    /* Sag form karti */
    .cdg-auth-form-wrap { width: 100%; }
    .cdg-auth-card {
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 30px 80px rgba(0,0,0,0.30), 0 0 0 1px rgba(255,255,255,0.10) inset;
        color: #0f172a;
    }
    .cdg-auth-card-head { text-align: center; margin-bottom: 28px; }
    .cdg-auth-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .cdg-auth-brand-logo {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, #2E3B4E, #485A75);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
        font-weight: 900;
        box-shadow: 0 8px 20px rgba(46,59,78,0.30);
    }
    .cdg-auth-brand-text { font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
    .cdg-auth-card-head h2 { margin: 6px 0 4px; font-size: 22px; font-weight: 800; color: #0f172a; }
    .cdg-auth-card-head p { margin: 0; color: #64748b; font-size: 14px; }

    /* Form alanlari */
    .cdg-auth-card form { margin: 0; }
    .cdg-auth-card .formcon, .cdg-auth-card .form-group, .cdg-auth-card .cdg-auth-field { margin-bottom: 14px; }
    .cdg-auth-card label { display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; }
    .cdg-auth-card input[type=text],
    .cdg-auth-card input[type=email],
    .cdg-auth-card input[type=password],
    .cdg-auth-card input[type=tel],
    .cdg-auth-card input[type=number],
    .cdg-auth-card select,
    .cdg-auth-card textarea {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        background: #fff;
        color: #0f172a;
        transition: all 0.2s;
        font-family: inherit;
        box-sizing: border-box;
    }
    .cdg-auth-card input:focus, .cdg-auth-card select:focus, .cdg-auth-card textarea:focus {
        border-color: #2E3B4E;
        box-shadow: 0 0 0 3px rgba(46,59,78,0.12);
    }
    .cdg-auth-card .mio-ajax-submit,
    .cdg-auth-card button[type=submit],
    .cdg-auth-card .gonderbtn,
    .cdg-auth-card a.gonderbtn,
    .cdg-auth-card .lbtn {
        display: block;
        width: 100%;
        padding: 14px 22px;
        margin-top: 8px;
        background: linear-gradient(135deg, #485A75, #2E3B4E);
        color: #fff !important;
        border: 0;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s;
        box-shadow: 0 8px 20px rgba(46,59,78,0.25);
        font-family: inherit;
    }
    .cdg-auth-card .mio-ajax-submit:hover,
    .cdg-auth-card button[type=submit]:hover,
    .cdg-auth-card .gonderbtn:hover {
        background: linear-gradient(135deg, #2E3B4E, #1A2332);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(46,59,78,0.35);
    }
    .cdg-auth-card .blueclear, .cdg-auth-card .greybtn {
        display: inline-block;
        color: #2E3B4E !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        margin-top: 8px;
    }
    .cdg-auth-card .blueclear:hover { text-decoration: underline; }
    .cdg-auth-card .formfoot, .cdg-auth-card .formfoot1 {
        text-align: center;
        font-size: 13px;
        color: #475569;
        margin-top: 18px;
    }
    .cdg-auth-card .formfoot a, .cdg-auth-card .formfoot1 a { color: #2E3B4E; font-weight: 700; }

    /* Hata/uyari mesajlari */
    .cdg-auth-card .alert, .cdg-auth-card #ErrorBlock {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 14px;
    }

    /* Divider */
    .cdg-auth-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 18px 0;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .cdg-auth-divider::before, .cdg-auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    /* Sosyal/connection butonlari */
    .cdg-auth-card .conlogin {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
    }
    .cdg-auth-card .conlogin a {
        flex: 1;
        padding: 11px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        color: #0f172a;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
    }
    .cdg-auth-card .conlogin a:hover { background: #f1f5f9; border-color: #cbd5e1; }

    /* Modal (two-factor / location verification) */
    .cdg-auth-modal-content {
        background: #fff;
        color: #0f172a;
        padding: 32px;
        border-radius: 16px;
        max-width: 440px;
        margin: 0 auto;
    }
    .cdg-auth-modal-content h1 {
        font-size: 22px;
        font-weight: 800;
        margin: 0 0 12px;
        text-align: center;
        color: #0f172a;
    }
    .cdg-auth-modal-content h1 i {
        display: block;
        font-size: 36px;
        color: #2E3B4E;
        margin-bottom: 8px;
    }
    .cdg-auth-modal-content p { color: #64748b; font-size: 14px; line-height: 1.6; text-align: center; margin: 0 0 14px; }
    .cdg-auth-modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }
    .cdg-auth-modal-actions a, .cdg-auth-modal-actions button {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        border: 0;
    }

    /* Bottom (logo/footer) */
    .cdg-auth-bottom {
        text-align: center;
        padding: 18px 0;
        color: rgba(255,255,255,0.60);
        font-size: 12px;
    }
    .cdg-auth-bottom a { color: rgba(255,255,255,0.85); }

    /* CTA card */
    .cdg-auth-cta-card {
        margin-top: 28px;
        padding: 16px 20px;
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.20);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        color: rgba(255,255,255,0.90);
        font-size: 13px;
        line-height: 1.6;
    }
    .cdg-auth-cta-btn {
        display: inline-block;
        padding: 10px 20px;
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.30);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        margin-top: 10px;
        transition: all 0.2s;
    }
    .cdg-auth-cta-btn:hover { background: rgba(255,255,255,0.25); }

    /* Preview kart (sol tarafta panel onizleme) */
    .cdg-auth-preview {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 16px;
        padding: 16px;
        backdrop-filter: blur(12px);
        margin-top: 24px;
    }
    .cdg-auth-preview-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        color: rgba(255,255,255,0.85);
        font-size: 12px;
    }
    .cdg-auth-preview-dots { display: flex; gap: 6px; }
    .cdg-auth-preview-dots span { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.30); }
    .cdg-auth-preview-live {
        margin-left: auto;
        padding: 3px 8px;
        background: rgba(16,185,129,0.20);
        border: 1px solid rgba(16,185,129,0.40);
        border-radius: 100px;
        color: #6ee7b7;
        font-size: 10px;
        font-weight: 700;
    }
    .cdg-auth-preview-bars { display: grid; gap: 6px; }
    .cdg-auth-preview-bars > div {
        height: 8px;
        background: linear-gradient(90deg, rgba(255,255,255,0.20), rgba(255,255,255,0.08));
        border-radius: 4px;
    }
    .cdg-auth-preview-bars > div:nth-child(1) { width: 80%; }
    .cdg-auth-preview-bars > div:nth-child(2) { width: 60%; }
    .cdg-auth-preview-bars > div:nth-child(3) { width: 90%; }

    .cdg-auth-preview-activity { margin-top: 14px; }
    .cdg-auth-preview-activity-head { color: rgba(255,255,255,0.70); font-size: 11px; margin-bottom: 8px; }
    /* ====================================================
       v3.5.49 - PREMIUM AUTH OVERHAUL
       ==================================================== */

    /* Section bg glow upgrade - daha derin */
    body#cdg-auth { background: #0B1220; }
    .cdg-auth-section {
        background:
            radial-gradient(ellipse at top right, rgba(0,229,255,0.18) 0%, transparent 50%),
            radial-gradient(ellipse at bottom left, rgba(46,59,78,0.45) 0%, transparent 50%),
            linear-gradient(135deg, #0B1220 0%, #1A2332 50%, #0B1220 100%);
        padding: 60px 24px;
    }
    .cdg-auth-glow-1 { background: rgba(0,229,255,0.18) !important; }
    .cdg-auth-glow-2 { background: rgba(0,211,229,0.16) !important; }

    /* Status pill - cyan kurumsal */
    .cdg-auth-pill {
        background: rgba(0,229,255,0.08) !important;
        border-color: rgba(0,229,255,0.30) !important;
        color: #00E5FF !important;
        backdrop-filter: blur(8px);
    }
    .cdg-auth-pill-dot { background: #00E5FF !important; box-shadow: 0 0 12px #00E5FF !important; }

    /* Hero h1 gradient - kurumsal */
    .cdg-auth-promo h1 .cdg-text-gradient,
    .cdg-auth-hero-text .cdg-text-gradient,
    .cdg-text-gradient-light {
        background: linear-gradient(135deg, #00D3E5 0%, #00E5FF 50%, #67E8F9 100%) !important;
        -webkit-background-clip: text !important;
        background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        color: transparent !important;
    }

    /* Brand mark - logo upgrade */
    .cdg-auth-brand-logo {
        background: linear-gradient(135deg, #2E3B4E 0%, #00D3E5 100%) !important;
        box-shadow: 0 12px 30px rgba(0,211,229,0.40), inset 0 1px 0 rgba(255,255,255,0.2) !important;
    }

    /* Browser preview - daha temiz */
    .cdg-auth-preview {
        background: rgba(255,255,255,0.04) !important;
        border-color: rgba(255,255,255,0.08) !important;
        box-shadow: 0 16px 50px rgba(0,0,0,0.30), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .cdg-auth-preview-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }
    .cdg-auth-preview-stat {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
        min-width: 0;
    }
    .cdg-auth-preview-stat-icon {
        width: 36px; height: 36px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 16px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.20);
    }
    .cdg-auth-preview-stat .num { font-size: 18px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 3px; }
    .cdg-auth-preview-stat .lbl { font-size: 10.5px; color: rgba(255,255,255,0.55); white-space: nowrap; }
    .cdg-auth-preview-bars {
        display: flex !important;
        align-items: flex-end;
        gap: 4px;
        height: 60px;
        margin-top: 10px;
    }
    .cdg-auth-preview-bars span {
        flex: 1;
        background: linear-gradient(180deg, #00E5FF 0%, #00D3E5 100%);
        border-radius: 3px 3px 0 0;
        animation: cdgAuthBar 2.5s ease-in-out infinite;
        opacity: 0.85;
    }
    .cdg-auth-preview-bars span:nth-child(2n) { animation-delay: 0.2s; }
    .cdg-auth-preview-bars span:nth-child(3n) { animation-delay: 0.4s; }
    @keyframes cdgAuthBar { 0%,100% { opacity: 0.65; transform: scaleY(0.85); transform-origin: bottom; } 50% { opacity: 1; transform: scaleY(1); } }
    .cdg-auth-preview-activity-head {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        font-size: 12px !important;
        color: rgba(255,255,255,0.7) !important;
    }
    .cdg-auth-preview-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px !important;
        background: rgba(0,229,255,0.10) !important;
        border-color: rgba(0,229,255,0.40) !important;
        color: #00E5FF !important;
        font-size: 10px !important;
    }
    .cdg-auth-preview-live .dot { width: 6px; height: 6px; border-radius: 50%; background: #00E5FF; box-shadow: 0 0 8px #00E5FF; animation: cdgAuthPulse 1.5s infinite; }

    /* Quick-grid - daha temiz */
    .cdg-auth-quick-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin: 18px 0;
    }
    .cdg-auth-quick-item {
        text-align: center;
        padding: 14px 8px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
        transition: all 0.2s;
    }
    .cdg-auth-quick-item:hover {
        background: rgba(0,229,255,0.06);
        border-color: rgba(0,229,255,0.25);
        transform: translateY(-2px);
    }
    .cdg-auth-quick-icon { font-size: 22px; margin-bottom: 6px; }
    .cdg-auth-quick-item span { font-size: 11px; color: rgba(255,255,255,0.75); font-weight: 600; }

    /* CTA kart upgrade */
    .cdg-auth-cta-card {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 22px !important;
        background: linear-gradient(135deg, rgba(0,229,255,0.10) 0%, rgba(0,211,229,0.06) 100%) !important;
        border: 1px solid rgba(0,229,255,0.25) !important;
        border-radius: 14px !important;
        margin-top: 24px !important;
    }
    .cdg-auth-cta-card small { display: block; font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    .cdg-auth-cta-card strong { display: block; font-size: 15px; color: #fff; font-weight: 700; }
    .cdg-auth-cta-btn {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
        padding: 12px 22px !important;
        background: linear-gradient(135deg, #00D3E5 0%, #00E5FF 100%) !important;
        color: #0f172a !important;
        border: 0 !important;
        border-radius: 10px !important;
        font-weight: 700;
        font-size: 14px !important;
        margin-top: 0 !important;
        white-space: nowrap;
        box-shadow: 0 8px 22px rgba(0,229,255,0.30);
        transition: all 0.2s !important;
    }
    .cdg-auth-cta-btn:hover {
        background: linear-gradient(135deg, #00E5FF 0%, #67E8F9 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,229,255,0.45) !important;
    }

    /* Trust row - 3 sutun yan yana - HER ZAMAN */
    .cdg-auth-trust-row {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 18px;
    }
    .cdg-auth-trust-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 12px 8px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
        text-align: center;
        font-size: 11px;
        color: rgba(255,255,255,0.75);
        font-weight: 600;
    }
    .cdg-auth-trust-item i { font-size: 18px; color: #00E5FF; }

    /* FORM upgrade - modern */
    .cdg-auth-card {
        background: #fff !important;
        border-radius: 24px !important;
        padding: 44px !important;
        box-shadow: 0 30px 80px rgba(0,0,0,0.30), 0 0 0 1px rgba(255,255,255,0.08) inset;
        position: relative;
        overflow: hidden;
    }
    .cdg-auth-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2E3B4E 0%, #00D3E5 50%, #00E5FF 100%);
    }
    .cdg-auth-card-head h2 {
        font-size: 26px !important;
        letter-spacing: -0.02em;
    }
    .cdg-auth-card-head p {
        font-size: 14px !important;
        color: #64748b !important;
    }
    .cdg-auth-card label {
        font-size: 12px !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569 !important;
    }
    .cdg-auth-card input[type=text],
    .cdg-auth-card input[type=email],
    .cdg-auth-card input[type=password],
    .cdg-auth-card input[type=tel],
    .cdg-auth-card input[type=number],
    .cdg-auth-card select,
    .cdg-auth-card textarea {
        padding: 14px 16px !important;
        border-radius: 12px !important;
        border-width: 2px !important;
        font-size: 15px !important;
        background: #f8fafc !important;
    }
    .cdg-auth-card input:focus, .cdg-auth-card select:focus, .cdg-auth-card textarea:focus {
        border-color: #00D3E5 !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(0,211,229,0.10) !important;
    }
    /* Password toggle */
    .cdg-auth-pwd-wrap { position: relative; }
    .cdg-auth-pwd-toggle {
        position: absolute;
        top: 50%; right: 12px;
        transform: translateY(-50%);
        background: transparent;
        border: 0;
        cursor: pointer;
        color: #64748b;
        padding: 6px;
        border-radius: 6px;
        font-size: 16px;
    }
    .cdg-auth-pwd-toggle:hover { color: #2E3B4E; background: #f1f5f9; }

    /* Submit - cyan gradient */
    .cdg-auth-card .mio-ajax-submit,
    .cdg-auth-card button[type=submit],
    .cdg-auth-card input[type=submit],
    .cdg-auth-submit {
        width: 100%;
        padding: 16px 24px !important;
        background: linear-gradient(135deg, #2E3B4E 0%, #1e293b 100%) !important;
        color: #fff !important;
        border: 0 !important;
        border-radius: 12px !important;
        font-size: 15px !important;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s !important;
        box-shadow: 0 12px 28px rgba(46,59,78,0.30);
        position: relative;
        overflow: hidden;
        margin-top: 10px;
    }
    .cdg-auth-card .mio-ajax-submit:hover,
    .cdg-auth-card button[type=submit]:hover,
    .cdg-auth-submit:hover {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(46,59,78,0.40) !important;
    }

    /* Sosyal login butonlari modern */
    #cdg-auth a.btn,
    #cdg-auth .login-button,
    #cdg-auth .social-btn,
    .cdg-auth-card .btn-google,
    .cdg-auth-card .btn-facebook,
    .cdg-auth-card .clientarea-connection-buttons a {
        display: flex !important;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px 16px !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        margin-bottom: 8px !important;
        border: 2px solid #e2e8f0 !important;
        background: #fff !important;
        color: #0f172a !important;
        transition: all 0.2s !important;
    }
    #cdg-auth a.btn:hover,
    .cdg-auth-card .clientarea-connection-buttons a:hover {
        border-color: #00D3E5 !important;
        background: #f0fdff !important;
        transform: translateY(-1px);
    }

    /* Captcha alanı - modern */
    .cdg-auth-card .captcha-image,
    .cdg-auth-card img[src*="captcha"] {
        border-radius: 10px !important;
        border: 2px solid #e2e8f0;
        max-width: 220px !important;
    }

    /* Beni hatırla checkbox */
    .cdg-auth-card .checkbox-row,
    .cdg-auth-card label[for*="remember"],
    .cdg-auth-card .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 0;
        font-size: 13px !important;
        color: #475569 !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        font-weight: 500 !important;
        cursor: pointer;
    }

    /* ====================================================
       RESPONSIVE - tum boyutlar duzenli
       ==================================================== */

    @media (max-width: 1100px) {
        .cdg-auth-grid {
            grid-template-columns: 1fr 420px !important;
            gap: 40px !important;
        }
        .cdg-auth-card { padding: 36px !important; }
        .cdg-auth-promo h1 { font-size: clamp(28px, 3.2vw, 36px) !important; }
        .cdg-auth-quick-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 980px) {
        .cdg-auth-grid {
            grid-template-columns: 1fr !important;
            gap: 32px !important;
            max-width: 540px !important;
        }
        .cdg-auth-promo {
            display: block !important;
            order: 1;
            text-align: center;
        }
        .cdg-auth-form-wrap { order: 2; }
        .cdg-auth-promo h1 { font-size: 28px !important; max-width: 100%; }
        .cdg-auth-lead { max-width: 100%; font-size: 14px !important; }
        .cdg-auth-brand { justify-content: center; }
        .cdg-auth-pill { margin: 0 auto 16px; }
        .cdg-auth-preview { display: none; }
        .cdg-auth-quick-grid { grid-template-columns: repeat(4, 1fr); margin: 16px 0; }
        .cdg-auth-quick-item { padding: 10px 6px; }
        .cdg-auth-quick-icon { font-size: 18px; }
        .cdg-auth-cta-card {
            flex-direction: row !important;
            text-align: left;
        }
        .cdg-auth-trust-row { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 640px) {
        .cdg-auth-section { padding: 24px 16px; }
        .cdg-auth-card { padding: 28px 20px !important; border-radius: 18px !important; }
        .cdg-auth-card-head h2 { font-size: 22px !important; }
        .cdg-auth-promo h1 { font-size: 24px !important; }
        .cdg-auth-lead { font-size: 13px !important; margin-bottom: 18px !important; }
        .cdg-auth-quick-grid { display: none; }
        .cdg-auth-cta-card {
            flex-direction: column !important;
            gap: 12px;
            text-align: center;
        }
        .cdg-auth-cta-btn { width: 100%; justify-content: center; }
        .cdg-auth-trust-row { gap: 6px; }
        .cdg-auth-trust-item { padding: 10px 4px; font-size: 10px; }
        .cdg-auth-trust-item i { font-size: 16px; }
    }

    @media (max-width: 420px) {
        .cdg-auth-card { padding: 24px 16px !important; }
        .cdg-auth-card label { font-size: 11px !important; }
        .cdg-auth-card input { font-size: 14px !important; padding: 12px 14px !important; }
    }



    /* ====================================================
       v3.5.51 - GERCEKTEN EKSIK CSS (duplicate yok)
       ==================================================== */

    /* Sign-up: Avantajlar grid (sign-up'a ozel - css/style.css'te yok) */
    .cdg-auth-benefits {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin: 24px 0;
    }
    .cdg-auth-benefit {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 16px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: all 0.2s;
    }
    .cdg-auth-benefit:hover {
        background: rgba(0,229,255,0.05);
        border-color: rgba(0,229,255,0.20);
        transform: translateX(2px);
    }
    .cdg-auth-benefit-icon {
        width: 44px; height: 44px;
        flex-shrink: 0;
        border-radius: 12px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.20);
    }
    .cdg-auth-benefit strong {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 3px;
    }
    .cdg-auth-benefit small {
        display: block;
        font-size: 12px;
        color: rgba(255,255,255,0.65);
        line-height: 1.5;
    }
    @media (max-width: 980px) {
        .cdg-auth-benefits { grid-template-columns: 1fr 1fr; }
        .cdg-auth-benefit { padding: 12px; }
        .cdg-auth-benefit-icon { width: 38px; height: 38px; font-size: 17px; }
    }
    @media (max-width: 640px) {
        .cdg-auth-benefits { grid-template-columns: 1fr; }
    }

    /* Password goster/gizle toggle - css/style.css'te yok */
    /* CRITICAL: cdg-input-icon i selector\'ı butonun icindeki ikonu da yakaliyor, RESET sart */
    .cdg-auth-pwd-wrap { position: relative; }
    .cdg-auth-pwd-wrap .cdg-form-control,
    .cdg-auth-pwd-wrap input { padding-right: 44px !important; }
    .cdg-auth-pwd-toggle {
        position: absolute !important;
        top: 50% !important;
        right: 8px !important;
        left: auto !important;
        transform: translateY(-50%) !important;
        background: transparent !important;
        border: 0 !important;
        cursor: pointer;
        color: #64748b !important;
        padding: 6px 8px !important;
        border-radius: 6px;
        font-size: 16px;
        line-height: 1;
        z-index: 2;
        width: auto !important;
        height: auto !important;
    }
    .cdg-auth-pwd-toggle:hover {
        color: #2E3B4E !important;
        background: #f1f5f9 !important;
    }
    /* Mevcut .cdg-input-icon i kuralini buton icindeki ikon icin RESET et */
    .cdg-input-icon .cdg-auth-pwd-toggle i {
        position: static !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        transform: none !important;
        color: inherit !important;
        font-size: 16px !important;
    }

</style>
<script>
/* Password göster/gizle toggle */
window.cdgPwdToggle = function(inputId, btn){
    var input = document.getElementById(inputId);
    if(!input) return;
    var icon = btn.querySelector('i');
    if(input.type === 'password'){
        input.type = 'text';
        if(icon){ icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
    } else {
        input.type = 'password';
        if(icon){ icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
    }
};
</script>

<style>
/* === FIX v3.5.74: Input icon - padding override === */
/* Auth-card içindeki cdg-input-icon altında bulunan input'larda
 * ikon (zarf, kilit) text ile çakışmaması için padding-left arttırılmalı.
 * .cdg-auth-card input[type=email] kuralı !important ile padding'i 16px yapıyordu,
 * cdg-input-icon'un padding-left:42px'i eziliyordu. Bu kural daha yüksek özgünlükte. */
.cdg-auth-card .cdg-input-icon input[type=text],
.cdg-auth-card .cdg-input-icon input[type=email],
.cdg-auth-card .cdg-input-icon input[type=password],
.cdg-auth-card .cdg-input-icon input[type=tel],
.cdg-auth-card .cdg-input-icon input[type=number] {
    padding-left: 46px !important;
}

/* Şifre toggle butonu için sağ padding (cdg-auth-pwd-toggle ile çakışmasın) */
.cdg-auth-card .cdg-auth-pwd-wrap input[type=password],
.cdg-auth-card .cdg-auth-pwd-wrap input[type=text] {
    padding-right: 48px !important;
}

/* Input ikonun konumu net - daha sol kenarda olsun */
.cdg-auth-card .cdg-input-icon > i:first-child {
    position: absolute !important;
    left: 16px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94a3b8 !important;
    font-size: 16px !important;
    pointer-events: none !important;
    z-index: 2;
}
</style>
