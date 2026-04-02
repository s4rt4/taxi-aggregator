@extends('layouts.admin')
@section('title', 'Settings')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Settings</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>System Settings</h1>
    <p class="text-muted mb-0">Configure platform-wide settings.</p>
</div>

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#general" role="tab">General</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#commission" role="tab">Commission</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#fleet-types-tab" role="tab">Fleet Types</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#meet-greet" role="tab">Meet & Greet Locations</a>
    </li>
</ul>

<div class="tab-content">
    {{-- General / Site Settings Tab --}}
    <div class="tab-pane fade show active" id="general" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @foreach($groups as $groupName => $groupSettings)
            <h5 class="text-capitalize mt-3">{{ $groupName }}</h5>
            <div class="card mb-4">
                <div class="card-body">
                    @foreach($groupSettings as $setting)
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ $setting->label }}</label>
                        @if($setting->type === 'textarea')
                            <textarea name="settings[{{ $setting->key }}]" class="form-control form-control-sm" rows="2">{{ $setting->value }}</textarea>
                        @else
                            <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control form-control-sm" value="{{ $setting->value }}">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            <div class="mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Commission Tab --}}
    <div class="tab-pane fade" id="commission" role="tabpanel">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Commission Settings</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Commission Rate (%)</label>
                            <input type="number" class="form-control" name="default_commission_rate" step="0.1" min="0" max="50" value="15">
                            <div class="form-text">Applied to new operators by default. Individual rates can be set per operator.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission by Tier</label>
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Tier</th>
                                        <th>Rate (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-secondary">Bronze</span></td>
                                        <td><input type="number" class="form-control form-control-sm" name="commission_bronze" step="0.1" value="15"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-dark">Silver</span></td>
                                        <td><input type="number" class="form-control form-control-sm" name="commission_silver" step="0.1" value="12"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning">Gold</span></td>
                                        <td><input type="number" class="form-control form-control-sm" name="commission_gold" step="0.1" value="10"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-info">Platinum</span></td>
                                        <td><input type="number" class="form-control form-control-sm" name="commission_platinum" step="0.1" value="8"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Commission Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Fleet Types Tab --}}
    <div class="tab-pane fade" id="fleet-types-tab" role="tabpanel">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Fleet Types</h6>
                <a href="{{ route('admin.fleet-types.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i> Manage Fleet Types
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Passengers</th>
                            <th>Fuel Category</th>
                            <th>Sort Order</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fleetTypes as $fleetType)
                            <tr>
                                <td class="fw-semibold">{{ $fleetType->name }}</td>
                                <td class="text-muted">{{ $fleetType->slug }}</td>
                                <td>{{ $fleetType->min_passengers }}-{{ $fleetType->max_passengers }}</td>
                                <td>{{ ucfirst($fleetType->fuel_category ?? '-') }}</td>
                                <td>{{ $fleetType->sort_order }}</td>
                                <td>
                                    @if($fleetType->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No fleet types configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Meet & Greet Tab --}}
    <div class="tab-pane fade" id="meet-greet" role="tabpanel">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-cone-striped text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3 fw-semibold">Coming Soon</h5>
                <p class="text-muted">Meet & Greet location management is under development.</p>
            </div>
        </div>
    </div>
</div>
@endsection
