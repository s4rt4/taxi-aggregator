<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Services\Payment\StripeConnectService;

class StripeConnectController extends Controller
{
    public function setup(StripeConnectService $stripeConnect)
    {
        $operator = auth()->user()->operator;

        if (!$operator) {
            return redirect()->route('operator.onboarding');
        }

        try {
            $url = $stripeConnect->createOnboardingLink($operator);
            return redirect($url);
        } catch (\Exception $e) {
            return redirect()->route('operator.account.index')
                ->with('error', 'Unable to connect to Stripe: ' . $e->getMessage());
        }
    }

    public function return(StripeConnectService $stripeConnect)
    {
        $operator = auth()->user()->operator;
        $status = $stripeConnect->checkAccountStatus($operator);

        return redirect()->route('operator.account.index')
            ->with('success', match ($status) {
                'active' => 'Stripe account connected successfully! You can now receive payouts.',
                'restricted' => 'Stripe account setup incomplete. Please complete the remaining requirements.',
                default => 'Stripe account setup in progress. We\'ll notify you when it\'s ready.',
            });
    }

    public function refresh()
    {
        return redirect()->route('operator.stripe.setup');
    }

    public function dashboard()
    {
        $operator = auth()->user()->operator;

        if (!$operator || !$operator->stripe_account_id) {
            return redirect()->route('operator.stripe.setup');
        }

        // Create a Stripe login link for the Express dashboard
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $link = \Stripe\Account::createLoginLink($operator->stripe_account_id);
            return redirect($link->url);
        } catch (\Exception $e) {
            return redirect()->route('operator.account.index')
                ->with('error', 'Unable to access Stripe dashboard.');
        }
    }
}
