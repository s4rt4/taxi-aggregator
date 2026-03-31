@extends('layouts.operator')
@section('title', 'Booking Log')

@push('styles')
<style>
    .countdown-ring {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid #dee2e6;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .countdown-ring .value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #333;
    }
    .countdown-ring .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: #999;
        letter-spacing: 0.5px;
    }
    .booking-card {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .booking-card-header {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .booking-ref {
        font-weight: 700;
        color: #333;
    }
    .booking-fare {
        font-weight: 700;
        font-size: 1.1rem;
        color: #333;
    }
    .booking-card-body {
        padding: 1rem;
    }
    .booking-card-body .detail-row {
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }
    .booking-card-body .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 140px;
        display: inline-block;
    }
    .badge-cancelled {
        background-color: #dc3545;
        color: #fff;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 0.25em 0.6em;
        border-radius: 3px;
    }
    .badge-confirmed {
        background-color: #198754;
        color: #fff;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 0.25em 0.6em;
        border-radius: 3px;
    }
    .badge-pending {
        background-color: #ffc107;
        color: #333;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 0.25em 0.6em;
        border-radius: 3px;
    }
    .btn-driver-view {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .btn-driver-view:hover {
        background-color: #157347;
        border-color: #146c43;
        color: #fff;
    }
    .btn-dead-leg {
        background-color: transparent;
        border: 2px solid #fd7e14;
        color: #fd7e14;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .btn-dead-leg:hover {
        background-color: #fd7e14;
        color: #fff;
    }
    .search-section {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Booking Log</h1>
    <div>
        <a href="#" class="text-decoration-none small"><i class="bi bi-plus-circle"></i> Add Fleet Type</a>
        <a href="#" class="text-decoration-none small ms-3"><i class="bi bi-question-circle"></i> Help</a>
    </div>
</div>

{{-- Fleet type tabs --}}
<div class="fleet-tabs mb-3">
    <span class="fleet-tab active">Petrol, Diesel &amp; Hybrid</span>
</div>

{{-- Standard sub-tab --}}
<div class="mb-4">
    <ul class="nav nav-pills gap-2">
        <li class="nav-item">
            <a class="nav-link active px-3 py-2 text-uppercase fw-bold small" href="#">Standard</a>
        </li>
    </ul>
</div>

<div class="row">
    {{-- Left column: Countdown + Bookings --}}
    <div class="col-lg-8">
        {{-- Countdown Widget --}}
        <div class="bg-white rounded border p-3 mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="d-flex gap-3 align-items-center">
                    <div class="countdown-ring">
                        <span class="value" id="countdown-days">{{ $countdownDays ?? 0 }}</span>
                        <span class="label">Days</span>
                    </div>
                    <div class="countdown-ring">
                        <span class="value" id="countdown-hours">{{ $countdownHours ?? 0 }}</span>
                        <span class="label">Hours</span>
                    </div>
                    <div class="countdown-ring">
                        <span class="value" id="countdown-minutes">{{ $countdownMinutes ?? 0 }}</span>
                        <span class="label">Minutes</span>
                    </div>
                </div>
                <div class="ms-3">
                    <span class="text-muted small text-uppercase fw-bold">Next Pickup In</span>
                </div>
            </div>
        </div>

        {{-- Booking Cards --}}
        @forelse($bookings ?? [] as $booking)
        <div class="booking-card">
            <div class="booking-card-header">
                <div>
                    <span class="booking-ref">{{ $booking->reference ?? 'MC-000000' }}</span>
                    <span class="text-muted ms-2 small">{{ $booking->pickup_date ?? '' }} {{ $booking->pickup_time ?? '' }}</span>
                    @if(($booking->status ?? '') === 'cancelled')
                        <span class="badge-cancelled ms-2">Cancelled</span>
                    @elseif(($booking->status ?? '') === 'confirmed')
                        <span class="badge-confirmed ms-2">Confirmed</span>
                    @elseif(($booking->status ?? '') === 'pending')
                        <span class="badge-pending ms-2">Pending</span>
                    @endif
                </div>
                <div class="booking-fare">&pound;{{ number_format($booking->fare ?? 0, 2) }}</div>
            </div>
            <div class="booking-card-body">
                <div class="detail-row">
                    <span class="detail-label">From:</span>
                    {{ $booking->pickup_address ?? 'N/A' }}
                </div>
                <div class="detail-row">
                    <span class="detail-label">To:</span>
                    {{ $booking->dropoff_address ?? 'N/A' }}
                </div>
                <div class="detail-row">
                    <span class="detail-label">Vehicle:</span>
                    {{ $booking->vehicle_type ?? 'Standard' }}, {{ $booking->vehicle_size ?? '1-4' }} seater
                </div>
                <div class="detail-row">
                    <span class="detail-label">Additional Info:</span>
                    {{ $booking->additional_info ?? 'None' }}
                </div>
                <div class="detail-row">
                    <span class="detail-label">Meet &amp; Greet:</span>
                    {{ $booking->meet_greet ? 'Yes' : 'No' }}
                </div>

                <hr class="my-3">

                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="detail-label mb-0">Status:</span>
                        <select class="form-select form-select-sm" style="width: 180px;" name="booking_status">
                            <option value="">Please select</option>
                            <option value="accepted" {{ ($booking->operator_status ?? '') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="driver_assigned" {{ ($booking->operator_status ?? '') === 'driver_assigned' ? 'selected' : '' }}>Driver Assigned</option>
                            <option value="en_route" {{ ($booking->operator_status ?? '') === 'en_route' ? 'selected' : '' }}>En Route</option>
                            <option value="completed" {{ ($booking->operator_status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="no_show" {{ ($booking->operator_status ?? '') === 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-driver-view btn-sm">
                        <i class="bi bi-eye"></i> Enable Driver View!
                    </button>
                    <button type="button" class="btn btn-dead-leg btn-sm">
                        <i class="bi bi-tag"></i> Add Dead Leg Discount
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded border p-4 text-center">
            <i class="bi bi-journal-x fs-1 text-muted"></i>
            <p class="text-muted mt-2 mb-0">No bookings found. Bookings will appear here once they are received.</p>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if(isset($bookings) && $bookings instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-3">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

    {{-- Right column: Search & Filters --}}
    <div class="col-lg-4">
        <div class="search-section">
            <h6 class="fw-bold mb-3">Search</h6>
            <form method="GET" action="{{ route('operator.bookings.index') }}">
                <div class="mb-3">
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Booking ref or Passenger name" value="{{ request('search') }}">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">From</label>
                        <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">To</label>
                        <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <select class="form-select form-select-sm" name="vehicle_type">
                        <option value="">Standard PDN</option>
                        <option value="standard" {{ request('vehicle_type') === 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="executive" {{ request('vehicle_type') === 'executive' ? 'selected' : '' }}>Executive</option>
                        <option value="luxury" {{ request('vehicle_type') === 'luxury' ? 'selected' : '' }}>Luxury</option>
                    </select>
                </div>

                <div class="mb-3">
                    <select class="form-select form-select-sm" name="fleet_size">
                        <option value="">All selected fleet sizes</option>
                        <option value="1-4" {{ request('fleet_size') === '1-4' ? 'selected' : '' }}>1-4 Seater</option>
                        <option value="5-6" {{ request('fleet_size') === '5-6' ? 'selected' : '' }}>5-6 Seater</option>
                        <option value="7" {{ request('fleet_size') === '7' ? 'selected' : '' }}>7 Seater</option>
                        <option value="8" {{ request('fleet_size') === '8' ? 'selected' : '' }}>8 Seater</option>
                    </select>
                </div>

                <div class="mb-3">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All selected statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dld_applied" id="dldApplied" value="1" {{ request('dld_applied') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="dldApplied">DLD applied</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
