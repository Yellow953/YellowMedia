@extends('layouts.app')

@section('title', $campaign->name)
@section('page-title', $campaign->name)

@section('topbar-actions')
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> All Campaigns
    </a>
    @if(strtolower($campaign->status) === 'draft')
        <form method="POST" action="{{ route('campaigns.launch', $campaign->id) }}" class="d-inline" onsubmit="return confirm('Launch this campaign to Meta Ads?')">
            @csrf
            <button type="submit" class="btn btn-yellow btn-sm">
                <i class="bi bi-rocket me-1"></i> Launch to Meta
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="row g-4">
    <div class="col-xl-8">

        {{-- Campaign Info --}}
        <div class="card mb-3">
            <div class="card-header py-3 px-4">Campaign Details</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4 text-muted">Status</dt>
                    <dd class="col-8">
                        @php $s = strtolower($campaign->status) @endphp
                        <span class="badge-{{ $s === 'active' ? 'active' : ($s === 'paused' ? 'paused' : 'draft') }}">{{ $campaign->status }}</span>
                    </dd>
                    <dt class="col-4 text-muted">Objective</dt>
                    <dd class="col-8">{{ $campaign->objective ?? '—' }}</dd>
                    <dt class="col-4 text-muted">Budget</dt>
                    <dd class="col-8">${{ number_format($campaign->budget, 2) }}/day</dd>
                    <dt class="col-4 text-muted">Spend</dt>
                    <dd class="col-8">${{ number_format($campaign->spend, 2) }}</dd>
                </dl>
            </div>
        </div>

        {{-- Ad Sets --}}
        <div class="card mb-3">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
                <span>Ad Sets</span>
            </div>
            <div class="card-body p-0">
                @forelse($campaign->adSets as $adSet)
                    <div class="p-4 border-bottom">
                        <div class="fw-semibold mb-1">{{ $adSet->name }}</div>
                        <div class="small text-muted mb-2">Placement: {{ ucfirst($adSet->placement) }} · Budget: ${{ number_format($adSet->daily_budget, 2) }}/day</div>

                        @foreach($adSet->ads as $ad)
                            <div class="border rounded p-3 mt-2 d-flex gap-3">
                                @if($ad->image?->url)
                                    <img src="{{ $ad->image->url }}" style="width:72px;height:72px;object-fit:cover;border-radius:6px;" alt="">
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">{{ $ad->name }}</div>
                                    <div class="small text-muted">{{ $ad->headline }}</div>
                                    <div class="small text-muted">{{ $ad->body }}</div>
                                    <span class="badge-draft mt-1 d-inline-block">{{ $ad->cta }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="p-4 text-center text-muted small">No ad sets yet.</div>
                @endforelse
            </div>
        </div>

        {{-- AI Review Actions --}}
        @if($campaign->actionLog->isNotEmpty())
            <div class="card">
                <div class="card-header py-3 px-4">AI Action Log</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="px-4">Action</th>
                                <th>Reason</th>
                                <th>Applied By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaign->actionLog as $log)
                                <tr>
                                    <td class="px-4"><code>{{ $log->action_type }}</code></td>
                                    <td class="small text-muted">{{ $log->reason }}</td>
                                    <td class="small">{{ $log->appliedBy->name ?? '—' }}</td>
                                    <td class="small text-muted">{{ $log->applied_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header py-3 px-4">AI Review</div>
            <div class="card-body">
                <p class="small text-muted">Let Gemini analyze this campaign's performance and generate actionable recommendations.</p>
                <form method="POST" action="{{ route('campaigns.review', $campaign->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-yellow w-100 btn-sm">
                        <i class="bi bi-lightbulb me-1"></i> Run AI Review
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header py-3 px-4">Quick Actions</div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('media.index') }}?campaign_id={{ $campaign->id }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-magic me-2"></i> Generate Creative
                </a>
                <a href="{{ route('meta.campaign.show', $campaign->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-bar-chart me-2"></i> View Metrics
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
