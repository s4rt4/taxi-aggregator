@extends('layouts.corporate')
@section('title', 'Employee Details')

@section('content')
<div class="container-fluid" style="max-width: 720px;">
    <h3 class="fw-bold mb-4">{{ $employee->user->name }}</h3>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">{{ $employee->user->email }}</dd>

                <dt class="col-sm-4">Phone</dt>
                <dd class="col-sm-8">{{ $employee->user->phone ?? '-' }}</dd>

                <dt class="col-sm-4">Role</dt>
                <dd class="col-sm-8">{{ $employee->isSuperUser() ? 'Super User' : 'Employee' }}</dd>

                <dt class="col-sm-4">Employee ID</dt>
                <dd class="col-sm-8">{{ $employee->employee_id ?? '-' }}</dd>

                <dt class="col-sm-4">Cost Centre</dt>
                <dd class="col-sm-8">{{ $employee->cost_centre ?? '-' }}</dd>

                <dt class="col-sm-4">Monthly Budget</dt>
                <dd class="col-sm-8">
                    @if($employee->monthly_budget === null)
                        Unlimited
                    @else
                        &pound;{{ number_format($employee->monthly_spent, 2) }} / &pound;{{ number_format($employee->monthly_budget, 2) }}
                    @endif
                </dd>

                <dt class="col-sm-4">Requires Approval</dt>
                <dd class="col-sm-8">{{ $employee->requires_approval ? 'Yes' : 'No' }}</dd>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">
                    @if($employee->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>
            </dl>

            <div class="d-flex gap-2">
                <a href="{{ route('corporate.employees.edit', $employee) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('corporate.employees.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
