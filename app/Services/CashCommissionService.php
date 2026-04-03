<?php

namespace App\Services;

use App\Models\Statement;

class CashCommissionService
{
    /**
     * Generate a commission invoice for cash bookings.
     * Cash bookings: operator collected the fare directly.
     * Platform needs to invoice operator for the commission portion.
     */
    public static function generateInvoice(Statement $statement): array
    {
        $cashItems = $statement->items()->where('payment_type', 'cash')->get();

        if ($cashItems->isEmpty()) {
            return ['has_cash' => false];
        }

        $totalCashFares = $cashItems->sum('fare_amount');
        $totalCommission = $cashItems->sum('commission_amount');
        $totalFines = $cashItems->sum('fine_amount');
        $amountDue = $totalCommission + $totalFines; // Operator owes this to platform

        return [
            'has_cash' => true,
            'invoice_reference' => 'CINV-' . $statement->reference,
            'operator' => $statement->operator,
            'period_start' => $statement->period_start,
            'period_end' => $statement->period_end,
            'cash_bookings_count' => $cashItems->count(),
            'total_cash_fares' => $totalCashFares,
            'commission_due' => $totalCommission,
            'fines_due' => $totalFines,
            'total_amount_due' => $amountDue,
            'items' => $cashItems,
            'vat' => VatService::breakdown($amountDue),
        ];
    }
}
