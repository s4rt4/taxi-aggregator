<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('passenger', 'operator', 'driver', 'admin', 'corporate') NOT NULL DEFAULT 'passenger'");
        }
        // SQLite doesn't enforce enum constraints, so no-op for other drivers
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('passenger', 'operator', 'driver', 'admin') NOT NULL DEFAULT 'passenger'");
        }
    }
};
