<?php

namespace App\Services\Dispatch;

use App\Models\Booking;
use App\Models\Operator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IcabbiDispatch implements DispatchInterface
{
    protected Operator $operator;
    protected string $baseUrl;

    public function __construct(Operator $operator)
    {
        $this->operator = $operator;
        $this->baseUrl = rtrim($operator->icabbi_api_url ?: 'https://api.icabbidispatch.com/icd', '/');
    }

    /**
     * iCabbi uses AppKey + SecretKey as headers for authentication.
     * Based on the official docs at api.icabbi.com/docs
     */
    protected function client()
    {
        return Http::withHeaders([
            'AppKey' => $this->operator->icabbi_app_key,
            'SecretKey' => $this->operator->icabbi_secret_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(30);
    }

    /**
     * Format phone number to international format for iCabbi.
     * iCabbi identifies customers by international phone number.
     * e.g., "07123456789" -> "00447123456789"
     */
    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '+44')) return '00' . substr($phone, 1);
        if (str_starts_with($phone, '44')) return '00' . $phone;
        if (str_starts_with($phone, '0')) return '0044' . substr($phone, 1);
        return $phone;
    }

    /**
     * Create a booking in iCabbi dispatch system.
     * Uses the "Add Booking" endpoint.
     * The request body is EJON format (JSON with iCabbi booking fields).
     */
    public function createJob(Booking $booking): array
    {
        try {
            $pickupTime = Carbon::parse($booking->pickup_datetime);

            // iCabbi EJON booking payload
            $payload = [
                'phone' => $this->formatPhone($booking->passenger_phone),
                'name' => $booking->passenger_name,
                'email' => $booking->passenger_email,
                'pickupAddress' => $booking->pickup_address,
                'pickupLatitude' => (float) $booking->pickup_lat,
                'pickupLongitude' => (float) $booking->pickup_lng,
                'destinationAddress' => $booking->destination_address,
                'destinationLatitude' => (float) $booking->destination_lat,
                'destinationLongitude' => (float) $booking->destination_lng,
                'pickupDate' => $pickupTime->format('Y-m-d'),
                'pickupTime' => $pickupTime->format('H:i'),
                'passengers' => $booking->passenger_count,
                'vehicleType' => $this->mapFleetType($booking->fleetType?->name),
                'notes' => $this->buildNotes($booking),
                'flightNumber' => $booking->flight_number,
                'externalReference' => $booking->reference,
                'fare' => (float) $booking->total_price,
                'paymentMethod' => $booking->payment_type === 'prepaid' ? 'account' : 'cash',
                'isPrebooked' => true,
            ];

            // Add meet & greet flag if applicable
            if ($booking->meet_and_greet) {
                $payload['meetAndGreet'] = true;
            }

            $response = $this->client()->post("{$this->baseUrl}/Add Booking", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $tripId = $data['tripId'] ?? $data['trip_id'] ?? $data['id'] ?? null;

                Log::info("iCabbi job created for booking {$booking->reference}", [
                    'trip_id' => $tripId,
                    'response' => $data,
                ]);

                // Store iCabbi trip ID on booking for future reference
                $booking->update([
                    'operator_notes' => trim(
                        ($booking->operator_notes ?? '') .
                        "\n[iCabbi] Dispatched | Trip ID: " . ($tripId ?? 'N/A') .
                        " | " . now()->format('d/m/Y H:i')
                    ),
                ]);

                return [
                    'dispatched' => true,
                    'method' => 'icabbi',
                    'icabbi_trip_id' => $tripId,
                    'response' => $data,
                ];
            }

            Log::warning("iCabbi dispatch failed for {$booking->reference}: HTTP {$response->status()}", [
                'body' => $response->body(),
            ]);

            return [
                'dispatched' => false,
                'method' => 'icabbi',
                'error' => $response->json()['message'] ?? $response->body(),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("iCabbi dispatch exception for {$booking->reference}: {$e->getMessage()}");
            return [
                'dispatched' => false,
                'method' => 'icabbi',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel a booking in iCabbi dispatch system.
     * Uses the "Cancel Booking" endpoint with the Trip ID.
     * Response code 200 = success.
     */
    public function cancelJob(Booking $booking): bool
    {
        try {
            // Extract trip ID from operator notes
            $tripId = $this->extractTripId($booking);

            if (!$tripId) {
                Log::warning("iCabbi cancel: No trip ID found for {$booking->reference}");
                return false;
            }

            $response = $this->client()->post("{$this->baseUrl}/Cancel Booking", [
                'tripId' => $tripId,
            ]);

            if ($response->successful()) {
                Log::info("iCabbi booking cancelled: {$booking->reference} | Trip ID: {$tripId}");

                $booking->update([
                    'operator_notes' => trim(
                        ($booking->operator_notes ?? '') .
                        "\n[iCabbi] Cancelled | " . now()->format('d/m/Y H:i')
                    ),
                ]);

                return true;
            }

            Log::warning("iCabbi cancel failed for {$booking->reference}: HTTP {$response->status()}");
            return false;
        } catch (\Exception $e) {
            Log::error("iCabbi cancel exception: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Update a booking in iCabbi dispatch system.
     * Uses the "Update Booking" endpoint.
     */
    public function updateJob(Booking $booking, array $updates): bool
    {
        try {
            $tripId = $this->extractTripId($booking);
            if (!$tripId) return false;

            $payload = array_merge(['tripId' => $tripId], $updates);
            $response = $this->client()->post("{$this->baseUrl}/Update Booking", $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("iCabbi update exception: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get booking status from iCabbi.
     */
    public function getJobStatus(Booking $booking): ?string
    {
        try {
            $tripId = $this->extractTripId($booking);
            if (!$tripId) return null;

            $response = $this->client()->get("{$this->baseUrl}/Get Booking", [
                'tripId' => $tripId,
            ]);

            if ($response->successful()) {
                return $response->json()['status'] ?? null;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Map our fleet type names to iCabbi vehicle types.
     */
    protected function mapFleetType(?string $fleetTypeName): string
    {
        if (!$fleetTypeName) return 'Saloon';

        return match (true) {
            str_contains($fleetTypeName, '1-4') && str_contains($fleetTypeName, 'Estate') => 'Estate',
            str_contains($fleetTypeName, '1-4') => 'Saloon',
            str_contains($fleetTypeName, '5-6') => 'MPV',
            str_contains($fleetTypeName, '7') => '7 Seater',
            str_contains($fleetTypeName, '8') => '8 Seater',
            str_contains($fleetTypeName, '9') => 'Minibus',
            str_contains($fleetTypeName, '10-14') => 'Minibus',
            str_contains($fleetTypeName, '15-16') => 'Minibus',
            default => 'Saloon',
        };
    }

    /**
     * Build notes string for iCabbi from booking data.
     */
    protected function buildNotes(Booking $booking): string
    {
        $notes = [];
        if ($booking->special_requirements) {
            $notes[] = $booking->special_requirements;
        }
        if ($booking->flight_number) {
            $notes[] = "Flight: {$booking->flight_number}";
        }
        if ($booking->train_number) {
            $notes[] = "Train: {$booking->train_number}";
        }
        if ($booking->meet_and_greet) {
            $notes[] = "Meet & Greet required";
        }
        $notes[] = "Ref: {$booking->reference}";
        $notes[] = "Booked via " . config('app.name');

        return implode(' | ', $notes);
    }

    /**
     * Extract iCabbi trip ID from booking operator_notes.
     */
    protected function extractTripId(Booking $booking): ?string
    {
        if (preg_match('/Trip ID:\s*(\S+)/', $booking->operator_notes ?? '', $matches)) {
            return $matches[1];
        }
        return null;
    }
}
