<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Number of Vehicles - how many cars can simultaneously commit per day per fleet type
        Schema::create('vehicle_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->enum('day_of_week', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
            $table->unsignedSmallInteger('max_vehicles')->default(0);
            $table->boolean('same_every_day')->default(true)->comment('Apply Mon value to all days');
            $table->timestamps();

            $table->unique(['operator_id', 'fleet_type_id', 'day_of_week'], 'va_unique');
        });

        // Notice Periods - minimum notice hours before pickup per fleet type
        Schema::create('notice_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->unsignedSmallInteger('hours_notice')->default(2)->comment('Minimum hours notice required');
            $table->timestamps();

            $table->unique(['operator_id', 'fleet_type_id']);
        });

        // Postcode Lead Times - extra notice for specific postcode areas
        Schema::create('postcode_lead_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->string('postcode_area', 4);
            $table->string('notice_type')->default('hours')->comment('hours or days');
            $table->unsignedSmallInteger('notice_value')->default(2);
            $table->json('fleet_type_ids')->nullable()->comment('Specific fleet types or null for all');
            $table->timestamps();

            $table->unique(['operator_id', 'postcode_area']);
        });

        // Trip Range - pickup & dropoff radius from operator's office base
        Schema::create('trip_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('pickup_range_miles')->default(50)->comment('Radius from office base');
            $table->unsignedSmallInteger('dropoff_range_miles')->default(200)->comment('How far driver will go');
            $table->timestamps();

            $table->unique('operator_id');
        });

        // Operating Hours - what times operator accepts pickups
        Schema::create('operating_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->boolean('is_24_hours')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->json('excluded_days')->nullable()->comment('Days of week not operating');
            $table->timestamps();

            $table->unique(['operator_id', 'fleet_type_id']);
        });

        // Pause Availability - temporary pauses (immediate or scheduled)
        Schema::create('availability_pauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->enum('pause_type', ['immediate', 'scheduled']);
            $table->unsignedSmallInteger('duration_minutes')->nullable()->comment('For immediate: 30, 60, etc.');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('all_fleet_types')->default(true);
            $table->json('fleet_type_ids')->nullable()->comment('Specific fleet types if not all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['operator_id', 'is_active']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_pauses');
        Schema::dropIfExists('operating_hours');
        Schema::dropIfExists('trip_ranges');
        Schema::dropIfExists('postcode_lead_times');
        Schema::dropIfExists('notice_periods');
        Schema::dropIfExists('vehicle_availability');
    }
};
