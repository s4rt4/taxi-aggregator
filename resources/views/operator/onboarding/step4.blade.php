@extends('operator.onboarding.layout')
@section('step', '4')

@section('content')
    <h5 class="fw-bold mb-1">Basic Pricing</h5>
    <p class="text-muted small mb-4">Select the fleet types you operate and set your basic per-mile rates. You can fine-tune pricing later.</p>

    <form method="POST" action="{{ route('operator.onboarding.save-step4') }}">
        @csrf

        @if($fleetTypes->isEmpty())
            <div class="alert alert-warning">
                No fleet types are currently configured. Please contact support.
            </div>
        @else
            <p class="small fw-semibold mb-2">Select your fleet types and set rates:</p>

            @foreach($fleetTypes as $ft)
                @php
                    $existingPrice = $operator?->perMilePrices->where('fleet_type_id', $ft->id)->first();
                    $isChecked = old('fleet_types') ? in_array($ft->id, old('fleet_types', [])) : ($existingPrice ? true : false);
                @endphp
                <div class="border rounded p-3 mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input fleet-type-check" type="checkbox"
                               name="fleet_types[]" value="{{ $ft->id }}"
                               id="ft_{{ $ft->id }}"
                               data-target="pricing_{{ $ft->id }}"
                               {{ $isChecked ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="ft_{{ $ft->id }}">
                            @if($ft->icon)
                                <i class="bi bi-{{ $ft->icon }} me-1"></i>
                            @endif
                            {{ $ft->name }}
                            <span class="text-muted fw-normal small ms-1">({{ $ft->min_passengers }}-{{ $ft->max_passengers }} passengers)</span>
                        </label>
                    </div>
                    <div class="row pricing-fields {{ $isChecked ? '' : 'd-none' }}" id="pricing_{{ $ft->id }}">
                        <div class="col-6">
                            <label class="field-label">Rate per mile</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">&pound;</span>
                                <input type="number" step="0.01" min="0.01" max="999.99"
                                       name="rate_per_mile[{{ $ft->id }}]"
                                       class="form-control"
                                       value="{{ old('rate_per_mile.' . $ft->id, $existingPrice->rate_per_mile ?? '') }}"
                                       placeholder="e.g. 2.50">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="field-label">Minimum fare</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">&pound;</span>
                                <input type="number" step="0.01" min="0" max="9999.99"
                                       name="minimum_fare[{{ $ft->id }}]"
                                       class="form-control"
                                       value="{{ old('minimum_fare.' . $ft->id, $existingPrice->minimum_fare ?? '') }}"
                                       placeholder="e.g. 10.00">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('operator.onboarding.step', 3) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" class="btn btn-success px-4">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.fleet-type-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.target);
            if (target) {
                target.classList.toggle('d-none', !this.checked);
            }
        });
    });
});
</script>
@endpush
