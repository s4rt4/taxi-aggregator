@extends('layouts.admin')
@section('title', 'All Bookings')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Bookings</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>All Bookings</h1>
    <p class="text-muted mb-0">View and manage all bookings across the platform.</p>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Reference, name, email or address..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'confirmed', 'accepted', 'driver_assigned', 'en_route', 'arrived', 'in_progress', 'completed', 'cancelled', 'no_show'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Bookings Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Passenger</th>
                    <th>Route</th>
                    <th>Operator</th>
                    <th>Fleet Type</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-decoration-none">
                                {{ $booking->reference }}
                            </a>
                        </td>
                        <td>
                            <small>{{ $booking->pickup_datetime?->format('d M Y') ?? '-' }}</small>
                            <br><small class="text-muted">{{ $booking->pickup_datetime?->format('H:i') ?? '' }}</small>
                        </td>
                        <td>
                            <div>{{ $booking->passenger_name ?? ($booking->passenger->name ?? '-') }}</div>
                            <small class="text-muted">{{ $booking->passenger_email ?? '' }}</small>
                        </td>
                        <td>
                            <small>
                                {{ \Illuminate\Support\Str::limit($booking->pickup_address, 25) }}
                                <br>
                                <i class="bi bi-arrow-down"></i>
                                {{ \Illuminate\Support\Str::limit($booking->destination_address, 25) }}
                            </small>
                        </td>
                        <td>{{ $booking->operator->operator_name ?? '-' }}</td>
                        <td><small>{{ $booking->fleetType->name ?? '-' }}</small></td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning', 'confirmed' => 'info', 'accepted' => 'info',
                                    'driver_assigned' => 'primary', 'en_route' => 'primary', 'arrived' => 'primary',
                                    'in_progress' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', 'no_show' => 'dark',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td class="fw-semibold">&pound;{{ number_format($booking->total_price, 2) }}</td>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-journal-text fs-2 d-block mb-2"></i>
                            No bookings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
        <div class="card-footer">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
