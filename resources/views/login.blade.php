<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank | Secure Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    @include('partials.site-header')

    <main class="login-page">
        <section class="login-shell" aria-labelledby="login-title">
            <div class="login-copy">
                <p class="eyebrow">Secure login</p>
                <h1 id="login-title">Access your banking dashboard.</h1>
                <p>
                    Sign in to review account balances, transfer requests, transaction history, and card services.
                </p>
                <div class="login-highlights" aria-label="Login security highlights">
                    <span>Encrypted access</span>
                    <span>Approval protected</span>
                </div>
            </div>

            <form class="login-card">
                <label for="login-email">Email address</label>
                <input id="login-email" name="email" type="email" placeholder="customer@example.com" autocomplete="email">

                <label for="login-password">Password</label>
                <input id="login-password" name="password" type="password" placeholder="Enter password" autocomplete="current-password">

                <div class="login-options">
                    <label class="remember-choice">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ url('/#support') }}">Need help?</a>
                </div>

                <button class="login-button" type="button">Login Securely</button>
            </form>
        </section>
    </main>

    @include('partials.site-footer')
</body>
</html>
