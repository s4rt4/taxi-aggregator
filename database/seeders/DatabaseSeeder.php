<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FleetTypeSeeder::class,
            PostcodeAreaSeeder::class,
            MeetGreetLocationSeeder::class,
            SiteSettingSeeder::class,
            AdminRoleSeeder::class,
        ]);

        // Create test users if they don't exist (admin users are created in AdminRoleSeeder)
        User::firstOrCreate(['email' => 'operator@test.com'], [
            'name' => 'Operator User',
            'role' => 'operator',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate(['email' => 'passenger@test.com'], [
            'name' => 'Passenger User',
            'role' => 'passenger',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
    }
}
