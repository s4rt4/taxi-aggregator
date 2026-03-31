<?php

namespace Tests\Feature;

use App\Models\FlashSale;
use App\Models\FleetType;
use App\Models\LocationPrice;
use App\Models\MeetGreetLocation;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class OperatorPricingTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected User $operatorUser;
    protected Operator $operator;
    protected FleetType $fleetType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operatorUser = $this->createOperatorUser();
        $this->operator = $this->createApprovedOperator($this->operatorUser);
        $this->fleetType = $this->createFleetTypeWithPricing($this->operator);
    }

    // ------------------------------------------------------------------
    // Per Mile Pricing
    // ------------------------------------------------------------------

    public function test_operator_can_view_pmp_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.pricing.per-mile'));

        $response->assertStatus(200);
    }

    public function test_operator_can_save_pmp_rates(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.pricing.save-per-mile'), [
                'rates' => [
                    $this->fleetType->id => [
                        'rate_per_mile' => 3.00,
                        'minimum_fare' => 12.00,
                    ],
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('per_mile_prices', [
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 3.00,
            'minimum_fare' => 12.00,
        ]);
    }

    // ------------------------------------------------------------------
    // Location Prices
    // ------------------------------------------------------------------

    public function test_operator_can_view_location_prices_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.pricing.location'));

        $response->assertStatus(200);
    }

    public function test_operator_can_add_a_location_price(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.pricing.store-location'), [
                'fleet_type_id' => $this->fleetType->id,
                'start_postcode' => 'SW1A',
                'start_radius_miles' => 5,
                'finish_postcode' => 'TW6',
                'finish_radius_miles' => 5,
                'price' => 45.00,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('location_prices', [
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'start_postcode' => 'SW1A',
            'finish_postcode' => 'TW6',
            'price' => 45.00,
        ]);
    }

    public function test_operator_can_delete_a_location_price(): void
    {
        $lp = LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'start_postcode' => 'EC1',
            'start_radius_miles' => 3,
            'finish_postcode' => 'W1',
            'finish_radius_miles' => 3,
            'price' => 20.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->operatorUser)
            ->delete(route('operator.pricing.destroy-location', $lp->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('location_prices', ['id' => $lp->id]);
    }

    // ------------------------------------------------------------------
    // Meet & Greet
    // ------------------------------------------------------------------

    public function test_operator_can_view_meet_and_greet_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.pricing.meet-greet'));

        $response->assertStatus(200);
    }

    public function test_operator_can_save_meet_and_greet_charges(): void
    {
        $location = MeetGreetLocation::create([
            'name' => 'Heathrow Terminal 5',
            'type' => 'airport',
            'code' => 'LHR',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.pricing.save-meet-greet'), [
                'charges' => [
                    $location->id => 5.00,
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meet_greet_charges', [
            'operator_id' => $this->operator->id,
            'meet_greet_location_id' => $location->id,
            'charge' => 5.00,
        ]);
    }

    // ------------------------------------------------------------------
    // Flash Sales
    // ------------------------------------------------------------------

    public function test_operator_can_view_flash_sales_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.pricing.flash-sales'));

        $response->assertStatus(200);
    }

    public function test_operator_can_create_a_flash_sale(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.pricing.store-flash-sale'), [
                'starts_at' => now()->addHour()->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'all_fleet_types' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('flash_sales', [
            'operator_id' => $this->operator->id,
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'status' => 'active',
        ]);
    }

    public function test_operator_can_disable_a_flash_sale(): void
    {
        $sale = FlashSale::create([
            'operator_id' => $this->operator->id,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'all_fleet_types' => true,
            'all_routes' => true,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->operatorUser)
            ->patch(route('operator.pricing.disable-flash-sale', $sale->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $sale->refresh();
        $this->assertEquals('disabled', $sale->status);
    }
}
