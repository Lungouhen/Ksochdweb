<?php

namespace App\Services\Payment;

use Cashfree\PgApiClient;
use Cashfree\Pg\Exceptions\CashfreeException;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Cashfree Payment Gateway Service
 * 
 * Official SDK integration for Cashfree Payments (India).
 * Supports payments, order management, refunds, and webhooks.
 */
class CashfreeService
{
    protected PgApiClient $client;
    protected string $appId;
    protected string $secretKey;
    protected string $clientId;
    protected string $clientSecret;
    protected string $environment;
    protected string $currency;

    public function __construct()
    {
        $this->appId = config('payment.gateways.cashfree.app_id');
        $this->secretKey = config('payment.gateways.cashfree.secret_key');
        $this->clientId = config('payment.gateways.cashfree.client_id');
        $this->clientSecret = config('payment.gateways.cashfree.client_secret');
        $this->environment = config('payment.gateways.cashfree.environment', 'test');
        $this->currency = config('payment.gateways.cashfree.currency', 'INR');

        // Initialize Cashfree PG API Client
        $isProduction = $this->environment === 'production';
        $this->client = new PgApiClient($this->clientId, $this->clientSecret, $isProduction);
    }

    /**
     * Create an order for payment
     * 
     * @param array $data Order data including amount, customer details
     * @return array Order creation response
     */
    public function createOrder(array $data): array
    {
        try {
            $orderData = [
                'order_amount' => (int) ($data['amount'] * 100), // Amount in paise
                'order_currency' => $data['currency'] ?? $this->currency,
                'order_id' => $data['order_id'] ?? 'ORD_' . time(),
                'customer_details' => [
                    'customer_id' => $data['customer_id'] ?? 'CUST_' . time(),
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'customer_email' => $data['customer_email'] ?? null,
                    'customer_name' => $data['customer_name'] ?? null,
                ],
                'order_meta' => [
                    'return_url' => $data['callback_url'] ?? route('payment.callback'),
                    'notify_url' => $data['webhook_url'] ?? null,
                ],
                'order_note' => $data['order_note'] ?? 'Donation payment',
            ];

            // Add payment methods if specified
            if (!empty($data['payment_methods'])) {
                $orderData['order_payment_methods'] = $data['payment_methods'];
            }

            $response = $this->client->getOrderApi()->createOrder($orderData);

            if ($response['order_status'] === 'ACTIVE') {
                return [
                    'success' => true,
                    'order_id' => $response['order_id'],
                    'cf_order_id' => $response['cf_order_id'] ?? null,
                    'amount' => $response['order_amount'] / 100,
                    'currency' => $response['order_currency'],
                    'status' => $response['order_status'],
                    'payment_link' => $response['payment_link'] ?? null,
                    'pay_page_url' => $response['pay_page_url'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => 'Order creation failed',
                'error' => $response['order_message'] ?? 'Unknown error',
            ];

        } catch (CashfreeException $e) {
            Log::error('Cashfree Order Creation Failed', [
                'order_id' => $data['order_id'] ?? 'unknown',
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment service unavailable',
                'error' => $e->getMessage(),
            ];

        } catch (Exception $e) {
            Log::error('Cashfree Order Exception', [
                'order_id' => $data['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment service error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch order details
     */
    public function fetchOrder(string $orderId): array
    {
        try {
            $response = $this->client->getOrderApi()->getOrderEntity($orderId);

            return [
                'success' => true,
                'order_id' => $response['order_id'],
                'cf_order_id' => $response['cf_order_id'] ?? null,
                'amount' => $response['order_amount'] / 100,
                'currency' => $response['order_currency'],
                'status' => $response['order_status'],
                'paid_amount' => isset($response['order_payments']) ? collect($response['order_payments'])->sum('payment_amount') / 100 : 0,
                'customer_details' => $response['customer_details'] ?? [],
                'created_at' => $response['created_at'] ?? null,
            ];

        } catch (CashfreeException $e) {
            Log::error('Cashfree Order Fetch Failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Order fetch failed',
                'error' => $e->getMessage(),
            ];

        } catch (Exception $e) {
            Log::error('Cashfree Order Fetch Exception', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Service error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify order payment status
     */
    public function verifyPayment(string $orderId): array
    {
        try {
            $order = $this->fetchOrder($orderId);

            if (!$order['success']) {
                return $order;
            }

            $isPaid = $order['status'] === 'PAID';
            $partiallyPaid = $order['status'] === 'PARTIALLY_PAID';

            return [
                'success' => $isPaid,
                'is_paid' => $isPaid,
                'is_partially_paid' => $partiallyPaid,
                'status' => $order['status'],
                'paid_amount' => $order['paid_amount'],
                'order_amount' => $order['amount'],
                'transaction_id' => $orderId,
            ];

        } catch (Exception $e) {
            Log::error('Cashfree Payment Verification Failed', [
                'order_id' => $orderId,
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
     * Process refund for an order
     */
    public function refund(string $orderId, array $data): array
    {
        try {
            $refundData = [
                'order_id' => $orderId,
                'refund_amount' => (int) ($data['amount'] * 100), // Amount in paise
                'refund_note' => $data['reason'] ?? 'Refund request',
            ];

            $response = $this->client->getRefundApi()->createRefund($refundData);

            if (isset($response['refund_id'])) {
                return [
                    'success' => true,
                    'refund_id' => $response['refund_id'],
                    'amount' => $response['refund_amount'] / 100,
                    'status' => $response['refund_status'] ?? 'PENDING',
                    'initiated_at' => $response['created_at'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => 'Refund initiation failed',
                'error' => $response['refund_message'] ?? 'Unknown error',
            ];

        } catch (CashfreeException $e) {
            Log::error('Cashfree Refund Failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund service unavailable',
                'error' => $e->getMessage(),
            ];

        } catch (Exception $e) {
            Log::error('Cashfree Refund Exception', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund service error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch refund status
     */
    public function fetchRefundStatus(string $refundId): array
    {
        try {
            $response = $this->client->getRefundApi()->getRefundEntity($refundId);

            return [
                'success' => true,
                'refund_id' => $response['refund_id'],
                'amount' => $response['refund_amount'] / 100,
                'status' => $response['refund_status'],
                'reason' => $response['refund_note'] ?? null,
                'initiated_at' => $response['created_at'] ?? null,
                'processed_at' => $response['updated_at'] ?? null,
            ];

        } catch (CashfreeException $e) {
            Log::error('Cashfree Refund Status Fetch Failed', [
                'refund_id' => $refundId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund status fetch failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate pay page URL directly
     */
    public function generatePayPageUrl(array $data): array
    {
        try {
            $orderData = [
                'order_amount' => (int) ($data['amount'] * 100),
                'order_currency' => $data['currency'] ?? $this->currency,
                'order_id' => $data['order_id'] ?? 'ORD_' . time(),
                'customer_details' => [
                    'customer_id' => $data['customer_id'] ?? 'GUEST_' . time(),
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'customer_email' => $data['customer_email'] ?? null,
                ],
                'order_meta' => [
                    'return_url' => $data['callback_url'] ?? route('payment.callback'),
                ],
            ];

            $response = $this->client->getOrderApi()->createOrder($orderData);

            if (isset($response['pay_page_url'])) {
                return [
                    'success' => true,
                    'payment_url' => $response['pay_page_url'],
                    'order_id' => $response['order_id'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Pay page generation failed',
            ];

        } catch (Exception $e) {
            Log::error('Cashfree Pay Page Generation Failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Service error',
                'error' => $e->getMessage(),
            ];
        }
    }
}
