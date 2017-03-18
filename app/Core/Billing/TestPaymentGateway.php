<?php

namespace App\Core\Billing;

class TestPaymentGateway implements PaymentGatewayInterface
{
    protected $charges;

    function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function charge($amount, $paymentToken)
    {
        if ($paymentToken !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }

        $this->charges->push($amount);
    }

    public function getTotalCharges()
    {
        return $this->charges->sum();
    }
}