@extends('layouts.app')

@section('title', 'Ad Accounts')
@section('page-title', 'Ad Accounts')

@section('topbar-actions')
    <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Connect Meta Ads
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header py-3 px-4">Connected Ad Accounts</div>
    <div class="card-body p-0">
        @if($accounts->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-plug fs-1 d-block mb-3 opacity-25"></i>
                <div class="fw-semibold">No ad accounts connected yet</div>
                <div class="small mt-1">Connect your Meta Ads account to monitor and manage campaigns</div>
                <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm mt-3">
                    <i class="bi bi-facebook me-1"></i> Connect Meta Ads
                </a>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="px-4">Account Name</th>
                        <th>Account ID</th>
                        <th>Status</th>
                        <th>Connected</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                        <tr>
                            <td class="px-4 fw-semibold">{{ $account->account_name }}</td>
                            <td class="text-muted small">{{ $account->account_id }}</td>
                            <td>
                                <span class="{{ $account->is_active ? 'badge-active' : 'badge-paused' }}">
                                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $account->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('settings.ad-accounts.remove', $account->id) }}"
                                      onsubmit="return confirm('Remove this ad account?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
