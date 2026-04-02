@extends('layouts.admin')
@section('title', 'Roles & Permissions')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Roles & Permissions</li>
</ol>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Roles & Permissions</h1>
        <p class="text-muted mb-0">Manage admin roles and what each role can access.</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create Custom Role
    </a>
</div>

<div class="row g-4">
    @foreach($roles as $role)
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">
                            {{ $role->name }}
                            @if($role->is_system)
                                <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">System</span>
                            @endif
                        </h5>
                        <p class="text-muted small mb-0">{{ $role->description }}</p>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
                </div>

                <div class="mb-3">
                    @if($role->slug === 'super-admin')
                        <span class="badge bg-danger-subtle text-danger">All Permissions</span>
                    @else
                        <span class="text-muted small">{{ count($role->permissions ?? []) }} permissions</span>
                        <div class="mt-2 d-flex flex-wrap gap-1">
                            @foreach(($role->permissions ?? []) as $perm)
                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ $allPermissions[$perm] ?? $perm }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    @if($role->slug !== 'super-admin')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Permissions
                        </a>
                    @endif
                    @if(!$role->is_system)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Delete this role? This cannot be undone.')"
                                {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
