@extends('layouts.operator')
@section('title', 'Notice Periods')

@push('styles')
<style>
    .notice-description {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        font-size: 0.8125rem;
        color: #495057;
    }
    .fleet-notice-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        align-items: flex-end;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .fleet-notice-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 90px;
    }
    .fleet-notice-item .fleet-icon-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .fleet-notice-item .fleet-icon-wrap i {
        font-size: 1.75rem;
        color: #6c757d;
    }
    .fleet-notice-item .fleet-icon-wrap .fleet-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #495057;
        text-align: center;
        margin-top: 0.15rem;
        line-height: 1.2;
    }
    .fleet-notice-item .notice-input-group {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .fleet-notice-item .notice-input-group input {
        width: 55px;
        text-align: center;
        font-size: 0.8rem;
        padding: 0.25rem 0.35rem;
    }
    .fleet-notice-item .notice-input-group .hours-label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        white-space: nowrap;
    }
    .plt-section {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1.25rem;
        margin-top: 2rem;
    }
    .plt-section h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
    }
    .plt-section .plt-description {
        font-size: 0.8125rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .plt-table thead th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }
    .plt-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #adb5bd;
        font-size: 0.875rem;
    }
    .btn-add-plt {
        font-size: 0.8125rem;
        color: #0d6efd;
        text-decoration: none;
        font-weight: 600;
    }
    .btn-add-plt:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Notice Periods</h1>
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

{{-- Description --}}
<div class="notice-description">
    <p class="mb-0">
        Set the minimum notice period required for each vehicle type. This is the minimum number of hours' notice
        you need before a pickup. For example, if set to 4 hours, customers will not be able to book a pickup that is
        less than 4 hours from the current time for that vehicle type. Lower notice periods may result in more bookings.
    </p>
</div>

<form method="POST" action="#">
    @csrf

    @php
        $fleetNoticeTypes = [
            ['key' => '1_4', 'label' => '1-4 PASSENGERS', 'icon' => 'bi-car-front', 'hours' => 4],
            ['key' => '1_4_estate', 'label' => '1-4 PASSENGERS (ESTATE)', 'icon' => 'bi-car-front', 'hours' => 4],
            ['key' => '5_6', 'label' => '5-6 PASSENGERS', 'icon' => 'bi-car-front', 'hours' => 6],
            ['key' => '7', 'label' => '7 PASSENGERS', 'icon' => 'bi-car-front', 'hours' => 8],
            ['key' => '8', 'label' => '8 PASSENGERS', 'icon' => 'bi-truck', 'hours' => 8],
            ['key' => '9', 'label' => '9 PASSENGERS', 'icon' => 'bi-truck', 'hours' => 12],
            ['key' => '10_14', 'label' => '10-14 PASSENGERS', 'icon' => 'bi-bus-front', 'hours' => 24],
            ['key' => '15_16', 'label' => '15-16 PASSENGERS', 'icon' => 'bi-bus-front', 'hours' => 24],
        ];
    @endphp

    {{-- Fleet notice items --}}
    <div class="fleet-notice-row">
        @foreach($fleetNoticeTypes as $fleet)
        <div class="fleet-notice-item">
            <div class="fleet-icon-wrap">
                <i class="bi {{ $fleet['icon'] }}"></i>
                <span class="fleet-label">{{ $fleet['label'] }}</span>
            </div>
            <div class="notice-input-group">
                <input
                    type="number"
                    class="form-control form-control-sm"
                    name="notice[{{ $fleet['key'] }}]"
                    value="{{ $fleet['hours'] }}"
                    min="0"
                    max="168"
                >
                <span class="hours-label">HOURS<br>NOTICE</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Action buttons --}}
    <div class="d-flex gap-3 mt-3 mb-4">
        <button type="submit" class="btn btn-mc-save">
            <i class="bi bi-check-lg me-1"></i> SAVE CHANGES
        </button>
        <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
            CANCEL AND DISCARD CHANGES
        </button>
    </div>
</form>

{{-- Postcode Lead Times (PLTs) --}}
<div class="plt-section" x-data="pltManager()">
    <h5>Postcode Lead Times (PLTs)</h5>
    <p class="plt-description">
        Postcode Lead Times allow you to set different notice periods for specific pickup areas. This is useful
        if certain areas require longer lead times due to distance or traffic conditions. PLTs override the
        default notice period set above for the specified pickup area and car size combination.
    </p>

    <table class="table plt-table">
        <thead>
            <tr>
                <th style="width: 35%;">Pickup area</th>
                <th style="width: 25%;">Pickup type</th>
                <th style="width: 25%;">Car sizes</th>
                <th style="width: 10%;">Hours</th>
                <th style="width: 5%;"></th>
            </tr>
        </thead>
        <tbody>
            <template x-if="plts.length === 0">
                <tr>
                    <td colspan="5">
                        <div class="plt-empty">
                            <i class="bi bi-geo-alt fs-3 d-block mb-2"></i>
                            No postcode lead times configured. Add one to set custom notice periods for specific pickup areas.
                        </div>
                    </td>
                </tr>
            </template>
            <template x-for="(plt, index) in plts" :key="index">
                <tr>
                    <td>
                        <input type="text" class="form-control form-control-sm" placeholder="e.g., SW1, EC, W1" x-model="plt.area">
                    </td>
                    <td>
                        <select class="form-select form-select-sm" x-model="plt.pickupType">
                            <option value="">Select type</option>
                            <option value="any">Any</option>
                            <option value="airport">Airport</option>
                            <option value="station">Station</option>
                            <option value="port">Port</option>
                            <option value="address">Address</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" x-model="plt.carSize">
                            <option value="">Select size</option>
                            <option value="all">All sizes</option>
                            <option value="1-4">1-4 Passengers</option>
                            <option value="5-6">5-6 Passengers</option>
                            <option value="7">7 Passengers</option>
                            <option value="8">8 Passengers</option>
                            <option value="9">9 Passengers</option>
                            <option value="10-14">10-14 Passengers</option>
                            <option value="15-16">15-16 Passengers</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" placeholder="Hrs" x-model="plt.hours" min="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removePlt(index)" title="Remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <a href="#" class="btn-add-plt" @click.prevent="addPlt()">
        <i class="bi bi-plus-circle me-1"></i> Add new postcode lead time
    </a>
</div>
@endsection

@push('scripts')
<script>
    function pltManager() {
        return {
            plts: [],
            addPlt() {
                this.plts.push({
                    area: '',
                    pickupType: '',
                    carSize: '',
                    hours: ''
                });
            },
            removePlt(index) {
                this.plts.splice(index, 1);
            }
        };
    }
</script>
@endpush
