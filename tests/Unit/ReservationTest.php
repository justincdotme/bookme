<?php

namespace Tests\Unit;

use App\Core\Address;
use App\Core\Payment\TestPaymentGateway;
use App\Core\Property\Property;
use App\Core\Reservation;
use App\Exceptions\AlreadyReservedException;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    protected $billingAddress;

    protected function setUp()
    {
        parent::setUp();
        $this->billingAddress = Address::create([
            'line1' => '123 Any St.',
            'line2' => 'Apt B.',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
        ]);
    }

    /**
     * @test
     */
    public function cancelled_reservations_are_not_active()
    {
        $pendingReservation = factory(Reservation::class)->states(['pending'])->create();
        $confirmedReservation = factory(Reservation::class)->states(['confirmed'])->create();
        $paidReservation = factory(Reservation::class)->states(['paid'])->create();
        $cancelledReservation = factory(Reservation::class)->states(['cancelled'])->create();

        $activeReservations = Reservation::active()->get();

        $this->assertTrue($activeReservations->contains($pendingReservation));
        $this->assertTrue($activeReservations->contains($confirmedReservation));
        $this->assertTrue($activeReservations->contains($paidReservation));
        $this->assertFalse($activeReservations->contains($cancelledReservation));
    }

    /**
     * @test
     */
    public function it_calculates_the_length_of_stay_in_days()
    {
        $reservation = factory(Reservation::class)->make([
            'date_start' => Carbon::now(),
            'date_end' => Carbon::parse('+1 week'),
        ]);

        $this->assertEquals(7, $reservation->getLengthOfStay());
    }

    /**
     * @test
     */
    public function it_calculates_reservation_total()
    {
        $property = factory(Property::class)->states(['available'])->create([
            'rate' => 555
        ]);

        $reservation = factory(Reservation::class)->make([
            'property_id' => $property->id,
            'date_start' => Carbon::now(),
            'date_end' => Carbon::parse('+1 week')
        ]);

        $this->assertEquals(3885, $reservation->calculateTotal());
    }

    /**
     * @test
     */
    public function cancelled_reservations_are_marked_cancelled()
    {
        $reservation = factory(Reservation::class)->create();
        $reservation->cancel();
        $this->assertEquals('cancelled', $reservation->status);
    }

    /**
     * @test
     */
    public function it_can_get_formatted_date_start()
    {
        $reservation = factory(Reservation::class)->make([
            'date_start' => Carbon::now(),
            'date_end' => Carbon::parse('+1 week'),
        ]);
        $formattedDate = Carbon::parse($reservation->date_start)->toFormattedDateString();

        $this->assertEquals($formattedDate, $reservation->formatted_date_start);
    }

    /**
     * @test
     */
    public function it_can_get_formatted_date_end()
    {
        $reservation = factory(Reservation::class)->make([
            'date_start' => Carbon::now(),
            'date_end' => Carbon::parse('+1 week'),
        ]);
        $formattedDate = Carbon::parse($reservation->date_end)->toFormattedDateString();

        $this->assertEquals($formattedDate, $reservation->formatted_date_end);
    }

    /**
     * @test
     *
     */
    public function charge_id_is_persisted_on_successful_reservation()
    {
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id
        ]);
        $paymentGateway = new TestPaymentGateway;
        $token = $paymentGateway->getValidTestToken();

        $reservation->complete($paymentGateway, $token, $this->billingAddress);

        $this->assertNotNull($reservation->charge_id);
        $this->assertStringStartsWith(
            'ch_',
            $reservation->charge_id,
            'A valid charge ID was not returned'
        );
    }

    /**
     * @test
     */
    public function it_can_get_formatted_total_amount()
    {
        $reservation = factory(Reservation::class)->create([
            'amount' => 50000
        ]);

        $amount = $reservation->formatted_amount;

        $this->assertEquals('$500.00', $amount);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_property_is_already_reserved_when_updating_reservation()
    {
        $property = factory(Property::class)->states(['available'])->create();
        $reservation1 = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'date_start' => Carbon::now(),
            'date_end' => Carbon::parse('+1 week')
        ]);
        factory(Reservation::class)->create([
            'property_id' => $property->id,
            'date_start' => Carbon::parse('+2 weeks'),
            'date_end' => Carbon::parse('+3 weeks')
        ]);

        try {
            $reservation1->updateReservation(
                $reservation1->status,
                $reservation1->amount,
                Carbon::parse('+2 weeks'),
                Carbon::parse('+3 weeks')
            );
            $this->expectException(AlreadyReservedException::class);
        } catch (AlreadyReservedException $e) {
            return;
        }

        $this->fail('Reservation was updated to a date that is already reserved!');
    }

    /**
     * @test
     */
    public function it_stores_billing_address()
    {
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id
        ]);
        $paymentGateway = new TestPaymentGateway;
        $token = $paymentGateway->getValidTestToken();

        $reservation->complete($paymentGateway, $token, $this->billingAddress);

        $this->assertNotNull($reservation->billingAddress);
        $this->assertArraySubset(
            $reservation->billingAddress->toArray(),
            $this->billingAddress->toArray()
        );
    }
}
