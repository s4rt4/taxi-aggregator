@extends('layouts.operator')
@section('title', 'Per Mile Prices')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Per Mile Prices (PMP)</h1>
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

{{-- Description --}}
<div class="bg-white rounded border p-4 mb-4">
    <p class="mb-2">
        Per Mile Prices (PMP) allow you to set mileage-based rates for each fleet type.
        The system uses these rates to calculate journey prices based on the total mileage of a trip.
        You can set different rates for different distance bands and fleet types.
    </p>
    <p class="mb-0 text-muted small">
        Rates shown are inclusive of minicabit commission and VAT. Prices are calculated using the mileage rate for each distance band.
    </p>
</div>

{{-- Mileage Rates Section --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Mileage rates</h5>
    <p class="text-muted small mb-3">
        Set your per mile rate for each distance band and fleet type. The rate applies to all miles within that band.
    </p>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start" style="min-width: 120px;">Mile range</th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">1-4 seater</small>
                        <span class="small">&pound; per mile rate for 1-04 minicabit commission (incl VAT)</span>
                    </th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">5-6 seater</small>
                    </th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">7 seater</small>
                    </th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">8 seater</small>
                    </th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">9 seater</small>
                    </th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">10-14 seater</small>
                    </th>
                    <th style="min-width: 100px;">
                        <small class="d-block text-muted">15-16 seater</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $mileRanges = [
                        '0 - 5 miles',
                        '5 - 10 miles',
                        '10 - 20 miles',
                        '20 - 40 miles',
                        '40 - 60 miles',
                        '60 - 80 miles',
                        '80 - 100 miles',
                        '100+ miles',
                    ];
                    $fleetTypes = ['1_4', '5_6', '7', '8', '9', '10_14', '15_16'];
                    $defaultRates = [
                        ['1.00', '1.20', '1.40', '1.60', '1.80', '2.20', '2.80'],
                        ['0.80', '1.00', '1.20', '1.40', '1.60', '2.00', '2.50'],
                        ['0.70', '0.90', '1.10', '1.30', '1.50', '1.80', '2.30'],
                        ['0.60', '0.80', '1.00', '1.20', '1.40', '1.60', '2.10'],
                        ['0.55', '0.75', '0.95', '1.15', '1.35', '1.50', '2.00'],
                        ['0.50', '0.70', '0.90', '1.10', '1.30', '1.40', '1.90'],
                        ['0.45', '0.65', '0.85', '1.05', '1.25', '1.35', '1.80'],
                        ['0.40', '0.60', '0.80', '1.00', '1.20', '1.30', '1.70'],
                    ];
                @endphp
                @foreach($mileRanges as $index => $range)
                <tr>
                    <td class="text-start fw-medium">{{ $range }}</td>
                    @foreach($fleetTypes as $fIndex => $fleet)
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">&pound;</span>
                            <input type="number"
                                   class="form-control form-control-sm text-center"
                                   name="rates[{{ $fleet }}][{{ $index }}]"
                                   value="{{ old("rates.{$fleet}.{$index}", $defaultRates[$index][$fIndex] ?? '0.00') }}"
                                   step="0.01"
                                   min="0">
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Minimum Fare --}}
    <h6 class="fw-bold mt-4 mb-3">Minimum fare (&pound;)</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start" style="min-width: 120px;">Minimum fare</th>
                    <th style="min-width: 100px;"><small>1-4 seater</small></th>
                    <th style="min-width: 100px;"><small>5-6 seater</small></th>
                    <th style="min-width: 100px;"><small>7 seater</small></th>
                    <th style="min-width: 100px;"><small>8 seater</small></th>
                    <th style="min-width: 100px;"><small>9 seater</small></th>
                    <th style="min-width: 100px;"><small>10-14 seater</small></th>
                    <th style="min-width: 100px;"><small>15-16 seater</small></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-start fw-medium">Min. fare</td>
                    @php
                        $minFares = ['5.00', '6.00', '7.00', '8.00', '10.00', '12.00', '15.00'];
                    @endphp
                    @foreach($fleetTypes as $fIndex => $fleet)
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">&pound;</span>
                            <input type="number"
                                   class="form-control form-control-sm text-center"
                                   name="min_fare[{{ $fleet }}]"
                                   value="{{ old("min_fare.{$fleet}", $minFares[$fIndex]) }}"
                                   step="0.01"
                                   min="0">
                        </div>
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Uplift Pricing Section --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Uplift pricing</h5>
    <p class="text-muted small mb-3">
        Set uplift percentages for each fleet type and distance band. Uplifts are applied as a percentage increase
        to the base per-mile rate, allowing you to add premiums for specific fleet types at certain distances.
        For example, a 10% uplift on a &pound;1.00 per mile rate would result in a &pound;1.10 per mile charge.
    </p>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start" style="min-width: 120px;">Fleet type</th>
                    @foreach($mileRanges as $range)
                    <th style="min-width: 90px;"><small>{{ $range }}</small></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $fleetLabels = ['1-4 seater', '5-6 seater', '7 seater', '8 seater', '9 seater', '10-14 seater', '15-16 seater'];
                @endphp
                @foreach($fleetTypes as $fIndex => $fleet)
                <tr>
                    <td class="text-start fw-medium">{{ $fleetLabels[$fIndex] }}</td>
                    @foreach($mileRanges as $rIndex => $range)
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number"
                                   class="form-control form-control-sm text-center"
                                   name="uplift[{{ $fleet }}][{{ $rIndex }}]"
                                   value="{{ old("uplift.{$fleet}.{$rIndex}", '0') }}"
                                   step="1"
                                   min="0"
                                   max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Mileage Pricing Calculator Section --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Mileage pricing calculator</h5>
    <p class="text-muted small mb-3">
        This table shows the calculated journey prices for common distances based on your per-mile rates and minimum fares above.
        Use this as a reference to check your pricing is competitive. Prices are calculated by applying the relevant band rate
        to each portion of the journey distance.
    </p>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start" style="min-width: 120px;">Distance</th>
                    <th style="min-width: 100px;"><small>1-4 seater</small></th>
                    <th style="min-width: 100px;"><small>5-6 seater</small></th>
                    <th style="min-width: 100px;"><small>7 seater</small></th>
                    <th style="min-width: 100px;"><small>8 seater</small></th>
                    <th style="min-width: 100px;"><small>9 seater</small></th>
                    <th style="min-width: 100px;"><small>10-14 seater</small></th>
                    <th style="min-width: 100px;"><small>15-16 seater</small></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $calcDistances = [1, 3, 5, 7, 10, 15, 20, 30, 50, 100];
                    // Example calculated prices based on default rates
                    $calcPrices = [
                        [5.00, 6.00, 7.00, 8.00, 10.00, 12.00, 15.00],
                        [5.00, 6.00, 7.00, 8.00, 10.00, 12.00, 15.00],
                        [5.00, 6.00, 7.00, 8.00, 10.00, 12.00, 15.00],
                        [6.20, 7.60, 9.00, 10.40, 11.80, 14.40, 18.20],
                        [8.60, 10.60, 12.60, 14.60, 16.60, 20.40, 25.70],
                        [12.10, 15.10, 18.10, 21.10, 24.10, 29.40, 37.20],
                        [15.60, 19.60, 23.60, 27.60, 31.60, 38.40, 48.70],
                        [27.60, 35.60, 43.60, 51.60, 59.60, 70.40, 90.70],
                        [49.60, 65.60, 81.60, 97.60, 113.60, 128.40, 168.70],
                        [89.60, 119.60, 149.60, 179.60, 209.60, 232.40, 306.70],
                    ];
                @endphp
                @foreach($calcDistances as $dIndex => $distance)
                <tr>
                    <td class="text-start fw-medium">{{ $distance }} {{ $distance === 1 ? 'mile' : 'miles' }}</td>
                    @foreach($fleetTypes as $fIndex => $fleet)
                    <td class="text-muted">
                        &pound;{{ number_format($calcPrices[$dIndex][$fIndex], 2) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
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
