<?php

namespace App\Jobs;

use App\Models\Operator;
use App\Models\Statement;
use App\Models\StatementItem;
use App\Services\CommissionService;
use App\Services\Payment\StripeConnectService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWeeklyPayouts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(StripeConnectService $stripeConnect): void
    {
        $weekStart = Carbon::now()->subWeek()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $operators = Operator::where('status', 'approved')->get();

        foreach ($operators as $operator) {
            $this->processOperatorPayout($operator, $weekStart, $weekEnd, $stripeConnect);
        }
    }

    protected function processOperatorPayout(Operator $operator, Carbon $weekStart, Carbon $weekEnd, StripeConnectService $stripeConnect): void
    {
        // Get completed bookings for this period
        $bookings = $operator->bookings()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$weekStart, $weekEnd])
            ->get();

        if ($bookings->isEmpty()) {
            return;
        }

        // Calculate totals
        $grossFares = $bookings->sum('total_price');
        $totalCommission = 0;
        $totalFines = 0;
        $prepaidCount = 0;
        $cashCount = 0;

        $items = [];
        foreach ($bookings as $booking) {
            $calc = CommissionService::calculate($operator, $booking->total_price);
            $fineAmount = $booking->tripIssues()->sum('fine_amount');
            $totalCommission += $calc['commission'];
            $totalFines += $fineAmount;

            if ($booking->payment_type === 'prepaid') {
                $prepaidCount++;
            } else {
                $cashCount++;
            }

            $items[] = [
                'booking_id' => $booking->id,
                'payment_type' => $booking->payment_type,
                'fare_amount' => $booking->total_price,
                'commission_amount' => $calc['commission'],
                'fine_amount' => $fineAmount,
                'net_amount' => $booking->total_price - $calc['commission'] - $fineAmount,
            ];
        }

        $netAmount = $grossFares - $totalCommission - $totalFines;

        // Create statement
        $reference = 'STM-' . $operator->id . '-' . $weekStart->format('Ymd');
        $statement = Statement::create([
            'reference' => $reference,
            'operator_id' => $operator->id,
            'period_start' => $weekStart,
            'period_end' => $weekEnd,
            'gross_fares' => $grossFares,
            'commission_deducted' => $totalCommission,
            'fines_deducted' => $totalFines,
            'adjustments' => 0,
            'net_amount' => $netAmount,
            'currency' => 'GBP',
            'prepaid_booking_count' => $prepaidCount,
            'cash_booking_count' => $cashCount,
            'status' => 'pending',
        ]);

        // Create statement items
        foreach ($items as $item) {
            StatementItem::create(array_merge($item, ['statement_id' => $statement->id]));
        }

        // Process payout via Stripe Connect (for prepaid bookings)
        if ($netAmount > 0 && $operator->stripe_account_id && $operator->stripe_status === 'active') {
            try {
                $transfer = $stripeConnect->transferToOperator(
                    $operator,
                    $netAmount,
                    "Weekly payout {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}",
                    "statement_{$statement->id}"
                );

                if ($transfer) {
                    $statement->update([
                        'stripe_transfer_id' => $transfer->id,
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Payout failed for operator {$operator->id}: {$e->getMessage()}");
                $statement->update(['status' => 'failed']);
            }
        }
    }
}
