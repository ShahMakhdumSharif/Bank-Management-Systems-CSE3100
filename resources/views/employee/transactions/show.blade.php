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
                <p>{{ $transaction->account->customer->name }} · {{ $transaction->account->account_number }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.transactions.index') }}">Transactions</a>
                <a class="button-muted" href="{{ route('employee.accounts.show', $transaction->account) }}">Account</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        <section class="approval-grid">
            <article class="management-card">
                <p class="eyebrow">Financial record</p>
                <h2>{{ $transaction->typeLabel() }}</h2>

                <dl class="detail-list">
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
                        <dt>Created</dt>
                        <dd>{{ $transaction->created_at->format('M d, Y h:i A') }}</dd>
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
            </article>

            <article class="management-card">
                <p class="eyebrow">Customer and account</p>
                <h2>{{ $transaction->account->customer->name }}</h2>

                <dl class="detail-list single-column">
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $transaction->account->customer->email }}</dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $transaction->account->customer->phone }}</dd>
                    </div>
                    <div>
                        <dt>Account</dt>
                        <dd>{{ $transaction->account->account_number }} · {{ ucfirst($transaction->account->account_type) }}</dd>
                    </div>
                    <div>
                        <dt>Branch</dt>
                        <dd>{{ $transaction->account->branch->name }} · {{ $transaction->account->branch->branch_code }}</dd>
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
                        <dt>Transfer request</dt>
                        <dd>
                            @if ($transaction->transferRequest)
                                #{{ $transaction->transferRequest->id }} · {{ ucfirst($transaction->transferRequest->status) }}
                            @else
                                Not applicable
                            @endif
                        </dd>
                    </div>
                </dl>
            </article>
        </section>
    </main>
@endsection
