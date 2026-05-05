<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — YellowMedia</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --y:   #F5C300;
            --ink: #0A0A0A;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ────────────────────────────────── */
        .auth-left {
            background: var(--ink);
            border-left: 5px solid var(--y);
            display: flex;
            flex-direction: column;
            padding: 2.25rem 2.25rem 2.25rem 2rem;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* subtle scanline texture */
        .auth-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 3px,
                rgba(255,255,255,.015) 3px,
                rgba(255,255,255,.015) 4px
            );
            pointer-events: none;
        }

        .auth-left > * { position: relative; z-index: 1; }

        /* logo */
        .left-logo img { width: 140px; height: auto; }

        /* headline block */
        .left-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1.5rem 0;
        }
        .left-eyebrow {
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--y);
            margin-bottom: .875rem;
        }
        .left-headline {
            font-size: 2.375rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.03em;
            color: #fff;
            margin-bottom: 1rem;
        }
        .left-headline span { color: var(--y); }
        .left-tagline {
            font-size: .875rem;
            font-weight: 400;
            color: rgba(255,255,255,.38);
            line-height: 1.6;
        }

        /* feature rows */
        .feature-rows {
            display: flex;
            flex-direction: column;
            gap: 0;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .feature-row {
            display: flex;
            align-items: flex-start;
            gap: .875rem;
            padding: .875rem 0;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .feature-icon {
            width: 30px;
            height: 30px;
            background: rgba(245,195,0,.1);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--y);
            font-size: .8rem;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .feature-text strong {
            display: block;
            font-size: .8125rem;
            font-weight: 600;
            color: rgba(255,255,255,.85);
            margin-bottom: .1rem;
        }
        .feature-text span {
            font-size: .75rem;
            color: rgba(255,255,255,.35);
            line-height: 1.4;
        }

        /* ── RIGHT PANEL ───────────────────────────────── */
        .auth-right {
            background: #F9F9F7;
            display: flex;
            flex-direction: column;
        }
        .auth-right-top-bar {
            height: 4px;
            background: var(--y);
            flex-shrink: 0;
        }
        .auth-right-inner {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
        }
        .auth-form-wrap { width: 100%; max-width: 360px; }
        .auth-footer {
            text-align: center;
            font-size: .7rem;
            color: #BFC3CC;
            padding: 1.25rem;
            flex-shrink: 0;
        }

        /* ── FORM ELEMENTS ─────────────────────────────── */
        .form-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--ink);
            margin-bottom: .25rem;
        }
        .form-sub {
            font-size: .875rem;
            color: #6B7280;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .field-group { margin-bottom: 1.25rem; }
        .field-label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #374151;
            margin-bottom: .5rem;
        }
        .field-inner { position: relative; }
        .field-icon {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #B0B7C3;
            font-size: .875rem;
            pointer-events: none;
            z-index: 1;
        }
        .field-input {
            width: 100%;
            padding: .72rem .875rem .72rem 2.45rem;
            font-size: .9rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 500;
            color: var(--ink);
            background: #fff;
            border: 1.5px solid #E5E7EB;
            border-radius: 9px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            -webkit-appearance: none;
        }
        .field-input::placeholder { color: #C4C9D4; font-weight: 400; }
        .field-input:focus {
            border-color: var(--y);
            box-shadow: 0 0 0 3.5px rgba(245,195,0,.18);
        }
        .field-input.is-error { border-color: #EF4444; }
        .field-input.is-error:focus { box-shadow: 0 0 0 3.5px rgba(239,68,68,.12); }

        .toggle-pass {
            position: absolute;
            right: .875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #B0B7C3;
            font-size: .875rem;
            line-height: 1;
            z-index: 1;
        }
        .toggle-pass:hover { color: #374151; }

        .row-split {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .check-label {
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
        }
        .check-box {
            width: 16px; height: 16px;
            border: 1.5px solid #D1D5DB;
            border-radius: 5px;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: background .15s, border-color .15s;
        }
        .check-box:checked { background: var(--y); border-color: var(--y); }
        .check-box:checked::after {
            content: '';
            position: absolute;
            top: 1.5px; left: 4px;
            width: 5px; height: 8px;
            border: 2px solid var(--ink);
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }
        .check-text { font-size: .8125rem; font-weight: 500; color: #4B5563; }

        .forgot-link {
            font-size: .8125rem;
            font-weight: 600;
            color: #6B7280;
            text-decoration: none;
        }
        .forgot-link:hover { color: var(--ink); }

        .btn-submit {
            width: 100%;
            padding: .8rem 1.25rem;
            background: var(--y);
            color: var(--ink);
            font-size: .9375rem;
            font-weight: 700;
            letter-spacing: -.01em;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: background .15s, box-shadow .15s, transform .1s;
        }
        .btn-submit:hover {
            background: #E8B800;
            box-shadow: 0 4px 20px rgba(245,195,0,.4);
        }
        .btn-submit:active { transform: scale(.98); }

        .error-alert {
            display: flex;
            align-items: center;
            gap: .625rem;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            border-radius: 9px;
            padding: .7rem 1rem;
            font-size: .8125rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
        }

        /* ── RESPONSIVE ────────────────────────────────── */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .auth-left { display: none; }
            .auth-right { min-height: 100vh; }
            .auth-right-inner {
                padding: 2rem 1.5rem;
                align-items: flex-start;
                padding-top: 4rem;
            }
        }
    </style>
</head>
<body>

<!-- ── LEFT PANEL ─────────────────────────────────────────── -->
<div class="auth-left">

    <div class="left-logo">
        <img src="/assets/images/logo.png" alt="YellowMedia">
    </div>

    <div class="left-main">
        <div class="left-eyebrow">AI-Powered Media Platform</div>
        <div class="left-headline">
            Smarter ads.<br>Faster <span>content.</span>
        </div>
        <div class="left-tagline">
            AI-generated visuals and Meta Ads intelligence for teams that move fast.
        </div>
    </div>

    <div class="feature-rows">
        <div class="feature-row">
            <div class="feature-icon"><i class="bi bi-magic"></i></div>
            <div class="feature-text">
                <strong>AI Image Generation</strong>
                <span>Social posts, stories and banners in seconds</span>
            </div>
        </div>
        <div class="feature-row">
            <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
            <div class="feature-text">
                <strong>Meta Ads Monitor</strong>
                <span>Live campaign metrics with AI-driven suggestions</span>
            </div>
        </div>
        <div class="feature-row">
            <div class="feature-icon"><i class="bi bi-send"></i></div>
            <div class="feature-text">
                <strong>Campaign Builder</strong>
                <span>Build and launch complete Meta campaigns from one place</span>
            </div>
        </div>
    </div>

</div>

<!-- ── RIGHT PANEL ────────────────────────────────────────── -->
<div class="auth-right">
    <div class="auth-right-top-bar"></div>
    <div class="auth-right-inner">
        <div class="auth-form-wrap">
            @yield('content')
        </div>
    </div>
    <div class="auth-footer">© {{ date('Y') }} YellowMedia · AI-Powered Media Studio</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
