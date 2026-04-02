@extends('layouts.app')
@section('title', $airport['title'] . ' | ' . config('app.name'))
@section('meta_description', $airport['description'])
@section('meta_keywords', $airport['name'] . ' taxi, ' . $airport['code'] . ' taxi, ' . $airport['name'] . ' transfer, ' . $airport['name'] . ' minicab, airport taxi ' . $airport['code'])

@section('content')
{{-- Hero Section with Search --}}
<section style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);" class="text-white py-4">
    <div class="container">
        <div class="text-center mb-3">
            <nav aria-label="breadcrumb" class="d-flex justify-content-center mb-2">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-warning text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page">{{ $airport['name'] }} Transfers</li>
                </ol>
            </nav>
            <div class="d-inline-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark fw-bold px-3 py-2">{{ $airport['code'] }}</span>
                <i class="bi bi-airplane fs-4"></i>
            </div>
            <h1 class="fw-bold mb-1" style="font-size:1.75rem;">{{ $airport['title'] }}</h1>
            <p class="mb-3 small opacity-75">{{ $airport['description'] }}</p>
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
                                <span class="input-group-text bg-white"><i class="bi bi-airplane text-primary"></i></span>
                                <input type="text" name="pickup_address" class="form-control" placeholder="{{ $airport['name'] }}" value="{{ $airport['name'] }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-dark mb-1">To</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                <input type="text" name="destination_address" class="form-control" placeholder="Your destination" required>
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

{{-- Terminal Info --}}
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
            <span class="fw-semibold small text-dark"><i class="bi bi-building me-1"></i>Terminals:</span>
            @foreach($airport['terminals'] as $terminal)
            <span class="badge bg-light text-dark border px-3 py-2 small">{{ $terminal }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- Popular Routes --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="fw-bold mb-4" style="font-size:1.5rem;">Popular Transfers from {{ $airport['name'] }}</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold">Destination</th>
                        <th class="small fw-semibold">Price From</th>
                        <th class="small fw-semibold">Est. Duration</th>
                        <th class="small fw-semibold"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($airport['popular_routes'] as $route)
                    <tr>
                        <td class="small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $route['to'] }}</td>
                        <td class="small fw-bold text-primary">&pound;{{ $route['price_from'] }}</td>
                        <td class="small text-muted"><i class="bi bi-clock me-1"></i>{{ $route['duration'] }}</td>
                        <td>
                            <a href="{{ url('/') }}" class="btn btn-primary btn-sm">Book Now</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2">* Prices are indicative starting fares for a standard saloon vehicle. Actual quotes may vary based on date, time, and operator availability.</p>
    </div>
</section>

{{-- Meet & Greet Section --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <img src="{{ $airport['image'] }}" alt="{{ $airport['name'] }}" class="img-fluid rounded shadow-sm" loading="lazy">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-3" style="font-size:1.5rem;">
                    <i class="bi bi-person-badge text-primary me-2"></i>Meet & Greet at {{ $airport['name'] }}
                </h2>
                <p class="text-muted">{{ $airport['meet_greet_info'] }}</p>
                <div class="card border-0 bg-white shadow-sm mt-3">
                    <div class="card-body p-3">
                        <h6 class="fw-semibold small"><i class="bi bi-lightbulb text-warning me-1"></i> Parking Tip</h6>
                        <p class="text-muted small mb-0">{{ $airport['parking_tip'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Vehicle Types --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-1" style="font-size:1.5rem;">{{ $airport['name'] }} Transfer Vehicles</h2>
        <p class="text-center text-muted small mb-4">Choose the right vehicle for your airport transfer</p>
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

{{-- Airport Tips --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4" style="font-size:1.5rem;">{{ $airport['name'] }} Transfer Tips</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-2 flex-shrink-0" style="width:28px;height:28px;"><i class="bi bi-clock-fill" style="font-size:0.7rem;"></i></span>
                        <h6 class="fw-bold mb-0 small">Timing</h6>
                    </div>
                    <p class="text-muted small mb-0">Book your {{ $airport['name'] }} transfer at least 24 hours in advance for the best rates. For peak travel periods and early morning flights, 48 hours is recommended.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle me-2 flex-shrink-0" style="width:28px;height:28px;"><i class="bi bi-airplane-fill" style="font-size:0.7rem;"></i></span>
                        <h6 class="fw-bold mb-0 small">Flight Tracking</h6>
                    </div>
                    <p class="text-muted small mb-0">All our {{ $airport['name'] }} operators monitor your flight in real time. If your arrival is delayed, your driver will adjust their timing automatically at no extra charge.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-warning text-white rounded-circle me-2 flex-shrink-0" style="width:28px;height:28px;"><i class="bi bi-luggage-fill" style="font-size:0.7rem;"></i></span>
                        <h6 class="fw-bold mb-0 small">Luggage</h6>
                    </div>
                    <p class="text-muted small mb-0">Standard luggage (2 suitcases + 2 carry-on bags) is included in all fares. For excess or oversized luggage, choose an estate or MPV vehicle when booking.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
<section class="py-5 bg-white">
    <div class="container" style="max-width:800px;">
        <h2 class="text-center fw-bold mb-4" style="font-size:1.5rem;">{{ $airport['name'] }} Taxi FAQs</h2>
        <div class="accordion" id="airportFaqAccordion">
            @foreach($airport['faqs'] as $i => $faq)
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#airportFaq{{ $i }}">
                        {{ $faq['q'] }}
                    </button>
                </h2>
                <div id="airportFaq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#airportFaqAccordion">
                    <div class="accordion-body small text-muted">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Other Airports & Cities --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Other Airport Transfers</h5>
                <div class="row g-2">
                    @foreach($allAirports as $airportSlug => $airportData)
                        @if($airportSlug !== $slug)
                        <div class="col-6">
                            <a href="{{ route('airport.show', $airportSlug) }}" class="text-decoration-none small text-muted">
                                <i class="bi bi-airplane me-1"></i>{{ $airportData['name'] }}
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">City Taxi Services</h5>
                <div class="row g-2">
                    @foreach($allCities as $citySlug => $cityData)
                    <div class="col-6">
                        <a href="{{ route('city.show', $citySlug) }}" class="text-decoration-none small text-muted">
                            <i class="bi bi-geo-alt me-1"></i>{{ $cityData['name'] }} Taxis
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
        <h3 class="fw-bold mb-2">Operate airport transfers at {{ $airport['name'] }}?</h3>
        <p class="mb-3 opacity-75">Join our network and connect with thousands of passengers needing {{ $airport['name'] }} transfers every day.</p>
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
