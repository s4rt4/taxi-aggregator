@extends('layouts.app')
@section('title', 'Quote Results')

@section('content')
{{-- Summary Bar --}}
<section class="bg-primary text-white py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1 fw-bold">
                    {{ $quotes->count() }} {{ Str::plural('quote', $quotes->count()) }} found
                </h4>
                <p class="mb-0 opacity-75">
                    <i class="bi bi-geo-alt me-1"></i>{{ $quoteSearch->pickup_address }}
                    <i class="bi bi-arrow-right mx-2"></i>
                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $quoteSearch->destination_address }}
                </p>
                <small class="opacity-75">
                    <i class="bi bi-calendar me-1"></i>{{ $quoteSearch->pickup_datetime->format('D, j M Y') }}
                    at {{ $quoteSearch->pickup_datetime->format('H:i') }}
                    &middot;
                    <i class="bi bi-people me-1"></i>{{ $quoteSearch->passenger_count }} {{ Str::plural('passenger', $quoteSearch->passenger_count) }}
                    @if($quoteSearch->luggage_count > 0)
                        &middot; <i class="bi bi-briefcase me-1"></i>{{ $quoteSearch->luggage_count }} {{ Str::plural('bag', $quoteSearch->luggage_count) }}
                    @endif
                </small>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> New Search
                </a>
            </div>
        </div>
    </div>
</section>

<div class="container py-4">
    @if($quotes->count() > 0)
        {{-- Sort Options --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted mb-0 small">Showing {{ $quotes->count() }} results sorted by price</p>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary active" data-sort="price">
                    <i class="bi bi-sort-numeric-down me-1"></i>Price
                </button>
                <button type="button" class="btn btn-outline-secondary" data-sort="rating">
                    <i class="bi bi-star me-1"></i>Rating
                </button>
            </div>
        </div>

        {{-- Quote Cards --}}
        <div class="row g-3" id="quote-list">
            @foreach($quotes as $quote)
                <div class="col-12" data-price="{{ $quote->total_price }}" data-rating="{{ $quote->operator_rating }}">
                    <div class="card shadow-sm border hover-shadow">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                {{-- Left: Operator Info --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold mb-1">{{ $quote->operator_name }}</h6>
                                    <div class="mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($quote->operator_rating))
                                                <i class="bi bi-star-fill text-warning small"></i>
                                            @else
                                                <i class="bi bi-star text-warning small"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted small ms-1">
                                            {{ number_format($quote->operator_rating, 1) }}
                                            @if($quote->operator && $quote->operator->rating_count)
                                                ({{ $quote->operator->rating_count }} {{ Str::plural('review', $quote->operator->rating_count) }})
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Center: Fleet Type --}}
                                <div class="col-md-4 text-md-center my-2 my-md-0">
                                    <div class="d-flex align-items-center justify-content-md-center">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;">
                                            <i class="bi bi-car-front text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $quote->fleet_type_name }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-people me-1"></i>Up to {{ $quote->max_passengers }}
                                                @if($quote->max_luggage > 0)
                                                    &middot; <i class="bi bi-briefcase me-1"></i>{{ $quote->max_luggage }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Price & Book --}}
                                <div class="col-md-4 text-md-end">
                                    <div class="d-flex align-items-center justify-content-md-end">
                                        <div class="me-3">
                                            @if($quote->flash_sale_discount > 0 || $quote->dead_leg_discount > 0)
                                                <small class="text-muted text-decoration-line-through d-block">
                                                    &pound;{{ number_format($quote->base_price + $quote->meet_greet_charge, 2) }}
                                                </small>
                                            @endif
                                            <span class="fs-4 fw-bold text-primary">&pound;{{ number_format($quote->total_price, 2) }}</span>
                                        </div>
                                        @auth
                                            <a href="{{ route('booking.create', $quote) }}" class="btn btn-primary">
                                                Book Now <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('login', ['redirect' => route('booking.create', $quote)]) }}" class="btn btn-primary">
                                                Book Now <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            {{-- Tags Row --}}
                            @if($quote->meet_and_greet || $quote->flash_sale_discount > 0 || $quote->dead_leg_discount > 0)
                                <div class="mt-2 pt-2 border-top">
                                    @if($quote->meet_and_greet)
                                        <span class="badge bg-info text-dark me-1">
                                            <i class="bi bi-person-badge me-1"></i>Meet & Greet
                                        </span>
                                    @endif
                                    @if($quote->flash_sale_discount > 0)
                                        <span class="badge bg-danger me-1">
                                            <i class="bi bi-lightning me-1"></i>Flash Sale -&pound;{{ number_format($quote->flash_sale_discount, 2) }}
                                        </span>
                                    @endif
                                    @if($quote->dead_leg_discount > 0)
                                        <span class="badge bg-success me-1">
                                            <i class="bi bi-tag me-1"></i>Dead Leg Discount -&pound;{{ number_format($quote->dead_leg_discount, 2) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-search display-1 text-muted"></i>
            </div>
            <h4 class="fw-bold">No quotes available</h4>
            <p class="text-muted mb-4">
                Unfortunately, no operators have availability for this journey at the moment.
                Try adjusting your date, time, or passenger count.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i> Try a Different Search
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
    .hover-shadow { transition: box-shadow 0.2s ease; }
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortButtons = document.querySelectorAll('[data-sort]');
    const quoteList = document.getElementById('quote-list');

    sortButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            sortButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');

            const sortBy = this.dataset.sort;
            const items = Array.from(quoteList.children);

            items.sort(function(a, b) {
                if (sortBy === 'price') {
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                } else {
                    return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                }
            });

            items.forEach(function(item) { quoteList.appendChild(item); });
        });
    });
});
</script>
@endpush
@endsection
