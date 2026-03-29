<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Meet & Greet Charges - Minicabit style
     *
     * Extra charges per airport/station/location for meet & greet service.
     * Long list of UK airports and stations with charge per operator.
     */
    public function up(): void
    {
        // Reference table for meet & greet locations (airports, stations, etc.)
        Schema::create('meet_greet_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Heathrow Terminal 5, Kings Cross Station');
            $table->enum('type', ['airport', 'station', 'port', 'other'])->default('airport');
            $table->string('code', 10)->nullable()->comment('IATA code for airports, e.g. LHR');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('meet_greet_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meet_greet_location_id')->constrained()->cascadeOnDelete();
            $table->decimal('charge', 8, 2)->comment('Extra charge in GBP');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['operator_id', 'meet_greet_location_id'], 'mg_operator_location_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meet_greet_charges');
        Schema::dropIfExists('meet_greet_locations');
    }
};
