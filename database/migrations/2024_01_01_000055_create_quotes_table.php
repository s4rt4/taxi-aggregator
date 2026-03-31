<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->comment('For guest searches');
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            $table->string('destination_address');
            $table->decimal('destination_lat', 10, 7);
            $table->decimal('destination_lng', 10, 7);
            $table->dateTime('pickup_datetime');
            $table->unsignedTinyInteger('passenger_count')->default(1);
            $table->unsignedTinyInteger('luggage_count')->default(0);
            $table->decimal('distance_miles', 8, 2);
            $table->unsignedInteger('estimated_duration_minutes');
            $table->boolean('is_return')->default(false);
            $table->dateTime('return_datetime')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_search_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->enum('price_source', ['pmp', 'lp', 'pap'])->default('pmp')->comment('Which pricing method was used');
            $table->decimal('base_price', 10, 2);
            $table->decimal('meet_greet_charge', 8, 2)->default(0);
            $table->decimal('flash_sale_discount', 8, 2)->default(0);
            $table->decimal('dead_leg_discount', 8, 2)->default(0);
            $table->decimal('surcharges', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('GBP');
            $table->unsignedTinyInteger('max_passengers');
            $table->unsignedTinyInteger('max_luggage');
            $table->string('fleet_type_name');
            $table->string('operator_name');
            $table->decimal('operator_rating', 3, 2)->default(0);
            $table->unsignedInteger('estimated_duration_minutes');
            $table->boolean('meet_and_greet')->default(false);
            $table->foreignId('flash_sale_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('dead_leg_discount_id')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['quote_search_id', 'total_price']);
            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('quote_searches');
    }
};
