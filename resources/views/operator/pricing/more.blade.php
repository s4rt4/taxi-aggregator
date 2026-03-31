@extends('layouts.operator')
@section('title', 'More Pricing Options')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>More Pricing Options</h1>
    <div>
        <span class="text-muted small">Help</span>
    </div>
</div>

{{-- Fleet type tabs --}}
<div class="fleet-tabs mb-3">
    <span class="fleet-tab active">Petrol, Diesel & Hybrid</span>
</div>

{{-- Sub tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="#">Standard</a>
    </li>
</ul>

{{-- More Pricing Options Tab Navigation --}}
@php
    $activeTab = request('tab', 'fpa');
@endphp
<ul class="nav nav-pills mb-4 flex-wrap gap-1" id="morePricingTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'fpa' ? 'active' : '' }} small"
                id="fpa-tab"
                data-bs-toggle="pill"
                data-bs-target="#fpa"
                type="button"
                role="tab"
                aria-controls="fpa"
                aria-selected="{{ $activeTab === 'fpa' ? 'true' : 'false' }}">
            Free Pickup Areas (FPA)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'wkd-return' ? 'active' : '' }} small"
                id="wkd-return-tab"
                data-bs-toggle="pill"
                data-bs-target="#wkd-return"
                type="button"
                role="tab"
                aria-controls="wkd-return"
                aria-selected="{{ $activeTab === 'wkd-return' ? 'true' : 'false' }}">
            Wkd and Return
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'overnight' ? 'active' : '' }} small"
                id="overnight-tab"
                data-bs-toggle="pill"
                data-bs-target="#overnight"
                type="button"
                role="tab"
                aria-controls="overnight"
                aria-selected="{{ $activeTab === 'overnight' ? 'true' : 'false' }}">
            Overnight Charges
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'holiday' ? 'active' : '' }} small"
                id="holiday-tab"
                data-bs-toggle="pill"
                data-bs-target="#holiday"
                type="button"
                role="tab"
                aria-controls="holiday"
                aria-selected="{{ $activeTab === 'holiday' ? 'true' : 'false' }}">
            Public Holiday Uplift
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'va-settings' ? 'active' : '' }} small"
                id="va-settings-tab"
                data-bs-toggle="pill"
                data-bs-target="#va-settings"
                type="button"
                role="tab"
                aria-controls="va-settings"
                aria-selected="{{ $activeTab === 'va-settings' ? 'true' : 'false' }}">
            VA Settings
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'cash-radius' ? 'active' : '' }} small"
                id="cash-radius-tab"
                data-bs-toggle="pill"
                data-bs-target="#cash-radius"
                type="button"
                role="tab"
                aria-controls="cash-radius"
                aria-selected="{{ $activeTab === 'cash-radius' ? 'true' : 'false' }}">
            Cash Payment Radius
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'add-fleet' ? 'active' : '' }} small"
                id="add-fleet-tab"
                data-bs-toggle="pill"
                data-bs-target="#add-fleet"
                type="button"
                role="tab"
                aria-controls="add-fleet"
                aria-selected="{{ $activeTab === 'add-fleet' ? 'true' : 'false' }}">
            <i class="bi bi-plus-circle"></i> Add Fleet Type
        </button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content" id="morePricingTabContent">

    {{-- Free Pickup Areas (FPA) Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'fpa' ? 'show active' : '' }}" id="fpa" role="tabpanel" aria-labelledby="fpa-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <div class="alert alert-danger mb-3">
                <strong>The pickup areas you can cover without charging to get from your base to the pickup point.</strong>
            </div>

            <p class="text-muted small mb-3">
                Free Pickup Areas define the postcode areas within which you will not charge passengers an extra fee
                for travelling from your base to their pickup location. This is essentially your local coverage area.
                Pickups outside these areas may incur a "dead mile" charge or may not be served at all.
            </p>
            <p class="text-muted small mb-3">
                Add or remove postcode areas to manage where you offer free pickups. The more areas you cover,
                the more bookings you may receive, but you should consider the cost of travelling to distant
                pickup locations.
            </p>
        </div>

        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">Free pickup postcode areas</h5>

            {{-- Add new postcode area --}}
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Add a postcode area</label>
                    <div class="input-group input-group-sm">
                        <input type="text"
                               class="form-control form-control-sm"
                               name="new_fpa_postcode"
                               placeholder="e.g. SW1, EC2, TW5">
                        <button class="btn btn-success btn-sm" type="button">
                            <i class="bi bi-plus-circle"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            {{-- Existing postcode areas table --}}
            @php
                $freePickupAreas = [
                    'TW5', 'PO1', 'PO16', 'PO17', 'PO11', 'PO12', 'PO13', 'PO14', 'PO15',
                    'PO16', 'PO17', 'PO18', 'PO19', 'PO2', 'PO20', 'PO21', 'PO22', 'PO3',
                    'PO30', 'PO31', 'PO32', 'PO33', 'PO34', 'PO35', 'PO36', 'PO37', 'PO38',
                    'PO39', 'PO4', 'PO40', 'PO41', 'PO5', 'PO6', 'PO7', 'PO8', 'PO9',
                    'SO14', 'SO15', 'SO16', 'SO17', 'SO18', 'SO19', 'SO20', 'SO21', 'SO22',
                    'SO23', 'SO24', 'SO30', 'SO31', 'SO32', 'SO40', 'SO41', 'SO42', 'SO43',
                    'SO45', 'SO50', 'SO51', 'SO52', 'SO53', 'BH1', 'BH2', 'BH3', 'BH4',
                    'BH5', 'BH6', 'BH7', 'BH8', 'BH9', 'BH10', 'BH11', 'BH12', 'BH13',
                    'BH14', 'BH15', 'BH16', 'BH17', 'BH18', 'BH19', 'BH20', 'BH21', 'BH22',
                    'BH23', 'BH24', 'BH25', 'BH31', 'SP1', 'SP2', 'SP3', 'SP4', 'SP5',
                    'SP6', 'SP7', 'SP8', 'GU1', 'GU2', 'GU3', 'GU4', 'GU5', 'GU7', 'GU8',
                    'GU9', 'GU10', 'GU11', 'GU12', 'GU14', 'GU15', 'GU16', 'GU17', 'GU18',
                    'GU19', 'GU20', 'GU21', 'GU22', 'GU23', 'GU24', 'GU25', 'GU26', 'GU27',
                    'GU28', 'GU29', 'GU30', 'GU31', 'GU32', 'GU33', 'GU34', 'GU35', 'GU46',
                    'GU47', 'GU51', 'GU52',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Pickup area</th>
                            <th class="text-center" style="width: 80px;">Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($freePickupAreas as $area)
                        <tr>
                            <td class="text-start">
                                <span class="badge bg-light text-dark border">{{ $area }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" title="Remove {{ $area }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Weekend and Return Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'wkd-return' ? 'show active' : '' }}" id="wkd-return" role="tabpanel" aria-labelledby="wkd-return-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">Weekend & Return Trip Settings</h5>
            <p class="text-muted small mb-3">
                Configure additional charges or discounts for weekend bookings and return journeys.
            </p>

            <div class="row g-4">
                {{-- Weekend Uplift --}}
                <div class="col-md-6">
                    <div class="border rounded p-3">
                        <h6 class="fw-bold mb-3">Weekend Uplift</h6>
                        <p class="text-muted small mb-3">
                            Set a percentage uplift for journeys that take place on weekends (Saturday & Sunday).
                        </p>
                        <div class="row g-2">
                            <div class="col-8">
                                <label class="form-label small">Saturday uplift</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" name="saturday_uplift" value="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-8">
                                <label class="form-label small">Sunday uplift</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" name="sunday_uplift" value="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Return Discount --}}
                <div class="col-md-6">
                    <div class="border rounded p-3">
                        <h6 class="fw-bold mb-3">Return Trip Discount</h6>
                        <p class="text-muted small mb-3">
                            Offer a discount when customers book a return journey.
                        </p>
                        <div class="col-8">
                            <label class="form-label small">Return trip discount</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm" name="return_discount" value="0" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Overnight Charges Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'overnight' ? 'show active' : '' }}" id="overnight" role="tabpanel" aria-labelledby="overnight-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">Overnight Charges</h5>
            <p class="text-muted small mb-3">
                Set additional charges for journeys that take place during overnight hours.
                Overnight charges are applied as a percentage uplift on the base fare.
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Overnight period starts at</label>
                    <select class="form-select form-select-sm" name="overnight_start">
                        @for($h = 18; $h <= 23; $h++)
                        <option value="{{ $h }}" {{ $h === 22 ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                        @endfor
                        <option value="0">00:00</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Overnight period ends at</label>
                    <select class="form-select form-select-sm" name="overnight_end">
                        @for($h = 4; $h <= 9; $h++)
                        <option value="{{ $h }}" {{ $h === 6 ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Overnight uplift</label>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control form-control-sm" name="overnight_uplift" value="0" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Public Holiday Uplift Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'holiday' ? 'show active' : '' }}" id="holiday" role="tabpanel" aria-labelledby="holiday-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">Public Holiday Uplift</h5>
            <p class="text-muted small mb-3">
                Set a percentage uplift for journeys booked on public holidays.
                This uplift applies to the base journey fare.
            </p>

            @php
                $holidays = [
                    "New Year's Day",
                    'Good Friday',
                    'Easter Monday',
                    'Early May Bank Holiday',
                    'Spring Bank Holiday',
                    'Summer Bank Holiday',
                    'Christmas Day',
                    'Boxing Day',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Holiday</th>
                            <th class="text-center" style="width: 180px;">Uplift (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holidays as $index => $holiday)
                        <tr>
                            <td class="text-start">{{ $holiday }}</td>
                            <td>
                                <div class="input-group input-group-sm mx-auto" style="max-width: 140px;">
                                    <input type="number"
                                           class="form-control form-control-sm text-center"
                                           name="holiday_uplift[{{ $index }}]"
                                           value="0"
                                           min="0"
                                           max="200">
                                    <span class="input-group-text">%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- VA Settings Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'va-settings' ? 'show active' : '' }}" id="va-settings" role="tabpanel" aria-labelledby="va-settings-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">VA Settings (Value Add)</h5>
            <p class="text-muted small mb-3">
                Configure Value Add settings that affect how your prices are presented to customers.
                These settings adjust the final displayed price without changing your base rates.
            </p>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-medium">VA percentage adjustment</label>
                    <div class="input-group input-group-sm">
                        <input type="number"
                               class="form-control form-control-sm"
                               name="va_percentage"
                               value="0"
                               step="0.1"
                               min="-50"
                               max="50">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="form-text text-muted">A positive value increases your displayed price; negative decreases it.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-medium">VA rounding</label>
                    <select class="form-select form-select-sm" name="va_rounding">
                        <option value="none">No rounding</option>
                        <option value="nearest_pound">Nearest pound</option>
                        <option value="round_up">Always round up</option>
                        <option value="round_down">Always round down</option>
                        <option value="nearest_50p">Nearest 50p</option>
                    </select>
                    <small class="form-text text-muted">How to round the final displayed price after VA adjustment.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Cash Payment Radius Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'cash-radius' ? 'show active' : '' }}" id="cash-radius" role="tabpanel" aria-labelledby="cash-radius-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">Cash Payment Radius</h5>
            <p class="text-muted small mb-3">
                Define the maximum distance from your base within which you accept cash payments.
                Beyond this radius, customers must pay by card or account. This helps protect your drivers
                on longer journeys.
            </p>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-medium">Cash payment radius</label>
                    <div class="input-group input-group-sm">
                        <input type="number"
                               class="form-control form-control-sm"
                               name="cash_radius"
                               value="50"
                               min="0"
                               max="500">
                        <span class="input-group-text">miles</span>
                    </div>
                    <small class="form-text text-muted">Set to 0 to disable cash payments entirely.</small>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="cash_enabled" id="cashEnabled" value="1" checked>
                        <label class="form-check-label small" for="cashEnabled">
                            Accept cash payments within the radius
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Fleet Type Tab --}}
    <div class="tab-pane fade {{ $activeTab === 'add-fleet' ? 'show active' : '' }}" id="add-fleet" role="tabpanel" aria-labelledby="add-fleet-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <h5 class="fw-bold mb-3">Add Fleet Type</h5>
            <p class="text-muted small mb-3">
                Add a new fleet type to your operator profile. Once added, you will be able to set
                pricing and availability for this fleet type across all pricing pages.
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Fleet type name</label>
                    <input type="text"
                           class="form-control form-control-sm"
                           name="fleet_type_name"
                           placeholder="e.g. Executive Saloon">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Passenger capacity</label>
                    <select class="form-select form-select-sm" name="fleet_capacity">
                        <option value="1_4">1-4 passengers</option>
                        <option value="5_6">5-6 passengers</option>
                        <option value="7">7 passengers</option>
                        <option value="8">8 passengers</option>
                        <option value="9">9 passengers</option>
                        <option value="10_14">10-14 passengers</option>
                        <option value="15_16">15-16 passengers</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Fuel type</label>
                    <select class="form-select form-select-sm" name="fleet_fuel_type">
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="electric">Electric</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-plus-circle"></i> Add Fleet Type
                    </button>
                </div>
            </div>

            {{-- Current fleet types --}}
            <h6 class="fw-bold mt-4 mb-3">Current fleet types</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fleet Type</th>
                            <th>Capacity</th>
                            <th>Fuel Type</th>
                            <th class="text-center" style="width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Standard Saloon</td>
                            <td>1-4 passengers</td>
                            <td>Petrol, Diesel & Hybrid</td>
                            <td class="text-center"><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Estate</td>
                            <td>5-6 passengers</td>
                            <td>Petrol, Diesel & Hybrid</td>
                            <td class="text-center"><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>MPV</td>
                            <td>7 passengers</td>
                            <td>Petrol, Diesel & Hybrid</td>
                            <td class="text-center"><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>8 Seater Minibus</td>
                            <td>8 passengers</td>
                            <td>Diesel</td>
                            <td class="text-center"><span class="badge bg-success">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Action Buttons --}}
<div class="d-flex gap-3 mb-4">
    <button type="submit" class="btn btn-success text-uppercase fw-bold px-4">
        Save Changes
    </button>
    <button type="button" class="btn btn-secondary text-uppercase fw-bold px-4">
        Cancel and Discard Changes
    </button>
</div>
@endsection
