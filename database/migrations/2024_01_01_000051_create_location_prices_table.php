<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Location Prices (LPs) - Minicabit style
     *
     * Fixed prices between any 2 zones/postcodes within the UK.
     * Operator enters start postcode + radius, finish postcode + radius, and a single price.
     * Takes priority over PMP but overridden by PAP.
     */
    public function up(): void
    {
        Schema::create('location_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->string('start_postcode', 10);
            $table->unsignedSmallInteger('start_radius_miles')->default(2);
            $table->string('finish_postcode', 10);
            $table->unsignedSmallInteger('finish_radius_miles')->default(2);
            $table->decimal('price', 10, 2)->comment('Fixed single price in GBP');
            $table->boolean('also_reverse')->default(false)->comment('Also create price for reverse direction');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['operator_id', 'fleet_type_id']);
            $table->index('start_postcode');
            $table->index('finish_postcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_prices');
    }
};
