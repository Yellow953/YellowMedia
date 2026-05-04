@extends('layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit: '.$tenant->name)

@section('content')
<div class="card" style="max-width:500px;">
    <div class="card-header py-3 px-4">Edit Tenant</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Business Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Plan</label>
                <select name="plan" class="form-select">
                    @foreach(['free','starter','pro'] as $plan)
                        <option value="{{ $plan }}" {{ $tenant->plan === $plan ? 'selected' : '' }}>{{ ucfirst($plan) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" {{ $tenant->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-yellow">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
