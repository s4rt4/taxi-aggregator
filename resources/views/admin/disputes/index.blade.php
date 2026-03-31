@extends('layouts.admin')
@section('title', 'Disputes')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Disputes</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Disputes</h1>
    <p class="text-muted mb-0">Manage customer and operator disputes.</p>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.disputes.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Reference or description..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['open', 'investigating', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['overcharge', 'poor_service', 'no_show', 'vehicle_condition', 'driver_behaviour', 'route', 'other'] as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Disputes Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Booking</th>
                    <th>Raised By</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $dispute)
                    <tr>
                        <td class="fw-semibold">{{ $dispute->reference ?? 'DSP-' . $dispute->id }}</td>
                        <td>
                            @if($dispute->booking)
                                <a href="{{ route('admin.bookings.show', $dispute->booking) }}" class="text-decoration-none">
                                    {{ $dispute->booking->reference }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div>{{ $dispute->raisedBy->name ?? '-' }}</div>
                            <small class="text-muted">{{ ucfirst($dispute->raised_by_role ?? '') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst(str_replace('_', ' ', $dispute->type ?? '-')) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $disputeStatusColors = ['open' => 'danger', 'investigating' => 'warning', 'resolved' => 'success', 'closed' => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $disputeStatusColors[$dispute->status] ?? 'secondary' }}">
                                {{ ucfirst($dispute->status) }}
                            </span>
                        </td>
                        <td>{{ $dispute->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.disputes.show', $dispute) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                            No disputes found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($disputes->hasPages())
        <div class="card-footer">
            {{ $disputes->links() }}
        </div>
    @endif
</div>
@endsection
