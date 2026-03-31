<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            default => view('dashboard.passenger'),
        };
    }

    // Admin dashboard now handled by App\Http\Controllers\Admin\DashboardController
}
