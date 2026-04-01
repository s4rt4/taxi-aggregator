@extends('layouts.admin')
@section('title', $operator->operator_name)

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.operators.index') }}">Operators</a></li>
    <li class="breadcrumb-item active">{{ $operator->operator_name }}</li>
</ol>
@endsection

@section('content')
{{-- Operator Header --}}
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="mb-0">{{ $operator->operator_name }}</h1>
            @php
                $tierColors = ['bronze' => 'secondary', 'silver' => 'light text-dark', 'gold' => 'warning', 'platinum' => 'info'];
                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'dark'];
            @endphp
            <span class="badge bg-{{ $tierColors[$operator->tier] ?? 'secondary' }}">{{ ucfirst($operator->tier ?? 'bronze') }}</span>
            <span class="badge bg-{{ $statusColors[$operator->status] ?? 'secondary' }}">{{ ucfirst($operator->status) }}</span>
        </div>
        <p class="text-muted mb-0">
            {{ $operator->email }}
            @if($operator->account_id)
                &middot; Account: {{ $operator->account_id }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($operator->status === 'pending')
            <form method="POST" action="{{ route('admin.operators.approve', $operator) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this operator?')">
                    <i class="bi bi-check-lg me-1"></i> Approve
                </button>
            </form>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-lg me-1"></i> Reject
            </button>
        @elseif($operator->status === 'approved')
            <form method="POST" action="{{ route('admin.operators.suspend', $operator) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-dark" onclick="return confirm('Suspend this operator?')">
                    <i class="bi bi-pause-circle me-1"></i> Suspend
                </button>
            </form>
        @elseif($operator->status === 'suspended')
            <form method="POST" action="{{ route('admin.operators.reactivate', $operator) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Reactivate this operator?')">
                    <i class="bi bi-play-circle me-1"></i> Reactivate
                </button>
            </form>
        @endif
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tierModal">
            <i class="bi bi-trophy me-1"></i> Change Tier
        </button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#commissionModal">
            <i class="bi bi-percent me-1"></i> Commission
        </button>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-label">Total Bookings</div>
                <div class="stat-value">{{ number_format($operator->total_bookings ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-label">Rating</div>
                <div class="stat-value">
                    @if($operator->rating_avg)
                        <i class="bi bi-star-fill text-warning"></i> {{ number_format($operator->rating_avg, 1) }}
                    @else
                        -
                    @endif
                </div>
                <small class="text-muted">{{ $operator->rating_count ?? 0 }} reviews</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-label">Commission Rate</div>
                <div class="stat-value">{{ number_format($operator->commission_rate ?? 0, 1) }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-label">Fleet Size</div>
                <div class="stat-value">{{ $operator->fleet_size ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#icabbi" role="tab">iCabbi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#documents" role="tab">Documents</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#bookings" role="tab">
            Bookings <span class="badge bg-secondary ms-1">{{ $operator->bookings->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#reviews" role="tab">
            Reviews <span class="badge bg-secondary ms-1">{{ $operator->reviews->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">Activity</a>
    </li>
</ul>

<div class="tab-content">
    {{-- Details Tab --}}
    <div class="tab-pane fade show active" id="details" role="tabpanel">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="fw-semibold mb-0">Company Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Operator Name</td>
                                <td class="fw-semibold">{{ $operator->operator_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Legal Company Name</td>
                                <td>{{ $operator->legal_company_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trading Name</td>
                                <td>{{ $operator->trading_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Registration Number</td>
                                <td>{{ $operator->registration_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">VAT Number</td>
                                <td>{{ $operator->vat_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Website</td>
                                <td>
                                    @if($operator->website)
                                        <a href="{{ $operator->website }}" target="_blank">{{ $operator->website }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dispatch System</td>
                                <td>{{ $operator->dispatch_system ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="fw-semibold mb-0">Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Email</td>
                                <td>{{ $operator->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td>{{ $operator->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Address</td>
                                <td>
                                    {{ $operator->address_line_1 ?? '' }}
                                    @if($operator->address_line_2)<br>{{ $operator->address_line_2 }}@endif
                                    @if($operator->city)<br>{{ $operator->city }}@endif
                                    @if($operator->county), {{ $operator->county }}@endif
                                    @if($operator->postcode)<br>{{ $operator->postcode }}@endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="fw-semibold mb-0">Licence Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Licence Number</td>
                                <td>{{ $operator->licence_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Licence Authority</td>
                                <td>{{ $operator->licence_authority ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Licence Expiry</td>
                                <td>
                                    @if($operator->licence_expiry)
                                        {{ $operator->licence_expiry->format('d M Y') }}
                                        @if($operator->licence_expiry->isPast())
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        @elseif($operator->licence_expiry->diffInDays(now()) < 30)
                                            <span class="badge bg-warning ms-1">Expiring Soon</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Public Liability Expiry</td>
                                <td>
                                    @if($operator->public_liability_expiry)
                                        {{ $operator->public_liability_expiry->format('d M Y') }}
                                        @if($operator->public_liability_expiry->isPast())
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- iCabbi Integration Tab --}}
    <div class="tab-pane fade" id="icabbi" role="tabpanel">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">iCabbi Dispatch Integration</h6>
                @if($operator->icabbi_enabled)
                    <span class="badge bg-success">Enabled</span>
                @else
                    <span class="badge bg-secondary">Disabled</span>
                @endif
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Status</td>
                        <td>
                            @if($operator->usesIcabbi())
                                <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Active & Configured</span>
                            @elseif($operator->icabbi_enabled)
                                <span class="text-warning fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Enabled but missing credentials</span>
                            @else
                                <span class="text-muted">Not enabled</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">API URL</td>
                        <td>{{ $operator->icabbi_api_url ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Integration Name</td>
                        <td>{{ $operator->icabbi_integration_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">App Key</td>
                        <td>
                            @if($operator->icabbi_app_key)
                                <span class="text-muted">{{ str_repeat('*', 8) }}{{ substr($operator->icabbi_app_key, -4) }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Secret Key</td>
                        <td>
                            @if($operator->icabbi_secret_key)
                                <span class="text-muted">{{ str_repeat('*', 8) }}{{ substr($operator->icabbi_secret_key, -4) }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Documents Tab --}}
    <div class="tab-pane fade" id="documents" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-semibold">Operator Licence</h6>
                        @if($operator->operator_licence_file)
                            <a href="{{ asset('storage/' . $operator->operator_licence_file) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                            </a>
                        @else
                            <p class="text-muted">No document uploaded.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-semibold">Public Liability Insurance</h6>
                        @if($operator->public_liability_insurance_file)
                            <a href="{{ asset('storage/' . $operator->public_liability_insurance_file) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                            </a>
                        @else
                            <p class="text-muted">No document uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings Tab --}}
    <div class="tab-pane fade" id="bookings" role="tabpanel">
        <div class="card">
            @if($operator->bookings->isEmpty())
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-journal-text fs-2 d-block mb-2"></i>
                    No bookings yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Fleet Type</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operator->bookings as $booking)
                                <tr>
                                    <td class="fw-semibold">{{ $booking->reference }}</td>
                                    <td>{{ $booking->pickup_datetime?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        <small>
                                            {{ \Illuminate\Support\Str::limit($booking->pickup_address, 30) }}
                                            <i class="bi bi-arrow-right mx-1"></i>
                                            {{ \Illuminate\Support\Str::limit($booking->destination_address, 30) }}
                                        </small>
                                    </td>
                                    <td>{{ $booking->fleetType->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $sc = ['pending'=>'warning','confirmed'=>'info','accepted'=>'info','completed'=>'success','cancelled'=>'danger','no_show'=>'dark'];
                                        @endphp
                                        <span class="badge bg-{{ $sc[$booking->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td>&pound;{{ number_format($booking->total_price, 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Reviews Tab --}}
    <div class="tab-pane fade" id="reviews" role="tabpanel">
        <div class="card">
            @if($operator->reviews->isEmpty())
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-star fs-2 d-block mb-2"></i>
                    No reviews yet.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($operator->reviews as $review)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $review->passenger->name ?? 'Unknown Passenger' }}</div>
                                    <div class="mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                        @endfor
                                        <span class="ms-1">{{ $review->rating }}/5</span>
                                    </div>
                                    @if($review->comment)
                                        <p class="mb-0 text-muted">{{ $review->comment }}</p>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Activity Tab --}}
    <div class="tab-pane fade" id="activity" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:30%">Registered</td>
                        <td>{{ $operator->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @if($operator->approved_at)
                        <tr>
                            <td class="text-muted">Approved</td>
                            <td>{{ $operator->approved_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endif
                    @if($operator->rejection_reason)
                        <tr>
                            <td class="text-muted">Rejection Reason</td>
                            <td class="text-danger">{{ $operator->rejection_reason }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Drivers</td>
                        <td>{{ $operator->drivers->count() }} registered</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Vehicles</td>
                        <td>{{ $operator->vehicles->count() }} registered</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contacts</td>
                        <td>{{ $operator->contacts->count() }} contacts</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.operators.reject', $operator) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject {{ $operator->operator_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required
                            placeholder="Provide a reason for rejecting this application..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tier Modal --}}
<div class="modal fade" id="tierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.operators.update-tier', $operator) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Change Operator Tier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tier</label>
                        <select name="tier" class="form-select" required>
                            @foreach(['bronze', 'silver', 'gold', 'platinum'] as $tier)
                                <option value="{{ $tier }}" {{ ($operator->tier ?? 'bronze') === $tier ? 'selected' : '' }}>
                                    {{ ucfirst($tier) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Tier</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Commission Modal --}}
<div class="modal fade" id="commissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.operators.update-commission', $operator) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Update Commission Rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Commission Rate (%)</label>
                        <input type="number" name="commission_rate" class="form-control" step="0.1" min="0" max="50"
                            value="{{ $operator->commission_rate ?? 15 }}" required>
                        <div class="form-text">Percentage charged on each booking (0-50%).</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Commission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
