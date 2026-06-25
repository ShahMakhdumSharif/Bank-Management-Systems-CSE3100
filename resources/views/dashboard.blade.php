@extends('layouts.app')

@section('title', config('bank.name') . ' | Customer Status')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <section class="login-shell" aria-labelledby="dashboard-title">
            <div class="login-copy">
                <p class="eyebrow">Customer dashboard</p>
                <h1 id="dashboard-title">Welcome, {{ $user->name }}.</h1>
                <p>
                    Your profile is created. Employee approval and full dashboard routing will be added in the next milestone.
                </p>
                <div class="login-highlights" aria-label="Account status">
                    <span>Role: {{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                    <span>Status: {{ ucfirst($user->status) }}</span>
                </div>
            </div>

            <section class="login-card" aria-label="Current account status">
                <p class="eyebrow form-eyebrow">Application status</p>
                <h2 class="status-title">{{ ucfirst($user->status) }}</h2>
                <p class="status-copy">
                    Your customer account is waiting for review. Once approved, banking account services and transaction features can be enabled.
                </p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="login-button" type="submit">Logout Securely</button>
                </form>
            </section>
        </section>
    </main>
@endsection
