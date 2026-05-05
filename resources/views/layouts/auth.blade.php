<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — YellowMedia</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --yellow: #F5C300;
            --yellow-dark: #d4a900;
            --yellow-pale: #fefce8;
            --black: #111111;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* Left panel */
        .auth-left {
            background: var(--black);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 80%, rgba(245,195,0,.18) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 80% 20%, rgba(245,195,0,.10) 0%, transparent 70%);
        }
        .auth-left > * { position: relative; z-index: 1; }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: var(--yellow);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: var(--black); font-weight: 800;
        }
        .brand-name { font-size: 1.1rem; font-weight: 700; color: #fff; letter-spacing: -.3px; }

        .auth-headline {
            color: #fff;
        }
        .auth-headline h1 {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.04em;
            margin-bottom: 1rem;
        }
        .auth-headline h1 em {
            font-style: normal;
            color: var(--yellow);
        }
        .auth-headline p {
            font-size: .9375rem;
            color: rgba(255,255,255,.55);
            line-height: 1.6;
            max-width: 340px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: .8125rem;
            color: rgba(255,255,255,.6);
        }
        .feature-dot {
            width: 28px; height: 28px;
            background: rgba(245,195,0,.12);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            color: var(--yellow);
            font-size: .8rem;
            flex-shrink: 0;
        }

        /* Right panel */
        .auth-right {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 380px;
        }

        .auth-form-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: .375rem;
            letter-spacing: -.03em;
        }
        .auth-form-sub {
            font-size: .875rem;
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .form-label {
            font-size: .8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .375rem;
        }
        .form-control {
            font-size: .875rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: .6rem .875rem;
            color: var(--black);
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus {
            border-color: var(--yellow);
            box-shadow: 0 0 0 3px rgba(245,195,0,.15);
            outline: none;
        }
        .form-control.is-invalid { border-color: #ef4444; }
        .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.12); }

        .input-icon-wrap { position: relative; }
        .input-icon-wrap .input-icon {
            position: absolute;
            left: .875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: .9rem;
            pointer-events: none;
        }
        .input-icon-wrap .form-control { padding-left: 2.5rem; }
        .input-icon-wrap .toggle-pass {
            position: absolute;
            right: .875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: #9ca3af;
            cursor: pointer;
            font-size: .9rem;
            line-height: 1;
        }
        .input-icon-wrap .toggle-pass:hover { color: #374151; }

        .btn-signin {
            width: 100%;
            padding: .7rem;
            background: var(--black);
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .15s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-signin:hover { background: #1f2937; }
        .btn-signin:active { transform: scale(.98); }

        .divider {
            display: flex; align-items: center; gap: .75rem;
            font-size: .75rem; color: #d1d5db; margin: 1.25rem 0;
        }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .remember-row .form-check { margin: 0; }
        .form-check-input:checked {
            background-color: var(--yellow);
            border-color: var(--yellow);
        }
        .form-check-input:focus { box-shadow: 0 0 0 3px rgba(245,195,0,.2); border-color: var(--yellow); }
        .form-check-label { font-size: .8125rem; color: #374151; cursor: pointer; }

        .forgot-link {
            font-size: .8125rem;
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { color: var(--black); }

        /* Responsive */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .auth-left { display: none; }
            .auth-right { padding: 2rem 1.25rem; align-items: flex-start; padding-top: 4rem; }
        }
    </style>
</head>
<body>

<div class="auth-left">
    <div class="brand-mark">
        <img src="/assets/images/logo.png" alt="YellowMedia" style="width: 180px; height: auto;">
    </div>

    <div class="auth-headline">
        <h1>Grow smarter.<br>Post <em>faster.</em></h1>
        <p>AI-generated visuals and Meta Ads intelligence — all in one dashboard built for modern teams.</p>
    </div>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-dot"><i class="bi bi-magic"></i></div>
            AI image generation for social media
        </div>
        <div class="feature-item">
            <div class="feature-dot"><i class="bi bi-bar-chart-line"></i></div>
            Real-time Meta Ads performance monitor
        </div>
        <div class="feature-item">
            <div class="feature-dot"><i class="bi bi-lightbulb"></i></div>
            AI-driven campaign optimization suggestions
        </div>
        <div class="feature-item">
            <div class="feature-dot"><i class="bi bi-building"></i></div>
            Multi-tenant — built for agencies &amp; teams
        </div>
    </div>
</div>

<div class="auth-right">
    <div class="auth-form-wrap">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
