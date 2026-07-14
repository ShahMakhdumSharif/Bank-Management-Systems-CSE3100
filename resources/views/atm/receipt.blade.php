@extends('layouts.app')

@section('title', config('bank.name') . ' | ATM Receipt')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">ATM receipt</p>
                <h1>Transaction complete</h1>
                <p>{{ $transaction->reference }} · {{ $card->maskedCardNumber() }}</p>
            </div>
            <div class="action-row">
                <a class="button" href="{{ route('atm.session') }}">Back to ATM</a>
                <form method="POST" action="{{ route('atm.logout') }}">
                    @csrf
                    <button class="button-danger" type="submit">Logout</button>
                </form>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-card receipt-card">
            <p class="eyebrow">Receipt details</p>
            <h2>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</h2>

            <dl class="detail-list">
                <div>
                    <dt>Reference</dt>
                    <dd>{{ $transaction->reference }}</dd>
                </div>
                <div>
                    <dt>Date</dt>
                    <dd>{{ $transaction->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt>Amount</dt>
                    <dd>BDT {{ number_format((float) $transaction->amount, 2) }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd><span class="status-pill {{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></dd>
                </div>
                <div>
                    <dt>Balance before</dt>
                    <dd>BDT {{ number_format((float) $transaction->balance_before, 2) }}</dd>
                </div>
                <div>
                    <dt>Balance after</dt>
                    <dd>BDT {{ number_format((float) $transaction->balance_after, 2) }}</dd>
                </div>
            </dl>
        </section>
    </main>
@endsection
