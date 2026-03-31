<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\FleetType;
use App\Models\Operator;
use App\Models\User;
use App\Notifications\OperatorApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->admin = $this->createAdmin();
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    public function test_admin_can_view_dashboard_with_stats(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    // ------------------------------------------------------------------
    // Operators list
    // ------------------------------------------------------------------

    public function test_admin_can_view_operators_list(): void
    {
        $operatorUser = $this->createOperatorUser();
        $this->createApprovedOperator($operatorUser);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.operators.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_pending_operators(): void
    {
        // Create a pending operator
        $operatorUser = $this->createOperatorUser();
        Operator::create([
            'user_id' => $operatorUser->id,
            'operator_name' => 'Pending Cabs',
            'email' => $operatorUser->email,
            'phone' => '020 7946 0000',
            'postcode' => 'SW1A 1AA',
            'address_line_1' => '1 Test Road',
            'city' => 'London',
            'licence_number' => 'PHO-99999',
            'licence_authority' => 'TfL',
            'licence_expiry' => now()->addYear(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.operators.pending'));

        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Approve / Reject operator
    // ------------------------------------------------------------------

    public function test_admin_can_approve_an_operator(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = Operator::create([
            'user_id' => $operatorUser->id,
            'operator_name' => 'Approve Me Cabs',
            'email' => $operatorUser->email,
            'phone' => '020 7946 0001',
            'postcode' => 'EC1',
            'address_line_1' => '2 Test Road',
            'city' => 'London',
            'licence_number' => 'PHO-APPV',
            'licence_authority' => 'TfL',
            'licence_expiry' => now()->addYear(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.operators.approve', $operator));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $operator->refresh();
        $this->assertEquals('approved', $operator->status);
        $this->assertNotNull($operator->approved_at);
        $this->assertEquals($this->admin->id, $operator->approved_by);

        Notification::assertSentTo($operatorUser, OperatorApproved::class);
    }

    public function test_admin_can_reject_an_operator_with_reason(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = Operator::create([
            'user_id' => $operatorUser->id,
            'operator_name' => 'Reject Me Cabs',
            'email' => $operatorUser->email,
            'phone' => '020 7946 0002',
            'postcode' => 'EC2',
            'address_line_1' => '3 Test Road',
            'city' => 'London',
            'licence_number' => 'PHO-RJCT',
            'licence_authority' => 'TfL',
            'licence_expiry' => now()->addYear(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.operators.reject', $operator), [
                'rejection_reason' => 'Missing documentation',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $operator->refresh();
        $this->assertEquals('rejected', $operator->status);
        $this->assertEquals('Missing documentation', $operator->rejection_reason);
    }

    // ------------------------------------------------------------------
    // Suspend / Reactivate
    // ------------------------------------------------------------------

    public function test_admin_can_suspend_an_operator(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = $this->createApprovedOperator($operatorUser);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.operators.suspend', $operator));

        $response->assertRedirect();

        $operator->refresh();
        $this->assertEquals('suspended', $operator->status);
    }

    public function test_admin_can_reactivate_an_operator(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = $this->createApprovedOperator($operatorUser);
        $operator->update(['status' => 'suspended']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.operators.reactivate', $operator));

        $response->assertRedirect();

        $operator->refresh();
        $this->assertEquals('approved', $operator->status);
    }

    // ------------------------------------------------------------------
    // Tier and Commission
    // ------------------------------------------------------------------

    public function test_admin_can_update_operator_tier(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = $this->createApprovedOperator($operatorUser);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.operators.update-tier', $operator), [
                'tier' => 'airport_approved',
            ]);

        $response->assertRedirect();

        $operator->refresh();
        $this->assertEquals('airport_approved', $operator->tier);
    }

    public function test_admin_can_update_operator_commission(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = $this->createApprovedOperator($operatorUser);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.operators.update-commission', $operator), [
                'commission_rate' => 15.00,
            ]);

        $response->assertRedirect();

        $operator->refresh();
        $this->assertEquals('15.00', $operator->commission_rate);
    }

    // ------------------------------------------------------------------
    // Bookings
    // ------------------------------------------------------------------

    public function test_admin_can_view_all_bookings(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.bookings.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_booking_detail(): void
    {
        $operatorUser = $this->createOperatorUser();
        $operator = $this->createApprovedOperator($operatorUser);
        $fleetType = $this->createFleetTypeWithPricing($operator);
        $passenger = $this->createPassenger();
        $booking = $this->createBooking($passenger, $operator, $fleetType);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.bookings.show', $booking));

        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Users
    // ------------------------------------------------------------------

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $user = $this->createPassenger(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.toggle-active', $user));

        $response->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->is_active);

        // Toggle back
        $this->actingAs($this->admin)
            ->post(route('admin.users.toggle-active', $user));

        $user->refresh();
        $this->assertTrue($user->is_active);
    }

    // ------------------------------------------------------------------
    // Revenue
    // ------------------------------------------------------------------

    public function test_admin_can_view_revenue_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.revenue'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    // ------------------------------------------------------------------
    // Access control
    // ------------------------------------------------------------------

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $passenger = $this->createPassenger();

        $response = $this->actingAs($passenger)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_operator_cannot_access_admin_routes(): void
    {
        $operatorUser = $this->createOperatorUser();

        $response = $this->actingAs($operatorUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
