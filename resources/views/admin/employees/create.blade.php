@extends('layouts.app')

@section('title', config('bank.name') . ' | Create Employee')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Employee management</p>
                <h1>Create employee</h1>
            </div>
            <a class="button-muted" href="{{ route('admin.employees.index') }}">Back to Employees</a>
        </section>

        <section class="management-panel">
            <form method="POST" action="{{ route('admin.employees.store') }}">
                @csrf
                @include('admin.employees.partials.form', ['submitLabel' => 'Create Employee'])
            </form>
        </section>
    </main>
@endsection
