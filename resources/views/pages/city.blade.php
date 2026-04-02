@extends('layouts.app')
@section('title', $city['title'] . ' | ' . config('app.name'))
@section('meta_description', $city['description'])
@section('meta_keywords', $city['name'] . ' taxi, ' . $city['name'] . ' minicab, ' . $city['name'] . ' cab, ' . $city['name'] . ' airport transfer, cheap taxi ' . $city['name'])

@section('content')
{{-- Hero Section with Search --}}
<section style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);" class="text-white py-4">
    <div class="container">
        <div class="text-center mb-3">
            <nav aria-label="breadcrumb" class="d-flex justify-content-center mb-2">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-warning text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page">{{ $city['name'] }} Taxis</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-1" style="font-size:1.75rem;">{{ $city['title'] }}</h1>
            <p class="mb-3 small opacity-75">{{ $city['description'] }}</p>
        </div>

        {{-- Search Form --}}
        <div class="card border-0 shadow-lg mx-auto" style="max-width:800px;">
            <div class="card-body p-3">
                <form action="{{ route('search') }}" method="POST" id="search-form">
                    @csrf
                    <input type="hidden" name="pickup_lat" value="0">
                    <input type="hidden" name="pickup_lng" value="0">
                    <input type="hidden" name="destination_lat" value="0">
                    <input type="hidden" name="destination_lng" value="0">
                    <input type="hidden" name="distance_miles" value="0">
                    <input type="hidden" name="estimated_duration_minutes" value="0">

                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-dark mb-1">From</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt text-success"></i></span>
                                <input type="text" name="pickup_address" class="form-control" placeholder="{{ $city['name'] }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-dark mb-1">To</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                <input type="text" name="destination_address" class="form-control" placeholder="Drop-off location" required>
                            </div>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <label class="form-label small fw-semibold text-dark mb-1">Date</label>
                            <input type="date" name="pickup_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <label class="form-label small fw-semibold text-dark mb-1">Time</label>
                            <input type="time" name="pickup_time" class="form-control form-control-sm" value="09:00" required>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <label class="form-label small fw-semibold text-dark mb-1">Passengers</label>
                            <select name="passengers" class="form-select form-select-sm">
                                @for($i = 1; $i <= 16; $i++)
                                    <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold py-2">Get Quotes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Popular Routes --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="fw-bold mb-4" style="font-size:1.5rem;">Popular {{ $city['name'] }} Taxi Routes</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold">From</th>
                        <th class="small fw-semibold">To</th>
                        <th class="small fw-semibold">Price From</th>
                        <th class="small fw-semibold"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($city['popular_routes'] as $route)
                    <tr>
                        <td class="small"><i class="bi bi-geo-alt text-success me-1"></i>{{ $route['from'] }}</td>
                        <td class="small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $route['to'] }}</td>
                        <td class="small fw-bold text-primary">&pound;{{ $route['price_from'] }}</td>
                        <td>
                            <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">Get Quote</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2">* Prices are indicative starting fares for a standard saloon vehicle. Actual quotes may vary based on date, time, and operator availability.</p>
    </div>
</section>

{{-- About Section --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <img src="{{ $city['image'] }}" alt="Taxis in {{ $city['name'] }}" class="img-fluid rounded shadow-sm" loading="lazy">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-3" style="font-size:1.5rem;">About {{ $city['name'] }} Taxi Services</h2>
                <p class="text-muted">{{ $city['about'] }}</p>
                <div class="d-flex gap-3 mt-3">
                    <div class="text-center">
                        <i class="bi bi-shield-check text-success fs-4"></i>
                        <div class="small fw-semibold mt-1">Licensed</div>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-cash-coin text-primary fs-4"></i>
                        <div class="small fw-semibold mt-1">Fixed Prices</div>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-clock text-warning fs-4"></i>
                        <div class="small fw-semibold mt-1">24/7 Service</div>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-star-fill text-info fs-4"></i>
                        <div class="small fw-semibold mt-1">Top Rated</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Vehicle Types --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-1" style="font-size:1.5rem;">{{ $city['name'] }} Taxi Vehicle Options</h2>
        <p class="text-center text-muted small mb-4">Choose the right vehicle for your {{ $city['name'] }} journey</p>
        <div class="row g-3">
            @php
                $vehicles = [
                    ['icon' => 'bi-car-front', 'name' => 'Saloon (1-4)', 'desc' => 'Standard saloon car, perfect for solo or couples', 'img' => 'taxi2'],
                    ['icon' => 'bi-car-front', 'name' => 'Estate (1-4)', 'desc' => 'Extra luggage space for airport runs', 'img' => 'taxi3'],
                    ['icon' => 'bi-truck', 'name' => 'MPV (5-6)', 'desc' => 'Ideal for families or small groups', 'img' => 'taxi4'],
                    ['icon' => 'bi-bus-front', 'name' => 'Minibus (7-16)', 'desc' => 'Groups, events, and corporate travel', 'img' => 'taxi5'],
                ];
            @endphp
            @foreach($vehicles as $v)
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <img src="https://picsum.photos/seed/{{ $v['img'] }}/300/180" alt="{{ $v['name'] }}" class="card-img-top" loading="lazy">
                    <div class="card-body p-2">
                        <h6 class="fw-semibold mb-1 small"><i class="{{ $v['icon'] }} me-1"></i>{{ $v['name'] }}</h6>
                        <p class="text-muted mb-0" style="font-size:0.75rem;">{{ $v['desc'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Tips Section --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4" style="font-size:1.5rem;">Top Tips for Taxi Travel in {{ $city['name'] }}</h2>
        <div class="row g-4">
            @foreach($city['tips'] as $i => $tip)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-2 flex-shrink-0" style="width:28px;height:28px;font-size:0.75rem;font-weight:700;">{{ $i + 1 }}</span>
                        <h6 class="fw-bold mb-0 small">Tip {{ $i + 1 }}</h6>
                    </div>
                    <p class="text-muted small mb-0">{{ $tip }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ Section --}}
<section class="py-5 bg-white">
    <div class="container" style="max-width:800px;">
        <h2 class="text-center fw-bold mb-4" style="font-size:1.5rem;">{{ $city['name'] }} Taxi FAQs</h2>
        <div class="accordion" id="cityFaqAccordion">
            @foreach($city['faqs'] as $i => $faq)
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#cityFaq{{ $i }}">
                        {{ $faq['q'] }}
                    </button>
                </h2>
                <div id="cityFaq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#cityFaqAccordion">
                    <div class="accordion-body small text-muted">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Map Placeholder --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-3" style="font-size:1.5rem;">{{ $city['name'] }} Coverage Area</h2>
        <div class="bg-white rounded shadow-sm d-flex align-items-center justify-content-center" style="height:300px;">
            <div class="text-center text-muted">
                <i class="bi bi-map fs-1"></i>
                <p class="mt-2 mb-0 small">Interactive map of {{ $city['name'] }} coverage area</p>
                <p class="small text-muted">Our operators cover {{ $city['name'] }} city centre and all surrounding areas</p>
            </div>
        </div>
    </div>
</section>

{{-- Other Cities & Airports --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Other Cities We Cover</h5>
                <div class="row g-2">
                    @foreach($allCities as $citySlug => $cityData)
                        @if($citySlug !== $slug)
                        <div class="col-6">
                            <a href="{{ route('city.show', $citySlug) }}" class="text-decoration-none small text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $cityData['name'] }} Taxis
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Airport Transfers</h5>
                <div class="row g-2">
                    @foreach($allAirports as $airportSlug => $airportData)
                    <div class="col-6">
                        <a href="{{ route('airport.show', $airportSlug) }}" class="text-decoration-none small text-muted">
                            <i class="bi bi-airplane me-1"></i>{{ $airportData['name'] }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Operator CTA --}}
<section class="py-4" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container text-center text-white">
        <h3 class="fw-bold mb-2">Are you a {{ $city['name'] }} taxi operator?</h3>
        <p class="mb-3 opacity-75">Join our network and reach thousands of passengers looking for rides in {{ $city['name'] }}. Set your own prices and grow your business.</p>
        <a href="{{ route('register') }}" class="btn btn-warning fw-bold"><i class="bi bi-building me-1"></i> Register as Operator</a>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMaps" async defer></script>
<script>
function initMaps() {
    let pickupPlace = null, destinationPlace = null;

    const pickupInput = document.querySelector('input[name="pickup_address"]');
    const destInput = document.querySelector('input[name="destination_address"]');
    if (!pickupInput || !destInput) return;

    const options = {
        componentRestrictions: { country: 'gb' },
        fields: ['formatted_address', 'geometry', 'name'],
    };

    const pickupAc = new google.maps.places.Autocomplete(pickupInput, options);
    const destAc = new google.maps.places.Autocomplete(destInput, options);

    pickupAc.addListener('place_changed', () => {
        pickupPlace = pickupAc.getPlace();
        if (pickupPlace.geometry) {
            document.querySelector('input[name="pickup_lat"]').value = pickupPlace.geometry.location.lat();
            document.querySelector('input[name="pickup_lng"]').value = pickupPlace.geometry.location.lng();
        }
    });

    destAc.addListener('place_changed', () => {
        destinationPlace = destAc.getPlace();
        if (destinationPlace.geometry) {
            document.querySelector('input[name="destination_lat"]').value = destinationPlace.geometry.location.lat();
            document.querySelector('input[name="destination_lng"]').value = destinationPlace.geometry.location.lng();
        }
    });

    document.querySelector('#search-form').addEventListener('submit', function(e) {
        if (pickupPlace && destinationPlace && pickupPlace.geometry && destinationPlace.geometry) {
            e.preventDefault();
            new google.maps.DistanceMatrixService().getDistanceMatrix({
                origins: [pickupPlace.geometry.location],
                destinations: [destinationPlace.geometry.location],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.IMPERIAL,
            }, (response, status) => {
                if (status === 'OK' && response.rows[0].elements[0].status === 'OK') {
                    const el = response.rows[0].elements[0];
                    document.querySelector('input[name="distance_miles"]').value = (el.distance.value / 1609.344).toFixed(2);
                    document.querySelector('input[name="estimated_duration_minutes"]').value = Math.ceil(el.duration.value / 60);
                }
                document.querySelector('#search-form').submit();
            });
        }
    });
}
</script>
@endpush
