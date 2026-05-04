@extends('layouts.app')

@section('title', 'Ads Monitor')
@section('page-title', 'Ads Monitor')

@section('topbar-actions')
    <form method="POST" action="{{ route('meta.sync') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-repeat me-1"></i> Sync Now
        </button>
    </form>
@endsection

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Total Spend</div>
                <div class="stat-value">${{ number_format($totals->total_spend ?? 0, 0) }}</div>
            </div>
            <div class="stat-icon" style="background:#fee2e2;">
                <i class="bi bi-currency-dollar" style="color:#991b1b;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Impressions</div>
                <div class="stat-value">{{ number_format($totals->total_impressions ?? 0) }}</div>
            </div>
            <div class="stat-icon" style="background:#dbeafe;">
                <i class="bi bi-eye" style="color:#1d4ed8;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Avg CTR</div>
                <div class="stat-value">{{ number_format($totals->avg_ctr ?? 0, 2) }}%</div>
            </div>
            <div class="stat-icon" style="background:#dcfce7;">
                <i class="bi bi-cursor" style="color:#166534;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Avg ROAS</div>
                <div class="stat-value">{{ number_format($totals->avg_roas ?? 0, 2) }}x</div>
            </div>
            <div class="stat-icon" style="background:#ede9fe;">
                <i class="bi bi-graph-up" style="color:#5b21b6;"></i>
            </div>
        </div>
    </div>
</div>

{{-- Campaigns Table --}}
<div class="card">
    <div class="card-header py-3 px-4">Campaigns</div>
    <div class="card-body p-0">
        @if($campaigns->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bar-chart fs-1 d-block mb-3 opacity-25"></i>
                No campaigns found. <a href="{{ route('meta.connect') }}">Connect your Meta Ads account</a> to get started.
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="px-4">Campaign</th>
                            <th>Status</th>
                            <th>Spend</th>
                            <th>Impressions</th>
                            <th>CTR</th>
                            <th>CPC</th>
                            <th>ROAS</th>
                            <th>Last Synced</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                            <tr>
                                <td class="px-4 fw-semibold">{{ $campaign->name }}</td>
                                <td>
                                    @php $s = strtolower($campaign->status) @endphp
                                    <span class="badge-{{ $s === 'active' ? 'active' : ($s === 'paused' ? 'paused' : 'draft') }}">
                                        {{ $campaign->status }}
                                    </span>
                                </td>
                                <td>${{ number_format($campaign->spend, 2) }}</td>
                                <td>{{ number_format($campaign->impressions) }}</td>
                                <td>{{ number_format($campaign->ctr, 2) }}%</td>
                                <td>${{ number_format($campaign->cpc, 2) }}</td>
                                <td>{{ number_format($campaign->roas, 2) }}x</td>
                                <td class="text-muted small">{{ $campaign->last_synced_at?->diffForHumans() ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('meta.campaign.show', $campaign->id) }}" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 d-flex justify-content-center">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
