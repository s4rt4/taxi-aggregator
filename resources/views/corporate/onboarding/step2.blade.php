@extends('corporate.onboarding.layout')

@section('onboarding-content')
<h6 class="fw-bold">Billing Information</h6>
<p class="text-muted small mb-4">Where should we send invoices?</p>

<form method="POST" action="{{ route('corporate.onboarding.save', 2) }}">
    @csrf

    <div class="mb-3">
        <label class="form-label small fw-semibold">Billing Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('billing_email') is-invalid @enderror"
               name="billing_email" value="{{ old('billing_email', $data['billing_email'] ?? auth()->user()->email) }}" required>
        @error('billing_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Billing Phone <span class="text-danger">*</span></label>
        <input type="tel" class="form-control @error('billing_phone') is-invalid @enderror"
               name="billing_phone" value="{{ old('billing_phone', $data['billing_phone'] ?? '') }}" required>
        @error('billing_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Address Line 1 <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('billing_address_line_1') is-invalid @enderror"
               name="billing_address_line_1" value="{{ old('billing_address_line_1', $data['billing_address_line_1'] ?? '') }}" required>
        @error('billing_address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Address Line 2</label>
        <input type="text" class="form-control" name="billing_address_line_2" value="{{ old('billing_address_line_2', $data['billing_address_line_2'] ?? '') }}">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-semibold">City <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('billing_city') is-invalid @enderror"
                   name="billing_city" value="{{ old('billing_city', $data['billing_city'] ?? '') }}" required>
            @error('billing_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-semibold">Postcode <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('billing_postcode') is-invalid @enderror"
                   name="billing_postcode" value="{{ old('billing_postcode', $data['billing_postcode'] ?? '') }}" required>
            @error('billing_postcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">County</label>
        <input type="text" class="form-control" name="billing_county" value="{{ old('billing_county', $data['billing_county'] ?? '') }}">
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('corporate.onboarding.step', 1) }}" class="btn btn-outline-secondary flex-fill">Back</a>
        <button type="submit" class="btn btn-primary flex-fill">Continue</button>
    </div>
</form>
@endsection
