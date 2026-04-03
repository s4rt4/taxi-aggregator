@extends('layouts.operator')
@section('title', 'My Account')

@push('styles')
<style>
    .account-tabs .nav-link {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #495057;
        border: 1px solid #dee2e6;
        border-bottom: none;
        border-radius: 4px 4px 0 0;
        padding: 0.5rem 1rem;
        margin-right: 2px;
        background: #f8f9fa;
    }
    .account-tabs .nav-link.active {
        background: #fff;
        color: #212529;
        border-bottom: 1px solid #fff;
        margin-bottom: -1px;
        position: relative;
        z-index: 1;
    }
    .account-tabs .nav-link:hover:not(.active) {
        background: #e9ecef;
    }
    .tab-content-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0 6px 6px 6px;
        padding: 1.5rem;
    }
    .field-highlighted-yellow {
        background-color: #fff3cd !important;
        border-color: #ffe69c !important;
    }
    .field-highlighted-green {
        background-color: #d1e7dd !important;
        border-color: #badbcc !important;
    }
    .field-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    .field-note {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.15rem;
    }
    .section-divider {
        border-top: 2px solid #e9ecef;
        margin: 1.5rem 0;
    }
    .contact-table th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
    }
    .contact-table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }
    .btn-add-contact {
        font-size: 0.8125rem;
        color: #0d6efd;
        text-decoration: none;
        font-weight: 600;
    }
    .btn-add-contact:hover {
        text-decoration: underline;
    }
    .file-upload-display {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    .file-upload-display .file-name {
        font-size: 0.8125rem;
        color: #495057;
        flex-grow: 1;
    }
    .file-upload-display .btn-download {
        font-size: 0.75rem;
        font-weight: 600;
    }
    .file-upload-display .btn-change {
        font-size: 0.75rem;
        font-weight: 600;
    }
    .password-requirements {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0;
    }
    .password-requirements li {
        font-size: 0.8125rem;
        color: #6c757d;
        padding: 0.15rem 0;
    }
    .password-requirements li i {
        margin-right: 0.35rem;
        font-size: 0.75rem;
    }
    .btn-change-postcode {
        background: #fd7e14;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        border: none;
        border-radius: 4px;
        padding: 0.35rem 0.75rem;
        text-transform: uppercase;
    }
    .btn-change-postcode:hover {
        background: #e8590c;
        color: #fff;
    }
    .phone-input-group {
        display: flex;
        gap: 0.5rem;
    }
    .phone-input-group select {
        width: 90px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>My Account</h1>
</div>

<div x-data="{ activeTab: 'company' }">
    {{-- Tab navigation --}}
    <ul class="nav account-tabs mb-0">
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'company' }" href="#" @click.prevent="activeTab = 'company'">Company details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'contact' }" href="#" @click.prevent="activeTab = 'contact'">Contact details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'authorised' }" href="#" @click.prevent="activeTab = 'authorised'">Authorised contact</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'licence' }" href="#" @click.prevent="activeTab = 'licence'">Licence &amp; Fleet</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'payment' }" href="#" @click.prevent="activeTab = 'payment'">Payment type</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'icabbi' }" href="#" @click.prevent="activeTab = 'icabbi'">iCabbi Integration</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{ 'active': activeTab === 'password' }" href="#" @click.prevent="activeTab = 'password'">Password</a>
        </li>
    </ul>

    <div class="tab-content-card">

        {{-- ============================================ --}}
        {{-- TAB: Company Details --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'company'" x-transition
             x-data="{ businessType: '{{ old('business_type', $operator->business_type ?? 'sole_trader') }}' }">
            <form method="POST" action="#">
                @csrf

                <div class="mb-3">
                    <label class="field-label">Account ID / Username</label>
                    <input type="text" class="form-control form-control-sm field-highlighted-yellow" value="OAPP100000008PDPE3" readonly>
                    <div class="field-note">This is your unique operator account identifier. It cannot be changed.</div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Business Type <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <div class="form-check border rounded p-2">
                                <input class="form-check-input" type="radio" name="business_type" value="sole_trader" id="acc_bt_sole"
                                       x-model="businessType"
                                       {{ old('business_type', $operator->business_type ?? 'sole_trader') === 'sole_trader' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="acc_bt_sole">
                                    <strong>Sole Trader</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-check border rounded p-2">
                                <input class="form-check-input" type="radio" name="business_type" value="limited_company" id="acc_bt_ltd"
                                       x-model="businessType"
                                       {{ old('business_type', $operator->business_type ?? '') === 'limited_company' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="acc_bt_ltd">
                                    <strong>Limited Company</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-check border rounded p-2">
                                <input class="form-check-input" type="radio" name="business_type" value="partnership" id="acc_bt_part"
                                       x-model="businessType"
                                       {{ old('business_type', $operator->business_type ?? '') === 'partnership' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="acc_bt_part">
                                    <strong>Partnership</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-check border rounded p-2">
                                <input class="form-check-input" type="radio" name="business_type" value="llp" id="acc_bt_llp"
                                       x-model="businessType"
                                       {{ old('business_type', $operator->business_type ?? '') === 'llp' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="acc_bt_llp">
                                    <strong>LLP</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label" for="cab_operator_name">Cab operator name</label>
                    <input type="text" class="form-control form-control-sm field-highlighted-yellow" id="cab_operator_name" name="cab_operator_name"
                           value="{{ old('cab_operator_name', $operator->operator_name ?? '') }}" placeholder="Enter your operator display name">
                    <div class="field-note">The name of your Operator that you want to be displayed to customers.</div>
                </div>

                <div class="mb-3">
                    <label class="field-label" for="legal_company_name">
                        <span x-show="businessType === 'sole_trader'">Full Legal Name (as on PHO licence)</span>
                        <span x-show="businessType === 'partnership'">Partnership Name</span>
                        <span x-show="businessType === 'limited_company' || businessType === 'llp'">Legal Company Name</span>
                    </label>
                    <input type="text" class="form-control form-control-sm" id="legal_company_name" name="legal_company_name"
                           value="{{ old('legal_company_name', $operator->legal_company_name ?? '') }}"
                           :placeholder="businessType === 'sole_trader' ? 'e.g. John Smith' : (businessType === 'partnership' ? 'e.g. Smith & Jones' : 'e.g. ABC Ltd')">
                    <div class="field-note" x-show="businessType !== 'sole_trader'">If different from your operator display name above.</div>
                </div>

                <div class="mb-3" x-show="businessType === 'limited_company' || businessType === 'llp'" x-transition>
                    <label class="field-label" for="registration_number">Companies House Number</label>
                    <input type="text" class="form-control form-control-sm" id="registration_number" name="registration_number"
                           value="{{ old('registration_number', $operator->registration_number ?? '') }}"
                           placeholder="e.g. 12345678">
                    <div class="field-note">Your company registration number at Companies House.</div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-mc-save">
                        <i class="bi bi-check-lg me-1"></i> SAVE CHANGES
                    </button>
                    <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
                        CANCEL AND DISCARD CHANGES
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================ --}}
        {{-- TAB: Contact Details --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'contact'" x-transition>
            <form method="POST" action="#">
                @csrf

                <div class="mb-3">
                    <label class="field-label" for="office_email">Office email address</label>
                    <input type="email" class="form-control form-control-sm" id="office_email" name="office_email" value="" placeholder="office@example.com">
                </div>

                <div class="mb-3">
                    <label class="field-label">Postcode</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control form-control-sm" name="postcode" value="" placeholder="e.g., SW1A 1AA" style="max-width: 180px;">
                        <button type="button" class="btn btn-change-postcode">Change postcode</button>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="field-label" for="address_line1">Address (line 1)</label>
                        <input type="text" class="form-control form-control-sm" id="address_line1" name="address_line1" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="field-label" for="address_line2">Address (line 2)</label>
                        <input type="text" class="form-control form-control-sm" id="address_line2" name="address_line2" value="">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="field-label" for="city">City / Town</label>
                        <input type="text" class="form-control form-control-sm field-highlighted-green" id="city" name="city" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="field-label" for="county">County</label>
                        <select class="form-select form-select-sm" id="county" name="county">
                            <option value="">Select county</option>
                            <option value="bedfordshire">Bedfordshire</option>
                            <option value="berkshire">Berkshire</option>
                            <option value="buckinghamshire">Buckinghamshire</option>
                            <option value="cambridgeshire">Cambridgeshire</option>
                            <option value="cheshire">Cheshire</option>
                            <option value="cornwall">Cornwall</option>
                            <option value="cumbria">Cumbria</option>
                            <option value="derbyshire">Derbyshire</option>
                            <option value="devon">Devon</option>
                            <option value="dorset">Dorset</option>
                            <option value="durham">Durham</option>
                            <option value="essex">Essex</option>
                            <option value="gloucestershire">Gloucestershire</option>
                            <option value="greater_london">Greater London</option>
                            <option value="greater_manchester">Greater Manchester</option>
                            <option value="hampshire">Hampshire</option>
                            <option value="hertfordshire">Hertfordshire</option>
                            <option value="kent">Kent</option>
                            <option value="lancashire">Lancashire</option>
                            <option value="leicestershire">Leicestershire</option>
                            <option value="lincolnshire">Lincolnshire</option>
                            <option value="merseyside">Merseyside</option>
                            <option value="middlesex">Middlesex</option>
                            <option value="norfolk">Norfolk</option>
                            <option value="northamptonshire">Northamptonshire</option>
                            <option value="northumberland">Northumberland</option>
                            <option value="nottinghamshire">Nottinghamshire</option>
                            <option value="oxfordshire">Oxfordshire</option>
                            <option value="shropshire">Shropshire</option>
                            <option value="somerset">Somerset</option>
                            <option value="staffordshire">Staffordshire</option>
                            <option value="suffolk">Suffolk</option>
                            <option value="surrey">Surrey</option>
                            <option value="sussex_east">East Sussex</option>
                            <option value="sussex_west">West Sussex</option>
                            <option value="tyne_and_wear">Tyne and Wear</option>
                            <option value="warwickshire">Warwickshire</option>
                            <option value="west_midlands">West Midlands</option>
                            <option value="wiltshire">Wiltshire</option>
                            <option value="worcestershire">Worcestershire</option>
                            <option value="yorkshire_north">North Yorkshire</option>
                            <option value="yorkshire_south">South Yorkshire</option>
                            <option value="yorkshire_west">West Yorkshire</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Office phone number</label>
                    <div class="phone-input-group">
                        <select class="form-select form-select-sm" name="phone_country_code">
                            <option value="+44" selected>+44</option>
                            <option value="+1">+1</option>
                            <option value="+353">+353</option>
                            <option value="+33">+33</option>
                            <option value="+49">+49</option>
                        </select>
                        <input type="tel" class="form-control form-control-sm" name="office_phone" value="" placeholder="e.g., 020 1234 5678">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label" for="website_url">Website URL</label>
                    <input type="url" class="form-control form-control-sm" id="website_url" name="website_url" value="" placeholder="https://www.example.com">
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-mc-save">
                        <i class="bi bi-check-lg me-1"></i> SAVE CHANGES
                    </button>
                    <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
                        CANCEL AND DISCARD CHANGES
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================ --}}
        {{-- TAB: Authorised Contact --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'authorised'" x-transition x-data="authorisedContacts()">
            <h6 class="fw-bold mb-3">Primary Contact</h6>
            <form method="POST" action="#">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="field-label" for="primary_name">Name</label>
                        <input type="text" class="form-control form-control-sm" id="primary_name" name="primary_name" value="">
                    </div>
                    <div class="col-md-4">
                        <label class="field-label" for="primary_email">Email</label>
                        <input type="email" class="form-control form-control-sm" id="primary_email" name="primary_email" value="">
                    </div>
                    <div class="col-md-4">
                        <label class="field-label" for="primary_phone">Phone</label>
                        <input type="tel" class="form-control form-control-sm" id="primary_phone" name="primary_phone" value="">
                    </div>
                </div>

                <div class="section-divider"></div>

                <h6 class="fw-bold mb-3">Secondary Contacts</h6>

                <table class="table contact-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email address</th>
                            <th>Phone number</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="contacts.length === 0">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    No secondary contacts added yet.
                                </td>
                            </tr>
                        </template>
                        <template x-for="(contact, index) in contacts" :key="index">
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm" :name="'contacts[' + index + '][name]'" x-model="contact.name" placeholder="Full name">
                                </td>
                                <td>
                                    <input type="email" class="form-control form-control-sm" :name="'contacts[' + index + '][email]'" x-model="contact.email" placeholder="Email address">
                                </td>
                                <td>
                                    <input type="tel" class="form-control form-control-sm" :name="'contacts[' + index + '][phone]'" x-model="contact.phone" placeholder="Phone number">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeContact(index)" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <a href="#" class="btn-add-contact" @click.prevent="addContact()">
                    <i class="bi bi-plus-circle me-1"></i> Add new
                </a>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-mc-save">
                        <i class="bi bi-check-lg me-1"></i> SAVE CHANGES
                    </button>
                    <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
                        CANCEL AND DISCARD CHANGES
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================ --}}
        {{-- TAB: Licence & Fleet --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'licence'" x-transition>
            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="field-label" for="dispatch_system">Dispatch system</label>
                    <select class="form-select form-select-sm" id="dispatch_system" name="dispatch_system" style="max-width: 300px;">
                        <option value="">Select dispatch system</option>
                        <option value="icabbi" selected>iCabbi</option>
                        <option value="autocab">Autocab</option>
                        <option value="cordic">Cordic</option>
                        <option value="cab_treasure">Cab Treasure</option>
                        <option value="other">Other</option>
                        <option value="none">None</option>
                    </select>
                </div>

                <div class="section-divider"></div>

                <h6 class="fw-bold mb-3">Permissions</h6>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="dead_leg_approved" name="dead_leg_approved" value="1">
                        <label class="form-check-label" for="dead_leg_approved">Dead Leg Discount approved</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="airports_approved" name="airports_approved" value="1">
                        <label class="form-check-label" for="airports_approved">Airports</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Approved for:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border">Standard</span>
                    </div>
                </div>

                <div class="section-divider"></div>

                <h6 class="fw-bold mb-3">Licensing</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="field-label" for="licence_number">Private Hire Operator Licence Number</label>
                        <input type="text" class="form-control form-control-sm" id="licence_number" name="licence_number" value="" placeholder="e.g., PH0001234">
                    </div>
                    <div class="col-md-6">
                        <label class="field-label" for="licence_expiry">Licence expiry date</label>
                        <input type="date" class="form-control form-control-sm" id="licence_expiry" name="licence_expiry" value="">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="field-label" for="licensing_authority">Licensing Local Authority</label>
                        <select class="form-select form-select-sm" id="licensing_authority" name="licensing_authority">
                            <option value="">Select authority</option>
                            <option value="tfl">Transport for London</option>
                            <option value="manchester">Manchester City Council</option>
                            <option value="birmingham">Birmingham City Council</option>
                            <option value="leeds">Leeds City Council</option>
                            <option value="liverpool">Liverpool City Council</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="field-label" for="tfl_licence">Transport for London (Public Carriage Office)</label>
                        <select class="form-select form-select-sm" id="tfl_licence" name="tfl_licence">
                            <option value="">Select</option>
                            <option value="pco_licensed">PCO Licensed</option>
                            <option value="not_applicable">Not Applicable</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label" for="fleet_size">Fleet size</label>
                    <input type="number" class="form-control form-control-sm" id="fleet_size" name="fleet_size" value="" placeholder="Total number of vehicles" style="max-width: 200px;" min="1">
                </div>

                <div class="section-divider"></div>

                <h6 class="fw-bold mb-3">Documents</h6>

                <div class="mb-3">
                    <label class="field-label">Operator Licence</label>
                    <div class="file-upload-display">
                        <i class="bi bi-file-earmark-pdf text-danger"></i>
                        <span class="file-name">operator_licence.pdf</span>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-download">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                        <label class="btn btn-sm btn-outline-secondary btn-change mb-0" style="cursor: pointer;">
                            <i class="bi bi-arrow-repeat me-1"></i> Change
                            <input type="file" name="operator_licence" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                        </label>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="field-label" for="liability_expiry">Public liability insurance expiry date</label>
                        <input type="date" class="form-control form-control-sm" id="liability_expiry" name="liability_expiry" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="field-label">Public liability insurance document</label>
                        <div class="file-upload-display">
                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                            <span class="file-name">liability_insurance.pdf</span>
                            <a href="#" class="btn btn-sm btn-outline-primary btn-download">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                            <label class="btn btn-sm btn-outline-secondary btn-change mb-0" style="cursor: pointer;">
                                <i class="bi bi-arrow-repeat me-1"></i> Change
                                <input type="file" name="liability_insurance" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-mc-save">
                        <i class="bi bi-check-lg me-1"></i> SAVE CHANGES
                    </button>
                    <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
                        CANCEL AND DISCARD CHANGES
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================ --}}
        {{-- TAB: Payment Type --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'payment'" x-transition>
            <h6 class="fw-bold mb-3">Your accepted payment methods:</h6>

            <div class="mb-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="prepaid_bookings" name="prepaid_bookings" value="1" checked disabled>
                    <label class="form-check-label fw-semibold" for="prepaid_bookings">
                        Pre-paid bookings
                    </label>
                    <div class="field-note ms-4">Customers pay online at the time of booking. Payment is settled to your account after trip completion.</div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="cash_bookings" name="cash_bookings" value="1" disabled>
                    <label class="form-check-label fw-semibold" for="cash_bookings">
                        Cash bookings
                    </label>
                    <div class="field-note ms-4">Customers pay the driver directly in cash at the end of the trip.</div>
                </div>
            </div>

            <div class="mt-4 p-3 rounded" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                <p class="mb-0 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    To update your accepted payment methods, please contact the admin team at
                    <a href="mailto:support@taxiaggregator.co.uk">support@taxiaggregator.co.uk</a>.
                    Changes to payment methods require verification and approval.
                </p>
            </div>

            {{-- Stripe Connect Section --}}
            <div class="section-divider"></div>
            <h6 class="fw-bold mb-3">Bank Account / Stripe Connect</h6>
            <p class="text-muted small mb-3">
                Connect your bank account via Stripe to receive weekly payouts for completed bookings.
            </p>

            @if($operator && $operator->stripe_status === 'active')
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-success" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                        <i class="bi bi-check-circle-fill me-1"></i> Stripe Connected
                    </span>
                    <a href="{{ route('operator.stripe.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View Stripe Dashboard
                    </a>
                </div>
                <div class="p-3 rounded" style="background: #d1e7dd; border: 1px solid #badbcc;">
                    <p class="mb-0 small text-success fw-semibold">
                        <i class="bi bi-check2-all me-1"></i>
                        Your Stripe account is active. Payouts are processed automatically every Monday for the previous week's completed bookings.
                    </p>
                </div>
            @elseif($operator && $operator->stripe_status === 'pending')
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-warning text-dark" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                        <i class="bi bi-hourglass-split me-1"></i> Setup In Progress
                    </span>
                    <a href="{{ route('operator.stripe.setup') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-arrow-repeat me-1"></i> Complete Setup
                    </a>
                </div>
                <div class="p-3 rounded" style="background: #fff3cd; border: 1px solid #ffe69c;">
                    <p class="mb-0 small text-dark">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Your Stripe account setup is in progress. Please complete all verification steps to start receiving payouts.
                    </p>
                </div>
            @elseif($operator && $operator->stripe_status === 'restricted')
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-danger" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Action Required
                    </span>
                    <a href="{{ route('operator.stripe.setup') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-arrow-repeat me-1"></i> Complete Requirements
                    </a>
                </div>
                <div class="p-3 rounded" style="background: #f8d7da; border: 1px solid #f5c2c7;">
                    <p class="mb-0 small text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Your Stripe account has outstanding requirements. Please complete them to continue receiving payouts.
                    </p>
                </div>
            @else
                <a href="{{ route('operator.stripe.setup') }}" class="btn btn-primary">
                    <i class="bi bi-stripe me-1"></i> Connect with Stripe
                </a>
                <div class="mt-3 p-3 rounded" style="background: #e2e3e5; border: 1px solid #d3d6d8;">
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Stripe provides secure payment processing. You will be redirected to Stripe to verify your identity and bank details.
                        This is required to receive weekly payouts for completed bookings.
                    </p>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- TAB: iCabbi Integration --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'icabbi'" x-transition x-data="icabbiSettings()">
            <div class="d-flex align-items-center gap-2 mb-3">
                <h6 class="fw-bold mb-0">iCabbi Dispatch Integration</h6>
                @if($operator && $operator->icabbi_enabled)
                    <span class="badge bg-success">Enabled</span>
                @else
                    <span class="badge bg-secondary">Disabled</span>
                @endif
            </div>
            <p class="text-muted small mb-3">
                Connect your iCabbi dispatch system to automatically send bookings to your fleet.
                Bookings accepted on this platform will be dispatched directly to your iCabbi system.
            </p>

            <form method="POST" action="{{ route('operator.account.update-icabbi') }}">
                @csrf

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="icabbi_enabled" name="icabbi_enabled" value="1"
                            {{ ($operator && $operator->icabbi_enabled) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="icabbi_enabled">Enable iCabbi Dispatch</label>
                    </div>
                    <div class="field-note">When enabled, bookings will be automatically dispatched to your iCabbi system.</div>
                </div>

                <div class="section-divider"></div>

                <h6 class="fw-bold mb-3">API Configuration</h6>

                <div class="mb-3" style="max-width: 500px;">
                    <label class="field-label" for="icabbi_api_url">API URL</label>
                    <input type="url" class="form-control form-control-sm" id="icabbi_api_url" name="icabbi_api_url"
                        value="{{ $operator->icabbi_api_url ?? 'https://api.icabbidispatch.com/icd/' }}"
                        placeholder="https://api.icabbidispatch.com/icd/">
                    <div class="field-note">The base URL for your iCabbi API instance. Leave as default unless instructed otherwise.</div>
                </div>

                <div class="mb-3" style="max-width: 500px;">
                    <label class="field-label" for="icabbi_app_key">App Key</label>
                    <input type="password" class="form-control form-control-sm" id="icabbi_app_key" name="icabbi_app_key"
                        value="{{ $operator->icabbi_app_key ?? '' }}"
                        placeholder="Enter your iCabbi App Key">
                    <div class="field-note">Your iCabbi application key. Contact iCabbi support if you do not have one.</div>
                </div>

                <div class="mb-3" style="max-width: 500px;">
                    <label class="field-label" for="icabbi_secret_key">Secret Key</label>
                    <input type="password" class="form-control form-control-sm" id="icabbi_secret_key" name="icabbi_secret_key"
                        value="{{ $operator->icabbi_secret_key ?? '' }}"
                        placeholder="Enter your iCabbi Secret Key">
                    <div class="field-note">Your iCabbi secret key. Keep this confidential.</div>
                </div>

                <div class="mb-3" style="max-width: 500px;">
                    <label class="field-label" for="icabbi_integration_name">Integration Name</label>
                    <input type="text" class="form-control form-control-sm" id="icabbi_integration_name" name="icabbi_integration_name"
                        value="{{ $operator->icabbi_integration_name ?? '' }}"
                        placeholder="e.g., My Taxi Company">
                    <div class="field-note">A friendly name for this integration (optional).</div>
                </div>

                <div class="section-divider"></div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-mc-save">
                        <i class="bi bi-check-lg me-1"></i> SAVE
                    </button>
                    <button type="button" class="btn btn-outline-info" @click="testConnection()" :disabled="testing">
                        <i class="bi bi-wifi me-1"></i>
                        <span x-text="testing ? 'Testing...' : 'Test Connection'"></span>
                    </button>
                    <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
                        CANCEL AND DISCARD CHANGES
                    </button>
                </div>
            </form>

            {{-- Test connection result --}}
            <div x-show="testResult !== null" class="mt-3">
                <div class="alert" :class="testResult?.success ? 'alert-success' : 'alert-danger'" role="alert">
                    <i class="bi" :class="testResult?.success ? 'bi-check-circle' : 'bi-x-circle'"></i>
                    <span x-text="testResult?.message"></span>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TAB: Password --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'password'" x-transition>
            <form method="POST" action="#">
                @csrf

                <div class="mb-3" style="max-width: 400px;">
                    <label class="field-label" for="current_password">Current password</label>
                    <input type="password" class="form-control form-control-sm" id="current_password" name="current_password" placeholder="Enter your current password">
                </div>

                <div class="mb-3" style="max-width: 400px;">
                    <label class="field-label" for="new_password">New password</label>
                    <input type="password" class="form-control form-control-sm" id="new_password" name="new_password" placeholder="Enter a new password">
                    <ul class="password-requirements mt-2">
                        <li><i class="bi bi-check-circle text-muted"></i> At least 8 characters</li>
                        <li><i class="bi bi-check-circle text-muted"></i> At least one number</li>
                        <li><i class="bi bi-check-circle text-muted"></i> At least one uppercase character</li>
                    </ul>
                </div>

                <div class="mb-3" style="max-width: 400px;">
                    <label class="field-label" for="new_password_confirmation">Confirm new password</label>
                    <input type="password" class="form-control form-control-sm" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm your new password">
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-mc-save">
                        <i class="bi bi-check-lg me-1"></i> SAVE PASSWORD
                    </button>
                    <button type="button" class="btn btn-mc-cancel" onclick="window.location.reload()">
                        CANCEL AND DISCARD CHANGES
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function authorisedContacts() {
        return {
            contacts: [],
            addContact() {
                this.contacts.push({
                    name: '',
                    email: '',
                    phone: ''
                });
            },
            removeContact(index) {
                this.contacts.splice(index, 1);
            }
        };
    }

    function icabbiSettings() {
        return {
            testing: false,
            testResult: null,
            async testConnection() {
                this.testing = true;
                this.testResult = null;
                try {
                    const response = await fetch('{{ route("operator.account.test-icabbi") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    });
                    this.testResult = await response.json();
                } catch (error) {
                    this.testResult = { success: false, message: 'Network error: ' + error.message };
                } finally {
                    this.testing = false;
                }
            }
        };
    }
</script>
@endpush
