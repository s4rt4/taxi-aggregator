@extends('operator.onboarding.layout')
@section('step', '1')

@section('content')
    <h5 class="fw-bold mb-1">Company Details</h5>
    <p class="text-muted small mb-3">Tell us about your taxi or private hire company.</p>

    <div class="alert alert-info small mb-4">
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>UK Legal Requirements:</strong> Whether you are a sole trader, partnership, or limited company, you must hold a valid
        <strong>Private Hire Operator Licence</strong> issued by your local licensing authority, as required under the
        <strong>Private Hire Vehicles (London) Act 1998</strong> or equivalent legislation outside London.
        You will need to provide your licence number, expiry date, and proof of
        <strong>Public Liability Insurance</strong> in Step 3. Applications without valid documentation will be rejected.
    </div>

    <form method="POST" action="{{ route('operator.onboarding.save-step1') }}"
          x-data="{ businessType: '{{ old('business_type', $operator->business_type ?? 'sole_trader') }}' }">
        @csrf

        <div class="mb-3">
            <label class="field-label">Business Type <span class="text-danger">*</span></label>
            <div class="row g-2">
                <div class="col-6">
                    <div class="form-check border rounded p-3">
                        <input class="form-check-input" type="radio" name="business_type" value="sole_trader" id="bt_sole"
                               x-model="businessType"
                               {{ old('business_type', $operator->business_type ?? 'sole_trader') === 'sole_trader' ? 'checked' : '' }}>
                        <label class="form-check-label" for="bt_sole">
                            <strong>Sole Trader</strong>
                            <div class="text-muted small">Individual operator or owner-driver</div>
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-check border rounded p-3">
                        <input class="form-check-input" type="radio" name="business_type" value="limited_company" id="bt_ltd"
                               x-model="businessType"
                               {{ old('business_type', $operator->business_type ?? '') === 'limited_company' ? 'checked' : '' }}>
                        <label class="form-check-label" for="bt_ltd">
                            <strong>Limited Company</strong>
                            <div class="text-muted small">Registered at Companies House</div>
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-check border rounded p-3">
                        <input class="form-check-input" type="radio" name="business_type" value="partnership" id="bt_part"
                               x-model="businessType"
                               {{ old('business_type', $operator->business_type ?? '') === 'partnership' ? 'checked' : '' }}>
                        <label class="form-check-label" for="bt_part">
                            <strong>Partnership</strong>
                            <div class="text-muted small">Two or more partners</div>
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-check border rounded p-3">
                        <input class="form-check-input" type="radio" name="business_type" value="llp" id="bt_llp"
                               x-model="businessType"
                               {{ old('business_type', $operator->business_type ?? '') === 'llp' ? 'checked' : '' }}>
                        <label class="form-check-label" for="bt_llp">
                            <strong>LLP</strong>
                            <div class="text-muted small">Limited Liability Partnership</div>
                        </label>
                    </div>
                </div>
            </div>
            @error('business_type')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">
                <span x-show="businessType === 'sole_trader'">Operator / Trading Name</span>
                <span x-show="businessType === 'partnership'">Partnership / Trading Name</span>
                <span x-show="businessType === 'limited_company' || businessType === 'llp'">Operator / Company Name</span>
                <span class="text-danger">*</span>
            </label>
            <input type="text" name="operator_name" class="form-control @error('operator_name') is-invalid @enderror"
                   value="{{ old('operator_name', $operator->operator_name ?? '') }}" required
                   placeholder="e.g. ABC Private Hire">
            @error('operator_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">
                <span x-show="businessType === 'sole_trader'">Full Legal Name (as on PHO licence)</span>
                <span x-show="businessType === 'partnership'">Partnership Name</span>
                <span x-show="businessType === 'limited_company' || businessType === 'llp'">Legal Company Name</span>
            </label>
            <input type="text" name="legal_company_name" class="form-control @error('legal_company_name') is-invalid @enderror"
                   value="{{ old('legal_company_name', $operator->legal_company_name ?? '') }}"
                   :placeholder="businessType === 'sole_trader' ? 'e.g. John Smith' : (businessType === 'partnership' ? 'e.g. Smith & Jones' : 'e.g. ABC Ltd (if different)')">
            @error('legal_company_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3" x-show="businessType === 'limited_company' || businessType === 'llp'" x-transition>
            <label class="field-label">Companies House Number</label>
            <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror"
                   value="{{ old('registration_number', $operator->registration_number ?? '') }}"
                   placeholder="e.g. 12345678">
            @error('registration_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">Contact Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $operator->email ?? auth()->user()->email) }}" required
                   placeholder="bookings@yourcompany.co.uk">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">Contact Phone (+44) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">+44</span>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $operator->phone ?? '') }}" required
                       placeholder="7123 456 789">
            </div>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-4">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </form>
@endsection
