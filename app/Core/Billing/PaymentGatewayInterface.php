<?php

namespace App\Core\Billing;

interface PaymentGatewayInterface {

    public function charge($amount, $paymentToken);

    public function getValidTestToken();

    public function getTotalCharges();
}