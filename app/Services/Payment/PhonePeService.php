<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * PhonePe Payment Gateway Service
 * 
 * Handles direct API integration with PhonePe payment gateway.
 * Uses HMAC SHA256 for request signing and verification.
 * Supports payments, status checks, refunds, and webhook verification.
 */
class PhonePeService
{
    protected string $merchantId;
    protected string $saltKey;
    protected int $saltIndex;
    protected string $baseUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->merchantId = config('payment.gateways.phonepe.merchant_id');
        $this->saltKey = config('payment.gateways.phonepe.salt_key');
        $this->saltIndex = (int) config('payment.gateways.phonepe.salt_index', 1);
        $this->isProduction = config('payment.gateways.phonepe.environment') === 'production';
        
        // PhonePe API endpoints
        $this->baseUrl = $this->isProduction
            ? 'https://api.phonepe.com/apis/hermes'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    /**
     * Generate checksum/header for PhonePe requests
     * Uses SHA256 hash of payload + saltKey + endpoint
     */
    protected function generateChecksum(string $payload, string $endpoint): string
    {
        $string = $payload . $endpoint . $this->saltKey;
        return hash('sha256', $string) . '###' . $this->saltIndex;
    }

    /**
     * Initiate a payment transaction
     * 
     * @param array $data Transaction data including amount, orderId, userId
     * @return array Payment initiation response
     * @throws Exception
     */
    public function initiatePayment(array $data): array
    {
        try {
            $endpoint = '/pg/v1/pay';
            
            $payload = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $data['order_id'],
                'merchantUserId' => $data['user_id'] ?? 'GUEST_' . time(),
                'amount' => (int) ($data['amount'] * 100), // Amount in paise
                'redirectUrl' => $data['callback_url'],
                'redirectMode' => 'POST',
                'callbackUrl' => $data['webhook_url'] ?? $data['callback_url'],
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE',
                ],
            ];

            $base64Payload = base64_encode(json_encode($payload));
            $checksum = $this->generateChecksum($base64Payload, $endpoint);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
            ])->post($this->baseUrl . $endpoint, [
                'request' => $base64Payload,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (($result['success'] ?? false) && isset($result['data']['instrumentResponse']['redirectInfo']['url'])) {
                    return [
                        'success' => true,
                        'payment_url' => $result['data']['instrumentResponse']['redirectInfo']['url'],
                        'transaction_id' => $data['order_id'],
                        'phonepe_order_id' => $result['data']['merchantTransactionId'] ?? null,
                        'raw_response' => $result,
                    ];
                }
            }

            Log::error('PhonePe Payment Initiation Failed', [
                'order_id' => $data['order_id'],
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed',
                'error' => $response->json('message') ?? 'Unknown error',
            ];

        } catch (Exception $e) {
            Log::error('PhonePe Payment Exception', [
                'order_id' => $data['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment service unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(string $orderId): array
    {
        try {
            $endpoint = "/pg/v1/status/{$this->merchantId}/{$orderId}";
            
            $payload = '';
            $checksum = $this->generateChecksum($payload, $endpoint);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
                'X-MERCHANT-ID' => $this->merchantId,
            ])->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => ($result['code'] ?? '') === 'PAYMENT_SUCCESS',
                    'status' => $result['code'] ?? 'UNKNOWN',
                    'transaction_id' => $orderId,
                    'amount' => isset($result['data']['amount']) ? $result['data']['amount'] / 100 : null,
                    'raw_response' => $result,
                ];
            }

            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Status check failed',
            ];

        } catch (Exception $e) {
            Log::error('PhonePe Status Check Exception', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook/callback signature
     */
    public function verifyWebhook(string $payload, string $receivedChecksum): bool
    {
        $expectedChecksum = hash('sha256', $payload . '/pg/v1/status/update' . $this->saltKey) . '###' . $this->saltIndex;
        return hash_equals($expectedChecksum, $receivedChecksum);
    }

    /**
     * Process refund (if supported)
     */
    public function refund(array $data): array
    {
        try {
            $endpoint = '/pg/v1/refund';
            
            $payload = [
                'merchantId' => $this->merchantId,
                'merchantUserId' => $data['user_id'],
                'originalTransactionId' => $data['original_order_id'],
                'merchantTransactionId' => $data['refund_order_id'],
                'amount' => (int) ($data['amount'] * 100),
            ];

            $base64Payload = base64_encode(json_encode($payload));
            $checksum = $this->generateChecksum($base64Payload, $endpoint);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
            ])->post($this->baseUrl . $endpoint, [
                'request' => $base64Payload,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => ($result['code'] ?? '') === 'REFUND_SUCCESS',
                    'refund_id' => $data['refund_order_id'],
                    'raw_response' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'Refund failed',
                'error' => $response->json('message') ?? 'Unknown error',
            ];

        } catch (Exception $e) {
            Log::error('PhonePe Refund Exception', [
                'order_id' => $data['original_order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund service unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }
}
