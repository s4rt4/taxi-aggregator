@extends('layouts.operator')
@section('title', 'Top Routes & Price Checker')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Top Routes & Price checker</h1>
    <div>
        <a href="#" class="text-decoration-none text-muted small">Help</a>
    </div>
</div>

{{-- Main Tabs --}}
<ul class="nav nav-tabs mb-4" id="priceCheckerTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="top-routes-tab" data-bs-toggle="tab" data-bs-target="#top-routes" type="button" role="tab" aria-controls="top-routes" aria-selected="true">
            Top Routes
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="price-checker-tab" data-bs-toggle="tab" data-bs-target="#price-checker" type="button" role="tab" aria-controls="price-checker" aria-selected="false">
            Price checker
        </button>
    </li>
</ul>

<div class="tab-content" id="priceCheckerTabContent">
    {{-- Top Routes Tab --}}
    <div class="tab-pane fade show active" id="top-routes" role="tabpanel" aria-labelledby="top-routes-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <p class="text-muted mb-3">
                Below are some of the most popular and competitive routes in your area. Keeping your prices competitive
                on these routes will help you win more bookings. Routes are ranked by popularity.
            </p>

            <h5 class="fw-bold text-uppercase mb-2">4-Seaters</h5>
            <p class="text-muted small mb-4">
                Update your prices below to stay competitive. Click "Update my price" to adjust your pricing for each route.
                The most popular price shows what other operators are charging for the same route.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small text-uppercase">Last changed</th>
                            <th class="text-muted small text-uppercase">From</th>
                            <th class="text-muted small text-uppercase">To</th>
                            <th class="text-muted small text-uppercase">Most popular price - 4 seater</th>
                            <th class="text-muted small text-uppercase">Your price - 4 seater</th>
                            <th class="text-muted small text-uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topRoutes ?? [] as $route)
                            <tr>
                                <td class="small">{{ $route->last_changed ?? '-' }}</td>
                                <td class="small">{{ $route->from ?? '-' }}</td>
                                <td class="small">{{ $route->to ?? '-' }}</td>
                                <td class="small">&pound;{{ number_format($route->popular_price ?? 0, 2) }}</td>
                                <td class="small">&pound;{{ number_format($route->your_price ?? 0, 2) }}</td>
                                <td>
                                    <a href="#" class="text-primary text-decoration-none small fw-semibold">Update my price</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i>
                                    No top routes data available yet. Routes will appear here once we have enough booking data for your area.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Price Checker Tab --}}
    <div class="tab-pane fade" id="price-checker" role="tabpanel" aria-labelledby="price-checker-tab">
        <div class="bg-white rounded border p-4">
            <p class="text-muted mb-4">
                Use the price checker to compare your prices against other operators for any route.
                Enter a pickup and destination below to see how your pricing compares.
            </p>

            <form action="#" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pickup location</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" class="form-control" name="pickup" placeholder="Enter pickup address or postcode" value="{{ request('pickup') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Destination</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" class="form-control" name="destination" placeholder="Enter destination address or postcode" value="{{ request('destination') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" class="form-control" name="travel_date" value="{{ request('travel_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold text-uppercase">
                            <i class="bi bi-calculator me-1"></i> Calculate
                        </button>
                    </div>
                </div>
            </form>

            @if(isset($priceResults) && count($priceResults) > 0)
                <hr class="my-4">
                <h6 class="fw-bold mb-3">Price Comparison Results</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted small text-uppercase">Car Size</th>
                                <th class="text-muted small text-uppercase">Most Popular Price</th>
                                <th class="text-muted small text-uppercase">Your Price</th>
                                <th class="text-muted small text-uppercase">Difference</th>
                                <th class="text-muted small text-uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceResults as $result)
                                <tr>
                                    <td class="small">{{ $result->car_size }}</td>
                                    <td class="small">&pound;{{ number_format($result->popular_price, 2) }}</td>
                                    <td class="small">&pound;{{ number_format($result->your_price, 2) }}</td>
                                    <td class="small">
                                        @if($result->your_price > $result->popular_price)
                                            <span class="text-danger">+&pound;{{ number_format($result->your_price - $result->popular_price, 2) }}</span>
                                        @elseif($result->your_price < $result->popular_price)
                                            <span class="text-success">-&pound;{{ number_format($result->popular_price - $result->your_price, 2) }}</span>
                                        @else
                                            <span class="text-muted">Match</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="text-primary text-decoration-none small fw-semibold">Update my price</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
