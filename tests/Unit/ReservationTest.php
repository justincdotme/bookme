<?php

namespace Tests\Unit;

use App\Core\Property;
use App\Core\Reservation;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;
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
}
