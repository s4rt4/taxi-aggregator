<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminPermission;
use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = AdminRole::withCount('users')->get();
        $allPermissions = AdminPermission::ALL;
        return view('admin.admin-roles.index', compact('roles', 'allPermissions'));
    }

    public function create()
    {
        $allPermissions = AdminPermission::ALL;
        $permissionGroups = AdminPermission::GROUPS;
        return view('admin.admin-roles.create', compact('allPermissions', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'in:' . implode(',', array_keys(AdminPermission::ALL))],
        ]);

        $slug = Str::slug($request->name);

        // Ensure unique slug
        if (AdminRole::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'A role with a similar name already exists.'])->withInput();
        }

        AdminRole::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'permissions' => $request->permissions,
            'is_system' => false,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(AdminRole $role)
    {
        $allPermissions = AdminPermission::ALL;
        $permissionGroups = AdminPermission::GROUPS;
        return view('admin.admin-roles.edit', compact('role', 'allPermissions', 'permissionGroups'));
    }

    public function update(Request $request, AdminRole $role)
    {
        // Cannot edit super-admin permissions
        abort_if($role->slug === 'super-admin', 403, 'Cannot modify Super Admin role.');

        $rules = [
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'in:' . implode(',', array_keys(AdminPermission::ALL))],
        ];

        // System roles can't change name
        if (!$role->is_system) {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules);

        $data = [
            'description' => $request->description,
            'permissions' => $request->permissions,
        ];

        if (!$role->is_system) {
            $data['name'] = $request->name;
        }

        $role->update($data);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(AdminRole $role)
    {
        abort_if($role->is_system, 403, 'Cannot delete system roles.');
        abort_if($role->users()->count() > 0, 403, 'Cannot delete a role that has assigned users. Reassign them first.');

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
