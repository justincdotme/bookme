<?php

namespace Tests\Feature;

use App\Core\Payment\PaymentGatewayInterface;
use App\Core\Payment\TestPaymentGateway;
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
        $this->app->instance(PaymentGatewayInterface::class, new TestPaymentGateway());
        $paymentGateway = app()->make(PaymentGatewayInterface::class);
        $charge = $paymentGateway->charge(4321, $paymentGateway->getValidTestToken());
        $user = factory(User::class)->states(['standard'])->create();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $property = factory(Property::class)->create([
            'state_id' => $state->id,
            'name' => 'foobar'
        ]);
        $reservation = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'status' => 'paid',
            'amount' => $charge->getAmount(),
            'charge_id' => $charge->getId()
        ]);

        $response = $this->actingAs($user)->get(
            "/users/{$user->id}/reservations/{$reservation->id}"
        );

        $response->assertStatus(200);
        $response->assertViewHas('reservation');
        $response->assertSee('$43.21');
        $response->assertSee("{$reservation->id}");
        $response->assertSee("{$reservation->formatted_date_start}");
        $response->assertSee("{$reservation->formatted_date_end}");
        $response->assertSee("Cancel Reservation");
        $response->assertViewHas('user');
        $response->assertSee($user->first_name);
        $response->assertSee($user->last_name);
        $response->assertViewHas('charge');
        $response->assertSee('4242');
        $response->assertSee('Visa');
    }

    /**
     * @test
     */
    public function standard_users_cannot_view_other_users_reservations_()
    {
        $user = factory(User::class)->states(['standard'])->create([

        ]);
        $property = factory(Property::class)->create();
        $reservation = factory(Reservation::class)->create([
            'user_id' => 2
        ]);

        $response = $this->actingAs($user)->get(
            "/users/{$user->id}/reservations/{$reservation->id}"
        );

        $response->assertStatus(403);
    }
}
