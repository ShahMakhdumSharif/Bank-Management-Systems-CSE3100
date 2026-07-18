@extends('layouts.app')

@section('title', config('bank.name') . ' | Audit Log Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Audit log details</p>
                <h1>{{ $auditLog->actionTypeLabel() }}</h1>
                <p>{{ $auditLog->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('admin.audit-logs.index') }}">All Audit Logs</a>
                <a class="button-muted" href="{{ route('admin.dashboard') }}">Dashboard</a>
            </div>
        </section>

        <section class="management-card">
            <dl class="detail-list">
                <div>
                    <dt>Employee</dt>
                    <dd>
                        {{ $auditLog->employee?->name ?? 'Deleted employee' }}
                        <br>
                        <small>{{ $auditLog->employee?->email ?? 'Employee record removed' }}</small>
                    </dd>
                </div>
                <div>
                    <dt>Action Type</dt>
                    <dd>{{ $auditLog->actionTypeLabel() }}</dd>
                </div>
                <div>
                    <dt>Subject</dt>
                    <dd>{{ $auditLog->subjectLabel() }}</dd>
                </div>
                <div>
                    <dt>Subject Status</dt>
                    <dd>{{ $auditLog->subject ? 'Record available' : 'Record unavailable' }}</dd>
                </div>
                <div>
                    <dt>IP Address</dt>
                    <dd>{{ $auditLog->ip_address ?? 'Not recorded' }}</dd>
                </div>
                <div>
                    <dt>Recorded</dt>
                    <dd>{{ $auditLog->created_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </section>

        <section class="management-card audit-detail-card">
            <p class="eyebrow">Description</p>
            <p>{{ $auditLog->description ?: 'No description was recorded.' }}</p>
        </section>

        <section class="management-card audit-detail-card">
            <p class="eyebrow">Metadata</p>

            @if (filled($auditLog->metadata))
                <dl class="detail-list single-column">
                    @foreach ($auditLog->metadata as $key => $value)
                        <div>
                            <dt>{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                            <dd>
                                @if (is_array($value))
                                    <pre class="metadata-block">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <p>No metadata was recorded.</p>
            @endif
        </section>
    </main>
@endsection
