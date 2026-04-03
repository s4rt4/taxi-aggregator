<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DisputeController as AdminDisputeController;
use App\Http\Controllers\Admin\IssueController as AdminIssueController;
use App\Http\Controllers\Admin\OperatorController as AdminOperatorController;
use App\Http\Controllers\Admin\RevenueController as AdminRevenueController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\StatementController as AdminStatementController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController as AdminAdminUserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AccountDeletionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationPageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\IcabbiWebhookController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Operator\AccountController as OperatorAccountController;
use App\Http\Controllers\Operator\StripeConnectController as OperatorStripeConnectController;
use App\Http\Controllers\Operator\AvailabilityController as OperatorAvailabilityController;
use App\Http\Controllers\Operator\BookingController as OperatorBookingController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Operator\DriverController as OperatorDriverController;
use App\Http\Controllers\Operator\IssueController as OperatorIssueController;
use App\Http\Controllers\Operator\OnboardingController as OperatorOnboardingController;
use App\Http\Controllers\Operator\PriceCheckerController as OperatorPriceCheckerController;
use App\Http\Controllers\Operator\PricingController as OperatorPricingController;
use App\Http\Controllers\Operator\StatementController as OperatorStatementController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Search
Route::post('/search', [SearchController::class, 'search'])->middleware('throttle:search')->name('search');

// Legal Pages
Route::get('privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('terms-of-service', [PageController::class, 'termsOfService'])->name('terms-of-service');
Route::get('cookie-policy', [PageController::class, 'cookiePolicy'])->name('cookie-policy');

// Info Pages
Route::get('about', [PageController::class, 'about'])->name('about');
Route::get('how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('for-operators', [PageController::class, 'forOperators'])->name('for-operators');
Route::get('contact', [PageController::class, 'contact'])->name('contact');

// City & Airport landing pages
Route::get('taxi/{slug}', [LocationPageController::class, 'city'])->name('city.show');
Route::get('airport-taxi/{slug}', [LocationPageController::class, 'airport'])->name('airport.show');

// Sitemap
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap')->header('Content-Type', 'application/xml');
});

// Webhooks (no CSRF verification)
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripe'])->name('webhooks.stripe');
Route::post('/webhooks/icabbi', [IcabbiWebhookController::class, 'handle'])->name('webhooks.icabbi');

// Auth - Guest only
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Auth - Logged in
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Email verification
    Route::get('verify-email', [VerifyEmailController::class, 'show'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');

    // Dashboard redirect
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Booking flow
    Route::get('/book/{quote}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/book/{quote}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');

    // Payment
    Route::get('/payment/{booking}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/{booking}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{booking}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Invoice
    Route::get('/invoice/{booking}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoice/cash-commission/{statement}', [InvoiceController::class, 'cashCommission'])->name('invoice.cash-commission');

    // Passenger portal
    Route::prefix('my')->name('passenger.')->group(function () {
        Route::get('bookings', [PassengerController::class, 'bookings'])->name('bookings');
        Route::get('bookings/{booking}', [PassengerController::class, 'bookingDetail'])->name('booking-detail');
        Route::post('bookings/{booking}/cancel', [PassengerController::class, 'cancelBooking'])->name('cancel-booking');
        Route::post('bookings/{booking}/review', [PassengerController::class, 'storeReview'])->name('store-review');
        Route::get('profile', [PassengerController::class, 'profile'])->name('profile');
        Route::post('profile', [PassengerController::class, 'updateProfile'])->name('update-profile');
        Route::get('delete-account', [AccountDeletionController::class, 'request'])->name('delete-account');
        Route::post('delete-account', [AccountDeletionController::class, 'destroy'])->name('delete-account.confirm');
    });
});

// Operator routes
Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    // Onboarding
    Route::get('onboarding', [OperatorOnboardingController::class, 'index'])->name('onboarding');
    Route::get('onboarding/step/{step}', [OperatorOnboardingController::class, 'step'])->name('onboarding.step');
    Route::post('onboarding/step/1', [OperatorOnboardingController::class, 'saveStep1'])->name('onboarding.save-step1');
    Route::post('onboarding/step/2', [OperatorOnboardingController::class, 'saveStep2'])->name('onboarding.save-step2');
    Route::post('onboarding/step/3', [OperatorOnboardingController::class, 'saveStep3'])->name('onboarding.save-step3');
    Route::post('onboarding/step/4', [OperatorOnboardingController::class, 'saveStep4'])->name('onboarding.save-step4');
    Route::post('onboarding/step/5', [OperatorOnboardingController::class, 'saveStep5'])->name('onboarding.save-step5');
    Route::get('onboarding/complete', [OperatorOnboardingController::class, 'complete'])->name('onboarding.complete');

    // Dashboard
    Route::get('dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');

    // VIEW section - Bookings
    Route::get('bookings', [OperatorBookingController::class, 'index'])->name('bookings.index');
    Route::patch('bookings/{booking}/status', [OperatorBookingController::class, 'updateStatus'])->name('bookings.update-status');

    // Fleet (placeholder for now)
    Route::get('fleet', fn () => view('placeholder', ['title' => 'Fleet Types']))->name('fleet.index');

    // Drivers
    Route::get('drivers', [OperatorDriverController::class, 'index'])->name('drivers.index');
    Route::post('drivers', [OperatorDriverController::class, 'store'])->name('drivers.store');
    Route::put('drivers/{driver}', [OperatorDriverController::class, 'update'])->name('drivers.update');
    Route::get('drivers/{driver}/edit', [OperatorDriverController::class, 'index'])->name('drivers.edit');
    Route::delete('drivers/{driver}', [OperatorDriverController::class, 'destroy'])->name('drivers.destroy');

    // Trip Issues & Ratings
    Route::get('issues', [OperatorIssueController::class, 'index'])->name('issues.index');

    // Statements
    Route::get('statements', [OperatorStatementController::class, 'index'])->name('statements.index');

    // ACTIONS section
    Route::get('price-checker', [OperatorPriceCheckerController::class, 'index'])->name('price-checker');

    // PRICING section
    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('per-mile', [OperatorPricingController::class, 'perMile'])->name('per-mile');
        Route::post('per-mile', [OperatorPricingController::class, 'savePerMile'])->name('save-per-mile');
        Route::get('location', [OperatorPricingController::class, 'location'])->name('location');
        Route::post('location', [OperatorPricingController::class, 'storeLocation'])->name('store-location');
        Route::delete('location/{id}', [OperatorPricingController::class, 'destroyLocation'])->name('destroy-location');
        Route::get('postcode-area', [OperatorPricingController::class, 'postcodeArea'])->name('postcode-area');
        Route::post('postcode-area', [OperatorPricingController::class, 'savePostcodeArea'])->name('save-postcode-area');
        Route::get('meet-greet', [OperatorPricingController::class, 'meetGreet'])->name('meet-greet');
        Route::post('meet-greet', [OperatorPricingController::class, 'saveMeetGreet'])->name('save-meet-greet');
        Route::get('flash-sales', [OperatorPricingController::class, 'flashSales'])->name('flash-sales');
        Route::post('flash-sales', [OperatorPricingController::class, 'storeFlashSale'])->name('store-flash-sale');
        Route::patch('flash-sales/{id}/disable', [OperatorPricingController::class, 'disableFlashSale'])->name('disable-flash-sale');
        Route::get('dead-leg', [OperatorPricingController::class, 'deadLeg'])->name('dead-leg');
        Route::get('more', [OperatorPricingController::class, 'more'])->name('more');
        Route::post('more/free-pickup', [OperatorPricingController::class, 'saveFreePickupPostcodes'])->name('save-free-pickup');
    });

    // AVAILABILITY section
    Route::prefix('availability')->name('availability.')->group(function () {
        Route::get('vehicles', [OperatorAvailabilityController::class, 'vehicles'])->name('vehicles');
        Route::post('vehicles', [OperatorAvailabilityController::class, 'saveVehicles'])->name('save-vehicles');
        Route::get('notice', [OperatorAvailabilityController::class, 'notice'])->name('notice');
        Route::post('notice', [OperatorAvailabilityController::class, 'saveNotice'])->name('save-notice');
        Route::get('trip-range', [OperatorAvailabilityController::class, 'tripRange'])->name('trip-range');
        Route::post('trip-range', [OperatorAvailabilityController::class, 'saveTripRange'])->name('save-trip-range');
        Route::get('hours', [OperatorAvailabilityController::class, 'hours'])->name('hours');
        Route::post('hours', [OperatorAvailabilityController::class, 'saveHours'])->name('save-hours');
        Route::get('pause', [OperatorAvailabilityController::class, 'pause'])->name('pause');
        Route::post('pause/immediate', [OperatorAvailabilityController::class, 'storeImmediatePause'])->name('store-immediate-pause');
        Route::post('pause/future', [OperatorAvailabilityController::class, 'storeFuturePause'])->name('store-future-pause');
    });

    // Stripe Connect
    Route::get('stripe/setup', [OperatorStripeConnectController::class, 'setup'])->name('stripe.setup');
    Route::get('stripe/return', [OperatorStripeConnectController::class, 'return'])->name('stripe.return');
    Route::get('stripe/refresh', [OperatorStripeConnectController::class, 'refresh'])->name('stripe.refresh');
    Route::get('stripe/dashboard', [OperatorStripeConnectController::class, 'dashboard'])->name('stripe.dashboard');

    // ACCOUNT section
    Route::get('account', [OperatorAccountController::class, 'index'])->name('account.index');
    Route::post('account/company', [OperatorAccountController::class, 'updateCompany'])->name('account.update-company');
    Route::post('account/contact', [OperatorAccountController::class, 'updateContact'])->name('account.update-contact');
    Route::post('account/authorised-contacts', [OperatorAccountController::class, 'updateAuthorisedContacts'])->name('account.update-authorised-contacts');
    Route::post('account/licence', [OperatorAccountController::class, 'updateLicence'])->name('account.update-licence');
    Route::post('account/payment', [OperatorAccountController::class, 'updatePayment'])->name('account.update-payment');
    Route::post('account/password', [OperatorAccountController::class, 'updatePassword'])->name('account.update-password');
    Route::post('account/icabbi', [OperatorAccountController::class, 'updateIcabbi'])->name('account.update-icabbi');
    Route::post('account/icabbi/test', [OperatorAccountController::class, 'testIcabbiConnection'])->name('account.test-icabbi');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Operators
    Route::middleware('can-admin:operators.view')->group(function () {
        Route::get('operators', [AdminOperatorController::class, 'index'])->name('operators.index');
        Route::get('operators/pending', [AdminOperatorController::class, 'pending'])->name('operators.pending');
        Route::get('operators/{operator}', [AdminOperatorController::class, 'show'])->name('operators.show');
    });
    Route::post('operators/{operator}/approve', [AdminOperatorController::class, 'approve'])->name('operators.approve')->middleware('can-admin:operators.approve');
    Route::post('operators/{operator}/reject', [AdminOperatorController::class, 'reject'])->name('operators.reject')->middleware('can-admin:operators.approve');
    Route::post('operators/{operator}/suspend', [AdminOperatorController::class, 'suspend'])->name('operators.suspend')->middleware('can-admin:operators.suspend');
    Route::post('operators/{operator}/reactivate', [AdminOperatorController::class, 'reactivate'])->name('operators.reactivate')->middleware('can-admin:operators.suspend');
    Route::patch('operators/{operator}/tier', [AdminOperatorController::class, 'updateTier'])->name('operators.update-tier')->middleware('can-admin:operators.edit-tier');
    Route::patch('operators/{operator}/commission', [AdminOperatorController::class, 'updateCommission'])->name('operators.update-commission')->middleware('can-admin:operators.edit-commission');

    // Bookings
    Route::middleware('can-admin:bookings.view')->group(function () {
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    });
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status')->middleware('can-admin:bookings.edit-status');
    Route::post('bookings/{booking}/note', [AdminBookingController::class, 'addNote'])->name('bookings.add-note')->middleware('can-admin:bookings.add-notes');

    // Revenue
    Route::get('revenue', [AdminRevenueController::class, 'index'])->name('revenue')->middleware('can-admin:revenue.view');

    // Disputes
    Route::middleware('can-admin:disputes.view')->group(function () {
        Route::get('disputes', [AdminDisputeController::class, 'index'])->name('disputes.index');
        Route::get('disputes/{dispute}', [AdminDisputeController::class, 'show'])->name('disputes.show');
    });
    Route::post('disputes/{dispute}/resolve', [AdminDisputeController::class, 'resolve'])->name('disputes.resolve')->middleware('can-admin:disputes.resolve');
    Route::post('disputes/{dispute}/message', [AdminDisputeController::class, 'addMessage'])->name('disputes.add-message')->middleware('can-admin:disputes.resolve');

    // Issues
    Route::get('issues', [AdminIssueController::class, 'index'])->name('issues.index')->middleware('can-admin:issues.view');

    // Users
    Route::middleware('can-admin:users.view')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    });
    Route::post('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active')->middleware('can-admin:users.manage');

    // Fleet Types
    Route::get('fleet-types', [AdminSettingsController::class, 'fleetTypes'])->name('fleet-types.index')->middleware('can-admin:fleet-types.manage');

    // Settings
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings')->middleware('can-admin:settings.view');
    Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update')->middleware('can-admin:settings.edit');

    // Statements
    Route::get('statements', [AdminStatementController::class, 'index'])->name('statements.index')->middleware('can-admin:statements.view');

    // Admin User Management
    Route::middleware('can-admin:admin-users.view')->group(function () {
        Route::get('admin-users', [AdminAdminUserController::class, 'index'])->name('admin-users.index');
        Route::get('admin-users/create', [AdminAdminUserController::class, 'create'])->name('admin-users.create')->middleware('can-admin:admin-users.manage');
        Route::post('admin-users', [AdminAdminUserController::class, 'store'])->name('admin-users.store')->middleware('can-admin:admin-users.manage');
        Route::get('admin-users/{user}/edit', [AdminAdminUserController::class, 'edit'])->name('admin-users.edit')->middleware('can-admin:admin-users.manage');
        Route::put('admin-users/{user}', [AdminAdminUserController::class, 'update'])->name('admin-users.update')->middleware('can-admin:admin-users.manage');
        Route::delete('admin-users/{user}', [AdminAdminUserController::class, 'destroy'])->name('admin-users.destroy')->middleware('can-admin:admin-users.manage');
    });

    // Role Management
    Route::middleware('can-admin:admin-roles.manage')->group(function () {
        Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [AdminRoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [AdminRoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
    });
});
