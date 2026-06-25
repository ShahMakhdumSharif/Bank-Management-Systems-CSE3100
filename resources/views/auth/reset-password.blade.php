@extends('layouts.app')

@section('title', config('bank.name') . ' | Set New Password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <section class="login-shell" aria-labelledby="reset-title">
            <div class="login-copy">
                <p class="eyebrow">New password</p>
                <h1 id="reset-title">Create a fresh secure password.</h1>
                <p>
                    Use your reset token to update your password and return to secure dashboard access.
                </p>
            </div>

            <form class="login-card" method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

                <button class="login-button" type="submit">Save New Password</button>
            </form>
        </section>
    </main>
@endsection
