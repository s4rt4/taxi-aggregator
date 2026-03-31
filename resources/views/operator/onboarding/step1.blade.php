@extends('operator.onboarding.layout')
@section('step', '1')

@section('content')
    <h5 class="fw-bold mb-1">Company Details</h5>
    <p class="text-muted small mb-4">Tell us about your taxi or private hire company.</p>

    <form method="POST" action="{{ route('operator.onboarding.save-step1') }}">
        @csrf

        <div class="mb-3">
            <label class="field-label">Operator / Company Name <span class="text-danger">*</span></label>
            <input type="text" name="operator_name" class="form-control @error('operator_name') is-invalid @enderror"
                   value="{{ old('operator_name', $operator->operator_name ?? '') }}" required
                   placeholder="e.g. ABC Private Hire">
            @error('operator_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">Legal Company Name</label>
            <input type="text" name="legal_company_name" class="form-control @error('legal_company_name') is-invalid @enderror"
                   value="{{ old('legal_company_name', $operator->legal_company_name ?? '') }}"
                   placeholder="e.g. ABC Ltd (if different)">
            @error('legal_company_name')
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
