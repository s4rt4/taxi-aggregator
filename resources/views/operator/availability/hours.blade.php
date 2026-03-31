@extends('layouts.operator')
@section('title', 'Operating Hours')

@push('styles')
<style>
    .hours-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .hours-card h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.5rem;
    }
    .hours-description {
        font-size: 0.8125rem;
        color: #495057;
        margin-bottom: 1.25rem;
    }
    .around-clock-toggle {
        background: #f0fdf4;
        border: 2px solid #86efac;
        border-radius: 6px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        cursor: pointer;
    }
    .around-clock-toggle .clock-icon {
        font-size: 1.5rem;
        color: #16a34a;
    }
    .around-clock-toggle .clock-text {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #15803d;
    }
    .around-clock-toggle .clock-sub {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 400;
    }
    .custom-hours-section {
        margin-top: 1rem;
    }
    .time-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f1f3f5;
    }
    .time-row:last-child {
        border-bottom: none;
    }
    .time-row .fleet-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 180px;
    }
    .time-row .fleet-info i {
        font-size: 1.25rem;
        color: #6c757d;
    }
    .time-row .fleet-info .fleet-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #495057;
    }
    .time-row .time-inputs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .time-row .time-inputs .time-separator {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
    }
    .time-row .time-inputs input[type="time"] {
        width: 120px;
        font-size: 0.8125rem;
    }
    .time-row .time-inputs select {
        width: 120px;
        font-size: 0.8125rem;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Operating Hours</h1>
    <div>
        <a href="#" class="text-decoration-none small"><i class="bi bi-plus-circle"></i> Add Fleet Type</a>
        <a href="#" class="text-decoration-none small ms-3"><i class="bi bi-question-circle"></i> Help</a>
    </div>
</div>

{{-- Fleet type tabs --}}
<div class="fleet-tabs mb-3">
    <span class="fleet-tab active">Petrol, Diesel &amp; Hybrid</span>
</div>

<form method="POST" action="#" x-data="operatingHours()">
    @csrf

    <div class="hours-card">
        <h5>What are the times of the day/night you will do pickups?</h5>
        <p class="hours-description">
            Select the hours during which you are available to do pickups. Customers will only be able to book
            pickups within the hours you set here. If your operating hours vary by vehicle type, uncheck the
            "around the clock" option and set specific hours for each vehicle type.
        </p>

        {{-- Around the clock toggle --}}
        <label class="around-clock-toggle" :class="{ 'border-success': aroundTheClock, 'border-secondary': !aroundTheClock }"
               :style="aroundTheClock ? 'background: #f0fdf4; border-color: #86efac;' : 'background: #f8f9fa; border-color: #dee2e6;'">
            <input type="checkbox" class="form-check-input" x-model="aroundTheClock" name="around_the_clock" value="1" style="display: none;">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" x-model="aroundTheClock" style="width: 2.5em; height: 1.25em;">
            </div>
            <div>
                <span class="clock-icon"><i class="bi bi-clock-fill"></i></span>
            </div>
            <div>
                <div class="clock-text">We work around the clock</div>
                <div class="clock-sub">Available 24 hours a day, 7 days a week for all vehicle types</div>
            </div>
        </label>

        {{-- Custom hours section (shown when around-the-clock is OFF) --}}
        <div class="custom-hours-section" x-show="!aroundTheClock" x-transition>
            <h6 class="fw-bold text-muted text-uppercase small mb-3">Custom operating hours by vehicle type</h6>

            @php
                $fleetHoursTypes = [
                    ['key' => '1_4', 'label' => '1-4 Passengers', 'icon' => 'bi-car-front'],
                    ['key' => '1_4_estate', 'label' => '1-4 Passengers (Estate)', 'icon' => 'bi-car-front'],
                    ['key' => '6', 'label' => '6 Passengers', 'icon' => 'bi-car-front'],
                    ['key' => '7', 'label' => '7 Passengers', 'icon' => 'bi-car-front'],
                    ['key' => '8', 'label' => '8 Passengers', 'icon' => 'bi-truck'],
                    ['key' => '9', 'label' => '9 Passengers', 'icon' => 'bi-truck'],
                    ['key' => '10_14', 'label' => '10-14 Passengers', 'icon' => 'bi-bus-front'],
                    ['key' => '15_16', 'label' => '15-16 Passengers', 'icon' => 'bi-bus-front'],
                ];
            @endphp

            @foreach($fleetHoursTypes as $fleet)
            <div class="time-row">
                <div class="fleet-info">
                    <i class="bi {{ $fleet['icon'] }}"></i>
                    <span class="fleet-name">{{ $fleet['label'] }}</span>
                </div>
                <div class="time-inputs">
                    <select class="form-select form-select-sm" name="hours[{{ $fleet['key'] }}][from]">
                        @for($h = 0; $h < 24; $h++)
                            <option value="{{ sprintf('%02d:00', $h) }}" {{ $h === 0 ? 'selected' : '' }}>
                                {{ sprintf('%02d:00', $h) }}
                            </option>
                            <option value="{{ sprintf('%02d:30', $h) }}">
                                {{ sprintf('%02d:30', $h) }}
                            </option>
                        @endfor
                    </select>
                    <span class="time-separator">to</span>
                    <select class="form-select form-select-sm" name="hours[{{ $fleet['key'] }}][to]">
                        @for($h = 0; $h < 24; $h++)
                            <option value="{{ sprintf('%02d:00', $h) }}" {{ $h === 23 ? 'selected' : '' }}>
                                {{ sprintf('%02d:00', $h) }}
                            </option>
                            <option value="{{ sprintf('%02d:30', $h) }}">
                                {{ sprintf('%02d:30', $h) }}
                            </option>
                        @endfor
                        <option value="23:59" selected>23:59</option>
                    </select>
                </div>
            </div>
            @endforeach
        </div>
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

@push('scripts')
<script>
    function operatingHours() {
        return {
            aroundTheClock: true
        };
    }
</script>
@endpush
