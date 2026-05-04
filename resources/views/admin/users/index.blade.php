@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('topbar-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New User
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header py-3 px-4">All Users</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="px-4">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tenant</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 fw-semibold">{{ $user->name }}</td>
                        <td class="text-muted small">{{ $user->email }}</td>
                        <td><span class="badge-draft">{{ ucfirst(str_replace('_',' ',$user->role)) }}</span></td>
                        <td class="text-muted small">{{ $user->tenant?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3 d-flex justify-content-center">{{ $users->links() }}</div>
    </div>
</div>
@endsection
