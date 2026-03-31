@extends('layouts.admin')
@section('title', 'Statements')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Statements</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Statements</h1>
    <p class="text-muted mb-0">View all operator payment statements.</p>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.statements.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['draft', 'issued', 'paid', 'overdue'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Operator ID</label>
                <input type="text" name="operator_id" class="form-control" placeholder="Operator ID..." value="{{ request('operator_id') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Statements Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Operator</th>
                    <th>Period</th>
                    <th>Gross Fares</th>
                    <th>Commission</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statements as $statement)
                    <tr>
                        <td class="fw-semibold">{{ $statement->reference }}</td>
                        <td>
                            @if($statement->operator)
                                <a href="{{ route('admin.operators.show', $statement->operator) }}" class="text-decoration-none">
                                    {{ $statement->operator->operator_name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $statement->period_start?->format('d M Y') ?? '-' }}
                            <br><small class="text-muted">to {{ $statement->period_end?->format('d M Y') ?? '-' }}</small>
                        </td>
                        <td>&pound;{{ number_format($statement->gross_fares, 2) }}</td>
                        <td class="text-danger">&pound;{{ number_format($statement->commission_deducted, 2) }}</td>
                        <td class="fw-semibold">&pound;{{ number_format($statement->net_amount, 2) }}</td>
                        <td>
                            @php
                                $stmtStatusColors = ['draft' => 'secondary', 'issued' => 'warning', 'paid' => 'success', 'overdue' => 'danger'];
                            @endphp
                            <span class="badge bg-{{ $stmtStatusColors[$statement->status] ?? 'secondary' }}">
                                {{ ucfirst($statement->status) }}
                            </span>
                        </td>
                        <td>{{ $statement->paid_at?->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                            No statements found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($statements->hasPages())
        <div class="card-footer">
            {{ $statements->links() }}
        </div>
    @endif
</div>
@endsection
