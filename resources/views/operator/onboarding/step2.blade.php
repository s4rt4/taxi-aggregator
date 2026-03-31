@extends('operator.onboarding.layout')
@section('step', '2')

@section('content')
    <h5 class="fw-bold mb-1">Address & Location</h5>
    <p class="text-muted small mb-4">Where is your operating base located?</p>

    <form method="POST" action="{{ route('operator.onboarding.save-step2') }}">
        @csrf

        <div class="mb-3">
            <label class="field-label">Postcode <span class="text-danger">*</span></label>
            <input type="text" name="postcode" class="form-control @error('postcode') is-invalid @enderror"
                   value="{{ old('postcode', $operator->postcode ?? '') }}" required
                   placeholder="e.g. SW1A 1AA" style="max-width:200px;">
            @error('postcode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">Address Line 1 <span class="text-danger">*</span></label>
            <input type="text" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror"
                   value="{{ old('address_line_1', $operator->address_line_1 ?? '') }}" required
                   placeholder="Street address">
            @error('address_line_1')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">Address Line 2</label>
            <input type="text" name="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror"
                   value="{{ old('address_line_2', $operator->address_line_2 ?? '') }}"
                   placeholder="Building, suite, etc.">
            @error('address_line_2')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="field-label">City / Town <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                       value="{{ old('city', $operator->city ?? '') }}" required>
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="field-label">County</label>
                <select name="county" class="form-select @error('county') is-invalid @enderror">
                    <option value="">-- Select --</option>
                    @php
                        $counties = ['Bedfordshire','Berkshire','Bristol','Buckinghamshire','Cambridgeshire','Cheshire',
                            'City of London','Cornwall','Cumbria','Derbyshire','Devon','Dorset','Durham',
                            'East Riding of Yorkshire','East Sussex','Essex','Gloucestershire','Greater London',
                            'Greater Manchester','Hampshire','Herefordshire','Hertfordshire','Isle of Wight','Kent',
                            'Lancashire','Leicestershire','Lincolnshire','Merseyside','Norfolk','North Yorkshire',
                            'Northamptonshire','Northumberland','Nottinghamshire','Oxfordshire','Rutland','Shropshire',
                            'Somerset','South Yorkshire','Staffordshire','Suffolk','Surrey','Tyne and Wear',
                            'Warwickshire','West Midlands','West Sussex','West Yorkshire','Wiltshire','Worcestershire'];
                    @endphp
                    @foreach($counties as $county)
                        <option value="{{ $county }}" {{ old('county', $operator->county ?? '') === $county ? 'selected' : '' }}>
                            {{ $county }}
                        </option>
                    @endforeach
                </select>
                @error('county')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('operator.onboarding.step', 1) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" class="btn btn-success px-4">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </form>
@endsection
