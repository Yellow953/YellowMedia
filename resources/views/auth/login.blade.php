@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')

<div class="auth-form-title">Welcome back</div>
<div class="auth-form-sub">Sign in to your YellowMedia account</div>

@if($errors->any())
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size:.8125rem;border-radius:8px;">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div class="mb-3">
        <label class="form-label" for="email">Email address</label>
        <div class="input-icon-wrap">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email" id="email" name="email"
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}"
                   placeholder="you@company.com"
                   required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <div class="input-icon-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" id="password" name="password"
                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="••••••••"
                   required>
            <button type="button" class="toggle-pass" onclick="togglePassword()" id="toggleBtn">
                <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
        </div>
    </div>

    <div class="remember-row">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        @if(Route::has('password.request'))
            <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
    </div>

    <button type="submit" class="btn-signin">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
    </button>
</form>

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush

@endsection
