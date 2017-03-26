<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\State;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewReservationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function authenticated_users_can_view_their_own_reservation()
    {
        $user = factory(User::class)->states(['standard'])->create();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $property = factory(Property::class)->create([
            'state_id' => $state->id
        ]);
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'status' => 'paid',
            'amount' => 50000,
            'charge_id' => 'ch_' . str_random(24)
        ]);

        $response = $this->actingAs($user)->get(
            "/properties/{$property->id}/reservations/{$reservation->id}"
        );

        $response->assertStatus(200);
        $response->assertViewHas('reservation');
        $response->assertSee('$500');
        $response->assertSee("{$reservation->id}");
        $response->assertSee("{$reservation->formatted_date_start}");
        $response->assertSee("{$reservation->formatted_date_end}");
        $response->assertSee("Cancel Reservation");
        $response->assertViewHas('property');
        $response->assertSee($property->name);
        $response->assertViewHas('user');
    }

    /**
     * @test
     */
    public function standard_users_cannot_view_other_users_reservations_()
    {
        $user1 = factory(User::class)->states(['standard'])->create();
        $user2 = factory(User::class)->states(['standard'])->create();
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'user_id' => $user1->id
        ]);

        $response = $this->actingAs($user2)->get(
            "/admin/properties/{$property->id}/reservations/{$reservation->id}"
        );

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_view_reservations()
    {
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create();

        $response = $this->get(
            "/admin/properties/{$property->id}/reservations/{$reservation->id}"
        );

        $response->assertStatus(403);
    }
}
