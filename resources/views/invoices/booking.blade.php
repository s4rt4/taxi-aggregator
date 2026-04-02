<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice_number }}</title>
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
        .invoice-meta dt {
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
        .invoice-meta dd {
            font-weight: bold;
            margin: 0 0 4px 0;
            font-size: 14px;
        }
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .party {
            width: 48%;
        }
        .party-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .party-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a2332;
            margin-bottom: 3px;
        }
        .party-detail {
            color: #555;
            font-size: 13px;
            margin: 2px 0;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1a2332;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 30px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background: #f8f9fa;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 2px solid #dee2e6;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .text-muted {
            color: #888;
        }
        .subtotal-row td {
            padding-top: 15px;
            border-bottom: none;
        }
        .vat-row td {
            color: #666;
            border-bottom: none;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .total-row td {
            font-weight: bold;
            font-size: 18px;
            color: #1a2332;
            border-top: 2px solid #1a2332;
            padding-top: 12px;
        }
        .discount-row td {
            color: #28a745;
        }
        .payment-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #999;
            text-align: center;
            line-height: 1.6;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-print {
            display: inline-block;
            padding: 10px 30px;
            background: #1a2332;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-print:hover {
            background: #2c3e50;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Print Button --}}
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        </div>

        {{-- Header --}}
        <div class="invoice-header">
            <div>
                <div class="invoice-title">INVOICE</div>
                <div class="company-name">{{ \App\Helpers\Settings::get('company_name', config('app.name')) }}</div>
            </div>
            <div class="invoice-meta">
                <dl>
                    <dt>Invoice Number</dt>
                    <dd>{{ $invoice_number }}</dd>
                    <dt>Date</dt>
                    <dd>{{ $invoice_date }}</dd>
                    <dt>Booking Ref</dt>
                    <dd>{{ $booking->reference }}</dd>
                </dl>
            </div>
        </div>

        {{-- Bill To / From --}}
        <div class="parties">
            <div class="party">
                <div class="party-label">Bill From</div>
                <div class="party-name">{{ $operator_name }}</div>
                @if($operator_address)
                    <div class="party-detail">{{ $operator_address }}</div>
                @endif
                @if($operator_vat)
                    <div class="party-detail">VAT: {{ $operator_vat }}</div>
                @endif
            </div>
            <div class="party">
                <div class="party-label">Bill To</div>
                <div class="party-name">{{ $passenger_name }}</div>
                @if($passenger_email)
                    <div class="party-detail">{{ $passenger_email }}</div>
                @endif
            </div>
        </div>

        {{-- Journey Details --}}
        <div class="section-title">Journey Details</div>
        <table>
            <tr>
                <td style="width:30%;"><span class="text-muted">Pickup</span></td>
                <td>{{ $pickup }}</td>
            </tr>
            <tr>
                <td><span class="text-muted">Destination</span></td>
                <td>{{ $destination }}</td>
            </tr>
            <tr>
                <td><span class="text-muted">Date &amp; Time</span></td>
                <td>{{ $pickup_date }}</td>
            </tr>
            <tr>
                <td><span class="text-muted">Vehicle Type</span></td>
                <td>{{ $fleet_type }}</td>
            </tr>
            @if($booking->distance_miles)
                <tr>
                    <td><span class="text-muted">Distance</span></td>
                    <td>{{ number_format($booking->distance_miles, 1) }} miles</td>
                </tr>
            @endif
        </table>

        {{-- Price Breakdown --}}
        <div class="section-title">Price Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Base Fare</td>
                    <td class="text-right">{{ $currency }}{{ number_format($base_fare, 2) }}</td>
                </tr>
                @if($meet_greet > 0)
                    <tr>
                        <td>Meet &amp; Greet</td>
                        <td class="text-right">{{ $currency }}{{ number_format($meet_greet, 2) }}</td>
                    </tr>
                @endif
                @if($surcharges > 0)
                    <tr>
                        <td>Surcharges</td>
                        <td class="text-right">{{ $currency }}{{ number_format($surcharges, 2) }}</td>
                    </tr>
                @endif
                @if($discount > 0)
                    <tr class="discount-row">
                        <td>Discount</td>
                        <td class="text-right">-{{ $currency }}{{ number_format($discount, 2) }}</td>
                    </tr>
                @endif
                <tr class="subtotal-row">
                    <td>Subtotal (ex. VAT)</td>
                    <td class="text-right">{{ $currency }}{{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr class="vat-row">
                    <td>VAT @ {{ number_format($vat_rate, 0) }}%</td>
                    <td class="text-right">{{ $currency }}{{ number_format($vat_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right">{{ $currency }}{{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Payment Status --}}
        <div style="margin-top: 20px;">
            <strong>Payment:</strong>
            @if($booking->status === 'completed' && $booking->payment)
                <span class="payment-status status-paid">Paid</span>
            @else
                <span class="payment-status status-pending">{{ ucfirst($booking->payment_type ?? 'Pending') }}</span>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>
                This invoice was generated by {{ \App\Helpers\Settings::get('company_name', config('app.name')) }}.<br>
                {{ \App\Helpers\Settings::get('contact_address') ? \App\Helpers\Settings::get('contact_address') . ' | ' : '' }}{{ \App\Helpers\Settings::get('contact_email', '') }}<br>
                All prices are shown in GBP ({{ $currency }}). VAT is charged at the standard UK rate of {{ number_format($vat_rate, 0) }}%.<br>
                For queries regarding this invoice, please contact us referencing booking {{ $booking->reference }}.
            </p>
            <p>
                Terms: Payment is due at time of booking unless otherwise agreed.<br>
                Cancellation charges may apply as per our cancellation policy.
            </p>
        </div>
    </div>
</body>
</html>
