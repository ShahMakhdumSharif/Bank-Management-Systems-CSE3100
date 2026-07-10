@extends('layouts.app')

@section('title', config('bank.name') . ' | ATM Session')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
    <main class="management-page">
        <section class="management-header">
            <div>
                <p class="eyebrow">Virtual ATM</p>
                <h1>ATM session active</h1>
                <p>{{ $card->account->customer->name }} · Card ending {{ substr($card->card_number, -4) }}</p>
            </div>
            <div class="action-row">
                <form method="POST" action="{{ route('atm.logout') }}">
                    @csrf
                    <button class="button-danger" type="submit">Logout</button>
                </form>
                <a class="button-muted" href="{{ route('home') }}#services">Online Services</a>
            </div>
        </section>

        <section class="management-card">
            <p class="eyebrow">Authenticated</p>
            <h2>Your ATM card login was verified.</h2>
            <p>ATM balance and transaction tools will appear in the next ATM dashboard stage.</p>
        </section>
    </main>
@endsection
