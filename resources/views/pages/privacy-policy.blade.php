@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('meta_description', 'Read our privacy policy to understand how we collect, use, and protect your personal data in compliance with UK GDPR.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Privacy Policy</h1>
            <p class="text-muted mb-4">Last updated: {{ date('j F Y') }}</p>

            <div class="card mb-4">
                <div class="card-body">
                    <p>{{ \App\Helpers\Settings::get('company_name', config('app.name')) }} ("we", "us", or "our") is committed to protecting your personal data. This privacy policy explains how we collect, use, store, and share your information when you use our taxi comparison and booking platform.</p>
                    <p>We are the data controller for your personal data. We comply with the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">1. What Data We Collect</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Personal Information</h5>
                    <ul>
                        <li>Full name</li>
                        <li>Email address</li>
                        <li>Phone number</li>
                        <li>Postal address</li>
                    </ul>

                    <h5>Booking Data</h5>
                    <ul>
                        <li>Pickup and drop-off locations</li>
                        <li>Journey dates and times</li>
                        <li>Number of passengers and luggage requirements</li>
                        <li>Special requirements or instructions</li>
                        <li>Booking history</li>
                    </ul>

                    <h5>Payment Data</h5>
                    <ul>
                        <li>Payment card details (processed securely via our payment processor; we do not store full card numbers)</li>
                        <li>Billing address</li>
                        <li>Transaction history</li>
                    </ul>

                    <h5>Technical Data</h5>
                    <ul>
                        <li>IP address</li>
                        <li>Browser type and version</li>
                        <li>Device information</li>
                        <li>Cookie data (see our <a href="{{ route('cookie-policy') }}">Cookie Policy</a>)</li>
                        <li>Pages visited and interactions with our platform</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">2. How We Use Your Data</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We use your personal data for the following purposes:</p>
                    <ul>
                        <li><strong>To provide our service:</strong> Processing bookings, matching you with transport operators, and facilitating payments.</li>
                        <li><strong>To communicate with you:</strong> Sending booking confirmations, journey updates, and responding to your enquiries.</li>
                        <li><strong>To improve our platform:</strong> Analysing usage patterns to enhance our services, user experience, and search results.</li>
                        <li><strong>To ensure safety and security:</strong> Fraud prevention, identity verification, and platform security.</li>
                        <li><strong>To comply with legal obligations:</strong> Meeting regulatory requirements and responding to lawful requests from authorities.</li>
                        <li><strong>Marketing:</strong> With your consent, sending promotional offers and updates about our services. You can opt out at any time.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">3. Legal Basis for Processing</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We process your personal data on the following legal bases under UK GDPR:</p>
                    <ul>
                        <li><strong>Contract:</strong> Processing is necessary for the performance of a contract (e.g., fulfilling your booking).</li>
                        <li><strong>Legitimate interests:</strong> Processing is necessary for our legitimate business interests, such as improving our services and preventing fraud, where these do not override your rights.</li>
                        <li><strong>Consent:</strong> Where you have given clear consent for us to process your data for a specific purpose (e.g., marketing emails).</li>
                        <li><strong>Legal obligation:</strong> Processing is necessary to comply with a legal obligation.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">4. Data Sharing</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We may share your personal data with the following categories of recipients:</p>
                    <ul>
                        <li><strong>Transport operators:</strong> We share your booking details (name, phone number, pickup/drop-off locations, journey details) with the operator assigned to fulfil your booking.</li>
                        <li><strong>Payment processors:</strong> We use Stripe to process payments securely. Your payment data is handled in accordance with PCI DSS standards.</li>
                        <li><strong>Service providers:</strong> Third parties who assist us with hosting, analytics, email delivery, and customer support.</li>
                        <li><strong>Legal and regulatory:</strong> Where required by law, regulation, or legal process.</li>
                    </ul>
                    <p>We do not sell your personal data to third parties.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">5. Data Retention</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We retain your personal data only for as long as necessary to fulfil the purposes for which it was collected:</p>
                    <ul>
                        <li><strong>Account data:</strong> Retained for the duration of your account and up to 2 years after account closure.</li>
                        <li><strong>Booking data:</strong> Retained for 6 years after the journey date to comply with tax and legal requirements.</li>
                        <li><strong>Payment records:</strong> Retained for 6 years as required by financial regulations.</li>
                        <li><strong>Marketing preferences:</strong> Retained until you withdraw consent or unsubscribe.</li>
                        <li><strong>Technical logs:</strong> Retained for up to 12 months for security and analytical purposes.</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">6. Your Rights</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>Under UK GDPR, you have the following rights regarding your personal data:</p>
                    <ul>
                        <li><strong>Right of access:</strong> You can request a copy of the personal data we hold about you.</li>
                        <li><strong>Right to rectification:</strong> You can request correction of inaccurate or incomplete data.</li>
                        <li><strong>Right to erasure:</strong> You can request deletion of your personal data where there is no compelling reason for continued processing.</li>
                        <li><strong>Right to data portability:</strong> You can request a copy of your data in a structured, commonly used, machine-readable format.</li>
                        <li><strong>Right to object:</strong> You can object to processing based on legitimate interests or for direct marketing purposes.</li>
                        <li><strong>Right to restrict processing:</strong> You can request restriction of processing in certain circumstances.</li>
                        <li><strong>Right to withdraw consent:</strong> Where processing is based on consent, you may withdraw it at any time.</li>
                    </ul>
                    <p>To exercise any of these rights, please contact us using the details below. We will respond to your request within one month.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">7. Data Security</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We take appropriate technical and organisational measures to protect your personal data, including:</p>
                    <ul>
                        <li>Encryption of data in transit using TLS/SSL</li>
                        <li>Secure storage with access controls</li>
                        <li>Regular security assessments</li>
                        <li>Staff training on data protection</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">8. Contact Information</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>If you have any questions about this privacy policy or wish to exercise your data rights, please contact us:</p>
                    <ul>
                        <li><strong>Email:</strong> {{ \App\Helpers\Settings::get('contact_email', 'support@rushxo.com') }}</li>
                        <li><strong>Post:</strong> Data Protection Officer, {{ \App\Helpers\Settings::get('company_legal_name', \App\Helpers\Settings::get('company_name', config('app.name'))) }}, {{ \App\Helpers\Settings::get('contact_address', '[Registered Address]') }}</li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">9. Complaints</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>If you are unhappy with how we have handled your personal data, you have the right to lodge a complaint with the Information Commissioner's Office (ICO):</p>
                    <ul>
                        <li><strong>Website:</strong> <a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer">ico.org.uk</a></li>
                        <li><strong>Phone:</strong> 0303 123 1113</li>
                    </ul>
                    <p>We would appreciate the opportunity to address your concerns before you contact the ICO, so please reach out to us first.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">10. Changes to This Policy</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We may update this privacy policy from time to time. Any changes will be posted on this page with an updated revision date. We encourage you to review this policy periodically.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
