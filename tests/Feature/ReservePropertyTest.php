<?php

namespace Tests\Feature;

use App\Core\Property;
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
    public function authenticated_user_can_make_reservation()
    {
        $property = factory(Property::class)->states(['available'])->create();
        $user = factory(User::class)->states(['standard'])->create();
        $this->be($user);
        $response = $this->json('POST', "/properties/{$property->id}/reservations", [
            'date_start' => Carbon::now()->toDateTimeString(),
            'date_end' => Carbon::parse('+1 week')->toDateTimeString(),
        ]);

        $responseJson = $response->decodeResponseJson();

        $this->assertArrayHasKey('status', $responseJson);
        $this->assertEquals($responseJson['status'], 'success');
        $this->assertArrayHasKey('reservation', $responseJson);
    }
}
