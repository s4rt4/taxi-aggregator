@extends('layouts.admin')
@section('title', $user->name)

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
</ol>
@endsection

@section('content')
@php
    $roleColors = ['admin' => 'danger', 'operator' => 'primary', 'driver' => 'info', 'passenger' => 'success'];
@endphp

{{-- User Header --}}
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="mb-0">{{ $user->name }}</h1>
            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span>
            @if($user->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </div>
        <p class="text-muted mb-0">{{ $user->email }} &middot; Joined {{ $user->created_at->format('d M Y') }}</p>
    </div>
    <div>
        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="d-inline">
            @csrf
            @if($user->is_active)
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Deactivate this user?')">
                    <i class="bi bi-person-x me-1"></i> Deactivate
                </button>
            @else
                <button type="submit" class="btn btn-success" onclick="return confirm('Activate this user?')">
                    <i class="bi bi-person-check me-1"></i> Activate
                </button>
            @endif
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">User Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">ID</td>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Name</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Role</td>
                        <td>
                            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email Verified</td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i> {{ $user->email_verified_at->format('d M Y H:i') }}</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i> Not verified</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Joined</td>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if($user->role === 'operator' && $user->operator)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0">Operator Profile</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Operator Name</td>
                            <td class="fw-semibold">{{ $user->operator->operator_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php $opStatusColors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','suspended'=>'dark']; @endphp
                                <span class="badge bg-{{ $opStatusColors[$user->operator->status] ?? 'secondary' }}">
                                    {{ ucfirst($user->operator->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">City</td>
                            <td>{{ $user->operator->city ?? '-' }}</td>
                        </tr>
                    </table>
                    <a href="{{ route('admin.operators.show', $user->operator) }}" class="btn btn-sm btn-outline-primary mt-2">
                        View Operator Profile
                    </a>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Recent Bookings</h6>
                <span class="badge bg-secondary">{{ $user->bookings->count() }}</span>
            </div>
            @if($user->bookings->isEmpty())
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-journal-text fs-2 d-block mb-2"></i>
                    No bookings.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($user->bookings as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold">{{ $booking->reference }}</span>
                                    <br><small class="text-muted">{{ $booking->pickup_datetime?->format('d M Y H:i') ?? '-' }}</small>
                                </div>
                                <div class="text-end">
                                    @php $sc=['pending'=>'warning','completed'=>'success','cancelled'=>'danger']; @endphp
                                    <span class="badge bg-{{ $sc[$booking->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                    <br><small class="fw-semibold">&pound;{{ number_format($booking->total_price, 2) }}</small>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
