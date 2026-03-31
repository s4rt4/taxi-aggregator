@extends('operator.onboarding.layout')
@section('step', '3')

@section('content')
    <h5 class="fw-bold mb-1">Licence & Fleet</h5>
    <p class="text-muted small mb-4">Your private hire licence details and fleet information.</p>

    <form method="POST" action="{{ route('operator.onboarding.save-step3') }}">
        @csrf

        <div class="mb-3">
            <label class="field-label">Private Hire Licence Number <span class="text-danger">*</span></label>
            <input type="text" name="licence_number" class="form-control @error('licence_number') is-invalid @enderror"
                   value="{{ old('licence_number', $operator->licence_number ?? '') }}" required
                   placeholder="e.g. PH/12345">
            @error('licence_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="field-label">Licensing Authority <span class="text-danger">*</span></label>
            <select name="licence_authority" class="form-select @error('licence_authority') is-invalid @enderror" required>
                <option value="">-- Select Authority --</option>
                @php
                    $authorities = ['Transport for London (TfL)','Birmingham City Council','Manchester City Council',
                        'Leeds City Council','Liverpool City Council','Sheffield City Council','Bristol City Council',
                        'Nottingham City Council','Newcastle City Council','Glasgow City Council',
                        'Edinburgh Council','Cardiff Council','Belfast City Council','Other'];
                @endphp
                @foreach($authorities as $authority)
                    <option value="{{ $authority }}" {{ old('licence_authority', $operator->licence_authority ?? '') === $authority ? 'selected' : '' }}>
                        {{ $authority }}
                    </option>
                @endforeach
            </select>
            @error('licence_authority')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="field-label">Licence Expiry Date <span class="text-danger">*</span></label>
                <input type="date" name="licence_expiry" class="form-control @error('licence_expiry') is-invalid @enderror"
                       value="{{ old('licence_expiry', $operator->licence_expiry?->format('Y-m-d') ?? '') }}" required>
                @error('licence_expiry')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="field-label">Fleet Size <span class="text-danger">*</span></label>
                <input type="number" name="fleet_size" class="form-control @error('fleet_size') is-invalid @enderror"
                       value="{{ old('fleet_size', $operator->fleet_size ?? '') }}" required min="1" max="9999"
                       placeholder="Number of vehicles">
                @error('fleet_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="field-label">Dispatch System</label>
            <select name="dispatch_system" class="form-select @error('dispatch_system') is-invalid @enderror">
                <option value="">-- Select (optional) --</option>
                @php
                    $systems = ['Autocab','iCabbi','Cordic','Haiilo','Cab Treasure','Sherlock','Other','None'];
                @endphp
                @foreach($systems as $system)
                    <option value="{{ $system }}" {{ old('dispatch_system', $operator->dispatch_system ?? '') === $system ? 'selected' : '' }}>
                        {{ $system }}
                    </option>
                @endforeach
            </select>
            @error('dispatch_system')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('operator.onboarding.step', 2) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" class="btn btn-success px-4">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </form>
@endsection
