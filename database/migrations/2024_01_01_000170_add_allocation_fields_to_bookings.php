<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_reallocated')->default(false)->after('operator_id');
            $table->unsignedBigInteger('original_operator_id')->nullable()->after('is_reallocated');
            $table->unsignedBigInteger('allocated_by')->nullable()->after('original_operator_id'); // admin user id
            $table->timestamp('allocated_at')->nullable()->after('allocated_by');
            $table->text('allocation_reason')->nullable()->after('allocated_at');

            $table->foreign('original_operator_id')->references('id')->on('operators')->nullOnDelete();
            $table->foreign('allocated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['original_operator_id']);
            $table->dropForeign(['allocated_by']);
            $table->dropColumn([
                'is_reallocated',
                'original_operator_id',
                'allocated_by',
                'allocated_at',
                'allocation_reason',
            ]);
        });
    }
};
