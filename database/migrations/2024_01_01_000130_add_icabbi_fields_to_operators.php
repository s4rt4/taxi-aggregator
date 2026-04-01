<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->boolean('icabbi_enabled')->default(false)->after('dispatch_system');
            $table->string('icabbi_api_url')->nullable()->after('icabbi_enabled');
            $table->string('icabbi_app_key')->nullable()->after('icabbi_api_url');
            $table->string('icabbi_secret_key')->nullable()->after('icabbi_app_key');
            $table->string('icabbi_integration_name')->nullable()->after('icabbi_secret_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn([
                'icabbi_enabled',
                'icabbi_api_url',
                'icabbi_app_key',
                'icabbi_secret_key',
                'icabbi_integration_name',
            ]);
        });
    }
};
