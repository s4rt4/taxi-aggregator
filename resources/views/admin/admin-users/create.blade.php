@extends('layouts.admin')
@section('title', 'Create Admin User')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.admin-users.index') }}">Admin Users</a></li>
    <li class="breadcrumb-item active">Create</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Create Admin User</h1>
    <p class="text-muted mb-0">Add a new administrator to the platform.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.admin-users.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_role_id" class="form-label fw-semibold">Role</label>
                        <select class="form-select @error('admin_role_id') is-invalid @enderror"
                                id="admin_role_id" name="admin_role_id" required>
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('admin_role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                    @if($role->description) - {{ $role->description }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('admin_role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Create Admin User
                        </button>
                        <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-1"></i> About Admin Users</h6>
                <p class="small text-muted mb-2">Admin users can only be created here by a Super Admin or a user with admin management permissions.</p>
                <p class="small text-muted mb-2">Each admin user must be assigned a role that determines what they can see and do in the admin panel.</p>
                <p class="small text-muted mb-0">The user will be able to log in immediately with the email and password you set.</p>
            </div>
        </div>
    </div>
</div>
@endsection
