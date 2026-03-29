<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_id')->unique()->nullable()->comment('Platform account ID e.g. OAPP100000XXX');

            // Company details (My Account > Company details)
            $table->string('operator_name')->comment('Cab operator name displayed to customers');
            $table->string('legal_company_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('registration_number')->nullable()->comment('Companies House number');
            $table->string('vat_number')->nullable();

            // Contact details (My Account > Contact details)
            $table->string('email');
            $table->string('phone', 20);
            $table->string('website')->nullable();
            $table->string('postcode', 10);
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('county')->nullable();

            // Office base coordinates (for Trip Range calculations)
            $table->decimal('base_lat', 10, 7)->nullable();
            $table->decimal('base_lng', 10, 7)->nullable();

            // Licence & Fleet (My Account > Licence & Fleet)
            $table->string('dispatch_system')->nullable()->comment('e.g. iCabbi, Autocab, Cordic');
            $table->string('licence_number')->comment('Private Hire Operator Licence');
            $table->string('licence_authority')->comment('Issuing council e.g. Transport for London');
            $table->date('licence_expiry');
            $table->unsignedInteger('fleet_size')->default(0)->comment('Total number of vehicles');
            $table->string('operator_licence_file')->nullable();
            $table->string('public_liability_insurance_file')->nullable();
            $table->date('public_liability_expiry')->nullable();

            // Payment type (My Account > Payment type)
            $table->boolean('accepts_prepaid')->default(true)->comment('Pre-paid card bookings');
            $table->boolean('accepts_cash')->default(false)->comment('Cash bookings');

            // Stripe Connect
            $table->string('stripe_account_id')->nullable();
            $table->enum('stripe_status', ['pending', 'active', 'restricted', 'disabled'])->default('pending');

            // Operator tier (Basic -> Airport Approved -> TOP TIER)
            $table->enum('tier', ['basic', 'airport_approved', 'top_tier'])->default('basic');

            // Platform status
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Commission
            $table->decimal('commission_rate', 5, 2)->default(12.00)->comment('Platform commission %');

            // Ratings & stats
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('total_bookings')->default(0);

            // Permissions/approvals
            $table->boolean('is_featured')->default(false);
            $table->boolean('dead_leg_approved')->default(false);
            $table->boolean('airport_approved')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('tier');
            $table->index('city');
            $table->index('postcode');
        });

        // Authorised contacts (My Account > Authorised contact)
        Schema::create('operator_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['primary', 'secondary']);
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->timestamps();

            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_contacts');
        Schema::dropIfExists('operators');
    }
};
