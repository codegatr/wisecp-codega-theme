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
    </style>