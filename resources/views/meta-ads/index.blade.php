@extends('layouts.app')

@section('title', 'Ad Accounts')
@section('page-title', 'Ad Accounts')

@section('topbar-actions')
    <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Connect Meta Ads
    </a>
@endsection

@section('content')
@if($accounts->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-plug fs-1 d-block mb-3 opacity-25 text-muted"></i>
            <div class="fw-semibold">No ad accounts connected</div>
            <div class="small text-muted mt-1">Connect your Meta Ads account to start monitoring campaigns</div>
            <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm mt-3">
                <i class="bi bi-facebook me-1"></i> Connect Meta Ads
            </a>
        </div>
    </div>
@else
    <div class="row g-3 mb-4">
        @foreach($accounts as $account)
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon" style="background:#dbeafe;">
                                <i class="bi bi-facebook" style="color:#1d4ed8;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $account->account_name }}</div>
                                <div class="small text-muted">ID: {{ $account->account_id }}</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('meta.dashboard') }}" class="btn btn-sm btn-yellow flex-fill">View Campaigns</a>
                            <form method="POST" action="{{ route('meta.sync') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-repeat"></i> Sync
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        <a href="{{ route('meta.connect') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Connect Another Account
        </a>
    </div>
@endif
@endsection
