@extends('layouts.corporate')
@section('title', 'Bookings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Bookings</h3>
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Booking
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                @if($isSuperUser)
                <div class="col-md-3">
                    <label class="form-label small">Employee</label>
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">All employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['pending','accepted','en_route','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Approval</label>
                    <select name="approval_status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($bookings->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
                    <p>No bookings yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Employee</th>
                                <th>Route</th>
                                <th>Pickup</th>
                                <th>Cost Centre</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $b)
                                <tr>
                                    <td><a href="{{ route('corporate.bookings.show', $b) }}">{{ $b->reference }}</a></td>
                                    <td>{{ $b->passenger?->name ?? '-' }}</td>
                                    <td>
                                        <small>{{ Str::limit($b->pickup_address, 25) }}<br>&rarr; {{ Str::limit($b->destination_address, 25) }}</small>
                                    </td>
                                    <td><small>{{ $b->pickup_datetime?->format('d M Y H:i') }}</small></td>
                                    <td>{{ $b->cost_centre ?? '-' }}</td>
                                    <td>
                                        @if($b->approval_status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending Approval</span>
                                        @elseif($b->approval_status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($b->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($b->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($b->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">&pound;{{ number_format($b->total_price, 2) }}</td>
                                    <td class="text-end">
                                        @if($isSuperUser && $b->approval_status === 'pending')
                                            <form method="POST" action="{{ route('corporate.bookings.approve', $b) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('corporate.bookings.reject', $b) }}" class="d-inline" onsubmit="return confirm('Reject this booking?');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($bookings->hasPages())
            <div class="card-footer bg-white">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
