@extends('layouts.guest')
@section('title', 'Account Pending Approval')

@section('content')
<div class="text-center py-4">
    <div class="mb-3">
        <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
    </div>
    <h5 class="fw-bold mb-2">Account Pending Approval</h5>
    <p class="text-muted">
        Thank you for registering your corporate account{{ $corporate ? ', <strong>' . e($corporate->company_name) . '</strong>' : '' }}!
    </p>
    <p class="text-muted small">
        Our admin team will review your account within 1 business day. You'll receive an email once approved.
    </p>

    <div class="alert alert-info small text-start mt-4">
        <strong>What happens next?</strong>
        <ul class="mb-0 mt-2">
            <li>Admin reviews your company details</li>
            <li>Account activated &amp; you'll be notified</li>
            <li>You can invite employees &amp; start booking</li>
            <li>Invoices auto-generated on your chosen cadence</li>
        </ul>
    </div>

    <div class="d-flex gap-2 mt-4">
        <form method="POST" action="{{ route('logout') }}" class="flex-fill">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
        </form>
        <a href="{{ route('home') }}" class="btn btn-primary flex-fill">Back to Home</a>
    </div>
</div>
@endsection
