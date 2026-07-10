@extends('layouts.app')

@section('title', config('bank.name') . ' | Virtual ATM')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <section class="login-shell">
            <div class="login-copy">
                <p class="eyebrow">Virtual ATM</p>
                <h1>Use your ATM card securely.</h1>
                <p>Sign in with your issued card number and PIN to start a protected ATM session.</p>
                <div class="login-highlights" aria-label="ATM security highlights">
                </div>
            </div>

            <form class="login-card" method="POST" action="{{ route('atm.login.store') }}">
                @csrf
                <p class="eyebrow form-eyebrow">ATM login</p>

                @if (session('status'))
                    <p class="auth-status">{{ session('status') }}</p>
                @endif

                @if (session('error'))
                    <p class="field-error">{{ session('error') }}</p>
                @endif

                <label for="card_number">Card number</label>
                <input id="card_number" name="card_number" type="text" inputmode="numeric" maxlength="16" value="{{ old('card_number') }}" required autofocus>
                @error('card_number') <p class="field-error">{{ $message }}</p> @enderror

                <label for="pin">PIN</label>
                <input id="pin" name="pin" type="password" inputmode="numeric" maxlength="4" required>
                @error('pin') <p class="field-error">{{ $message }}</p> @enderror

                <button class="login-button" type="submit">Enter ATM</button>

                <p class="auth-switch">
                    <a href="{{ route('home') }}#services">Back to online services</a>
                </p>
            </form>
        </section>
    </main>
@endsection
