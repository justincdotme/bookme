<?php

namespace App\Core\Payment;

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
        $this->apiKey = $apiKey;
        $this->charges = collect([]);
    }

    /**
     * @param string $cardNumber
     * @return string
     */
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

    /**
     * @param $amount
     * @param $paymentToken
     * @return Charge
     */
    public function charge($amount, $paymentToken)
    {
        try {
            $stripeCharge = StripeCharge::create([
                'amount' => $amount,
                'source' => $paymentToken,
                'currency' => 'usd'
            ], ['api_key' => $this->apiKey]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException();
        }
        $charge = new Charge([
            'id' => $stripeCharge->id,
            'amount' => $stripeCharge->amount,
            'exp_month' => $stripeCharge->source->exp_month,
            'exp_year' => $stripeCharge->source->exp_year,
            'last_four' => $stripeCharge->source->last4,
            'brand' => $stripeCharge->source->brand
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
}