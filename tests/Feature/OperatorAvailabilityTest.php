<?php

namespace Tests\Feature;

use App\Models\FleetType;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class OperatorAvailabilityTest extends TestCase
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
    // Vehicles page
    // ------------------------------------------------------------------

    public function test_operator_can_view_vehicles_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.availability.vehicles'));

        $response->assertStatus(200);
    }

    public function test_operator_can_save_vehicle_availability(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.availability.save-vehicles'), [
                'vehicles' => [
                    $this->fleetType->id => [
                        'mon' => ['max_vehicles' => 10],
                        'tue' => ['max_vehicles' => 10],
                        'wed' => ['max_vehicles' => 10],
                        'thu' => ['max_vehicles' => 10],
                        'fri' => ['max_vehicles' => 10],
                        'sat' => ['max_vehicles' => 8],
                        'sun' => ['max_vehicles' => 5],
                    ],
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // ------------------------------------------------------------------
    // Trip Range
    // ------------------------------------------------------------------

    public function test_operator_can_view_trip_range_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.availability.trip-range'));

        $response->assertStatus(200);
    }

    public function test_operator_can_save_trip_range(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.availability.save-trip-range'), [
                'pickup_range_miles' => 75,
                'dropoff_range_miles' => 300,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trip_ranges', [
            'operator_id' => $this->operator->id,
            'pickup_range_miles' => 75,
            'dropoff_range_miles' => 300,
        ]);
    }

    // ------------------------------------------------------------------
    // Immediate Pause
    // ------------------------------------------------------------------

    public function test_operator_can_create_immediate_pause(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.availability.store-immediate-pause'), [
                'duration_minutes' => 60,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('availability_pauses', [
            'operator_id' => $this->operator->id,
            'pause_type' => 'immediate',
            'duration_minutes' => 60,
            'all_fleet_types' => true,
            'is_active' => true,
        ]);
    }

    // ------------------------------------------------------------------
    // Future Pause
    // ------------------------------------------------------------------

    public function test_operator_can_create_future_pause(): void
    {
        $startsAt = now()->addDays(2)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.availability.store-future-pause'), [
                'duration_minutes' => 120,
                'starts_at' => $startsAt,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('availability_pauses', [
            'operator_id' => $this->operator->id,
            'pause_type' => 'scheduled',
            'duration_minutes' => 120,
            'is_active' => true,
        ]);
    }

    // ------------------------------------------------------------------
    // Notice Periods
    // ------------------------------------------------------------------

    public function test_operator_can_view_notice_periods_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.availability.notice'));

        $response->assertStatus(200);
    }

    public function test_operator_can_save_notice_periods(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.availability.save-notice'), [
                'notice_periods' => [
                    $this->fleetType->id => 4,
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notice_periods', [
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'hours_notice' => 4,
        ]);
    }

    // ------------------------------------------------------------------
    // Operating Hours
    // ------------------------------------------------------------------

    public function test_operator_can_view_operating_hours_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.availability.hours'));

        $response->assertStatus(200);
    }

    public function test_operator_can_save_operating_hours(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.availability.save-hours'), [
                'hours' => [
                    $this->fleetType->id => [
                        'is_24_hours' => false,
                        'start_time' => '08:00',
                        'end_time' => '22:00',
                        'excluded_days' => [6], // Sunday
                    ],
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('operating_hours', [
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'is_24_hours' => false,
            'start_time' => '08:00:00',
            'end_time' => '22:00:00',
        ]);
    }
}
