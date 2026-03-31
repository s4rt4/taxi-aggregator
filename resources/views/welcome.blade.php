@extends('layouts.app')
@section('title', 'Compare Taxi Prices Across the UK')

@section('content')
{{-- Hero Section --}}
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3">Compare Taxi Prices Across the UK</h1>
                <p class="lead mb-4">Get instant quotes from hundreds of licensed taxi operators. Book online and save up to 30%.</p>
            </div>
            <div class="col-lg-6">
                {{-- Search Form --}}
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title text-dark fw-semibold mb-3">Get a Quote</h5>
                        <form action="{{ route('search') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Pickup Location</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt text-success"></i></span>
                                    <input type="text" name="pickup_address" class="form-control @error('pickup_address') is-invalid @enderror" placeholder="Enter pickup address or postcode" value="{{ old('pickup_address') }}" required>
                                </div>
                                @error('pickup_address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Destination</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                    <input type="text" name="destination_address" class="form-control @error('destination_address') is-invalid @enderror" placeholder="Enter destination address or postcode" value="{{ old('destination_address') }}" required>
                                </div>
                                @error('destination_address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-dark">Date</label>
                                    <input type="date" name="pickup_date" class="form-control @error('pickup_date') is-invalid @enderror" value="{{ old('pickup_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                    @error('pickup_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-dark">Time</label>
                                    <input type="time" name="pickup_time" class="form-control @error('pickup_time') is-invalid @enderror" value="{{ old('pickup_time', '09:00') }}" required>
                                    @error('pickup_time')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-dark">Passengers</label>
                                    <select name="passengers" class="form-select">
                                        @for($i = 1; $i <= 16; $i++)
                                            <option value="{{ $i }}" {{ old('passengers', 1) == $i ? 'selected' : '' }}>{{ $i }} {{ Str::plural('passenger', $i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-dark">Luggage</label>
                                    <select name="luggage" class="form-select">
                                        @for($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('luggage', 0) == $i ? 'selected' : '' }}>{{ $i }} {{ Str::plural('item', $i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-search me-1"></i> Compare Prices
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Why Choose {{ config('app.name') }}?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                        <i class="bi bi-currency-pound text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-semibold">Best Prices</h5>
                    <p class="text-muted">Compare quotes from hundreds of operators to find the best deal for your journey.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                        <i class="bi bi-shield-check text-success fs-3"></i>
                    </div>
                    <h5 class="fw-semibold">Licensed Operators</h5>
                    <p class="text-muted">Every operator is verified and holds a valid Private Hire Operator Licence.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                        <i class="bi bi-star text-warning fs-3"></i>
                    </div>
                    <h5 class="fw-semibold">Rated & Reviewed</h5>
                    <p class="text-muted">Read real reviews from passengers and choose the highest-rated operators.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- How it Works --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">How It Works</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold">1</span>
                </div>
                <h6 class="fw-semibold">Enter Your Journey</h6>
                <p class="text-muted small">Tell us your pickup, destination, date and number of passengers.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold">2</span>
                </div>
                <h6 class="fw-semibold">Compare Quotes</h6>
                <p class="text-muted small">See prices from multiple operators, sorted by price or rating.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold">3</span>
                </div>
                <h6 class="fw-semibold">Book Online</h6>
                <p class="text-muted small">Choose the best option and book securely online with Stripe.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                    <span class="fw-bold">4</span>
                </div>
                <h6 class="fw-semibold">Travel with Ease</h6>
                <p class="text-muted small">Get picked up on time with real-time tracking and SMS updates.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA for Operators --}}
<section class="py-5">
    <div class="container">
        <div class="card bg-dark text-white border-0">
            <div class="card-body text-center py-5">
                <h3 class="fw-bold mb-3">Are you a taxi operator?</h3>
                <p class="mb-4">Join our network and reach thousands of passengers. Set your own prices and grow your business.</p>
                <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-semibold">
                    <i class="bi bi-building me-1"></i> Register as Operator
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
