@extends('layouts.app')
@section('title', 'For Operators')
@section('meta_description', 'Join our UK taxi aggregator network. Set your own prices, manage your fleet, and receive bookings from thousands of passengers.')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container text-center text-white">
        <h1 class="fw-bold mb-2">Grow Your Taxi Business</h1>
        <p class="lead opacity-75 mb-3">Join hundreds of operators already earning more through {{ config('app.name') }}</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold"><i class="bi bi-building me-1"></i> Register as Operator</a>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 text-center">
                    <i class="bi bi-currency-pound text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">Set Your Own Prices</h5>
                    <p class="text-muted small">You control per-mile rates, location prices, and postcode area prices. Three pricing tiers let you optimise for every route and distance.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 text-center">
                    <i class="bi bi-graph-up-arrow text-success fs-1 mb-3"></i>
                    <h5 class="fw-bold">More Bookings</h5>
                    <p class="text-muted small">Reach thousands of passengers searching for taxis across the UK. Your quotes appear alongside competitors - competitive pricing wins bookings.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 text-center">
                    <i class="bi bi-phone text-info fs-1 mb-3"></i>
                    <h5 class="fw-bold">Easy Management</h5>
                    <p class="text-muted small">Full dashboard to manage pricing, availability, drivers, and bookings. Integrates with iCabbi and other dispatch systems.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">How It Works for Operators</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;"><span class="fw-bold">1</span></div>
                <h6 class="fw-semibold">Register</h6>
                <p class="text-muted small">Create your account and complete the 5-step onboarding wizard with your company and licence details.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;"><span class="fw-bold">2</span></div>
                <h6 class="fw-semibold">Get Approved</h6>
                <p class="text-muted small">Our team verifies your Private Hire Operator Licence and insurance. Approval typically takes 1-2 business days.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;"><span class="fw-bold">3</span></div>
                <h6 class="fw-semibold">Set Pricing</h6>
                <p class="text-muted small">Configure per-mile rates, location prices, meet & greet charges, and availability for each vehicle type.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;"><span class="fw-bold">4</span></div>
                <h6 class="fw-semibold">Receive Bookings</h6>
                <p class="text-muted small">Passengers see your quotes in search results. Bookings arrive in your dashboard and optionally dispatch to iCabbi.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container" style="max-width:800px;">
        <h2 class="text-center fw-bold mb-4">Dashboard Features</h2>
        <div class="row g-3">
            @php
                $features = [
                    ['icon' => 'bi-speedometer', 'title' => 'Per Mile Pricing', 'desc' => 'Set rates per mile with mileage brackets and fleet type uplifts'],
                    ['icon' => 'bi-geo-alt', 'title' => 'Location Prices', 'desc' => 'Fixed prices between specific postcodes with radius matching'],
                    ['icon' => 'bi-map', 'title' => 'Postcode Area Prices', 'desc' => 'Grid-based pricing across all 124 UK postcode areas'],
                    ['icon' => 'bi-airplane', 'title' => 'Meet & Greet', 'desc' => 'Set charges for 59 airports, stations, and cruise ports'],
                    ['icon' => 'bi-lightning', 'title' => 'Flash Sales', 'desc' => 'Time-limited discounts to fill quieter periods'],
                    ['icon' => 'bi-arrow-left-right', 'title' => 'Dead Leg Discounts', 'desc' => 'Discount empty return legs to maximise vehicle utilisation'],
                    ['icon' => 'bi-car-front', 'title' => 'Fleet Management', 'desc' => '8 vehicle types from saloon to 16-seater minibus'],
                    ['icon' => 'bi-people', 'title' => 'Driver Management', 'desc' => 'DBS tracking, licence details, vehicle assignments'],
                    ['icon' => 'bi-calendar-check', 'title' => 'Availability Control', 'desc' => 'Vehicle counts, notice periods, operating hours, pause function'],
                    ['icon' => 'bi-receipt', 'title' => 'Statements', 'desc' => 'Weekly financial statements with commission breakdown'],
                    ['icon' => 'bi-star', 'title' => 'Ratings & Reviews', 'desc' => '5-category rating system with trend analysis'],
                    ['icon' => 'bi-gear', 'title' => 'iCabbi Integration', 'desc' => 'Auto-dispatch bookings to your dispatch system'],
                ];
            @endphp
            @foreach($features as $f)
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10" style="width:40px;height:40px;">
                        <i class="bi {{ $f['icon'] }} text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 small">{{ $f['title'] }}</h6>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">{{ $f['desc'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Commission Structure</h2>
        <p class="text-muted mb-4">Simple, transparent pricing. No monthly fees. No setup costs.</p>
        <div class="card border-0 shadow mx-auto" style="max-width:400px;">
            <div class="card-body p-4 text-center">
                <div class="display-4 fw-bold text-primary">12%</div>
                <div class="text-muted">per completed booking</div>
                <hr>
                <ul class="list-unstyled text-start small text-muted">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>No monthly subscription</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>No setup fees</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>No minimum booking requirements</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Weekly payouts to your bank</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Reduced rates for TOP TIER operators</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to grow your business?</h2>
        <p class="text-muted mb-4">Registration takes 5 minutes. Approval in 1-2 business days.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-bold"><i class="bi bi-building me-1"></i> Register Now - It's Free</a>
    </div>
</section>
@endsection
