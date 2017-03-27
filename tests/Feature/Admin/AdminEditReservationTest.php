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
