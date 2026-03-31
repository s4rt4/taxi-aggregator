<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\FleetType;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class PassengerPortalTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected User $passenger;
    protected User $operatorUser;
    protected Operator $operator;
    protected FleetType $fleetType;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->operatorUser = $this->createOperatorUser();
        $this->operator = $this->createApprovedOperator($this->operatorUser);
        $this->fleetType = $this->createFleetTypeWithPricing($this->operator);
        $this->passenger = $this->createPassenger();
    }

    // ------------------------------------------------------------------
    // Bookings list
    // ------------------------------------------------------------------

    public function test_passenger_can_view_their_bookings(): void
    {
        $this->createBooking($this->passenger, $this->operator, $this->fleetType);

        $response = $this->actingAs($this->passenger)
            ->get(route('passenger.bookings'));

        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Booking detail
    // ------------------------------------------------------------------

    public function test_passenger_can_view_booking_detail(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);

        $response = $this->actingAs($this->passenger)
            ->get(route('passenger.booking-detail', $booking));

        $response->assertStatus(200);
    }

    public function test_passenger_cannot_view_another_passengers_booking(): void
    {
        $otherPassenger = $this->createPassenger();
        $booking = $this->createBooking($otherPassenger, $this->operator, $this->fleetType);

        $response = $this->actingAs($this->passenger)
            ->get(route('passenger.booking-detail', $booking));

        $response->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Profile
    // ------------------------------------------------------------------

    public function test_passenger_can_view_profile(): void
    {
        $response = $this->actingAs($this->passenger)
            ->get(route('passenger.profile'));

        $response->assertStatus(200);
    }

    public function test_passenger_can_update_profile(): void
    {
        $response = $this->actingAs($this->passenger)
            ->post(route('passenger.update-profile'), [
                'name' => 'Updated Name',
                'email' => $this->passenger->email,
                'phone' => '07700 900999',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->passenger->refresh();
        $this->assertEquals('Updated Name', $this->passenger->name);
        $this->assertEquals('07700 900999', $this->passenger->phone);
    }

    // ------------------------------------------------------------------
    // Notifications
    // ------------------------------------------------------------------

    public function test_passenger_can_view_notifications_page(): void
    {
        $response = $this->actingAs($this->passenger)
            ->get(route('notifications.index'));

        $response->assertStatus(200);
    }
}
