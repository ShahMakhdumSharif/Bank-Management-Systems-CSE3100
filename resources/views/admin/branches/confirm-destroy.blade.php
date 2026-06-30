@extends('layouts.app')

@section('title', config('bank.name') . ' | Delete Branch')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="danger-panel">
            <p class="eyebrow">Confirm deletion</p>
            <h1>Delete {{ $branch->name }}?</h1>
            <p>
                This branch has {{ $branch->employees_count }} assigned employees and {{ $branch->accounts_count }} accounts.
                Branches with accounts cannot be deleted.
            </p>

            <form class="form-actions" method="POST" action="{{ route('admin.branches.destroy', $branch) }}">
                @csrf
                @method('DELETE')
                <button class="button-danger" type="submit">Delete Branch</button>
                <a class="button-muted" href="{{ route('admin.branches.show', $branch) }}">Cancel</a>
            </form>
        </section>
    </main>
@endsection
