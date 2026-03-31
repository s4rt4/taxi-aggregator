<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">
    <div class="sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Admin Sidebar --}}
    <aside class="sidebar" :class="{ 'show': sidebarOpen }">
        <div class="sidebar-brand">
            @if(config('app.brand.logo'))
                <img src="{{ asset(config('app.brand.logo')) }}" alt="{{ config('app.name') }}" height="28" class="me-2">
            @else
                <i class="bi bi-shield-lock-fill text-warning" style="font-size: 1.5rem;"></i>
            @endif
            <span class="brand-text">{{ config('app.name') }} Admin</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Overview</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
            </ul>

            <div class="nav-section">Operators</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.operators.*') ? 'active' : '' }}" href="{{ route('admin.operators.index') }}">
                        <i class="bi bi-building"></i> All Operators
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.operators.pending') ? 'active' : '' }}" href="{{ route('admin.operators.pending') }}">
                        <i class="bi bi-hourglass-split"></i> Pending Approval
                    </a>
                </li>
            </ul>

            <div class="nav-section">Bookings</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                        <i class="bi bi-journal-text"></i> All Bookings
                    </a>
                </li>
            </ul>

            <div class="nav-section">Financial</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.revenue') ? 'active' : '' }}" href="{{ route('admin.revenue') }}">
                        <i class="bi bi-graph-up"></i> Revenue
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.statements.*') ? 'active' : '' }}" href="{{ route('admin.statements.index') }}">
                        <i class="bi bi-receipt"></i> Statements
                    </a>
                </li>
            </ul>

            <div class="nav-section">Quality</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.disputes.*') ? 'active' : '' }}" href="{{ route('admin.disputes.index') }}">
                        <i class="bi bi-exclamation-octagon"></i> Disputes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.issues.*') ? 'active' : '' }}" href="{{ route('admin.issues.index') }}">
                        <i class="bi bi-exclamation-triangle"></i> Trip Issues
                    </a>
                </li>
            </ul>

            <div class="nav-section">System</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.fleet-types.*') ? 'active' : '' }}" href="{{ route('admin.fleet-types.index') }}">
                        <i class="bi bi-truck"></i> Fleet Types
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-shield-lock-fill text-dark"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-2 overflow-hidden">
                    <div class="text-white small fw-semibold text-truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="text-muted small">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted p-0" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="top-navbar d-flex align-items-center py-2">
            <button class="btn btn-link text-dark d-lg-none me-2 p-0" @click="sidebarOpen = !sidebarOpen">
                <i class="bi bi-list fs-4"></i>
            </button>

            <nav aria-label="breadcrumb" class="flex-grow-1">
                @yield('breadcrumb')
            </nav>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative p-0" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width:300px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div class="dropdown-item text-muted small">No new notifications</div>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 me-1"></i>
                        <span class="small d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text text-muted small">Administrator</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
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
    </div>

    @stack('scripts')
</body>
</html>
