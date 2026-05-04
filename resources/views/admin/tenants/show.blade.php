@extends('layouts.app')

@section('title', $tenant->name)
@section('page-title', $tenant->name)

@section('topbar-actions')
    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-yellow btn-sm">Edit</a>
    <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header py-3 px-4">Tenant Info</div>
            <div class="card-body">
                <dl class="mb-0" style="font-size:.875rem;">
                    <dt class="text-muted">Plan</dt><dd>{{ ucfirst($tenant->plan) }}</dd>
                    <dt class="text-muted">Status</dt><dd><span class="{{ $tenant->is_active ? 'badge-active' : 'badge-paused' }}">{{ $tenant->is_active ? 'Active' : 'Inactive' }}</span></dd>
                    <dt class="text-muted">Created</dt><dd>{{ $tenant->created_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
                <span>Users</span>
                <a href="{{ route('admin.users.create') }}?tenant_id={{ $tenant->id }}" class="btn btn-sm btn-yellow">Add User</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th class="px-4">Name</th><th>Email</th><th>Role</th></tr></thead>
                    <tbody>
                        @forelse($tenant->users as $user)
                            <tr>
                                <td class="px-4">{{ $user->name }}</td>
                                <td class="text-muted small">{{ $user->email }}</td>
                                <td><span class="badge-draft">{{ ucfirst(str_replace('_',' ',$user->role)) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No users yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
