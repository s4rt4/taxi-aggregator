@extends('layouts.corporate')
@section('title', 'Invoice ' . $invoice->reference)

@section('content')
<div class="container-fluid" style="max-width: 960px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Invoice {{ $invoice->reference }}</h3>
        <div>
            <a href="{{ route('corporate.invoices.download', $invoice) }}" class="btn btn-primary" target="_blank">
                <i class="bi bi-download"></i> Download PDF
            </a>
            <a href="{{ route('corporate.invoices.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Period</p>
                    <p class="fw-bold mb-0">{{ $invoice->period_start?->format('d M Y') }} - {{ $invoice->period_end?->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Due Date</p>
                    <p class="fw-bold mb-0">{{ $invoice->due_date?->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Due</p>
                    <p class="fw-bold fs-4 mb-0">&pound;{{ number_format($invoice->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0">Line Items ({{ $invoice->bookings->count() }} bookings)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Route</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->bookings as $b)
                            <tr>
                                <td>{{ $b->reference }}</td>
                                <td>{{ $b->pickup_datetime?->format('d M Y') }}</td>
                                <td>{{ $b->passenger?->name }}</td>
                                <td><small>{{ Str::limit($b->pickup_address, 30) }} &rarr; {{ Str::limit($b->destination_address, 30) }}</small></td>
                                <td class="text-end">&pound;{{ number_format($b->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Subtotal (ex VAT):</td>
                            <td class="text-end">&pound;{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">VAT (20%):</td>
                            <td class="text-end">&pound;{{ number_format($invoice->vat_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold">&pound;{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
