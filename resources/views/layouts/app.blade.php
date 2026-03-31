<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TaxiAggregator'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
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
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Log In</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">Register</a>
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
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0 small">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
