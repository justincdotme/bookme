<?php

namespace Tests\Feature;

use App\Core\Property;
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
    public function date_start_is_required_to_check_reservation_dates()
    {
        $isAvailableResponse = $this->checkValidation([
            'date_end' => Carbon::parse('+6 days')->toDateString(),
        ]);

        $this->assertFieldHasValidationError('date_start', $isAvailableResponse);
    }

    /**
     * @test
     */
    public function date_end_is_required_to_check_reservation_dates()
    {
        $isAvailableResponse = $this->checkValidation([
            'date_start' => Carbon::parse('+6 days')->toDateString(),
        ]);

        $this->assertFieldHasValidationError('date_end', $isAvailableResponse);
    }

    /**
     * @test
     */
    public function date_start_must_be_a_valid_date_to_check_reservation_dates()
    {
        $invalidDateStartResponse = $this->checkValidation([
            'date_start' => 'abc123',
            'date_end' => Carbon::parse('+6 days')->toDateString()
        ]);

        $this->assertFieldHasValidationError('date_start', $invalidDateStartResponse);
    }

    /**
     * @test
     */
    public function date_end_must_be_a_valid_date_to_check_reservation_dates()
    {
        $invalidDateEndResponse = $this->checkValidation([
            'date_start' => Carbon::parse('+6 days')->toDateString(),
            'date_end' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('date_end', $invalidDateEndResponse);
    }

    protected function checkValidation(array $params)
    {
        $property = factory(Property::class)->states(['available'])->create();
        $user = factory(User::class)->states(['standard'])->make();
        $this->be($user);

        return $this->json('POST', "/properties/{$property->id}/reservations/check", $params);
    }
}
