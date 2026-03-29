<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drivers - Minicabit style
     *
     * Each operator manages their own drivers.
     * Driver has: licence number, mobile, vehicle info (make/model), reg no, DBS check.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Optional user account');
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('licence_number')->comment('PHV driver licence');
            $table->string('mobile_number', 20);
            $table->string('vehicle_make')->nullable()->comment('e.g. Toyota');
            $table->string('vehicle_model')->nullable()->comment('e.g. Prius');
            $table->unsignedTinyInteger('vehicle_max_passengers')->nullable();
            $table->string('registration_plate', 15)->nullable();
            $table->enum('dbs_status', ['pending', 'clear', 'flagged', 'expired', 'not_checked'])->default('not_checked')->comment('DBS criminal record check');
            $table->date('dbs_expiry')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(false);
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('operator_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
