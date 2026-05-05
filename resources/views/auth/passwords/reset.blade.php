@extends('layouts.auth')

@section('title', 'Set New Password')

@section('content')

<div class="form-title">Set new password</div>
<div class="form-sub">Choose a strong password for your account</div>

@if($errors->any())
    <div class="error-alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="field-group">
        <label class="field-label" for="email">Email address</label>
        <div class="field-inner">
            <i class="bi bi-envelope field-icon"></i>
            <input type="email" id="email" name="email"
                   class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                   value="{{ $email ?? old('email') }}"
                   placeholder="you@company.com"
                   required autocomplete="email" autofocus>
        </div>
    </div>

    <div class="field-group">
        <label class="field-label" for="password">New password</label>
        <div class="field-inner">
            <i class="bi bi-lock field-icon"></i>
            <input type="password" id="password" name="password"
                   class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                   placeholder="••••••••"
                   required autocomplete="new-password">
            <button type="button" class="toggle-pass" onclick="togglePassword('password','icon1')">
                <i class="bi bi-eye" id="icon1"></i>
            </button>
        </div>
    </div>

    <div class="field-group">
        <label class="field-label" for="password-confirm">Confirm password</label>
        <div class="field-inner">
            <i class="bi bi-lock field-icon"></i>
            <input type="password" id="password-confirm" name="password_confirmation"
                   class="field-input"
                   placeholder="••••••••"
                   required autocomplete="new-password">
            <button type="button" class="toggle-pass" onclick="togglePassword('password-confirm','icon2')">
                <i class="bi bi-eye" id="icon2"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-submit">
        Reset Password <i class="bi bi-arrow-right"></i>
    </button>
</form>

@push('scripts')
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
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
