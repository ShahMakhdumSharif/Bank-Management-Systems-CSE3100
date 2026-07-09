@extends('layouts.app')

@section('title', config('bank.name') . ' | ATM Card Request Review')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">ATM card review</p>
                <h1>{{ $cardRequest->account->customer->name }}</h1>
                <p>{{ $cardRequest->account->account_number }} · {{ ucfirst($cardRequest->status) }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.card-requests.index') }}">Card Queue</a>
                <a class="button-muted" href="{{ route('employee.atm-cards.index') }}">Issued Cards</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('card')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        @if ($issuedPin && optional($cardRequest->atmCard)->id === $issuedCardId)
            <section class="management-card account-history">
                <p class="eyebrow">Show PIN once</p>
                <h2>Temporary PIN: {{ $issuedPin }}</h2>
                <p>This PIN is not stored in plain text and will disappear after this page load.</p>
            </section>
        @endif

        <section class="approval-grid">
            <article class="management-card">
                <p class="eyebrow">Request details</p>
                <h2>Customer and account</h2>

                <dl class="detail-list">
                    <div>
                        <dt>Customer</dt>
                        <dd>{{ $cardRequest->account->customer->name }}</dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $cardRequest->account->customer->email }}</dd>
                    </div>
                    <div>
                        <dt>Account</dt>
                        <dd>{{ $cardRequest->account->account_number }}</dd>
                    </div>
                    <div>
                        <dt>Account status</dt>
                        <dd><span class="status-pill {{ $cardRequest->account->status }}">{{ ucfirst($cardRequest->account->status) }}</span></dd>
                    </div>
                    <div>
                        <dt>Branch</dt>
                        <dd>{{ $cardRequest->account->branch->name }}</dd>
                    </div>
                    <div>
                        <dt>Requested</dt>
                        <dd>{{ optional($cardRequest->requested_at)->format('M d, Y h:i A') ?: $cardRequest->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                </dl>

                @if ($cardRequest->atmCard)
                    <dl class="detail-list single-column">
                        <div>
                            <dt>Issued card</dt>
                            <dd>{{ $cardRequest->atmCard->card_number }} · {{ ucfirst($cardRequest->atmCard->status) }}</dd>
                        </div>
                        <div>
                            <dt>Expires</dt>
                            <dd>{{ optional($cardRequest->atmCard->expires_at)->format('M d, Y') ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                @endif
            </article>

            @if ($cardRequest->status === \App\Models\ATMCardRequest::STATUS_PENDING)
                <article class="management-card">
                    <p class="eyebrow">Decision</p>
                    <h2>Approve or reject</h2>

                    <form method="POST" action="{{ route('employee.card-requests.approve', $cardRequest) }}">
                        @csrf
                        <div class="form-actions">
                            <button class="button" type="submit">Approve and Issue Card</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('employee.card-requests.reject', $cardRequest) }}">
                        @csrf
                        <div class="form-field">
                            <label for="rejection_reason">Rejection reason</label>
                            <textarea id="rejection_reason" name="rejection_reason" required>{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-actions">
                            <button class="button-danger" type="submit">Reject Request</button>
                        </div>
                    </form>
                </article>
            @else
                <article class="management-card">
                    <p class="eyebrow">Decision completed</p>
                    <h2>{{ ucfirst($cardRequest->status) }}</h2>
                    <p>{{ $cardRequest->rejection_reason ?: 'No rejection reason recorded.' }}</p>
                </article>
            @endif
        </section>
    </main>
@endsection
