<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating')->comment('Overall 1-5 stars');
            $table->unsignedTinyInteger('timing_rating')->nullable()->comment('1-5 punctuality');
            $table->unsignedTinyInteger('fare_rating')->nullable()->comment('1-5 value for money');
            $table->unsignedTinyInteger('driver_rating')->nullable()->comment('1-5');
            $table->unsignedTinyInteger('vehicle_rating')->nullable()->comment('1-5');
            $table->unsignedTinyInteger('route_rating')->nullable()->comment('1-5');
            $table->text('comment')->nullable();
            $table->text('operator_reply')->nullable();
            $table->timestamp('operator_replied_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique('booking_id');
            $table->index('operator_id');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
