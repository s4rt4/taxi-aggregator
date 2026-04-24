@extends('layouts.corporate')
@section('title', 'Invoices')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4">Invoices</h3>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($invoices->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-receipt fs-1 mb-2 d-block"></i>
                    <p>No invoices yet. Invoices are auto-generated based on your company's {{ $corporate->invoicing_frequency }} cadence.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Period</th>
                                <th>Bookings</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr>
                                    <td class="fw-semibold">{{ $inv->reference }}</td>
                                    <td>{{ $inv->period_start?->format('d M Y') }} - {{ $inv->period_end?->format('d M Y') }}</td>
                                    <td>{{ $inv->total_bookings }}</td>
                                    <td>{{ $inv->due_date?->format('d M Y') }}</td>
                                    <td>
                                        @switch($inv->status)
                                            @case('draft') <span class="badge bg-secondary">Draft</span> @break
                                            @case('sent') <span class="badge bg-primary">Sent</span> @break
                                            @case('paid') <span class="badge bg-success">Paid</span> @break
                                            @case('overdue') <span class="badge bg-danger">Overdue</span> @break
                                            @case('cancelled') <span class="badge bg-dark">Cancelled</span> @break
                                        @endswitch
                                    </td>
                                    <td class="text-end">&pound;{{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('corporate.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="{{ route('corporate.invoices.download', $inv) }}" class="btn btn-sm btn-outline-secondary" target="_blank">PDF</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($invoices->hasPages())
            <div class="card-footer bg-white">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
