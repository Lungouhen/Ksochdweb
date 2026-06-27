<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Unified Payment Gateway Service
 * 
 * Central orchestrator for all payment gateway integrations.
 * Provides a consistent interface regardless of the underlying gateway.
 * Supports gateway switching, fallback mechanisms, and unified logging.
 */
class PaymentGatewayService
{
    protected string $defaultGateway;
    protected array $enabledGateways;
    
    protected ?StripeService $stripe = null;
    protected ?RazorpayService $razorpay = null;
    protected ?CashfreeService $cashfree = null;
    protected ?PaytmService $paytm = null;
    protected ?PhonePeService $phonepe = null;

    public function __construct()
    {
        $this->defaultGateway = config('payment.default', 'stripe');
        $this->enabledGateways = $this->getEnabledGateways();
        
        // Initialize enabled gateway services
        $this->initializeServices();
    }

    /**
     * Get list of enabled gateways from config
     */
    protected function getEnabledGateways(): array
    {
        $gateways = [];
        $config = config('payment.gateways', []);

        foreach ($config as $name => $settings) {
            if (!empty($settings['enabled'])) {
                $gateways[] = $name;
            }
        }

        return $gateways;
    }

    /**
     * Initialize service instances for enabled gateways
     */
    protected function initializeServices(): void
    {
        try {
            if (in_array('stripe', $this->enabledGateways)) {
                $this->stripe = new StripeService();
            }
        } catch (Exception $e) {
            Log::warning('Stripe service initialization failed', ['error' => $e->getMessage()]);
        }

        try {
            if (in_array('razorpay', $this->enabledGateways)) {
                $this->razorpay = new RazorpayService();
            }
        } catch (Exception $e) {
            Log::warning('Razorpay service initialization failed', ['error' => $e->getMessage()]);
        }

        try {
            if (in_array('cashfree', $this->enabledGateways)) {
                $this->cashfree = new CashfreeService();
            }
        } catch (Exception $e) {
            Log::warning('Cashfree service initialization failed', ['error' => $e->getMessage()]);
        }

        try {
            if (in_array('paytm', $this->enabledGateways)) {
                $this->paytm = new PaytmService();
            }
        } catch (Exception $e) {
            Log::warning('Paytm service initialization failed', ['error' => $e->getMessage()]);
        }

        try {
            if (in_array('phonepe', $this->enabledGateways)) {
                $this->phonepe = new PhonePeService();
            }
        } catch (Exception $e) {
            Log::warning('PhonePe service initialization failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get the service instance for a specific gateway
     */
    protected function getGateway(string $gateway): object
    {
        $service = match ($gateway) {
            'stripe' => $this->stripe,
            'razorpay' => $this->razorpay,
            'cashfree' => $this->cashfree,
            'paytm' => $this->paytm,
            'phonepe' => $this->phonepe,
            default => throw new Exception("Unknown payment gateway: {$gateway}"),
        };

        if (!$service) {
            throw new Exception("Payment gateway '{$gateway}' is not enabled or failed to initialize");
        }

        return $service;
    }

    /**
     * Create a payment order/transaction
     * Unified method that routes to the appropriate gateway
     */
    public function createOrder(array $data, ?string $gateway = null): array
    {
        $gateway = $gateway ?? $this->defaultGateway;
        
        Log::info('Payment order creation requested', [
            'gateway' => $gateway,
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? null,
        ]);

        try {
            $service = $this->getGateway($gateway);
            
            $result = match ($gateway) {
                'stripe' => $service->createIntent($data),
                'razorpay' => $service->createOrder($data),
                'cashfree' => $service->createOrder($data),
                'paytm' => $service->initiatePayment($data),
                'phonepe' => $service->initiatePayment($data),
                default => throw new Exception("Unsupported gateway: {$gateway}"),
            };

            $result['gateway'] = $gateway;
            
            Log::info('Payment order created successfully', [
                'gateway' => $gateway,
                'order_id' => $result['order_id'] ?? null,
                'success' => $result['success'] ?? false,
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Payment order creation failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment service unavailable',
                'error' => $e->getMessage(),
                'gateway' => $gateway,
            ];
        }
    }

    /**
     * Verify a payment after callback/webhook
     */
    public function verifyPayment(string $gateway, array $data): array
    {
        try {
            $service = $this->getGateway($gateway);

            $result = match ($gateway) {
                'stripe' => $service->verifyPaymentIntent($data),
                'razorpay' => [
                    'success' => $service->verifyPayment(
                        $data['order_id'],
                        $data['payment_id'],
                        $data['signature']
                    ),
                ],
                'cashfree' => $service->verifyPayment($data['order_id']),
                'paytm' => $service->fetchTransactionStatus($data['order_id']),
                'phonepe' => $service->checkStatus($data['order_id']),
                default => throw new Exception("Unsupported gateway: {$gateway}"),
            };

            $result['gateway'] = $gateway;

            Log::info('Payment verification completed', [
                'gateway' => $gateway,
                'order_id' => $data['order_id'] ?? null,
                'success' => $result['success'] ?? false,
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Payment verification failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage(),
                'gateway' => $gateway,
            ];
        }
    }

    /**
     * Process a refund
     */
    public function refund(string $transactionId, array $data, ?string $gateway = null): array
    {
        $gateway = $gateway ?? $this->defaultGateway;

        try {
            $service = $this->getGateway($gateway);

            $result = match ($gateway) {
                'stripe' => $service->refund($transactionId, $data),
                'razorpay' => $service->refund($transactionId, $data),
                'cashfree' => $service->refund($transactionId, $data),
                'paytm' => $service->refund($transactionId, $data['txn_id'] ?? $transactionId, $data),
                'phonepe' => $service->refund([
                    'original_order_id' => $transactionId,
                    'refund_order_id' => $data['refund_order_id'] ?? 'REF_' . time(),
                    'amount' => $data['amount'] ?? 0,
                    'user_id' => $data['user_id'] ?? null,
                ]),
                default => throw new Exception("Unsupported gateway: {$gateway}"),
            };

            $result['gateway'] = $gateway;

            Log::info('Refund processed', [
                'gateway' => $gateway,
                'transaction_id' => $transactionId,
                'success' => $result['success'] ?? false,
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Refund processing failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund failed',
                'error' => $e->getMessage(),
                'gateway' => $gateway,
            ];
        }
    }

    /**
     * Get available gateways for frontend selection
     */
    public function getAvailableGateways(): array
    {
        $gateways = [];
        $config = config('payment.gateways', []);

        foreach ($this->enabledGateways as $name) {
            $settings = $config[$name] ?? [];
            
            $gateways[] = [
                'id' => $name,
                'name' => $this->getGatewayDisplayName($name),
                'currency' => $settings['currency'] ?? 'USD',
                'logo' => asset("images/payment/{$name}.svg"),
                'enabled' => true,
            ];
        }

        return $gateways;
    }

    /**
     * Get human-readable gateway name
     */
    protected function getGatewayDisplayName(string $gateway): string
    {
        return match ($gateway) {
            'stripe' => 'Credit/Debit Card (Stripe)',
            'razorpay' => 'Razorpay (India)',
            'cashfree' => 'Cashfree (India)',
            'paytm' => 'Paytm (India)',
            'phonepe' => 'PhonePe (India)',
            default => ucfirst($gateway),
        };
    }

    /**
     * Check if a gateway is enabled
     */
    public function isGatewayEnabled(string $gateway): bool
    {
        return in_array($gateway, $this->enabledGateways);
    }

    /**
     * Get default gateway
     */
    public function getDefaultGateway(): string
    {
        return $this->defaultGateway;
    }

    /**
     * Set default gateway dynamically
     */
    public function setDefaultGateway(string $gateway): self
    {
        if ($this->isGatewayEnabled($gateway)) {
            $this->defaultGateway = $gateway;
        }

        return $this;
    }
}
