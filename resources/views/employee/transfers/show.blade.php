@extends('layouts.app')

@section('title', config('bank.name') . ' | Transfer Review')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Transfer review</p>
                <h1>BDT {{ number_format((float) $transfer->amount, 2) }}</h1>
                <p>{{ ucfirst($transfer->status) }} request #{{ $transfer->id }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.transfers.index') }}">Transfer Queue</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('transfer')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="approval-grid">
            <article class="management-card">
                <p class="eyebrow">Transfer details</p>
                <h2>Sender and receiver</h2>

                <dl class="detail-list">
                    <div>
                        <dt>Sender</dt>
                        <dd>{{ $transfer->senderAccount->customer->name }}</dd>
                    </div>
                    <div>
                        <dt>Sender account</dt>
                        <dd>{{ $transfer->senderAccount->account_number }}</dd>
                    </div>
                    <div>
                        <dt>Sender status</dt>
                        <dd><span class="status-pill {{ $transfer->senderAccount->status }}">{{ ucfirst($transfer->senderAccount->status) }}</span></dd>
                    </div>
                    <div>
                        <dt>Sender balance</dt>
                        <dd>BDT {{ number_format((float) $transfer->senderAccount->balance, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Receiver</dt>
                        <dd>{{ $transfer->receiverAccount->customer->name }}</dd>
                    </div>
                    <div>
                        <dt>Receiver account</dt>
                        <dd>{{ $transfer->receiverAccount->account_number }}</dd>
                    </div>
                    <div>
                        <dt>Receiver status</dt>
                        <dd><span class="status-pill {{ $transfer->receiverAccount->status }}">{{ ucfirst($transfer->receiverAccount->status) }}</span></dd>
                    </div>
                    <div>
                        <dt>Receiver balance</dt>
                        <dd>BDT {{ number_format((float) $transfer->receiverAccount->balance, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Requested</dt>
                        <dd>{{ optional($transfer->requested_at)->format('M d, Y h:i A') ?: $transfer->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt>Handled by</dt>
                        <dd>{{ optional($transfer->handler)->name ?: 'Pending review' }}</dd>
                    </div>
                </dl>
            </article>

            @if ($transfer->status === \App\Models\TransferRequest::STATUS_PENDING)
                <article class="management-card">
                    <p class="eyebrow">Decision</p>
                    <h2>Approve or reject</h2>

                    <form method="POST" action="{{ route('employee.transfers.approve', $transfer) }}">
                        @csrf
                        <div class="form-actions">
                            <button class="button" type="submit">Approve Transfer</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('employee.transfers.reject', $transfer) }}">
                        @csrf
                        <div class="form-field">
                            <label for="rejection_reason">Rejection reason</label>
                            <textarea id="rejection_reason" name="rejection_reason" required>{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-actions">
                            <button class="button-danger" type="submit">Reject Transfer</button>
                        </div>
                    </form>
                </article>
            @else
                <article class="management-card">
                    <p class="eyebrow">Decision completed</p>
                    <h2>{{ ucfirst($transfer->status) }}</h2>
                    <p>{{ $transfer->rejection_reason ?: 'No rejection reason recorded.' }}</p>
                </article>
            @endif
        </section>
    </main>
@endsection
