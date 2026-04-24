<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->reference }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .invoice-container { max-width: 800px; margin: 0 auto; }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a2332;
        }
        .invoice-title { font-size: 32px; font-weight: bold; color: #1a2332; margin: 0 0 5px 0; }
        .company-name { font-size: 16px; color: #555; }
        .invoice-meta { text-align: right; }
        .invoice-meta dt {
            color: #888; font-size: 11px; text-transform: uppercase;
            letter-spacing: 0.5px; margin-top: 8px;
        }
        .invoice-meta dd { font-weight: bold; margin: 0 0 4px 0; font-size: 14px; }
        .parties { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .party { width: 48%; }
        .party-label {
            font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;
            color: #888; margin-bottom: 5px; font-weight: bold;
        }
        .party-name { font-size: 16px; font-weight: bold; color: #1a2332; margin-bottom: 3px; }
        .party-detail { color: #555; font-size: 13px; margin: 2px 0; }
        .section-title {
            font-size: 14px; font-weight: bold; color: #1a2332;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin: 30px 0 10px 0; padding-bottom: 5px; border-bottom: 1px solid #ddd;
        }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th {
            background: #f8f9fa; text-align: left; padding: 8px 10px;
            border-bottom: 2px solid #dee2e6; font-size: 11px;
            text-transform: uppercase; letter-spacing: 0.5px; color: #555;
        }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        .text-right { text-align: right; }
        .text-muted { color: #888; }
        .subtotal-row td { padding-top: 15px; border-bottom: none; font-size: 13px; }
        .vat-row td { color: #666; border-bottom: none; padding-top: 5px; padding-bottom: 5px; }
        .total-row td {
            font-weight: bold; font-size: 18px; color: #1a2332;
            border-top: 2px solid #1a2332; padding-top: 12px;
        }
        .payment-status {
            display: inline-block; padding: 4px 12px; border-radius: 4px;
            font-size: 12px; font-weight: bold; text-transform: uppercase;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-sent { background: #cce5ff; color: #004085; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd;
            font-size: 11px; color: #999; text-align: center; line-height: 1.6;
        }
        .no-print { margin-bottom: 20px; text-align: center; }
        .btn-print {
            display: inline-block; padding: 10px 30px; background: #1a2332;
            color: #fff; text-decoration: none; border: none; border-radius: 5px;
            font-size: 14px; cursor: pointer;
        }
        .btn-print:hover { background: #2c3e50; }
        .payment-info {
            background: #f8f9fa; padding: 15px; border-radius: 5px;
            margin-top: 20px; font-size: 12px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        </div>

        <div class="invoice-header">
            <div>
                <div class="invoice-title">INVOICE</div>
                <div class="company-name">{{ \App\Helpers\Settings::get('company_name', config('app.name')) }}</div>
            </div>
            <div class="invoice-meta">
                <dl>
                    <dt>Invoice Number</dt>
                    <dd>{{ $invoice->reference }}</dd>
                    <dt>Issue Date</dt>
                    <dd>{{ $invoice->created_at?->format('d M Y') }}</dd>
                    <dt>Due Date</dt>
                    <dd>{{ $invoice->due_date?->format('d M Y') }}</dd>
                    <dt>Status</dt>
                    <dd>
                        @switch($invoice->status)
                            @case('paid') <span class="payment-status status-paid">Paid</span> @break
                            @case('sent') <span class="payment-status status-sent">Sent</span> @break
                            @case('overdue') <span class="payment-status status-overdue">Overdue</span> @break
                            @default {{ ucfirst($invoice->status) }}
                        @endswitch
                    </dd>
                </dl>
            </div>
        </div>

        <div class="parties">
            <div class="party">
                <div class="party-label">Bill From</div>
                <div class="party-name">{{ \App\Helpers\Settings::get('company_name', config('app.name')) }}</div>
                @if(\App\Helpers\Settings::get('contact_address'))
                    <div class="party-detail">{{ \App\Helpers\Settings::get('contact_address') }}</div>
                @endif
                @if(\App\Helpers\Settings::get('company_vat'))
                    <div class="party-detail">VAT: {{ \App\Helpers\Settings::get('company_vat') }}</div>
                @endif
                @if(\App\Helpers\Settings::get('company_registration'))
                    <div class="party-detail">Reg No: {{ \App\Helpers\Settings::get('company_registration') }}</div>
                @endif
            </div>
            <div class="party">
                <div class="party-label">Bill To</div>
                <div class="party-name">{{ $corporate->company_name }}</div>
                @if($corporate->legal_name && $corporate->legal_name !== $corporate->company_name)
                    <div class="party-detail">{{ $corporate->legal_name }}</div>
                @endif
                <div class="party-detail">{{ $corporate->billing_address_line_1 }}</div>
                @if($corporate->billing_address_line_2)
                    <div class="party-detail">{{ $corporate->billing_address_line_2 }}</div>
                @endif
                <div class="party-detail">{{ $corporate->billing_city }}, {{ $corporate->billing_postcode }}</div>
                @if($corporate->billing_county)
                    <div class="party-detail">{{ $corporate->billing_county }}</div>
                @endif
                @if($corporate->vat_number)
                    <div class="party-detail">VAT: {{ $corporate->vat_number }}</div>
                @endif
                <div class="party-detail">{{ $corporate->billing_email }}</div>
            </div>
        </div>

        <div class="section-title">Period: {{ $invoice->period_start?->format('d M Y') }} - {{ $invoice->period_end?->format('d M Y') }}</div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Ref</th>
                    <th>Employee</th>
                    <th>From &rarr; To</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->bookings as $booking)
                    <tr>
                        <td>{{ $booking->pickup_datetime?->format('d/m/Y') }}</td>
                        <td>{{ $booking->reference }}</td>
                        <td>{{ $booking->passenger?->name ?? '-' }}</td>
                        <td style="font-size: 11px;">
                            {{ \Illuminate\Support\Str::limit($booking->pickup_address, 30) }}<br>
                            &rarr; {{ \Illuminate\Support\Str::limit($booking->destination_address, 30) }}
                        </td>
                        <td class="text-right">&pound;{{ number_format($booking->total_price, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="4"><strong>Subtotal (ex. VAT)</strong></td>
                    <td class="text-right"><strong>&pound;{{ number_format($invoice->subtotal, 2) }}</strong></td>
                </tr>
                <tr class="vat-row">
                    <td colspan="4">VAT @ {{ number_format($vat_rate, 0) }}%</td>
                    <td class="text-right">&pound;{{ number_format($invoice->vat_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4">Total Due</td>
                    <td class="text-right">&pound;{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="payment-info">
            <strong>Payment Terms:</strong> Net {{ $corporate->payment_terms_days }} days. Due by {{ $invoice->due_date?->format('d M Y') }}.<br>
            <strong>Payment Method:</strong> Bank transfer<br>
            <strong>Bank Details:</strong>
            {{ \App\Helpers\Settings::get('bank_name', '[Bank Name]') }} &middot;
            Account: {{ \App\Helpers\Settings::get('bank_account', '[Account Number]') }} &middot;
            Sort: {{ \App\Helpers\Settings::get('bank_sort_code', '[Sort Code]') }}<br>
            <strong>Reference:</strong> {{ $invoice->reference }}
        </div>

        <div class="footer">
            <p>
                This invoice was generated by {{ \App\Helpers\Settings::get('company_name', config('app.name')) }}.<br>
                {{ \App\Helpers\Settings::get('contact_email', '') }}<br>
                All prices in GBP. VAT charged at {{ number_format($vat_rate, 0) }}% standard UK rate.
            </p>
        </div>
    </div>
</body>
</html>
