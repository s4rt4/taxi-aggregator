<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statements / Payouts - Minicabit style
     *
     * Weekly financial statements showing:
     * - Fares owed to operator (for prepaid bookings)
     * - Commission deducted
     * - Performance penalties deducted
     * Operator can view by date range and print self-billing invoice.
     */
    public function up(): void
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_fares', 10, 2)->default(0)->comment('Total booking fares');
            $table->decimal('commission_deducted', 10, 2)->default(0);
            $table->decimal('fines_deducted', 10, 2)->default(0)->comment('Performance penalties');
            $table->decimal('adjustments', 10, 2)->default(0)->comment('Manual adjustments +/-');
            $table->decimal('net_amount', 10, 2)->default(0)->comment('Amount payable to operator');
            $table->string('currency', 3)->default('GBP');
            $table->unsignedInteger('prepaid_booking_count')->default(0);
            $table->unsignedInteger('cash_booking_count')->default(0);
            $table->string('stripe_transfer_id')->nullable();
            $table->string('stripe_payout_id')->nullable();
            $table->enum('status', ['draft', 'finalised', 'processing', 'paid', 'failed'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('operator_id');
            $table->index('status');
            $table->unique(['operator_id', 'period_start', 'period_end']);
        });

        Schema::create('statement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_type', ['prepaid', 'cash']);
            $table->decimal('fare_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->timestamps();

            $table->index('statement_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statement_items');
        Schema::dropIfExists('statements');
    }
};
