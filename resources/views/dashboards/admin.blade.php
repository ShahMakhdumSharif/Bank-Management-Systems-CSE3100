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
                <a class="dashboard-link" href="{{ route('admin.audit-logs.index') }}">View Audit Logs</a>
            </article>
        </section>

        <section class="analytics-grid" aria-label="Bank analytics">
            <article class="analytics-card">
                <p class="card-kicker">Customers</p>
                <strong>{{ number_format($analytics['customers']['total']) }}</strong>
                <dl class="analytics-list">
                    <div>
                        <dt>Approved</dt>
                        <dd>{{ number_format($analytics['customers']['approved']) }}</dd>
                    </div>
                    <div>
                        <dt>Pending</dt>
                        <dd>{{ number_format($analytics['customers']['pending']) }}</dd>
                    </div>
                    <div>
                        <dt>Rejected</dt>
                        <dd>{{ number_format($analytics['customers']['rejected']) }}</dd>
                    </div>
                </dl>
            </article>

            <article class="analytics-card">
                <p class="card-kicker">Accounts</p>
                <strong>{{ number_format($analytics['accounts']['total']) }}</strong>
                <dl class="analytics-list">
                    <div>
                        <dt>Active</dt>
                        <dd>{{ number_format($analytics['accounts']['active']) }}</dd>
                    </div>
                    <div>
                        <dt>Frozen</dt>
                        <dd>{{ number_format($analytics['accounts']['frozen']) }}</dd>
                    </div>
                    <div>
                        <dt>Total balance</dt>
                        <dd>BDT {{ number_format((float) $analytics['accounts']['totalBalance'], 2) }}</dd>
                    </div>
                </dl>
            </article>

            <article class="analytics-card">
                <p class="card-kicker">Deposit totals</p>
                <strong>BDT {{ number_format((float) $analytics['deposits']['completedTotal'], 2) }}</strong>
                <dl class="analytics-list">
                    <div>
                        <dt>Completed deposits</dt>
                        <dd>{{ number_format($analytics['deposits']['completedCount']) }}</dd>
                    </div>
                </dl>
            </article>
        </section>

        <section class="dashboard-panel analytics-panel" aria-labelledby="employee-performance-title">
            <div>
                <p class="eyebrow">Employee performance</p>
                <h2 id="employee-performance-title">Processing activity</h2>
                <p>Counts are based on audit logs created by employee banking actions.</p>
            </div>

            <div class="dashboard-table-wrap analytics-table-wrap">
                <table class="dashboard-table dashboard-table-compact">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Customers</th>
                            <th>Transfers</th>
                            <th>ATM cards</th>
                            <th>Freeze / unfreeze</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($analytics['employeePerformance'] as $employee)
                            <tr>
                                <td>
                                    {{ $employee->name }}
                                    <br>
                                    <small>{{ $employee->email }}</small>
                                </td>
                                <td>{{ number_format($employee->customer_decisions) }}</td>
                                <td>{{ number_format($employee->transfer_decisions) }}</td>
                                <td>{{ number_format($employee->atm_card_actions) }}</td>
                                <td>{{ number_format($employee->account_status_actions) }}</td>
                                <td>{{ number_format($employee->total_actions) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No employee actions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
