@extends('layouts.admin')
@section('title', 'Booking ' . $booking->reference)

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item active">{{ $booking->reference }}</li>
</ol>
@endsection

@section('content')
@php
    $statusColors = [
        'pending' => 'warning', 'confirmed' => 'info', 'accepted' => 'info',
        'driver_assigned' => 'primary', 'en_route' => 'primary', 'arrived' => 'primary',
        'in_progress' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', 'no_show' => 'dark',
    ];
@endphp

{{-- Booking Header --}}
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="mb-0">{{ $booking->reference }}</h1>
            <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }} fs-6">
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
        </div>
        <p class="text-muted mb-0">
            Created {{ $booking->created_at->format('d M Y H:i') }}
            @if($booking->pickup_datetime)
                &middot; Pickup: {{ $booking->pickup_datetime->format('d M Y H:i') }}
            @endif
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
            <i class="bi bi-pencil me-1"></i> Update Status
        </button>
    </div>
</div>

<div class="row g-4">
    {{-- Journey Details --}}
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Journey Details</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="bi bi-geo-alt-fill text-success"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-muted small">Pickup</div>
                                <div class="fw-semibold">{{ $booking->pickup_address ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-muted small">Destination</div>
                                <div class="fw-semibold">{{ $booking->destination_address ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-muted small">Distance</div>
                        <div class="fw-semibold">{{ $booking->distance_miles ? number_format($booking->distance_miles, 1) . ' miles' : '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Duration</div>
                        <div class="fw-semibold">{{ $booking->estimated_duration_minutes ? $booking->estimated_duration_minutes . ' min' : '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Passengers</div>
                        <div class="fw-semibold">{{ $booking->passenger_count ?? '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Luggage</div>
                        <div class="fw-semibold">{{ $booking->luggage_count ?? '-' }}</div>
                    </div>
                </div>
                @if($booking->special_requirements)
                    <div class="mt-3 p-2 bg-light rounded">
                        <div class="text-muted small">Special Requirements</div>
                        <div>{{ $booking->special_requirements }}</div>
                    </div>
                @endif
                @if($booking->flight_number || $booking->train_number)
                    <div class="row mt-3">
                        @if($booking->flight_number)
                            <div class="col-md-4">
                                <div class="text-muted small">Flight Number</div>
                                <div class="fw-semibold">{{ $booking->flight_number }}</div>
                            </div>
                        @endif
                        @if($booking->train_number)
                            <div class="col-md-4">
                                <div class="text-muted small">Train Number</div>
                                <div class="fw-semibold">{{ $booking->train_number }}</div>
                            </div>
                        @endif
                    </div>
                @endif
                @if($booking->is_return_journey)
                    <div class="mt-3 p-2 border rounded">
                        <div class="text-muted small">Return Journey</div>
                        <div class="fw-semibold">{{ $booking->return_datetime?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Price Breakdown --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Price Breakdown</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Base Price</td>
                        <td class="text-end">&pound;{{ number_format($booking->base_price, 2) }}</td>
                    </tr>
                    @if($booking->meet_and_greet)
                        <tr>
                            <td class="text-muted">Meet & Greet</td>
                            <td class="text-end">&pound;{{ number_format($booking->meet_greet_charge, 2) }}</td>
                        </tr>
                    @endif
                    @if($booking->surcharges > 0)
                        <tr>
                            <td class="text-muted">Surcharges</td>
                            <td class="text-end">&pound;{{ number_format($booking->surcharges, 2) }}</td>
                        </tr>
                    @endif
                    @if($booking->discount_amount > 0)
                        <tr>
                            <td class="text-muted">Discount</td>
                            <td class="text-end text-success">-&pound;{{ number_format($booking->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="border-top">
                        <td class="fw-semibold">Total Price</td>
                        <td class="text-end fw-semibold fs-5">&pound;{{ number_format($booking->total_price, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Commission ({{ number_format($booking->commission_rate, 1) }}%)</td>
                        <td class="text-end">&pound;{{ number_format($booking->commission_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment Type</td>
                        <td class="text-end">{{ ucfirst($booking->payment_type ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Price Source</td>
                        <td class="text-end">{{ ucfirst(str_replace('_', ' ', $booking->price_source ?? '-')) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Status Timeline --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Status Timeline</h6>
            </div>
            <div class="card-body">
                @php
                    $timeline = collect([
                        ['label' => 'Created', 'time' => $booking->created_at, 'icon' => 'bi-plus-circle'],
                        ['label' => 'Accepted', 'time' => $booking->accepted_at, 'icon' => 'bi-check-circle'],
                        ['label' => 'Driver Assigned', 'time' => $booking->driver_assigned_at, 'icon' => 'bi-person-check'],
                        ['label' => 'En Route', 'time' => $booking->en_route_at, 'icon' => 'bi-car-front'],
                        ['label' => 'Arrived', 'time' => $booking->arrived_at, 'icon' => 'bi-geo-alt'],
                        ['label' => 'Started', 'time' => $booking->started_at, 'icon' => 'bi-play-circle'],
                        ['label' => 'Completed', 'time' => $booking->completed_at, 'icon' => 'bi-check-circle-fill'],
                    ])->filter(fn ($item) => $item['time'] !== null);

                    if ($booking->cancelled_at) {
                        $timeline->push(['label' => 'Cancelled', 'time' => $booking->cancelled_at, 'icon' => 'bi-x-circle']);
                    }
                @endphp

                @foreach($timeline as $event)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i class="bi {{ $event['icon'] }} text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $event['label'] }}</div>
                            <small class="text-muted">{{ $event['time']->format('d M Y H:i:s') }}</small>
                        </div>
                    </div>
                @endforeach

                @if($booking->cancellation_reason)
                    <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded">
                        <div class="text-muted small">Cancellation Reason</div>
                        <div>{{ $booking->cancellation_reason }}</div>
                        @if($booking->cancelled_by)
                            <small class="text-muted">Cancelled by: {{ ucfirst($booking->cancelled_by) }}</small>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Admin Notes</h6>
            </div>
            <div class="card-body">
                @if($booking->admin_notes)
                    <pre class="mb-3" style="white-space:pre-wrap;font-family:inherit;">{{ $booking->admin_notes }}</pre>
                @else
                    <p class="text-muted mb-3">No admin notes yet.</p>
                @endif

                <form method="POST" action="{{ route('admin.bookings.add-note', $booking) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add a note..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i> Add Note
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-md-4">
        {{-- Passenger Info --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Passenger</h6>
            </div>
            <div class="card-body">
                <div class="fw-semibold">{{ $booking->passenger_name ?? '-' }}</div>
                <div class="text-muted">{{ $booking->passenger_email ?? '-' }}</div>
                <div class="text-muted">{{ $booking->passenger_phone ?? '-' }}</div>
                @if($booking->passenger)
                    <a href="{{ route('admin.users.show', $booking->passenger) }}" class="btn btn-sm btn-outline-primary mt-2">
                        View User Profile
                    </a>
                @endif
            </div>
        </div>

        {{-- Operator Info --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Operator</h6>
            </div>
            <div class="card-body">
                @if($booking->operator)
                    <div class="fw-semibold">{{ $booking->operator->operator_name }}</div>
                    <div class="text-muted">{{ $booking->operator->email }}</div>
                    <div class="text-muted">{{ $booking->operator->phone ?? '' }}</div>
                    <a href="{{ route('admin.operators.show', $booking->operator) }}" class="btn btn-sm btn-outline-primary mt-2">
                        View Operator
                    </a>
                @else
                    <span class="text-muted">No operator assigned</span>
                @endif
            </div>
        </div>

        {{-- Driver Info --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Driver</h6>
            </div>
            <div class="card-body">
                @if($booking->driver)
                    <div class="fw-semibold">{{ $booking->driver->name }}</div>
                    <div class="text-muted">{{ $booking->driver->phone ?? '' }}</div>
                    @if($booking->driver->badge_number)
                        <div class="text-muted">Badge: {{ $booking->driver->badge_number }}</div>
                    @endif
                @else
                    <span class="text-muted">No driver assigned</span>
                @endif
            </div>
        </div>

        {{-- Fleet Type --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Vehicle</h6>
            </div>
            <div class="card-body">
                <div class="fw-semibold">{{ $booking->fleetType->name ?? '-' }}</div>
                @if($booking->vehicle)
                    <div class="text-muted">{{ $booking->vehicle->make ?? '' }} {{ $booking->vehicle->model ?? '' }}</div>
                    <div class="text-muted">{{ $booking->vehicle->registration ?? '' }}</div>
                @endif
            </div>
        </div>

        {{-- Review --}}
        @if($booking->review)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0">Review</h6>
                </div>
                <div class="card-body">
                    <div class="mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $booking->review->rating ? '-fill' : '' }} text-warning"></i>
                        @endfor
                        <span class="ms-1">{{ $booking->review->rating }}/5</span>
                    </div>
                    @if($booking->review->comment)
                        <p class="text-muted mb-0">{{ $booking->review->comment }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Trip Issues --}}
        @if($booking->tripIssues->isNotEmpty())
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0">Trip Issues</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($booking->tripIssues as $issue)
                        <div class="list-group-item">
                            <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</div>
                            <small class="text-muted">{{ $issue->description }}</small>
                            @if($issue->fine_amount)
                                <div class="mt-1">
                                    <span class="badge bg-danger">Fine: &pound;{{ number_format($issue->fine_amount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Status Update Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Update Booking Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending', 'confirmed', 'accepted', 'driver_assigned', 'en_route', 'arrived', 'in_progress', 'completed', 'cancelled', 'no_show'] as $status)
                                <option value="{{ $status }}" {{ $booking->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
