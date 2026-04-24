<?php

namespace Tests\Feature;

use App\Jobs\GenerateCorporateInvoices;
use App\Models\Booking;
use App\Models\Corporate;
use App\Models\CorporateInvoice;
use App\Models\CorporateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class CorporateTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    protected function createCorporateUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'corporate',
            'is_active' => true,
        ], $attrs));
    }

    protected function createApprovedCorporate(?User $superUser = null, array $attrs = []): Corporate
    {
        $superUser = $superUser ?? $this->createCorporateUser();

        $corporate = Corporate::create(array_merge([
            'company_name' => 'Acme Corp',
            'legal_name' => 'Acme Corporation Ltd',
            'business_type' => 'limited_company',
            'billing_email' => 'billing@acme.test',
            'billing_phone' => '020 1234 5678',
            'billing_address_line_1' => '1 Acme Way',
            'billing_city' => 'London',
            'billing_postcode' => 'EC1A 1AA',
            'monthly_budget' => 5000,
            'invoicing_frequency' => 'monthly',
            'payment_terms_days' => 14,
            'status' => 'approved',
            'approved_at' => now(),
        ], $attrs));

        CorporateUser::create([
            'corporate_id' => $corporate->id,
            'user_id' => $superUser->id,
            'corporate_role' => 'super_user',
            'is_active' => true,
        ]);

        return $corporate;
    }

    // ------------------------------------------------------------------
    // Registration & Onboarding
    // ------------------------------------------------------------------

    public function test_user_can_register_as_corporate(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Corporate Admin',
            'email' => 'corpadmin@example.com',
            'phone' => '07700 900000',
            'role' => 'corporate',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect(route('corporate.onboarding'));

        $this->assertDatabaseHas('users', [
            'email' => 'corpadmin@example.com',
            'role' => 'corporate',
        ]);
    }

    public function test_corporate_user_can_complete_onboarding(): void
    {
        $user = $this->createCorporateUser();

        // Step 1
        $this->actingAs($user)->post(route('corporate.onboarding.save', 1), [
            'company_name' => 'Acme Corp',
            'business_type' => 'limited_company',
        ])->assertRedirect(route('corporate.onboarding.step', 2));

        // Step 2
        $this->actingAs($user)->post(route('corporate.onboarding.save', 2), [
            'billing_email' => 'billing@acme.test',
            'billing_phone' => '020 1234 5678',
            'billing_address_line_1' => '1 Acme Way',
            'billing_city' => 'London',
            'billing_postcode' => 'EC1A 1AA',
        ])->assertRedirect(route('corporate.onboarding.step', 3));

        // Step 3
        $this->actingAs($user)->post(route('corporate.onboarding.save', 3), [
            'monthly_budget' => 5000,
            'invoicing_frequency' => 'monthly',
            'payment_terms_days' => 14,
        ])->assertRedirect(route('corporate.onboarding.complete'));

        $this->assertDatabaseHas('corporates', [
            'company_name' => 'Acme Corp',
            'billing_email' => 'billing@acme.test',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('corporate_users', [
            'user_id' => $user->id,
            'corporate_role' => 'super_user',
            'is_active' => true,
        ]);
    }

    // ------------------------------------------------------------------
    // Access control
    // ------------------------------------------------------------------

    public function test_non_corporate_user_cannot_access_corporate_dashboard(): void
    {
        $user = $this->createPassenger();

        $response = $this->actingAs($user)->get(route('corporate.dashboard'));
        $response->assertStatus(403);
    }

    public function test_corporate_user_without_active_profile_redirects_to_onboarding(): void
    {
        $user = $this->createCorporateUser();

        $response = $this->actingAs($user)->get(route('corporate.dashboard'));
        $response->assertRedirect(route('corporate.onboarding'));
    }

    public function test_approved_corporate_super_user_can_access_dashboard(): void
    {
        $superUser = $this->createCorporateUser();
        $this->createApprovedCorporate($superUser);

        $response = $this->actingAs($superUser)->get(route('corporate.dashboard'));
        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Employee management
    // ------------------------------------------------------------------

    public function test_super_user_can_invite_employee(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser);

        $response = $this->actingAs($superUser)->post(route('corporate.employees.store'), [
            'name' => 'Jane Employee',
            'email' => 'jane@acme.test',
            'corporate_role' => 'user',
            'monthly_budget' => 500,
        ]);

        $response->assertRedirect(route('corporate.employees.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'jane@acme.test',
            'role' => 'corporate',
        ]);

        $newUser = User::where('email', 'jane@acme.test')->first();
        $this->assertDatabaseHas('corporate_users', [
            'corporate_id' => $corporate->id,
            'user_id' => $newUser->id,
            'corporate_role' => 'user',
        ]);
    }

    public function test_regular_employee_cannot_invite_other_employees(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser);

        $employee = $this->createCorporateUser(['email' => 'employee@acme.test']);
        CorporateUser::create([
            'corporate_id' => $corporate->id,
            'user_id' => $employee->id,
            'corporate_role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->post(route('corporate.employees.store'), [
            'name' => 'Another Guy',
            'email' => 'another@acme.test',
            'corporate_role' => 'user',
        ]);

        $response->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Budget enforcement
    // ------------------------------------------------------------------

    public function test_corporate_company_budget_is_tracked(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser, ['monthly_budget' => 1000]);

        $corporate->addSpending(250);
        $this->assertEquals(250.00, $corporate->fresh()->monthly_spent);
        $this->assertEquals(750.00, $corporate->fresh()->remainingBudget());
        $this->assertTrue($corporate->fresh()->hasBudget(500));
        $this->assertFalse($corporate->fresh()->hasBudget(800));
    }

    public function test_unlimited_budget_corporate_always_has_budget(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser, ['monthly_budget' => null]);

        $this->assertNull($corporate->remainingBudget());
        $this->assertTrue($corporate->hasBudget(1000000));
    }

    // ------------------------------------------------------------------
    // Booking approval workflow
    // ------------------------------------------------------------------

    public function test_super_user_can_approve_pending_booking(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser);

        $employee = $this->createCorporateUser(['email' => 'emp@acme.test']);
        CorporateUser::create([
            'corporate_id' => $corporate->id,
            'user_id' => $employee->id,
            'corporate_role' => 'user',
            'is_active' => true,
            'requires_approval' => true,
        ]);

        $operator = $this->createApprovedOperator();
        $fleet = $this->createFleetTypeWithPricing($operator);
        $booking = $this->createBooking($employee, $operator, $fleet);
        $booking->update([
            'corporate_id' => $corporate->id,
            'approval_status' => 'pending',
        ]);

        $response = $this->actingAs($superUser)->post(route('corporate.bookings.approve', $booking));
        $response->assertRedirect();

        $this->assertEquals('approved', $booking->fresh()->approval_status);
    }

    public function test_super_user_can_reject_pending_booking(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser);

        $employee = $this->createCorporateUser(['email' => 'emp@acme.test']);
        CorporateUser::create([
            'corporate_id' => $corporate->id,
            'user_id' => $employee->id,
            'corporate_role' => 'user',
            'is_active' => true,
            'requires_approval' => true,
        ]);

        $operator = $this->createApprovedOperator();
        $fleet = $this->createFleetTypeWithPricing($operator);
        $booking = $this->createBooking($employee, $operator, $fleet);
        $booking->update([
            'corporate_id' => $corporate->id,
            'approval_status' => 'pending',
        ]);

        $response = $this->actingAs($superUser)->post(route('corporate.bookings.reject', $booking), [
            'reason' => 'Not authorised',
        ]);
        $response->assertRedirect();

        $booking->refresh();
        $this->assertEquals('rejected', $booking->approval_status);
        $this->assertEquals('cancelled', $booking->status);
    }

    // ------------------------------------------------------------------
    // Admin approval
    // ------------------------------------------------------------------

    public function test_admin_can_approve_new_corporate(): void
    {
        $admin = $this->createAdmin();
        $corporate = Corporate::create([
            'company_name' => 'Pending Co',
            'business_type' => 'limited_company',
            'billing_email' => 'pending@test.com',
            'billing_phone' => '020 1234 5678',
            'billing_address_line_1' => '1 Pending Way',
            'billing_city' => 'London',
            'billing_postcode' => 'EC1A 1AA',
            'invoicing_frequency' => 'monthly',
            'payment_terms_days' => 14,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.corporates.approve', $corporate));
        $response->assertRedirect();

        $corporate->refresh();
        $this->assertEquals('approved', $corporate->status);
        $this->assertNotNull($corporate->approved_at);
        $this->assertEquals($admin->id, $corporate->approved_by);
    }

    public function test_admin_can_suspend_corporate(): void
    {
        $admin = $this->createAdmin();
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser);

        $response = $this->actingAs($admin)->post(route('admin.corporates.suspend', $corporate), [
            'reason' => 'Payment overdue',
        ]);

        $response->assertRedirect();
        $this->assertEquals('suspended', $corporate->fresh()->status);
    }

    // ------------------------------------------------------------------
    // Invoice generation
    // ------------------------------------------------------------------

    public function test_invoice_job_generates_invoice_for_completed_bookings(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser, [
            'invoicing_frequency' => 'monthly',
        ]);

        $operator = $this->createApprovedOperator();
        $fleet = $this->createFleetTypeWithPricing($operator);

        // Create 2 completed bookings in last month
        $lastMonth = now()->subMonth();
        for ($i = 0; $i < 2; $i++) {
            $booking = $this->createBooking($superUser, $operator, $fleet);
            $booking->update([
                'corporate_id' => $corporate->id,
                'status' => 'completed',
                'completed_at' => $lastMonth->copy()->addDays(5),
                'total_price' => 100.00,
            ]);
        }

        (new GenerateCorporateInvoices('monthly'))->handle();

        $this->assertDatabaseHas('corporate_invoices', [
            'corporate_id' => $corporate->id,
            'total_bookings' => 2,
            'subtotal' => 200.00,
            'vat_amount' => 40.00,
            'total_amount' => 240.00,
        ]);

        // Bookings linked to invoice
        $invoice = CorporateInvoice::where('corporate_id', $corporate->id)->first();
        $this->assertEquals(2, $invoice->bookings()->count());
    }

    public function test_invoice_job_skips_already_invoiced_bookings(): void
    {
        $superUser = $this->createCorporateUser();
        $corporate = $this->createApprovedCorporate($superUser);
        $operator = $this->createApprovedOperator();
        $fleet = $this->createFleetTypeWithPricing($operator);

        $existingInvoice = CorporateInvoice::create([
            'corporate_id' => $corporate->id,
            'period_start' => now()->subMonth()->startOfMonth(),
            'period_end' => now()->subMonth()->endOfMonth(),
            'total_bookings' => 1,
            'subtotal' => 100,
            'vat_amount' => 20,
            'total_amount' => 120,
            'due_date' => now()->addDays(14),
            'status' => 'sent',
        ]);

        $booking = $this->createBooking($superUser, $operator, $fleet);
        $booking->update([
            'corporate_id' => $corporate->id,
            'corporate_invoice_id' => $existingInvoice->id,
            'status' => 'completed',
            'completed_at' => now()->subMonth()->addDays(5),
            'total_price' => 100.00,
        ]);

        (new GenerateCorporateInvoices('monthly'))->handle();

        // No new invoice
        $this->assertEquals(1, CorporateInvoice::where('corporate_id', $corporate->id)->count());
    }

    // ------------------------------------------------------------------
    // Dashboard redirect
    // ------------------------------------------------------------------

    public function test_corporate_dashboardroute_returns_corporate_route(): void
    {
        $user = $this->createCorporateUser();
        $this->assertEquals('corporate.dashboard', $user->dashboardRoute());
    }
}
