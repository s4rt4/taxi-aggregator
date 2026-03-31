@extends('operator.onboarding.layout')
@section('step', '5')

@section('content')
    <h5 class="fw-bold mb-1">Availability & Range</h5>
    <p class="text-muted small mb-4">Set your operating range. You can add detailed availability (hours, notice periods, vehicle counts) later from the dashboard.</p>

    <form method="POST" action="{{ route('operator.onboarding.save-step5') }}">
        @csrf

        @php
            $tripRange = $operator?->tripRange;
        @endphp

        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label class="field-label">Pickup Range (miles) <span class="text-danger">*</span></label>
                <input type="number" name="pickup_range_miles" class="form-control @error('pickup_range_miles') is-invalid @enderror"
                       value="{{ old('pickup_range_miles', $tripRange->pickup_range_miles ?? 30) }}" required min="1" max="500"
                       placeholder="e.g. 30">
                <small class="text-muted">Maximum distance from your base for pickups</small>
                @error('pickup_range_miles')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="field-label">Dropoff Range (miles) <span class="text-danger">*</span></label>
                <input type="number" name="dropoff_range_miles" class="form-control @error('dropoff_range_miles') is-invalid @enderror"
                       value="{{ old('dropoff_range_miles', $tripRange->dropoff_range_miles ?? 200) }}" required min="1" max="500"
                       placeholder="e.g. 200">
                <small class="text-muted">Maximum trip distance for destinations</small>
                @error('dropoff_range_miles')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="alert alert-info small mb-4">
            <i class="bi bi-info-circle me-1"></i>
            You can configure detailed vehicle counts per day, operating hours, and notice periods from the <strong>Availability</strong> section in your dashboard after setup.
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('operator.onboarding.step', 4) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-check-circle me-1"></i> Complete Setup
            </button>
        </div>
    </form>
@endsection
