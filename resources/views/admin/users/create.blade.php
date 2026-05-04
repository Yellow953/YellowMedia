@extends('layouts.app')

@section('title', 'New User')
@section('page-title', 'New User')

@section('content')
<div class="card" style="max-width:500px;">
    <div class="card-header py-3 px-4">Create User</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select name="role" class="form-select">
                    <option value="user">User</option>
                    <option value="tenant_admin">Tenant Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Tenant</label>
                <select name="tenant_id" class="form-select">
                    <option value="">None (Super Admin)</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-yellow">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection
