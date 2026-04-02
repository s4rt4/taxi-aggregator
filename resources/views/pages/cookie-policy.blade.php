@extends('layouts.app')

@section('title', 'Cookie Policy')
@section('meta_description', 'Learn about how we use cookies on our taxi comparison platform and how you can manage your preferences.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">Cookie Policy</h1>
            <p class="text-muted mb-4">Last updated: {{ date('j F Y') }}</p>

            <div class="alert alert-light border mb-4">
                <p class="mb-0">This Cookie Policy explains what cookies are, how <strong>{{ config('app.name') }}</strong> ("we", "us", "our") uses cookies on our taxi comparison and booking platform ("Platform"), and how you can control your cookie preferences. This policy should be read alongside our <a href="{{ route('privacy-policy') }}">Privacy Policy</a>.</p>
            </div>

            {{-- 1. What Are Cookies --}}
            <h2 class="h4 mt-4 mb-3">1. What Are Cookies?</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>Cookies are small text files that are placed on your device (computer, tablet, or mobile phone) when you visit a website. They are widely used to make websites work more efficiently, remember your preferences, and provide information to the website owners.</p>
                    <p>Cookies can be:</p>
                    <ul>
                        <li><strong>Session cookies</strong> - temporary cookies that are deleted when you close your browser</li>
                        <li><strong>Persistent cookies</strong> - remain on your device until they expire or you delete them</li>
                        <li><strong>First-party cookies</strong> - set by the website you are visiting</li>
                        <li><strong>Third-party cookies</strong> - set by a domain other than the one you are visiting (e.g. Google, Stripe)</li>
                    </ul>
                </div>
            </div>

            {{-- 2. Essential Cookies --}}
            <h2 class="h4 mt-4 mb-3">2. Strictly Necessary Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>These cookies are essential for the Platform to function properly. Without them, you would not be able to log in, search for quotes, or make bookings. <strong>These cookies cannot be disabled.</strong></p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:25%">Cookie Name</th>
                                    <th>Purpose</th>
                                    <th style="width:15%">Duration</th>
                                    <th style="width:12%">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>XSRF-TOKEN</code></td>
                                    <td>Protects against cross-site request forgery (CSRF) attacks. Essential for secure form submissions including booking and payment forms.</td>
                                    <td>Session</td>
                                    <td>First-party</td>
                                </tr>
                                <tr>
                                    <td><code>{{ strtolower(str_replace(' ', '_', config('app.name'))) }}_session</code></td>
                                    <td>Maintains your logged-in session and remembers your state as you navigate between pages (e.g. search results, booking progress).</td>
                                    <td>2 hours</td>
                                    <td>First-party</td>
                                </tr>
                                <tr>
                                    <td><code>remember_web_*</code></td>
                                    <td>Keeps you logged in between browser sessions if you selected "Remember me" during login.</td>
                                    <td>30 days</td>
                                    <td>First-party</td>
                                </tr>
                                <tr>
                                    <td><code>cookie_consent</code></td>
                                    <td>Stores your cookie consent preference so the cookie banner is not shown again on every visit.</td>
                                    <td>1 year</td>
                                    <td>First-party</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 3. Functional Cookies --}}
            <h2 class="h4 mt-4 mb-3">3. Functional Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>These cookies enable enhanced functionality and personalisation on our Platform, such as remembering your recent searches and preferred pickup locations.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:25%">Cookie Name</th>
                                    <th>Purpose</th>
                                    <th style="width:15%">Duration</th>
                                    <th style="width:12%">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>recent_searches</code></td>
                                    <td>Remembers your recent pickup and destination addresses so you can quickly rebook popular routes.</td>
                                    <td>30 days</td>
                                    <td>First-party</td>
                                </tr>
                                <tr>
                                    <td><code>preferred_fleet</code></td>
                                    <td>Stores your preferred vehicle type (e.g. saloon, estate, MPV) to pre-select on the search form.</td>
                                    <td>30 days</td>
                                    <td>First-party</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 4. Third-Party Service Cookies --}}
            <h2 class="h4 mt-4 mb-3">4. Third-Party Service Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We use trusted third-party services to provide key features of our Platform. These services may set their own cookies.</p>

                    <h5 class="mt-3"><i class="bi bi-geo-alt text-success me-1"></i> Google Maps</h5>
                    <p>We use Google Maps for address autocomplete and route distance calculation. When you use our search form, Google may set cookies to improve the map experience and for fraud prevention.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:25%">Cookie Name</th>
                                    <th>Purpose</th>
                                    <th style="width:15%">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>NID</code></td>
                                    <td>Google - stores preferences and information for Google Maps</td>
                                    <td>6 months</td>
                                </tr>
                                <tr>
                                    <td><code>CONSENT</code></td>
                                    <td>Google - tracks consent status for Google services</td>
                                    <td>2 years</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted">For more information, see <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google's Privacy Policy</a>.</p>

                    <h5 class="mt-4"><i class="bi bi-credit-card text-primary me-1"></i> Stripe (Payments)</h5>
                    <p>We use Stripe to process secure card payments. When you proceed to checkout, Stripe may set cookies for fraud detection and to process your payment securely.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:25%">Cookie Name</th>
                                    <th>Purpose</th>
                                    <th style="width:15%">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>__stripe_mid</code></td>
                                    <td>Stripe - fraud prevention and detection</td>
                                    <td>1 year</td>
                                </tr>
                                <tr>
                                    <td><code>__stripe_sid</code></td>
                                    <td>Stripe - fraud prevention session identifier</td>
                                    <td>Session</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted">For more information, see <a href="https://stripe.com/privacy" target="_blank" rel="noopener">Stripe's Privacy Policy</a>.</p>
                </div>
            </div>

            {{-- 5. Analytics Cookies --}}
            <h2 class="h4 mt-4 mb-3">5. Analytics Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>These cookies help us understand how visitors use our Platform by collecting anonymous statistical data. This helps us improve the booking experience for all users. <strong>These cookies are only set if you consent via the cookie banner.</strong></p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:25%">Cookie Name</th>
                                    <th>Purpose</th>
                                    <th style="width:15%">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>_ga</code></td>
                                    <td>Google Analytics - distinguishes unique visitors to measure site traffic</td>
                                    <td>2 years</td>
                                </tr>
                                <tr>
                                    <td><code>_ga_*</code></td>
                                    <td>Google Analytics - maintains session state and tracks page views</td>
                                    <td>2 years</td>
                                </tr>
                                <tr>
                                    <td><code>_gid</code></td>
                                    <td>Google Analytics - identifies unique visitors within a 24-hour period</td>
                                    <td>24 hours</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted mt-2">We use Google Analytics to understand which routes are most popular, how passengers find our Platform, and where we can improve the booking experience. Data is anonymised and not used to personally identify you.</p>
                </div>
            </div>

            {{-- 6. Managing Cookies --}}
            <h2 class="h4 mt-4 mb-3">6. How to Manage Your Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Cookie Consent Banner</h5>
                    <p>When you first visit our Platform, a cookie consent banner appears at the bottom of the page. You can choose:</p>
                    <ul>
                        <li><strong>Accept All</strong> - enables all cookies including analytics</li>
                        <li><strong>Essential Only</strong> - only allows cookies required for the Platform to function</li>
                    </ul>
                    <p>To change your preference later, clear your browser cookies and the banner will appear again on your next visit.</p>

                    <h5 class="mt-3">Browser Settings</h5>
                    <p>You can also manage cookies through your browser settings. Most browsers allow you to:</p>
                    <ul>
                        <li>View and delete cookies stored on your device</li>
                        <li>Block all or specific cookies</li>
                        <li>Set your browser to notify you when a cookie is being set</li>
                    </ul>
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Please note:</strong> Blocking strictly necessary cookies may prevent you from logging in, searching for quotes, or completing a booking.
                    </div>

                    <h5 class="mt-3">Browser-Specific Instructions</h5>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-browser-chrome me-1"></i> Chrome
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-browser-firefox me-1"></i> Firefox
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="https://support.apple.com/en-gb/guide/safari/sfri11471/mac" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-browser-safari me-1"></i> Safari
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="https://support.microsoft.com/en-us/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-browser-edge me-1"></i> Edge
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. Changes --}}
            <h2 class="h4 mt-4 mb-3">7. Changes to This Policy</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We may update this Cookie Policy from time to time to reflect changes in technology, regulation, or our business practices. Any changes will be posted on this page with an updated revision date. We encourage you to review this page periodically.</p>
                </div>
            </div>

            {{-- 8. Contact --}}
            <h2 class="h4 mt-4 mb-3">8. Contact Us</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>If you have any questions about our use of cookies or this Cookie Policy, please contact us:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i><strong>Email:</strong> <a href="mailto:privacy@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk">privacy@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk</a></li>
                        <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i><strong>Phone:</strong> 0800 123 4567</li>
                    </ul>
                    <p class="mb-0">For more information about how we handle your personal data, please see our <a href="{{ route('privacy-policy') }}">Privacy Policy</a>. For the terms governing your use of our Platform, see our <a href="{{ route('terms-of-service') }}">Terms of Service</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
