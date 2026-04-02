@extends('layouts.admin')
@section('title', 'Create Custom Role')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
    <li class="breadcrumb-item active">Create</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Create Custom Role</h1>
    <p class="text-muted mb-0">Define a new role with specific permissions.</p>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Role Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Marketing">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3" placeholder="What is this role for?">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Create Role
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">Permissions</h6>
                </div>
                <div class="card-body">
                    @error('permissions')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    @foreach($permissionGroups as $group => $permissions)
                        <div class="mb-4">
                            <h6 class="fw-semibold text-primary mb-2">
                                <i class="bi bi-folder me-1"></i> {{ $group }}
                            </h6>
                            <div class="row">
                                @foreach($permissions as $permKey)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="permissions[]" value="{{ $permKey }}"
                                                   id="perm_{{ $permKey }}"
                                                   {{ in_array($permKey, old('permissions', [])) ? 'checked' : '' }}>
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
@endsection
