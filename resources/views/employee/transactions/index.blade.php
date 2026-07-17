@extends('layouts.app')

@section('title', config('bank.name') . ' | Transaction Search')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Transaction history</p>
                <h1>Search transactions</h1>
                <p>Find customer activity by account, customer, reference, source, status, or date.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.accounts.index') }}">Accounts</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <form class="search-form" method="GET" action="{{ route('employee.transactions.index') }}">
                <input name="search" type="search" value="{{ $filters['search'] }}" placeholder="Reference, customer, phone, email, or account">

                <select name="type" aria-label="Transaction type">
                    <option value="all" @selected($filters['type'] === 'all')>All types</option>
                    @foreach ($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="status" aria-label="Transaction status">
                    <option value="all" @selected($filters['status'] === 'all')>All statuses</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="source" aria-label="Transaction source">
                    <option value="all" @selected($filters['source'] === 'all')>All sources</option>
                    @foreach ($sourceOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['source'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="account_status" aria-label="Account status">
                    <option value="all" @selected($filters['account_status'] === 'all')>All account statuses</option>
                    @foreach ($accountStatusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['account_status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <input name="from" type="date" value="{{ $filters['from'] }}" aria-label="From date">
                <input name="to" type="date" value="{{ $filters['to'] }}" aria-label="To date">

                <button class="button-muted" type="submit">Apply</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->reference }}</td>
                            <td>
                                <strong>{{ $transaction->account->customer->name }}</strong><br>
                                <span>{{ $transaction->account->customer->email }}</span>
                            </td>
                            <td>
                                {{ $transaction->account->account_number }}<br>
                                <span>{{ ucfirst($transaction->account->status) }} · {{ $transaction->account->branch->name }}</span>
                            </td>
                            <td>{{ $transaction->typeLabel() }}</td>
                            <td><span class="status-pill {{ $transaction->status }}">{{ $transaction->statusLabel() }}</span></td>
                            <td>BDT {{ number_format((float) $transaction->amount, 2) }}</td>
                            <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <a class="button-muted" href="{{ route('employee.transactions.show', $transaction) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $transactions->links() }}
            </div>
        </section>
    </main>
@endsection
