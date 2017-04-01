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

    /**
     * @test
     */
    public function it_can_fetch_charge_based_on_charge_id()
    {
        $testPaymentGateway = new TestPaymentGateway();
        $charge1 = $testPaymentGateway->charge(4321, $testPaymentGateway->getValidTestToken());
        $charge2 = $testPaymentGateway->charge(1234, $testPaymentGateway->getValidTestToken());

        $fetchedCharge = $testPaymentGateway->getChargeById($charge1->getId());

        $this->assertInstanceOf(Charge::class, $fetchedCharge);
        $this->assertEquals($charge1->getId(), $fetchedCharge->getId());
        $this->assertEquals($charge1->getAmount(), $fetchedCharge->getAmount());
        $this->assertNotEquals($charge2->getId(), $fetchedCharge->getId());
        $this->assertNotEquals($charge2->getAmount(), $fetchedCharge->getAmount());
    }
}
