@extends('layouts.app')
@section('title', 'My Bookings')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Bookings</h3>
            <p class="text-muted mb-0">View and manage your taxi bookings</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Booking
        </a>
    </div>

    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs mb-4" id="bookingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                All <span class="badge bg-secondary ms-1">{{ $bookings->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                Upcoming
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                Past
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                Cancelled
            </button>
        </li>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content" id="bookingTabContent">
        {{-- All Bookings --}}
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            @forelse($bookings as $booking)
                @include('passenger._booking-card', ['booking' => $booking])
            @empty
                @include('passenger._empty-bookings')
            @endforelse
        </div>

        {{-- Upcoming --}}
        <div class="tab-pane fade" id="upcoming" role="tabpanel">
            @php
                $upcomingBookings = $bookings->filter(fn($b) => in_array($b->status, ['pending', 'accepted', 'en_route', 'arrived']) && $b->pickup_datetime->isFuture());
            @endphp
            @forelse($upcomingBookings as $booking)
                @include('passenger._booking-card', ['booking' => $booking])
            @empty
                @include('passenger._empty-bookings', ['message' => 'No upcoming bookings.'])
            @endforelse
        </div>

        {{-- Past --}}
        <div class="tab-pane fade" id="past" role="tabpanel">
            @php
                $pastBookings = $bookings->filter(fn($b) => $b->status === 'completed');
            @endphp
            @forelse($pastBookings as $booking)
                @include('passenger._booking-card', ['booking' => $booking])
            @empty
                @include('passenger._empty-bookings', ['message' => 'No completed bookings yet.'])
            @endforelse
        </div>

        {{-- Cancelled --}}
        <div class="tab-pane fade" id="cancelled" role="tabpanel">
            @php
                $cancelledBookings = $bookings->filter(fn($b) => $b->status === 'cancelled');
            @endphp
            @forelse($cancelledBookings as $booking)
                @include('passenger._booking-card', ['booking' => $booking])
            @empty
                @include('passenger._empty-bookings', ['message' => 'No cancelled bookings.'])
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
