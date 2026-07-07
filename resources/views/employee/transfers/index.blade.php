@extends('layouts.app')

@section('title', config('bank.name') . ' | Transfer Queue')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Transfer queue</p>
                <h1>Pending transfer requests</h1>
                <p>Approve valid requests or reject them to return the reserved amount.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Amount</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transferRequests as $transfer)
                        <tr>
                            <td>
                                {{ $transfer->senderAccount->customer->name }}<br>
                                <small>{{ $transfer->senderAccount->account_number }}</small>
                            </td>
                            <td>
                                {{ $transfer->receiverAccount->customer->name }}<br>
                                <small>{{ $transfer->receiverAccount->account_number }}</small>
                            </td>
                            <td>BDT {{ number_format((float) $transfer->amount, 2) }}</td>
                            <td>{{ optional($transfer->requested_at)->format('M d, Y h:i A') ?: $transfer->created_at->format('M d, Y h:i A') }}</td>
                            <td><span class="status-pill {{ $transfer->status }}">{{ ucfirst($transfer->status) }}</span></td>
                            <td>
                                <a class="button-muted" href="{{ route('employee.transfers.show', $transfer) }}">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No pending transfer requests.</td>
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
