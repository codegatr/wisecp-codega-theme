<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<style>
/* CODEGA Migration Pages CSS - ortak dosya */

/* === REFERANSLAR === */
/* CODEGA - Referanslar sayfası (codega.com.tr migration) */
    .cdg-refs-hero {
        position: relative;
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-refs-hero::before {
        content: '';
        position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
        pointer-events: none;
    }
    .cdg-refs-hero::after {
        content: '';
        position: absolute;
        bottom: -120px; left: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,211,229,0.18) 0%, transparent 70%);
        filter: blur(80px);
        pointer-events: none;
    }
    .cdg-refs-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
    }
    .cdg-refs-hero-content { position: relative; z-index: 1; text-align: center; max-width: 760px; margin: 0 auto; }
    .cdg-refs-eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 16px;
        background: rgba(0,229,255,0.10);
        border: 1px solid rgba(0,229,255,0.30);
        border-radius: 100px;
        color: #00E5FF;
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.05em; text-transform: uppercase;
        margin-bottom: 22px;
    }
    .cdg-refs-eyebrow .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #00E5FF;
        box-shadow: 0 0 8px #00E5FF;
        animation: cdgRefsPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgRefsPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .cdg-refs-hero h1 {
        font-size: clamp(32px, 4.5vw, 48px);
        font-weight: 800;
        margin: 0 0 16px;
        letter-spacing: -0.02em;
        line-height: 1.15;
        color: #fff;
    }
    .cdg-refs-hero h1 span {
        background: linear-gradient(135deg, #00D3E5 0%, #00E5FF 50%, #67E8F9 100%);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .cdg-refs-hero p {
        font-size: 17px;
        color: rgba(255,255,255,0.78);
        line-height: 1.65;
        margin: 0 0 36px;
    }
    .cdg-refs-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        max-width: 720px;
        margin: 0 auto;
    }
    .cdg-refs-stat {
        padding: 18px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        backdrop-filter: blur(8px);
    }
    .cdg-refs-stat-num {
        font-size: 30px;
        font-weight: 800;
        color: #00E5FF;
        line-height: 1;
        margin-bottom: 5px;
    }
    .cdg-refs-stat-lbl {
        font-size: 12px;
        color: rgba(255,255,255,0.65);
        font-weight: 500;
    }
    @media (max-width: 640px) {
        .cdg-refs-stats { grid-template-columns: repeat(2, 1fr); }
        .cdg-refs-stat { padding: 14px 10px; }
        .cdg-refs-stat-num { font-size: 24px; }
    }

    /* Filtre chip'leri */
    .cdg-refs-filter {
        padding: 24px 0 12px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }
    .cdg-refs-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }
    .cdg-refs-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: inherit;
    }
    .cdg-refs-chip:hover {
        border-color: #00D3E5;
        color: #2E3B4E;
        background: #f0fdff;
    }
    .cdg-refs-chip.active {
        background: linear-gradient(135deg, #2E3B4E, #1e293b);
        border-color: #2E3B4E;
        color: #fff;
        box-shadow: 0 6px 16px rgba(46,59,78,0.25);
    }
    .cdg-refs-chip .count {
        display: inline-block;
        padding: 1px 7px;
        background: rgba(0,0,0,0.08);
        color: inherit;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
    }
    .cdg-refs-chip.active .count {
        background: rgba(0,229,255,0.20);
        color: #00E5FF;
    }

    /* Premium müşteri kartı */
    .cdg-refs-premium {
        padding: 60px 0 40px;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .cdg-refs-section-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 40px;
    }
    .cdg-refs-section-eyebrow {
        display: inline-block;
        padding: 6px 14px;
        background: rgba(0,211,229,0.10);
        border: 1px solid rgba(0,211,229,0.25);
        border-radius: 100px;
        color: #00D3E5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }
    .cdg-refs-section-head h2 {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -0.02em;
    }
    .cdg-refs-section-head p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    .cdg-refs-premium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 18px;
    }
    .cdg-refs-premium-card {
        position: relative;
        padding: 28px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }
    .cdg-refs-premium-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #2E3B4E 0%, #00D3E5 50%, #00E5FF 100%);
    }
    .cdg-refs-premium-card:hover {
        transform: translateY(-4px);
        border-color: #00D3E5;
        box-shadow: 0 20px 50px rgba(46,59,78,0.10);
    }
    .cdg-refs-premium-head {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .cdg-refs-premium-logo {
        width: 60px; height: 60px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.02em;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .cdg-refs-premium-info { min-width: 0; flex: 1; }
    .cdg-refs-premium-name {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.3;
    }
    .cdg-refs-premium-sector {
        font-size: 12px;
        color: #00D3E5;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .cdg-refs-premium-service {
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .cdg-refs-premium-service i {
        color: #00D3E5;
        margin-top: 2px;
        flex-shrink: 0;
    }
    .cdg-refs-premium-badge {
        position: absolute;
        top: 16px; right: 16px;
        padding: 4px 10px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-radius: 100px;
        box-shadow: 0 4px 10px rgba(251,191,36,0.35);
    }

    /* Standart müşteri grid */
    .cdg-refs-standard {
        padding: 40px 0 80px;
        background: #fff;
    }
    .cdg-refs-standard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }
    .cdg-refs-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .cdg-refs-card:hover {
        border-color: #00D3E5;
        background: #f0fdff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,211,229,0.12);
    }
    .cdg-refs-logo {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .cdg-refs-info { min-width: 0; flex: 1; }
    .cdg-refs-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cdg-refs-sector {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Boş sonuç */
    .cdg-refs-empty {
        display: none;
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    .cdg-refs-empty i { font-size: 48px; color: #cbd5e1; margin-bottom: 12px; display: block; }

    /* CTA */
    .cdg-refs-cta {
        padding: 60px 0;
        background: #f8fafc;
        text-align: center;
    }
    .cdg-refs-cta-card {
        max-width: 720px;
        margin: 0 auto;
        padding: 44px 32px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #fff;
    }
    .cdg-refs-cta-card::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        pointer-events: none;
    }
    .cdg-refs-cta-card h3 {
        position: relative;
        font-size: 28px;
        font-weight: 800;
        margin: 0 0 10px;
        letter-spacing: -0.01em;
        color: #fff;
    }
    .cdg-refs-cta-card p {
        position: relative;
        font-size: 15px;
        color: rgba(255,255,255,0.80);
        margin: 0 0 24px;
        line-height: 1.6;
    }
    .cdg-refs-cta-actions {
        position: relative;
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .cdg-refs-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-refs-cta-btn-primary {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        color: #0f172a !important;
        box-shadow: 0 8px 22px rgba(0,229,255,0.30);
    }
    .cdg-refs-cta-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,229,255,0.45);
    }
    .cdg-refs-cta-btn-outline {
        background: rgba(255,255,255,0.10);
        color: #fff !important;
        border: 2px solid rgba(255,255,255,0.20);
    }
    .cdg-refs-cta-btn-outline:hover {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.40);
    }

    @media (max-width: 768px) {
        .cdg-refs-hero { padding: 50px 0 40px; }
        .cdg-refs-section-head h2 { font-size: 24px; }
        .cdg-refs-premium-card { padding: 22px; }
        .cdg-refs-standard-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
        .cdg-refs-cta-card { padding: 32px 22px; }
        .cdg-refs-cta-card h3 { font-size: 22px; }
    }

/* === VISION === */
.cdg-vision-hero {
        position: relative;
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-vision-hero::before {
        content: ''; position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-vision-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
    }
    .cdg-vision-hero-content { position: relative; z-index: 1; text-align: center; max-width: 720px; margin: 0 auto; }
    .cdg-vision-hero h1 {
        font-size: clamp(32px, 4.5vw, 48px);
        font-weight: 800; margin: 0 0 16px;
        letter-spacing: -0.02em; line-height: 1.15; color: #fff;
    }
    .cdg-vision-hero h1 span {
        background: linear-gradient(135deg, #00D3E5, #00E5FF, #67E8F9);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cdg-vision-hero p {
        font-size: 17px;
        color: rgba(255,255,255,0.78);
        line-height: 1.65; margin: 0;
    }

    /* Vizyon + Misyon kartlar */
    .cdg-vision-vm {
        padding: 80px 0;
        background: #fff;
    }
    .cdg-vision-vm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .cdg-vision-vm-card {
        position: relative;
        padding: 36px 32px;
        background: linear-gradient(180deg, #f8fafc, #fff);
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        transition: all 0.3s;
    }
    .cdg-vision-vm-card:hover {
        transform: translateY(-4px);
        border-color: #00D3E5;
        box-shadow: 0 24px 60px rgba(46,59,78,0.10);
    }
    .cdg-vision-vm-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2E3B4E, #00D3E5);
        border-radius: 20px 20px 0 0;
    }
    .cdg-vision-vm-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 26px;
        margin-bottom: 18px;
        box-shadow: 0 12px 24px rgba(46,59,78,0.20);
    }
    .cdg-vision-vm-label {
        font-size: 12px;
        font-weight: 800;
        color: #00D3E5;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 10px;
    }
    .cdg-vision-vm-card h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 14px;
        letter-spacing: -0.01em;
        line-height: 1.25;
    }
    .cdg-vision-vm-card p {
        font-size: 15px;
        color: #475569;
        line-height: 1.7;
        margin: 0;
    }
    @media (max-width: 768px) {
        .cdg-vision-vm-grid { grid-template-columns: 1fr; }
    }

    /* Değerler */
    .cdg-vision-values {
        padding: 80px 0;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .cdg-vision-section-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 48px;
    }
    .cdg-vision-section-eyebrow {
        display: inline-block;
        padding: 6px 14px;
        background: rgba(0,211,229,0.10);
        border: 1px solid rgba(0,211,229,0.25);
        border-radius: 100px;
        color: #00D3E5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }
    .cdg-vision-section-head h2 {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -0.02em;
    }
    .cdg-vision-section-head p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    .cdg-vision-values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }
    .cdg-vision-value {
        padding: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-vision-value:hover {
        border-color: #00D3E5;
        background: #f0fdff;
        transform: translateY(-2px);
    }
    .cdg-vision-value-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(0,211,229,0.10), rgba(0,229,255,0.05));
        color: #00D3E5;
        display: grid;
        place-items: center;
        font-size: 22px;
        margin-bottom: 16px;
    }
    .cdg-vision-value h3 {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
    }
    .cdg-vision-value p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }

    /* Tarihçe timeline */
    .cdg-vision-timeline {
        padding: 80px 0;
        background: #fff;
    }
    .cdg-vision-timeline-list {
        max-width: 720px;
        margin: 0 auto;
        position: relative;
    }
    .cdg-vision-timeline-list::before {
        content: '';
        position: absolute;
        top: 30px; bottom: 30px;
        left: 30px;
        width: 2px;
        background: linear-gradient(180deg, #00D3E5, #2E3B4E);
        opacity: 0.2;
    }
    .cdg-vision-milestone {
        position: relative;
        display: flex;
        gap: 24px;
        padding: 16px 0 32px;
    }
    .cdg-vision-milestone:last-child { padding-bottom: 0; }
    .cdg-vision-milestone-icon {
        position: relative;
        z-index: 1;
        width: 60px; height: 60px;
        flex-shrink: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 24px;
        box-shadow: 0 12px 28px rgba(46,59,78,0.20);
    }
    .cdg-vision-milestone-content {
        flex: 1;
        padding: 18px 22px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-vision-milestone-content:hover {
        border-color: #00D3E5;
        background: #fff;
        box-shadow: 0 12px 28px rgba(0,211,229,0.10);
    }
    .cdg-vision-milestone-year {
        display: inline-block;
        padding: 3px 10px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        border-radius: 100px;
        margin-bottom: 8px;
    }
    .cdg-vision-milestone h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cdg-vision-milestone p {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    @media (max-width: 640px) {
        .cdg-vision-timeline-list::before { left: 24px; }
        .cdg-vision-milestone-icon { width: 48px; height: 48px; font-size: 20px; }
    }

    /* CTA */
    .cdg-vision-cta {
        padding: 60px 0 80px;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        text-align: center;
    }
    .cdg-vision-cta-card {
        max-width: 720px;
        margin: 0 auto;
        padding: 44px 32px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #fff;
    }
    .cdg-vision-cta-card::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
    }
    .cdg-vision-cta-card h3 {
        position: relative;
        font-size: 26px;
        font-weight: 800;
        margin: 0 0 10px;
        color: #fff;
    }
    .cdg-vision-cta-card p {
        position: relative;
        font-size: 15px;
        color: rgba(255,255,255,0.80);
        margin: 0 0 24px;
        line-height: 1.6;
    }
    .cdg-vision-cta-actions {
        position: relative;
        display: inline-flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .cdg-vision-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-vision-cta-btn-primary {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        color: #0f172a !important;
        box-shadow: 0 8px 22px rgba(0,229,255,0.30);
    }
    .cdg-vision-cta-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,229,255,0.45);
    }

/* === ERP === */
/* CODEGA ERP sayfası - codega.com.tr/pages/erp.php migration */
    .cdg-erp-hero {
        position: relative;
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-erp-hero::before {
        content: '';
        position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-erp-hero::after {
        content: '';
        position: absolute;
        bottom: -120px; left: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(0,211,229,0.18) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-erp-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
    }
    .cdg-erp-hero-content { position: relative; z-index: 1; text-align: center; max-width: 820px; margin: 0 auto; }
    .cdg-erp-eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 16px;
        background: rgba(0,229,255,0.10);
        border: 1px solid rgba(0,229,255,0.30);
        border-radius: 100px;
        color: #00E5FF;
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.05em; text-transform: uppercase;
        margin-bottom: 22px;
    }
    .cdg-erp-eyebrow .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #00E5FF;
        box-shadow: 0 0 8px #00E5FF;
        animation: cdgErpPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgErpPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    .cdg-erp-hero h1 {
        font-size: clamp(32px, 5vw, 56px);
        font-weight: 800;
        margin: 0 0 18px;
        letter-spacing: -0.02em;
        line-height: 1.1;
        color: #fff;
    }
    .cdg-erp-hero h1 span {
        background: linear-gradient(135deg, #00D3E5 0%, #00E5FF 50%, #67E8F9 100%);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .cdg-erp-hero p {
        font-size: 18px;
        color: rgba(255,255,255,0.78);
        line-height: 1.65;
        margin: 0 0 32px;
        max-width: 640px;
        margin-left: auto; margin-right: auto;
    }
    .cdg-erp-hero-actions {
        display: flex; justify-content: center; gap: 12px;
        flex-wrap: wrap;
    }
    .cdg-erp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-erp-btn-primary {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        color: #0f172a !important;
        box-shadow: 0 12px 28px rgba(0,229,255,0.35);
    }
    .cdg-erp-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(0,229,255,0.50);
        color: #0f172a !important;
    }
    .cdg-erp-btn-outline {
        background: rgba(255,255,255,0.10);
        color: #fff !important;
        border: 2px solid rgba(255,255,255,0.20);
        backdrop-filter: blur(8px);
    }
    .cdg-erp-btn-outline:hover {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.40);
    }

    /* Section başlık */
    .cdg-erp-section {
        padding: 80px 0;
        background: #fff;
    }
    .cdg-erp-section.alt { background: linear-gradient(180deg, #f8fafc 0%, #fff 100%); }
    .cdg-erp-section-head {
        text-align: center;
        max-width: 720px;
        margin: 0 auto 56px;
    }
    .cdg-erp-section-eyebrow {
        display: inline-block;
        padding: 6px 14px;
        background: rgba(0,211,229,0.10);
        border: 1px solid rgba(0,211,229,0.25);
        border-radius: 100px;
        color: #00D3E5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .cdg-erp-section-head h2 {
        font-size: clamp(28px, 3.5vw, 38px);
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 14px;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .cdg-erp-section-head h2 span {
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cdg-erp-section-head p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.65;
        margin: 0;
    }

    /* Modül grid */
    .cdg-erp-modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }
    .cdg-erp-module {
        position: relative;
        padding: 28px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .cdg-erp-module:hover {
        transform: translateY(-4px);
        border-color: #00D3E5;
        box-shadow: 0 20px 40px rgba(46,59,78,0.08);
    }
    .cdg-erp-module-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 26px;
        margin-bottom: 18px;
    }
    .cdg-erp-module h3 {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
        letter-spacing: -0.01em;
    }
    .cdg-erp-module-desc {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 18px;
    }
    .cdg-erp-module-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .cdg-erp-module-features li {
        position: relative;
        padding: 6px 0 6px 22px;
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
    }
    .cdg-erp-module-features li::before {
        content: '';
        position: absolute;
        left: 0; top: 11px;
        width: 14px; height: 14px;
        background: linear-gradient(135deg, #00D3E5, #00E5FF);
        mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E") center/contain no-repeat;
        -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E") center/contain no-repeat;
    }

    /* Sektör grid */
    .cdg-erp-sectors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }
    .cdg-erp-sector {
        padding: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-erp-sector:hover {
        border-color: #00D3E5;
        box-shadow: 0 12px 28px rgba(0,211,229,0.10);
        transform: translateY(-2px);
    }
    .cdg-erp-sector-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(0,211,229,0.10), rgba(0,229,255,0.05));
        color: #00D3E5;
        display: grid;
        place-items: center;
        font-size: 24px;
        margin-bottom: 14px;
    }
    .cdg-erp-sector h3 {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cdg-erp-sector p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.55;
        margin: 0;
    }

    /* Avantajlar */
    .cdg-erp-advantages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }
    .cdg-erp-advantage {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 22px 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s;
    }
    .cdg-erp-advantage:hover {
        border-color: #00D3E5;
        background: #f0fdff;
    }
    .cdg-erp-advantage-icon {
        width: 44px; height: 44px;
        flex-shrink: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 20px;
        box-shadow: 0 8px 18px rgba(46,59,78,0.20);
    }
    .cdg-erp-advantage-content { flex: 1; min-width: 0; }
    .cdg-erp-advantage h3 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .cdg-erp-advantage p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.55;
        margin: 0;
    }

    /* Karşılaştırma */
    .cdg-erp-compare {
        max-width: 760px;
        margin: 0 auto;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 32px rgba(15,23,42,0.05);
    }
    .cdg-erp-compare table {
        width: 100%;
        border-collapse: collapse;
    }
    .cdg-erp-compare th {
        padding: 18px 16px;
        background: linear-gradient(135deg, #f8fafc, #fff);
        text-align: center;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        border-bottom: 2px solid #e2e8f0;
    }
    .cdg-erp-compare th.highlight {
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        position: relative;
    }
    .cdg-erp-compare th.highlight::after {
        content: 'EN İYİ';
        position: absolute;
        top: 6px; right: 6px;
        padding: 2px 8px;
        background: #fbbf24;
        color: #0f172a;
        font-size: 9px;
        font-weight: 800;
        border-radius: 100px;
        letter-spacing: 0.05em;
    }
    .cdg-erp-compare td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }
    .cdg-erp-compare tr:last-child td { border-bottom: 0; }
    .cdg-erp-compare td:first-child {
        text-align: left;
        font-weight: 600;
        color: #0f172a;
    }
    .cdg-erp-compare .ok i { color: #10b981; font-size: 18px; }
    .cdg-erp-compare .no i { color: #ef4444; font-size: 18px; opacity: 0.5; }
    .cdg-erp-compare .partial i { color: #f59e0b; font-size: 18px; }

    /* CTA */
    .cdg-erp-cta {
        padding: 80px 0;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        position: relative;
        overflow: hidden;
        text-align: center;
        color: #fff;
    }
    .cdg-erp-cta::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(0,229,255,0.20) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-erp-cta-content {
        position: relative;
        z-index: 1;
        max-width: 720px;
        margin: 0 auto;
    }
    .cdg-erp-cta h2 {
        font-size: clamp(28px, 3.5vw, 40px);
        font-weight: 800;
        margin: 0 0 14px;
        letter-spacing: -0.02em;
    }
    .cdg-erp-cta h2 span {
        background: linear-gradient(135deg, #00D3E5, #00E5FF, #67E8F9);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cdg-erp-cta p {
        font-size: 17px;
        color: rgba(255,255,255,0.80);
        line-height: 1.65;
        margin: 0 0 32px;
    }
    .cdg-erp-cta-actions {
        display: flex; justify-content: center; gap: 12px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .cdg-erp-hero { padding: 50px 0 40px; }
        .cdg-erp-section { padding: 56px 0; }
        .cdg-erp-modules-grid { grid-template-columns: 1fr; }
        .cdg-erp-module { padding: 22px; }
        .cdg-erp-compare { font-size: 12px; }
        .cdg-erp-compare th, .cdg-erp-compare td { padding: 10px 8px; }
        .cdg-erp-compare th.highlight::after { display: none; }
    }

/* === SYSTEM-STATUS === */
.cdg-sys-hero {
        position: relative;
        padding: 60px 0 40px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2E3B4E 100%);
        color: #fff;
        overflow: hidden;
    }
    .cdg-sys-hero::before {
        content: ''; position: absolute;
        top: -120px; right: -120px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%);
        filter: blur(80px);
    }
    .cdg-sys-hero-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
        background-size: 48px 48px;
    }
    .cdg-sys-hero-content { position: relative; z-index: 1; text-align: center; max-width: 720px; margin: 0 auto; }
    .cdg-sys-hero h1 {
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800; margin: 0 0 14px;
        letter-spacing: -0.02em; color: #fff;
    }
    .cdg-sys-hero p {
        font-size: 16px;
        color: rgba(255,255,255,0.78);
        margin: 0 0 24px;
    }

    .cdg-sys-overall {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 14px 24px;
        background: rgba(16,185,129,0.10);
        border: 2px solid rgba(16,185,129,0.40);
        border-radius: 100px;
        font-size: 15px;
        font-weight: 700;
        color: #34d399;
    }
    .cdg-sys-overall .dot {
        width: 12px; height: 12px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 12px #10b981;
        animation: cdgSysPulse 1.5s ease-in-out infinite;
    }
    @keyframes cdgSysPulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.2); } }
    .cdg-sys-overall.degraded { background: rgba(245,158,11,0.10); border-color: rgba(245,158,11,0.40); color: #fbbf24; }
    .cdg-sys-overall.degraded .dot { background: #f59e0b; box-shadow: 0 0 12px #f59e0b; }
    .cdg-sys-overall.outage { background: rgba(239,68,68,0.10); border-color: rgba(239,68,68,0.40); color: #f87171; }
    .cdg-sys-overall.outage .dot { background: #ef4444; box-shadow: 0 0 12px #ef4444; }

    /* Servisler */
    .cdg-sys-services {
        padding: 60px 0;
        background: #fff;
    }
    .cdg-sys-services-list {
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .cdg-sys-category {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .cdg-sys-category:hover {
        border-color: #00D3E5;
        box-shadow: 0 12px 28px rgba(0,211,229,0.05);
    }
    .cdg-sys-category-head {
        padding: 18px 22px;
        background: linear-gradient(180deg, #f8fafc, #fff);
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cdg-sys-category-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2E3B4E, #00D3E5);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .cdg-sys-category-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        flex: 1;
    }
    .cdg-sys-category-services {
        padding: 8px 0;
    }
    .cdg-sys-service {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 22px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .cdg-sys-service:last-child { border-bottom: 0; }
    .cdg-sys-service-status {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .cdg-sys-service-name {
        flex: 1;
        color: #0f172a;
        font-weight: 500;
    }
    .cdg-sys-service-uptime {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .cdg-sys-service-label {
        padding: 4px 10px;
        background: rgba(16,185,129,0.10);
        color: #059669;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Olaylar */
    .cdg-sys-incidents {
        padding: 60px 0;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .cdg-sys-section-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 36px;
    }
    .cdg-sys-section-head h2 {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 10px;
        letter-spacing: -0.02em;
    }
    .cdg-sys-section-head p {
        font-size: 15px;
        color: #64748b;
        margin: 0;
    }
    .cdg-sys-incidents-list {
        max-width: 760px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .cdg-sys-incident {
        padding: 18px 22px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .cdg-sys-incident:hover {
        border-color: #00D3E5;
        box-shadow: 0 8px 20px rgba(0,211,229,0.05);
    }
    .cdg-sys-incident-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }
    .cdg-sys-incident-date {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .cdg-sys-incident-resolved {
        padding: 3px 10px;
        background: rgba(16,185,129,0.10);
        color: #059669;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .cdg-sys-incident h3 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cdg-sys-incident p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.55;
        margin: 0 0 8px;
    }
    .cdg-sys-incident-duration {
        font-size: 12px;
        color: #475569;
    }
    .cdg-sys-incident-duration i { color: #00D3E5; }

    /* Boş olay durumu */
    .cdg-sys-no-incidents {
        text-align: center;
        padding: 36px 24px;
        background: rgba(16,185,129,0.05);
        border: 1px dashed rgba(16,185,129,0.30);
        border-radius: 14px;
        color: #059669;
    }
    .cdg-sys-no-incidents i { font-size: 48px; margin-bottom: 10px; display: block; }

    /* CTA */
    .cdg-sys-cta {
        padding: 60px 0;
        background: #fff;
        text-align: center;
    }
    .cdg-sys-cta-card {
        max-width: 640px;
        margin: 0 auto;
        padding: 32px;
        background: linear-gradient(135deg, #f8fafc, #fff);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .cdg-sys-cta h3 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
    }
    .cdg-sys-cta p {
        font-size: 14px;
        color: #64748b;
        margin: 0 0 20px;
    }
    .cdg-sys-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        background: linear-gradient(135deg, #2E3B4E, #1e293b);
        color: #fff !important;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cdg-sys-cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(46,59,78,0.30);
    }

    @media (max-width: 640px) {
        .cdg-sys-service { flex-wrap: wrap; padding: 12px 16px; }
        .cdg-sys-service-uptime { width: 100%; padding-left: 24px; }
        .cdg-sys-incident-head { flex-direction: column; align-items: flex-start; gap: 6px; }
    }
</style>