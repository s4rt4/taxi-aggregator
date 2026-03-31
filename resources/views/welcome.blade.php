@extends('layouts.app')
@section('title', 'Pre-Book Taxis Online - Cheap Minicab Quotes')
@section('meta_description', 'Compare taxi prices from hundreds of licensed UK operators. Pre-book online and save up to 30% on airport transfers, long distance and local journeys.')

@section('content')
{{-- Hero Section with Search --}}
<section style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);" class="text-white py-4">
    <div class="container">
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center gap-2 mb-2">
                <span class="text-warning"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></span>
                <span class="small">Trusted by thousands of UK travellers</span>
            </div>
            <h1 class="fw-bold mb-1" style="font-size:1.75rem;">Pre-Book Taxis Online - Cheap Minicab Quotes</h1>
            <p class="mb-3 small opacity-75">Save up to 30% by comparing quotes from licensed UK taxi operators</p>
        </div>

        {{-- Search Form --}}
        <div class="card border-0 shadow-lg mx-auto" style="max-width:800px;">
            <div class="card-body p-3">
                <form action="{{ route('search') }}" method="POST" id="search-form">
                    @csrf
                    <input type="hidden" name="pickup_lat" value="{{ old('pickup_lat', 0) }}">
                    <input type="hidden" name="pickup_lng" value="{{ old('pickup_lng', 0) }}">
                    <input type="hidden" name="destination_lat" value="{{ old('destination_lat', 0) }}">
                    <input type="hidden" name="destination_lng" value="{{ old('destination_lng', 0) }}">
                    <input type="hidden" name="distance_miles" value="{{ old('distance_miles', 0) }}">
                    <input type="hidden" name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes', 0) }}">

                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-dark mb-1">From</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt text-success"></i></span>
                                <input type="text" name="pickup_address" class="form-control @error('pickup_address') is-invalid @enderror" placeholder="Pickup location" value="{{ old('pickup_address') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-dark mb-1">To</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                <input type="text" name="destination_address" class="form-control @error('destination_address') is-invalid @enderror" placeholder="Drop-off location" value="{{ old('destination_address') }}" required>
                            </div>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <label class="form-label small fw-semibold text-dark mb-1">Date</label>
                            <input type="date" name="pickup_date" class="form-control form-control-sm" value="{{ old('pickup_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <label class="form-label small fw-semibold text-dark mb-1">Time</label>
                            <input type="time" name="pickup_time" class="form-control form-control-sm" value="{{ old('pickup_time', '09:00') }}" required>
                        </div>
                        <div class="col-6 col-md-1.5">
                            <label class="form-label small fw-semibold text-dark mb-1">Passengers</label>
                            <select name="passengers" class="form-select form-select-sm">
                                @for($i = 1; $i <= 16; $i++)
                                    <option value="{{ $i }}" {{ old('passengers', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
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

{{-- Trust Badges --}}
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <i class="bi bi-shield-check text-success fs-4"></i>
                <div class="small fw-semibold">Licensed Operators</div>
                <div class="small text-muted">All operators verified</div>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-cash-coin text-primary fs-4"></i>
                <div class="small fw-semibold">No Hidden Charges</div>
                <div class="small text-muted">Price you see is final</div>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-arrow-left-right text-info fs-4"></i>
                <div class="small fw-semibold">Free Cancellation</div>
                <div class="small text-muted">Up to 48hrs before</div>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-headset text-warning fs-4"></i>
                <div class="small fw-semibold">24/7 Support</div>
                <div class="small text-muted">We're always here</div>
            </div>
        </div>
    </div>
</section>

{{-- How it Works --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">How it works</h2>
        <p class="text-center text-muted mb-5">It's as easy as 1-2-3. Compare prices and book a taxi in just 3 simple steps</p>
        <div class="row g-4">
            @php
                $steps = [
                    ['icon' => 'bi-geo-alt-fill', 'color' => '#e74c3c', 'bg' => 'rgba(231,76,60,0.1)', 'title' => 'Tell us your route', 'desc' => 'Enter your pickup and drop-off locations, date and number of passengers'],
                    ['icon' => 'bi-bar-chart-line-fill', 'color' => '#3498db', 'bg' => 'rgba(52,152,219,0.1)', 'title' => 'Compare your quotes', 'desc' => 'Browse quotes from multiple operators side by side, sorted by price or rating'],
                    ['icon' => 'bi-credit-card-fill', 'color' => '#27ae60', 'bg' => 'rgba(39,174,96,0.1)', 'title' => 'Book securely online', 'desc' => 'Pay securely with Stripe. Receive instant confirmation and driver details'],
                ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4 position-relative overflow-hidden">
                    {{-- Step number watermark --}}
                    <div class="position-absolute top-0 end-0 pe-3 pt-1" style="font-size:4rem;font-weight:900;color:rgba(0,0,0,0.04);line-height:1;">{{ $i + 1 }}</div>
                    {{-- Icon --}}
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:72px;height:72px;background:{{ $step['bg'] }};">
                        <i class="bi {{ $step['icon'] }}" style="font-size:1.75rem;color:{{ $step['color'] }};"></i>
                    </div>
                    {{-- Step label --}}
                    <div class="mb-2">
                        <span class="badge rounded-pill text-white px-3 py-1" style="background:{{ $step['color'] }};">Step {{ $i + 1 }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $step['title'] }}</h5>
                    <p class="text-muted mb-0">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Connecting arrows (desktop only) --}}
        <div class="d-none d-md-flex justify-content-center mt-4 gap-5">
            <div class="text-center">
                <i class="bi bi-arrow-right fs-3 text-muted opacity-25"></i>
            </div>
        </div>
    </div>
</section>

{{-- Stats / Numbers --}}
<section class="py-5" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container">
        <h2 class="text-center fw-bold text-white mb-2">{{ config('app.name') }} in numbers</h2>
        <p class="text-center text-white-50 mb-5">Trusted by passengers and operators across the United Kingdom</p>
        <div class="row g-4 text-center">
            @php
                $stats = [
                    ['value' => '500+', 'label' => 'Licensed Operators', 'icon' => 'bi-building', 'color' => '#3498db'],
                    ['value' => '124', 'label' => 'UK Postcode Areas', 'icon' => 'bi-map', 'color' => '#e74c3c'],
                    ['value' => '59', 'label' => 'Airports & Stations', 'icon' => 'bi-airplane', 'color' => '#f39c12'],
                    ['value' => '4.7/5', 'label' => 'Average Rating', 'icon' => 'bi-star-fill', 'color' => '#27ae60'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="col-6 col-md-3">
                <div class="card border-0 bg-white bg-opacity-10 rounded-3 p-4" style="backdrop-filter:blur(4px);">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width:56px;height:56px;background:{{ $stat['color'] }}20;">
                        <i class="bi {{ $stat['icon'] }}" style="font-size:1.5rem;color:{{ $stat['color'] }};"></i>
                    </div>
                    <div class="display-6 fw-bold text-white mb-1">{{ $stat['value'] }}</div>
                    <div class="small text-white-50">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Popular Routes Ticker --}}
<section class="py-3 bg-light border-top border-bottom">
    <div class="container">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <span class="badge bg-primary small">Popular</span>
            <div class="d-flex gap-4 small text-muted" style="white-space:nowrap;">
                <span>London to Heathrow from £35</span>
                <span>Manchester to Airport from £22</span>
                <span>Edinburgh to Glasgow from £45</span>
                <span>Birmingham to Gatwick from £120</span>
                <span>Leeds to Manchester from £40</span>
                <span>Southampton to Heathrow from £65</span>
            </div>
        </div>
    </div>
</section>

{{-- Feature Image + Text --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <img src="https://picsum.photos/seed/taxi1/600/400" alt="Taxi booking" class="img-fluid rounded shadow-sm">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold" style="font-size:1.5rem;">Pre-Book Your Taxi Online - Save Time & Money</h2>
                <p class="text-muted">Our comparison platform connects you with hundreds of licensed UK taxi operators. Whether you need an airport transfer, a long-distance journey, or a local ride, we'll find you the best price from trusted operators.</p>
                <p class="text-muted">Every operator on our platform holds a valid Private Hire Operator Licence, so you can travel with confidence knowing your driver is fully licensed and insured.</p>
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started Free</a>
            </div>
        </div>
    </div>
</section>

{{-- Get a Ride that Fits --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-1" style="font-size:1.5rem;">Get a ride that fits your needs</h2>
        <p class="text-center text-muted small mb-4">From solo travellers to large groups, we have the right vehicle for every journey</p>
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
                    <img src="https://picsum.photos/seed/{{ $v['img'] }}/300/180" alt="{{ $v['name'] }}" class="card-img-top">
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

{{-- Our Top Services --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-4" style="font-size:1.5rem;">Our top services</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://picsum.photos/seed/airport1/400/200" alt="Airport transfers" class="card-img-top">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="bi bi-airplane text-primary me-1"></i> Airport Transfers</h6>
                        <p class="text-muted small mb-0">Meet & Greet service at all major UK airports. Heathrow, Gatwick, Manchester, Edinburgh and 50+ more.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://picsum.photos/seed/longdist/400/200" alt="Long distance" class="card-img-top">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="bi bi-signpost-2 text-primary me-1"></i> Long Distance</h6>
                        <p class="text-muted small mb-0">City-to-city transfers across the UK. Fixed prices, no surge charges, professional drivers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://picsum.photos/seed/cruise1/400/200" alt="Cruise transfers" class="card-img-top">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="bi bi-water text-primary me-1"></i> Cruise & Port Transfers</h6>
                        <p class="text-muted small mb-0">Southampton, Dover, Portsmouth and more. Door-to-terminal service with luggage assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mobile App Promo --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <h2 class="fw-bold" style="font-size:1.5rem;">Book on the move with our app</h2>
                <p class="text-muted">Download our mobile app to compare prices, book rides, and track your driver - all from your phone. Get instant notifications and manage your bookings on the go.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Compare prices from 500+ operators</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Real-time booking confirmations</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Rate and review your journey</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Save favourite routes</li>
                </ul>
                <div class="d-flex gap-2">
                    <span class="btn btn-dark btn-sm disabled"><i class="bi bi-apple me-1"></i>App Store - Coming Soon</span>
                    <span class="btn btn-dark btn-sm disabled"><i class="bi bi-google-play me-1"></i>Google Play - Coming Soon</span>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://picsum.photos/seed/mobileapp/350/500" alt="Mobile app" class="img-fluid rounded shadow" style="max-height:400px;">
            </div>
        </div>
    </div>
</section>

{{-- Why Compare --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold">Why compare taxi prices?</h5>
                <p class="text-muted small">Comparing prices is the smartest way to book a taxi. Different operators charge different rates for the same journey. Our platform shows you all available options so you can pick the best deal without ringing around.</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold">How do we keep prices low?</h5>
                <p class="text-muted small">We work directly with operators across the UK, cutting out middlemen. Operators set their own competitive prices, and our comparison engine ensures you always see the lowest available fare for your route.</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold">Are all operators licensed?</h5>
                <p class="text-muted small">Every operator on our platform holds a valid Private Hire Operator Licence issued by their local authority. We verify all licences before approval and conduct regular compliance checks.</p>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-5 bg-light">
    <div class="container" style="max-width:800px;">
        <h2 class="text-center fw-bold mb-4" style="font-size:1.5rem;">Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            @php
                $faqs = [
                    ['q' => 'How far in advance should I book?', 'a' => 'We recommend booking at least 24 hours before your journey. However, some operators accept bookings with as little as 2 hours notice. Airport transfers should be booked at least 48 hours ahead.'],
                    ['q' => 'Can I cancel or change my booking?', 'a' => 'Yes. Cancellations more than 48 hours before pickup receive a full refund. See our cancellation policy for time-based refund tiers.'],
                    ['q' => 'What about the booking fee?', 'a' => 'There is no booking fee. The price you see on the comparison page is the total price you pay. No hidden charges.'],
                    ['q' => 'Is it safe to book online?', 'a' => 'Absolutely. All payments are processed securely through Stripe. All operators are licensed and verified. Every journey is insured.'],
                    ['q' => 'Are your operators available nationwide?', 'a' => 'Yes, we cover all 124 UK postcode areas with operators based across England, Scotland, Wales and Northern Ireland.'],
                ];
            @endphp
            @foreach($faqs as $i => $faq)
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                        {{ $faq['q'] }}
                    </button>
                </h2>
                <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body small text-muted">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Cities & Airports We Cover --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Main cities we cover</h5>
                <div class="row g-2">
                    @foreach(['London Taxis', 'Manchester Taxis', 'Birmingham Taxis', 'Edinburgh Taxis', 'Glasgow Taxis', 'Liverpool Taxis', 'Leeds Taxis', 'Bristol Taxis', 'Newcastle Taxis', 'Southampton Taxis', 'Cardiff Taxis', 'Belfast Taxis'] as $city)
                    <div class="col-6"><a href="#" class="text-decoration-none small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $city }}</a></div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Main airports we cover</h5>
                <div class="row g-2">
                    @foreach(['Heathrow Airport', 'Gatwick Airport', 'Manchester Airport', 'Stansted Airport', 'Luton Airport', 'Edinburgh Airport', 'Birmingham Airport', 'Bristol Airport', 'Glasgow Airport', 'Newcastle Airport', 'Leeds Bradford', 'Southampton Airport'] as $airport)
                    <div class="col-6"><a href="#" class="text-decoration-none small text-muted"><i class="bi bi-airplane me-1"></i>{{ $airport }}</a></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Operator CTA --}}
<section class="py-4" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container text-center text-white">
        <h3 class="fw-bold mb-2">Are you a taxi operator?</h3>
        <p class="mb-3 opacity-75">Join our network and reach thousands of passengers. Set your own prices and grow your business.</p>
        <a href="{{ route('register') }}" class="btn btn-warning fw-bold"><i class="bi bi-building me-1"></i> Register as Operator</a>
    </div>
</section>

{{-- Support Bar --}}
<section class="py-3 bg-light border-top">
    <div class="container">
        <div class="text-center mb-2">
            <span class="fw-bold small">We support your bookings 24/7, contactable by</span>
        </div>
        <div class="row g-2 text-center">
            <div class="col-3">
                <i class="bi bi-telephone text-primary"></i>
                <div class="small text-muted">Phone</div>
            </div>
            <div class="col-3">
                <i class="bi bi-envelope text-primary"></i>
                <div class="small text-muted">Email</div>
            </div>
            <div class="col-3">
                <i class="bi bi-chat-dots text-primary"></i>
                <div class="small text-muted">Live Chat</div>
            </div>
            <div class="col-3">
                <i class="bi bi-whatsapp text-success"></i>
                <div class="small text-muted">WhatsApp</div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(async function() {
    // Wait for Google Maps to load
    const { Place } = await google.maps.importLibrary('places');
    const { DistanceMatrixService } = await google.maps.importLibrary('routes');

    let pickupPlace = null, destinationPlace = null;

    // Setup autocomplete for pickup
    const pickupInput = document.querySelector('input[name="pickup_address"]');
    const destInput = document.querySelector('input[name="destination_address"]');
    if (!pickupInput || !destInput) return;

    function setupAutocomplete(inputEl, onSelect) {
        const autocomplete = new google.maps.places.Autocomplete(inputEl, {
            componentRestrictions: { country: 'gb' },
            fields: ['formatted_address', 'geometry', 'name'],
        });
        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place && place.geometry) onSelect(place);
        });
    }

    setupAutocomplete(pickupInput, (place) => {
        pickupPlace = place;
        document.querySelector('input[name="pickup_lat"]').value = place.geometry.location.lat();
        document.querySelector('input[name="pickup_lng"]').value = place.geometry.location.lng();
    });

    setupAutocomplete(destInput, (place) => {
        destinationPlace = place;
        document.querySelector('input[name="destination_lat"]').value = place.geometry.location.lat();
        document.querySelector('input[name="destination_lng"]').value = place.geometry.location.lng();
    });

    // Calculate distance on form submit
    document.querySelector('#search-form').addEventListener('submit', function(e) {
        if (pickupPlace && destinationPlace) {
            e.preventDefault();
            const service = new DistanceMatrixService();
            service.getDistanceMatrix({
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
})();
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places,routes&loading=async" async defer></script>
@endpush
