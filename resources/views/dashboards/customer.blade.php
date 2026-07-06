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
                <span>Bank account</span>
                <strong>Status: {{ $account ? ucfirst($account->status) : 'Not Created' }}</strong>
                <small>{{ $account ? ucfirst($account->account_type) . ' account' : 'Awaiting account creation' }}</small>
            </div>
        </section>

        <section class="dashboard-grid" aria-label="Customer overview">
            <article class="dashboard-card">
                <p class="card-kicker">Profile</p>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Account</p>
                <h2>{{ $account ? $account->account_number : 'Awaiting account' }}</h2>
                <p>{{ $account ? 'Balance BDT ' . number_format((float) $account->balance, 2) : 'An employee will create your account after approval.' }}</p>
                @if ($account?->isActive())
                    <a class="dashboard-link" href="{{ route('customer.account.transactions') }}">Deposit / Withdraw</a>
                @endif
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Transfers</p>
                <h2>Request transfer</h2>
                <p>Submit a transfer request for employee review and track pending requests.</p>
                @if ($account?->isActive())
                    <a class="dashboard-link" href="{{ route('customer.transfers.index') }}">Open Transfers</a>
                @endif
            </article>
        </section>

    </main>
@endsection
