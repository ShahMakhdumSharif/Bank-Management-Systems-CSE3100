@extends('layouts.app')

@section('title', config('bank.name') . ' | Issued ATM Cards')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Issued cards</p>
                <h1>ATM card management</h1>
                <p>Block or unblock issued ATM cards.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.card-requests.index') }}">Card Queue</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('card')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="management-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Card</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th>Expires</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cards as $card)
                        <tr>
                            <td>{{ $card->account->customer->name }}</td>
                            <td>{{ $card->card_number }}</td>
                            <td>{{ $card->account->account_number }}</td>
                            <td><span class="status-pill {{ $card->status }}">{{ ucfirst($card->status) }}</span></td>
                            <td>{{ optional($card->expires_at)->format('M d, Y') ?: 'Not set' }}</td>
                            <td>
                                @if ($card->status === \App\Models\ATMCard::STATUS_ACTIVE)
                                    <form method="POST" action="{{ route('employee.atm-cards.block', $card) }}">
                                        @csrf
                                        <button class="button-danger" type="submit">Block</button>
                                    </form>
                                @elseif ($card->status === \App\Models\ATMCard::STATUS_BLOCKED)
                                    <form method="POST" action="{{ route('employee.atm-cards.unblock', $card) }}">
                                        @csrf
                                        <button class="button" type="submit">Unblock</button>
                                    </form>
                                @else
                                    <span>Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No issued ATM cards yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $cards->links() }}
            </div>
        </section>
    </main>
@endsection
