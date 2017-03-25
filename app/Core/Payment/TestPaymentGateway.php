<?php

namespace App\Core\Payment;

class TestPaymentGateway implements PaymentGatewayInterface
{
    protected $charge;

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function charge($amount, $paymentToken)
    {
        if ($paymentToken !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }

        $this->charge = $amount;
    }

    public function getTotalCharges()
    {
        return $this->charge;
    }
}