<?php

namespace Tests\Feature\Admin;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\State;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminViewReservationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function admin_users_can_view_admin_reservation_detail()
    {
        $user = factory(User::class)->states(['admin'])->create();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $property = factory(Property::class)->create([
            'state_id' => $state->id
        ]);
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'status' => 'paid',
            'amount' => 12345,
            'charge_id' => 'ch_' . str_random(24)
        ]);

        $response = $this->actingAs($user)->get(
            "/admin/properties/{$property->id}/reservations/{$reservation->id}"
        );
        $responseJson = $response->decodeResponseJson();

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertArrayHasKey('paymentUrl', $responseJson);
        $this->assertEquals(
            'https://dashboard.stripe.com/test/payments/' . $reservation->charge_id,
            $responseJson['paymentUrl']
        );
        $this->assertArrayHasKey('reservation', $responseJson);
        $this->assertArraySubset($reservation->toArray(), $responseJson['reservation']);
        $this->assertArrayHasKey('property', $responseJson);
        $this->assertArraySubset($property->toArray(), $responseJson['property']);
        $this->assertArrayHasKey('user', $responseJson);
        $this->assertArraySubset($user->toArray(), $responseJson['user']);
    }

    /**
     * @test
     */
    public function standard_users_cannot_view_admin_reservation_detail()
    {
        $user = factory(User::class)->states(['standard'])->create();
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create();

        $response = $this->actingAs($user)->get(
            "/admin/properties/{$property->id}/reservations/{$reservation->id}"
        );

        $response->assertStatus(403);
    }
}
