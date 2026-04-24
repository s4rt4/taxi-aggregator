@extends('layouts.corporate')
@section('title', 'Corporate Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}</h3>
            <p class="text-muted small mb-0">{{ $corporate->company_name }} &middot; Corporate Dashboard</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Booking
        </a>
    </div>

    {{-- Stats cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-journal-text text-primary fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Bookings this month</div>
                            <div class="fs-4 fw-bold">{{ $bookingsThisMonth }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-currency-pound text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Spent this month</div>
                            <div class="fs-4 fw-bold">&pound;{{ number_format($spentThisMonth, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-wallet2 text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Remaining Budget</div>
                            <div class="fs-4 fw-bold">
                                @if($remainingBudget === null)
                                    <span class="text-muted">Unlimited</span>
                                @else
                                    &pound;{{ number_format($remainingBudget, 2) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-people text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Active Employees</div>
                            <div class="fs-4 fw-bold">{{ $activeEmployees }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Budget progress --}}
    @if($corporate->monthly_budget !== null)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Monthly Budget Usage</span>
                <span class="text-muted small">&pound;{{ number_format($corporate->monthly_spent, 2) }} / &pound;{{ number_format($corporate->monthly_budget, 2) }}</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar {{ $budgetPercent >= 80 ? 'bg-danger' : ($budgetPercent >= 60 ? 'bg-warning' : 'bg-success') }}"
                     role="progressbar"
                     style="width: {{ $budgetPercent }}%;"
                     aria-valuenow="{{ $budgetPercent }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            @if($budgetPercent >= 80)
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    Warning: You've used {{ number_format($budgetPercent, 1) }}% of your monthly budget.
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Pending approvals callout --}}
    @if($pendingApprovals > 0 && auth()->user()->isCorporateSuperUser())
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-clock-history"></i>
            <strong>{{ $pendingApprovals }}</strong> booking(s) awaiting your approval.
        </div>
        <a href="{{ route('corporate.bookings.index', ['approval_status' => 'pending']) }}" class="btn btn-sm btn-warning">Review Bookings</a>
    </div>
    @endif

    {{-- Recent bookings --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Recent Bookings</h6>
            <a href="{{ route('corporate.bookings.index') }}" class="btn btn-sm btn-link">View All</a>
        </div>
        <div class="card-body p-0">
            @if($recentBookings->isEmpty())
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
                                <th>From &rarr; To</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('corporate.bookings.show', $booking) }}" class="text-decoration-none">{{ $booking->reference }}</a>
                                    </td>
                                    <td>{{ $booking->passenger?->name ?? '-' }}</td>
                                    <td>
                                        <small>{{ Str::limit($booking->pickup_address, 25) }}<br>&rarr; {{ Str::limit($booking->destination_address, 25) }}</small>
                                    </td>
                                    <td><small>{{ $booking->pickup_datetime?->format('d M Y H:i') }}</small></td>
                                    <td>
                                        @if($booking->approval_status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending Approval</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">&pound;{{ number_format($booking->total_price, 2) }}</td>
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
