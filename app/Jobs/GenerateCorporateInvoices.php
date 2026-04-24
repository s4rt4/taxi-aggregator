<?php

namespace App\Jobs;

use App\Models\Corporate;
use App\Models\CorporateInvoice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateCorporateInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $frequency = 'monthly')
    {
        //
    }

    public function handle(): void
    {
        if ($this->frequency === 'weekly') {
            $periodStart = Carbon::now()->subWeek()->startOfWeek();
            $periodEnd = Carbon::now()->subWeek()->endOfWeek();
        } else {
            $periodStart = Carbon::now()->subMonth()->startOfMonth();
            $periodEnd = Carbon::now()->subMonth()->endOfMonth();
        }

        $corporates = Corporate::approved()
            ->where('invoicing_frequency', $this->frequency)
            ->get();

        foreach ($corporates as $corporate) {
            try {
                $this->generateInvoiceForCorporate($corporate, $periodStart, $periodEnd);
            } catch (\Exception $e) {
                Log::error("Failed to generate invoice for corporate {$corporate->id}: " . $e->getMessage());
            }
        }
    }

    protected function generateInvoiceForCorporate(Corporate $corporate, Carbon $periodStart, Carbon $periodEnd): ?CorporateInvoice
    {
        // Get completed bookings that haven't been invoiced
        $bookings = $corporate->bookings()
            ->where('status', 'completed')
            ->whereNull('corporate_invoice_id')
            ->whereBetween('completed_at', [$periodStart, $periodEnd])
            ->get();

        if ($bookings->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($corporate, $periodStart, $periodEnd, $bookings) {
            $subtotal = (float) $bookings->sum('total_price');
            $vatAmount = round($subtotal * 0.20, 2);
            $totalAmount = round($subtotal + $vatAmount, 2);
            $dueDate = Carbon::now()->addDays($corporate->payment_terms_days ?? 14);

            $invoice = CorporateInvoice::create([
                'corporate_id' => $corporate->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'total_bookings' => $bookings->count(),
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'currency' => 'GBP',
                'due_date' => $dueDate->toDateString(),
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Link bookings to invoice
            $corporate->bookings()
                ->whereIn('id', $bookings->pluck('id'))
                ->update(['corporate_invoice_id' => $invoice->id]);

            // Send email (best-effort)
            try {
                Mail::raw(
                    "A new invoice ({$invoice->reference}) for £" . number_format($totalAmount, 2) .
                    " has been generated for {$corporate->company_name}.\n\n" .
                    "Period: {$periodStart->format('d M Y')} - {$periodEnd->format('d M Y')}\n" .
                    "Due: {$dueDate->format('d M Y')}\n" .
                    "Bookings: {$bookings->count()}",
                    function ($message) use ($corporate, $invoice) {
                        $message->to($corporate->billing_email)
                            ->subject("New Invoice {$invoice->reference} - " . config('app.name'));
                    }
                );
            } catch (\Exception $e) {
                Log::warning("Failed to email invoice {$invoice->reference}: " . $e->getMessage());
            }

            return $invoice;
        });
    }
}
