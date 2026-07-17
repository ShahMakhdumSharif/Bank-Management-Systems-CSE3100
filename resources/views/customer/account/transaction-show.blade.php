@extends('layouts.app')

@section('title', config('bank.name') . ' | Transaction Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Transaction details</p>
                <h1>{{ $transaction->reference }}</h1>
                <p>{{ $account->account_number }} · {{ $transaction->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('customer.account.transactions') }}">History</a>
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
            </div>
        </section>

        <section class="management-card receipt-card">
            <p class="eyebrow">Financial record</p>
            <h2>{{ $transaction->typeLabel() }}</h2>

            <dl class="detail-list single-column">
                <div>
                    <dt>Status</dt>
                    <dd><span class="status-pill {{ $transaction->status }}">{{ $transaction->statusLabel() }}</span></dd>
                </div>
                <div>
                    <dt>Source</dt>
                    <dd>{{ $transaction->sourceLabel() }}</dd>
                </div>
                <div>
                    <dt>Amount</dt>
                    <dd>BDT {{ number_format((float) $transaction->amount, 2) }}</dd>
                </div>
                <div>
                    <dt>Balance before</dt>
                    <dd>BDT {{ number_format((float) $transaction->balance_before, 2) }}</dd>
                </div>
                <div>
                    <dt>Balance after</dt>
                    <dd>BDT {{ number_format((float) $transaction->balance_after, 2) }}</dd>
                </div>
                <div>
                    <dt>Related account</dt>
                    <dd>
                        @if ($transaction->relatedAccount)
                            {{ $transaction->relatedAccount->account_number }} · {{ $transaction->relatedAccount->customer->name }}
                        @else
                            Not applicable
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Handled by</dt>
                    <dd>{{ optional($transaction->handler)->name ?: 'System' }}</dd>
                </div>
                <div>
                    <dt>Description</dt>
                    <dd>{{ $transaction->description ?: 'No description recorded.' }}</dd>
                </div>
            </dl>
        </section>
    </main>
@endsection
