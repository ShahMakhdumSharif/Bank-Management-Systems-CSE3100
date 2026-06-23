@php
    $homePrefix = request()->is('/') ? '' : url('/');
@endphp

<header class="site-header">
    <nav class="navbar" aria-label="Main navigation">
        <a class="brand" href="{{ request()->is('/') ? '#home' : url('/#home') }}" aria-label="SecureBank home">
            <span class="brand-mark">CB</span>
            <span>CentralBank</span>
        </a>

        <input class="nav-menu-check" id="nav-menu-check" type="checkbox" aria-label="Toggle navigation">
        <label class="nav-toggle" for="nav-menu-check" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <div class="nav-links">
            <a href="{{ $homePrefix }}#cards">Cards</a>
            <a href="{{ $homePrefix }}#accounts">Accounts</a>
            <a href="{{ $homePrefix }}#services">Services</a>
            <a href="{{ $homePrefix }}#security">Security</a>
            <a href="{{ route('login') }}">Login</a>
            <a class="nav-action" href="{{ $homePrefix }}#apply">Apply Now</a>
        </div>
    </nav>
</header>
