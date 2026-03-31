<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.seo-meta')
    <title>@yield('title', 'Operator Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">
    <div class="sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Minicabit-style Sidebar --}}
    <aside class="sidebar" :class="{ 'show': sidebarOpen }">
        <div class="sidebar-brand">
            @if(config('app.brand.logo'))
                <img src="{{ asset(config('app.brand.logo')) }}" alt="{{ config('app.name') }}" class="brand-logo">
            @endif
            <div class="brand-text">
                {{ config('app.brand.prefix') }}<span class="brand-highlight">{{ config('app.brand.highlight') }}</span>{{ config('app.brand.suffix') }}
                <span class="brand-suffix">admin</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            {{-- VIEW section --}}
            <div class="nav-section">View</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}" href="{{ route('operator.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> My Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.bookings.*') ? 'active' : '' }}" href="{{ route('operator.bookings.index') }}">
                        <i class="bi bi-journal-text"></i> Booking Log
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.drivers.*') ? 'active' : '' }}" href="{{ route('operator.drivers.index') }}">
                        <i class="bi bi-people"></i> Drivers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.issues.*') ? 'active' : '' }}" href="{{ route('operator.issues.index') }}">
                        <i class="bi bi-exclamation-triangle"></i> Trip Issues & Ratings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.statements.*') ? 'active' : '' }}" href="{{ route('operator.statements.index') }}">
                        <i class="bi bi-receipt"></i> Statements
                    </a>
                </li>
            </ul>

            {{-- ACTIONS section --}}
            <div class="nav-section">Actions</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.availability.pause') ? 'active' : '' }}" href="{{ route('operator.availability.pause') }}">
                        <i class="bi bi-pause-circle"></i> Pause Availability
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.price-checker') ? 'active' : '' }}" href="{{ route('operator.price-checker') }}">
                        <i class="bi bi-calculator"></i> Top Routes & Price checker
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.flash-sales') ? 'active' : '' }}" href="{{ route('operator.pricing.flash-sales') }}">
                        <i class="bi bi-lightning"></i> Flash Sales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.dead-leg') ? 'active' : '' }}" href="{{ route('operator.pricing.dead-leg') }}">
                        <i class="bi bi-arrow-left-right"></i> Dead Leg Discounts
                    </a>
                </li>
            </ul>

            {{-- PRICING section --}}
            <div class="nav-section">Pricing</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.per-mile') ? 'active' : '' }}" href="{{ route('operator.pricing.per-mile') }}">
                        <i class="bi bi-speedometer"></i> Per Mile Prices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.location') ? 'active' : '' }}" href="{{ route('operator.pricing.location') }}">
                        <i class="bi bi-geo-alt"></i> Location Prices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.postcode-area') ? 'active' : '' }}" href="{{ route('operator.pricing.postcode-area') }}">
                        <i class="bi bi-pin-map"></i> Postcode Area Prices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.meet-greet') ? 'active' : '' }}" href="{{ route('operator.pricing.meet-greet') }}">
                        <i class="bi bi-arrow-right-circle"></i> Meet & Greet Charges
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.pricing.more') ? 'active' : '' }}" href="{{ route('operator.pricing.more') }}">
                        <i class="bi bi-three-dots"></i> More Options
                    </a>
                </li>
            </ul>

            {{-- AVAILABILITY section --}}
            <div class="nav-section">Availability</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.availability.vehicles') ? 'active' : '' }}" href="{{ route('operator.availability.vehicles') }}">
                        <i class="bi bi-car-front"></i> Number of Vehicles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.availability.notice') ? 'active' : '' }}" href="{{ route('operator.availability.notice') }}">
                        <i class="bi bi-clock-history"></i> Notice Periods
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.availability.trip-range') ? 'active' : '' }}" href="{{ route('operator.availability.trip-range') }}">
                        <i class="bi bi-arrows-angle-expand"></i> Trip Range
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('operator.availability.hours') ? 'active' : '' }}" href="{{ route('operator.availability.hours') }}">
                        <i class="bi bi-clock"></i> Operating Hours
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Top Yellow Banner --}}
        <div class="top-banner">
            For any queries or changes regarding your account, please email <a href="mailto:support@{{ strtolower(config('app.name')) }}.co.uk">support@{{ strtolower(config('app.name')) }}.co.uk</a>
            &middot; Any requests for account changes require min. 1 weekday notice.
        </div>

        {{-- Top Navbar with tier progress --}}
        <div class="top-navbar">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                {{-- Mobile toggle --}}
                <button class="btn btn-link text-dark d-lg-none p-0 me-2" @click="sidebarOpen = !sidebarOpen">
                    <i class="bi bi-list fs-4"></i>
                </button>

                {{-- Tier Progress --}}
                <div class="tier-progress flex-grow-1">
                    <div class="tier-step active">
                        <span class="tier-label">Basic</span>
                    </div>
                    <div class="tier-step">
                        <span class="tier-label">Airport Approved</span>
                    </div>
                    <div class="tier-step">
                        <span class="tier-label">TOP TIER</span>
                    </div>
                </div>

                {{-- Right side controls --}}
                <div class="top-controls">
                    <span class="customer-rating"><i class="bi bi-star-fill"></i> 5.0</span>
                    <div class="top-links d-none d-md-flex">
                        <a href="#" class="btn-book-call">Book a Call</a>
                        <a href="#">About</a>
                        <a href="#">Help</a>
                        <a href="#">Contact</a>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text fw-semibold">{{ Auth::user()->name }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('operator.account.index') }}"><i class="bi bi-gear me-2"></i>My Account</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Page Content --}}
        <div class="content-wrapper">
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

            @yield('content')
        </div>

        {{-- Minicabit-style Footer --}}
        <div class="mc-footer">
            Registration no. 00000000 &nbsp;&middot;&nbsp; VAT no. 000 0000 00 &nbsp;&middot;&nbsp;
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. &nbsp;&middot;&nbsp;
            <a href="{{ route('terms-of-service') }}">Terms and Conditions</a> &nbsp;&middot;&nbsp;
            <a href="{{ route('privacy-policy') }}">Privacy Policy</a> &nbsp;&middot;&nbsp;
            <a href="{{ route('cookie-policy') }}">Cookie Policy</a>
        </div>
    </div>

    {{-- Real-time Toast Notifications --}}
    @include('components.realtime-toast')

    @stack('scripts')
</body>
</html>
