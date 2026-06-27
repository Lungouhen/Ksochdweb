<?php

namespace App\Services\Payment;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Razorpay Payment Gateway Service
 * 
 * Official SDK integration for Razorpay (India's leading payment gateway).
 * Supports payments, subscriptions, refunds, and webhooks.
 */
class RazorpayService
{
    protected Api $api;
    protected string $keyId;
    protected string $keySecret;
    protected string $webhookSecret;
    protected string $currency;

    public function __construct()
    {
        $this->keyId = config('payment.gateways.razorpay.key_id');
        $this->keySecret = config('payment.gateways.razorpay.key_secret');
        $this->webhookSecret = config('payment.gateways.razorpay.webhook_secret');
        $this->currency = config('payment.gateways.razorpay.currency', 'INR');

        // Initialize Razorpay API client
        $this->api = new Api($this->keyId, $this->keySecret);
    }

    /**
     * Create an order for payment
     * 
     * @param array $data Order data including amount, currency, receipt ID
     * @return array Order creation response
     */
    public function createOrder(array $data): array
    {
        try {
            $orderData = [
                'amount' => (int) ($data['amount'] * 100), // Amount in paise
                'currency' => $data['currency'] ?? $this->currency,
                'receipt' => $data['receipt_id'] ?? 'receipt_' . time(),
                'notes' => $data['notes'] ?? [],
            ];

            if (!empty($data['user_id'])) {
                $orderData['customer_id'] = $data['user_id'];
            }

            $order = $this->api->order->create($orderData);

            return [
                'success' => true,
                'order_id' => $order->id,
                'amount' => $order->amount / 100,
                'currency' => $order->currency,
                'receipt' => $order->receipt,
                'status' => $order->status,
                'razorpay_order_id' => $order->id,
            ];

        } catch (Exception $e) {
            Log::error('Razorpay Order Creation Failed', [
                'receipt_id' => $data['receipt_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment signature after checkout
     * 
     * @param string $orderId Razorpay Order ID
     * @param string $paymentId Razorpay Payment ID
     * @param string $signature Razorpay Signature
     * @return bool Verification result
     */
    public function verifyPayment(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);
            
            return true;

        } catch (Exception $e) {
            Log::error('Razorpay Payment Verification Failed', [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Fetch payment details by ID
     */
    public function fetchPayment(string $paymentId): array
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'amount' => $payment->amount / 100,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'method' => $payment->method ?? null,
                'bank' => $payment->bank ?? null,
                'wallet' => $payment->wallet ?? null,
                'vpa' => $payment->vpa ?? null,
                'email' => $payment->email ?? null,
                'contact' => $payment->contact ?? null,
                'notes' => $payment->notes ?? [],
                'created_at' => $payment->created_at,
            ];

        } catch (Exception $e) {
            Log::error('Razorpay Payment Fetch Failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment fetch failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process refund for a payment
     */
    public function refund(string $paymentId, array $data = []): array
    {
        try {
            $refundData = [];

            if (!empty($data['amount'])) {
                $refundData['amount'] = (int) ($data['amount'] * 100); // Partial refund in paise
            }

            if (!empty($data['speed'])) {
                $refundData['speed'] = $data['speed']; // 'optimum' or 'instant'
            }

            if (!empty($data['notes'])) {
                $refundData['notes'] = $data['notes'];
            }

            $refund = $this->api->payment->fetch($paymentId)->refund($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
                'status' => $refund->status,
                'reason' => $refund->reason ?? null,
                'receipt' => $refund->receipt ?? null,
            ];

        } catch (Exception $e) {
            Log::error('Razorpay Refund Failed', [
                'payment_id' => $paymentId,
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
     * Create customer for recurring payments
     */
    public function createCustomer(array $data): array
    {
        try {
            $customerData = [
                'name' => $data['name'] ?? 'Customer',
                'email' => $data['email'] ?? null,
                'contact' => $data['contact'] ?? null,
                'notes' => $data['notes'] ?? [],
            ];

            $customer = $this->api->customer->create($customerData);

            return [
                'success' => true,
                'customer_id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'contact' => $customer->contact,
            ];

        } catch (Exception $e) {
            Log::error('Razorpay Customer Creation Failed', [
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
     * Create subscription for recurring donations
     */
    public function createSubscription(array $data): array
    {
        try {
            $subscriptionData = [
                'plan_id' => $data['plan_id'],
                'customer_id' => $data['customer_id'],
                'total_count' => $data['total_count'] ?? 12,
                'quantity' => $data['quantity'] ?? 1,
                'notes' => $data['notes'] ?? [],
                'offer_id' => $data['offer_id'] ?? null,
            ];

            $subscription = $this->api->subscription->create($subscriptionData);

            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'current_start' => $subscription->current_start,
                'current_end' => $subscription->current_end,
                'short_url' => $subscription->short_url ?? null,
            ];

        } catch (Exception $e) {
            Log::error('Razorpay Subscription Creation Failed', [
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
     * Verify webhook signature
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        try {
            $this->api->utility->verifyWebhookSignature($payload, $signature, $this->webhookSecret);
            return true;

        } catch (Exception $e) {
            Log::error('Razorpay Webhook Verification Failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate checkout form HTML/JS
     */
    public function generateCheckoutForm(array $options): string
    {
        $prefill = [
            'name' => $options['name'] ?? '',
            'email' => $options['email'] ?? '',
            'contact' => $options['contact'] ?? '',
        ];

        $notes = $options['notes'] ?? [];

        return view('payments.checkout.razorpay', [
            'key' => $this->keyId,
            'amount' => ($options['amount'] ?? 0) * 100,
            'currency' => $options['currency'] ?? $this->currency,
            'name' => $options['name'] ?? config('app.name'),
            'description' => $options['description'] ?? 'Donation',
            'order_id' => $options['order_id'] ?? null,
            'prefill' => $prefill,
            'notes' => $notes,
            'callback_url' => $options['callback_url'] ?? route('payment.callback'),
        ])->render();
    }
}
