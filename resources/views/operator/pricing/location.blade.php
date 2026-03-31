@extends('layouts.operator')
@section('title', 'Location Prices')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Location Prices (LPs)</h1>
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
        Location Prices (LPs) allow you to set a fixed price between two specific zones (postcodes).
        This is useful for routes you serve regularly, such as airport transfers, station runs, or popular local routes.
        Location prices take priority over Per Mile Prices and Postcode Area Prices when a matching route is found.
    </p>
    <p class="mb-0 text-muted small">
        You can set prices for each fleet type individually. Use the form below to add new location prices.
    </p>
</div>

{{-- Add Location Price Form --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Add a location price for a 4-seater</h5>

    <form action="#" method="POST">
        @csrf

        <div class="row g-3 align-items-end">
            {{-- Vehicle Type --}}
            <div class="col-md-2">
                <label class="form-label small fw-medium">Vehicle Type</label>
                <select class="form-select form-select-sm" name="vehicle_type">
                    <option value="1_4" selected>1-4 seater</option>
                    <option value="5_6">5-6 seater</option>
                    <option value="7">7 seater</option>
                    <option value="8">8 seater</option>
                    <option value="9">9 seater</option>
                    <option value="10_14">10-14 seater</option>
                    <option value="15_16">15-16 seater</option>
                </select>
            </div>

            {{-- Start Postcode --}}
            <div class="col-md-3">
                <label class="form-label small fw-medium">Start (postcode)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="text"
                           class="form-control form-control-sm"
                           name="start_postcode"
                           placeholder="e.g. SW1A 1AA"
                           style="flex: 1;">
                    <span class="text-muted small text-nowrap">within</span>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <input type="number"
                               class="form-control form-control-sm text-center"
                               name="start_radius"
                               value="3"
                               min="1"
                               max="50">
                        <span class="input-group-text">miles</span>
                    </div>
                </div>
            </div>

            {{-- Finish Postcode --}}
            <div class="col-md-3">
                <label class="form-label small fw-medium">Finish (postcode)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="text"
                           class="form-control form-control-sm"
                           name="finish_postcode"
                           placeholder="e.g. LHR"
                           style="flex: 1;">
                    <span class="text-muted small text-nowrap">within</span>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <input type="number"
                               class="form-control form-control-sm text-center"
                               name="finish_radius"
                               value="3"
                               min="1"
                               max="50">
                        <span class="input-group-text">miles</span>
                    </div>
                </div>
            </div>

            {{-- Single Price --}}
            <div class="col-md-2">
                <label class="form-label small fw-medium">Single price</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">&pound;</span>
                    <input type="number"
                           class="form-control form-control-sm"
                           name="single_price"
                           placeholder="0.00"
                           step="0.01"
                           min="0">
                </div>
            </div>

            {{-- Add Button --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-sm w-100">
                    <i class="bi bi-plus-circle"></i> Add
                </button>
            </div>
        </div>

        {{-- Reverse direction checkbox --}}
        <div class="form-check mt-3">
            <input class="form-check-input"
                   type="checkbox"
                   name="reverse_direction"
                   id="reverseDirection"
                   value="1"
                   checked>
            <label class="form-check-label small" for="reverseDirection">
                Also create a price for reverse direction?
            </label>
        </div>
    </form>
</div>

{{-- Existing Location Prices --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Your location prices</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Vehicle Type</th>
                    <th>Start Location</th>
                    <th>Start Radius</th>
                    <th>Finish Location</th>
                    <th>Finish Radius</th>
                    <th class="text-center">Price</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locationPrices ?? [] as $lp)
                <tr>
                    <td>{{ $lp->vehicle_type_label ?? '' }}</td>
                    <td>{{ $lp->start_postcode ?? '' }}</td>
                    <td>{{ $lp->start_radius ?? '' }} miles</td>
                    <td>{{ $lp->finish_postcode ?? '' }}</td>
                    <td>{{ $lp->finish_radius ?? '' }} miles</td>
                    <td class="text-center">&pound;{{ number_format($lp->price ?? 0, 2) }}</td>
                    <td class="text-center">
                        <button class="btn btn-outline-danger btn-sm" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-geo-alt fs-2 d-block mb-2"></i>
                        No location prices set yet. Use the form above to add your first location price.
                    </td>
                </tr>
                @endforelse
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
