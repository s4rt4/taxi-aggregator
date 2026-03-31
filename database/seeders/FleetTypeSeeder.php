<?php

namespace Database\Seeders;

use App\Models\FleetType;
use Illuminate\Database\Seeder;

class FleetTypeSeeder extends Seeder
{
    /**
     * Seed the fleet_types table with Minicabit-style fleet types.
     */
    public function run(): void
    {
        $fleetTypes = [
            ['name' => '1-4 Passengers', 'slug' => '1-4-passengers', 'min_passengers' => 1, 'max_passengers' => 4, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 1],
            ['name' => '1-4 Passengers (Estate)', 'slug' => '1-4-passengers-estate', 'min_passengers' => 1, 'max_passengers' => 4, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 2],
            ['name' => '5-6 Passengers', 'slug' => '5-6-passengers', 'min_passengers' => 5, 'max_passengers' => 6, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 3],
            ['name' => '7 Passengers', 'slug' => '7-passengers', 'min_passengers' => 7, 'max_passengers' => 7, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 4],
            ['name' => '8 Passengers', 'slug' => '8-passengers', 'min_passengers' => 8, 'max_passengers' => 8, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 5],
            ['name' => '9 Passengers', 'slug' => '9-passengers', 'min_passengers' => 9, 'max_passengers' => 9, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 6],
            ['name' => '10-14 Passengers', 'slug' => '10-14-passengers', 'min_passengers' => 10, 'max_passengers' => 14, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 7],
            ['name' => '15-16 Passengers', 'slug' => '15-16-passengers', 'min_passengers' => 15, 'max_passengers' => 16, 'fuel_category' => 'petrol_diesel_hybrid', 'sort_order' => 8],
        ];

        foreach ($fleetTypes as $fleetType) {
            FleetType::updateOrCreate(
                ['slug' => $fleetType['slug']],
                $fleetType
            );
        }
    }
}
