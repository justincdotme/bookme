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

class ReservePropertyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function reserved_property_cannot_be_reserved()
    {
        $unavailableResponse = $this->reserveProperty([
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
    public function unreserved_property_can_be_reserved()
    {
        $isAvailableResponse = $this->reserveProperty([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => Carbon::parse('+6 days')->toDateString(),
        ]);

        $isAvailableResponse->assertJsonFragment([
            'status' => 'success',
            'msg' => 'The property is available for this date range.'
        ]);
    }

    protected function reserveProperty(array $params)
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
