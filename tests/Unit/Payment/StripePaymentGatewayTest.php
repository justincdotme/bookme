<?php

namespace Tests\Unit\Payment;

use App\Core\Payment\Charge;
use App\Core\Payment\PaymentFailedException;
use App\Core\Payment\StripePaymentGateway;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group external
 */

class StripePaymentGatewayTest extends TestCase
{
    protected $lastCharge;

    protected function setUp()
    {
        parent::setUp();
        $this->lastCharge = $this->fetchLastCharge();
    }

    /**
     * @test
     */
    public function it_successfully_charges_when_passed_valid_token()
    {
        //Create new stripe payment gateway
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        $paymentGateway->charge(12500, $paymentGateway->getValidTestToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(12500, $this->fetchLastCharge()->amount);
    }

    /**
     * @test
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
            $paymentGateway->charge(12500, 'invalid-token');
        } catch (PaymentFailedException $e) {
            return;
        }

        $this->fail('Charge succeeded with invalid test token.');
    }

    protected function newCharges()
    {
        return \Stripe\Charge::all([
            'limit' => 1,
            'ending_before' => $this->lastCharge->id
        ],
        [
            'api_key' => config('services.stripe.secret')
        ])['data'];
    }

    protected function fetchLastCharge()
    {
        return \Stripe\Charge::all(
            [
                'limit' => 1
            ],
            [
                'api_key' => config('services.stripe.secret')
            ]
        )['data'][0];
    }

    /**
     * @test
     */
    public function it_returns_charge_object_on_successful_charge()
    {
        //Create new stripe payment gateway
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        $charge = $paymentGateway->charge(12500, $paymentGateway->getValidTestToken());

        $this->assertInstanceOf(Charge::class, $charge);
    }
}
