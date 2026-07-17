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
            </div>

            <div class="identity-panel" aria-label="Account status">
                <span>Bank account</span>
                <strong>Status: {{ $account ? ucfirst($account->status) : 'Not Created' }}</strong>
                <small>{{ $account ? ucfirst($account->account_type) . ' account' : 'Awaiting account creation' }}</small>
            </div>
        </section>

        <section class="dashboard-grid" aria-label="Customer overview">
            <article class="dashboard-card dashboard-card-full">
                <p class="card-kicker">Account</p>
                <div class="dashboard-table-wrap">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Account No.</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($account)
                                <tr>
                                    <td>{{ $account->account_number }}</td>
                                    <td>BDT {{ number_format((float) $account->balance, 2) }}</td>
                                    <td>{{ ucfirst($account->status) }}</td>
                                    <td>
                                        <div class="dashboard-actions" aria-label="Account actions">
                                            <a class="dashboard-link" href="{{ route('customer.account.transactions') }}">History</a>
                                            <a class="dashboard-link" href="{{ route('customer.account.transactions') }}#deposit-form">Deposit</a>
                                            <a class="dashboard-link dashboard-link-muted" href="{{ route('customer.account.transactions') }}#withdraw-form">Withdraw</a>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4">An employee will create your account after approval.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="dashboard-card dashboard-card-full">
                <p class="card-kicker">Transfers</p>
                <div class="dashboard-table-wrap">
                    <table class="dashboard-table dashboard-table-compact">
                        <thead>
                            <tr>
                                <th>Receiver</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transferRequests as $transfer)
                                <tr>
                                    <td>
                                        {{ $transfer->receiverAccount->customer->name }}<br>
                                        <small>{{ $transfer->receiverAccount->account_number }}</small>
                                    </td>
                                    <td>BDT {{ number_format((float) $transfer->amount, 2) }}</td>
                                    <td>{{ ucfirst($transfer->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No transfer requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($account)
                    <a class="dashboard-link" href="{{ route('customer.transfers.create') }}">New Transfer</a>
                @endif
            </article>

            <article class="dashboard-card dashboard-card-full">
                <p class="card-kicker">ATM Cards</p>
                <div class="dashboard-table-wrap">
                    <table class="dashboard-table dashboard-table-compact">
                        <thead>
                            <tr>
                                <th>Requested</th>
                                <th>Status</th>
                                <th>Handled By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cardRequests as $cardRequest)
                                <tr>
                                    <td>{{ optional($cardRequest->requested_at)->format('M d, Y h:i A') ?: $cardRequest->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ ucfirst($cardRequest->status) }}</td>
                                    <td>{{ optional($cardRequest->handler)->name ?: 'Pending' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No ATM-card requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($account)
                    <a class="dashboard-link" href="{{ route('customer.card-requests.index') }}">Request ATM Card</a>
                @endif
            </article>

            @if ($account)
                <article class="dashboard-card dashboard-card-full">
                    <p class="card-kicker">Foreign Exchange</p>
                    <h2>Currency converter</h2>
                    <p>Convert BDT and supported foreign currencies through the secure backend API.</p>
                    <a class="dashboard-link" href="{{ route('customer.currency-exchange.index') }}">Open Converter</a>
                </article>
            @endif
        </section>

    </main>
@endsection
