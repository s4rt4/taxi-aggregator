<?php

namespace App\Http\Controllers;

use App\Listeners\SendBookingNotifications;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IcabbiWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('iCabbi webhook received', $payload);

        $reference = $payload['booking_reference'] ?? $payload['external_reference'] ?? null;
        if (!$reference) {
            return response('Missing reference', 400);
        }

        $booking = Booking::where('reference', $reference)->first();
        if (!$booking) {
            return response('Booking not found', 404);
        }

        // Map iCabbi status to our booking status
        $icabbiStatus = $payload['status'] ?? $payload['job_status'] ?? null;
        $statusMap = [
            'dispatched' => 'accepted',
            'accepted' => 'accepted',
            'driver_assigned' => 'driver_assigned',
            'driver_on_way' => 'en_route',
            'arrived' => 'arrived',
            'passenger_on_board' => 'in_progress',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'no_show' => 'no_show',
        ];

        $newStatus = $statusMap[$icabbiStatus] ?? null;
        if ($newStatus && $newStatus !== $booking->status) {
            $booking->update([
                'status' => $newStatus,
                'operator_notes' => trim(($booking->operator_notes ?? '') . "\n[iCabbi] Status: {$icabbiStatus} at " . now()),
            ]);

            // Update driver info if provided
            if (!empty($payload['driver_name']) || !empty($payload['driver_phone'])) {
                $booking->update([
                    'operator_notes' => trim($booking->operator_notes . "\n[iCabbi] Driver: " . ($payload['driver_name'] ?? 'N/A') . " | " . ($payload['driver_phone'] ?? 'N/A')),
                ]);
            }

            // Dispatch notifications
            SendBookingNotifications::onBookingStatusUpdated($booking, $newStatus);
        }

        return response('OK', 200);
    }
}
