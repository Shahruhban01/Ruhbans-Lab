<?php

declare(strict_types=1);

namespace App\Services\Commerce;

use App\Services\Commerce\Contracts\PaymentGatewayInterface;
use App\Services\Commerce\Adapters\RazorpayAdapter;
use App\Services\Commerce\Adapters\StripeAdapter;
use App\Services\Commerce\Adapters\CashfreeAdapter;
use App\Services\Commerce\Adapters\PaypalAdapter;
use App\Core\Application;

final class GatewayManager
{
    private Application $app;
    private array $gateways = array();

    public function __construct(Application $app)
    {
        $this->app = $app;
        
        // Register default adapters
        $this->registerGateway(new RazorpayAdapter());
        $this->registerGateway(new StripeAdapter());
        $this->registerGateway(new CashfreeAdapter());
        $this->registerGateway(new PaypalAdapter());
    }

    public function registerGateway(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->getProviderName()] = $gateway;
    }

    public function getGateway(string $provider): ?PaymentGatewayInterface
    {
        return $this->gateways[strtolower($provider)] ?? null;
    }

    public function getActiveGatewayName(): string
    {
        return (string) $this->app->config()->get('commerce.default_gateway', 'razorpay');
    }

    public function getActiveGateway(): PaymentGatewayInterface
    {
        $activeName = $this->getActiveGatewayName();
        $gateway = $this->getGateway($activeName);
        if (!$gateway) {
            return $this->getGateway('razorpay');
        }
        return $gateway;
    }
}
