@extends('layouts.operator')
@section('title', 'My Dashboard')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>My Dashboard</h1>
    <div>
        <a href="#" class="text-decoration-none small"><i class="bi bi-plus-circle"></i> Add Fleet Type</a>
        <span class="ms-3 text-muted small">Help</span>
    </div>
</div>

{{-- Fleet type tabs --}}
<div class="fleet-tabs mb-3">
    <span class="fleet-tab active">Petrol, Diesel & Hybrid</span>
</div>

{{-- Fleet type icons row --}}
<div class="bg-white rounded border p-3 mb-4">
    <div class="d-flex align-items-center gap-4 overflow-auto">
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-car-front fs-4 text-muted"></i>
            <div class="small text-muted mt-1">1-4 Seater</div>
        </div>
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-car-front fs-4 text-muted"></i>
            <div class="small text-muted mt-1">5-6 Seater</div>
        </div>
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-car-front fs-4 text-muted"></i>
            <div class="small text-muted mt-1">7 Seater</div>
        </div>
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-car-front fs-4 text-muted"></i>
            <div class="small text-muted mt-1">8 Seater</div>
        </div>
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-truck fs-4 text-muted"></i>
            <div class="small text-muted mt-1">9 Seater</div>
        </div>
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-bus-front fs-4 text-muted"></i>
            <div class="small text-muted mt-1">10-14</div>
        </div>
        <div class="text-center" style="min-width: 60px;">
            <i class="bi bi-bus-front fs-4 text-muted"></i>
            <div class="small text-muted mt-1">15-16</div>
        </div>
    </div>
</div>

{{-- Tabs: Needs Attention / Upcoming / Latest --}}
<div class="mb-4">
    <ul class="nav nav-pills gap-2 mb-3">
        <li class="nav-item">
            <a class="nav-link active px-3 py-2 text-uppercase fw-bold small" style="background:#dc3545;border:none;" href="#">Needs Attention</a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 text-uppercase fw-bold small text-muted" href="#">Upcoming pickups</a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 text-uppercase fw-bold small text-muted" href="#">Latest bookings</a>
        </li>
    </ul>
</div>

{{-- Countdown + Empty state --}}
<div class="bg-white rounded border p-4 text-center">
    {{-- Countdown --}}
    <div class="d-flex justify-content-center mb-4">
        <div class="countdown-widget">
            <div class="countdown-circle">
                <span class="countdown-value">0</span>
                <span class="countdown-label">Days</span>
            </div>
            <div class="countdown-circle">
                <span class="countdown-value">0</span>
                <span class="countdown-label">Hours</span>
            </div>
            <div class="countdown-circle">
                <span class="countdown-value">0</span>
                <span class="countdown-label">Minutes</span>
            </div>
        </div>
    </div>
    <div class="text-center small text-muted mb-2">NEXT PICKUP IN</div>

    <hr>

    <p class="text-muted mb-2">No bookings yet? We're here to help you get more bookings, just follow these steps at <a href="#">FAQ page</a></p>
</div>

{{-- Footer info --}}
<div class="text-center mt-4 small text-muted">
    <a href="{{ route('operator.account.index') }}">My Account</a>
</div>
@endsection
