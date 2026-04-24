<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporates', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('legal_name')->nullable();
            $table->enum('business_type', ['limited_company', 'sole_trader', 'partnership', 'llp', 'public_sector', 'charity'])->default('limited_company');
            $table->string('registration_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('billing_email');
            $table->string('billing_phone', 20);
            $table->string('billing_address_line_1');
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city');
            $table->string('billing_postcode', 10);
            $table->string('billing_county')->nullable();
            $table->decimal('monthly_budget', 10, 2)->nullable()->comment('Total company monthly limit, null = unlimited');
            $table->decimal('monthly_spent', 10, 2)->default(0);
            $table->enum('invoicing_frequency', ['weekly', 'monthly'])->default('monthly');
            $table->unsignedTinyInteger('payment_terms_days')->default(14);
            $table->enum('status', ['pending', 'approved', 'suspended', 'closed'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
        });

        Schema::create('corporate_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('corporate_role', ['super_user', 'user'])->default('user');
            $table->string('employee_id')->nullable();
            $table->string('cost_centre')->nullable();
            $table->decimal('monthly_budget', 10, 2)->nullable()->comment('Per-user limit, null = no personal limit');
            $table->decimal('monthly_spent', 10, 2)->default(0);
            $table->boolean('requires_approval')->default(false)->comment('Bookings need super_user approval before confirmation');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['corporate_id', 'user_id']);
        });

        Schema::create('corporate_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('total_bookings');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('GBP');
            $table->date('due_date');
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['corporate_id', 'status']);
        });

        // Add corporate fields to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('corporate_id')->nullable()->after('passenger_id')->constrained()->nullOnDelete();
            $table->foreignId('corporate_invoice_id')->nullable()->after('corporate_id')->constrained()->nullOnDelete();
            $table->string('cost_centre')->nullable()->after('corporate_invoice_id');
            $table->enum('approval_status', ['not_required', 'pending', 'approved', 'rejected'])->default('not_required')->after('cost_centre');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['corporate_id']);
            $table->dropForeign(['corporate_invoice_id']);
            $table->dropColumn(['corporate_id', 'corporate_invoice_id', 'cost_centre', 'approval_status']);
        });

        Schema::dropIfExists('corporate_invoices');
        Schema::dropIfExists('corporate_users');
        Schema::dropIfExists('corporates');
    }
};
