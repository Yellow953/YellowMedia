@extends('layouts.app')

@section('title', 'Get Started')
@section('page-title', 'Get Started')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="text-center mb-4">
            <div style="font-size:2.5rem;">👋</div>
            <h4 class="fw-bold mt-2 mb-1">Welcome to YellowMedia!</h4>
            <p class="text-muted mb-0">Complete these steps to get the most out of your account.</p>
        </div>

        {{-- Progress bar --}}
        @php
            $done = collect($steps)->filter()->count();
            $total = count($steps);
            $pct = (int) round($done / $total * 100);
        @endphp
        <div class="card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="font-size:.875rem;">Setup Progress</span>
                    <span class="fw-bold" style="color:var(--yellow-dark);font-size:.875rem;">{{ $done }} / {{ $total }} complete</span>
                </div>
                <div class="progress" style="height:8px;border-radius:99px;">
                    <div class="progress-bar" role="progressbar"
                         style="width:{{ $pct }}%;background:var(--yellow);border-radius:99px;"></div>
                </div>
            </div>
        </div>

        {{-- Steps --}}
        <div class="d-flex flex-column gap-3 mb-4">

            {{-- Step 1: Account created --}}
            <div class="card">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                background:{{ $steps['account'] ? '#d1fae5' : '#f3f4f6' }};">
                        @if($steps['account'])
                            <i class="bi bi-check-lg" style="color:#065f46;font-size:1.1rem;"></i>
                        @else
                            <i class="bi bi-person" style="color:#6b7280;font-size:1.1rem;"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.9rem;">Create your account</div>
                        <div class="text-muted" style="font-size:.8rem;">You're in. Account is active and ready.</div>
                    </div>
                    @if($steps['account'])
                        <span class="badge-active">Done</span>
                    @endif
                </div>
            </div>

            {{-- Step 2: Connect Meta --}}
            <div class="card {{ $steps['meta'] ? '' : 'border-warning' }}" style="{{ $steps['meta'] ? '' : 'border-color:var(--yellow) !important;' }}">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                background:{{ $steps['meta'] ? '#d1fae5' : '#fef9c3' }};">
                        @if($steps['meta'])
                            <i class="bi bi-check-lg" style="color:#065f46;font-size:1.1rem;"></i>
                        @else
                            <i class="bi bi-plug" style="color:#a16207;font-size:1.1rem;"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.9rem;">Connect a Meta Ads account</div>
                        <div class="text-muted" style="font-size:.8rem;">Link your Facebook/Instagram ad account to start syncing campaigns.</div>
                    </div>
                    @if($steps['meta'])
                        <span class="badge-active">Done</span>
                    @else
                        <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm">Connect</a>
                    @endif
                </div>
            </div>

            {{-- Step 3: Generate image --}}
            <div class="card">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                background:{{ $steps['image'] ? '#d1fae5' : '#f3f4f6' }};">
                        @if($steps['image'])
                            <i class="bi bi-check-lg" style="color:#065f46;font-size:1.1rem;"></i>
                        @else
                            <i class="bi bi-magic" style="color:#6b7280;font-size:1.1rem;"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.9rem;">Generate your first AI image</div>
                        <div class="text-muted" style="font-size:.8rem;">Create a social media image using AI in seconds.</div>
                    </div>
                    @if($steps['image'])
                        <span class="badge-active">Done</span>
                    @else
                        <a href="{{ route('media.index') }}" class="btn btn-sm btn-outline-secondary">Go to Studio</a>
                    @endif
                </div>
            </div>

            {{-- Step 4: Create campaign --}}
            <div class="card">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                background:{{ $steps['campaign'] ? '#d1fae5' : '#f3f4f6' }};">
                        @if($steps['campaign'])
                            <i class="bi bi-check-lg" style="color:#065f46;font-size:1.1rem;"></i>
                        @else
                            <i class="bi bi-megaphone" style="color:#6b7280;font-size:1.1rem;"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.9rem;">Create your first campaign</div>
                        <div class="text-muted" style="font-size:.8rem;">Build and launch a Meta ad campaign from YellowMedia.</div>
                    </div>
                    @if($steps['campaign'])
                        <span class="badge-active">Done</span>
                    @else
                        <a href="{{ route('campaigns.create') }}" class="btn btn-sm btn-outline-secondary">Create</a>
                    @endif
                </div>
            </div>

        </div>

        {{-- CTA --}}
        <div class="text-center">
            @if($allDone)
                <form method="POST" action="{{ route('onboarding.complete') }}">
                    @csrf
                    <button type="submit" class="btn btn-yellow btn-lg px-5">
                        <i class="bi bi-rocket-takeoff me-2"></i>Go to Dashboard
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('onboarding.complete') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">
                        Skip for now
                    </button>
                </form>
                <p class="text-muted mt-2" style="font-size:.8rem;">You can always complete setup from Settings.</p>
            @endif
        </div>

    </div>
</div>
@endsection
