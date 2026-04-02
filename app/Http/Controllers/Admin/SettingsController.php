<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Settings;
use App\Http\Controllers\Controller;
use App\Models\FleetType;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $fleetTypes = FleetType::ordered()->get();
        $groups = SiteSetting::all()->groupBy('group');

        return view('admin.settings', compact('fleetTypes', 'groups'));
    }

    public function update(Request $request)
    {
        foreach ($request->input('settings', []) as $key => $value) {
            SiteSetting::where('key', $key)->update(['value' => $value]);
        }

        Settings::clearCache();

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    public function fleetTypes()
    {
        $fleetTypes = FleetType::ordered()->get();

        return view('admin.fleet-types', compact('fleetTypes'));
    }
}
