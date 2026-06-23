@php
    $homePrefix = request()->is('/') ? '' : url('/');
@endphp

<footer class="site-footer" id="support">
    <div>
        <a class="brand" href="{{ request()->is('/') ? '#home' : url('/#home') }}" aria-label="SecureBank home">
            <span class="brand-mark">CB</span>
            <span>CentralBank</span>
        </a>
        <p>Professional personal banking, card services, transfer requests, and secure account access.</p>
    </div>
    <div class="footer-links">
        <a href="{{ $homePrefix }}#cards">Cards</a>
        <a href="{{ $homePrefix }}#accounts">Accounts</a>
        <a href="{{ $homePrefix }}#services">Services</a>
        <a href="{{ $homePrefix }}#security">Security</a>
        <a href="{{ route('login') }}">Login</a>
    </div>
</footer>
