@extends('layouts.app')

@section('title', config('bank.name') . ' | Create Branch')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Branch management</p>
                <h1>Create branch</h1>
            </div>
            <a class="button-muted" href="{{ route('admin.branches.index') }}">Back to Branches</a>
        </section>

        <section class="management-panel">
            <form method="POST" action="{{ route('admin.branches.store') }}">
                @csrf
                @include('admin.branches.partials.form', ['submitLabel' => 'Create Branch'])
            </form>
        </section>
    </main>
@endsection
