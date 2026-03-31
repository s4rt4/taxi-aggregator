<?php

namespace App\Services;

class VatService
{
    const UK_VAT_RATE = 20.0; // 20%

    /**
     * Calculate VAT amount from a net amount.
     */
    public static function calculateVat(float $netAmount): float
    {
        return round($netAmount * (self::UK_VAT_RATE / 100), 2);
    }

    /**
     * Add VAT to a net amount, returning the gross.
     */
    public static function addVat(float $netAmount): float
    {
        return round($netAmount * (1 + self::UK_VAT_RATE / 100), 2);
    }

    /**
     * Extract the VAT component from a gross (VAT-inclusive) amount.
     */
    public static function extractVat(float $grossAmount): float
    {
        return round($grossAmount - ($grossAmount / (1 + self::UK_VAT_RATE / 100)), 2);
    }

    /**
     * Get the net amount from a gross (VAT-inclusive) amount.
     */
    public static function netFromGross(float $grossAmount): float
    {
        return round($grossAmount / (1 + self::UK_VAT_RATE / 100), 2);
    }

    /**
     * Return a full VAT breakdown from a gross amount.
     */
    public static function breakdown(float $grossAmount): array
    {
        $net = self::netFromGross($grossAmount);
        $vat = round($grossAmount - $net, 2);
        return [
            'gross' => $grossAmount,
            'net' => $net,
            'vat' => $vat,
            'vat_rate' => self::UK_VAT_RATE,
        ];
    }
}
