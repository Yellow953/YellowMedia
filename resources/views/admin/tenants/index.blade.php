@extends('layouts.app')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('topbar-actions')
    <a href="{{ route('admin.tenants.create') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Tenant
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header py-3 px-4">All Tenants</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="px-4">Name</th>
                    <th>Slug</th>
                    <th>Plan</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td class="px-4 fw-semibold">{{ $tenant->name }}</td>
                        <td class="text-muted small"><code>{{ $tenant->slug }}</code></td>
                        <td><span class="badge-draft">{{ ucfirst($tenant->plan) }}</span></td>
                        <td>{{ $tenant->users_count }}</td>
                        <td><span class="{{ $tenant->is_active ? 'badge-active' : 'badge-paused' }}">{{ $tenant->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-muted small">{{ $tenant->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" onsubmit="return confirm('Delete this tenant?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No tenants yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3 d-flex justify-content-center">{{ $tenants->links() }}</div>
    </div>
</div>
@endsection
