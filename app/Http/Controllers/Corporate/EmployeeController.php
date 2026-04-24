<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    protected function ensureSuperUser()
    {
        $user = auth()->user();
        if (!$user->isCorporateSuperUser()) {
            abort(403, 'Only super users can manage employees.');
        }

        $corporate = $user->activeCorporate();
        if (!$corporate || $corporate->status !== 'approved') {
            abort(403, 'Your corporate account is not yet active.');
        }

        return $corporate;
    }

    public function index()
    {
        $corporate = $this->ensureSuperUser();

        $employees = $corporate->corporateUsers()
            ->with('user')
            ->orderByDesc('is_active')
            ->orderBy('corporate_role')
            ->get();

        return view('corporate.employees.index', compact('corporate', 'employees'));
    }

    public function create()
    {
        $corporate = $this->ensureSuperUser();

        return view('corporate.employees.create', compact('corporate'));
    }

    public function store(Request $request)
    {
        $corporate = $this->ensureSuperUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'employee_id' => ['nullable', 'string', 'max:100'],
            'cost_centre' => ['nullable', 'string', 'max:100'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'requires_approval' => ['nullable', 'boolean'],
            'corporate_role' => ['required', 'in:user,super_user'],
        ]);

        // Check if user already exists
        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            // User exists - ensure they're not already linked
            $existing = CorporateUser::where('corporate_id', $corporate->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                return back()->withErrors(['email' => 'This user is already an employee of this company.']);
            }

            // Only add if they are a corporate user (role)
            if (!in_array($user->role, ['corporate'])) {
                return back()->withErrors(['email' => 'This email belongs to an account that cannot be added as a corporate employee.']);
            }
        } else {
            // Create a new corporate user with a random password (they'll need to reset)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role' => 'corporate',
                'password' => Str::random(16),
                'is_active' => true,
            ]);
        }

        CorporateUser::create([
            'corporate_id' => $corporate->id,
            'user_id' => $user->id,
            'corporate_role' => $validated['corporate_role'],
            'employee_id' => $validated['employee_id'] ?? null,
            'cost_centre' => $validated['cost_centre'] ?? null,
            'monthly_budget' => $validated['monthly_budget'] ?? null,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => true,
        ]);

        return redirect()->route('corporate.employees.index')
            ->with('success', "Employee {$user->name} added successfully. They will need to reset their password to log in.");
    }

    public function edit(CorporateUser $employee)
    {
        $corporate = $this->ensureSuperUser();

        abort_unless($employee->corporate_id === $corporate->id, 403);

        $employee->load('user');

        return view('corporate.employees.edit', compact('corporate', 'employee'));
    }

    public function update(Request $request, CorporateUser $employee)
    {
        $corporate = $this->ensureSuperUser();

        abort_unless($employee->corporate_id === $corporate->id, 403);

        $validated = $request->validate([
            'employee_id' => ['nullable', 'string', 'max:100'],
            'cost_centre' => ['nullable', 'string', 'max:100'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'requires_approval' => ['nullable', 'boolean'],
            'corporate_role' => ['required', 'in:user,super_user'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $employee->update([
            'employee_id' => $validated['employee_id'] ?? null,
            'cost_centre' => $validated['cost_centre'] ?? null,
            'monthly_budget' => $validated['monthly_budget'] ?? null,
            'requires_approval' => $request->boolean('requires_approval'),
            'corporate_role' => $validated['corporate_role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('corporate.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function show(CorporateUser $employee)
    {
        $corporate = $this->ensureSuperUser();
        abort_unless($employee->corporate_id === $corporate->id, 403);
        $employee->load('user');

        return view('corporate.employees.show', compact('corporate', 'employee'));
    }

    public function destroy(CorporateUser $employee)
    {
        $corporate = $this->ensureSuperUser();

        abort_unless($employee->corporate_id === $corporate->id, 403);

        // Do not allow removal of the last super user
        if ($employee->isSuperUser()) {
            $superUserCount = $corporate->corporateUsers()
                ->where('corporate_role', 'super_user')
                ->where('is_active', true)
                ->count();

            if ($superUserCount <= 1) {
                return back()->with('error', 'Cannot remove the last super user. Promote another user first.');
            }
        }

        $employee->delete();

        return redirect()->route('corporate.employees.index')
            ->with('success', 'Employee removed successfully.');
    }
}
