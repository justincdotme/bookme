<?php

namespace Tests\Unit\Payment;

use App\Core\Payment\Charge;
use App\Core\Payment\PaymentFailedException;
use App\Core\Payment\TestPaymentGateway;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestPaymentGatewayTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_charges_when_passed_valid_token()
    {
        $testPaymentGateway = new TestPaymentGateway();

        $charge = $testPaymentGateway->charge(350000, $testPaymentGateway->getValidTestToken());

        $this->assertEquals(350000, $charge->getAmount());
    }

    /**
     * @test
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new TestPaymentGateway();
            $paymentGateway->charge(1, 'invalid-token');
        } catch (PaymentFailedException $e) {
            return;
        }

        $this->fail('Charge succeeded with invalid test token.');
    }

    /**
     * @test
     */
    public function it_returns_charge_object_on_successful_charge()
    {
        $testPaymentGateway = new TestPaymentGateway();

        $charge = $testPaymentGateway->charge(350000, $testPaymentGateway->getValidTestToken());

        $this->assertInstanceOf(Charge::class, $charge);
    }
}
