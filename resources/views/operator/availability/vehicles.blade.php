@extends('layouts.operator')
@section('title', 'Number of Vehicles')

@push('styles')
<style>
    .instruction-text {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    .instruction-text .text-danger {
        font-size: 0.875rem;
        font-weight: 600;
    }
    .instruction-text .text-muted {
        font-size: 0.8125rem;
    }
    .vehicle-row {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
    }
    .vehicle-row .fleet-icon {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 100px;
    }
    .vehicle-row .fleet-icon i {
        font-size: 2rem;
        color: #6c757d;
    }
    .vehicle-row .fleet-icon .fleet-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #495057;
        text-align: center;
        margin-top: 0.25rem;
    }
    .day-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .day-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 50px;
    }
    .day-col .day-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .day-col input {
        width: 50px;
        text-align: center;
        font-size: 0.8rem;
        padding: 0.25rem 0.35rem;
    }
    .same-every-day {
        font-size: 0.75rem;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Number of Vehicles</h1>
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

{{-- Instruction text --}}
<div class="instruction-text">
    <p class="text-danger mb-2">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        Please note: Car sizes refer to the MAXIMUM number of passengers only. For example, a "1-4 passengers" vehicle should comfortably seat up to 4 passengers with luggage. Do not enter vehicles that cannot meet the passenger capacity for that category.
    </p>
    <p class="text-muted mb-0">
        The number of vehicles you enter here determines how many quotes you can provide at any one time.
        For example, if you have 10 vehicles set for Monday in the "1-4 passengers" category, you can have up to 10
        active quotes for that vehicle type on Mondays. Once a vehicle is booked, it becomes unavailable until the
        trip is completed. Enter the suffix "C" after the number (e.g. "20C") to indicate confirmed availability.
    </p>
</div>

{{-- Vehicle rows --}}
<form method="POST" action="#" x-data="vehiclesForm()">
    @csrf

    @php
        $fleetTypes = [
            ['key' => '1_4', 'label' => '1-4 PASSENGERS', 'icon' => 'bi-car-front', 'defaults' => ['20C','20C','20C','20C','20C','15C','10C']],
            ['key' => '1_4_estate', 'label' => '1-4 PASSENGERS (ESTATE)', 'icon' => 'bi-car-front', 'defaults' => ['15C','15C','15C','15C','15C','10C','10C']],
            ['key' => '6', 'label' => '6 PASSENGERS', 'icon' => 'bi-car-front', 'defaults' => ['10C','10C','10C','10C','10C','8C','5C']],
            ['key' => '7', 'label' => '7 PASSENGERS', 'icon' => 'bi-car-front', 'defaults' => ['10C','10C','10C','10C','10C','8C','5C']],
            ['key' => '8', 'label' => '8 PASSENGERS', 'icon' => 'bi-truck', 'defaults' => ['8C','8C','8C','8C','8C','5C','3C']],
            ['key' => '9', 'label' => '9 PASSENGERS', 'icon' => 'bi-truck', 'defaults' => ['5C','5C','5C','5C','5C','3C','2C']],
            ['key' => '10_14', 'label' => '10-14 PASSENGERS', 'icon' => 'bi-bus-front', 'defaults' => ['3C','3C','3C','3C','3C','2C','1C']],
            ['key' => '15_16', 'label' => '15-16 PASSENGERS', 'icon' => 'bi-bus-front', 'defaults' => ['2C','2C','2C','2C','2C','1C','1C']],
        ];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    @endphp

    @foreach($fleetTypes as $fleet)
    <div class="vehicle-row">
        <div class="d-flex flex-wrap align-items-center gap-3">
            {{-- Fleet icon and label --}}
            <div class="fleet-icon">
                <i class="bi {{ $fleet['icon'] }}"></i>
                <span class="fleet-label">{{ $fleet['label'] }}</span>
            </div>

            {{-- Day inputs --}}
            <div class="day-inputs flex-grow-1">
                @foreach($days as $index => $day)
                <div class="day-col">
                    <span class="day-label">{{ $day }}</span>
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        name="vehicles[{{ $fleet['key'] }}][{{ strtolower($day) }}]"
                        value="{{ $fleet['defaults'][$index] }}"
                        x-bind:disabled="sameEveryDay_{{ $fleet['key'] }} && {{ $index }} > 0"
                        x-bind:value="sameEveryDay_{{ $fleet['key'] }} && {{ $index }} > 0 ? mondayVal_{{ $fleet['key'] }} : '{{ $fleet['defaults'][$index] }}'"
                    >
                </div>
                @endforeach
            </div>
        </div>

        {{-- Same every day checkbox --}}
        <div class="mt-2 ms-5 ps-5">
            <div class="form-check same-every-day">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="vehicles[{{ $fleet['key'] }}][same_every_day]"
                    id="sameDay_{{ $fleet['key'] }}"
                    value="1"
                >
                <label class="form-check-label" for="sameDay_{{ $fleet['key'] }}">
                    Same no. of vehicles every day, based on Monday's value
                </label>
            </div>
        </div>
    </div>
    @endforeach

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
    function vehiclesForm() {
        return {
            @foreach($fleetTypes as $fleet)
            sameEveryDay_{{ $fleet['key'] }}: false,
            mondayVal_{{ $fleet['key'] }}: '{{ $fleet['defaults'][0] }}',
            @endforeach
        };
    }
</script>
@endpush
