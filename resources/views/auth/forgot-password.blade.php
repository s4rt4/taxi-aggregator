@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
<h5 class="fw-bold mb-3">Forgot your password?</h5>
<p class="text-muted small mb-4">Enter your email and we'll send you a reset link.</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror"
               id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>

    <p class="text-center small mb-0">
        <a href="{{ route('login') }}">Back to login</a>
    </p>
</form>
@endsection
