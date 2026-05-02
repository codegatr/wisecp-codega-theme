<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<style>
/* CODEGA - Public sayfa ortak stilleri (hosting/domain/server/sms/software/contact/knowledgebase) */

.cdg-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.cdg-section { padding: 80px 0; }
.cdg-section-head { text-align: center; max-width: 720px; margin: 0 auto 48px; }
.cdg-section-head h2 { font-size: clamp(28px, 4vw, 40px); font-weight: 900; color: #0f172a; line-height: 1.2; margin: 12px 0 14px; letter-spacing: -0.02em; }
.cdg-section-head p { color: #475569; font-size: 16px; line-height: 1.7; margin: 0; }

/* Eyebrow */
.cdg-eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; background: rgba(30,64,175,0.08); color: #1e40af;
    border-radius: 100px; font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 1px;
}
.cdg-eyebrow-glow {
    background: linear-gradient(135deg, rgba(251,191,36,0.20), rgba(245,158,11,0.30));
    color: #92400e;
    box-shadow: 0 4px 16px rgba(251,191,36,0.25);
}

/* Gradient text */
.cdg-text-gradient {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #06b6d4 100%);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent; color: transparent;
}
.cdg-text-gradient-light {
    background: linear-gradient(135deg, #fbbf24, #fde047, #06b6d4);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent; color: transparent;
}

/* HERO (page) */
.cdg-page-hero {
    position: relative;
    background: linear-gradient(135deg, #0a1f44 0%, #1e3a8a 40%, #1e40af 70%, #2563eb 100%);
    padding: 80px 0 100px;
    overflow: hidden;
    color: #fff;
}
.cdg-page-hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.cdg-mesh-gradient {
    position: absolute; top: -30%; left: -10%; width: 70%; height: 200%;
    background: radial-gradient(circle, rgba(96,165,250,0.30) 0%, transparent 60%);
    filter: blur(60px);
    animation: cdgPubFloat 14s ease-in-out infinite;
}
.cdg-mesh-gradient::after {
    content: ''; position: absolute;
    top: 30%; right: -50%; width: 60%; height: 80%;
    background: radial-gradient(circle, rgba(56,189,248,0.30) 0%, transparent 60%);
    filter: blur(80px);
    animation: cdgPubFloat 18s ease-in-out infinite reverse;
}
.cdg-hero-grid-pattern {
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
}
.cdg-auth-particles { position: absolute; inset: 0; pointer-events: none; }
.cdg-auth-particles span {
    position: absolute; width: 6px; height: 6px;
    background: rgba(255,255,255,0.50); border-radius: 50%;
    box-shadow: 0 0 12px rgba(96,165,250,0.80);
    animation: cdgPubParticle 12s linear infinite;
}
.cdg-auth-particles span:nth-child(1) { left: 8%; top: 20%; animation-delay: 0s; }
.cdg-auth-particles span:nth-child(2) { left: 18%; top: 70%; animation-delay: -2s; }
.cdg-auth-particles span:nth-child(3) { left: 35%; top: 30%; animation-delay: -4s; width: 4px; height: 4px; }
.cdg-auth-particles span:nth-child(4) { left: 55%; top: 80%; animation-delay: -6s; }
.cdg-auth-particles span:nth-child(5) { left: 70%; top: 25%; animation-delay: -8s; width: 8px; height: 8px; }
.cdg-auth-particles span:nth-child(6) { left: 92%; top: 35%; animation-delay: -3s; width: 5px; height: 5px; }
@keyframes cdgPubFloat { 0%,100% { transform: translate(0,0); } 50% { transform: translate(8%, 6%); } }
@keyframes cdgPubParticle { 0%,100% { transform: translateY(0) scale(1); opacity: 0.5; } 50% { transform: translateY(-30px) scale(1.4); opacity: 1; } }

.cdg-page-hero-content {
    position: relative; z-index: 2;
    text-align: center; max-width: 800px; margin: 0 auto;
}
.cdg-page-hero-content .cdg-eyebrow { background: rgba(255,255,255,0.12); color: #fde047; backdrop-filter: blur(10px); }
.cdg-page-hero-content .cdg-eyebrow-glow {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #422006;
    box-shadow: 0 8px 24px rgba(251,191,36,0.40);
}
.cdg-page-hero-content h1 {
    font-size: clamp(34px, 5vw, 52px); font-weight: 900; line-height: 1.15;
    margin: 18px 0 16px; color: #fff; letter-spacing: -0.02em;
    text-shadow: 0 2px 20px rgba(0,0,0,0.20);
}
.cdg-page-hero-content p {
    font-size: 17px; line-height: 1.7;
    color: rgba(255,255,255,0.85);
    margin: 0 auto 32px; max-width: 620px;
}
.cdg-page-hero-content p strong { color: #fde047; font-weight: 700; }
.cdg-page-hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

/* Buton variant'lari (public sayfa) */
.cdg-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 22px; border-radius: 10px; font-size: 14px; font-weight: 800; text-decoration: none; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease; line-height: 1; white-space: nowrap; }
.cdg-btn-lg { padding: 16px 28px; font-size: 15px; border-radius: 12px; }
.cdg-btn-block { display: flex; width: 100%; }
.cdg-btn-primary { background: linear-gradient(135deg, #2563eb, #1e40af); color: #fff !important; }
.cdg-btn-primary:hover { background: linear-gradient(135deg, #1e40af, #1e3a8a); transform: translateY(-2px); box-shadow: 0 12px 28px rgba(30,64,175,0.30); }
.cdg-btn-outline { background: rgba(255,255,255,0.10); color: #fff !important; border-color: rgba(255,255,255,0.30); backdrop-filter: blur(10px); }
.cdg-btn-outline:hover { background: rgba(255,255,255,0.20); border-color: #fde047; color: #fde047 !important; }
.cdg-btn-glow { box-shadow: 0 0 0 0 rgba(251,191,36,0.40); animation: cdgPubGlow 2s ease-in-out infinite; }
@keyframes cdgPubGlow { 0%,100% { box-shadow: 0 8px 24px rgba(30,64,175,0.30); } 50% { box-shadow: 0 12px 32px rgba(30,64,175,0.50), 0 0 0 8px rgba(30,64,175,0.10); } }

/* Section bg over public (white) */
.cdg-section .cdg-eyebrow { background: rgba(30,64,175,0.10); color: #1e40af; }
.cdg-section .cdg-btn-outline { background: transparent; color: #1e40af !important; border-color: #1e40af; backdrop-filter: none; }
.cdg-section .cdg-btn-outline:hover { background: #1e40af; color: #fff !important; }

/* PERFORMANCE GRID */
.cdg-perf-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
@media (max-width: 768px) { .cdg-perf-grid { grid-template-columns: repeat(2, 1fr); } }
.cdg-perf-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    padding: 24px 20px; text-align: center;
    transition: all 0.3s ease;
}
.cdg-perf-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.08); border-color: #1e40af; }
.cdg-perf-icon { font-size: 32px; margin-bottom: 12px; }
.cdg-perf-num { font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1; letter-spacing: -0.02em; }
.cdg-perf-num span { font-size: 16px; color: #64748b; font-weight: 700; margin-left: 2px; }
.cdg-perf-lbl { color: #64748b; font-size: 13px; font-weight: 600; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.5px; }

/* PRICING TABS */
.cdg-pricing-tabs {
    display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;
    margin-bottom: 32px; padding: 6px; background: #f8fafc;
    border-radius: 14px; max-width: fit-content; margin-left: auto; margin-right: auto;
    border: 1px solid #e2e8f0;
}
.cdg-pricing-tab {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 20px; background: transparent; border: 0;
    border-radius: 10px; cursor: pointer;
    color: #64748b; font-size: 14px; font-weight: 700;
    transition: all 0.2s; font-family: inherit;
}
.cdg-pricing-tab:hover { color: #0f172a; background: rgba(255,255,255,0.60); }
.cdg-pricing-tab.active { background: #fff; color: #0f172a; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.cdg-pricing-tab i { font-size: 18px; }
.cdg-pricing-tab small { color: #94a3b8; font-size: 11px; font-weight: 600; }
.cdg-pricing-tab.active small { color: #1e40af; }
@media (max-width: 768px) { .cdg-pricing-tab span { display: none; } .cdg-pricing-tab { padding: 10px 14px; } }

.cdg-pricing-pane { display: none; animation: cdgPubFadeIn 0.3s ease; }
.cdg-pricing-pane.active { display: block; }
@keyframes cdgPubFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.cdg-pricing-pane-desc { text-align: center; color: #64748b; font-size: 15px; margin-bottom: 28px; max-width: 600px; margin-left: auto; margin-right: auto; }

/* Pricing GRID + Card */
.cdg-pricing-grid { display: grid; gap: 20px; max-width: 1200px; margin: 0 auto; }
.cdg-pricing-grid-2 { grid-template-columns: repeat(2, 1fr); max-width: 720px; }
.cdg-pricing-grid-3 { grid-template-columns: repeat(3, 1fr); max-width: 1000px; }
.cdg-pricing-grid-4 { grid-template-columns: repeat(4, 1fr); }
@media (max-width: 1024px) { .cdg-pricing-grid-3, .cdg-pricing-grid-4 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { .cdg-pricing-grid-2, .cdg-pricing-grid-3, .cdg-pricing-grid-4 { grid-template-columns: 1fr; } }

.cdg-price-card {
    background: #fff; border: 2px solid #e2e8f0; border-radius: 16px;
    padding: 28px 22px; position: relative;
    transition: all 0.3s ease;
    display: flex; flex-direction: column;
}
.cdg-price-card:hover { transform: translateY(-6px); box-shadow: 0 20px 50px rgba(0,0,0,0.10); border-color: #cbd5e1; }
.cdg-price-card-highlight {
    border-color: #1e40af !important;
    box-shadow: 0 16px 40px rgba(30,64,175,0.20);
    transform: scale(1.02);
}
.cdg-price-card-highlight:hover { transform: scale(1.02) translateY(-6px); box-shadow: 0 24px 60px rgba(30,64,175,0.30); }
.cdg-price-ribbon {
    position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #422006; padding: 5px 14px; border-radius: 100px;
    font-size: 11px; font-weight: 800; letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(251,191,36,0.40);
}
.cdg-price-cat-tag {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 6px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
    margin-bottom: 12px;
}
.cdg-price-name { font-size: 20px; font-weight: 900; color: #0f172a; margin: 0 0 4px; }
.cdg-price-subtitle { color: #64748b; font-size: 13px; margin: 0 0 18px; }
.cdg-price-amount { display: flex; align-items: baseline; gap: 6px; padding: 16px 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; margin-bottom: 18px; }
.cdg-price-current { display: flex; align-items: baseline; gap: 4px; }
.cdg-price-curr { font-size: 18px; color: #1e40af; font-weight: 800; }
.cdg-price-num { font-size: 36px; font-weight: 900; color: #0f172a; line-height: 1; letter-spacing: -0.02em; }
.cdg-price-period { color: #64748b; font-size: 13px; font-weight: 600; }
.cdg-price-features { list-style: none; padding: 0; margin: 0 0 20px; flex: 1; }
.cdg-price-features li { display: flex; align-items: flex-start; gap: 8px; padding: 7px 0; color: #334155; font-size: 13px; line-height: 1.5; }
.cdg-price-features li i { color: #10b981; font-size: 16px; flex-shrink: 0; margin-top: 1px; }

/* AVANTAJ GRID */
.cdg-adv-grid { display: grid; gap: 20px; }
.cdg-adv-grid-3 { grid-template-columns: repeat(3, 1fr); }
.cdg-adv-grid-4 { grid-template-columns: repeat(4, 1fr); }
@media (max-width: 980px) { .cdg-adv-grid-3, .cdg-adv-grid-4 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .cdg-adv-grid-3, .cdg-adv-grid-4 { grid-template-columns: 1fr; } }
.cdg-adv-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    padding: 28px 22px; transition: all 0.3s;
}
.cdg-adv-card:hover { transform: translateY(-4px); border-color: #1e40af; box-shadow: 0 12px 32px rgba(0,0,0,0.08); }
.cdg-adv-icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; margin-bottom: 14px;
}
.cdg-adv-card h3 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.cdg-adv-card p { color: #64748b; font-size: 13px; line-height: 1.6; margin: 0; }

/* FAQ */
.cdg-faq-list { display: flex; flex-direction: column; gap: 10px; }
.cdg-faq-item {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 0; overflow: hidden;
    transition: all 0.2s;
}
.cdg-faq-item[open] { border-color: #1e40af; box-shadow: 0 8px 24px rgba(30,64,175,0.10); }
.cdg-faq-item summary {
    list-style: none; cursor: pointer;
    padding: 18px 22px;
    display: flex; align-items: center; justify-content: space-between; gap: 14px;
    font-size: 15px; font-weight: 700; color: #0f172a;
}
.cdg-faq-item summary::-webkit-details-marker { display: none; }
.cdg-faq-item summary i { color: #1e40af; font-size: 16px; transition: transform 0.2s; flex-shrink: 0; }
.cdg-faq-item[open] summary i { transform: rotate(45deg); }
.cdg-faq-answer { padding: 0 22px 18px; color: #475569; font-size: 14px; line-height: 1.7; }
.cdg-faq-answer strong { color: #0f172a; }

/* FINAL CTA */
.cdg-final-cta {
    padding: 80px 0;
    background: linear-gradient(135deg, #0a1f44 0%, #1e40af 60%, #2563eb 100%);
    color: #fff;
    position: relative;
    overflow: hidden;
}
.cdg-final-cta::before {
    content: ''; position: absolute;
    top: -50%; right: -20%; width: 60%; height: 200%;
    background: radial-gradient(circle, rgba(251,191,36,0.20) 0%, transparent 60%);
    filter: blur(80px); pointer-events: none;
}
.cdg-final-cta-content { text-align: center; max-width: 720px; margin: 0 auto; position: relative; z-index: 2; }
.cdg-final-cta-content .cdg-eyebrow { background: rgba(255,255,255,0.15); color: #fde047; }
.cdg-final-cta-content h2 { font-size: clamp(28px, 4vw, 40px); font-weight: 900; line-height: 1.2; color: #fff; margin: 14px 0 14px; }
.cdg-final-cta-content p { font-size: 17px; line-height: 1.7; color: rgba(255,255,255,0.85); margin: 0 auto 28px; max-width: 540px; }
.cdg-final-cta-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
</style>
