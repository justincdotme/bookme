<?php

namespace App\Core\Payment;

use Carbon\Carbon;

class TestPaymentGateway implements PaymentGatewayInterface
{
    protected $charges;

    function __construct()
    {
        $this->charges = collect([]);
    }

    /**
     * @return string
     */
    public function getValidTestToken()
    {
        return 'valid-token';
    }

    /**
     * @param $amount
     * @param $paymentToken
     * @return Charge
     */
    public function charge($amount, $paymentToken)
    {
        if ($paymentToken !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }

        $charge = new Charge([
            'id' => 'ch_' . str_random(24),
            'amount' => $amount,
            'exp_month' => Carbon::parse('+2 months')->format('n'),
            'exp_year' => Carbon::parse('+2 years')->format('Y'),
            'last_four' => 4242,
            'brand' => 'Visa'
        ]);
        $this->charges->push($charge);

        return $charge;
    }

    /**
     * @return int
     */
    public function getTotalCharges()
    {
        return $this->charges->reduce(function ($carry, $item) {
            return ($carry + $item->getAmount());
        });
    }

    /**
     * @param $chargeId
     * @return static
     */
    public function getChargeById($chargeId)
    {
        $charge = $this->charges->reject(function ($value, $key) use ($chargeId) {
            return $value->getId() != $chargeId;
        })->first();

        return $charge;
    }
}