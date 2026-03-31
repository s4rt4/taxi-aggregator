<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send(string $to, string $message): bool
    {
        // Skip if not configured
        if (!config('services.vonage.key') || config('services.vonage.key') === 'your_key') {
            Log::info("SMS (skipped - not configured): To: {$to} | {$message}");
            return false;
        }

        try {
            $response = Http::post('https://rest.nexmo.com/sms/json', [
                'from' => config('services.vonage.sms_from'),
                'text' => $message,
                'to' => self::formatUkNumber($to),
                'api_key' => config('services.vonage.key'),
                'api_secret' => config('services.vonage.secret'),
            ]);

            $data = $response->json();
            $status = $data['messages'][0]['status'] ?? '1';

            if ($status === '0') {
                Log::info("SMS sent to {$to}");
                return true;
            }

            Log::warning("SMS failed to {$to}: " . ($data['messages'][0]['error-text'] ?? 'Unknown error'));
            return false;
        } catch (\Exception $e) {
            Log::error("SMS exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format UK number: "07123456789" -> "447123456789"
     */
    public static function formatUkNumber(string $number): string
    {
        $number = preg_replace('/\s+/', '', $number);
        $number = preg_replace('/[^0-9+]/', '', $number);

        if (str_starts_with($number, '+44')) return substr($number, 1);
        if (str_starts_with($number, '44')) return $number;
        if (str_starts_with($number, '0')) return '44' . substr($number, 1);

        return $number;
    }
}
