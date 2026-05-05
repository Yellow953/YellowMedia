@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')

<div class="form-title">Welcome back</div>
<div class="form-sub">Sign in to your YellowMedia account</div>

@if($errors->any())
    <div class="error-alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" novalidate>
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

    <div class="field-group">
        <label class="field-label" for="password">Password</label>
        <div class="field-inner">
            <i class="bi bi-lock field-icon"></i>
            <input type="password" id="password" name="password"
                   class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                   placeholder="••••••••"
                   required>
            <button type="button" class="toggle-pass" onclick="togglePassword()">
                <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
        </div>
    </div>

    <div class="row-split">
        <label class="check-label">
            <input class="check-box" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="check-text">Remember me</span>
        </label>
        @if(Route::has('password.request'))
            <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
    </div>

    <button type="submit" class="btn-submit">
        Sign In <i class="bi bi-arrow-right"></i>
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
