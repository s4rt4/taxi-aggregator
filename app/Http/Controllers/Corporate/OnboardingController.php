<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate;
use App\Models\CorporateUser;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If user already has an active corporate profile, send them to dashboard
        if ($user->activeCorporate()) {
            return redirect()->route('corporate.dashboard');
        }

        // Check if they have a pending corporate
        $corporateUser = $user->corporateUsers()->latest()->first();
        if ($corporateUser && $corporateUser->corporate) {
            if ($corporateUser->corporate->status === 'approved') {
                return redirect()->route('corporate.dashboard');
            }

            // If they have a corporate that is still pending, show complete page
            return redirect()->route('corporate.onboarding.complete');
        }

        // Otherwise start at step 1
        return redirect()->route('corporate.onboarding.step', 1);
    }

    public function step(int $step, Request $request)
    {
        if ($step < 1 || $step > 3) {
            return redirect()->route('corporate.onboarding');
        }

        $user = auth()->user();
        $data = session('corporate_onboarding', []);
        $totalSteps = 3;

        return view('corporate.onboarding.step' . $step, compact('data', 'step', 'totalSteps'));
    }

    public function save(int $step, Request $request)
    {
        if ($step < 1 || $step > 3) {
            return redirect()->route('corporate.onboarding');
        }

        $data = session('corporate_onboarding', []);

        if ($step === 1) {
            $validated = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'legal_name' => ['nullable', 'string', 'max:255'],
                'business_type' => ['required', 'in:limited_company,sole_trader,partnership,llp,public_sector,charity'],
                'registration_number' => ['nullable', 'string', 'max:50'],
                'vat_number' => ['nullable', 'string', 'max:50'],
            ]);

            $data = array_merge($data, $validated);
            session(['corporate_onboarding' => $data]);

            return redirect()->route('corporate.onboarding.step', 2);
        }

        if ($step === 2) {
            $validated = $request->validate([
                'billing_email' => ['required', 'email', 'max:255'],
                'billing_phone' => ['required', 'string', 'max:20'],
                'billing_address_line_1' => ['required', 'string', 'max:255'],
                'billing_address_line_2' => ['nullable', 'string', 'max:255'],
                'billing_city' => ['required', 'string', 'max:255'],
                'billing_postcode' => ['required', 'string', 'max:10'],
                'billing_county' => ['nullable', 'string', 'max:255'],
            ]);

            $data = array_merge($data, $validated);
            session(['corporate_onboarding' => $data]);

            return redirect()->route('corporate.onboarding.step', 3);
        }

        if ($step === 3) {
            $validated = $request->validate([
                'monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
                'invoicing_frequency' => ['required', 'in:weekly,monthly'],
                'payment_terms_days' => ['required', 'integer', 'min:1', 'max:90'],
            ]);

            $data = array_merge($data, $validated);

            // Validate that step 1+2 data exists
            if (empty($data['company_name']) || empty($data['billing_email'])) {
                return redirect()->route('corporate.onboarding.step', 1)
                    ->with('error', 'Please complete all onboarding steps.');
            }

            // Create the corporate
            $user = auth()->user();
            $corporate = Corporate::create([
                'company_name' => $data['company_name'],
                'legal_name' => $data['legal_name'] ?? null,
                'business_type' => $data['business_type'],
                'registration_number' => $data['registration_number'] ?? null,
                'vat_number' => $data['vat_number'] ?? null,
                'billing_email' => $data['billing_email'],
                'billing_phone' => $data['billing_phone'],
                'billing_address_line_1' => $data['billing_address_line_1'],
                'billing_address_line_2' => $data['billing_address_line_2'] ?? null,
                'billing_city' => $data['billing_city'],
                'billing_postcode' => $data['billing_postcode'],
                'billing_county' => $data['billing_county'] ?? null,
                'monthly_budget' => $data['monthly_budget'] ?? null,
                'invoicing_frequency' => $data['invoicing_frequency'],
                'payment_terms_days' => $data['payment_terms_days'],
                'status' => 'pending',
            ]);

            CorporateUser::create([
                'corporate_id' => $corporate->id,
                'user_id' => $user->id,
                'corporate_role' => 'super_user',
                'is_active' => true,
            ]);

            session()->forget('corporate_onboarding');

            return redirect()->route('corporate.onboarding.complete');
        }

        return redirect()->route('corporate.onboarding');
    }

    public function complete()
    {
        $user = auth()->user();
        $corporateUser = $user->corporateUsers()->latest()->first();
        $corporate = $corporateUser?->corporate;

        return view('corporate.onboarding.complete', compact('corporate'));
    }
}
