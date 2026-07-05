@extends('layouts.app')

@section('title', config('bank.name') . ' | Confirm Transfer Request')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Confirm transfer</p>
                <h1>Review before submitting</h1>
                <p>The request will stay pending until an employee approves or rejects it.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('customer.transfers.create') }}">Edit Details</a>
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @error('account')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="approval-grid">
            <article class="management-card">
                <p class="eyebrow">Sender</p>
                <h2>{{ $account->customer->name }}</h2>
                <dl class="detail-list single-column">
                    <div>
                        <dt>Account number</dt>
                        <dd>{{ $account->account_number }}</dd>
                    </div>
                    <div>
                        <dt>Available balance</dt>
                        <dd>BDT {{ number_format((float) $account->balance, 2) }}</dd>
                    </div>
                </dl>
            </article>

            <form class="management-card" method="POST" action="{{ route('customer.transfers.store') }}">
                @csrf
                <input type="hidden" name="receiver_account_number" value="{{ $receiverAccount->account_number }}">
                <input type="hidden" name="amount" value="{{ $amount }}">

                <p class="eyebrow">Receiver</p>
                <h2>{{ $receiverAccount->customer->name }}</h2>
                <dl class="detail-list single-column">
                    <div>
                        <dt>Account number</dt>
                        <dd>{{ $receiverAccount->account_number }}</dd>
                    </div>
                    <div>
                        <dt>Branch</dt>
                        <dd>{{ $receiverAccount->branch->name }} · {{ $receiverAccount->branch->branch_code }}</dd>
                    </div>
                    <div>
                        <dt>Amount</dt>
                        <dd>BDT {{ number_format((float) $amount, 2) }}</dd>
                    </div>
                </dl>

                <div class="form-actions">
                    <button class="button" type="submit">Submit Request</button>
                    <a class="button-muted" href="{{ route('customer.transfers.create') }}">Cancel</a>
                </div>
            </form>
        </section>
    </main>
@endsection
