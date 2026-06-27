<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\PaymentGatewayService;

/**
 * Payment Service Provider
 * 
 * Registers the unified payment gateway service in the container.
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('payment', function ($app) {
            return new PaymentGatewayService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/payment.php' => config_path('payment.php'),
        ], 'payment-config');
    }
}
