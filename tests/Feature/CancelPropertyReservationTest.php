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
        $user = factory(User::class)->states(['standard'])->create();
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put(
            "/properties/{$property->id}/reservations/{$reservation->id}", [
            'status' => 'cancelled'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_cannot_cancel_other_users_resrvation()
    {
        $user1 = factory(User::class)->states(['standard'])->create();
        $user2 = factory(User::class)->states(['standard'])->create();
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'user_id' => $user1->id
        ]);

        $response = $this->actingAs($user2)->put(
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
        $user = factory(User::class)->create();
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put(
            "/properties/{$property->id}/reservations/{$reservation->id}", [
            'status' => 'cancelled'
        ]);

        $response->assertStatus(200);
        $this->seeEmailWasSent();
        $this->seeEmailsSent(1);
        $this->seeEmailTo(config('mail.accounts.admin.to'));
        $this->seeEmailFrom('no-reply@bookme.justinc.me');
    }
}
