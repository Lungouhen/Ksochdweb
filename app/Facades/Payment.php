<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Facade;

/**
 * Payment Gateway Facade
 * 
 * Provides a unified interface for all payment gateway services.
 * Automatically routes to the configured default gateway or specified gateway.
 * 
 * @see \App\Services\Payment\PaymentGatewayService
 */
class Payment extends Facade
{
    /**
     * Get the registered name of the component in the service container.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'payment';
    }
}
