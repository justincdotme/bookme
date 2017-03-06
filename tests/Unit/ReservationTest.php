<?php

namespace Tests\Unit;

use App\Core\Reservation;
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
}
