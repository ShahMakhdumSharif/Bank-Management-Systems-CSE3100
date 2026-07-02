@extends('layouts.app')

@section('title', config('bank.name') . ' | Branches')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Branch management</p>
                <h1>Branches</h1>
                <p>Create branches, assign employees, and keep locations searchable.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="button" href="{{ route('admin.branches.create') }}">New Branch</a>
            </div>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <form class="search-form" method="GET" action="{{ route('admin.branches.index') }}">
                <input name="search" type="search" value="{{ $search }}" placeholder="Search branches">
                <button class="button-muted" type="submit">Search</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Code</th>
                        <th>City</th>
                        <th>Employees</th>
                        <th>Accounts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branches as $branch)
                        <tr>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->branch_code }}</td>
                            <td>{{ $branch->city }}</td>
                            <td>{{ $branch->employees_count }}</td>
                            <td>{{ $branch->accounts_count }}</td>
                            <td>
                                <span class="status-pill {{ $branch->is_active ? '' : 'inactive' }}">
                                    {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-row">
                                    <a class="button-muted" href="{{ route('admin.branches.show', $branch) }}">View</a>
                                    <a class="button-muted" href="{{ route('admin.branches.edit', $branch) }}">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No branches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $branches->links() }}
            </div>
        </section>
    </main>
@endsection
