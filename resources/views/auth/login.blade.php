@extends('layouts.app')

@section('title', config('bank.name') . ' | Secure Login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <section class="login-shell" aria-labelledby="login-title">
            <div class="login-copy">
                <p class="eyebrow">Secure login</p>
                <h1 id="login-title">Access your banking dashboard.</h1>
                <p>
                    Sign in to review approval status, account services, transfer requests, transaction history, and card services.
                </p>
                <div class="login-highlights" aria-label="Login security highlights">
                    <span>Encrypted access</span>
                    <span>Approval protected</span>
                </div>
            </div>

            <form class="login-card" method="POST" action="{{ route('login') }}">
                @csrf

                @if (session('status'))
                    <p class="auth-status">{{ session('status') }}</p>
                @endif

                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <div class="login-options">
                    <label class="remember-choice" for="remember">
                        <input id="remember" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button class="login-button" type="submit">Login</button>

                <p class="auth-switch">
                    New customer?
                    <a href="{{ route('register') }}">Create an account</a>
                </p>
            </form>
        </section>
    </main>
@endsection
