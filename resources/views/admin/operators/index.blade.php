@extends('layouts.admin')
@section('title', 'All Operators')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Operators</li>
</ol>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>All Operators</h1>
        <p class="text-muted mb-0">Manage registered operators on the platform.</p>
    </div>
    <a href="{{ route('admin.operators.pending') }}" class="btn btn-warning">
        <i class="bi bi-hourglass-split me-1"></i> Pending Approval
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.operators.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, city or account ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'approved', 'rejected', 'suspended'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tier</label>
                <select name="tier" class="form-select">
                    <option value="">All Tiers</option>
                    @foreach(['bronze', 'silver', 'gold', 'platinum'] as $tier)
                        <option value="{{ $tier }}" {{ request('tier') === $tier ? 'selected' : '' }}>
                            {{ ucfirst($tier) }}
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

{{-- Operators Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Operator</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Bookings</th>
                    <th>Rating</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($operators as $operator)
                    <tr>
                        <td class="text-muted">{{ $operator->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $operator->operator_name }}</div>
                            @if($operator->account_id)
                                <small class="text-muted">{{ $operator->account_id }}</small>
                            @endif
                        </td>
                        <td>
                            @switch($operator->business_type)
                                @case('sole_trader') <span class="badge bg-info">Sole Trader</span> @break
                                @case('limited_company') <span class="badge bg-primary">Ltd</span> @break
                                @case('partnership') <span class="badge bg-warning">Partnership</span> @break
                                @case('llp') <span class="badge bg-secondary">LLP</span> @break
                                @default <span class="text-muted">-</span>
                            @endswitch
                        </td>
                        <td>{{ $operator->email }}</td>
                        <td>{{ $operator->city ?? '-' }}</td>
                        <td>
                            @php
                                $tierColors = ['bronze' => 'secondary', 'silver' => 'light text-dark', 'gold' => 'warning', 'platinum' => 'info'];
                            @endphp
                            <span class="badge bg-{{ $tierColors[$operator->tier] ?? 'secondary' }}">
                                {{ ucfirst($operator->tier ?? 'bronze') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'dark'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$operator->status] ?? 'secondary' }}">
                                {{ ucfirst($operator->status) }}
                            </span>
                        </td>
                        <td>{{ number_format($operator->total_bookings ?? 0) }}</td>
                        <td>
                            @if($operator->rating_avg)
                                <i class="bi bi-star-fill text-warning"></i> {{ number_format($operator->rating_avg, 1) }}
                                <small class="text-muted">({{ $operator->rating_count }})</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.operators.show', $operator) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-2 d-block mb-2"></i>
                            No operators found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($operators->hasPages())
        <div class="card-footer">
            {{ $operators->links() }}
        </div>
    @endif
</div>
@endsection
