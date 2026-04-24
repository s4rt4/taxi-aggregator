<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.seo-meta')
    <title>@yield('title', 'Corporate Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">
    <div class="sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

    @php
        $corporate = auth()->user()->activeCorporate();
        $isSuperUser = auth()->user()->isCorporateSuperUser();
    @endphp

    <aside class="sidebar" :class="{ 'show': sidebarOpen }">
        <div class="sidebar-brand">
            @if(config('app.brand.logo'))
                <img src="{{ asset(config('app.brand.logo')) }}" alt="{{ config('app.name') }}" class="brand-logo">
            @endif
            <div class="brand-text">
                {{ config('app.brand.prefix') }}<span class="brand-highlight">{{ config('app.brand.highlight') }}</span>{{ config('app.brand.suffix') }}
                <span class="brand-suffix">corporate</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Overview</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('corporate.dashboard') ? 'active' : '' }}" href="{{ route('corporate.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
            </ul>

            @if($isSuperUser)
            <div class="nav-section">Management</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('corporate.employees.*') ? 'active' : '' }}" href="{{ route('corporate.employees.index') }}">
                        <i class="bi bi-people"></i> Employees
                    </a>
                </li>
            </ul>
            @endif

            <div class="nav-section">Activity</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('corporate.bookings.*') ? 'active' : '' }}" href="{{ route('corporate.bookings.index') }}">
                        <i class="bi bi-journal-text"></i> Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('corporate.invoices.*') ? 'active' : '' }}" href="{{ route('corporate.invoices.index') }}">
                        <i class="bi bi-receipt"></i> Invoices
                    </a>
                </li>
            </ul>

            @if($isSuperUser)
            <div class="nav-section">Admin</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('corporate.settings.*') ? 'active' : '' }}" href="{{ route('corporate.settings.index') }}">
                        <i class="bi bi-gear"></i> Company Settings
                    </a>
                </li>
            </ul>
            @endif
        </nav>
    </aside>

    <div class="main-content">
        <div class="top-banner">
            @if($corporate)
                <strong>{{ $corporate->company_name }}</strong>
                @if($corporate->status === 'pending')
                    &middot; <span class="badge bg-warning text-dark">Pending Admin Approval</span>
                @elseif($corporate->status === 'suspended')
                    &middot; <span class="badge bg-danger">Account Suspended</span>
                @endif
            @endif
        </div>

        <div class="top-navbar">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                <button class="btn btn-link text-dark d-lg-none p-0 me-2" @click="sidebarOpen = !sidebarOpen">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="flex-grow-1">
                    <span class="fw-semibold">{{ $corporate?->company_name ?? 'Corporate' }}</span>
                    @if($isSuperUser)
                        <span class="badge bg-primary ms-2">Super User</span>
                    @else
                        <span class="badge bg-secondary ms-2">Employee</span>
                    @endif
                </div>

                <div class="top-controls">
                    <div class="top-links d-none d-md-flex">
                        <a href="{{ route('home') }}">Book a Ride</a>
                        <a href="{{ route('contact') }}">Contact</a>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text fw-semibold">{{ Auth::user()->name }}</span></li>
                                <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                @if($isSuperUser)
                                    <li><a class="dropdown-item" href="{{ route('corporate.settings.index') }}"><i class="bi bi-gear me-2"></i>Company Settings</a></li>
                                @endif
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

        <div class="mc-footer">
            &copy; {{ date('Y') }} {{ \App\Helpers\Settings::get('company_name', config('app.name')) }}. All rights reserved. &nbsp;&middot;&nbsp;
            <a href="{{ route('terms-of-service') }}">Terms</a> &nbsp;&middot;&nbsp;
            <a href="{{ route('privacy-policy') }}">Privacy</a>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
