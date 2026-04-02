@extends('layouts.app')
@section('title', 'About Us')
@section('meta_description', 'Learn about our mission to make taxi booking fairer and more transparent across the United Kingdom.')

@section('content')
{{-- Hero --}}
<section class="py-5" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container text-center text-white">
        <h1 class="fw-bold mb-2">About {{ config('app.name') }}</h1>
        <p class="lead opacity-75 mb-0">Making taxi booking fairer, easier, and more transparent across the UK</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <img src="https://picsum.photos/seed/aboutus/600/400" alt="Our team" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">Our Story</h2>
                <p class="text-muted">We started {{ config('app.name') }} because booking a taxi in the UK was broken. Passengers had no way to compare prices without ringing multiple operators. Operators struggled to fill seats on quieter routes. There had to be a better way.</p>
                <p class="text-muted">Our platform connects passengers with hundreds of licensed operators across 124 UK postcode areas. Every operator is verified, every price is transparent, and every booking is backed by our customer service team.</p>
                <p class="text-muted">Whether you need an airport transfer at 4am, a long-distance ride for a family of six, or a daily commute from the suburbs, we make it simple to find the right ride at the right price.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">What We Believe</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:64px;height:64px;">
                        <i class="bi bi-eye text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Transparency</h5>
                    <p class="text-muted small mb-0">No hidden charges, no surge pricing. The price you see when you book is the price you pay. Every fee is shown upfront.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width:64px;height:64px;">
                        <i class="bi bi-shield-check text-success fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Safety</h5>
                    <p class="text-muted small mb-0">Every operator holds a valid Private Hire Operator Licence. We verify licences before approval and conduct regular compliance audits.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10" style="width:64px;height:64px;">
                        <i class="bi bi-people text-warning fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Fairness</h5>
                    <p class="text-muted small mb-0">We give small operators the same visibility as large fleets. Passengers choose based on price and rating, not advertising budget.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary">500+</div>
                <div class="text-muted">Licensed Operators</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary">124</div>
                <div class="text-muted">UK Postcode Areas</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary">59</div>
                <div class="text-muted">Airports & Stations</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary">24/7</div>
                <div class="text-muted">Customer Support</div>
            </div>
        </div>
    </div>
</section>
@endsection
