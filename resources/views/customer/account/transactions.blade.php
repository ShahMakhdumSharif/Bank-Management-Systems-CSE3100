@extends('layouts.app')

@section('title', config('bank.name') . ' | Deposit and Withdraw')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Account activity</p>
                <h1>Deposit or withdraw</h1>
                <p>{{ $account->account_number }} · Balance BDT {{ number_format((float) $account->balance, 2) }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
                <a class="button-muted" href="{{ route('customer.transfers.index') }}">Transfers</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('account')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="approval-grid">
            <form id="deposit-form" class="management-card" method="POST" action="{{ route('customer.account.deposit') }}">
                @csrf
                <p class="eyebrow">Deposit</p>
                <h2>Add money</h2>
                <div class="form-field">
                    <label for="deposit_amount">Amount</label>
                    <input id="deposit_amount" name="amount" type="number" min="1" max="999999999.99" step="0.01" required>
                    @error('amount') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-actions">
                    <button class="button" type="submit">Deposit</button>
                </div>
            </form>

            <form id="withdraw-form" class="management-card" method="POST" action="{{ route('customer.account.withdraw') }}">
                @csrf
                <p class="eyebrow">Withdraw</p>
                <h2>Take money out</h2>
                <div class="form-field">
                    <label for="withdraw_amount">Amount</label>
                    <input id="withdraw_amount" name="amount" type="number" min="1" max="999999999.99" step="0.01" required>
                    @error('amount') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-actions">
                    <button class="button-danger" type="submit">Withdraw</button>
                </div>
            </form>
        </section>

        <section class="management-card account-history">
            <p class="eyebrow">Recent activity</p>
            <h2>Latest transactions</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($account->transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->reference }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</td>
                            <td>BDT {{ number_format((float) $transaction->amount, 2) }}</td>
                            <td>BDT {{ number_format((float) $transaction->balance_after, 2) }}</td>
                            <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No account transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
@endsection
