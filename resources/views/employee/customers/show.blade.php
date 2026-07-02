@extends('layouts.app')

@section('title', config('bank.name') . ' | Customer Review')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Customer review</p>
                <h1>{{ $customer->name }}</h1>
                <p>{{ $customer->email }} · {{ $customer->phone }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('employee.customers.pending') }}">Pending Queue</a>
                <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        @error('customer')
            <p class="flash-error">{{ $message }}</p>
        @enderror

        <section class="management-card">
            <dl class="detail-list">
                <div>
                    <dt>Status</dt>
                    <dd><span class="status-pill {{ $customer->status }}">{{ ucfirst($customer->status) }}</span></dd>
                </div>
                <div>
                    <dt>Applied</dt>
                    <dd>{{ $customer->created_at->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt>Role</dt>
                    <dd>{{ ucfirst($customer->role) }}</dd>
                </div>
                <div>
                    <dt>Accounts</dt>
                    <dd>{{ $customer->accounts->count() }}</dd>
                </div>
            </dl>
        </section>

        @if ($customer->status === App\Models\User::STATUS_PENDING)
            <section class="approval-grid">
                <form class="management-panel" method="POST" action="{{ route('employee.customers.approve', $customer) }}">
                    @csrf
                    <p class="eyebrow">Approve</p>
                    <h2>Create customer account</h2>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="branch_id">Branch</label>
                            <select id="branch_id" name="branch_id" required>
                                <option value="">Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>
                                        {{ $branch->name }} · {{ $branch->branch_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-field">
                            <label for="account_type">Account type</label>
                            <select id="account_type" name="account_type" required>
                                <option value="{{ App\Models\Account::TYPE_SAVINGS }}" @selected(old('account_type') === App\Models\Account::TYPE_SAVINGS)>Savings</option>
                                <option value="{{ App\Models\Account::TYPE_CURRENT }}" @selected(old('account_type') === App\Models\Account::TYPE_CURRENT)>Current</option>
                            </select>
                            @error('account_type') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit">Approve and Create Account</button>
                    </div>
                </form>

                <form class="danger-panel" method="POST" action="{{ route('employee.customers.reject', $customer) }}">
                    @csrf
                    <p class="eyebrow">Reject</p>
                    <h2>Reject application</h2>
                    <div class="form-field">
                        <label for="rejection_reason">Reason</label>
                        <textarea id="rejection_reason" name="rejection_reason" required>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-actions">
                        <button class="button-danger" type="submit">Reject Customer</button>
                    </div>
                </form>
            </section>
        @else
            <section class="management-card">
                <p class="eyebrow">Review result</p>
                <h2>Account and audit history</h2>

                <ul class="assigned-list">
                    @forelse ($customer->accounts as $account)
                        <li>
                            <span>{{ $account->account_number }} · {{ ucfirst($account->account_type) }}</span>
                            <span>{{ $account->branch->name }}</span>
                        </li>
                    @empty
                        <li>No account was created for this customer.</li>
                    @endforelse
                </ul>

                <ul class="assigned-list">
                    @forelse ($customer->subjectActions as $action)
                        <li>
                            <span>{{ ucfirst(str_replace('_', ' ', $action->action_type)) }}</span>
                            <span>{{ $action->employee->name }}</span>
                        </li>
                    @empty
                        <li>No audit actions recorded.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </main>
@endsection
