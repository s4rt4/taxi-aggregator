<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FleetType;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $fleetTypes = FleetType::ordered()->get();

        return view('admin.settings', compact('fleetTypes'));
    }

    public function update(Request $request)
    {
        // Placeholder for settings update logic
        // Will be expanded as settings system is built out

        return back()->with('success', 'Settings updated successfully.');
    }

    public function fleetTypes()
    {
        $fleetTypes = FleetType::ordered()->get();

        return view('admin.fleet-types', compact('fleetTypes'));
    }
}
