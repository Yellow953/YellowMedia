@extends('layouts.app')

@section('title', 'Campaign Builder')
@section('page-title', 'Campaign Builder')

@section('topbar-actions')
    <a href="{{ route('campaigns.create') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Campaign
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header py-3 px-4">All Campaigns</div>
    <div class="card-body p-0">
        @if($campaigns->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-megaphone fs-1 d-block mb-3 opacity-25"></i>
                <div class="fw-semibold">No campaigns yet</div>
                <div class="small mt-1">Build your first campaign and publish it to Meta</div>
                <a href="{{ route('campaigns.create') }}" class="btn btn-yellow btn-sm mt-3">
                    <i class="bi bi-plus-lg me-1"></i> Create Campaign
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="px-4">Name</th>
                            <th>Status</th>
                            <th>Objective</th>
                            <th>Spend</th>
                            <th>ROAS</th>
                            <th>Created</th>
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
                                <td class="text-muted">{{ $campaign->objective ?? '—' }}</td>
                                <td>${{ number_format($campaign->spend, 0) }}</td>
                                <td>{{ number_format($campaign->roas, 2) }}x</td>
                                <td class="text-muted small">{{ $campaign->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('campaigns.show', $campaign->id) }}" class="btn btn-sm btn-outline-secondary">Manage</a>
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
