@extends('layouts.operator')
@section('title', 'Statements')

@push('styles')
<style>
    .statements-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #666;
        font-weight: 600;
        letter-spacing: 0.3px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }
    .statements-table td {
        font-size: 0.9rem;
        vertical-align: middle;
        color: #333;
    }
    .statements-table .period-cell {
        font-weight: 600;
    }
    .status-badge {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2em 0.6em;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .status-badge.paid {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .status-badge.pending {
        background-color: #fff3cd;
        color: #664d03;
    }
    .status-badge.overdue {
        background-color: #f8d7da;
        color: #842029;
    }
    .no-record-alert {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Statements</h1>
    <div>
        <a href="#" class="text-decoration-none small"><i class="bi bi-question-circle"></i> Help</a>
    </div>
</div>

{{-- Description --}}
<div class="bg-white rounded border p-4 mb-4">
    <p class="text-muted small mb-0">
        Below you will find your weekly financial statements. Each statement covers a Monday to Sunday period and includes
        all completed bookings, commission charges, and the net amount payable to you. Statements are typically generated
        on the following Monday. If you have any queries regarding your statements, please contact
        <a href="mailto:accounts@taxiaggregator.co.uk">accounts@taxiaggregator.co.uk</a>.
    </p>
</div>

{{-- Date Range Filter --}}
<div class="bg-white rounded border p-4 mb-4">
    <form method="GET" action="{{ route('operator.statements.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">From</label>
                <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">To</label>
                <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Statements Table or Empty State --}}
@if(isset($statements) && count($statements) > 0)
<div class="bg-white rounded border">
    <div class="table-responsive">
        <table class="table table-hover mb-0 statements-table">
            <thead>
                <tr>
                    <th class="ps-3">Period</th>
                    <th class="text-center">Total Bookings</th>
                    <th class="text-end">Total Fare</th>
                    <th class="text-end">Commission</th>
                    <th class="text-end">Net Payable</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statements as $statement)
                <tr>
                    <td class="ps-3 period-cell">
                        {{ $statement->period_start ?? '' }} - {{ $statement->period_end ?? '' }}
                    </td>
                    <td class="text-center">{{ $statement->total_bookings ?? 0 }}</td>
                    <td class="text-end">&pound;{{ number_format($statement->total_fare ?? 0, 2) }}</td>
                    <td class="text-end">&pound;{{ number_format($statement->commission ?? 0, 2) }}</td>
                    <td class="text-end fw-bold">&pound;{{ number_format($statement->net_payable ?? 0, 2) }}</td>
                    <td class="text-center">
                        @if(($statement->status ?? '') === 'paid')
                            <span class="status-badge paid">Paid</span>
                        @elseif(($statement->status ?? '') === 'pending')
                            <span class="status-badge pending">Pending</span>
                        @elseif(($statement->status ?? '') === 'overdue')
                            <span class="status-badge overdue">Overdue</span>
                        @else
                            <span class="status-badge pending">{{ ucfirst($statement->status ?? 'Pending') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($statements instanceof \Illuminate\Pagination\LengthAwarePaginator && $statements->lastPage() > 1)
<div class="d-flex justify-content-center mt-4">
    {{ $statements->appends(request()->query())->links() }}
</div>
@endif

@else
<div class="no-record-alert">
    <i class="bi bi-exclamation-circle me-1"></i> No record found!
</div>
@endif
@endsection
