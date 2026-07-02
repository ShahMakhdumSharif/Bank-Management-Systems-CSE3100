@extends('layouts.app')

@section('title', config('bank.name') . ' | Pending Customers')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Customer approval</p>
                <h1>Pending customers</h1>
                <p>Review new applications before account creation.</p>
            </div>
            <a class="button-muted" href="{{ route('employee.dashboard') }}">Dashboard</a>
        </section>

        @include('admin.partials.flash')

        <section class="management-panel">
            <form class="search-form" method="GET" action="{{ route('employee.customers.pending') }}">
                <input name="search" type="search" value="{{ $search }}" placeholder="Search pending customers">
                <button class="button-muted" type="submit">Search</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td><span class="status-pill pending">Pending</span></td>
                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                            <td>
                                <a class="button-muted" href="{{ route('employee.customers.show', $customer) }}">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No pending customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $customers->links() }}
            </div>
        </section>
    </main>
@endsection
