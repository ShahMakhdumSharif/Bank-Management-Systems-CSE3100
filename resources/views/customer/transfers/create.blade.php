@extends('layouts.app')

@section('title', config('bank.name') . ' | New Transfer Request')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Transfer request</p>
                <h1>Request a money transfer</h1>
                <p>From {{ $account->account_number }} · Balance BDT {{ number_format((float) $account->balance, 2) }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('customer.transfers.index') }}">Transfer History</a>
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('account')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="management-panel">
            <form method="POST" action="{{ route('customer.transfers.confirm') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-field">
                        <label for="receiver_account_number">Receiver account number</label>
                        <input id="receiver_account_number" name="receiver_account_number" value="{{ old('receiver_account_number') }}" required autofocus>
                        @error('receiver_account_number') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field">
                        <label for="amount">Amount</label>
                        <input id="amount" name="amount" type="number" min="1" max="999999999.99" step="0.01" value="{{ old('amount') }}" required>
                        @error('amount') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Review Transfer</button>
                </div>
            </form>
        </section>
    </main>
@endsection
