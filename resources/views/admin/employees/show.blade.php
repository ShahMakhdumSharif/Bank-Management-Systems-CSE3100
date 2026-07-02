@extends('layouts.app')

@section('title', config('bank.name') . ' | Employee Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Employee details</p>
                <h1>{{ $employee->name }}</h1>
                <p>{{ $employee->employee_code }} · {{ $employee->email }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('admin.employees.index') }}">All Employees</a>
                <a class="button" href="{{ route('admin.employees.edit', $employee) }}">Edit</a>
                <a class="button-danger" href="{{ route('admin.employees.confirm-destroy', $employee) }}">Delete</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-card">
            <dl class="detail-list">
                <div>
                    <dt>Phone</dt>
                    <dd>{{ $employee->phone }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd>{{ ucfirst($employee->status) }}</dd>
                </div>
                <div>
                    <dt>Role</dt>
                    <dd>{{ ucfirst(str_replace('_', ' ', $employee->role)) }}</dd>
                </div>
                <div>
                    <dt>Created</dt>
                    <dd>{{ $employee->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>

            <h2>Assigned branches</h2>
            <ul class="assigned-list">
                @forelse ($employee->branches as $branch)
                    <li>
                        <span>{{ $branch->name }}</span>
                        <span>{{ $branch->branch_code }}</span>
                    </li>
                @empty
                    <li>No branches assigned.</li>
                @endforelse
            </ul>
        </section>
    </main>
@endsection
