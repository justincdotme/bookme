<?php

namespace Tests\Feature\Admin;

use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminEditReservationTest extends TestCase
{
    use DatabaseMigrations;

    protected $reservation;

    /**
     * @test
     */
    public function admin_users_can_edit_all_reservations()
    {
        $this->createReservation();

        $response = $this->updateReservation([
            'status' => 'paid',
            'amount' => 12345,
            'date_start' => $this->reservation->date_start,
            'date_end' => $this->reservation->date_end
        ]);

        $response->assertStatus(200);
        $this->assertEquals($this->reservation->fresh()->status, 'paid');
        $this->assertEquals($this->reservation->fresh()->amount, 12345);
    }

    /**
     * @test
     */
    public function it_returns_error_if_property_already_reserved_for_date_range()
    {
        $user = factory(User::class)->states(['admin'])->create();
        $property = factory(Property::class)->states(['available'])->create();
        $reservation1 = factory(Reservation::class)->create([
            'property_id' => $property->id,
            'date_start' => Carbon::now(),
            'date_end' => Carbon::parse('+1 week')
        ]);
        factory(Reservation::class)->create([
            'property_id' => $property->id,
            'date_start' => Carbon::parse('+2 weeks'),
            'date_end' => Carbon::parse('+3 weeks')
        ]);

        $response = $this->actingAs($user)->put(
            "/admin/properties/{$property->id}/reservations/{$reservation1->id}", [
                'status' => 'paid',
                'amount' => 12345,
                'date_start' => Carbon::parse('+2 weeks'),
                'date_end' => Carbon::parse('+3 weeks')
            ]
        );


        $response->assertStatus(422);
        $response->assertJsonFragment([
            'status' => 'error',
            'msg' => 'The property has already been reserved for these dates.'
        ]);
    }

    /**
     * @test
     */
    public function date_start_is_required_to_update_a_reservation()
    {
        $this->createReservation();

        $this->response = $this->updateReservation([]);

        $this->assertFieldHasValidationError('date_start');
    }

    /**
     * @test
     */
    public function date_end_is_required_to_update_a_reservation()
    {
        $this->createReservation();
        $this->response = $this->updateReservation([]);

        $this->assertFieldHasValidationError('date_end');
    }

    protected function createReservation()
    {
        if (null === $this->property) {
            $this->property = factory(Property::class)->states(['available'])->create();
        }

        if (null === $this->user) {
            $this->user = factory(User::class)->states(['admin'])->create();
        }

        if (null === $this->reservation) {
            $this->reservation = factory(Reservation::class)->create([
                'property_id' => $this->property->id,
                'status' => 'pending'
            ]);
        }
    }

    protected function updateReservation($params, $noAuthenticatedUser = false)
    {
        if (null === $this->property) {
            $this->property = factory(Property::class)->states(['available'])->create();
        }

        if (null === $this->user) {
            $this->user = factory(User::class)->states(['standard'])->create();
        }

        if (!$noAuthenticatedUser) {
            $this->actingAs($this->user);
        }

        return $response = $this->actingAs($this->user)->put(
            "/admin/properties/{$this->property->id}/reservations/{$this->reservation->id}", $params
        );
    }
}
