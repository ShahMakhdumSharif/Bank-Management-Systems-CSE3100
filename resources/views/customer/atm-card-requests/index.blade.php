@extends('layouts.app')

@section('title', config('bank.name') . ' | ATM Card Requests')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">ATM cards</p>
                <h1>ATM-card request history</h1>
                <p>{{ $account->account_number }} · {{ ucfirst($account->status) }}</p>
            </div>
            <div class="action-row">
                <form method="POST" action="{{ route('customer.card-requests.store') }}">
                    @csrf
                    <button class="button" type="submit">Request ATM Card</button>
                </form>
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('account')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        @error('card')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="management-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Handled By</th>
                        <th>Decision</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cardRequests as $cardRequest)
                        <tr>
                            <td>{{ $account->account_number }}</td>
                            <td><span class="status-pill {{ $cardRequest->status }}">{{ ucfirst($cardRequest->status) }}</span></td>
                            <td>{{ optional($cardRequest->requested_at)->format('M d, Y h:i A') ?: $cardRequest->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ optional($cardRequest->handler)->name ?: 'Pending' }}</td>
                            <td>{{ $cardRequest->rejection_reason ?: 'Awaiting review' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No ATM-card requests yet.</td>
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
