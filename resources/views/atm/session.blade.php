@extends('layouts.app')

@section('title', config('bank.name') . ' | ATM Session')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Virtual ATM</p>
                <h1>ATM dashboard</h1>
                <p>{{ $account->customer->name }} · {{ $card->maskedCardNumber() }}</p>
            </div>
            <div class="action-row">
                <form method="POST" action="{{ route('atm.logout') }}">
                    @csrf
                    <button class="button-danger" type="submit">Logout</button>
                </form>
                <a class="button-muted" href="{{ route('home') }}#services">Online Services</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('account')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        @error('card')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="atm-dashboard-grid">
            <article class="atm-balance-panel">
                <div>
                    <p class="eyebrow">Available balance</p>
                    <h2>BDT {{ number_format((float) $account->balance, 2) }}</h2>
                    <p>{{ ucfirst($account->account_type) }} account · {{ $account->maskedAccountNumber() }}</p>
                </div>
                <span class="status-pill {{ $account->status }}">{{ ucfirst($account->status) }}</span>
            </article>

            <article class="management-card">
                <p class="eyebrow">Card details</p>
                <dl class="detail-list single-column">
                    <div>
                        <dt>Card number</dt>
                        <dd>{{ $card->maskedCardNumber() }}</dd>
                    </div>
                    <div>
                        <dt>Card status</dt>
                        <dd><span class="status-pill {{ $card->status }}">{{ ucfirst($card->status) }}</span></dd>
                    </div>
                    <div>
                        <dt>Expiry date</dt>
                        <dd>{{ optional($card->expires_at)->format('M d, Y') ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </article>

            <article class="management-card">
                <p class="eyebrow">Account profile</p>
                <dl class="detail-list single-column">
                    <div>
                        <dt>Account number</dt>
                        <dd>{{ $account->maskedAccountNumber() }}</dd>
                    </div>
                    <div>
                        <dt>Branch</dt>
                        <dd>{{ $account->branch->name }} · {{ $account->branch->city }}</dd>
                    </div>
                    <div>
                        <dt>Account type</dt>
                        <dd>{{ ucfirst($account->account_type) }}</dd>
                    </div>
                </dl>
            </article>

            <article class="management-card">
                <p class="eyebrow">Session activity</p>
                <dl class="detail-list single-column">
                    <div>
                        <dt>Authenticated at</dt>
                        <dd>{{ optional($authenticatedAt)->format('M d, Y h:i A') ?: 'Unavailable' }}</dd>
                    </div>
                    <div>
                        <dt>Last verified activity</dt>
                        <dd>{{ optional($lastActivityAt)->format('M d, Y h:i A') ?: 'Unavailable' }}</dd>
                    </div>
                    <div>
                        <dt>ATM access</dt>
                        <dd>Secure session active</dd>
                    </div>
                </dl>
            </article>

            <form id="atm-deposit-form" class="management-card atm-transaction-form" method="POST" action="{{ route('atm.deposit') }}">
                @csrf
                <p class="eyebrow">ATM deposit</p>
                <h2>Add cash</h2>
                <p>Limit BDT {{ number_format((float) config('bank.atm.deposit_limit'), 2) }} per transaction.</p>
                <div class="form-field">
                    <label for="deposit_amount">Amount</label>
                    <input id="deposit_amount" name="amount" type="number" min="1" max="{{ config('bank.atm.deposit_limit') }}" step="0.01" required>
                    @error('amount') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-actions">
                    <button class="button" type="submit">Deposit</button>
                </div>
            </form>

            <form id="atm-withdraw-form" class="management-card atm-transaction-form" method="POST" action="{{ route('atm.withdraw') }}">
                @csrf
                <p class="eyebrow">ATM withdrawal</p>
                <h2>Withdraw cash</h2>
                <p>Limit BDT {{ number_format((float) config('bank.atm.withdrawal_limit'), 2) }} per transaction.</p>
                <div class="form-field">
                    <label for="withdraw_amount">Amount</label>
                    <input id="withdraw_amount" name="amount" type="number" min="1" max="{{ config('bank.atm.withdrawal_limit') }}" step="0.01" required>
                    @error('amount') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-actions">
                    <button class="button-danger" type="submit">Withdraw</button>
                </div>
            </form>
        </section>
    </main>
@endsection
