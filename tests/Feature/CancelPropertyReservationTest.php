<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\User;
use App\Mail\ReservationCancelled;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CancelPropertyReservationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function authenticated_user_can_cancel_their_own_reservation()
    {
        Mail::fake();
        $this->makeReservation();

        $response = $this->actingAs($this->user)->put(
            "/properties/{$this->property->id}/reservations/{$this->reservation->id}", [
            'status' => 'cancelled'
        ]);

        $response->assertStatus(200);
        Mail::assertQueued(ReservationCancelled::class, function ($mail) {
            return $mail->hasTo(config('mail.accounts.admin.to'));
        });
    }

    /**
     * @test
     */
    public function user_cannot_cancel_other_users_resrvation()
    {
        Mail::fake();
        $user = factory(User::class)->states(['standard'])->create([
            'id' => 2
        ]);
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'user_id' => 1
        ]);

        $response = $this->actingAs($user)->put(
            "/properties/{$property->id}/reservations/{$reservation->id}", [
                'status' => 'cancelled'
            ]);

        Mail::assertNotQueued(ReservationCancelled::class);
        $response->assertStatus(403);
    }

    /**
     * Utility method to create a reservation.
     */
    protected function makeReservation()
    {
        $this->user = factory(User::class)->create();
        $this->property = factory(Property::class)->create();
        $this->reservation = factory(Reservation::class)->create([
            'property_id' => $this->property->id,
            'user_id' => $this->user->id
        ]);
    }
}
