<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Free Pickup Postcodes (More Pricing Options) - Minicabit style
     *
     * Postcode areas where operator covers pickup without extra charge.
     * Uses base postcode pricing instead of adding pickup surcharge.
     */
    public function up(): void
    {
        Schema::create('free_pickup_postcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->string('postcode_area', 4)->comment('e.g. TW, SW, W');
            $table->timestamps();

            $table->unique(['operator_id', 'postcode_area']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_pickup_postcodes');
    }
};
