<?php
/**
 * CODEGA Theme - Dynamic Stylesheet
 * 
 * Generated CSS with theme settings injected as CSS variables.
 * Served via /templates/website/codega/css/wisecp.css route.
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

$color1 = "#" . ltrim(Config::get("theme/color1") ?: "0a1628", "#");
$color2 = "#" . ltrim(Config::get("theme/color2") ?: "d4a574", "#");
$tcolor = "#" . ltrim(Config::get("theme/text-color") ?: "1a2238", "#");
?>
/*  ============================================================
    CODEGA Theme v1.0.0 — Premium Navy/Gold
    codega.com.tr · 2026
    ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

:root {
    /* Brand colors - injected from theme settings */
    --cg-navy:        <?= $color1 ?>;
    --cg-navy-2:      #142042;
    --cg-navy-3:      #1a2851;
    --cg-gold:        <?= $color2 ?>;
    --cg-gold-soft:   #e8c896;
    --cg-gold-deep:   #b8895a;
    --cg-text:        <?= $tcolor ?>;
    --cg-text-muted:  #5a6478;
    --cg-text-dim:    #8a92a5;

    /* Neutrals */
    --cg-cream:       #faf7f2;
    --cg-cream-2:     #f5f1ea;
    --cg-bg:          #ffffff;
    --cg-bg-soft:     #f9fafb;
    --cg-border:      #e5e7eb;
    --cg-border-soft: #f0f1f3;

    /* Status */
    --cg-success:     #10b981;
    --cg-warning:     #f59e0b;
    --cg-danger:      #ef4444;
    --cg-info:        #3b82f6;

    /* Typography */
    --cg-font-display: 'Cormorant Garamond', 'Georgia', serif;
    --cg-font-body:    'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --cg-font-mono:    'JetBrains Mono', 'Consolas', monospace;

    /* Layout */
    --cg-radius-sm: 6px;
    --cg-radius:    10px;
    --cg-radius-lg: 16px;
    --cg-radius-xl: 24px;

    --cg-shadow-sm: 0 1px 2px rgba(10, 22, 40, 0.04), 0 1px 3px rgba(10, 22, 40, 0.06);
    --cg-shadow:    0 4px 6px -1px rgba(10, 22, 40, 0.06), 0 2px 4px -1px rgba(10, 22, 40, 0.04);
    --cg-shadow-md: 0 10px 25px -5px rgba(10, 22, 40, 0.08), 0 8px 10px -6px rgba(10, 22, 40, 0.04);
    --cg-shadow-lg: 0 25px 50px -12px rgba(10, 22, 40, 0.15);
    --cg-shadow-gold: 0 8px 24px rgba(212, 165, 116, 0.25);

    --cg-transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================================
   RESET & BASE
   ============================================================ */
*, *::before, *::after { box-sizing: border-box; }
* { margin: 0; padding: 0; }

html {
    scroll-behavior: smooth;
    -webkit-text-size-adjust: 100%;
}

body {
    font-family: var(--cg-font-body);
    font-size: 15px;
    line-height: 1.6;
    color: var(--cg-text);
    background: var(--cg-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
}

img, svg { max-width: 100%; height: auto; display: block; }
a { color: var(--cg-navy); text-decoration: none; transition: color var(--cg-transition); }
a:hover { color: var(--cg-gold); }

::selection { background: var(--cg-gold); color: var(--cg-navy); }

/* ============================================================
   TYPOGRAPHY
   ============================================================ */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--cg-font-display);
    font-weight: 600;
    line-height: 1.2;
    color: var(--cg-navy);
    letter-spacing: -0.01em;
}
h1 { font-size: clamp(2.25rem, 4vw, 3.5rem); font-weight: 500; }
h2 { font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 500; }
h3 { font-size: clamp(1.375rem, 2vw, 1.75rem); }
h4 { font-size: 1.25rem; }
h5 { font-size: 1.125rem; }
h6 { font-size: 1rem; }

.cg-eyebrow {
    font-family: var(--cg-font-body);
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--cg-gold-deep);
}
.cg-display { font-family: var(--cg-font-display); font-style: italic; font-weight: 500; }
.cg-mono    { font-family: var(--cg-font-mono); font-size: 0.875em; }

/* ============================================================
   LAYOUT
   ============================================================ */
.cg-container {
    width: 100%;
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}
.cg-container-tight { max-width: 920px; margin: 0 auto; padding: 0 24px; }
.cg-section { padding: 80px 0; }
.cg-section-sm { padding: 48px 0; }

/* ============================================================
   HEADER & NAVIGATION
   ============================================================ */
.cg-header {
    background: var(--cg-navy);
    color: white;
    border-bottom: 1px solid rgba(212, 165, 116, 0.12);
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(8px);
}
.cg-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 76px;
    gap: 32px;
}
.cg-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}
.cg-logo-mark {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--cg-gold) 0%, var(--cg-gold-deep) 100%);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--cg-navy);
    font-family: var(--cg-font-display);
    font-weight: 700;
    font-size: 20px;
    box-shadow: 0 2px 8px rgba(212, 165, 116, 0.3);
}
.cg-logo-text {
    font-family: var(--cg-font-display);
    font-size: 22px;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: white;
}
.cg-logo-text span { color: var(--cg-gold); }

.cg-nav { display: flex; align-items: center; gap: 8px; }
.cg-nav-item {
    color: rgba(255, 255, 255, 0.78);
    font-size: 14px;
    font-weight: 500;
    padding: 8px 14px;
    border-radius: var(--cg-radius-sm);
    transition: all var(--cg-transition);
}
.cg-nav-item:hover {
    color: var(--cg-gold);
    background: rgba(212, 165, 116, 0.08);
}
.cg-nav-item.active { color: var(--cg-gold); }

.cg-header-actions { display: flex; align-items: center; gap: 12px; }

/* ============================================================
   BUTTONS
   ============================================================ */
.cg-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 22px;
    font-family: var(--cg-font-body);
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
    border-radius: var(--cg-radius);
    border: 1px solid transparent;
    cursor: pointer;
    transition: all var(--cg-transition);
    text-decoration: none;
    letter-spacing: 0.01em;
    white-space: nowrap;
}
.cg-btn-primary {
    background: var(--cg-gold);
    color: var(--cg-navy);
    box-shadow: var(--cg-shadow-gold);
}
.cg-btn-primary:hover {
    background: var(--cg-gold-soft);
    color: var(--cg-navy);
    transform: translateY(-1px);
    box-shadow: 0 12px 28px rgba(212, 165, 116, 0.35);
}
.cg-btn-secondary {
    background: transparent;
    color: white;
    border-color: rgba(255, 255, 255, 0.2);
}
.cg-btn-secondary:hover {
    border-color: var(--cg-gold);
    color: var(--cg-gold);
}
.cg-btn-dark {
    background: var(--cg-navy);
    color: white;
}
.cg-btn-dark:hover {
    background: var(--cg-navy-2);
    color: white;
    transform: translateY(-1px);
}
.cg-btn-ghost {
    background: transparent;
    color: var(--cg-navy);
    border-color: var(--cg-border);
}
.cg-btn-ghost:hover {
    border-color: var(--cg-navy);
    background: var(--cg-bg-soft);
}
.cg-btn-sm { padding: 8px 16px; font-size: 13px; }
.cg-btn-lg { padding: 14px 28px; font-size: 15px; }
.cg-btn-block { width: 100%; }

/* ============================================================
   HERO
   ============================================================ */
.cg-hero {
    position: relative;
    background: var(--cg-navy);
    color: white;
    padding: 96px 0 120px;
    overflow: hidden;
}
.cg-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 800px 400px at 80% -10%, rgba(212, 165, 116, 0.18), transparent 60%),
        radial-gradient(ellipse 600px 400px at 10% 110%, rgba(212, 165, 116, 0.08), transparent 60%);
    pointer-events: none;
}
.cg-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(212, 165, 116, 0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(212, 165, 116, 0.04) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
    -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
    pointer-events: none;
}
.cg-hero-inner {
    position: relative;
    z-index: 1;
    max-width: 760px;
}
.cg-hero h1 {
    color: white;
    margin: 18px 0 20px;
    font-weight: 400;
}
.cg-hero h1 em {
    color: var(--cg-gold);
    font-style: italic;
    font-weight: 500;
}
.cg-hero-lead {
    font-size: 1.125rem;
    line-height: 1.65;
    color: rgba(255, 255, 255, 0.75);
    max-width: 580px;
    margin-bottom: 36px;
}
.cg-hero-cta { display: flex; gap: 14px; flex-wrap: wrap; }

.cg-hero-stats {
    margin-top: 64px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 32px;
    padding-top: 36px;
    border-top: 1px solid rgba(212, 165, 116, 0.18);
    position: relative;
    z-index: 1;
}
.cg-hero-stat-num {
    font-family: var(--cg-font-display);
    font-size: 2.5rem;
    font-weight: 500;
    color: var(--cg-gold);
    line-height: 1;
    margin-bottom: 6px;
}
.cg-hero-stat-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
    letter-spacing: 0.04em;
}

/* ============================================================
   CARDS & GRIDS
   ============================================================ */
.cg-grid { display: grid; gap: 24px; }
.cg-grid-2 { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
.cg-grid-3 { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
.cg-grid-4 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }

.cg-card {
    background: white;
    border: 1px solid var(--cg-border);
    border-radius: var(--cg-radius-lg);
    padding: 28px;
    transition: all var(--cg-transition);
}
.cg-card:hover {
    border-color: var(--cg-gold);
    box-shadow: var(--cg-shadow-md);
    transform: translateY(-2px);
}
.cg-card-icon {
    width: 48px; height: 48px;
    background: var(--cg-cream);
    border-radius: var(--cg-radius);
    display: flex; align-items: center; justify-content: center;
    color: var(--cg-gold-deep);
    margin-bottom: 18px;
}

/* Pricing card */
.cg-pricing-card {
    background: white;
    border: 1px solid var(--cg-border);
    border-radius: var(--cg-radius-lg);
    padding: 32px 28px;
    position: relative;
    transition: all var(--cg-transition);
}
.cg-pricing-card.featured {
    border-color: var(--cg-gold);
    box-shadow: var(--cg-shadow-gold);
    background: linear-gradient(180deg, #fffdf9 0%, white 100%);
}
.cg-pricing-card .cg-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--cg-gold);
    color: var(--cg-navy);
    font-size: 11px;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 20px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.cg-pricing-name {
    font-family: var(--cg-font-display);
    font-size: 1.5rem;
    font-weight: 500;
    color: var(--cg-navy);
}
.cg-pricing-price {
    font-family: var(--cg-font-display);
    font-size: 3rem;
    font-weight: 500;
    color: var(--cg-navy);
    line-height: 1;
    margin: 18px 0 4px;
}
.cg-pricing-price .currency {
    font-size: 1.25rem;
    color: var(--cg-gold-deep);
    vertical-align: super;
    margin-right: 2px;
}
.cg-pricing-period {
    font-size: 0.875rem;
    color: var(--cg-text-muted);
    margin-bottom: 24px;
}
.cg-pricing-features {
    list-style: none;
    padding: 18px 0;
    border-top: 1px solid var(--cg-border-soft);
    margin-bottom: 22px;
}
.cg-pricing-features li {
    padding: 7px 0;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.cg-pricing-features li::before {
    content: '✓';
    color: var(--cg-gold-deep);
    font-weight: 700;
}

/* ============================================================
   FORMS
   ============================================================ */
.cg-form-group { margin-bottom: 18px; }
.cg-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--cg-text);
    margin-bottom: 6px;
    letter-spacing: 0.01em;
}
.cg-input, .cg-select, .cg-textarea {
    width: 100%;
    padding: 11px 14px;
    font-family: var(--cg-font-body);
    font-size: 14px;
    color: var(--cg-text);
    background: white;
    border: 1px solid var(--cg-border);
    border-radius: var(--cg-radius);
    transition: all var(--cg-transition);
}
.cg-input:focus, .cg-select:focus, .cg-textarea:focus {
    outline: none;
    border-color: var(--cg-gold);
    box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.15);
}
.cg-textarea { resize: vertical; min-height: 96px; }
.cg-help { font-size: 12px; color: var(--cg-text-muted); margin-top: 4px; }
.cg-error { font-size: 12px; color: var(--cg-danger); margin-top: 4px; }

/* ============================================================
   AUTH PAGES (Login / Register)
   ============================================================ */
.cg-auth {
    min-height: calc(100vh - 76px);
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--cg-bg-soft);
}
.cg-auth-side {
    background: var(--cg-navy);
    color: white;
    padding: 64px 56px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.cg-auth-side::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 600px 400px at 100% 0%, rgba(212, 165, 116, 0.15), transparent 60%);
    pointer-events: none;
}
.cg-auth-side > * { position: relative; z-index: 1; }
.cg-auth-side h2 {
    color: white;
    font-weight: 400;
    margin-top: 12px;
}
.cg-auth-side h2 em { color: var(--cg-gold); font-style: italic; }
.cg-auth-side p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    line-height: 1.65;
    margin-top: 16px;
    max-width: 440px;
}

.cg-auth-form {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 64px 32px;
}
.cg-auth-form-inner {
    width: 100%;
    max-width: 420px;
}
.cg-auth-form h1 {
    font-size: 2rem;
    margin-bottom: 8px;
}
.cg-auth-form .lead {
    color: var(--cg-text-muted);
    margin-bottom: 32px;
    font-size: 0.9375rem;
}
.cg-auth-divider {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 20px 0;
    color: var(--cg-text-dim);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.cg-auth-divider::before, .cg-auth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--cg-border);
}

/* ============================================================
   CLIENT AREA - Sidebar Layout
   ============================================================ */
.cg-client {
    display: grid;
    grid-template-columns: 260px 1fr;
    min-height: calc(100vh - 76px);
    background: var(--cg-bg-soft);
}
.cg-sidebar {
    background: white;
    border-right: 1px solid var(--cg-border);
    padding: 28px 0;
    position: sticky;
    top: 76px;
    height: calc(100vh - 76px);
    overflow-y: auto;
}
.cg-sidebar-section {
    padding: 0 24px;
    margin-bottom: 22px;
}
.cg-sidebar-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--cg-text-dim);
    margin-bottom: 8px;
}
.cg-sidebar-link {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 14px;
    color: var(--cg-text);
    font-size: 14px;
    font-weight: 500;
    border-radius: var(--cg-radius-sm);
    transition: all var(--cg-transition);
    text-decoration: none;
}
.cg-sidebar-link:hover {
    background: var(--cg-cream);
    color: var(--cg-navy);
}
.cg-sidebar-link.active {
    background: var(--cg-navy);
    color: white;
}
.cg-sidebar-link svg { flex-shrink: 0; opacity: 0.7; }
.cg-sidebar-link.active svg { opacity: 1; color: var(--cg-gold); }

.cg-content {
    padding: 36px 40px;
    max-width: 100%;
    overflow-x: auto;
}

/* Page header inside client area */
.cg-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}
.cg-page-header h1 {
    font-size: 2rem;
    margin-bottom: 4px;
    font-weight: 500;
}
.cg-page-header p {
    color: var(--cg-text-muted);
    font-size: 0.9375rem;
}

/* Stat cards */
.cg-stat-card {
    background: white;
    border: 1px solid var(--cg-border);
    border-radius: var(--cg-radius-lg);
    padding: 22px;
    transition: all var(--cg-transition);
}
.cg-stat-card:hover { border-color: var(--cg-gold-soft); box-shadow: var(--cg-shadow-sm); }
.cg-stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--cg-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 10px;
}
.cg-stat-value {
    font-family: var(--cg-font-display);
    font-size: 2rem;
    font-weight: 500;
    color: var(--cg-navy);
    line-height: 1;
}
.cg-stat-meta {
    margin-top: 8px;
    font-size: 13px;
    color: var(--cg-text-muted);
}
.cg-stat-meta.up { color: var(--cg-success); }
.cg-stat-meta.down { color: var(--cg-danger); }

/* ============================================================
   TABLES
   ============================================================ */
.cg-table-wrap {
    background: white;
    border: 1px solid var(--cg-border);
    border-radius: var(--cg-radius-lg);
    overflow: hidden;
}
.cg-table {
    width: 100%;
    border-collapse: collapse;
}
.cg-table th {
    text-align: left;
    padding: 14px 20px;
    background: var(--cg-bg-soft);
    border-bottom: 1px solid var(--cg-border);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--cg-text-muted);
}
.cg-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--cg-border-soft);
    font-size: 14px;
    vertical-align: middle;
}
.cg-table tr:last-child td { border-bottom: none; }
.cg-table tr:hover td { background: var(--cg-bg-soft); }

/* ============================================================
   STATUS PILLS
   ============================================================ */
.cg-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 11px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 20px;
    letter-spacing: 0.02em;
}
.cg-pill::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
}
.cg-pill-active   { color: var(--cg-success); background: rgba(16, 185, 129, 0.1); }
.cg-pill-pending  { color: var(--cg-warning); background: rgba(245, 158, 11, 0.1); }
.cg-pill-paid     { color: var(--cg-success); background: rgba(16, 185, 129, 0.1); }
.cg-pill-unpaid   { color: var(--cg-danger);  background: rgba(239, 68, 68, 0.1); }
.cg-pill-cancelled{ color: var(--cg-text-muted); background: rgba(90, 100, 120, 0.1); }

/* ============================================================
   ALERTS
   ============================================================ */
.cg-alert {
    display: flex;
    gap: 12px;
    padding: 14px 18px;
    border-radius: var(--cg-radius);
    font-size: 14px;
    border: 1px solid transparent;
    margin-bottom: 16px;
}
.cg-alert-info    { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.cg-alert-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
.cg-alert-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.cg-alert-danger  { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.cg-alert-gold    { background: var(--cg-cream); color: var(--cg-navy); border-color: var(--cg-gold-soft); }

/* ============================================================
   FOOTER
   ============================================================ */
.cg-footer {
    background: var(--cg-navy);
    color: rgba(255, 255, 255, 0.7);
    padding: 64px 0 32px;
    margin-top: auto;
}
.cg-footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 48px;
    margin-bottom: 48px;
}
.cg-footer h4 {
    color: white;
    font-family: var(--cg-font-body);
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin-bottom: 18px;
}
.cg-footer-link {
    display: block;
    padding: 5px 0;
    color: rgba(255, 255, 255, 0.65);
    font-size: 14px;
    text-decoration: none;
    transition: color var(--cg-transition);
}
.cg-footer-link:hover { color: var(--cg-gold); }
.cg-footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 28px;
    border-top: 1px solid rgba(212, 165, 116, 0.15);
    font-size: 13px;
    flex-wrap: wrap;
    gap: 12px;
}
.cg-footer-bottom a { color: var(--cg-gold); }

/* ============================================================
   UTILITIES
   ============================================================ */
.cg-text-center { text-align: center; }
.cg-text-muted  { color: var(--cg-text-muted); }
.cg-text-gold   { color: var(--cg-gold-deep); }
.cg-mt-0  { margin-top: 0; }
.cg-mt-1  { margin-top: 8px; }
.cg-mt-2  { margin-top: 16px; }
.cg-mt-3  { margin-top: 24px; }
.cg-mt-4  { margin-top: 32px; }
.cg-mb-0  { margin-bottom: 0; }
.cg-mb-2  { margin-bottom: 16px; }
.cg-mb-3  { margin-bottom: 24px; }
.cg-mb-4  { margin-bottom: 32px; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 992px) {
    .cg-footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
    .cg-auth { grid-template-columns: 1fr; }
    .cg-auth-side { display: none; }
    .cg-client { grid-template-columns: 1fr; }
    .cg-sidebar {
        position: fixed;
        top: 76px;
        left: -260px;
        z-index: 50;
        transition: left var(--cg-transition);
        box-shadow: var(--cg-shadow-lg);
    }
    .cg-sidebar.open { left: 0; }
}

@media (max-width: 768px) {
    .cg-header-inner { height: 64px; gap: 16px; }
    .cg-nav { display: none; }
    .cg-section { padding: 56px 0; }
    .cg-hero { padding: 64px 0 80px; }
    .cg-hero-stats { grid-template-columns: 1fr 1fr; gap: 24px; }
    .cg-content { padding: 24px 20px; }
    .cg-footer-grid { grid-template-columns: 1fr; gap: 32px; }
    .cg-footer-bottom { flex-direction: column; text-align: center; }
    .cg-table th:nth-child(n+4), .cg-table td:nth-child(n+4) { display: none; }
}

@media (max-width: 480px) {
    .cg-container, .cg-container-tight { padding: 0 16px; }
    .cg-hero h1 { font-size: 2rem; }
    .cg-hero-cta { flex-direction: column; }
    .cg-hero-cta .cg-btn { width: 100%; }
    .cg-hero-stats { grid-template-columns: 1fr; }
    .cg-card, .cg-pricing-card { padding: 22px 18px; }
    .cg-content { padding: 20px 14px; }
}

/* ============================================================
   ANIMATIONS
   ============================================================ */
@keyframes cg-fade-up {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.cg-fade-up { animation: cg-fade-up 600ms cubic-bezier(0.4, 0, 0.2, 1) both; }

@keyframes cg-pulse-gold {
    0%, 100% { box-shadow: 0 0 0 0 rgba(212, 165, 116, 0.4); }
    50%      { box-shadow: 0 0 0 12px rgba(212, 165, 116, 0); }
}
.cg-pulse { animation: cg-pulse-gold 2s ease-in-out infinite; }

/* Print */
@media print {
    .cg-header, .cg-footer, .cg-sidebar, .cg-btn { display: none !important; }
    body { background: white; }
}
