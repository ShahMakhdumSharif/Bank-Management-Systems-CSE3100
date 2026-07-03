@extends('layouts.app')

@section('title', config('bank.name') . ' | Account Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Account management</p>
                <h1>Customer accounts</h1>
                <p>Search approved customers, review account details, and manage frozen status.</p>
            </div>
            <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <form class="search-form" method="GET" action="{{ route('employee.accounts.index') }}">
                <input name="search" type="search" value="{{ $search }}" placeholder="Search customer, email, phone, or account">

                <select name="status" aria-label="Account status">
                    <option value="all" @selected($status === 'all')>All statuses</option>
                    <option value="{{ App\Models\Account::STATUS_ACTIVE }}" @selected($status === App\Models\Account::STATUS_ACTIVE)>Active</option>
                    <option value="{{ App\Models\Account::STATUS_FROZEN }}" @selected($status === App\Models\Account::STATUS_FROZEN)>Frozen</option>
                </select>

                <select name="page_size" aria-label="Rows per page">
                    @foreach ($pageSizeOptions as $option)
                        <option value="{{ $option }}" @selected($pageSize === $option)>{{ $option }} rows</option>
                    @endforeach
                </select>

                <button class="button-muted" type="submit">Apply</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Account</th>
                        <th>Branch</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr>
                            <td>
                                <strong>{{ $account->customer->name }}</strong><br>
                                <span>{{ $account->customer->email }}</span>
                            </td>
                            <td>
                                {{ $account->account_number }}<br>
                                <span>{{ ucfirst($account->account_type) }}</span>
                            </td>
                            <td>{{ $account->branch->name }}</td>
                            <td>BDT {{ number_format((float) $account->balance, 2) }}</td>
                            <td><span class="status-pill {{ $account->status }}">{{ ucfirst($account->status) }}</span></td>
                            <td>
                                <a class="button-muted" href="{{ route('employee.accounts.show', $account) }}">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No customer accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $accounts->links() }}
            </div>
        </section>
    </main>
@endsection
