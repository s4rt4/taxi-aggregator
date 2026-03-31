@extends('layouts.admin')
@section('title', 'Users')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Users</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Users</h1>
    <p class="text-muted mb-0">Manage all registered users on the platform.</p>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach(['passenger', 'operator', 'driver', 'admin'] as $role)
                        <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Users Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-muted">{{ $user->id }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleColors = ['admin' => 'danger', 'operator' => 'primary', 'driver' => 'info', 'passenger' => 'success'];
                            @endphp
                            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="d-inline">
                                    @csrf
                                    @if($user->is_active)
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Deactivate"
                                            onclick="return confirm('Deactivate this user?')">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activate"
                                            onclick="return confirm('Activate this user?')">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
