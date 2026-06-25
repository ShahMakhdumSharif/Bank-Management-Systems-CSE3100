@php
    $bankName = config('bank.name', 'CentralBank');
    $brandInitials = config('bank.brand_initials', 'CB');
    $homePrefix = request()->is('/') ? '' : url('/');
@endphp

<header class="site-header">
    <nav class="navbar" aria-label="Main navigation">
        <a class="brand" href="{{ request()->is('/') ? '#home' : url('/#home') }}" aria-label="{{ $bankName }} home">
            <span class="brand-mark">{{ $brandInitials }}</span>
            <span>{{ $bankName }}</span>
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
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <form class="nav-logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a class="nav-action" href="{{ route('register') }}">Apply Now</a>
            @endauth
        </div>
    </nav>
</header>
