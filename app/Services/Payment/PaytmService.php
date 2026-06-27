<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Paytm Payment Gateway Service
 * 
 * Integration with Paytm All-in-One Payment Gateway.
 * Uses checksum generation for secure transactions.
 * Supports web, UPI, and mobile payments.
 */
class PaytmService
{
    protected string $merchantId;
    protected string $merchantKey;
    protected string $website;
    protected string $channel;
    protected string $industryType;
    protected string $environment;
    protected string $currency;
    protected string $callbackUrl;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('payment.gateways.paytm.merchant_id');
        $this->merchantKey = config('payment.gateways.paytm.merchant_key');
        $this->website = config('payment.gateways.paytm.website', 'DEFAULT');
        $this->channel = config('payment.gateways.paytm.channel', 'WEB');
        $this->industryType = config('payment.gateways.paytm.industry_type', 'Retail');
        $this->environment = config('payment.gateways.paytm.environment', 'staging');
        $this->currency = config('payment.gateways.paytm.currency', 'INR');
        $this->callbackUrl = config('payment.gateways.paytm.callback_url') ?? route('payment.callback');

        // Paytm API endpoints
        $this->baseUrl = $this->environment === 'production'
            ? 'https://securegw.paytm.in'
            : 'https://securegw-stage.paytm.in';
    }

    /**
     * Generate unique order ID with prefix
     */
    protected function generateOrderId(): string
    {
        return 'ORD_' . time() . '_' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Initialize payment transaction
     * 
     * @param array $data Transaction data including amount, user details
     * @return array Payment initialization response with form parameters
     */
    public function initiatePayment(array $data): array
    {
        try {
            $orderId = $data['order_id'] ?? $this->generateOrderId();
            $amount = number_format($data['amount'], 2, '.', '');
            
            // Prepare request payload
            $payload = [
                'MID' => $this->merchantId,
                'WEBSITE' => $this->website,
                'CHANNEL_ID' => $this->channel,
                'INDUSTRY_TYPE_ID' => $this->industryType,
                'ORDER_ID' => $orderId,
                'TXN_AMOUNT' => $amount,
                'CURRENCY' => $this->currency,
                'CUST_ID' => $data['customer_id'] ?? 'CUST_' . time(),
                'EMAIL' => $data['email'] ?? '',
                'MOBILE_NO' => $data['mobile'] ?? '',
                'CALLBACK_URL' => $this->callbackUrl,
                'UDF1' => $data['udf1'] ?? '', // User defined field 1
                'UDF2' => $data['udf2'] ?? '', // User defined field 2
                'UDF3' => $data['udf3'] ?? '', // User defined field 3
                'UDF4' => $data['udf4'] ?? '', // User defined field 4
                'UDF5' => $data['udf5'] ?? '', // User defined field 5
                'THEME' => 'WEB',
            ];

            // Generate checksum using Paytm's algorithm
            $checksum = $this->generateChecksum($payload);
            $payload['CHECKSUMHASH'] = $checksum;

            Log::info('Paytm Payment Initiated', [
                'order_id' => $orderId,
                'amount' => $amount,
                'customer_id' => $payload['CUST_ID'],
            ]);

            return [
                'success' => true,
                'order_id' => $orderId,
                'amount' => $amount,
                'checksum' => $checksum,
                'paytm_params' => $payload,
                'action_url' => $this->baseUrl . '/theia/processTransaction',
                'merchant_id' => $this->merchantId,
            ];

        } catch (Exception $e) {
            Log::error('Paytm Payment Initiation Failed', [
                'order_id' => $data['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate checksum hash for Paytm requests
     */
    protected function generateChecksum(array $params): string
    {
        // Sort the array by key
        ksort($params);
        
        // Create parameter string
        $paramString = '';
        foreach ($params as $key => $value) {
            if ($value !== '' && !is_array($value)) {
                $paramString .= $key . '=' . $value . '&';
            }
        }
        
        // Remove trailing ampersand
        $paramString = rtrim($paramString, '&');
        
        // Add merchant key at the end
        $paramString .= $this->merchantKey;
        
        // Generate hash
        $checksum = hash('sha256', $paramString);
        
        // Add IV and pad
        $iv = '@@@@&&&&####$$$$';
        $encrypted = openssl_encrypt($checksum, 'AES-128-CBC', $this->merchantKey, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($encrypted);
    }

    /**
     * Verify checksum from Paytm callback
     */
    public function verifyChecksum(array $receivedParams, string $receivedChecksum): bool
    {
        try {
            // Extract checksum from received params
            $paytmChecksum = $receivedParams['CHECKSUMHASH'] ?? $receivedChecksum;
            unset($receivedParams['CHECKSUMHASH']);

            // Sort parameters
            ksort($receivedParams);

            // Create parameter string
            $paramString = '';
            foreach ($receivedParams as $key => $value) {
                if ($key !== 'CHECKSUMHASH' && $value !== '' && !is_array($value)) {
                    $paramString .= $key . '=' . $value . '&';
                }
            }

            $paramString = rtrim($paramString, '&');
            $paramString .= $this->merchantKey;

            // Generate hash
            $checksum = hash('sha256', $paramString);

            // Add IV and pad
            $iv = '@@@@&&&&####$$$$';
            $encrypted = openssl_encrypt($checksum, 'AES-128-CBC', $this->merchantKey, OPENSSL_RAW_DATA, $iv);
            $generatedChecksum = base64_encode($encrypted);

            return hash_equals($generatedChecksum, $paytmChecksum);

        } catch (Exception $e) {
            Log::error('Paytm Checksum Verification Failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Fetch transaction status from Paytm
     */
    public function fetchTransactionStatus(string $orderId): array
    {
        try {
            $head = [
                'Content-Type: application/json',
                'x-checksum: ' . $this->generateStatusChecksum($orderId),
            ];

            $body = [
                'mid' => $this->merchantId,
                'orderId' => $orderId,
            ];

            $response = Http::withHeaders($head)->post(
                $this->baseUrl . '/theia/api/v1/order/status',
                $body
            );

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'status' => $result['body']['resultInfo']['resultStatus'] ?? 'UNKNOWN',
                    'txn_id' => $result['body']['txnId'] ?? null,
                    'txn_amount' => $result['body']['txnAmount'] ?? null,
                    'txn_date' => $result['body']['txnDate'] ?? null,
                    'gateway_name' => $result['body']['gatewayName'] ?? null,
                    'bank_txn_id' => $result['body']['bankTxnId'] ?? null,
                    'payment_mode' => $result['body']['paymentMode'] ?? null,
                    'raw_response' => $result,
                ];
            }

            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Status fetch failed',
            ];

        } catch (Exception $e) {
            Log::error('Paytm Status Fetch Failed', [
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
     * Generate checksum for status API
     */
    protected function generateStatusChecksum(string $orderId): string
    {
        $string = $orderId . '|' . $this->merchantId . '|' . $this->merchantKey;
        $checksum = hash('sha256', $string);
        
        $iv = '@@@@&&&&####$$$$';
        $encrypted = openssl_encrypt($checksum, 'AES-128-CBC', $this->merchantKey, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($encrypted);
    }

    /**
     * Process refund for a transaction
     */
    public function refund(string $orderId, string $txnId, array $data): array
    {
        try {
            $refundAmount = number_format($data['amount'] ?? 0, 2, '.', '');
            $refundId = 'REF_' . time() . '_' . strtoupper(substr(md5(uniqid()), 0, 6));

            $head = [
                'Content-Type: application/json',
                'x-checksum: ' . $this->generateRefundChecksum($orderId, $refundAmount),
            ];

            $body = [
                'mid' => $this->merchantId,
                'orderId' => $orderId,
                'txnId' => $txnId,
                'refundAmount' => $refundAmount,
                'refundType' => 'FULL', // FULL or PARTIAL
                'refundId' => $refundId,
                'reason' => $data['reason'] ?? 'Refund request',
            ];

            $response = Http::withHeaders($head)->post(
                $this->baseUrl . '/theia/api/v1/doRefund',
                $body
            );

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'success' => ($result['body']['resultInfo']['resultStatus'] ?? '') === 'SUCCESS',
                    'refund_id' => $refundId,
                    'amount' => $refundAmount,
                    'status' => $result['body']['resultInfo']['resultStatus'] ?? 'PENDING',
                    'message' => $result['body']['resultInfo']['resultMessage'] ?? null,
                    'raw_response' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'Refund failed',
                'error' => $response->json('body.resultInfo.resultMessage') ?? 'Unknown error',
            ];

        } catch (Exception $e) {
            Log::error('Paytm Refund Failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund service unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate checksum for refund API
     */
    protected function generateRefundChecksum(string $orderId, string $refundAmount): string
    {
        $string = $orderId . '|' . $refundAmount . '|' . $this->merchantId . '|' . $this->merchantKey;
        $checksum = hash('sha256', $string);
        
        $iv = '@@@@&&&&####$$$$';
        $encrypted = openssl_encrypt($checksum, 'AES-128-CBC', $this->merchantKey, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($encrypted);
    }

    /**
     * Generate HTML form for payment submission
     */
    public function generatePaymentForm(array $params): string
    {
        $html = '<form id="paytmForm" method="POST" action="' . $this->baseUrl . '/theia/processTransaction">';
        
        foreach ($params as $key => $value) {
            $html .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
        }
        
        $html .= '</form>';
        $html .= '<script>document.getElementById("paytmForm").submit();</script>';
        
        return $html;
    }
}
