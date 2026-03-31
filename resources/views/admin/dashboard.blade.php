@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Admin Dashboard</h1>
    <p class="text-muted mb-0">Platform overview and key metrics.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-label">Operators</div>
                </div>
                <div class="stat-value">{{ number_format($stats['operators']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-label">Passengers</div>
                </div>
                <div class="stat-value">{{ number_format($stats['passengers']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="stat-label">Bookings</div>
                </div>
                <div class="stat-value">{{ number_format($stats['bookings']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-currency-pound"></i>
                    </div>
                    <div class="stat-label">Revenue</div>
                </div>
                <div class="stat-value">&pound;{{ number_format($stats['revenue'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Pending Operator Approvals</h6>
                <a href="{{ route('admin.operators.pending') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            @if($pendingOperators->isEmpty())
                <div class="card-body text-center text-muted py-4">
                    <i class="bi bi-hourglass fs-2 d-block mb-2"></i>
                    <p class="mb-0">No pending approvals.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Operator</th>
                                <th>City</th>
                                <th>Applied</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingOperators as $operator)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $operator->operator_name }}</div>
                                        <small class="text-muted">{{ $operator->email }}</small>
                                    </td>
                                    <td>{{ $operator->city ?? '-' }}</td>
                                    <td>{{ $operator->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.operators.show', $operator) }}" class="btn btn-sm btn-outline-primary">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Recent Bookings</h6>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            @if($recentBookings->isEmpty())
                <div class="card-body text-center text-muted py-4">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    <p class="mb-0">No bookings yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Operator</th>
                                <th>Status</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-decoration-none">
                                            {{ $booking->reference }}
                                        </a>
                                        <br><small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>{{ $booking->operator->operator_name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'accepted' => 'info',
                                                'driver_assigned' => 'primary',
                                                'en_route' => 'primary',
                                                'arrived' => 'primary',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'no_show' => 'dark',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td>&pound;{{ number_format($booking->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
