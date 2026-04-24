@extends('corporate.onboarding.layout')

@section('onboarding-content')
<h6 class="fw-bold">Company Details</h6>
<p class="text-muted small mb-4">Tell us about your organisation.</p>

<form method="POST" action="{{ route('corporate.onboarding.save', 1) }}">
    @csrf

    <div class="mb-3">
        <label class="form-label small fw-semibold">Trading / Company Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('company_name') is-invalid @enderror"
               name="company_name" value="{{ old('company_name', $data['company_name'] ?? '') }}" required>
        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Legal Name (if different)</label>
        <input type="text" class="form-control" name="legal_name" value="{{ old('legal_name', $data['legal_name'] ?? '') }}">
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Business Type <span class="text-danger">*</span></label>
        <select name="business_type" class="form-select @error('business_type') is-invalid @enderror" required>
            @php $bt = old('business_type', $data['business_type'] ?? 'limited_company'); @endphp
            <option value="limited_company" {{ $bt === 'limited_company' ? 'selected' : '' }}>Limited Company</option>
            <option value="sole_trader" {{ $bt === 'sole_trader' ? 'selected' : '' }}>Sole Trader</option>
            <option value="partnership" {{ $bt === 'partnership' ? 'selected' : '' }}>Partnership</option>
            <option value="llp" {{ $bt === 'llp' ? 'selected' : '' }}>LLP</option>
            <option value="public_sector" {{ $bt === 'public_sector' ? 'selected' : '' }}>Public Sector</option>
            <option value="charity" {{ $bt === 'charity' ? 'selected' : '' }}>Charity</option>
        </select>
        @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Company Registration Number</label>
        <input type="text" class="form-control" name="registration_number" value="{{ old('registration_number', $data['registration_number'] ?? '') }}">
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">VAT Number</label>
        <input type="text" class="form-control" name="vat_number" value="{{ old('vat_number', $data['vat_number'] ?? '') }}">
    </div>

    <button type="submit" class="btn btn-primary w-100">Continue</button>
</form>
@endsection
