@extends('layouts.app')

@section('title', 'Cookie Policy')
@section('meta_description', 'Learn about how we use cookies on our taxi comparison platform and how you can manage your preferences.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Cookie Policy</h1>
            <p class="text-muted mb-4">Last updated: {{ date('j F Y') }}</p>

            <div class="card mb-4">
                <div class="card-body">
                    <p>This Cookie Policy explains what cookies are, how {{ config('app.name') }} uses cookies, and how you can control your cookie preferences.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">1. What Are Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>Cookies are small text files that are placed on your device (computer, tablet, or mobile phone) when you visit a website. They are widely used to make websites work more efficiently and to provide information to the website owners.</p>
                    <p>Cookies can be "persistent" (remaining on your device until deleted or until they expire) or "session" cookies (deleted when you close your browser).</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">2. Essential Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>These cookies are strictly necessary for the Platform to function. They cannot be switched off as the Platform would not work properly without them.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Cookie</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>XSRF-TOKEN</code></td>
                                    <td>Prevents cross-site request forgery attacks (CSRF protection)</td>
                                    <td>Session</td>
                                </tr>
                                <tr>
                                    <td><code>{{ strtolower(str_replace(' ', '_', config('app.name'))) }}_session</code></td>
                                    <td>Maintains your session state across page requests</td>
                                    <td>2 hours</td>
                                </tr>
                                <tr>
                                    <td><code>cookie_consent</code></td>
                                    <td>Stores your cookie consent preference</td>
                                    <td>1 year</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">3. Analytics Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>These cookies help us understand how visitors interact with the Platform by collecting and reporting information anonymously. This helps us improve our Platform.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Cookie</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>_ga</code></td>
                                    <td>Google Analytics - distinguishes unique users</td>
                                    <td>2 years</td>
                                </tr>
                                <tr>
                                    <td><code>_ga_*</code></td>
                                    <td>Google Analytics - maintains session state</td>
                                    <td>2 years</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2">Analytics cookies are only set if you have given your consent via the cookie consent banner.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">4. How to Manage Cookies</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>You can control and manage cookies in several ways:</p>

                    <h5>Cookie Consent Banner</h5>
                    <p>When you first visit our Platform, you will see a cookie consent banner. You can choose to accept all cookies or only essential cookies. You can change your preference at any time by clearing your browser cookies and revisiting the Platform.</p>

                    <h5>Browser Settings</h5>
                    <p>Most browsers allow you to manage cookies through their settings. You can typically:</p>
                    <ul>
                        <li>View what cookies are stored on your device</li>
                        <li>Delete individual or all cookies</li>
                        <li>Block cookies from specific or all websites</li>
                        <li>Set your browser to notify you when a cookie is set</li>
                    </ul>
                    <p>Please note that blocking essential cookies may affect the functionality of the Platform.</p>

                    <h5>Browser-Specific Instructions</h5>
                    <ul>
                        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
                        <li><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
                        <li><a href="https://support.apple.com/en-gb/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Safari</a></li>
                        <li><a href="https://support.microsoft.com/en-us/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
                    </ul>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">5. Changes to This Policy</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated revision date.</p>
                </div>
            </div>

            <h2 class="h4 mt-4 mb-3">6. Contact Us</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p>If you have any questions about our use of cookies, please contact us:</p>
                    <ul>
                        <li><strong>Email:</strong> privacy@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk</li>
                    </ul>
                    <p>For more information about your privacy rights, please see our <a href="{{ route('privacy-policy') }}">Privacy Policy</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
