@extends('layouts.admin')
@section('title', 'Revenue')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Revenue</li>
</ol>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Revenue Dashboard</h1>
        <p class="text-muted mb-0">Financial overview and commission tracking.</p>
    </div>
    <form method="GET" action="{{ route('admin.revenue') }}" class="d-flex gap-2">
        <select name="period" class="form-select" onchange="this.form.submit()">
            <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
            <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
            <option value="all_time" {{ $period === 'all_time' ? 'selected' : '' }}>All Time</option>
        </select>
    </form>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-currency-pound"></i>
                    </div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-value">&pound;{{ number_format($stats['total_revenue'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="stat-label">Commission Earned</div>
                </div>
                <div class="stat-value">&pound;{{ number_format($stats['commission_earned'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div class="stat-label">Completed Bookings</div>
                </div>
                <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-calculator"></i>
                    </div>
                    <div class="stat-label">Avg Booking Value</div>
                </div>
                <div class="stat-value">&pound;{{ number_format($stats['avg_booking_value'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Revenue by Operator --}}
<div class="card">
    <div class="card-header bg-white">
        <h6 class="fw-semibold mb-0">Revenue by Operator</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th>City</th>
                    <th>Completed Bookings</th>
                    <th>Total Revenue</th>
                    <th>Commission</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($operatorRevenue as $operator)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $operator->operator_name }}</div>
                            <small class="text-muted">{{ $operator->email }}</small>
                        </td>
                        <td>{{ $operator->city ?? '-' }}</td>
                        <td>{{ number_format($operator->booking_count) }}</td>
                        <td class="fw-semibold">&pound;{{ number_format($operator->total_revenue, 2) }}</td>
                        <td>&pound;{{ number_format($operator->total_commission, 2) }}</td>
                        <td>
                            <a href="{{ route('admin.operators.show', $operator) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-graph-up fs-2 d-block mb-2"></i>
                            No revenue data for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
