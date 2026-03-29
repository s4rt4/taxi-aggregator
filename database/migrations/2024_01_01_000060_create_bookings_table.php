<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique()->comment('Public booking ref e.g. TX-20240101-ABCD');
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_type_id')->constrained();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();

            // Journey details
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            $table->string('destination_address');
            $table->decimal('destination_lat', 10, 7);
            $table->decimal('destination_lng', 10, 7);
            $table->json('waypoints')->nullable()->comment('Intermediate stops');
            $table->decimal('distance_miles', 8, 2);
            $table->unsignedInteger('estimated_duration_minutes');
            $table->dateTime('pickup_datetime');
            $table->boolean('is_return_journey')->default(false);
            $table->dateTime('return_datetime')->nullable();

            // Passenger info
            $table->string('passenger_name');
            $table->string('passenger_phone', 20);
            $table->string('passenger_email')->nullable();
            $table->unsignedTinyInteger('passenger_count')->default(1);
            $table->unsignedTinyInteger('luggage_count')->default(0);
            $table->text('special_requirements')->nullable();

            // Flight/train info (for airport/station pickups)
            $table->string('flight_number', 20)->nullable();
            $table->string('train_number', 20)->nullable();

            // Meet & Greet
            $table->boolean('meet_and_greet')->default(false);
            $table->decimal('meet_greet_charge', 8, 2)->default(0);

            // Pricing
            $table->enum('price_source', ['pmp', 'lp', 'pap'])->default('pmp');
            $table->decimal('base_price', 10, 2)->comment('Price before commission');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('surcharges', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Flash sale / dead leg discount');
            $table->decimal('total_price', 10, 2)->comment('Final price charged to passenger');
            $table->string('currency', 3)->default('GBP');
            $table->enum('payment_type', ['prepaid', 'cash'])->default('prepaid');

            // Status
            $table->enum('status', [
                'pending',
                'accepted',
                'driver_assigned',
                'en_route',
                'arrived',
                'in_progress',
                'completed',
                'cancelled',
                'no_show',
                'disputed',
            ])->default('pending');

            $table->enum('cancelled_by', ['passenger', 'operator', 'admin'])->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('driver_assigned_at')->nullable();
            $table->timestamp('en_route_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('operator_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('reference');
            $table->index('status');
            $table->index('passenger_id');
            $table->index('operator_id');
            $table->index('pickup_datetime');
            $table->index(['status', 'pickup_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
