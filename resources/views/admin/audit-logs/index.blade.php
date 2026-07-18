@extends('layouts.app')

@section('title', config('bank.name') . ' | Audit Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Admin audit logs</p>
                <h1>Employee actions</h1>
                <p>Review staff approvals, account controls, transfer decisions, ATM-card actions, and request history.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('admin.dashboard') }}">Dashboard</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <form class="search-form audit-filter-form" method="GET" action="{{ route('admin.audit-logs.index') }}">
                <input name="search" type="search" value="{{ $search }}" placeholder="Search employee or action">

                <select name="action_type" aria-label="Filter by action type">
                    <option value="">All actions</option>
                    @foreach ($actionTypes as $value => $label)
                        <option value="{{ $value }}" @selected($selectedActionType === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <button class="button-muted" type="submit">Filter</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $auditLog)
                        <tr>
                            <td>
                                {{ $auditLog->created_at->format('M d, Y') }}
                                <br>
                                <small>{{ $auditLog->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                {{ $auditLog->employee?->name ?? 'Deleted employee' }}
                                <br>
                                <small>{{ $auditLog->employee?->email ?? 'Employee record removed' }}</small>
                            </td>
                            <td><span class="status-pill audit-action">{{ $auditLog->actionTypeLabel() }}</span></td>
                            <td>
                                <a class="button-muted" href="{{ route('admin.audit-logs.show', $auditLog) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $auditLogs->links() }}
            </div>
        </section>
    </main>
@endsection
