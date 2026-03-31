@extends('layouts.app')
@section('title', 'Payment Cancelled')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            {{-- Yellow Warning Banner --}}
            <div class="card border-warning mb-4">
                <div class="card-body text-center py-5">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                        <i class="bi bi-exclamation-triangle-fill text-warning display-5"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-2">Payment Cancelled</h2>
                    <p class="text-muted mb-0">Your booking is still pending. You can complete the payment later.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('payment.checkout', $booking) }}" class="btn btn-primary">
                    <i class="bi bi-credit-card me-1"></i> Try Again
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-speedometer2 me-1"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
