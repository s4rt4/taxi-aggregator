<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per Mile Prices (PMP) - Minicabit style
     *
     * Matrix of mileage ranges x fleet types.
     * Each operator sets rate per mile for each fleet type,
     * with mileage range brackets and uplift percentages.
     */
    public function up(): void
    {
        // Base per-mile rates per operator per fleet type
        Schema::create('per_mile_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->decimal('rate_per_mile', 8, 2)->comment('Base rate per mile in GBP inc. commission');
            $table->decimal('minimum_fare', 8, 2)->default(0)->comment('Minimum charge regardless of distance');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['operator_id', 'fleet_type_id']);
        });

        // Mileage range brackets for rate variation
        // e.g. 0-5 miles: £2/mile, 5-10 miles: £1.80/mile, etc.
        Schema::create('per_mile_price_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('per_mile_price_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('mile_from');
            $table->unsignedInteger('mile_to')->nullable()->comment('null = unlimited');
            $table->decimal('rate_per_mile', 8, 2);
            $table->timestamps();

            $table->index('per_mile_price_id');
        });

        // Uplift pricing - percentage adjustments per distance band per fleet type
        // Minicabit shows uplift grid: distance bands on X, fleet types on Y
        Schema::create('per_mile_uplifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->unsignedInteger('mile_from');
            $table->unsignedInteger('mile_to')->nullable();
            $table->decimal('uplift_percentage', 5, 2)->default(0)->comment('e.g. 5.00 = +5%');
            $table->timestamps();

            $table->index(['operator_id', 'fleet_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('per_mile_uplifts');
        Schema::dropIfExists('per_mile_price_ranges');
        Schema::dropIfExists('per_mile_prices');
    }
};
