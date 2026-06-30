@extends('layouts.app')

@section('title', config('bank.name') . ' | Delete Employee')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="danger-panel">
            <p class="eyebrow">Confirm deletion</p>
            <h1>Delete {{ $employee->name }}?</h1>
            <p>
                This employee is assigned to {{ $employee->branches_count }} branches,
                has {{ $employee->employee_actions_count }} audit actions,
                and performed {{ $employee->performed_transactions_count }} transactions.
            </p>

            <form class="form-actions" method="POST" action="{{ route('admin.employees.destroy', $employee) }}">
                @csrf
                @method('DELETE')
                <button class="button-danger" type="submit">Delete Employee</button>
                <a class="button-muted" href="{{ route('admin.employees.show', $employee) }}">Cancel</a>
            </form>
        </section>
    </main>
@endsection
