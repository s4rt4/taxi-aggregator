@extends('layouts.operator')
@section('title', 'Flash Sales')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Flash Sales</h1>
    <div>
        <a href="#" class="text-decoration-none text-muted small">Help</a>
    </div>
</div>

{{-- Add Flash Sale Section --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Add Flash Sale</h5>

    <form action="#" method="POST">
        @csrf

        {{-- Date/Time Row --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Starting on</label>
                <div class="row g-2">
                    <div class="col-7">
                        <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-5">
                        <input type="time" class="form-control" name="start_time" value="00:00">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ending on</label>
                <div class="row g-2">
                    <div class="col-7">
                        <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                    </div>
                    <div class="col-5">
                        <input type="time" class="form-control" name="end_time" value="23:59">
                    </div>
                </div>
            </div>
        </div>

        <p class="text-muted small mb-3">
            Flash sales apply to all bookings with a pickup date/time within the sale period.
            The sale will automatically expire at the end date and time.
        </p>

        {{-- Apply to --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Apply to:</label>
            <div class="mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="flash_all_sizes" name="all_car_sizes" checked>
                    <label class="form-check-label fw-semibold" for="flash_all_sizes">All car sizes</label>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #4caf50; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="1-4" checked>
                    <i class="bi bi-check2 me-1"></i> 1-4 Passengers
                </span>
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #4caf50; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="5-6" checked>
                    <i class="bi bi-check2 me-1"></i> 5-6 Passengers
                </span>
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #2196f3; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="7" checked>
                    <i class="bi bi-check2 me-1"></i> 7 Passengers
                </span>
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #ff9800; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="8" checked>
                    <i class="bi bi-check2 me-1"></i> 8 Passengers
                </span>
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #4caf50; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="9" checked>
                    <i class="bi bi-check2 me-1"></i> 9 Passengers
                </span>
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #009688; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="10-14" checked>
                    <i class="bi bi-check2 me-1"></i> 10-14 Passengers
                </span>
                <span class="badge rounded-pill fs-6 fw-normal py-2 px-3" style="background-color: #009688; cursor: pointer;">
                    <input type="checkbox" class="d-none" name="car_sizes[]" value="15-16" checked>
                    <i class="bi bi-check2 me-1"></i> 15-16 Passengers
                </span>
            </div>
        </div>

        {{-- Discount Type --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Discount</label>
            <div class="d-flex align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="discount_type" id="discount_percent" value="percent" checked>
                    <label class="form-check-label" for="discount_percent">%</label>
                </div>
                <div class="input-group" style="max-width: 180px;">
                    <input type="number" class="form-control" name="discount_value" placeholder="10" min="1" max="100" value="">
                    <span class="input-group-text" id="discount-suffix">%</span>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="discount_type" id="discount_fixed" value="fixed">
                    <label class="form-check-label" for="discount_fixed">&pound;</label>
                </div>
            </div>
        </div>

        {{-- Sale Routes --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">Sale routes:</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sale_routes" id="routes_all" value="all" checked>
                    <label class="form-check-label" for="routes_all">All routes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sale_routes" id="routes_specific" value="specific">
                    <label class="form-check-label" for="routes_specific">Specific routes</label>
                </div>
            </div>

            {{-- Specific routes selection (hidden by default) --}}
            <div id="specific-routes-panel" class="mt-3" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small">From</label>
                        <input type="text" class="form-control" name="route_from" placeholder="Enter pickup area or postcode">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">To</label>
                        <input type="text" class="form-control" name="route_to" placeholder="Enter destination area or postcode">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-primary w-100">Add Route</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success text-uppercase fw-bold px-4">
                Add Flash Sale
            </button>
            <button type="button" class="btn btn-secondary text-uppercase fw-bold px-4">
                Cancel
            </button>
        </div>
    </form>
</div>

{{-- Live Sales Section --}}
<div class="bg-white rounded border p-4 mb-4">
    <h5 class="fw-bold mb-3">Live Sales</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted small text-uppercase">Flash sale ID</th>
                    <th class="text-muted small text-uppercase">From</th>
                    <th class="text-muted small text-uppercase">Until</th>
                    <th class="text-muted small text-uppercase">Routes</th>
                    <th class="text-muted small text-uppercase">All/Specific car sizes</th>
                    <th class="text-muted small text-uppercase">Discount Type</th>
                    <th class="text-muted small text-uppercase">Status</th>
                    <th class="text-muted small text-uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($liveSales ?? [] as $sale)
                    <tr>
                        <td class="small">{{ $sale->id }}</td>
                        <td class="small">{{ $sale->start_date }}</td>
                        <td class="small">{{ $sale->end_date }}</td>
                        <td class="small">{{ $sale->routes_type === 'all' ? 'All routes' : 'Specific' }}</td>
                        <td class="small">{{ $sale->all_car_sizes ? 'All' : 'Specific' }}</td>
                        <td class="small">
                            @if($sale->discount_type === 'percent')
                                {{ $sale->discount_value }}%
                            @else
                                &pound;{{ number_format($sale->discount_value, 2) }}
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success">LIVE</span>
                        </td>
                        <td>
                            <a href="#" class="text-danger text-decoration-none small fw-semibold">Disable</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i>
                            No live flash sales. Create one above to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Expired Sales Section --}}
<div class="bg-white rounded border p-4">
    <h5 class="fw-bold mb-3">Expired Sales</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted small text-uppercase">Flash sale ID</th>
                    <th class="text-muted small text-uppercase">From</th>
                    <th class="text-muted small text-uppercase">Until</th>
                    <th class="text-muted small text-uppercase">Routes</th>
                    <th class="text-muted small text-uppercase">All/Specific car sizes</th>
                    <th class="text-muted small text-uppercase">Discount Type</th>
                    <th class="text-muted small text-uppercase">Status</th>
                    <th class="text-muted small text-uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expiredSales ?? [] as $sale)
                    <tr class="text-muted">
                        <td class="small">{{ $sale->id }}</td>
                        <td class="small">{{ $sale->start_date }}</td>
                        <td class="small">{{ $sale->end_date }}</td>
                        <td class="small">{{ $sale->routes_type === 'all' ? 'All routes' : 'Specific' }}</td>
                        <td class="small">{{ $sale->all_car_sizes ? 'All' : 'Specific' }}</td>
                        <td class="small">
                            @if($sale->discount_type === 'percent')
                                {{ $sale->discount_value }}%
                            @else
                                &pound;{{ number_format($sale->discount_value, 2) }}
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">DISABLED</span>
                        </td>
                        <td>
                            <a href="#" class="text-primary text-decoration-none small fw-semibold">Re-enable</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i>
                            No expired flash sales.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle discount suffix
        const percentRadio = document.getElementById('discount_percent');
        const fixedRadio = document.getElementById('discount_fixed');
        const discountSuffix = document.getElementById('discount-suffix');

        if (percentRadio && fixedRadio && discountSuffix) {
            percentRadio.addEventListener('change', function () {
                if (this.checked) discountSuffix.textContent = '%';
            });
            fixedRadio.addEventListener('change', function () {
                if (this.checked) discountSuffix.textContent = '\u00A3';
            });
        }

        // Toggle specific routes panel
        const routesAll = document.getElementById('routes_all');
        const routesSpecific = document.getElementById('routes_specific');
        const specificPanel = document.getElementById('specific-routes-panel');

        if (routesAll && routesSpecific && specificPanel) {
            routesAll.addEventListener('change', function () {
                if (this.checked) specificPanel.style.display = 'none';
            });
            routesSpecific.addEventListener('change', function () {
                if (this.checked) specificPanel.style.display = 'block';
            });
        }

        // Toggle all car sizes
        const allSizesCheckbox = document.getElementById('flash_all_sizes');
        if (allSizesCheckbox) {
            const badges = document.querySelectorAll('.badge[style*="cursor: pointer"]');
            allSizesCheckbox.addEventListener('change', function () {
                badges.forEach(function (badge) {
                    const cb = badge.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = allSizesCheckbox.checked;
                    badge.style.opacity = allSizesCheckbox.checked ? '1' : '0.5';
                });
            });

            badges.forEach(function (badge) {
                badge.addEventListener('click', function () {
                    const cb = badge.querySelector('input[type="checkbox"]');
                    if (cb) {
                        cb.checked = !cb.checked;
                        badge.style.opacity = cb.checked ? '1' : '0.5';
                    }
                    // Update "All car sizes" checkbox
                    const allChecked = Array.from(document.querySelectorAll('input[name="car_sizes[]"]')).every(function (c) { return c.checked; });
                    allSizesCheckbox.checked = allChecked;
                });
            });
        }
    });
</script>
@endpush
