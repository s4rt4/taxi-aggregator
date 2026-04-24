@extends('layouts.admin')
@section('title', 'Corporates')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4">Corporate Accounts</h3>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search company name, email, reg no" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        @foreach(['pending','approved','suspended','closed'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($corporates->isEmpty())
                <div class="text-center py-5 text-muted">No corporates found.</div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Billing Email</th>
                                <th>Users</th>
                                <th>Bookings</th>
                                <th>Invoices</th>
                                <th>Freq</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($corporates as $c)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $c->company_name }}</div>
                                        <small class="text-muted">{{ $c->business_type }}</small>
                                    </td>
                                    <td>{{ $c->billing_email }}</td>
                                    <td>{{ $c->corporate_users_count }}</td>
                                    <td>{{ $c->bookings_count }}</td>
                                    <td>{{ $c->invoices_count }}</td>
                                    <td>{{ ucfirst($c->invoicing_frequency) }}</td>
                                    <td>
                                        @switch($c->status)
                                            @case('pending') <span class="badge bg-warning text-dark">Pending</span> @break
                                            @case('approved') <span class="badge bg-success">Approved</span> @break
                                            @case('suspended') <span class="badge bg-danger">Suspended</span> @break
                                            @case('closed') <span class="badge bg-dark">Closed</span> @break
                                        @endswitch
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.corporates.show', $c) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($corporates->hasPages())
            <div class="card-footer bg-white">{{ $corporates->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
