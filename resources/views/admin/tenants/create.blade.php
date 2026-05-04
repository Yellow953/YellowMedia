@extends('layouts.app')

@section('title', 'New Tenant')
@section('page-title', 'New Tenant')

@section('content')
<div class="card" style="max-width:500px;">
    <div class="card-header py-3 px-4">Create Tenant</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.tenants.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Business Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Plan</label>
                <select name="plan" class="form-select @error('plan') is-invalid @enderror">
                    <option value="free">Free</option>
                    <option value="starter">Starter</option>
                    <option value="pro">Pro</option>
                </select>
                @error('plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-yellow">Create Tenant</button>
            </div>
        </form>
    </div>
</div>
@endsection
