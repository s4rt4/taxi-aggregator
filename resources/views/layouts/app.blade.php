<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-maps-key" content="{{ config('services.google_maps.api_key') }}">
    @include('components.seo-meta')
    <title>@yield('title', config('app.name', 'TaxiAggregator'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    {{-- Ensure Google Maps autocomplete dropdown appears above Bootstrap modals/cards --}}
    <style>
        .pac-container {
            z-index: 10501 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Top Navbar (public pages) --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
                @if(config('app.brand.logo'))
                    <img src="{{ asset(config('app.brand.logo')) }}" alt="{{ config('app.name') }}" height="28" class="me-1">
                @else
                    <i class="bi bi-taxi-front-fill me-1"></i>
                @endif
                {{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Log In</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                @if(Auth::user()->isPassenger())
                                    <li><a class="dropdown-item" href="{{ route('passenger.bookings') }}"><i class="bi bi-journal-check me-2"></i>My Bookings</a></li>
                                    <li><a class="dropdown-item" href="{{ route('passenger.profile') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="bg-dark text-light pt-5 pb-3">
        <div class="container">
            {{-- Top links row --}}
            <div class="row g-4 mb-4">
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold text-white small text-uppercase mb-3">Company</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="{{ route('about') }}" class="text-secondary text-decoration-none">About Us</a></li>
                        <li class="mb-1"><a href="{{ route('how-it-works') }}" class="text-secondary text-decoration-none">How It Works</a></li>
                        <li class="mb-1"><a href="{{ route('for-operators') }}" class="text-secondary text-decoration-none">For Operators</a></li>
                        <li class="mb-1"><a href="{{ route('register') }}" class="text-secondary text-decoration-none">Register</a></li>
                        <li class="mb-1"><a href="{{ route('contact') }}" class="text-secondary text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold text-white small text-uppercase mb-3">Support</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="{{ route('contact') }}" class="text-secondary text-decoration-none">Help Centre</a></li>
                        <li class="mb-1"><a href="{{ route('privacy-policy') }}" class="text-secondary text-decoration-none">Privacy Policy</a></li>
                        <li class="mb-1"><a href="{{ route('terms-of-service') }}" class="text-secondary text-decoration-none">Terms & Conditions</a></li>
                        <li class="mb-1"><a href="{{ route('cookie-policy') }}" class="text-secondary text-decoration-none">Cookie Policy</a></li>
                        <li class="mb-1"><a href="#" class="text-secondary text-decoration-none">Accessibility</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold text-white small text-uppercase mb-3">Popular Cities</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="{{ route('city.show', 'london') }}" class="text-secondary text-decoration-none">London</a></li>
                        <li class="mb-1"><a href="{{ route('city.show', 'manchester') }}" class="text-secondary text-decoration-none">Manchester</a></li>
                        <li class="mb-1"><a href="{{ route('city.show', 'birmingham') }}" class="text-secondary text-decoration-none">Birmingham</a></li>
                        <li class="mb-1"><a href="{{ route('city.show', 'edinburgh') }}" class="text-secondary text-decoration-none">Edinburgh</a></li>
                        <li class="mb-1"><a href="{{ route('city.show', 'glasgow') }}" class="text-secondary text-decoration-none">Glasgow</a></li>
                        <li class="mb-1"><a href="{{ route('city.show', 'liverpool') }}" class="text-secondary text-decoration-none">Liverpool</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold text-white small text-uppercase mb-3">Airports</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="{{ route('airport.show', 'heathrow') }}" class="text-secondary text-decoration-none">Heathrow</a></li>
                        <li class="mb-1"><a href="{{ route('airport.show', 'gatwick') }}" class="text-secondary text-decoration-none">Gatwick</a></li>
                        <li class="mb-1"><a href="{{ route('airport.show', 'manchester-airport') }}" class="text-secondary text-decoration-none">Manchester</a></li>
                        <li class="mb-1"><a href="{{ route('airport.show', 'stansted') }}" class="text-secondary text-decoration-none">Stansted</a></li>
                        <li class="mb-1"><a href="{{ route('airport.show', 'luton') }}" class="text-secondary text-decoration-none">Luton</a></li>
                        <li class="mb-1"><a href="{{ route('airport.show', 'edinburgh-airport') }}" class="text-secondary text-decoration-none">Edinburgh</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-4">
                    <h6 class="fw-bold text-white small text-uppercase mb-3">Contact & Social</h6>
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-1"><i class="bi bi-envelope me-2 text-secondary"></i><a href="mailto:{{ \App\Helpers\Settings::get('contact_email', 'support@rushxo.com') }}" class="text-secondary text-decoration-none">{{ \App\Helpers\Settings::get('contact_email', 'support@rushxo.com') }}</a></li>
                        <li class="mb-1"><i class="bi bi-telephone me-2 text-secondary"></i><span class="text-secondary">{{ \App\Helpers\Settings::get('contact_phone', '+44 1474 554933') }}</span></li>
                        <li class="mb-1"><i class="bi bi-clock me-2 text-secondary"></i><span class="text-secondary">24/7 Customer Support</span></li>
                    </ul>
                    <div class="d-flex gap-3">
                        <a href="{{ \App\Helpers\Settings::get('social_facebook', '#') ?: '#' }}" class="text-secondary fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="{{ \App\Helpers\Settings::get('social_twitter', '#') ?: '#' }}" class="text-secondary fs-5"><i class="bi bi-twitter-x"></i></a>
                        <a href="{{ \App\Helpers\Settings::get('social_instagram', '#') ?: '#' }}" class="text-secondary fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="{{ \App\Helpers\Settings::get('social_linkedin', '#') ?: '#' }}" class="text-secondary fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>

            {{-- Bottom bar --}}
            <hr class="border-secondary mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center small text-secondary">
                <span>&copy; {{ date('Y') }} {{ \App\Helpers\Settings::get('company_name', config('app.name')) }}. All rights reserved.</span>
                <span>
                    <a href="{{ route('privacy-policy') }}" class="text-secondary text-decoration-none me-2">Privacy</a> |
                    <a href="{{ route('terms-of-service') }}" class="text-secondary text-decoration-none mx-2">Terms</a> |
                    <a href="{{ route('cookie-policy') }}" class="text-secondary text-decoration-none ms-2">Cookies</a>
                </span>
            </div>
        </div>
    </footer>

    {{-- Cookie Consent --}}
    @include('components.cookie-consent')

    {{-- Real-time Toast Notifications --}}
    @include('components.realtime-toast')

    @stack('scripts')
</body>
</html>
