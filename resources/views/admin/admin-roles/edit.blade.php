@extends('layouts.admin')
@section('title', 'Edit Role')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
    <li class="breadcrumb-item active">Edit {{ $role->name }}</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Edit Role: {{ $role->name }}</h1>
    <p class="text-muted mb-0">
        @if($role->is_system)
            System role &mdash; name cannot be changed, but permissions can be adjusted.
        @else
            Update this custom role's name, description, and permissions.
        @endif
    </p>
</div>

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Role Name</label>
                        @if($role->is_system)
                            <input type="text" class="form-control" value="{{ $role->name }}" disabled>
                            <div class="form-text">System role names cannot be changed.</div>
                        @else
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small">{{ $role->users()->count() }} {{ Str::plural('user', $role->users()->count()) }} assigned to this role</span>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Permissions</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">Deselect All</button>
                    </div>
                </div>
                <div class="card-body">
                    @error('permissions')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    @php
                        $currentPermissions = old('permissions', $role->permissions ?? []);
                    @endphp

                    @foreach($permissionGroups as $group => $permissions)
                        <div class="mb-4">
                            <h6 class="fw-semibold text-primary mb-2">
                                <i class="bi bi-folder me-1"></i> {{ $group }}
                            </h6>
                            <div class="row">
                                @foreach($permissions as $permKey)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input perm-checkbox" type="checkbox"
                                                   name="permissions[]" value="{{ $permKey }}"
                                                   id="perm_{{ $permKey }}"
                                                   {{ in_array($permKey, $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permKey }}">
                                                {{ $allPermissions[$permKey] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function toggleAll(checked) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
}
</script>
@endpush
@endsection
