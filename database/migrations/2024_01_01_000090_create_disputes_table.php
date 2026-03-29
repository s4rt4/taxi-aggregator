<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raised_by')->constrained('users')->cascadeOnDelete();
            $table->enum('raised_by_role', ['passenger', 'operator']);
            $table->enum('type', [
                'overcharge',
                'no_show_driver',
                'no_show_passenger',
                'poor_service',
                'vehicle_issue',
                'route_issue',
                'payment_issue',
                'other',
            ]);
            $table->text('description');
            $table->enum('status', [
                'open',
                'under_review',
                'awaiting_response',
                'resolved',
                'closed',
            ])->default('open');
            $table->enum('resolution', [
                'full_refund',
                'partial_refund',
                'credit_issued',
                'no_action',
                'warning_issued',
                'operator_suspended',
            ])->nullable();
            $table->text('resolution_notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('booking_id');
            $table->index('status');
            $table->index('raised_by');
        });

        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false)->comment('Admin-only notes');
            $table->timestamps();

            $table->index('dispute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_messages');
        Schema::dropIfExists('disputes');
    }
};
