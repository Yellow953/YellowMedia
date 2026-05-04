@extends('layouts.app')

@section('title', $user->name)
@section('page-title', $user->name)

@section('topbar-actions')
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-yellow btn-sm">Edit</a>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
@endsection

@section('content')
<div class="card" style="max-width:500px;">
    <div class="card-header py-3 px-4">User Details</div>
    <div class="card-body">
        <dl class="row mb-0" style="font-size:.875rem;">
            <dt class="col-4 text-muted">Name</dt><dd class="col-8">{{ $user->name }}</dd>
            <dt class="col-4 text-muted">Email</dt><dd class="col-8">{{ $user->email }}</dd>
            <dt class="col-4 text-muted">Role</dt><dd class="col-8"><span class="badge-draft">{{ ucfirst(str_replace('_',' ',$user->role)) }}</span></dd>
            <dt class="col-4 text-muted">Tenant</dt><dd class="col-8">{{ $user->tenant?->name ?? '—' }}</dd>
            <dt class="col-4 text-muted">Joined</dt><dd class="col-8">{{ $user->created_at->format('M d, Y') }}</dd>
        </dl>
    </div>
</div>
@endsection
