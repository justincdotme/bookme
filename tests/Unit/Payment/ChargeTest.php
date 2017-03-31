<?php

namespace Tests\Unit\Payment;

use App\Core\Payment\Charge;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group external
 */

class ChargeTest extends TestCase
{
    protected $lastCharge;
    protected $charge;

    protected function setUp()
    {
        parent::setUp();
        $this->charge = new Charge([
            'id' => 'ch_' . str_random(24),
            'amount' => 123456,
            'exp_month' => Carbon::parse('+2 months')->format('n'),
            'exp_year' => Carbon::parse('+2 years')->format('Y'),
            'last_four' => 2424,
            'brand' => 'Visa'
        ]);
    }

    /**
     * @test
     */
    public function it_can_return_charge_id()
    {
        $this->assertContains('ch_', $this->charge->getId());
    }

    /**
     * @test
     */
    public function it_can_return_amount()
    {
        $this->assertEquals(123456, $this->charge->getAmount());
    }

    /**
     * @test
     */
    public function it_can_return_charge_card_last_four()
    {
        $this->assertEquals(2424, $this->charge->getCardLastFour());
    }

    /**
     * @test
     */
    public function it_can_return_charge_card_exp_month()
    {
        $this->assertEquals($this->charge->getExpMonth(), Carbon::parse('+2 months')->format('n'));
    }

    /**
     * @test
     */
    public function it_can_return_charge_card_exp_year()
    {
        $this->assertEquals($this->charge->getExpYear(), Carbon::parse('+2 years')->format('Y'));
    }

    /**
     * @test
     */
    public function it_can_return_charge_card_brand()
    {
        $this->assertEquals($this->charge->getCardBrand(), 'Visa');
    }
}
