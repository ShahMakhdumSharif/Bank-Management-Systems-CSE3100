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
                <p>
                    Review customer applications, manage account requests, and support branch banking operations.
                </p>
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
                <h2>Customer queue</h2>
                <p>Pending customers will be listed here once approval workflows are added.</p>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Accounts</p>
                <h2>Account service</h2>
                <p>Search, freeze, unfreeze, and inspect accounts from this area in upcoming milestones.</p>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Transfers</p>
                <h2>Transfer review</h2>
                <p>Transfer approval and rejection queues will connect to this dashboard later.</p>
            </article>
        </section>

        <section class="dashboard-panel" aria-labelledby="employee-focus-title">
            <div>
                <p class="eyebrow">Current focus</p>
                <h2 id="employee-focus-title">Branch banking operations</h2>
            </div>
            <p>
                This protected area is ready for employee-only tools while the database layer is built out.
            </p>
        </section>
    </main>
@endsection
