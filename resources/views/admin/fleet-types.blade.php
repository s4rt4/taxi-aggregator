@extends('layouts.admin')
@section('title', 'Fleet Types')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Fleet Types</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Fleet Types</h1>
    <p class="text-muted mb-0">Manage vehicle categories available on the platform.</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Sort</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Passengers</th>
                    <th>Fuel Category</th>
                    <th>Description</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fleetTypes as $fleetType)
                    <tr>
                        <td class="text-muted">{{ $fleetType->sort_order }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($fleetType->icon)
                                    <i class="bi bi-{{ $fleetType->icon }} me-2 fs-5"></i>
                                @else
                                    <i class="bi bi-truck me-2 fs-5"></i>
                                @endif
                                <span class="fw-semibold">{{ $fleetType->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $fleetType->slug }}</td>
                        <td>{{ $fleetType->min_passengers }} - {{ $fleetType->max_passengers }}</td>
                        <td>{{ ucfirst($fleetType->fuel_category ?? '-') }}</td>
                        <td><small>{{ \Illuminate\Support\Str::limit($fleetType->description, 50) }}</small></td>
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
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-truck fs-2 d-block mb-2"></i>
                            No fleet types configured. Add fleet types to enable vehicle selection.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
