@extends('corporate.onboarding.layout')

@section('onboarding-content')
<h6 class="fw-bold">Invoicing Preferences</h6>
<p class="text-muted small mb-4">Set up budgets and invoicing cadence.</p>

<form method="POST" action="{{ route('corporate.onboarding.save', 3) }}">
    @csrf

    <div class="mb-3">
        <label class="form-label small fw-semibold">Monthly Budget (optional)</label>
        <div class="input-group">
            <span class="input-group-text">&pound;</span>
            <input type="number" step="0.01" min="0" class="form-control @error('monthly_budget') is-invalid @enderror"
                   name="monthly_budget" value="{{ old('monthly_budget', $data['monthly_budget'] ?? '') }}" placeholder="Leave blank for no limit">
        </div>
        <small class="text-muted">Total spending cap across all employees per month.</small>
        @error('monthly_budget')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Invoicing Frequency <span class="text-danger">*</span></label>
        <select name="invoicing_frequency" class="form-select @error('invoicing_frequency') is-invalid @enderror" required>
            @php $freq = old('invoicing_frequency', $data['invoicing_frequency'] ?? 'monthly'); @endphp
            <option value="monthly" {{ $freq === 'monthly' ? 'selected' : '' }}>Monthly (1st of each month)</option>
            <option value="weekly" {{ $freq === 'weekly' ? 'selected' : '' }}>Weekly (every Monday)</option>
        </select>
        @error('invoicing_frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold">Payment Terms (days) <span class="text-danger">*</span></label>
        <input type="number" min="1" max="90" class="form-control @error('payment_terms_days') is-invalid @enderror"
               name="payment_terms_days" value="{{ old('payment_terms_days', $data['payment_terms_days'] ?? 14) }}" required>
        <small class="text-muted">Standard is net 14 days.</small>
        @error('payment_terms_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="alert alert-info small">
        <i class="bi bi-info-circle"></i> After submitting, your corporate account will be reviewed by our admin team before being activated. This usually takes 1 business day.
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('corporate.onboarding.step', 2) }}" class="btn btn-outline-secondary flex-fill">Back</a>
        <button type="submit" class="btn btn-primary flex-fill">Submit for Approval</button>
    </div>
</form>
@endsection
