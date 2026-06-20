<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank | Personal Banking, Cards & Accounts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <header class="site-header" data-header>
        <nav class="navbar" aria-label="Main navigation">
            <a class="brand" href="#home" aria-label="SecureBank home">
                <span class="brand-mark">SB</span>
                <span>SecureBank</span>
            </a>

            <button class="nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-nav-toggle>
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-links" data-nav-menu>
                <a href="#cards">Cards</a>
                <a href="#accounts">Accounts</a>
                <a href="#services">Services</a>
                <a href="#security">Security</a>
                <a href="#login">Login</a>
                <a class="nav-action" href="#apply">Apply Now</a>
            </div>
        </nav>
    </header>

    <main id="home">
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-content reveal">
                    <p class="eyebrow">Personal banking made secure</p>
                    <h1>Manage your money with confidence, anywhere.</h1>
                    <p class="hero-copy">
                        Open an account, request a card, transfer funds, and manage everyday banking through a secure digital experience built for customers.
                    </p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#apply">Open an Account</a>
                        <a class="btn btn-secondary" href="#cards">Explore Cards</a>
                    </div>
                </div>

                <div class="hero-card reveal" aria-label="Premium banking card preview">
                    <div class="card-chip"></div>
                    <p>SecureBank Platinum</p>
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
                    <h3>SecureBank Everyday Card</h3>
                    <p>Pay, withdraw, and manage spending with a card connected to your approved account.</p>
                    <a href="#apply">Request Card</a>
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
                    Customers can register for an account, wait for secure approval, and then access balance, transaction history, transfer requests, and card services from one place.
                </p>
                <a class="text-link" href="#apply">Start your application</a>
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
                    <span>Accounts</span>
                    <h3>Open and manage accounts</h3>
                </a>
                <a class="service-item reveal" href="#login">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331945.png" alt="Transfer services icon">
                    <span>Transfers</span>
                    <h3>Request money transfers</h3>
                </a>
                <a class="service-item reveal" href="#cards">
                    <img src="https://cdn-icons-png.flaticon.com/512/633/633611.png" alt="Card services icon">
                    <span>Cards</span>
                    <h3>Request Card</h3>
                </a>
                <button class="service-item reveal" type="button" data-atm-open>
                    <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" alt="ATM card services icon">
                    <span>ATM</span>
                    <h3>Open Virtual ATM</h3>
                </button>
                <article class="service-item reveal">
                    <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" alt="Security services icon">
                    <span>Security</span>
                    <h3>Protected account access</h3>
                </article>
                <article class="service-item reveal">
                    <img src="https://cdn-icons-png.flaticon.com/512/159/159832.png" alt="Support services icon">
                    <span>Support</span>
                    <h3>Customer assistance</h3>
                </article>
            </div>

            <div class="virtual-atm-panel" data-atm-panel hidden>
                <div class="virtual-atm-screen">
                    <p>SecureBank Virtual ATM</p>
                    <strong>Card Login</strong>
                    <span>Enter card number and PIN to continue</span>
                </div>

                <form class="virtual-atm-form">
                    <label for="atm-card-number">Card number</label>
                    <input id="atm-card-number" type="text" inputmode="numeric" placeholder="4589 2210 7741 0935">

                    <label for="atm-pin">PIN</label>
                    <input id="atm-pin" type="password" inputmode="numeric" placeholder="Enter PIN">

                    <button class="btn btn-primary" type="button">Access ATM</button>
                </form>
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
                        <strong data-count="24">0</strong>
                        <span>Hour banking</span>
                    </div>
                    <div>
                        <strong data-count="3">0</strong>
                        <span>Role checks</span>
                    </div>
                    <div>
                        <strong data-count="1">0</strong>
                        <span>Secure account</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-section section-band" id="login">
            <div class="login-copy reveal">
                <p class="eyebrow">Secure login</p>
                <h2>Access your banking dashboard.</h2>
                <p>
                    Sign in to review account balances, transfer requests, transaction history, and card services after your account is approved.
                </p>
                <div class="login-highlights">
                    <span>Encrypted access</span>
                    <span>Role-based dashboard</span>
                    <span>Approval protected</span>
                </div>
            </div>

            <form class="login-card reveal" data-login-form>
                <label for="login-email">Email address</label>
                <input id="login-email" name="email" type="email" placeholder="customer@example.com" autocomplete="email">

                <label for="login-password">Password</label>
                <input id="login-password" name="password" type="password" placeholder="Enter password" autocomplete="current-password">

                <div class="login-options">
                    <label class="remember-choice">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#support">Need help?</a>
                </div>

                <button class="btn btn-primary" type="submit">Login Securely</button>
            </form>
        </section>

        <section class="apply-section section-band" id="apply">
            <div class="apply-panel reveal">
                <p class="eyebrow">Start banking</p>
                <h2>Ready to open your SecureBank account?</h2>
                <p>Apply online, wait for approval, and begin using account, transfer, and card services after activation.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="#home">Apply Online</a>
                    <a class="btn btn-secondary" href="#support">Contact Support</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer" id="support">
        <div>
            <a class="brand" href="#home" aria-label="SecureBank home">
                <span class="brand-mark">SB</span>
                <span>SecureBank</span>
            </a>
            <p>Professional personal banking, card services, transfer requests, and secure account access.</p>
        </div>
        <div class="footer-links">
            <a href="#cards">Cards</a>
            <a href="#accounts">Accounts</a>
            <a href="#services">Services</a>
            <a href="#security">Security</a>
            <a href="#login">Login</a>
        </div>
    </footer>

    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
