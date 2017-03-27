<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\User;
use EmailTestHelpers;
use Illuminate\Support\Facades\Mail;
use TestingMailEventListener;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CancelPropertyReservationTest extends TestCase
{
    use DatabaseMigrations;
    use EmailTestHelpers;

    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        Mail::getSwiftMailer()
            ->registerPlugin(new TestingMailEventListener($this));
    }

    /**
     * @test
     */
    public function authenticated_user_can_cancel_their_own_reservation()
    {
        $this->makeReservation();

        $response = $this->actingAs($this->user)->put(
            "/properties/{$this->property->id}/reservations/{$this->reservation->id}", [
            'status' => 'cancelled'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_cannot_cancel_other_users_resrvation()
    {
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

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_sends_a_cancellation_notice_to_admin()
    {
        $this->makeReservation();

        $response = $this->actingAs($this->user)->put(
            "/properties/{$this->property->id}/reservations/{$this->reservation->id}", [
            'status' => 'cancelled'
        ]);

        $response->assertStatus(200);
        $this->seeEmailWasSent();
        $this->seeEmailsSent(1);
        $this->seeEmailTo(config('mail.accounts.admin.to'));
        $this->seeEmailFrom('no-reply@bookme.justinc.me');
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
