<?php

declare(strict_types=1);

namespace App\Services\Payment;

class PayInContext
{
    /** @var PayInInterface[] */
    protected $payInStrategies;

    public function __construct()
    {
        $this->payInStrategies = [];
    }

    public function addPaymentProvider(PayInInterface $paymentProvider)
    {
        $this->payInStrategies[] = $paymentProvider;
    }

    public function getPaymentProvider(string $provider): ?PayInInterface
    {
        foreach ($this->payInStrategies as $payInStrategy) {
            if ($payInStrategy->getProvider() == $provider) {
                return $payInStrategy;
            }
        }

        return null;
    }
}
