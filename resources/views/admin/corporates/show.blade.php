@extends('layouts.admin')
@section('title', 'Corporate: ' . $corporate->company_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ $corporate->company_name }}</h3>
            <p class="text-muted small mb-0">
                Status:
                @switch($corporate->status)
                    @case('pending') <span class="badge bg-warning text-dark">Pending</span> @break
                    @case('approved') <span class="badge bg-success">Approved</span> @break
                    @case('suspended') <span class="badge bg-danger">Suspended</span> @break
                    @case('closed') <span class="badge bg-dark">Closed</span> @break
                @endswitch
            </p>
        </div>
        <div>
            @if($corporate->status === 'pending' && auth()->user()->hasAdminPermission('corporates.approve'))
                <form method="POST" action="{{ route('admin.corporates.approve', $corporate) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.corporates.reject', $corporate) }}" class="d-inline" onsubmit="return confirm('Reject this corporate?');">
                    @csrf
                    <button class="btn btn-outline-danger">Reject</button>
                </form>
            @endif
            @if($corporate->status === 'approved' && auth()->user()->hasAdminPermission('corporates.suspend'))
                <form method="POST" action="{{ route('admin.corporates.suspend', $corporate) }}" class="d-inline" onsubmit="return confirm('Suspend this corporate?');">
                    @csrf
                    <button class="btn btn-outline-warning">Suspend</button>
                </form>
            @endif
            @if($corporate->status === 'suspended' && auth()->user()->hasAdminPermission('corporates.suspend'))
                <form method="POST" action="{{ route('admin.corporates.reactivate', $corporate) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success">Reactivate</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><h6 class="fw-bold mb-0">Company Information</h6></div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-4">Legal Name</dt><dd class="col-sm-8">{{ $corporate->legal_name ?? '-' }}</dd>
                        <dt class="col-sm-4">Business Type</dt><dd class="col-sm-8">{{ str_replace('_', ' ', ucfirst($corporate->business_type)) }}</dd>
                        <dt class="col-sm-4">Registration</dt><dd class="col-sm-8">{{ $corporate->registration_number ?? '-' }}</dd>
                        <dt class="col-sm-4">VAT</dt><dd class="col-sm-8">{{ $corporate->vat_number ?? '-' }}</dd>
                        <dt class="col-sm-4">Billing Email</dt><dd class="col-sm-8">{{ $corporate->billing_email }}</dd>
                        <dt class="col-sm-4">Billing Phone</dt><dd class="col-sm-8">{{ $corporate->billing_phone }}</dd>
                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">
                            {{ $corporate->billing_address_line_1 }}<br>
                            @if($corporate->billing_address_line_2){{ $corporate->billing_address_line_2 }}<br>@endif
                            {{ $corporate->billing_city }}, {{ $corporate->billing_postcode }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><h6 class="fw-bold mb-0">Budget &amp; Invoicing</h6></div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-5">Monthly Budget</dt>
                        <dd class="col-sm-7">{{ $corporate->monthly_budget === null ? 'Unlimited' : '£' . number_format($corporate->monthly_budget, 2) }}</dd>
                        <dt class="col-sm-5">Monthly Spent</dt>
                        <dd class="col-sm-7">£{{ number_format($corporate->monthly_spent, 2) }}</dd>
                        <dt class="col-sm-5">Invoicing</dt>
                        <dd class="col-sm-7">{{ ucfirst($corporate->invoicing_frequency) }}</dd>
                        <dt class="col-sm-5">Payment Terms</dt>
                        <dd class="col-sm-7">{{ $corporate->payment_terms_days }} days</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white"><h6 class="fw-bold mb-0">Users ({{ $corporate->corporateUsers->count() }})</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($corporate->corporateUsers as $cu)
                            <tr>
                                <td>{{ $cu->user->name ?? '-' }}</td>
                                <td>{{ $cu->user->email ?? '-' }}</td>
                                <td>
                                    @if($cu->isSuperUser())
                                        <span class="badge bg-primary">Super User</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                                <td>{{ $cu->is_active ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white"><h6 class="fw-bold mb-0">Recent Invoices ({{ $corporate->invoices->count() }})</h6></div>
        <div class="card-body p-0">
            @if($corporate->invoices->isEmpty())
                <div class="text-center py-3 text-muted small">No invoices yet.</div>
            @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Reference</th><th>Period</th><th>Due</th><th>Status</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($corporate->invoices as $inv)
                            <tr>
                                <td>{{ $inv->reference }}</td>
                                <td>{{ $inv->period_start?->format('d M Y') }} - {{ $inv->period_end?->format('d M Y') }}</td>
                                <td>{{ $inv->due_date?->format('d M Y') }}</td>
                                <td>{{ ucfirst($inv->status) }}</td>
                                <td class="text-end">£{{ number_format($inv->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h6 class="fw-bold mb-0">Recent Bookings ({{ $recentBookings->count() }})</h6></div>
        <div class="card-body p-0">
            @if($recentBookings->isEmpty())
                <div class="text-center py-3 text-muted small">No bookings yet.</div>
            @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Ref</th><th>Employee</th><th>Pickup</th><th>Operator</th><th>Status</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $b)
                            <tr>
                                <td>{{ $b->reference }}</td>
                                <td>{{ $b->passenger?->name ?? '-' }}</td>
                                <td>{{ $b->pickup_datetime?->format('d M Y H:i') }}</td>
                                <td>{{ $b->operator?->operator_name ?? '-' }}</td>
                                <td>{{ ucfirst($b->status) }}</td>
                                <td class="text-end">£{{ number_format($b->total_price, 2) }}</td>
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
