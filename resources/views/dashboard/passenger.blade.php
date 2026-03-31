@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="container py-4">
    <div class="page-header">
        <h1>My Dashboard</h1>
        <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ Auth::user()->bookings()->count() }}</div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ Auth::user()->bookings()->whereIn('status', ['pending', 'accepted'])->where('pickup_datetime', '>=', now())->count() }}</div>
                        <div class="stat-label">Upcoming Trips</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-star"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ \App\Models\Review::where('passenger_id', Auth::id())->count() }}</div>
                        <div class="stat-label">Reviews Given</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Book a Ride</h5>
                    <p class="text-muted small">Compare prices from hundreds of taxi operators across the UK.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Search & Compare
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Manage Bookings</h5>
                    <p class="text-muted small">View your upcoming and past bookings, cancel or leave reviews.</p>
                    <a href="{{ route('passenger.bookings') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-list me-1"></i> My Bookings
                    </a>
                    <a href="{{ route('passenger.profile') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person me-1"></i> My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Bookings --}}
    @php
        $recentBookings = Auth::user()->bookings()->with(['operator', 'fleetType'])->latest('pickup_datetime')->take(3)->get();
    @endphp
    @if($recentBookings->count() > 0)
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Recent Bookings</h6>
                <a href="{{ route('passenger.bookings') }}" class="small text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td><span class="font-monospace fw-bold small">{{ $booking->reference }}</span></td>
                                    <td class="small">{{ $booking->pickup_datetime->format('j M Y H:i') }}</td>
                                    <td class="small">{{ Str::limit($booking->pickup_address, 20) }} &rarr; {{ Str::limit($booking->destination_address, 20) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'accepted' => 'info',
                                                'en_route' => 'primary',
                                                'arrived' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">&pound;{{ number_format($booking->total_price, 2) }}</td>
                                    <td>
                                        <a href="{{ route('passenger.booking-detail', $booking) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
