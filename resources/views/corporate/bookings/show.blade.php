@extends('layouts.corporate')
@section('title', 'Booking ' . $booking->reference)

@section('content')
<div class="container-fluid" style="max-width: 960px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Booking {{ $booking->reference }}</h3>
        <a href="{{ route('corporate.bookings.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Journey</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">From</dt>
                        <dd class="col-sm-9">{{ $booking->pickup_address }}</dd>

                        <dt class="col-sm-3">To</dt>
                        <dd class="col-sm-9">{{ $booking->destination_address }}</dd>

                        <dt class="col-sm-3">Pickup Date</dt>
                        <dd class="col-sm-9">{{ $booking->pickup_datetime?->format('d M Y H:i') }}</dd>

                        <dt class="col-sm-3">Passengers</dt>
                        <dd class="col-sm-9">{{ $booking->passenger_count }}</dd>

                        @if($booking->distance_miles)
                        <dt class="col-sm-3">Distance</dt>
                        <dd class="col-sm-9">{{ number_format($booking->distance_miles, 1) }} miles</dd>
                        @endif

                        @if($booking->flight_number)
                        <dt class="col-sm-3">Flight</dt>
                        <dd class="col-sm-9">{{ $booking->flight_number }}</dd>
                        @endif

                        @if($booking->special_requirements)
                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $booking->special_requirements }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Status</h6>
                </div>
                <div class="card-body">
                    <p><strong>Booking Status:</strong>
                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                    </p>
                    <p><strong>Approval:</strong>
                        <span class="badge bg-info">{{ ucfirst($booking->approval_status) }}</span>
                    </p>
                    <p><strong>Employee:</strong> {{ $booking->passenger?->name }}</p>
                    @if($booking->cost_centre)
                        <p><strong>Cost Centre:</strong> {{ $booking->cost_centre }}</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Price</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Total:</span>
                        <strong>&pound;{{ number_format($booking->total_price, 2) }}</strong>
                    </div>
                    @if($booking->corporateInvoice)
                        <hr>
                        <p class="small mb-0">
                            On invoice
                            <a href="{{ route('corporate.invoices.show', $booking->corporateInvoice) }}">{{ $booking->corporateInvoice->reference }}</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
