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
        ]);

        // Create test users if they don't exist
        User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Admin User',
            'role' => 'admin',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate(['email' => 'operator@test.com'], [
            'name' => 'Operator User',
            'role' => 'operator',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate(['email' => 'passenger@test.com'], [
            'name' => 'Passenger User',
            'role' => 'passenger',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
    }
}
