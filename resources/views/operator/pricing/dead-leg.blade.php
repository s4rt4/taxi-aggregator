@extends('layouts.operator')
@section('title', 'Dead Leg Discounts')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Dead Leg Discounts</h1>
    <div>
        <a href="#" class="text-decoration-none text-primary small me-3"><i class="bi bi-plus-circle"></i> Add Fleet Type</a>
        <a href="#" class="text-decoration-none text-muted small">Help</a>
    </div>
</div>

{{-- Description --}}
<div class="bg-white rounded border p-4 mb-4">
    <p class="text-muted mb-2">
        Dead Leg Discounts allow you to offer reduced prices for journeys where your vehicle would
        otherwise be travelling empty. For example, if you have a booking to drop off a passenger at an
        airport, you can offer a discounted fare for the return journey from the airport back to your area.
    </p>
    <p class="text-muted mb-0">
        This helps maximise vehicle utilisation and generate additional revenue from trips that would
        otherwise be unproductive. Passengers benefit from lower fares, and you fill empty legs.
    </p>
</div>

{{-- Dead Leg Discounts Table --}}
<div class="bg-white rounded border p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-muted small text-uppercase">Dead Leg Discount #</th>
                    <th class="text-muted small text-uppercase">From</th>
                    <th class="text-muted small text-uppercase">Until</th>
                    <th class="text-muted small text-uppercase">At</th>
                    <th class="text-muted small text-uppercase">Going to</th>
                    <th class="text-muted small text-uppercase">Discount Type</th>
                    <th class="text-muted small text-uppercase">Status</th>
                    <th class="text-muted small text-uppercase">Actions</th>
                    <th class="text-muted small text-uppercase">Linked to full price booking</th>
                    <th class="text-muted small text-uppercase">DLD booking ref</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deadLegDiscounts ?? [] as $discount)
                    <tr>
                        <td class="small">{{ $discount->id }}</td>
                        <td class="small">{{ $discount->from_datetime }}</td>
                        <td class="small">{{ $discount->until_datetime }}</td>
                        <td class="small">{{ $discount->at_location }}</td>
                        <td class="small">{{ $discount->going_to }}</td>
                        <td class="small">
                            @if($discount->discount_type === 'percent')
                                {{ $discount->discount_value }}%
                            @else
                                &pound;{{ number_format($discount->discount_value, 2) }}
                            @endif
                        </td>
                        <td>
                            @if($discount->status === 'active')
                                <span class="badge bg-success">ACTIVE</span>
                            @elseif($discount->status === 'expired')
                                <span class="badge bg-secondary">EXPIRED</span>
                            @elseif($discount->status === 'booked')
                                <span class="badge bg-primary">BOOKED</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ strtoupper($discount->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($discount->status === 'active')
                                <a href="#" class="text-danger text-decoration-none small fw-semibold">Cancel</a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="small">{{ $discount->linked_booking_ref ?? '-' }}</td>
                        <td class="small">{{ $discount->dld_booking_ref ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <div class="mb-2">
                                <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                            </div>
                            <p class="mb-1 fw-semibold">No Dead Leg Discounts</p>
                            <p class="small mb-0">
                                You haven't created any dead leg discounts yet. Use the "Add Fleet Type" link above
                                to set up discounted return journeys and fill empty legs.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
