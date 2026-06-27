<?php

namespace App\Services\Payment;

use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Stripe Payment Gateway Service
 * 
 * Official SDK integration for Stripe (Global payments).
 * Supports Payment Intents, refunds, webhooks, and subscriptions.
 */
class StripeService
{
    protected StripeClient $client;
    protected string $secretKey;
    protected string $publishableKey;
    protected string $webhookSecret;
    protected string $currency;

    public function __construct()
    {
        $this->secretKey = config('payment.gateways.stripe.secret');
        $this->publishableKey = config('payment.gateways.stripe.key');
        $this->webhookSecret = config('payment.gateways.stripe.webhook_secret');
        $this->currency = config('payment.gateways.stripe.currency', 'USD');

        $this->client = new StripeClient($this->secretKey);
    }

    /**
     * Create a Payment Intent for one-time payments
     */
    public function createIntent(array $data): array
    {
        try {
            $amount = (int) ($data['amount'] * 100); // Convert to cents

            $intent = $this->client->paymentIntents->create([
                'amount' => $amount,
                'currency' => $data['currency'] ?? $this->currency,
                'payment_method_types' => $data['payment_methods'] ?? ['card'],
                'metadata' => [
                    'order_id' => $data['order_id'] ?? '',
                    'user_id' => $data['user_id'] ?? '',
                    'type' => $data['type'] ?? 'donation',
                ],
                'description' => $data['description'] ?? 'Payment',
                'receipt_email' => $data['email'] ?? null,
                'automatic_payment_methods' => [
                    'enabled' => $data['auto_methods'] ?? true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
                'amount' => $amount / 100,
                'currency' => $intent->currency,
                'status' => $intent->status,
                'next_action' => $intent->next_action,
            ];

        } catch (Exception $e) {
            Log::error('Stripe Payment Intent Creation Failed', [
                'order_id' => $data['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment creation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a Payment Intent status
     */
    public function verifyPaymentIntent(array $data): array
    {
        try {
            $intentId = $data['payment_intent_id'] ?? $data['intent_id'] ?? null;
            
            if (!$intentId) {
                return [
                    'success' => false,
                    'message' => 'Payment intent ID required',
                ];
            }

            $intent = $this->client->paymentIntents->retrieve($intentId);

            return [
                'success' => in_array($intent->status, ['succeeded', 'processing']),
                'status' => $intent->status,
                'amount' => $intent->amount / 100,
                'currency' => $intent->currency,
                'payment_method' => $intent->payment_method,
                'receipt_url' => $intent->receipt_url,
                'metadata' => $intent->metadata,
                'created_at' => date('Y-m-d H:i:s', $intent->created),
            ];

        } catch (Exception $e) {
            Log::error('Stripe Payment Intent Verification Failed', [
                'intent_id' => $data['payment_intent_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process a refund
     */
    public function refund(string $paymentIntentId, array $data): array
    {
        try {
            $refundData = [
                'payment_intent' => $paymentIntentId,
            ];

            // Partial refund if amount specified
            if (!empty($data['amount'])) {
                $refundData['amount'] = (int) ($data['amount'] * 100);
            }

            if (!empty($data['reason'])) {
                $refundData['reason'] = $data['reason'];
            }

            if (!empty($data['metadata'])) {
                $refundData['metadata'] = $data['metadata'];
            }

            $refund = $this->client->refunds->create($refundData);

            return [
                'success' => $refund->status === 'succeeded',
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
                'status' => $refund->status,
                'reason' => $refund->reason ?? null,
                'created_at' => date('Y-m-d H:i:s', $refund->created),
            ];

        } catch (Exception $e) {
            Log::error('Stripe Refund Failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret
            );

            return [
                'success' => true,
                'event_type' => $event->type,
                'event_data' => $event->data->object,
            ];

        } catch (Exception $e) {
            Log::error('Stripe Webhook Verification Failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Invalid signature',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a customer for recurring payments
     */
    public function createCustomer(array $data): array
    {
        try {
            $customer = $this->client->customers->create([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'description' => $data['description'] ?? 'Non-profit donor',
                'metadata' => $data['metadata'] ?? [],
            ]);

            return [
                'success' => true,
                'customer_id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ];

        } catch (Exception $e) {
            Log::error('Stripe Customer Creation Failed', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Customer creation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a subscription for recurring donations
     */
    public function createSubscription(array $data): array
    {
        try {
            $subscription = $this->client->subscriptions->create([
                'customer' => $data['customer_id'],
                'items' => [[
                    'price' => $data['price_id'],
                    'quantity' => $data['quantity'] ?? 1,
                ]],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => $data['metadata'] ?? [],
            ]);

            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
                'client_secret' => $subscription->latest_invoice->payment_intent->client_secret ?? null,
            ];

        } catch (Exception $e) {
            Log::error('Stripe Subscription Creation Failed', [
                'customer_id' => $data['customer_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Subscription creation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get publishable key for frontend
     */
    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }
}
