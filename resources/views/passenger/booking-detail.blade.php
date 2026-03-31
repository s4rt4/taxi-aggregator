@extends('layouts.app')
@section('title', 'Booking ' . $booking->reference)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('passenger.bookings') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Back to My Bookings
            </a>
            <h3 class="fw-bold mt-1 mb-0">Booking <span class="text-primary font-monospace">{{ $booking->reference }}</span></h3>
        </div>
        @if(in_array($booking->status, ['pending', 'accepted']))
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                <i class="bi bi-x-circle me-1"></i>Cancel Booking
            </button>
        @endif
    </div>

    {{-- Status Timeline --}}
    <div class="card mb-4">
        <div class="card-body py-4">
            @php
                $steps = [
                    'pending' => ['label' => 'Pending', 'icon' => 'bi-hourglass-split'],
                    'accepted' => ['label' => 'Accepted', 'icon' => 'bi-check-circle'],
                    'en_route' => ['label' => 'En Route', 'icon' => 'bi-car-front'],
                    'arrived' => ['label' => 'Arrived', 'icon' => 'bi-geo-alt'],
                    'completed' => ['label' => 'Completed', 'icon' => 'bi-flag'],
                ];
                $statusOrder = array_keys($steps);
                $currentIndex = array_search($booking->status, $statusOrder);
                if ($currentIndex === false) $currentIndex = -1;
                $isCancelled = $booking->status === 'cancelled';
            @endphp

            @if($isCancelled)
                <div class="text-center">
                    <span class="badge bg-danger fs-6 px-4 py-2">
                        <i class="bi bi-x-circle me-2"></i>Cancelled
                        @if($booking->cancelled_by)
                            by {{ ucfirst($booking->cancelled_by) }}
                        @endif
                    </span>
                    @if($booking->cancellation_reason)
                        <p class="text-muted mt-2 mb-0">Reason: {{ $booking->cancellation_reason }}</p>
                    @endif
                </div>
            @else
                <div class="d-flex justify-content-between position-relative px-4">
                    {{-- Progress Line --}}
                    <div class="position-absolute" style="top:20px; left:60px; right:60px; height:3px; background:#e9ecef; z-index:0;">
                        @if($currentIndex > 0)
                            <div class="bg-primary h-100" style="width:{{ ($currentIndex / (count($steps) - 1)) * 100 }}%;"></div>
                        @endif
                    </div>

                    @foreach($steps as $key => $step)
                        @php
                            $stepIndex = array_search($key, $statusOrder);
                            $isCompleted = $stepIndex < $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                        @endphp
                        <div class="text-center position-relative" style="z-index:1;">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2
                                {{ $isCompleted ? 'bg-primary text-white' : ($isCurrent ? 'bg-primary text-white' : 'bg-light text-muted border') }}"
                                style="width:40px;height:40px;">
                                <i class="bi {{ $step['icon'] }}"></i>
                            </div>
                            <div class="small {{ $isCurrent ? 'fw-bold text-primary' : ($isCompleted ? 'fw-semibold' : 'text-muted') }}">
                                {{ $step['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Details --}}
        <div class="col-lg-8">
            {{-- Journey Details --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-map me-2"></i>Journey Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Pickup</small>
                            <span class="fw-medium">{{ $booking->pickup_address }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Destination</small>
                            <span class="fw-medium">{{ $booking->destination_address }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Date & Time</small>
                            <span class="fw-medium">{{ $booking->pickup_datetime->format('D, j M Y \a\t H:i') }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Passengers</small>
                            <span class="fw-medium">{{ $booking->passenger_count }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Distance</small>
                            <span class="fw-medium">{{ number_format($booking->distance_miles, 1) }} miles</span>
                        </div>
                        @if($booking->flight_number)
                            <div class="col-sm-4">
                                <small class="text-muted d-block">Flight / Train</small>
                                <span class="fw-medium">{{ $booking->flight_number }}</span>
                            </div>
                        @endif
                        @if($booking->special_requirements)
                            <div class="col-12">
                                <small class="text-muted d-block">Special Requirements</small>
                                <span class="fw-medium">{{ $booking->special_requirements }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Operator Info --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-building me-2"></i>Operator Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Operator</small>
                            <span class="fw-medium">{{ $booking->operator->operator_name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Vehicle Type</small>
                            <span class="fw-medium">{{ $booking->fleetType->name ?? 'N/A' }}</span>
                        </div>
                        @if($booking->driver)
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Driver</small>
                                <span class="fw-medium">{{ $booking->driver->name }}</span>
                            </div>
                            @if($booking->driver->phone)
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Driver Phone</small>
                                    <span class="fw-medium">{{ $booking->driver->phone }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Review Section --}}
            @if($booking->status === 'completed')
                <div class="card mb-4" id="review">
                    <div class="card-header bg-white">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-star me-2"></i>Your Review</h6>
                    </div>
                    <div class="card-body">
                        @if($booking->review)
                            {{-- Display existing review --}}
                            <div class="mb-3">
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $booking->review->rating)
                                            <i class="bi bi-star-fill text-warning fs-5"></i>
                                        @else
                                            <i class="bi bi-star text-warning fs-5"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2 fw-semibold">{{ $booking->review->rating }}/5</span>
                                </div>

                                @if($booking->review->comment)
                                    <p class="mb-2">{{ $booking->review->comment }}</p>
                                @endif

                                {{-- Sub-ratings --}}
                                <div class="row g-2 mt-2">
                                    @foreach(['timing_rating' => 'Timing', 'fare_rating' => 'Fare', 'driver_rating' => 'Driver', 'vehicle_rating' => 'Vehicle', 'route_rating' => 'Route'] as $field => $label)
                                        @if($booking->review->$field)
                                            <div class="col-auto">
                                                <span class="badge bg-light text-dark border">
                                                    {{ $label }}: {{ $booking->review->$field }}/5
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if($booking->review->operator_reply)
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <small class="text-muted fw-semibold d-block mb-1">Operator Reply:</small>
                                        <p class="mb-0 small">{{ $booking->review->operator_reply }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- Review Form --}}
                            <form action="{{ route('passenger.store-review', $booking) }}" method="POST">
                                @csrf

                                {{-- Overall Rating --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Overall Rating <span class="text-danger">*</span></label>
                                    <div class="rating-input" id="overall-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="me-1" style="cursor:pointer;">
                                                <input type="radio" name="rating" value="{{ $i }}" class="d-none" {{ old('rating') == $i ? 'checked' : '' }}>
                                                <i class="bi bi-star fs-4 rating-star" data-value="{{ $i }}"></i>
                                            </label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Sub-ratings --}}
                                <div class="row g-3 mb-3">
                                    @foreach(['timing_rating' => 'Timing', 'fare_rating' => 'Value for Money', 'driver_rating' => 'Driver', 'vehicle_rating' => 'Vehicle', 'route_rating' => 'Route'] as $field => $label)
                                        <div class="col-md-4 col-6">
                                            <label class="form-label small">{{ $label }}</label>
                                            <select name="{{ $field }}" class="form-select form-select-sm">
                                                <option value="">--</option>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}" {{ old($field) == $i ? 'selected' : '' }}>{{ $i }} {{ Str::plural('star', $i) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Comment --}}
                                <div class="mb-3">
                                    <label class="form-label">Comment <small class="text-muted">(optional)</small></label>
                                    <textarea name="comment" class="form-control" rows="3" maxlength="1000"
                                              placeholder="Tell us about your experience...">{{ old('comment') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-star me-1"></i> Submit Review
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Price Breakdown --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2"></i>Price Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Base fare</span>
                        <span>&pound;{{ number_format($booking->base_price, 2) }}</span>
                    </div>

                    @if($booking->meet_and_greet && $booking->meet_greet_charge > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Meet & Greet</span>
                            <span>+&pound;{{ number_format($booking->meet_greet_charge, 2) }}</span>
                        </div>
                    @endif

                    @if($booking->surcharges > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Surcharges</span>
                            <span>+&pound;{{ number_format($booking->surcharges, 2) }}</span>
                        </div>
                    @endif

                    @if($booking->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span><i class="bi bi-tag me-1"></i>Discount</span>
                            <span>-&pound;{{ number_format($booking->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span class="text-primary">&pound;{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="text-muted small mt-1">
                        Payment: {{ ucfirst($booking->payment_type) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
@if(in_array($booking->status, ['pending', 'accepted']))
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('passenger.cancel-booking', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel booking <strong>{{ $booking->reference }}</strong>?</p>
                    <div class="mb-3">
                        <label for="cancel-reason" class="form-label">Reason for cancellation <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" id="cancel-reason" name="reason" rows="3"
                                  placeholder="Please let us know why you are cancelling..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Booking</button>
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star rating interaction
    document.querySelectorAll('.rating-input').forEach(function(container) {
        var stars = container.querySelectorAll('.rating-star');
        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                var value = parseInt(this.dataset.value);
                stars.forEach(function(s) {
                    var sv = parseInt(s.dataset.value);
                    s.classList.toggle('bi-star-fill', sv <= value);
                    s.classList.toggle('bi-star', sv > value);
                    s.classList.toggle('text-warning', sv <= value);
                });
            });
            star.addEventListener('mouseenter', function() {
                var value = parseInt(this.dataset.value);
                stars.forEach(function(s) {
                    var sv = parseInt(s.dataset.value);
                    s.classList.toggle('bi-star-fill', sv <= value);
                    s.classList.toggle('bi-star', sv > value);
                    s.classList.toggle('text-warning', sv <= value);
                });
            });
        });
        container.addEventListener('mouseleave', function() {
            var checked = container.querySelector('input:checked');
            var checkedVal = checked ? parseInt(checked.value) : 0;
            stars.forEach(function(s) {
                var sv = parseInt(s.dataset.value);
                s.classList.toggle('bi-star-fill', sv <= checkedVal);
                s.classList.toggle('bi-star', sv > checkedVal);
                s.classList.toggle('text-warning', sv <= checkedVal);
            });
        });
    });
});
</script>
@endpush
@endsection
