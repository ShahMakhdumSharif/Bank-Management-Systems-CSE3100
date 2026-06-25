@php
    $bankName = config('bank.name', 'CentralBank');
    $brandInitials = config('bank.brand_initials', 'CB');
    $homePrefix = request()->is('/') ? '' : url('/');
@endphp

<footer class="site-footer" id="support">
    <div>
        <a class="brand" href="{{ request()->is('/') ? '#home' : url('/#home') }}" aria-label="{{ $bankName }} home">
            <span class="brand-mark">{{ $brandInitials }}</span>
            <span>{{ $bankName }}</span>
        </a>
        <p>Professional personal banking, card services, transfer requests, and secure account access.</p>
    </div>
    <div class="footer-links">
        <a href="{{ $homePrefix }}#cards">Cards</a>
        <a href="{{ $homePrefix }}#accounts">Accounts</a>
        <a href="{{ $homePrefix }}#services">Services</a>
        <a href="{{ $homePrefix }}#security">Security</a>
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
        @endauth
    </div>
</footer>
