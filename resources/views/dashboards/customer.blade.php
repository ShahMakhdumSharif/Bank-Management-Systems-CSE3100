@extends('layouts.app')

@section('title', config('bank.name') . ' | Customer Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <main class="dashboard-page">
        <section class="dashboard-hero" aria-labelledby="dashboard-title">
            <div>
                <p class="eyebrow">Customer dashboard</p>
                <h1 id="dashboard-title">Welcome, {{ $user->name }}.</h1>
                <p>
                    Your profile is ready. Banking tools will unlock as employees approve applications and connect accounts.
                </p>
            </div>

            <div class="identity-panel" aria-label="Account status">
                <span>Application status</span>
                <strong>{{ ucfirst($user->status) }}</strong>
                <small>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</small>
            </div>
        </section>

        <section class="dashboard-grid" aria-label="Customer overview">
            <article class="dashboard-card">
                <p class="card-kicker">Profile</p>
                <h2>Application submitted</h2>
                <p>Your customer profile is stored with pending status until an employee reviews it.</p>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Account</p>
                <h2>Account setup pending</h2>
                <p>After approval, a banking account can be created and connected to your dashboard.</p>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Transfers</p>
                <h2>Transfer tools coming</h2>
                <p>Transfer requests and transaction history will appear after account activation.</p>
            </article>
        </section>

        <section class="dashboard-panel" aria-labelledby="customer-next-title">
            <div>
                <p class="eyebrow">Next step</p>
                <h2 id="customer-next-title">Wait for verification</h2>
            </div>
            <p>
                Keep your login details safe. Bank authority will verify your application.
            </p>
        </section>
    </main>
@endsection
