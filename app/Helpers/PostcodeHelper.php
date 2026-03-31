<?php

namespace App\Helpers;

class PostcodeHelper
{
    /**
     * Format postcode to standard form: "SW1A 1AA"
     */
    public static function format(string $postcode): string
    {
        $postcode = strtoupper(preg_replace('/\s+/', '', trim($postcode)));
        if (strlen($postcode) >= 5) {
            return substr($postcode, 0, -3) . ' ' . substr($postcode, -3);
        }
        return $postcode;
    }

    /**
     * Extract outward code: "SW1A 1AA" -> "SW1A"
     */
    public static function outwardCode(string $postcode): string
    {
        $formatted = self::format($postcode);
        return explode(' ', $formatted)[0] ?? $postcode;
    }

    /**
     * Extract area: "SW1A 1AA" -> "SW"
     */
    public static function area(string $postcode): string
    {
        $postcode = strtoupper(trim($postcode));
        preg_match('/^([A-Z]{1,2})/', $postcode, $matches);
        return $matches[1] ?? '';
    }
}
