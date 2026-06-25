@extends('layouts.app')

@section('title', config('bank.name') . ' | Customer Registration')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <section class="login-shell" aria-labelledby="register-title">
            <div class="login-copy">
                <p class="eyebrow">Customer registration</p>
                <h1 id="register-title">Apply for a secure banking profile.</h1>
                <p>
                    New customers are registered with pending status until an employee reviews and approves the account.
                </p>
                <div class="login-highlights" aria-label="Registration highlights">
                    <span>Verification Phase</span>
                </div>
            </div>

            <form class="login-card" method="POST" action="{{ route('register') }}">
                @csrf

                <label for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="phone">Phone number</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" autocomplete="tel" required>
                @error('phone')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

                <button class="login-button" type="submit">Register as Customer</button>

                <p class="auth-switch">
                    Already registered?
                    <a href="{{ route('login') }}">Login instead</a>
                </p>
            </form>
        </section>
    </main>
@endsection
