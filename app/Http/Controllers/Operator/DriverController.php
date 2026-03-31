<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $operator = auth()->user()->operator;

        $drivers = $operator
            ? $operator->drivers()->latest()->paginate(15)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        return view('operator.drivers.index', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'licence_number' => ['required', 'string', 'max:50'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'vehicle_make' => ['nullable', 'string', 'max:100'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_max_passengers' => ['nullable', 'integer', 'min:1', 'max:16'],
            'registration_plate' => ['nullable', 'string', 'max:15'],
            'dbs_status' => ['nullable', 'in:pending,clear,flagged,expired,not_checked'],
            'dbs_expiry' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $operator = auth()->user()->operator;

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile before adding drivers.');
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('driver-photos', 'public');
        }

        $validated['operator_id'] = $operator->id;
        $validated['is_active'] = true;

        Driver::create($validated);

        return redirect()->route('operator.drivers.index')->with('success', 'Driver added successfully.');
    }

    public function update(Request $request, Driver $driver)
    {
        // Authorize: driver must belong to the authenticated user's operator
        $operator = auth()->user()->operator;

        if (!$operator || $driver->operator_id !== $operator->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'licence_number' => ['required', 'string', 'max:50'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'vehicle_make' => ['nullable', 'string', 'max:100'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_max_passengers' => ['nullable', 'integer', 'min:1', 'max:16'],
            'registration_plate' => ['nullable', 'string', 'max:15'],
            'dbs_status' => ['nullable', 'in:pending,clear,flagged,expired,not_checked'],
            'dbs_expiry' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('driver-photos', 'public');
        }

        $driver->update($validated);

        return redirect()->route('operator.drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        // Authorize: driver must belong to the authenticated user's operator
        $operator = auth()->user()->operator;

        if (!$operator || $driver->operator_id !== $operator->id) {
            abort(403, 'Unauthorized action.');
        }

        $driver->delete(); // Soft delete

        return redirect()->route('operator.drivers.index')->with('success', 'Driver removed successfully.');
    }
}
