@extends('layouts.corporate')
@section('title', 'Edit Employee')

@section('content')
<div class="container-fluid" style="max-width: 720px;">
    <h3 class="fw-bold mb-4">Edit Employee: {{ $employee->user->name }}</h3>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('corporate.employees.update', $employee) }}">
                @csrf @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Role</label>
                        <select name="corporate_role" class="form-select">
                            <option value="user" {{ $employee->corporate_role === 'user' ? 'selected' : '' }}>Employee</option>
                            <option value="super_user" {{ $employee->corporate_role === 'super_user' ? 'selected' : '' }}>Super User</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $employee->employee_id) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Cost Centre</label>
                        <input type="text" name="cost_centre" class="form-control" value="{{ old('cost_centre', $employee->cost_centre) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-semibold">Monthly Budget</label>
                        <div class="input-group">
                            <span class="input-group-text">&pound;</span>
                            <input type="number" step="0.01" min="0" name="monthly_budget" class="form-control" value="{{ old('monthly_budget', $employee->monthly_budget) }}" placeholder="No limit">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="requires_approval" id="requires_approval" value="1" class="form-check-input" {{ $employee->requires_approval ? 'checked' : '' }}>
                            <label for="requires_approval" class="form-check-label">Requires approval</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ $employee->is_active ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('corporate.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
