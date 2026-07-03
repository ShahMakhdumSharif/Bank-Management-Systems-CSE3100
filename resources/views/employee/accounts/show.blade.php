@extends('layouts.app')

@section('title', config('bank.name') . ' | Account Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Account details</p>
                <h1>{{ $account->customer->name }}</h1>
                <p>{{ $account->account_number }} · {{ ucfirst($account->account_type) }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.accounts.index') }}">Accounts</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('account')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="approval-grid">
            <article class="management-card">
                <p class="eyebrow">Customer overview</p>
                <h2>Profile and account</h2>

                <dl class="detail-list">
                    <div>
                        <dt>Name</dt>
                        <dd>{{ $account->customer->name }}</dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $account->customer->email }}</dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $account->customer->phone }}</dd>
                    </div>
                    <div>
                        <dt>Customer status</dt>
                        <dd><span class="status-pill {{ $account->customer->status }}">{{ ucfirst($account->customer->status) }}</span></dd>
                    </div>
                    <div>
                        <dt>Account status</dt>
                        <dd><span class="status-pill {{ $account->status }}">{{ ucfirst($account->status) }}</span></dd>
                    </div>
                    <div>
                        <dt>Balance</dt>
                        <dd>BDT {{ number_format((float) $account->balance, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Branch</dt>
                        <dd>{{ $account->branch->name }} · {{ $account->branch->branch_code }}</dd>
                    </div>
                    <div>
                        <dt>Approved</dt>
                        <dd>{{ optional($account->approved_at)->format('M d, Y') ?: 'Not recorded' }}</dd>
                    </div>
                </dl>
            </article>

            @if ($account->isActive())
                <form class="danger-panel" method="POST" action="{{ route('employee.accounts.freeze', $account) }}">
                    @csrf
                    <p class="eyebrow">Freeze account</p>
                    <h2>Restrict account activity</h2>
                    <div class="form-field">
                        <label for="freeze_reason">Freeze reason</label>
                        <textarea id="freeze_reason" name="freeze_reason" required>{{ old('freeze_reason') }}</textarea>
                        @error('freeze_reason') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-actions">
                        <button class="button-danger" type="submit">Freeze Account</button>
                    </div>
                </form>
            @elseif ($account->isFrozen())
                <form class="management-panel" method="POST" action="{{ route('employee.accounts.unfreeze', $account) }}">
                    @csrf
                    <p class="eyebrow">Frozen account</p>
                    <h2>Review freeze details</h2>

                    <dl class="detail-list single-column">
                        <div>
                            <dt>Reason</dt>
                            <dd>{{ $account->freeze_reason }}</dd>
                        </div>
                        <div>
                            <dt>Frozen by</dt>
                            <dd>{{ optional($account->freezer)->name ?: 'Not recorded' }}</dd>
                        </div>
                        <div>
                            <dt>Frozen at</dt>
                            <dd>{{ optional($account->frozen_at)->format('M d, Y h:i A') ?: 'Not recorded' }}</dd>
                        </div>
                    </dl>

                    <div class="form-actions">
                        <button class="button" type="submit">Unfreeze Account</button>
                    </div>
                </form>
            @endif
        </section>

        <section class="management-card account-history">
            <p class="eyebrow">Audit history</p>
            <h2>Account actions</h2>

            <ul class="assigned-list">
                @forelse ($account->subjectActions as $action)
                    <li>
                        <span>{{ ucfirst(str_replace('_', ' ', $action->action_type)) }}</span>
                        <span>{{ optional($action->employee)->name ?: 'System' }} · {{ $action->created_at->format('M d, Y') }}</span>
                    </li>
                @empty
                    <li>No account actions recorded.</li>
                @endforelse
            </ul>
        </section>
    </main>
@endsection
