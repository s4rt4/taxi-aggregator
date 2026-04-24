@extends('layouts.guest')
@section('title', 'Log In')

@section('content')
@php
    $role = request()->query('role');
    $badges = [
        'operator' => ['label' => 'Operator Log In', 'icon' => 'bi-building-fill', 'color' => 'success', 'desc' => 'For licensed taxi operators'],
        'corporate' => ['label' => 'Corporate Log In', 'icon' => 'bi-briefcase-fill', 'color' => 'warning', 'desc' => 'For business & agency accounts'],
    ];
    $badge = $badges[$role] ?? null;
@endphp

@if($badge)
<div class="alert alert-{{ $badge['color'] }} bg-{{ $badge['color'] }} bg-opacity-10 border-0 d-flex align-items-center gap-2 mb-3 small">
    <i class="bi {{ $badge['icon'] }} text-{{ $badge['color'] }} fs-5"></i>
    <div>
        <strong>{{ $badge['label'] }}</strong><br>
        <span class="text-muted">{{ $badge['desc'] }}</span>
    </div>
</div>
@endif

<h5 class="fw-bold mb-3">Welcome back</h5>
<p class="text-muted small mb-4">Log in to your account to continue.</p>

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror"
               id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <label for="password" class="form-label small fw-semibold">Password</label>
            <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
        </div>
        <input type="password" class="form-control @error('password') is-invalid @enderror"
               id="password" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label small" for="remember">Remember me</label>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Log In</button>

    <p class="text-center text-muted small mb-0">
        Don't have an account? <a href="{{ route('register') }}">Register</a>
    </p>
</form>
@endsection
