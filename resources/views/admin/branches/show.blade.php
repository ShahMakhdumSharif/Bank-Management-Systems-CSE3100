@extends('layouts.app')

@section('title', config('bank.name') . ' | Branch Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Branch details</p>
                <h1>{{ $branch->name }}</h1>
                <p>{{ $branch->branch_code }} · {{ $branch->city }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('admin.branches.index') }}">All Branches</a>
                <a class="button" href="{{ route('admin.branches.edit', $branch) }}">Edit</a>
                <a class="button-danger" href="{{ route('admin.branches.confirm-destroy', $branch) }}">Delete</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-card">
            <dl class="detail-list">
                <div>
                    <dt>Address</dt>
                    <dd>{{ $branch->address }}</dd>
                </div>
                <div>
                    <dt>Country</dt>
                    <dd>{{ $branch->country_code }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd>{{ $branch->is_active ? 'Active' : 'Inactive' }}</dd>
                </div>
                <div>
                    <dt>Accounts</dt>
                    <dd>{{ $branch->accounts_count }}</dd>
                </div>
            </dl>

            <h2>Assigned employees</h2>
            <ul class="assigned-list">
                @forelse ($branch->employees as $employee)
                    <li>
                        <span>{{ $employee->name }}</span>
                        <span>{{ $employee->employee_code }}</span>
                    </li>
                @empty
                    <li>No employees assigned.</li>
                @endforelse
            </ul>
        </section>
    </main>
@endsection
