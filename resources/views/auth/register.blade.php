@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<h5 class="fw-bold mb-3">Create an account</h5>
<p class="text-muted small mb-4">Join {{ config('app.name') }} as a passenger or operator.</p>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label small fw-semibold">Full name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror"
               id="name" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror"
               id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label small fw-semibold">Phone number</label>
        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
               id="phone" name="phone" value="{{ old('phone') }}" placeholder="+44">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="role" class="form-label small fw-semibold">Register as</label>
        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="passenger" {{ old('role') == 'passenger' ? 'selected' : '' }}>Passenger</option>
            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Taxi Operator</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror"
               id="password" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label small fw-semibold">Confirm password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>

    <p class="text-center text-muted small mb-0">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </p>
</form>
@endsection
