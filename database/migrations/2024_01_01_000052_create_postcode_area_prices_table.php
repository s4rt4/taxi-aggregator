<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Postcode Area Prices (PAPs) - Minicabit style
     *
     * Prices between postcode area pairs (e.g. TW → WC).
     * Takes highest priority over PMP and LP.
     * Grid: all UK postcode areas x fleet types.
     */
    public function up(): void
    {
        // Reference table of UK postcode areas
        Schema::create('postcode_areas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->unique()->comment('e.g. TW, WC, EC, SW');
            $table->string('area_name')->comment('e.g. Twickenham, Western Central London');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('postcode_area_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->string('from_postcode_area', 4);
            $table->string('to_postcode_area', 4);
            $table->decimal('price', 10, 2)->comment('Fixed price in GBP');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['operator_id', 'fleet_type_id']);
            $table->index(['from_postcode_area', 'to_postcode_area']);
            $table->unique(['operator_id', 'fleet_type_id', 'from_postcode_area', 'to_postcode_area'], 'pap_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postcode_area_prices');
        Schema::dropIfExists('postcode_areas');
    }
};
