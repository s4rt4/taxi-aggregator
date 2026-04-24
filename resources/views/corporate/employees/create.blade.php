@extends('layouts.corporate')
@section('title', 'Add Employee')

@section('content')
<div class="container-fluid" style="max-width: 720px;">
    <h3 class="fw-bold mb-4">Add Employee</h3>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('corporate.employees.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Phone</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Role</label>
                        <select name="corporate_role" class="form-select">
                            <option value="user" {{ old('corporate_role', 'user') === 'user' ? 'selected' : '' }}>Employee</option>
                            <option value="super_user" {{ old('corporate_role') === 'super_user' ? 'selected' : '' }}>Super User</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Cost Centre</label>
                        <input type="text" name="cost_centre" class="form-control" value="{{ old('cost_centre') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Monthly Budget (optional)</label>
                        <div class="input-group">
                            <span class="input-group-text">&pound;</span>
                            <input type="number" step="0.01" min="0" name="monthly_budget" class="form-control" value="{{ old('monthly_budget') }}" placeholder="No limit">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="requires_approval" id="requires_approval" value="1" class="form-check-input" {{ old('requires_approval') ? 'checked' : '' }}>
                            <label for="requires_approval" class="form-check-label">Bookings require super user approval</label>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i> If the user already has a corporate account with us, they'll be added to your company. Otherwise, a new account will be created and they'll need to reset their password to log in.
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('corporate.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
