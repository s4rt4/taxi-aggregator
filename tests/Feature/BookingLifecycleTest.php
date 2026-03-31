<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\FleetType;
use App\Models\Operator;
use App\Models\Quote;
use App\Models\QuoteSearch;
use App\Models\Review;
use App\Models\User;
use App\Services\Pricing\QuoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class BookingLifecycleTest extends TestCase
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
    // Search validation
    // ------------------------------------------------------------------

    public function test_search_form_validation_rejects_missing_fields(): void
    {
        $response = $this->post(route('search'), []);

        $response->assertSessionHasErrors([
            'pickup_address',
            'destination_address',
            'pickup_date',
            'pickup_time',
            'passengers',
        ]);
    }

    // ------------------------------------------------------------------
    // Search returns results
    // ------------------------------------------------------------------

    public function test_search_returns_quote_results_page(): void
    {
        $response = $this->post(route('search'), [
            'pickup_address' => '10 Downing Street, London',
            'destination_address' => 'Heathrow Airport',
            'pickup_date' => now()->addDays(3)->format('Y-m-d'),
            'pickup_time' => '10:00',
            'passengers' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search.results');
        $response->assertViewHas('quotes');
    }

    // ------------------------------------------------------------------
    // QuoteService generates quotes
    // ------------------------------------------------------------------

    public function test_quote_service_generates_quotes_for_configured_operator(): void
    {
        $quoteService = app(QuoteService::class);

        $quoteSearch = $quoteService->generateQuotes([
            'user_id' => $this->passenger->id,
            'session_id' => 'test-session',
            'pickup_address' => '10 Downing Street, London',
            'pickup_lat' => 51.5034,
            'pickup_lng' => -0.1276,
            'pickup_postcode' => 'SW1A',
            'destination_address' => 'Heathrow Airport',
            'destination_lat' => 51.4700,
            'destination_lng' => -0.4543,
            'destination_postcode' => 'TW6',
            'pickup_datetime' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'passenger_count' => 2,
            'luggage_count' => 1,
            'distance_miles' => 15.0,
            'estimated_duration_minutes' => 30,
            'is_return' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertNotNull($quoteSearch);
        $this->assertGreaterThan(0, $quoteSearch->quotes->count());

        $quote = $quoteSearch->quotes->first();
        $this->assertEquals($this->operator->id, $quote->operator_id);
        $this->assertEquals($this->fleetType->id, $quote->fleet_type_id);
        $this->assertGreaterThan(0, $quote->total_price);
    }

    // ------------------------------------------------------------------
    // Booking form
    // ------------------------------------------------------------------

    public function test_passenger_can_view_booking_form_for_a_quote(): void
    {
        $quote = $this->createQuote();

        $response = $this->actingAs($this->passenger)
            ->get(route('booking.create', $quote));

        $response->assertStatus(200);
        $response->assertViewIs('booking.create');
    }

    // ------------------------------------------------------------------
    // Create booking from quote
    // ------------------------------------------------------------------

    public function test_passenger_can_create_booking_from_quote(): void
    {
        $quote = $this->createQuote();

        $response = $this->actingAs($this->passenger)
            ->post(route('booking.store', $quote), [
                'passenger_name' => 'Jane Doe',
                'passenger_phone' => '07700 900123',
                'passenger_email' => 'jane@example.com',
                'special_requirements' => 'Wheelchair access',
                'terms_accepted' => true,
            ]);

        $response->assertRedirect();

        $booking = Booking::where('quote_id', $quote->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals('pending', $booking->status);
        $this->assertEquals($this->passenger->id, $booking->passenger_id);
        $this->assertEquals($this->operator->id, $booking->operator_id);
    }

    // ------------------------------------------------------------------
    // Reference number format
    // ------------------------------------------------------------------

    public function test_booking_gets_reference_number_in_tx_format(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);

        $this->assertNotNull($booking->reference);
        $this->assertMatchesRegularExpression('/^TX-\d{8}-[A-F0-9]{4}$/', $booking->reference);
    }

    // ------------------------------------------------------------------
    // Operator views bookings
    // ------------------------------------------------------------------

    public function test_operator_can_view_booking_in_their_booking_log(): void
    {
        $this->createBooking($this->passenger, $this->operator, $this->fleetType);

        $response = $this->actingAs($this->operatorUser)
            ->get(route('operator.bookings.index'));

        $response->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Status transitions
    // ------------------------------------------------------------------

    public function test_operator_can_accept_booking(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);
        $this->assertEquals('pending', $booking->status);

        $response = $this->actingAs($this->operatorUser)
            ->patch(route('operator.bookings.update-status', $booking), [
                'status' => 'accepted',
            ]);

        $response->assertRedirect();

        $booking->refresh();
        $this->assertEquals('accepted', $booking->status);
        $this->assertNotNull($booking->accepted_at);
    }

    public function test_operator_can_mark_booking_as_completed(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);

        // Walk through the full status chain: pending -> accepted -> driver_assigned -> en_route -> arrived -> in_progress -> completed
        $booking->update(['status' => 'in_progress', 'started_at' => now()]);

        $response = $this->actingAs($this->operatorUser)
            ->patch(route('operator.bookings.update-status', $booking), [
                'status' => 'completed',
            ]);

        $response->assertRedirect();

        $booking->refresh();
        $this->assertEquals('completed', $booking->status);
        $this->assertNotNull($booking->completed_at);
    }

    public function test_invalid_status_transitions_are_rejected(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);
        $this->assertEquals('pending', $booking->status);

        // pending -> completed is not a valid transition
        $response = $this->actingAs($this->operatorUser)
            ->patch(route('operator.bookings.update-status', $booking), [
                'status' => 'completed',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals('pending', $booking->status);
    }

    // ------------------------------------------------------------------
    // Passenger cancellation
    // ------------------------------------------------------------------

    public function test_passenger_can_cancel_a_pending_booking(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);

        $response = $this->actingAs($this->passenger)
            ->post(route('passenger.cancel-booking', $booking), [
                'reason' => 'Changed my plans',
            ]);

        $response->assertRedirect(route('passenger.bookings'));

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
        $this->assertEquals('passenger', $booking->cancelled_by);
        $this->assertNotNull($booking->cancelled_at);
    }

    // ------------------------------------------------------------------
    // Reviews
    // ------------------------------------------------------------------

    public function test_passenger_can_leave_a_review_on_completed_booking(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);
        $booking->update(['status' => 'completed', 'completed_at' => now()]);

        $response = $this->actingAs($this->passenger)
            ->post(route('passenger.store-review', $booking), [
                'rating' => 5,
                'timing_rating' => 4,
                'comment' => 'Great service!',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'passenger_id' => $this->passenger->id,
            'operator_id' => $this->operator->id,
            'rating' => 5,
        ]);
    }

    public function test_passenger_cannot_review_uncompleted_booking(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);
        // booking is still 'pending'

        $response = $this->actingAs($this->passenger)
            ->post(route('passenger.store-review', $booking), [
                'rating' => 5,
                'comment' => 'Should not be saved',
            ]);

        $response->assertStatus(400);

        $this->assertDatabaseMissing('reviews', [
            'booking_id' => $booking->id,
        ]);
    }

    public function test_passenger_cannot_review_twice(): void
    {
        $booking = $this->createBooking($this->passenger, $this->operator, $this->fleetType);
        $booking->update(['status' => 'completed', 'completed_at' => now()]);

        // First review
        Review::create([
            'booking_id' => $booking->id,
            'passenger_id' => $this->passenger->id,
            'operator_id' => $this->operator->id,
            'rating' => 4,
        ]);

        // Attempt second review
        $response = $this->actingAs($this->passenger)
            ->post(route('passenger.store-review', $booking), [
                'rating' => 5,
                'comment' => 'Duplicate',
            ]);

        $response->assertStatus(400);

        $this->assertEquals(1, Review::where('booking_id', $booking->id)->count());
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    protected function createQuote(): Quote
    {
        $quoteSearch = QuoteSearch::create([
            'user_id' => $this->passenger->id,
            'session_id' => 'test-session',
            'pickup_address' => '10 Downing Street, London',
            'pickup_lat' => 51.5034,
            'pickup_lng' => -0.1276,
            'destination_address' => 'Heathrow Airport',
            'destination_lat' => 51.4700,
            'destination_lng' => -0.4543,
            'pickup_datetime' => now()->addDays(3),
            'passenger_count' => 2,
            'luggage_count' => 1,
            'distance_miles' => 15.00,
            'estimated_duration_minutes' => 30,
        ]);

        return Quote::create([
            'quote_search_id' => $quoteSearch->id,
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'price_source' => 'pmp',
            'base_price' => 37.50,
            'meet_greet_charge' => 0,
            'flash_sale_discount' => 0,
            'dead_leg_discount' => 0,
            'surcharges' => 0,
            'total_price' => 37.50,
            'currency' => 'GBP',
            'max_passengers' => 4,
            'max_luggage' => 2,
            'fleet_type_name' => 'Standard',
            'operator_name' => $this->operator->operator_name,
            'operator_rating' => 0,
            'estimated_duration_minutes' => 30,
            'meet_and_greet' => false,
            'is_available' => true,
            'expires_at' => now()->addMinutes(30),
        ]);
    }
}
