@extends('layouts.app')
@section('title', 'Booking Confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Success Banner --}}
            <div class="text-center mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                    <i class="bi bi-check-circle-fill text-success display-5"></i>
                </div>
                <h2 class="fw-bold">Booking Confirmed!</h2>
                <p class="text-muted">Your booking has been sent to the operator. You will receive a confirmation shortly.</p>
            </div>

            {{-- Booking Reference --}}
            <div class="card mb-4 border-primary">
                <div class="card-body text-center py-4">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Booking Reference</small>
                    <h2 class="fw-bold text-primary mb-0 font-monospace">{{ $booking->reference }}</h2>
                </div>
            </div>

            {{-- Journey Details --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-map me-2"></i>Journey Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Pickup</small>
                            <span class="fw-medium">{{ $booking->pickup_address }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Destination</small>
                            <span class="fw-medium">{{ $booking->destination_address }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Date & Time</small>
                            <span class="fw-medium">{{ $booking->pickup_datetime->format('D, j M Y \a\t H:i') }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Passengers</small>
                            <span class="fw-medium">{{ $booking->passenger_count }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Vehicle</small>
                            <span class="fw-medium">{{ $booking->fleetType->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Operator & Price --}}
            <div class="row g-4 mb-4">
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3"><i class="bi bi-building me-2"></i>Operator</h6>
                            <p class="fw-medium mb-1">{{ $booking->operator->operator_name ?? 'N/A' }}</p>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>{{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3"><i class="bi bi-receipt me-2"></i>Total Price</h6>
                            <p class="fs-3 fw-bold text-primary mb-1">&pound;{{ number_format($booking->total_price, 2) }}</p>
                            <small class="text-muted">Payment: {{ ucfirst($booking->payment_type) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passenger Info --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-person me-2"></i>Passenger Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Name</small>
                            <span class="fw-medium">{{ $booking->passenger_name }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Phone</small>
                            <span class="fw-medium">{{ $booking->passenger_phone }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-medium">{{ $booking->passenger_email ?? '-' }}</span>
                        </div>
                        @if($booking->flight_number)
                            <div class="col-sm-4">
                                <small class="text-muted d-block">Flight / Train</small>
                                <span class="fw-medium">{{ $booking->flight_number }}</span>
                            </div>
                        @endif
                        @if($booking->special_requirements)
                            <div class="col-12">
                                <small class="text-muted d-block">Special Requirements</small>
                                <span class="fw-medium">{{ $booking->special_requirements }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pay Now (for prepaid bookings that haven't been paid) --}}
            @if($booking->payment_type === 'prepaid' && ($booking->payment === null || $booking->payment->status !== 'succeeded'))
                <div class="card mb-4 border-warning">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-credit-card text-warning display-6 mb-2 d-block"></i>
                        <h5 class="fw-semibold mb-2">Payment Required</h5>
                        <p class="text-muted mb-3">This booking requires prepayment to confirm.</p>
                        <a href="{{ route('payment.checkout', $booking) }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-lock-fill me-1"></i> Pay Now &mdash; &pound;{{ number_format($booking->total_price, 2) }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('passenger.bookings') }}" class="btn btn-outline-primary">
                    <i class="bi bi-list me-1"></i> View My Bookings
                </a>
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Book Another Ride
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
