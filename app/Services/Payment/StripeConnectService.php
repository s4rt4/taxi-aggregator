<?php

namespace App\Services\Payment;

use App\Models\Operator;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Transfer;

class StripeConnectService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Connect Express account for an operator.
     */
    public function createAccount(Operator $operator): string
    {
        $account = Account::create([
            'type' => 'express',
            'country' => 'GB',
            'email' => $operator->email,
            'capabilities' => [
                'transfers' => ['requested' => true],
            ],
            'business_type' => $this->mapBusinessType($operator->business_type),
            'business_profile' => [
                'mcc' => '4121', // Taxicabs/Limousines
                'product_description' => 'Private hire taxi operator',
                'url' => $operator->website,
            ],
            'metadata' => [
                'operator_id' => $operator->id,
                'operator_name' => $operator->operator_name,
            ],
        ]);

        $operator->update([
            'stripe_account_id' => $account->id,
            'stripe_status' => 'pending',
        ]);

        return $account->id;
    }

    /**
     * Generate onboarding link for operator to complete Stripe KYC.
     */
    public function createOnboardingLink(Operator $operator): string
    {
        if (!$operator->stripe_account_id) {
            $this->createAccount($operator);
        }

        $link = AccountLink::create([
            'account' => $operator->stripe_account_id,
            'refresh_url' => route('operator.stripe.refresh'),
            'return_url' => route('operator.stripe.return'),
            'type' => 'account_onboarding',
        ]);

        return $link->url;
    }

    /**
     * Check if operator's Stripe account is fully onboarded.
     */
    public function checkAccountStatus(Operator $operator): string
    {
        if (!$operator->stripe_account_id) {
            return 'not_started';
        }

        $account = Account::retrieve($operator->stripe_account_id);

        if ($account->charges_enabled && $account->payouts_enabled) {
            $operator->update(['stripe_status' => 'active']);
            return 'active';
        }

        if ($account->requirements->currently_due && count($account->requirements->currently_due) > 0) {
            $operator->update(['stripe_status' => 'restricted']);
            return 'restricted';
        }

        $operator->update(['stripe_status' => 'pending']);
        return 'pending';
    }

    /**
     * Transfer funds to operator's connected account.
     */
    public function transferToOperator(Operator $operator, float $amount, string $description, ?string $transferGroup = null): ?Transfer
    {
        if (!$operator->stripe_account_id || $operator->stripe_status !== 'active') {
            return null;
        }

        return Transfer::create([
            'amount' => (int) ($amount * 100), // pence
            'currency' => 'gbp',
            'destination' => $operator->stripe_account_id,
            'description' => $description,
            'transfer_group' => $transferGroup,
            'metadata' => [
                'operator_id' => $operator->id,
            ],
        ]);
    }

    protected function mapBusinessType(string $type): string
    {
        return match ($type) {
            'sole_trader' => 'individual',
            'limited_company' => 'company',
            'partnership' => 'company',
            'llp' => 'company',
            default => 'individual',
        };
    }
}
