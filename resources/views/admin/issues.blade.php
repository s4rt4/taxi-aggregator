@extends('layouts.admin')
@section('title', 'Trip Issues')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Trip Issues</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Trip Issues</h1>
    <p class="text-muted mb-0">Review and manage trip issues reported across the platform.</p>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.issues.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Issue Type</label>
                <select name="issue_type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['late_pickup', 'no_show_driver', 'wrong_vehicle', 'driver_behaviour', 'overcharge', 'vehicle_condition', 'route_deviation', 'other'] as $type)
                        <option value="{{ $type }}" {{ request('issue_type') === $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Investigation Status</label>
                <select name="investigation_status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'investigating', 'resolved', 'dismissed'] as $status)
                        <option value="{{ $status }}" {{ request('investigation_status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.issues.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Issues Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Booking</th>
                    <th>Operator</th>
                    <th>Issue Type</th>
                    <th>Description</th>
                    <th>Fine</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issues as $issue)
                    <tr>
                        <td class="text-muted">{{ $issue->id }}</td>
                        <td>
                            @if($issue->booking)
                                <a href="{{ route('admin.bookings.show', $issue->booking) }}" class="text-decoration-none fw-semibold">
                                    {{ $issue->booking->reference }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($issue->operator)
                                <a href="{{ route('admin.operators.show', $issue->operator) }}" class="text-decoration-none">
                                    {{ $issue->operator->operator_name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ \Illuminate\Support\Str::limit($issue->description, 60) }}</small>
                        </td>
                        <td>
                            @if($issue->fine_amount)
                                <span class="fw-semibold text-danger">&pound;{{ number_format($issue->fine_amount, 2) }}</span>
                                <br><small class="text-muted">{{ ucfirst($issue->fine_status ?? '') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $issueStatusColors = ['pending' => 'warning', 'investigating' => 'info', 'resolved' => 'success', 'dismissed' => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $issueStatusColors[$issue->investigation_status] ?? 'secondary' }}">
                                {{ ucfirst($issue->investigation_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>{{ $issue->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                            No trip issues found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($issues->hasPages())
        <div class="card-footer">
            {{ $issues->links() }}
        </div>
    @endif
</div>
@endsection
