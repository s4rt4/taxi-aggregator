<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flash Sales - Minicabit style
     *
     * Time-limited discounts set by operator.
     * Applied to pickup times within the sale period.
     * Can target specific fleet types or all, specific routes or all.
     * Discount can be percentage or fixed amount.
     */
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2)->comment('% or £ amount');
            $table->boolean('all_fleet_types')->default(true);
            $table->boolean('all_routes')->default(true);
            $table->enum('status', ['active', 'disabled', 'expired'])->default('active');
            $table->timestamps();

            $table->index(['operator_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });

        // Which fleet types the flash sale applies to (if not all)
        Schema::create('flash_sale_fleet_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->timestamps();

            $table->unique(['flash_sale_id', 'fleet_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sale_fleet_types');
        Schema::dropIfExists('flash_sales');
    }
};
