<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\Reservation;
use App\Core\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CheckReservationTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $property;
    protected $existingReservation;

    /**
     * @test
     */
    public function cancelled_reservations_do_not_count()
    {
        $this->existingReservation = factory(Reservation::class)->make([
            'property_id' => 1,
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days'),
            'status' => 'cancelled'
        ]);

        $isAvailableResponse = $this->checkPropertyReservation([
            'property_id' => 1,
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString(),
        ]);

        $isAvailableResponse->assertJsonFragment([
            'status' => 'success',
            'msg' => 'The property is available for this date range.'
        ]);
    }

    /**
     * @test
     */
    public function it_returns_error_when_property_is_already_reserved()
    {
        $unavailableResponse = $this->checkPropertyReservation([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString(),
        ]);

        $unavailableResponse->assertJsonFragment([
            'status' => 'error',
            'msg' => 'The property is unavailable for this date range.'
        ]);
    }

    /**
     * @test
     */
    public function it_returns_success_when_property_is_not_already_reserved()
    {
        $isAvailableResponse = $this->checkPropertyReservation([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => Carbon::parse('+6 days')->toDateString(),
        ]);

        $isAvailableResponse->assertJsonFragment([
            'status' => 'success',
            'msg' => 'The property is available for this date range.'
        ]);
    }

    /**
     * @test
     */
    public function date_start_is_required_to_check_reservation_dates()
    {
        $this->response = $this->checkPropertyReservation([
            'date_end' => Carbon::now()->toDateString(),
        ]);

        $this->assertFieldHasValidationError('date_start');
    }

    /**
     * @test
     */
    public function date_end_is_required_to_check_reservation_dates()
    {
        $this->response = $this->checkPropertyReservation([
            'date_start' => Carbon::now()->toDateString(),
        ]);

        $this->assertFieldHasValidationError('date_end');
    }

    /**
     * @test
     */
    public function date_start_must_be_a_valid_date_to_check_reservation_dates()
    {
        $this->response = $this->checkPropertyReservation([
            'date_start' => 'abc123',
            'date_end' => Carbon::now()->toDateString()
        ]);

        $this->assertFieldHasValidationError('date_start');
    }

    /**
     * @test
     */
    public function date_end_must_be_a_valid_date_to_check_reservation_dates()
    {
        $this->response = $this->checkPropertyReservation([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('date_end');
    }

    protected function checkPropertyReservation(array $params)
    {
        if (null === $this->property) {
            $this->property = factory(Property::class)->states(['available'])->create();
        }

        if (null === $this->user) {
            $this->user = factory(User::class)->states(['standard'])->create();
        }

        $this->be($this->user);

        if (null === $this->existingReservation) {
            $this->existingReservation = factory(Reservation::class)->create([
                'property_id' => $this->property->id,
                'date_start' => Carbon::parse('+1 week'),
                'date_end' => Carbon::parse('+10 days')
            ]);
        }

        return $this->json('POST', "/properties/{$this->property->id}/reservations/check", $params);
    }
}
