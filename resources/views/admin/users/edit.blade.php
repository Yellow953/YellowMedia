@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit: '.$user->name)

@section('content')
<div class="card" style="max-width:500px;">
    <div class="card-header py-3 px-4">Edit User</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password <span class="text-muted fw-normal">(leave blank to keep)</span></label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select name="role" class="form-select">
                    @foreach(['user','tenant_admin','super_admin'] as $role)
                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$role)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Tenant</label>
                <select name="tenant_id" class="form-select">
                    <option value="">None (Super Admin)</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ $user->tenant_id == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-yellow">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
