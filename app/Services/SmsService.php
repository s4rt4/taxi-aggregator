<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send(string $to, string $message): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        if (!$sid || !$token || $sid === 'your_sid') {
            Log::info("SMS (skipped - not configured): To: {$to} | {$message}");
            return false;
        }

        try {
            $from = config('services.twilio.from');
            $toFormatted = '+' . self::formatUkNumber($to);

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => $toFormatted,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("SMS sent to {$to} | SID: " . ($data['sid'] ?? 'N/A'));
                return true;
            }

            Log::warning("SMS failed to {$to}: " . $response->body());
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
