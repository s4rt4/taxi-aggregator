<?php

namespace Tests\Feature;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class AuthTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    // ------------------------------------------------------------------
    // Guest pages
    // ------------------------------------------------------------------

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Registration
    // ------------------------------------------------------------------

    public function test_user_can_register_as_passenger(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '07700 900001',
            'role' => 'passenger',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'passenger',
        ]);

        $this->assertAuthenticated();
    }

    public function test_user_can_register_as_operator(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Operator Joe',
            'email' => 'joe@cabs.com',
            'phone' => '07700 900002',
            'role' => 'operator',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect(route('operator.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'joe@cabs.com',
            'role' => 'operator',
        ]);

        $this->assertAuthenticated();
    }

    // ------------------------------------------------------------------
    // Login / Logout
    // ------------------------------------------------------------------

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = $this->createPassenger([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $this->createPassenger([
            'email' => 'wrong@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = $this->createPassenger();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    // ------------------------------------------------------------------
    // Authenticated user redirected from login
    // ------------------------------------------------------------------

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = $this->createPassenger();

        $response = $this->actingAs($user)->get(route('login'));

        // Guest middleware should redirect authenticated users away from login
        $response->assertRedirect();
    }

    // ------------------------------------------------------------------
    // Password reset
    // ------------------------------------------------------------------

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = $this->createPassenger(['email' => 'reset@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'reset@example.com',
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    // ------------------------------------------------------------------
    // Role middleware
    // ------------------------------------------------------------------

    public function test_operator_cannot_access_admin_routes(): void
    {
        $user = $this->createOperatorUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_access_operator_routes(): void
    {
        $user = $this->createPassenger();

        $response = $this->actingAs($user)->get(route('operator.dashboard'));

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_access_admin_routes(): void
    {
        $user = $this->createPassenger();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
