<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    public function index()
    {
        $adminUsers = User::where('role', 'admin')
            ->with('adminRole')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.admin-users.index', compact('adminUsers'));
    }

    public function create()
    {
        $roles = AdminRole::all();
        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'admin_role_id' => ['required', 'exists:admin_roles,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin',
            'admin_role_id' => $request->admin_role_id,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user created successfully.');
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'admin', 404);
        $roles = AdminRole::all();
        return view('admin.admin-users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 404);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'admin_role_id' => ['required', 'exists:admin_roles,id'],
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'admin_role_id' => $request->admin_role_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => $request->password]);
        }

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'admin', 404);
        abort_if($user->isSuperAdmin(), 403, 'Cannot delete Super Admin.');
        abort_if($user->id === auth()->id(), 403, 'Cannot delete your own account.');

        $user->delete();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user removed.');
    }
}
