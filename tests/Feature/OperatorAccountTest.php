<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class OperatorAccountTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected User $operatorUser;
    protected Operator $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operatorUser = $this->createOperatorUser();
        $this->operator = $this->createApprovedOperator($this->operatorUser);
    }

    // ------------------------------------------------------------------
    // Account page
    // ------------------------------------------------------------------

    public function test_operator_can_view_account_page(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.account.index'));

        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Company details
    // ------------------------------------------------------------------

    public function test_operator_can_update_company_details(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.account.update-company'), [
                'cab_operator_name' => 'New Cabs Ltd',
                'legal_company_name' => 'New Cabs Limited',
                'trading_name' => 'NewCabs',
                'registration_number' => '12345678',
                'vat_number' => 'GB123456789',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->operator->refresh();
        $this->assertEquals('New Cabs Ltd', $this->operator->operator_name);
        $this->assertEquals('New Cabs Limited', $this->operator->legal_company_name);
    }

    // ------------------------------------------------------------------
    // Contact details
    // ------------------------------------------------------------------

    public function test_operator_can_update_contact_details(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.account.update-contact'), [
                'office_email' => 'office@newcabs.com',
                'postcode' => 'EC1A 1BB',
                'address_line1' => '123 New Street',
                'city' => 'Manchester',
                'county' => 'Greater Manchester',
                'phone_country_code' => '+44',
                'office_phone' => '0161 123 4567',
                'website_url' => 'https://newcabs.com',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->operator->refresh();
        $this->assertEquals('office@newcabs.com', $this->operator->email);
        $this->assertEquals('EC1A 1BB', $this->operator->postcode);
        $this->assertEquals('Manchester', $this->operator->city);
    }

    // ------------------------------------------------------------------
    // Password change
    // ------------------------------------------------------------------

    public function test_operator_can_change_password_with_current_password_validation(): void
    {
        // Set a known password
        $this->operatorUser->update(['password' => Hash::make('OldPassword1!')]);

        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.account.update-password'), [
                'current_password' => 'OldPassword1!',
                'new_password' => 'NewPassword2!',
                'new_password_confirmation' => 'NewPassword2!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->operatorUser->refresh();
        $this->assertTrue(Hash::check('NewPassword2!', $this->operatorUser->password));
    }

    public function test_operator_cannot_change_password_with_wrong_current_password(): void
    {
        $this->operatorUser->update(['password' => Hash::make('OldPassword1!')]);

        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.account.update-password'), [
                'current_password' => 'WrongPassword!',
                'new_password' => 'NewPassword2!',
                'new_password_confirmation' => 'NewPassword2!',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    // ------------------------------------------------------------------
    // Auto-create operator record
    // ------------------------------------------------------------------

    public function test_new_operator_gets_auto_created_operator_record_on_first_account_update(): void
    {
        $newUser = $this->createOperatorUser(['name' => 'Fresh Operator']);

        // This user has no Operator record yet
        $this->assertNull($newUser->operator);

        $response = $this->actingAs($newUser)
            ->post(route('operator.account.update-company'), [
                'cab_operator_name' => 'Fresh Cabs',
                'legal_company_name' => 'Fresh Cabs Ltd',
            ]);

        $response->assertRedirect();

        $newUser->refresh();
        $this->assertNotNull($newUser->operator);
        $this->assertEquals('Fresh Cabs', $newUser->operator->operator_name);
        $this->assertEquals('pending', $newUser->operator->status);
    }

    // ------------------------------------------------------------------
    // Drivers
    // ------------------------------------------------------------------

    public function test_operator_can_view_their_drivers_list(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.drivers.index'));

        $response->assertStatus(200);
    }

    public function test_operator_can_add_a_driver(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->post(route('operator.drivers.store'), [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'licence_number' => 'DRV-001',
                'mobile_number' => '07700 900555',
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Prius',
                'registration_plate' => 'XY12 ZAB',
            ]);

        $response->assertRedirect(route('operator.drivers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('drivers', [
            'operator_id' => $this->operator->id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'licence_number' => 'DRV-001',
        ]);
    }

    public function test_operator_can_delete_a_driver(): void
    {
        $driver = Driver::create([
            'operator_id' => $this->operator->id,
            'first_name' => 'Delete',
            'last_name' => 'Me',
            'licence_number' => 'DRV-DEL',
            'mobile_number' => '07700 900666',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->operatorUser)
            ->delete(route('operator.drivers.destroy', $driver));

        $response->assertRedirect(route('operator.drivers.index'));

        // Soft deleted
        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }

    public function test_operator_cannot_access_another_operators_driver(): void
    {
        // Create a different operator's driver
        $otherUser = $this->createOperatorUser();
        $otherOperator = $this->createApprovedOperator($otherUser);

        $otherDriver = Driver::create([
            'operator_id' => $otherOperator->id,
            'first_name' => 'Other',
            'last_name' => 'Driver',
            'licence_number' => 'DRV-OTH',
            'mobile_number' => '07700 900777',
            'is_active' => true,
        ]);

        // Try to delete as the first operator
        $response = $this->actingAs($this->operatorUser)
            ->delete(route('operator.drivers.destroy', $otherDriver));

        $response->assertStatus(403);

        // Driver should still exist
        $this->assertDatabaseHas('drivers', ['id' => $otherDriver->id]);
    }
}
