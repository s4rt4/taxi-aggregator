@extends('layouts.guest')
@section('title', 'Verify Email')

@section('content')
<h5 class="fw-bold mb-3">Verify your email</h5>
<p class="text-muted small mb-4">
    We've sent a verification link to your email. Please check your inbox and click the link to verify.
</p>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success small">A new verification link has been sent to your email.</div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn btn-primary w-100 mb-3">Resend Verification Email</button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-outline-secondary w-100">Log Out</button>
</form>
@endsection
