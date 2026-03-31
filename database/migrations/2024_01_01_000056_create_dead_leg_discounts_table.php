<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dead Leg Discounts - Minicabit style
     *
     * When a driver drops off a passenger far from base, the return trip is a "dead leg".
     * Operators can offer discounts on bookings that fill these dead legs.
     * Tracks the original trip, the DLD applied, and the linked booking.
     */
    public function up(): void
    {
        Schema::create('dead_leg_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->string('from_area')->comment('Dropoff area of original trip');
            $table->string('to_area')->comment('Pickup area back toward base');
            $table->dateTime('available_from');
            $table->dateTime('available_until');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2);
            $table->enum('status', ['active', 'claimed', 'expired', 'disabled'])->default('active');
            $table->unsignedBigInteger('original_booking_id')->nullable();
            $table->unsignedBigInteger('dld_booking_id')->nullable()->comment('Booking that claimed this DLD');
            $table->timestamps();

            $table->index(['operator_id', 'status']);
            $table->index(['available_from', 'available_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dead_leg_discounts');
    }
};
