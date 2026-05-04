@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('media.index') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Generate Image
    </a>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Images Generated</div>
                <div class="stat-value">{{ number_format($stats['images_generated']) }}</div>
            </div>
            <div class="stat-icon" style="background:#fef9c3;">
                <i class="bi bi-image" style="color:#a16207;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Active Campaigns</div>
                <div class="stat-value">{{ number_format($stats['active_campaigns']) }}</div>
            </div>
            <div class="stat-icon" style="background:#dcfce7;">
                <i class="bi bi-megaphone" style="color:#166534;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Total Spend</div>
                <div class="stat-value">${{ number_format($stats['total_spend'], 0) }}</div>
            </div>
            <div class="stat-icon" style="background:#fce7f3;">
                <i class="bi bi-currency-dollar" style="color:#9d174d;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Avg ROAS</div>
                <div class="stat-value">{{ number_format($stats['avg_roas'], 2) }}x</div>
            </div>
            <div class="stat-icon" style="background:#ede9fe;">
                <i class="bi bi-graph-up" style="color:#5b21b6;"></i>
            </div>
        </div>
    </div>
</div>

{{-- Usage / Plan widget (hidden for super admin with no tenant) --}}
@if($usage)
<div class="card mb-4">
    <div class="card-body px-4 py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="fw-semibold" style="font-size:.875rem;">Plan Usage</span>
            <span class="badge rounded-pill px-3 py-1 fw-semibold"
                  style="background:{{ $usage['plan'] === 'pro' ? 'var(--yellow)' : ($usage['plan'] === 'starter' ? '#dbeafe' : '#f3f4f6') }};
                         color:{{ $usage['plan'] === 'pro' ? 'var(--black)' : ($usage['plan'] === 'starter' ? '#1e40af' : '#374151') }};
                         font-size:.75rem;">
                {{ $usage['plan_name'] }}
            </span>
        </div>
        <div class="row g-4">
            {{-- Images --}}
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                    <span class="text-muted">Images this month</span>
                    <span class="fw-semibold">
                        {{ $usage['images_used'] }}
                        @if($usage['images_limit'] > 0) / {{ $usage['images_limit'] }} @else <span class="text-muted">/ ∞</span> @endif
                    </span>
                </div>
                @if($usage['images_limit'] > 0)
                <div class="progress" style="height:6px;border-radius:99px;">
                    <div class="progress-bar" role="progressbar"
                         style="width:{{ $usage['images_pct'] }}%;
                                background:{{ $usage['images_pct'] >= 90 ? '#ef4444' : 'var(--yellow)' }};
                                border-radius:99px;"></div>
                </div>
                @if($usage['images_pct'] >= 90)
                    <div class="mt-1 text-danger" style="font-size:.75rem;"><i class="bi bi-exclamation-circle me-1"></i>Approaching limit</div>
                @endif
                @else
                <div class="progress" style="height:6px;border-radius:99px;">
                    <div class="progress-bar" style="width:30%;background:var(--yellow);border-radius:99px;"></div>
                </div>
                <div class="mt-1 text-muted" style="font-size:.75rem;">Unlimited on Pro</div>
                @endif
            </div>
            {{-- Campaigns --}}
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                    <span class="text-muted">Campaigns</span>
                    <span class="fw-semibold">
                        {{ $usage['campaigns_used'] }}
                        @if($usage['campaigns_limit'] > 0) / {{ $usage['campaigns_limit'] }} @else <span class="text-muted">/ ∞</span> @endif
                    </span>
                </div>
                @if($usage['campaigns_limit'] > 0)
                <div class="progress" style="height:6px;border-radius:99px;">
                    <div class="progress-bar" role="progressbar"
                         style="width:{{ $usage['campaigns_pct'] }}%;
                                background:{{ $usage['campaigns_pct'] >= 90 ? '#ef4444' : '#6366f1' }};
                                border-radius:99px;"></div>
                </div>
                @if($usage['campaigns_pct'] >= 90)
                    <div class="mt-1 text-danger" style="font-size:.75rem;"><i class="bi bi-exclamation-circle me-1"></i>Approaching limit</div>
                @endif
                @else
                <div class="progress" style="height:6px;border-radius:99px;">
                    <div class="progress-bar" style="width:30%;background:#6366f1;border-radius:99px;"></div>
                </div>
                <div class="mt-1 text-muted" style="font-size:.75rem;">Unlimited on Pro</div>
                @endif
            </div>
        </div>
        @if(! $usage['ai_suggestions'])
        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
            <span style="font-size:.8rem;color:#6b7280;"><i class="bi bi-stars me-1"></i>AI suggestions &amp; review are available on Starter and Pro plans.</span>
            <a href="{{ route('settings.index') }}" class="btn btn-yellow btn-sm">Upgrade</a>
        </div>
        @endif
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Recent Images --}}
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
                <span>Recent Media</span>
                <a href="{{ route('media.library') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body">
                @if($recentImages->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-images fs-1 d-block mb-2 opacity-25"></i>
                        No images generated yet.
                        <div class="mt-2"><a href="{{ route('media.index') }}" class="btn btn-yellow btn-sm">Generate First Image</a></div>
                    </div>
                @else
                    <div class="row g-2">
                        @foreach($recentImages as $image)
                            <div class="col-6">
                                <div class="position-relative rounded overflow-hidden" style="aspect-ratio:1; background:#f3f4f6;">
                                    @if($image->url)
                                        <img src="{{ $image->url }}" alt="" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <i class="bi bi-image fs-2"></i>
                                        </div>
                                    @endif
                                    <span class="position-absolute bottom-0 start-0 m-1 badge-draft" style="font-size:.65rem;">{{ ucfirst($image->format) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Campaigns --}}
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
                <span>Recent Campaigns</span>
                <a href="{{ route('meta.dashboard') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentCampaigns->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-25"></i>
                        No campaigns synced yet.
                        <div class="mt-2"><a href="{{ route('settings.ad-accounts') }}" class="btn btn-yellow btn-sm">Connect Meta Ads</a></div>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="px-4">Campaign</th>
                                <th>Status</th>
                                <th>Spend</th>
                                <th>ROAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCampaigns as $campaign)
                                <tr>
                                    <td class="px-4">
                                        <a href="{{ route('meta.campaign.show', $campaign->id) }}" class="text-decoration-none fw-semibold text-dark">
                                            {{ $campaign->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-{{ strtolower($campaign->status) === 'active' ? 'active' : (strtolower($campaign->status) === 'paused' ? 'paused' : 'draft') }}">
                                            {{ $campaign->status }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($campaign->spend, 0) }}</td>
                                    <td>{{ number_format($campaign->roas, 2) }}x</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
