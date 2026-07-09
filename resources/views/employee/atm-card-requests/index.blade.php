@extends('layouts.app')

@section('title', config('bank.name') . ' | ATM Card Request Queue')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">ATM card queue</p>
                <h1>Pending ATM-card requests</h1>
                <p>Review card applications before approval and card generation in the next stage.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.atm-cards.index') }}">Issued Cards</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Account</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cardRequests as $cardRequest)
                        <tr>
                            <td>
                                {{ $cardRequest->account->customer->name }}<br>
                                <small>{{ $cardRequest->account->customer->email }}</small>
                            </td>
                            <td>
                                {{ $cardRequest->account->account_number }}<br>
                                <small>{{ ucfirst($cardRequest->account->status) }}</small>
                            </td>
                            <td>{{ $cardRequest->account->branch->name }}</td>
                            <td><span class="status-pill {{ $cardRequest->status }}">{{ ucfirst($cardRequest->status) }}</span></td>
                            <td>{{ optional($cardRequest->requested_at)->format('M d, Y h:i A') ?: $cardRequest->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <a class="button-muted" href="{{ route('employee.card-requests.show', $cardRequest) }}">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No pending ATM-card requests.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $cardRequests->links() }}
            </div>
        </section>
    </main>
@endsection
