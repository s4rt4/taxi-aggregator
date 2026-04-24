<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\Booking;
use App\Models\FleetType;
use App\Models\Operator;
use App\Models\User;
use App\Notifications\NewBookingReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class AdminJobAllocationTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected User $admin;
    protected Operator $originalOperator;
    protected Operator $newOperator;
    protected FleetType $fleetType;
    protected User $passenger;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->admin = $this->createAdmin();

        // Original operator
        $originalOperatorUser = $this->createOperatorUser();
        $this->originalOperator = $this->createApprovedOperator($originalOperatorUser);

        // New operator (target for reallocation)
        $newOperatorUser = $this->createOperatorUser();
        $this->newOperator = $this->createApprovedOperator($newOperatorUser);

        $this->fleetType = $this->createFleetTypeWithPricing($this->originalOperator);
        $this->passenger = $this->createPassenger();
    }

    public function test_admin_with_permission_can_allocate_pending_booking_to_another_operator(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
                'reason' => 'Load balancing',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals($this->newOperator->id, $booking->operator_id);
        $this->assertTrue($booking->is_reallocated);
        $this->assertEquals($this->admin->id, $booking->allocated_by);
        $this->assertNotNull($booking->allocated_at);
        $this->assertEquals('Load balancing', $booking->allocation_reason);
    }

    public function test_booking_status_resets_to_pending_after_allocation(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);
        $booking->update(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
            ]);

        $booking->refresh();
        $this->assertEquals('pending', $booking->status);
    }

    public function test_original_operator_id_is_stored_on_first_allocation_only(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);

        // First allocation
        $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
            ]);

        $booking->refresh();
        $this->assertEquals($this->originalOperator->id, $booking->original_operator_id);

        // Second allocation to a third operator
        $thirdOperatorUser = $this->createOperatorUser();
        $thirdOperator = $this->createApprovedOperator($thirdOperatorUser);

        $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $thirdOperator->id,
            ]);

        $booking->refresh();
        // original_operator_id should still be the very first operator, not the second
        $this->assertEquals($this->originalOperator->id, $booking->original_operator_id);
        $this->assertEquals($thirdOperator->id, $booking->operator_id);
    }

    public function test_cannot_allocate_completed_bookings(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);
        $booking->update(['status' => 'completed']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals($this->originalOperator->id, $booking->operator_id);
        $this->assertFalse((bool) $booking->is_reallocated);
    }

    public function test_cannot_allocate_cancelled_bookings(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);
        $booking->update(['status' => 'cancelled']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals($this->originalOperator->id, $booking->operator_id);
        $this->assertFalse((bool) $booking->is_reallocated);
    }

    public function test_cannot_allocate_to_non_approved_operator(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);

        // Create pending (non-approved) operator
        $pendingOperatorUser = $this->createOperatorUser();
        $pendingOperator = Operator::create([
            'user_id' => $pendingOperatorUser->id,
            'operator_name' => 'Pending Op',
            'email' => $pendingOperatorUser->email,
            'phone' => '020 7946 0000',
            'postcode' => 'SW1A 1AA',
            'address_line_1' => '1 Test Road',
            'city' => 'London',
            'licence_number' => 'PHO-PND-1',
            'licence_authority' => 'TfL',
            'licence_expiry' => now()->addYear(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $pendingOperator->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals($this->originalOperator->id, $booking->operator_id);
        $this->assertFalse((bool) $booking->is_reallocated);
    }

    public function test_notification_sent_to_new_operator_user(): void
    {
        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);

        $this->actingAs($this->admin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
            ]);

        Notification::assertSentTo($this->newOperator->user, NewBookingReceived::class);
    }

    public function test_admin_without_bookings_allocate_permission_gets_403(): void
    {
        // Create role WITHOUT bookings.allocate permission
        $limitedRole = AdminRole::create([
            'name' => 'Limited Admin',
            'slug' => 'limited-admin',
            'is_system' => false,
            'permissions' => [
                'bookings.view',
                'bookings.edit-status',
            ],
        ]);

        $limitedAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'admin_role_id' => $limitedRole->id,
        ]);

        $booking = $this->createBooking($this->passenger, $this->originalOperator, $this->fleetType);

        $response = $this->actingAs($limitedAdmin)
            ->post(route('admin.bookings.allocate', $booking), [
                'operator_id' => $this->newOperator->id,
            ]);

        $response->assertStatus(403);

        $booking->refresh();
        $this->assertEquals($this->originalOperator->id, $booking->operator_id);
        $this->assertFalse((bool) $booking->is_reallocated);
    }
}
