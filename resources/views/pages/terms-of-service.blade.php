@extends('layouts.app')

@section('title', 'Terms of Service')
@section('meta_description', 'Read our terms of service governing the use of our taxi comparison and booking platform.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Terms of Service</h1>
            <p class="text-muted mb-4">Last updated: {{ date('j F Y') }}</p>

            <div class="card mb-4">
                <div class="card-body">
                    <p>These Terms of Service ("Terms") govern your use of the {{ \App\Helpers\Settings::get('company_name', config('app.name')) }} platform ("Platform"). By accessing or using the Platform, you agree to be bound by these Terms. If you do not agree with any part of these Terms, you must not use the Platform.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">1. Platform Description</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>{{ \App\Helpers\Settings::get('company_name', config('app.name')) }} operates as a <strong>marketplace and comparison platform</strong> that connects passengers with licensed taxi and private hire operators. We are <strong>not a transport operator</strong> and do not directly provide transportation services.</p>
                    <p>Our Platform enables you to:</p>
                    <ul>
                        <li>Compare prices from multiple licensed operators</li>
                        <li>Book journeys through our Platform</li>
                        <li>Manage your bookings and communicate with operators</li>
                    </ul>
                    <p>The contract for transportation is between you and the operator. We act as an intermediary to facilitate the booking.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">2. User Obligations</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>By using the Platform, you agree to:</p>
                    <ul>
                        <li>Provide accurate and complete information when creating an account and making bookings.</li>
                        <li>Keep your account credentials secure and not share them with third parties.</li>
                        <li>Be at least 18 years of age, or have parental/guardian consent.</li>
                        <li>Not use the Platform for any unlawful purpose or in violation of these Terms.</li>
                        <li>Not attempt to interfere with the proper functioning of the Platform.</li>
                        <li>Treat operators, drivers, and other users with respect.</li>
                        <li>Ensure passengers under 18 are accompanied by a responsible adult where required by law.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">3. Booking Terms</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>When you make a booking through the Platform:</p>
                    <ul>
                        <li>You are entering into a contract with the transport operator for the provision of transportation services.</li>
                        <li>Prices displayed are provided by operators and include VAT where applicable.</li>
                        <li>We will send you a booking confirmation by email once your booking is accepted.</li>
                        <li>You are responsible for ensuring that the journey details (pickup location, time, destination) are correct.</li>
                        <li>The operator is responsible for providing the transportation service as agreed.</li>
                        <li>Meet and greet services, where available, may incur additional charges as displayed at the time of booking.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">4. Cancellation Policy</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>Cancellation terms vary depending on the type of booking and the operator:</p>
                    <ul>
                        <li><strong>Free cancellation:</strong> Bookings may be cancelled free of charge up to the cancellation deadline specified in your booking confirmation.</li>
                        <li><strong>Late cancellations:</strong> Cancellations made after the deadline may be subject to a cancellation fee, up to the full booking amount.</li>
                        <li><strong>No-shows:</strong> If you fail to meet your driver at the agreed time and location, the full fare may be charged.</li>
                        <li><strong>Operator cancellations:</strong> If an operator cancels your booking, we will attempt to find an alternative operator or provide a full refund.</li>
                    </ul>
                    <p>You can cancel your booking through your account dashboard or by contacting our support team.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">5. Payment Terms</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <ul>
                        <li>Payment is collected at the time of booking via our secure payment processor (Stripe).</li>
                        <li>All prices are displayed in British Pounds (GBP).</li>
                        <li>Additional charges may apply for waiting time, route changes, or tolls as specified by the operator.</li>
                        <li>Refunds, where applicable, will be processed to the original payment method within 5-10 business days.</li>
                        <li>We reserve the right to hold payment until the journey is confirmed as completed by the operator.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">6. Liability Limitations</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>As a marketplace platform:</p>
                    <ul>
                        <li>We are not liable for the acts, omissions, or negligence of transport operators or their drivers.</li>
                        <li>We do not guarantee the availability of operators or specific vehicle types.</li>
                        <li>We are not responsible for delays caused by traffic, weather, or other circumstances beyond our control.</li>
                        <li>Our total liability to you for any claim arising from the use of the Platform shall not exceed the amount you paid for the relevant booking.</li>
                        <li>Nothing in these Terms excludes or limits our liability for death or personal injury caused by our negligence, fraud or fraudulent misrepresentation, or any other liability that cannot be excluded by law.</li>
                    </ul>
                    <p>We make reasonable efforts to verify that all operators on our Platform are licensed and insured, but we cannot guarantee the quality of service provided by individual operators.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">7. Dispute Resolution</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>If you have a complaint about a journey booked through our Platform:</p>
                    <ol>
                        <li><strong>Contact us first:</strong> Please raise your complaint with our support team within 48 hours of the journey. We will investigate and attempt to resolve the issue.</li>
                        <li><strong>Mediation:</strong> If we cannot resolve the dispute directly, we may suggest mediation through an independent third party.</li>
                        <li><strong>Operator disputes:</strong> For issues relating to the quality of transportation, we will facilitate communication between you and the operator.</li>
                        <li><strong>Refunds and compensation:</strong> Where a complaint is upheld, we may offer a partial or full refund, or credit towards a future booking.</li>
                    </ol>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">8. Intellectual Property</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>All content on the Platform, including text, graphics, logos, and software, is the property of {{ \App\Helpers\Settings::get('company_name', config('app.name')) }} or its licensors and is protected by intellectual property laws. You may not reproduce, distribute, or create derivative works without our prior written consent.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">9. Account Suspension and Termination</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We reserve the right to suspend or terminate your account if:</p>
                    <ul>
                        <li>You breach these Terms.</li>
                        <li>We suspect fraudulent or abusive activity.</li>
                        <li>You fail to pay for bookings.</li>
                        <li>Your behaviour poses a risk to operators, drivers, or other users.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">10. Governing Law</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>These Terms are governed by and construed in accordance with the laws of <strong>England and Wales</strong>. Any disputes arising from these Terms or your use of the Platform shall be subject to the exclusive jurisdiction of the courts of England and Wales.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">11. Changes to These Terms</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We may update these Terms from time to time. If we make material changes, we will notify you by email or through the Platform. Your continued use of the Platform after changes are posted constitutes acceptance of the updated Terms.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">12. Contact Us</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>If you have any questions about these Terms, please contact us:</p>
                    <ul>
                        <li><strong>Email:</strong> {{ \App\Helpers\Settings::get('contact_email', 'support@rushxo.com') }}</li>
                        <li><strong>Post:</strong> {{ \App\Helpers\Settings::get('company_legal_name', \App\Helpers\Settings::get('company_name', config('app.name'))) }}, {{ \App\Helpers\Settings::get('contact_address', '[Registered Address]') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
