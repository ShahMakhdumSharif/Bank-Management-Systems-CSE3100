@extends('layouts.app')

@section('title', config('bank.name') . ' | Password Reset')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <section class="login-shell" aria-labelledby="forgot-title">
            <div class="login-copy">
                <p class="eyebrow">Password reset</p>
                <h1 id="forgot-title">Recover your secure access.</h1>
                <p>
                    Enter your registered email address and the system will send a password reset link when the account exists.
                </p>
            </div>

            <form class="login-card" method="POST" action="{{ route('password.email') }}">
                @csrf

                @if (session('status'))
                    <p class="auth-status">{{ session('status') }}</p>
                @endif

                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <button class="login-button" type="submit">Email Reset Link</button>

                <p class="auth-switch">
                    Remembered it?
                    <a href="{{ route('login') }}">Back to login</a>
                </p>
            </form>
        </section>
    </main>
@endsection
