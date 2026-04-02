@extends('layouts.admin')
@section('title', 'Edit Admin User')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.admin-users.index') }}">Admin Users</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Edit Admin User</h1>
    <p class="text-muted mb-0">Update details for {{ $user->name }}.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.admin-users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_role_id" class="form-label fw-semibold">Role</label>
                        <select class="form-select @error('admin_role_id') is-invalid @enderror"
                                id="admin_role_id" name="admin_role_id" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('admin_role_id', $user->admin_role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('admin_role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-semibold mb-3">Reset Password (optional)</h6>
                    <p class="text-muted small">Leave blank to keep the current password.</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
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
                <h6 class="fw-semibold mb-3">User Details</h6>
                <dl class="small mb-0">
                    <dt class="text-muted">Created</dt>
                    <dd>{{ $user->created_at->format('d M Y H:i') }}</dd>
                    <dt class="text-muted">Last Updated</dt>
                    <dd>{{ $user->updated_at->format('d M Y H:i') }}</dd>
                    <dt class="text-muted">Status</dt>
                    <dd>
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
        <div class="card mt-3 border-danger">
            <div class="card-body">
                <h6 class="fw-semibold text-danger mb-3">Danger Zone</h6>
                <p class="small text-muted mb-3">Permanently remove this admin user. This action cannot be undone.</p>
                <form method="POST" action="{{ route('admin.admin-users.destroy', $user) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to permanently delete this admin user?')">
                        <i class="bi bi-trash me-1"></i> Delete Admin User
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
