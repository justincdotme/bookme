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
        $isAvailableResponse = $this->createTestReservation([
            'date_end' => Carbon::now()->toDateString(),
        ]);

        $this->assertFieldHasValidationError('date_start', $isAvailableResponse);
    }

    /**
     * @test
     */
    public function date_end_is_required_to_check_reservation_dates()
    {
        $isAvailableResponse = $this->createTestReservation([
            'date_start' => Carbon::now()->toDateString(),
        ]);

        $this->assertFieldHasValidationError('date_end', $isAvailableResponse);
    }

    /**
     * @test
     */
    public function date_start_must_be_a_valid_date_to_check_reservation_dates()
    {
        $invalidDateStartResponse = $this->createTestReservation([
            'date_start' => 'abc123',
            'date_end' => Carbon::now()->toDateString()
        ]);

        $this->assertFieldHasValidationError('date_start', $invalidDateStartResponse);
    }

    /**
     * @test
     */
    public function date_end_must_be_a_valid_date_to_check_reservation_dates()
    {
        $invalidDateEndResponse = $this->createTestReservation([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('date_end', $invalidDateEndResponse);
    }

    protected function createTestReservation(array $params)
    {
        $property = factory(Property::class)->states(['available'])->create();
        $user = factory(User::class)->states(['standard'])->make();
        $this->be($user);

        return $this->json('POST', "/properties/{$property->id}/reservations/check", $params);
    }

    protected function checkPropertyReservation(array $params)
    {
        $property = factory(Property::class)->states(['available'])->create();
        $user = factory(User::class)->states(['standard'])->create();
        $this->be($user);

        factory(Reservation::class)->create([
            'property_id' => $property->id,
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days')
        ]);

        return $this->json('POST', "/properties/{$property->id}/reservations/check", $params);
    }
}
