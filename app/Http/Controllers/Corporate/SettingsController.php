<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected function ensureSuperUser()
    {
        $user = auth()->user();
        if (!$user->isCorporateSuperUser()) {
            abort(403, 'Only super users can change settings.');
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

        return view('corporate.settings.index', compact('corporate'));
    }

    public function update(Request $request)
    {
        $corporate = $this->ensureSuperUser();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'business_type' => ['required', 'in:limited_company,sole_trader,partnership,llp,public_sector,charity'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['required', 'string', 'max:20'],
            'billing_address_line_1' => ['required', 'string', 'max:255'],
            'billing_address_line_2' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['required', 'string', 'max:255'],
            'billing_postcode' => ['required', 'string', 'max:10'],
            'billing_county' => ['nullable', 'string', 'max:255'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'invoicing_frequency' => ['required', 'in:weekly,monthly'],
            'payment_terms_days' => ['required', 'integer', 'min:1', 'max:90'],
        ]);

        $corporate->update($validated);

        return redirect()->route('corporate.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
