@extends('layouts.app')

@section('title', config('bank.name') . ' | Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <main class="dashboard-page">
        <section class="dashboard-hero" aria-labelledby="dashboard-title">
            <div>
                <p class="eyebrow">Master admin dashboard</p>
                <h1 id="dashboard-title">Welcome back, {{ $user->name }}.</h1>
                <p>
                    Manage bank-wide users, branches, account approvals, and operational controls from one protected workspace.
                </p>
            </div>

            <div class="identity-panel" aria-label="Signed in user">
                <span>Signed in as</span>
                <strong>{{ $user->email }}</strong>
                <small>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</small>
            </div>
        </section>

        <section class="dashboard-grid" aria-label="Admin overview">
            <article class="dashboard-card">
                <p class="card-kicker">Users</p>
                <a class="dashboard-link" href="{{ route('admin.employees.index') }}">Manage employees</a>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Branches</p>
                <a class="dashboard-link" href="{{ route('admin.branches.index') }}">Manage branches</a>
            </article>

            <article class="dashboard-card">
                <p class="card-kicker">Audit</p>
                <h2>Action visibility</h2>
                <p>Employee actions and request history will be tracked as the workflow grows.</p>
            </article>
        </section>
    </main>
@endsection
