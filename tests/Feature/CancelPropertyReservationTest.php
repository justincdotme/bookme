<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CancelPropertyReservationTest extends TestCase
{
    use DatabaseMigrations;

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
}
