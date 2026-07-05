@extends('layouts.app')

@section('title', config('bank.name') . ' | Transfer Requests')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Transfers</p>
                <h1>Transfer request history</h1>
                <p>{{ $account->account_number }} · Balance BDT {{ number_format((float) $account->balance, 2) }}</p>
            </div>
            <div class="action-row">
                <a class="button" href="{{ route('customer.transfers.create') }}">New Transfer</a>
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('transfer')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="management-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Receiver</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Handled By</th>
                        <th>Action</th>
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
                            <td><span class="status-pill {{ $transfer->status }}">{{ ucfirst($transfer->status) }}</span></td>
                            <td>{{ optional($transfer->requested_at)->format('M d, Y h:i A') ?: $transfer->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ optional($transfer->handler)->name ?: 'Pending' }}</td>
                            <td>
                                @if ($transfer->status === \App\Models\TransferRequest::STATUS_PENDING)
                                    <form method="POST" action="{{ route('customer.transfers.cancel', $transfer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="button-danger" type="submit">Cancel</button>
                                    </form>
                                @else
                                    <span>Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No transfer requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $transferRequests->links() }}
            </div>
        </section>
    </main>
@endsection
