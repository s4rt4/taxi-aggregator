@extends('layouts.app')
@section('title', 'How It Works')
@section('meta_description', 'Find out how to compare taxi prices, book online, and travel with confidence using our UK taxi comparison platform.')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container text-center text-white">
        <h1 class="fw-bold mb-2">How It Works</h1>
        <p class="lead opacity-75 mb-0">Compare, book, and travel in 3 simple steps</p>
    </div>
</section>

{{-- Steps --}}
<section class="py-5">
    <div class="container" style="max-width:800px;">
        {{-- Step 1 --}}
        <div class="row g-4 align-items-center mb-5">
            <div class="col-md-6">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold fs-5">1</span>
                </div>
                <h3 class="fw-bold">Enter your journey details</h3>
                <p class="text-muted">Type your pickup and drop-off addresses into the search form. Our Google Maps integration suggests UK addresses as you type. Choose your date, time, and number of passengers.</p>
                <ul class="text-muted small">
                    <li>Address autocomplete for every UK postcode</li>
                    <li>Automatic distance and duration calculation</li>
                    <li>Support for airport, station, and port pickups</li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="https://picsum.photos/seed/step1/500/300" alt="Enter journey" class="img-fluid rounded shadow-sm">
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="row g-4 align-items-center mb-5 flex-md-row-reverse">
            <div class="col-md-6">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold fs-5">2</span>
                </div>
                <h3 class="fw-bold">Compare quotes side by side</h3>
                <p class="text-muted">We instantly check prices from every operator covering your route. You see them all on one page, sorted by price or rating. Each quote shows the operator name, vehicle type, passenger rating, and total fare.</p>
                <ul class="text-muted small">
                    <li>Sort by cheapest price or highest rating</li>
                    <li>See meet & greet availability and flash sale discounts</li>
                    <li>Every operator is licensed and verified</li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="https://picsum.photos/seed/step2/500/300" alt="Compare quotes" class="img-fluid rounded shadow-sm">
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="row g-4 align-items-center mb-5">
            <div class="col-md-6">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold fs-5">3</span>
                </div>
                <h3 class="fw-bold">Book and pay securely</h3>
                <p class="text-muted">Choose your preferred quote, enter your passenger details, and pay securely with Stripe. You receive an instant booking confirmation with your unique reference number and the operator's details.</p>
                <ul class="text-muted small">
                    <li>Secure payment via Stripe (card, Apple Pay, Google Pay)</li>
                    <li>Instant confirmation by email and SMS</li>
                    <li>Free cancellation up to 48 hours before</li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="https://picsum.photos/seed/step3/500/300" alt="Book securely" class="img-fluid rounded shadow-sm">
            </div>
        </div>
    </div>
</section>

{{-- After Booking --}}
<section class="py-5 bg-light">
    <div class="container" style="max-width:800px;">
        <h2 class="text-center fw-bold mb-4">After You Book</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-3 text-center">
                    <i class="bi bi-envelope-check text-primary fs-2 mb-2"></i>
                    <h6 class="fw-bold">Confirmation</h6>
                    <p class="text-muted small mb-0">Receive email and SMS confirmation with your booking reference, operator details, and driver contact number.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-3 text-center">
                    <i class="bi bi-bell text-warning fs-2 mb-2"></i>
                    <h6 class="fw-bold">Status Updates</h6>
                    <p class="text-muted small mb-0">Get real-time notifications when your driver is assigned, en route, and arrived at the pickup point.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-3 text-center">
                    <i class="bi bi-star text-success fs-2 mb-2"></i>
                    <h6 class="fw-bold">Rate & Review</h6>
                    <p class="text-muted small mb-0">After your journey, rate your experience across 5 categories to help other passengers and reward great operators.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to get started?</h2>
        <p class="text-muted mb-4">Compare taxi prices from hundreds of UK operators in seconds.</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-lg"><i class="bi bi-search me-1"></i> Search & Compare</a>
    </div>
</section>
@endsection
