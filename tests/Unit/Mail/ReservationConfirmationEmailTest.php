<?php

namespace Tests\Unit\Mail;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\State;
use App\Core\User;
use App\Mail\ReservationComplete;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReservationConfirmationEmailTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        $this->createReservation();
    }
    /**
     * @test
     */
    public function email_contains_reservation_data()
    {
        $email = new ReservationComplete($this->user, $this->reservation);

        $rendered = $this->renderMailable($email);

        $this->assertContains('<h1>Reservation Confirmation</h1>', $rendered);
        $this->assertContains($this->user->first_name, e($rendered));
        $this->assertContains($this->reservation->formatted_date_start, $rendered);
        $this->assertContains($this->reservation->formatted_date_end, $rendered);
        $this->assertContains((string)$this->reservation->getLengthOfStay(), $rendered);
        $this->assertContains((string)$this->reservation->id, $rendered);
        $this->assertContains($this->property->name, $rendered);
    }

    /**
     * @test
     */
    public function email_has_subject()
    {
        $email = new ReservationComplete($this->user, $this->reservation);

        $this->assertEquals('Reservation Confirmed', $email->build()->subject);
    }

    protected function createReservation()
    {
        $this->user = factory(User::class)->create();
        $this->state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $this->property = factory(Property::class)->states(['available'])->create([
            'state_id' => $this->state->id
        ]);
        $this->reservation = factory(Reservation::class)->make([
            'property_id' => $this->property->id,
            'user_id' => $this->user->id
        ]);
    }
}
