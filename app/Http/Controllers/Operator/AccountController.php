<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\OperatorContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $operator = $user->operator;
        $contacts = $operator?->contacts ?? collect();

        return view('operator.account.index', compact('operator', 'contacts'));
    }

    /**
     * Update company details tab.
     */
    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'business_type' => ['required', 'in:sole_trader,limited_company,partnership,llp'],
            'cab_operator_name' => ['required', 'string', 'max:255'],
            'legal_company_name' => ['nullable', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'vat_number' => ['nullable', 'string', 'max:20'],
        ]);

        $operator = $this->getOrCreateOperator();

        $operator->update([
            'business_type' => $validated['business_type'],
            'operator_name' => $validated['cab_operator_name'],
            'legal_company_name' => $validated['legal_company_name'] ?? null,
            'trading_name' => $validated['trading_name'] ?? null,
            'registration_number' => $validated['registration_number'] ?? null,
            'vat_number' => $validated['vat_number'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Company details updated successfully.');
    }

    /**
     * Update contact details tab.
     */
    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'office_email' => ['required', 'email', 'max:255'],
            'postcode' => ['required', 'string', 'max:10'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'county' => ['nullable', 'string', 'max:255'],
            'phone_country_code' => ['nullable', 'string', 'max:5'],
            'office_phone' => ['required', 'string', 'max:20'],
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        $operator = $this->getOrCreateOperator();

        $phone = trim(($validated['phone_country_code'] ?? '+44') . ' ' . $validated['office_phone']);

        $operator->update([
            'email' => $validated['office_email'],
            'postcode' => $validated['postcode'],
            'address_line_1' => $validated['address_line1'],
            'address_line_2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'county' => $validated['county'] ?? null,
            'phone' => $phone,
            'website' => $validated['website_url'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Contact details updated successfully.');
    }

    /**
     * Update authorised contacts tab.
     */
    public function updateAuthorisedContacts(Request $request)
    {
        $validated = $request->validate([
            'primary_name' => ['required', 'string', 'max:255'],
            'primary_email' => ['required', 'email', 'max:255'],
            'primary_phone' => ['required', 'string', 'max:20'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.email' => ['required', 'email', 'max:255'],
            'contacts.*.phone' => ['required', 'string', 'max:20'],
        ]);

        $operator = $this->getOrCreateOperator();

        // Update or create primary contact
        $operator->contacts()->updateOrCreate(
            ['type' => 'primary'],
            [
                'name' => $validated['primary_name'],
                'email' => $validated['primary_email'],
                'phone' => $validated['primary_phone'],
            ]
        );

        // Remove existing secondary contacts and recreate
        $operator->contacts()->where('type', 'secondary')->delete();

        if (!empty($validated['contacts'])) {
            foreach ($validated['contacts'] as $contact) {
                $operator->contacts()->create([
                    'type' => 'secondary',
                    'name' => $contact['name'],
                    'email' => $contact['email'],
                    'phone' => $contact['phone'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Authorised contacts updated successfully.');
    }

    /**
     * Update licence & fleet tab.
     */
    public function updateLicence(Request $request)
    {
        $validated = $request->validate([
            'dispatch_system' => ['nullable', 'string', 'max:50'],
            'licence_number' => ['required', 'string', 'max:50'],
            'licence_expiry' => ['required', 'date', 'after:today'],
            'licensing_authority' => ['required', 'string', 'max:255'],
            'fleet_size' => ['required', 'integer', 'min:1'],
            'operator_licence' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'liability_insurance' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'liability_expiry' => ['nullable', 'date'],
        ]);

        $operator = $this->getOrCreateOperator();

        $updateData = [
            'dispatch_system' => $validated['dispatch_system'],
            'licence_number' => $validated['licence_number'],
            'licence_expiry' => $validated['licence_expiry'],
            'licence_authority' => $validated['licensing_authority'],
            'fleet_size' => $validated['fleet_size'],
            'public_liability_expiry' => $validated['liability_expiry'],
        ];

        // Handle operator licence file upload
        if ($request->hasFile('operator_licence')) {
            $path = $request->file('operator_licence')->store('operator-licences', 'public');
            $updateData['operator_licence_file'] = $path;
        }

        // Handle public liability insurance file upload
        if ($request->hasFile('liability_insurance')) {
            $path = $request->file('liability_insurance')->store('liability-insurance', 'public');
            $updateData['public_liability_insurance_file'] = $path;
        }

        $operator->update($updateData);

        return redirect()->back()->with('success', 'Licence & fleet details updated successfully.');
    }

    /**
     * Update payment preferences tab.
     */
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'accepts_prepaid' => ['nullable', 'boolean'],
            'accepts_cash' => ['nullable', 'boolean'],
        ]);

        $operator = $this->getOrCreateOperator();

        $operator->update([
            'accepts_prepaid' => $validated['accepts_prepaid'] ?? false,
            'accepts_cash' => $validated['accepts_cash'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Payment preferences updated successfully.');
    }

    /**
     * Update password tab.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    /**
     * Update iCabbi integration settings.
     */
    public function updateIcabbi(Request $request)
    {
        $request->validate([
            'icabbi_enabled' => 'boolean',
            'icabbi_api_url' => 'nullable|url',
            'icabbi_app_key' => 'nullable|string|max:255',
            'icabbi_secret_key' => 'nullable|string|max:255',
            'icabbi_integration_name' => 'nullable|string|max:255',
        ]);

        $operator = $this->getOrCreateOperator();
        $operator->update($request->only([
            'icabbi_enabled', 'icabbi_api_url', 'icabbi_app_key', 'icabbi_secret_key', 'icabbi_integration_name'
        ]));

        return redirect()->back()->with('success', 'iCabbi settings updated.');
    }

    /**
     * Test iCabbi API connection.
     */
    public function testIcabbiConnection(Request $request)
    {
        $operator = auth()->user()->operator;
        if (!$operator || !$operator->usesIcabbi()) {
            return response()->json(['success' => false, 'message' => 'iCabbi not configured']);
        }

        try {
            $response = Http::withHeaders([
                'AppKey' => $operator->icabbi_app_key,
                'SecretKey' => $operator->icabbi_secret_key,
            ])->get(rtrim($operator->icabbi_api_url, '/') . '/status');

            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->successful() ? 'Connection successful!' : 'Connection failed: ' . $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get the authenticated user's operator, or create a skeleton one for new registrations.
     */
    protected function getOrCreateOperator(): Operator
    {
        $user = auth()->user();
        $operator = $user->operator;

        if (!$operator) {
            $operator = Operator::create([
                'user_id' => $user->id,
                'operator_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'postcode' => '',
                'address_line_1' => '',
                'city' => '',
                'licence_number' => '',
                'licence_authority' => '',
                'licence_expiry' => now()->addYear(),
                'status' => 'pending',
            ]);

            // Refresh the relationship
            $user->refresh();
        }

        return $operator;
    }
}
