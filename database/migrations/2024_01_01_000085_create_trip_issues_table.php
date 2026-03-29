<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trip Issues tracking - Minicabit style
     *
     * Tracks weekly performance metrics per operator:
     * - Number of pickups
     * - Rejected trips
     * - Driver no shows
     * - Lost from no shows (revenue lost)
     * - Late trips
     * - Failed meet & greet
     * Fines may be applied after investigation.
     */
    public function up(): void
    {
        Schema::create('trip_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->enum('issue_type', [
                'rejected',
                'driver_no_show',
                'passenger_no_show',
                'late_pickup',
                'failed_meet_greet',
                'wrong_vehicle',
                'overcharge',
                'poor_condition',
                'rude_driver',
                'unsafe_driving',
                'other',
            ]);
            $table->text('description')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0)->comment('Penalty applied after investigation');
            $table->enum('fine_status', ['none', 'pending', 'applied', 'waived'])->default('none');
            $table->enum('investigation_status', ['open', 'investigating', 'resolved'])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['operator_id', 'issue_type']);
            $table->index('booking_id');
            $table->index('created_at');
        });

        // Weekly performance summary per operator (aggregated view)
        Schema::create('operator_weekly_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->unsignedInteger('total_pickups')->default(0);
            $table->unsignedInteger('rejected_trips')->default(0);
            $table->unsignedInteger('driver_no_shows')->default(0);
            $table->decimal('lost_from_no_shows', 10, 2)->default(0)->comment('Revenue lost');
            $table->unsignedInteger('late_trips')->default(0);
            $table->unsignedInteger('failed_meet_greets')->default(0);
            $table->decimal('total_fines', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['operator_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_weekly_stats');
        Schema::dropIfExists('trip_issues');
    }
};
