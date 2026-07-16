@extends('layouts.app')

@section('title', config('bank.name') . ' | Foreign Exchange')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@push('scripts')
    @unless (app()->environment('testing'))
        @vite('resources/js/currency/currency-ui.js')
    @endunless
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Foreign exchange</p>
                <h1>BDT exchange rates</h1>
                <p>View supported foreign-currency rates based on BDT, then convert specific amounts.</p>
            </div>
            <div class="action-row">
                <a class="button-muted" href="{{ route('customer.dashboard') }}">Dashboard</a>
            </div>
        </section>

        <section
            class="management-card exchange-rate-table-card"
            data-rates-url="{{ route('customer.currency-exchange.rates') }}"
        >
            <div class="section-title-row">
                <div>
                    <p class="eyebrow">Rate table</p>
                    <h2>All rates based on BDT</h2>
                </div>
                <span class="status-pill" id="rate-table-status">Loading</span>
            </div>

            <div class="dashboard-table-wrap">
                <table class="data-table exchange-rate-table">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Name</th>
                            <th>1 BDT equals</th>
                            <th>1 unit equals</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody id="exchange-rate-table-body">
                        <tr>
                            <td colspan="5">Loading exchange rates...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="field-error" id="rate-table-error" hidden></p>
        </section>

        <section class="approval-grid">
            <form
                id="currency-converter-form"
                class="management-card currency-converter"
                method="POST"
                action="{{ route('customer.currency-exchange.convert') }}"
                data-metadata-url="{{ route('customer.currency-exchange.metadata') }}"
            >
                @csrf
                <p class="eyebrow">Live converter</p>
                <h2>Exchange amount</h2>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="amount">Amount</label>
                        <input id="amount" name="amount" type="number" min="0.01" max="999999999.99" step="0.01" value="1000" required>
                    </div>

                    <div class="form-field">
                        <label for="from_currency">From</label>
                        <select id="from_currency" name="from_currency" required>
                            @foreach ($currencies as $code => $name)
                                <option value="{{ $code }}" @selected($code === config('bank.currency'))>{{ $code }} - {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="to_currency">To</label>
                        <select id="to_currency" name="to_currency" required>
                            @foreach ($currencies as $code => $name)
                                <option value="{{ $code }}" @selected($code === 'USD')>{{ $code }} - {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Convert</button>
                    <button class="button-muted" id="swap-currencies" type="button">Swap</button>
                </div>

                <p class="field-error" id="currency-error" hidden></p>
            </form>

            <article class="management-card currency-result-panel" aria-live="polite">
                <p class="eyebrow">Result</p>
                <div id="currency-result">
                    <h2>Ready to convert</h2>
                    <p>Exchange calculations are completed by Laravel on the server.</p>
                </div>
                <div id="currency-metadata" class="currency-metadata"></div>
            </article>
        </section>
    </main>
@endsection
