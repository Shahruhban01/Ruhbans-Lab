<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Core\Application;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

final class PaymentManager
{
    private Application $app;
    private GatewayFactory $factory;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->factory = new GatewayFactory();
    }

    public function getGateway(string $provider): PaymentGatewayInterface
    {
        return $this->factory->make($provider);
    }

    public function getActiveGateway(): PaymentGatewayInterface
    {
        $provider = (string) $this->app->config()->get('payment.default_gateway', 'razorpay');
        return $this->getGateway($provider);
    }
}
