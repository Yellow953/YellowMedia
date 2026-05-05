<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YellowMedia — AI-Powered Media Platform</title>
    <meta name="description" content="AI-generated social media visuals and Meta Ads intelligence for agencies and marketing teams.">
    <link rel="icon" type="image/png" href="/assets/images/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --y:      #F5C300;
            --y-bg:   rgba(245,195,0,.08);
            --y-ring: rgba(245,195,0,.25);
            --ink:    #0D0D0D;
            --muted:  #6B7280;
            --border: #E5E7EB;
            --surface: #F9FAFB;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff;
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        /* ── NAV ──────────────────────────────────────── */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            height: 68px;
            display: flex;
            align-items: center;
            padding: 0 3rem;
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .nav-logo { margin-right: auto; }
        .nav-logo img { height: 26px; width: auto; }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-right: 2rem;
        }
        .nav-links a {
            font-size: .875rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            transition: color .15s;
        }
        .nav-links a:hover { color: var(--ink); }
        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--ink);
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            padding: .525rem 1.125rem;
            border-radius: 8px;
            transition: opacity .15s, transform .1s;
        }
        .nav-cta:hover { opacity: .82; color: #fff; transform: translateY(-1px); }

        /* ── HERO ─────────────────────────────────────── */
        .hero {
            padding: 7rem 3rem 0;
            max-width: 1200px;
            margin: 0 auto;
        }
        .hero-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            padding-bottom: 5rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--y-bg);
            border: 1px solid var(--y-ring);
            color: #7a5c00;
            font-size: .75rem;
            font-weight: 600;
            padding: .3rem .875rem;
            border-radius: 20px;
            margin-bottom: 1.5rem;
        }
        .hero-badge i { color: var(--y); }

        .hero-title {
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -.04em;
            color: var(--ink);
            margin-bottom: 1.25rem;
        }
        .hero-title .hl {
            background: linear-gradient(transparent 60%, rgba(245,195,0,.4) 60%);
        }

        .hero-sub {
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 2.25rem;
            max-width: 420px;
        }

        .hero-btns {
            display: flex;
            align-items: center;
            gap: .875rem;
            flex-wrap: wrap;
        }
        .btn-y {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: var(--y);
            color: var(--ink);
            font-size: .9375rem;
            font-weight: 700;
            text-decoration: none;
            padding: .75rem 1.625rem;
            border-radius: 9px;
            transition: background .15s, box-shadow .15s, transform .1s;
        }
        .btn-y:hover {
            background: #E8B800;
            color: var(--ink);
            box-shadow: 0 4px 20px rgba(245,195,0,.4);
            transform: translateY(-1px);
        }
        .hero-invite {
            font-size: .8rem;
            font-weight: 500;
            color: #9CA3AF;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        /* ── HERO VISUAL ──────────────────────────────── */
        .hero-visual {
            position: relative;
        }

        /* Main dashboard card */
        .dash-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 24px 60px rgba(0,0,0,.09), 0 4px 12px rgba(0,0,0,.05);
            position: relative;
            z-index: 2;
        }
        .dash-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .dash-header-title {
            font-size: .75rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -.01em;
        }
        .dash-badge-live {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .65rem;
            font-weight: 600;
            color: #16a34a;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: .2rem .5rem;
            border-radius: 20px;
        }
        .live-dot {
            width: 6px; height: 6px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .5; transform: scale(.8); }
        }

        /* Metric row */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .75rem;
            margin-bottom: 1.25rem;
        }
        .metric {
            background: var(--surface);
            border-radius: 10px;
            padding: .75rem .875rem;
        }
        .metric-l {
            font-size: .6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
            margin-bottom: .25rem;
        }
        .metric-n {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--ink);
            line-height: 1;
        }
        .metric-d {
            font-size: .65rem;
            font-weight: 600;
            color: #16a34a;
            margin-top: .2rem;
        }
        .metric-d.warn { color: #ea580c; }

        /* AI Suggestion card */
        .ai-pill {
            display: flex;
            align-items: flex-start;
            gap: .625rem;
            background: linear-gradient(135deg, #fffbeb, #fef9c3);
            border: 1px solid rgba(245,195,0,.35);
            border-radius: 10px;
            padding: .75rem .875rem;
            margin-bottom: 1rem;
        }
        .ai-pill-icon {
            width: 26px; height: 26px;
            background: var(--y);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: .7rem;
            color: var(--ink);
        }
        .ai-pill-text {
            font-size: .75rem;
            font-weight: 500;
            color: #78350f;
            line-height: 1.45;
        }
        .ai-pill-text strong { font-weight: 700; display: block; margin-bottom: .1rem; }

        /* Mini chart bars */
        .mini-chart {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 40px;
        }
        .bar {
            flex: 1;
            background: #E5E7EB;
            border-radius: 3px 3px 0 0;
            transition: background .2s;
        }
        .bar.active { background: var(--y); }

        /* Floating generate card */
        .float-card {
            position: absolute;
            bottom: -1.5rem;
            left: -2rem;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.125rem;
            box-shadow: 0 12px 40px rgba(0,0,0,.1);
            width: 200px;
            z-index: 3;
        }
        .float-card-label {
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted);
            margin-bottom: .5rem;
        }
        .float-img {
            width: 100%;
            height: 72px;
            border-radius: 8px;
            background: linear-gradient(135deg, #1e1b4b 0%, #1e3a5f 40%, #1a2a1a 100%);
            position: relative;
            overflow: hidden;
            margin-bottom: .625rem;
        }
        .float-img::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 40% 60%, rgba(245,195,0,.3) 0%, transparent 55%),
                        radial-gradient(ellipse at 75% 30%, rgba(99,179,237,.2) 0%, transparent 45%);
        }
        .float-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .float-status span {
            font-size: .65rem;
            font-weight: 500;
            color: var(--muted);
        }
        .float-done {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            font-size: .6rem;
            font-weight: 600;
            padding: .15rem .4rem;
            border-radius: 4px;
        }

        /* ── LOGOS / TRUST ────────────────────────────── */
        .trust {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
        }
        .trust-label {
            font-size: .75rem;
            font-weight: 600;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: .07em;
        }
        .trust-items {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            flex-wrap: wrap;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8125rem;
            font-weight: 600;
            color: #9CA3AF;
        }
        .trust-item i { font-size: 1rem; }

        /* ── FEATURES ─────────────────────────────────── */
        .features {
            padding: 6rem 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-eyebrow {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .75rem;
        }
        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1.15;
            color: var(--ink);
            max-width: 480px;
            margin-bottom: 3.5rem;
        }
        .section-title .hl { color: var(--y); }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        /* Feature card with illustration area */
        .fc {
            border: 1.5px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            transition: border-color .2s, box-shadow .2s, transform .2s;
        }
        .fc:hover {
            border-color: rgba(245,195,0,.5);
            box-shadow: 0 12px 36px rgba(0,0,0,.07);
            transform: translateY(-3px);
        }
        .fc-illus {
            height: 160px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Feature 1: Image gen — dark with gradient blob */
        .fc-illus-gen {
            background: #0f0f1a;
        }
        .fc-illus-gen::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 30% 60%, rgba(245,195,0,.25) 0%, transparent 55%),
                        radial-gradient(ellipse at 80% 30%, rgba(99,102,241,.2) 0%, transparent 50%);
        }
        .fc-illus-gen-ui {
            position: relative;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 10px;
            padding: .75rem 1rem;
            width: 75%;
        }
        .fc-illus-gen-label {
            font-size: .55rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: rgba(255,255,255,.3);
            margin-bottom: .3rem;
        }
        .fc-illus-gen-prompt {
            font-size: .65rem;
            color: rgba(255,255,255,.55);
            margin-bottom: .5rem;
            line-height: 1.4;
        }
        .fc-illus-gen-btn {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            background: var(--y);
            color: var(--ink);
            font-size: .6rem;
            font-weight: 700;
            padding: .25rem .625rem;
            border-radius: 5px;
        }

        /* Feature 2: Ads Monitor — chart */
        .fc-illus-ads {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem .5rem;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-end;
        }
        .fc-chart-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .375rem;
        }
        .fc-chart-label { font-size: .6rem; font-weight: 600; color: var(--muted); }
        .fc-chart-val { font-size: .6rem; font-weight: 700; color: var(--ink); }
        .fc-chart-bar-wrap {
            flex: 1;
            height: 6px;
            background: #F3F4F6;
            border-radius: 3px;
            margin: 0 .625rem;
            overflow: hidden;
        }
        .fc-chart-bar-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--y);
        }
        .fc-chart-nums {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: .5rem;
            margin-top: .75rem;
            padding-top: .75rem;
            border-top: 1px solid var(--border);
        }
        .fc-num-item { text-align: center; }
        .fc-num { font-size: .875rem; font-weight: 800; letter-spacing: -.02em; color: var(--ink); }
        .fc-num-l { font-size: .55rem; font-weight: 500; color: var(--muted); margin-top: .1rem; }

        /* Feature 3: Campaign Builder — steps */
        .fc-illus-build {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            flex-direction: column;
            align-items: stretch;
            padding: 1rem 1.25rem;
            justify-content: center;
            gap: .5rem;
        }
        .fc-step {
            display: flex;
            align-items: center;
            gap: .625rem;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .5rem .75rem;
        }
        .fc-step-num {
            width: 20px; height: 20px;
            background: var(--ink);
            color: #fff;
            font-size: .6rem;
            font-weight: 700;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .fc-step-num.done {
            background: var(--y);
            color: var(--ink);
        }
        .fc-step-text {
            font-size: .65rem;
            font-weight: 600;
            color: var(--ink);
        }
        .fc-step-check {
            margin-left: auto;
            color: #22c55e;
            font-size: .75rem;
        }

        .fc-body {
            padding: 1.375rem 1.375rem 1.5rem;
        }
        .fc-title {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -.02em;
            color: var(--ink);
            margin-bottom: .5rem;
        }
        .fc-desc {
            font-size: .875rem;
            font-weight: 400;
            line-height: 1.6;
            color: var(--muted);
        }
        .fc-tag {
            display: inline-block;
            margin-top: 1rem;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #9CA3AF;
            background: var(--surface);
            padding: .25rem .625rem;
            border-radius: 5px;
        }

        /* ── HOW IT WORKS ─────────────────────────────── */
        .hiw {
            background: var(--ink);
            padding: 6rem 3rem;
        }
        .hiw-inner {
            max-width: 1200px;
            margin: 0 auto;
        }
        .hiw .section-eyebrow { color: rgba(255,255,255,.35); }
        .hiw .section-title { color: #fff; max-width: 400px; }
        .hiw .section-title .hl { color: var(--y); }

        .hiw-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 3.5rem;
        }
        .hiw-step {
            position: relative;
            padding-top: 1rem;
        }
        .hiw-num {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -.04em;
            color: rgba(255,255,255,.06);
            line-height: 1;
            margin-bottom: .75rem;
        }
        .hiw-icon {
            width: 44px; height: 44px;
            background: rgba(245,195,0,.12);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: var(--y);
            font-size: 1.1rem;
            margin-bottom: 1.125rem;
        }
        .hiw-title {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -.02em;
            color: #fff;
            margin-bottom: .5rem;
        }
        .hiw-desc {
            font-size: .875rem;
            line-height: 1.65;
            color: rgba(255,255,255,.4);
        }
        .hiw-connector {
            position: absolute;
            top: 2.75rem;
            right: -1rem;
            width: 2rem;
            height: 1px;
            background: rgba(255,255,255,.1);
        }
        .hiw-connector::after {
            content: '';
            position: absolute;
            right: 0; top: -3px;
            width: 6px; height: 6px;
            border-top: 1px solid rgba(255,255,255,.15);
            border-right: 1px solid rgba(255,255,255,.15);
            transform: rotate(45deg);
        }

        /* ── CTA ──────────────────────────────────────── */
        .cta-wrap {
            padding: 5rem 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .cta {
            background: var(--y);
            border-radius: 20px;
            padding: 4.5rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .cta::before {
            content: '';
            position: absolute;
            top: -80px; left: 50%;
            transform: translateX(-50%);
            width: 600px; height: 300px;
            background: radial-gradient(ellipse, rgba(255,255,255,.25) 0%, transparent 65%);
        }
        .cta > * { position: relative; }
        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1.1;
            color: var(--ink);
            margin-bottom: .75rem;
        }
        .cta-sub {
            font-size: 1rem;
            color: rgba(10,10,10,.55);
            margin-bottom: 2rem;
        }
        .btn-dark {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: var(--ink);
            color: #fff;
            font-size: .9375rem;
            font-weight: 700;
            text-decoration: none;
            padding: .8rem 1.875rem;
            border-radius: 9px;
            transition: opacity .15s, transform .1s;
        }
        .btn-dark:hover { opacity: .85; color: #fff; transform: translateY(-1px); }

        /* ── FOOTER ───────────────────────────────────── */
        .site-footer {
            background: #0A0A0A;
            padding: 4rem 3rem 2rem;
        }
        .footer-top {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-brand img { height: 24px; width: auto; margin-bottom: 1rem; opacity: .7; }
        .footer-brand p {
            font-size: .8125rem;
            line-height: 1.65;
            color: rgba(255,255,255,.3);
            max-width: 240px;
        }
        .footer-col-title {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: rgba(255,255,255,.25);
            margin-bottom: 1rem;
        }
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: .625rem;
        }
        .footer-links a {
            font-size: .8125rem;
            font-weight: 500;
            color: rgba(255,255,255,.4);
            text-decoration: none;
            transition: color .15s;
        }
        .footer-links a:hover { color: #fff; }
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            padding-top: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .footer-copy {
            font-size: .8rem;
            color: rgba(255,255,255,.2);
        }
        .footer-made {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8rem;
            color: rgba(255,255,255,.2);
        }
        .footer-made i { color: var(--y); font-size: .7rem; }

        /* ── ANIMATIONS ───────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: none; }
        }
        .hero-badge  { animation: fadeUp .4s .05s ease both; }
        .hero-title  { animation: fadeUp .4s .12s ease both; }
        .hero-sub    { animation: fadeUp .4s .2s  ease both; }
        .hero-btns   { animation: fadeUp .4s .27s ease both; }
        .hero-visual { animation: fadeUp .5s .2s  ease both; }

        .reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .5s ease, transform .5s ease;
        }
        .reveal.in { opacity: 1; transform: none; }
        .d1 { transition-delay: .08s; }
        .d2 { transition-delay: .16s; }

        /* ── RESPONSIVE ───────────────────────────────── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; }
            .hero-visual { display: none; }
            .feature-grid { grid-template-columns: 1fr 1fr; }
            .hiw-grid { grid-template-columns: 1fr; }
            .hiw-connector { display: none; }
            .footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 700px) {
            .nav { padding: 0 1.25rem; }
            .nav-links { display: none; }
            .hero { padding: 6rem 1.25rem 0; }
            .hero-title { font-size: 2.25rem; }
            .features, .cta-wrap { padding: 4rem 1.25rem; }
            .hiw { padding: 4rem 1.25rem; }
            .feature-grid { grid-template-columns: 1fr; }
            .site-footer { padding: 3rem 1.25rem 1.5rem; }
            .footer-top { grid-template-columns: 1fr; gap: 2rem; }
            .footer-bottom { flex-direction: column; gap: .75rem; text-align: center; }
            .trust { padding: 1.25rem; gap: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- ── NAV ──────────────────────────────────────────────── -->
<nav class="nav">
    <div class="nav-logo">
        <img src="/assets/images/logo.png" alt="YellowMedia">
    </div>
    <div class="nav-links">
        <a href="#features">Features</a>
        <a href="#how-it-works">How it works</a>
        <a href="#platform">Platform</a>
    </div>
    <a href="/login" class="nav-cta">
        Sign In <i class="bi bi-arrow-right"></i>
    </a>
</nav>

<!-- ── HERO ─────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-inner">
        <!-- Text -->
        <div>
            <div class="hero-badge">
                <i class="bi bi-stars"></i> AI-Powered Media Platform
            </div>
            <h1 class="hero-title">
                Smarter ads and<br><span class="hl">faster content</span><br>for modern teams.
            </h1>
            <p class="hero-sub">
                Generate AI visuals, monitor Meta Ads performance,
                and launch campaigns — all from one dashboard.
            </p>
            <div class="hero-btns">
                <a href="/login" class="btn-y">
                    Sign In to Dashboard <i class="bi bi-arrow-right"></i>
                </a>
                <span class="hero-invite">
                    <i class="bi bi-lock" style="font-size:.7rem"></i>
                    Invite-only platform
                </span>
            </div>
        </div>

        <!-- Visual -->
        <div class="hero-visual">
            <div class="dash-card">
                <div class="dash-header">
                    <span class="dash-header-title">Campaign Overview</span>
                    <span class="dash-badge-live">
                        <span class="live-dot"></span> Live
                    </span>
                </div>

                <div class="metrics-row">
                    <div class="metric">
                        <div class="metric-l">Spend</div>
                        <div class="metric-n">$1,240</div>
                        <div class="metric-d">↑ 8% today</div>
                    </div>
                    <div class="metric">
                        <div class="metric-l">ROAS</div>
                        <div class="metric-n">4.2×</div>
                        <div class="metric-d">↑ 0.4 pts</div>
                    </div>
                    <div class="metric">
                        <div class="metric-l">CTR</div>
                        <div class="metric-n">3.8%</div>
                        <div class="metric-d warn">↓ 0.2%</div>
                    </div>
                </div>

                <div class="ai-pill">
                    <div class="ai-pill-icon"><i class="bi bi-lightbulb-fill"></i></div>
                    <div class="ai-pill-text">
                        <strong>AI Suggestion — High Priority</strong>
                        Increase budget on "Summer Sale" ad set — it's outperforming by 2.1× ROAS.
                    </div>
                </div>

                <div class="mini-chart">
                    <div class="bar" style="height:35%"></div>
                    <div class="bar" style="height:55%"></div>
                    <div class="bar" style="height:45%"></div>
                    <div class="bar active" style="height:80%"></div>
                    <div class="bar" style="height:60%"></div>
                    <div class="bar active" style="height:90%"></div>
                    <div class="bar" style="height:70%"></div>
                    <div class="bar active" style="height:100%"></div>
                </div>
            </div>

            <!-- Floating image gen card -->
            <div class="float-card">
                <div class="float-card-label">Media Studio</div>
                <div class="float-img"></div>
                <div class="float-status">
                    <span>post_summer.png</span>
                    <span class="float-done">
                        <i class="bi bi-check-circle-fill" style="font-size:.55rem"></i> 3.8s
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── TRUST BAR ─────────────────────────────────────────── -->
<div class="trust">
    <span class="trust-label">Built on</span>
    <div class="trust-items">
        <span class="trust-item"><i class="bi bi-google" style="color:#4285F4"></i> Gemini AI</span>
        <span class="trust-item"><i class="bi bi-meta" style="color:#0082FB"></i> Meta Marketing API v19</span>
        <span class="trust-item"><i class="bi bi-shield-check" style="color:#22c55e"></i> Multi-Tenant Architecture</span>
        <span class="trust-item"><i class="bi bi-database" style="color:#8b5cf6"></i> Queue-Powered Generation</span>
    </div>
</div>

<!-- ── FEATURES ──────────────────────────────────────────── -->
<section class="features" id="features">
    <p class="section-eyebrow reveal">Platform features</p>
    <h2 class="section-title reveal">Three tools.<br>One <span class="hl">dashboard.</span></h2>

    <div class="feature-grid" id="platform">
        <!-- Card 1: AI Image Generation -->
        <div class="fc reveal">
            <div class="fc-illus fc-illus-gen">
                <div class="fc-illus-gen-ui">
                    <div class="fc-illus-gen-label">Describe your image</div>
                    <div class="fc-illus-gen-prompt">Modern café, warm lighting, social media post...</div>
                    <div class="fc-illus-gen-btn">
                        <i class="bi bi-magic"></i> Generate
                    </div>
                </div>
            </div>
            <div class="fc-body">
                <div class="fc-title">AI Image Generation</div>
                <p class="fc-desc">Generate social posts, stories, and banners in seconds using Gemini AI. Brand-aware prompts with Arabic text support and instant download.</p>
                <span class="fc-tag">Media Studio</span>
            </div>
        </div>

        <!-- Card 2: Meta Ads Monitor -->
        <div class="fc reveal d1">
            <div class="fc-illus fc-illus-ads">
                <div style="width:100%">
                    <div class="fc-chart-row">
                        <span class="fc-chart-label">ROAS</span>
                        <div class="fc-chart-bar-wrap"><div class="fc-chart-bar-fill" style="width:84%"></div></div>
                        <span class="fc-chart-val">4.2×</span>
                    </div>
                    <div class="fc-chart-row">
                        <span class="fc-chart-label">CTR</span>
                        <div class="fc-chart-bar-wrap"><div class="fc-chart-bar-fill" style="width:60%"></div></div>
                        <span class="fc-chart-val">3.8%</span>
                    </div>
                    <div class="fc-chart-row">
                        <span class="fc-chart-label">CPC</span>
                        <div class="fc-chart-bar-wrap"><div class="fc-chart-bar-fill" style="width:42%"></div></div>
                        <span class="fc-chart-val">$0.74</span>
                    </div>
                    <div class="fc-chart-nums">
                        <div class="fc-num-item"><div class="fc-num">$8.4k</div><div class="fc-num-l">Spend</div></div>
                        <div class="fc-num-item"><div class="fc-num">241k</div><div class="fc-num-l">Reach</div></div>
                        <div class="fc-num-item"><div class="fc-num">9.1k</div><div class="fc-num-l">Clicks</div></div>
                        <div class="fc-num-item"><div class="fc-num">$34k</div><div class="fc-num-l">Revenue</div></div>
                    </div>
                </div>
            </div>
            <div class="fc-body">
                <div class="fc-title">Meta Ads Monitor</div>
                <p class="fc-desc">Live campaign metrics — CTR, ROAS, CPC — synced every 6 hours with AI-generated optimization suggestions for every campaign.</p>
                <span class="fc-tag">Ads Dashboard</span>
            </div>
        </div>

        <!-- Card 3: Campaign Builder -->
        <div class="fc reveal d2">
            <div class="fc-illus fc-illus-build">
                <div class="fc-step">
                    <div class="fc-step-num done"><i class="bi bi-check" style="font-size:.6rem"></i></div>
                    <span class="fc-step-text">Campaign & objective</span>
                    <i class="bi bi-check-circle-fill fc-step-check"></i>
                </div>
                <div class="fc-step">
                    <div class="fc-step-num done"><i class="bi bi-check" style="font-size:.6rem"></i></div>
                    <span class="fc-step-text">Audience & budget</span>
                    <i class="bi bi-check-circle-fill fc-step-check"></i>
                </div>
                <div class="fc-step">
                    <div class="fc-step-num" style="background:var(--y);color:var(--ink)">3</div>
                    <span class="fc-step-text">Ad creative & copy</span>
                </div>
            </div>
            <div class="fc-body">
                <div class="fc-title">Campaign Builder</div>
                <p class="fc-desc">Build complete Meta campaigns end-to-end — creative, targeting, copy — with AI-written captions and launch directly from your dashboard.</p>
                <span class="fc-tag">Campaign Builder</span>
            </div>
        </div>
    </div>
</section>

<!-- ── HOW IT WORKS ───────────────────────────────────────── -->
<section class="hiw" id="how-it-works">
    <div class="hiw-inner">
        <p class="section-eyebrow reveal">How it works</p>
        <h2 class="section-title reveal">From idea to <span class="hl">live campaign</span> in minutes.</h2>

        <div class="hiw-grid">
            <div class="hiw-step reveal">
                <div class="hiw-num">01</div>
                <div class="hiw-icon"><i class="bi bi-plug"></i></div>
                <div class="hiw-title">Connect your Meta account</div>
                <p class="hiw-desc">Link your Meta Ads account via OAuth. Your campaigns sync automatically every 6 hours with full metrics.</p>
                <div class="hiw-connector"></div>
            </div>
            <div class="hiw-step reveal d1">
                <div class="hiw-num">02</div>
                <div class="hiw-icon"><i class="bi bi-magic"></i></div>
                <div class="hiw-title">Generate AI visuals</div>
                <p class="hiw-desc">Describe your content in plain language. Gemini AI generates brand-ready social media images in under 4 seconds.</p>
                <div class="hiw-connector"></div>
            </div>
            <div class="hiw-step reveal d2">
                <div class="hiw-num">03</div>
                <div class="hiw-icon"><i class="bi bi-rocket-takeoff"></i></div>
                <div class="hiw-title">Build & launch campaigns</div>
                <p class="hiw-desc">Use the Campaign Builder to create a full Meta campaign with AI-generated copy and publish it directly from your dashboard.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────── -->
<div class="cta-wrap">
    <div class="cta">
        <h2 class="cta-title reveal">Ready to create<br>smarter campaigns?</h2>
        <p class="cta-sub reveal">Sign in to your YellowMedia account and get started.</p>
        <a href="/login" class="btn-dark reveal">
            Go to Dashboard <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>

<!-- ── FOOTER ────────────────────────────────────────────── -->
<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <img src="/assets/images/logo.png" alt="YellowMedia">
            <p>AI-powered media creation and Meta Ads intelligence for agencies and modern marketing teams.</p>
        </div>
        <div>
            <div class="footer-col-title">Platform</div>
            <div class="footer-links">
                <a href="#features">Media Studio</a>
                <a href="#features">Ads Monitor</a>
                <a href="#features">Campaign Builder</a>
                <a href="#how-it-works">How it works</a>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Product</div>
            <div class="footer-links">
                <a href="#features">Features</a>
                <a href="#platform">AI Generation</a>
                <a href="#platform">Meta Integration</a>
                <a href="#platform">Analytics</a>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Account</div>
            <div class="footer-links">
                <a href="/login">Sign In</a>
                <a href="/password/reset">Reset Password</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <span class="footer-copy">© {{ date('Y') }} YellowMedia. All rights reserved.</span>
        <span class="footer-made">
            <i class="bi bi-stars"></i> Powered by Gemini AI & Meta Marketing API
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const io = new IntersectionObserver(
        entries => entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
        }),
        { threshold: 0.1 }
    );
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
</script>
</body>
</html>
