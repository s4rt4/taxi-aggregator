@extends('layouts.app')
@section('title', 'Complete Your Booking')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Main Form --}}
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4">Complete Your Booking</h3>

            {{-- Journey Summary --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-map me-2"></i>Journey Details</h6>
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Pickup</small>
                            <span class="fw-medium">{{ $quote->quoteSearch->pickup_address }}</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Destination</small>
                            <span class="fw-medium">{{ $quote->quoteSearch->destination_address }}</span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <small class="text-muted d-block">Date & Time</small>
                            <span class="fw-medium">{{ $quote->quoteSearch->pickup_datetime->format('D, j M Y \a\t H:i') }}</span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <small class="text-muted d-block">Vehicle</small>
                            <span class="fw-medium">{{ $quote->fleet_type_name }}</span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <small class="text-muted d-block">Operator</small>
                            <span class="fw-medium">{{ $quote->operator_name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passenger Details Form --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-person me-2"></i>Passenger Details</h6>
                    <form action="{{ route('booking.store', $quote) }}" method="POST" id="booking-form">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="passenger_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('passenger_name') is-invalid @enderror"
                                       id="passenger_name" name="passenger_name"
                                       value="{{ old('passenger_name', auth()->user()->name) }}" required>
                                @error('passenger_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="passenger_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('passenger_phone') is-invalid @enderror"
                                       id="passenger_phone" name="passenger_phone"
                                       value="{{ old('passenger_phone', auth()->user()->phone) }}" required
                                       placeholder="+44 7xxx xxx xxx">
                                @error('passenger_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="passenger_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('passenger_email') is-invalid @enderror"
                                       id="passenger_email" name="passenger_email"
                                       value="{{ old('passenger_email', auth()->user()->email) }}">
                                @error('passenger_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="flight_number" class="form-label">Flight / Train Number <small class="text-muted">(optional)</small></label>
                                <input type="text" class="form-control @error('flight_number') is-invalid @enderror"
                                       id="flight_number" name="flight_number"
                                       value="{{ old('flight_number') }}"
                                       placeholder="e.g. BA1234">
                                @error('flight_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="special_requirements" class="form-label">Special Requirements <small class="text-muted">(optional)</small></label>
                                <textarea class="form-control @error('special_requirements') is-invalid @enderror"
                                          id="special_requirements" name="special_requirements"
                                          rows="3" placeholder="e.g. wheelchair access, child seat required, extra luggage...">{{ old('special_requirements') }}</textarea>
                                @error('special_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Terms --}}
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input @error('terms_accepted') is-invalid @enderror"
                                   id="terms_accepted" name="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }}>
                            <label class="form-check-label" for="terms_accepted">
                                I accept the <a href="#" target="_blank">terms and conditions</a> and <a href="#" target="_blank">cancellation policy</a>
                                <span class="text-danger">*</span>
                            </label>
                            @error('terms_accepted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-lock me-2"></i>Confirm Booking - &pound;{{ number_format($quote->total_price, 2) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Price Sidebar --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2"></i>Price Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Base fare</span>
                        <span>&pound;{{ number_format($quote->base_price, 2) }}</span>
                    </div>

                    @if($quote->meet_and_greet && $quote->meet_greet_charge > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Meet & Greet</span>
                            <span>+&pound;{{ number_format($quote->meet_greet_charge, 2) }}</span>
                        </div>
                    @endif

                    @if($quote->surcharges > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Surcharges</span>
                            <span>+&pound;{{ number_format($quote->surcharges, 2) }}</span>
                        </div>
                    @endif

                    @if($quote->flash_sale_discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span><i class="bi bi-lightning me-1"></i>Flash Sale</span>
                            <span>-&pound;{{ number_format($quote->flash_sale_discount, 2) }}</span>
                        </div>
                    @endif

                    @if($quote->dead_leg_discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span><i class="bi bi-tag me-1"></i>Dead Leg Discount</span>
                            <span>-&pound;{{ number_format($quote->dead_leg_discount, 2) }}</span>
                        </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span class="text-primary">&pound;{{ number_format($quote->total_price, 2) }}</span>
                    </div>
                </div>

                {{-- Operator Info --}}
                <div class="card-footer bg-light">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;">
                            <i class="bi bi-building text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">{{ $quote->operator_name }}</div>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($quote->operator_rating))
                                        <i class="bi bi-star-fill text-warning" style="font-size:0.7rem;"></i>
                                    @else
                                        <i class="bi bi-star text-warning" style="font-size:0.7rem;"></i>
                                    @endif
                                @endfor
                                <small class="text-muted ms-1">{{ number_format($quote->operator_rating, 1) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
