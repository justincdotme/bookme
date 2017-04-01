<?php

namespace App\Core\Payment;

interface PaymentGatewayInterface {

    /**
     * @param $amount
     * @param $paymentToken
     * @return mixed
     */
    public function charge($amount, $paymentToken);

    /**
     * @return mixed
     */
    public function getValidTestToken();

    /**
     * @param $chargeId
     * @return mixed
     */
    public function getChargeById($chargeId);
}