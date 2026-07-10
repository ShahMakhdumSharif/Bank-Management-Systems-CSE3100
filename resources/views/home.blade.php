@extends('layouts.app')

@section('title', config('bank.name') . ' | Personal Banking, Cards & Accounts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    <main id="home">
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-content reveal">
                    <p class="eyebrow">Personal banking made secure</p>
                    <h1>Manage your money with confidence, anywhere.</h1>
                    <p class="hero-copy">
                        Open an account, request a card, transfer funds, and manage everyday banking through a secure digital experience.
                    </p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ route('register') }}">Open an Account</a>
                        <a class="btn btn-secondary" href="#cards">Explore Cards</a>
                    </div>
                </div>

                <div class="hero-card reveal" aria-label="Premium banking card preview">
                    <div class="card-chip"></div>
                    <p>CentralBank Platinum</p>
                    <strong>4589 2210 7741 0935</strong>
                    <div>
                        <span>SHAH MAKHDUM SHARIF</span>
                        <span>Valid 09/29</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="product-section section-band" id="cards">
            <div class="section-heading reveal">
                <p class="eyebrow">Cards</p>
                <h2>Choose a card for secure everyday banking.</h2>
            </div>

            <div class="product-grid">
                <article class="product-card product-card-dark reveal">
                    <p>Debit Card</p>
                    <h3>CentralBank Everyday Card</h3>
                    <p>Pay, withdraw, and manage spending with a card connected to your registered account.</p>
                    <a href="{{ route('register') }}">Request Card</a>
                </article>
                <article class="product-card reveal">
                    <p>Savings</p>
                    <h3>Secure Savings Account</h3>
                    <p>Keep money organized with balance visibility, account status updates, and transaction records.</p>
                    <a href="#accounts">Learn More</a>
                </article>
                <article class="product-card reveal">
                    <p>Student Banking</p>
                    <h3>Simple Account Card</h3>
                    <p>A clean card experience for deposits, transfers, and essential banking services.</p>
                    <a href="#accounts">See Account</a>
                </article>
            </div>
        </section>

        <section class="accounts-section section-band" id="accounts">
            <div class="account-copy reveal">
                <p class="eyebrow">Accounts</p>
                <h2>Bank accounts built for clear control.</h2>
                <p>
                    Customers can open an account, then access balance, transaction history, transfer requests, and card services from one place.
                </p>
                <a class="text-link" href="{{ route('register') }}">Start your application</a>
            </div>

            <div class="account-panel reveal">
                <div class="account-row">
                    <span>Available balance</span>
                    <strong>BDT 128,450.00</strong>
                </div>
                <div class="account-row">
                    <span>Account status</span>
                    <strong class="status-good">Active</strong>
                </div>
                <div class="account-row">
                    <span>Recent transfer</span>
                    <strong>BDT 12,500.00</strong>
                </div>
            </div>
        </section>

        <section class="services-section section-band" id="services">
            <div class="section-heading reveal">
                <p class="eyebrow">Online services</p>
                <h2>Essential banking services, organized for customers.</h2>
            </div>

            <div class="service-list">
                <a class="service-item reveal" href="#apply">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Account services icon">
                    <p>Accounts</p>
                </a>
                <a class="service-item reveal" href="{{ route('login') }}">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331945.png" alt="Transfer services icon">
                    <p>Transfers</p>
                </a>
                <a class="service-item reveal" href="{{ route('atm.login') }}">
                    <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" alt="ATM services icon">
                    <p>ATM</p>
                </a>
                <a class="service-item reveal" href="#cards">
                    <img src="https://cdn-icons-png.flaticon.com/512/633/633611.png" alt="Card services icon">
                    <p>Cards</p>
                </a>
                <article class="service-item reveal">
                    <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" alt="Security services icon">
                    <p>Security</p>
                </article>
                <article class="service-item reveal">
                    <img src="https://cdn-icons-png.flaticon.com/512/159/159832.png" alt="Support services icon">
                    <p>Support</p>
                </article>
            </div>

        </section>

        <section class="security-section section-band" id="security">
            <div class="security-banner reveal">
                <div>
                    <p class="eyebrow">Security and service</p>
                    <h2>Protected access with reviewed banking actions.</h2>
                </div>
                <div class="security-stats" aria-label="Security highlights">
                    <div>
                        <strong>24</strong>
                        <span>Hour banking</span>
                    </div>
                    <div>
                        <strong>3</strong>
                        <span>Layer verifications</span>
                    </div>
                    <div>
                        <strong>1</strong>
                        <span>Secure account</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="apply-section section-band" id="apply">
            <div class="apply-panel reveal">
                <p class="eyebrow">Start banking</p>
                <h2>Ready to open your CentralBank account?</h2>
                <p>Apply online, wait for approval, and begin using account, transfer, and card services after activation.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('register') }}">Apply Online</a>
                    <a class="btn btn-secondary" href="#support">Contact Support</a>
                </div>
            </div>
        </section>
    </main>
@endsection
