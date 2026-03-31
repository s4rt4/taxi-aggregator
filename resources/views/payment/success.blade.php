@extends('layouts.app')
@section('title', 'Payment Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            {{-- Green Success Banner --}}
            <div class="card border-success mb-4">
                <div class="card-body text-center py-5">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                        <i class="bi bi-check-circle-fill text-success display-5"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">Payment Successful!</h2>
                    <p class="text-muted mb-0">Your booking has been confirmed.</p>
                </div>
            </div>

            {{-- Booking Details --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Booking Reference</small>
                            <span class="fw-bold font-monospace text-primary">{{ $booking->reference }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Amount Paid</small>
                            <span class="fw-bold fs-5">&pound;{{ number_format($booking->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('passenger.booking-detail', $booking) }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i> View Booking
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-1"></i> Return Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
