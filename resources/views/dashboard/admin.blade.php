@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Admin Dashboard</h1>
    <p class="text-muted mb-0">Platform overview and key metrics.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-label">Operators</div>
                </div>
                <div class="stat-value">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-label">Passengers</div>
                </div>
                <div class="stat-value">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="stat-label">Bookings</div>
                </div>
                <div class="stat-value">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-currency-pound"></i>
                    </div>
                    <div class="stat-label">Revenue</div>
                </div>
                <div class="stat-value">&pound;0</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Pending Operator Approvals</h6>
                <a href="{{ route('admin.operators.pending') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-hourglass fs-2 d-block mb-2"></i>
                <p class="mb-0">No pending approvals.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Recent Bookings</h6>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                <p class="mb-0">No bookings yet.</p>
            </div>
        </div>
    </div>
</div>
@endsection
