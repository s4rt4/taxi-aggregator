<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate all data needed to render a booking invoice.
     */
    public static function generateData(Booking $booking): array
    {
        $booking->load(['operator', 'fleetType']);
        $vatBreakdown = VatService::breakdown((float) $booking->total_price);

        return [
            'invoice_number' => 'INV-' . $booking->reference,
            'invoice_date' => $booking->created_at->format('d/m/Y'),
            'booking' => $booking,
            'operator_name' => $booking->operator?->operator_name ?? 'N/A',
            'operator_address' => implode(', ', array_filter([
                $booking->operator?->address_line_1,
                $booking->operator?->city,
                $booking->operator?->postcode,
            ])),
            'operator_vat' => $booking->operator?->vat_number,
            'passenger_name' => $booking->passenger_name,
            'passenger_email' => $booking->passenger_email,
            'pickup' => $booking->pickup_address,
            'destination' => $booking->destination_address,
            'pickup_date' => Carbon::parse($booking->pickup_datetime)->format('d/m/Y H:i'),
            'fleet_type' => $booking->fleetType?->name ?? 'Standard',
            'base_fare' => (float) $booking->base_price,
            'meet_greet' => (float) $booking->meet_greet_charge,
            'surcharges' => (float) $booking->surcharges,
            'discount' => (float) $booking->discount_amount,
            'subtotal' => $vatBreakdown['net'],
            'vat_rate' => $vatBreakdown['vat_rate'],
            'vat_amount' => $vatBreakdown['vat'],
            'total' => $vatBreakdown['gross'],
            'currency' => '£',
        ];
    }
}
