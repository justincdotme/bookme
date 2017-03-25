<?php

namespace App\Core\Payment;

interface PaymentGatewayInterface {

    public function charge($amount, $paymentToken);

    public function getValidTestToken();

    public function getTotalCharges();
}