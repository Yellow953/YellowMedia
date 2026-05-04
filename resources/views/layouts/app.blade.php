<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — YellowMedia</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --yellow: #F5C300;
            --yellow-dark: #d4a900;
            --black: #111111;
            --sidebar-width: 240px;
        }
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--black);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--yellow);
            letter-spacing: -0.5px;
        }
        .sidebar-brand .logo-sub {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.4);
            display: block;
            margin-top: -2px;
        }
        .sidebar-nav { padding: 1rem 0; flex: 1; }
        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            padding: 0.75rem 1.5rem 0.25rem;
        }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 0.55rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            border-left: 3px solid transparent;
            transition: all 0.15s;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.25rem; }
        .sidebar-nav .nav-link:hover { color: #fff; background: rgba(255,255,255,0.07); }
        .sidebar-nav .nav-link.active { color: #fff; background: rgba(255,255,255,0.07); border-left-color: var(--yellow); }
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
        }

        /* Main */
        #main-content { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }

        /* Topbar */
        #topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-title { font-size: 1rem; font-weight: 600; color: var(--black); }

        /* Page content */
        .page-content { padding: 1.75rem; flex: 1; }

        /* Cards */
        .card { border: 1px solid #e9ecef; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .card-header { background: #fff; border-bottom: 1px solid #e9ecef; font-weight: 600; border-radius: 10px 10px 0 0 !important; }

        /* Stat cards */
        .stat-card { border-radius: 10px; border: 1px solid #e9ecef; background: #fff; padding: 1.25rem 1.5rem; }
        .stat-card .stat-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #6c757d; }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--black); line-height: 1.2; margin-top: 0.25rem; }

        /* Buttons */
        .btn-yellow { background: var(--yellow); color: var(--black); font-weight: 600; border: none; }
        .btn-yellow:hover { background: var(--yellow-dark); color: var(--black); }
        .btn-outline-yellow { border: 1.5px solid var(--yellow); color: var(--black); background: transparent; font-weight: 600; }
        .btn-outline-yellow:hover { background: var(--yellow); color: var(--black); }

        /* Tables */
        .table th { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; border-bottom: 2px solid #e9ecef; }
        .table td { vertical-align: middle; font-size: 0.875rem; }

        /* Pagination */
        .ym-pagination-nav { display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem; flex-wrap: wrap; gap: .5rem; }
        .ym-pagination { display: flex; align-items: center; gap: .25rem; list-style: none; margin: 0; padding: 0; }
        .ym-page-link {
            display: flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; padding: 0 .6rem;
            border-radius: 8px;
            font-size: .8125rem; font-weight: 500;
            color: #374151;
            background: #fff;
            border: 1px solid #e5e7eb;
            text-decoration: none;
            transition: all .15s;
            line-height: 1;
        }
        .ym-page-item a.ym-page-link:hover { background: #f9fafb; border-color: #d1d5db; color: #111; }
        .ym-page-item.active .ym-page-link { background: var(--yellow); border-color: var(--yellow); color: #111; font-weight: 700; }
        .ym-page-item.disabled .ym-page-link { opacity: .4; cursor: default; }
        .ym-pagination-info { font-size: .8rem; color: #6b7280; }

        /* Status badges */
        .badge-active { background: #d1fae5; color: #065f46; font-size: .7rem; font-weight: 600; padding: .3em .65em; border-radius: 5px; }
        .badge-paused { background: #fef3c7; color: #92400e; font-size: .7rem; font-weight: 600; padding: .3em .65em; border-radius: 5px; }
        .badge-draft  { background: #f3f4f6; color: #374151; font-size: .7rem; font-weight: 600; padding: .3em .65em; border-radius: 5px; }
        .badge-high   { background: #fee2e2; color: #991b1b; font-size: .7rem; font-weight: 600; padding: .3em .65em; border-radius: 5px; }
        .badge-medium { background: #fef3c7; color: #92400e; font-size: .7rem; font-weight: 600; padding: .3em .65em; border-radius: 5px; }
        .badge-low    { background: #d1fae5; color: #065f46; font-size: .7rem; font-weight: 600; padding: .3em .65em; border-radius: 5px; }
    </style>

    @stack('styles')
</head>
<body>

<div id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-text">YellowMedia</div>
        <span class="logo-sub">Social Media Suite</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="sidebar-section-title">Create</div>
        <a href="{{ route('media.index') }}" class="nav-link {{ request()->routeIs('media.index') || request()->routeIs('media.generate') ? 'active' : '' }}">
            <i class="bi bi-magic"></i> Media Studio
        </a>
        <a href="{{ route('media.library') }}" class="nav-link {{ request()->routeIs('media.library') ? 'active' : '' }}">
            <i class="bi bi-images"></i> Media Library
        </a>

        <div class="sidebar-section-title">Campaigns</div>
        <a href="{{ route('campaigns.index') }}" class="nav-link {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> Campaign Builder
        </a>
        <a href="{{ route('meta.dashboard') }}" class="nav-link {{ request()->routeIs('meta.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Ads Monitor
        </a>

        <div class="sidebar-section-title">Account</div>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>
        <a href="{{ route('settings.ad-accounts') }}" class="nav-link {{ request()->routeIs('settings.ad-accounts') ? 'active' : '' }}">
            <i class="bi bi-plug"></i> Ad Accounts
        </a>

        @if(auth()->user()->isSuperAdmin())
        <div class="sidebar-section-title">Admin</div>
        <a href="{{ route('admin.tenants.index') }}" class="nav-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Tenants
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="fw-semibold" style="color:#fff;font-size:.8rem;">{{ auth()->user()->name }}</div>
        <div>{{ auth()->user()->tenant?->name ?? 'Super Admin' }}</div>
    </div>
</div>

<div id="main-content">
    <div id="topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="d-flex align-items-center gap-3">
            @yield('topbar-actions')
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
