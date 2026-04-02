@extends('layouts.admin')
@section('title', 'Admin Users')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Admin Users</li>
</ol>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Admin Users</h1>
        <p class="text-muted mb-0">Manage administrator accounts and their roles.</p>
    </div>
    @if(auth()->user()->hasAdminPermission('admin-users.manage'))
    <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create Admin User
    </a>
    @endif
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    @if(auth()->user()->hasAdminPermission('admin-users.manage'))
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($adminUsers as $adminUser)
                    <tr>
                        <td class="fw-semibold">{{ $adminUser->name }}</td>
                        <td>{{ $adminUser->email }}</td>
                        <td>
                            @if($adminUser->adminRole)
                                @php
                                    $roleBadge = match($adminUser->adminRole->slug) {
                                        'super-admin' => 'danger',
                                        'admin' => 'primary',
                                        'finance' => 'warning',
                                        'support' => 'info',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $roleBadge }}">{{ $adminUser->adminRole->name }}</span>
                            @else
                                <span class="badge bg-secondary">No Role</span>
                            @endif
                        </td>
                        <td>
                            @if($adminUser->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $adminUser->created_at->format('d M Y') }}</td>
                        @if(auth()->user()->hasAdminPermission('admin-users.manage'))
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.admin-users.edit', $adminUser) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$adminUser->isSuperAdmin() && $adminUser->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.admin-users.destroy', $adminUser) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this admin user?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-person-badge fs-2 d-block mb-2"></i>
                            No admin users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($adminUsers->hasPages())
        <div class="card-footer">
            {{ $adminUsers->links() }}
        </div>
    @endif
</div>
@endsection
