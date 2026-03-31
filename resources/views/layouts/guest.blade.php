<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TaxiAggregator'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="bi bi-taxi-front-fill text-primary" style="font-size: 2.5rem;"></i>
                <h1>{{ config('app.name') }}</h1>
            </div>

            @if(session('status'))
                <div class="alert alert-success small">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
