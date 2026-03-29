<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fleet types based on passenger capacity (Minicabit style)
        // e.g. "Standard" (1-4 pax), "Estate" (1-4 pax), "MPV" (5-6 pax),
        //      "7-seater", "8-seater", "9-seater", "Minibus 10-14", "Minibus 15-16"
        Schema::create('fleet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Standard, Estate, MPV, 8-Seater');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('min_passengers')->default(1);
            $table->unsignedTinyInteger('max_passengers');
            $table->string('fuel_category')->default('petrol_diesel_hybrid')->comment('petrol_diesel_hybrid or electric');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Actual vehicles registered by operator
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->string('make')->comment('e.g. Toyota');
            $table->string('model')->comment('e.g. Prius');
            $table->string('colour', 30)->nullable();
            $table->year('year')->nullable();
            $table->string('registration_plate', 15);
            $table->unsignedTinyInteger('max_passengers')->default(4);
            $table->unsignedTinyInteger('max_luggage')->default(2);
            $table->boolean('wheelchair_accessible')->default(false);
            $table->boolean('child_seat_available')->default(false);
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('operator_id');
            $table->index('fleet_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('fleet_types');
    }
};
