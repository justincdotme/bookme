<?php

namespace Tests\Unit\Mail;

use App\Core\Reservation;
use App\Core\User;
use App\Mail\ReservationCancelled;
use Tests\TestCase;

class ReservationCancelledNotificationEmailTest extends TestCase
{
    /**
     * @test
     */
    public function email_has_correct_contents()
    {
        $user = factory(User::class)->make([
            'email' => 'foo@bar.com'
        ]);
        $reservation = factory(Reservation::class)->make([
            'id' => 1,
            'property_id' => 1,
            'user_id' => 1
        ]);
        $email = new ReservationCancelled($user, $reservation);

        $rendered = $this->renderMailable($email);

        $this->assertContains('<h1>Reservation Cancellation</h1>', $rendered);
        $this->assertContains(
            "<h2>{$user->email} has cancelled reservation #{$reservation->id}</h2>",
            $rendered
        );
    }

    /**
     * @test
     */
    public function email_has_subject()
    {
        $user = factory(User::class)->make();
        $reservation = factory(Reservation::class)->make([
            'property_id' => 1,
            'user_id' => 1
        ]);

        $email = new ReservationCancelled($user, $reservation);

        $this->assertEquals('Reservation Cancellation Notice', $email->build()->subject);
    }
}
