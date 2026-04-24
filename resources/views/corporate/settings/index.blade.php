@extends('layouts.corporate')
@section('title', 'Company Settings')

@section('content')
<div class="container-fluid" style="max-width: 960px;">
    <h3 class="fw-bold mb-4">Company Settings</h3>

    <form method="POST" action="{{ route('corporate.settings.update') }}">
        @csrf @method('PUT')

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Company Information</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $corporate->company_name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Legal Name</label>
                        <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name', $corporate->legal_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Business Type</label>
                        <select name="business_type" class="form-select">
                            @foreach(['limited_company' => 'Limited Company', 'sole_trader' => 'Sole Trader', 'partnership' => 'Partnership', 'llp' => 'LLP', 'public_sector' => 'Public Sector', 'charity' => 'Charity'] as $k => $v)
                                <option value="{{ $k }}" {{ $corporate->business_type === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Registration Number</label>
                        <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $corporate->registration_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">VAT Number</label>
                        <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number', $corporate->vat_number) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Billing Information</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Billing Email</label>
                        <input type="email" name="billing_email" class="form-control" value="{{ old('billing_email', $corporate->billing_email) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Billing Phone</label>
                        <input type="tel" name="billing_phone" class="form-control" value="{{ old('billing_phone', $corporate->billing_phone) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Address Line 1</label>
                        <input type="text" name="billing_address_line_1" class="form-control" value="{{ old('billing_address_line_1', $corporate->billing_address_line_1) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Address Line 2</label>
                        <input type="text" name="billing_address_line_2" class="form-control" value="{{ old('billing_address_line_2', $corporate->billing_address_line_2) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">City</label>
                        <input type="text" name="billing_city" class="form-control" value="{{ old('billing_city', $corporate->billing_city) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Postcode</label>
                        <input type="text" name="billing_postcode" class="form-control" value="{{ old('billing_postcode', $corporate->billing_postcode) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">County</label>
                        <input type="text" name="billing_county" class="form-control" value="{{ old('billing_county', $corporate->billing_county) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Budget &amp; Invoicing</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Monthly Budget</label>
                        <div class="input-group">
                            <span class="input-group-text">&pound;</span>
                            <input type="number" step="0.01" min="0" name="monthly_budget" class="form-control" value="{{ old('monthly_budget', $corporate->monthly_budget) }}" placeholder="No limit">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Invoicing Frequency</label>
                        <select name="invoicing_frequency" class="form-select">
                            <option value="monthly" {{ $corporate->invoicing_frequency === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ $corporate->invoicing_frequency === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Payment Terms (days)</label>
                        <input type="number" min="1" max="90" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', $corporate->payment_terms_days) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
