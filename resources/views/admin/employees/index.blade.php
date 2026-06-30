@extends('layouts.app')

@section('title', config('bank.name') . ' | Employees')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Employee management</p>
                <h1>Employees</h1>
                <p>Create employees, assign branches, and maintain staff access.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="button" href="{{ route('admin.employees.create') }}">New Employee</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <form class="search-form" method="GET" action="{{ route('admin.employees.index') }}">
                <input name="search" type="search" value="{{ $search }}" placeholder="Search employees">
                <button class="button-muted" type="submit">Search</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Email</th>
                        <th>Branches</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->branches_count }}</td>
                            <td><span class="status-pill {{ $employee->status }}">{{ ucfirst($employee->status) }}</span></td>
                            <td>
                                <div class="action-row">
                                    <a class="button-muted" href="{{ route('admin.employees.show', $employee) }}">View</a>
                                    <a class="button-muted" href="{{ route('admin.employees.edit', $employee) }}">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $employees->links() }}
            </div>
        </section>
    </main>
@endsection
