@extends('layouts.corporate')
@section('title', 'Employees')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Employees</h3>
        <a href="{{ route('corporate.employees.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add Employee
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($employees->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 mb-2 d-block"></i>
                    <p>No employees yet. Add your first employee to start booking.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Employee ID</th>
                                <th>Cost Centre</th>
                                <th class="text-end">Budget</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                                <tr>
                                    <td class="fw-semibold">{{ $emp->user->name ?? '-' }}</td>
                                    <td>{{ $emp->user->email ?? '-' }}</td>
                                    <td>
                                        @if($emp->corporate_role === 'super_user')
                                            <span class="badge bg-primary">Super User</span>
                                        @else
                                            <span class="badge bg-secondary">Employee</span>
                                        @endif
                                    </td>
                                    <td>{{ $emp->employee_id ?? '-' }}</td>
                                    <td>{{ $emp->cost_centre ?? '-' }}</td>
                                    <td class="text-end">
                                        @if($emp->monthly_budget === null)
                                            <span class="text-muted">No limit</span>
                                        @else
                                            &pound;{{ number_format($emp->monthly_spent, 2) }} / &pound;{{ number_format($emp->monthly_budget, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($emp->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('corporate.employees.edit', $emp) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="{{ route('corporate.employees.destroy', $emp) }}" class="d-inline" onsubmit="return confirm('Remove this employee?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
