@extends('layouts.app')

@section('title', config('bank.name') . ' | Edit Branch')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Branch management</p>
                <h1>Edit {{ $branch->name }}</h1>
            </div>
            <a class="button-muted" href="{{ route('admin.branches.show', $branch) }}">Back to Details</a>
        </section>

        <section class="management-panel">
            <form method="POST" action="{{ route('admin.branches.update', $branch) }}">
                @csrf
                @method('PUT')
                @include('admin.branches.partials.form', ['submitLabel' => 'Update Branch'])
            </form>
        </section>
    </main>
@endsection
