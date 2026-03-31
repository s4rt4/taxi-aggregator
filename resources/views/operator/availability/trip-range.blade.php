@extends('layouts.operator')
@section('title', 'Trip Range')

@push('styles')
<style>
    .trip-range-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .trip-range-card h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 1rem;
    }
    .range-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .range-input-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #495057;
        white-space: nowrap;
        min-width: 210px;
    }
    .range-input-group input {
        width: 100px;
        text-align: center;
    }
    .range-input-group .unit-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
    }
    .trip-range-description {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem 1.25rem;
        font-size: 0.8125rem;
        color: #495057;
        margin-top: 1.5rem;
    }
    .trip-range-note {
        background: #fff3cd;
        border: 1px solid #ffe69c;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        font-size: 0.8125rem;
        color: #664d03;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Trip Range</h1>
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

<form method="POST" action="#">
    @csrf

    <div class="trip-range-card">
        <h5>How far will you travel?</h5>

        <div class="row g-4">
            {{-- Pickup range --}}
            <div class="col-md-6">
                <div class="range-input-group">
                    <label for="pickup_range">Pickup range from office base:</label>
                    <input
                        type="number"
                        class="form-control form-control-sm"
                        id="pickup_range"
                        name="pickup_range"
                        value="{{ old('pickup_range', 100) }}"
                        min="1"
                        max="500"
                    >
                    <span class="unit-label">miles</span>
                </div>
            </div>

            {{-- Dropoff range --}}
            <div class="col-md-6">
                <div class="range-input-group">
                    <label for="dropoff_range">Dropoff range from office base:</label>
                    <input
                        type="number"
                        class="form-control form-control-sm"
                        id="dropoff_range"
                        name="dropoff_range"
                        value="{{ old('dropoff_range', 200) }}"
                        min="1"
                        max="500"
                    >
                    <span class="unit-label">miles</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="trip-range-description">
        <p class="mb-2">
            <strong>How pickup and dropoff ranges work:</strong>
        </p>
        <p class="mb-2">
            The <strong>pickup range</strong> determines how far from your office base a customer's pickup location can be
            for you to receive quote requests. The <strong>dropoff range</strong> determines how far from your office base a
            customer's destination can be.
        </p>
        <p class="mb-2">
            For mileage-based jobs, the system calculates the distance from your office base postcode to the pickup
            point, and from your office base to the dropoff point. If either exceeds your set range, you will not
            receive a quote request for that job.
        </p>
        <p class="mb-0">
            Setting a wider range means you will receive more quote requests, but you should only set ranges that you
            can realistically and reliably service.
        </p>
    </div>

    {{-- Note about 500 miles --}}
    <div class="trip-range-note">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Note:</strong> The maximum range is 500 miles. If you need to increase your range beyond 500 miles,
        please contact the admin team at
        <a href="mailto:support@taxiaggregator.co.uk">support@taxiaggregator.co.uk</a>
        and they will be able to assist you.
    </div>

    {{-- Action buttons --}}
    <div class="d-flex gap-3 mt-4">
        <button type="submit" class="btn btn-mc-save">
            <i class="bi bi-check-lg me-1"></i> SAVE CHANGES
        </button>
        <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
            CANCEL AND DISCARD CHANGES
        </button>
    </div>
</form>
@endsection
