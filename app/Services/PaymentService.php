<?php

namespace App\Services;

use Stripe\Customer;
use Stripe\Price;

/**
 * Service class for handling payment operations with Stripe
 */
class PaymentService
{
    /**
     * Create or find a Stripe customer by email
     */
    public function createOrFindCustomer(string $email, string $name, ?string $phone = null): string
    {
        // Search for existing customer
        $existingCustomers = Customer::all(['email' => $email]);
        
        if ($existingCustomers->data) {
            return $existingCustomers->data[0]->id;
        }

        // Create new customer
        $customer = Customer::create([
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'description' => 'Donor - Non-Profit Platform',
            'metadata' => [
                'source' => 'nonprofit_platform',
                'created_at' => now()->toIso8601String(),
            ],
        ]);

        return $customer->id;
    }

    /**
     * Get or create a Stripe Price object for recurring donations
     */
    public function getOrCreateDonationPrice(int $amount, string $currency, string $interval): string
    {
        // Search for existing price
        $existingPrices = Price::all([
            'unit_amount' => $amount,
            'currency' => $currency,
            'recurring[interval]' => $interval,
            'active' => true,
        ]);

        if ($existingPrices->data) {
            return $existingPrices->data[0]->id;
        }

        // Create new product and price
        $product = \Stripe\Product::create([
            'name' => "Recurring Donation - {$currency} {$amount}",
            'type' => 'service',
            'metadata' => [
                'donation_type' => 'recurring',
                'interval' => $interval,
            ],
        ]);

        $price = Price::create([
            'unit_amount' => $amount,
            'currency' => $currency,
            'recurring' => ['interval' => $interval],
            'product' => $product->id,
            'nickname' => "{$currency} " . number_format($amount / 100, 2) . " per {$interval}",
        ]);

        return $price->id;
    }

    /**
     * Refund a charge
     */
    public function refundCharge(string $chargeId, ?int $amount = null): \Stripe\Refund
    {
        $refundData = ['charge' => $chargeId];
        
        if ($amount) {
            $refundData['amount'] = $amount;
        }

        return \Stripe\Refund::create($refundData);
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): \Stripe\Subscription
    {
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        $subscription->cancel();
        
        return $subscription;
    }

    /**
     * Update subscription quantity/amount
     */
    public function updateSubscription(string $subscriptionId, int $newAmount): \Stripe\Subscription
    {
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        
        // Update the price
        $subscription->items[0]->price = $this->getOrCreateDonationPrice(
            $newAmount,
            $subscription->currency,
            $subscription->items->data[0]->plan->interval
        );
        
        $subscription->save();
        
        return $subscription;
    }
}
