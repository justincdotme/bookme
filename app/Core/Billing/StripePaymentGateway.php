<?php

namespace App\Core\Billing;

use Stripe\Charge as StripeCharge;
use Stripe\Error\InvalidRequest;
use Stripe\Token as StripeToken;

class StripePaymentGateway implements PaymentGatewayInterface
{
    const TEST_CARD_NUMBER = '4242424242424242';

    protected $charges;
    protected $apiKey;

    function __construct($apiKey)
    {
        $this->charges = collect([]);
        $this->apiKey = $apiKey;
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        return StripeToken::create([
            "card" => [
                "number" => $cardNumber,
                "exp_month" => 1,
                "exp_year" => date('Y') + 1,
                "cvc" => "123"
            ]
        ], ['api_key' => $this->apiKey])->id;
    }

    public function charge($amount, $paymentToken)
    {
        try {
            StripeCharge::create([
                'amount' => $amount,
                'source' => $paymentToken,
                'currency' => 'usd'
            ], ['api_key' => $this->apiKey]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException();
        }

        return $this->charges->push($amount);
    }

    public function getTotalCharges()
    {
        return $this->charges->sum();
    }
}