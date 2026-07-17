@extends('layouts.app')

@section('title', config('bank.name') . ' | Employee Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <main class="dashboard-page">
        <section class="dashboard-hero" aria-labelledby="dashboard-title">
            <div>
                <p class="eyebrow">Employee dashboard</p>
                <h1 id="dashboard-title">Good day, {{ $user->name }}.</h1>
            </div>

            <div class="identity-panel" aria-label="Employee profile">
                <span>Employee code</span>
                <strong>{{ $user->employee_code ?: 'Not assigned' }}</strong>
                <small>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</small>
            </div>
        </section>

        <section class="dashboard-grid" aria-label="Employee overview">
            <article class="dashboard-card">
                <p class="card-kicker">Approvals</p>
                <a class="dashboard-link" href="{{ route('employee.customers.pending') }}">Review customers</a>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Accounts</p>
                <a class="dashboard-link" href="{{ route('employee.accounts.index') }}">Manage customer accounts</a>
                <a class="dashboard-link dashboard-link-muted" href="{{ route('employee.transactions.index') }}">Search transactions</a>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Transfers</p>
                <a class="dashboard-link" href="{{ route('employee.transfers.index') }}">Review transfers</a>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">ATM Cards</p>
                <a class="dashboard-link" href="{{ route('employee.card-requests.index') }}">Review card requests</a>
                <a class="dashboard-link dashboard-link-muted" href="{{ route('employee.atm-cards.index') }}">Manage issued cards</a>
            </article>
        </section>
    </main>
@endsection
