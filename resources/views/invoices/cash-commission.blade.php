<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Commission Invoice {{ $invoice_reference }}</title>
    <style>
        /* Clean print-friendly invoice styles */
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a2332;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #1a2332;
            margin: 0 0 5px 0;
        }
        .company-name {
            font-size: 16px;
            color: #555;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta table td {
            padding: 2px 0;
            font-size: 13px;
        }
        .invoice-meta table td:first-child {
            text-align: right;
            padding-right: 12px;
            color: #777;
            font-weight: 600;
        }
        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .address-block {
            width: 45%;
        }
        .address-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #999;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .address-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
        }
        .address-line {
            font-size: 13px;
            color: #555;
        }
        .line-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .line-items thead th {
            background: #1a2332;
            color: #fff;
            padding: 10px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        .line-items thead th:last-child,
        .line-items thead th.text-right {
            text-align: right;
        }
        .line-items tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        .line-items tbody td:last-child,
        .line-items tbody td.text-right {
            text-align: right;
        }
        .line-items tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .totals-table td:first-child {
            text-align: right;
            color: #777;
            font-weight: 600;
        }
        .totals-table td:last-child {
            text-align: right;
            font-weight: 600;
        }
        .totals-table .total-row td {
            border-top: 2px solid #1a2332;
            font-size: 16px;
            font-weight: 700;
            padding-top: 10px;
            color: #1a2332;
        }
        .payment-terms {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .payment-terms h4 {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #1a2332;
        }
        .payment-terms p {
            font-size: 13px;
            color: #555;
            margin: 4px 0;
        }
        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 40px;
        }
        .badge-cash {
            display: inline-block;
            background: #ffc107;
            color: #212529;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Print button --}}
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 8px 20px; background: #1a2332; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                Print / Save as PDF
            </button>
        </div>

        {{-- Header --}}
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">Commission Invoice</h1>
                <div class="company-name">RushXO Ltd</div>
            </div>
            <div class="invoice-meta">
                <table>
                    <tr>
                        <td>Invoice Reference:</td>
                        <td><strong>{{ $invoice_reference }}</strong></td>
                    </tr>
                    <tr>
                        <td>Date Issued:</td>
                        <td>{{ now()->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td>Period:</td>
                        <td>{{ $period_start->format('d M Y') }} - {{ $period_end->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td>Payment Due:</td>
                        <td><strong>{{ now()->addDays(14)->format('d M Y') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="addresses">
            <div class="address-block">
                <div class="address-label">From</div>
                <div class="address-name">RushXO Ltd</div>
                <div class="address-line">
                    Taxi Aggregator Platform<br>
                    United Kingdom<br>
                    accounts@rushxo.com
                </div>
            </div>
            <div class="address-block">
                <div class="address-label">Bill To</div>
                <div class="address-name">{{ $operator->operator_name }}</div>
                <div class="address-line">
                    @if($operator->address_line_1){{ $operator->address_line_1 }}<br>@endif
                    @if($operator->address_line_2){{ $operator->address_line_2 }}<br>@endif
                    @if($operator->city){{ $operator->city }}@endif
                    @if($operator->county), {{ $operator->county }}@endif
                    @if($operator->postcode)<br>{{ $operator->postcode }}@endif
                    <br>{{ $operator->email }}
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <p style="margin-bottom: 20px; font-size: 13px; color: #555;">
            This invoice covers commission and applicable fines owed on <strong>{{ $cash_bookings_count }}</strong>
            cash booking{{ $cash_bookings_count !== 1 ? 's' : '' }} completed during the period
            {{ $period_start->format('d M Y') }} to {{ $period_end->format('d M Y') }}.
            As the fares were collected directly by the operator, the platform commission is due as outlined below.
        </p>

        {{-- Line Items --}}
        <table class="line-items">
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>Date</th>
                    <th class="text-right">Fare</th>
                    <th class="text-right">Commission</th>
                    <th class="text-right">Fines</th>
                    <th class="text-right">Amount Due</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            {{ $item->booking->reference ?? 'BK-' . $item->booking_id }}
                            <span class="badge-cash">Cash</span>
                        </td>
                        <td>{{ $item->booking->completed_at?->format('d M Y') ?? '-' }}</td>
                        <td class="text-right">&pound;{{ number_format($item->fare_amount, 2) }}</td>
                        <td class="text-right">&pound;{{ number_format($item->commission_amount, 2) }}</td>
                        <td class="text-right">
                            @if($item->fine_amount > 0)
                                &pound;{{ number_format($item->fine_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">&pound;{{ number_format($item->commission_amount + $item->fine_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Total Cash Fares:</td>
                    <td>&pound;{{ number_format($total_cash_fares, 2) }}</td>
                </tr>
                <tr>
                    <td>Commission Due:</td>
                    <td>&pound;{{ number_format($commission_due, 2) }}</td>
                </tr>
                @if($fines_due > 0)
                    <tr>
                        <td>Fines Due:</td>
                        <td>&pound;{{ number_format($fines_due, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Subtotal (Net):</td>
                    <td>&pound;{{ number_format($vat['net'], 2) }}</td>
                </tr>
                <tr>
                    <td>VAT ({{ $vat['vat_rate'] }}%):</td>
                    <td>&pound;{{ number_format($vat['vat'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Due:</td>
                    <td>&pound;{{ number_format($vat['gross'], 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Payment Terms --}}
        <div class="payment-terms">
            <h4>Payment Terms</h4>
            <p><strong>Due within 14 days</strong> of the invoice date.</p>
            <p>Please make payment by bank transfer to:</p>
            <p>
                <strong>Account Name:</strong> RushXO Ltd<br>
                <strong>Sort Code:</strong> XX-XX-XX<br>
                <strong>Account Number:</strong> XXXXXXXX<br>
                <strong>Reference:</strong> {{ $invoice_reference }}
            </p>
            <p style="margin-top: 10px;">
                For queries regarding this invoice, please contact <strong>accounts@rushxo.com</strong>
                quoting the invoice reference above.
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer-note">
            RushXO Ltd &middot; Registered in England &amp; Wales &middot; Company No. XXXXXXXX &middot; VAT No. GB XXXXXXXXX
        </div>
    </div>
</body>
</html>
