@extends('layouts.app')

@section('title', $campaign->name)
@section('page-title', $campaign->name)

@section('topbar-actions')
    <a href="{{ route('meta.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Monitor
    </a>
@endsection

@section('content')
<div class="row g-4">
    {{-- Metrics --}}
    <div class="col-12">
        <div class="row g-3">
            @php $metrics = ['Spend' => '$'.number_format($campaign->spend,2), 'Impressions' => number_format($campaign->impressions), 'Clicks' => number_format($campaign->clicks), 'CTR' => number_format($campaign->ctr,2).'%', 'CPC' => '$'.number_format($campaign->cpc,2), 'ROAS' => number_format($campaign->roas,2).'x'] @endphp
            @foreach($metrics as $label => $value)
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card text-center">
                        <div class="stat-label">{{ $label }}</div>
                        <div class="stat-value" style="font-size:1.4rem;">{{ $value }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- AI Suggestions --}}
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
                <span><i class="bi bi-lightbulb me-2" style="color:var(--yellow);"></i>AI Suggestions</span>
                <form method="POST" action="{{ route('meta.sync') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-repeat me-1"></i> Refresh
                    </button>
                </form>
            </div>
            <div class="card-body">
                @if($campaign->suggestions->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-lightbulb fs-2 d-block mb-2 opacity-25"></i>
                        No suggestions yet. Sync campaigns to generate AI recommendations.
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($campaign->suggestions as $suggestion)
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex gap-2 mb-2">
                                            <span class="badge-{{ $suggestion->priority }}">{{ ucfirst($suggestion->priority) }}</span>
                                            <span class="badge-draft">{{ ucfirst($suggestion->type) }}</span>
                                        </div>
                                        <div class="small">{{ $suggestion->suggestion }}</div>
                                    </div>
                                    <form method="POST" action="{{ route('meta.suggestion.dismiss', $suggestion->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Dismiss">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Campaign Info --}}
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header py-3 px-4">Campaign Details</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        @php $s = strtolower($campaign->status) @endphp
                        <span class="badge-{{ $s === 'active' ? 'active' : ($s === 'paused' ? 'paused' : 'draft') }}">{{ $campaign->status }}</span>
                    </dd>
                    <dt class="col-5 text-muted">Objective</dt>
                    <dd class="col-7">{{ $campaign->objective ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Budget</dt>
                    <dd class="col-7">${{ number_format($campaign->budget, 2) }}</dd>
                    <dt class="col-5 text-muted">Ad Account</dt>
                    <dd class="col-7">{{ $campaign->adAccount->account_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Last Synced</dt>
                    <dd class="col-7">{{ $campaign->last_synced_at?->diffForHumans() ?? 'Never' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header py-3 px-4">Quick Actions</div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('media.index') }}?campaign_id={{ $campaign->id }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-magic me-2"></i> Refresh Creative in Media Studio
                </a>
                <a href="{{ route('campaigns.show', $campaign->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil me-2"></i> Edit in Campaign Builder
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
