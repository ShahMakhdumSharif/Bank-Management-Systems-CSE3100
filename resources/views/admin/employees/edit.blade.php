@extends('layouts.app')

@section('title', config('bank.name') . ' | Edit Employee')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Employee management</p>
                <h1>Edit {{ $employee->name }}</h1>
            </div>
            <a class="button-muted" href="{{ route('admin.employees.show', $employee) }}">Back to Details</a>
        </section>

        <section class="management-panel">
            <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
                @csrf
                @method('PUT')
                @include('admin.employees.partials.form', ['submitLabel' => 'Update Employee'])
            </form>
        </section>
    </main>
@endsection
