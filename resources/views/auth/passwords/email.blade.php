@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

<div class="form-title">Forgot password?</div>
<div class="form-sub">Enter your email and we'll send you a reset link</div>

@if(session('status'))
    <div class="error-alert" style="background:#f0fdf4;border-color:#bbf7d0;color:#16a34a;margin-bottom:1.25rem;">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div class="error-alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" novalidate>
    @csrf

    <div class="field-group">
        <label class="field-label" for="email">Email address</label>
        <div class="field-inner">
            <i class="bi bi-envelope field-icon"></i>
            <input type="email" id="email" name="email"
                   class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                   value="{{ old('email') }}"
                   placeholder="you@company.com"
                   required autofocus>
        </div>
    </div>

    <button type="submit" class="btn-submit">
        Send Reset Link <i class="bi bi-arrow-right"></i>
    </button>
</form>

<div style="text-align:center;margin-top:1.5rem;">
    <a href="{{ route('login') }}" class="forgot-link">
        <i class="bi bi-arrow-left" style="font-size:.75rem;"></i> Back to sign in
    </a>
</div>

@endsection
