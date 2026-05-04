@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="row g-3">
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('settings.ad-accounts') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background:#dbeafe;">
                        <i class="bi bi-plug" style="color:#1d4ed8;"></i>
                    </div>
                    <div class="fw-semibold mb-1">Ad Accounts</div>
                    <div class="small text-muted">Connect and manage your Meta Ads accounts</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="stat-icon mb-3" style="background:#dcfce7;">
                    <i class="bi bi-person-circle" style="color:#166534;"></i>
                </div>
                <div class="fw-semibold mb-1">Profile</div>
                <div class="small text-muted">Your account: {{ auth()->user()->email }}</div>
                <div class="small text-muted mt-1">Role: {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
            </div>
        </div>
    </div>
    @if(auth()->user()->tenant)
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="stat-icon mb-3" style="background:#fef9c3;">
                    <i class="bi bi-building" style="color:#a16207;"></i>
                </div>
                <div class="fw-semibold mb-1">Organization</div>
                <div class="small text-muted">{{ auth()->user()->tenant->name }}</div>
                <div class="small text-muted mt-1">Plan: <span class="fw-semibold">{{ ucfirst(auth()->user()->tenant->plan) }}</span></div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
