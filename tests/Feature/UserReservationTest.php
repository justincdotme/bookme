<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserReservationTest extends TestCase
{
    use DatabaseMigrations;


    /**
     * @test
     */
    public function authenticated_users_can_view_a_list_of_their_own_reservations()
    {
        $user = factory(User::class)->states(['standard'])->create();
        factory(Property::class)->create();
        factory(Reservation::class, 2)->create([
            'amount' => 12345,
            'charge_id' => 'ch_12345678'
        ]);

        $response = $this->actingAs($user)->get(
            "/users/{$user->id}/reservations"
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'reservations'
        ]);
        $json = $response->decodeResponseJson();
        $this->assertArrayHasKey('reservations', $json);
        $this->assertCount(2, $json['reservations']);
    }



    /**
     * @test
     */
    public function authenticated_users_can_view_another_users_reservation_list()
    {
        $user1 = factory(User::class)->states(['standard'])->create();
        $user2 = factory(User::class)->states(['standard'])->create();

        factory(Property::class)->create();
        factory(Reservation::class, 2)->create();

        $response = $this->actingAs($user2)->get(
            "/users/{$user1->id}/reservations"
        );

        $response->assertStatus(403);
    }
}
